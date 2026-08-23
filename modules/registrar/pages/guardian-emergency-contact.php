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

<div class="container-fluid py-4<?php echo $student ? ' guardian-student-page' : ''; ?>">
<div class="mpl" data-mpl>

<?php if (!$student): ?>

    <div class="mpl-top">
        <div>
            <p>System-wide guardian records. Review records, spot missing contact information, and open a student to manage their guardians.</p>
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

    <section class="mpl-stats" aria-label="Guardian summary">
        <article class="mpl-stat">
            <div class="mpl-stat-icon blue"><i class="fas fa-user-friends"></i></div>
            <div>
                <span>Total Guardians</span>
                <strong><?php echo count($guardians); ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon green"><i class="fas fa-user-check"></i></div>
            <div>
                <span>Students With Guardians</span>
                <strong><?php echo $studentsWithGuardians; ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon amber"><i class="fas fa-exclamation-circle"></i></div>
            <div>
                <span>Students Without Guardians</span>
                <strong><?php echo $studentsWithoutGuardians; ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon purple"><i class="fas fa-bell"></i></div>
            <div>
                <span>Emergency Contacts</span>
                <strong><?php echo $emergencyCount; ?></strong>
            </div>
        </article>
    </section>

    <?php if (!empty($missingGuardianStudents)): ?>
    <section class="mpl-panel" style="margin-bottom: 1rem;">
        <div class="mpl-panel-head">
            <div>
                <h2>Needs Attention</h2>
                <p><?php echo count($missingGuardianStudents); ?> active student(s) with no guardian on file.</p>
            </div>
        </div>
        <div class="mpl-table-wrap">
            <table class="mpl-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Program</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($missingGuardianStudents as $m): ?>
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
                        <td>
                            <div class="mpl-actions">
                                <a href="guardian-emergency-contact.php?student_id=<?php echo (int)$m['id']; ?>&open=add" title="Add guardian" aria-label="Add guardian"><i class="fas fa-plus"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php else: ?>
    <div class="mpl-alert mpl-alert-auto">
        <i class="fas fa-check-circle"></i> All active students have at least one guardian on file.
    </div>
    <?php endif; ?>

    <div class="mpl-filters">
        <label class="mpl-search">
            <i class="fas fa-search"></i>
            <input type="search" id="dashboardFilter" placeholder="Search by student number, name, guardian, or contact..." aria-label="Search guardian records">
        </label>
        <a class="mpl-refresh" href="?"><i class="fas fa-sync-alt" aria-hidden="true"></i> Refresh</a>
    </div>

    <section class="mpl-panel">
        <div class="mpl-panel-head">
            <div>
                <h2>All Guardian Records</h2>
                <p><?php echo count($guardians); ?> total records</p>
            </div>
        </div>
        <div class="mpl-table-wrap">
            <table class="mpl-table" id="dashboardTable">
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
                        <td colspan="6" style="text-align:center;color:var(--sms-text-muted);padding:1.5rem;">
                            <i class="fas fa-info-circle"></i> No guardian records in the system yet.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($guardians as $g): ?>
                    <tr class="dashboard-row" data-search="<?php echo htmlspecialchars(strtolower(($g['student_number'] ?? '') . ' ' . ($g['last_name'] ?? '') . ' ' . ($g['first_name'] ?? '') . ' ' . ($g['full_name'] ?? '') . ' ' . ($g['contact'] ?? ''))); ?>">
                        <td>
                            <div class="mpl-person">
                                <span class="mpl-avatar"><?php echo htmlspecialchars(substr(($g['first_name'] ?? 'S'), 0, 1) . substr(($g['last_name'] ?? 'T'), 0, 1)); ?></span>
                                <div>
                                    <strong><?php echo htmlspecialchars(($g['last_name'] ?? '') . ', ' . ($g['first_name'] ?? '')); ?></strong>
                                    <small><?php echo htmlspecialchars($g['student_number'] ?? ''); ?></small>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($g['full_name'] ?? '—'); ?></td>
                        <td><span class="guardian-role"><?php echo htmlspecialchars($g['relationship'] ?? '—'); ?></span></td>
                        <td><?php echo htmlspecialchars($g['contact'] ?? '—'); ?></td>
                        <td>
                            <?php if ((int)($g['is_primary'] ?? 0) === 1): ?><span class="guardian-status primary">Primary</span><?php endif; ?>
                            <?php if ((int)($g['is_emergency'] ?? 0) === 1): ?><span class="guardian-status emergency">Emergency</span><?php endif; ?>
                            <?php if ((int)($g['is_primary'] ?? 0) !== 1 && (int)($g['is_emergency'] ?? 0) !== 1): ?><span class="guardian-status neutral">Additional</span><?php endif; ?>
                        </td>
                        <td>
                            <div class="mpl-actions">
                                <a href="guardian-emergency-contact.php?student_id=<?php echo (int)$g['student_id']; ?>" title="Manage guardian" aria-label="Manage guardian"><i class="fas fa-cog"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

<?php else: ?>

    <div class="mpl-top">
        <div>
            <p>Emergency contacts and guardian information for <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></p>
        </div>
        <div class="mpl-toolbar">
            <a class="mpl-add" href="javascript:void(0)" onclick="openAddModal()">
                <i class="fas fa-plus" aria-hidden="true"></i> Add Guardian
            </a>
            <a class="mpl-btn mpl-btn-ghost" href="guardian-emergency-contact.php">
                <i class="fas fa-arrow-left" aria-hidden="true"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <section class="mpl-stats" aria-label="Student guardian summary">
        <article class="mpl-stat">
            <div class="mpl-stat-icon blue"><i class="fas fa-user-friends"></i></div>
            <div>
                <span>Total Guardians</span>
                <strong><?php echo count($guardians); ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon green"><i class="fas fa-female"></i></div>
            <div>
                <span>Mother(s)</span>
                <strong><?php echo count(array_filter($guardians, fn($g) => $g['relationship'] === 'Mother')); ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon amber"><i class="fas fa-male"></i></div>
            <div>
                <span>Father(s)</span>
                <strong><?php echo count(array_filter($guardians, fn($g) => $g['relationship'] === 'Father')); ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon purple"><i class="fas fa-people-arrows"></i></div>
            <div>
                <span>Other</span>
                <strong><?php echo count(array_filter($guardians, fn($g) => !in_array($g['relationship'], ['Mother', 'Father']))); ?></strong>
            </div>
        </article>
    </section>

    <section class="mpl-panel" style="margin-bottom: 1rem;">
        <div class="mpl-panel-head">
            <div>
                <h2>Student Snapshot</h2>
                <p>Student and enrollment overview.</p>
            </div>
        </div>
        <div class="guardian-summary">
            <div>
                <span>Student</span>
                <strong><?php echo htmlspecialchars($student['student_number'] . ' - ' . $student['first_name'] . ' ' . $student['last_name']); ?></strong>
            </div>
            <div>
                <span>Program</span>
                <strong><?php echo htmlspecialchars($student['program_course'] ?? '—'); ?></strong>
            </div>
            <div>
                <span>Year &amp; Section</span>
                <strong><?php echo htmlspecialchars($student['year_section'] ?? '—'); ?></strong>
            </div>
            <div>
                <span>Status</span>
                <strong><?php echo htmlspecialchars($student['status'] ?? '—'); ?></strong>
            </div>
        </div>
    </section>

    <div class="mpl-filters">
        <label class="mpl-search">
            <i class="fas fa-search"></i>
            <input type="search" id="recordSearch" placeholder="Search by guardian name, relationship, contact, or address..." aria-label="Search guardian records">
        </label>
        <a class="mpl-refresh" href="?student_id=<?php echo (int)$studentId; ?>"><i class="fas fa-sync-alt" aria-hidden="true"></i> Refresh</a>
    </div>

    <section class="mpl-panel">
        <div class="mpl-panel-head">
            <div>
                <h2>Guardian List</h2>
                <p><?php echo count($guardians); ?> guardian(s) on file.</p>
            </div>
        </div>
        <div class="guardian-grid">
            <?php if (empty($guardians)): ?>
            <div class="guardian-empty">
                <i class="fas fa-info-circle"></i> No guardians registered yet. Click "Add Guardian" to add emergency contacts.
            </div>
            <?php else: ?>
            <?php foreach ($guardians as $guardian): ?>
            <article class="guardian-card" id="guardian-<?php echo (int)$guardian['id']; ?>" data-search="<?php echo htmlspecialchars(strtolower(($guardian['full_name'] ?? '') . ' ' . ($guardian['relationship'] ?? '') . ' ' . ($guardian['contact'] ?? '') . ' ' . ($guardian['address'] ?? ''))); ?>">
                <div class="guardian-card-head">
                    <div>
                        <h3><?php echo htmlspecialchars($guardian['full_name']); ?></h3>
                        <small>
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
                    <div class="mpl-actions">
                        <a href="javascript:void(0)" onclick="openEditModal(<?php echo (int)$guardian['id']; ?>)" title="Edit" aria-label="Edit"><i class="fas fa-pen"></i></a>
                        <a class="danger" href="javascript:void(0)" onclick="deleteGuardian(<?php echo (int)$guardian['id']; ?>)" title="Delete" aria-label="Delete"><i class="fas fa-trash"></i></a>
                    </div>
                </div>

                <div class="guardian-card-body">
                    <div class="guardian-field">
                        <span>Contact Number</span>
                        <strong><i class="fas fa-phone"></i> <?php echo htmlspecialchars($guardian['contact'] ?? 'Not provided'); ?></strong>
                    </div>
                    <div class="guardian-field">
                        <span>Email</span>
                        <strong><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($guardian['email'] ?? 'Not provided'); ?></strong>
                    </div>
                    <div class="guardian-field">
                        <span>Role</span>
                        <div class="guardian-status-wrap">
                            <?php if ((int)($guardian['is_primary'] ?? 0) === 1): ?><span class="guardian-status primary">Primary</span><?php endif; ?>
                            <?php if ((int)($guardian['is_emergency'] ?? 0) === 1): ?><span class="guardian-status emergency">Emergency</span><?php endif; ?>
                            <?php if ((int)($guardian['is_primary'] ?? 0) !== 1 && (int)($guardian['is_emergency'] ?? 0) !== 1): ?><span class="guardian-status neutral">Additional</span><?php endif; ?>
                        </div>
                    </div>
                    <div class="guardian-field">
                        <span>Address</span>
                        <strong><?php echo htmlspecialchars($guardian['address'] ?? 'Not provided'); ?></strong>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

<?php endif; ?>

</div>
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

window.setTimeout(function () {
    document.querySelectorAll('.mpl-alert-auto').forEach(function (alert) {
        alert.classList.add('mpl-alert-hide');
        window.setTimeout(function () { alert.remove(); }, 350);
    });
}, 2000);

<?php if (!$student): ?>
/* ============ Dashboard: client-side filter over the already-rendered table (no extra requests needed) ============ */
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
/* ============ Guardian CRUD (shown when a student is selected) ============ */
const studentId = <?php echo (int)$studentId; ?>;
const recordSearch = document.getElementById('recordSearch');
if (recordSearch) {
    recordSearch.addEventListener('input', debounce(function () {
        const q = recordSearch.value.trim().toLowerCase();
        document.querySelectorAll('.guardian-card[data-search]').forEach(function (card) {
            const match = (card.dataset.search || card.textContent || '').toLowerCase();
            card.style.display = match.includes(q) ? '' : 'none';
        });
    }, 150));
}

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