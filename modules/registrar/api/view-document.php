<?php
/**
 * SMS2 - Registrar API: View Document
 * Secure file viewer & downloader for student uploaded credentials
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';

requireAuth();

$fileId = isset($_GET['file_id']) ? (int) $_GET['file_id'] : 0;

if ($fileId <= 0) {
    http_response_code(400);
    die('Invalid file ID');
}

$db = db();

// Get file record (support both active files and legacy rows where is_deleted may be 0/null)
$stmt = $db->prepare("
    SELECT f.*, s.student_number
    FROM reg_files f
    LEFT JOIN reg_students s ON f.student_id = s.id
    WHERE f.id = ? AND (f.status = 'Active' OR f.status IS NULL)
");
$stmt->execute([$fileId]);
$file = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$file) {
    http_response_code(404);
    die('File not found');
}

$studentNumber = (string) ($file['student_number'] ?? '');
$studentId     = (int) ($file['student_id'] ?? 0);
$storedName    = basename((string) ($file['stored_name'] ?? ''));

$candidatePaths = [];
if ($studentNumber !== '') {
    $candidatePaths[] = ROOT_PATH . '/storage/uploads/credentials/' . $studentNumber . '/' . $storedName;
}
if ($studentId > 0) {
    $candidatePaths[] = ROOT_PATH . '/storage/uploads/registrar/' . $studentId . '/' . $storedName;
    $candidatePaths[] = ROOT_PATH . '/storage/uploads/credentials/' . $studentId . '/' . $storedName;
}
$candidatePaths[] = ROOT_PATH . '/storage/uploads/registrar/generated/' . $storedName;
$candidatePaths[] = ROOT_PATH . '/storage/uploads/registrar/' . $storedName;

$filePath = null;
foreach ($candidatePaths as $path) {
    if ($storedName !== '' && is_file($path)) {
        $filePath = $path;
        break;
    }
}

if (!$filePath) {
    http_response_code(404);
    die('File not found on disk');
}

$mime = (string) ($file['mime'] ?: '');
if ($mime === '' && function_exists('mime_content_type')) {
    $mime = (string) (mime_content_type($filePath) ?: 'application/octet-stream');
}
if ($mime === '') {
    $mime = 'application/octet-stream';
}

$fileSize = filesize($filePath) ?: (int) ($file['size'] ?? 0);
$originalName = (string) ($file['original_name'] ?: ('document-' . $fileId));
$safeFilename = preg_replace('/[^a-zA-Z0-9_\-\. ]/', '_', $originalName) ?: ('document-' . $fileId);

$mode = strtolower((string) ($_GET['mode'] ?? 'inline'));
$disposition = ($mode === 'download') ? 'attachment' : 'inline';

header('Content-Type: ' . $mime);
header('Content-Disposition: ' . $disposition . '; filename="' . $safeFilename . '"');
header('Content-Length: ' . $fileSize);
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

readfile($filePath);
exit;
