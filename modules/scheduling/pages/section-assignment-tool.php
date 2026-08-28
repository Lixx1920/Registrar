<?php
/**
 * SMS 2 - Section Assignment Tool
 * Module: Class Scheduling
 *
 * Real CRUD: create sections, assign existing Registrar students into them,
 * remove assignments. This is the data source the Registrar Masterlist
 * Generator will read "Enrolled" from (via schGetEnrollmentMap()).
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/scheduling-service.php';

$pageTitle    = 'Section Assignment Tool';
$activeModule = 'scheduling';
$activePage   = 'section-assignment-tool';

$db = db();

// Section detail view (?section_id=X) vs the sections dashboard.
$sectionId = (int) ($_GET['section_id'] ?? 0);
$section   = null;
$notFound  = false;

if ($sectionId > 0) {
    $section = schGetSection($sectionId);
    if (!$section) {
        $notFound = true;
        $sectionId = 0;
    }
}

$assignments = [];
if ($section) {
    $assignments = schListAssignmentsForSection($sectionId);
} else {
    $sections = schListSections();
    $totalSections = count($sections);
    $totalAssignments = (int) $db->query("SELECT COUNT(*) FROM `sch_section_assignments` WHERE `status` = 'Enrolled'")->fetchColumn();
    $studentsAssigned = (int) $db->query("SELECT COUNT(DISTINCT student_id) FROM `sch_section_assignments` WHERE `status` = 'Enrolled'")->fetchColumn();
    $totalActiveStudents = (int) $db->query("SELECT COUNT(*) FROM `reg_students` WHERE `status` = 'Active'")->fetchColumn();
    $studentsUnassigned = max(0, $totalActiveStudents - $studentsAssigned);
}

if (!function_exists('schInitials')) {
    function schInitials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $letters = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $letters .= strtoupper(substr($part, 0, 1));
        }
        return $letters !== '' ? $letters : 'ST';
    }
}

$breadcrumbs = [
    ['label' => 'Class Scheduling', 'url' => BASE_URL . '/modules/scheduling/index.php'],
    ['label' => 'Section Assignment Tool', 'url' => $section ? (BASE_URL . '/modules/scheduling/pages/section-assignment-tool.php') : null],
];
if ($section) {
    $breadcrumbs[] = ['label' => $section['program_course'] . ' - ' . $section['year_section'], 'url' => null];
    $pageBannerBackUrl   = BASE_URL . '/modules/scheduling/pages/section-assignment-tool.php';
    $pageBannerBackLabel = 'Back to Sections';
}

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<link href="<?php echo BASE_URL; ?>/assets/css/module-process-list.css?v=2" rel="stylesheet">

<?php renderBreadcrumbs($breadcrumbs); ?>

<div class="container-fluid py-4">
<div id="schAlertBox"></div>
<div class="mpl" data-mpl>

<?php if (!$section): ?>

    <!-- ============ SECTIONS DASHBOARD ============ -->
    <div class="mpl-top">
        <p>Create sections and assign students into them for a given school year and semester.</p>
        <div class="mpl-toolbar">
            <a class="mpl-add" href="javascript:void(0)" onclick="openAddSectionModal()">
                <i class="fas fa-plus" aria-hidden="true"></i> New Section
            </a>
        </div>
    </div>

    <?php if ($notFound): ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i> Section #<?php echo (int)($_GET['section_id'] ?? 0); ?> was not found.
    </div>
    <?php endif; ?>

    <section class="mpl-stats" aria-label="Section assignment summary">
        <article class="mpl-stat">
            <div class="mpl-stat-icon blue"><i class="fas fa-layer-group"></i></div>
            <div><span>Total Sections</span><strong><?php echo $totalSections; ?></strong></div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div><span>Students Assigned</span><strong><?php echo $studentsAssigned; ?></strong></div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon amber"><i class="fas fa-exclamation-triangle"></i></div>
            <div><span>Students Unassigned</span><strong><?php echo $studentsUnassigned; ?></strong></div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon purple"><i class="fas fa-user-check"></i></div>
            <div><span>Total Assignments</span><strong><?php echo $totalAssignments; ?></strong></div>
        </article>
    </section>

    <section class="mpl-panel">
        <div class="mpl-panel-head">
            <div>
                <h2>Sections</h2>
                <p>All sections across every program and school year.</p>
            </div>
        </div>
        <div class="mpl-table-wrap">
            <table class="mpl-table">
                <thead>
                    <tr>
                        <th>Program</th>
                        <th>Year &amp; Section</th>
                        <th>School Year</th>
                        <th>Semester</th>
                        <th>Students</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sections)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center;color:var(--sms-text-muted);padding:1.5rem;">
                            No sections yet. Click "New Section" to create one.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($sections as $sec): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($sec['program_course']); ?></strong></td>
                        <td><span class="mpl-status scheduled"><?php echo htmlspecialchars($sec['year_section']); ?></span></td>
                        <td><?php echo htmlspecialchars($sec['school_year']); ?></td>
                        <td><?php echo htmlspecialchars($sec['semester']); ?></td>
                        <td><?php echo (int)$sec['student_count']; ?></td>
                        <td>
                            <div class="mpl-actions">
                                <a href="section-assignment-tool.php?section_id=<?php echo (int)$sec['id']; ?>" title="Manage" aria-label="Manage"><i class="fas fa-cog"></i></a>
                                <a class="danger" href="javascript:void(0)" onclick="deleteSection(<?php echo (int)$sec['id']; ?>, '<?php echo htmlspecialchars(addslashes($sec['program_course'] . ' - ' . $sec['year_section']), ENT_QUOTES); ?>')" title="Delete" aria-label="Delete"><i class="fas fa-trash"></i></a>
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

    <!-- ============ SECTION DETAIL: assigned students ============ -->
    <div class="mpl-top">
        <p><?php echo htmlspecialchars($section['program_course'] . ' - ' . $section['year_section']); ?> &middot;
           <?php echo htmlspecialchars($section['school_year']); ?> &middot;
           <?php echo htmlspecialchars($section['semester']); ?>
           <?php if (!empty($section['adviser_name'])): ?> &middot; Adviser: <?php echo htmlspecialchars($section['adviser_name']); ?><?php endif; ?>
        </p>
        <div class="mpl-toolbar">
            <a class="mpl-add" href="javascript:void(0)" onclick="showAssignPanel()">
                <i class="fas fa-user-plus" aria-hidden="true"></i> Assign Student
            </a>
        </div>
    </div>

    <!-- Assign-student search (hidden until "Assign Student" is clicked) -->
    <div id="assignPanel" style="display:none; background:transparent; border:1px solid rgba(13,110,253,.2); border-radius:8px; padding:1rem; margin-bottom:1.5rem;">
        <label style="display:block; font-weight:600; margin-bottom:.5rem;">Search a student to assign</label>
        <input type="search" id="assignSearchInput" class="form-control form-control-lg" placeholder="Student number or name..." autocomplete="off">
        <div id="assignSearchResults" class="mt-3"></div>
    </div>

    <section class="mpl-panel">
        <div class="mpl-panel-head">
            <div>
                <h2>Assigned Students</h2>
                <p>Students currently enrolled in this section.</p>
            </div>
        </div>
        <div class="mpl-table-wrap">
            <table class="mpl-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Home Program / Year</th>
                        <th>Status</th>
                        <th>Assigned</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="assignmentRows">
                    <?php if (empty($assignments)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center;color:var(--sms-text-muted);padding:1.5rem;">
                            No students assigned to this section yet.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($assignments as $a):
                        $fullName = $a['first_name'] . ' ' . $a['last_name'];
                    ?>
                    <tr>
                        <td>
                            <div class="mpl-person">
                                <span class="mpl-avatar"><?php echo htmlspecialchars(schInitials($fullName)); ?></span>
                                <div>
                                    <strong><?php echo htmlspecialchars($a['last_name'] . ', ' . $a['first_name']); ?></strong>
                                    <small><?php echo htmlspecialchars($a['student_number']); ?></small>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars(($a['program_course'] ?? '-') . ' / ' . ($a['student_year_section'] ?? '-')); ?></td>
                        <td><span class="mpl-status completed"><?php echo htmlspecialchars($a['status']); ?></span></td>
                        <td><small><?php echo htmlspecialchars(date('M j, Y', strtotime($a['assigned_at']))); ?></small></td>
                        <td>
                            <div class="mpl-actions">
                                <a class="danger" href="javascript:void(0)" onclick="unassignStudent(<?php echo (int)$a['id']; ?>, '<?php echo htmlspecialchars(addslashes($fullName), ENT_QUOTES); ?>')" title="Remove" aria-label="Remove"><i class="fas fa-user-minus"></i></a>
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

<!-- Add Section Modal -->
<div class="modal fade" id="sectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="sectionForm" onsubmit="return handleSectionForm(event)">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Program *</label>
                        <input type="text" name="program_course" id="fProgram" class="form-control" required placeholder="e.g., BS Information Technology">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Year &amp; Section *</label>
                            <input type="text" name="year_section" id="fYearSection" class="form-control" required placeholder="e.g., II-A">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Section Code</label>
                            <input type="text" name="section_code" id="fSectionCode" class="form-control" placeholder="Optional label">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">School Year *</label>
                            <input type="text" name="school_year" id="fSchoolYear" class="form-control" required placeholder="e.g., 2026-2027">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Semester</label>
                            <select name="semester" id="fSemester" class="form-select">
                                <option value="1st Semester">1st Semester</option>
                                <option value="2nd Semester">2nd Semester</option>
                                <option value="Summer">Summer</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Adviser</label>
                            <input type="text" name="adviser_name" id="fAdviser" class="form-control" placeholder="Optional">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Max Students</label>
                            <input type="number" name="max_students" id="fMaxStudents" class="form-control" min="1" placeholder="Optional">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Section</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const API_BASE = '<?php echo BASE_URL; ?>/modules/scheduling/api';
const REG_API_BASE = '<?php echo BASE_URL; ?>/modules/registrar/api'; // reused for the student search picker
const CSRF = '<?= e(csrfToken()) ?>';

function debounce(fn, delay) {
    let t;
    return function (...args) {
        clearTimeout(t);
        t = setTimeout(() => fn.apply(this, args), delay);
    };
}

function schShowAlert(type, message) {
    const box = document.getElementById('schAlertBox');
    box.innerHTML = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
        message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function postJson(url, payload) {
    return fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF },
        body: JSON.stringify(payload || {})
    }).then(r => r.json());
}

/* ============ Sections dashboard ============ */
function openAddSectionModal() {
    document.getElementById('sectionForm').reset();
    new bootstrap.Modal(document.getElementById('sectionModal')).show();
}

async function handleSectionForm(e) {
    e.preventDefault();
    const form = document.getElementById('sectionForm');
    const data = Object.fromEntries(new FormData(form));

    try {
        const result = await postJson(API_BASE + '/sections.php?action=save', data);
        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('sectionModal'))?.hide();
            schShowAlert('success', result.message || 'Section created.');
            setTimeout(() => location.reload(), 900);
        } else {
            schShowAlert('danger', result.error || 'Failed to create section.');
        }
    } catch (err) {
        schShowAlert('danger', 'Error: ' + err.message);
    }
    return false;
}

async function deleteSection(id, label) {
    if (!confirm('Delete section "' + label + '"? This also removes all student assignments in it.')) return;
    try {
        const result = await postJson(API_BASE + '/sections.php?action=delete', { id });
        if (result.success) {
            schShowAlert('success', 'Section deleted.');
            setTimeout(() => location.reload(), 900);
        } else {
            schShowAlert('danger', result.error || 'Delete failed.');
        }
    } catch (err) {
        schShowAlert('danger', 'Error: ' + err.message);
    }
}

/* ============ Section detail: assign / unassign students ============ */
<?php if ($section): ?>
const sectionId = <?php echo (int) $sectionId; ?>;

function showAssignPanel() {
    const panel = document.getElementById('assignPanel');
    panel.style.display = panel.style.display === 'none' ? '' : 'none';
    document.getElementById('assignSearchInput').focus();
}

const assignInput = document.getElementById('assignSearchInput');
const assignResults = document.getElementById('assignSearchResults');

assignInput.addEventListener('input', debounce(async function () {
    const q = assignInput.value.trim();
    if (q.length < 2) {
        assignResults.innerHTML = '<p class="text-muted mb-0">Keep typing (at least 2 characters).</p>';
        return;
    }
    assignResults.innerHTML = '<p class="text-muted mb-0"><i class="fas fa-spinner fa-spin"></i> Searching...</p>';

    try {
        // Cross-module fetch: this page (Class Scheduling) reads from the Registrar
        // module's student-search API to find who to assign.
        const response = await fetch(REG_API_BASE + '/students.php?action=search&q=' + encodeURIComponent(q) + '&limit=8');
        const result = await response.json();

        if (!result.success || !result.data || result.data.length === 0) {
            assignResults.innerHTML = '<p class="text-muted mb-0">No matching students found.</p>';
            return;
        }

        let html = '<div class="mpl-table-wrap"><table class="mpl-table"><tbody>';
        result.data.forEach(function (s) {
            html += '<tr>' +
                '<td><span class="badge bg-primary">' + s.student_number + '</span> <strong>' + s.last_name + ', ' + s.first_name + '</strong></td>' +
                '<td>' + (s.program_course || '-') + '</td>' +
                '<td><button type="button" class="btn btn-sm btn-primary" onclick="assignStudent(' + s.id + ')">' +
                '<i class="fas fa-user-plus"></i> Assign</button></td>' +
                '</tr>';
        });
        html += '</tbody></table></div>';
        assignResults.innerHTML = html;
    } catch (err) {
        assignResults.innerHTML = '<p class="text-danger mb-0">Error searching students: ' + err.message + '</p>';
    }
}, 300));

async function assignStudent(studentId) {
    try {
        const result = await postJson(API_BASE + '/assignments.php?action=assign', { section_id: sectionId, student_id: studentId });
        if (result.success) {
            schShowAlert('success', result.message || 'Student assigned.');
            setTimeout(() => location.reload(), 900);
        } else {
            schShowAlert('danger', result.error || 'Failed to assign student.');
        }
    } catch (err) {
        schShowAlert('danger', 'Error: ' + err.message);
    }
}

async function unassignStudent(assignmentId, name) {
    if (!confirm('Remove ' + name + ' from this section?')) return;
    try {
        const result = await postJson(API_BASE + '/assignments.php?action=unassign', { id: assignmentId });
        if (result.success) {
            schShowAlert('success', 'Student removed.');
            setTimeout(() => location.reload(), 900);
        } else {
            schShowAlert('danger', result.error || 'Failed to remove.');
        }
    } catch (err) {
        schShowAlert('danger', 'Error: ' + err.message);
    }
}
<?php endif; ?>
</script>

<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>