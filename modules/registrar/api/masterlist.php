<?php
/**
 * SMS 2 - Registrar API: Student Masterlist
 *
 * Actions (GET unless noted):
 *   fetch        — JSON list of students matching filter params
 *   filter_opts  — JSON dropdown options (school years, programs, sections)
 *   export_csv   — streams a UTF-8 BOM CSV file
 *   print_view   — self-contained HTML print page (open in new tab)
 *   history      — JSON last-N export log rows for the history card (AJAX)
 *   log_export   — POST: record an export event
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/api-helpers.php';

// Cross-module: scheduling service for schGetEnrollmentMap() / schListSections().
require_once __DIR__ . '/../../scheduling/includes/scheduling-service.php';

// Ensure the export-log table exists (idempotent).
_mlEnsureLogTable();

// ── Dispatch ──────────────────────────────────────────────────────────────────
$action = trim((string) ($_GET['action'] ?? ''));
match ($action) {
    'fetch'       => mlFetch(),
    'filter_opts' => mlFilterOpts(),
    'export_csv'  => mlExportCsv(),
    'print_view'  => mlPrintView(),
    'history'     => mlHistory(),
    'log_export'  => mlLogExport(),
    default       => regApiJson(['success' => false, 'error' => 'Unknown action.'], 404),
};

// ── Filter builder ────────────────────────────────────────────────────────────

/**
 * Build WHERE clause, bound params, and extracted filter scalars.
 * Returns [$whereSQL, $params, $schoolYear, $semester, $sectionId].
 */
function mlBuildFilter(): array
{
    $where  = ["s.`status` != 'Deleted'"];
    $params = [];

    $program    = trim((string) ($_GET['program']      ?? ''));
    $yearSec    = trim((string) ($_GET['year_section'] ?? ''));
    $status     = trim((string) ($_GET['status']       ?? ''));
    $schoolYear = trim((string) ($_GET['school_year']  ?? ''));
    $semester   = trim((string) ($_GET['semester']     ?? ''));
    $sectionId  = (int) ($_GET['section_id'] ?? 0);

    if ($program    !== '') { $where[] = 's.`program_course` = ?'; $params[] = $program;  }
    if ($yearSec    !== '') { $where[] = 's.`year_section` = ?';   $params[] = $yearSec;  }
    if ($status     !== '') { $where[] = 's.`status` = ?';         $params[] = $status;   }

    if ($sectionId > 0) {
        // Limit to students assigned to this specific scheduling section.
        $where[]  = "s.`id` IN (
            SELECT `student_id` FROM `sch_section_assignments`
            WHERE `section_id` = ? AND `status` = 'Enrolled')";
        $params[] = $sectionId;
    }

    return ['WHERE ' . implode(' AND ', $where), $params, $schoolYear, $semester, $sectionId];
}

/** Execute the masterlist query and attach enrollment/section metadata. */
function mlQueryRows(): array
{
    [$where, $params, $schoolYear, $semester, $sectionId] = mlBuildFilter();

    $db  = db();
    $sql = "SELECT s.`id`, s.`student_number`,
                   s.`first_name`, s.`middle_name`, s.`last_name`, s.`suffix`,
                   s.`program_course`, s.`year_section`, s.`college_department`,
                   s.`gender`, s.`status`, s.`enrollment_date`
            FROM `reg_students` s
            $where
            ORDER BY s.`last_name`, s.`first_name`
            LIMIT 5000";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ── Enrollment map from Scheduling ────────────────────────────────────
    $enrollmentMap = [];
    if ($schoolYear !== '') {
        $enrollmentMap = schGetEnrollmentMap($schoolYear, $semester);
    }

    // ── Section label per student ─────────────────────────────────────────
    $sectionMap = [];
    if ($schoolYear !== '') {
        if ($sectionId > 0) {
            $sec = schGetSection($sectionId);
            if ($sec) {
                $s2 = $db->prepare(
                    "SELECT `student_id` FROM `sch_section_assignments`
                     WHERE `section_id` = ? AND `status` = 'Enrolled'"
                );
                $s2->execute([$sectionId]);
                $label = $sec['year_section'];
                foreach ($s2->fetchAll(PDO::FETCH_COLUMN) as $sid) {
                    $sectionMap[(int)$sid] = $label;
                }
            }
        } else {
            $semSQL    = $semester !== '' ? "AND sec.`semester` = ?" : '';
            $semParams = $semester !== '' ? [$schoolYear, $semester] : [$schoolYear];
            $s2 = $db->prepare(
                "SELECT a.`student_id`, sec.`year_section`, sec.`program_course` AS sec_program
                 FROM `sch_section_assignments` a
                 JOIN `sch_sections` sec ON sec.`id` = a.`section_id`
                 WHERE a.`status` = 'Enrolled' AND sec.`school_year` = ? $semSQL"
            );
            $s2->execute($semParams);
            foreach ($s2->fetchAll(PDO::FETCH_ASSOC) as $r) {
                $sectionMap[(int)$r['student_id']] ??= $r['year_section'];
            }
        }
    }

    // ── Annotate rows ─────────────────────────────────────────────────────
    foreach ($rows as &$row) {
        $id = (int) $row['id'];

        $mi = $row['middle_name'] ? ' ' . mb_substr($row['middle_name'], 0, 1) . '.' : '';
        $sf = $row['suffix']      ? ' ' . $row['suffix'] : '';
        $row['full_name']        = trim($row['last_name'] . ', ' . $row['first_name'] . $mi . $sf);
        $row['enrolled']         = $schoolYear !== '' && isset($enrollmentMap[$id]);
        $row['section_assigned'] = $sectionMap[$id] ?? '';
    }
    unset($row);

    return $rows;
}

// ── Action handlers ───────────────────────────────────────────────────────────

function mlFetch(): void
{
    $rows      = mlQueryRows();
    $schoolYear = trim((string) ($_GET['school_year'] ?? ''));
    $enrolled  = count(array_filter($rows, fn($r) => $r['enrolled']));

    // Status breakdown for mini chart.
    $byStatus = [];
    foreach ($rows as $r) {
        $byStatus[$r['status']] = ($byStatus[$r['status']] ?? 0) + 1;
    }

    // Program breakdown.
    $byProgram = [];
    foreach ($rows as $r) {
        $prog = $r['program_course'] ?: '(None)';
        $byProgram[$prog] = ($byProgram[$prog] ?? 0) + 1;
    }
    arsort($byProgram);

    regApiJson([
        'success'    => true,
        'total'      => count($rows),
        'enrolled'   => $enrolled,
        'has_sy'     => $schoolYear !== '',
        'by_status'  => $byStatus,
        'by_program' => $byProgram,
        'data'       => $rows,
    ]);
}

function mlFilterOpts(): void
{
    $db = db();

    $programs = $db->query(
        "SELECT DISTINCT `program_course` FROM `reg_students`
         WHERE `status` != 'Deleted' AND `program_course` IS NOT NULL AND `program_course` != ''
         ORDER BY `program_course`"
    )->fetchAll(PDO::FETCH_COLUMN);

    $yearSections = $db->query(
        "SELECT DISTINCT `year_section` FROM `reg_students`
         WHERE `status` != 'Deleted' AND `year_section` IS NOT NULL AND `year_section` != ''
         ORDER BY `year_section`"
    )->fetchAll(PDO::FETCH_COLUMN);

    // School years: from scheduling + from reg_students enrollment_date, merged.
    $syFromSch = $db->query(
        "SELECT DISTINCT `school_year` FROM `sch_sections` WHERE `school_year` IS NOT NULL AND `school_year` != '' ORDER BY `school_year` DESC"
    )->fetchAll(PDO::FETCH_COLUMN);

    // Build SY strings from enrollment_date (e.g., 2025-08 → "2025-2026").
    $syFromEnroll = $db->query(
        "SELECT DISTINCT YEAR(`enrollment_date`) AS yr FROM `reg_students`
         WHERE `enrollment_date` IS NOT NULL AND `status` != 'Deleted' ORDER BY yr DESC"
    )->fetchAll(PDO::FETCH_COLUMN);
    $syFromEnrollStr = array_map(fn($y) => $y . '-' . ($y + 1), array_filter($syFromEnroll));

    $schoolYears = array_values(array_unique(array_merge($syFromSch, $syFromEnrollStr)));
    // Sort descending.
    usort($schoolYears, fn($a, $b) => strcmp($b, $a));

    $sy      = trim((string) ($_GET['school_year'] ?? ''));
    $semFilt = trim((string) ($_GET['semester']    ?? ''));
    $sections = schListSections($sy, $semFilt);

    regApiJson([
        'success'       => true,
        'programs'      => $programs,
        'year_sections' => $yearSections,
        'school_years'  => $schoolYears,
        'sections'      => $sections,
    ]);
}

function mlHistory(): void
{
    $limit = min(20, max(1, (int) ($_GET['limit'] ?? 10)));
    try {
        $rows = db()->prepare(
            "SELECT e.*, u.`name` AS exported_by_name
             FROM `reg_masterlist_exports` e
             LEFT JOIN `users` u ON u.`id` = e.`exported_by`
             ORDER BY e.`created_at` DESC LIMIT ?"
        );
        $rows->execute([$limit]);
        regApiJson(['success' => true, 'data' => $rows->fetchAll(PDO::FETCH_ASSOC)]);
    } catch (Throwable $e) {
        regApiJson(['success' => true, 'data' => []]);
    }
}

function mlExportCsv(): void
{
    $rows     = mlQueryRows();
    $schoolYear = trim((string) ($_GET['school_year'] ?? ''));
    $filename = 'masterlist_' . date('Ymd_His') . '.csv';

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel.

    fputcsv($out, [
        '#', 'Student No.', 'Last Name', 'First Name', 'Middle Name', 'Suffix',
        'Program / Course', 'Year & Section', 'College / Department',
        'Gender', 'Status', 'Enrollment Date',
        'Sched. Section (Scheduling)', 'Enrolled (Scheduling)',
    ]);

    foreach ($rows as $i => $r) {
        fputcsv($out, [
            $i + 1,
            $r['student_number'],
            $r['last_name'],
            $r['first_name'],
            $r['middle_name'] ?? '',
            $r['suffix']      ?? '',
            $r['program_course']     ?? '',
            $r['year_section']       ?? '',
            $r['college_department'] ?? '',
            $r['gender']             ?? '',
            $r['status'],
            $r['enrollment_date']    ?? '',
            $r['section_assigned'],
            $schoolYear !== '' ? ($r['enrolled'] ? 'Yes' : 'No') : '',
        ]);
    }

    fclose($out);
    _mlInsertLog('csv', count($rows), mlCurrentFilters());
    exit;
}

function mlPrintView(): void
{
    $rows        = mlQueryRows();
    $schoolYear  = trim((string) ($_GET['school_year']  ?? ''));
    $semester    = trim((string) ($_GET['semester']     ?? ''));
    $program     = trim((string) ($_GET['program']      ?? ''));
    $yearSection = trim((string) ($_GET['year_section'] ?? ''));
    $status      = trim((string) ($_GET['status']       ?? ''));
    $enrolled    = count(array_filter($rows, fn($r) => $r['enrolled']));
    $total       = count($rows);
    $generatedAt = date('F j, Y g:i A');

    header('Content-Type: text/html; charset=utf-8');

    $metaItems = array_filter([
        $schoolYear  ? 'School Year: <strong>' . htmlspecialchars($schoolYear) . '</strong>'  : '',
        $semester    ? 'Semester: <strong>'    . htmlspecialchars($semester)   . '</strong>'  : '',
        $program     ? 'Program: <strong>'     . htmlspecialchars($program)    . '</strong>'  : '',
        $yearSection ? 'Year &amp; Section: <strong>' . htmlspecialchars($yearSection) . '</strong>' : '',
        $status      ? 'Status: <strong>'      . htmlspecialchars($status)     . '</strong>'  : '',
        'Total: <strong>' . $total . '</strong>',
        $schoolYear  ? 'Enrolled: <strong>'    . $enrolled . '</strong>'       : '',
    ]);

    echo '<!DOCTYPE html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Student Masterlist — ' . htmlspecialchars($schoolYear ?: 'All Years') . '</title>
<style>
 *{box-sizing:border-box} body{font-family:Arial,sans-serif;font-size:11.5px;color:#111;margin:0;padding:16px}
 .header{border-bottom:3px solid #2c3e50;padding-bottom:8px;margin-bottom:10px}
 h1{font-size:17px;margin:0 0 2px;color:#2c3e50}
 .sub{font-size:10.5px;color:#666}
 .meta{display:flex;flex-wrap:wrap;gap:8px;margin:8px 0 12px;font-size:10.5px}
 .meta span{background:#f0f4f8;border-radius:3px;padding:3px 8px}
 table{width:100%;border-collapse:collapse}
 thead th{background:#2c3e50;color:#fff;padding:5px 7px;text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:.3px}
 tbody tr:nth-child(even){background:#f8f8f8}
 tbody td{padding:4px 7px;border-bottom:1px solid #e8e8e8;vertical-align:top}
 .badge{display:inline-block;padding:1px 6px;border-radius:20px;font-size:9.5px;font-weight:700}
 .by{background:#d4edda;color:#155724} .bn{background:#f8d7da;color:#721c24}
 .ba{background:#cce5ff;color:#004085} .bi{background:#fff3cd;color:#856404}
 .bg{background:#d1ecf1;color:#0c5460} .br{background:#f3e5f5;color:#6a1b9a}
 .footer{margin-top:12px;font-size:9.5px;color:#999;text-align:right;border-top:1px solid #eee;padding-top:6px}
 .no-print{margin-bottom:10px}
 @media print{.no-print{display:none!important}
   thead th,tbody tr:nth-child(even),.badge{-webkit-print-color-adjust:exact;print-color-adjust:exact}}
</style></head><body>';

    echo '<div class="no-print">
  <button onclick="window.print()" style="padding:7px 16px;background:#2c3e50;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:12px;margin-right:6px;">🖨 Print / Save as PDF</button>
  <button onclick="window.close()" style="padding:7px 12px;background:#e0e0e0;border:none;border-radius:4px;cursor:pointer;font-size:12px;">✕ Close</button>
</div>';

    echo '<div class="header"><h1>Student Masterlist</h1><div class="sub">Generated ' . htmlspecialchars($generatedAt) . ' · SMS 2 Registrar Module</div></div>';
    echo '<div class="meta">';
    foreach ($metaItems as $m) echo '<span>' . $m . '</span>';
    echo '</div>';

    echo '<table><thead><tr>
      <th>#</th><th>Student No.</th><th>Full Name</th>
      <th>Program / Course</th><th>Year &amp; Section</th>
      <th>Gender</th><th>Status</th>';
    if ($schoolYear !== '') echo '<th>Sched. Section</th><th>Enrolled</th>';
    echo '</tr></thead><tbody>';

    $statusCls = ['Active'=>'ba','Inactive'=>'bi','Graduated'=>'bg','Irregular'=>'br',
                  'Leave of Absence'=>'bi','Transferred'=>'ba','Dismissed'=>'bn','Dropout'=>'bn'];

    foreach ($rows as $i => $r) {
        $cls = $statusCls[$r['status']] ?? '';
        echo '<tr>';
        echo '<td>' . ($i + 1) . '</td>';
        echo '<td><code style="font-size:9.5px;">' . htmlspecialchars($r['student_number']) . '</code></td>';
        echo '<td><strong>' . htmlspecialchars($r['full_name']) . '</strong></td>';
        echo '<td>' . htmlspecialchars($r['program_course'] ?? '') . '</td>';
        echo '<td>' . htmlspecialchars($r['year_section']   ?? '') . '</td>';
        echo '<td>' . htmlspecialchars($r['gender']         ?? '') . '</td>';
        echo '<td><span class="badge ' . $cls . '">' . htmlspecialchars($r['status']) . '</span></td>';
        if ($schoolYear !== '') {
            echo '<td>' . htmlspecialchars($r['section_assigned']) . '</td>';
            echo '<td><span class="badge ' . ($r['enrolled'] ? 'by' : 'bn') . '">' . ($r['enrolled'] ? 'Yes' : 'No') . '</span></td>';
        }
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '<div class="footer">SMS 2 – Registrar Module &bull; ' . htmlspecialchars($generatedAt) . '</div>';
    echo '</body></html>';

    _mlInsertLog('pdf', $total, mlCurrentFilters());
    exit;
}

function mlLogExport(): void
{
    regApiRequireCsrf();
    $body    = regApiBody();
    $type    = in_array($body['type'] ?? '', ['csv', 'pdf'], true) ? $body['type'] : 'csv';
    $count   = (int) ($body['count'] ?? 0);
    $filters = is_array($body['filters'] ?? null) ? $body['filters'] : [];
    _mlInsertLog($type, $count, $filters);
    regApiJson(['success' => true]);
}

// ── Internals ─────────────────────────────────────────────────────────────────

function mlCurrentFilters(): array
{
    return [
        'school_year'  => trim((string) ($_GET['school_year']  ?? '')),
        'semester'     => trim((string) ($_GET['semester']     ?? '')),
        'program'      => trim((string) ($_GET['program']      ?? '')),
        'year_section' => trim((string) ($_GET['year_section'] ?? '')),
        'status'       => trim((string) ($_GET['status']       ?? '')),
        'section_id'   => (int) ($_GET['section_id'] ?? 0),
    ];
}

function _mlEnsureLogTable(): void
{
    static $done = false;
    if ($done) return;
    $done = true;
    try {
        db()->exec("CREATE TABLE IF NOT EXISTS `reg_masterlist_exports` (
            `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `filters`       JSON NOT NULL,
            `student_count` SMALLINT UNSIGNED DEFAULT 0,
            `export_type`   ENUM('csv','pdf') NOT NULL,
            `exported_by`   INT UNSIGNED NULL,
            `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    } catch (Throwable) {}
}

function _mlInsertLog(string $type, int $count, array $filters): void
{
    try {
        $uid = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
        db()->prepare(
            "INSERT INTO `reg_masterlist_exports` (`filters`,`student_count`,`export_type`,`exported_by`)
             VALUES (?,?,?,?)"
        )->execute([json_encode($filters), $count, $type, $uid]);
    } catch (Throwable) {}
}
