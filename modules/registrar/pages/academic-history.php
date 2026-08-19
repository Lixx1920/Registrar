<?php
/**
 * SMS 2 - Academic History
 * Module: Registrar
 * Manage student educational background and previous school records.
 *
 * If no ?student_id= is given (e.g. clicked from the sidebar), shows a
 * student search picker instead of a hard error.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/registrar-service.php';

regRequireAction('registrar.view');

$pageTitle    = 'Academic History';
$activeModule = 'registrar';
$activePage   = 'academic-history';

// Get student from query parameter (may be absent, e.g. clicked from sidebar)
$studentId = (int)($_GET['student_id'] ?? 0);
$student   = null;
$notFound  = false;

if ($studentId > 0) {
    $student = regGetStudent($studentId);
    if (!$student) {
        $notFound = true;
        $studentId = 0; // fall through to picker view
    }
}

$records = [];
if ($student) {
    $db = db();
    $stmt = $db->prepare("SELECT * FROM `reg_academic_history` WHERE `student_id` = ? ORDER BY `from_year` DESC, `to_year` DESC");
    $stmt->execute([$studentId]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$breadcrumbs = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Academic History', 'url' => $student ? (BASE_URL . '/modules/registrar/pages/academic-history.php') : null],
];
if ($student) {
    $breadcrumbs[] = ['label' => $student['first_name'] . ' ' . $student['last_name'], 'url' => null];

    // Shows a "Back" pill on the right end of the dark page-title banner,
    // so a wrong student click is one click to undo.
    $pageBannerBackUrl   = BASE_URL . '/modules/registrar/pages/academic-history.php';
    $pageBannerBackLabel = 'Back to Search';
}

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/modules/registrar/assets/css/registrar.css">

<?php renderBreadcrumbs($breadcrumbs); ?>

<div class="container-fluid py-4">

<?php if (!$student): ?>

    <!-- ============ STUDENT PICKER (no student_id in URL, or invalid one) ============ -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 text-dark mb-1">
                <i class="fas fa-history text-primary me-2"></i>Academic History
            </h1>
            <p class="text-muted mb-0">Search for a student to view or manage their academic history</p>
        </div>
    </div>

    <?php if ($notFound): ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i> Student #<?php echo (int)($_GET['student_id'] ?? 0); ?> was not found. Search below to continue.
    </div>
    <?php endif; ?>

    <div class="reg-card mb-4">
        <div class="reg-card-body">
            <div class="reg-form-group mb-0">
                <label>Search by student number or name</label>
                <input type="text" id="studentSearchInput" class="form-control form-control-lg"
                       placeholder="e.g., 2024001 or Juan Santos" autocomplete="off" autofocus>
            </div>
        </div>
    </div>

    <div id="studentSearchResults">
        <div class="alert reg-search-hint text-center">
            <i class="fas fa-search"></i> Start typing to find a student.
        </div>
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
        <div>
            <button class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Add School Record
            </button>
        </div>
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
/* ============ Student search picker (shown when no student is selected) ============ */
const searchInput = document.getElementById('studentSearchInput');
const resultsBox = document.getElementById('studentSearchResults');

const runSearch = debounce(async function () {
    const q = searchInput.value.trim();
    if (q.length < 2) {
        resultsBox.innerHTML = '<div class="alert reg-search-hint text-center">' +
            '<i class="fas fa-search"></i> Keep typing (at least 2 characters).</div>';
        return;
    }

    resultsBox.innerHTML = '<div class="text-center text-muted py-3"><i class="fas fa-spinner fa-spin"></i> Searching...</div>';

    try {
        const response = await fetch(API_BASE + '/students.php?action=search&q=' + encodeURIComponent(q) + '&limit=10');
        const result = await response.json();

        if (!result.success || !result.data || result.data.length === 0) {
            resultsBox.innerHTML = '<div class="alert reg-search-hint reg-search-hint--static text-center">' +
                '<i class="fas fa-info-circle"></i> No matching students found.</div>';
            return;
        }

        let html = '<div class="card reg-shadow"><div class="table-responsive"><table class="table reg-table mb-0"><thead><tr>' +
            '<th>Student No.</th><th>Name</th><th>Program</th><th>Year/Section</th><th></th></tr></thead><tbody>';

        result.data.forEach(function (s) {
            html += '<tr>' +
                '<td><span class="badge bg-primary">' + escapeHtml(s.student_number) + '</span></td>' +
                '<td><strong>' + escapeHtml(s.last_name + ', ' + s.first_name) + '</strong></td>' +
                '<td>' + escapeHtml(s.program_course || '-') + '</td>' +
                '<td>' + escapeHtml(s.year_section || '-') + '</td>' +
                '<td><a class="btn btn-sm btn-primary" href="academic-history.php?student_id=' + encodeURIComponent(s.id) + '">' +
                'Select <i class="fas fa-arrow-right"></i></a></td>' +
                '</tr>';
        });

        html += '</tbody></table></div></div>';
        resultsBox.innerHTML = html;
    } catch (error) {
        console.error(error);
        resultsBox.innerHTML = '<div class="alert alert-danger">Error searching students: ' + error.message + '</div>';
    }
}, 350);

searchInput.addEventListener('input', runSearch);

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str == null ? '' : String(str);
    return div.innerHTML;
}
<?php else: ?>
/* ============ Academic history CRUD (shown when a student is selected) ============ */
const studentId = <?php echo (int)$studentId; ?>;

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