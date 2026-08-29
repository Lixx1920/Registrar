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
        'academic' => ['application/pdf', 'image/jpeg', 'image/png'],
        'documents' => ['application/pdf', 'image/jpeg', 'image/png'],
        'photos' => ['image/jpeg', 'image/png'],
        'form_138' => ['application/pdf', 'image/jpeg', 'image/png'],
        'form_137' => ['application/pdf', 'image/jpeg', 'image/png'],
        'good_moral' => ['application/pdf', 'image/jpeg', 'image/png'],
        'psa_birth_cert' => ['application/pdf', 'image/jpeg', 'image/png'],
        'barangay_clearance' => ['application/pdf', 'image/jpeg', 'image/png'],
        'general' => ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 
                      'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
    ];
    return $mimes[$category] ?? $mimes['general'];
}

/**
 * Standard required institutional document types for Digital File Storage
 */
function regGetDigitalDocumentTypes(): array
{
    return [
        'form_138' => [
            'code'        => 'form_138',
            'title'       => 'Form 138 (Report Card)',
            'short_title' => 'Form 138',
            'icon'        => 'fa-file-invoice',
            'color'       => '#2563eb', // blue
            'bg_light'    => '#eff6ff',
            'border'      => '#bfdbfe',
            'description' => 'Official student report card with periodic grading records and attendance.'
        ],
        'form_137' => [
            'code'        => 'form_137',
            'title'       => 'Form 137',
            'short_title' => 'Form 137',
            'icon'        => 'fa-file-alt',
            'color'       => '#7c3aed', // purple
            'bg_light'    => '#f5f3ff',
            'border'      => '#ddd6fe',
            'description' => 'Permanent student academic record transcript from previous school / DepEd.'
        ],
        'good_moral' => [
            'code'        => 'good_moral',
            'title'       => 'Certificate of Good Moral',
            'short_title' => 'Good Moral',
            'icon'        => 'fa-award',
            'color'       => '#059669', // emerald
            'bg_light'    => '#ecfdf5',
            'border'      => '#a7f3d0',
            'description' => 'Official certificate of good moral character issued by previous institution.'
        ],
        'psa_birth_cert' => [
            'code'        => 'psa_birth_cert',
            'title'       => 'PSA Authenticated Birth Certificate',
            'short_title' => 'PSA Birth Cert',
            'icon'        => 'fa-id-card',
            'color'       => '#d97706', // amber
            'bg_light'    => '#fffbeb',
            'border'      => '#fde68a',
            'description' => 'Philippine Statistics Authority (PSA) authenticated copy of birth certificate.'
        ],
        'barangay_clearance' => [
            'code'        => 'barangay_clearance',
            'title'       => 'Barangay Clearance',
            'short_title' => 'Brgy Clearance',
            'icon'        => 'fa-shield-alt',
            'color'       => '#0d9488', // teal
            'bg_light'    => '#f0fdfa',
            'border'      => '#99f6e4',
            'description' => 'Valid barangay certificate / residency clearance of the student.'
        ],
    ];
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
        if ($studentId > 0 && in_array($category, ['form_138', 'form_137', 'good_moral', 'psa_birth_cert', 'barangay_clearance', 'identity', 'health'])) {
            $stmtOld = $db->prepare("UPDATE `reg_files` SET `is_deleted` = 1, `updated_at` = NOW() WHERE `student_id` = ? AND `category` = ? AND `is_deleted` = 0");
            $stmtOld->execute([$studentId, $category]);
        }

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

/**
 * Get the 5 required digital document status for a student
 * Returns array indexed by doc type key with file metadata or null if not uploaded.
 */
function regGetStudentDigitalDocuments(int $studentId): array
{
    $docTypes = regGetDigitalDocumentTypes();
    $files = regListStudentFiles($studentId);
    
    $result = [];
    foreach ($docTypes as $code => $def) {
        $found = null;
        foreach ($files as $f) {
            if ($f['category'] === $code) {
                $found = $f;
                break;
            }
        }
        $result[$code] = [
            'type'        => $def,
            'is_uploaded' => ($found !== null),
            'file'        => $found,
        ];
    }
    
    return $result;
}

/**
 * Get summary stats for Digital File Storage dashboard
 */
function regGetDigitalStorageSummary(): array
{
    $db = db();
    $docTypes = regGetDigitalDocumentTypes();
    
    $totalStudents = (int)$db->query("SELECT COUNT(*) FROM `reg_students` WHERE `status` = 'Active'")->fetchColumn();
    $totalFiles = (int)$db->query("SELECT COUNT(*) FROM `reg_files` WHERE `is_deleted` = 0 AND `status` = 'Active'")->fetchColumn();
    
    // Submissions per document type
    $categoryCounts = [];
    foreach (array_keys($docTypes) as $code) {
        $stmt = $db->prepare("SELECT COUNT(DISTINCT student_id) FROM `reg_files` 
            WHERE `category` = ? AND `is_deleted` = 0 AND `status` = 'Active'");
        $stmt->execute([$code]);
        $categoryCounts[$code] = (int)$stmt->fetchColumn();
    }
    
    // Students compliance calculation
    $studentDocCounts = $db->query("
        SELECT student_id, COUNT(DISTINCT category) AS uploaded_types
        FROM `reg_files`
        WHERE `category` IN ('form_138', 'form_137', 'good_moral', 'psa_birth_cert', 'barangay_clearance')
          AND `is_deleted` = 0 AND `status` = 'Active' AND `student_id` IS NOT NULL
        GROUP BY student_id
    ")->fetchAll(PDO::FETCH_KEY_PAIR);
    
    $fullyCompliant = 0;
    foreach ($studentDocCounts as $sId => $cnt) {
        if ((int)$cnt >= 5) {
            $fullyCompliant++;
        }
    }
    
    $incompleteCount = max(0, $totalStudents - $fullyCompliant);
    
    return [
        'total_students'   => $totalStudents,
        'total_files'      => $totalFiles,
        'category_counts'  => $categoryCounts,
        'fully_compliant'  => $fullyCompliant,
        'incomplete_count' => $incompleteCount,
        'doc_types'        => $docTypes,
    ];
}

/**
 * Format bytes to readable string (e.g. 1.25 MB)
 */
function regFormatFileSize(int $bytes): string
{
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    }
    if ($bytes >= 1024) {
        return number_format($bytes / 1024, 1) . ' KB';
    }
    return $bytes . ' B';
}
?>

