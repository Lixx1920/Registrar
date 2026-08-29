<?php
/**
 * SMS 2 - Academic History & Grade Tracking
 * Module: Registrar
 * Manage complete collegiate grade history from 1st Year 1st Sem to Current Term,
 * plus educational background and prior school records.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/registrar-service.php';

regRequireAction('registrar.view');

$pageTitle    = 'Academic History & Grades';
$activeModule = 'registrar';
$activePage   = 'academic-history';

$db = db();

// Get student from query parameter
$studentId = (int)($_GET['student_id'] ?? 0);
$activeTab = trim((string)($_GET['tab'] ?? 'grades'));
if (!in_array($activeTab, ['grades', 'background'], true)) {
    $activeTab = 'grades';
}

$student   = null;
$notFound  = false;

if ($studentId > 0) {
    $student = regGetStudent($studentId);
    if (!$student) {
        $notFound = true;
        $studentId = 0; // fall through to dashboard view
    }
}

$records = [];
$earliestRecordYear = '-';
$gradeHistory = null;

if ($student) {
    // Single-student view:
    // 1. Previous educational background records
    $stmt = $db->prepare("SELECT * FROM `reg_academic_history` WHERE `student_id` = ? ORDER BY `from_year` DESC, `to_year` DESC");
    $stmt->execute([$studentId]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $recordYears = array_filter(array_column($records, 'from_year'));
    if ($recordYears) {
        $earliestRecordYear = (string)min($recordYears);
    }

    // 2. Full collegiate grade history (1st Year 1st Sem to Current)
    $gradeHistory = regGetStudentGradeHistory($studentId);
} else {
    // Dashboard view: System-wide student academic overview
    $studentsList = $db->query("
        SELECT s.*, 
               (SELECT COUNT(*) FROM `reg_academic_subjects` sub WHERE sub.student_id = s.id) AS total_subjects,
               (SELECT COUNT(DISTINCT CONCAT(sub.year_level, '|', sub.term, '|', sub.academic_year)) FROM `reg_academic_subjects` sub WHERE sub.student_id = s.id) AS terms_count,
               (SELECT COUNT(*) FROM `reg_academic_history` ah WHERE ah.student_id = s.id) AS school_history_count
        FROM `reg_students` s
        WHERE s.status != 'Deleted'
        ORDER BY s.last_name, s.first_name
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Compute student stats & overall GWA for dashboard
    $studentMap = [];
    $totalActiveStudents = 0;
    $studentsWithGrades = 0;
    $gwaSum = 0.0;
    $gwaCount = 0;
    $missingRecords = [];

    foreach ($studentsList as $st) {
        if ($st['status'] === 'Active') {
            $totalActiveStudents++;
        }
        $gh = regGetStudentGradeHistory((int)$st['id']);
        $st['grade_summary'] = $gh['summary'];
        $studentMap[] = $st;

        if ($st['total_subjects'] > 0) {
            $studentsWithGrades++;
            if ($gh['summary']['cumulative_gwa'] !== null) {
                $gwaSum += (float)$gh['summary']['cumulative_gwa'];
                $gwaCount++;
            }
        }

        if ($st['total_subjects'] == 0 && $st['school_history_count'] == 0 && $st['status'] === 'Active') {
            $missingRecords[] = $st;
        }
    }

    $avgInstitutionalGwa = $gwaCount > 0 ? number_format($gwaSum / $gwaCount, 2) : '—';
}

$breadcrumbs = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Academic History', 'url' => $student ? (BASE_URL . '/modules/registrar/pages/academic-history.php') : null],
];
if ($student) {
    $breadcrumbs[] = ['label' => $student['first_name'] . ' ' . $student['last_name'], 'url' => null];

    $pageBannerBackUrl   = BASE_URL . '/modules/registrar/pages/academic-history.php';
    $pageBannerBackLabel = 'Back to Dashboard';
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

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<link href="<?php echo BASE_URL; ?>/assets/css/module-process-list.css?v=2" rel="stylesheet">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/modules/registrar/assets/css/registrar.css">

<?php renderBreadcrumbs($breadcrumbs); ?>

<div class="container-fluid py-4">
<div class="mpl" data-mpl>

<?php if (!$student): ?>

    <!-- ====================================================================
         SYSTEM-WIDE ACADEMIC DASHBOARD VIEW
         ==================================================================== -->
    <div class="mpl-top">
        <div>
            <p>System-wide collegiate academic and educational records. Track grade progression from 1st Year 1st Sem to current standing across all enrolled students.</p>
        </div>
        <div class="mpl-toolbar">
            <a class="mpl-add" href="<?php echo BASE_URL; ?>/modules/registrar/pages/student-information-system.php">
                <i class="fas fa-users" aria-hidden="true"></i> Student Directory
            </a>
        </div>
    </div>

    <?php if ($notFound): ?>
    <div class="mpl-alert mpl-alert-auto" style="border-color: rgba(217, 119, 6, 0.28); background: rgba(245, 158, 11, 0.08); color: #92400e;">
        <i class="fas fa-exclamation-triangle"></i> Student #<?php echo (int)($_GET['student_id'] ?? 0); ?> was not found.
    </div>
    <?php endif; ?>

    <section class="mpl-stats" aria-label="Academic history system summary">
        <article class="mpl-stat">
            <div class="mpl-stat-icon blue"><i class="fas fa-user-graduate"></i></div>
            <div>
                <span>Total Active Students</span>
                <strong><?php echo $totalActiveStudents; ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon green"><i class="fas fa-award"></i></div>
            <div>
                <span>Students With Grade History</span>
                <strong><?php echo $studentsWithGrades; ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon purple"><i class="fas fa-chart-line"></i></div>
            <div>
                <span>Avg. Institutional GWA</span>
                <strong><?php echo $avgInstitutionalGwa; ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon amber"><i class="fas fa-exclamation-triangle"></i></div>
            <div>
                <span>Needs Academic Setup</span>
                <strong><?php echo count($missingRecords); ?></strong>
            </div>
        </article>
    </section>

    <?php if (!empty($missingRecords)): ?>
    <section class="mpl-panel" style="margin-bottom: 1rem;">
        <div class="mpl-panel-head">
            <div>
                <h2>Needs Setup Attention</h2>
                <p><?php echo count($missingRecords); ?> active student(s) have no grades or prior educational background recorded.</p>
            </div>
        </div>
        <div class="mpl-table-wrap">
            <table class="mpl-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Program</th>
                        <th>Year & Section</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($missingRecords as $m): ?>
                    <tr>
                        <td>
                            <div class="mpl-person">
                                <span class="mpl-avatar"><?php echo htmlspecialchars(substr($m['first_name'], 0, 1) . substr($m['last_name'], 0, 1)); ?></span>
                                <div>
                                    <strong><?php echo htmlspecialchars($m['last_name'] . ', ' . $m['first_name']); ?></strong>
                                    <small><?php echo htmlspecialchars($m['student_number']); ?></small>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($m['program_course'] ?? '—'); ?></td>
                        <td><?php echo htmlspecialchars($m['year_section'] ?? '—'); ?></td>
                        <td>
                            <div class="mpl-actions">
                                <a href="academic-history.php?student_id=<?php echo (int)$m['id']; ?>&tab=grades" class="btn btn-sm btn-outline-primary" title="Setup Grades">
                                    <i class="fas fa-plus"></i> Setup Academic Records
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php endif; ?>

    <div class="mpl-filters">
        <label class="mpl-search">
            <i class="fas fa-search"></i>
            <input type="search" id="dashboardFilter" placeholder="Search by student number, name, program, or standing..." aria-label="Search student academic records">
        </label>
        <a class="mpl-refresh" href="?"><i class="fas fa-sync-alt" aria-hidden="true"></i> Refresh</a>
    </div>

    <section class="mpl-panel ah-panel">
        <div class="mpl-panel-head">
            <div>
                <h2>Student Academic Records Directory</h2>
                <p><?php echo count($studentMap); ?> student(s) registered in system</p>
            </div>
        </div>
        <div class="mpl-table-wrap">
            <table class="mpl-table" id="dashboardTable">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Program</th>
                        <th>Year &amp; Section</th>
                        <th>Cumulative GWA</th>
                        <th>Academic Standing</th>
                        <th>Terms Completed</th>
                        <th>Units Earned</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($studentMap)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center;color:var(--sms-text-muted);padding:2rem;">
                            <i class="fas fa-info-circle"></i> No student records found.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($studentMap as $st): 
                        $summary = $st['grade_summary'];
                        $gwaFmt = $summary['cumulative_gwa_fmt'];
                        $standing = $summary['academic_standing'];
                        
                        $standingClass = 'good';
                        if ($standing === "President's Lister") $standingClass = 'presidents';
                        elseif ($standing === "Dean's Lister") $standingClass = 'deans';
                        elseif ($standing === 'Academic Warning') $standingClass = 'warning';
                        elseif ($standing === 'No Records') $standingClass = '';
                    ?>
                    <tr class="dashboard-row" data-search="<?php echo htmlspecialchars(strtolower($st['student_number'] . ' ' . $st['last_name'] . ' ' . $st['first_name'] . ' ' . ($st['program_course'] ?? '') . ' ' . $standing)); ?>">
                        <td>
                            <div class="mpl-person">
                                <span class="mpl-avatar"><?php echo htmlspecialchars(substr($st['first_name'], 0, 1) . substr($st['last_name'], 0, 1)); ?></span>
                                <div>
                                    <strong><?php echo htmlspecialchars($st['last_name'] . ', ' . $st['first_name']); ?></strong>
                                    <small><?php echo htmlspecialchars($st['student_number']); ?></small>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($st['program_course'] ?? '—'); ?></td>
                        <td><span class="ah-pill"><?php echo htmlspecialchars($st['year_section'] ?? '—'); ?></span></td>
                        <td>
                            <strong><?php echo $gwaFmt !== '—' ? $gwaFmt : '<span style="color:var(--sms-text-muted);">N/A</span>'; ?></strong>
                        </td>
                        <td>
                            <?php if ($standing !== 'No Records'): ?>
                                <span class="standing-pill <?php echo $standingClass; ?>"><?php echo htmlspecialchars($standing); ?></span>
                            <?php else: ?>
                                <span class="badge bg-light text-muted">No Grades</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo (int)$summary['terms_count']; ?> Sem(s)</td>
                        <td><strong><?php echo (float)$summary['total_units_passed']; ?></strong> / <?php echo (float)$summary['total_units_enrolled']; ?> Units</td>
                        <td>
                            <div class="mpl-actions">
                                <a href="academic-history.php?student_id=<?php echo (int)$st['id']; ?>" class="btn btn-sm btn-outline-primary" title="View Grade History">
                                    <i class="fas fa-graduation-cap"></i> View Grades
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

<?php else: 
    // ====================================================================
    // SINGLE STUDENT VIEW: COLLEGIATE GRADES + EDUCATIONAL BACKGROUND
    // ====================================================================
    $summary = $gradeHistory['summary'];
    $terms   = $gradeHistory['terms'];

    $overallStanding = $summary['academic_standing'];
    $overallStandingClass = 'good';
    if ($overallStanding === "President's Lister") $overallStandingClass = 'presidents';
    elseif ($overallStanding === "Dean's Lister") $overallStandingClass = 'deans';
    elseif ($overallStanding === 'Academic Warning') $overallStandingClass = 'warning';
?>

    <div class="mpl-top">
        <div>
            <p>Collegiate grade progression &amp; educational background for <strong><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></strong> (<?php echo htmlspecialchars($student['student_number']); ?>)</p>
        </div>
        <div class="mpl-toolbar">
            <button type="button" class="btn btn-outline-secondary" onclick="openTranscriptModal()">
                <i class="fas fa-print" aria-hidden="true"></i> Print Transcript / Grades
            </button>
        </div>
    </div>

    <!-- Student Key Academic Profile Summary Cards -->
    <section class="mpl-stats" aria-label="Student academic summary">
        <article class="mpl-stat">
            <div class="mpl-stat-icon purple"><i class="fas fa-chart-pie"></i></div>
            <div>
                <span>Cumulative GWA</span>
                <strong><?php echo $summary['cumulative_gwa_fmt']; ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div>
                <span>Units Passed / Enrolled</span>
                <strong><?php echo (float)$summary['total_units_passed']; ?> / <?php echo (float)$summary['total_units_enrolled']; ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon blue"><i class="fas fa-calendar-check"></i></div>
            <div>
                <span>Semesters Completed</span>
                <strong><?php echo (int)$summary['terms_count']; ?> Terms</strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon amber"><i class="fas fa-award"></i></div>
            <div>
                <span>Academic Standing</span>
                <strong><span class="standing-pill <?php echo $overallStandingClass; ?>" style="font-size:0.8rem;"><?php echo htmlspecialchars($overallStanding); ?></span></strong>
            </div>
        </article>
    </section>

    <!-- Student Snapshot Details -->
    <section class="mpl-panel" style="margin-bottom: 1rem;">
        <div class="mpl-panel-head">
            <div>
                <h2>Student Information Snapshot</h2>
                <p>Curriculum &amp; Program Enrollment Profile</p>
            </div>
        </div>
        <div class="ah-summary">
            <div>
                <span>Student Full Name</span>
                <strong><?php echo htmlspecialchars($student['last_name'] . ', ' . $student['first_name'] . ' ' . ($student['middle_name'] ?? '') . ' ' . ($student['suffix'] ?? '')); ?></strong>
            </div>
            <div>
                <span>Program / Degree</span>
                <strong><?php echo htmlspecialchars($student['program_course'] ?? '—'); ?></strong>
            </div>
            <div>
                <span>Year &amp; Section</span>
                <strong><?php echo htmlspecialchars($student['year_section'] ?? '—'); ?></strong>
            </div>
            <div>
                <span>Enrollment Status</span>
                <strong><?php echo htmlspecialchars($student['status'] ?? 'Active'); ?></strong>
            </div>
        </div>
    </section>

    <!-- Navigation Tabs -->
    <div class="ah-tabs-nav">
        <button type="button" class="ah-tab-btn <?php echo $activeTab === 'grades' ? 'active' : ''; ?>" onclick="switchTab('grades')">
            <i class="fas fa-graduation-cap"></i> Collegiate Grade History (TOR)
            <span class="badge bg-primary text-white"><?php echo (int)$summary['total_subjects']; ?> Subjects</span>
        </button>
        <button type="button" class="ah-tab-btn <?php echo $activeTab === 'background' ? 'active' : ''; ?>" onclick="switchTab('background')">
            <i class="fas fa-school"></i> Previous Educational Background
            <span class="badge bg-secondary text-white"><?php echo count($records); ?> Records</span>
        </button>
    </div>

    <!-- TAB 1: COLLEGIATE GRADE HISTORY (1ST YEAR 1ST SEM TO CURRENT) -->
    <div id="tabContentGrades" style="<?php echo $activeTab === 'grades' ? '' : 'display:none;'; ?>">
        
        <div class="mpl-filters">
            <label class="mpl-search ah-row-search">
                <i class="fas fa-search"></i>
                <input type="search" id="gradeSubjectSearch" placeholder="Search by subject code, title, grade, or instructor..." aria-label="Search subjects in grade history">
            </label>
            <div class="d-flex gap-2 align-items-center">
                <select id="yearLevelFilter" class="form-select form-select-sm" style="width: auto;" onchange="filterYearLevel(this.value)">
                    <option value="">All Year Levels</option>
                    <option value="1st Year">1st Year</option>
                    <option value="2nd Year">2nd Year</option>
                    <option value="3rd Year">3rd Year</option>
                    <option value="4th Year">4th Year</option>
                </select>
                <a class="mpl-refresh" href="?student_id=<?php echo (int)$studentId; ?>&tab=grades"><i class="fas fa-sync-alt" aria-hidden="true"></i> Refresh</a>
            </div>
        </div>

        <?php if (empty($terms)): ?>
        <div class="mpl-panel" style="padding: 2.5rem; text-align: center;">
            <div style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"><i class="fas fa-book-open"></i></div>
            <h3 style="font-weight: 700; color: var(--sms-heading);">No Collegiate Grades Recorded Yet</h3>
            <p style="color: var(--sms-text-muted); max-width: 480px; margin: 0 auto 1.5rem;">Start building this student's grade history from 1st Year 1st Semester up to their current year level.</p>
            <div class="d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-primary" onclick="openBatchAddModal()">
                    <i class="fas fa-layer-group"></i> Quick Batch Semester Entry
                </button>
                <button type="button" class="btn btn-outline-primary" onclick="openAddSubjectModal()">
                    <i class="fas fa-plus"></i> Add Single Subject
                </button>
            </div>
        </div>
        <?php else: ?>

        <!-- Chronological Semester Blocks -->
        <div id="semesterBlockContainer">
            <?php foreach ($terms as $tIndex => $t): 
                $termStats = $t['stats'];
                $gwa = $termStats['gwa'];
                $gwaFmt = $termStats['gwa_formatted'];
                
                $badgeClass = 'gwa-mid';
                if ($gwa !== null) {
                    if ($gwa <= 1.25) $badgeClass = 'gwa-high';
                    elseif ($gwa <= 1.75) $badgeClass = 'gwa-mid';
                    elseif ($gwa <= 3.00) $badgeClass = 'gwa-low';
                    else $badgeClass = 'gwa-fail';
                }
            ?>
            <section class="ah-term-card" data-year-level="<?php echo htmlspecialchars($t['year_level']); ?>">
                <div class="ah-term-header" onclick="toggleTermCard(this)">
                    <div class="ah-term-title-wrap">
                        <div class="ah-term-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div>
                            <h3 class="ah-term-title"><?php echo htmlspecialchars($t['year_level'] . ' - ' . $t['term_display']); ?></h3>
                            <div class="ah-term-ay">Academic Year <?php echo htmlspecialchars($t['academic_year']); ?> &bull; <?php echo count($t['subjects']); ?> Subject(s)</div>
                        </div>
                    </div>
                    <div class="ah-term-meta" onclick="event.stopPropagation()">
                        <span class="ah-term-badge <?php echo $badgeClass; ?>">
                            <i class="fas fa-chart-line"></i> Term GWA: <strong><?php echo $gwaFmt; ?></strong>
                        </span>
                        <span class="ah-term-badge units">
                            <i class="fas fa-calculator"></i> <?php echo (float)$termStats['total_units']; ?> Units
                        </span>
                        <button type="button" class="ah-term-toggle" aria-label="Toggle semester">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                </div>

                <div class="ah-term-body">
                    <div class="mpl-table-wrap">
                        <table class="mpl-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 14%;">Code</th>
                                    <th style="width: 36%;">Subject Title / Description</th>
                                    <th style="width: 10%; text-align: center;">Units</th>
                                    <th style="width: 12%; text-align: center;">Grade</th>
                                    <th style="width: 12%; text-align: center;">Status / Remarks</th>
                                    <th style="width: 10%;">Instructor</th>
                                    <th style="width: 6%; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($t['subjects'] as $s): 
                                    $gradeVal = trim((string)($s['grade'] ?? ''));
                                    $statusVal = trim((string)($s['status'] ?? 'Passed'));
                                    
                                    $gradePillClass = 'pass';
                                    if (is_numeric($gradeVal)) {
                                        $numG = (float)$gradeVal;
                                        if ($numG <= 1.25) $gradePillClass = 'high';
                                        elseif ($numG <= 1.75) $gradePillClass = 'mid';
                                        elseif ($numG <= 3.00) $gradePillClass = 'pass';
                                        else $gradePillClass = 'fail';
                                    } elseif (strcasecmp($gradeVal, 'INC') === 0) {
                                        $gradePillClass = 'inc';
                                    } elseif (strcasecmp($gradeVal, 'DRP') === 0) {
                                        $gradePillClass = 'drp';
                                    } elseif (strcasecmp($statusVal, 'Enrolled') === 0) {
                                        $gradePillClass = 'enrolled';
                                    }
                                ?>
                                <tr class="subject-row" data-search="<?php echo htmlspecialchars(strtolower($s['subject_code'] . ' ' . $s['subject_name'] . ' ' . $gradeVal . ' ' . ($s['instructor'] ?? '') . ' ' . $statusVal)); ?>">
                                    <td><strong><?php echo htmlspecialchars($s['subject_code']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($s['subject_name']); ?></td>
                                    <td style="text-align: center;"><?php echo number_format((float)$s['units'], 1); ?></td>
                                    <td style="text-align: center;">
                                        <span class="grade-pill <?php echo $gradePillClass; ?>">
                                            <?php echo htmlspecialchars($gradeVal !== '' ? $gradeVal : '—'); ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <?php if ($statusVal === 'Passed'): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success fw-bold">Passed</span>
                                        <?php elseif ($statusVal === 'Failed'): ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger fw-bold">Failed</span>
                                        <?php elseif ($statusVal === 'Incomplete'): ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning fw-bold">INC</span>
                                        <?php elseif ($statusVal === 'Dropped'): ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary fw-bold">Dropped</span>
                                        <?php elseif ($statusVal === 'Enrolled'): ?>
                                            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold">Enrolled</span>
                                        <?php else: ?>
                                            <span class="badge bg-info bg-opacity-10 text-info fw-bold"><?php echo htmlspecialchars($statusVal); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><small style="color:var(--sms-text-muted);"><?php echo htmlspecialchars($s['instructor'] ?? '—'); ?></small></td>
                                    <td style="text-align: center;">
                                        <div class="mpl-actions justify-content-center">
                                            <a href="javascript:void(0)" onclick="openEditSubjectModal(<?php echo (int)$s['id']; ?>)" title="Edit Grade" aria-label="Edit Grade"><i class="fas fa-pen"></i></a>
                                            <a class="danger" href="javascript:void(0)" onclick="deleteSubjectGrade(<?php echo (int)$s['id']; ?>)" title="Delete Grade" aria-label="Delete Grade"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- TAB 2: EDUCATIONAL BACKGROUND / PREVIOUS SCHOOLS -->
    <div id="tabContentBackground" style="<?php echo $activeTab === 'background' ? '' : 'display:none;'; ?>">
        
        <div class="mpl-filters">
            <label class="mpl-search ah-row-search">
                <i class="fas fa-search"></i>
                <input type="search" id="recordSearch" placeholder="Search by school, level, award, or remarks..." aria-label="Search previous school records">
            </label>
            <div class="d-flex gap-2">
                <button type="button" class="mpl-add" onclick="openAddModal()">
                    <i class="fas fa-plus" aria-hidden="true"></i> Add School Record
                </button>
                <a class="mpl-refresh" href="?student_id=<?php echo (int)$studentId; ?>&tab=background"><i class="fas fa-sync-alt" aria-hidden="true"></i> Refresh</a>
            </div>
        </div>

        <section class="mpl-panel ah-panel">
            <div class="mpl-panel-head">
                <div>
                    <h2>Educational Background &amp; Previous Schools</h2>
                    <p><?php echo count($records); ?> record(s) on file.</p>
                </div>
            </div>
            <div class="mpl-table-wrap">
                <table class="mpl-table" id="studentRecordTable">
                    <thead>
                        <tr>
                            <th>School Name</th>
                            <th>Level</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Awards / Honors</th>
                            <th>Remarks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                        <tr>
                            <td colspan="7" style="text-align:center;color:var(--sms-text-muted);padding:1.5rem;">
                                <i class="fas fa-info-circle"></i> No prior educational background recorded. Click "Add School Record" to add elementary, junior high, or senior high schools.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($records as $r): ?>
                        <tr data-search="<?php echo htmlspecialchars(strtolower(($r['school_name'] ?? '') . ' ' . ($r['level'] ?? '') . ' ' . ($r['awards'] ?? '') . ' ' . ($r['remarks'] ?? ''))); ?>">
                            <td><strong><?php echo htmlspecialchars($r['school_name']); ?></strong></td>
                            <td><span class="ah-pill"><?php echo htmlspecialchars($r['level'] ?? '—'); ?></span></td>
                            <td><?php echo htmlspecialchars((string)($r['from_year'] ?? '-')); ?></td>
                            <td><?php echo htmlspecialchars((string)($r['to_year'] ?? '-')); ?></td>
                            <td><?php echo htmlspecialchars($r['awards'] ?? '—'); ?></td>
                            <td><small style="color:var(--sms-text-muted);"><?php echo htmlspecialchars($r['remarks'] ?? '—'); ?></small></td>
                            <td>
                                <div class="mpl-actions">
                                    <a href="javascript:void(0)" onclick="openEditModal(<?php echo (int)$r['id']; ?>)" title="Edit" aria-label="Edit"><i class="fas fa-pen"></i></a>
                                    <a class="danger" href="javascript:void(0)" onclick="deleteRecord(<?php echo (int)$r['id']; ?>)" title="Delete" aria-label="Delete"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

<?php endif; ?>

</div>
</div>

<?php if ($student): ?>
<!-- ====================================================================
     MODALS FOR STUDENT GRADE HISTORY & PREVIOUS SCHOOLS
     ==================================================================== -->

<!-- Modal 1: Single Subject Add / Edit Modal -->
<div class="modal fade" id="subjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="reg-modal-header">
                <h5 class="modal-title" id="subjectModalTitle">Add Subject Grade</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="subjectForm" onsubmit="return handleSubjectForm(event)">
                <div class="modal-body">
                    <input type="hidden" name="id" id="subjectId" value="">
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Year Level *</label>
                            <select name="year_level" id="subjectYearLevel" class="form-select" required>
                                <option value="1st Year">1st Year</option>
                                <option value="2nd Year">2nd Year</option>
                                <option value="3rd Year">3rd Year</option>
                                <option value="4th Year">4th Year</option>
                                <option value="5th Year">5th Year</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Term / Semester *</label>
                            <select name="term" id="subjectTerm" class="form-select" required>
                                <option value="1st">1st Semester</option>
                                <option value="2nd">2nd Semester</option>
                                <option value="Summer">Summer / Mid-Year</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Academic Year *</label>
                            <input type="text" name="academic_year" id="subjectAcademicYear" class="form-control" placeholder="e.g. 2024-2025" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Subject Code *</label>
                            <input type="text" name="subject_code" id="subjectCode" class="form-control" placeholder="e.g., CC101, IT202" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Subject Title / Description *</label>
                            <input type="text" name="subject_name" id="subjectName" class="form-control" placeholder="e.g., Data Structures and Algorithms" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Units *</label>
                            <input type="number" step="0.5" min="0.5" max="12" name="units" id="subjectUnits" class="form-control" value="3.0" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Final Grade</label>
                            <input type="text" name="grade" id="subjectGrade" class="form-control" placeholder="e.g., 1.25, 1.50, INC, DRP" oninput="autoInferStatus()">
                            <small class="text-muted">Scale: 1.00 (Highest) - 3.00 (Pass), 5.00 (Fail)</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Status</label>
                            <select name="status" id="subjectStatus" class="form-select">
                                <option value="Passed">Passed</option>
                                <option value="Failed">Failed</option>
                                <option value="Incomplete">Incomplete (INC)</option>
                                <option value="Dropped">Dropped (DRP)</option>
                                <option value="Enrolled">Enrolled / Ongoing</option>
                                <option value="Credited">Credited (Transferee)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Instructor</label>
                            <input type="text" name="instructor" id="subjectInstructor" class="form-control" placeholder="e.g., Prof. Santos">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Remarks / Notes</label>
                            <input type="text" name="remarks" id="subjectRemarks" class="form-control" placeholder="Optional notes (e.g. Cleared INC, Equivalent subject)">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Subject Grade</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 2: Batch Semester Entry Modal -->
<div class="modal fade" id="batchModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="reg-modal-header">
                <h5 class="modal-title">Quick Batch Semester Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="batchForm" onsubmit="return handleBatchForm(event)">
                <div class="modal-body">
                    <p class="text-muted mb-3">Add multiple subjects and grades for an entire term at once.</p>
                    
                    <div class="row g-3 mb-3 p-3 bg-light rounded">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Year Level *</label>
                            <select id="batchYearLevel" class="form-select" required>
                                <option value="1st Year">1st Year</option>
                                <option value="2nd Year">2nd Year</option>
                                <option value="3rd Year">3rd Year</option>
                                <option value="4th Year">4th Year</option>
                                <option value="5th Year">5th Year</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Term / Semester *</label>
                            <select id="batchTerm" class="form-select" required>
                                <option value="1st">1st Semester</option>
                                <option value="2nd">2nd Semester</option>
                                <option value="Summer">Summer / Mid-Year</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Academic Year *</label>
                            <input type="text" id="batchAcademicYear" class="form-control" value="<?php echo date('Y') . '-' . (date('Y') + 1); ?>" required>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle" id="batchSubjectsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 18%;">Subject Code *</th>
                                    <th style="width: 38%;">Subject Title / Description *</th>
                                    <th style="width: 12%;">Units *</th>
                                    <th style="width: 14%;">Grade</th>
                                    <th style="width: 14%;">Instructor</th>
                                    <th style="width: 4%; text-align: center;">&times;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i < 6; $i++): ?>
                                <tr>
                                    <td><input type="text" class="form-control form-control-sm b-code" placeholder="e.g. IT10<?php echo $i+1; ?>"></td>
                                    <td><input type="text" class="form-control form-control-sm b-name" placeholder="Subject Title"></td>
                                    <td><input type="number" step="0.5" class="form-control form-control-sm b-units" value="3.0"></td>
                                    <td><input type="text" class="form-control form-control-sm b-grade" placeholder="1.25"></td>
                                    <td><input type="text" class="form-control form-control-sm b-inst" placeholder="Prof. Name"></td>
                                    <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeBatchRow(this)"><i class="fas fa-trash"></i></button></td>
                                </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="addBatchRow()">
                        <i class="fas fa-plus"></i> Add Row
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save All Subjects</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 3: Official Transcript & Copy of Grades Preview / Print Modal -->
<div class="modal fade" id="transcriptModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="fas fa-file-alt"></i> Official Transcript &amp; Final Report of Grades</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="printableTranscript" class="transcript-print-sheet">
                    <!-- School Header -->
                    <div class="text-center mb-4 pb-2 border-bottom">
                        <h2 style="font-size: 1.35rem; font-weight: 800; text-transform: uppercase; margin-bottom: 2px;">Bestlink College of the Philippines</h2>
                        <div style="font-size: 0.85rem; color: #4b5563;">Bulacan Campus &bull; Office of the University Registrar</div>
                        <h3 style="font-size: 1.05rem; font-weight: 800; text-transform: uppercase; margin-top: 10px; letter-spacing: 0.5px;">Official Transcript / Summary of Academic Grades</h3>
                    </div>

                    <!-- Student Metadata -->
                    <div class="row g-2 mb-4 pb-2 border-bottom" style="font-size: 0.85rem;">
                        <div class="col-6"><strong>Student Name:</strong> <?php echo htmlspecialchars($student['last_name'] . ', ' . $student['first_name'] . ' ' . ($student['middle_name'] ?? '')); ?></div>
                        <div class="col-6"><strong>Student Number:</strong> <?php echo htmlspecialchars($student['student_number']); ?></div>
                        <div class="col-6"><strong>Degree Program:</strong> <?php echo htmlspecialchars($student['program_course'] ?? '—'); ?></div>
                        <div class="col-6"><strong>Year &amp; Section:</strong> <?php echo htmlspecialchars($student['year_section'] ?? '—'); ?></div>
                        <div class="col-6"><strong>Date Issued:</strong> <?php echo date('F d, Y'); ?></div>
                        <div class="col-6"><strong>Academic Standing:</strong> <strong><?php echo htmlspecialchars($summary['academic_standing']); ?></strong></div>
                    </div>

                    <!-- Semester-by-Semester Tables -->
                    <?php if (!empty($terms)): ?>
                    <?php foreach ($terms as $t): ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1" style="font-weight: bold; font-size: 0.9rem;">
                                <span><?php echo htmlspecialchars($t['year_level'] . ' - ' . $t['term_display'] . ' (A.Y. ' . $t['academic_year'] . ')'); ?></span>
                                <span>Term GWA: <?php echo htmlspecialchars($t['stats']['gwa_formatted']); ?> &bull; Units: <?php echo (float)$t['stats']['total_units']; ?></span>
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th style="width: 15%;">Course Code</th>
                                        <th style="width: 50%;">Course Title</th>
                                        <th style="width: 10%; text-align: center;">Units</th>
                                        <th style="width: 12%; text-align: center;">Final Grade</th>
                                        <th style="width: 13%; text-align: center;">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($t['subjects'] as $s): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($s['subject_code']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($s['subject_name']); ?></td>
                                        <td style="text-align: center;"><?php echo number_format((float)$s['units'], 1); ?></td>
                                        <td style="text-align: center;"><strong><?php echo htmlspecialchars($s['grade'] ?? '—'); ?></strong></td>
                                        <td style="text-align: center;"><?php echo htmlspecialchars($s['status'] ?? 'Passed'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-muted">No collegiate grade records available.</p>
                    <?php endif; ?>

                    <!-- Final Cumulative Summary -->
                    <div class="p-3 bg-light border rounded mt-4" style="font-size: 0.9rem;">
                        <div class="row">
                            <div class="col-4"><strong>Total Units Completed:</strong> <?php echo (float)$summary['total_units_passed']; ?> Units</div>
                            <div class="col-4"><strong>Cumulative GWA:</strong> <span style="font-size: 1.1rem; font-weight: bold; color: #1d4ed8;"><?php echo $summary['cumulative_gwa_fmt']; ?></span></div>
                            <div class="col-4"><strong>Official Standing:</strong> <?php echo htmlspecialchars($summary['academic_standing']); ?></div>
                        </div>
                    </div>

                    <!-- Signatures -->
                    <div class="row mt-5 pt-4 text-center">
                        <div class="col-6">
                            <div style="border-top: 1px solid #000; width: 70%; margin: 0 auto; padding-top: 5px;">
                                <strong>Records In-Charge</strong><br><small>Office of the Registrar</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div style="border-top: 1px solid #000; width: 70%; margin: 0 auto; padding-top: 5px;">
                                <strong>University Registrar</strong><br><small>Bestlink College of the Philippines</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Print Official Copy</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 4: Add/Edit Previous Educational Background Modal -->
<div class="modal fade" id="academicModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="reg-modal-header">
                <h5 class="modal-title" id="academicModalTitle">Add School Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="academicForm" onsubmit="return handleAcademicForm(event)">
                <div class="modal-body">
                    <input type="hidden" name="id" id="academicId" value="">
                    <div class="reg-form-section">
                        <div class="reg-form-group">
                            <label>School Name *</label>
                            <input type="text" name="school_name" id="academicSchoolName" class="form-control" required placeholder="e.g., Bestlink College of the Philippines">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Level</label>
                                    <select name="level" id="academicLevel" class="form-select">
                                        <option value="">Select Level</option>
                                        <option value="Elementary">Elementary</option>
                                        <option value="Junior High School">Junior High School</option>
                                        <option value="Senior High School">Senior High School</option>
                                        <option value="College">College</option>
                                        <option value="Vocational">Vocational</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="reg-form-group">
                                    <label>From Year</label>
                                    <input type="number" name="from_year" id="academicFromYear" class="form-control" min="1950" max="2100" placeholder="2018">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="reg-form-group">
                                    <label>To Year</label>
                                    <input type="number" name="to_year" id="academicToYear" class="form-control" min="1950" max="2100" placeholder="2022">
                                </div>
                            </div>
                        </div>

                        <div class="reg-form-group">
                            <label>Awards / Honors</label>
                            <input type="text" name="awards" id="academicAwards" class="form-control" placeholder="e.g., With Honors, Leadership Award">
                        </div>

                        <div class="reg-form-group">
                            <label>Remarks</label>
                            <textarea name="remarks" id="academicRemarks" class="form-control" rows="2" placeholder="Additional notes"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-reg-primary">Save Record</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script src="<?php echo BASE_URL; ?>/modules/registrar/assets/js/registrar.js"></script>
<script>
const API_BASE = '<?php echo BASE_URL; ?>/modules/registrar/api';
const CSRF = '<?= e(csrfToken()) ?>';

document.querySelectorAll('.mpl-alert-auto').forEach(function (alert) {
    window.setTimeout(function () {
        alert.classList.add('mpl-alert-hide');
        window.setTimeout(function () { alert.remove(); }, 350);
    }, 2000);
});

<?php if (!$student): ?>
/* ==================== Dashboard Client-side Search ==================== */
const dashboardFilter = document.getElementById('dashboardFilter');
if (dashboardFilter) {
    dashboardFilter.addEventListener('input', debounce(function () {
        const q = dashboardFilter.value.trim().toLowerCase();
        document.querySelectorAll('#dashboardTable tbody tr.dashboard-row').forEach(function (row) {
            const match = (row.dataset.search || row.textContent || '').toLowerCase();
            row.style.display = match.includes(q) ? '' : 'none';
        });
    }, 150));
}

<?php else: ?>
/* ==================== Single Student Grade Management ==================== */
const studentId = <?php echo (int)$studentId; ?>;
const allSubjects = <?php 
    $flatSubjs = [];
    if (!empty($terms)) {
        foreach ($terms as $t) {
            foreach ($t['subjects'] as $s) {
                $flatSubjs[] = $s;
            }
        }
    }
    echo json_encode($flatSubjs, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); 
?>;
const academicRecords = <?php echo json_encode($records, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

function postJson(action, payload) {
    return fetch(API_BASE + '/academic.php?action=' + action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': CSRF
        },
        body: JSON.stringify(payload)
    }).then(function (r) { return r.json(); });
}

function switchTab(tabName) {
    const gradesTab = document.getElementById('tabContentGrades');
    const bgTab = document.getElementById('tabContentBackground');
    const buttons = document.querySelectorAll('.ah-tab-btn');

    if (tabName === 'grades') {
        gradesTab.style.display = '';
        bgTab.style.display = 'none';
        buttons[0].classList.add('active');
        buttons[1].classList.remove('active');
    } else {
        gradesTab.style.display = 'none';
        bgTab.style.display = '';
        buttons[0].classList.remove('active');
        buttons[1].classList.add('active');
    }
}

function toggleTermCard(headerElem) {
    const card = headerElem.closest('.ah-term-card');
    card.classList.toggle('collapsed');
}

// Live Search for Subjects in Grade History
const gradeSubjectSearch = document.getElementById('gradeSubjectSearch');
if (gradeSubjectSearch) {
    gradeSubjectSearch.addEventListener('input', debounce(function () {
        const q = gradeSubjectSearch.value.trim().toLowerCase();
        document.querySelectorAll('#semesterBlockContainer tr.subject-row').forEach(function (row) {
            const match = (row.dataset.search || row.textContent || '').toLowerCase();
            row.style.display = match.includes(q) ? '' : 'none';
        });
    }, 150));
}

function filterYearLevel(yearVal) {
    document.querySelectorAll('#semesterBlockContainer .ah-term-card').forEach(function (card) {
        if (!yearVal || card.dataset.yearLevel === yearVal) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

/* ==================== Single Subject Modal ==================== */
function resetSubjectForm() {
    document.getElementById('subjectForm').reset();
    document.getElementById('subjectId').value = '';
    document.getElementById('subjectAcademicYear').value = '<?php echo date('Y') . '-' . (date('Y') + 1); ?>';
    document.getElementById('subjectUnits').value = '3.0';
    document.getElementById('subjectStatus').value = 'Passed';
}

function openAddSubjectModal() {
    resetSubjectForm();
    document.getElementById('subjectModalTitle').textContent = 'Add Subject Grade';
    showRegModal('subjectModal');
}

function openAddSubjectModalWithTerm(yearLevel, term, ay) {
    resetSubjectForm();
    document.getElementById('subjectModalTitle').textContent = 'Add Subject - ' + yearLevel + ' (' + term + ' Sem)';
    document.getElementById('subjectYearLevel').value = yearLevel;
    document.getElementById('subjectTerm').value = term;
    document.getElementById('subjectAcademicYear').value = ay;
    showRegModal('subjectModal');
}

function openEditSubjectModal(subjId) {
    const subj = allSubjects.find(s => parseInt(s.id, 10) === subjId);
    if (!subj) {
        showRegError('Subject record not found');
        return;
    }

    document.getElementById('subjectModalTitle').textContent = 'Edit Subject Grade - ' + subj.subject_code;
    document.getElementById('subjectId').value = subj.id;
    document.getElementById('subjectYearLevel').value = subj.year_level || '1st Year';
    document.getElementById('subjectTerm').value = subj.term || '1st';
    document.getElementById('subjectAcademicYear').value = subj.academic_year || '';
    document.getElementById('subjectCode').value = subj.subject_code || '';
    document.getElementById('subjectName').value = subj.subject_name || '';
    document.getElementById('subjectUnits').value = subj.units || '3.0';
    document.getElementById('subjectGrade').value = subj.grade || '';
    document.getElementById('subjectStatus').value = subj.status || 'Passed';
    document.getElementById('subjectInstructor').value = subj.instructor || '';
    document.getElementById('subjectRemarks').value = subj.remarks || '';

    showRegModal('subjectModal');
}

function autoInferStatus() {
    const grade = document.getElementById('subjectGrade').value.trim();
    const statusSelect = document.getElementById('subjectStatus');
    if (!grade) {
        statusSelect.value = 'Enrolled';
    } else if (grade.toUpperCase() === 'INC') {
        statusSelect.value = 'Incomplete';
    } else if (grade.toUpperCase() === 'DRP') {
        statusSelect.value = 'Dropped';
    } else if (!isNaN(parseFloat(grade))) {
        const num = parseFloat(grade);
        statusSelect.value = num <= 3.0 ? 'Passed' : 'Failed';
    }
}

async function handleSubjectForm(e) {
    e.preventDefault();
    const form = document.getElementById('subjectForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    data.student_id = studentId;

    if (!data.id) delete data.id;

    try {
        const res = await postJson('save_subject', data);
        if (res.success) {
            showRegSuccess(res.message || 'Subject grade saved');
            hideRegModal('subjectModal');
            setTimeout(() => location.reload(), 700);
        } else {
            showRegError(res.error || 'Save failed');
        }
    } catch (err) {
        showRegError('Error: ' + err.message);
    }
    return false;
}

async function deleteSubjectGrade(subjId) {
    if (!confirm('Are you sure you want to delete this subject grade record?')) return;
    try {
        const res = await postJson('delete_subject', { id: subjId });
        if (res.success) {
            showRegSuccess('Subject grade deleted');
            setTimeout(() => location.reload(), 700);
        } else {
            showRegError(res.error || 'Delete failed');
        }
    } catch (err) {
        showRegError('Error: ' + err.message);
    }
}

/* ==================== Batch Add Modal ==================== */
function openBatchAddModal() {
    showRegModal('batchModal');
}

function addBatchRow() {
    const tbody = document.querySelector('#batchSubjectsTable tbody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="text" class="form-control form-control-sm b-code" placeholder="Subject Code"></td>
        <td><input type="text" class="form-control form-control-sm b-name" placeholder="Subject Title"></td>
        <td><input type="number" step="0.5" class="form-control form-control-sm b-units" value="3.0"></td>
        <td><input type="text" class="form-control form-control-sm b-grade" placeholder="1.25"></td>
        <td><input type="text" class="form-control form-control-sm b-inst" placeholder="Prof. Name"></td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeBatchRow(this)"><i class="fas fa-trash"></i></button></td>
    `;
    tbody.appendChild(tr);
}

function removeBatchRow(btn) {
    const tr = btn.closest('tr');
    if (document.querySelectorAll('#batchSubjectsTable tbody tr').length > 1) {
        tr.remove();
    } else {
        tr.querySelectorAll('input').forEach(i => i.value = '');
    }
}

async function handleBatchForm(e) {
    e.preventDefault();
    const yearLevel = document.getElementById('batchYearLevel').value;
    const term = document.getElementById('batchTerm').value;
    const academicYear = document.getElementById('batchAcademicYear').value;

    const subjects = [];
    document.querySelectorAll('#batchSubjectsTable tbody tr').forEach(tr => {
        const code = tr.querySelector('.b-code').value.trim();
        const name = tr.querySelector('.b-name').value.trim();
        const units = parseFloat(tr.querySelector('.b-units').value) || 3.0;
        const grade = tr.querySelector('.b-grade').value.trim();
        const inst = tr.querySelector('.b-inst').value.trim();

        if (code && name) {
            subjects.push({
                subject_code: code,
                subject_name: name,
                units: units,
                grade: grade || null,
                instructor: inst || null
            });
        }
    });

    if (subjects.length === 0) {
        showRegError('Please enter at least one valid subject (code and title).');
        return false;
    }

    try {
        const payload = {
            student_id: studentId,
            year_level: yearLevel,
            term: term,
            academic_year: academicYear,
            subjects: subjects
        };

        const res = await postJson('batch_save_subjects', payload);
        if (res.success) {
            showRegSuccess(res.message || 'Batch subjects saved');
            hideRegModal('batchModal');
            setTimeout(() => location.reload(), 700);
        } else {
            showRegError(res.error || 'Batch save failed');
        }
    } catch (err) {
        showRegError('Error: ' + err.message);
    }
    return false;
}

/* ==================== Transcript Modal ==================== */
function openTranscriptModal() {
    showRegModal('transcriptModal');
}

/* ==================== Educational Background Records ==================== */
const recordSearch = document.getElementById('recordSearch');
if (recordSearch) {
    recordSearch.addEventListener('input', debounce(function () {
        const q = recordSearch.value.trim().toLowerCase();
        document.querySelectorAll('#studentRecordTable tbody tr[data-search]').forEach(function (row) {
            const match = (row.dataset.search || row.textContent || '').toLowerCase();
            row.style.display = match.includes(q) ? '' : 'none';
        });
    }, 150));
}

function resetAcademicForm() {
    document.getElementById('academicForm').reset();
    document.getElementById('academicId').value = '';
}

function openAddModal() {
    resetAcademicForm();
    document.getElementById('academicModalTitle').textContent = 'Add School Record';
    showRegModal('academicModal');
}

function openEditModal(recordId) {
    const record = academicRecords.find(r => parseInt(r.id, 10) === recordId);
    if (!record) {
        showRegError('Record not found');
        return;
    }

    document.getElementById('academicModalTitle').textContent = 'Edit School Record';
    document.getElementById('academicId').value = record.id;
    document.getElementById('academicSchoolName').value = record.school_name || '';
    document.getElementById('academicLevel').value = record.level || '';
    document.getElementById('academicFromYear').value = record.from_year || '';
    document.getElementById('academicToYear').value = record.to_year || '';
    document.getElementById('academicAwards').value = record.awards || '';
    document.getElementById('academicRemarks').value = record.remarks || '';

    showRegModal('academicModal');
}

async function handleAcademicForm(e) {
    e.preventDefault();
    const form = document.getElementById('academicForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    data.student_id = studentId;

    if (!data.id) delete data.id;

    try {
        const result = await postJson('save', data);
        if (result.success) {
            showRegSuccess(result.message || 'Academic record saved');
            hideRegModal('academicModal');
            setTimeout(() => location.reload(), 700);
        } else {
            showRegError(result.error || 'Save failed');
        }
    } catch (error) {
        showRegError('Error: ' + error.message);
    }
    return false;
}

async function deleteRecord(recordId) {
    if (!confirm('Delete this academic history record?')) return;
    try {
        const result = await postJson('delete', { id: recordId });
        if (result.success) {
            showRegSuccess('Record deleted');
            setTimeout(() => location.reload(), 700);
        } else {
            showRegError(result.error || 'Delete failed');
        }
    } catch (error) {
        showRegError('Error: ' + error.message);
    }
}
<?php endif; ?>
</script>

<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>