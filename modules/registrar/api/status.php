<?php
/**
 * SMS 2 - Registrar API: Student Status Tracker
 *
 * Actions:
 *   GET  ?action=recent_changes — status transition audit ledger across all students
 *   GET  ?action=list           — paginated student directory with status & LOA residency metrics
 *   GET  ?action=search         — quick search for student picker / autocomplete
 *   GET  ?action=history&id=N   — status-change timeline for one student
 *   POST ?action=change_status  — update a student's status + record statutory audit history
 *   GET  ?action=stats          — aggregated registrar KPIs & status distribution counts
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/api-helpers.php';

// Ensure the status history log table and extended columns exist (idempotent).
_stEnsureHistoryTable();

// ── Dispatch ──────────────────────────────────────────────────────────────────
$action = trim((string) ($_GET['action'] ?? ($_POST['action'] ?? '')));

match ($action) {
    'recent_changes' => stRecentChanges(),
    'list'           => stList(),
    'search'         => stSearch(),
    'history'        => stHistory(),
    'change_status'  => stChangeStatus(),
    'stats'          => stStats(),
    default          => regApiJson(['success' => false, 'error' => 'Unknown action.'], 404),
};

// ── Action handlers ───────────────────────────────────────────────────────────

/**
 * Status Transition Audit Ledger across all students.
 * Chronological ledger showing who changed status, reason, reference number, effective term, and officer.
 */
function stRecentChanges(): void
{
    $db     = db();
    $page   = max(1, (int) ($_GET['page'] ?? 1));
    $limit  = min(50, max(10, (int) ($_GET['limit'] ?? 20)));
    $offset = ($page - 1) * $limit;

    $statusTo = trim((string) ($_GET['status_to'] ?? ''));
    $category = trim((string) ($_GET['category']  ?? ''));
    $q        = trim((string) ($_GET['q']         ?? ''));

    $where  = ['1=1'];
    $params = [];

    if ($statusTo !== '') {
        $where[]  = 'h.`changed_to` = ?';
        $params[] = $statusTo;
    }
    if ($category !== '') {
        $where[]  = 'h.`reason_category` = ?';
        $params[] = $category;
    }
    if ($q !== '') {
        $like = "%$q%";
        $where[]  = '(s.`student_number` LIKE ? OR s.`first_name` LIKE ? OR s.`last_name` LIKE ? OR h.`ref_no` LIKE ? OR h.`reason` LIKE ?)';
        $params[] = $like; $params[] = $like; $params[] = $like; $params[] = $like; $params[] = $like;
    }

    $whereClause = 'WHERE ' . implode(' AND ', $where);

    $stmtCount = $db->prepare(
        "SELECT COUNT(*) FROM `reg_status_history` h
         JOIN `reg_students` s ON s.`id` = h.`student_id`
         $whereClause"
    );
    $stmtCount->execute($params);
    $total = (int) $stmtCount->fetchColumn();

    $stmtData = $db->prepare(
        "SELECT h.`id`, h.`student_id`, h.`changed_from`, h.`changed_to`,
                h.`reason`, h.`reason_category`, h.`ref_no`, h.`effective_term`,
                h.`notes`, h.`changed_at`,
                s.`student_number`, s.`first_name`, s.`last_name`, s.`middle_name`,
                s.`program_course`, s.`year_section`, s.`avatar_url`,
                COALESCE(u.`full_name`, u.`username`) AS changed_by_name
         FROM `reg_status_history` h
         JOIN `reg_students` s ON s.`id` = h.`student_id`
         LEFT JOIN `users` u ON u.`id` = h.`changed_by`
         $whereClause
         ORDER BY h.`changed_at` DESC
         LIMIT ? OFFSET ?"
    );
    $stmtData->execute(array_merge($params, [$limit, $offset]));
    $rows = $stmtData->fetchAll(PDO::FETCH_ASSOC);

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

/** Paginated student registry, optionally filtered by status / program / search. */
function stList(): void
{
    $db      = db();
    $page    = max(1, (int) ($_GET['page'] ?? 1));
    $limit   = min(50, max(10, (int) ($_GET['limit'] ?? 20)));
    $offset  = ($page - 1) * $limit;

    $status  = trim((string) ($_GET['status']  ?? ''));
    $program = trim((string) ($_GET['program'] ?? ''));
    $q       = trim((string) ($_GET['q']       ?? ''));

    $where  = ["s.`status` NOT IN ('Pending', 'Verified', 'Deleted')"];
    $params = [];

    if ($status !== '') {
        $where[]  = 's.`status` = ?';
        $params[] = $status;
    }
    if ($program !== '') {
        $where[]  = 's.`program_course` = ?';
        $params[] = $program;
    }
    if ($q !== '') {
        $like = "%$q%";
        $where[]  = '(s.`student_number` LIKE ? OR s.`first_name` LIKE ? OR s.`last_name` LIKE ?)';
        $params[] = $like; $params[] = $like; $params[] = $like;
    }

    $whereClause = 'WHERE ' . implode(' AND ', $where);

    $stmtCount = $db->prepare("SELECT COUNT(*) FROM `reg_students` s $whereClause");
    $stmtCount->execute($params);
    $total = (int) $stmtCount->fetchColumn();

    $stmtData = $db->prepare(
        "SELECT s.`id`, s.`student_number`, s.`first_name`, s.`last_name`,
                s.`middle_name`, s.`program_course`, s.`year_section`, s.`status`,
                s.`avatar_url`, s.`updated_at`,
                sh.`changed_to` AS last_status,
                sh.`changed_at` AS last_changed_at,
                sh.`reason` AS last_reason,
                sh.`reason_category` AS last_reason_category,
                sh.`ref_no` AS last_ref_no,
                sh.`effective_term` AS last_effective_term
         FROM `reg_students` s
         LEFT JOIN `reg_status_history` sh ON sh.`id` = (
             SELECT MAX(`id`) FROM `reg_status_history` WHERE `student_id` = s.`id`
         )
         $whereClause
         ORDER BY s.`last_name`, s.`first_name`
         LIMIT ? OFFSET ?"
    );
    $stmtData->execute(array_merge($params, [$limit, $offset]));
    $rows = $stmtData->fetchAll(PDO::FETCH_ASSOC);

    // Compute LOA residency duration and remaining allowable time (CHED CMO 40 s. 2008 1-year max)
    $now = new DateTimeImmutable();
    foreach ($rows as &$r) {
        $r['loa_days'] = null;
        $r['loa_remaining_days'] = null;
        $r['is_loa_critical'] = false;

        if ($r['status'] === 'Leave of Absence' && !empty($r['last_changed_at'])) {
            try {
                $changedAt = new DateTimeImmutable($r['last_changed_at']);
                $diff = $now->diff($changedAt);
                $days = (int) $diff->days;
                $r['loa_days'] = $days;
                $r['loa_remaining_days'] = max(0, 365 - $days);
                $r['is_loa_critical'] = ($days >= 305); // within 60 days of 1-year limit
            } catch (Throwable) {}
        }
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

/** Quick search for the student picker / autocomplete. */
function stSearch(): void
{
    $q     = trim((string) ($_GET['q'] ?? ''));
    $limit = min(20, max(1, (int) ($_GET['limit'] ?? 10)));

    if (strlen($q) < 2) {
        regApiJson(['success' => true, 'data' => []]);
    }

    $like = "%$q%";
    $stmt = db()->prepare(
        "SELECT `id`, `student_number`, `first_name`, `last_name`, `middle_name`,
                `program_course`, `year_section`, `status`, `avatar_url`
         FROM `reg_students`
         WHERE `status` NOT IN ('Deleted', 'Pending')
           AND (`student_number` LIKE ? OR `first_name` LIKE ? OR `last_name` LIKE ?)
         ORDER BY `last_name`, `first_name`
         LIMIT ?"
    );
    $stmt->execute([$like, $like, $like, $limit]);
    regApiJson(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
}

/** Full status-change timeline for one student. */
function stHistory(): void
{
    $studentId = (int) ($_GET['id'] ?? 0);
    if ($studentId <= 0) {
        regApiJson(['success' => false, 'error' => 'Invalid student ID.'], 400);
    }

    $db = db();

    $student = $db->prepare(
        "SELECT `id`, `student_number`, `first_name`, `last_name`, `middle_name`,
                `program_course`, `year_section`, `status`, `avatar_url`
         FROM `reg_students` WHERE `id` = ?"
    );
    $student->execute([$studentId]);
    $student = $student->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        regApiJson(['success' => false, 'error' => 'Student not found.'], 404);
    }

    $hist = $db->prepare(
        "SELECT h.`id`, h.`changed_from`, h.`changed_to`, h.`reason`, h.`reason_category`,
                h.`ref_no`, h.`effective_term`, h.`notes`, h.`changed_at`,
                COALESCE(u.`full_name`, u.`username`) AS changed_by_name
         FROM `reg_status_history` h
         LEFT JOIN `users` u ON u.`id` = h.`changed_by`
         WHERE h.`student_id` = ?
         ORDER BY h.`changed_at` DESC"
    );
    $hist->execute([$studentId]);

    regApiJson([
        'success' => true,
        'student' => $student,
        'history' => $hist->fetchAll(PDO::FETCH_ASSOC),
    ]);
}

/** Status distribution counts & macro registrar KPIs. */
function stStats(): void
{
    $db = db();
    $rows = $db->query(
        "SELECT `status`, COUNT(*) AS cnt FROM `reg_students`
         WHERE `status` NOT IN ('Deleted', 'Pending', 'Verified')
         GROUP BY `status`"
    )->fetchAll(PDO::FETCH_KEY_PAIR);

    $active    = (int) ($rows['Active'] ?? 0);
    $irregular = (int) ($rows['Irregular'] ?? 0);
    $loa       = (int) ($rows['Leave of Absence'] ?? 0);
    $graduated = (int) ($rows['Graduated'] ?? 0);
    $transfer  = (int) ($rows['Transferred'] ?? 0);
    $dismissed = (int) ($rows['Dismissed'] ?? 0);
    $dropout   = (int) ($rows['Dropout'] ?? 0);
    $inactive  = (int) ($rows['Inactive'] ?? 0);

    $totalEnrollment = $active + $irregular;
    $totalAttrition  = $transfer + $dismissed + $dropout + $inactive;
    $totalAudited    = array_sum($rows);
    $attritionRate   = $totalAudited > 0 ? round(($totalAttrition / $totalAudited) * 100, 1) : 0.0;

    // Check critical LOAs (> 305 days / approaching 1-year residency limit)
    $stmtLoaCrit = $db->query(
        "SELECT COUNT(*) FROM `reg_students` s
         JOIN `reg_status_history` sh ON sh.`id` = (
             SELECT MAX(`id`) FROM `reg_status_history` WHERE `student_id` = s.`id`
         )
         WHERE s.`status` = 'Leave of Absence'
           AND DATEDIFF(NOW(), sh.`changed_at`) >= 305"
    );
    $loaCritical = (int) ($stmtLoaCrit ? $stmtLoaCrit->fetchColumn() : 0);

    // Recent transitions count (last 30 days)
    $stmt30d = $db->query(
        "SELECT COUNT(*) FROM `reg_status_history` WHERE `changed_at` >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
    );
    $recent30d = (int) ($stmt30d ? $stmt30d->fetchColumn() : 0);

    regApiJson([
        'success' => true,
        'stats'   => $rows,
        'kpi'     => [
            'total_active'     => $totalEnrollment,
            'active_regular'   => $active,
            'irregular'        => $irregular,
            'loa_total'        => $loa,
            'loa_critical'     => $loaCritical,
            'attrition_total'  => $totalAttrition,
            'attrition_rate'   => $attritionRate,
            'transferred'      => $transfer,
            'graduated'        => $graduated,
            'total_students'   => $totalAudited,
            'recent_30d_count' => $recent30d,
        ],
    ]);
}

/** Change a student's status and record statutory audit history. */
function stChangeStatus(): void
{
    regApiRequireAccess();
    regApiRequireCsrf();

    $body           = regApiBody();
    $studentId      = (int) ($body['student_id'] ?? 0);
    $newStatus      = trim((string) ($body['new_status'] ?? ''));
    $reasonCategory = trim((string) ($body['reason_category'] ?? ''));
    $refNo          = trim((string) ($body['ref_no'] ?? ''));
    $effectiveTerm  = trim((string) ($body['effective_term'] ?? ''));
    $reason         = trim((string) ($body['reason'] ?? ''));
    $notes          = trim((string) ($body['notes'] ?? ''));
    $userId         = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;

    $valid = ['Active', 'Inactive', 'Irregular', 'Graduated', 'Leave of Absence', 'Transferred', 'Dismissed', 'Dropout'];
    if ($studentId <= 0 || !in_array($newStatus, $valid, true)) {
        regApiJson(['success' => false, 'error' => 'Invalid student or target status.'], 400);
    }

    if ($reason === '') {
        regApiJson(['success' => false, 'error' => 'A specific reason/justification is required for the audit trail.'], 400);
    }

    $db = db();
    $student = $db->prepare("SELECT `id`, `status`, `student_number`, `first_name`, `last_name` FROM `reg_students` WHERE `id` = ? AND `status` != 'Deleted'");
    $student->execute([$studentId]);
    $student = $student->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        regApiJson(['success' => false, 'error' => 'Student record not found.'], 404);
    }

    $oldStatus = $student['status'];
    if ($oldStatus === $newStatus) {
        regApiJson(['success' => false, 'error' => "Student is already in '{$newStatus}' standing."], 400);
    }

    $db->prepare("UPDATE `reg_students` SET `status` = ?, `updated_at` = NOW(), `updated_by` = ? WHERE `id` = ?")
       ->execute([$newStatus, $userId, $studentId]);

    $stmtHist = $db->prepare(
        "INSERT INTO `reg_status_history` 
         (`student_id`, `changed_from`, `changed_to`, `reason`, `reason_category`, `ref_no`, `effective_term`, `notes`, `changed_by`, `changed_at`)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
    );
    $stmtHist->execute([
        $studentId,
        $oldStatus,
        $newStatus,
        $reason,
        $reasonCategory ?: null,
        $refNo ?: null,
        $effectiveTerm ?: null,
        $notes ?: null,
        $userId,
    ]);

    $refText = $refNo ? " [Ref: $refNo]" : '';
    regApiLog('change_status', "Student #{$student['student_number']} status changed: $oldStatus → $newStatus (Category: $reasonCategory$refText, Reason: $reason)");

    regApiJson([
        'success' => true,
        'message' => "Academic standing updated: <strong>" . htmlspecialchars($student['last_name'] . ', ' . $student['first_name']) . "</strong> is now <strong class='text-primary'>" . htmlspecialchars($newStatus) . "</strong>.",
        'old'     => $oldStatus,
        'new'     => $newStatus,
    ]);
}

// ── Internal utilities ────────────────────────────────────────────────────────

function _stEnsureHistoryTable(): void
{
    static $done = false;
    if ($done) return;
    $done = true;

    try {
        $db = db();
        $db->exec("CREATE TABLE IF NOT EXISTS `reg_status_history` (
            `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `student_id`      INT UNSIGNED NOT NULL,
            `changed_from`    VARCHAR(50) NOT NULL,
            `changed_to`      VARCHAR(50) NOT NULL,
            `reason`          VARCHAR(255) NULL,
            `reason_category` VARCHAR(100) NULL,
            `ref_no`          VARCHAR(100) NULL,
            `effective_term`  VARCHAR(50) NULL,
            `notes`           TEXT NULL,
            `changed_by`      INT UNSIGNED NULL,
            `changed_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX (`student_id`),
            INDEX (`changed_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // Safe column additions if table was created previously without them
        $cols = $db->query("SHOW COLUMNS FROM `reg_status_history`")->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('reason_category', $cols, true)) {
            $db->exec("ALTER TABLE `reg_status_history` ADD COLUMN `reason_category` VARCHAR(100) NULL AFTER `reason`");
        }
        if (!in_array('ref_no', $cols, true)) {
            $db->exec("ALTER TABLE `reg_status_history` ADD COLUMN `ref_no` VARCHAR(100) NULL AFTER `reason_category`");
        }
        if (!in_array('effective_term', $cols, true)) {
            $db->exec("ALTER TABLE `reg_status_history` ADD COLUMN `effective_term` VARCHAR(50) NULL AFTER `ref_no`");
        }
    } catch (Throwable) {
        // Non-fatal.
    }
}
