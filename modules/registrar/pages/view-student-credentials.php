<?php
/**
 * SMS 2 - Registrar: View Student Credentials
 */
require_once __DIR__ . '/../../../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';

requireAuth();

$pageTitle = 'View Student Credentials';
$activeModule = 'registrar';
$activePage = 'validate-credentials';

require_once ROOT_PATH . '/includes/layout-start.php';

$pdo = db();
$studentId = $_GET['id'] ?? null;

if (!$studentId) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Student ID not provided.</div></div>";
    require_once ROOT_PATH . '/includes/layout-end.php';
    exit;
}

$stmt = $pdo->prepare("
    SELECT id, student_number, first_name, last_name, email_address, contact_number, program_course, year_section, status
    FROM reg_students
    WHERE id = ?
");
$stmt->execute([$studentId]);
$student = $stmt->fetch();

if (!$student) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Student not found.</div></div>";
    require_once ROOT_PATH . '/includes/layout-end.php';
    exit;
}

// Fetch documents
$docStmt = $pdo->prepare("
    SELECT id, category, original_name, stored_name, mime, size, created_at
    FROM reg_files
    WHERE student_id = ?
    ORDER BY created_at DESC
");
$docStmt->execute([$studentId]);
$documents = $docStmt->fetchAll();
?>

<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 text-dark fw-bold"><i class="fas fa-user-check text-primary me-2"></i>Student Credential Review</h2>
            <p class="text-muted mb-0">Review the documents uploaded by <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>.</p>
        </div>
        <a href="validate-credentials.php" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <div class="row">
        <!-- Student Information Card -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-primary text-white p-4 rounded-top-4">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-info-circle me-2"></i>Student Details</h5>
                </div>
                <div class="card-body p-4">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-bold">Student No.</span>
                            <span class="badge bg-primary rounded-pill"><?= htmlspecialchars($student['student_number']) ?></span>
                        </li>
                        <li class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-bold">Full Name</span>
                            <span class="fw-bold text-dark text-end"><?= htmlspecialchars($student['last_name'] . ', ' . $student['first_name']) ?></span>
                        </li>
                        <li class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-bold">Email</span>
                            <span class="text-dark text-end"><?= htmlspecialchars($student['email_address'] ?: 'N/A') ?></span>
                        </li>
                        <li class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-bold">Contact</span>
                            <span class="text-dark text-end"><?= htmlspecialchars($student['contact_number'] ?: 'N/A') ?></span>
                        </li>
                        <li class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-bold">Program</span>
                            <span class="text-dark text-end"><?= htmlspecialchars($student['program_course'] ?: 'N/A') ?></span>
                        </li>
                        <li class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center border-bottom-0">
                            <span class="text-muted fw-bold">Current Status</span>
                            <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i><?= htmlspecialchars($student['status']) ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Uploaded Documents Card -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white p-4 border-bottom rounded-top-4">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-folder-open text-primary me-2"></i>Uploaded Credentials</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Document Type</th>
                                    <th>Filename</th>
                                    <th>Size</th>
                                    <th>Date Uploaded</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($documents)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No documents uploaded yet.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($documents as $doc): ?>
                                        <tr>
                                            <td class="fw-bold"><?= htmlspecialchars($doc['category']) ?></td>
                                            <td><?= htmlspecialchars($doc['original_name']) ?></td>
                                            <td><?= round($doc['size'] / 1024, 2) ?> KB</td>
                                            <td><?= date('M d, Y h:i A', strtotime($doc['created_at'])) ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill shadow-sm px-3 view-doc-btn" 
                                                    data-url="<?= BASE_URL ?>/modules/registrar/api/view-document.php?file_id=<?= $doc['id'] ?>"
                                                    data-title="<?= htmlspecialchars($doc['category']) ?>">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </button>
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
    
    <!-- Activation Action -->
    <div class="row mt-3">
        <div class="col-12 text-end">
            <form action="../api/activate-student.php" method="POST" id="activateForm">
                <input type="hidden" name="student_id" value="<?= htmlspecialchars($student['id']) ?>">
                <button type="button" class="btn btn-success btn-lg rounded-pill shadow-sm px-5 fw-bold" onclick="confirmActivation()">
                    <i class="fas fa-user-check me-2"></i> OFFICIALLY ENROLL & ACTIVATE
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function confirmActivation() {
    if (confirm("Activate Student?\n\nThis will officially enroll the student, update their account status, and notify them via email. Proceed?")) {
        document.getElementById('activateForm').submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const docModal = new bootstrap.Modal(document.getElementById('documentPreviewModal'));
    
    document.querySelectorAll('.view-doc-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.dataset.url;
            const title = this.dataset.title;
            
            document.getElementById('docPreviewTitle').textContent = title;
            const container = document.getElementById('docPreviewContainer');
            
            // We use an iframe to preview the document (works for PDFs and Images in modern browsers)
            container.innerHTML = `<iframe src="${url}" style="width:100%; height:100%; border:none;"></iframe>`;
            
            docModal.show();
        });
    });
    
    // Clear iframe on close to stop loading/playing
    document.getElementById('documentPreviewModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('docPreviewContainer').innerHTML = '';
    });
});
</script>

<!-- Document Preview Modal -->
<div class="modal fade" id="documentPreviewModal" tabindex="-1" aria-labelledby="documentPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="height: 90vh;">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold" id="documentPreviewModalLabel">
                    <i class="fas fa-file-alt me-2 text-primary"></i> <span id="docPreviewTitle">Document Preview</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-light" id="docPreviewContainer" style="overflow: hidden;">
                <!-- Iframe injected here via JS -->
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/includes/layout-end.php'; ?>
