<?php
/**
 * SMS 2 - Online Pre-registration
 * Module: Enrollment Management
 */
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

$pageTitle    = 'Online Pre-registration';
$activeModule = 'enrollment';
$activePage   = 'online-pre-registration';
$breadcrumbs  = [
    ['label' => 'Enrollment Management', 'url' => BASE_URL . '/modules/enrollment/index.php'],
    ['label' => 'Online Pre-registration', 'url' => null],
];

$pdo = db();
$studentsPending = [];
$studentsArchive = [];
if ($pdo) {
    try {
        $stmtPending = $pdo->query("SELECT * FROM reg_students WHERE status = 'Pending' OR status IS NULL OR status = '' ORDER BY created_at DESC LIMIT 100");
        $studentsPending = $stmtPending->fetchAll();

        $stmtArchive = $pdo->query("SELECT * FROM reg_students WHERE status IN ('Verified', 'Active') ORDER BY created_at DESC LIMIT 100");
        $studentsArchive = $stmtArchive->fetchAll();
    } catch (PDOException $e) {
        $error = "Error fetching students: " . $e->getMessage();
    }
} else {
    $error = "Database connection not available.";
}

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm mb-4 border-0" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom-0">
                <h5 class="mb-0 text-primary fw-bold">
                    <i class="fas fa-list-alt me-2 text-primary"></i>Online Pre-registered Students
                </h5>
                <a href="<?= BASE_URL ?>/login/student-admission.php" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold shadow-sm" target="_blank" style="transition: all 0.2s;">
                    <i class="fas fa-external-link-alt me-1"></i>Open Admission Form
                </a>
            </div>
            <div class="card-body bg-white pt-0">
                <div class="alert alert-primary bg-primary bg-opacity-10 border-0 text-primary py-2 mb-4 rounded-3 d-flex align-items-center shadow-sm">
                    <i class="fas fa-info-circle me-3 fs-5"></i>
                    <div>
                        <strong class="d-block mb-0">Automatic Data Flow Sync</strong>
                        <span class="small text-dark text-opacity-75">This table automatically lists students successfully registered from the Student Admission portal.</span>
                    </div>
                </div>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger border-0 shadow-sm rounded-3"><i class="fas fa-exclamation-triangle me-2"></i> <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <!-- Tabs Navigation -->
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill fw-semibold px-4" id="pills-pending-tab" data-bs-toggle="pill" data-bs-target="#pills-pending" type="button" role="tab" aria-controls="pills-pending" aria-selected="true">
                            Pending Registration
                            <span class="badge bg-primary ms-2 rounded-pill"><?= count($studentsPending) ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill fw-semibold px-4" id="pills-archive-tab" data-bs-toggle="pill" data-bs-target="#pills-archive" type="button" role="tab" aria-controls="pills-archive" aria-selected="false">
                            Archive (Confirmed)
                            <span class="badge bg-secondary ms-2 rounded-pill"><?= count($studentsArchive) ?></span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="pills-tabContent">
                    <!-- Pending Tab -->
                    <div class="tab-pane fade show active" id="pills-pending" role="tabpanel" aria-labelledby="pills-pending-tab" tabindex="0">
                        <div class="table-responsive" style="border-radius: 10px; border: 1px solid rgba(0,0,0,0.05);">
                            <table class="table table-hover table-borderless align-middle mb-0">
                                <thead class="table-light text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.5px;">
                                    <tr>
                                        <th class="ps-4 rounded-start">Student No.</th>
                                        <th>Name</th>
                                        <th>Program / Course</th>
                                        <th>Registration Date</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4 rounded-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    <?php if (empty($studentsPending)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <div class="mb-2"><i class="fas fa-folder-open fs-2 text-black-50 opacity-50"></i></div>
                                            <p class="mb-0 fw-semibold">No pending pre-registered students found.</p>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($studentsPending as $s): ?>
                                        <tr style="border-bottom: 1px solid rgba(0,0,0,0.03);">
                                            <td class="fw-bold text-dark ps-4"><?= htmlspecialchars($s['student_number'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 38px; height: 38px; font-weight: 700;">
                                                        <?= strtoupper(substr($s['first_name'] ?? 'U', 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-bold text-dark"><?= htmlspecialchars(($s['first_name'] ?? '') . ' ' . ($s['last_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h6>
                                                        <small class="text-muted"><?= htmlspecialchars($s['gender'] ?? 'Not Specified', ENT_QUOTES, 'UTF-8') ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-medium text-dark text-opacity-75"><?= htmlspecialchars($s['program_course'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></span>
                                            </td>
                                            <td>
                                                <div class="text-secondary small fw-medium">
                                                    <?php
                                                        if (!empty($s['created_at'])) {
                                                            echo '<i class="far fa-calendar-alt me-1"></i>' . date('M d, Y', strtotime($s['created_at'])) . '<br>';
                                                            echo '<small class="text-muted opacity-75 ms-3">' . date('h:i A', strtotime($s['created_at'])) . '</small>';
                                                        } else {
                                                            echo 'N/A';
                                                        }
                                                    ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning text-dark border border-warning border-opacity-25 px-3 py-2 rounded-pill fw-bold">
                                                    <?= htmlspecialchars($s['status'] ?: 'Pending', ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="btn-group shadow-sm rounded-pill">
                                                    <button type="button" class="btn btn-sm btn-light border-0 text-primary fw-semibold px-3 view-student-btn" data-student='<?= htmlspecialchars(json_encode($s), ENT_QUOTES, 'UTF-8') ?>' title="View Details" style="background: rgba(13,110,253,0.05);">
                                                        <i class="fas fa-eye me-1"></i> View
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-primary border-0 fw-semibold px-3 enroll-student-btn" data-id="<?= htmlspecialchars($s['id'], ENT_QUOTES, 'UTF-8') ?>" data-name="<?= htmlspecialchars(($s['first_name'] ?? '') . ' ' . ($s['last_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" title="Proceed to Enrollment">
                                                        <i class="fas fa-check-circle me-1"></i> Enroll
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Archive Tab -->
                    <div class="tab-pane fade" id="pills-archive" role="tabpanel" aria-labelledby="pills-archive-tab" tabindex="0">
                        <div class="table-responsive" style="border-radius: 10px; border: 1px solid rgba(0,0,0,0.05);">
                            <table class="table table-hover table-borderless align-middle mb-0">
                                <thead class="table-light text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.5px;">
                                    <tr>
                                        <th class="ps-4 rounded-start">Student No.</th>
                                        <th>Name</th>
                                        <th>Program / Course</th>
                                        <th>Registration Date</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4 rounded-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    <?php if (empty($studentsArchive)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <div class="mb-2"><i class="fas fa-archive fs-2 text-black-50 opacity-50"></i></div>
                                            <p class="mb-0 fw-semibold">No confirmed students in archive.</p>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($studentsArchive as $s): ?>
                                        <tr style="border-bottom: 1px solid rgba(0,0,0,0.03);">
                                            <td class="fw-bold text-dark ps-4"><?= htmlspecialchars($s['student_number'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 38px; height: 38px; font-weight: 700;">
                                                        <?= strtoupper(substr($s['first_name'] ?? 'U', 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-bold text-dark"><?= htmlspecialchars(($s['first_name'] ?? '') . ' ' . ($s['last_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h6>
                                                        <small class="text-muted"><?= htmlspecialchars($s['gender'] ?? 'Not Specified', ENT_QUOTES, 'UTF-8') ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-medium text-dark text-opacity-75"><?= htmlspecialchars($s['program_course'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></span>
                                            </td>
                                            <td>
                                                <div class="text-secondary small fw-medium">
                                                    <?php
                                                        if (!empty($s['created_at'])) {
                                                            echo '<i class="far fa-calendar-alt me-1"></i>' . date('M d, Y', strtotime($s['created_at'])) . '<br>';
                                                            echo '<small class="text-muted opacity-75 ms-3">' . date('h:i A', strtotime($s['created_at'])) . '</small>';
                                                        } else {
                                                            echo 'N/A';
                                                        }
                                                    ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                    $statusBadgeClass = ($s['status'] === 'Verified') ? 'bg-info bg-opacity-10 text-info border-info' : 'bg-success bg-opacity-10 text-success border-success';
                                                ?>
                                                <span class="badge <?= $statusBadgeClass ?> border border-opacity-25 px-3 py-2 rounded-pill fw-bold">
                                                    <?= htmlspecialchars($s['status'] ?? 'Active', ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="btn-group shadow-sm rounded-pill">
                                                    <button type="button" class="btn btn-sm btn-light border-0 text-primary fw-semibold px-3 view-student-btn" data-student='<?= htmlspecialchars(json_encode($s), ENT_QUOTES, 'UTF-8') ?>' title="View Details" style="background: rgba(13,110,253,0.05);">
                                                        <i class="fas fa-eye me-1"></i> View Details
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-secondary border-0 fw-semibold px-3" disabled title="Already Confirmed">
                                                        <i class="fas fa-lock me-1"></i> Confirmed
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- View Student Modal -->
<div class="modal fade" id="viewStudentModal" tabindex="-1" aria-labelledby="viewStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-primary" id="viewStudentModalLabel"><i class="fas fa-user-graduate me-2"></i>Student Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="text-secondary fw-bold border-bottom pb-2 mb-3">Personal Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr><td class="text-muted w-50">Student No:</td><td class="fw-bold" id="vs-student-no"></td></tr>
                            <tr><td class="text-muted">Full Name:</td><td class="fw-bold" id="vs-name"></td></tr>
                            <tr><td class="text-muted">Gender:</td><td id="vs-gender"></td></tr>
                            <tr><td class="text-muted">Date of Birth:</td><td id="vs-dob"></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-secondary fw-bold border-bottom pb-2 mb-3">Enrollment Details</h6>
                        <table class="table table-sm table-borderless">
                            <tr><td class="text-muted w-50">Program / Course:</td><td class="fw-bold" id="vs-course"></td></tr>
                            <tr><td class="text-muted">Year Level:</td><td id="vs-year"></td></tr>
                            <tr><td class="text-muted">Status:</td><td><span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 rounded-pill" id="vs-status"></span></td></tr>
                            <tr><td class="text-muted">Registered On:</td><td id="vs-registered"></td></tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Verify & Enroll Confirmation Modal -->
<div class="modal fade" id="enrollStudentModal" tabindex="-1" aria-labelledby="enrollStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="enrollStudentModalLabel"><i class="fas fa-user-check me-2"></i>Verify Student Enrollment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <i class="fas fa-envelope-open-text fa-4x text-primary opacity-75"></i>
                </div>
                <h5 class="fw-bold mb-3">Verify <span id="es-student-name" class="text-primary"></span>?</h5>
                <p class="text-muted mb-4">
                    This action will:
                    <ul class="text-start text-muted mx-auto" style="max-width: 300px; font-size: 0.95rem;">
                        <li>Mark the student as <strong>Verified</strong>.</li>
                        <li>Auto-generate a <strong>PRE ACCOUNT</strong> for the student portal.</li>
                        <li>Send an <strong>Email Notification</strong> with login credentials and instructions.</li>
                    </ul>
                </p>
                <div id="enroll-alert" class="alert d-none"></div>
            </div>
            <div class="modal-footer bg-light justify-content-center">
                <form id="enrollForm" data-no-loader="1" class="w-100 d-flex gap-2 justify-content-center">
                    <input type="hidden" name="student_id" id="es-student-id">
                    <button type="button" class="btn btn-light border px-4 rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold" id="btn-confirm-enroll">
                        <i class="fas fa-check me-2"></i>Confirm Verification
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.02) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    z-index: 1;
    position: relative;
}
.table-hover tbody tr {
    transition: all 0.2s ease-in-out;
}
.avatar {
    font-size: 1.1rem;
    box-shadow: inset 0 0 0 1px rgba(13, 110, 253, 0.1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // View Modal Logic
    const viewModal = new bootstrap.Modal(document.getElementById('viewStudentModal'));
    document.querySelectorAll('.view-student-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const data = JSON.parse(this.dataset.student);
            document.getElementById('vs-student-no').textContent = data.student_number || 'N/A';
            document.getElementById('vs-name').textContent = (data.first_name || '') + ' ' + (data.middle_name ? data.middle_name + ' ' : '') + (data.last_name || '');
            document.getElementById('vs-gender').textContent = data.gender || 'N/A';
            document.getElementById('vs-dob').textContent = data.date_of_birth || 'N/A';
            document.getElementById('vs-course').textContent = data.program_course || 'N/A';
            document.getElementById('vs-year').textContent = data.year_section || 'N/A';
            document.getElementById('vs-status').textContent = data.status || 'N/A';
            document.getElementById('vs-registered').textContent = data.created_at ? new Date(data.created_at).toLocaleString() : 'N/A';
            viewModal.show();
        });
    });

    // Enroll Modal Logic
    const enrollModal = new bootstrap.Modal(document.getElementById('enrollStudentModal'));
    document.querySelectorAll('.enroll-student-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('es-student-id').value = this.dataset.id;
            document.getElementById('es-student-name').textContent = this.dataset.name;
            document.getElementById('enroll-alert').className = 'alert d-none';
            document.getElementById('btn-confirm-enroll').disabled = false;
            document.getElementById('btn-confirm-enroll').innerHTML = '<i class="fas fa-check me-2"></i>Confirm Verification';
            enrollModal.show();
        });
    });

    // Enroll Form Submit Logic
    document.getElementById('enrollForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('btn-confirm-enroll');
        const alertEl = document.getElementById('enroll-alert');
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
        alertEl.className = 'alert d-none';
        
        try {
            const formData = new FormData(this);
            const response = await fetch('<?= BASE_URL ?>/modules/enrollment/api/verify-student.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                alertEl.className = 'alert alert-success';
                alertEl.innerHTML = '<i class="fas fa-check-circle me-2"></i>' + result.message;
                setTimeout(() => window.location.reload(), 1500);
            } else {
                throw new Error(result.error || 'Verification failed');
            }
        } catch (error) {
            alertEl.className = 'alert alert-danger';
            alertEl.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>' + error.message;
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check me-2"></i>Confirm Verification';
        }
    });
});
</script>

<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
