<?php
/**
 * SMS 2 - Document Requests
 * Module: Registrar
 * Phase 7: Full request workflow implementation
 * Documents: Form 137, Good Moral Certificate, TOR, COE, COG, Diploma, Honorable Dismissal
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/registrar-service.php';

regRequireAction('registrar.view');

$pageTitle    = 'Document Requests';
$activeModule = 'registrar';
$activePage   = 'document-requests';

$db = db();

// Get filter parameters
$statusFilter = isset($_GET['status']) ? trim($_GET['status']) : 'all';
$channelFilter = isset($_GET['channel']) ? trim($_GET['channel']) : 'all';

// Build query based on filters
$whereConditions = [];
$params = [];

if ($statusFilter !== 'all') {
    $whereConditions[] = "dr.status = ?";
    $params[] = $statusFilter;
}

if ($channelFilter !== 'all') {
    $whereConditions[] = "dr.channel = ?";
    $params[] = $channelFilter;
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Get all requests with student info
$query = "
    SELECT
        dr.*,
        s.student_number,
        s.first_name,
        s.last_name,
        s.program_course,
        COUNT(dri.id) as item_count,
        GROUP_CONCAT(dri.doc_type SEPARATOR ', ') as doc_types
    FROM reg_doc_requests dr
    INNER JOIN reg_students s ON s.id = dr.student_id
    LEFT JOIN reg_doc_request_items dri ON dri.request_id = dr.id
    $whereClause
    GROUP BY dr.id
    ORDER BY dr.created_at DESC
    LIMIT 100
";

$stmt = $db->prepare($query);
$stmt->execute($params);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$stats = [
    'submitted' => (int)$db->query("SELECT COUNT(*) FROM reg_doc_requests WHERE status = 'Submitted'")->fetchColumn(),
    'for_review' => (int)$db->query("SELECT COUNT(*) FROM reg_doc_requests WHERE status = 'For Review'")->fetchColumn(),
    'processing' => (int)$db->query("SELECT COUNT(*) FROM reg_doc_requests WHERE status = 'Processing'")->fetchColumn(),
    'for_release' => (int)$db->query("SELECT COUNT(*) FROM reg_doc_requests WHERE status = 'For Release'")->fetchColumn(),
    'released_today' => (int)$db->query("SELECT COUNT(*) FROM reg_doc_requests WHERE status = 'Released' AND DATE(updated_at) = CURDATE()")->fetchColumn(),
    'total' => (int)$db->query("SELECT COUNT(*) FROM reg_doc_requests")->fetchColumn(),
];

$breadcrumbs = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Document Requests', 'url' => null],
];

// Helper function for status badges
function getStatusBadge(string $status): string {
    $badges = [
        'Submitted' => 'bg-info',
        'For Review' => 'bg-warning',
        'Processing' => 'bg-primary',
        'For Release' => 'bg-success',
        'Released' => 'bg-secondary',
        'Cancelled' => 'bg-danger',
    ];
    $class = $badges[$status] ?? 'bg-secondary';
    return "<span class='badge $class'>$status</span>";
}

// Helper function for channel badges
function getChannelBadge(string $channel): string {
    $icons = [
        'walk-in' => '<i class="fas fa-store me-1"></i>Walk-in',
        'online' => '<i class="fas fa-globe me-1"></i>Online',
        'email' => '<i class="fas fa-envelope me-1"></i>Email',
    ];
    return '<span class="text-muted small">' . ($icons[$channel] ?? $channel) . '</span>';
}

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<link href="<?php echo BASE_URL; ?>/assets/css/module-process-list.css?v=2" rel="stylesheet">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/modules/registrar/assets/css/registrar.css?v=<?php echo time(); ?>">

<style>
/* ==========================================================================
   DOCUMENT REQUESTS WORKFLOW & VIEW MODAL (LIGHT & DARK MODE)
   ========================================================================== */
.dr-stepper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 0.85rem 1.15rem;
    margin-bottom: 1.1rem;
    gap: 0.5rem;
    overflow-x: auto;
}
.dr-step {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 0.4rem 0.75rem;
    border-radius: 8px;
    font-size: 0.78rem;
    font-weight: 700;
    color: #64748b;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.15s ease;
    border: 1px solid transparent;
    background: transparent;
}
.dr-step:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.dr-step.completed {
    color: #15803d;
    background: #dcfce7;
    border-color: #bbf7d0;
}
.dr-step.active {
    color: #ffffff;
    background: #2563eb;
    border-color: #2563eb;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
}
.dr-step-arrow {
    color: #cbd5e1;
    font-size: 0.72rem;
}

.dr-modal-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.15rem 1.25rem;
    margin-bottom: 1rem;
}
.dr-payment-bar {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 11px;
    padding: 0.85rem 1.1rem;
    margin-bottom: 1.1rem;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
}
.dr-items-wrap {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    background: #ffffff;
}
.dr-items-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.83rem;
}
.dr-items-table th {
    background: #f8fafc;
    color: #64748b;
    font-size: 0.72rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #e2e8f0;
}
.dr-items-table td {
    padding: 0.85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    color: #0f172a;
    vertical-align: middle;
}
.dr-items-table tr:last-child td {
    border-bottom: none;
}

/* Dark Mode Adaptations */
[data-theme="dark"] #viewRequestModal .modal-content,
[data-theme="dark"] #createRequestModal .modal-content,
[data-theme="dark"] #updateStatusModal .modal-content,
[data-theme="dark"] #releaseItemModal .modal-content {
    background: #1e293b !important;
    border: 1px solid #334155 !important;
    color: #f8fafc !important;
}
[data-theme="dark"] #createRequestModal .modal-header,
[data-theme="dark"] #createRequestModal .modal-footer,
[data-theme="dark"] #createRequestModal .card,
[data-theme="dark"] #updateStatusModal .modal-header,
[data-theme="dark"] #releaseItemModal .modal-header {
    background: #0f172a !important;
    color: #f8fafc !important;
}
[data-theme="dark"] #createRequestModal .text-dark,
[data-theme="dark"] #updateStatusModal .text-dark,
[data-theme="dark"] #viewRequestModal .text-dark {
    color: #f8fafc !important;
}
[data-theme="dark"] .dr-stepper,
[data-theme="dark"] .dr-payment-bar {
    background: #0f172a;
    border-color: #334155;
}
[data-theme="dark"] .dr-step {
    color: #94a3b8;
}
[data-theme="dark"] .dr-step:hover {
    background: #1e293b;
    color: #f8fafc;
}
[data-theme="dark"] .dr-step.completed {
    background: rgba(22, 163, 74, 0.2);
    border-color: rgba(74, 222, 128, 0.35);
    color: #4ade80;
}
[data-theme="dark"] .dr-step.active {
    background: #2563eb;
    border-color: #3b82f6;
    color: #ffffff;
}
[data-theme="dark"] .dr-modal-card,
[data-theme="dark"] .dr-items-wrap {
    background: #0f172a;
    border-color: #334155;
}
[data-theme="dark"] .dr-items-table th {
    background: #1e293b;
    color: #94a3b8;
    border-bottom-color: #334155;
}
[data-theme="dark"] .dr-items-table td {
    border-bottom-color: #1e293b;
    color: #e2e8f0;
}
</style>

<?php renderBreadcrumbs($breadcrumbs); ?>

<div class="container-fluid py-4">
<div class="mpl" data-mpl>

    <!-- Page Header -->
    <div class="mpl-top d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h1 class="h4 mb-1 fw-bold">
                <i class="fas fa-file-signature text-primary me-2"></i>Document Requests &amp; Fulfillment Pipeline
            </h1>
            <p class="text-muted small mb-0">
                End-to-end processing, digital PDF generation, payment verification, and claiming release for official student credentials.
            </p>
        </div>
        <div class="mpl-toolbar d-flex align-items-center gap-2">
            <button type="button" class="btn btn-primary btn-sm" onclick="openCreateRequestModal()">
                <i class="fas fa-plus-circle me-1"></i> New Document Request
            </button>
            <a class="btn btn-outline-secondary btn-sm" href="<?php echo BASE_URL; ?>/modules/registrar/pages/student-information-system.php">
                <i class="fas fa-users me-1" aria-hidden="true"></i> Student Records
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <section class="mpl-stats" aria-label="Document request summary">
        <article class="mpl-stat">
            <div class="mpl-stat-icon blue"><i class="fas fa-inbox"></i></div>
            <div>
                <span>Submitted</span>
                <strong><?php echo $stats['submitted']; ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon amber"><i class="fas fa-search"></i></div>
            <div>
                <span>For Review</span>
                <strong><?php echo $stats['for_review']; ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon purple"><i class="fas fa-cog"></i></div>
            <div>
                <span>Processing</span>
                <strong><?php echo $stats['processing']; ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon green"><i class="fas fa-box"></i></div>
            <div>
                <span>For Release</span>
                <strong><?php echo $stats['for_release']; ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div>
                <span>Released Today</span>
                <strong><?php echo $stats['released_today']; ?></strong>
            </div>
        </article>
    </section>

    <!-- Filters -->
    <div class="mpl-filters">
        <label class="mpl-search">
            <i class="fas fa-search"></i>
            <input type="search" id="requestSearch" placeholder="Search by request number, student name, or document type..." aria-label="Search requests">
        </label>
        <select id="statusFilter" aria-label="Filter by status" onchange="applyFilters()">
            <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>All Statuses</option>
            <option value="Submitted" <?php echo $statusFilter === 'Submitted' ? 'selected' : ''; ?>>Submitted</option>
            <option value="For Review" <?php echo $statusFilter === 'For Review' ? 'selected' : ''; ?>>For Review</option>
            <option value="Processing" <?php echo $statusFilter === 'Processing' ? 'selected' : ''; ?>>Processing</option>
            <option value="For Release" <?php echo $statusFilter === 'For Release' ? 'selected' : ''; ?>>For Release</option>
            <option value="Released" <?php echo $statusFilter === 'Released' ? 'selected' : ''; ?>>Released</option>
        </select>
        <select id="channelFilter" aria-label="Filter by channel" onchange="applyFilters()">
            <option value="all" <?php echo $channelFilter === 'all' ? 'selected' : ''; ?>>All Channels</option>
            <option value="walk-in" <?php echo $channelFilter === 'walk-in' ? 'selected' : ''; ?>>Walk-in</option>
            <option value="online" <?php echo $channelFilter === 'online' ? 'selected' : ''; ?>>Online</option>
            <option value="email" <?php echo $channelFilter === 'email' ? 'selected' : ''; ?>>Email</option>
        </select>
        <a class="mpl-refresh" href="?"><i class="fas fa-sync-alt" aria-hidden="true"></i> Refresh</a>
    </div>

    <!-- Requests Table -->
    <section class="mpl-panel">
        <div class="mpl-panel-head">
            <div>
                <h2>Document Requests Queue</h2>
                <p><?php echo count($requests); ?> request(s) in queue</p>
            </div>
        </div>

        <div class="mpl-table-wrap">
            <table class="mpl-table" id="requestsTable">
                <thead>
                    <tr>
                        <th>Request No.</th>
                        <th>Student</th>
                        <th>Document(s)</th>
                        <th>Purpose</th>
                        <th>Channel</th>
                        <th>Requested</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($requests)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center;color:var(--sms-text-muted);padding:1.5rem;">
                            <i class="fas fa-inbox"></i> No document requests found. Click "New Request" to create one.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($requests as $req):
                        $searchString = strtolower($req['request_no'] . ' ' . $req['student_number'] . ' ' . $req['first_name'] . ' ' . $req['last_name'] . ' ' . ($req['doc_types'] ?? ''));
                        $timeAgo = date('M d, Y g:i A', strtotime($req['created_at']));
                        $isPaid = (int)$req['paid'] === 1;
                    ?>
                    <tr class="request-row" data-search="<?php echo htmlspecialchars($searchString); ?>">
                        <td>
                            <strong><?php echo htmlspecialchars($req['request_no']); ?></strong>
                            <?php if ($isPaid): ?>
                            <span class="badge bg-success ms-1" title="Paid"><i class="fas fa-check"></i></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="mpl-person" onclick="viewStudent(<?php echo (int)$req['student_id']; ?>, 'docs')" style="cursor:pointer;" title="Click to open 360° Student Dossier">
                                <span class="mpl-avatar"><?php echo strtoupper(substr((string)$req['first_name'], 0, 1) . substr((string)$req['last_name'], 0, 1)); ?></span>
                                <div>
                                    <strong class="text-primary"><?php echo htmlspecialchars($req['last_name'] . ', ' . $req['first_name']); ?></strong>
                                    <small><?php echo htmlspecialchars((string)$req['student_number']); ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <small class="fw-semibold"><?php echo htmlspecialchars((string)($req['doc_types'] ?? 'N/A')); ?></small>
                            <?php if ((int)$req['item_count'] > 1): ?>
                            <span class="badge bg-secondary ms-1"><?php echo (int)$req['item_count']; ?> item(s)</span>
                            <?php endif; ?>
                        </td>
                        <td><small><?php echo htmlspecialchars(substr((string)($req['purpose'] ?? 'Not specified'), 0, 50)); ?></small></td>
                        <td><?php echo getChannelBadge((string)$req['channel']); ?></td>
                        <td><small><?php echo $timeAgo; ?></small></td>
                        <td><?php echo getStatusBadge((string)$req['status']); ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewRequest(<?php echo (int)$req['id']; ?>)" title="Review &amp; Process Request">
                                    <i class="fas fa-tasks me-1"></i> Review
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="updateStatus(<?php echo (int)$req['id']; ?>, '<?php echo htmlspecialchars((string)$req['status'], ENT_QUOTES); ?>')" title="Quick Status Change">
                                    <i class="fas fa-exchange-alt"></i>
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
            <span class="meta" id="requestMeta">Showing <?php echo count($requests); ?> of <?php echo $stats['total']; ?> requests</span>
        </div>
    </section>

</div>
</div>

<!-- Create Request Modal -->
<div class="modal fade" id="createRequestModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-3 pt-4 px-4 bg-light bg-gradient rounded-top-4">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-plus-circle text-primary me-2"></i>Create Document Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createRequestForm" onsubmit="return handleCreateRequest(event)">
                <div class="modal-body px-4 py-4">
                    
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">Student <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg shadow-sm rounded-3">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                            <input type="text"
                                   id="studentSearch"
                                   class="form-control border-start-0 ps-0"
                                   placeholder="Search by student number or name..."
                                   autocomplete="off"
                                   required>
                        </div>
                        <input type="hidden" name="student_id" id="selectedStudentId">
                        <div id="studentSearchResults" class="list-group mt-2 shadow-sm rounded-3 border-0" style="display:none; max-height:250px; overflow-y:auto;"></div>
                    </div>

                    <div class="card bg-light border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <label class="form-label text-muted small fw-bold text-uppercase tracking-wide mb-3">Document Type(s) <span class="text-danger">*</span></label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-check custom-checkbox mb-2">
                                        <input class="form-check-input" type="checkbox" name="doc_types[]" value="Form 137" id="docForm137">
                                        <label class="form-check-label fw-medium" for="docForm137">Form 137 (Permanent Record)</label>
                                    </div>
                                    <div class="form-check custom-checkbox mb-2">
                                        <input class="form-check-input" type="checkbox" name="doc_types[]" value="Good Moral" id="docGoodMoral">
                                        <label class="form-check-label fw-medium" for="docGoodMoral">Good Moral Certificate</label>
                                    </div>
                                    <div class="form-check custom-checkbox mb-2">
                                        <input class="form-check-input" type="checkbox" name="doc_types[]" value="TOR" id="docTOR">
                                        <label class="form-check-label fw-medium" for="docTOR">Transcript of Records (TOR)</label>
                                    </div>
                                    <div class="form-check custom-checkbox mb-2">
                                        <input class="form-check-input" type="checkbox" name="doc_types[]" value="COE" id="docCOE">
                                        <label class="form-check-label fw-medium" for="docCOE">Certificate of Enrollment</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check custom-checkbox mb-2">
                                        <input class="form-check-input" type="checkbox" name="doc_types[]" value="COG" id="docCOG">
                                        <label class="form-check-label fw-medium" for="docCOG">Certificate of Grades</label>
                                    </div>
                                    <div class="form-check custom-checkbox mb-2">
                                        <input class="form-check-input" type="checkbox" name="doc_types[]" value="Diploma" id="docDiploma">
                                        <label class="form-check-label fw-medium" for="docDiploma">Diploma Copy</label>
                                    </div>
                                    <div class="form-check custom-checkbox mb-2">
                                        <input class="form-check-input" type="checkbox" name="doc_types[]" value="Honorable Dismissal" id="docHonorableDismissal">
                                        <label class="form-check-label fw-medium" for="docHonorableDismissal">Honorable Dismissal</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">Channel <span class="text-danger">*</span></label>
                            <select name="channel" class="form-select form-select-lg shadow-sm border-light" required>
                                <option value="walk-in">Walk-in</option>
                                <option value="online">Online</option>
                                <option value="email">Email</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">Payment Status</label>
                            <select name="paid" class="form-select form-select-lg shadow-sm border-light">
                                <option value="0">Unpaid</option>
                                <option value="1">Paid</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">Purpose <span class="text-danger">*</span></label>
                        <textarea name="purpose" class="form-control shadow-sm border-light" rows="2" placeholder="e.g., For employment, Transfer to another school..." required></textarea>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">Payment Reference <span class="text-muted fw-normal text-capitalize">(Optional)</span></label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white text-muted border-light"><i class="fas fa-receipt"></i></span>
                                <input type="text" name="payment_ref" class="form-control border-light" placeholder="e.g., OR #12345">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">Student Email <span class="text-muted fw-normal text-capitalize">(For notification)</span></label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white text-muted border-light"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="student_email" class="form-control border-light" placeholder="student@example.com">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 bg-white rounded-bottom-4">
                    <button type="button" class="btn btn-light shadow-sm text-secondary fw-semibold px-4 rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary shadow-sm fw-semibold px-4 rounded-pill"><i class="fas fa-check me-2"></i>Create Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View & Process Request Command Modal -->
<div class="modal fade" id="viewRequestModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 py-3 px-4 text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-0">
                        <i class="fas fa-file-signature me-2"></i>Review &amp; Process Document Request
                    </h5>
                    <small class="text-white-50" id="viewRequestSubtitle">Verify student standing, generate signed PDFs, and manage release</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-light fw-bold" onclick="printRequestClaimStub()" title="Print Official Claim Stub">
                        <i class="fas fa-print text-primary me-1"></i> Claim Stub
                    </button>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-4" id="requestDetailsBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Release / Claim Document Item Modal -->
<div class="modal fade" id="releaseItemModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 py-3 px-4 text-white" style="background: linear-gradient(135deg, #0f766e, #14b8a6);">
                <h5 class="modal-title fw-bold mb-0"><i class="fas fa-hand-holding me-2"></i>Release Document &amp; Issue Claim Slip</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="releaseItemForm" onsubmit="return handleReleaseItem(event)">
                <div class="modal-body p-4">
                    <input type="hidden" id="releaseItemId" value="">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-muted mb-1">Document Being Released</label>
                        <div class="fw-bold fs-6 text-primary" id="releaseDocTitle">—</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold mb-1">Claimant Full Name <span class="text-danger">*</span></label>
                        <input type="text" id="releaseClaimantName" class="form-control" required placeholder="Student or authorized representative name">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Presented Valid ID / Student ID No.</label>
                        <input type="text" id="releaseClaimantId" class="form-control" placeholder="e.g. School ID, National ID, Authorization Letter">
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light border btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-bold" id="releaseSubmitBtn">
                        <i class="fas fa-check-circle me-1"></i> Confirm Release
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-edit text-primary me-2"></i>Update Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateStatusForm" onsubmit="return handleUpdateStatus(event)">
                <div class="modal-body px-4 py-4">
                    <input type="hidden" name="request_id" id="updateStatusRequestId">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">New Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select form-select-lg shadow-sm border-light" id="updateStatusSelect" required style="cursor: pointer;">
                            <option value="Submitted">Submitted</option>
                            <option value="For Review">For Review</option>
                            <option value="Processing">Processing</option>
                            <option value="For Release">For Release</option>
                            <option value="Released">Released</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-0 mt-4 rounded-3" style="background-color: #f0f9ff; color: #0284c7;">
                        <i class="fas fa-info-circle fs-4 me-3"></i>
                        <span class="small fw-medium">Changing status will be logged in the request history.</span>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light shadow-sm text-secondary fw-semibold px-4 rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary shadow-sm fw-semibold px-4 rounded-pill">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Document Preview Modal -->
<div class="modal fade" id="documentPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="reg-modal-header">
                <h5 class="modal-title">Document Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="documentPreviewBody" style="height: 80vh;">
                <!-- Preview content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>/modules/registrar/assets/js/registrar.js"></script>
<script>
// Safely configure PDF.js worker only if PDF.js is loaded
if (typeof pdfjsLib !== 'undefined' && pdfjsLib.GlobalWorkerOptions) {
    pdfjsLib.GlobalWorkerOptions.workerSrc = '<?php echo BASE_URL; ?>/assets/js/vendor/pdf.worker.min.js';
}

const API_BASE = '<?php echo BASE_URL; ?>/modules/registrar/api';
const CSRF = '<?= e(csrfToken()) ?>';

// Filter functionality
function applyFilters() {
    const status = document.getElementById('statusFilter').value;
    const channel = document.getElementById('channelFilter').value;
    let url = '?';
    if (status !== 'all') url += 'status=' + status + '&';
    if (channel !== 'all') url += 'channel=' + channel;
    window.location.href = url;
}

// Search functionality
const requestSearch = document.getElementById('requestSearch');
if (requestSearch) {
    requestSearch.addEventListener('input', debounce(function() {
        const query = requestSearch.value.trim().toLowerCase();
        const rows = document.querySelectorAll('.request-row');
        let visible = 0;

        rows.forEach(row => {
            const searchData = row.getAttribute('data-search') || '';
            const matches = searchData.includes(query);
            row.style.display = matches ? '' : 'none';
            if (matches) visible++;
        });

        document.getElementById('requestMeta').textContent =
            `Showing ${visible} of ${rows.length} requests`;
    }, 200));
}

// Open create request modal
function openCreateRequestModal() {
    document.getElementById('createRequestForm').reset();
    document.getElementById('selectedStudentId').value = '';
    document.getElementById('studentSearchResults').style.display = 'none';
    showRegModal('createRequestModal');
}

// Student search autocomplete
let studentSearchTimeout;
const studentSearchInput = document.getElementById('studentSearch');
if (studentSearchInput) {
    studentSearchInput.addEventListener('input', function() {
        clearTimeout(studentSearchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            document.getElementById('studentSearchResults').style.display = 'none';
            return;
        }

        studentSearchTimeout = setTimeout(() => {
            fetch(API_BASE + '/students.php?action=search&q=' + encodeURIComponent(query))
                .then(r => r.json())
                .then(data => {
                    const results = document.getElementById('studentSearchResults');
                    if (data.success && data.data.length > 0) {
                        results.innerHTML = data.data.map(s => `
                            <a href="javascript:void(0)"
                               class="list-group-item list-group-item-action"
                               onclick="selectStudent(${s.id}, '${s.student_number}', '${s.first_name}', '${s.last_name}')">
                                <strong>${s.student_number}</strong> - ${s.last_name}, ${s.first_name}
                                ${s.program_course ? '<br><small class="text-muted">' + s.program_course + '</small>' : ''}
                            </a>
                        `).join('');
                        results.style.display = 'block';
                    } else {
                        results.innerHTML = '<div class="list-group-item">No students found</div>';
                        results.style.display = 'block';
                    }
                })
                .catch(err => console.error('Student search error:', err));
        }, 300);
    });
}

function selectStudent(id, number, firstName, lastName) {
    document.getElementById('selectedStudentId').value = id;
    document.getElementById('studentSearch').value = `${number} - ${lastName}, ${firstName}`;
    document.getElementById('studentSearchResults').style.display = 'none';
}

// Create request
async function handleCreateRequest(e) {
    e.preventDefault();

    const form = document.getElementById('createRequestForm');
    const formData = new FormData(form);

    // Validate student selected
    if (!formData.get('student_id')) {
        showRegError('Please select a student');
        return false;
    }

    // Get checked document types
    const docTypes = [];
    form.querySelectorAll('input[name="doc_types[]"]:checked').forEach(cb => {
        docTypes.push(cb.value);
    });

    if (docTypes.length === 0) {
        showRegError('Please select at least one document type');
        return false;
    }

    const data = {
        csrf_token: CSRF,
        student_id: parseInt(formData.get('student_id')),
        doc_types: docTypes,
        purpose: formData.get('purpose'),
        channel: formData.get('channel'),
        paid: parseInt(formData.get('paid')) || 0,
        payment_ref: formData.get('payment_ref') || null,
        student_email: formData.get('student_email') || null,
    };

    try {
        const response = await fetch(API_BASE + '/documents.php?action=create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': CSRF
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showRegSuccess(`Request ${result.request_no} created successfully`);
            hideRegModal('createRequestModal');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showRegError(result.error || 'Failed to create request');
            console.error('Create request API error:', result);
        }
    } catch (error) {
        console.error('Create request error:', error);
        showRegError('Error: ' + error.message);
    }

    return false;
}

let currentViewedRequest = null;
let currentViewedItems = [];

function drEsc(str) {
    if (str === null || str === undefined) return '';
    const d = document.createElement('div');
    d.textContent = String(str);
    return d.innerHTML;
}

// View & Process request details
async function viewRequest(requestId) {
    const modalEl = document.getElementById('viewRequestModal');
    if (modalEl && !modalEl.classList.contains('show')) {
        showRegModal('viewRequestModal');
    }

    try {
        const response = await fetch(API_BASE + '/documents.php?action=get&id=' + requestId);
        const result = await response.json();

        if (result.success) {
            const req = result.data;
            const items = result.items || [];
            currentViewedRequest = req;
            currentViewedItems = items;

            const subEl = document.getElementById('viewRequestSubtitle');
            if (subEl) {
                subEl.textContent = `${req.request_no} • ${req.last_name}, ${req.first_name} (${req.student_number})`;
            }

            // 5-Step Pipeline Stepper
            const steps = ['Submitted', 'For Review', 'Processing', 'For Release', 'Released'];
            const currentIdx = steps.indexOf(req.status);
            const nextStatus = (currentIdx >= 0 && currentIdx < steps.length - 1) ? steps[currentIdx + 1] : null;

            const stepperHtml = steps.map((st, idx) => {
                let cls = '';
                let icon = `<span class="badge rounded-pill bg-secondary" style="font-size:0.68rem;">${idx + 1}</span>`;
                if (req.status === st) {
                    cls = 'active';
                    icon = `<i class="fas fa-dot-circle"></i>`;
                } else if (currentIdx > idx) {
                    cls = 'completed';
                    icon = `<i class="fas fa-check-circle"></i>`;
                }
                const arrow = idx < steps.length - 1 ? `<i class="fas fa-chevron-right dr-step-arrow"></i>` : '';
                return `
                    <button type="button" class="dr-step ${cls}" onclick="quickSetRequestStatus(${req.id}, '${st}')" title="Click to set status to ${st}">
                        ${icon} <span>${st}</span>
                    </button>
                    ${arrow}
                `;
            }).join('');

            const studentFullName = `${req.last_name}, ${req.first_name}`;

            document.getElementById('requestDetailsBody').innerHTML = `
                <!-- 1. Interactive 5-Step Workflow Pipeline -->
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                    <span class="small fw-bold text-uppercase text-muted"><i class="fas fa-project-diagram text-primary me-1"></i> Workflow Stage (Click any stage to transition)</span>
                    <div class="d-flex gap-2">
                        ${nextStatus ? `
                            <button type="button" class="btn btn-sm btn-primary fw-bold" onclick="quickSetRequestStatus(${req.id}, '${nextStatus}')">
                                <i class="fas fa-arrow-right me-1"></i> Advance to "${nextStatus}"
                            </button>
                        ` : ''}
                        ${req.status !== 'Cancelled' && req.status !== 'Released' ? `
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="quickSetRequestStatus(${req.id}, 'Cancelled')">
                                <i class="fas fa-ban me-1"></i> Cancel Request
                            </button>
                        ` : ''}
                    </div>
                </div>
                <div class="dr-stepper">
                    ${stepperHtml}
                </div>

                <!-- 2. Student Verification & Request Metadata Card -->
                <div class="dr-modal-card">
                    <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-2 mb-3 gap-2">
                        <div class="fw-bold text-uppercase small text-muted">
                            <i class="fas fa-id-card text-primary me-1"></i> Student &amp; Request Information
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openDossierFromRequest(${req.student_id})">
                            <i class="fas fa-user-graduate me-1"></i> Check 360° Student Dossier &amp; Grades
                        </button>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="small text-muted text-uppercase fw-semibold" style="font-size:0.72rem;">Student</div>
                            <div class="fw-bold text-primary fs-6">${drEsc(studentFullName)}</div>
                            <div class="small text-muted">${drEsc(req.student_number)} &bull; ${drEsc(req.student_status || 'Active')}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="small text-muted text-uppercase fw-semibold" style="font-size:0.72rem;">Program &amp; Section</div>
                            <div class="fw-semibold">${drEsc(req.program_course || '—')}</div>
                            <div class="small text-muted">${drEsc(req.year_section || '—')}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="small text-muted text-uppercase fw-semibold" style="font-size:0.72rem;">Request Reference &amp; Channel</div>
                            <div class="fw-bold">${drEsc(req.request_no)} ${getStatusBadgeHTML(req.status)}</div>
                            <div class="small text-muted text-capitalize"><i class="fas fa-clock me-1"></i>${new Date(req.created_at).toLocaleString()} (${drEsc(req.channel)})</div>
                        </div>
                        <div class="col-12">
                            <div class="small text-muted text-uppercase fw-semibold mb-1" style="font-size:0.72rem;">Stated Purpose</div>
                            <div class="p-2 rounded border small fw-medium">${drEsc(req.purpose || 'Not specified')}</div>
                        </div>
                    </div>
                </div>

                <!-- 3. Inline Cashier Payment & Official Receipt (OR #) Verification Bar -->
                <div class="dr-payment-bar">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-receipt fa-lg ${req.paid == 1 ? 'text-success' : 'text-warning'}"></i>
                        <div>
                            <div class="fw-bold small">Cashier Payment Verification</div>
                            <div class="text-muted" style="font-size:0.74rem;">Verify Official Receipt (OR #) before generating or releasing official credentials.</div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <select id="inlinePaidSelect" class="form-select form-select-sm" style="width:130px;">
                            <option value="1" ${req.paid == 1 ? 'selected' : ''}>✓ Paid</option>
                            <option value="0" ${req.paid != 1 ? 'selected' : ''}>⏳ Unpaid</option>
                        </select>
                        <input type="text" id="inlinePaymentRef" class="form-control form-control-sm" style="width:180px;" placeholder="OR / Receipt #..." value="${drEsc(req.payment_ref || '')}">
                        <button type="button" class="btn btn-sm btn-outline-success fw-semibold" onclick="saveRequestPayment(${req.id})">
                            <i class="fas fa-save me-1"></i> Save Payment
                        </button>
                    </div>
                </div>

                <!-- 4. Document Items & Fulfillment Actions -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-bold text-uppercase text-muted">
                        <i class="fas fa-file-alt text-primary me-1"></i> Requested Document Items (${items.length})
                    </span>
                </div>
                <div class="dr-items-wrap">
                    <table class="dr-items-table">
                        <thead>
                            <tr>
                                <th>Document Type</th>
                                <th class="text-center">Copies</th>
                                <th>Item Status / Release Info</th>
                                <th class="text-end">Fulfillment Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${items.length === 0 ? `
                                <tr><td colspan="4" class="text-center text-muted py-4">No individual document items attached to this request.</td></tr>
                            ` : items.map(item => `
                                <tr>
                                    <td class="fw-bold">
                                        <i class="far fa-file-pdf text-danger me-2 fs-5 align-middle"></i>${drEsc(item.doc_type)}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill px-3">${parseInt(item.copies || 1, 10)}</span>
                                    </td>
                                    <td>
                                        <div>${getStatusBadgeHTML(item.status)}</div>
                                        ${item.release_slip_no ? `<div class="text-muted mt-1" style="font-size:0.72rem;"><i class="fas fa-check-double text-success me-1"></i>Slip: <strong>${drEsc(item.release_slip_no)}</strong> (${drEsc(item.claimant_name || '')})</div>` : ''}
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex flex-wrap gap-1 justify-content-end">
                                            <button type="button" class="btn btn-sm btn-outline-info" onclick="previewDocument(${item.id}, '${drEsc(item.doc_type)}')" title="Preview Document">
                                                <i class="fas fa-eye me-1"></i> Preview
                                            </button>
                                            ${item.status === 'Pending' ? `
                                                <button type="button" class="btn btn-sm btn-primary" onclick="generateDocument(${item.id}, '${drEsc(item.doc_type)}', ${req.id})" title="Generate &amp; Digitally Sign PDF">
                                                    <i class="fas fa-cogs me-1"></i> Generate PDF
                                                </button>
                                            ` : `
                                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="generateDocument(${item.id}, '${drEsc(item.doc_type)}', ${req.id})" title="Re-Generate Signed PDF">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                            `}
                                            ${item.generated_file_id ? `
                                                <button type="button" class="btn btn-sm btn-success" onclick="downloadDocument(${item.generated_file_id})" title="Open / Print Generated PDF">
                                                    <i class="fas fa-print me-1"></i> Open PDF
                                                </button>
                                            ` : ''}
                                            ${item.status === 'Generated' ? (
                                                (req.channel || '').toLowerCase() === 'email'
                                                ? `<button type="button" class="btn btn-sm btn-outline-primary" onclick="notifyStudent(${item.id}, 'email', ${req.id})"><i class="fas fa-paper-plane me-1"></i> Email</button>`
                                                : `<button type="button" class="btn btn-sm btn-outline-primary" onclick="notifyStudent(${item.id}, 'notify', ${req.id})"><i class="fas fa-bell me-1"></i> Notify</button>`
                                            ) : ''}
                                            ${item.status !== 'Released' ? `
                                                <button type="button" class="btn btn-sm btn-teal text-white" style="background:#0d9488;" onclick="openReleaseItemModal(${item.id}, '${drEsc(item.doc_type)}', '${drEsc(studentFullName)}')" title="Record Claimant &amp; Release Document">
                                                    <i class="fas fa-hand-holding me-1"></i> Release
                                                </button>
                                            ` : ''}
                                        </div>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        } else {
            document.getElementById('requestDetailsBody').innerHTML = `
                <div class="alert alert-danger">${result.error || 'Failed to load request details'}</div>
            `;
        }
    } catch (error) {
        console.error('View request error:', error);
        document.getElementById('requestDetailsBody').innerHTML = `
            <div class="alert alert-danger">Error loading request details</div>
        `;
    }
}

async function quickSetRequestStatus(requestId, newStatus) {
    try {
        const response = await fetch(API_BASE + '/documents.php?action=update_status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': CSRF
            },
            body: JSON.stringify({ request_id: requestId, status: newStatus })
        });
        const result = await response.json();
        if (result.success) {
            showRegSuccess(`Request status updated to "${newStatus}"`);
            await viewRequest(requestId);
        } else {
            showRegError(result.error || 'Failed to update status');
        }
    } catch (err) {
        showRegError('Error: ' + err.message);
    }
}

async function saveRequestPayment(requestId) {
    const paid = parseInt(document.getElementById('inlinePaidSelect')?.value || '0', 10);
    const paymentRef = (document.getElementById('inlinePaymentRef')?.value || '').trim();
    try {
        const response = await fetch(API_BASE + '/documents.php?action=update_payment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': CSRF
            },
            body: JSON.stringify({ request_id: requestId, paid, payment_ref: paymentRef })
        });
        const result = await response.json();
        if (result.success) {
            showRegSuccess('Payment status and OR reference saved');
            await viewRequest(requestId);
        } else {
            showRegError(result.error || 'Failed to update payment');
        }
    } catch (err) {
        showRegError('Error: ' + err.message);
    }
}

function openDossierFromRequest(studentId) {
    hideRegModal('viewRequestModal');
    setTimeout(() => {
        if (typeof viewStudent === 'function') {
            viewStudent(studentId, 'docs');
        }
    }, 250);
}

function openReleaseItemModal(itemId, docType, defaultClaimant) {
    document.getElementById('releaseItemId').value = itemId;
    document.getElementById('releaseDocTitle').textContent = docType;
    document.getElementById('releaseClaimantName').value = defaultClaimant || '';
    document.getElementById('releaseClaimantId').value = 'School ID / Valid ID';
    showRegModal('releaseItemModal');
}

async function handleReleaseItem(e) {
    e.preventDefault();
    const itemId = parseInt(document.getElementById('releaseItemId').value || '0', 10);
    const claimantName = document.getElementById('releaseClaimantName').value.trim();
    const claimantId = document.getElementById('releaseClaimantId').value.trim();
    if (!itemId || !claimantName) return false;

    try {
        const res = await fetch(API_BASE + '/documents.php?action=release', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': CSRF
            },
            body: JSON.stringify({ item_id: itemId, claimant_name: claimantName, claimant_id: claimantId })
        });
        const data = await res.json();
        if (data.success) {
            showRegSuccess(`Released! Claim Slip: ${data.release_slip_no}`);
            hideRegModal('releaseItemModal');
            if (currentViewedRequest) {
                await viewRequest(currentViewedRequest.id);
            }
        } else {
            showRegError(data.error || 'Failed to release document');
        }
    } catch (err) {
        showRegError('Error: ' + err.message);
    }
    return false;
}

function printRequestClaimStub() {
    if (!currentViewedRequest) return;
    const req = currentViewedRequest;
    const items = currentViewedItems || [];
    const win = window.open('', '_blank', 'width=720,height=560');
    if (!win) return;
    win.document.write(`<!DOCTYPE html>
<html>
<head>
    <title>Claim Stub - ${drEsc(req.request_no)}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; color: #0f172a; font-size: 12px; }
        .stub { border: 2px dashed #1e3a8a; border-radius: 10px; padding: 20px; }
        h2 { margin: 0 0 4px 0; color: #1e3a8a; font-size: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 10px; text-align: left; }
        th { background: #f8fafc; }
    </style>
</head>
<body>
    <div class="stub">
        <div style="display:flex;justify-content:space-between;border-bottom:2px solid #1e3a8a;padding-bottom:8px;">
            <div>
                <h2>REGISTRAR DOCUMENT CLAIM STUB</h2>
                <div>Office of the University Registrar</div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:15px;font-weight:bold;">${drEsc(req.request_no)}</div>
                <div>Status: <strong>${drEsc(req.status)}</strong></div>
            </div>
        </div>
        <p><strong>Student:</strong> ${drEsc(req.last_name)}, ${drEsc(req.first_name)} (${drEsc(req.student_number)})<br>
        <strong>Program:</strong> ${drEsc(req.program_course || '—')}<br>
        <strong>Payment:</strong> ${req.paid == 1 ? 'PAID' : 'UNPAID'} ${req.payment_ref ? '(OR #: ' + drEsc(req.payment_ref) + ')' : ''}</p>
        <table>
            <thead><tr><th>Requested Document</th><th>Copies</th><th>Status</th></tr></thead>
            <tbody>
                ${items.map(i => `<tr><td>${drEsc(i.doc_type)}</td><td>${i.copies || 1}</td><td>${drEsc(i.status)}</td></tr>`).join('')}
            </tbody>
        </table>
        <p style="margin-top:16px;font-size:11px;color:#475569;">Please present this claim stub and a valid ID at the Registrar Release Window.</p>
    </div>
    <script>window.onload = function() { window.print(); };<\/script>
</body>
</html>`);
    win.document.close();
}

function getStatusBadgeHTML(status) {
    const badges = {
        'Submitted': 'bg-info',
        'For Review': 'bg-warning',
        'Processing': 'bg-primary',
        'For Release': 'bg-success',
        'Released': 'bg-secondary',
        'Cancelled': 'bg-danger',
        'Pending': 'bg-warning',
        'Generated': 'bg-success',
    };
    const cls = badges[status] || 'bg-secondary';
    return `<span class="badge ${cls}">${status}</span>`;
}

// Download generated document
function downloadDocument(fileId) {
    if (!fileId) {
        showRegError('File not found');
        return;
    }
    const downloadUrl = `<?= BASE_URL ?>/modules/registrar/api/download.php?file_id=${fileId}`;
    window.open(downloadUrl, '_blank');
}

// Custom Confirmation Modal
function confirmAction(title, text) {
    return new Promise((resolve) => {
        const overlay = document.createElement('div');
        overlay.id = 'confirmActionOverlay';
        overlay.innerHTML = `
            <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.6); z-index: 100000; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
                <div class="bg-white rounded-4 shadow-lg p-4" style="max-width: 400px; width: 90%;">
                    <div class="text-center mb-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-question-circle fs-2"></i>
                        </div>
                    </div>
                    <h5 class="text-center fw-bold text-dark mb-2">${title}</h5>
                    <p class="text-center text-muted mb-4 fs-6">${text}</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button id="confirmCancelBtn" class="btn btn-light border text-secondary fw-semibold px-4 rounded-pill">Cancel</button>
                        <button id="confirmProceedBtn" class="btn btn-primary fw-semibold px-4 rounded-pill">Proceed</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);

        document.getElementById('confirmCancelBtn').addEventListener('click', () => {
            overlay.remove();
            resolve(false);
        });

        document.getElementById('confirmProceedBtn').addEventListener('click', () => {
            overlay.remove();
            resolve(true);
        });
    });
}

// Notify student (Email or Pickup)
async function notifyStudent(itemId, actionType) {
    const message = actionType === 'email' ? 'Send document as an email attachment to the student?' : 'Notify student that the document is ready for pickup?';
    const isConfirmed = await confirmAction('Confirm Notification', message);
    if (!isConfirmed) return;
    
    try {
        const response = await fetch(API_BASE + '/documents.php?action=notify_student', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': CSRF
            },
            body: JSON.stringify({ item_id: itemId, action_type: actionType })
        });
        
        const result = await response.json();
        if (result.success) {
            showRegSuccess('Notification sent successfully');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showRegError(result.error || 'Failed to send notification');
        }
    } catch (error) {
        console.error('Notify error:', error);
        showRegError('Error sending notification');
    }
}

// Preview document before generating
async function previewDocument(itemId, docType) {
    if (!itemId || !docType) return;
    
    // Show modal and loading state
    const previewBody = document.getElementById('documentPreviewBody');
    const iframeSrc = `${API_BASE}/documents.php?action=serve_preview&item_id=${itemId}&doc_type=${encodeURIComponent(docType)}`;
    
    // Use absolute positioning for the loader so it sits behind the iframe.
    // The iframe background is transparent until the PDF loads, revealing the loader.
    // Once the PDF loads, its built-in viewer will cover the loader.
    previewBody.innerHTML = `
        <div class="position-relative w-100" style="height: 75vh;">
            <div class="position-absolute top-50 start-50 translate-middle d-flex flex-column align-items-center justify-content-center" style="z-index: 1;">
                <div class="spinner-border text-primary mb-2" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="text-muted fw-bold">Loading document...</div>
            </div>
            <iframe src="${iframeSrc}" class="position-relative" style="width: 100%; height: 100%; border: none; z-index: 2; background: transparent;"></iframe>
        </div>
    `;
    
    showRegModal('documentPreviewModal');
}

// Update status
function updateStatus(requestId, currentStatus) {
    document.getElementById('updateStatusRequestId').value = requestId;
    document.getElementById('updateStatusSelect').value = currentStatus;
    showRegModal('updateStatusModal');
}

async function handleUpdateStatus(e) {
    e.preventDefault();

    const form = document.getElementById('updateStatusForm');
    const formData = new FormData(form);

    const data = {
        request_id: parseInt(formData.get('request_id')),
        status: formData.get('status'),
    };

    try {
        const response = await fetch(API_BASE + '/documents.php?action=update_status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': CSRF
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showRegSuccess('Status updated successfully');
            hideRegModal('updateStatusModal');
            setTimeout(() => window.location.reload(), 800);
        } else {
            showRegError(result.error || 'Failed to update status');
        }
    } catch (error) {
        console.error('Update status error:', error);
        showRegError('Error: ' + error.message);
    }

    return false;
}

// Generate document
async function generateDocument(itemId, docType) {
    const isConfirmed = await confirmAction('Generate Document', `Generate ${docType} document? This will create a signed PDF.`);
    if (!isConfirmed) return;

    // Create and show a beautiful full-screen loading overlay
    const overlay = document.createElement('div');
    overlay.id = 'generateDocOverlay';
    overlay.innerHTML = `
        <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.85); z-index: 99999; display: flex; flex-direction: column; align-items: center; justify-content: center; backdrop-filter: blur(8px); animation: fadeIn 0.3s ease;">
            <div class="spinner-border text-primary mb-4" role="status" style="width: 4.5rem; height: 4.5rem; border-width: 0.4rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h3 class="text-white fw-bolder tracking-wide text-uppercase" style="letter-spacing: 2px;">Generating Document</h3>
            <p class="text-white-50 fs-5 mt-2">Processing ${docType} — Please wait...</p>
        </div>
    `;
    document.body.appendChild(overlay);

    try {
        const response = await fetch(API_BASE + '/documents.php?action=generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': CSRF
            },
            body: JSON.stringify({
                csrf_token: CSRF,
                item_id: itemId,
                doc_type: docType
            })
        });

        const result = await response.json();

        if (result.success) {
            showRegSuccess(`${docType} generated successfully`);
            if (currentViewedRequest) {
                await viewRequest(currentViewedRequest.id);
            } else {
                setTimeout(() => window.location.reload(), 800);
            }
        } else {
            showRegError(result.error || 'Failed to generate document');
            console.error('Generate API error:', result);
        }
    } catch (error) {
        console.error('Generate document error:', error);
        showRegError('Error: ' + error.message);
    } finally {
        // Remove the overlay regardless of success or failure (unless reloading takes over)
        const activeOverlay = document.getElementById('generateDocOverlay');
        if (activeOverlay && document.body.contains(activeOverlay)) {
            activeOverlay.remove();
        }
    }
}
</script>

<?php require_once __DIR__ . '/../includes/student-dossier-drawer.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
