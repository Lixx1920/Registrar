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
    $db = db();
    
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
            
            regLog('update_student', "Student $studentId updated", $userId);
            
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
            regLog('create_student', "New student created: {$data['student_number']}", $userId);
            
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
    $db = db();
    
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
    $db = db();
    
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
function regCreateDocumentRequest(
    int $studentId,
    array $docTypes,
    string $purpose,
    string $channel = 'walk-in',
    int $userId,
    int $paid = 0,
    ?string $paymentRef = null,
    ?string $studentEmail = null
): array
{
    $db = db();

    try {
        // Create request
        $requestNo = regGenerateDocumentNumber('REQ_NO');
        $stmt = $db->prepare("INSERT INTO `reg_doc_requests`
            (`request_no`, `student_id`, `purpose`, `channel`, `student_email`, `paid`, `payment_ref`, `status`, `requested_by`, `created_by`)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Submitted', ?, ?)");
        $stmt->execute([$requestNo, $studentId, $purpose, $channel, $studentEmail, $paid, $paymentRef, $userId, $userId]);
        $requestId = (int)$db->lastInsertId();

        // Create request items
        foreach ($docTypes as $docType) {
            $stmt = $db->prepare("INSERT INTO `reg_doc_request_items`
                (`request_id`, `doc_type`, `copies`, `status`)
                VALUES (?, ?, ?, 'Pending')");
            $stmt->execute([$requestId, $docType, 1]);
        }

        regLog('create_request', "Document request $requestNo created for student $studentId", $userId);

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
    $db = db();

    $validStatuses = ['Submitted', 'For Review', 'Processing', 'For Release', 'Released', 'Cancelled'];
    if (!in_array($newStatus, $validStatuses, true)) {
        return ['success' => false, 'error' => "Invalid status: $newStatus"];
    }

    try {
        $stmt = $db->prepare("UPDATE `reg_doc_requests` SET `status` = ?, `updated_at` = NOW() WHERE `id` = ?");
        $stmt->execute([$newStatus, $requestId]);

        regLog('update_request_status', "Request $requestId status: $newStatus", $userId);
        
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
    $db = db();

    // Fetch request item with request details
    $stmt = $db->prepare("SELECT i.*, r.`student_id`, r.`request_no` FROM `reg_doc_request_items` i
        JOIN `reg_doc_requests` r ON i.`request_id` = r.`id`
        WHERE i.`id` = ?");
    $stmt->execute([$itemId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        return ['success' => false, 'error' => 'Request item not found'];
    }

    // Generate verification code
    $verCode = strtoupper(substr(bin2hex(random_bytes(6)), 0, 12));
    
    // Fetch registrar user data
    $stmt = $db->prepare("SELECT full_name, digital_signature FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $registrar = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['full_name' => 'AUTHORIZED SIGNATORY', 'digital_signature' => null];

    // Generate metadata payload and RSA signature
    $metadataPayload = "VER:$verCode|DOC:$docType|STU:{$item['student_id']}|DATE:" . date('Y-m-d H:i:s');
    require_once __DIR__ . '/signing-service.php';
    $signResult = regSignPayload($metadataPayload);
    $rsaSignature = $signResult['success'] ? $signResult['signature'] : 'UNAVAILABLE';
    
    $options = [
        'verification_code' => $verCode,
        'registrar_name' => $registrar['full_name'],
        'registrar_signature' => $registrar['digital_signature'],
        'rsa_signature' => $rsaSignature
    ];

    // Generate PDF based on document type
    $docResult = match ($docType) {
        'Form 137', 'FORM137' => regGenerateForm137($item['student_id'], $options),
        'Good Moral', 'GMC', 'Good Moral Certificate' => regGenerateGoodMoral($item['student_id'], $options),
        'TOR' => regGenerateForm137($item['student_id'], $options),
        'COE', 'Certificate of Enrollment' => regGenerateCertification($item['student_id'], 'Certificate of Enrollment', $options),
        'COG', 'Certificate of Grades' => regGenerateCertification($item['student_id'], 'Certificate of Grades', $options),
        'Diploma', 'Diploma Copy' => regGenerateCertification($item['student_id'], 'Diploma Copy', $options),
        'Honorable Dismissal' => regGenerateCertification($item['student_id'], 'Honorable Dismissal', $options),
        default => ['success' => false, 'error' => "Unknown document type: $docType"]
    };

    if (!$docResult['success']) {
        return $docResult;
    }

    // Compute file hash
    $fileHash = hash_file('sha256', $docResult['pdf_path']);

    $verResult = regCreateVerification($fileHash, $docType, $item['student_id'], $metadataPayload, $verCode);
    if (!$verResult['success']) {
        return $verResult;
    }

    // Update request item
    try {
        $stmt = $db->prepare("UPDATE `reg_doc_request_items`
            SET `generated_file_id` = ?, `verification_code_id` = ?, `status` = 'Generated'
            WHERE `id` = ?");
        $stmt->execute([$docResult['file_id'], $verResult['code_id'], $itemId]);

        // Automatically set parent request to For Release if it was Processing
        $stmt = $db->prepare("UPDATE `reg_doc_requests` SET `status` = 'For Release' WHERE `id` = ? AND `status` = 'Processing'");
        $stmt->execute([$item['request_id']]);

        regLog('generate_document', "Generated $docType: {$verResult['code']}", $userId);

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
 * Generate a document preview (temporary PDF, no signatures, no DB changes)
 */
function regPreviewRequestDocument(int $itemId, string $docType, int $userId): array
{
    $db = db();

    // Fetch request item with request details
    $stmt = $db->prepare("SELECT i.*, r.`student_id`, r.`request_no` FROM `reg_doc_request_items` i
        JOIN `reg_doc_requests` r ON i.`request_id` = r.`id`
        WHERE i.`id` = ?");
    $stmt->execute([$itemId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        return ['success' => false, 'error' => 'Request item not found'];
    }

    // Generate PDF based on document type with temp verification code and preview flag
    $options = [
        'verification_code' => 'PREVIEW-ONLY',
        'preview' => true
    ];
    
    $docResult = match ($docType) {
        'Form 137', 'FORM137' => regGenerateForm137($item['student_id'], $options),
        'Good Moral', 'GMC', 'Good Moral Certificate' => regGenerateGoodMoral($item['student_id'], $options),
        'TOR' => regGenerateForm137($item['student_id'], $options),
        'COE', 'Certificate of Enrollment' => regGenerateCertification($item['student_id'], 'Certificate of Enrollment', $options),
        'COG', 'Certificate of Grades' => regGenerateCertification($item['student_id'], 'Certificate of Grades', $options),
        'Diploma', 'Diploma Copy' => regGenerateCertification($item['student_id'], 'Diploma Copy', $options),
        'Honorable Dismissal' => regGenerateCertification($item['student_id'], 'Honorable Dismissal', $options),
        default => ['success' => false, 'error' => "Unknown document type: $docType"]
    };

    if (!$docResult['success']) {
        return $docResult;
    }

    return [
        'success' => true,
        'pdf_path' => $docResult['pdf_path']
    ];
}

/**
 * Release a document to claimant (creates release record)
 */
function regReleaseDocument(int $itemId, string $claimantName, ?string $claimantId, int $releasedBy): array
{
    $db = db();
    
    try {
        $releaseNo = regGenerateDocumentNumber('RELEASE_NO');
        
        $stmt = $db->prepare("INSERT INTO `reg_doc_releases` 
            (`request_item_id`, `claim_type`, `release_slip_no`, `released_by`, `claimant_name`, `claimant_id`)
            VALUES (?, 'walk-in', ?, ?, ?, ?)");
        $stmt->execute([$itemId, $releaseNo, $releasedBy, $claimantName, $claimantId]);
        
        // Update item status
        $stmt = $db->prepare("UPDATE `reg_doc_request_items` SET `status` = 'Released', `released_at` = NOW() WHERE `id` = ?");
        $stmt->execute([$itemId]);
        
        regLog('release_document', "Released: $releaseNo", $releasedBy);
        
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
    $db = db();
    
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
    $db = db();
    
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
    $db = db();
    
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

/* ============================================================================
   ACADEMIC GRADE HISTORY & GWA CALCULATION
   ============================================================================ */

/**
 * Calculate GWA (General Weighted Average) and unit breakdown for a set of subjects
 */
function regCalculateGwa(array $subjects): array
{
    $totalUnits    = 0.0;
    $gradedUnits   = 0.0;
    $passedUnits   = 0.0;
    $failedUnits   = 0.0;
    $incUnits      = 0.0;
    $drpUnits      = 0.0;
    $enrolledUnits = 0.0;
    $totalPoints   = 0.0;

    foreach ($subjects as $s) {
        $units = (float)($s['units'] ?? 0.0);
        $totalUnits += $units;

        $gradeRaw = trim((string)($s['grade'] ?? ''));
        $status   = trim((string)($s['status'] ?? ''));

        // Check if grade is numeric (1.00 - 5.00)
        if (is_numeric($gradeRaw) && (float)$gradeRaw > 0) {
            $gradeVal = (float)$gradeRaw;
            $gradedUnits += $units;
            $totalPoints += ($gradeVal * $units);

            if ($gradeVal <= 3.00) {
                $passedUnits += $units;
            } else {
                $failedUnits += $units;
            }
        } elseif (strcasecmp($gradeRaw, 'INC') === 0 || strcasecmp($status, 'Incomplete') === 0) {
            $incUnits += $units;
        } elseif (strcasecmp($gradeRaw, 'DRP') === 0 || strcasecmp($status, 'Dropped') === 0) {
            $drpUnits += $units;
        } elseif (strcasecmp($status, 'Enrolled') === 0 || strcasecmp($status, 'Ongoing') === 0) {
            $enrolledUnits += $units;
        } elseif (strcasecmp($status, 'Passed') === 0 || strcasecmp($gradeRaw, 'P') === 0) {
            $passedUnits += $units;
        }
    }

    $gwa = null;
    $gwaFormatted = '—';
    if ($gradedUnits > 0) {
        $gwa = round($totalPoints / $gradedUnits, 2);
        $gwaFormatted = number_format($gwa, 2);
    }

    // Determine Academic Standing
    $standing = 'Good Standing';
    if ($gwa !== null) {
        if ($gwa <= 1.25 && $failedUnits == 0 && $incUnits == 0) {
            $standing = "President's Lister";
        } elseif ($gwa <= 1.75 && $failedUnits == 0 && $incUnits == 0) {
            $standing = "Dean's Lister";
        } elseif ($gwa <= 3.00 && $failedUnits == 0) {
            $standing = "Good Standing";
        } elseif ($failedUnits > 0 || $gwa > 3.00) {
            $standing = "Academic Warning";
        }
    }

    return [
        'gwa'             => $gwa,
        'gwa_formatted'   => $gwaFormatted,
        'total_units'     => $totalUnits,
        'graded_units'    => $gradedUnits,
        'passed_units'    => $passedUnits,
        'failed_units'    => $failedUnits,
        'inc_units'       => $incUnits,
        'drp_units'       => $drpUnits,
        'enrolled_units'  => $enrolledUnits,
        'total_points'    => $totalPoints,
        'standing'        => $standing,
        'subject_count'   => count($subjects)
    ];
}

/**
 * Get structured semester-by-semester collegiate grade history for a student
 * Chronologically sorted from 1st Year 1st Sem to Current Term.
 */
function regGetStudentGradeHistory(int $studentId): array
{
    $db = db();

    $stmt = $db->prepare("
        SELECT * FROM `reg_academic_subjects`
        WHERE `student_id` = ?
        ORDER BY `id` ASC
    ");
    $stmt->execute([$studentId]);
    $rawSubjects = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    if (empty($rawSubjects)) {
        return [
            'terms'   => [],
            'summary' => [
                'cumulative_gwa'       => null,
                'cumulative_gwa_fmt'   => '—',
                'total_units_enrolled' => 0.0,
                'total_units_passed'   => 0.0,
                'total_units_failed'   => 0.0,
                'total_inc'            => 0,
                'academic_standing'    => 'No Records',
                'total_subjects'       => 0,
                'terms_count'          => 0,
            ]
        ];
    }

    // Helper rank for year levels
    $yearRank = function (string $yl): int {
        $y = strtolower(trim($yl));
        if (str_contains($y, '1st') || str_contains($y, '1')) return 1;
        if (str_contains($y, '2nd') || str_contains($y, '2')) return 2;
        if (str_contains($y, '3rd') || str_contains($y, '3')) return 3;
        if (str_contains($y, '4th') || str_contains($y, '4')) return 4;
        if (str_contains($y, '5th') || str_contains($y, '5')) return 5;
        return 99;
    };

    // Helper rank for terms
    $termRank = function (string $t): int {
        $tm = strtolower(trim($t));
        if (str_contains($tm, '1st') || str_starts_with($tm, '1')) return 1;
        if (str_contains($tm, '2nd') || str_starts_with($tm, '2')) return 2;
        if (str_contains($tm, '3rd') || str_starts_with($tm, '3')) return 3;
        if (str_contains($tm, 'sum') || str_contains($tm, 'mid')) return 4;
        return 99;
    };

    // Group subjects into distinct terms
    $grouped = [];
    foreach ($rawSubjects as $subj) {
        $yl = !empty($subj['year_level']) ? trim($subj['year_level']) : '1st Year';
        $tm = !empty($subj['term']) ? trim($subj['term']) : '1st';
        $ay = !empty($subj['academic_year']) ? trim($subj['academic_year']) : date('Y') . '-' . (date('Y') + 1);

        // Normalize term name for display (e.g., '1st Sem', '2nd Sem', 'Summer')
        $tmDisplay = $tm;
        if (!str_ends_with(strtolower($tmDisplay), 'sem') && !str_ends_with(strtolower($tmDisplay), 'summer')) {
            $tmDisplay .= ' Sem';
        }

        $termKey = $yl . '|' . $tm . '|' . $ay;

        if (!isset($grouped[$termKey])) {
            $grouped[$termKey] = [
                'key'           => $termKey,
                'year_level'    => $yl,
                'term'          => $tm,
                'term_display'  => $tmDisplay,
                'academic_year' => $ay,
                'year_rank'     => $yearRank($yl),
                'term_rank'     => $termRank($tm),
                'ay_start'      => (int)substr($ay, 0, 4),
                'subjects'      => []
            ];
        }

        $grouped[$termKey]['subjects'][] = $subj;
    }

    // Sort terms chronologically: AY Start ASC -> Year Level Rank ASC -> Term Rank ASC
    usort($grouped, function ($a, $b) {
        if ($a['ay_start'] !== $b['ay_start']) {
            return $a['ay_start'] <=> $b['ay_start'];
        }
        if ($a['year_rank'] !== $b['year_rank']) {
            return $a['year_rank'] <=> $b['year_rank'];
        }
        return $a['term_rank'] <=> $b['term_rank'];
    });

    // Calculate term-by-term statistics
    $terms = [];
    foreach ($grouped as $g) {
        $termStats = regCalculateGwa($g['subjects']);
        $g['stats'] = $termStats;
        $terms[] = $g;
    }

    // Calculate cumulative overall stats
    $overallStats = regCalculateGwa($rawSubjects);

    return [
        'terms'   => $terms,
        'summary' => [
            'cumulative_gwa'       => $overallStats['gwa'],
            'cumulative_gwa_fmt'   => $overallStats['gwa_formatted'],
            'total_units_enrolled' => $overallStats['total_units'],
            'total_units_passed'   => $overallStats['passed_units'],
            'total_units_failed'   => $overallStats['failed_units'],
            'total_inc'            => (int)($overallStats['inc_units'] > 0 ? 1 : 0),
            'academic_standing'    => $overallStats['standing'],
            'total_subjects'       => $overallStats['subject_count'],
            'terms_count'          => count($terms),
        ]
    ];
}

/**
 * Create or update a collegiate subject grade
 */
function regSaveAcademicSubject(array $data, int $userId): array
{
    $db = db();

    $studentId    = (int)($data['student_id'] ?? 0);
    $subjectCode  = trim((string)($data['subject_code'] ?? ''));
    $subjectName  = trim((string)($data['subject_name'] ?? ''));
    $units        = (float)($data['units'] ?? 3.0);
    $yearLevel    = trim((string)($data['year_level'] ?? '1st Year'));
    $term         = trim((string)($data['term'] ?? '1st'));
    $academicYear = trim((string)($data['academic_year'] ?? date('Y') . '-' . (date('Y') + 1)));
    $grade        = isset($data['grade']) ? trim((string)$data['grade']) : null;
    $instructor   = isset($data['instructor']) ? trim((string)$data['instructor']) : null;
    $remarks      = isset($data['remarks']) ? trim((string)$data['remarks']) : null;
    $status       = isset($data['status']) ? trim((string)$data['status']) : null;

    if ($studentId <= 0 || $subjectCode === '' || $subjectName === '') {
        return ['success' => false, 'error' => 'Student ID, Subject Code, and Subject Name are required.'];
    }

    // Auto-infer status if not explicitly provided
    if (empty($status)) {
        if ($grade === null || $grade === '') {
            $status = 'Enrolled';
        } elseif (strcasecmp($grade, 'INC') === 0) {
            $status = 'Incomplete';
        } elseif (strcasecmp($grade, 'DRP') === 0) {
            $status = 'Dropped';
        } elseif (is_numeric($grade)) {
            $status = ((float)$grade <= 3.00) ? 'Passed' : 'Failed';
        } else {
            $status = 'Passed';
        }
    }

    if (empty($remarks)) {
        $remarks = $status;
    }

    try {
        if (!empty($data['id'])) {
            $id = (int)$data['id'];
            $stmt = $db->prepare("UPDATE `reg_academic_subjects` SET
                `student_id`    = ?,
                `subject_code`  = ?,
                `subject_name`  = ?,
                `units`         = ?,
                `year_level`    = ?,
                `term`          = ?,
                `academic_year` = ?,
                `grade`         = ?,
                `remarks`       = ?,
                `status`        = ?,
                `instructor`    = ?
                WHERE `id` = ?");
            $stmt->execute([
                $studentId, $subjectCode, $subjectName, $units,
                $yearLevel, $term, $academicYear,
                $grade, $remarks, $status, $instructor,
                $id
            ]);

            regLog('update_subject', "Updated subject $subjectCode for student $studentId", $userId);
            return ['success' => true, 'id' => $id, 'action' => 'updated'];
        } else {
            $stmt = $db->prepare("INSERT INTO `reg_academic_subjects`
                (`student_id`, `subject_code`, `subject_name`, `units`, `year_level`, `term`, `academic_year`, `grade`, `remarks`, `status`, `instructor`, `created_by`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $studentId, $subjectCode, $subjectName, $units,
                $yearLevel, $term, $academicYear,
                $grade, $remarks, $status, $instructor, $userId
            ]);
            $newId = (int)$db->lastInsertId();

            regLog('create_subject', "Added subject $subjectCode for student $studentId", $userId);
            return ['success' => true, 'id' => $newId, 'action' => 'created'];
        }
    } catch (Throwable $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Delete a collegiate subject grade
 */
function regDeleteAcademicSubject(int $id, int $userId): array
{
    $db = db();
    try {
        $stmt = $db->prepare("DELETE FROM `reg_academic_subjects` WHERE `id` = ?");
        $stmt->execute([$id]);
        regLog('delete_subject', "Deleted subject record #$id", $userId);
        return ['success' => true];
    } catch (Throwable $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}
?>
