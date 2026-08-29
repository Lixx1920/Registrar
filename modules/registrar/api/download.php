<?php
/**
 * SMS2 - Registrar API: File Download
 * Secure file download with access control
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/api-helpers.php';

regApiRequireAccess();

$fileId = isset($_GET['file_id']) ? (int) $_GET['file_id'] : 0;

if ($fileId === 0) {
    http_response_code(400);
    die('Invalid file ID');
}

$db = db();

// Get file record
$stmt = $db->prepare("SELECT * FROM `reg_files` WHERE `id` = ? AND `status` = 'Active'");
$stmt->execute([$fileId]);
$file = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$file) {
    http_response_code(404);
    die('File not found');
}

// Build file path - check student directory, generated directory, or base registrar uploads
$possiblePaths = [];
if (!empty($file['student_id'])) {
    $possiblePaths[] = ROOT_PATH . '/storage/uploads/registrar/' . $file['student_id'] . '/' . $file['stored_name'];
}
$possiblePaths[] = ROOT_PATH . '/storage/uploads/registrar/generated/' . $file['stored_name'];
$possiblePaths[] = ROOT_PATH . '/storage/uploads/registrar/' . $file['stored_name'];

$filePath = null;
foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        $filePath = $path;
        break;
    }
}

if (!$filePath) {
    http_response_code(404);
    die('File not found on disk');
}

// Verify file integrity
if (!empty($file['sha256_hash'])) {
    $currentHash = hash_file('sha256', $filePath);
    if (!hash_equals($file['sha256_hash'], $currentHash)) {
        error_log("File integrity check failed for file ID $fileId");
        http_response_code(500);
        die('File integrity check failed');
    }
}

$mode = strtolower((string)($_GET['mode'] ?? 'inline'));
$originalName = $file['original_name'] ?: ('document-' . $fileId);
$mime = $file['mime'] ?: 'application/octet-stream';
$fileSize = filesize($filePath) ?: ($file['size'] ?? 0);

// Clean filename for header
$safeFilename = preg_replace('/[^a-zA-Z0-9_\-\. ]/', '_', $originalName);

// Set headers for download / inline preview
header('Content-Type: ' . $mime);
if ($mode === 'download') {
    header('Content-Disposition: attachment; filename="' . $safeFilename . '"');
} else {
    // Inline preview (PDFs, Images, HTML render in browser)
    header('Content-Disposition: inline; filename="' . $safeFilename . '"');
}
header('Content-Length: ' . $fileSize);
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Output file
readfile($filePath);

// Log download
try {
    $userId = $_SESSION['user_id'] ?? 0;
    $stmt = $db->prepare("INSERT INTO `reg_audit_log` (`action`, `details`, `user_id`) VALUES ('download_file', ?, ?)");
    $stmt->execute(["Downloaded file: {$file['original_name']} (ID: $fileId)", $userId]);
} catch (Throwable $e) {
    error_log('Failed to log file download: ' . $e->getMessage());
}

exit;
?>
