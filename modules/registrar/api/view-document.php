<?php
/**
 * SMS2 - Registrar API: View Document
 * Secure file viewer for student uploaded credentials
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';

requireAuth();

// Ensure only registrar (or authorized users) can view this
// Or you could allow the student to view their own by checking roles.
// For now, requiring auth is the baseline.

$fileId = isset($_GET['file_id']) ? (int) $_GET['file_id'] : 0;

if ($fileId === 0) {
    http_response_code(400);
    die('Invalid file ID');
}

$db = db();

// Get file record
$stmt = $db->prepare("
    SELECT f.*, s.student_number 
    FROM reg_files f
    LEFT JOIN reg_students s ON f.student_id = s.id
    WHERE f.id = ? AND f.status = 'Active'
");
$stmt->execute([$fileId]);
$file = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$file) {
    http_response_code(404);
    die('File not found');
}

// Build file path for student credentials
$studentNumber = $file['student_number'] ?? '';
$filePath = ROOT_PATH . '/storage/uploads/credentials/' . $studentNumber . '/' . $file['stored_name'];

if (!file_exists($filePath)) {
    http_response_code(404);
    die('File not found on disk');
}

$mime = $file['mime'] ?: 'application/octet-stream';
$fileSize = filesize($filePath) ?: ($file['size'] ?? 0);

// Clean filename for header
$safeFilename = preg_replace('/[^a-zA-Z0-9_\-\. ]/', '_', $file['original_name']);

// Set headers for inline preview (PDFs, Images, HTML render in browser)
header('Content-Type: ' . $mime);
header('Content-Disposition: inline; filename="' . $safeFilename . '"');
header('Content-Length: ' . $fileSize);
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Output file
readfile($filePath);
exit;
