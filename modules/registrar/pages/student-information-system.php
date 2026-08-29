<?php
/**
 * SMS 2 - Student Information System
 * Module: Registrar
 * Manage student profiles with full CRUD operations.
 *
 * Visual design uses the app's shared "process list" design tokens
 * (assets/css/module-process-list.css, class prefix mpl-) so this page
 * matches the rest of the system's stat cards / filter bar / table look
 * and gets dark & light theme support for free via [data-theme].
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/registrar-service.php';

regRequireAction('registrar.view');

$pageTitle    = 'Student Information System';
$activeModule = 'registrar';
$activePage   = 'student-information-system';
$breadcrumbs  = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Student Information System', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';

$db = db();

// All active/inactive/graduated students, client-side filtered (dataset is small enough
// that a live filter is more responsive than server round-trips per keystroke).
$students = $db->query("
    SELECT * FROM `reg_students`
    WHERE `status` != 'Deleted'
    ORDER BY `last_name`, `first_name`
    LIMIT 500
")->fetchAll(PDO::FETCH_ASSOC);

$total      = count($students);
$activeCount     = count(array_filter($students, fn($s) => $s['status'] === 'Active'));
$inactiveCount   = count(array_filter($students, fn($s) => $s['status'] === 'Inactive'));
$graduatedCount  = count(array_filter($students, fn($s) => $s['status'] === 'Graduated'));

$programCounts = $db->query("
    SELECT `program_course`, COUNT(*) AS cnt FROM `reg_students`
    WHERE `status` != 'Deleted' AND `program_course` IS NOT NULL AND `program_course` != ''
    GROUP BY `program_course`
    ORDER BY `program_course`
")->fetchAll(PDO::FETCH_ASSOC);

// Keep a flat list too (used by the Program filter dropdown).
$programs = array_column($programCounts, 'program_course');

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

// Infers a year level label from the free-text `year_section` field
// (seed data uses Roman-numeral prefixes like "I-A", "II-B", "III-C", "IV-A";
// this also tolerates a plain leading digit like "1-A").
if (!function_exists('regInferYearLevel')) {
    function regInferYearLevel(?string $yearSection): string
    {
        $prefix = strtoupper(trim((string) $yearSection));
        if (preg_match('/^(IV|III|II|I|[1-4])\b/', $prefix, $m)) {
            $map = ['I' => '1st Year', 'II' => '2nd Year', 'III' => '3rd Year', 'IV' => '4th Year',
                    '1' => '1st Year', '2' => '2nd Year', '3' => '3rd Year', '4' => '4th Year'];
            return $map[$m[1]] ?? 'Other';
        }
        return 'Other';
    }
}

// Active/Inactive/Graduated map onto the shared status-pill palette already
// defined in module-process-list.css (no new CSS needed).
$statusPillClass = [
    'Active'    => 'active',     // purple
    'Inactive'  => 'cancelled',  // gray
    'Graduated' => 'completed',  // green
];
?>

<link href="<?php echo BASE_URL; ?>/assets/css/module-process-list.css?v=2" rel="stylesheet">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/modules/registrar/assets/css/registrar.css">

<?php renderBreadcrumbs($breadcrumbs); ?>

<div class="container-fluid py-4 student-information-page">
<div class="mpl" data-mpl>

    <div class="mpl-top">
        <p>Manage student profiles, academic status, and personal information.</p>
        <div class="mpl-toolbar">
            <a class="mpl-add" href="javascript:void(0)" onclick="openAddModal()">
                <i class="fas fa-plus" aria-hidden="true"></i> New Student
            </a>
        </div>
    </div>

    <!-- Stats -->
    <section class="mpl-stats" aria-label="Student summary">
        <article class="mpl-stat">
            <div class="mpl-stat-icon blue"><i class="fas fa-users"></i></div>
            <div>
                <span>Total Students</span>
                <strong><?php echo $total; ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div>
                <span>Active</span>
                <strong><?php echo $activeCount; ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon amber"><i class="fas fa-pause-circle"></i></div>
            <div>
                <span>Inactive</span>
                <strong><?php echo $inactiveCount; ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon purple"><i class="fas fa-graduation-cap"></i></div>
            <div>
                <span>Graduated</span>
                <strong><?php echo $graduatedCount; ?></strong>
            </div>
        </article>
    </section>

    <!-- Browse by Program -->
    <?php if (!empty($programCounts)): ?>
    <section class="mpl-panel mb-3">
        <div class="mpl-panel-head">
            <div>
                <h2>Browse by Program</h2>
                <p>Click a program to jump straight to its students, or drill into a specific year level.</p>
            </div>
        </div>
        <div class="row g-2 px-3 pb-3">
            <?php foreach ($programCounts as $pc): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="javascript:void(0)" class="text-decoration-none d-block"
                   onclick="selectProgram('<?php echo htmlspecialchars(addslashes(strtolower($pc['program_course']))); ?>', '<?php echo htmlspecialchars(addslashes($pc['program_course'])); ?>')">
                    <div class="mpl-stat">
                        <div class="mpl-stat-icon blue"><i class="fas fa-graduation-cap"></i></div>
                        <div>
                            <span><?php echo htmlspecialchars($pc['program_course']); ?></span>
                            <strong><?php echo (int)$pc['cnt']; ?> student<?php echo (int)$pc['cnt'] === 1 ? '' : 's'; ?></strong>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Browse by Year Level (revealed once a program is selected above) -->
    <section class="mpl-panel mb-3" id="yearLevelPanel" style="display:none;">
        <div class="mpl-panel-head">
            <div>
                <h2 id="yearLevelHeading">Browse by Year Level</h2>
                <p>Click a year level to narrow the list further.</p>
            </div>
        </div>
        <div class="row g-2 px-3 pb-3" id="yearLevelCards"></div>
    </section>

    <!-- Filters -->
    <div class="mpl-filters">
        <label class="mpl-search">
            <i class="fas fa-search"></i>
            <input type="search" id="mplSearch" placeholder="Search by student number, name, or program..." aria-label="Search students">
        </label>
        <select id="mplStatus" aria-label="Filter by status">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="graduated">Graduated</option>
        </select>
        <select id="mplProgram" aria-label="Filter by program">
            <option value="">All Programs</option>
            <?php foreach ($programs as $prog): ?>
            <option value="<?php echo htmlspecialchars(strtolower($prog)); ?>"><?php echo htmlspecialchars($prog); ?></option>
            <?php endforeach; ?>
        </select>
        <a class="mpl-refresh" href="?"><i class="fas fa-sync-alt" aria-hidden="true"></i> Refresh</a>
    </div>

    <!-- Table -->
    <section class="mpl-panel" id="studentsTablePanel">
        <div class="mpl-panel-head">
            <div>
                <h2>Student Records</h2>
                <p>View and manage all student master records.</p>
            </div>
        </div>

        <div class="mpl-table-wrap">
            <table class="mpl-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Program</th>
                        <th>Year &amp; Section</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="mplRows">
                    <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center;color:var(--sms-text-muted);padding:1.5rem;">
                            No students found.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($students as $student):
                        $fullName = trim($student['first_name'] . ' ' . $student['last_name']);
                        $status = $student['status'];
                        $pillClass = $statusPillClass[$status] ?? 'cancelled';
                        $searchBlob = strtolower(
                            $student['student_number'] . ' ' . $fullName . ' ' . ($student['program_course'] ?? '')
                        );
                    ?>
                    <tr data-search="<?php echo htmlspecialchars($searchBlob); ?>"
                        data-status="<?php echo htmlspecialchars(strtolower($status)); ?>"
                        data-program="<?php echo htmlspecialchars(strtolower($student['program_course'] ?? '')); ?>"
                        data-year="<?php echo htmlspecialchars(strtolower(regInferYearLevel($student['year_section'] ?? ''))); ?>">
                        <td>
                            <div class="mpl-person">
                                <span class="mpl-avatar"><?php echo htmlspecialchars(regInitials($fullName)); ?></span>
                                <div>
                                    <strong><?php echo htmlspecialchars($student['last_name'] . ', ' . $student['first_name']); ?></strong>
                                    <small><?php echo htmlspecialchars($student['student_number']); ?></small>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($student['program_course'] ?? '—'); ?></td>
                        <td><?php echo htmlspecialchars($student['year_section'] ?? '—'); ?></td>
                        <td><span class="mpl-status <?php echo $pillClass; ?>"><?php echo htmlspecialchars($status); ?></span></td>
                        <td>
                            <div class="student-actions">
                            <div class="mpl-actions mb-1">
                                <a href="javascript:void(0)" onclick="viewStudent(<?php echo (int)$student['id']; ?>)" title="View" aria-label="View"><i class="fas fa-eye"></i></a>
                                <a href="javascript:void(0)" onclick="openEditModal(<?php echo (int)$student['id']; ?>)" title="Edit" aria-label="Edit"><i class="fas fa-pen"></i></a>
                                <a class="danger" href="javascript:void(0)" onclick="deleteStudent(<?php echo (int)$student['id']; ?>)" title="Delete" aria-label="Delete"><i class="fas fa-trash"></i></a>
                            </div>
                            <div class="mpl-actions">
                                <a href="guardian-emergency-contact.php?student_id=<?php echo (int)$student['id']; ?>" title="Guardian & Emergency Contact" aria-label="Guardian & Emergency Contact"><i class="fas fa-phone-alt"></i></a>
                                <a href="persona-file-database.php?student_id=<?php echo (int)$student['id']; ?>" title="Persona File Database" aria-label="Persona File Database"><i class="fas fa-database"></i></a>
                                <a href="academic-history.php?student_id=<?php echo (int)$student['id']; ?>" title="Academic History" aria-label="Academic History"><i class="fas fa-history"></i></a>
                                <a href="digital-file-storage.php?student_id=<?php echo (int)$student['id']; ?>" title="Digital File Storage" aria-label="Digital File Storage"><i class="fas fa-folder-open"></i></a>
                            </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mpl-foot">
            <span class="meta" id="mplMeta">Showing <?php echo $total; ?> of <?php echo $total; ?> records</span>
        </div>
    </section>

</div>
</div>

<!-- Student Information Modal -->
<div class="modal fade" id="studentInfoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm student-info-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="student-info-heading">
                    <span class="mpl-avatar" id="infoInitials">ST</span>
                    <div>
                        <strong id="infoName">Student Name</strong>
                        <small id="infoNumber">Student number</small>
                    </div>
                </div>
                <dl class="student-info-list">
                    <div><dt>Program</dt><dd id="infoProgram">-</dd></div>
                    <div><dt>Year &amp; Section</dt><dd id="infoYearSection">-</dd></div>
                    <div><dt>Status</dt><dd id="infoStatus">-</dd></div>
                    <div><dt>Date of Birth</dt><dd id="infoDob">-</dd></div>
                    <div><dt>Gender</dt><dd id="infoGender">-</dd></div>
                </dl>
            </div>
            <div class="student-info-footer">
                <button type="button" class="btn btn-secondary student-info-close" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Student Modal -->
<div class="modal fade" id="studentModalAdd" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered student-edit-dialog">
        <div class="modal-content">
            <form id="studentForm" onsubmit="return handleStudentForm(event)">
                <div class="modal-body student-edit-body">
                    <input type="hidden" name="id" id="studentId" value="">
                    <div class="reg-form-section">
                        <h5>Personal Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Student Number *</label>
                                    <input type="text" name="student_number" id="fStudentNumber" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Status *</label>
                                    <select name="status" id="fStatus" class="form-select" required>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                        <option value="Graduated">Graduated</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="reg-form-group">
                                    <label>First Name *</label>
                                    <input type="text" name="first_name" id="fFirstName" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="reg-form-group">
                                    <label>Middle Name</label>
                                    <input type="text" name="middle_name" id="fMiddleName" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="reg-form-group">
                                    <label>Last Name *</label>
                                    <input type="text" name="last_name" id="fLastName" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Date of Birth</label>
                                    <input type="date" name="date_of_birth" id="fDob" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Gender</label>
                                    <select name="gender" id="fGender" class="form-select">
                                        <option value="">Select</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="reg-form-section">
                        <h5>Academic Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Program</label>
                                    <input type="text" name="program_course" id="fProgram" class="form-control" placeholder="e.g., BS Computer Science">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Year &amp; Section</label>
                                    <input type="text" name="year_section" id="fYearSection" class="form-control" placeholder="e.g., 2nd Year - A">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary student-form-button" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-reg-primary student-form-button">Save Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>/modules/registrar/assets/js/registrar.js"></script>
<script>
const API_BASE = '<?php echo BASE_URL; ?>/modules/registrar/api';
const CSRF = '<?= e(csrfToken()) ?>';

// Records embedded server-side so Edit can populate the form without a round trip.
const studentRecords = <?php echo json_encode($students, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

/* ============ Live filter (search + status + program), no page reload ============ */
let currentYear = ''; // set only via the Year Level cards (no dropdown for this one)
let applyFilters = function () {};

(function () {
    const search = document.getElementById('mplSearch');
    const status = document.getElementById('mplStatus');
    const program = document.getElementById('mplProgram');
    const rows = document.querySelectorAll('#mplRows tr[data-search]');
    const meta = document.getElementById('mplMeta');
    const total = <?php echo $total; ?>;

    applyFilters = function () {
        const q = (search.value || '').toLowerCase().trim();
        const st = (status.value || '').toLowerCase();
        const pr = (program.value || '').toLowerCase();
        const yr = (currentYear || '').toLowerCase();
        let visible = 0;

        rows.forEach(function (row) {
            const hay = row.getAttribute('data-search') || '';
            const rowStatus = row.getAttribute('data-status') || '';
            const rowProgram = row.getAttribute('data-program') || '';
            const rowYear = row.getAttribute('data-year') || '';
            const matchQ = !q || hay.includes(q);
            const matchS = !st || rowStatus === st;
            const matchP = !pr || rowProgram === pr;
            const matchY = !yr || rowYear === yr;
            const show = matchQ && matchS && matchP && matchY;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        meta.textContent = 'Showing ' + visible + ' of ' + total + ' records';
    };

    search.addEventListener('input', debounce(applyFilters, 150));
    status.addEventListener('change', applyFilters);
    program.addEventListener('change', function () {
        // Manually changing the Program dropdown (not via a card) hides the
        // year-level drill-down, since it no longer matches a chosen program.
        currentYear = '';
        document.getElementById('yearLevelPanel').style.display = 'none';
        applyFilters();
    });
})();

/* ============ Year-level inference (mirrors regInferYearLevel() in PHP) ============ */
function inferYearLevel(yearSection) {
    const prefix = (yearSection || '').toUpperCase().trim();
    const m = prefix.match(/^(IV|III|II|I|[1-4])\b/);
    if (!m) return 'Other';
    const map = { I: '1st Year', II: '2nd Year', III: '3rd Year', IV: '4th Year',
                  '1': '1st Year', '2': '2nd Year', '3': '3rd Year', '4': '4th Year' };
    return map[m[1]] || 'Other';
}

/* ============ "Browse by Program" cards: filter + reveal the Year Level drill-down ============ */
function selectProgram(programLower, programLabel) {
    const programSelect = document.getElementById('mplProgram');
    programSelect.value = programLower;
    currentYear = '';
    applyFilters();

    // Build year-level counts for this program from the records already on the page
    // (no extra request needed -- studentRecords is already embedded for Edit).
    const counts = { '1st Year': 0, '2nd Year': 0, '3rd Year': 0, '4th Year': 0, 'Other': 0 };
    let programTotal = 0;
    studentRecords.forEach(function (s) {
        if ((s.program_course || '').toLowerCase() === programLower) {
            counts[inferYearLevel(s.year_section)]++;
            programTotal++;
        }
    });

    const heading = document.getElementById('yearLevelHeading');
    heading.textContent = 'Browse ' + programLabel + ' by Year Level';

    const cardsBox = document.getElementById('yearLevelCards');
    let html = '';
    ['1st Year', '2nd Year', '3rd Year', '4th Year', 'Other'].forEach(function (label) {
        const count = counts[label];
        if (count === 0) return; // skip empty buckets, nothing to click into
        html += '<div class="col-6 col-md-3">' +
            '<a href="javascript:void(0)" class="text-decoration-none d-block" onclick="filterByYear(\'' + label.toLowerCase() + '\')">' +
            '<div class="mpl-stat">' +
            '<div class="mpl-stat-icon green"><i class="fas fa-layer-group"></i></div>' +
            '<div><span>' + label + '</span><strong>' + count + ' student' + (count === 1 ? '' : 's') + '</strong></div>' +
            '</div></a></div>';
    });

    cardsBox.innerHTML = html || '<div class="col-12"><p class="text-muted mb-0 px-2">No year-level data for this program.</p></div>';
    document.getElementById('yearLevelPanel').style.display = programTotal > 0 ? '' : 'none';

    document.getElementById('yearLevelPanel')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function filterByYear(yearLower) {
    currentYear = yearLower;
    applyFilters();
    document.getElementById('studentsTablePanel')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

/* ============ Add / Edit ============ */
function resetStudentForm() {
    document.getElementById('studentForm').reset();
    document.getElementById('studentId').value = '';
}

function openAddModal() {
    resetStudentForm();
    showRegModal('studentModalAdd');
}

function openEditModal(studentId) {
    const record = studentRecords.find(s => parseInt(s.id, 10) === studentId);
    if (!record) {
        showRegError('Student record not found');
        return;
    }

    document.getElementById('studentId').value = record.id;
    document.getElementById('fStudentNumber').value = record.student_number || '';
    document.getElementById('fStatus').value = record.status || 'Active';
    document.getElementById('fFirstName').value = record.first_name || '';
    document.getElementById('fMiddleName').value = record.middle_name || '';
    document.getElementById('fLastName').value = record.last_name || '';
    document.getElementById('fDob').value = record.date_of_birth || '';
    document.getElementById('fGender').value = record.gender || '';
    document.getElementById('fProgram').value = record.program_course || '';
    document.getElementById('fYearSection').value = record.year_section || '';

    showRegModal('studentModalAdd');
}

async function handleStudentForm(e) {
    e.preventDefault();
    const form = document.getElementById('studentForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);

    if (!data.id) {
        delete data.id;
    }

    try {
        const response = await fetch(API_BASE + '/students.php?action=save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': CSRF
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showRegSuccess(result.message || 'Student saved');
            form.reset();
            hideRegModal('studentModalAdd');
            setTimeout(() => location.reload(), 1200);
        } else {
            showRegError(result.error || 'Failed to save student');
        }
    } catch (error) {
        console.error(error);
        showRegError('Error: ' + error.message);
    }

    return false;
}

async function viewStudent(studentId) {
    try {
        const response = await fetch(API_BASE + '/students.php?action=get', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id: studentId})
        });

        const result = await response.json();

        if (result.success) {
            const student = result.data;
            const fullName = [student.first_name, student.middle_name, student.last_name, student.suffix].filter(Boolean).join(' ');
            document.getElementById('infoInitials').textContent = (student.first_name?.[0] || 'S') + (student.last_name?.[0] || 'T');
            document.getElementById('infoName').textContent = fullName || 'Student Name';
            document.getElementById('infoNumber').textContent = student.student_number || '-';
            document.getElementById('infoProgram').textContent = student.program_course || '-';
            document.getElementById('infoYearSection').textContent = student.year_section || '-';
            document.getElementById('infoStatus').textContent = student.status || '-';
            document.getElementById('infoDob').textContent = student.date_of_birth || '-';
            document.getElementById('infoGender').textContent = student.gender || '-';
            showRegModal('studentInfoModal');
        }
    } catch (error) {
        console.error(error);
        showRegError('Error loading student');
    }
}

function deleteStudent(studentId) {
    const row = document.querySelector('a[onclick="deleteStudent(' + studentId + ')"]')?.closest('tr');
    const studentName = row?.querySelector('.mpl-person strong')?.textContent || 'this student';
    if (!confirm('Delete ' + studentName + '? The student will be hidden from active records.')) return;

    fetch(API_BASE + '/students.php?action=delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': CSRF
        },
        body: JSON.stringify({id: studentId})
    }).then(response => response.json()).then(result => {
        if (!result.success) throw new Error(result.error || 'Failed to delete student');
        showRegSuccess(result.message || 'Student deleted');
        setTimeout(() => location.reload(), 700);
    }).catch(error => {
        console.error(error);
        showRegError(error.message);
    });
}
</script>

<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>