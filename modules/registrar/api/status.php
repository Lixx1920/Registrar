<?php
/**
 * SMS 2 - Registrar API: Student Status Tracker
 *
 * Actions:
 *   GET  ?action=list          — paginated student list with live status counts
 *   GET  ?action=search        — search students (name / student number)
 *   GET  ?action=history&id=N  — status-change timeline for one student
 *   POST ?action=change_status — update a student's status + record history
 *   GET  ?action=stats         — status distribution counts
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/api-helpers.php';

// Ensure the status history log table exists (idempotent).
_stEnsureHistoryTable();

// ── Dispatch ──────────────────────────────────────────────────────────────────
$action = trim((string) ($_GET['action'] ?? ($_POST['action'] ?? '')));

match ($action) {
    'list'          => stList(),
    'search'        => stSearch(),
    'history'       => stHistory(),
    'change_status' => stChangeStatus(),
    'stats'         => stStats(),
    default         => regApiJson(['success' => false, 'error' => 'Unknown action.'], 404),
};

// ── Action handlers ───────────────────────────────────────────────────────────

/** Paginated student list, optionally filtered by status / program / search. */
function stList(): void
{
    $db      = db();
    $page    = max(1, (int) ($_GET['page'] ?? 1));
    $limit   = min(50, max(10, (int) ($_GET['limit'] ?? 25)));
    $offset  = ($page - 1) * $limit;

    $status  = trim((string) ($_GET['status']  ?? ''));
    $program = trim((string) ($_GET['program'] ?? ''));
    $q       = trim((string) ($_GET['q']       ?? ''));

    $where  = ["s.`status` != 'Deleted'"];
    $params = [];

    if ($status !== '')  { $where[] = 's.`status` = ?';         $params[] = $status;  }
    if ($program !== '') { $where[] = 's.`program_course` = ?'; $params[] = $program; }
    if ($q !== '') {
        $like = "%$q%";
        $where[]  = '(s.`student_number` LIKE ? OR s.`first_name` LIKE ? OR s.`last_name` LIKE ?)';
        $params[] = $like; $params[] = $like; $params[] = $like;
    }

    $whereClause = 'WHERE ' . implode(' AND ', $where);

    $total = (int) $db->prepare("SELECT COUNT(*) FROM `reg_students` s $whereClause")
        ->execute($params) ? $db->prepare("SELECT COUNT(*) FROM `reg_students` s $whereClause")->execute($params) : 0;

    // Re-run with params properly (PDO can't reuse same prepared object).
    $stmtCount = $db->prepare("SELECT COUNT(*) FROM `reg_students` s $whereClause");
    $stmtCount->execute($params);
    $total = (int) $stmtCount->fetchColumn();

    $stmtData = $db->prepare(
        "SELECT s.`id`, s.`student_number`, s.`first_name`, s.`last_name`,
                s.`middle_name`, s.`program_course`, s.`year_section`, s.`status`,
                s.`updated_at`,
                -- Last status change info from history
                (SELECT sh.`changed_to` FROM `reg_status_history` sh
                 WHERE sh.`student_id` = s.`id` ORDER BY sh.`changed_at` DESC LIMIT 1) AS last_status,
                (SELECT sh.`changed_at` FROM `reg_status_history` sh
                 WHERE sh.`student_id` = s.`id` ORDER BY sh.`changed_at` DESC LIMIT 1) AS last_changed_at,
                (SELECT sh.`reason` FROM `reg_status_history` sh
                 WHERE sh.`student_id` = s.`id` ORDER BY sh.`changed_at` DESC LIMIT 1) AS last_reason
         FROM `reg_students` s
         $whereClause
         ORDER BY s.`last_name`, s.`first_name`
         LIMIT ? OFFSET ?"
    );
    $stmtData->execute(array_merge($params, [$limit, $offset]));
    $rows = $stmtData->fetchAll(PDO::FETCH_ASSOC);

    regApiJson([
        'success' => true,
        'data'    => $rows,
        'pagination' => [
            'page'  => $page,
            'limit' => $limit,
            'total' => $total,
            'pages' => (int) ceil($total / $limit),
        ],
    ]);
}

/** Quick search for the student picker. */
function stSearch(): void
{
    $q     = trim((string) ($_GET['q'] ?? ''));
    $limit = min(20, max(1, (int) ($_GET['limit'] ?? 10)));

    if (strlen($q) < 2) {
        regApiJson(['success' => true, 'data' => []]);
    }

    $like   = "%$q%";
    $stmt   = db()->prepare(
        "SELECT `id`, `student_number`, `first_name`, `last_name`, `program_course`, `year_section`, `status`
         FROM `reg_students`
         WHERE `status` != 'Deleted'
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
        regApiJson(['success' => false, 'error' => 'Invalid student id.'], 400);
    }

    $db = db();

    $student = $db->prepare("SELECT `id`,`student_number`,`first_name`,`last_name`,`program_course`,`year_section`,`status` FROM `reg_students` WHERE `id` = ?");
    $student->execute([$studentId]);
    $student = $student->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        regApiJson(['success' => false, 'error' => 'Student not found.'], 404);
    }

    $hist = $db->prepare(
        "SELECT h.`id`, h.`changed_from`, h.`changed_to`, h.`reason`, h.`notes`,
                h.`changed_at`, u.`name` AS changed_by_name
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

/** Status distribution counts. */
function stStats(): void
{
    $db = db();
    $rows = $db->query(
        "SELECT `status`, COUNT(*) AS cnt FROM `reg_students`
         WHERE `status` != 'Deleted'
         GROUP BY `status`"
    )->fetchAll(PDO::FETCH_KEY_PAIR);

    $total = array_sum($rows);

    regApiJson(['success' => true, 'stats' => $rows, 'total' => $total]);
}

/** Change a student's status and record history. */
function stChangeStatus(): void
{
    regApiRequireAccess();
    regApiRequireCsrf();

    $body      = regApiBody();
    $studentId = (int) ($body['student_id'] ?? 0);
    $newStatus = trim((string) ($body['new_status'] ?? ''));
    $reason    = trim((string) ($body['reason']     ?? ''));
    $notes     = trim((string) ($body['notes']      ?? ''));
    $userId    = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;

    $valid = ['Active', 'Inactive', 'Irregular', 'Graduated', 'Leave of Absence', 'Transferred', 'Dismissed', 'Dropout'];
    if ($studentId <= 0 || !in_array($newStatus, $valid, true)) {
        regApiJson(['success' => false, 'error' => 'Invalid student or status.'], 400);
    }

    $db = db();
    $student = $db->prepare("SELECT `id`, `status` FROM `reg_students` WHERE `id` = ? AND `status` != 'Deleted'");
    $student->execute([$studentId]);
    $student = $student->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        regApiJson(['success' => false, 'error' => 'Student not found.'], 404);
    }

    $oldStatus = $student['status'];
    if ($oldStatus === $newStatus) {
        regApiJson(['success' => false, 'error' => 'Student already has this status.'], 400);
    }

    $db->prepare("UPDATE `reg_students` SET `status` = ?, `updated_at` = NOW(), `updated_by` = ? WHERE `id` = ?")
       ->execute([$newStatus, $userId, $studentId]);

    $db->prepare(
        "INSERT INTO `reg_status_history` (`student_id`, `changed_from`, `changed_to`, `reason`, `notes`, `changed_by`, `changed_at`)
         VALUES (?, ?, ?, ?, ?, ?, NOW())"
    )->execute([$studentId, $oldStatus, $newStatus, $reason ?: null, $notes ?: null, $userId]);

    regApiLog('change_status', "Student #$studentId status changed: $oldStatus → $newStatus (reason: $reason)");

    regApiJson([
        'success'  => true,
        'message'  => "Status updated from <strong>$oldStatus</strong> to <strong>$newStatus</strong>.",
        'old'      => $oldStatus,
        'new'      => $newStatus,
    ]);
}

// ── Internal utilities ────────────────────────────────────────────────────────

function _stEnsureHistoryTable(): void
{
    static $done = false;
    if ($done) return;
    $done = true;

    try {
        db()->exec("CREATE TABLE IF NOT EXISTS `reg_status_history` (
            `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `student_id`   INT UNSIGNED NOT NULL,
            `changed_from` VARCHAR(50) NOT NULL,
            `changed_to`   VARCHAR(50) NOT NULL,
            `reason`       VARCHAR(255) NULL,
            `notes`        TEXT NULL,
            `changed_by`   INT UNSIGNED NULL,
            `changed_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX (`student_id`),
            INDEX (`changed_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    } catch (Throwable) {
        // Non-fatal.
    }
}
