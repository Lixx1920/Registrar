<?php
/**
 * SMS 2 - Student Status Tracker
 * Module: Registrar
 *
 * Compliance-grade Academic Standing & Institutional Lifecycle Management.
 * Implements CHED CMO No. 40 s. 2008 registrar standards:
 *  - 4 Consolidated Macro Registrar KPIs (Active Enrollment, Approved LOA, Attrition, Graduated)
 *  - Dual-View Workspace:
 *      1. Status Transition Audit Ledger (Default - chronological official change log)
 *      2. Student Standing Registry (Filterable student population with LOA 1-yr residency clocks)
 *  - Direct "+ Log Status Transition" modal with student autocomplete, CHED statutory reason
 *    categories, resolution/clearance reference numbers, and effective terms.
 *  - Slide-in Offcanvas for single student lifecycle timeline.
 *  - Exportable CSV Attrition & Transition Ledger.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';

regRequireAction('registrar.view');

$pageTitle    = 'Student Status Tracker';
$activeModule = 'registrar';
$activePage   = 'student-status-tracker';
$breadcrumbs  = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Student Status Tracker', 'url' => null],
];

$db = db();

// Distinct academic programs for filter dropdown
$programs = $db->query(
    "SELECT DISTINCT `program_course` FROM `reg_students`
     WHERE `status` NOT IN ('Deleted', 'Pending') AND `program_course` IS NOT NULL AND `program_course` != ''
     ORDER BY `program_course`"
)->fetchAll(PDO::FETCH_COLUMN);

// Statutory Reason Categories for CHED & Registrar compliance
$statutoryCategories = [
    'Financial / Economic Hardship',
    'Employment / Work Schedule Conflict',
    'Health / Medical Incapacity',
    'Family / Relocation / Domestic Matters',
    'Transfer to Another HEI (Honorable Dismissal)',
    'Academic Deficiency / Prerequisite Failure',
    'Completion of All Curricular Requirements',
    'Disciplinary Sanction / Administrative Exclusion',
    'Return from Approved Leave (Re-admission)',
    'Institutional / Administrative Adjustment',
    'Other Official Reason',
];

$statuses = [
    'Active',
    'Irregular',
    'Leave of Absence',
    'Transferred',
    'Graduated',
    'Inactive',
    'Dismissed',
    'Dropout',
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<link href="<?php echo BASE_URL; ?>/assets/css/module-process-list.css?v=2" rel="stylesheet">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/modules/registrar/assets/css/registrar.css">

<?php renderBreadcrumbs($breadcrumbs); ?>

<div id="sstAlertBox"></div>

<div class="container-fluid py-3">
<div class="mpl" data-mpl>

    <!-- Page Top Bar & Actions -->
    <div class="mpl-top d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <h1 class="h4 mb-1 fw-bold text-dark">
                <i class="fas fa-user-clock text-primary me-2"></i>Student Status Tracker
            </h1>
            <p class="text-muted small mb-0">
                Official registrar academic standing lifecycle management, CHED attrition monitoring, and status transition audit ledger.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="sstExportLedger()" title="Export current audit ledger to CSV">
                <i class="fas fa-file-export me-1"></i> Export Ledger
            </button>
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="sstRefreshAll()" title="Refresh live status metrics">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
            <button type="button" class="btn btn-primary btn-sm" onclick="sstOpenLogModal()">
                <i class="fas fa-plus-circle me-1"></i> Log Status Transition
            </button>
        </div>
    </div>

    <!-- ── 4 Macro Registrar KPI Cards ────────────────────────────────────── -->
    <section class="sst-kpi-grid" aria-label="Registrar Status Metrics">
        <!-- 1. Active Enrollment -->
        <article class="sst-kpi-card" onclick="sstSwitchToRegistry('Active')" style="cursor:pointer;" title="View Active Enrollment">
            <div class="sst-kpi-content">
                <div class="sst-kpi-label">Active Enrollment</div>
                <div class="sst-kpi-value" id="kpiActiveTotal">—</div>
                <div class="sst-kpi-sub">
                    <span id="kpiActiveBreakdown">Loading enrollment…</span>
                </div>
            </div>
            <div class="sst-kpi-icon blue">
                <i class="fas fa-user-check"></i>
            </div>
        </article>

        <!-- 2. On Approved Leave (LOA) -->
        <article class="sst-kpi-card" onclick="sstSwitchToRegistry('Leave of Absence')" style="cursor:pointer;" title="View Students on Leave of Absence">
            <div class="sst-kpi-content">
                <div class="sst-kpi-label">On Approved Leave (LOA)</div>
                <div class="sst-kpi-value" id="kpiLoaTotal">—</div>
                <div class="sst-kpi-sub" id="kpiLoaSub">
                    <span>Max 1-Year Residency Clock</span>
                </div>
            </div>
            <div class="sst-kpi-icon amber">
                <i class="fas fa-calendar-times"></i>
            </div>
        </article>

        <!-- 3. Total Attrition & Out-Transfers -->
        <article class="sst-kpi-card" onclick="sstSwitchToRegistry('Transferred')" style="cursor:pointer;" title="View Attrition & Transferred Students">
            <div class="sst-kpi-content">
                <div class="sst-kpi-label">Institutional Attrition</div>
                <div class="sst-kpi-value" id="kpiAttritionTotal">—</div>
                <div class="sst-kpi-sub">
                    <span id="kpiAttritionRate">Attrition: —%</span>
                    <span class="text-muted">·</span>
                    <span id="kpiTransferredCount">0 Out-transfers</span>
                </div>
            </div>
            <div class="sst-kpi-icon purple">
                <i class="fas fa-exchange-alt"></i>
            </div>
        </article>

        <!-- 4. Degrees Conferred / Graduated -->
        <article class="sst-kpi-card" onclick="sstSwitchToRegistry('Graduated')" style="cursor:pointer;" title="View Conferred / Graduated Students">
            <div class="sst-kpi-content">
                <div class="sst-kpi-label">Degrees Conferred</div>
                <div class="sst-kpi-value" id="kpiGraduatedTotal">—</div>
                <div class="sst-kpi-sub">
                    <span class="text-success"><i class="fas fa-award me-1"></i>Completed Curriculum</span>
                </div>
            </div>
            <div class="sst-kpi-icon teal">
                <i class="fas fa-graduation-cap"></i>
            </div>
        </article>
    </section>

    <!-- ── Dual-View Switcher Bar ─────────────────────────────────────────── -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div class="sst-view-nav" role="tablist">
            <button type="button" class="sst-view-btn active" id="btnTabLedger" onclick="sstSetView('ledger')">
                <i class="fas fa-clipboard-list text-primary"></i>
                Status Transition Audit Ledger
                <span class="badge bg-primary text-white" id="badgeLedgerCount">Live</span>
            </button>
            <button type="button" class="sst-view-btn" id="btnTabRegistry" onclick="sstSetView('registry')">
                <i class="fas fa-users-cog text-secondary"></i>
                Student Standing Registry
            </button>
        </div>
        <div class="text-muted small" id="viewModeHelper">
            <i class="fas fa-shield-alt text-success me-1"></i>
            Chronological audit trail of all student standing changes and official board references.
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════════════════
         VIEW 1: STATUS TRANSITION AUDIT LEDGER (DEFAULT)
         ══════════════════════════════════════════════════════════════════════ -->
    <div id="viewLedgerWrapper">
        <!-- Ledger Filters -->
        <div class="mpl-filters mb-3">
            <label class="mpl-search">
                <i class="fas fa-search"></i>
                <input type="search" id="sstLedgerSearch" placeholder="Search by student, ref #, or reason…" aria-label="Search transitions">
            </label>
            <select id="sstLedgerStatusFilter" aria-label="Filter by target status">
                <option value="">All Target Standings</option>
                <?php foreach ($statuses as $st): ?>
                <option value="<?php echo htmlspecialchars($st); ?>"><?php echo htmlspecialchars($st); ?></option>
                <?php endforeach; ?>
            </select>
            <select id="sstLedgerCategoryFilter" aria-label="Filter by statutory category">
                <option value="">All Statutory Categories</option>
                <?php foreach ($statutoryCategories as $cat): ?>
                <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="button" class="mpl-refresh btn btn-link text-decoration-none p-0" onclick="sstLedgerPage=1; sstLoadLedger();">
                <i class="fas fa-sync-alt" aria-hidden="true"></i> Filter
            </button>
        </div>

        <!-- Ledger Table Panel -->
        <section class="mpl-panel">
            <div class="mpl-panel-head d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="h6 mb-0 fw-bold">Official Status Transition Ledger</h2>
                    <p class="small text-muted mb-0">Complete chronological audit record of student standing changes with administrative resolutions.</p>
                </div>
            </div>
            <div class="mpl-table-wrap">
                <table class="mpl-table">
                    <thead>
                        <tr>
                            <th style="width:140px;">Date &amp; Term</th>
                            <th>Student</th>
                            <th>Program / Section</th>
                            <th>Standing Transition</th>
                            <th>Statutory Category &amp; Ref #</th>
                            <th>Justification / Reason</th>
                            <th>Authorized Officer</th>
                            <th style="width:70px; text-align:center;">History</th>
                        </tr>
                    </thead>
                    <tbody id="sstLedgerTbody">
                        <tr>
                            <td colspan="8" style="text-align:center;color:var(--sms-text-muted);padding:2.5rem;">
                                <i class="fas fa-spinner fa-spin fa-2x text-primary mb-2"></i>
                                <p class="mb-0">Loading status transition ledger…</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mpl-foot">
                <span class="meta" id="sstLedgerPagInfo">—</span>
                <div class="mpl-pager" id="sstLedgerPager"></div>
            </div>
        </section>
    </div>

    <!-- ══════════════════════════════════════════════════════════════════════
         VIEW 2: STUDENT STANDING REGISTRY
         ══════════════════════════════════════════════════════════════════════ -->
    <div id="viewRegistryWrapper" style="display:none;">
        <!-- Registry Status Filter Pills -->
        <div class="d-flex align-items-center gap-2 flex-wrap mb-3" id="registryFilterPills">
            <button type="button" class="btn btn-sm btn-outline-secondary active" onclick="sstSetRegistryFilter('')">
                All Students
            </button>
            <button type="button" class="btn btn-sm btn-outline-success" onclick="sstSetRegistryFilter('Active')">
                <i class="fas fa-circle me-1" style="font-size:0.55rem;"></i> Active Regular
            </button>
            <button type="button" class="btn btn-sm btn-outline-purple" style="color:#6b21a8; border-color:#d8b4fe;" onclick="sstSetRegistryFilter('Irregular')">
                <i class="fas fa-circle me-1" style="font-size:0.55rem;"></i> Irregular
            </button>
            <button type="button" class="btn btn-sm btn-outline-warning" onclick="sstSetRegistryFilter('Leave of Absence')">
                <i class="fas fa-calendar-times me-1"></i> Leave of Absence
            </button>
            <button type="button" class="btn btn-sm btn-outline-info" onclick="sstSetRegistryFilter('Transferred')">
                <i class="fas fa-exchange-alt me-1"></i> Transferred Out
            </button>
            <button type="button" class="btn btn-sm btn-outline-teal" style="color:#0f766e; border-color:#99f6e4;" onclick="sstSetRegistryFilter('Graduated')">
                <i class="fas fa-graduation-cap me-1"></i> Graduated
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="sstSetRegistryFilter('Dismissed')">
                <i class="fas fa-ban me-1"></i> Dismissed / Inactive
            </button>
        </div>

        <!-- Registry Filters -->
        <div class="mpl-filters mb-3">
            <label class="mpl-search">
                <i class="fas fa-search"></i>
                <input type="search" id="sstRegistrySearch" placeholder="Search by student number or name…" aria-label="Search students">
            </label>
            <select id="sstRegistryProgramFilter" aria-label="Filter by program">
                <option value="">All Programs</option>
                <?php foreach ($programs as $prog): ?>
                <option value="<?php echo htmlspecialchars($prog); ?>"><?php echo htmlspecialchars($prog); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="button" class="mpl-refresh btn btn-link text-decoration-none p-0" onclick="sstRegistryPage=1; sstLoadRegistry();">
                <i class="fas fa-sync-alt" aria-hidden="true"></i> Refresh
            </button>
        </div>

        <!-- Registry Table Panel -->
        <section class="mpl-panel">
            <div class="mpl-panel-head">
                <div>
                    <h2 class="h6 mb-0 fw-bold">Student Standing &amp; Residency Registry</h2>
                    <p class="small text-muted mb-0">Active student population, institutional standing, and LOA maximum residency compliance clocks.</p>
                </div>
            </div>
            <div class="mpl-table-wrap">
                <table class="mpl-table">
                    <thead>
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>Student</th>
                            <th>Program / Year &amp; Section</th>
                            <th>Current Standing</th>
                            <th>Last Status Change</th>
                            <th>Residency / LOA Status</th>
                            <th style="width:130px; text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sstRegistryTbody">
                        <tr>
                            <td colspan="7" style="text-align:center;color:var(--sms-text-muted);padding:2rem;">
                                <i class="fas fa-spinner fa-spin text-primary"></i>
                                <p class="mt-2 mb-0">Loading student registry…</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mpl-foot">
                <span class="meta" id="sstRegistryPagInfo">—</span>
                <div class="mpl-pager" id="sstRegistryPager"></div>
            </div>
        </section>
    </div>

</div><!-- /mpl -->
</div><!-- /container -->

<!-- ══════════════════════════════════════════════════════════════════════════
     MODAL: LOG STATUS TRANSITION (+ STUDENT PICKER)
     ══════════════════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="logTransitionModal" tabindex="-1" aria-labelledby="logTransitionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px; overflow:hidden;">
            <!-- Modal Header -->
            <div class="modal-header text-white" style="background:linear-gradient(135deg, #1e293b, #2563eb); padding:1rem 1.4rem;">
                <div>
                    <h5 class="modal-title fw-bold fs-6 mb-0" id="logTransitionModalLabel">
                        <i class="fas fa-plus-circle me-2"></i>Log Official Status Transition
                    </h5>
                    <small style="opacity:0.85; font-size:0.78rem;">
                        Record academic standing change with CHED statutory classification and resolution reference.
                    </small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <form id="logTransitionForm" onsubmit="return sstSubmitModalTransition(event)">
                <div class="modal-body p-4" style="background:#f8fafc;">
                    <input type="hidden" id="ltStudentId" required>

                    <!-- Student Picker Section -->
                    <div class="mb-3 position-relative">
                        <label class="form-label fw-bold text-dark small mb-1">
                            Select Student <span class="text-danger">*</span>
                        </label>
                        <div id="ltPickerInputGroup">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" id="ltStudentSearch" class="form-control border-start-0" 
                                       placeholder="Type student name or student number (e.g. 2024001, Juan)..." autocomplete="off">
                            </div>
                            <div class="sst-picker-results" id="ltPickerResults"></div>
                        </div>

                        <!-- Selected Student Preview Card -->
                        <div id="ltSelectedCard" class="sst-selected-student-card mt-2" style="display:none;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="mpl-avatar" id="ltCardAvatar" style="width:40px; height:40px; font-size:0.85rem; border-radius:8px;">ST</div>
                                <div>
                                    <div class="fw-bold text-dark fs-6" id="ltCardName">—</div>
                                    <div class="small text-muted" id="ltCardMeta">—</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div id="ltCardStatusBadge"></div>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" onclick="sstClearSelectedStudent()" title="Change student">
                                    <i class="fas fa-times"></i> Change
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Status Change Details Grid -->
                    <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius:10px; background:#fff;">
                        <div class="row g-3">
                            <!-- Target Status -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark mb-1">
                                    New Academic Standing <span class="text-danger">*</span>
                                </label>
                                <select id="ltNewStatus" class="form-select form-select-sm" required>
                                    <option value="">— Select Target Standing —</option>
                                    <?php foreach ($statuses as $st): ?>
                                    <option value="<?php echo htmlspecialchars($st); ?>"><?php echo htmlspecialchars($st); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Statutory Reason Category -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark mb-1">
                                    Statutory Reason Category <span class="text-danger">*</span>
                                </label>
                                <select id="ltReasonCategory" class="form-select form-select-sm" required>
                                    <option value="">— Select CHED / Official Category —</option>
                                    <?php foreach ($statutoryCategories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Resolution / Reference Number -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark mb-1">
                                    Board / Clearance Reference No.
                                </label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-muted"><i class="fas fa-hashtag"></i></span>
                                    <input type="text" id="ltRefNo" class="form-control" placeholder="e.g. LOA-2024-0012, BOR-RES-104" maxlength="100">
                                </div>
                                <div class="form-text" style="font-size:0.72rem;">Official resolution, clearance slip, or memorandum reference.</div>
                            </div>

                            <!-- Effective Term -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark mb-1">
                                    Effective Academic Year &amp; Term
                                </label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-muted"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="text" id="ltEffectiveTerm" class="form-control" placeholder="e.g. AY 2024-2025, 1st Semester" maxlength="50">
                                </div>
                                <div class="form-text" style="font-size:0.72rem;">Academic term when transition takes institutional effect.</div>
                            </div>

                            <!-- Specific Rationale / Justification -->
                            <div class="col-12">
                                <label class="form-label fw-bold small text-dark mb-1">
                                    Official Rationale / Justification <span class="text-danger">*</span>
                                </label>
                                <textarea id="ltReason" class="form-control form-control-sm" rows="2" required maxlength="255" 
                                          placeholder="Provide specific justification (e.g. Approved leave for medical procedure with clearance, prerequisite deficiency, etc.)."></textarea>
                            </div>

                            <!-- Internal Remarks -->
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted mb-1">
                                    Internal Registrar Notes (Optional)
                                </label>
                                <textarea id="ltNotes" class="form-control form-control-sm" rows="2" maxlength="1000" 
                                          placeholder="Additional confidential office notes, supporting attachments noted, or instructions."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer bg-light px-4 py-2 border-top">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3" id="ltSubmitBtn">
                        <i class="fas fa-save me-1"></i> Apply &amp; Record Transition
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════════════════
     OFFCANVAS: SINGLE STUDENT LIFECYCLE TIMELINE & HISTORY
     ══════════════════════════════════════════════════════════════════════════ -->
<div class="offcanvas offcanvas-end shadow-lg" tabindex="-1" id="sstOffcanvas" style="width:460px;" aria-labelledby="sstOffcanvasLabel">
    <!-- Header -->
    <div class="offcanvas-header text-white" style="background:linear-gradient(135deg, #1e293b, #2563eb); padding:1rem 1.25rem;">
        <div>
            <h5 class="offcanvas-title fs-6 fw-bold mb-0" id="sstOffcanvasLabel">Student Standing &amp; History</h5>
            <small id="ocStudentSub" style="opacity:0.85; font-size:0.75rem;"></small>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-0" style="overflow-y:auto;">
        <!-- Quick Status Change Accordion / Card -->
        <div class="p-3 border-bottom bg-light">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="fw-bold small text-dark"><i class="fas fa-exchange-alt me-1 text-primary"></i> Apply Standing Change</span>
                <span id="ocCurrentStandingBadge"></span>
            </div>
            <form id="ocChangeForm" onsubmit="return sstSubmitOcChange(event)">
                <input type="hidden" id="ocStudentId">
                <div class="row g-2">
                    <div class="col-12">
                        <select id="ocNewStatus" class="form-select form-select-sm" required>
                            <option value="">— Target Standing —</option>
                            <?php foreach ($statuses as $st): ?>
                            <option value="<?php echo htmlspecialchars($st); ?>"><?php echo htmlspecialchars($st); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <select id="ocReasonCategory" class="form-select form-select-sm" required>
                            <option value="">— Statutory Category —</option>
                            <?php foreach ($statutoryCategories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6">
                        <input type="text" id="ocRefNo" class="form-control form-control-sm" placeholder="Ref / Board #" maxlength="100">
                    </div>
                    <div class="col-6">
                        <input type="text" id="ocEffectiveTerm" class="form-control form-control-sm" placeholder="Effective Term" maxlength="50">
                    </div>
                    <div class="col-12">
                        <input type="text" id="ocReason" class="form-control form-control-sm" required placeholder="Specific rationale / justification" maxlength="255">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-sm w-100" id="ocSubmitBtn">
                            <i class="fas fa-save me-1"></i> Update Standing
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Timeline Section -->
        <div class="p-3">
            <div class="fw-bold small text-dark mb-3">
                <i class="fas fa-history text-info me-1"></i> Status Transition Timeline
            </div>
            <div id="ocTimeline">
                <p class="text-muted text-center py-4 small">
                    <i class="fas fa-spinner fa-spin text-primary"></i><br>Loading history…
                </p>
            </div>
        </div>
    </div>
</div>

<script>
const SST_API = '<?php echo BASE_URL; ?>/modules/registrar/api/status.php';
const CSRF    = '<?= e(csrfToken()) ?>';

// ── State ──────────────────────────────────────────────────────────────────
let sstCurrentView = 'ledger'; // 'ledger' or 'registry'

// Ledger pagination state
let sstLedgerPage  = 1;
let sstLedgerTotal = 0;
let sstLedgerPages = 1;
const LEDGER_LIMIT = 20;

// Registry pagination state
let sstRegistryPage   = 1;
let sstRegistryTotal  = 0;
let sstRegistryPages  = 1;
let sstRegistryFilter = '';
const REGISTRY_LIMIT  = 20;

let sstDebounceTimer;
let sstPickerTimer;
let sstSelectedStudent = null;

// ── Initialization ─────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    sstRefreshAll();

    // Ledger filters listeners
    document.getElementById('sstLedgerSearch').addEventListener('input', function () {
        clearTimeout(sstDebounceTimer);
        sstDebounceTimer = setTimeout(() => { sstLedgerPage = 1; sstLoadLedger(); }, 300);
    });
    document.getElementById('sstLedgerStatusFilter').addEventListener('change', () => { sstLedgerPage = 1; sstLoadLedger(); });
    document.getElementById('sstLedgerCategoryFilter').addEventListener('change', () => { sstLedgerPage = 1; sstLoadLedger(); });

    // Registry filters listeners
    document.getElementById('sstRegistrySearch').addEventListener('input', function () {
        clearTimeout(sstDebounceTimer);
        sstDebounceTimer = setTimeout(() => { sstRegistryPage = 1; sstLoadRegistry(); }, 300);
    });
    document.getElementById('sstRegistryProgramFilter').addEventListener('change', () => { sstRegistryPage = 1; sstLoadRegistry(); });

    // Student picker autocomplete in modal
    document.getElementById('ltStudentSearch').addEventListener('input', function () {
        clearTimeout(sstPickerTimer);
        const q = this.value.trim();
        if (q.length < 2) {
            document.getElementById('ltPickerResults').style.display = 'none';
            return;
        }
        sstPickerTimer = setTimeout(() => sstSearchPicker(q), 250);
    });

    // Close picker results when clicking outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('#ltPickerInputGroup')) {
            document.getElementById('ltPickerResults').style.display = 'none';
        }
    });
});

// ── View Switcher ──────────────────────────────────────────────────────────
function sstSetView(view) {
    sstCurrentView = view;
    const btnLedger   = document.getElementById('btnTabLedger');
    const btnRegistry = document.getElementById('btnTabRegistry');
    const wrapLedger   = document.getElementById('viewLedgerWrapper');
    const wrapRegistry = document.getElementById('viewRegistryWrapper');
    const helper       = document.getElementById('viewModeHelper');

    if (view === 'ledger') {
        btnLedger.classList.add('active');
        btnRegistry.classList.remove('active');
        wrapLedger.style.display = 'block';
        wrapRegistry.style.display = 'none';
        helper.innerHTML = '<i class="fas fa-shield-alt text-success me-1"></i> Chronological audit trail of all student standing changes and official board references.';
        sstLoadLedger();
    } else {
        btnLedger.classList.remove('active');
        btnRegistry.classList.add('active');
        wrapLedger.style.display = 'none';
        wrapRegistry.style.display = 'block';
        helper.innerHTML = '<i class="fas fa-clock text-warning me-1"></i> Filterable student population directory with CHED Leave of Absence (LOA) residency compliance monitoring.';
        sstLoadRegistry();
    }
}

function sstSwitchToRegistry(statusFilter) {
    sstSetRegistryFilter(statusFilter);
    sstSetView('registry');
}

function sstRefreshAll() {
    sstLoadStats();
    if (sstCurrentView === 'ledger') {
        sstLoadLedger();
    } else {
        sstLoadRegistry();
    }
}

// ── 1. Macro KPI Stats ─────────────────────────────────────────────────────
async function sstLoadStats() {
    try {
        const res  = await fetch(SST_API + '?action=stats');
        const data = await res.json();
        if (!data.success) return;

        const kpi = data.kpi;

        // Active Enrollment
        document.getElementById('kpiActiveTotal').textContent = kpi.total_active ?? 0;
        document.getElementById('kpiActiveBreakdown').innerHTML = 
            `<strong>${kpi.active_regular ?? 0}</strong> Regular · <strong>${kpi.irregular ?? 0}</strong> Irregular`;

        // LOA
        const loaTotal = kpi.loa_total ?? 0;
        document.getElementById('kpiLoaTotal').textContent = loaTotal;
        const loaSub = document.getElementById('kpiLoaSub');
        if (kpi.loa_critical > 0) {
            loaSub.innerHTML = `<span class="badge bg-danger text-white"><i class="fas fa-exclamation-triangle me-1"></i>${kpi.loa_critical} Near 1-Yr Limit</span>`;
        } else {
            loaSub.innerHTML = `<span class="text-success"><i class="fas fa-check-circle me-1"></i>Within 1-Yr Limit</span>`;
        }

        // Attrition
        document.getElementById('kpiAttritionTotal').textContent = kpi.attrition_total ?? 0;
        document.getElementById('kpiAttritionRate').textContent = `Attrition: ${kpi.attrition_rate ?? 0}%`;
        document.getElementById('kpiTransferredCount').textContent = `${kpi.transferred ?? 0} Transferred`;

        // Conferred
        document.getElementById('kpiGraduatedTotal').textContent = kpi.graduated ?? 0;

        // Badge on tab
        const b = document.getElementById('badgeLedgerCount');
        if (b && kpi.recent_30d_count !== undefined) {
            b.textContent = `${kpi.recent_30d_count} this month`;
        }
    } catch (_) {}
}

// ── 2. View 1: Status Transition Audit Ledger ─────────────────────────────
async function sstLoadLedger() {
    const q        = document.getElementById('sstLedgerSearch').value.trim();
    const statusTo = document.getElementById('sstLedgerStatusFilter').value;
    const category = document.getElementById('sstLedgerCategoryFilter').value;

    const params = new URLSearchParams({
        action: 'recent_changes',
        page: sstLedgerPage,
        limit: LEDGER_LIMIT,
        q,
        status_to: statusTo,
        category,
    });

    const tbody = document.getElementById('sstLedgerTbody');
    tbody.innerHTML = `
        <tr><td colspan="8" style="text-align:center;color:var(--sms-text-muted);padding:2.5rem;">
            <i class="fas fa-spinner fa-spin fa-2x text-primary mb-2"></i>
            <p class="mb-0">Loading status transition ledger…</p>
        </td></tr>`;

    try {
        const res  = await fetch(SST_API + '?' + params);
        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Failed to load ledger');

        sstLedgerTotal = data.pagination.total;
        sstLedgerPages = data.pagination.pages;

        _renderLedgerTable(data.data);
        _renderLedgerPager();
        _renderLedgerPagInfo();
    } catch (e) {
        tbody.innerHTML = `
            <tr><td colspan="8" style="text-align:center;color:#ef4444;padding:2rem;">
                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                <p class="mb-0">Error loading transition ledger: ${_esc(e.message)}</p>
            </td></tr>`;
    }
}

function _renderLedgerTable(rows) {
    const tbody = document.getElementById('sstLedgerTbody');
    if (!rows || rows.length === 0) {
        tbody.innerHTML = `
            <tr><td colspan="8" style="text-align:center;color:var(--sms-text-muted);padding:3rem 1rem;">
                <i class="fas fa-clipboard-check fa-3x text-muted mb-2" style="opacity:0.4;"></i>
                <h6 class="fw-bold mb-1">No Status Transitions Recorded</h6>
                <p class="small mb-0">No transitions match the selected criteria, or no status changes have been recorded yet.</p>
            </td></tr>`;
        return;
    }

    let html = '';
    rows.forEach(r => {
        const dateFormatted = _formatDateTime(r.changed_at);
        const initials = ((r.first_name?.[0] ?? '') + (r.last_name?.[0] ?? '')).toUpperCase() || 'ST';
        const progSection = _esc(r.program_course || '—') + (r.year_section ? ' / ' + _esc(r.year_section) : '');
        const officer = r.changed_by_name ? _esc(r.changed_by_name) : 'Authorized Staff';
        const refPill = r.ref_no ? `<span class="sst-ref-pill"><i class="fas fa-hashtag"></i>${_esc(r.ref_no)}</span>` : '';
        const termBadge = r.effective_term ? `<span class="badge bg-light text-dark border ms-1" style="font-size:0.68rem;">${_esc(r.effective_term)}</span>` : '';

        html += `
            <tr>
                <td>
                    <div class="fw-bold text-dark small">${dateFormatted.date}</div>
                    <div class="text-muted" style="font-size:0.72rem;">${dateFormatted.time} ${termBadge}</div>
                </td>
                <td>
                    <div class="mpl-person">
                        <span class="mpl-avatar">${initials}</span>
                        <div>
                            <strong>${_esc(r.last_name + ', ' + r.first_name)}</strong>
                            <small class="text-muted">${_esc(r.student_number)}</small>
                        </div>
                    </div>
                </td>
                <td style="max-width:200px;">
                    <div class="text-truncate small" title="${_esc(progSection)}">${progSection}</div>
                </td>
                <td>
                    <div class="sst-transition-flow">
                        ${_renderStatusBadge(r.changed_from)}
                        <i class="fas fa-arrow-right"></i>
                        ${_renderStatusBadge(r.changed_to)}
                    </div>
                </td>
                <td>
                    <div class="fw-semibold text-dark small mb-1">${_esc(r.reason_category || 'General Administrative')}</div>
                    ${refPill}
                </td>
                <td style="max-width:260px;">
                    <div class="small text-dark fw-medium">${_esc(r.reason || '—')}</div>
                    ${r.notes ? `<div class="text-muted small mt-1" style="font-size:0.72rem;"><i class="fas fa-sticky-note me-1"></i>${_esc(r.notes)}</div>` : ''}
                </td>
                <td>
                    <span class="small text-dark"><i class="fas fa-user-shield text-secondary me-1"></i>${officer}</span>
                </td>
                <td style="text-align:center;">
                    <button type="button" class="btn btn-sm btn-light border py-1 px-2" onclick="sstOpenPanel(${r.student_id})" title="View Complete History">
                        <i class="fas fa-history text-primary"></i>
                    </button>
                </td>
            </tr>`;
    });
    tbody.innerHTML = html;
}

function _renderLedgerPager() {
    const el = document.getElementById('sstLedgerPager');
    if (sstLedgerPages <= 1) { el.innerHTML = ''; return; }

    let html = `<button onclick="sstGoLedgerPage(${Math.max(1, sstLedgerPage - 1)})" ${sstLedgerPage <= 1 ? 'disabled' : ''}>‹ Prev</button>`;
    const start = Math.max(1, sstLedgerPage - 2);
    const end   = Math.min(sstLedgerPages, sstLedgerPage + 2);
    for (let p = start; p <= end; p++) {
        html += `<button onclick="sstGoLedgerPage(${p})" class="${p === sstLedgerPage ? 'active' : ''}">${p}</button>`;
    }
    html += `<button onclick="sstGoLedgerPage(${Math.min(sstLedgerPages, sstLedgerPage + 1)})" ${sstLedgerPage >= sstLedgerPages ? 'disabled' : ''}>Next ›</button>`;
    el.innerHTML = html;
}

function _renderLedgerPagInfo() {
    const offset = (sstLedgerPage - 1) * LEDGER_LIMIT;
    const end    = Math.min(offset + LEDGER_LIMIT, sstLedgerTotal);
    document.getElementById('sstLedgerPagInfo').textContent =
        sstLedgerTotal === 0 ? 'No transition records' : `Showing ${offset + 1}–${end} of ${sstLedgerTotal} transitions`;
}

function sstGoLedgerPage(p) { sstLedgerPage = p; sstLoadLedger(); }

// ── 3. View 2: Student Standing Registry ──────────────────────────────────
function sstSetRegistryFilter(st) {
    sstRegistryFilter = st;
    const pillButtons = document.querySelectorAll('#registryFilterPills button');
    pillButtons.forEach(btn => btn.classList.remove('active'));

    // Highlight clicked button
    pillButtons.forEach(btn => {
        if (st === '' && btn.textContent.includes('All Students')) btn.classList.add('active');
        else if (st !== '' && btn.textContent.includes(st)) btn.classList.add('active');
    });

    sstRegistryPage = 1;
    sstLoadRegistry();
}

async function sstLoadRegistry() {
    const q       = document.getElementById('sstRegistrySearch').value.trim();
    const program = document.getElementById('sstRegistryProgramFilter').value;

    const params = new URLSearchParams({
        action: 'list',
        page: sstRegistryPage,
        limit: REGISTRY_LIMIT,
        status: sstRegistryFilter,
        program,
        q,
    });

    const tbody = document.getElementById('sstRegistryTbody');
    tbody.innerHTML = `
        <tr><td colspan="7" style="text-align:center;color:var(--sms-text-muted);padding:2rem;">
            <i class="fas fa-spinner fa-spin text-primary"></i>
            <p class="mt-2 mb-0">Loading student registry…</p>
        </td></tr>`;

    try {
        const res  = await fetch(SST_API + '?' + params);
        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Failed to load registry');

        sstRegistryTotal = data.pagination.total;
        sstRegistryPages = data.pagination.pages;

        _renderRegistryTable(data.data);
        _renderRegistryPager();
        _renderRegistryPagInfo();
    } catch (e) {
        tbody.innerHTML = `
            <tr><td colspan="7" style="text-align:center;color:#ef4444;padding:2rem;">
                <i class="fas fa-exclamation-triangle"></i>
                <p class="mt-2 mb-0">Error: ${_esc(e.message)}</p>
            </td></tr>`;
    }
}

function _renderRegistryTable(rows) {
    const tbody = document.getElementById('sstRegistryTbody');
    const offset = (sstRegistryPage - 1) * REGISTRY_LIMIT;

    if (!rows || rows.length === 0) {
        tbody.innerHTML = `
            <tr><td colspan="7" style="text-align:center;padding:2.5rem;color:var(--sms-text-muted)">
                <i class="fas fa-user-slash fa-2x mb-2 text-muted"></i>
                <p class="mb-0">No students match current standing filters.</p>
            </td></tr>`;
        return;
    }

    let html = '';
    rows.forEach((r, idx) => {
        const initials = ((r.first_name?.[0] ?? '') + (r.last_name?.[0] ?? '')).toUpperCase() || 'ST';
        const progYear = _esc(r.program_course || '—') + (r.year_section ? ' / ' + _esc(r.year_section) : '');
        const since = r.last_changed_at ? _formatDateTime(r.last_changed_at).date : 'Active Enrollment';

        // LOA Residency clock
        let residencyClock = '<span class="text-muted small">—</span>';
        if (r.status === 'Leave of Absence') {
            if (r.is_loa_critical) {
                residencyClock = `
                    <span class="sst-loa-chip critical" title="Approaching 1-year CHED maximum leave limit">
                        <i class="fas fa-exclamation-triangle"></i> Day ${r.loa_days} / 365 (${r.loa_remaining_days}d left)
                    </span>`;
            } else if (r.loa_days !== null) {
                residencyClock = `
                    <span class="sst-loa-chip" title="Allowable leave: 365 days max">
                        <i class="fas fa-clock"></i> Day ${r.loa_days} / 365 (${r.loa_remaining_days}d left)
                    </span>`;
            } else {
                residencyClock = `<span class="sst-loa-chip"><i class="fas fa-clock"></i> On Approved LOA</span>`;
            }
        } else if (r.status === 'Graduated') {
            residencyClock = `<span class="badge bg-teal-subtle text-teal" style="font-size:0.72rem; background:#ccfbf1; color:#0f766e;"><i class="fas fa-award me-1"></i>Conferred</span>`;
        } else if (r.status === 'Transferred') {
            residencyClock = `<span class="badge bg-light text-muted border" style="font-size:0.72rem;"><i class="fas fa-file-alt me-1"></i>Honorable Dismissal</span>`;
        }

        html += `
            <tr>
                <td style="font-size:0.75rem;color:var(--sms-text-muted);">${offset + idx + 1}</td>
                <td>
                    <div class="mpl-person">
                        <span class="mpl-avatar">${initials}</span>
                        <div>
                            <strong>${_esc(r.last_name + ', ' + r.first_name)}</strong>
                            <small class="text-muted">${_esc(r.student_number)}</small>
                        </div>
                    </div>
                </td>
                <td>${progYear}</td>
                <td>${_renderStatusBadge(r.status)}</td>
                <td><small class="text-muted">${since}</small></td>
                <td>${residencyClock}</td>
                <td style="text-align:center;">
                    <div class="d-flex align-items-center justify-content-center gap-1">
                        <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2" 
                                onclick="sstOpenLogModalForStudent(${r.id}, '${_esc(r.last_name + ', ' + r.first_name)}', '${_esc(r.student_number)}', '${_esc(r.program_course || '')}', '${_esc(r.status)}')" 
                                title="Change Standing">
                            <i class="fas fa-exchange-alt"></i> Change
                        </button>
                        <button type="button" class="btn btn-sm btn-light border py-1 px-2" onclick="sstOpenPanel(${r.id})" title="View History">
                            <i class="fas fa-history text-secondary"></i>
                        </button>
                    </div>
                </td>
            </tr>`;
    });
    tbody.innerHTML = html;
}

function _renderRegistryPager() {
    const el = document.getElementById('sstRegistryPager');
    if (sstRegistryPages <= 1) { el.innerHTML = ''; return; }

    let html = `<button onclick="sstGoRegistryPage(${Math.max(1, sstRegistryPage - 1)})" ${sstRegistryPage <= 1 ? 'disabled' : ''}>‹ Prev</button>`;
    const start = Math.max(1, sstRegistryPage - 2);
    const end   = Math.min(sstRegistryPages, sstRegistryPage + 2);
    for (let p = start; p <= end; p++) {
        html += `<button onclick="sstGoRegistryPage(${p})" class="${p === sstRegistryPage ? 'active' : ''}">${p}</button>`;
    }
    html += `<button onclick="sstGoRegistryPage(${Math.min(sstRegistryPages, sstRegistryPage + 1)})" ${sstRegistryPage >= sstRegistryPages ? 'disabled' : ''}>Next ›</button>`;
    el.innerHTML = html;
}

function _renderRegistryPagInfo() {
    const offset = (sstRegistryPage - 1) * REGISTRY_LIMIT;
    const end    = Math.min(offset + REGISTRY_LIMIT, sstRegistryTotal);
    document.getElementById('sstRegistryPagInfo').textContent =
        sstRegistryTotal === 0 ? 'No students' : `Showing ${offset + 1}–${end} of ${sstRegistryTotal} students`;
}

function sstGoRegistryPage(p) { sstRegistryPage = p; sstLoadRegistry(); }

// ── 4. Log Status Transition Modal ─────────────────────────────────────────
function sstOpenLogModal() {
    sstClearSelectedStudent();
    document.getElementById('logTransitionForm').reset();
    document.getElementById('ltStudentId').value = '';
    new bootstrap.Modal(document.getElementById('logTransitionModal')).show();
}

function sstOpenLogModalForStudent(id, name, number, program, currentStatus) {
    sstSelectStudent({
        id: id,
        first_name: name.split(', ')[1] || '',
        last_name: name.split(', ')[0] || '',
        student_number: number,
        program_course: program,
        status: currentStatus,
    });
    document.getElementById('logTransitionForm').reset();
    document.getElementById('ltStudentId').value = id;
    new bootstrap.Modal(document.getElementById('logTransitionModal')).show();
}

async function sstSearchPicker(q) {
    const container = document.getElementById('ltPickerResults');
    try {
        const res  = await fetch(SST_API + '?action=search&q=' + encodeURIComponent(q));
        const data = await res.json();
        if (!data.success || !data.data || data.data.length === 0) {
            container.innerHTML = '<div class="p-2 text-muted small text-center">No students found.</div>';
            container.style.display = 'block';
            return;
        }

        let html = '';
        data.data.forEach(s => {
            const name = _esc(s.last_name + ', ' + s.first_name);
            const num  = _esc(s.student_number);
            const prog = _esc(s.program_course || '—');
            const safeObj = JSON.stringify(s).replace(/"/g, '&quot;');
            html += `
                <div class="sst-picker-item" onclick="sstSelectStudent(${safeObj})">
                    <div>
                        <div class="fw-bold small text-dark">${name}</div>
                        <div class="text-muted" style="font-size:0.72rem;">${num} · ${prog}</div>
                    </div>
                    <div>${_renderStatusBadge(s.status)}</div>
                </div>`;
        });
        container.innerHTML = html;
        container.style.display = 'block';
    } catch (_) {
        container.style.display = 'none';
    }
}

function sstSelectStudent(student) {
    sstSelectedStudent = student;
    document.getElementById('ltStudentId').value = student.id;
    document.getElementById('ltPickerInputGroup').style.display = 'none';

    const card = document.getElementById('ltSelectedCard');
    document.getElementById('ltCardName').textContent = student.last_name + ', ' + student.first_name;
    document.getElementById('ltCardMeta').textContent = student.student_number + ' · ' + (student.program_course || '—');
    document.getElementById('ltCardAvatar').textContent = ((student.first_name?.[0] || '') + (student.last_name?.[0] || '')).toUpperCase();
    document.getElementById('ltCardStatusBadge').innerHTML = `Currently: ${_renderStatusBadge(student.status)}`;
    card.style.display = 'flex';

    // Disable target option matching current status
    const sel = document.getElementById('ltNewStatus');
    Array.from(sel.options).forEach(opt => {
        opt.disabled = (opt.value === student.status);
    });
}

function sstClearSelectedStudent() {
    sstSelectedStudent = null;
    document.getElementById('ltStudentId').value = '';
    document.getElementById('ltStudentSearch').value = '';
    document.getElementById('ltPickerResults').style.display = 'none';
    document.getElementById('ltPickerInputGroup').style.display = 'block';
    document.getElementById('ltSelectedCard').style.display = 'none';
}

async function sstSubmitModalTransition(e) {
    e.preventDefault();
    const studentId      = parseInt(document.getElementById('ltStudentId').value);
    const newStatus      = document.getElementById('ltNewStatus').value;
    const reasonCategory = document.getElementById('ltReasonCategory').value;
    const refNo          = document.getElementById('ltRefNo').value.trim();
    const effectiveTerm  = document.getElementById('ltEffectiveTerm').value.trim();
    const reason         = document.getElementById('ltReason').value.trim();
    const notes          = document.getElementById('ltNotes').value.trim();
    const btn            = document.getElementById('ltSubmitBtn');

    if (!studentId) {
        sstAlert('danger', 'Please select a student first.');
        return false;
    }
    if (!newStatus || !reasonCategory || !reason) {
        sstAlert('danger', 'Please complete all required transition fields.');
        return false;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving…';

    try {
        const res = await fetch(SST_API + '?action=change_status', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF },
            body: JSON.stringify({
                student_id: studentId,
                new_status: newStatus,
                reason_category: reasonCategory,
                ref_no: refNo,
                effective_term: effectiveTerm,
                reason,
                notes,
            }),
        });
        const data = await res.json();

        if (!data.success) {
            sstAlert('danger', data.error || 'Failed to record transition.');
        } else {
            bootstrap.Modal.getInstance(document.getElementById('logTransitionModal'))?.hide();
            sstAlert('success', data.message || 'Status transition recorded successfully.');
            sstRefreshAll();
        }
    } catch (err) {
        sstAlert('danger', 'Error: ' + err.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i> Apply &amp; Record Transition';
    }
    return false;
}

// ── 5. Offcanvas Student History Drawer ─────────────────────────────────────
async function sstOpenPanel(studentId) {
    document.getElementById('ocStudentId').value = studentId;
    document.getElementById('ocChangeForm').reset();
    document.getElementById('ocStudentId').value = studentId;

    document.getElementById('sstOffcanvasLabel').textContent = 'Loading Student Profile…';
    document.getElementById('ocStudentSub').textContent = '—';
    document.getElementById('ocCurrentStandingBadge').innerHTML = '';
    document.getElementById('ocTimeline').innerHTML = `
        <p class="text-muted text-center py-4 small">
            <i class="fas fa-spinner fa-spin text-primary"></i><br>Loading history…
        </p>`;

    new bootstrap.Offcanvas(document.getElementById('sstOffcanvas')).show();

    try {
        const res  = await fetch(SST_API + '?action=history&id=' + studentId);
        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Failed to load');

        const s = data.student;
        document.getElementById('sstOffcanvasLabel').textContent = s.last_name + ', ' + s.first_name;
        document.getElementById('ocStudentSub').textContent =
            s.student_number + ' · ' + (s.program_course || '') + (s.year_section ? ' / ' + s.year_section : '');
        document.getElementById('ocCurrentStandingBadge').innerHTML = _renderStatusBadge(s.status);

        // Mark current in select
        const sel = document.getElementById('ocNewStatus');
        Array.from(sel.options).forEach(o => {
            o.disabled = (o.value === s.status);
        });
        sel.value = '';

        _renderTimeline(data.history);
    } catch (e) {
        document.getElementById('ocTimeline').innerHTML = `
            <p class="text-danger small text-center">Error: ${_esc(e.message)}</p>`;
    }
}

function _renderTimeline(history) {
    const el = document.getElementById('ocTimeline');
    if (!history || history.length === 0) {
        el.innerHTML = '<p class="text-muted text-center small py-3">No status changes recorded yet.</p>';
        return;
    }

    let html = '<div class="oc-timeline">';
    history.forEach(h => {
        const dt = _formatDateTime(h.changed_at);
        const by = h.changed_by_name ? 'Authorized: ' + _esc(h.changed_by_name) : 'Authorized Staff';
        const refPill = h.ref_no ? `<span class="sst-ref-pill ms-1"><i class="fas fa-hashtag"></i>${_esc(h.ref_no)}</span>` : '';
        const termBadge = h.effective_term ? `<span class="badge bg-light text-dark border ms-1" style="font-size:0.68rem;">${_esc(h.effective_term)}</span>` : '';

        html += `
            <div class="oc-tl-item">
                <div class="oc-tl-dot green"><i class="fas fa-check" style="font-size:0.55rem;"></i></div>
                <div class="oc-tl-card">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <div class="sst-transition-flow">
                            ${_renderStatusBadge(h.changed_from)}
                            <i class="fas fa-arrow-right"></i>
                            ${_renderStatusBadge(h.changed_to)}
                        </div>
                        <div class="text-muted" style="font-size:0.7rem;">${dt.date}</div>
                    </div>
                    <div class="small fw-bold text-dark mt-2">${_esc(h.reason_category || 'General Administrative')} ${refPill} ${termBadge}</div>
                    ${h.reason ? `<div class="tl-reason text-dark small mt-1">${_esc(h.reason)}</div>` : ''}
                    ${h.notes  ? `<div class="text-muted small mt-1" style="font-size:0.72rem;"><i class="fas fa-sticky-note me-1"></i>${_esc(h.notes)}</div>` : ''}
                    <div class="tl-meta text-muted mt-2" style="font-size:0.72rem;">${dt.time} · ${by}</div>
                </div>
            </div>`;
    });
    html += '</div>';
    el.innerHTML = html;
}

async function sstSubmitOcChange(e) {
    e.preventDefault();
    const btn            = document.getElementById('ocSubmitBtn');
    const studentId      = parseInt(document.getElementById('ocStudentId').value);
    const newStatus      = document.getElementById('ocNewStatus').value;
    const reasonCategory = document.getElementById('ocReasonCategory').value;
    const refNo          = document.getElementById('ocRefNo').value.trim();
    const effectiveTerm  = document.getElementById('ocEffectiveTerm').value.trim();
    const reason         = document.getElementById('ocReason').value.trim();

    if (!newStatus || !reasonCategory || !reason) {
        sstAlert('danger', 'Please select a status, category, and provide a reason.');
        return false;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving…';

    try {
        const res = await fetch(SST_API + '?action=change_status', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF },
            body: JSON.stringify({
                student_id: studentId,
                new_status: newStatus,
                reason_category: reasonCategory,
                ref_no: refNo,
                effective_term: effectiveTerm,
                reason,
            }),
        });
        const data = await res.json();

        if (!data.success) {
            sstAlert('danger', data.error || 'Failed to update status.');
        } else {
            bootstrap.Offcanvas.getInstance(document.getElementById('sstOffcanvas'))?.hide();
            sstAlert('success', data.message || 'Status updated successfully.');
            sstRefreshAll();
        }
    } catch (err) {
        sstAlert('danger', 'Error: ' + err.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i> Update Standing';
    }
    return false;
}

// ── 6. Export Attrition & Transition Ledger (CSV) ─────────────────────────
async function sstExportLedger() {
    try {
        const res  = await fetch(SST_API + '?action=recent_changes&limit=500');
        const data = await res.json();
        if (!data.success || !data.data || data.data.length === 0) {
            sstAlert('warning', 'No transition ledger records available to export.');
            return;
        }

        const rows = data.data;
        const headers = ['Timestamp', 'Effective Term', 'Student ID', 'Student Name', 'Program', 'Section', 'Previous Status', 'New Status', 'Statutory Category', 'Reference No', 'Reason', 'Authorized Officer'];
        
        let csvContent = 'data:text/csv;charset=utf-8,' + headers.join(',') + '\n';
        rows.forEach(r => {
            const row = [
                `"${r.changed_at || ''}"`,
                `"${r.effective_term || ''}"`,
                `"${r.student_number || ''}"`,
                `"${(r.last_name || '') + ', ' + (r.first_name || '')}"`,
                `"${r.program_course || ''}"`,
                `"${r.year_section || ''}"`,
                `"${r.changed_from || ''}"`,
                `"${r.changed_to || ''}"`,
                `"${r.reason_category || ''}"`,
                `"${r.ref_no || ''}"`,
                `"${(r.reason || '').replace(/"/g, '""')}"`,
                `"${r.changed_by_name || 'Staff'}"`,
            ];
            csvContent += row.join(',') + '\n';
        });

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement('a');
        link.setAttribute('href', encodedUri);
        link.setAttribute('download', `Registrar_Status_Transition_Ledger_${new Date().toISOString().slice(0, 10)}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        sstAlert('success', 'Status Transition Ledger exported successfully.');
    } catch (e) {
        sstAlert('danger', 'Failed to export CSV: ' + e.message);
    }
}

// ── Global Helper Utilities ────────────────────────────────────────────────
function _renderStatusBadge(status) {
    if (!status) return '<span class="sst-status-badge Inactive">Inactive</span>';
    const cls = status.replace(/\s+/g, '-');
    return `<span class="sst-status-badge ${cls}">${_esc(status)}</span>`;
}

function _formatDateTime(dtStr) {
    if (!dtStr) return { date: '—', time: '' };
    const d = new Date(dtStr);
    return {
        date: d.toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' }),
        time: d.toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit' }),
    };
}

function _esc(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function sstAlert(type, msg) {
    const box = document.getElementById('sstAlertBox');
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
