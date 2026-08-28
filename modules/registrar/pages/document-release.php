<?php
/**
 * SMS2 - Registrar: Document Release Management
 * Manage document pickup, signature capture, and release tracking
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/registrar-auth.php';
require_once __DIR__ . '/../includes/registrar-service.php';

regRequireAuth();

$db = db();

// Fetch documents ready for release (status = 'For Release')
$query = "
    SELECT
        dr.id as request_id,
        dr.request_no,
        dr.channel,
        dr.created_at,
        s.id as student_id,
        s.student_number,
        s.first_name,
        s.last_name,
        s.program_course,
        COUNT(dri.id) as total_items,
        SUM(CASE WHEN dri.status = 'Generated' THEN 1 ELSE 0 END) as ready_items,
        GROUP_CONCAT(dri.doc_type SEPARATOR ', ') as doc_types
    FROM reg_doc_requests dr
    INNER JOIN reg_students s ON s.id = dr.student_id
    LEFT JOIN reg_doc_request_items dri ON dri.request_id = dr.id
    WHERE dr.status = 'For Release'
    GROUP BY dr.id
    ORDER BY dr.created_at ASC
    LIMIT 100
";
$stmt = $db->query($query);
$releaseQueue = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

// Fetch recently released documents (today)
$recentQuery = "
    SELECT
        drel.id,
        drel.release_slip_no,
        drel.claimant_name,
        drel.claimant_id,
        drel.released_at,
        dri.doc_type,
        dr.request_no,
        s.student_number,
        s.first_name,
        s.last_name
    FROM reg_doc_releases drel
    INNER JOIN reg_doc_request_items dri ON dri.id = drel.request_item_id
    INNER JOIN reg_doc_requests dr ON dr.id = dri.request_id
    INNER JOIN reg_students s ON s.id = dr.student_id
    WHERE DATE(drel.released_at) = CURDATE()
    ORDER BY drel.released_at DESC
    LIMIT 50
";
$stmt = $db->query($recentQuery);
$recentReleases = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

// Stats
$stats = [
    'for_release' => count($releaseQueue),
    'released_today' => count($recentReleases),
];

$pageTitle = "Document Release Management";
require_once __DIR__ . '/../../../layouts/header.php';
?>

<style>
.release-signature-pad {
    border: 2px solid #dee2e6;
    border-radius: 8px;
    cursor: crosshair;
    background: #fff;
    width: 100%;
    height: 200px;
}
.release-id-preview {
    max-width: 100%;
    max-height: 200px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    margin-top: 8px;
}
</style>

<div class="mpl" data-mpl>
    <div class="mpl-top">
        <div>
            <h1><?= e($pageTitle) ?></h1>
            <p>Process document releases with signature capture and ID verification</p>
        </div>
        <div class="mpl-toolbar">
            <a class="mpl-add" href="document-requests.php">
                <i class="fas fa-arrow-left"></i> Back to Requests
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="mpl-stats">
        <div class="mpl-stat">
            <div class="mpl-stat-value"><?= $stats['for_release'] ?></div>
            <div class="mpl-stat-label">For Release</div>
        </div>
        <div class="mpl-stat">
            <div class="mpl-stat-value"><?= $stats['released_today'] ?></div>
            <div class="mpl-stat-label">Released Today</div>
        </div>
    </div>

    <!-- Release Queue -->
    <div class="mpl-panel">
        <div class="mpl-panel-header">
            <h2>Release Queue</h2>
        </div>
        <div class="mpl-filters">
            <input type="text" id="queueFilter" class="mpl-search" placeholder="Search by request no, student name, or number...">
        </div>
        <div class="mpl-table-wrap">
            <table class="mpl-table" id="queueTable">
                <thead>
                    <tr>
                        <th>Request No</th>
                        <th>Student</th>
                        <th>Documents</th>
                        <th>Ready Items</th>
                        <th>Channel</th>
                        <th>Requested</th>
                        <th class="mpl-actions-col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($releaseQueue)): ?>
                        <tr class="mpl-empty">
                            <td colspan="7">No documents ready for release</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($releaseQueue as $req): ?>
                            <?php
                            $searchData = strtolower(
                                $req['request_no'] . ' ' .
                                $req['student_number'] . ' ' .
                                $req['first_name'] . ' ' .
                                $req['last_name']
                            );
                            ?>
                            <tr class="queue-row" data-search="<?= e($searchData) ?>">
                                <td><strong><?= e($req['request_no']) ?></strong></td>
                                <td>
                                    <div class="mpl-person">
                                        <div class="mpl-avatar"><?= e(strtoupper(substr($req['first_name'], 0, 1))) ?></div>
                                        <div>
                                            <div><?= e($req['first_name'] . ' ' . $req['last_name']) ?></div>
                                            <small><?= e($req['student_number']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><small><?= e($req['doc_types']) ?></small></td>
                                <td>
                                    <span class="badge bg-<?= $req['ready_items'] == $req['total_items'] ? 'success' : 'warning' ?>">
                                        <?= (int)$req['ready_items'] ?> / <?= (int)$req['total_items'] ?>
                                    </span>
                                </td>
                                <td><span class="badge bg-secondary"><?= e(ucfirst($req['channel'])) ?></span></td>
                                <td><?= e(date('M d, Y g:i A', strtotime($req['created_at']))) ?></td>
                                <td class="mpl-actions">
                                    <button class="mpl-btn-icon" onclick="viewReleaseRequest(<?= (int)$req['request_id'] ?>)" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="mpl-btn-icon" onclick="startRelease(<?= (int)$req['request_id'] ?>)" title="Process Release">
                                        <i class="fas fa-hand-holding"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Releases -->
    <div class="mpl-panel">
        <div class="mpl-panel-header">
            <h2>Released Today</h2>
        </div>
        <div class="mpl-table-wrap">
            <table class="mpl-table">
                <thead>
                    <tr>
                        <th>Release Slip No</th>
                        <th>Student</th>
                        <th>Document</th>
                        <th>Claimant</th>
                        <th>Released At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentReleases)): ?>
                        <tr class="mpl-empty">
                            <td colspan="5">No releases today</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentReleases as $rel): ?>
                            <tr>
                                <td><strong><?= e($rel['release_slip_no']) ?></strong></td>
                                <td>
                                    <div><?= e($rel['first_name'] . ' ' . $rel['last_name']) ?></div>
                                    <small><?= e($rel['student_number']) ?></small>
                                </td>
                                <td><?= e($rel['doc_type']) ?></td>
                                <td>
                                    <div><?= e($rel['claimant_name']) ?></div>
                                    <?php if (!empty($rel['claimant_id'])): ?>
                                        <small>ID: <?= e($rel['claimant_id']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= e(date('g:i A', strtotime($rel['released_at']))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- View Release Request Modal -->
<div class="modal fade" id="viewReleaseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Release Request Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewReleaseContent">
                <div class="text-center py-4">
                    <div class="spinner-border" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Release Process Modal -->
<div class="modal fade" id="releaseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="releaseForm">
                <div class="modal-header">
                    <h5 class="modal-title">Process Document Release</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="releaseRequestId" name="request_id">

                    <div id="releaseItemsList"></div>

                    <hr class="my-4">

                    <h6>Claimant Information</h6>

                    <div class="mb-3">
                        <label class="form-label">Claimant Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="claimant_name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Valid ID Type</label>
                        <select class="form-select" name="claimant_id_type">
                            <option value="">Select ID type</option>
                            <option value="Student ID">Student ID</option>
                            <option value="Driver's License">Driver's License</option>
                            <option value="Passport">Passport</option>
                            <option value="National ID">National ID</option>
                            <option value="SSS ID">SSS ID</option>
                            <option value="Other">Other Government ID</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ID Number</label>
                        <input type="text" class="form-control" name="claimant_id" placeholder="Enter ID number">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Signature <span class="text-danger">*</span></label>
                        <div>
                            <canvas id="signaturePad" class="release-signature-pad"></canvas>
                            <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="clearSignature()">
                                <i class="fas fa-eraser"></i> Clear Signature
                            </button>
                        </div>
                        <input type="hidden" name="signature_data" id="signatureData">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i> Complete Release
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const API_BASE = '<?= API_BASE ?>/registrar/api';
let signaturePad = null;
let currentReleaseItems = [];

// Initialize signature pad when modal opens
document.getElementById('releaseModal').addEventListener('shown.bs.modal', function() {
    if (!signaturePad) {
        const canvas = document.getElementById('signaturePad');
        const ctx = canvas.getContext('2d');

        // Set canvas size
        canvas.width = canvas.offsetWidth;
        canvas.height = canvas.offsetHeight;

        let drawing = false;
        let lastX = 0;
        let lastY = 0;

        canvas.addEventListener('mousedown', (e) => {
            drawing = true;
            const rect = canvas.getBoundingClientRect();
            lastX = e.clientX - rect.left;
            lastY = e.clientY - rect.top;
        });

        canvas.addEventListener('mousemove', (e) => {
            if (!drawing) return;
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(x, y);
            ctx.strokeStyle = '#000';
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.stroke();

            lastX = x;
            lastY = y;
        });

        canvas.addEventListener('mouseup', () => { drawing = false; });
        canvas.addEventListener('mouseout', () => { drawing = false; });

        signaturePad = { canvas, ctx };
    }
});

function clearSignature() {
    if (signaturePad) {
        signaturePad.ctx.clearRect(0, 0, signaturePad.canvas.width, signaturePad.canvas.height);
        document.getElementById('signatureData').value = '';
    }
}

async function viewReleaseRequest(requestId) {
    const modal = new bootstrap.Modal(document.getElementById('viewReleaseModal'));
    modal.show();

    try {
        const res = await fetch(`${API_BASE}/documents.php?action=get&id=${requestId}`);
        const data = await res.json();

        if (!data.success) throw new Error(data.error || 'Failed to fetch request');

        const req = data.data;
        const items = data.items || [];

        let html = `
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Request No:</strong> ${escapeHtml(req.request_no)}<br>
                    <strong>Student:</strong> ${escapeHtml(req.first_name + ' ' + req.last_name)}<br>
                    <strong>Student No:</strong> ${escapeHtml(req.student_number)}
                </div>
                <div class="col-md-6">
                    <strong>Program:</strong> ${escapeHtml(req.program_course)}<br>
                    <strong>Channel:</strong> ${escapeHtml(req.channel)}<br>
                    <strong>Status:</strong> <span class="badge bg-info">${escapeHtml(req.status)}</span>
                </div>
            </div>
            <hr>
            <h6>Document Items</h6>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Document Type</th>
                        <th>Copies</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
        `;

        items.forEach(item => {
            html += `
                <tr>
                    <td>${escapeHtml(item.doc_type)}</td>
                    <td>${item.copies}</td>
                    <td><span class="badge bg-${item.status === 'Generated' ? 'success' : 'warning'}">${escapeHtml(item.status)}</span></td>
                </tr>
            `;
        });

        html += '</tbody></table>';

        document.getElementById('viewReleaseContent').innerHTML = html;
    } catch (err) {
        document.getElementById('viewReleaseContent').innerHTML = `
            <div class="alert alert-danger">Error: ${escapeHtml(err.message)}</div>
        `;
    }
}

async function startRelease(requestId) {
    try {
        const res = await fetch(`${API_BASE}/documents.php?action=get&id=${requestId}`);
        const data = await res.json();

        if (!data.success) throw new Error(data.error || 'Failed to fetch request');

        const items = (data.items || []).filter(i => i.status === 'Generated');

        if (items.length === 0) {
            alert('No documents are ready for release in this request.');
            return;
        }

        currentReleaseItems = items;
        document.getElementById('releaseRequestId').value = requestId;

        let itemsHtml = '<h6>Documents to Release</h6><ul class="list-group mb-3">';
        items.forEach(item => {
            itemsHtml += `
                <li class="list-group-item">
                    <strong>${escapeHtml(item.doc_type)}</strong> - ${item.copies} ${item.copies > 1 ? 'copies' : 'copy'}
                    <input type="hidden" name="item_ids[]" value="${item.id}">
                </li>
            `;
        });
        itemsHtml += '</ul>';

        document.getElementById('releaseItemsList').innerHTML = itemsHtml;

        const modal = new bootstrap.Modal(document.getElementById('releaseModal'));
        modal.show();
    } catch (err) {
        alert('Error: ' + err.message);
    }
}

document.getElementById('releaseForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    // Capture signature
    if (signaturePad) {
        document.getElementById('signatureData').value = signaturePad.canvas.toDataURL('image/png');
    }

    if (!document.getElementById('signatureData').value) {
        alert('Please provide a signature before completing the release.');
        return;
    }

    const formData = new FormData(this);
    const claimantName = formData.get('claimant_name');
    const claimantId = formData.get('claimant_id') || null;
    const itemIds = formData.getAll('item_ids[]');

    try {
        // Release each item
        for (const itemId of itemIds) {
            const res = await fetch(`${API_BASE}/documents.php?action=release`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    csrf_token: '<?= csrfToken() ?>',
                    item_id: parseInt(itemId),
                    claimant_name: claimantName,
                    claimant_id: claimantId
                })
            });

            const data = await res.json();
            if (!data.success) throw new Error(data.error || 'Release failed');
        }

        // Update request status to Released
        const reqId = parseInt(document.getElementById('releaseRequestId').value);
        await fetch(`${API_BASE}/documents.php?action=update_status`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                csrf_token: '<?= csrfToken() ?>',
                request_id: reqId,
                status: 'Released'
            })
        });

        alert('Documents released successfully!');
        location.reload();
    } catch (err) {
        alert('Error: ' + err.message);
    }
});

// Queue filter
const queueFilter = document.getElementById('queueFilter');
if (queueFilter) {
    queueFilter.addEventListener('input', debounce(function() {
        const q = this.value.trim().toLowerCase();
        document.querySelectorAll('#queueTable tbody tr.queue-row').forEach(row => {
            const match = (row.dataset.search || '').toLowerCase();
            row.style.display = match.includes(q) ? '' : 'none';
        });
    }, 150));
}

function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<?php require_once __DIR__ . '/../../../layouts/footer.php'; ?>
