<?php
/**
 * SMS 2 - Guardian & Emergency Contact
 * Module: Registrar
 * Manage student guardian information
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/registrar-service.php';

regRequireAction('registrar.view');

$pageTitle    = 'Guardian & Emergency Contact';
$activeModule = 'registrar';
$activePage   = 'guardian-emergency-contact';
$breadcrumbs  = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Guardian & Emergency Contact', 'url' => null],
];

// Get student from query parameter
$studentId = (int)($_GET['student_id'] ?? 0);
if ($studentId === 0) {
    die('Student ID required');
}

$student = regGetStudent($studentId);
if (!$student) {
    die('Student not found');
}

// Get guardians for this student
$db = db();
$stmt = $db->prepare("SELECT * FROM `reg_guardians` WHERE `student_id` = ? ORDER BY `relationship`");
$stmt->execute([$studentId]);
$guardians = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/modules/registrar/assets/css/registrar.css">

<?php renderBreadcrumbs($breadcrumbs); ?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-dark">Guardian & Emergency Contact</h1>
            <p class="text-muted">Emergency contacts and guardian information for <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></p>
        </div>
        <button class="btn btn-primary" onclick="showRegModal('guardianModalAdd')">
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
            <div class="reg-card">
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
                            <button class="btn btn-sm btn-warning" onclick="editGuardian(<?php echo $guardian['id']; ?>)" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteGuardian(<?php echo $guardian['id']; ?>)" title="Delete">
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
</div>

<!-- Add/Edit Guardian Modal -->
<div class="modal fade" id="guardianModalAdd" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="reg-modal-header">
                <h5 class="modal-title">Add Guardian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="guardianForm" onsubmit="return handleGuardianForm(event)">
                <div class="modal-body">
                    <div class="reg-form-section">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="reg-form-group">
                                    <label>Full Name *</label>
                                    <input type="text" name="full_name" class="form-control" required placeholder="e.g., Maria Dela Cruz">
                                </div>
                            </div>
                        </div>

                        <div class="reg-form-group">
                            <label>Relationship *</label>
                            <select name="relationship" class="form-select" required>
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
                                    <input type="tel" name="contact" class="form-control" required placeholder="+63 9XX XXX XXXX">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" placeholder="guardian@example.com">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Primary Contact</label>
                                    <select name="is_primary" class="form-select">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Emergency Contact</label>
                                    <select name="is_emergency" class="form-select">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="reg-form-group">
                            <label>Address</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="Street, City, Province"></textarea>
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

<script src="<?php echo BASE_URL; ?>/modules/registrar/assets/js/registrar.js"></script>
<script>
const studentId = <?php echo $studentId; ?>;
const API_BASE = '<?php echo BASE_URL; ?>/modules/registrar/api';
const CSRF = '<?= e(csrfToken()) ?>';

function postJson(payload) {
    return fetch(API_BASE + '/guardians.php?action=' + (payload.action || ''), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': CSRF
        },
        body: JSON.stringify(payload)
    }).then(function (r) { return r.json(); });
}

async function handleGuardianForm(e) {
    e.preventDefault();

    const form = document.getElementById('guardianForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    data.student_id = studentId;

    try {
        const result = await postJson(data);
        if (result.success) {
            showRegSuccess('Guardian saved successfully');
            form.reset();
            hideRegModal('guardianModalAdd');
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

function editGuardian(guardianId) {
    const card = document.getElementById('guardian-' + guardianId);
    showRegInfo('Edit Guardian #' + guardianId + ': open record and update details.');
}

async function deleteGuardian(guardianId) {
    if (!confirm('Delete this guardian record?')) return;

    try {
        const result = await postJson({ action: 'delete', id: guardianId });
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
</script>

<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
