<?php
/**
 * SMS 2 - Registrar API: Faculty Academic Clearance Management
 *
 * Integrated clearance verification endpoint connecting the Faculty Subsystem
 * to the Registrar Clearance Office.
 *
 * Actions:
 *   GET  ?action=stats             — counts for Pending, Denied, Approved, Active, Archived
 *   GET  ?action=list              — filterable & paginated list (active vs archived)
 *   GET  ?action=get&id=N          — detailed clearance record & uploaded requirement for Review Modal
 *   POST ?action=review            — approve or deny clearance with remarks
 *   POST ?action=submit_clearance  — endpoint for Faculty subsystem uploads
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/api-helpers.php';

$action = trim((string) ($_GET['action'] ?? ($_POST['action'] ?? '')));

match ($action) {
    'stats'            => fcStats(),
    'list'             => fcList(),
    'get'              => fcGet(),
    'review'           => fcReview(),
    'submit_clearance' => fcSubmitClearance(),
    default            => regApiJson(['success' => false, 'error' => 'Unknown action.'], 404),
};

// ── Action Handlers ───────────────────────────────────────────────────────────

/** Return counts for macro cards and tabs. */
function fcStats(): void
{
    $pdo = db();

    $stmt = $pdo->query(
        "SELECT 
            SUM(CASE WHEN `status` = 'Under Verification' THEN 1 ELSE 0 END) AS pending_count,
            SUM(CASE WHEN `status` = 'Denied' THEN 1 ELSE 0 END) AS denied_count,
            SUM(CASE WHEN `status` = 'Approved' THEN 1 ELSE 0 END) AS approved_count,
            SUM(CASE WHEN `status` = 'Not Submitted' THEN 1 ELSE 0 END) AS not_submitted_count,
            SUM(CASE WHEN `status` != 'Approved' THEN 1 ELSE 0 END) AS active_total,
            SUM(CASE WHEN `status` = 'Approved' THEN 1 ELSE 0 END) AS archived_total
         FROM `reg_faculty_clearances`"
    );
    $stats = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

    regApiJson([
        'success' => true,
        'stats' => [
            'total_faculty' => (int) (($stats['active_total'] ?? 0) + ($stats['archived_total'] ?? 0)),
            'pending'       => (int) ($stats['pending_count'] ?? 0),
            'denied'        => (int) ($stats['denied_count'] ?? 0),
            'approved'      => (int) ($stats['approved_count'] ?? 0),
            'not_submitted' => (int) ($stats['not_submitted_count'] ?? 0),
            'active_total'  => (int) ($stats['active_total'] ?? 0),
            'archived_total'=> (int) ($stats['archived_total'] ?? 0),
        ],
    ]);
}

/** Paginated and filterable list of faculty clearances. */
function fcList(): void
{
    $pdo    = db();
    $page   = max(1, (int) ($_GET['page'] ?? 1));
    $limit  = min(50, max(10, (int) ($_GET['limit'] ?? 20)));
    $offset = ($page - 1) * $limit;

    $view       = trim((string) ($_GET['view'] ?? 'active')); // 'active' or 'archived'
    $status     = trim((string) ($_GET['status'] ?? ''));
    $employment = trim((string) ($_GET['employment'] ?? ''));
    $department = trim((string) ($_GET['department'] ?? ''));
    $q          = trim((string) ($_GET['q'] ?? ''));

    $where  = [];
    $params = [];

    if ($view === 'archived') {
        $where[] = "`status` = 'Approved'";
    } else {
        $where[] = "`status` != 'Approved'";
    }

    if ($status !== '') {
        $where[]  = "`status` = ?";
        $params[] = $status;
    }

    if ($employment !== '') {
        $where[]  = "`employment_status` = ?";
        $params[] = $employment;
    }

    if ($department !== '') {
        $where[]  = "`department` = ?";
        $params[] = $department;
    }

    if ($q !== '') {
        $like = "%$q%";
        $where[]  = "(`faculty_name` LIKE ? OR `faculty_id` LIKE ? OR `email` LIKE ?)";
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }

    $whereClause = 'WHERE ' . implode(' AND ', $where);

    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM `reg_faculty_clearances` $whereClause");
    $stmtCount->execute($params);
    $total = (int) $stmtCount->fetchColumn();

    $stmtData = $pdo->prepare(
        "SELECT `id`, `faculty_id`, `faculty_name`, `email`, `department`,
                `employment_status`, `contract_expiry`, `academic_year`, `semester`,
                `requirement_name`, `file_path`, `file_name`, `file_size`,
                `status`, `submitted_at`, `reviewed_at`, `remarks`,
                TIMESTAMPDIFF(DAY, CURDATE(), `contract_expiry`) AS days_remaining
         FROM `reg_faculty_clearances`
         $whereClause
         ORDER BY 
            CASE 
                WHEN `status` = 'Under Verification' THEN 1
                WHEN `status` = 'Denied' THEN 2
                WHEN `status` = 'Not Submitted' THEN 3
                ELSE 4
            END,
            `faculty_name` ASC
         LIMIT ? OFFSET ?"
    );
    $stmtData->execute(array_merge($params, [$limit, $offset]));
    $rows = $stmtData->fetchAll(PDO::FETCH_ASSOC);

    // Format fields for UI rendering
    foreach ($rows as &$r) {
        $r['contract_label'] = _fcFormatContract($r['contract_expiry'], isset($r['days_remaining']) ? (int)$r['days_remaining'] : null);
        $r['submitted_label'] = !empty($r['submitted_at']) ? date('n/j/Y', strtotime($r['submitted_at'])) : '—';
        $r['progress_percent'] = ($r['status'] === 'Approved') ? 100 : 0;
        $r['progress_ratio']   = ($r['status'] === 'Approved') ? '100% (1/1)' : '0% (0/1)';
        $r['has_file']         = !empty($r['file_path']) && file_exists(ROOT_PATH . '/' . $r['file_path']);
        $r['file_url']         = !empty($r['file_path']) ? BASE_URL . '/' . $r['file_path'] : '';
    }
    unset($r);

    regApiJson([
        'success'    => true,
        'data'       => $rows,
        'pagination' => [
            'page'  => $page,
            'limit' => $limit,
            'total' => $total,
            'pages' => (int) ceil($total / $limit),
        ],
    ]);
}

/** Retrieve full record for Review Clearance Modal. */
function fcGet(): void
{
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        regApiJson(['success' => false, 'error' => 'Invalid clearance ID.'], 400);
    }

    $pdo = db();
    $stmt = $pdo->prepare(
        "SELECT c.*, TIMESTAMPDIFF(DAY, CURDATE(), c.`contract_expiry`) AS days_remaining,
                COALESCE(u.`full_name`, u.`username`) AS reviewer_name
         FROM `reg_faculty_clearances` c
         LEFT JOIN `users` u ON u.`id` = c.`reviewed_by`
         WHERE c.`id` = ? LIMIT 1"
    );
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        regApiJson(['success' => false, 'error' => 'Faculty clearance record not found.'], 404);
    }

    $row['contract_label'] = _fcFormatContract($row['contract_expiry'], isset($row['days_remaining']) ? (int)$row['days_remaining'] : null);
    $row['submitted_label'] = !empty($row['submitted_at']) ? date('M j, Y h:i A', strtotime($row['submitted_at'])) : 'Not yet submitted';
    $row['reviewed_label']  = !empty($row['reviewed_at']) ? date('M j, Y h:i A', strtotime($row['reviewed_at'])) : '—';
    $row['progress_percent'] = ($row['status'] === 'Approved') ? 100 : 0;
    $row['progress_ratio']   = ($row['status'] === 'Approved') ? '100% (1/1)' : '0% (0/1)';
    $row['has_file']         = !empty($row['file_path']) && file_exists(ROOT_PATH . '/' . $row['file_path']);
    $row['file_url']         = !empty($row['file_path']) ? BASE_URL . '/' . $row['file_path'] : '';

    // Scope checklist defined by institution
    $scopeItems = [
        'Grade sheets submitted and finalized',
        'Class records & grading sheets completed',
        'Course syllabi & instructional materials submitted',
        'Attendance records & DTR completed',
        'Pending student academic concerns resolved',
        'Verified directly by administrative office',
    ];

    regApiJson([
        'success'   => true,
        'clearance' => $row,
        'scope'     => $scopeItems,
    ]);
}

/** Submit review action (Approve or Deny). */
function fcReview(): void
{
    regApiRequireAccess();
    regApiRequireCsrf();

    $body     = regApiBody();
    $id       = (int) ($body['id'] ?? 0);
    $decision = trim((string) ($body['decision'] ?? ''));
    $remarks  = trim((string) ($body['remarks'] ?? ''));
    $userId   = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;

    if ($id <= 0 || !in_array($decision, ['Approved', 'Denied'], true)) {
        regApiJson(['success' => false, 'error' => 'Invalid review parameters.'], 400);
    }

    // Denial requires remarks so faculty knows what deficiency to fix
    if ($decision === 'Denied' && $remarks === '') {
        regApiJson(['success' => false, 'error' => 'Please provide specific remarks explaining why this clearance was denied and what needs resubmission.'], 400);
    }

    if ($decision === 'Approved' && $remarks === '') {
        $remarks = 'Requirement approved.';
    }

    $pdo = db();
    $stmt = $pdo->prepare("SELECT `id`, `faculty_name`, `faculty_id`, `status` FROM `reg_faculty_clearances` WHERE `id` = ?");
    $stmt->execute([$id]);
    $fc = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$fc) {
        regApiJson(['success' => false, 'error' => 'Clearance record not found.'], 404);
    }

    $pdo->prepare(
        "UPDATE `reg_faculty_clearances` 
         SET `status` = ?, `remarks` = ?, `reviewed_by` = ?, `reviewed_at` = NOW()
         WHERE `id` = ?"
    )->execute([$decision, $remarks, $userId, $id]);

    $logMsg = sprintf(
        "Faculty clearance for %s (%s) set to %s. Remarks: %s",
        $fc['faculty_name'],
        $fc['faculty_id'],
        $decision,
        $remarks
    );
    regApiLog('review_faculty_clearance', $logMsg);

    regApiJson([
        'success' => true,
        'message' => "Clearance requirement for <strong>" . htmlspecialchars($fc['faculty_name']) . "</strong> has been " . 
                     ($decision === 'Approved' ? "<strong class='text-success'>Approved</strong>." : "<strong class='text-danger'>Denied</strong> and flagged for resubmission."),
        'decision' => $decision,
        'remarks'  => $remarks,
    ]);
}

/** Endpoint for receiving uploads from the Faculty Subsystem. */
function fcSubmitClearance(): void
{
    $facultyId = trim((string) ($_POST['faculty_id'] ?? ''));
    if ($facultyId === '') {
        regApiJson(['success' => false, 'error' => 'Missing faculty ID.'], 400);
    }

    if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        regApiJson(['success' => false, 'error' => 'No valid document uploaded.'], 400);
    }

    $file = $_FILES['file'];
    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'pdf') {
        regApiJson(['success' => false, 'error' => 'Only PDF documents (max 10 MB) are accepted.'], 400);
    }

    if ($file['size'] > 10 * 1024 * 1024) {
        regApiJson(['success' => false, 'error' => 'File size exceeds 10 MB limit.'], 400);
    }

    $uploadDir = ROOT_PATH . '/uploads/clearances/faculty';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $safeName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $file['name']);
    $uniqueName = 'clearance_' . strtolower($facultyId) . '_' . time() . '_' . $safeName;
    $targetPath = $uploadDir . '/' . $uniqueName;
    $relPath    = 'uploads/clearances/faculty/' . $uniqueName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        regApiJson(['success' => false, 'error' => 'Failed to save uploaded file.'], 500);
    }

    $pdo = db();
    $stmt = $pdo->prepare("SELECT `id` FROM `reg_faculty_clearances` WHERE `faculty_id` = ? LIMIT 1");
    $stmt->execute([$facultyId]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $pdo->prepare(
            "UPDATE `reg_faculty_clearances`
             SET `file_path` = ?, `file_name` = ?, `file_size` = ?, `status` = 'Under Verification',
                 `submitted_at` = NOW(), `remarks` = NULL
             WHERE `id` = ?"
        )->execute([$relPath, $file['name'], $file['size'], $existing['id']]);
    } else {
        $facultyName = trim((string) ($_POST['faculty_name'] ?? 'Faculty Member'));
        $department  = trim((string) ($_POST['department'] ?? 'BSIT'));
        $pdo->prepare(
            "INSERT INTO `reg_faculty_clearances` 
             (`faculty_id`, `faculty_name`, `department`, `file_path`, `file_name`, `file_size`, `status`, `submitted_at`)
             VALUES (?, ?, ?, ?, ?, ?, 'Under Verification', NOW())"
        )->execute([$facultyId, $facultyName, $department, $relPath, $file['name'], $file['size']]);
    }

    regApiJson([
        'success'   => true,
        'message'   => 'Academic clearance document submitted successfully to the Registrar.',
        'file_name' => $file['name'],
        'status'    => 'Under Verification',
    ]);
}

// ── Helpers ───────────────────────────────────────────────────────────────────

function _fcFormatContract(?string $dateStr, ?int $daysRem): string
{
    if (!$dateStr) {
        return 'Not set';
    }
    $d = date('M j, Y', strtotime($dateStr));
    if ($daysRem === null) {
        return $d;
    }
    if ($daysRem < 0) {
        return "$d · <span class='text-danger fw-bold'>Expired</span>";
    }
    if ($daysRem <= 30) {
        return "$d · <span class='text-danger fw-bold'>$daysRem days remaining</span>";
    }
    if ($daysRem <= 90) {
        return "$d · <span class='text-warning fw-semibold'>$daysRem days remaining</span>";
    }
    return "$d · <span class='text-muted'>$daysRem days remaining</span>";
}

