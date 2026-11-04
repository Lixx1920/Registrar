<?php
/**
 * SMS 2 - Student Information System
 * Module: Registrar
 * Manage student directory and profiles with full master-detail integration and CRUD.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/registrar-service.php';

regRequireAction('registrar.view');

$pageTitle    = 'Student Information System';
$activeModule = 'registrar';
$activePage   = 'student-information-system';
$hideModulePageBanner = true;
$GLOBALS['hideModulePageBanner'] = true;
$breadcrumbs  = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Student Information System', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';

$db = db();

// Fetch all active/inactive/graduated students joined with primary guardian contacts
$students = [];
try {
    $stmt = $db->query("
        SELECT 
            s.*,
            g.contact AS g_contact,
            g.email AS g_email,
            g.address AS g_address,
            g.full_name AS g_name
        FROM `reg_students` s
        LEFT JOIN (
            SELECT student_id, contact, email, address, full_name
            FROM reg_guardians
            WHERE is_primary = 1 OR is_emergency = 1
            ORDER BY is_primary DESC, id ASC
        ) g ON g.student_id = s.id
        WHERE s.`status` NOT IN ('Pending', 'Verified', 'Deleted')
        GROUP BY s.id
        ORDER BY s.`last_name`, s.`first_name`
        LIMIT 500
    ");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    // Fallback if guardian table has query limitation
    $students = $db->query("
        SELECT * FROM `reg_students`
        WHERE `status` NOT IN ('Pending', 'Verified', 'Deleted')
        ORDER BY `last_name`, `first_name`
        LIMIT 500
    ")->fetchAll(PDO::FETCH_ASSOC);
}

if (!function_exists('regInitials')) {
    function regInitials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $letters = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $letters .= strtoupper(substr($part, 0, 1));
        }
        return $letters !== '' ? $letters : 'ST';
    }
}

if (!function_exists('regInferYearLevel')) {
    function regInferYearLevel(?string $yearSection): string
    {
        $prefix = strtoupper(trim((string) $yearSection));
        if (preg_match('/^(IV|4TH|4)\b/i', $prefix)) return '4th Year';
        if (preg_match('/^(III|3RD|3)\b/i', $prefix)) return '3rd Year';
        if (preg_match('/^(II|2ND|2)\b/i', $prefix)) return '2nd Year';
        if (preg_match('/^(I|1ST|1|FIRST)\b/i', $prefix)) return '1st Year';
        if (preg_match('/GRADE\s*11\b/i', $prefix)) return 'Grade 11';
        if (preg_match('/GRADE\s*12\b/i', $prefix)) return 'Grade 12';
        return $yearSection !== '' ? $yearSection : '1st Year';
    }
}

if (!function_exists('regInferAcademicLevel')) {
    function regInferAcademicLevel(?string $program, ?string $yearSection): string
    {
        $prog = strtoupper(trim((string)$program));
        $ys = strtoupper(trim((string)$yearSection));
        if (
            strpos($prog, 'STEM') !== false || strpos($prog, 'ABM') !== false || strpos($prog, 'HUMSS') !== false ||
            strpos($prog, 'GAS') !== false || strpos($prog, 'TVL') !== false ||
            strpos($ys, 'GRADE 11') !== false || strpos($ys, 'GRADE 12') !== false ||
            strpos($prog, 'SENIOR HIGH') !== false || strpos($prog, 'SHS') !== false
        ) {
            return 'Senior High School';
        }
        return 'College';
    }
}

if (!function_exists('regProgramAbbr')) {
    function regProgramAbbr(string $prog): string {
        $map = [
            'BS Information Technology' => 'BSIT',
            'BS Computer Science' => 'BSCS',
            'BS Information Systems' => 'BSIS',
            'BS Accountancy' => 'BSA',
            'BS Business Administration' => 'BSBA',
            'BS Civil Engineering' => 'BSCE',
            'BS Electronics Engineering' => 'BSECE',
            'BS Electrical Engineering' => 'BSEE',
            'BS Computer Engineering' => 'BSCpE',
            'BS Hotel and Restaurant Management' => 'BSHRM',
            'BS Hospitality Management' => 'BSHM',
            'BS Tourism Management' => 'BSTM',
            'BS Marine Biology' => 'BSMB',
            'BS Biology' => 'BSBio',
            'BS Psychology' => 'BSPsych',
            'BS Nursing' => 'BSN',
            'BS Education' => 'BSEd',
            'BS Criminology' => 'BSCrim',
            'Senior High School - STEM' => 'STEM',
            'Senior High School - ABM' => 'ABM',
            'Senior High School - HUMSS' => 'HUMSS',
            'Senior High School - GAS' => 'GAS',
            'Senior High School - TVL' => 'TVL',
        ];
        return $map[$prog] ?? (strlen($prog) > 16 ? preg_replace('/(?<=\w)\w*\s*/', '', $prog) : $prog);
    }
}

// Compute live metrics
$totalStudents = count($students);
$collegeCount  = 0;
$shsCount      = 0;
$activeCount   = 0;
$programs      = [];

foreach ($students as &$st) {
    $lvl = regInferAcademicLevel($st['program_course'] ?? '', $st['year_section'] ?? '');
    $st['academic_level'] = $lvl;
    $st['year_level']     = regInferYearLevel($st['year_section'] ?? '');

    if ($lvl === 'Senior High School') {
        $shsCount++;
    } else {
        $collegeCount++;
    }

    if (($st['status'] ?? '') === 'Active') {
        $activeCount++;
    }

    $prog = trim((string)($st['program_course'] ?? ''));
    if ($prog !== '' && !in_array($prog, $programs, true)) {
        $programs[] = $prog;
    }
}
unset($st);
sort($programs);

// Initial selected student id (from query param or first student)
$targetStudentId = (int)($_GET['student_id'] ?? 0);
$selectedStudent = null;
if ($targetStudentId > 0) {
    foreach ($students as $s) {
        if ((int)$s['id'] === $targetStudentId) {
            $selectedStudent = $s;
            break;
        }
    }
}
if (!$selectedStudent && !empty($students)) {
    $selectedStudent = $students[0];
}
?>

<link href="<?php echo BASE_URL; ?>/assets/css/module-process-list.css?v=2" rel="stylesheet">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/modules/registrar/assets/css/registrar.css">

<style>
/* ── Disable Main Page Scrolling & Lock to 100vh on Desktop ──────────────── */
.module-page-banner {
    display: none !important;
}

@media (min-width: 992px) {
    html:has(.student-information-page),
    body:has(.student-information-page),
    html.sis-viewport-fit,
    body.sis-viewport-fit {
        height: 100vh !important;
        height: 100dvh !important;
        overflow: hidden !important;
        scrollbar-gutter: auto !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    body:has(.student-information-page) .sms-wrapper,
    body.sis-viewport-fit .sms-wrapper {
        height: 100vh !important;
        height: 100dvh !important;
        min-height: 100vh !important;
        width: 100% !important;
        max-width: 100% !important;
        overflow: hidden !important;
        padding-top: var(--sms-navbar-height, 60px) !important;
        box-sizing: border-box !important;
    }

    body:has(.student-information-page) .sms-content,
    body.sis-viewport-fit .sms-content {
        height: calc(100vh - var(--sms-navbar-height, 60px)) !important;
        height: calc(100dvh - var(--sms-navbar-height, 60px)) !important;
        min-height: calc(100vh - var(--sms-navbar-height, 60px)) !important;
        width: calc(100% - var(--sms-sidebar-width, 262px)) !important;
        max-width: calc(100% - var(--sms-sidebar-width, 262px)) !important;
        overflow: hidden !important;
        display: flex !important;
        flex-direction: column !important;
        box-sizing: border-box !important;
    }

    body.sidebar-collapsed:has(.student-information-page) .sms-content,
    body.sidebar-collapsed.sis-viewport-fit .sms-content {
        margin-left: var(--sms-sidebar-collapsed-width, 76px) !important;
        width: calc(100% - var(--sms-sidebar-collapsed-width, 76px)) !important;
        max-width: calc(100% - var(--sms-sidebar-collapsed-width, 76px)) !important;
    }

    body:has(.student-information-page) .sms-main,
    body.sis-viewport-fit .sms-main {
        flex: 1 1 0% !important;
        min-height: 0 !important;
        height: 100% !important;
        width: 100% !important;
        max-width: 100% !important;
        overflow: hidden !important;
        padding: 0.4rem 1.25rem 0.35rem 1.25rem !important;
        display: flex !important;
        flex-direction: column !important;
        box-sizing: border-box !important;
    }

    body:has(.student-information-page) .sms-footer,
    body.sis-viewport-fit .sms-footer {
        display: block !important;
        flex: 0 0 auto !important;
        padding: 0.35rem 1.25rem !important;
        font-size: 0.74rem !important;
        background: var(--sms-surface, #0d1e3d) !important;
        border-top: 1px solid var(--sms-border, rgba(59, 130, 246, 0.2)) !important;
        z-index: 10 !important;
    }

    nav[aria-label="breadcrumb"] {
        flex: 0 0 auto;
        margin-bottom: 0.25rem !important;
    }
    nav[aria-label="breadcrumb"] .breadcrumb {
        margin-bottom: 0 !important;
        padding: 0 !important;
        font-size: 0.78rem !important;
    }
}

/* Page Shell Container */
.student-information-page {
    flex: 1 1 0%;
    min-height: 0;
    height: 100%;
    width: 100% !important;
    max-width: 100% !important;
    display: flex;
    flex-direction: column;
    padding: 0 !important;
    margin: 0 !important;
    overflow: hidden;
    box-sizing: border-box;
}

/* ── Main Two-Column Viewport Grid (No Right Gap, 100% Width) ───────────── */
.sis-main-grid {
    flex: 1 1 0%;
    min-height: 0;
    height: 100%;
    width: 100% !important;
    max-width: 100% !important;
    display: grid !important;
    grid-template-columns: minmax(0, 1fr) clamp(320px, 28%, 390px) !important;
    gap: 0.85rem !important;
    align-items: stretch !important;
    overflow: hidden !important;
    box-sizing: border-box !important;
}

/* ── LEFT COLUMN: Summary Cards + Table ─────────────────────────────────── */
.sis-left-col {
    display: flex;
    flex-direction: column;
    min-height: 0;
    height: 100%;
    gap: 0.65rem;
    overflow: hidden;
}

/* 4 Summary Cards arranged in ONE horizontal row */
.sis-kpi-grid {
    flex: 0 0 auto;
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.65rem;
    margin: 0;
}
.sis-kpi-card {
    background: var(--sms-surface-solid, #ffffff);
    border: 1px solid var(--sms-border, #e2e8f0);
    border-radius: 10px;
    padding: 0.55rem 0.85rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    display: flex;
    flex-direction: column;
    justify-content: center;
    transition: transform 0.18s ease, box-shadow 0.18s ease;
}
.sis-kpi-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
}
.sis-kpi-title {
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--sms-text-muted, #64748b);
    margin-bottom: 0.15rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.sis-kpi-num {
    font-size: 1.45rem;
    font-weight: 800;
    color: var(--sms-heading, #0f172a);
    line-height: 1.1;
    margin-bottom: 0.15rem;
    letter-spacing: -0.02em;
}
.sis-kpi-sub {
    font-size: 0.68rem;
    color: var(--sms-text-muted, #94a3b8);
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* All Students Table Container Card */
.sis-table-card {
    flex: 1 1 0%;
    min-height: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: var(--sms-surface-solid, #ffffff);
    border: 1px solid var(--sms-border, #e2e8f0);
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
}
.sis-card-header {
    flex: 0 0 auto;
    padding: 0.55rem 0.95rem;
    border-bottom: 1px solid var(--sms-border, #e2e8f0);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}
.sis-card-title {
    font-size: 0.98rem;
    font-weight: 700;
    color: var(--sms-heading, #0f172a);
    margin: 0;
}
.sis-record-counter {
    font-size: 0.76rem;
    color: var(--sms-text-muted, #64748b);
    font-weight: 600;
}

/* Filter Controls Bar */
.sis-filter-bar {
    flex: 0 0 auto;
    padding: 0.45rem 0.95rem;
    background: rgba(248, 250, 252, 0.6);
    border-bottom: 1px solid var(--sms-border, #e2e8f0);
    display: grid;
    grid-template-columns: 1.35fr 0.95fr 0.95fr 1.15fr;
    gap: 0.5rem;
}
.sis-search-box {
    position: relative;
}
.sis-search-box i {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 0.78rem;
    pointer-events: none;
}
.sis-search-box input {
    width: 100%;
    height: 32px;
    padding: 0.35rem 0.65rem 0.35rem 2rem;
    font-size: 0.78rem;
    border: 1px solid var(--sms-border, #cbd5e1);
    border-radius: 6px;
    background: var(--sms-surface-solid, #ffffff);
    color: var(--sms-heading, #0f172a);
    outline: none;
    transition: all 0.18s ease;
}
.sis-search-box input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15);
}
.sis-select {
    width: 100%;
    height: 32px;
    padding: 0.35rem 0.6rem;
    font-size: 0.78rem;
    border: 1px solid var(--sms-border, #cbd5e1);
    border-radius: 6px;
    background: var(--sms-surface-solid, #ffffff);
    color: var(--sms-heading, #0f172a);
    outline: none;
    cursor: pointer;
    transition: all 0.18s ease;
}
.sis-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15);
}

/* Scrollable Student Table (Only this section scrolls vertically!) */
.sis-table-wrap {
    flex: 1 1 0%;
    min-height: 0;
    overflow-y: auto;
    overflow-x: auto;
}
.sis-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin: 0;
}
.sis-table thead th {
    position: sticky;
    top: 0;
    z-index: 2;
    background: #f8fafc;
    color: var(--sms-text-muted, #64748b);
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 0.55rem 0.95rem;
    border-bottom: 1px solid var(--sms-border, #e2e8f0);
    white-space: nowrap;
}
.sis-table tbody tr {
    cursor: pointer;
    transition: background 0.12s ease;
}
.sis-table tbody tr td {
    padding: 0.52rem 0.95rem;
    vertical-align: middle;
    border-bottom: 1px solid var(--sms-border, #f1f5f9);
    color: var(--sms-heading, #0f172a);
    font-size: 0.8rem;
}
.sis-table tbody tr:hover td {
    background: rgba(241, 245, 249, 0.65);
}
.sis-table tbody tr.sis-row-active td {
    background: rgba(239, 246, 255, 0.95) !important;
}
.sis-table tbody tr.sis-row-active td:first-child {
    border-left: 3.5px solid #2563eb;
}

/* Person cell */
.sis-person {
    display: flex;
    align-items: center;
    gap: 0.65rem;
}
.sis-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #e0e7ff;
    color: #3730a3;
    font-weight: 700;
    font-size: 0.72rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.sis-person-name {
    font-weight: 600;
    color: var(--sms-heading, #0f172a);
    line-height: 1.2;
    display: block;
}
.sis-person-id {
    font-size: 0.7rem;
    color: var(--sms-text-muted, #64748b);
    display: block;
}

/* Status Pill */
.sis-status-pill {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 600;
    text-align: center;
    white-space: nowrap;
}
.sis-status-active {
    background: #dcfce7;
    color: #15803d;
}
.sis-status-inactive {
    background: #f1f5f9;
    color: #64748b;
}
.sis-status-graduated {
    background: #f3e8ff;
    color: #7e22ce;
}

/* Table Footer / Pagination */
.sis-table-foot {
    flex: 0 0 auto;
    padding: 0.4rem 0.95rem;
    border-top: 1px solid var(--sms-border, #e2e8f0);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    font-size: 0.75rem;
    color: var(--sms-text-muted, #64748b);
}
.sis-pagination {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}
.sis-page-btn {
    border: 1px solid var(--sms-border, #cbd5e1);
    background: var(--sms-surface-solid, #ffffff);
    color: var(--sms-heading, #0f172a);
    font-size: 0.72rem;
    font-weight: 600;
    min-width: 26px;
    height: 26px;
    border-radius: 5px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
    padding: 0 4px;
}
.sis-page-btn:hover:not(:disabled) {
    background: #f1f5f9;
    border-color: #94a3b8;
}
.sis-page-btn.active {
    background: #2563eb;
    border-color: #2563eb;
    color: #ffffff;
}
.sis-page-btn:disabled {
    opacity: 0.45;
    cursor: not-allowed;
}

/* ── RIGHT COLUMN: Student Details Panel (Aligned with Top of Cards) ─────── */
.sis-details-card {
    height: 100%;
    min-height: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: var(--sms-surface-solid, #ffffff);
    border: 1px solid var(--sms-border, #e2e8f0);
    border-radius: 10px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
}
.sis-details-header {
    flex: 0 0 auto;
    padding: 0.55rem 0.95rem;
    border-bottom: 1px solid var(--sms-border, #e2e8f0);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.sis-details-header h3 {
    font-size: 0.96rem;
    font-weight: 700;
    color: var(--sms-heading, #0f172a);
    margin: 0;
}
.sis-details-popout-btn {
    color: var(--sms-text-muted, #64748b);
    background: none;
    border: none;
    font-size: 0.85rem;
    cursor: pointer;
    padding: 2px;
    transition: color 0.15s ease;
}
.sis-details-popout-btn:hover {
    color: #2563eb;
}

/* Hero Profile */
.sis-details-hero {
    flex: 0 0 auto;
    padding: 0.75rem 0.95rem 0.5rem;
    text-align: center;
}
.sis-hero-avatar {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: #e0e7ff;
    color: #3730a3;
    font-size: 1.15rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.35rem;
    box-shadow: 0 3px 10px rgba(55, 48, 163, 0.12);
}
.sis-hero-name {
    font-size: 1.05rem;
    font-weight: 800;
    color: var(--sms-heading, #0f172a);
    margin: 0 0 0.1rem 0;
    line-height: 1.2;
}
.sis-hero-id {
    font-size: 0.75rem;
    color: var(--sms-text-muted, #64748b);
    margin: 0 0 0.35rem 0;
}
.sis-hero-badge {
    display: inline-block;
    padding: 2px 10px;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 600;
    background: #dcfce7;
    color: #15803d;
}

/* Details Body Section Groups */
.sis-details-body {
    flex: 1 1 0%;
    min-height: 0;
    overflow-y: auto;
    padding: 0 0.95rem 0.5rem;
}
.sis-section-header {
    font-size: 0.68rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--sms-text-muted, #64748b);
    margin: 0.65rem 0 0.35rem;
    padding-bottom: 0.25rem;
    border-bottom: 1px solid var(--sms-border, #f1f5f9);
}
.sis-info-list {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    margin: 0;
}
.sis-info-item {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 0.5rem;
    font-size: 0.78rem;
}
.sis-info-label {
    color: var(--sms-text-muted, #64748b);
    font-weight: 500;
    flex-shrink: 0;
}
.sis-info-value {
    color: var(--sms-heading, #0f172a);
    font-weight: 600;
    text-align: right;
    word-break: break-word;
}

/* Bottom 4 Buttons (2x2 Grid) */
.sis-btn-grid {
    flex: 0 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.45rem;
    padding: 0.65rem 0.95rem 0.75rem;
    border-top: 1px solid var(--sms-border, #e2e8f0);
    background: rgba(248, 250, 252, 0.4);
}
.sis-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    padding: 0.45rem 0.55rem;
    font-size: 0.74rem;
    font-weight: 600;
    border-radius: 7px;
    border: 1px solid var(--sms-border, #cbd5e1);
    background: var(--sms-surface-solid, #ffffff);
    color: var(--sms-heading, #0f172a);
    text-decoration: none;
    transition: all 0.16s ease;
    white-space: nowrap;
}
.sis-action-btn:hover {
    background: #eff6ff;
    border-color: #3b82f6;
    color: #1e40af;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.12);
}
.sis-action-btn.sis-btn-primary {
    background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
    border-color: transparent;
    color: #ffffff !important;
}
.sis-action-btn.sis-btn-primary:hover {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
    color: #ffffff !important;
    box-shadow: 0 3px 10px rgba(37, 99, 235, 0.22);
}
.sis-action-btn i {
    font-size: 0.8rem;
}

/* Custom Scrollbars */
.sis-table-wrap::-webkit-scrollbar,
.sis-details-card::-webkit-scrollbar,
.sis-details-body::-webkit-scrollbar {
    width: 5px;
    height: 5px;
}
.sis-table-wrap::-webkit-scrollbar-track,
.sis-details-card::-webkit-scrollbar-track,
.sis-details-body::-webkit-scrollbar-track {
    background: transparent;
}
.sis-table-wrap::-webkit-scrollbar-thumb,
.sis-details-card::-webkit-scrollbar-thumb,
.sis-details-body::-webkit-scrollbar-thumb {
    background: rgba(148, 163, 184, 0.4);
    border-radius: 4px;
}
.sis-table-wrap::-webkit-scrollbar-thumb:hover,
.sis-details-card::-webkit-scrollbar-thumb:hover,
.sis-details-body::-webkit-scrollbar-thumb:hover {
    background: rgba(59, 130, 246, 0.6);
}

/* ── Dark Theme Support (SMS2 Registrar Palette) ─────────────────────────── */
[data-theme="dark"] .sis-kpi-card,
[data-theme="dark"] .sis-table-card,
[data-theme="dark"] .sis-details-card {
    background: #0d1e3d;
    border-color: rgba(59, 130, 246, 0.22);
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.35);
}
[data-theme="dark"] .sis-card-header,
[data-theme="dark"] .sis-details-header {
    border-color: rgba(59, 130, 246, 0.18);
}
[data-theme="dark"] .sis-filter-bar {
    background: rgba(10, 24, 53, 0.8);
    border-color: rgba(59, 130, 246, 0.18);
}
[data-theme="dark"] .sis-search-box input,
[data-theme="dark"] .sis-select {
    background: #081329;
    border-color: #1f355b;
    color: #f1f5f9;
}
[data-theme="dark"] .sis-search-box input:focus,
[data-theme="dark"] .sis-select:focus {
    border-color: #3b82f6;
    background: #0a1835;
}
[data-theme="dark"] .sis-table thead th {
    background: #0a1835;
    border-color: rgba(59, 130, 246, 0.18);
    color: #94a3b8;
}
[data-theme="dark"] .sis-table tbody tr td {
    border-color: rgba(255, 255, 255, 0.05);
    color: #e2e8f0;
}
[data-theme="dark"] .sis-table tbody tr:hover td {
    background: rgba(30, 58, 138, 0.25);
}
[data-theme="dark"] .sis-table tbody tr.sis-row-active td {
    background: rgba(37, 99, 235, 0.28) !important;
}
[data-theme="dark"] .sis-btn-grid {
    background: rgba(10, 24, 53, 0.6);
    border-color: rgba(59, 130, 246, 0.18);
}
[data-theme="dark"] .sis-action-btn:not(.sis-btn-primary) {
    background: #0d1e3d;
    border-color: rgba(59, 130, 246, 0.22);
    color: #e2e8f0;
}
[data-theme="dark"] .sis-action-btn:not(.sis-btn-primary):hover {
    background: #1e3a8a;
    border-color: #3b82f6;
    color: #ffffff;
}
[data-theme="dark"] .sis-page-btn {
    background: #0d1e3d;
    border-color: #1f355b;
    color: #cbd5e1;
}
[data-theme="dark"] .sis-page-btn:hover:not(:disabled) {
    background: #1e3a8a;
    color: #ffffff;
}
[data-theme="dark"] .sis-section-header {
    border-color: rgba(255, 255, 255, 0.08);
}
[data-theme="dark"] .sis-avatar {
    background: #1e3a8a;
    color: #93c5fd;
}
[data-theme="dark"] .sis-hero-avatar {
    background: #1e3a8a;
    color: #bfdbfe;
    box-shadow: 0 3px 12px rgba(30, 58, 138, 0.45);
}
[data-theme="dark"] .sis-hero-badge {
    background: rgba(34, 197, 94, 0.15);
    color: #4ade80;
}
[data-theme="dark"] .sis-status-active {
    background: rgba(34, 197, 94, 0.15);
    color: #4ade80;
}
[data-theme="dark"] .sis-status-inactive {
    background: rgba(148, 163, 184, 0.15);
    color: #94a3b8;
}
[data-theme="dark"] .sis-status-graduated {
    background: rgba(168, 85, 247, 0.15);
    color: #c084fc;
}

/* ── Responsive Adaptations for Small / Tablet Screens ───────────────────── */
@media (max-width: 991.98px) {
    .sis-main-grid {
        grid-template-columns: 1fr;
        height: auto;
        overflow: visible;
    }
    .sis-left-col {
        height: auto;
        overflow: visible;
    }
    .sis-table-card {
        height: 520px;
    }
    .sis-details-card {
        height: auto;
        max-height: none;
        overflow: visible;
    }
    .sis-kpi-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 575.98px) {
    .sis-kpi-grid {
        grid-template-columns: 1fr;
    }
    .sis-filter-bar {
        grid-template-columns: 1fr;
    }
}
</style>

<?php renderBreadcrumbs($breadcrumbs); ?>

<div class="container-fluid student-information-page">

    <!-- Main Two-Column Viewport Fit Grid -->
    <div class="sis-main-grid">

        <!-- LEFT COLUMN: 4 Summary Cards + All Students Table (~72% width) -->
        <div class="sis-left-col">

            <!-- 4 Summary Cards in ONE horizontal row -->
            <section class="sis-kpi-grid" aria-label="Student metrics summary">
                <div class="sis-kpi-card">
                    <div class="sis-kpi-title">Total Students</div>
                    <div class="sis-kpi-num"><?= number_format($totalStudents) ?></div>
                    <p class="sis-kpi-sub">All academic levels</p>
                </div>
                <div class="sis-kpi-card">
                    <div class="sis-kpi-title">College Students</div>
                    <div class="sis-kpi-num"><?= number_format($collegeCount) ?></div>
                    <p class="sis-kpi-sub">Across all courses</p>
                </div>
                <div class="sis-kpi-card">
                    <div class="sis-kpi-title">Senior High School</div>
                    <div class="sis-kpi-num"><?= number_format($shsCount) ?></div>
                    <p class="sis-kpi-sub">Grades 11 and 12</p>
                </div>
                <div class="sis-kpi-card">
                    <div class="sis-kpi-title">Active Enrollment</div>
                    <div class="sis-kpi-num"><?= number_format($activeCount) ?></div>
                    <p class="sis-kpi-sub">Currently enrolled</p>
                </div>
            </section>

            <!-- All Students Table Card (Fills remaining height, internal scroll) -->
            <div class="sis-table-card">
                <div class="sis-card-header">
                    <h2 class="sis-card-title">All Students</h2>
                    <span class="sis-record-counter" id="recordCounterText">Showing <?= count($students) ?> records</span>
                </div>

                <!-- Live Filter Controls Bar -->
                <div class="sis-filter-bar">
                    <div class="sis-search-box">
                        <i class="fas fa-search" aria-hidden="true"></i>
                        <input type="text" id="filterSearch" placeholder="Search name or student ID..." autocomplete="off">
                    </div>
                    <div>
                        <select id="filterLevel" class="sis-select">
                            <option value="">All Levels</option>
                            <option value="College">College</option>
                            <option value="Senior High School">Senior High School</option>
                        </select>
                    </div>
                    <div>
                        <select id="filterYear" class="sis-select">
                            <option value="">All Year Levels</option>
                            <option value="1st Year">1st Year</option>
                            <option value="2nd Year">2nd Year</option>
                            <option value="3rd Year">3rd Year</option>
                            <option value="4th Year">4th Year</option>
                            <option value="Grade 11">Grade 11</option>
                            <option value="Grade 12">Grade 12</option>
                        </select>
                    </div>
                    <div>
                        <select id="filterProgram" class="sis-select">
                            <option value="">All Courses / Strands</option>
                            <?php foreach ($programs as $prog): ?>
                                <option value="<?= htmlspecialchars($prog) ?>"><?= htmlspecialchars($prog) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Table Wrapper (Independently scrollable) -->
                <div class="sis-table-wrap">
                    <table class="sis-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Program</th>
                                <th>Year / Grade</th>
                                <th>Section</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="studentTableBody">
                            <?php if (empty($students)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No student records found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($students as $index => $st):
                                    $fullName = trim(($st['first_name'] ?? '') . ' ' . ($st['last_name'] ?? ''));
                                    $initials = regInitials($fullName);
                                    $status   = $st['status'] ?? 'Active';
                                    $statusClass = match(strtolower($status)) {
                                        'active' => 'sis-status-active',
                                        'graduated' => 'sis-status-graduated',
                                        default => 'sis-status-inactive',
                                    };
                                    $progAbbr = regProgramAbbr((string)($st['program_course'] ?? ''));
                                    $searchBlob = strtolower(
                                        ($st['student_number'] ?? '') . ' ' . 
                                        $fullName . ' ' . 
                                        ($st['program_course'] ?? '') . ' ' . 
                                        ($st['year_section'] ?? '')
                                    );
                                    $isActiveRow = ($selectedStudent && (int)$selectedStudent['id'] === (int)$st['id']);
                                ?>
                                <tr class="sis-student-row <?= $isActiveRow ? 'sis-row-active' : '' ?>"
                                    data-id="<?= (int)$st['id'] ?>"
                                    data-search="<?= htmlspecialchars($searchBlob) ?>"
                                    data-level="<?= htmlspecialchars($st['academic_level'] ?? 'College') ?>"
                                    data-year="<?= htmlspecialchars($st['year_level'] ?? '1st Year') ?>"
                                    data-program="<?= htmlspecialchars($st['program_course'] ?? '') ?>"
                                    data-status="<?= htmlspecialchars($status) ?>"
                                    onclick="selectStudentRow(<?= (int)$st['id'] ?>, this)">
                                    <td>
                                        <div class="sis-person">
                                            <span class="sis-avatar"><?= htmlspecialchars($initials) ?></span>
                                            <div>
                                                <span class="sis-person-name"><?= htmlspecialchars($fullName) ?></span>
                                                <span class="sis-person-id"><?= htmlspecialchars($st['student_number'] ?? '') ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span title="<?= htmlspecialchars($st['program_course'] ?? '') ?>">
                                            <?= htmlspecialchars($progAbbr) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($st['year_level'] ?? '—') ?></td>
                                    <td><?= htmlspecialchars($st['year_section'] ?? '—') ?></td>
                                    <td>
                                        <span class="sis-status-pill <?= $statusClass ?>"><?= htmlspecialchars($status) ?></span>
                                    </td>
                                    <td class="text-center" onclick="event.stopPropagation()">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-link text-muted p-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="viewStudent(<?= (int)$st['id'] ?>)"><i class="fas fa-eye me-2 text-primary"></i>View Full Modal</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="openEditModal(<?= (int)$st['id'] ?>)"><i class="fas fa-edit me-2 text-secondary"></i>Edit Details</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deleteStudent(<?= (int)$st['id'] ?>)"><i class="fas fa-trash-alt me-2"></i>Delete</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <tr id="noResultsRow" style="display:none;">
                                <td colspan="6" class="text-center py-4 text-muted">No students match the selected filters.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer with Pagination -->
                <div class="sis-table-foot">
                    <span id="paginationSummary">Showing 1 to <?= min(25, count($students)) ?> of <?= count($students) ?> records</span>
                    <div class="sis-pagination" id="paginationControls">
                        <!-- Populated dynamically via JS -->
                    </div>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: Student Details Panel (~28% width, Top-Aligned with Cards) -->
        <aside class="sis-details-card" aria-label="Selected Student Profile">
            <div class="sis-details-header">
                <h3>Student Details</h3>
                <button type="button" class="sis-details-popout-btn" title="View Full Profile Modal" onclick="popoutCurrentStudent()">
                    <i class="fas fa-expand-alt"></i>
                </button>
            </div>

            <!-- Hero Avatar & Name -->
            <div class="sis-details-hero">
                <div class="sis-hero-avatar" id="detailAvatar">
                    <?= $selectedStudent ? htmlspecialchars(regInitials(trim($selectedStudent['first_name'] . ' ' . $selectedStudent['last_name']))) : 'ST' ?>
                </div>
                <h4 class="sis-hero-name" id="detailFullName">
                    <?= $selectedStudent ? htmlspecialchars(trim($selectedStudent['first_name'] . ' ' . $selectedStudent['last_name'])) : 'Select a Student' ?>
                </h4>
                <div class="sis-hero-id" id="detailStudentNumber">
                    <?= $selectedStudent ? htmlspecialchars($selectedStudent['student_number'] ?? '—') : '—' ?>
                </div>
                <div>
                    <span class="sis-hero-badge" id="detailStatusBadge">
                        <?= $selectedStudent ? htmlspecialchars(($selectedStudent['status'] ?? 'Active') . ' Student') : 'Active Student' ?>
                    </span>
                </div>
            </div>

            <!-- Details Body Information Groups -->
            <div class="sis-details-body">
                <!-- Academic Information -->
                <div class="sis-section-header">Academic Info</div>
                <div class="sis-info-list">
                    <div class="sis-info-item">
                        <span class="sis-info-label">Academic Level</span>
                        <span class="sis-info-value" id="detailLevel"><?= $selectedStudent ? htmlspecialchars($selectedStudent['academic_level'] ?? 'College') : '—' ?></span>
                    </div>
                    <div class="sis-info-item">
                        <span class="sis-info-label">Course / Program</span>
                        <span class="sis-info-value" id="detailProgram"><?= $selectedStudent ? htmlspecialchars($selectedStudent['program_course'] ?? '—') : '—' ?></span>
                    </div>
                    <div class="sis-info-item">
                        <span class="sis-info-label">Year Level</span>
                        <span class="sis-info-value" id="detailYear"><?= $selectedStudent ? htmlspecialchars($selectedStudent['year_level'] ?? '—') : '—' ?></span>
                    </div>
                    <div class="sis-info-item">
                        <span class="sis-info-label">Section</span>
                        <span class="sis-info-value" id="detailSection"><?= $selectedStudent ? htmlspecialchars($selectedStudent['year_section'] ?? '—') : '—' ?></span>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="sis-section-header">Personal Info</div>
                <div class="sis-info-list">
                    <div class="sis-info-item">
                        <span class="sis-info-label">Date of Birth</span>
                        <span class="sis-info-value" id="detailDob"><?= $selectedStudent ? htmlspecialchars($selectedStudent['date_of_birth'] ?? '—') : '—' ?></span>
                    </div>
                    <div class="sis-info-item">
                        <span class="sis-info-label">Gender</span>
                        <span class="sis-info-value" id="detailGender"><?= $selectedStudent ? htmlspecialchars($selectedStudent['gender'] ?? '—') : '—' ?></span>
                    </div>
                    <div class="sis-info-item">
                        <span class="sis-info-label">Email Address</span>
                        <span class="sis-info-value" id="detailEmail">
                            <?php
                            $emailVal = $selectedStudent['email_address'] ?? $selectedStudent['g_email'] ?? '';
                            echo $emailVal !== '' ? htmlspecialchars($emailVal) : '—';
                            ?>
                        </span>
                    </div>
                    <div class="sis-info-item">
                        <span class="sis-info-label">Contact Number</span>
                        <span class="sis-info-value" id="detailContact">
                            <?php
                            $phoneVal = $selectedStudent['contact_number'] ?? $selectedStudent['g_contact'] ?? '';
                            echo $phoneVal !== '' ? htmlspecialchars($phoneVal) : '—';
                            ?>
                        </span>
                    </div>
                    <div class="sis-info-item">
                        <span class="sis-info-label">Home Address</span>
                        <span class="sis-info-value" id="detailAddress">
                            <?php
                            $addrVal = $selectedStudent['g_address'] ?? '';
                            echo $addrVal !== '' ? htmlspecialchars($addrVal) : '—';
                            ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- The 4 Module Redirect Buttons (Bottom 2x2 Grid) -->
            <?php
            $currentId = $selectedStudent ? (int)$selectedStudent['id'] : 0;
            ?>
            <div class="sis-btn-grid" aria-label="Student action redirects">
                <a id="btnContact" href="guardian-emergency-contact.php?student_id=<?= $currentId ?>" class="sis-action-btn sis-btn-primary" title="View & Edit Contact Details">
                    <i class="fas fa-phone-alt"></i>
                    <span>Contact</span>
                </a>
                <a id="btnAcademicHistory" href="academic-history.php?student_id=<?= $currentId ?>" class="sis-action-btn" title="View Academic History">
                    <i class="fas fa-history"></i>
                    <span>Academic History</span>
                </a>
                <a id="btnHealthRecord" href="health-record-log.php?student_id=<?= $currentId ?>" class="sis-action-btn" title="View Health Profile & Logs">
                    <i class="fas fa-heartbeat text-danger"></i>
                    <span>Health Record</span>
                </a>
                <a id="btnFileStorage" href="digital-file-storage.php?student_id=<?= $currentId ?>" class="sis-action-btn" title="Manage Digital Files & Uploads">
                    <i class="fas fa-folder-open text-warning"></i>
                    <span>File Storage</span>
                </a>
            </div>
        </aside>

    </div>

</div>

<!-- Student Information Modal -->
<div class="modal fade" id="studentInfoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm student-info-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="student-info-heading">
                    <span class="mpl-avatar" id="infoInitials">ST</span>
                    <div>
                        <strong id="infoName">Student Name</strong>
                        <small id="infoNumber">Student number</small>
                    </div>
                </div>
                <dl class="student-info-list">
                    <div><dt>Program</dt><dd id="infoProgram">-</dd></div>
                    <div><dt>Year &amp; Section</dt><dd id="infoYearSection">-</dd></div>
                    <div><dt>Status</dt><dd id="infoStatus">-</dd></div>
                    <div><dt>Date of Birth</dt><dd id="infoDob">-</dd></div>
                    <div><dt>Gender</dt><dd id="infoGender">-</dd></div>
                </dl>
            </div>
            <div class="student-info-footer">
                <button type="button" class="btn btn-secondary student-info-close" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Student Modal -->
<div class="modal fade" id="studentModalAdd" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered student-edit-dialog">
        <div class="modal-content">
            <form id="studentForm" onsubmit="return handleStudentForm(event)">
                <div class="modal-body student-edit-body">
                    <input type="hidden" name="id" id="studentId" value="">
                    <div class="reg-form-section">
                        <h5>Personal Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Student Number *</label>
                                    <input type="text" name="student_number" id="fStudentNumber" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Status *</label>
                                    <select name="status" id="fStatus" class="form-select" required>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                        <option value="Graduated">Graduated</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="reg-form-group">
                                    <label>First Name *</label>
                                    <input type="text" name="first_name" id="fFirstName" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="reg-form-group">
                                    <label>Middle Name</label>
                                    <input type="text" name="middle_name" id="fMiddleName" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="reg-form-group">
                                    <label>Last Name *</label>
                                    <input type="text" name="last_name" id="fLastName" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Date of Birth</label>
                                    <input type="date" name="date_of_birth" id="fDob" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Gender</label>
                                    <select name="gender" id="fGender" class="form-select">
                                        <option value="">Select</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="reg-form-section">
                        <h5>Academic Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Program</label>
                                    <input type="text" name="program_course" id="fProgram" class="form-control" placeholder="e.g., BS Computer Science">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Year &amp; Section</label>
                                    <input type="text" name="year_section" id="fYearSection" class="form-control" placeholder="e.g., 2nd Year - A">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary student-form-button" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-reg-primary student-form-button">Save Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>/modules/registrar/assets/js/registrar.js"></script>
<script>
const API_BASE = '<?php echo BASE_URL; ?>/modules/registrar/api';
const CSRF = '<?= e(csrfToken()) ?>';

// Records embedded server-side for instant zero-latency panel updates and edit population
const studentRecords = <?php echo json_encode($students, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
let activeStudentId = <?= $selectedStudent ? (int)$selectedStudent['id'] : 0 ?>;

const PAGE_SIZE = 25;
let currentPage = 1;
let filteredRows = [];

function getInitials(name) {
    if (!name) return 'ST';
    const parts = name.trim().split(/\s+/);
    let str = '';
    for (let i = 0; i < Math.min(2, parts.length); i++) {
        if (parts[i]) str += parts[i].charAt(0).toUpperCase();
    }
    return str || 'ST';
}

function selectStudentRow(studentId, rowElement) {
    activeStudentId = studentId;

    // Toggle active class on rows
    document.querySelectorAll('.sis-student-row').forEach(r => r.classList.remove('sis-row-active'));
    if (rowElement) {
        rowElement.classList.add('sis-row-active');
    } else {
        const matchingRow = document.querySelector(`.sis-student-row[data-id="${studentId}"]`);
        if (matchingRow) matchingRow.classList.add('sis-row-active');
    }

    const record = studentRecords.find(s => parseInt(s.id, 10) === parseInt(studentId, 10));
    if (!record) return;

    const fullName = [record.first_name, record.middle_name, record.last_name].filter(Boolean).join(' ');
    const initials = getInitials(fullName);

    // Update panel hero
    document.getElementById('detailAvatar').textContent = initials;
    document.getElementById('detailFullName').textContent = fullName || 'Unnamed Student';
    document.getElementById('detailStudentNumber').textContent = record.student_number || '—';
    document.getElementById('detailStatusBadge').textContent = (record.status || 'Active') + ' Student';

    // Update academic info
    document.getElementById('detailLevel').textContent = record.academic_level || 'College';
    document.getElementById('detailProgram').textContent = record.program_course || '—';
    document.getElementById('detailYear').textContent = record.year_level || '—';
    document.getElementById('detailSection').textContent = record.year_section || '—';

    // Update personal info
    document.getElementById('detailDob').textContent = record.date_of_birth || '—';
    document.getElementById('detailGender').textContent = record.gender || '—';
    document.getElementById('detailEmail').textContent = record.email_address || record.g_email || '—';
    document.getElementById('detailContact').textContent = record.contact_number || record.g_contact || '—';
    document.getElementById('detailAddress').textContent = record.g_address || '—';

    // Update the 4 redirect buttons with specific student_id
    document.getElementById('btnContact').href = `guardian-emergency-contact.php?student_id=${studentId}`;
    document.getElementById('btnAcademicHistory').href = `academic-history.php?student_id=${studentId}`;
    document.getElementById('btnHealthRecord').href = `health-record-log.php?student_id=${studentId}`;
    document.getElementById('btnFileStorage').href = `digital-file-storage.php?student_id=${studentId}`;
}

function popoutCurrentStudent() {
    if (activeStudentId > 0) {
        viewStudent(activeStudentId);
    }
}

/* ============ Live Filter & Pagination ============ */
function applyFilters() {
    const q = (document.getElementById('filterSearch')?.value || '').toLowerCase().trim();
    const lvl = (document.getElementById('filterLevel')?.value || '').toLowerCase();
    const yr = (document.getElementById('filterYear')?.value || '').toLowerCase();
    const pr = (document.getElementById('filterProgram')?.value || '').toLowerCase();

    const allRows = Array.from(document.querySelectorAll('.sis-student-row'));
    filteredRows = [];

    allRows.forEach(row => {
        const hay = (row.getAttribute('data-search') || '').toLowerCase();
        const rowLvl = (row.getAttribute('data-level') || '').toLowerCase();
        const rowYr = (row.getAttribute('data-year') || '').toLowerCase();
        const rowPr = (row.getAttribute('data-program') || '').toLowerCase();

        const matchQ = !q || hay.includes(q);
        const matchL = !lvl || rowLvl === lvl;
        const matchY = !yr || rowYr === yr;
        const matchP = !pr || rowPr === pr;

        if (matchQ && matchL && matchY && matchP) {
            filteredRows.push(row);
        } else {
            row.style.display = 'none';
        }
    });

    const counter = document.getElementById('recordCounterText');
    if (counter) {
        counter.textContent = `Showing ${filteredRows.length} records`;
    }

    const noResults = document.getElementById('noResultsRow');
    if (noResults) {
        noResults.style.display = filteredRows.length === 0 ? '' : 'none';
    }

    currentPage = 1;
    renderPagination();
}

function renderPagination() {
    const total = filteredRows.length;
    const totalPages = Math.ceil(total / PAGE_SIZE) || 1;
    if (currentPage > totalPages) currentPage = totalPages;

    const startIdx = (currentPage - 1) * PAGE_SIZE;
    const endIdx = Math.min(startIdx + PAGE_SIZE, total);

    // Show only rows on the current page
    filteredRows.forEach((row, i) => {
        row.style.display = (i >= startIdx && i < endIdx) ? '' : 'none';
    });

    // Summary text
    const summary = document.getElementById('paginationSummary');
    if (summary) {
        if (total === 0) {
            summary.textContent = 'Showing 0 records';
        } else {
            summary.textContent = `Showing ${startIdx + 1} to ${endIdx} of ${total} records`;
        }
    }

    // Render page buttons
    const container = document.getElementById('paginationControls');
    if (!container) return;
    container.innerHTML = '';

    if (totalPages <= 1) return;

    // Previous
    const prevBtn = document.createElement('button');
    prevBtn.className = 'sis-page-btn';
    prevBtn.innerHTML = '&laquo;';
    prevBtn.disabled = currentPage === 1;
    prevBtn.onclick = () => { if (currentPage > 1) { currentPage--; renderPagination(); } };
    container.appendChild(prevBtn);

    // Pages
    for (let p = 1; p <= totalPages; p++) {
        const pBtn = document.createElement('button');
        pBtn.className = `sis-page-btn ${p === currentPage ? 'active' : ''}`;
        pBtn.textContent = p;
        pBtn.onclick = () => { currentPage = p; renderPagination(); };
        container.appendChild(pBtn);
    }

    // Next
    const nextBtn = document.createElement('button');
    nextBtn.className = 'sis-page-btn';
    nextBtn.innerHTML = '&raquo;';
    nextBtn.disabled = currentPage === totalPages;
    nextBtn.onclick = () => { if (currentPage < totalPages) { currentPage++; renderPagination(); } };
    container.appendChild(nextBtn);
}

document.getElementById('filterSearch')?.addEventListener('input', debounce(applyFilters, 120));
document.getElementById('filterLevel')?.addEventListener('change', applyFilters);
document.getElementById('filterYear')?.addEventListener('change', applyFilters);
document.getElementById('filterProgram')?.addEventListener('change', applyFilters);

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    // Add viewport lock classes to document for full-screen no-scroll behavior
    document.documentElement.classList.add('sis-viewport-fit');
    document.body.classList.add('sis-viewport-fit');

    applyFilters();
    if (activeStudentId > 0) {
        selectStudentRow(activeStudentId);
    }
});

/* ============ Add / Edit Modals ============ */
function resetStudentForm() {
    document.getElementById('studentForm').reset();
    document.getElementById('studentId').value = '';
}

function openAddModal() {
    resetStudentForm();
    showRegModal('studentModalAdd');
}

function openEditModal(studentId) {
    const record = studentRecords.find(s => parseInt(s.id, 10) === studentId);
    if (!record) {
        showRegError('Student record not found');
        return;
    }

    document.getElementById('studentId').value = record.id;
    document.getElementById('fStudentNumber').value = record.student_number || '';
    document.getElementById('fStatus').value = record.status || 'Active';
    document.getElementById('fFirstName').value = record.first_name || '';
    document.getElementById('fMiddleName').value = record.middle_name || '';
    document.getElementById('fLastName').value = record.last_name || '';
    document.getElementById('fDob').value = record.date_of_birth || '';
    document.getElementById('fGender').value = record.gender || '';
    document.getElementById('fProgram').value = record.program_course || '';
    document.getElementById('fYearSection').value = record.year_section || '';

    showRegModal('studentModalAdd');
}

async function handleStudentForm(e) {
    e.preventDefault();
    const form = document.getElementById('studentForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);

    if (!data.id) {
        delete data.id;
    }

    try {
        const response = await fetch(API_BASE + '/students.php?action=save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': CSRF
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showRegSuccess(result.message || 'Student saved');
            form.reset();
            hideRegModal('studentModalAdd');
            setTimeout(() => location.reload(), 1200);
        } else {
            showRegError(result.error || 'Failed to save student');
        }
    } catch (error) {
        console.error(error);
        showRegError('Error: ' + error.message);
    }

    return false;
}

async function viewStudent(studentId) {
    try {
        const response = await fetch(API_BASE + '/students.php?action=get', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id: studentId})
        });

        const result = await response.json();

        if (result.success) {
            const student = result.data;
            const fullName = [student.first_name, student.middle_name, student.last_name, student.suffix].filter(Boolean).join(' ');
            document.getElementById('infoInitials').textContent = (student.first_name?.[0] || 'S') + (student.last_name?.[0] || 'T');
            document.getElementById('infoName').textContent = fullName || 'Student Name';
            document.getElementById('infoNumber').textContent = student.student_number || '-';
            document.getElementById('infoProgram').textContent = student.program_course || '-';
            document.getElementById('infoYearSection').textContent = student.year_section || '-';
            document.getElementById('infoStatus').textContent = student.status || '-';
            document.getElementById('infoDob').textContent = student.date_of_birth || '-';
            document.getElementById('infoGender').textContent = student.gender || '-';
            showRegModal('studentInfoModal');
        }
    } catch (error) {
        console.error(error);
        showRegError('Error loading student');
    }
}

function deleteStudent(studentId) {
    const row = document.querySelector(`.sis-student-row[data-id="${studentId}"]`);
    const studentName = row?.querySelector('.sis-person-name')?.textContent || 'this student';
    if (!confirm('Delete ' + studentName + '? The student will be hidden from active records.')) return;

    fetch(API_BASE + '/students.php?action=delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': CSRF
        },
        body: JSON.stringify({id: studentId})
    }).then(response => response.json()).then(result => {
        if (!result.success) throw new Error(result.error || 'Failed to delete student');
        showRegSuccess(result.message || 'Student deleted');
        setTimeout(() => location.reload(), 700);
    }).catch(error => {
        console.error(error);
        showRegError(error.message);
    });
}
</script>

<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
