<?php
/**
 * SMS 2 - Guardian & Emergency Contact
 * Module: Registrar
 * Manage student guardian information.
 *
 * If no ?student_id= is given (e.g. clicked from the sidebar), shows a
 * system-wide dashboard of guardian records instead of a hard error.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/registrar-service.php';

regRequireAction('registrar.view');

$pageTitle    = 'Guardian & Emergency Contact';
$activeModule = 'registrar';
$activePage   = 'guardian-emergency-contact';

$db = db();

// Get student from query parameter (may be absent, e.g. clicked from sidebar)
$studentId = (int)($_GET['student_id'] ?? 0);
$student   = null;
$notFound  = false;

if ($studentId > 0) {
    $student = regGetStudent($studentId);
    if (!$student) {
        $notFound = true;
        $studentId = 0; // fall through to dashboard view
    }
}

$guardians = [];
if ($student) {
    // Single-student view: just this student's guardians.
    $stmt = $db->prepare("SELECT * FROM `reg_guardians` WHERE `student_id` = ? ORDER BY `is_emergency` DESC, `relationship`");
    $stmt->execute([$studentId]);
    $guardians = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Dashboard view: every guardian record system-wide, joined with student info.
    $guardians = $db->query("
        SELECT g.*, s.student_number, s.first_name, s.last_name, s.program_course
        FROM `reg_guardians` g
        JOIN `reg_students` s ON s.id = g.student_id
        ORDER BY g.created_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $totalStudents          = (int)$db->query("SELECT COUNT(*) FROM `reg_students` WHERE `status` = 'Active'")->fetchColumn();
    $studentsWithGuardians   = (int)$db->query("SELECT COUNT(DISTINCT student_id) FROM `reg_guardians`")->fetchColumn();
    $studentsWithoutGuardians = max(0, $totalStudents - $studentsWithGuardians);
    $emergencyCount          = count(array_filter($guardians, fn($g) => (int)($g['is_emergency'] ?? 0) === 1));

    // "Needs Attention" — active students with zero guardian records on file.
    $missingGuardianStudents = $db->query("
        SELECT s.id, s.student_number, s.first_name, s.last_name, s.program_course
        FROM `reg_students` s
        LEFT JOIN `reg_guardians` g ON g.student_id = s.id
        WHERE s.status = 'Active' AND g.id IS NULL
        ORDER BY s.last_name, s.first_name
    ")->fetchAll(PDO::FETCH_ASSOC);
}

$breadcrumbs = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Guardian & Emergency Contact', 'url' => $student ? (BASE_URL . '/modules/registrar/pages/guardian-emergency-contact.php') : null],
];
if ($student) {
    $breadcrumbs[] = ['label' => $student['first_name'] . ' ' . $student['last_name'], 'url' => null];

    // Shows a "Back" pill on the right end of the dark page-title banner.
    $pageBannerBackUrl   = BASE_URL . '/modules/registrar/pages/guardian-emergency-contact.php';
    $pageBannerBackLabel = 'Back to Dashboard';
}

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/modules/registrar/assets/css/registrar.css">

<?php renderBreadcrumbs($breadcrumbs); ?>

<div class="container-fluid py-4">

<?php if (!$student): ?>

    <!-- ============ DASHBOARD (no student_id in URL, or invalid one) ============ -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 text-dark mb-1">
                <i class="fas fa-users text-primary me-2"></i>Guardian & Emergency Contact
            </h1>
            <p class="text-muted mb-0">System-wide guardian records. Filter below or open a student from the Student Information System to manage their guardians.</p>
        </div>
    </div>

    <?php if ($notFound): ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i> Student #<?php echo (int)($_GET['student_id'] ?? 0); ?> was not found.
    </div>
    <?php endif; ?>

    <!-- Summary Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="reg-stat-card">
                <p class="stat-value"><?php echo count($guardians); ?></p>
                <p class="stat-label">Total Guardians</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="reg-stat-card success">
                <p class="stat-value"><?php echo $studentsWithGuardians; ?></p>
                <p class="stat-label">Students With Guardians</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="reg-stat-card warning">
                <p class="stat-value"><?php echo $studentsWithoutGuardians; ?></p>
                <p class="stat-label">Students Without Guardians</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="reg-stat-card danger">
                <p class="stat-value"><?php echo $emergencyCount; ?></p>
                <p class="stat-label">Emergency Contacts</p>
            </div>
        </div>
    </div>

    <!-- Needs Attention -->
    <?php if (!empty($missingGuardianStudents)): ?>
    <div class="alert alert-warning">
        <h6 class="mb-3"><i class="fas fa-exclamation-triangle"></i> Needs Attention — <?php echo count($missingGuardianStudents); ?> active student(s) with no guardian on file</h6>
        <div class="table-responsive">
            <table class="table reg-table mb-0">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Program</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($missingGuardianStudents as $m): ?>
                    <tr>
                        <td>
                            <span class="badge bg-primary"><?php echo htmlspecialchars($m['student_number']); ?></span>
                            <strong><?php echo htmlspecialchars($m['last_name'] . ', ' . $m['first_name']); ?></strong>
                        </td>
                        <td><?php echo htmlspecialchars($m['program_course'] ?? '-'); ?></td>
                        <td>
                            <a class="btn btn-sm btn-primary" href="guardian-emergency-contact.php?student_id=<?php echo (int)$m['id']; ?>&open=add">
                                <i class="fas fa-plus"></i> Add Guardian
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> All active students have at least one guardian on file.
    </div>
    <?php endif; ?>

    <!-- Filter -->
    <div class="reg-search-box">
        <div class="reg-form-group mb-0">
            <label>Filter records</label>
            <input type="text" id="dashboardFilter" class="form-control form-control-lg"
                   placeholder="Filter by student number, name, or guardian name..." autocomplete="off">
        </div>
    </div>

    <!-- Records Table -->
    <div class="card reg-shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-users me-2"></i>All Guardian Records</h5>
        </div>
        <div class="table-responsive">
            <table class="table reg-table mb-0" id="dashboardTable">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Guardian</th>
                        <th>Relationship</th>
                        <th>Contact</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($guardians)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle"></i> No guardian records in the system yet.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($guardians as $g): ?>
                    <tr class="dashboard-row">
                        <td>
                            <span class="badge bg-primary"><?php echo htmlspecialchars($g['student_number']); ?></span>
                            <strong><?php echo htmlspecialchars($g['last_name'] . ', ' . $g['first_name']); ?></strong>
                        </td>
                        <td><?php echo htmlspecialchars($g['full_name']); ?></td>
                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($g['relationship']); ?></span></td>
                        <td><?php echo htmlspecialchars($g['contact'] ?? '-'); ?></td>
                        <td>
                            <?php if ((int)($g['is_primary'] ?? 0) === 1): ?><span class="badge bg-success">Primary</span><?php endif; ?>
                            <?php if ((int)($g['is_emergency'] ?? 0) === 1): ?><span class="badge bg-danger">Emergency</span><?php endif; ?>
                        </td>
                        <td>
                            <a class="btn btn-sm btn-primary" href="guardian-emergency-contact.php?student_id=<?php echo (int)$g['student_id']; ?>">
                                <i class="fas fa-cog"></i> Manage
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php else: ?>

    <!-- ============ GUARDIANS FOR THE SELECTED STUDENT ============ -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-dark mb-1">Guardian & Emergency Contact</h1>
            <p class="text-muted mb-0">Emergency contacts and guardian information for <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></p>
        </div>
        <button class="btn btn-primary" onclick="openAddModal()">
            <i class="fas fa-plus"></i> Add Guardian
        </button>
    </div>

    <!-- Student Summary Card -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="reg-card">
                <div class="reg-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Student:</strong> <?php echo htmlspecialchars($student['student_number'] . ' - ' . $student['first_name'] . ' ' . $student['last_name']); ?></p>
                            <p><strong>Program:</strong> <?php echo htmlspecialchars($student['program_course'] ?? '-'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Year & Section:</strong> <?php echo htmlspecialchars($student['year_section'] ?? '-'); ?></p>
                            <p><strong>Status:</strong> <?php echo htmlspecialchars($student['status'] ?? '-'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Guardians Grid -->
    <div class="row">
        <?php if (empty($guardians)): ?>
        <div class="col-md-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No guardians registered yet. Click "Add Guardian" to add emergency contacts.
            </div>
        </div>
        <?php else: ?>
        <?php foreach ($guardians as $guardian): ?>
        <div class="col-md-6 mb-3">
            <div class="reg-card" id="guardian-<?php echo (int)$guardian['id']; ?>">
                <div class="reg-card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0"><?php echo htmlspecialchars($guardian['full_name']); ?></h6>
                            <small class="text-muted">
                                <?php
                                    $relationshipIcons = [
                                        'Mother' => '👩',
                                        'Father' => '👨',
                                        'Guardian' => '👥',
                                        'Sibling' => '👫',
                                        'Relative' => '👪',
                                        'Other' => '📋'
                                    ];
                                    $icon = $relationshipIcons[$guardian['relationship']] ?? '📋';
                                    echo $icon . ' ' . htmlspecialchars($guardian['relationship']);
                                ?>
                            </small>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-warning" onclick="openEditModal(<?php echo (int)$guardian['id']; ?>)" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteGuardian(<?php echo (int)$guardian['id']; ?>)" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="reg-card-body">
                    <div class="mb-3">
                        <p class="mb-1"><small class="text-muted">Contact Number</small></p>
                        <p class="mb-0">
                            <i class="fas fa-phone text-primary"></i>
                            <strong><?php echo htmlspecialchars($guardian['contact'] ?? 'Not provided'); ?></strong>
                        </p>
                    </div>

                    <div class="mb-3">
                        <p class="mb-1"><small class="text-muted">Email</small></p>
                        <p class="mb-0">
                            <i class="fas fa-envelope text-primary"></i>
                            <strong><?php echo htmlspecialchars($guardian['email'] ?? 'Not provided'); ?></strong>
                        </p>
                    </div>

                    <div class="mb-3">
                        <p class="mb-1"><small class="text-muted">Role</small></p>
                        <p class="mb-0">
                            <?php if ((int)($guardian['is_primary'] ?? 0) === 1): ?>
                                <span class="badge bg-success">Primary</span>
                            <?php endif; ?>
                            <?php if ((int)($guardian['is_emergency'] ?? 0) === 1): ?>
                                <span class="badge bg-danger">Emergency</span>
                            <?php endif; ?>
                            <?php if ((int)($guardian['is_primary'] ?? 0) !== 1 && (int)($guardian['is_emergency'] ?? 0) !== 1): ?>
                                <span class="badge bg-secondary">Additional</span>
                            <?php endif; ?>
                        </p>
                    </div>

                    <div class="reg-divider"></div>

                    <p class="mb-0"><small class="text-muted">Address:</small><br><?php echo htmlspecialchars($guardian['address'] ?? 'Not provided'); ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Summary Stats -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="reg-stat-card">
                <p class="stat-value"><?php echo count($guardians); ?></p>
                <p class="stat-label">Guardians</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="reg-stat-card success">
                <p class="stat-value">
                    <?php
                    $mothers = count(array_filter($guardians, fn($g) => $g['relationship'] === 'Mother'));
                    echo $mothers;
                    ?>
                </p>
                <p class="stat-label">Mother(s)</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="reg-stat-card info">
                <p class="stat-value">
                    <?php
                    $fathers = count(array_filter($guardians, fn($g) => $g['relationship'] === 'Father'));
                    echo $fathers;
                    ?>
                </p>
                <p class="stat-label">Father(s)</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="reg-stat-card warning">
                <p class="stat-value">
                    <?php
                    $others = count(array_filter($guardians, fn($g) => !in_array($g['relationship'], ['Mother', 'Father'])));
                    echo $others;
                    ?>
                </p>
                <p class="stat-label">Other</p>
            </div>
        </div>
    </div>

<?php endif; ?>

</div>

<?php if ($student): ?>
<!-- Add/Edit Guardian Modal -->
<div class="modal fade" id="guardianModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="reg-modal-header">
                <h5 class="modal-title" id="guardianModalTitle">Add Guardian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="guardianForm" onsubmit="return handleGuardianForm(event)">
                <div class="modal-body">
                    <input type="hidden" name="id" id="guardianId" value="">
                    <div class="reg-form-section">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="reg-form-group">
                                    <label>Full Name *</label>
                                    <input type="text" name="full_name" id="guardianFullName" class="form-control" required placeholder="e.g., Maria Dela Cruz">
                                </div>
                            </div>
                        </div>

                        <div class="reg-form-group">
                            <label>Relationship *</label>
                            <select name="relationship" id="guardianRelationship" class="form-select" required>
                                <option value="">Select Relationship</option>
                                <option value="Mother">👩 Mother</option>
                                <option value="Father">👨 Father</option>
                                <option value="Guardian">👥 Guardian</option>
                                <option value="Sibling">👫 Sibling</option>
                                <option value="Relative">👪 Relative</option>
                                <option value="Other">📋 Other</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Contact Number *</label>
                                    <input type="tel" name="contact" id="guardianContact" class="form-control" required placeholder="+63 9XX XXX XXXX">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" id="guardianEmail" class="form-control" placeholder="guardian@example.com">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Primary Contact</label>
                                    <select name="is_primary" id="guardianIsPrimary" class="form-select">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Emergency Contact</label>
                                    <select name="is_emergency" id="guardianIsEmergency" class="form-select">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="reg-form-group">
                            <label>Address</label>
                            <textarea name="address" id="guardianAddress" class="form-control" rows="2" placeholder="Street, City, Province"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-reg-primary">Save Guardian</button>
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

<?php if (!$student): ?>
/* ============ Dashboard: client-side filter over the already-rendered table (no extra requests needed) ============ */
const dashboardFilter = document.getElementById('dashboardFilter');
if (dashboardFilter) {
    dashboardFilter.addEventListener('input', debounce(function () {
        const q = dashboardFilter.value.trim().toLowerCase();
        document.querySelectorAll('#dashboardTable tbody tr.dashboard-row').forEach(function (row) {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    }, 150));
}
<?php else: ?>
/* ============ Guardian CRUD (shown when a student is selected) ============ */
const studentId = <?php echo (int)$studentId; ?>;

// If we arrived from the "Needs Attention" panel (?open=add), jump straight into the Add form.
<?php if (($_GET['open'] ?? '') === 'add'): ?>
document.addEventListener('DOMContentLoaded', function () {
    openAddModal();
});
<?php endif; ?>

// Records are embedded server-side so Edit can populate the form without a round trip.
const guardianRecords = <?php echo json_encode($guardians, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

function postJson(action, payload) {
    return fetch(API_BASE + '/guardians.php?action=' + action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': CSRF
        },
        body: JSON.stringify(payload)
    }).then(function (r) { return r.json(); });
}

function resetGuardianForm() {
    document.getElementById('guardianForm').reset();
    document.getElementById('guardianId').value = '';
}

function openAddModal() {
    resetGuardianForm();
    document.getElementById('guardianModalTitle').textContent = 'Add Guardian';
    showRegModal('guardianModal');
}

function openEditModal(guardianId) {
    const record = guardianRecords.find(g => parseInt(g.id, 10) === guardianId);
    if (!record) {
        showRegError('Guardian record not found');
        return;
    }

    document.getElementById('guardianModalTitle').textContent = 'Edit Guardian';
    document.getElementById('guardianId').value = record.id;
    document.getElementById('guardianFullName').value = record.full_name || '';
    document.getElementById('guardianRelationship').value = record.relationship || '';
    document.getElementById('guardianContact').value = record.contact || '';
    document.getElementById('guardianEmail').value = record.email || '';
    document.getElementById('guardianIsPrimary').value = String(parseInt(record.is_primary, 10) || 0);
    document.getElementById('guardianIsEmergency').value = String(parseInt(record.is_emergency, 10) || 0);
    document.getElementById('guardianAddress').value = record.address || '';

    showRegModal('guardianModal');
}

async function handleGuardianForm(e) {
    e.preventDefault();

    const form = document.getElementById('guardianForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    data.student_id = studentId;

    if (!data.id) {
        delete data.id;
    }

    try {
        const result = await postJson('save', data);
        if (result.success) {
            showRegSuccess(result.message || 'Guardian saved successfully');
            hideRegModal('guardianModal');
            setTimeout(() => location.reload(), 800);
        } else {
            showRegError(result.error || 'Save failed');
        }
    } catch (error) {
        console.error(error);
        showRegError('Error: ' + error.message);
    }

    return false;
}

async function deleteGuardian(guardianId) {
    if (!confirm('Delete this guardian record?')) return;

    try {
        const result = await postJson('delete', { id: guardianId });
        if (result.success) {
            showRegSuccess('Guardian deleted');
            setTimeout(() => location.reload(), 800);
        } else {
            showRegError(result.error || 'Delete failed');
        }
    } catch (error) {
        console.error(error);
        showRegError('Error: ' + error.message);
    }
}
<?php endif; ?>
</script>

<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>