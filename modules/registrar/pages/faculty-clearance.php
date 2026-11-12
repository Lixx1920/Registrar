<?php
/**
 * SMS 2 - Faculty Clearance Portal
 * Module: Registrar
 *
 * Clearance Office view for reviewing, tracking, and verifying academic clearances
 * submitted by faculty members across academic departments.
 *
 * Implements:
 *   - Macro KPI Cards: Pending Verification, Denied / Action Required, Approved & Archived
 *   - Dual Tab Switcher: Active Clearance Tracking vs. Archived Completed Records
 *   - Detailed Review Modal: Contract & employment details, uploaded requirements, file viewer/download
 *   - Approve / Deny Actions with mandatory remarks on rejection for faculty resubmission
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';

regRequireAction('registrar.view');

$pageTitle    = 'Faculty Clearance Portal';
$activeModule = 'registrar';
$activePage   = 'faculty-clearance';
$breadcrumbs  = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Faculty Clearance', 'url' => null],
];

$pdo = db();

// Distinct departments for filter dropdown
$departments = $pdo->query(
    "SELECT DISTINCT `department` FROM `reg_faculty_clearances`
     WHERE `department` IS NOT NULL AND `department` != ''
     ORDER BY `department`"
)->fetchAll(PDO::FETCH_COLUMN);

$employmentTypes = ['Regular', 'Probationary', 'Contractual', 'Part-Time'];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<link href="<?php echo BASE_URL; ?>/assets/css/module-process-list.css?v=2" rel="stylesheet">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/modules/registrar/assets/css/registrar.css?v=<?php echo time(); ?>">

<style>
/* ── KPI Command Center Card Styles (Light Mode) ─────────────────────────── */
.fc-kpi-grid,
.sst-kpi-grid {
    display: grid !important;
    grid-template-columns: repeat(4, 1fr) !important;
    gap: 1.15rem !important;
    margin-bottom: 1.35rem !important;
}

@media (max-width: 1199.98px) {
    .fc-kpi-grid, .sst-kpi-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}

@media (max-width: 575.98px) {
    .fc-kpi-grid, .sst-kpi-grid {
        grid-template-columns: 1fr !important;
    }
}

.fc-kpi-card,
.sst-kpi-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    padding: 1.15rem 1.25rem !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
    display: flex !important;
    align-items: flex-start !important;
    justify-content: space-between !important;
    transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease, background-color 0.2s ease !important;
    cursor: pointer !important;
    text-decoration: none !important;
    min-height: 108px !important;
}

.fc-kpi-card:hover,
.sst-kpi-card:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08) !important;
    border-color: #cbd5e1 !important;
}

.fc-kpi-content,
.sst-kpi-content {
    flex: 1 !important;
    margin-right: 0.75rem !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: flex-start !important;
}

.fc-kpi-label,
.sst-kpi-label {
    font-size: 0.74rem !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.04em !important;
    color: #64748b !important;
    margin-bottom: 0.25rem !important;
    line-height: 1.2 !important;
    transition: color 0.2s ease !important;
}

.fc-kpi-value,
.sst-kpi-value {
    font-size: 1.75rem !important;
    font-weight: 800 !important;
    line-height: 1.1 !important;
    color: #0f172a !important;
    margin-bottom: 0.35rem !important;
    transition: color 0.2s ease !important;
}

.fc-kpi-sub,
.sst-kpi-sub {
    font-size: 0.76rem !important;
    color: #64748b !important;
    display: flex !important;
    align-items: center !important;
    gap: 0.4rem !important;
    flex-wrap: wrap !important;
    line-height: 1.2 !important;
    transition: color 0.2s ease !important;
}

.fc-kpi-icon,
.sst-kpi-icon {
    width: 44px !important;
    height: 44px !important;
    border-radius: 10px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 1.15rem !important;
    flex-shrink: 0 !important;
    transition: background-color 0.2s ease, color 0.2s ease !important;
}

.fc-kpi-icon.blue,   .sst-kpi-icon.blue   { background: #eff6ff !important; color: #2563eb !important; }
.fc-kpi-icon.amber,  .sst-kpi-icon.amber  { background: #fffbeb !important; color: #d97706 !important; }
.fc-kpi-icon.purple, .sst-kpi-icon.purple { background: #faf5ff !important; color: #7e22ce !important; }
.fc-kpi-icon.teal,   .sst-kpi-icon.teal   { background: #f0fdf4 !important; color: #16a34a !important; }

/* ── Dark Mode Adaptations (Triggered by data-theme="dark") ───────────────── */
[data-theme="dark"] .fc-kpi-card,
[data-theme="dark"] .sst-kpi-card {
    background: #121c34 !important;
    border-color: rgba(148, 163, 184, 0.2) !important;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.35) !important;
}

[data-theme="dark"] .fc-kpi-card:hover,
[data-theme="dark"] .sst-kpi-card:hover {
    border-color: rgba(96, 165, 250, 0.4) !important;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.45) !important;
}

[data-theme="dark"] .fc-kpi-label,
[data-theme="dark"] .sst-kpi-label {
    color: #94a3b8 !important;
}

[data-theme="dark"] .fc-kpi-value,
[data-theme="dark"] .sst-kpi-value {
    color: #f8fafc !important;
}

[data-theme="dark"] .fc-kpi-sub,
[data-theme="dark"] .sst-kpi-sub {
    color: #cbd5e1 !important;
}

[data-theme="dark"] .fc-kpi-icon.blue,
[data-theme="dark"] .sst-kpi-icon.blue {
    background: rgba(37, 99, 235, 0.22) !important;
    color: #93c5fd !important;
}

[data-theme="dark"] .fc-kpi-icon.amber,
[data-theme="dark"] .sst-kpi-icon.amber {
    background: rgba(217, 119, 6, 0.22) !important;
    color: #fde68a !important;
}

[data-theme="dark"] .fc-kpi-icon.purple,
[data-theme="dark"] .sst-kpi-icon.purple {
    background: rgba(126, 34, 206, 0.22) !important;
    color: #d8b4fe !important;
}

[data-theme="dark"] .fc-kpi-icon.teal,
[data-theme="dark"] .sst-kpi-icon.teal {
    background: rgba(22, 163, 74, 0.22) !important;
    color: #86efac !important;
}

/* Switcher Buttons in Dark Mode */
[data-theme="dark"] .fc-switcher-btn {
    background: rgba(15, 23, 42, 0.72) !important;
    border-color: rgba(148, 163, 184, 0.2) !important;
    color: #cbd5e1 !important;
}

[data-theme="dark"] .fc-switcher-btn:hover {
    background: rgba(30, 41, 59, 0.9) !important;
    color: #f8fafc !important;
    border-color: rgba(148, 163, 184, 0.35) !important;
}

[data-theme="dark"] .fc-switcher-btn.active {
    background: #2563eb !important;
    border-color: #3b82f6 !important;
    color: #ffffff !important;
}

[data-theme="dark"] .fc-switcher-btn .badge {
    background: rgba(255, 255, 255, 0.18) !important;
    color: #ffffff !important;
}

/* Filter Bar & Pills in Dark Mode */
[data-theme="dark"] #filterPillsWrapper {
    background: rgba(15, 23, 42, 0.85) !important;
    border-color: rgba(148, 163, 184, 0.2) !important;
}

[data-theme="dark"] .fc-pill {
    background: rgba(255, 255, 255, 0.06) !important;
    border-color: rgba(148, 163, 184, 0.2) !important;
    color: #cbd5e1 !important;
}

[data-theme="dark"] .fc-pill:hover {
    background: rgba(255, 255, 255, 0.12) !important;
    color: #ffffff !important;
}

[data-theme="dark"] .fc-pill.active {
    background: #3b82f6 !important;
    border-color: #3b82f6 !important;
    color: #ffffff !important;
}

[data-theme="dark"] .fc-pill.blue.active {
    background: #0284c7 !important;
    border-color: #0284c7 !important;
}

[data-theme="dark"] .fc-pill.red.active {
    background: #dc2626 !important;
    border-color: #dc2626 !important;
}

/* Status Badges in Dark Mode */
[data-theme="dark"] .fc-status-badge.under-verification {
    background: rgba(3, 105, 161, 0.22) !important;
    color: #7dd3fc !important;
    border-color: rgba(56, 189, 248, 0.35) !important;
}

[data-theme="dark"] .fc-status-badge.approved {
    background: rgba(21, 128, 61, 0.22) !important;
    color: #86efac !important;
    border-color: rgba(74, 222, 128, 0.35) !important;
}

[data-theme="dark"] .fc-status-badge.denied {
    background: rgba(185, 28, 28, 0.22) !important;
    color: #fca5a5 !important;
    border-color: rgba(248, 113, 113, 0.35) !important;
}

[data-theme="dark"] .fc-status-badge.not-submitted {
    background: rgba(255, 255, 255, 0.08) !important;
    color: #94a3b8 !important;
    border-color: rgba(148, 163, 184, 0.2) !important;
}

/* Review Modal in Dark Mode */
[data-theme="dark"] #reviewClearanceModal .modal-content,
[data-theme="dark"] #approveModal .modal-content,
[data-theme="dark"] #denyModal .modal-content {
    background: #121c34 !important;
    color: #f8fafc !important;
    border: 1px solid rgba(148, 163, 184, 0.2) !important;
}

[data-theme="dark"] #reviewClearanceModal .modal-body {
    background: #0b1224 !important;
}

[data-theme="dark"] #reviewClearanceModal .modal-footer,
[data-theme="dark"] #reviewClearanceModal .bg-white {
    background: #182440 !important;
    border-color: rgba(148, 163, 184, 0.2) !important;
    color: #f8fafc !important;
}

[data-theme="dark"] #reviewClearanceModal .bg-white .text-dark,
[data-theme="dark"] #approveModal .text-dark,
[data-theme="dark"] #denyModal .text-dark {
    color: #f8fafc !important;
}

[data-theme="dark"] .fc-scope-box {
    background: rgba(15, 23, 42, 0.75) !important;
    border-color: rgba(148, 163, 184, 0.2) !important;
}

[data-theme="dark"] .fc-scope-item {
    color: #cbd5e1 !important;
}
</style>

<?php renderBreadcrumbs($breadcrumbs); ?>

<div id="fcAlertBox"></div>

<div class="container-fluid py-3">
<div class="mpl" data-mpl>

    <!-- Top Bar -->
    <div class="mpl-top d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <div class="text-uppercase fw-bold text-primary small" style="letter-spacing:0.05em; font-size:0.75rem;">
                <i class="fas fa-university me-1"></i>Registrar Clearance Office
            </div>
            <h1 class="h4 mb-1 fw-bold text-dark">
                <i class="fas fa-graduation-cap text-primary me-2"></i>Faculty Clearance Portal
            </h1>
            <p class="text-muted small mb-0">
                Track ongoing clearance submissions, review requirements, and inspect archived completed records.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="fcRefreshAll()" title="Refresh data">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
        </div>
    </div>

    <!-- ── 4 Macro Registrar KPI Cards (Matching Screenshot) ─────────────── -->
    <section class="sst-kpi-grid fc-kpi-grid" aria-label="Faculty Clearance Status Metrics">
        <!-- 1. Active Clearance Queue -->
        <article class="sst-kpi-card fc-kpi-card" onclick="fcFilterByCard('')" style="cursor:pointer;" title="View Active Clearance Queue">
            <div class="sst-kpi-content fc-kpi-content">
                <div class="sst-kpi-label fc-kpi-label">Active Clearance Queue</div>
                <div class="sst-kpi-value fc-kpi-value" id="kpiActiveTotal">—</div>
                <div class="sst-kpi-sub fc-kpi-sub" id="kpiActiveBreakdown">
                    <span id="kpiActiveSub">17 Not Submitted · 1 Pending</span>
                </div>
            </div>
            <div class="sst-kpi-icon fc-kpi-icon blue">
                <i class="fas fa-user-check"></i>
            </div>
        </article>

        <!-- 2. Pending Verification -->
        <article class="sst-kpi-card fc-kpi-card" onclick="fcFilterByCard('Under Verification')" style="cursor:pointer;" title="View Submissions Pending Verification">
            <div class="sst-kpi-content fc-kpi-content">
                <div class="sst-kpi-label fc-kpi-label">Pending Verification</div>
                <div class="sst-kpi-value fc-kpi-value" id="kpiPending">—</div>
                <div class="sst-kpi-sub fc-kpi-sub">
                    <span class="text-success"><i class="fas fa-check-circle me-1"></i>Awaiting Review</span>
                </div>
            </div>
            <div class="sst-kpi-icon fc-kpi-icon amber">
                <i class="fas fa-calendar-times"></i>
            </div>
        </article>

        <!-- 3. Denied / Action Required -->
        <article class="sst-kpi-card fc-kpi-card" onclick="fcFilterByCard('Denied')" style="cursor:pointer;" title="View Denied Clearances Requiring Action">
            <div class="sst-kpi-content fc-kpi-content">
                <div class="sst-kpi-label fc-kpi-label">Institutional Clearance Hold</div>
                <div class="sst-kpi-value fc-kpi-value" id="kpiDenied">—</div>
                <div class="sst-kpi-sub fc-kpi-sub">
                    <span class="text-danger"><i class="fas fa-exchange-alt me-1"></i>Requires Resubmission</span>
                </div>
            </div>
            <div class="sst-kpi-icon fc-kpi-icon purple">
                <i class="fas fa-exchange-alt"></i>
            </div>
        </article>

        <!-- 4. Approved & Archived -->
        <article class="sst-kpi-card fc-kpi-card" onclick="fcSwitchTab('archived')" style="cursor:pointer;" title="View Approved and Conferred Clearances">
            <div class="sst-kpi-content fc-kpi-content">
                <div class="sst-kpi-label fc-kpi-label">Degrees &amp; Clearances Conferred</div>
                <div class="sst-kpi-value fc-kpi-value" id="kpiApproved">—</div>
                <div class="sst-kpi-sub fc-kpi-sub">
                    <span class="text-success"><i class="fas fa-award me-1"></i>Completed Clearances</span>
                </div>
            </div>
            <div class="sst-kpi-icon fc-kpi-icon teal">
                <i class="fas fa-graduation-cap"></i>
            </div>
        </article>
    </section>

    <!-- ── Segmented Switcher Buttons (Active vs Archived) ───────────────── -->
    <div class="fc-view-switcher">
        <button type="button" class="fc-switcher-btn active" id="btnTabActive" onclick="fcSwitchTab('active')">
            <i class="fas fa-tasks"></i> Active Clearance Tracking
            <span class="badge bg-white text-primary" id="badgeActiveCount">—</span>
        </button>
        <button type="button" class="fc-switcher-btn" id="btnTabArchived" onclick="fcSwitchTab('archived')">
            <i class="fas fa-archive"></i> Archived Completed Records
            <span class="badge bg-white text-secondary" id="badgeArchivedCount">—</span>
        </button>
    </div>

    <!-- ── Main Table Panel ──────────────────────────────────────────────── -->
    <section class="mpl-panel">
        <div class="mpl-panel-head d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h2 class="h6 mb-0 fw-bold" id="panelHeading">
                    <i class="fas fa-list-check text-primary me-2"></i>Ongoing Faculty Clearance Records
                </h2>
                <p class="small text-muted mb-0" id="panelSubheading">
                    Review and verify academic clearance requirements submitted by teaching faculty.
                </p>
            </div>
        </div>

        <!-- Filter Pills (Active View) -->
        <div class="p-3 border-bottom bg-light" id="filterPillsWrapper">
            <div class="fc-filter-pills">
                <button type="button" class="fc-pill active" id="pillAll" onclick="fcSetStatusPill('')">
                    All Active <span id="pillCountAll" class="ms-1">—</span>
                </button>
                <button type="button" class="fc-pill blue" id="pillPending" onclick="fcSetStatusPill('Under Verification')">
                    Pending Verification <span id="pillCountPending" class="ms-1">—</span>
                </button>
                <button type="button" class="fc-pill red" id="pillDenied" onclick="fcSetStatusPill('Denied')">
                    Denied / Resubmission <span id="pillCountDenied" class="ms-1">—</span>
                </button>
                <button type="button" class="fc-pill" id="pillNotSubmitted" onclick="fcSetStatusPill('Not Submitted')">
                    Not Submitted <span id="pillCountNotSubmitted" class="ms-1">—</span>
                </button>
            </div>

            <!-- Filters Bar -->
            <div class="mpl-filters pt-2">
                <label class="mpl-search">
                    <i class="fas fa-search"></i>
                    <input type="search" id="fcSearch" placeholder="Search faculty or ID…" aria-label="Search faculty">
                </label>
                <select id="fcEmploymentFilter" aria-label="Filter by employment type">
                    <option value="">All Employment Types</option>
                    <?php foreach ($employmentTypes as $et): ?>
                    <option value="<?php echo htmlspecialchars($et); ?>"><?php echo htmlspecialchars($et); ?></option>
                    <?php endforeach; ?>
                </select>
                <select id="fcDeptFilter" aria-label="Filter by department">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $dept): ?>
                    <option value="<?php echo htmlspecialchars($dept); ?>"><?php echo htmlspecialchars($dept); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="button" class="mpl-refresh btn btn-link text-decoration-none p-0 text-muted" onclick="fcResetFilters()">
                    <i class="fas fa-undo me-1"></i> Reset
                </button>
            </div>
        </div>

        <!-- Table Wrap -->
        <div class="mpl-table-wrap">
            <table class="mpl-table">
                <thead>
                    <tr>
                        <th>Faculty</th>
                        <th>Department</th>
                        <th>Contract Expiration</th>
                        <th style="width:140px;">Progress</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th style="width:110px; text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody id="fcTbody">
                    <tr>
                        <td colspan="7" style="text-align:center;color:var(--sms-text-muted);padding:2.5rem;">
                            <i class="fas fa-spinner fa-spin fa-2x text-primary mb-2"></i>
                            <p class="mb-0">Loading faculty clearances…</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mpl-foot">
            <span class="meta" id="fcPagInfo">—</span>
            <div class="mpl-pager" id="fcPager"></div>
        </div>
    </section>

</div><!-- /mpl -->
</div><!-- /container -->

<!-- ══════════════════════════════════════════════════════════════════════════
     MODAL: REVIEW CLEARANCE (Matching Images 4 & 1)
     ══════════════════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="reviewClearanceModal" tabindex="-1" aria-labelledby="reviewClearanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px; overflow:hidden;">
            <!-- Modal Header -->
            <div class="modal-header text-white" style="background:linear-gradient(135deg, #1e40af, #2563eb); padding:1rem 1.4rem;">
                <div>
                    <h5 class="modal-title fw-bold fs-6 mb-0" id="reviewClearanceModalLabel">
                        <i class="fas fa-file-signature me-2"></i>Review Clearance - <span id="mFacultyName">—</span>
                    </h5>
                    <small style="opacity:0.85; font-size:0.78rem;" id="mFacultyMeta">—</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4" style="background:#f8fafc;">
                <!-- Top Summary Grid -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="bg-white p-3 rounded-3 border">
                            <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size:0.7rem;">Current Contract Expiry</small>
                            <div class="fw-bold text-dark fs-6" id="mContractExpiry">—</div>
                            <small class="text-muted" id="mContractDays">—</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-white p-3 rounded-3 border">
                            <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size:0.7rem;">Employment Status</small>
                            <div class="mt-1" id="mEmploymentStatus">—</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-white p-3 rounded-3 border">
                            <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size:0.7rem;">Clearance Progress</small>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <div class="progress flex-grow-1" style="height:6px;">
                                    <div class="progress-bar bg-success" id="mProgressBar" style="width:0%;"></div>
                                </div>
                                <span class="fw-bold small" id="mProgressText">0% (0/1)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Scope of Verification Accordion / Box (Matching Image 1) -->
                <div class="fc-scope-box mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase fw-bold text-secondary" style="font-size:0.72rem; letter-spacing:0.04em;">
                            <i class="fas fa-tasks text-primary me-1"></i>Scope of Verification (Academic Affairs / Registrar)
                        </span>
                        <span class="badge bg-light text-dark border" style="font-size:0.7rem;">6 Criteria</span>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="fc-scope-item"><i class="fas fa-check-circle"></i> Grade sheets submitted and finalized</div>
                            <div class="fc-scope-item"><i class="fas fa-check-circle"></i> Class records &amp; grading sheets completed</div>
                            <div class="fc-scope-item"><i class="fas fa-check-circle"></i> Course syllabi &amp; instructional materials submitted</div>
                        </div>
                        <div class="col-md-6">
                            <div class="fc-scope-item"><i class="fas fa-check-circle"></i> Attendance records &amp; DTR completed</div>
                            <div class="fc-scope-item"><i class="fas fa-check-circle"></i> Pending student academic concerns resolved</div>
                            <div class="fc-scope-item"><i class="fas fa-check-circle"></i> Verified directly by administrative office</div>
                        </div>
                    </div>
                </div>

                <!-- Submitted Requirements Card (Matching Image 4) -->
                <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius:10px;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase fw-bold text-dark small" style="font-size:0.75rem;">
                            <i class="fas fa-file-lines text-primary me-1"></i>Submitted Academic Clearance Requirement
                        </span>
                        <span id="mSubmittedDate" class="text-muted small" style="font-size:0.72rem;">—</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>Requirement</th>
                                    <th>File Attachment</th>
                                    <th>Status</th>
                                    <th>Remark</th>
                                    <th style="width:90px; text-align:center;">Review Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark small" id="mReqName">Academic Clearance</div>
                                    </td>
                                    <td>
                                        <div id="mFileAttachment">
                                            <span class="text-muted small">No file attached</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div id="mStatusBadge">—</div>
                                    </td>
                                    <td>
                                        <div class="small text-muted" id="mRemarks">—</div>
                                    </td>
                                    <td style="text-align:center;">
                                        <div class="fc-action-btn-group" id="mReviewButtons">
                                            <button type="button" class="fc-action-btn approve" onclick="fcOpenApproveModal()" title="Approve Requirement">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="fc-action-btn deny" onclick="fcOpenDenyModal()" title="Deny &amp; Request Resubmission">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-light px-4 py-2 border-top">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════════════════
     SUB-MODAL: APPROVE CONFIRMATION (Matching Image 2)
     ══════════════════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 440px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:12px; overflow:hidden;">
            <div class="modal-body p-4 text-center">
                <div class="d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width:54px; height:54px; border-radius:50%; background:#dcfce7; color:#16a34a; font-size:1.6rem;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h5 class="fw-bold fs-6 mb-1 text-dark" id="approveModalLabel">Approve Requirement</h5>
                <p class="text-muted small mb-3">You are approving this clearance submission.</p>

                <div class="text-start mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Remark (Optional)</label>
                    <textarea id="approveRemarks" class="form-control form-control-sm" rows="2" 
                              placeholder="Requirement approved.">Requirement approved.</textarea>
                    <small class="text-muted" style="font-size:0.72rem;">Leave blank to use the default approval message.</small>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success btn-sm px-3" id="btnConfirmApprove" onclick="fcSubmitDecision('Approved')">
                        <i class="fas fa-check me-1"></i> Approve
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════════════════
     SUB-MODAL: DENY & REQUIRE RESUBMISSION (Per User Explanation)
     ══════════════════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="denyModal" tabindex="-1" aria-labelledby="denyModalLabel" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 440px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:12px; overflow:hidden;">
            <div class="modal-body p-4 text-center">
                <div class="d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width:54px; height:54px; border-radius:50%; background:#fee2e2; color:#dc2626; font-size:1.6rem;">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h5 class="fw-bold fs-6 mb-1 text-dark" id="denyModalLabel">Deny Requirement</h5>
                <p class="text-muted small mb-3">Specify the deficiency or reason why this clearance submission is denied.</p>

                <div class="text-start mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">
                        Denial Remarks / Resubmission Reason <span class="text-danger">*</span>
                    </label>
                    <textarea id="denyRemarks" class="form-control form-control-sm" rows="3" required
                              placeholder="e.g. Missing signed grade sheet for Section IV-A, attendance records incomplete. Please re-upload corrected file."></textarea>
                    <small class="text-danger" style="font-size:0.72rem;">Faculty will see this remark in their portal and must resubmit.</small>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm px-3" id="btnConfirmDeny" onclick="fcSubmitDecision('Denied')">
                        <i class="fas fa-times me-1"></i> Deny &amp; Request Resubmit
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════════════════
     MODAL: DOCUMENT PREVIEW (PDF VIEWER)
     ══════════════════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="docPreviewModal" tabindex="-1" aria-labelledby="docPreviewModalLabel" aria-hidden="true" style="z-index: 1080;">
    <div class="modal-dialog modal-dialog-centered modal-xl" style="height: 90vh;">
        <div class="modal-content h-100 border-0 shadow-lg" style="border-radius:12px; overflow:hidden;">
            <div class="modal-header bg-dark text-white py-2 px-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-file-pdf text-danger"></i>
                    <h6 class="modal-title fs-6 mb-0" id="docPreviewModalLabel">Clearance Document Preview</h6>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a id="btnDocDownload" href="#" download class="btn btn-sm btn-outline-light py-1 px-2">
                        <i class="fas fa-download me-1"></i> Download
                    </a>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0 bg-secondary" style="height: calc(100% - 55px);">
                <iframe id="docPreviewFrame" src="" style="width:100%; height:100%; border:none;"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
const FC_API = '<?php echo BASE_URL; ?>/modules/registrar/api/faculty-clearance.php';
const CSRF   = '<?= e(csrfToken()) ?>';

// ── State ──────────────────────────────────────────────────────────────────
let fcCurrentView   = 'active'; // 'active' or 'archived'
let fcStatusFilter  = '';       // '', 'Under Verification', 'Denied', 'Not Submitted'
let fcPage          = 1;
let fcTotal         = 0;
let fcPages         = 1;
const FC_LIMIT      = 20;

let fcDebounceTimer;
let fcActiveRecordId = null;
let fcActiveRecord   = null;

// ── Initialization ─────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    fcRefreshAll();

    document.getElementById('fcSearch').addEventListener('input', function () {
        clearTimeout(fcDebounceTimer);
        fcDebounceTimer = setTimeout(() => { fcPage = 1; fcLoadList(); }, 280);
    });
    document.getElementById('fcEmploymentFilter').addEventListener('change', () => { fcPage = 1; fcLoadList(); });
    document.getElementById('fcDeptFilter').addEventListener('change', () => { fcPage = 1; fcLoadList(); });
});

function fcRefreshAll() {
    fcLoadStats();
    fcLoadList();
}

// ── 1. Macro KPI Stats ─────────────────────────────────────────────────────
async function fcLoadStats() {
    try {
        const res  = await fetch(FC_API + '?action=stats');
        const data = await res.json();
        if (!data.success) return;

        const s = data.stats;
        const activeTotal = s.active_total ?? 0;
        const notSub      = s.not_submitted ?? 0;
        const pending     = s.pending ?? 0;

        const elActive = document.getElementById('kpiActiveTotal');
        if (elActive) elActive.textContent = activeTotal;

        const elSub = document.getElementById('kpiActiveSub');
        if (elSub) {
            elSub.innerHTML = `<strong>${notSub}</strong> Not Submitted · <strong>${pending}</strong> Pending`;
        }

        document.getElementById('kpiPending').textContent       = s.pending ?? 0;
        document.getElementById('kpiDenied').textContent        = s.denied ?? 0;
        document.getElementById('kpiApproved').textContent      = s.approved ?? 0;
        document.getElementById('badgeActiveCount').textContent   = s.active_total ?? 0;
        document.getElementById('badgeArchivedCount').textContent = s.archived_total ?? 0;

        document.getElementById('pillCountAll').textContent          = s.active_total ?? 0;
        document.getElementById('pillCountPending').textContent      = s.pending ?? 0;
        document.getElementById('pillCountDenied').textContent       = s.denied ?? 0;
        document.getElementById('pillCountNotSubmitted').textContent = s.not_submitted ?? 0;
    } catch (_) {}
}

// ── 2. Segmented Switcher & Filter Pills ───────────────────────────────────
function fcSwitchTab(view) {
    fcCurrentView = view;
    fcPage = 1;
    fcStatusFilter = '';

    const btnActive   = document.getElementById('btnTabActive');
    const btnArchived = document.getElementById('btnTabArchived');
    const heading     = document.getElementById('panelHeading');
    const subheading  = document.getElementById('panelSubheading');
    const pillsWrap   = document.getElementById('filterPillsWrapper');

    if (view === 'active') {
        btnActive.classList.add('active');
        btnArchived.classList.remove('active');
        heading.innerHTML    = '<i class="fas fa-list-check text-primary me-2"></i>Ongoing Faculty Clearance Records';
        subheading.innerHTML = 'Review and verify academic clearance requirements submitted by teaching faculty.';
        pillsWrap.style.display = 'block';
    } else {
        btnActive.classList.remove('active');
        btnArchived.classList.add('active');
        heading.innerHTML    = '<i class="fas fa-archive text-success me-2"></i>Archived Completed Records';
        subheading.innerHTML = 'Historical record of fully verified and approved faculty academic clearances.';
        pillsWrap.style.display = 'none';
    }

    fcLoadList();
}

function fcSetStatusPill(status) {
    fcStatusFilter = status;
    const pills = document.querySelectorAll('.fc-filter-pills .fc-pill');
    pills.forEach(p => p.classList.remove('active'));

    if (status === '') document.getElementById('pillAll').classList.add('active');
    else if (status === 'Under Verification') document.getElementById('pillPending').classList.add('active');
    else if (status === 'Denied') document.getElementById('pillDenied').classList.add('active');
    else if (status === 'Not Submitted') document.getElementById('pillNotSubmitted').classList.add('active');

    fcPage = 1;
    fcLoadList();
}

function fcFilterByCard(status) {
    if (fcCurrentView !== 'active') {
        fcSwitchTab('active');
    }
    fcSetStatusPill(status);
}

function fcResetFilters() {
    document.getElementById('fcSearch').value = '';
    document.getElementById('fcEmploymentFilter').value = '';
    document.getElementById('fcDeptFilter').value = '';
    fcSetStatusPill('');
}

// ── 3. Table Data Rendering ────────────────────────────────────────────────
async function fcLoadList() {
    const q          = document.getElementById('fcSearch').value.trim();
    const employment = document.getElementById('fcEmploymentFilter').value;
    const dept       = document.getElementById('fcDeptFilter').value;

    const params = new URLSearchParams({
        action: 'list',
        view: fcCurrentView,
        status: fcStatusFilter,
        employment,
        department: dept,
        q,
        page: fcPage,
        limit: FC_LIMIT,
    });

    const tbody = document.getElementById('fcTbody');
    tbody.innerHTML = `
        <tr><td colspan="7" style="text-align:center;color:var(--sms-text-muted);padding:2.5rem;">
            <i class="fas fa-spinner fa-spin fa-2x text-primary mb-2"></i>
            <p class="mb-0">Loading clearances…</p>
        </td></tr>`;

    try {
        const res  = await fetch(FC_API + '?' + params);
        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Failed to load list');

        fcTotal = data.pagination.total;
        fcPages = data.pagination.pages;

        _renderTable(data.data);
        _renderPager();
        _renderPagInfo();
    } catch (e) {
        tbody.innerHTML = `
            <tr><td colspan="7" style="text-align:center;color:#ef4444;padding:2rem;">
                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                <p class="mb-0">Error: ${_esc(e.message)}</p>
            </td></tr>`;
    }
}

function _renderTable(rows) {
    const tbody = document.getElementById('fcTbody');
    if (!rows || rows.length === 0) {
        tbody.innerHTML = `
            <tr><td colspan="7" style="text-align:center;padding:3rem 1rem;color:var(--sms-text-muted);">
                <i class="fas fa-folder-open fa-3x text-muted mb-2" style="opacity:0.35;"></i>
                <h6 class="fw-bold mb-1">No Clearance Records Found</h6>
                <p class="small mb-0">No faculty members match the selected criteria or filters.</p>
            </td></tr>`;
        return;
    }

    let html = '';
    rows.forEach(r => {
        const initials = ((r.faculty_name?.[0] ?? '') + (r.faculty_name?.split(' ')?.[1]?.[0] ?? '')).toUpperCase() || 'FC';
        const progWidth = r.progress_percent || 0;
        const progClass = progWidth === 100 ? 'bg-success' : 'bg-primary';

        html += `
            <tr>
                <td>
                    <div class="mpl-person">
                        <span class="mpl-avatar" style="background:#e0f2fe; color:#0369a1; font-weight:700;">${initials}</span>
                        <div>
                            <strong>${_esc(r.faculty_name)}</strong>
                            <small class="text-muted d-block font-monospace" style="font-size:0.75rem;">${_esc(r.faculty_id)}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge bg-light text-dark border fw-bold" style="font-size:0.74rem;">${_esc(r.department)}</span>
                </td>
                <td>
                    <div class="small">${r.contract_label}</div>
                </td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="height:5px;">
                            <div class="progress-bar ${progClass}" style="width:${progWidth}%;"></div>
                        </div>
                        <span class="small text-muted" style="font-size:0.72rem;">${r.progress_ratio}</span>
                    </div>
                </td>
                <td>
                    ${_renderStatusBadge(r.status)}
                </td>
                <td>
                    <small class="text-muted">${r.submitted_label}</small>
                </td>
                <td style="text-align:center;">
                    <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2" 
                            onclick="fcOpenReviewModal(${r.id})" title="Review Academic Clearance">
                        <i class="fas fa-search me-1"></i> Review
                    </button>
                </td>
            </tr>`;
    });
    tbody.innerHTML = html;
}

function _renderPager() {
    const el = document.getElementById('fcPager');
    if (fcPages <= 1) { el.innerHTML = ''; return; }

    let html = `<button onclick="fcGoPage(${Math.max(1, fcPage - 1)})" ${fcPage <= 1 ? 'disabled' : ''}>‹ Prev</button>`;
    const start = Math.max(1, fcPage - 2);
    const end   = Math.min(fcPages, fcPage + 2);
    for (let p = start; p <= end; p++) {
        html += `<button onclick="fcGoPage(${p})" class="${p === fcPage ? 'active' : ''}">${p}</button>`;
    }
    html += `<button onclick="fcGoPage(${Math.min(fcPages, fcPage + 1)})" ${fcPage >= fcPages ? 'disabled' : ''}>Next ›</button>`;
    el.innerHTML = html;
}

function _renderPagInfo() {
    const offset = (fcPage - 1) * FC_LIMIT;
    const end    = Math.min(offset + FC_LIMIT, fcTotal);
    document.getElementById('fcPagInfo').textContent =
        fcTotal === 0 ? 'No records' : `Showing ${offset + 1}–${end} of ${fcTotal} faculty records`;
}

function fcGoPage(p) { fcPage = p; fcLoadList(); }

// ── 4. Review Clearance Modal ──────────────────────────────────────────────
async function fcOpenReviewModal(id) {
    fcActiveRecordId = id;
    fcActiveRecord   = null;

    document.getElementById('mFacultyName').textContent = 'Loading…';
    document.getElementById('mFacultyMeta').textContent = '—';
    document.getElementById('mContractExpiry').textContent = '—';
    document.getElementById('mContractDays').textContent = '';
    document.getElementById('mEmploymentStatus').innerHTML = '—';
    document.getElementById('mProgressBar').style.width = '0%';
    document.getElementById('mProgressText').textContent = '0% (0/1)';
    document.getElementById('mFileAttachment').innerHTML = '<span class="text-muted small">Loading…</span>';
    document.getElementById('mStatusBadge').innerHTML = '—';
    document.getElementById('mRemarks').textContent = '—';
    document.getElementById('mSubmittedDate').textContent = '';

    const reviewModal = new bootstrap.Modal(document.getElementById('reviewClearanceModal'));
    reviewModal.show();

    try {
        const res  = await fetch(FC_API + '?action=get&id=' + id);
        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Failed to load details');

        const c = data.clearance;
        fcActiveRecord = c;

        document.getElementById('mFacultyName').textContent = c.faculty_name;
        document.getElementById('mFacultyMeta').textContent = `${c.faculty_id} · ${c.department}`;
        document.getElementById('mContractExpiry').textContent = c.contract_expiry ? new Date(c.contract_expiry).toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'}) : 'Not set';
        document.getElementById('mContractDays').innerHTML = c.days_remaining !== null 
            ? (c.days_remaining < 0 ? '<span class="text-danger fw-bold">Expired</span>' : `${c.days_remaining} days remaining`) 
            : '';
        document.getElementById('mEmploymentStatus').innerHTML = `<span class="badge bg-warning-subtle text-warning-emphasis border" style="font-size:0.75rem;">${_esc(c.employment_status)}</span>`;
        document.getElementById('mProgressBar').style.width = (c.progress_percent || 0) + '%';
        document.getElementById('mProgressText').textContent = c.progress_ratio || '0% (0/1)';
        document.getElementById('mStatusBadge').innerHTML = _renderStatusBadge(c.status);
        document.getElementById('mRemarks').textContent = c.remarks || 'No remark';
        document.getElementById('mSubmittedDate').textContent = c.submitted_at ? 'Submitted: ' + c.submitted_label : 'Not submitted yet';

        // File Attachment rendering
        const fileWrap = document.getElementById('mFileAttachment');
        if (c.file_name && c.file_url) {
            fileWrap.innerHTML = `
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-link text-primary p-0 fw-semibold text-decoration-none" 
                            onclick="fcPreviewPdf('${_esc(c.file_url)}', '${_esc(c.file_name)}')">
                        <i class="fas fa-eye me-1"></i> View file
                    </button>
                    <a href="${_esc(c.file_url)}" download="${_esc(c.file_name)}" class="text-secondary small ms-1" title="Download File">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
                <div class="text-muted font-monospace" style="font-size:0.72rem;">${_esc(c.file_name)}</div>`;
        } else {
            fileWrap.innerHTML = `<span class="text-muted small"><i class="fas fa-minus-circle me-1"></i>No file attached</span>`;
        }

        // Show/hide review action buttons based on status
        const btnGroup = document.getElementById('mReviewButtons');
        if (c.status === 'Approved') {
            btnGroup.innerHTML = `<span class="text-success small fw-bold"><i class="fas fa-check-circle me-1"></i>Approved</span>`;
        } else {
            btnGroup.innerHTML = `
                <button type="button" class="fc-action-btn approve" onclick="fcOpenApproveModal()" title="Approve Requirement">
                    <i class="fas fa-check"></i>
                </button>
                <button type="button" class="fc-action-btn deny" onclick="fcOpenDenyModal()" title="Deny &amp; Request Resubmission">
                    <i class="fas fa-times"></i>
                </button>`;
        }
    } catch (e) {
        document.getElementById('mFacultyName').textContent = 'Error';
        document.getElementById('mFileAttachment').innerHTML = `<span class="text-danger small">${_esc(e.message)}</span>`;
    }
}

// ── 5. Approve & Deny Sub-Modals ───────────────────────────────────────────
function fcOpenApproveModal() {
    document.getElementById('approveRemarks').value = 'Requirement approved.';
    new bootstrap.Modal(document.getElementById('approveModal')).show();
}

function fcOpenDenyModal() {
    document.getElementById('denyRemarks').value = '';
    new bootstrap.Modal(document.getElementById('denyModal')).show();
}

async function fcSubmitDecision(decision) {
    if (!fcActiveRecordId) return;

    let remarks = '';
    let btn = null;

    if (decision === 'Approved') {
        remarks = document.getElementById('approveRemarks').value.trim();
        btn = document.getElementById('btnConfirmApprove');
    } else {
        remarks = document.getElementById('denyRemarks').value.trim();
        btn = document.getElementById('btnConfirmDeny');
        if (!remarks) {
            alert('Please specify the reason/deficiency for denial so faculty can correct and resubmit.');
            return;
        }
    }

    btn.disabled = true;
    const origHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processing…';

    try {
        const res = await fetch(FC_API + '?action=review', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF },
            body: JSON.stringify({
                id: fcActiveRecordId,
                decision: decision,
                remarks: remarks,
            }),
        });
        const data = await res.json();

        if (!data.success) {
            alert(data.error || 'Failed to update clearance status.');
        } else {
            // Close sub-modals
            bootstrap.Modal.getInstance(document.getElementById('approveModal'))?.hide();
            bootstrap.Modal.getInstance(document.getElementById('denyModal'))?.hide();
            bootstrap.Modal.getInstance(document.getElementById('reviewClearanceModal'))?.hide();

            fcAlert('success', data.message);
            fcRefreshAll();
        }
    } catch (err) {
        alert('Error: ' + err.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = origHtml;
    }
}

// ── 6. PDF Document Preview Modal ──────────────────────────────────────────
function fcPreviewPdf(url, fileName) {
    document.getElementById('docPreviewModalLabel').textContent = fileName || 'Clearance Document';
    document.getElementById('btnDocDownload').href = url;
    document.getElementById('btnDocDownload').setAttribute('download', fileName || 'clearance_document.pdf');
    document.getElementById('docPreviewFrame').src = url;
    new bootstrap.Modal(document.getElementById('docPreviewModal')).show();
}

// ── Utilities ──────────────────────────────────────────────────────────────
function _renderStatusBadge(status) {
    if (!status || status === 'Not Submitted') {
        return '<span class="fc-status-badge not-submitted"><i class="fas fa-minus-circle"></i> Not Submitted</span>';
    }
    if (status === 'Under Verification') {
        return '<span class="fc-status-badge under-verification"><i class="fas fa-clock"></i> Under Verification</span>';
    }
    if (status === 'Approved') {
        return '<span class="fc-status-badge approved"><i class="fas fa-check-circle"></i> Approved</span>';
    }
    if (status === 'Denied') {
        return '<span class="fc-status-badge denied"><i class="fas fa-exclamation-circle"></i> Denied</span>';
    }
    return `<span class="fc-status-badge not-submitted">${_esc(status)}</span>`;
}

function _esc(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function fcAlert(type, msg) {
    const box = document.getElementById('fcAlertBox');
    box.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show mx-3 mt-2 shadow-sm" role="alert">
            ${msg}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;
    window.scrollTo({ top: 0, behavior: 'smooth' });
    if (type === 'success') {
        setTimeout(() => { const a = box.querySelector('.alert'); if (a) a.remove(); }, 4000);
    }
}
</script>

<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>

