<?php
/**
 * SMS 2 - Academic History
 * Module: Registrar
 * Manage student educational background and previous school records.
 *
 * If no ?student_id= is given (e.g. clicked from the sidebar), shows a
 * system-wide dashboard of academic history records instead of a hard error.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/registrar-service.php';

regRequireAction('registrar.view');

$pageTitle    = 'Academic History';
$activeModule = 'registrar';
$activePage   = 'academic-history';

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

$records = [];
$earliestRecordYear = '-';
if ($student) {
    // Single-student view: just this student's records.
    $stmt = $db->prepare("SELECT * FROM `reg_academic_history` WHERE `student_id` = ? ORDER BY `from_year` DESC, `to_year` DESC");
    $stmt->execute([$studentId]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $recordYears = array_filter(array_column($records, 'from_year'));
    if ($recordYears) {
        $earliestRecordYear = (string)min($recordYears);
    }
} else {
    // Dashboard view: every record system-wide, joined with student info.
    $records = $db->query("
        SELECT ah.*, s.student_number, s.first_name, s.last_name, s.program_course
        FROM `reg_academic_history` ah
        JOIN `reg_students` s ON s.id = ah.student_id
        ORDER BY ah.created_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $totalStudents        = (int)$db->query("SELECT COUNT(*) FROM `reg_students` WHERE `status` = 'Active'")->fetchColumn();
    $studentsWithRecords  = (int)$db->query("SELECT COUNT(DISTINCT student_id) FROM `reg_academic_history`")->fetchColumn();
    $studentsWithoutRecords = max(0, $totalStudents - $studentsWithRecords);
    $withAwardsCount      = count(array_filter($records, fn($r) => !empty($r['awards'])));

    // "Needs Attention" — active students with zero academic history records on file.
    $missingRecordsStudents = $db->query("
        SELECT s.id, s.student_number, s.first_name, s.last_name, s.program_course
        FROM `reg_students` s
        LEFT JOIN `reg_academic_history` ah ON ah.student_id = s.id
        WHERE s.status = 'Active' AND ah.id IS NULL
        ORDER BY s.last_name, s.first_name
    ")->fetchAll(PDO::FETCH_ASSOC);
}

$breadcrumbs = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Academic History', 'url' => $student ? (BASE_URL . '/modules/registrar/pages/academic-history.php') : null],
];
if ($student) {
    $breadcrumbs[] = ['label' => $student['first_name'] . ' ' . $student['last_name'], 'url' => null];

    // Shows a "Back" pill on the right end of the dark page-title banner.
    $pageBannerBackUrl   = BASE_URL . '/modules/registrar/pages/academic-history.php';
    $pageBannerBackLabel = 'Back to Dashboard';
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

    <div class="mpl-top">
        <div>
            <p>System-wide academic history records. Review records, spot missing student history, and open a student to manage their school records.</p>
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

    <section class="mpl-stats" aria-label="Academic history summary">
        <article class="mpl-stat">
            <div class="mpl-stat-icon blue"><i class="fas fa-school"></i></div>
            <div>
                <span>Total Records</span>
                <strong><?php echo count($records); ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon green"><i class="fas fa-user-check"></i></div>
            <div>
                <span>Students With Records</span>
                <strong><?php echo $studentsWithRecords; ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon amber"><i class="fas fa-exclamation-circle"></i></div>
            <div>
                <span>Students Without Records</span>
                <strong><?php echo $studentsWithoutRecords; ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon purple"><i class="fas fa-award"></i></div>
            <div>
                <span>With Awards</span>
                <strong><?php echo $withAwardsCount; ?></strong>
            </div>
        </article>
    </section>

    <?php if (!empty($missingRecordsStudents)): ?>
    <section class="mpl-panel" style="margin-bottom: 1rem;">
        <div class="mpl-panel-head">
            <div>
                <h2>Needs Attention</h2>
                <p><?php echo count($missingRecordsStudents); ?> active student(s) with no academic history on file.</p>
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
                    <?php foreach ($missingRecordsStudents as $m): ?>
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
                                <a href="academic-history.php?student_id=<?php echo (int)$m['id']; ?>&open=add" title="Add record" aria-label="Add record"><i class="fas fa-plus"></i></a>
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
        <i class="fas fa-check-circle"></i> All active students have at least one academic history record on file.
    </div>
    <?php endif; ?>

    <div class="mpl-filters">
        <label class="mpl-search">
            <i class="fas fa-search"></i>
            <input type="search" id="dashboardFilter" placeholder="Search by student number, name, or school..." aria-label="Search academic history records">
        </label>
        <a class="mpl-refresh" href="?"><i class="fas fa-sync-alt" aria-hidden="true"></i> Refresh</a>
    </div>

    <section class="mpl-panel ah-panel">
        <div class="mpl-panel-head">
            <div>
                <h2>All Academic History Records</h2>
                <p><?php echo count($records); ?> total records</p>
            </div>
        </div>
        <div class="mpl-table-wrap">
            <table class="mpl-table" id="dashboardTable">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>School Name</th>
                        <th>Level</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Awards</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($records)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center;color:var(--sms-text-muted);padding:1.5rem;">
                            <i class="fas fa-info-circle"></i> No academic history records in the system yet.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($records as $r): ?>
                    <tr class="dashboard-row" data-search="<?php echo htmlspecialchars(strtolower(($r['student_number'] ?? '') . ' ' . ($r['last_name'] ?? '') . ' ' . ($r['first_name'] ?? '') . ' ' . ($r['school_name'] ?? ''))); ?>">
                        <td>
                            <div class="mpl-person">
                                <span class="mpl-avatar"><?php echo htmlspecialchars(substr(($r['first_name'] ?? 'S'), 0, 1) . substr(($r['last_name'] ?? 'T'), 0, 1)); ?></span>
                                <div>
                                    <strong><?php echo htmlspecialchars(($r['last_name'] ?? '') . ', ' . ($r['first_name'] ?? '')); ?></strong>
                                    <small><?php echo htmlspecialchars($r['student_number'] ?? ''); ?></small>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($r['school_name'] ?? '—'); ?></td>
                        <td><span class="ah-pill"><?php echo htmlspecialchars($r['level'] ?? '—'); ?></span></td>
                        <td><?php echo htmlspecialchars((string)($r['from_year'] ?? '-')); ?></td>
                        <td><?php echo htmlspecialchars((string)($r['to_year'] ?? '-')); ?></td>
                        <td><?php echo htmlspecialchars($r['awards'] ?? '—'); ?></td>
                        <td>
                            <div class="mpl-actions">
                                <a href="academic-history.php?student_id=<?php echo (int)$r['student_id']; ?>" title="Manage record" aria-label="Manage record"><i class="fas fa-cog"></i></a>
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
            <p>Educational background for <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></p>
        </div>
        <div class="mpl-toolbar">
            <a class="mpl-add" href="javascript:void(0)" onclick="openAddModal()">
                <i class="fas fa-plus" aria-hidden="true"></i> Add School Record
            </a>
            <a class="mpl-btn mpl-btn-ghost" href="academic-history.php">
                <i class="fas fa-arrow-left" aria-hidden="true"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <section class="mpl-stats" aria-label="Student record summary">
        <article class="mpl-stat">
            <div class="mpl-stat-icon blue"><i class="fas fa-school"></i></div>
            <div>
                <span>Total Records</span>
                <strong><?php echo count($records); ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon green"><i class="fas fa-award"></i></div>
            <div>
                <span>With Awards</span>
                <strong><?php echo count(array_filter($records, fn($r) => !empty($r['awards']))); ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon amber"><i class="fas fa-paperclip"></i></div>
            <div>
                <span>With Attachments</span>
                <strong><?php echo count(array_filter($records, fn($r) => !empty($r['file_id']))); ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon purple"><i class="fas fa-calendar-alt"></i></div>
            <div>
                <span>Earliest Record</span>
                <strong><?php echo htmlspecialchars($earliestRecordYear); ?></strong>
            </div>
        </article>
    </section>

    <section class="mpl-panel" style="margin-bottom: 1rem;">
        <div class="mpl-panel-head">
            <div>
                <h2>Student Snapshot</h2>
                <p>Academic profile overview for this student.</p>
            </div>
        </div>
        <div class="ah-summary">
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
        <label class="mpl-search ah-row-search">
            <i class="fas fa-search"></i>
            <input type="search" id="recordSearch" placeholder="Search by school, level, award, or remarks..." aria-label="Search student academic records">
        </label>
        <a class="mpl-refresh" href="?student_id=<?php echo (int)$studentId; ?>"><i class="fas fa-sync-alt" aria-hidden="true"></i> Refresh</a>
    </div>

    <section class="mpl-panel ah-panel">
        <div class="mpl-panel-head">
            <div>
                <h2>School History</h2>
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
                            <i class="fas fa-info-circle"></i> No academic history recorded yet. Click "Add School Record" to get started.
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

<?php endif; ?>

</div>
</div>

<?php if ($student): ?>
<!-- Add/Edit Academic History Modal -->
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
                            <input type="text" name="awards" id="academicAwards" class="form-control" placeholder="e.g., Dean's Lister, With Honors">
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
/* ============ Academic history CRUD (shown when a student is selected) ============ */
const studentId = <?php echo (int)$studentId; ?>;
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

// If we arrived from the "Needs Attention" panel (?open=add), jump straight into the Add form.
<?php if (($_GET['open'] ?? '') === 'add'): ?>
document.addEventListener('DOMContentLoaded', function () {
    openAddModal();
});
<?php endif; ?>

// Records are embedded server-side so Edit can populate the form without a round trip.
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

    if (!data.id) {
        delete data.id;
    }

    try {
        const result = await postJson('save', data);
        if (result.success) {
            showRegSuccess(result.message || 'Academic record saved');
            hideRegModal('academicModal');
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

async function deleteRecord(recordId) {
    if (!confirm('Delete this academic history record?')) return;

    try {
        const result = await postJson('delete', { id: recordId });
        if (result.success) {
            showRegSuccess('Record deleted');
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