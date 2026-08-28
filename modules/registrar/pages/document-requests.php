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
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/modules/registrar/assets/css/registrar.css">

<?php renderBreadcrumbs($breadcrumbs); ?>

<div class="container-fluid py-4">
<div class="mpl" data-mpl>

    <!-- Page Header -->
    <div class="mpl-top">
        <div>
            <p>Process and track student document requests — Form 137, Good Moral, TOR, Certificate of Enrollment, and more</p>
        </div>
        <div class="mpl-toolbar">
            <a class="mpl-add" href="javascript:void(0)" onclick="openCreateRequestModal()">
                <i class="fas fa-plus" aria-hidden="true"></i> New Request
            </a>
            <a class="mpl-btn mpl-btn-ghost" href="<?php echo BASE_URL; ?>/modules/registrar/pages/student-information-system.php">
                <i class="fas fa-users" aria-hidden="true"></i> Student Records
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
                            <div class="mpl-person">
                                <span class="mpl-avatar"><?php echo strtoupper(substr($req['first_name'], 0, 1) . substr($req['last_name'], 0, 1)); ?></span>
                                <div>
                                    <strong><?php echo htmlspecialchars($req['last_name'] . ', ' . $req['first_name']); ?></strong>
                                    <small><?php echo htmlspecialchars($req['student_number']); ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <small><?php echo htmlspecialchars($req['doc_types'] ?? 'N/A'); ?></small>
                            <?php if ((int)$req['item_count'] > 1): ?>
                            <span class="badge bg-secondary ms-1"><?php echo (int)$req['item_count']; ?> item(s)</span>
                            <?php endif; ?>
                        </td>
                        <td><small><?php echo htmlspecialchars(substr($req['purpose'] ?? 'Not specified', 0, 50)); ?></small></td>
                        <td><?php echo getChannelBadge($req['channel']); ?></td>
                        <td><small><?php echo $timeAgo; ?></small></td>
                        <td><?php echo getStatusBadge($req['status']); ?></td>
                        <td>
                            <div class="mpl-actions">
                                <a href="javascript:void(0)" onclick="viewRequest(<?php echo (int)$req['id']; ?>)" title="View Details" aria-label="View"><i class="fas fa-eye"></i></a>
                                <a href="javascript:void(0)" onclick="updateStatus(<?php echo (int)$req['id']; ?>, '<?php echo htmlspecialchars($req['status']); ?>')" title="Update Status" aria-label="Update"><i class="fas fa-edit"></i></a>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="reg-modal-header">
                <h5 class="modal-title">Create Document Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createRequestForm" onsubmit="return handleCreateRequest(event)">
                <div class="modal-body">
                    <div class="reg-form-section">
                        <div class="reg-form-group">
                            <label>Student *</label>
                            <input type="text"
                                   id="studentSearch"
                                   class="form-control"
                                   placeholder="Search by student number or name..."
                                   autocomplete="off"
                                   required>
                            <input type="hidden" name="student_id" id="selectedStudentId">
                            <div id="studentSearchResults" class="list-group mt-2" style="display:none;max-height:200px;overflow-y:auto;"></div>
                        </div>

                        <div class="reg-form-group">
                            <label>Document Type(s) *</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="doc_types[]" value="Form 137" id="docForm137">
                                        <label class="form-check-label" for="docForm137">Form 137 (Permanent Record)</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="doc_types[]" value="Good Moral" id="docGoodMoral">
                                        <label class="form-check-label" for="docGoodMoral">Good Moral Certificate</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="doc_types[]" value="TOR" id="docTOR">
                                        <label class="form-check-label" for="docTOR">Transcript of Records (TOR)</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="doc_types[]" value="COE" id="docCOE">
                                        <label class="form-check-label" for="docCOE">Certificate of Enrollment</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="doc_types[]" value="COG" id="docCOG">
                                        <label class="form-check-label" for="docCOG">Certificate of Grades</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="doc_types[]" value="Diploma" id="docDiploma">
                                        <label class="form-check-label" for="docDiploma">Diploma Copy</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="doc_types[]" value="Honorable Dismissal" id="docHonorableDismissal">
                                        <label class="form-check-label" for="docHonorableDismissal">Honorable Dismissal</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Channel *</label>
                                    <select name="channel" class="form-select" required>
                                        <option value="walk-in">Walk-in</option>
                                        <option value="online">Online</option>
                                        <option value="email">Email</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Payment Status</label>
                                    <select name="paid" class="form-select">
                                        <option value="0">Unpaid</option>
                                        <option value="1">Paid</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="reg-form-group">
                            <label>Purpose *</label>
                            <textarea name="purpose" class="form-control" rows="2" placeholder="e.g., For employment, Transfer to another school..." required></textarea>
                        </div>

                        <div class="reg-form-group">
                            <label>Payment Reference (Optional)</label>
                            <input type="text" name="payment_ref" class="form-control" placeholder="e.g., OR #12345, Transaction ID">
                        </div>

                        <div class="reg-form-group">
                            <label>Student Email (for notification)</label>
                            <input type="email" name="student_email" class="form-control" placeholder="student@example.com">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-reg-primary">Create Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Request Modal -->
<div class="modal fade" id="viewRequestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="reg-modal-header">
                <h5 class="modal-title">Request Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="requestDetailsBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="reg-modal-header">
                <h5 class="modal-title">Update Request Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="updateStatusForm" onsubmit="return handleUpdateStatus(event)">
                <div class="modal-body">
                    <input type="hidden" name="request_id" id="updateStatusRequestId">
                    <div class="reg-form-group">
                        <label>New Status *</label>
                        <select name="status" class="form-select" id="updateStatusSelect" required>
                            <option value="Submitted">Submitted</option>
                            <option value="For Review">For Review</option>
                            <option value="Processing">Processing</option>
                            <option value="For Release">For Release</option>
                            <option value="Released">Released</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i>
                        Changing status will be logged in the request history.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-reg-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>/modules/registrar/assets/js/registrar.js"></script>
<script>
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

// View request details
async function viewRequest(requestId) {
    showRegModal('viewRequestModal');

    try {
        const response = await fetch(API_BASE + '/documents.php?action=get&id=' + requestId);
        const result = await response.json();

        if (result.success) {
            const req = result.data;
            const items = result.items || [];

            document.getElementById('requestDetailsBody').innerHTML = `
                <div class="reg-form-section">
                    <h6 class="border-bottom pb-2 mb-3">Request Information</h6>
                    <div class="row mb-2">
                        <div class="col-4"><strong>Request No:</strong></div>
                        <div class="col-8">${req.request_no}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><strong>Student:</strong></div>
                        <div class="col-8">${req.student_number} - ${req.last_name}, ${req.first_name}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><strong>Status:</strong></div>
                        <div class="col-8">${getStatusBadgeHTML(req.status)}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><strong>Channel:</strong></div>
                        <div class="col-8">${req.channel}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><strong>Purpose:</strong></div>
                        <div class="col-8">${req.purpose || 'Not specified'}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><strong>Payment:</strong></div>
                        <div class="col-8">${req.paid == 1 ? '<span class="badge bg-success">Paid</span>' : '<span class="badge bg-warning">Unpaid</span>'} ${req.payment_ref ? '(Ref: ' + req.payment_ref + ')' : ''}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><strong>Requested:</strong></div>
                        <div class="col-8">${new Date(req.created_at).toLocaleString()}</div>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3 mt-4">Document Items (${items.length})</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Document Type</th>
                                    <th>Copies</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${items.map(item => `
                                    <tr>
                                        <td>${item.doc_type}</td>
                                        <td>${item.copies}</td>
                                        <td>${getStatusBadgeHTML(item.status)}</td>
                                        <td>
                                            ${item.status === 'Pending' ? `<button class="btn btn-sm btn-primary" onclick="generateDocument(${item.id}, '${item.doc_type}')">Generate</button>` : ''}
                                            ${item.generated_file_id ? `<button class="btn btn-sm btn-success" onclick="downloadDocument(${item.generated_file_id})"><i class="fas fa-download"></i> Download</button>` : ''}
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
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
        alert('File not found');
        return;
    }

    // Create download URL
    const downloadUrl = `<?= BASE_URL ?>/modules/registrar/api/download.php?file_id=${fileId}`;

    // Open in new tab - user can use browser's Print to PDF
    const newWindow = window.open(downloadUrl, '_blank');

    // Show instruction
    setTimeout(() => {
        if (newWindow) {
            alert('Document opened in new tab.\n\nTo save as PDF:\n1. Press Ctrl+P (or Cmd+P on Mac)\n2. Select "Save as PDF" as destination\n3. Click Save');
        }
    }, 500);
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
    if (!confirm(`Generate ${docType} document? This will create a signed PDF.`)) return;

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
            setTimeout(() => window.location.reload(), 800);
        } else {
            showRegError(result.error || 'Failed to generate document');
            console.error('Generate API error:', result);
        }
    } catch (error) {
        console.error('Generate document error:', error);
        showRegError('Error: ' + error.message);
    }
}
</script>

<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
