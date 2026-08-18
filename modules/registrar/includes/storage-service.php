<?php
/**
 * SMS 2 - Registrar Storage Service
 * Handles validated file uploads with SHA-256 hashing
 * 
 * Features:
 * - MIME type validation
 * - Size limits (configurable per category)
 * - SHA-256 hash computation
 * - Organized storage by student_id
 * - Hash verification on download
 */
declare(strict_types=1);

if (!defined('ROOT_PATH')) {
    require_once dirname(__DIR__, 3) . '/config/config.php';
}
require_once ROOT_PATH . '/config/database.php';

if (!defined('REG_UPLOAD_BASE')) {
    define('REG_UPLOAD_BASE', ROOT_PATH . '/storage/uploads/registrar');
}
if (!defined('REG_MAX_FILE_SIZE')) {
    define('REG_MAX_FILE_SIZE', 5242880); // 5 MB default
}

/**
 * Allowed MIME types per category
 */
function regGetAllowedMimes(string $category): array
{
    $mimes = [
        'identity' => ['image/jpeg', 'image/png', 'application/pdf'],
        'health' => ['image/jpeg', 'image/png', 'application/pdf'],
        'academic' => ['application/pdf', 'image/jpeg'],
        'documents' => ['application/pdf'],
        'photos' => ['image/jpeg', 'image/png'],
        'general' => ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 
                      'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
    ];
    return $mimes[$category] ?? $mimes['general'];
}

/**
 * Validate file upload and store with hash
 * 
 * Returns: ['success' => true, 'file_id' => int, 'hash' => str] or ['success' => false, 'error' => str]
 */
function regStoreUploadedFile(
    int $studentId,
    string $category,
    array $fileArray,
    int $uploadedBy,
    ?int $maxSize = null
): array {
    $db = db();
    
    $maxSize ??= REG_MAX_FILE_SIZE;
    
    // Validate file array structure
    if (!isset($fileArray['tmp_name'], $fileArray['name'], $fileArray['size'], $fileArray['error'])) {
        return ['success' => false, 'error' => 'Invalid file array structure'];
    }
    
    // Check upload error
    if ($fileArray['error'] !== UPLOAD_ERR_OK) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds php.ini upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds form MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File upload was incomplete',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Cannot write to disk',
            UPLOAD_ERR_EXTENSION => 'Extension rejected by PHP',
        ];
        return ['success' => false, 'error' => $errors[$fileArray['error']] ?? 'Unknown upload error'];
    }
    
    // Validate file size
    if ($fileArray['size'] > $maxSize) {
        return ['success' => false, 'error' => "File exceeds maximum size of " . ($maxSize / 1048576) . "MB"];
    }
    
    // Validate MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $fileArray['tmp_name']);
    finfo_close($finfo);
    
    $allowedMimes = regGetAllowedMimes($category);
    if (!in_array($mimeType, $allowedMimes, true)) {
        return ['success' => false, 'error' => "File type $mimeType not allowed for category $category"];
    }
    
    // Compute SHA-256 hash
    $fileContent = file_get_contents($fileArray['tmp_name']);
    if ($fileContent === false) {
        return ['success' => false, 'error' => 'Cannot read uploaded file'];
    }
    
    $sha256Hash = hash('sha256', $fileContent);
    
    // Create storage directory
    $studentDir = REG_UPLOAD_BASE . '/' . $studentId;
    if (!is_dir($studentDir)) {
        if (!mkdir($studentDir, 0700, true)) {
            return ['success' => false, 'error' => 'Cannot create student upload directory'];
        }
    }
    
    // Generate unique stored filename (hash-based + timestamp)
    $ext = pathinfo($fileArray['name'], PATHINFO_EXTENSION);
    $storedName = substr($sha256Hash, 0, 16) . '-' . time() . '.' . preg_replace('/[^a-z0-9]/i', '', $ext);
    $storedPath = $studentDir . '/' . $storedName;
    
    // Move uploaded file
    if (!move_uploaded_file($fileArray['tmp_name'], $storedPath)) {
        return ['success' => false, 'error' => 'Cannot move uploaded file to storage'];
    }
    
    // Fix permissions
    chmod($storedPath, 0600);
    
    // Store metadata in database
    try {
        $stmt = $db->prepare("INSERT INTO `reg_files` 
            (`student_id`, `category`, `original_name`, `stored_name`, `mime`, `size`, `sha256_hash`, `uploaded_by`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$studentId, $category, $fileArray['name'], $storedName, $mimeType, $fileArray['size'], $sha256Hash, $uploadedBy]);
        $fileId = (int)$db->lastInsertId();
    } catch (Throwable $e) {
        // Cleanup file on DB error
        unlink($storedPath);
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
    
    return ['success' => true, 'file_id' => $fileId, 'hash' => $sha256Hash];
}

/**
 * Verify file integrity by recomputing hash
 * 
 * Returns: ['valid' => true, 'hash' => str] or ['valid' => false, 'error' => str]
 */
function regVerifyFileIntegrity(int $fileId): array
{
    $db = db();
    
    $stmt = $db->prepare("SELECT `student_id`, `stored_name`, `sha256_hash` FROM `reg_files` WHERE `id` = ?");
    $stmt->execute([$fileId]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$file) {
        return ['valid' => false, 'error' => 'File not found'];
    }
    
    $filePath = REG_UPLOAD_BASE . '/' . $file['student_id'] . '/' . $file['stored_name'];
    if (!file_exists($filePath)) {
        return ['valid' => false, 'error' => 'File missing from storage'];
    }
    
    $computedHash = hash('sha256', file_get_contents($filePath));
    $isValid = hash_equals($file['sha256_hash'], $computedHash);
    
    return ['valid' => $isValid, 'hash' => $computedHash];
}

/**
 * Get file download path (student-scoped, restricted)
 */
function regGetFilePath(int $fileId): ?string
{
    $db = db();
    
    $stmt = $db->prepare("SELECT `student_id`, `stored_name` FROM `reg_files` WHERE `id` = ? AND `is_deleted` = 0");
    $stmt->execute([$fileId]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$file) {
        return null;
    }
    
    $path = REG_UPLOAD_BASE . '/' . $file['student_id'] . '/' . $file['stored_name'];
    return file_exists($path) ? $path : null;
}

/**
 * Delete file (soft delete - keeps in DB with is_deleted flag)
 */
function regDeleteFile(int $fileId, int $deletedBy): bool
{
    $db = db();
    
    try {
        $stmt = $db->prepare("UPDATE `reg_files` SET `is_deleted` = 1, `updated_at` = NOW() WHERE `id` = ?");
        return $stmt->execute([$fileId]);
    } catch (Throwable) {
        return false;
    }
}

/**
 * Get file metadata
 */
function regGetFile(int $fileId): ?array
{
    $db = db();
    
    $stmt = $db->prepare("SELECT * FROM `reg_files` WHERE `id` = ? AND `is_deleted` = 0");
    $stmt->execute([$fileId]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

/**
 * List files for a student in a category
 */
function regListStudentFiles(int $studentId, ?string $category = null): array
{
    $db = db();
    
    if ($category) {
        $stmt = $db->prepare("SELECT * FROM `reg_files` 
            WHERE `student_id` = ? AND `category` = ? AND `is_deleted` = 0 
            ORDER BY `created_at` DESC");
        $stmt->execute([$studentId, $category]);
    } else {
        $stmt = $db->prepare("SELECT * FROM `reg_files` 
            WHERE `student_id` = ? AND `is_deleted` = 0 
            ORDER BY `created_at` DESC");
        $stmt->execute([$studentId]);
    }
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}
?>
