<?php
/**
 * SMS 2 - Registrar Service
 * High-level repository functions using all core services
 * 
 * Orchestrates:
 * - Student CRUD operations
 * - Document request workflow
 * - File storage and verification
 * - Digital signatures and verification codes
 * - Audit logging
 */
declare(strict_types=1);

// Load all core services
require_once __DIR__ . '/storage-service.php';
require_once __DIR__ . '/signing-service.php';
require_once __DIR__ . '/document-engine.php';

/* ============================================================================
   STUDENT OPERATIONS
   ============================================================================ */

/**
 * Create or update student record
 */
function regSaveStudent(array $data, int $userId): array
{
    global $db;
    
    $studentId = $data['id'] ?? null;
    $now = date('Y-m-d H:i:s');
    
    try {
        if ($studentId) {
            // Update existing
            $stmt = $db->prepare("UPDATE `reg_students` SET 
                `first_name` = ?, `middle_name` = ?, `last_name` = ?, `suffix` = ?,
                `date_of_birth` = ?, `gender` = ?, `nationality` = ?,
                `program_course` = ?, `year_section` = ?, `college_department` = ?,
                `birth_cert_no` = ?, `enrollment_date` = ?, `status` = ?,
                `updated_at` = ?, `updated_by` = ?
                WHERE `id` = ?");
            
            $stmt->execute([
                $data['first_name'] ?? null,
                $data['middle_name'] ?? null,
                $data['last_name'] ?? null,
                $data['suffix'] ?? null,
                $data['date_of_birth'] ?? null,
                $data['gender'] ?? null,
                $data['nationality'] ?? null,
                $data['program_course'] ?? null,
                $data['year_section'] ?? null,
                $data['college_department'] ?? null,
                $data['birth_cert_no'] ?? null,
                $data['enrollment_date'] ?? null,
                $data['status'] ?? 'Active',
                $now,
                $userId,
                $studentId
            ]);
            
            regLog($userId, 'registrar', 'update_student', "Student $studentId updated", ['student_id' => $studentId]);
            
            return ['success' => true, 'student_id' => $studentId, 'action' => 'updated'];
        } else {
            // Create new
            $stmt = $db->prepare("INSERT INTO `reg_students` 
                (`student_number`, `first_name`, `middle_name`, `last_name`, `suffix`,
                 `date_of_birth`, `gender`, `nationality`, `program_course`, `year_section`,
                 `college_department`, `birth_cert_no`, `enrollment_date`, `status`, `created_by`, `updated_by`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $data['student_number'],
                $data['first_name'] ?? null,
                $data['middle_name'] ?? null,
                $data['last_name'] ?? null,
                $data['suffix'] ?? null,
                $data['date_of_birth'] ?? null,
                $data['gender'] ?? null,
                $data['nationality'] ?? null,
                $data['program_course'] ?? null,
                $data['year_section'] ?? null,
                $data['college_department'] ?? null,
                $data['birth_cert_no'] ?? null,
                $data['enrollment_date'] ?? null,
                $data['status'] ?? 'Active',
                $userId,
                $userId
            ]);
            
            $newId = (int)$db->lastInsertId();
            regLog($userId, 'registrar', 'create_student', "New student created: {$data['student_number']}", ['student_id' => $newId]);
            
            return ['success' => true, 'student_id' => $newId, 'action' => 'created'];
        }
    } catch (Throwable $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Get student with all related data
 */
function regGetStudent(int $studentId): ?array
{
    global $db;
    
    $stmt = $db->prepare("SELECT * FROM `reg_students` WHERE `id` = ?");
    $stmt->execute([$studentId]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$student) {
        return null;
    }
    
    // Add related data
    $stmt = $db->prepare("SELECT * FROM `reg_guardians` WHERE `student_id` = ? ORDER BY `is_primary` DESC");
    $stmt->execute([$studentId]);
    $student['guardians'] = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    
    $stmt = $db->prepare("SELECT * FROM `reg_academic_history` WHERE `student_id` = ? ORDER BY `from_year` DESC");
    $stmt->execute([$studentId]);
    $student['academic_history'] = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    
    $stmt = $db->prepare("SELECT * FROM `reg_health_records` WHERE `student_id` = ? ORDER BY `checkup_date` DESC LIMIT 5");
    $stmt->execute([$studentId]);
    $student['health_records'] = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    
    $stmt = $db->prepare("SELECT * FROM `reg_credentials` WHERE `student_id` = ? AND `status` = 'Active'");
    $stmt->execute([$studentId]);
    $student['credentials'] = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    
    return $student;
}

/**
 * Search students by name, student number, or program
 */
function regSearchStudents(string $query, ?int $limit = 20): array
{
    global $db;
    
    $q = "%$query%";
    $stmt = $db->prepare("SELECT * FROM `reg_students` 
        WHERE `student_number` LIKE ? OR `first_name` LIKE ? OR `last_name` LIKE ? OR `program_course` LIKE ?
        ORDER BY `last_name`, `first_name`
        LIMIT ?");
    $stmt->execute([$q, $q, $q, $q, $limit ?? 20]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

/* ============================================================================
   DOCUMENT REQUEST WORKFLOW
   ============================================================================ */

/**
 * Create a new document request
 */
function regCreateDocumentRequest(int $studentId, array $docTypes, string $purpose, string $channel = 'walk-in', int $userId): array
{
    global $db;
    
    try {
        // Create request
        $requestNo = regGenerateDocumentNumber('REQ_NO');
        $stmt = $db->prepare("INSERT INTO `reg_doc_requests` 
            (`request_no`, `student_id`, `purpose`, `channel`, `status`, `requested_by`, `created_by`)
            VALUES (?, ?, ?, ?, 'Submitted', ?, ?)");
        $stmt->execute([$requestNo, $studentId, $purpose, $channel, $userId, $userId]);
        $requestId = (int)$db->lastInsertId();
        
        // Create request items
        foreach ($docTypes as $docType) {
            $stmt = $db->prepare("INSERT INTO `reg_doc_request_items` 
                (`request_id`, `doc_type`, `copies`, `status`)
                VALUES (?, ?, ?, 'Pending')");
            $stmt->execute([$requestId, $docType, 1]);
        }
        
        regLog($userId, 'registrar', 'create_request', "Document request $requestNo created", ['request_id' => $requestId]);
        
        return ['success' => true, 'request_id' => $requestId, 'request_no' => $requestNo];
    } catch (Throwable $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Update request status in workflow
 */
function regUpdateRequestStatus(int $requestId, string $newStatus, int $userId): array
{
    global $db;
    
    $validStatuses = ['Submitted', 'Verified', 'Processing', 'For Release', 'Released', 'Cancelled'];
    if (!in_array($newStatus, $validStatuses, true)) {
        return ['success' => false, 'error' => "Invalid status: $newStatus"];
    }
    
    try {
        $stmt = $db->prepare("UPDATE `reg_doc_requests` SET `status` = ?, `updated_at` = NOW() WHERE `id` = ?");
        $stmt->execute([$newStatus, $requestId]);
        
        regLog($userId, 'registrar', 'update_request_status', "Request $requestId status: $newStatus", ['request_id' => $requestId]);
        
        return ['success' => true, 'status' => $newStatus];
    } catch (Throwable $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Generate and sign a document for a request item
 * Returns verification code and document number
 */
function regGenerateRequestDocument(int $itemId, string $docType, int $userId): array
{
    global $db;
    
    // Fetch request item with request details
    $stmt = $db->prepare("SELECT i.*, r.`student_id`, r.`request_no` FROM `reg_doc_request_items` i 
        JOIN `reg_doc_requests` r ON i.`request_id` = r.`id` 
        WHERE i.`id` = ?");
    $stmt->execute([$itemId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$item) {
        return ['success' => false, 'error' => 'Request item not found'];
    }
    
    // Generate PDF based on document type
    $docResult = match ($docType) {
        'FORM137' => regGenerateForm137($item['student_id']),
        'GMC' => regGenerateGoodMoral($item['student_id']),
        'TOR' => regGenerateForm137($item['student_id']),
        'CERT' => regGenerateCertification($item['student_id']),
        default => ['success' => false, 'error' => "Unknown document type: $docType"]
    };
    
    if (!$docResult['success']) {
        return $docResult;
    }
    
    // Compute file hash and create verification code
    $fileHash = hash_file('sha256', $docResult['pdf_path']);
    $payload = "{$docResult['doc_no']}|{$item['student_id']}|$docType|" . date('Y-m-d H:i:s') . "|$fileHash";
    
    $verResult = regCreateVerification($fileHash, $docType, $item['student_id'], $payload);
    if (!$verResult['success']) {
        return $verResult;
    }
    
    // Update request item
    try {
        $stmt = $db->prepare("UPDATE `reg_doc_request_items` 
            SET `generated_file_id` = ?, `verification_code_id` = ?, `status` = 'Generated'
            WHERE `id` = ?");
        $stmt->execute([$docResult['file_id'], $verResult['code_id'], $itemId]);
        
        regLog($userId, 'registrar', 'generate_document', 
            "Generated $docType: {$verResult['code']}", 
            ['item_id' => $itemId, 'verification_code' => $verResult['code']]);
        
        return [
            'success' => true,
            'file_id' => $docResult['file_id'],
            'verification_code' => $verResult['code'],
            'doc_no' => $docResult['doc_no']
        ];
    } catch (Throwable $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Release a document to claimant (creates release record)
 */
function regReleaseDocument(int $itemId, string $claimantName, ?string $claimantId, int $releasedBy): array
{
    global $db;
    
    try {
        $releaseNo = regGenerateDocumentNumber('RELEASE_NO');
        
        $stmt = $db->prepare("INSERT INTO `reg_doc_releases` 
            (`request_item_id`, `claim_type`, `release_slip_no`, `released_by`, `claimant_name`, `claimant_id`)
            VALUES (?, 'walk-in', ?, ?, ?, ?)");
        $stmt->execute([$itemId, $releaseNo, $releasedBy, $claimantName, $claimantId]);
        
        // Update item status
        $stmt = $db->prepare("UPDATE `reg_doc_request_items` SET `status` = 'Released', `released_at` = NOW() WHERE `id` = ?");
        $stmt->execute([$itemId]);
        
        regLog($releasedBy, 'registrar', 'release_document', "Released: $releaseNo", ['item_id' => $itemId, 'slip_no' => $releaseNo]);
        
        return ['success' => true, 'release_slip_no' => $releaseNo];
    } catch (Throwable $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/* ============================================================================
   REPORTING & ANALYTICS
   ============================================================================ */

/**
 * Get registrar dashboard statistics
 */
function regGetDashboardStats(): array
{
    global $db;
    
    $stats = [];
    
    // Total students
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM `reg_students` WHERE `status` = 'Active'");
    $stats['total_students'] = (int)$stmt->fetchColumn();
    
    // Active students
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM `reg_student_statuses` 
        WHERE `status` = 'Active' AND `effective_date` <= CURDATE()");
    $stats['active_today'] = (int)$stmt->fetchColumn();
    
    // Pending requests
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM `reg_doc_requests` 
        WHERE `status` IN ('Submitted', 'Verified', 'Processing')");
    $stats['pending_requests'] = (int)$stmt->fetchColumn();
    
    // For release
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM `reg_doc_requests` WHERE `status` = 'For Release'");
    $stats['for_release'] = (int)$stmt->fetchColumn();
    
    // Released today
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM `reg_doc_releases` WHERE DATE(`released_at`) = CURDATE()");
    $stats['released_today'] = (int)$stmt->fetchColumn();
    
    return $stats;
}

/**
 * Get request queue by status
 */
function regGetRequestQueue(string $status = 'Submitted', int $limit = 50): array
{
    global $db;
    
    $stmt = $db->prepare("SELECT r.*, s.`first_name`, s.`last_name`, s.`student_number` 
        FROM `reg_doc_requests` r 
        JOIN `reg_students` s ON r.`student_id` = s.`id`
        WHERE r.`status` = ?
        ORDER BY r.`created_at` ASC
        LIMIT ?");
    $stmt->execute([$status, $limit]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

/**
 * Generate student masterlist
 */
function regGenerateMasterlist(array $filters = []): array
{
    global $db;
    
    $query = "SELECT `student_number`, `first_name`, `middle_name`, `last_name`, 
                     `program_course`, `year_section`, `status`
              FROM `reg_students` WHERE 1=1";
    $params = [];
    
    if (!empty($filters['program'])) {
        $query .= " AND `program_course` = ?";
        $params[] = $filters['program'];
    }
    
    if (!empty($filters['year_section'])) {
        $query .= " AND `year_section` = ?";
        $params[] = $filters['year_section'];
    }
    
    if (!empty($filters['status'])) {
        $query .= " AND `status` = ?";
        $params[] = $filters['status'];
    }
    
    $query .= " ORDER BY `last_name`, `first_name`";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}
?>
