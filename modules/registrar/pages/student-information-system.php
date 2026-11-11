<?php
/**
 * SMS 2 - Student Information System
 * Module: Registrar
 * Manage student profiles with full CRUD operations.
 *
 * Visual design uses the app's shared "process list" design tokens
 * (assets/css/module-process-list.css, class prefix mpl-) so this page
 * matches the rest of the system's stat cards / filter bar / table look
 * and gets dark & light theme support for free via [data-theme].
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/registrar-service.php';

regRequireAction('registrar.view');

$pageTitle    = 'Student Information System';
$activeModule = 'registrar';
$activePage   = 'student-information-system';
$breadcrumbs  = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Student Information System', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';

$db = db();

// All active/inactive/graduated students, client-side filtered (dataset is small enough
// that a live filter is more responsive than server round-trips per keystroke).
$students = $db->query("
    SELECT * FROM `reg_students`
    WHERE `status` NOT IN ('Pending', 'Verified', 'Deleted')
    ORDER BY `last_name`, `first_name`
    LIMIT 500
")->fetchAll(PDO::FETCH_ASSOC);

$total      = count($students);
$activeCount     = count(array_filter($students, fn($s) => $s['status'] === 'Active'));
$inactiveCount   = count(array_filter($students, fn($s) => $s['status'] === 'Inactive'));
$graduatedCount  = count(array_filter($students, fn($s) => $s['status'] === 'Graduated'));

$officialPrograms = [
    'Bachelor of Science in Information Technology (BSIT)',
    'Bachelor of Science in Computer Engineering (BSCpE)',
    'Bachelor of Science in Criminology (BSCrim)',
    'Bachelor of Science in Business Administration (BSBA)',
    'Bachelor of Science in Accounting Information Systems (BSAIS)',
    'Bachelor of Science in Entrepreneurship (BSENTREP)',
    'Bachelor of Science in Office Administration (BSOA)',
    'Bachelor of Science in Hospitality Management (BSHM)',
    'Bachelor of Science in Tourism Management (BSTM)',
    'Bachelor of Science in Psychology (BSP)',
    'Bachelor of Library and Information Science (BLIS)',
    'Bachelor of Elementary Education (BEEd)',
    'Bachelor of Secondary Education (BSEd)',
    'Bachelor of Technology and Livelihood Education (BTLED)',
    'Bachelor of Technical-Vocational Teacher Education (BTVTED)',
    'Bachelor of Physical Education (BPEd)',
];

$programs = $officialPrograms;

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

// Infers a year level label from the free-text `year_section` field
// (seed data uses Roman-numeral prefixes like "I-A", "II-B", "III-C", "IV-A";
// this also tolerates a plain leading digit like "1-A").
if (!function_exists('regInferYearLevel')) {
    function regInferYearLevel(?string $yearSection): string
    {
        $prefix = strtoupper(trim((string) $yearSection));
        if (preg_match('/^(IV|III|II|I|[1-4])\b/', $prefix, $m)) {
            $map = ['I' => '1st Year', 'II' => '2nd Year', 'III' => '3rd Year', 'IV' => '4th Year',
                    '1' => '1st Year', '2' => '2nd Year', '3' => '3rd Year', '4' => '4th Year'];
            return $map[$m[1]] ?? 'Other';
        }
        return 'Other';
    }
}

// Active/Inactive/Graduated map onto the shared status-pill palette already
// defined in module-process-list.css (no new CSS needed).
$statusPillClass = [
    'Active'    => 'active',     // purple
    'Inactive'  => 'cancelled',  // gray
    'Graduated' => 'completed',  // green
];

// Department mapping for directory categorization (matches institutional college names)
$defaultDeptMap = [
    'Bachelor of Science in Information Technology (BSIT)' => 'College of Computer Studies',
    'Bachelor of Science in Computer Engineering (BSCpE)' => 'College of Engineering',
    'Bachelor of Science in Criminology (BSCrim)' => 'College of Criminal Justice',
    'Bachelor of Science in Business Administration (BSBA)' => 'College of Business & Accountancy',
    'Bachelor of Science in Accounting Information Systems (BSAIS)' => 'College of Business & Accountancy',
    'Bachelor of Science in Entrepreneurship (BSENTREP)' => 'College of Business & Accountancy',
    'Bachelor of Science in Office Administration (BSOA)' => 'College of Business & Accountancy',
    'Bachelor of Science in Hospitality Management (BSHM)' => 'College of Hospitality & Tourism',
    'Bachelor of Science in Tourism Management (BSTM)' => 'College of Hospitality & Tourism',
    'Bachelor of Science in Psychology (BSP)' => 'College of Arts and Sciences',
    'Bachelor of Library and Information Science (BLIS)' => 'College of Arts and Sciences',
    'Bachelor of Elementary Education (BEEd)' => 'College of Education',
    'Bachelor of Secondary Education (BSEd)' => 'College of Education',
    'Bachelor of Technology and Livelihood Education (BTLED)' => 'College of Education',
    'Bachelor of Technical-Vocational Teacher Education (BTVTED)' => 'College of Education',
    'Bachelor of Physical Education (BPEd)' => 'College of Education',
    // Fallback aliases
    'BS Information Technology' => 'College of Computer Studies',
    'BS Computer Science' => 'College of Computer Studies',
    'BS Information Systems' => 'College of Computer Studies',
    'BS Accountancy' => 'College of Business & Accountancy',
    'BS Business Administration' => 'College of Business & Accountancy',
    'BS Civil Engineering' => 'College of Engineering',
    'BS Electronics Engineering' => 'College of Engineering',
    'BS Electrical Engineering' => 'College of Engineering',
    'BS Computer Engineering' => 'College of Engineering',
    'BS Hotel and Restaurant Mgt.' => 'College of Hospitality & Tourism',
    'BS Hotel and Restaurant Management' => 'College of Hospitality & Tourism',
    'BS Hospitality Management' => 'College of Hospitality & Tourism',
    'BS Tourism Management' => 'College of Hospitality & Tourism',
    'BS Marine Biology' => 'College of Natural Sciences',
    'BS Biology' => 'College of Natural Sciences',
    'BS Psychology' => 'College of Arts and Sciences',
    'BS Nursing' => 'College of Allied Health & Nursing',
    'BS Education' => 'College of Education',
    'Bachelor of Secondary Education' => 'College of Education',
    'Bachelor of Elementary Education' => 'College of Education',
    'BS Criminology' => 'College of Criminal Justice',
];

// Pre-initialize program directory with all 16 official programs
$programDirectory = [];
$departmentsSet = [];

foreach ($officialPrograms as $op) {
    $dept = $defaultDeptMap[$op] ?? 'General Academic Studies';
    $departmentsSet[$dept] = true;
    $programDirectory[$op] = [
        'name' => $op,
        'department' => $dept,
        'years' => [
            '1st' => 0,
            '2nd' => 0,
            '3rd' => 0,
            '4th' => 0,
        ],
        'total' => 0,
    ];
}

foreach ($students as $st) {
    $prog = trim((string)($st['program_course'] ?? ''));
    if ($prog === '') continue;

    if (!isset($programDirectory[$prog])) {
        $dept = trim((string)($st['college_department'] ?? ''));
        if (isset($defaultDeptMap[$prog])) {
            $dept = $defaultDeptMap[$prog];
        } elseif ($dept === '') {
            $dept = 'General Academic Studies';
        }
        $departmentsSet[$dept] = true;

        $programDirectory[$prog] = [
            'name' => $prog,
            'department' => $dept,
            'years' => [
                '1st' => 0,
                '2nd' => 0,
                '3rd' => 0,
                '4th' => 0,
            ],
            'total' => 0,
        ];
    }

    $yearLabel = regInferYearLevel($st['year_section'] ?? '');
    if (strpos($yearLabel, '1st') !== false) {
        $programDirectory[$prog]['years']['1st']++;
    } elseif (strpos($yearLabel, '2nd') !== false) {
        $programDirectory[$prog]['years']['2nd']++;
    } elseif (strpos($yearLabel, '3rd') !== false) {
        $programDirectory[$prog]['years']['3rd']++;
    } elseif (strpos($yearLabel, '4th') !== false) {
        $programDirectory[$prog]['years']['4th']++;
    }
    $programDirectory[$prog]['total']++;
}

// Sort programs: highest enrolled first, then alphabetical
uasort($programDirectory, function($a, $b) {
    if ($b['total'] !== $a['total']) {
        return $b['total'] <=> $a['total'];
    }
    return strcasecmp($a['name'], $b['name']);
});

$departmentsList = array_keys($departmentsSet);
sort($departmentsList);
?>

<link href="<?php echo BASE_URL; ?>/assets/css/module-process-list.css?v=2" rel="stylesheet">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/modules/registrar/assets/css/registrar.css">

<?php renderBreadcrumbs($breadcrumbs); ?>

<div class="container-fluid py-4 student-information-page">
<div class="mpl" data-mpl>

    <div class="mpl-top">
        <div class="mpl-toolbar">
        </div>
    </div>

    <!-- Stats -->
    <section class="mpl-stats" aria-label="Student summary">
        <article class="mpl-stat">
            <div class="mpl-stat-icon blue"><i class="fas fa-users"></i></div>
            <div>
                <span>Total Students</span>
                <strong><?php echo $total; ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div>
                <span>Active</span>
                <strong><?php echo $activeCount; ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon amber"><i class="fas fa-pause-circle"></i></div>
            <div>
                <span>Inactive</span>
                <strong><?php echo $inactiveCount; ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon purple"><i class="fas fa-graduation-cap"></i></div>
            <div>
                <span>Graduated</span>
                <strong><?php echo $graduatedCount; ?></strong>
            </div>
        </article>
    </section>

    <!-- Academic Programs Directory (Replaces bulky cards) -->
    <style>
    .apd-section {
        margin-bottom: 1.75rem;
    }
    .apd-card {
        background: #080f1e;
        border: 1px solid #16243b;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.35);
        color: #f1f5f9;
    }
    .apd-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .apd-title-group {
        flex: 1;
        min-width: 280px;
    }
    .apd-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #ffffff;
        margin: 0 0 0.35rem 0;
        letter-spacing: -0.015em;
    }
    .apd-subtitle {
        font-size: 0.84rem;
        color: #8da2be;
        margin: 0;
        line-height: 1.4;
    }
    .apd-controls {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        flex-wrap: wrap;
    }
    .apd-search-wrapper {
        position: relative;
        min-width: 250px;
    }
    .apd-search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        font-size: 0.85rem;
        pointer-events: none;
    }
    .apd-search-input {
        width: 100%;
        background: #0c1527;
        border: 1px solid #1f2f4a;
        border-radius: 8px;
        padding: 8px 14px 8px 36px;
        font-size: 0.85rem;
        color: #f1f5f9;
        outline: none;
        transition: all 0.2s ease;
    }
    .apd-search-input:focus {
        border-color: #3b82f6;
        background: #0e1a33;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }
    .apd-search-input::placeholder {
        color: #5d718c;
    }
    .apd-select-wrapper {
        position: relative;
        min-width: 190px;
    }
    .apd-dept-select {
        width: 100%;
        background: #0c1527;
        border: 1px solid #1f2f4a;
        border-radius: 8px;
        padding: 8px 34px 8px 14px;
        font-size: 0.85rem;
        color: #cbd5e1;
        outline: none;
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        transition: all 0.2s ease;
    }
    .apd-dept-select:focus {
        border-color: #3b82f6;
        background: #0e1a33;
    }
    .apd-dept-select option {
        background: #0c1527;
        color: #f1f5f9;
    }
    .apd-chevron-icon {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        font-size: 0.72rem;
        pointer-events: none;
    }

    /* Table styling */
    .apd-table-responsive {
        overflow-x: auto;
        border-radius: 8px;
    }
    .apd-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 6px;
        margin: 0;
    }
    .apd-table thead th {
        background: transparent;
        border: none;
        color: #5b6f88;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        padding: 8px 16px;
        white-space: nowrap;
    }
    .apd-col-program { text-align: left; width: 24%; }
    .apd-col-dept { text-align: left; width: 25%; }
    .apd-col-breakdown { text-align: left; width: 29%; }
    .apd-col-enrolled { text-align: center; width: 12%; }
    .apd-col-action { text-align: center; width: 10%; }

    .apd-row {
        background: #0d172e;
        transition: all 0.16s ease;
        cursor: pointer;
    }
    .apd-row td {
        padding: 12px 16px;
        vertical-align: middle;
        border-top: 1px solid #142036;
        border-bottom: 1px solid #142036;
        background: inherit;
    }
    .apd-row td:first-child {
        border-left: 1px solid #142036;
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
    }
    .apd-row td:last-child {
        border-right: 1px solid #142036;
        border-top-right-radius: 8px;
        border-bottom-right-radius: 8px;
    }
    .apd-row:hover {
        background: #111e3b;
    }

    /* Selected Row State (Matching screenshot with glowing blue outline) */
    .apd-row.apd-row-selected {
        background: #0e1c3a;
    }
    .apd-row.apd-row-selected td {
        border-top: 1.5px solid #2563eb !important;
        border-bottom: 1.5px solid #2563eb !important;
    }
    .apd-row.apd-row-selected td:first-child {
        border-left: 1.5px solid #2563eb !important;
    }
    .apd-row.apd-row-selected td:last-child {
        border-right: 1.5px solid #2563eb !important;
    }

    .apd-prog-title {
        font-size: 0.88rem;
        font-weight: 500;
        color: #f1f5f9;
        display: inline-block;
    }
    .apd-dept-title {
        font-size: 0.84rem;
        color: #8da2be;
        display: inline-block;
    }

    /* Year pills */
    .apd-pills-row {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: nowrap;
    }
    .apd-year-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.76rem;
        font-weight: 500;
        padding: 3px 8px;
        border-radius: 5px;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.15s ease;
        text-decoration: none;
        line-height: 1.25;
        background: none;
        white-space: nowrap;
    }
    .apd-year-pill.active {
        background: rgba(14, 116, 144, 0.28);
        border-color: rgba(56, 189, 248, 0.35);
        color: #38bdf8;
    }
    .apd-year-pill.active:hover {
        background: rgba(14, 116, 144, 0.45);
        border-color: #38bdf8;
        color: #e0f2fe;
    }
    .apd-year-pill.muted {
        background: rgba(15, 23, 42, 0.45);
        border-color: rgba(255, 255, 255, 0.05);
        color: #475569;
    }
    .apd-year-pill.muted:hover {
        background: rgba(30, 41, 59, 0.55);
        color: #64748b;
    }
    .apd-year-pill.apd-year-pill-active {
        background: #0284c7 !important;
        border-color: #38bdf8 !important;
        color: #ffffff !important;
        box-shadow: 0 0 8px rgba(56, 189, 248, 0.4);
    }

    /* Enrolled Badge */
    .apd-enrolled-badge {
        display: inline-block;
        padding: 5px 16px;
        border-radius: 8px;
        font-size: 0.82rem;
        text-align: center;
        white-space: nowrap;
        background: rgba(15, 23, 42, 0.7);
        border: 1px solid rgba(148, 163, 184, 0.22);
        color: #94a3b8;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    /* Turns blue only when the course or specific year is clicked/selected */
    .apd-row.apd-row-selected .apd-enrolled-badge,
    .apd-enrolled-badge.highlight {
        background: #1d4ed8 !important;
        border: 1px solid #3b82f6 !important;
        color: #ffffff !important;
        font-weight: 600;
        box-shadow: 0 0 10px rgba(37, 99, 235, 0.35);
    }

    /* Action button */
    .apd-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(15, 23, 42, 0.45);
        border: 1px solid rgba(148, 163, 184, 0.22);
        color: #93c5fd;
        border-radius: 8px;
        padding: 5px 15px;
        font-size: 0.82rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.18s ease;
        white-space: nowrap;
    }
    .apd-action-btn:hover {
        background: rgba(37, 99, 235, 0.28);
        border-color: #3b82f6;
        color: #ffffff;
    }
    </style>

    <section class="apd-section" aria-label="Academic Programs Directory">
        <div class="apd-card">
            <div class="apd-header">
                <div class="apd-title-group">
                    <h2 class="apd-title">Academic Programs Directory</h2>
                    <p class="apd-subtitle">Compact list view replacing heavy cards &middot; Quick filter &amp; drill-down by year level</p>
                </div>
                <div class="apd-controls">
                    <div class="apd-search-wrapper">
                        <i class="fas fa-search apd-search-icon" aria-hidden="true"></i>
                        <input type="text" id="apdSearchInput" class="apd-search-input" placeholder="Search programs..." autocomplete="off">
                    </div>
                    <div class="apd-select-wrapper">
                        <select id="apdDepartmentFilter" class="apd-dept-select">
                            <option value="">All Departments</option>
                            <?php foreach ($departmentsList as $deptItem): ?>
                            <option value="<?php echo htmlspecialchars(strtolower($deptItem)); ?>"><?php echo htmlspecialchars($deptItem); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <i class="fas fa-chevron-down apd-chevron-icon" aria-hidden="true"></i>
                    </div>
                </div>
            </div>

            <div class="apd-table-responsive">
                <table class="apd-table">
                    <thead>
                        <tr>
                            <th class="apd-col-program">PROGRAM NAME</th>
                            <th class="apd-col-dept">COLLEGE / DEPARTMENT</th>
                            <th class="apd-col-breakdown">YEAR BREAKDOWN</th>
                            <th class="apd-col-enrolled">ENROLLED</th>
                            <th class="apd-col-action">ACTION</th>
                        </tr>
                    </thead>
                    <tbody id="apdTableBody">
                        <?php if (empty($programDirectory)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No programs registered.</td>
                        </tr>
                        <?php else:
                            foreach ($programDirectory as $progName => $pData):
                                $progLower = strtolower($progName);
                                $deptLower = strtolower($pData['department']);
                                $totalStudents = $pData['total'];

                                // Year breakdown consistently consists of 1st to 4th year
                                $yearKeys = ['1st', '2nd', '3rd', '4th'];
                        ?>
                        <tr class="apd-row"
                            data-program="<?php echo htmlspecialchars($progLower); ?>"
                            data-department="<?php echo htmlspecialchars($deptLower); ?>"
                            onclick="apdSelectProgram('<?php echo htmlspecialchars(addslashes($progLower)); ?>', '<?php echo htmlspecialchars(addslashes($progName)); ?>', this)">
                            <td class="apd-cell-program">
                                <span class="apd-prog-title"><?php echo htmlspecialchars($progName); ?></span>
                            </td>
                            <td class="apd-cell-dept">
                                <span class="apd-dept-title"><?php echo htmlspecialchars($pData['department']); ?></span>
                            </td>
                            <td class="apd-cell-breakdown">
                                <div class="apd-pills-row">
                                    <?php foreach ($yearKeys as $yk):
                                        $yCount = $pData['years'][$yk];
                                        $isYearActive = $yCount > 0;
                                        $pillClass = $isYearActive ? 'active' : 'muted';
                                        $fullYearLabel = $yk . ' Year';
                                    ?>
                                    <button type="button"
                                            class="apd-year-pill <?php echo $pillClass; ?>"
                                            onclick="apdFilterByYear(event, '<?php echo htmlspecialchars(addslashes($progLower)); ?>', '<?php echo htmlspecialchars(addslashes($progName)); ?>', '<?php echo $yk; ?>', '<?php echo $fullYearLabel; ?>', this, <?php echo (int)$yCount; ?>)"
                                            title="Filter by <?php echo htmlspecialchars($progName); ?> (<?php echo $fullYearLabel; ?>)">
                                        <?php echo $yk; ?>: <?php echo $yCount; ?>
                                    </button>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td class="apd-cell-enrolled">
                                <span class="apd-enrolled-badge" data-total="<?php echo $totalStudents; ?>">
                                    <?php echo $totalStudents; ?> students
                                </span>
                            </td>
                            <td class="apd-cell-action">
                                <button type="button"
                                        class="apd-action-btn"
                                        onclick="apdViewProgram(event, '<?php echo htmlspecialchars(addslashes($progLower)); ?>', '<?php echo htmlspecialchars(addslashes($progName)); ?>', this.closest('tr'))">
                                    View &rarr;
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        <tr id="apdNoResults" style="display:none;">
                            <td colspan="5" class="text-center py-4 text-muted">
                                No matching programs found in this directory.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Filters -->
    <div class="mpl-filters">
        <label class="mpl-search">
            <i class="fas fa-search"></i>
            <input type="search" id="mplSearch" placeholder="Search by student number, name, or program..." aria-label="Search students">
        </label>
        <select id="mplStatus" aria-label="Filter by status">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="graduated">Graduated</option>
        </select>
        <select id="mplProgram" aria-label="Filter by program">
            <option value="">All Programs</option>
            <?php foreach ($programs as $prog): ?>
            <option value="<?php echo htmlspecialchars(strtolower($prog)); ?>"><?php echo htmlspecialchars($prog); ?></option>
            <?php endforeach; ?>
        </select>
        <a class="mpl-refresh" href="?"><i class="fas fa-sync-alt" aria-hidden="true"></i> Refresh</a>
    </div>

    <!-- Table -->
    <section class="mpl-panel" id="studentsTablePanel">
        <div class="mpl-panel-head">
            <div>
                <h2>Student Records</h2>
                <p>View and manage all student master records.</p>
            </div>
        </div>

        <div class="mpl-table-wrap">
            <table class="mpl-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Program</th>
                        <th>Year &amp; Section</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="mplRows">
                    <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center;color:var(--sms-text-muted);padding:1.5rem;">
                            No students found.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($students as $student):
                        $fullName = trim($student['first_name'] . ' ' . $student['last_name']);
                        $status = $student['status'];
                        $pillClass = $statusPillClass[$status] ?? 'cancelled';
                        $searchBlob = strtolower(
                            $student['student_number'] . ' ' . $fullName . ' ' . ($student['program_course'] ?? '')
                        );
                    ?>
                    <tr data-search="<?php echo htmlspecialchars($searchBlob); ?>"
                        data-status="<?php echo htmlspecialchars(strtolower($status)); ?>"
                        data-program="<?php echo htmlspecialchars(strtolower($student['program_course'] ?? '')); ?>"
                        data-year="<?php echo htmlspecialchars(strtolower(regInferYearLevel($student['year_section'] ?? ''))); ?>">
                        <td>
                            <div class="mpl-person" onclick="viewStudent(<?php echo (int)$student['id']; ?>)" style="cursor: pointer;" title="Click to view 360° Profile">
                                <span class="mpl-avatar"><?php echo htmlspecialchars(regInitials($fullName)); ?></span>
                                <div>
                                    <strong class="text-primary"><?php echo htmlspecialchars($student['last_name'] . ', ' . $student['first_name']); ?></strong>
                                    <small><?php echo htmlspecialchars($student['student_number']); ?></small>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($student['program_course'] ?? '—'); ?></td>
                        <td><?php echo htmlspecialchars($student['year_section'] ?? '—'); ?></td>
                        <td><span class="mpl-status <?php echo $pillClass; ?>"><?php echo htmlspecialchars($status); ?></span></td>
                        <td>
                            <div class="d-flex align-items-center gap-1 justify-content-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewStudent(<?php echo (int)$student['id']; ?>)" title="View 360° Profile">
                                    <i class="fas fa-id-card-alt"></i> Profile
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="openEditModal(<?php echo (int)$student['id']; ?>)" title="Edit Student">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteStudent(<?php echo (int)$student['id']; ?>)" title="Delete / Archive">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mpl-foot">
            <span class="meta" id="mplMeta">Showing <?php echo $total; ?> of <?php echo $total; ?> records</span>
        </div>
    </section>

</div>
</div>

<!-- Unified Student 360° Profile Modal -->
<div class="modal fade" id="studentProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered student-360-dialog">
        <div class="modal-content">
            <!-- Header -->
            <div class="s360-header">
                <div class="s360-header-main">
                    <span class="s360-avatar" id="s360Avatar">ST</span>
                    <div class="s360-header-info">
                        <div class="s360-chips">
                            <span class="s360-chip s360-chip-num"><i class="fas fa-id-card"></i> <span id="s360Number">-</span></span>
                            <span class="s360-chip s360-chip-status" id="s360StatusBadge">Active</span>
                            <span class="s360-chip s360-chip-sec" id="s360Section">-</span>
                        </div>
                        <h3 class="s360-name" id="s360Name">Student Name</h3>
                        <div class="s360-program"><i class="fas fa-graduation-cap text-primary"></i> <span id="s360Program">-</span></div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Tabbed Navigation Bar -->
            <div class="s360-nav-tabs">
                <button type="button" class="s360-tab-btn active" onclick="switchS360Tab('bio')">
                    <i class="fas fa-user"></i> Bio &amp; Profile
                </button>
                <button type="button" class="s360-tab-btn" onclick="switchS360Tab('guardians')">
                    <i class="fas fa-user-friends"></i> Family &amp; Guardians <span class="badge bg-secondary" id="s360GuardianBadge">0</span>
                </button>
                <button type="button" class="s360-tab-btn" onclick="switchS360Tab('docs')">
                    <i class="fas fa-folder-open"></i> Document Vault <span class="badge bg-secondary" id="s360DocBadge">0/5</span>
                </button>
                <button type="button" class="s360-tab-btn" onclick="switchS360Tab('health')">
                    <i class="fas fa-heartbeat"></i> Health &amp; Clearance
                </button>
                <button type="button" class="s360-tab-btn" onclick="switchS360Tab('academics')">
                    <i class="fas fa-award"></i> Scholastic Standing
                </button>
            </div>

            <!-- Modal Body (Tab Panes) -->
            <div class="modal-body s360-body">
                <!-- TAB 1: BIO & PROFILE -->
                <div id="s360TabBio" class="s360-tab-pane">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="s360-card">
                                <h6><i class="fas fa-id-badge text-primary"></i> Personal Information</h6>
                                <dl class="s360-dl">
                                    <div><dt>Full Name:</dt><dd id="s360FullName">-</dd></div>
                                    <div><dt>Date of Birth:</dt><dd id="s360Dob">-</dd></div>
                                    <div><dt>Gender:</dt><dd id="s360Gender">-</dd></div>
                                    <div><dt>Nationality:</dt><dd id="s360Nationality">-</dd></div>
                                    <div><dt>Birth Certificate No:</dt><dd id="s360BirthCertNo">-</dd></div>
                                </dl>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="s360-card">
                                <h6><i class="fas fa-graduation-cap text-success"></i> Academic Enrollment</h6>
                                <dl class="s360-dl">
                                    <div><dt>Degree Program:</dt><dd id="s360AcademicProgram">-</dd></div>
                                    <div><dt>Year &amp; Section:</dt><dd id="s360YearSectionFull">-</dd></div>
                                    <div><dt>College Department:</dt><dd id="s360Department">-</dd></div>
                                    <div><dt>Enrollment Date:</dt><dd id="s360EnrollmentDate">-</dd></div>
                                    <div><dt>Current Status:</dt><dd id="s360StatusDetail">-</dd></div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: GUARDIANS & EMERGENCY CONTACTS -->
                <div id="s360TabGuardians" class="s360-tab-pane" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">Parent &amp; designated emergency contact records</span>
                        <a href="javascript:void(0)" id="s360ManageGuardiansLink" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-external-link-alt"></i> Open Full Guardian Editor
                        </a>
                    </div>
                    <div id="s360GuardiansContainer" class="row g-3">
                        <!-- Populated dynamically via JS -->
                    </div>
                </div>

                <!-- TAB 3: DOCUMENT VAULT & CREDENTIALS -->
                <div id="s360TabDocs" class="s360-tab-pane" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">Standard institutional document requirement compliance</span>
                        <a href="javascript:void(0)" id="s360ManageDocsLink" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-folder-open"></i> Open Digital File Storage
                        </a>
                    </div>
                    <div id="s360DocsContainer" class="s360-doc-grid">
                        <!-- Populated dynamically via JS -->
                    </div>
                </div>

                <!-- TAB 4: HEALTH & CLEARANCE STATUS -->
                <div id="s360TabHealth" class="s360-tab-pane" style="display:none;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="s360-card">
                                <h6><i class="fas fa-notes-medical text-danger"></i> Medical Profile</h6>
                                <dl class="s360-dl">
                                    <div><dt>Blood Type:</dt><dd id="s360BloodType">-</dd></div>
                                    <div><dt>Known Allergies:</dt><dd id="s360Allergies">-</dd></div>
                                    <div><dt>Height / Weight:</dt><dd id="s360BodyMetrics">-</dd></div>
                                </dl>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="s360-card">
                                <h6><i class="fas fa-clinic-medical text-primary"></i> Institutional Health Clearance</h6>
                                <div id="s360ClinicClearanceBox" class="p-3 rounded text-center" style="background:#f8fafc; border:1px solid #e2e8f0;">
                                    <span class="badge bg-success p-2 mb-2"><i class="fas fa-check-circle"></i> Clinic Clearance Active</span>
                                    <div class="small text-muted">Managed by School Clinic &bull; Compliant with institutional safety requirements</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 5: ACADEMICS & STANDING -->
                <div id="s360TabAcademics" class="s360-tab-pane" style="display:none;">
                    <div class="s360-card mb-3">
                        <div class="row align-items-center">
                            <div class="col-md-4 text-center border-end">
                                <span class="text-muted small text-uppercase fw-bold">Cumulative GWA</span>
                                <div class="fs-2 fw-bold text-primary" id="s360GwaVal">—</div>
                                <span class="standing-pill" id="s360StandingPill">Good Standing</span>
                            </div>
                            <div class="col-md-4 text-center border-end">
                                <span class="text-muted small text-uppercase fw-bold">Units Completed</span>
                                <div class="fs-4 fw-bold text-dark mt-1" id="s360UnitsVal">0.0 Units</div>
                                <small class="text-muted" id="s360TermsVal">0 Term(s) Evaluated</small>
                            </div>
                            <div class="col-md-4 text-center">
                                <span class="text-muted small text-uppercase fw-bold">Collegiate Ledger</span>
                                <div class="mt-2">
                                    <a href="javascript:void(0)" id="s360OpenAcademicHistoryLink" class="btn btn-sm btn-primary">
                                        <i class="fas fa-graduation-cap"></i> View Full Grade Ledger
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer s360-footer">
                <div class="d-flex justify-content-between w-100 align-items-center">
                    <div>
                        <a href="javascript:void(0)" id="s360PrintIdBtn" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-id-card"></i> Print Student ID
                        </a>
                        <button type="button" id="s360EditBtn" class="btn btn-sm btn-outline-primary ms-1">
                            <i class="fas fa-pen"></i> Edit Profile
                        </button>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
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
                                    <select name="program_course" id="fProgram" class="form-select">
                                        <option value="">— Select Program —</option>
                                        <?php foreach ($officialPrograms as $p): ?>
                                        <option value="<?php echo htmlspecialchars($p); ?>"><?php echo htmlspecialchars($p); ?></option>
                                        <?php endforeach; ?>
                                    </select>
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

// Records embedded server-side so Edit can populate the form without a round trip.
const studentRecords = <?php echo json_encode($students, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

/* ============ Live filter (search + status + program), no page reload ============ */
let currentYear = ''; // set only via the Year Level cards (no dropdown for this one)
let applyFilters = function () {};

(function () {
    const search = document.getElementById('mplSearch');
    const status = document.getElementById('mplStatus');
    const program = document.getElementById('mplProgram');
    const rows = document.querySelectorAll('#mplRows tr[data-search]');
    const meta = document.getElementById('mplMeta');
    const total = <?php echo $total; ?>;

    applyFilters = function () {
        const q = (search.value || '').toLowerCase().trim();
        const st = (status.value || '').toLowerCase();
        const pr = (program.value || '').toLowerCase();
        const yr = (currentYear || '').toLowerCase();
        let visible = 0;

        rows.forEach(function (row) {
            const hay = row.getAttribute('data-search') || '';
            const rowStatus = row.getAttribute('data-status') || '';
            const rowProgram = row.getAttribute('data-program') || '';
            const rowYear = row.getAttribute('data-year') || '';
            const matchQ = !q || hay.includes(q);
            const matchS = !st || rowStatus === st;
            const matchP = !pr || rowProgram === pr;
            const matchY = !yr || rowYear === yr;
            const show = matchQ && matchS && matchP && matchY;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        meta.textContent = 'Showing ' + visible + ' of ' + total + ' records';
    };

    search.addEventListener('input', debounce(applyFilters, 150));
    status.addEventListener('change', applyFilters);
    program.addEventListener('change', function () {
        currentYear = '';
        const selectedVal = (program.value || '').toLowerCase();
        document.querySelectorAll('.apd-row').forEach(r => {
            const rowProg = (r.getAttribute('data-program') || '').toLowerCase();
            const badge = r.querySelector('.apd-enrolled-badge');
            if (badge && badge.dataset.total) {
                badge.textContent = badge.dataset.total + ' students';
            }
            if (selectedVal && rowProg === selectedVal) {
                r.classList.add('apd-row-selected');
            } else {
                r.classList.remove('apd-row-selected');
            }
        });
        document.querySelectorAll('.apd-year-pill').forEach(p => p.classList.remove('apd-year-pill-active'));
        if (document.getElementById('yearLevelPanel')) {
            document.getElementById('yearLevelPanel').style.display = 'none';
        }
        applyFilters();
    });
})();

/* ============ Academic Programs Directory Interactions ============ */
function apdSelectProgram(progLower, progName, rowElement, shouldScroll = false) {
    const programSelect = document.getElementById('mplProgram');
    const isAlreadySelected = rowElement && rowElement.classList.contains('apd-row-selected');

    // Reset all rows and their enrolled badges back to total
    document.querySelectorAll('.apd-row').forEach(r => {
        r.classList.remove('apd-row-selected');
        const badge = r.querySelector('.apd-enrolled-badge');
        if (badge && badge.dataset.total) {
            badge.textContent = badge.dataset.total + ' students';
        }
    });
    document.querySelectorAll('.apd-year-pill').forEach(p => p.classList.remove('apd-year-pill-active'));

    if (isAlreadySelected) {
        // Toggle off if clicking the already-selected program
        if (programSelect) programSelect.value = '';
        currentYear = '';
    } else {
        if (rowElement) {
            rowElement.classList.add('apd-row-selected');
            const badge = rowElement.querySelector('.apd-enrolled-badge');
            if (badge && badge.dataset.total) {
                badge.textContent = badge.dataset.total + ' students';
            }
        }
        if (programSelect) programSelect.value = progLower;
        currentYear = '';
    }

    applyFilters();

    // Only scroll down when explicitly instructed (i.e., when View button was clicked)
    if (shouldScroll) {
        document.getElementById('studentsTablePanel')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function apdViewProgram(event, progLower, progName, rowElement) {
    if (event) event.stopPropagation();
    apdSelectProgram(progLower, progName, rowElement, true);
}

function apdFilterByYear(event, progLower, progName, yearKey, fullYearLabel, pillElement, yearCount) {
    if (event) event.stopPropagation();

    const row = pillElement ? pillElement.closest('.apd-row') : null;

    // Reset all other rows and their enrolled badges
    document.querySelectorAll('.apd-row').forEach(r => {
        if (r !== row) {
            r.classList.remove('apd-row-selected');
            const b = r.querySelector('.apd-enrolled-badge');
            if (b && b.dataset.total) {
                b.textContent = b.dataset.total + ' students';
            }
        }
    });

    const isAlreadyActive = pillElement && pillElement.classList.contains('apd-year-pill-active');
    document.querySelectorAll('.apd-year-pill').forEach(p => p.classList.remove('apd-year-pill-active'));

    const programSelect = document.getElementById('mplProgram');
    const badge = row ? row.querySelector('.apd-enrolled-badge') : null;

    if (isAlreadyActive) {
        // Toggle off year level (keep course selected with its total count)
        currentYear = '';
        if (row) row.classList.add('apd-row-selected');
        if (badge && badge.dataset.total) {
            badge.textContent = badge.dataset.total + ' students';
        }
    } else {
        if (row) row.classList.add('apd-row-selected');
        if (pillElement) pillElement.classList.add('apd-year-pill-active');
        if (programSelect) programSelect.value = progLower;
        currentYear = fullYearLabel.toLowerCase();

        // Enrolled badge turns blue and displays the count for the clicked year level!
        if (badge) {
            const countNum = parseInt(yearCount, 10) || 0;
            badge.textContent = countNum + ' students';
        }
    }

    applyFilters();
    // Do NOT automatically scroll down when filtering by year
}

function filterProgramDirectory() {
    const searchVal = (document.getElementById('apdSearchInput')?.value || '').toLowerCase().trim();
    const deptVal = (document.getElementById('apdDepartmentFilter')?.value || '').toLowerCase().trim();
    const rows = document.querySelectorAll('#apdTableBody .apd-row');
    let visibleCount = 0;

    rows.forEach(function(row) {
        const prog = (row.getAttribute('data-program') || '').toLowerCase();
        const dept = (row.getAttribute('data-department') || '').toLowerCase();

        const matchSearch = !searchVal || prog.includes(searchVal) || dept.includes(searchVal);
        const matchDept = !deptVal || dept === deptVal;

        if (matchSearch && matchDept) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    const noResults = document.getElementById('apdNoResults');
    if (noResults) {
        noResults.style.display = visibleCount === 0 ? '' : 'none';
    }
}

document.getElementById('apdSearchInput')?.addEventListener('input', function() {
    filterProgramDirectory();
});
document.getElementById('apdDepartmentFilter')?.addEventListener('change', function() {
    filterProgramDirectory();
});

/* Legacy aliases to maintain backward compatibility */
function selectProgram(programLower, programLabel) {
    apdSelectProgram(programLower, programLabel, document.querySelector('.apd-row[data-program="' + programLower + '"]'));
}

function filterByYear(yearLower) {
    currentYear = yearLower;
    applyFilters();
}

/* ============ Add / Edit ============ */
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

let currentS360Student = null;

function switchS360Tab(tabKey) {
    document.querySelectorAll('.s360-tab-pane').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.s360-tab-btn').forEach(b => b.classList.remove('active'));
    
    const targetPane = document.getElementById('s360Tab' + tabKey.charAt(0).toUpperCase() + tabKey.slice(1));
    if (targetPane) targetPane.style.display = '';
    
    const activeBtn = document.querySelector('.s360-tab-btn[onclick*="' + tabKey + '"]');
    if (activeBtn) activeBtn.classList.add('active');
}

function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

async function viewStudent(studentId) {
    try {
        const response = await fetch(API_BASE + '/students.php?action=get', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id: studentId})
        });

        const result = await response.json();
        if (!result.success || !result.data) {
            showRegError(result.error || 'Student record not found');
            return;
        }

        const s = result.data;
        currentS360Student = s;
        const fullName = [s.first_name, s.middle_name, s.last_name, s.suffix].filter(Boolean).join(' ');

        // Header
        const initials = ((s.first_name?.[0] || 'S') + (s.last_name?.[0] || 'T')).toUpperCase();
        document.getElementById('s360Avatar').textContent = initials;
        document.getElementById('s360Name').textContent = fullName || 'Student Profile';
        document.getElementById('s360Number').textContent = s.student_number || '—';
        document.getElementById('s360Section').textContent = s.year_section || 'Unassigned Section';
        document.getElementById('s360Program').textContent = s.program_course || 'No Program Assigned';
        
        const statusBadge = document.getElementById('s360StatusBadge');
        statusBadge.textContent = s.status || 'Active';
        statusBadge.className = 's360-chip ' + (s.status === 'Active' ? 'bg-success text-white' : 'bg-secondary text-white');

        // Tab 1: Bio
        document.getElementById('s360FullName').textContent = fullName;
        document.getElementById('s360Dob').textContent = s.date_of_birth || '—';
        document.getElementById('s360Gender').textContent = s.gender || '—';
        document.getElementById('s360Nationality').textContent = s.nationality || 'Filipino';
        document.getElementById('s360BirthCertNo').textContent = s.birth_cert_no || '—';
        document.getElementById('s360AcademicProgram').textContent = s.program_course || '—';
        document.getElementById('s360YearSectionFull').textContent = s.year_section || '—';
        document.getElementById('s360Department').textContent = s.college_department || 'General Academic Studies';
        document.getElementById('s360EnrollmentDate').textContent = s.enrollment_date || '—';
        document.getElementById('s360StatusDetail').textContent = s.status || 'Active';

        // Tab 2: Guardians
        const guardians = s.guardians || [];
        document.getElementById('s360GuardianBadge').textContent = guardians.length;
        document.getElementById('s360ManageGuardiansLink').href = 'guardian-emergency-contact.php?student_id=' + s.id;
        
        const gContainer = document.getElementById('s360GuardiansContainer');
        if (guardians.length === 0) {
            gContainer.innerHTML = '<div class="col-12 text-center text-muted py-4"><i class="fas fa-user-friends fa-2x mb-2 text-secondary"></i><br>No guardian or emergency contact records on file.</div>';
        } else {
            gContainer.innerHTML = guardians.map(g => `
                <div class="col-md-6">
                    <div class="s360-guardian-card">
                        <div class="s360-guardian-head">
                            <span class="s360-guardian-role">${escapeHtml(g.relationship || 'Guardian')}</span>
                            ${g.is_primary ? '<span class="badge bg-primary text-white">Primary</span>' : ''}
                            ${g.is_emergency ? '<span class="badge bg-danger text-white">Emergency</span>' : ''}
                        </div>
                        <div class="s360-guardian-name">${escapeHtml(g.full_name || '—')}</div>
                        <div class="s360-guardian-detail"><i class="fas fa-phone text-primary"></i> ${escapeHtml(g.contact || 'No phone recorded')}</div>
                        <div class="s360-guardian-detail"><i class="fas fa-envelope text-muted"></i> ${escapeHtml(g.email || 'No email recorded')}</div>
                        ${g.address ? `<div class="s360-guardian-detail"><i class="fas fa-map-marker-alt text-muted"></i> ${escapeHtml(g.address)}</div>` : ''}
                    </div>
                </div>
            `).join('');
        }

        // Tab 3: Digital Document Vault
        const docs = s.digital_docs || {};
        const docKeys = Object.keys(docs);
        let uploadedCount = 0;
        docKeys.forEach(k => { if (docs[k]?.is_uploaded) uploadedCount++; });
        document.getElementById('s360DocBadge').textContent = uploadedCount + '/' + (docKeys.length || 5);
        document.getElementById('s360ManageDocsLink').href = 'digital-file-storage.php?student_id=' + s.id;

        const docContainer = document.getElementById('s360DocsContainer');
        if (docKeys.length === 0) {
            docContainer.innerHTML = '<div class="text-center text-muted py-4">No documents tracked.</div>';
        } else {
            docContainer.innerHTML = docKeys.map(k => {
                const item = docs[k];
                const isUp = item?.is_uploaded;
                const title = item?.type?.name || k;
                return `
                    <div class="s360-doc-card">
                        <div class="s360-doc-icon ${isUp ? 'uploaded' : 'missing'}">
                            <i class="fas ${isUp ? 'fa-check' : 'fa-times'}"></i>
                        </div>
                        <div class="s360-doc-info">
                            <div class="s360-doc-title">${escapeHtml(title)}</div>
                            <div class="s360-doc-sub">${isUp ? '<span class="text-success fw-bold">Submitted / Verified</span>' : '<span class="text-danger fw-bold">Missing / Pending</span>'}</div>
                        </div>
                        ${isUp ? `<a href="${API_BASE}/storage.php?action=download&id=${item.file.id}" target="_blank" class="btn btn-xs btn-outline-primary" title="View Document"><i class="fas fa-eye"></i></a>` : ''}
                    </div>
                `;
            }).join('');
        }

        // Tab 4: Health & Clearance
        const hp = s.health_profile || {};
        document.getElementById('s360BloodType').textContent = hp.blood_type || 'Not Recorded';
        document.getElementById('s360Allergies').textContent = hp.allergies || 'None Reported';
        const heightVal = hp.height ? hp.height + ' cm' : '';
        const weightVal = hp.weight ? hp.weight + ' kg' : '';
        document.getElementById('s360BodyMetrics').textContent = (heightVal || weightVal) ? (heightVal + ' / ' + weightVal) : 'Not Recorded';

        // Tab 5: Scholastic Standing
        const gsum = s.grade_summary || {};
        document.getElementById('s360GwaVal').textContent = gsum.cumulative_gwa_fmt || '—';
        document.getElementById('s360UnitsVal').textContent = (gsum.total_units_passed || 0) + ' Units Passed';
        document.getElementById('s360TermsVal').textContent = (gsum.terms_count || 0) + ' Term(s) Evaluated';
        
        const standing = gsum.academic_standing || 'No Records';
        const standingPill = document.getElementById('s360StandingPill');
        standingPill.textContent = standing;
        let standingClass = 'good';
        if (standing === 'Summa Cum Laude') standingClass = 'summa';
        else if (standing === 'Magna Cum Laude') standingClass = 'magna';
        else if (standing === 'Cum Laude') standingClass = 'cumlaude';
        else if (standing === 'Academic Warning') standingClass = 'warning';
        standingPill.className = 'standing-pill ' + standingClass;

        document.getElementById('s360OpenAcademicHistoryLink').href = 'academic-history.php?student_id=' + s.id;

        // Footer buttons
        document.getElementById('s360PrintIdBtn').href = 'student-id-generation.php?student_id=' + s.id;
        document.getElementById('s360EditBtn').onclick = function() {
            hideRegModal('studentProfileModal');
            setTimeout(() => openEditModal(s.id), 250);
        };

        // Reset to first tab & show modal
        switchS360Tab('bio');
        showRegModal('studentProfileModal');

    } catch (err) {
        console.error(err);
        showRegError('Failed to load student 360 profile: ' + err.message);
    }
}

function deleteStudent(studentId) {
    const row = document.querySelector('a[onclick="deleteStudent(' + studentId + ')"]')?.closest('tr');
    const studentName = row?.querySelector('.mpl-person strong')?.textContent || 'this student';
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