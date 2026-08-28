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

// Build file path
$filePath = ROOT_PATH . '/storage/uploads/registrar/generated/' . $file['stored_name'];

if (!file_exists($filePath)) {
    http_response_code(404);
    die('File not found on disk');
}

// Verify file integrity
$currentHash = hash_file('sha256', $filePath);
if ($currentHash !== $file['sha256_hash']) {
    error_log("File integrity check failed for file ID $fileId");
    http_response_code(500);
    die('File integrity check failed');
}

// Set headers for download
if ($file['mime'] === 'text/html') {
    // Serve HTML files directly (browser can print to PDF)
    header('Content-Type: text/html; charset=UTF-8');
    header('Content-Disposition: inline; filename="' . $file['original_name'] . '.html"');
} else {
    // Serve PDFs as download
    header('Content-Type: ' . $file['mime']);
    header('Content-Disposition: attachment; filename="' . $file['original_name'] . '.pdf"');
}
header('Content-Length: ' . $file['size']);
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
