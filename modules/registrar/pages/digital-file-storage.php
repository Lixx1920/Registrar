<?php
/**
 * SMS 2 - Digital File Storage
 * Module: Registrar
 * Centralized repository for student institutional documents
 * (Form 138, Form 137, Good Moral, PSA Birth Certificate, Barangay Clearance)
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/registrar-service.php';
require_once __DIR__ . '/../includes/storage-service.php';

regRequireAction('registrar.view');

$pageTitle    = 'Digital File Storage';
$activeModule = 'registrar';
$activePage   = 'digital-file-storage';

$db = db();

// Get student from query parameter (may be absent for main dashboard)
$studentId = (int)($_GET['student_id'] ?? 0);
$student   = null;
$notFound  = false;
$studentDocs = [];

if ($studentId > 0) {
    $student = regGetStudent($studentId);
    if (!$student) {
        $notFound  = true;
        $studentId = 0;
    } else {
        $studentDocs = regGetStudentDigitalDocuments($studentId);
    }
}

// Global Summary Stats for the 5 Document Cards
$summary = regGetDigitalStorageSummary();
$docTypes = regGetDigitalDocumentTypes();

// If on main dashboard, load all students with aggregated document checklist flags
$studentsList = [];
$programsList = [];
if (!$student) {
    $stmt = $db->query("
        SELECT 
            s.id, s.student_number, s.first_name, s.middle_name, s.last_name, s.program_course, s.year_section, s.status,
            MAX(CASE WHEN f.category = 'form_138' AND f.is_deleted = 0 THEN f.id ELSE NULL END) AS file_id_form_138,
            MAX(CASE WHEN f.category = 'form_137' AND f.is_deleted = 0 THEN f.id ELSE NULL END) AS file_id_form_137,
            MAX(CASE WHEN f.category = 'good_moral' AND f.is_deleted = 0 THEN f.id ELSE NULL END) AS file_id_good_moral,
            MAX(CASE WHEN f.category = 'psa_birth_cert' AND f.is_deleted = 0 THEN f.id ELSE NULL END) AS file_id_psa_birth_cert,
            MAX(CASE WHEN f.category = 'barangay_clearance' AND f.is_deleted = 0 THEN f.id ELSE NULL END) AS file_id_barangay_clearance,
            COUNT(DISTINCT CASE WHEN f.category IN ('form_138', 'form_137', 'good_moral', 'psa_birth_cert', 'barangay_clearance') AND f.is_deleted = 0 THEN f.category ELSE NULL END) AS total_uploaded_docs
        FROM `reg_students` s
        LEFT JOIN `reg_files` f ON f.student_id = s.id AND f.is_deleted = 0 AND f.status = 'Active'
        WHERE s.status = 'Active'
        GROUP BY s.id
        ORDER BY s.last_name ASC, s.first_name ASC
    ");
    $studentsList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($studentsList as $s) {
        if (!empty($s['program_course']) && !in_array($s['program_course'], $programsList, true)) {
            $programsList[] = $s['program_course'];
        }
    }
    sort($programsList);
}

$breadcrumbs = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Digital File Storage', 'url' => $student ? (BASE_URL . '/modules/registrar/pages/digital-file-storage.php') : null],
];
if ($student) {
    $breadcrumbs[] = ['label' => $student['first_name'] . ' ' . $student['last_name'], 'url' => null];
    $pageBannerBackUrl   = BASE_URL . '/modules/registrar/pages/digital-file-storage.php';
    $pageBannerBackLabel = 'Back to Storage Directory';
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

<style>
/* Digital File Storage Custom Aesthetic Styles */
.dfs-doc-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.dfs-doc-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid rgba(0, 0, 0, 0.08);
    padding: 1.25rem 1.1rem;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
    transition: all 0.2s ease-in-out;
    position: relative;
    overflow: hidden;
    cursor: pointer;
}
.dfs-doc-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.08);
    border-color: rgba(37, 99, 235, 0.3);
}
.dfs-doc-card.active-filter {
    border-color: #2563eb;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
    background: #f8faff;
}
.dfs-doc-card-top {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.85rem;
}
.dfs-doc-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
}
.dfs-doc-card-title {
    font-size: 0.92rem;
    font-weight: 700;
    color: #1e293b;
    line-height: 1.25;
    margin: 0;
}
.dfs-doc-stat {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    margin-bottom: 0.5rem;
}
.dfs-doc-stat strong {
    font-size: 1.4rem;
    font-weight: 800;
    color: #0f172a;
}
.dfs-doc-stat span {
    font-size: 0.8rem;
    font-weight: 600;
    color: #64748b;
}
.dfs-doc-progress {
    height: 6px;
    border-radius: 999px;
    background: #e2e8f0;
    overflow: hidden;
}
.dfs-doc-progress-bar {
    height: 100%;
    border-radius: 999px;
    transition: width 0.4s ease;
}

/* Student document cards grid */
.student-doc-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.25rem;
}
.student-doc-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    padding: 1.25rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.2s ease;
}
.student-doc-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 6px 14px rgba(0,0,0,0.05);
}
.student-doc-card.is-uploaded {
    border-left: 4px solid #10b981;
}
.student-doc-card.is-missing {
    border-left: 4px solid #94a3b8;
    background: #fafafa;
}
.student-doc-card-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.85rem;
}
.student-doc-badge {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.25rem 0.6rem;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}
.student-doc-badge.uploaded {
    background: #d1fae5;
    color: #065f46;
}
.student-doc-badge.missing {
    background: #f1f5f9;
    color: #64748b;
}
.student-doc-meta {
    background: #f8fafc;
    border-radius: 8px;
    padding: 0.75rem;
    font-size: 0.82rem;
    color: #475569;
    margin-bottom: 1rem;
    border: 1px solid #edf2f7;
}
.student-doc-meta div {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.25rem;
}
.student-doc-meta div:last-child {
    margin-bottom: 0;
}
.student-doc-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}
.student-doc-actions .btn {
    font-size: 0.82rem;
    padding: 0.4rem 0.75rem;
    font-weight: 600;
    border-radius: 6px;
}
.btn-doc-view {
    background-color: #2563eb;
    border-color: #2563eb;
    color: #ffffff;
    flex: 1;
}
.btn-doc-view:hover:not(:disabled) {
    background-color: #1d4ed8;
    border-color: #1d4ed8;
    color: #ffffff;
}
.btn-doc-view:disabled {
    background-color: #e2e8f0;
    border-color: #e2e8f0;
    color: #94a3b8;
    cursor: not-allowed;
    opacity: 0.8;
}

/* Checklist Pills in Table */
.dfs-checklist {
    display: flex;
    gap: 0.35rem;
    align-items: center;
}
.dfs-pill {
    width: 26px;
    height: 26px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    text-decoration: none;
    transition: transform 0.15s ease;
}
.dfs-pill.uploaded {
    background: #d1fae5;
    color: #059669;
    border: 1px solid #a7f3d0;
}
.dfs-pill.missing {
    background: #f1f5f9;
    color: #94a3b8;
    border: 1px dashed #cbd5e1;
}
.dfs-pill:hover {
    transform: scale(1.15);
}

/* Modal Preview Box */
.doc-preview-container {
    width: 100%;
    min-height: 520px;
    height: 70vh;
    background: #f1f5f9;
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}
.doc-preview-container iframe {
    width: 100%;
    height: 100%;
    border: none;
}
.doc-preview-container img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}
.upload-dropzone {
    border: 2px dashed #93c5fd;
    background: #f8faff;
    border-radius: 12px;
    padding: 2.5rem 1.5rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
}
.upload-dropzone:hover, .upload-dropzone.dragover {
    border-color: #2563eb;
    background: #eff6ff;
}
</style>

<?php renderBreadcrumbs($breadcrumbs); ?>

<div class="container-fluid py-4">
<div class="mpl" data-mpl>

<?php if (!$student): ?>

    <!-- ==================== DASHBOARD VIEW ==================== -->
    <div class="mpl-top">
        <div>
            <h1 class="h3 text-dark mb-1"><i class="fas fa-folder-open text-primary me-2"></i>Digital File Storage</h1>
            <p>Secure digital storage for student credentials and institutional records with SHA-256 hash verification.</p>
        </div>
        <div class="mpl-toolbar">
            <a class="mpl-add" href="<?php echo BASE_URL; ?>/modules/registrar/pages/student-information-system.php">
                <i class="fas fa-arrow-left" aria-hidden="true"></i> Back to Student Records
            </a>
        </div>
    </div>

    <?php if ($notFound): ?>
    <div class="mpl-alert mpl-alert-auto" style="border-color: rgba(217, 119, 6, 0.28); background: rgba(245, 158, 11, 0.08); color: #92400e;">
        <i class="fas fa-exclamation-triangle"></i> Student #<?php echo (int)($_GET['student_id'] ?? 0); ?> was not found.
    </div>
    <?php endif; ?>

    <!-- 5 Document Types Overview Cards -->
    <div class="dfs-doc-cards" aria-label="Required Document Categories">
        <?php foreach ($docTypes as $code => $def): 
            $count = $summary['category_counts'][$code] ?? 0;
            $total = $summary['total_students'] > 0 ? $summary['total_students'] : 1;
            $pct = round(($count / $total) * 100);
        ?>
        <article class="dfs-doc-card" data-doc-filter="<?php echo htmlspecialchars($code); ?>" onclick="filterByDocType('<?php echo htmlspecialchars($code); ?>')" title="Filter students missing or having <?php echo htmlspecialchars($def['title']); ?>">
            <div class="dfs-doc-card-top">
                <div class="dfs-doc-icon" style="background-color: <?php echo $def['bg_light']; ?>; color: <?php echo $def['color']; ?>; border: 1px solid <?php echo $def['border']; ?>;">
                    <i class="fas <?php echo $def['icon']; ?>"></i>
                </div>
                <div>
                    <h3 class="dfs-doc-card-title"><?php echo htmlspecialchars($def['title']); ?></h3>
                </div>
            </div>
            <div class="dfs-doc-stat">
                <strong><?php echo $count; ?> <small style="font-size:0.75rem;font-weight:normal;color:#64748b;">/ <?php echo $summary['total_students']; ?></small></strong>
                <span><?php echo $pct; ?>% Submitted</span>
            </div>
            <div class="dfs-doc-progress">
                <div class="dfs-doc-progress-bar" style="width: <?php echo $pct; ?>%; background-color: <?php echo $def['color']; ?>;"></div>
            </div>
        </article>
        <?php endforeach; ?>
    </div>

    <!-- Summary Stats -->
    <section class="mpl-stats" aria-label="Digital storage summary">
        <article class="mpl-stat">
            <div class="mpl-stat-icon blue"><i class="fas fa-users"></i></div>
            <div>
                <span>Total Active Students</span>
                <strong><?php echo $summary['total_students']; ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon purple"><i class="fas fa-file-archive"></i></div>
            <div>
                <span>Total Digital Files</span>
                <strong><?php echo $summary['total_files']; ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon green"><i class="fas fa-check-double"></i></div>
            <div>
                <span>Complete (5/5 Docs)</span>
                <strong><?php echo $summary['fully_compliant']; ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon amber"><i class="fas fa-exclamation-triangle"></i></div>
            <div>
                <span>Incomplete Records</span>
                <strong><?php echo $summary['incomplete_count']; ?></strong>
            </div>
        </article>
    </section>

    <!-- Filters & Search Toolbar -->
    <div class="mpl-filters">
        <label class="mpl-search">
            <i class="fas fa-search"></i>
            <input type="search" id="studentTableSearch" placeholder="Search by student number, name, or program..." aria-label="Search students">
        </label>
        <div class="mpl-select-wrap">
            <select id="statusFilter" aria-label="Filter by compliance status" class="form-select form-select-sm">
                <option value="all">All Compliance Statuses</option>
                <option value="complete">Fully Complete (5/5)</option>
                <option value="incomplete">Incomplete (&lt;5 Docs)</option>
            </select>
        </div>
        <div class="mpl-select-wrap">
            <select id="programFilter" aria-label="Filter by program" class="form-select form-select-sm">
                <option value="all">All Academic Programs</option>
                <?php foreach ($programsList as $p): ?>
                <option value="<?php echo htmlspecialchars(strtolower($p)); ?>"><?php echo htmlspecialchars($p); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mpl-select-wrap">
            <select id="docTypeSelectFilter" aria-label="Filter by missing document" class="form-select form-select-sm">
                <option value="all">All Document Requirements</option>
                <option value="missing_form_138">Missing Form 138</option>
                <option value="missing_form_137">Missing Form 137</option>
                <option value="missing_good_moral">Missing Good Moral</option>
                <option value="missing_psa_birth_cert">Missing PSA Birth Cert</option>
                <option value="missing_barangay_clearance">Missing Brgy Clearance</option>
            </select>
        </div>
        <a class="mpl-refresh" href="?" title="Reset filters"><i class="fas fa-sync-alt" aria-hidden="true"></i> Refresh</a>
    </div>

    <!-- Main Students Directory Table -->
    <section class="mpl-panel">
        <div class="mpl-panel-head">
            <div>
                <h2>Student Document Repository</h2>
                <p><?php echo count($studentsList); ?> total student records</p>
            </div>
        </div>
        <div class="mpl-table-wrap">
            <table class="mpl-table" id="digitalFilesTable">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Program &amp; Year</th>
                        <th>Document Checklist</th>
                        <th>Compliance</th>
                        <th style="width: 100px; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($studentsList)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center;color:var(--sms-text-muted);padding:2rem;">
                            <i class="fas fa-info-circle"></i> No active student records found.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($studentsList as $s): 
                        $uploadedCount = (int)$s['total_uploaded_docs'];
                        $isComplete = ($uploadedCount >= 5);
                        $fullName = trim($s['last_name'] . ', ' . $s['first_name'] . ' ' . ($s['middle_name'] ?? ''));
                        $searchData = strtolower($s['student_number'] . ' ' . $fullName . ' ' . ($s['program_course'] ?? '') . ' ' . ($s['year_section'] ?? ''));
                    ?>
                    <tr class="student-row" 
                        data-search="<?php echo htmlspecialchars($searchData); ?>"
                        data-program="<?php echo htmlspecialchars(strtolower($s['program_course'] ?? '')); ?>"
                        data-status="<?php echo $isComplete ? 'complete' : 'incomplete'; ?>"
                        data-form_138="<?php echo $s['file_id_form_138'] ? '1' : '0'; ?>"
                        data-form_137="<?php echo $s['file_id_form_137'] ? '1' : '0'; ?>"
                        data-good_moral="<?php echo $s['file_id_good_moral'] ? '1' : '0'; ?>"
                        data-psa_birth_cert="<?php echo $s['file_id_psa_birth_cert'] ? '1' : '0'; ?>"
                        data-barangay_clearance="<?php echo $s['file_id_barangay_clearance'] ? '1' : '0'; ?>">
                        <td>
                            <div class="mpl-person">
                                <span class="mpl-avatar"><?php echo htmlspecialchars(regInitials($fullName)); ?></span>
                                <div>
                                    <strong><?php echo htmlspecialchars($fullName); ?></strong>
                                    <small><?php echo htmlspecialchars($s['student_number']); ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($s['program_course'] ?? '—'); ?></strong>
                            <small class="d-block text-muted"><?php echo htmlspecialchars($s['year_section'] ?? '—'); ?></small>
                        </td>
                        <td>
                            <div class="dfs-checklist">
                                <!-- Form 138 -->
                                <span class="dfs-pill <?php echo $s['file_id_form_138'] ? 'uploaded' : 'missing'; ?>" title="Form 138 (Report Card): <?php echo $s['file_id_form_138'] ? 'Uploaded' : 'Missing'; ?>">
                                    <i class="fas <?php echo $s['file_id_form_138'] ? 'fa-check' : 'fa-times'; ?>"></i>
                                </span>
                                <!-- Form 137 -->
                                <span class="dfs-pill <?php echo $s['file_id_form_137'] ? 'uploaded' : 'missing'; ?>" title="Form 137: <?php echo $s['file_id_form_137'] ? 'Uploaded' : 'Missing'; ?>">
                                    <i class="fas <?php echo $s['file_id_form_137'] ? 'fa-check' : 'fa-times'; ?>"></i>
                                </span>
                                <!-- Good Moral -->
                                <span class="dfs-pill <?php echo $s['file_id_good_moral'] ? 'uploaded' : 'missing'; ?>" title="Certificate of Good Moral: <?php echo $s['file_id_good_moral'] ? 'Uploaded' : 'Missing'; ?>">
                                    <i class="fas <?php echo $s['file_id_good_moral'] ? 'fa-check' : 'fa-times'; ?>"></i>
                                </span>
                                <!-- PSA Birth Cert -->
                                <span class="dfs-pill <?php echo $s['file_id_psa_birth_cert'] ? 'uploaded' : 'missing'; ?>" title="PSA Birth Certificate: <?php echo $s['file_id_psa_birth_cert'] ? 'Uploaded' : 'Missing'; ?>">
                                    <i class="fas <?php echo $s['file_id_psa_birth_cert'] ? 'fa-check' : 'fa-times'; ?>"></i>
                                </span>
                                <!-- Barangay Clearance -->
                                <span class="dfs-pill <?php echo $s['file_id_barangay_clearance'] ? 'uploaded' : 'missing'; ?>" title="Barangay Clearance: <?php echo $s['file_id_barangay_clearance'] ? 'Uploaded' : 'Missing'; ?>">
                                    <i class="fas <?php echo $s['file_id_barangay_clearance'] ? 'fa-check' : 'fa-times'; ?>"></i>
                                </span>
                                <span class="ms-2 fw-semibold text-muted" style="font-size: 0.8rem;"><?php echo $uploadedCount; ?>/5 Docs</span>
                            </div>
                        </td>
                        <td>
                            <?php if ($isComplete): ?>
                            <span class="mpl-status active"><i class="fas fa-check-circle me-1"></i> Complete</span>
                            <?php else: ?>
                            <span class="mpl-status warning"><i class="fas fa-clock me-1"></i> <?php echo (5 - $uploadedCount); ?> Missing</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <div class="mpl-actions justify-content-center">
                                <a href="digital-file-storage.php?student_id=<?php echo (int)$s['id']; ?>" title="View student documents" aria-label="View student documents">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="mpl-foot">
            <span class="meta" id="tableMetaCount">Showing <?php echo count($studentsList); ?> of <?php echo count($studentsList); ?> students</span>
        </div>
    </section>

<?php else: ?>

    <!-- ==================== SINGLE STUDENT DIGITAL STORAGE VIEW ==================== -->
    <div class="mpl-top">
        <div>
            <h1 class="h3 text-dark mb-1">
                <i class="fas fa-folder-open text-primary me-2"></i>Digital File Storage — <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
            </h1>
            <p>Review, verify, preview, and upload official institutional documents for this student.</p>
        </div>
    </div>

    <!-- Student Snapshot Banner -->
    <section class="mpl-panel mb-4">
        <div class="mpl-panel-head border-bottom">
            <div>
                <h2>Student Snapshot</h2>
                <p>Academic &amp; verification details</p>
            </div>
            <div>
                <?php 
                    $uploadedCount = 0;
                    foreach ($studentDocs as $d) { if ($d['is_uploaded']) $uploadedCount++; }
                    $compliancePct = round(($uploadedCount / 5) * 100);
                ?>
                <span class="badge <?php echo $uploadedCount === 5 ? 'bg-success' : 'bg-warning text-dark'; ?> fs-6 px-3 py-2 rounded-pill shadow-sm">
                    <i class="fas <?php echo $uploadedCount === 5 ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> me-1"></i>
                    <?php echo $uploadedCount; ?> of 5 Required Documents Uploaded (<?php echo $compliancePct; ?>%)
                </span>
            </div>
        </div>
        <div class="p-4 bg-light bg-gradient rounded-bottom">
            <div class="row g-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-white p-3 rounded shadow-sm text-primary me-3">
                            <i class="fas fa-id-badge fa-lg"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-bold text-uppercase tracking-wide mb-1">Student</div>
                            <div class="fw-bolder text-dark fs-5 text-truncate" style="max-width: 200px;" title="<?php echo htmlspecialchars($student['student_number'] . ' — ' . $student['first_name'] . ' ' . $student['last_name']); ?>">
                                <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
                            </div>
                            <div class="text-secondary small"><?php echo htmlspecialchars($student['student_number']); ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-white p-3 rounded shadow-sm text-info me-3">
                            <i class="fas fa-graduation-cap fa-lg"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-bold text-uppercase tracking-wide mb-1">Program</div>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($student['program_course'] ?? '—'); ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-white p-3 rounded shadow-sm text-warning me-3">
                            <i class="fas fa-calendar-alt fa-lg"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-bold text-uppercase tracking-wide mb-1">Year &amp; Section</div>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($student['year_section'] ?? '—'); ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-white p-3 rounded shadow-sm text-success me-3">
                            <i class="fas fa-check-circle fa-lg"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-bold text-uppercase tracking-wide mb-1">Enrollment Status</div>
                            <div class="fw-bold text-dark">
                                <?php 
                                    $status = $student['status'] ?? '—';
                                    $badgeClass = strtolower($status) === 'active' ? 'bg-success' : 'bg-secondary';
                                ?>
                                <span class="badge <?php echo $badgeClass; ?> px-2 py-1"><?php echo htmlspecialchars($status); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- The 5 Required Document Cards for this Student -->
    <section class="mpl-panel">
        <div class="mpl-panel-head">
            <div>
                <h2>Required Institutional Documents (5)</h2>
                <p>Click "View Document" to preview submitted records, or "Upload" to attach missing files.</p>
            </div>
        </div>

        <div class="student-doc-grid p-3">
            <?php foreach ($studentDocs as $code => $item): 
                $def = $item['type'];
                $isUploaded = $item['is_uploaded'];
                $file = $item['file'];
            ?>
            <article class="student-doc-card <?php echo $isUploaded ? 'is-uploaded' : 'is-missing'; ?>" id="doc-card-<?php echo htmlspecialchars($code); ?>">
                <div>
                    <div class="student-doc-card-head">
                        <div class="d-flex align-items-center gap-2">
                            <div class="dfs-doc-icon" style="background-color: <?php echo $def['bg_light']; ?>; color: <?php echo $def['color']; ?>; border: 1px solid <?php echo $def['border']; ?>; width:36px; height:36px; font-size:1rem;">
                                <i class="fas <?php echo $def['icon']; ?>"></i>
                            </div>
                            <div>
                                <h3 style="font-size: 0.98rem; font-weight: 700; margin: 0; color: #1e293b;"><?php echo htmlspecialchars($def['title']); ?></h3>
                            </div>
                        </div>
                        <?php if ($isUploaded): ?>
                        <span class="student-doc-badge uploaded"><i class="fas fa-check-circle"></i> Uploaded</span>
                        <?php else: ?>
                        <span class="student-doc-badge missing"><i class="fas fa-minus-circle"></i> Not Uploaded</span>
                        <?php endif; ?>
                    </div>

                    <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 0.85rem; line-height: 1.35;">
                        <?php echo htmlspecialchars($def['description']); ?>
                    </p>

                    <!-- Document Metadata Box -->
                    <div class="student-doc-meta">
                        <?php if ($isUploaded && $file): ?>
                        <div>
                            <span><i class="fas fa-file me-1 text-muted"></i> File:</span>
                            <strong class="text-truncate" style="max-width: 170px;" title="<?php echo htmlspecialchars($file['original_name']); ?>">
                                <?php echo htmlspecialchars($file['original_name']); ?>
                            </strong>
                        </div>
                        <div>
                            <span><i class="fas fa-weight-hanging me-1 text-muted"></i> Size:</span>
                            <strong><?php echo regFormatFileSize((int)$file['size']); ?></strong>
                        </div>
                        <div>
                            <span><i class="fas fa-calendar-alt me-1 text-muted"></i> Uploaded:</span>
                            <strong><?php echo date('M d, Y', strtotime($file['created_at'])); ?></strong>
                        </div>
                        <div>
                            <span><i class="fas fa-shield-alt me-1 text-muted"></i> SHA-256:</span>
                            <code style="font-size:0.75rem; color:#059669;"><?php echo substr($file['sha256_hash'] ?? '', 0, 10) . '...'; ?></code>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-2 text-muted">
                            <i class="fas fa-file-upload fa-lg d-block mb-1 opacity-50"></i>
                            <em>No document uploaded yet for this requirement.</em>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Document Actions (View is enabled only when file is uploaded) -->
                <div class="student-doc-actions">
                    <?php if ($isUploaded && $file): ?>
                    <!-- CLICKABLE: Document is present -->
                    <button type="button" class="btn btn-doc-view" 
                            onclick="openDocPreview(<?php echo (int)$file['id']; ?>, '<?php echo htmlspecialchars(addslashes($def['title'])); ?>', '<?php echo htmlspecialchars(addslashes($file['original_name'])); ?>', '<?php echo htmlspecialchars($file['mime']); ?>', '<?php echo htmlspecialchars($file['sha256_hash']); ?>')">
                        <i class="fas fa-eye me-1"></i> View Document
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="openUploadModal(<?php echo (int)$student['id']; ?>, '<?php echo htmlspecialchars($code); ?>', '<?php echo htmlspecialchars(addslashes($def['title'])); ?>')" title="Replace document">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button type="button" class="btn btn-outline-success" onclick="verifyFile(<?php echo (int)$file['id']; ?>)" title="Verify SHA-256 Integrity">
                        <i class="fas fa-shield-alt"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger" onclick="deleteDocFile(<?php echo (int)$file['id']; ?>)" title="Delete document">
                        <i class="fas fa-trash"></i>
                    </button>
                    <?php else: ?>
                    <!-- UNCLICKABLE / DISABLED: Document is missing -->
                    <button type="button" class="btn btn-doc-view" disabled title="No document uploaded yet for this requirement">
                        <i class="fas fa-eye-slash me-1"></i> View Document
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="openUploadModal(<?php echo (int)$student['id']; ?>, '<?php echo htmlspecialchars($code); ?>', '<?php echo htmlspecialchars(addslashes($def['title'])); ?>')">
                        <i class="fas fa-upload me-1"></i> Upload
                    </button>
                    <?php endif; ?>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </section>

<?php endif; ?>

</div>
</div>

<!-- ==================== DOCUMENT PREVIEW MODAL ==================== -->
<div class="modal fade" id="docPreviewModal" tabindex="-1" aria-labelledby="docPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-file-alt text-warning fa-lg"></i>
                    <div>
                        <h5 class="modal-title mb-0" id="docPreviewModalLabel">Document Viewer</h5>
                        <small class="text-white-50" id="docPreviewSubtitle">Secure Student Document</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Preview Viewer Container -->
                <div class="doc-preview-container" id="docPreviewContainer">
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-spinner fa-spin fa-2x mb-2 text-primary"></i>
                        <p>Loading document preview...</p>
                    </div>
                </div>

                <!-- Integrity & File Info Bar -->
                <div class="p-3 bg-light border-top d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-secondary" id="previewMimeBadge">PDF</span>
                        <span class="text-muted" style="font-size:0.85rem;" id="previewHashBadge">SHA-256: Computing...</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-success" id="previewVerifyBtn" onclick="verifyActivePreviewFile()">
                            <i class="fas fa-shield-alt me-1"></i> Check Integrity
                        </button>
                        <a href="#" class="btn btn-sm btn-primary" id="previewDownloadBtn" target="_blank" download>
                            <i class="fas fa-download me-1"></i> Download
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== UPLOAD DOCUMENT MODAL ==================== -->
<div class="modal fade" id="docUploadModal" tabindex="-1" aria-labelledby="docUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="reg-modal-header">
                <h5 class="modal-title" id="docUploadModalLabel">Upload Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="docUploadForm" onsubmit="return handleDocUpload(event)" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="student_id" id="uploadStudentId" value="">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Document Type</label>
                        <select name="category" id="uploadCategory" class="form-select" required>
                            <?php foreach ($docTypes as $code => $def): ?>
                            <option value="<?php echo htmlspecialchars($code); ?>"><?php echo htmlspecialchars($def['title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Select File (PDF, PNG, JPG - max 5MB)</label>
                        <div class="upload-dropzone" id="uploadDropzone" onclick="document.getElementById('fileInput').click()">
                            <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-2"></i>
                            <p class="mb-1 fw-semibold text-dark" id="dropzoneText">Click to select file or drag &amp; drop here</p>
                            <small class="text-muted">Supported formats: .pdf, .jpg, .jpeg, .png (Max: 5MB)</small>
                            <input type="file" name="file" id="fileInput" class="d-none" accept=".pdf,.png,.jpg,.jpeg" required onchange="handleFileSelected(this)">
                        </div>
                    </div>

                    <div id="uploadSelectedFileInfo" class="alert alert-primary d-none py-2 px-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong id="selectedFileName">filename.pdf</strong>
                                <small class="d-block text-muted" id="selectedFileSize">0 KB</small>
                            </div>
                            <span class="badge bg-success">Ready to upload</span>
                        </div>
                    </div>

                    <div id="uploadProgressContainer" class="progress d-none mb-3" style="height: 8px;">
                        <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width: 0%"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-reg-primary" id="uploadSubmitBtn">
                        <i class="fas fa-upload me-1"></i> Upload File
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>/modules/registrar/assets/js/registrar.js"></script>
<script>
const API_BASE = '<?php echo BASE_URL; ?>/modules/registrar/api';
const CSRF = '<?= e(csrfToken()) ?>';

let currentPreviewFileId = null;

/* ==================== CLIENT FILTER LOGIC FOR MAIN DIRECTORY ==================== */
const studentSearchInput = document.getElementById('studentTableSearch');
const statusFilter = document.getElementById('statusFilter');
const programFilter = document.getElementById('programFilter');
const docTypeSelectFilter = document.getElementById('docTypeSelectFilter');
const tableMetaCount = document.getElementById('tableMetaCount');

let activeDocCardFilter = null;

function applyDirectoryFilters() {
    const query = studentSearchInput ? studentSearchInput.value.trim().toLowerCase() : '';
    const statusVal = statusFilter ? statusFilter.value : 'all';
    const programVal = programFilter ? programFilter.value : 'all';
    const docSelectVal = docTypeSelectFilter ? docTypeSelectFilter.value : 'all';

    let totalVisible = 0;
    const rows = document.querySelectorAll('#digitalFilesTable tbody tr.student-row');
    
    rows.forEach(function (row) {
        const search = row.dataset.search || '';
        const status = row.dataset.status || '';
        const program = row.dataset.program || '';

        let matchSearch = (query === '' || search.includes(query));
        let matchStatus = (statusVal === 'all' || status === statusVal);
        let matchProgram = (programVal === 'all' || program === programVal);

        // Document specific missing filter
        let matchDoc = true;
        if (docSelectVal !== 'all') {
            const docCode = docSelectVal.replace('missing_', '');
            if (row.dataset[docCode] === '1') {
                matchDoc = false;
            }
        }

        // Active top-card filter
        if (activeDocCardFilter) {
            // Filter by students who have this doc missing
            if (row.dataset[activeDocCardFilter] === '1') {
                matchDoc = false;
            }
        }

        if (matchSearch && matchStatus && matchProgram && matchDoc) {
            row.style.display = '';
            totalVisible++;
        } else {
            row.style.display = 'none';
        }
    });

    if (tableMetaCount) {
        tableMetaCount.textContent = `Showing ${totalVisible} of ${rows.length} students`;
    }
}

if (studentSearchInput) {
    studentSearchInput.addEventListener('input', debounce(applyDirectoryFilters, 150));
}
if (statusFilter) {
    statusFilter.addEventListener('change', applyDirectoryFilters);
}
if (programFilter) {
    programFilter.addEventListener('change', applyDirectoryFilters);
}
if (docTypeSelectFilter) {
    docTypeSelectFilter.addEventListener('change', function () {
        activeDocCardFilter = null;
        document.querySelectorAll('.dfs-doc-card').forEach(c => c.classList.remove('active-filter'));
        applyDirectoryFilters();
    });
}

function filterByDocType(docCode) {
    if (activeDocCardFilter === docCode) {
        activeDocCardFilter = null;
        document.querySelectorAll('.dfs-doc-card').forEach(c => c.classList.remove('active-filter'));
    } else {
        activeDocCardFilter = docCode;
        document.querySelectorAll('.dfs-doc-card').forEach(c => {
            if (c.dataset.docFilter === docCode) {
                c.classList.add('active-filter');
            } else {
                c.classList.remove('active-filter');
            }
        });
    }
    applyDirectoryFilters();
}

/* ==================== PREVIEW & VIEWER LOGIC ==================== */
function openDocPreview(fileId, docTitle, fileName, mime, hash) {
    currentPreviewFileId = fileId;
    
    document.getElementById('docPreviewModalLabel').textContent = docTitle || 'Document Viewer';
    document.getElementById('docPreviewSubtitle').textContent = fileName || 'Student Document';
    document.getElementById('previewMimeBadge').textContent = (mime || 'Document').toUpperCase();
    document.getElementById('previewHashBadge').textContent = 'SHA-256: ' + (hash ? hash.substring(0, 24) + '...' : 'Available');

    const downloadUrl = `${API_BASE}/download.php?file_id=${fileId}&mode=download`;
    const inlineUrl = `${API_BASE}/download.php?file_id=${fileId}&mode=inline`;
    document.getElementById('previewDownloadBtn').href = downloadUrl;

    const container = document.getElementById('docPreviewContainer');
    container.innerHTML = '';

    if (mime && mime.startsWith('image/')) {
        const img = document.createElement('img');
        img.src = inlineUrl;
        img.alt = fileName;
        img.className = 'img-fluid';
        container.appendChild(img);
    } else {
        // PDF or HTML embed iframe
        const iframe = document.createElement('iframe');
        iframe.src = inlineUrl;
        iframe.title = docTitle;
        container.appendChild(iframe);
    }

    const modalEl = document.getElementById('docPreviewModal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
}

function verifyActivePreviewFile() {
    if (!currentPreviewFileId) return;
    verifyFile(currentPreviewFileId);
}

async function verifyFile(fileId) {
    try {
        const res = await fetch(`${API_BASE}/files.php?action=verify&file_id=${fileId}`);
        const data = await res.json();
        if (data.valid || data.success) {
            showRegSuccess(`✓ SHA-256 Integrity Verified!\nHash: ${(data.hash || '').substring(0, 32)}...`);
        } else {
            showRegError('Integrity Verification Failed: ' + (data.reason || 'File modified or missing'));
        }
    } catch (err) {
        showRegError('Error verifying integrity: ' + err.message);
    }
}

/* ==================== UPLOAD MODAL & FILE HANDLING ==================== */
function openUploadModal(studentId, categoryCode, categoryTitle) {
    document.getElementById('docUploadForm').reset();
    document.getElementById('uploadStudentId').value = studentId;
    if (categoryCode) {
        document.getElementById('uploadCategory').value = categoryCode;
    }
    document.getElementById('uploadSelectedFileInfo').classList.add('d-none');
    document.getElementById('uploadProgressContainer').classList.add('d-none');
    document.getElementById('dropzoneText').textContent = 'Click to select file or drag & drop here';
    document.getElementById('uploadSubmitBtn').disabled = false;

    if (categoryTitle) {
        document.getElementById('docUploadModalLabel').textContent = 'Upload ' + categoryTitle;
    } else {
        document.getElementById('docUploadModalLabel').textContent = 'Upload Document';
    }

    const modalEl = document.getElementById('docUploadModal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
}

function handleFileSelected(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.size > 5242880) {
            showRegError('File exceeds 5MB size limit.');
            input.value = '';
            return;
        }
        document.getElementById('selectedFileName').textContent = file.name;
        document.getElementById('selectedFileSize').textContent = formatBytes(file.size);
        document.getElementById('uploadSelectedFileInfo').classList.remove('d-none');
        document.getElementById('dropzoneText').textContent = 'Selected: ' + file.name;
    }
}

// Drag & drop support
const dropzone = document.getElementById('uploadDropzone');
if (dropzone) {
    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.add('dragover');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.remove('dragover');
        }, false);
    });

    dropzone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files && files[0]) {
            document.getElementById('fileInput').files = files;
            handleFileSelected(document.getElementById('fileInput'));
        }
    });
}

function formatBytes(bytes, decimals = 2) {
    if (!+bytes) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
}

async function handleDocUpload(e) {
    e.preventDefault();

    const form = document.getElementById('docUploadForm');
    const formData = new FormData(form);
    const submitBtn = document.getElementById('uploadSubmitBtn');
    const progressContainer = document.getElementById('uploadProgressContainer');
    const progressBar = document.getElementById('uploadProgressBar');

    submitBtn.disabled = true;
    progressContainer.classList.remove('d-none');
    progressBar.style.width = '60%';

    try {
        const response = await fetch(`${API_BASE}/files.php?action=upload`, {
            method: 'POST',
            headers: {
                'X-CSRF-Token': CSRF
            },
            body: formData
        });
        const result = await response.json();

        progressBar.style.width = '100%';

        if (result.success) {
            showRegSuccess(result.message || 'Document uploaded successfully!');
            const modalEl = document.getElementById('docUploadModal');
            bootstrap.Modal.getInstance(modalEl).hide();
            setTimeout(() => location.reload(), 600);
        } else {
            showRegError(result.error || 'Upload failed');
            submitBtn.disabled = false;
        }
    } catch (err) {
        showRegError('Error during upload: ' + err.message);
        submitBtn.disabled = false;
    }

    return false;
}

async function deleteDocFile(fileId) {
    if (!confirm('Are you sure you want to delete this document from digital storage?')) return;

    try {
        const res = await fetch(`${API_BASE}/files.php?action=delete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': CSRF
            },
            body: JSON.stringify({ file_id: fileId })
        });
        const result = await res.json();
        if (result.success) {
            showRegSuccess('Document deleted');
            setTimeout(() => location.reload(), 600);
        } else {
            showRegError(result.error || 'Delete failed');
        }
    } catch (err) {
        showRegError('Error: ' + err.message);
    }
}
</script>

<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
