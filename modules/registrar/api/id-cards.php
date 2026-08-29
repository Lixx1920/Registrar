<?php
/**
 * SMS2 - Registrar API: Student ID Cards
 *
 * Actions (GET unless noted):
 *   filter_opts   — JSON dropdown options (courses, year levels, sections, school years, strands)
 *   list          — JSON list of students with their ID card status
 *   preview       — Full card data (front + back) for a single student
 *   create        — POST: create/generate a new ID card record
 *   update_status — POST: update ID card status (Ready / Printed / Released)
 *   bulk_generate — POST: batch status update for selected students
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/api-helpers.php';

$action = trim((string) ($_GET['action'] ?? ($_POST['action'] ?? '')));

match ($action) {
    'filter_opts'   => idcFilterOpts(),
    'list'          => idcList(),
    'preview'       => idcPreview(),
    'create'        => idcCreate(),
    'update_status' => idcUpdateStatus(),
    'bulk_generate' => idcBulkGenerate(),
    default         => regApiJson(['success' => false, 'error' => 'Unknown action.'], 404),
};

/* ============================================================================
   CONSTANTS
   ============================================================================ */

/**
 * Official BCP College programs – kept in sync with the Masterlist module.
 */
function idcCollegePrograms(): array
{
    return [
        'BS Information Technology',
        'BS Hospitality Management',
        'BS Accounting Information System',
        'BS Tourism Management',
        'BS Office Administration',
        'BS Entrepreneurship',
        'BS Business Administration',
        'Bachelor of Library Information Science',
        'BS Computer Engineering',
        'BS Psychology',
        'BS Criminology',
        'BS Physical Education',
        'BS Technological & Livelihood Education',
        'BS Elementary Education',
        'BS Secondary Education',
    ];
}

function idcSHSStrands(): array
{
    return ['STEM', 'ABM', 'HUMSS', 'GAS', 'TVL-ICT', 'TVL-HE'];
}

/* ============================================================================
   filter_opts
   ============================================================================ */
function idcFilterOpts(): void
{
    $db = db();

    // School years from reg_student_ids batch_no or enrollment_date
    $syRows = $db->query(
        "SELECT DISTINCT YEAR(`enrollment_date`) AS yr FROM `reg_students`
         WHERE `enrollment_date` IS NOT NULL AND `status` != 'Deleted'
         ORDER BY yr DESC"
    )->fetchAll(PDO::FETCH_COLUMN);
    $schoolYears = array_map(fn($y) => $y . '-' . ($y + 1), array_filter($syRows));
    if (empty($schoolYears)) {
        // Fallback: last 4 years
        $curY = (int) date('Y');
        for ($y = $curY; $y >= $curY - 3; $y--) {
            $schoolYears[] = $y . '-' . ($y + 1);
        }
    }

    // Sections from year_section field
    $sections = $db->query(
        "SELECT DISTINCT `year_section` FROM `reg_students`
         WHERE `status` != 'Deleted' AND `year_section` IS NOT NULL AND `year_section` != ''
         ORDER BY `year_section`"
    )->fetchAll(PDO::FETCH_COLUMN);

    regApiJson([
        'success'      => true,
        'school_years' => array_values(array_unique($schoolYears)),
        'programs'     => idcCollegePrograms(),
        'strands'      => idcSHSStrands(),
        'sections'     => $sections,
    ]);
}

/* ============================================================================
   list
   ============================================================================ */
function idcList(): void
{
    $db = db();

    $department = trim((string) ($_GET['department'] ?? 'College'));
    $program    = trim((string) ($_GET['program']    ?? ''));
    $yearLevel  = trim((string) ($_GET['year_level'] ?? ''));
    $section    = trim((string) ($_GET['section']    ?? ''));
    $batchYear  = trim((string) ($_GET['batch_year'] ?? ''));
    $idStatus   = trim((string) ($_GET['id_status']  ?? ''));
    $search     = trim((string) ($_GET['q']          ?? ''));

    $where  = ["s.`status` != 'Deleted'"];
    $params = [];

    // Department split: SHS students have year_section starting with "G11" or "G12"
    if ($department === 'SHS') {
        $where[] = "(s.`year_section` LIKE 'G11%' OR s.`year_section` LIKE 'G12%'
                    OR s.`program_course` IN ('STEM','ABM','HUMSS','GAS','TVL-ICT','TVL-HE')
                    OR s.`college_department` LIKE '%Senior High%'
                    OR s.`college_department` LIKE '%SHS%')";
    } else {
        $where[] = "(s.`year_section` NOT LIKE 'G11%' AND s.`year_section` NOT LIKE 'G12%'
                    AND (s.`college_department` NOT LIKE '%Senior High%'
                         OR s.`college_department` IS NULL))";
    }

    if ($program !== '')   { $where[] = 's.`program_course` = ?';    $params[] = $program;   }
    if ($yearLevel !== '') { $where[] = 's.`year_section` LIKE ?';   $params[] = $yearLevel . '%'; }
    if ($section !== '')   { $where[] = 's.`year_section` LIKE ?';   $params[] = '%' . $section;   }
    if ($search !== '') {
        $where[] = "(s.`first_name` LIKE ? OR s.`last_name` LIKE ? OR s.`student_number` LIKE ?)";
        $like = '%' . $search . '%';
        $params[] = $like; $params[] = $like; $params[] = $like;
    }

    $whereSQL = 'WHERE ' . implode(' AND ', $where);

    // Join with reg_student_ids to get ID card status
    $sql = "SELECT s.`id`, s.`student_number`,
                   s.`first_name`, s.`middle_name`, s.`last_name`, s.`suffix`,
                   s.`program_course`, s.`year_section`, s.`college_department`,
                   s.`gender`, s.`status`, s.`enrollment_date`, s.`date_of_birth`,
                   si.`id` AS id_card_id,
                   si.`id_number`, si.`status` AS id_status,
                   si.`batch_no`, si.`printed_at`, si.`released_at`,
                   si.`notes` AS id_notes,
                   si.`created_at` AS id_created_at,
                   CONCAT(si.`id_number`) AS display_id,
                   (SELECT g.`full_name` FROM `reg_guardians` g
                    WHERE g.`student_id` = s.`id` AND g.`is_emergency` = 1
                    LIMIT 1) AS emergency_contact_name,
                   (SELECT g.`contact` FROM `reg_guardians` g
                    WHERE g.`student_id` = s.`id` AND g.`is_emergency` = 1
                    LIMIT 1) AS emergency_contact_phone
            FROM `reg_students` s
            LEFT JOIN `reg_student_ids` si ON si.`student_id` = s.`id`
                AND si.`status` != 'Cancelled'
            $whereSQL
            ORDER BY s.`last_name`, s.`first_name`
            LIMIT 500";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Filter by ID status
    if ($idStatus !== '') {
        $rows = array_values(array_filter($rows, function ($r) use ($idStatus) {
            if ($idStatus === 'Not Created') {
                return $r['id_card_id'] === null;
            }
            return ($r['id_status'] ?? '') === $idStatus;
        }));
    }

    // Attach batch year filter (based on batch_no)
    if ($batchYear !== '') {
        $rows = array_values(array_filter($rows, fn($r) => str_contains((string)($r['batch_no'] ?? ''), $batchYear)));
    }

    // Add computed fields
    foreach ($rows as &$row) {
        $mi = $row['middle_name'] ? ' ' . mb_substr($row['middle_name'], 0, 1) . '.' : '';
        $sf = $row['suffix']      ? ' ' . $row['suffix'] : '';
        $row['full_name'] = trim($row['last_name'] . ', ' . $row['first_name'] . $mi . $sf);
        $row['has_id']    = $row['id_card_id'] !== null;
    }
    unset($row);

    $stats = [
        'total'       => count($rows),
        'ready'       => count(array_filter($rows, fn($r) => ($r['id_status'] ?? '') === 'Ready')),
        'printed'     => count(array_filter($rows, fn($r) => ($r['id_status'] ?? '') === 'Printed')),
        'released'    => count(array_filter($rows, fn($r) => ($r['id_status'] ?? '') === 'Released')),
        'not_created' => count(array_filter($rows, fn($r) => !$r['has_id'])),
    ];

    regApiJson([
        'success' => true,
        'stats'   => $stats,
        'data'    => $rows,
    ]);
}

/* ============================================================================
   preview – full card data for front & back
   ============================================================================ */
function idcPreview(): void
{
    $db        = db();
    $studentId = (int) ($_GET['id'] ?? 0);

    if ($studentId <= 0) {
        regApiJson(['success' => false, 'error' => 'Invalid student id.'], 400);
    }

    $stmt = $db->prepare(
        "SELECT s.*, si.`id` AS id_card_id, si.`id_number`, si.`status` AS id_status,
                si.`batch_no`, si.`notes` AS id_notes, si.`created_at` AS id_created_at,
                si.`printed_at`, si.`released_at`
         FROM `reg_students` s
         LEFT JOIN `reg_student_ids` si ON si.`student_id` = s.`id` AND si.`status` != 'Cancelled'
         WHERE s.`id` = ?
         LIMIT 1"
    );
    $stmt->execute([$studentId]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        regApiJson(['success' => false, 'error' => 'Student not found.'], 404);
    }

    // Guardian / emergency contact (back-side info)
    $gStmt = $db->prepare(
        "SELECT `full_name`, `relationship`, `contact`, `address`
         FROM `reg_guardians`
         WHERE `student_id` = ? AND `is_emergency` = 1
         LIMIT 1"
    );
    $gStmt->execute([$studentId]);
    $emergency = $gStmt->fetch(PDO::FETCH_ASSOC) ?: [];

    // Primary guardian for address if no emergency
    if (empty($emergency)) {
        $gStmt2 = $db->prepare(
            "SELECT `full_name`, `relationship`, `contact`, `address`
             FROM `reg_guardians`
             WHERE `student_id` = ? AND `is_primary` = 1
             LIMIT 1"
        );
        $gStmt2->execute([$studentId]);
        $emergency = $gStmt2->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    // Compute expiry from id_created_at or now
    $createdAt = $student['id_created_at'] ?? date('Y-m-d');
    $expiryDate = date('F Y', strtotime($createdAt . ' +1 year'));

    $mi = $student['middle_name'] ? ' ' . mb_substr($student['middle_name'], 0, 1) . '.' : '';
    $sf = $student['suffix']      ? ' ' . $student['suffix'] : '';

    regApiJson([
        'success' => true,
        'card'    => [
            'student_id'     => $student['id'],
            'id_card_id'     => $student['id_card_id'],
            'id_number'      => $student['id_number'] ?? 'PENDING',
            'id_status'      => $student['id_status'] ?? 'Not Created',
            'full_name'      => trim($student['first_name'] . $mi . ' ' . $student['last_name'] . $sf),
            'last_first'     => trim($student['last_name'] . ', ' . $student['first_name'] . $mi . $sf),
            'student_number' => $student['student_number'],
            'program_course' => $student['program_course'],
            'year_section'   => $student['year_section'],
            'gender'         => $student['gender'],
            'date_of_birth'  => $student['date_of_birth'],
            'expiry_display' => $expiryDate,
            'id_created_at'  => $createdAt,
            'printed_at'     => $student['printed_at'],
            'batch_no'       => $student['batch_no'],
            // back-side
            'emergency_name'     => $emergency['full_name'] ?? '',
            'emergency_relation' => $emergency['relationship'] ?? '',
            'emergency_phone'    => $emergency['contact'] ?? '',
            'emergency_address'  => $emergency['address'] ?? '',
        ],
    ]);
}

/* ============================================================================
   create — POST
   ============================================================================ */
function idcCreate(): void
{
    regApiRequireAccess();
    regApiRequireCsrf();

    $db     = db();
    $userId = (int) ($_SESSION['user_id'] ?? 0);
    $data   = [];

    // Support multipart form data (file upload) or JSON
    if (!empty($_POST)) {
        $data = $_POST;
    } else {
        $data = regApiBody();
    }

    $studentNumber = trim((string) ($data['student_number'] ?? ''));
    $firstName     = trim((string) ($data['first_name']     ?? ''));
    $lastName      = trim((string) ($data['last_name']      ?? ''));
    $middleName    = trim((string) ($data['middle_name']     ?? ''));
    $suffix        = trim((string) ($data['suffix']         ?? ''));
    $programCourse = trim((string) ($data['program_course'] ?? ''));
    $yearSection   = trim((string) ($data['year_section']   ?? ''));
    $batchYear     = trim((string) ($data['batch_year']     ?? date('Y') . '-' . (date('Y') + 1)));
    $emergencyName = trim((string) ($data['emergency_name'] ?? ''));
    $emergencyPhone= trim((string) ($data['emergency_phone']?? ''));
    $address       = trim((string) ($data['address']        ?? ''));
    // Auto-compute expiry 1 year from now
    $expiryDate    = date('Y-m-d', strtotime('+1 year'));

    if ($studentNumber === '' || $firstName === '' || $lastName === '') {
        regApiJson(['success' => false, 'error' => 'Missing required fields: student_number, first_name, last_name.'], 400);
    }

    // Upsert student record
    $stmt = $db->prepare("SELECT `id` FROM `reg_students` WHERE `student_number` = ? LIMIT 1");
    $stmt->execute([$studentNumber]);
    $existingStudent = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingStudent) {
        $studentId = (int) $existingStudent['id'];
        // Update emergency contact if provided
        if ($emergencyName !== '') {
            $gCheck = $db->prepare("SELECT `id` FROM `reg_guardians` WHERE `student_id` = ? AND `is_emergency` = 1 LIMIT 1");
            $gCheck->execute([$studentId]);
            $existingGuardian = $gCheck->fetch(PDO::FETCH_ASSOC);
            if ($existingGuardian) {
                $gUp = $db->prepare("UPDATE `reg_guardians` SET `full_name`=?,`contact`=?,`address`=? WHERE `id`=?");
                $gUp->execute([$emergencyName, $emergencyPhone, $address, $existingGuardian['id']]);
            } else {
                $gIns = $db->prepare("INSERT INTO `reg_guardians` (`student_id`,`full_name`,`relationship`,`contact`,`address`,`is_primary`,`is_emergency`,`created_by`) VALUES (?,?,?,?,?,0,1,?)");
                $gIns->execute([$studentId, $emergencyName, 'Guardian', $emergencyPhone, $address, $userId]);
            }
        }
    } else {
        // Create new student
        $ins = $db->prepare(
            "INSERT INTO `reg_students`
             (`student_number`,`first_name`,`middle_name`,`last_name`,`suffix`,
              `program_course`,`year_section`,`status`,`created_by`)
             VALUES (?,?,?,?,?,?,?,'Active',?)"
        );
        $ins->execute([$studentNumber, $firstName, $middleName, $lastName, $suffix ?: null, $programCourse ?: null, $yearSection ?: null, $userId]);
        $studentId = (int) $db->lastInsertId();

        // Insert emergency contact
        if ($emergencyName !== '') {
            $gIns = $db->prepare("INSERT INTO `reg_guardians` (`student_id`,`full_name`,`relationship`,`contact`,`address`,`is_primary`,`is_emergency`,`created_by`) VALUES (?,?,?,?,?,0,1,?)");
            $gIns->execute([$studentId, $emergencyName, 'Guardian', $emergencyPhone, $address, $userId]);
        }
    }

    // Handle photo upload
    $photoFileId = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        require_once __DIR__ . '/../includes/storage-service.php';
        $upload = regStoreUploadedFile($studentId, 'id_photo', $_FILES['photo'], $userId, 5242880);
        if ($upload['success'] ?? false) {
            $photoFileId = $upload['file_id'];
        }
    }

    // Generate ID number
    $year    = date('Y');
    $seqStmt = $db->query("SELECT COUNT(*) FROM `reg_student_ids` WHERE `id_number` LIKE 'ID{$year}%'");
    $seq     = ((int) $seqStmt->fetchColumn()) + 1;
    $idNumber = 'ID' . $year . str_pad((string) $seq, 5, '0', STR_PAD_LEFT);

    // Cancel any old ID card records for this student
    $db->prepare("UPDATE `reg_student_ids` SET `status`='Cancelled' WHERE `student_id`=?")->execute([$studentId]);

    // Create ID card record
    $ins2 = $db->prepare(
        "INSERT INTO `reg_student_ids`
         (`student_id`,`batch_no`,`template_name`,`photo_file_id`,`id_number`,`status`,`notes`,`created_by`)
         VALUES (?,?,?,?,?,'Ready',?,?)"
    );
    $ins2->execute([
        $studentId,
        'BATCH-' . str_replace('-', '-', $batchYear),
        'standard',
        $photoFileId,
        $idNumber,
        'Created via ID Generation module - Expiry: ' . $expiryDate,
        $userId,
    ]);
    $idCardId = (int) $db->lastInsertId();

    regApiLog('create_id_card', "Created ID card {$idNumber} for student {$studentNumber} (id {$studentId})");

    regApiJson([
        'success'    => true,
        'message'    => 'ID card created successfully.',
        'student_id' => $studentId,
        'id_card_id' => $idCardId,
        'id_number'  => $idNumber,
        'expiry'     => $expiryDate,
    ]);
}

/* ============================================================================
   update_status — POST
   ============================================================================ */
function idcUpdateStatus(): void
{
    regApiRequireAccess();
    regApiRequireCsrf();

    $db     = db();
    $userId = (int) ($_SESSION['user_id'] ?? 0);
    $body   = regApiBody();

    $idCardId = (int) ($body['id_card_id'] ?? 0);
    $status   = trim((string) ($body['status'] ?? ''));

    $allowed = ['Ready', 'Printed', 'Released', 'Cancelled'];
    if ($idCardId <= 0 || !in_array($status, $allowed, true)) {
        regApiJson(['success' => false, 'error' => 'Invalid id_card_id or status.'], 400);
    }

    $extra = '';
    if ($status === 'Printed')  { $extra = ', `printed_at`  = NOW()'; }
    if ($status === 'Released') { $extra = ', `released_at` = NOW(), `released_by` = ' . $userId; }

    $db->prepare("UPDATE `reg_student_ids` SET `status` = ? {$extra}, `updated_at` = NOW() WHERE `id` = ?")->execute([$status, $idCardId]);

    regApiLog('update_id_status', "ID card {$idCardId} → {$status}");
    regApiJson(['success' => true, 'message' => "Status updated to {$status}."]);
}

/* ============================================================================
   bulk_generate — POST
   ============================================================================ */
function idcBulkGenerate(): void
{
    regApiRequireAccess();
    regApiRequireCsrf();

    $db     = db();
    $userId = (int) ($_SESSION['user_id'] ?? 0);
    $body   = regApiBody();

    $studentIds = array_filter(array_map('intval', (array) ($body['student_ids'] ?? [])));
    $targetStatus = trim((string) ($body['status'] ?? 'Printed'));

    if (empty($studentIds)) {
        regApiJson(['success' => false, 'error' => 'No student IDs provided.'], 400);
    }

    $count = 0;
    foreach ($studentIds as $sid) {
        $check = $db->prepare("SELECT `id` FROM `reg_student_ids` WHERE `student_id` = ? AND `status` NOT IN ('Cancelled') LIMIT 1");
        $check->execute([$sid]);
        $row = $check->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $db->prepare("UPDATE `reg_student_ids` SET `status` = ?, `updated_at` = NOW() WHERE `id` = ?")->execute([$targetStatus, $row['id']]);
            $count++;
        }
    }

    regApiLog('bulk_id_status', "Bulk set {$count} ID cards → {$targetStatus}");
    regApiJson(['success' => true, 'message' => "Updated {$count} ID card(s) to {$targetStatus}.", 'updated' => $count]);
}
