<?php
/**
 * SMS 2 - Student Information System
 * Module: Registrar
 * Manage student profiles with full CRUD operations
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

// Get pagination params
$page = (int)($_GET['page'] ?? 1);
$search = $_GET['search'] ?? '';
$limit = 20;
$offset = ($page - 1) * $limit;

// Fetch students
$db = db();
$students = [];
$total = 0;

if ($search) {
    $results = regSearchStudents($search, $limit);
    $students = $results;
    $total = count($results);
} else {
    $stmt = $db->prepare("SELECT * FROM `reg_students` 
        WHERE `status` != 'Deleted' 
        ORDER BY `last_name`, `first_name` 
        LIMIT ? OFFSET ?");
    $stmt->execute([$limit, $offset]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $db->query("SELECT COUNT(*) FROM `reg_students` WHERE `status` != 'Deleted'");
    $total = (int)$stmt->fetchColumn();
}

$totalPages = ceil($total / $limit);
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/modules/registrar/assets/css/registrar.css">
<style>
/* Match the search/filter box to the blue accent design used across the module, and drop the hardcoded white background. */
.reg-search-box {
    background: transparent;
    box-shadow: none;
    border: 1px solid rgba(13,110,253,.2);
}
.reg-search-box .form-control:focus,
.reg-search-box .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13,110,253,.25);
}
.reg-search-box .btn-reg-primary {
    background-color: #0d6efd;
}
.reg-search-box .btn-reg-primary:hover {
    background-color: #0b5ed7;
}
</style>

<?php renderBreadcrumbs($breadcrumbs); ?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-dark">Student Information System</h1>
            <p class="text-muted">Manage student profiles and personal information</p>
        </div>
        <button class="btn btn-primary" onclick="showRegModal('studentModalAdd')">
            <i class="fas fa-plus"></i> Add Student
        </button>
    </div>

    <!-- Search & Filter -->
    <div class="reg-search-box">
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" 
                    placeholder="Search by student number, name, email..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                    <option value="Graduated">Graduated</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-reg-primary w-100">Search</button>
                <a href="?page=1" class="btn btn-secondary w-100 mt-2">Clear</a>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="reg-stat-card">
                <p class="stat-value"><?php echo $total; ?></p>
                <p class="stat-label">Total Students</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="reg-stat-card success">
                <p class="stat-value">
                    <?php
                    $stmt = $db->query("SELECT COUNT(*) FROM `reg_students` WHERE `status` = 'Active'");
                    echo $stmt->fetchColumn();
                    ?>
                </p>
                <p class="stat-label">Active Students</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="reg-stat-card warning">
                <p class="stat-value">
                    <?php
                    $stmt = $db->query("SELECT COUNT(*) FROM `reg_students` WHERE `status` = 'Inactive'");
                    echo $stmt->fetchColumn();
                    ?>
                </p>
                <p class="stat-label">Inactive</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="reg-stat-card success">
                <p class="stat-value">
                    <?php
                    $stmt = $db->query("SELECT COUNT(*) FROM `reg_students` WHERE `status` = 'Graduated'");
                    echo $stmt->fetchColumn();
                    ?>
                </p>
                <p class="stat-label">Graduated</p>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card reg-shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Student Records (<?php echo $total; ?> total)</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover reg-table mb-0">
                <thead>
                    <tr>
                        <th>Student #</th>
                        <th>Name</th>
                        <th>Program</th>
                        <th>Year</th>
                        <th>Status</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <p class="text-muted mb-0">No students found</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($students as $student): ?>
                    <tr>
                        <td>
                            <span class="badge bg-primary"><?php echo htmlspecialchars($student['student_number']); ?></span>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($student['last_name'] . ', ' . $student['first_name']); ?></strong>
                            <br>
                            <small class="text-muted"><?php echo htmlspecialchars($student['middle_name'] ?? ''); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($student['program_course'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($student['year_section'] ?? '-'); ?></td>
                        <td>
                            <span class="badge-status <?php echo strtolower($student['status']); ?>">
                                <?php echo htmlspecialchars($student['status']); ?>
                            </span>
                        </td>
                        <td>
                            <small>
                                <i class="fas fa-id-badge"></i> <?php echo htmlspecialchars($student['student_number']); ?><br>
                                <i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($student['program_course'] ?? '-'); ?>
                            </small>
                        </td>
                        <td>
                            <div class="btn-group mb-1" role="group">
                                <button class="btn btn-sm btn-info" onclick="viewStudent(<?php echo $student['id']; ?>)" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="editStudent(<?php echo $student['id']; ?>)" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteStudent(<?php echo $student['id']; ?>)" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="btn-group" role="group">
                                <a class="btn btn-sm btn-outline-secondary" href="guardian-emergency-contact.php?student_id=<?php echo (int)$student['id']; ?>" title="Guardian & Emergency Contact">
                                    <i class="fas fa-users"></i>
                                </a>
                                <a class="btn btn-sm btn-outline-secondary" href="persona-file-database.php?student_id=<?php echo (int)$student['id']; ?>" title="Persona File Database">
                                    <i class="fas fa-folder-open"></i>
                                </a>
                                <a class="btn btn-sm btn-outline-secondary" href="academic-history.php?student_id=<?php echo (int)$student['id']; ?>" title="Academic History">
                                    <i class="fas fa-history"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=1&search=<?php echo urlencode($search); ?>">First</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">Previous</a>
            </li>
            <?php endif; ?>

            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">
                    <?php echo $i; ?>
                </a>
            </li>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Next</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $totalPages; ?>&search=<?php echo urlencode($search); ?>">Last</a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<!-- Add/Edit Student Modal -->
<div class="modal fade" id="studentModalAdd" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="reg-modal-header">
                <h5 class="modal-title">Add New Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="studentForm" onsubmit="return handleStudentForm(event)">
                <div class="modal-body">
                    <div class="reg-form-section">
                        <h5>Personal Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Student Number *</label>
                                    <input type="text" name="student_number" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Status *</label>
                                    <select name="status" class="form-select" required>
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
                                    <input type="text" name="first_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="reg-form-group">
                                    <label>Middle Name</label>
                                    <input type="text" name="middle_name" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="reg-form-group">
                                    <label>Last Name *</label>
                                    <input type="text" name="last_name" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Date of Birth</label>
                                    <input type="date" name="date_of_birth" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Gender</label>
                                    <select name="gender" class="form-select">
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
                                    <input type="text" name="program" class="form-control" placeholder="e.g., BS Computer Science">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Year & Section</label>
                                    <input type="text" name="year_section" class="form-control" placeholder="e.g., 2nd Year - A">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="reg-form-section">
                        <h5>Contact Information</h5>
                        <div class="reg-form-group">
                            <label>Email *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Phone Number</label>
                                    <input type="tel" name="phone" class="form-control" placeholder="+63 9XX XXX XXXX">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label>Alternate Phone</label>
                                    <input type="tel" name="alternate_phone" class="form-control">
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
                    <button type="submit" class="btn btn-reg-primary">Save Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>/modules/registrar/assets/js/registrar.js"></script>
<script>
const API_BASE = '<?php echo BASE_URL; ?>/modules/registrar/api';

async function handleStudentForm(e) {
    e.preventDefault();
    const form = document.getElementById('studentForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch(API_BASE + '/students.php?action=save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?= e(csrfToken()) ?>'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showRegSuccess(result.message);
            form.reset();
            hideRegModal('studentModalAdd');
            setTimeout(() => location.reload(), 1500);
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
            alert(`Student: ${student.first_name} ${student.last_name}\nStudent No: ${student.student_number}\nProgram: ${student.program_course || '-'}\nStatus: ${student.status}`);
        }
    } catch (error) {
        console.error(error);
        showRegError('Error loading student');
    }
}

function editStudent(studentId) {
    showRegSuccess('Edit functionality coming soon');
}

function deleteStudent(studentId) {
    if (!confirm('Are you sure?')) return;
    showRegSuccess('Delete functionality coming soon');
}
</script>

<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>