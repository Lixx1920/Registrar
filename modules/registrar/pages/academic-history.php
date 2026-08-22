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
if ($student) {
    // Single-student view: just this student's records.
    $stmt = $db->prepare("SELECT * FROM `reg_academic_history` WHERE `student_id` = ? ORDER BY `from_year` DESC, `to_year` DESC");
    $stmt->execute([$studentId]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

<div class="container-fluid py-4">

<?php if (!$student): ?>

    <!-- ============ DASHBOARD (no student_id in URL, or invalid one) ============ -->
    <div class="mpl" data-mpl>

        <div class="mpl-top">
            <p>System-wide academic history records. Filter below or open a student from the Student Information System to manage their records.</p>
        </div>

        <?php if ($notFound): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> Student #<?php echo (int)($_GET['student_id'] ?? 0); ?> was not found.
        </div>
        <?php endif; ?>

        <!-- Stats -->
        <section class="mpl-stats" aria-label="Academic history summary">
            <article class="mpl-stat">
                <div class="mpl-stat-icon blue"><i class="fas fa-book"></i></div>
                <div>
                    <span>Total Records</span>
                    <strong><?php echo count($records); ?></strong>
                </div>
            </article>
            <article class="mpl-stat">
                <div class="mpl-stat-icon green"><i class="fas fa-check-circle"></i></div>
                <div>
                    <span>Students With Records</span>
                    <strong><?php echo $studentsWithRecords; ?></strong>
                </div>
            </article>
            <article class="mpl-stat">
                <div class="mpl-stat-icon amber"><i class="fas fa-exclamation-triangle"></i></div>
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

        <!-- Needs Attention -->
        <?php if (!empty($missingRecordsStudents)): ?>
        <div class="alert alert-warning">
            <h6 class="mb-3"><i class="fas fa-exclamation-triangle"></i> Needs Attention — <?php echo count($missingRecordsStudents); ?> active student(s) with no academic history on file</h6>
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
                        <?php foreach ($missingRecordsStudents as $m): ?>
                        <tr>
                            <td>
                                <span class="badge bg-primary"><?php echo htmlspecialchars($m['student_number']); ?></span>
                                <strong><?php echo htmlspecialchars($m['last_name'] . ', ' . $m['first_name']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($m['program_course'] ?? '-'); ?></td>
                            <td>
                                <a class="btn btn-sm btn-primary" href="academic-history.php?student_id=<?php echo (int)$m['id']; ?>&open=add">
                                    <i class="fas fa-plus"></i> Add Record
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
            <i class="fas fa-check-circle"></i> All active students have at least one academic history record on file.
        </div>
        <?php endif; ?>

        <!-- Filters -->
        <div class="mpl-filters">
            <label class="mpl-search">
                <i class="fas fa-search"></i>
                <input type="search" id="mplSearch" placeholder="Search by student number, name, or school..." aria-label="Search academic history">
            </label>
            <select id="mplLevel" aria-label="Filter by level">
                <option value="">All Levels</option>
                <option value="elementary">Elementary</option>
                <option value="junior high school">Junior High School</option>
                <option value="senior high school">Senior High School</option>
                <option value="college">College</option>
                <option value="vocational">Vocational</option>
                <option value="other">Other</option>
            </select>
            <a class="mpl-refresh" href="academic-history.php"><i class="fas fa-sync-alt" aria-hidden="true"></i> Refresh</a>
        </div>

        <!-- Records Table -->
        <section class="mpl-panel">
            <div class="mpl-panel-head">
                <div>
                    <h2>All Academic History Records</h2>
                    <p>System-wide records across every student.</p>
                </div>
            </div>

            <div class="mpl-table-wrap">
                <table class="mpl-table">
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
                    <tbody id="mplRows">
                        <?php if (empty($records)): ?>
                        <tr>
                            <td colspan="7" style="text-align:center;color:var(--sms-text-muted);padding:1.5rem;">
                                No academic history records in the system yet.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($records as $r):
                            $fullName = $r['first_name'] . ' ' . $r['last_name'];
                            $searchBlob = strtolower($r['student_number'] . ' ' . $fullName . ' ' . $r['school_name']);
                        ?>
                        <tr data-search="<?php echo htmlspecialchars($searchBlob); ?>" data-level="<?php echo htmlspecialchars(strtolower($r['level'] ?? '')); ?>">
                            <td>
                                <div class="mpl-person">
                                    <span class="mpl-avatar"><?php echo htmlspecialchars(regInitials($fullName)); ?></span>
                                    <div>
                                        <strong><?php echo htmlspecialchars($r['last_name'] . ', ' . $r['first_name']); ?></strong>
                                        <small><?php echo htmlspecialchars($r['student_number']); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($r['school_name']); ?></td>
                            <td><span class="mpl-status scheduled"><?php echo htmlspecialchars($r['level'] ?? '-'); ?></span></td>
                            <td><?php echo htmlspecialchars((string)($r['from_year'] ?? '-')); ?></td>
                            <td><?php echo htmlspecialchars((string)($r['to_year'] ?? '-')); ?></td>
                            <td><small><?php echo htmlspecialchars($r['awards'] ?? '-'); ?></small></td>
                            <td>
                                <div class="mpl-actions">
                                    <a href="academic-history.php?student_id=<?php echo (int)$r['student_id']; ?>" title="Manage" aria-label="Manage"><i class="fas fa-cog"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mpl-foot">
                <span class="meta" id="mplMeta">Showing <?php echo count($records); ?> of <?php echo count($records); ?> records</span>
            </div>
        </section>

    </div>

<?php else: ?>

    <!-- ============ ACADEMIC HISTORY FOR THE SELECTED STUDENT ============ -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-dark mb-1">
                <i class="fas fa-history text-primary me-2"></i>Academic History
            </h1>
            <p class="text-muted mb-0">Educational background for <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></p>
        </div>
        <button class="btn btn-primary" onclick="openAddModal()">
            <i class="fas fa-plus"></i> Add School Record
        </button>
    </div>

    <!-- Student Summary Card -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="reg-card">
                <div class="reg-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Student:</strong> <?php echo htmlspecialchars($student['student_number'] . ' - ' . $student['first_name'] . ' ' . $student['last_name']); ?></p>
                            <p class="mb-0"><strong>Program:</strong> <?php echo htmlspecialchars($student['program_course'] ?? '-'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Year & Section:</strong> <?php echo htmlspecialchars($student['year_section'] ?? '-'); ?></p>
                            <p class="mb-0"><strong>Status:</strong> <?php echo htmlspecialchars($student['status'] ?? '-'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="reg-stat-card">
                <p class="stat-value"><?php echo count($records); ?></p>
                <p class="stat-label">Total Records</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="reg-stat-card success">
                <p class="stat-value"><?php echo count(array_filter($records, fn($r) => !empty($r['awards']))); ?></p>
                <p class="stat-label">With Awards</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="reg-stat-card info">
                <p class="stat-value"><?php echo count(array_filter($records, fn($r) => !empty($r['file_id']))); ?></p>
                <p class="stat-label">With Attachments</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="reg-stat-card warning">
                <?php
                    $years = array_filter(array_column($records, 'from_year'));
                    $earliest = $years ? min($years) : null;
                ?>
                <p class="stat-value"><?php echo $earliest ?? '-'; ?></p>
                <p class="stat-label">Earliest Record</p>
            </div>
        </div>
    </div>

    <!-- Records Table -->
    <div class="card reg-shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-school me-2"></i>School History (<?php echo count($records); ?>)</h5>
        </div>
        <div class="table-responsive">
            <table class="table reg-table mb-0">
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
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle"></i> No academic history recorded yet. Click "Add School Record" to get started.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($records as $r): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($r['school_name']); ?></strong></td>
                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($r['level'] ?? '-'); ?></span></td>
                        <td><?php echo htmlspecialchars((string)($r['from_year'] ?? '-')); ?></td>
                        <td><?php echo htmlspecialchars((string)($r['to_year'] ?? '-')); ?></td>
                        <td><?php echo htmlspecialchars($r['awards'] ?? '-'); ?></td>
                        <td><small class="text-muted"><?php echo htmlspecialchars($r['remarks'] ?? '-'); ?></small></td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="openEditModal(<?php echo (int)$r['id']; ?>)" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteRecord(<?php echo (int)$r['id']; ?>)" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php endif; ?>

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

<?php if (!$student): ?>
/* ============ Dashboard: client-side filter over the already-rendered table (no extra requests needed) ============ */
(function () {
    const search = document.getElementById('mplSearch');
    const level = document.getElementById('mplLevel');
    const rows = document.querySelectorAll('#mplRows tr[data-search]');
    const meta = document.getElementById('mplMeta');
    const total = <?php echo count($records); ?>;
    if (!search) return;

    function applyFilters() {
        const q = (search.value || '').toLowerCase().trim();
        const lv = (level.value || '').toLowerCase();
        let visible = 0;

        rows.forEach(function (row) {
            const hay = row.getAttribute('data-search') || '';
            const rowLevel = row.getAttribute('data-level') || '';
            const show = (!q || hay.includes(q)) && (!lv || rowLevel === lv);
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        if (meta) meta.textContent = 'Showing ' + visible + ' of ' + total + ' records';
    }

    search.addEventListener('input', debounce(applyFilters, 150));
    level.addEventListener('change', applyFilters);
})();
<?php else: ?>
/* ============ Academic history CRUD (shown when a student is selected) ============ */
const studentId = <?php echo (int)$studentId; ?>;

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