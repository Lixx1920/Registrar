<?php
/**
 * SMS 2 - Registrar: Validate Credentials
 */
require_once __DIR__ . '/../../../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';

requireAuth();

$pageTitle = 'Validate Credentials';
$activeModule = 'registrar';
$activePage = 'validate-credentials';

require_once ROOT_PATH . '/includes/layout-start.php';

$pdo = db();

// Fetch students who have uploaded documents
$stmt = $pdo->query("
    SELECT s.id, s.student_number, s.first_name, s.last_name, s.email_address, s.program_course, s.status,
           COUNT(f.id) as doc_count, MAX(f.created_at) as last_uploaded
    FROM reg_students s
    JOIN reg_files f ON s.id = f.student_id
    WHERE s.status = 'Verified'
    GROUP BY s.id
    ORDER BY last_uploaded DESC
");
$students = $stmt->fetchAll();
?>

<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 text-dark fw-bold"><i class="fas fa-file-signature text-primary me-2"></i>Validate Credentials</h2>
            <p class="text-muted mb-0">Review submitted documents of pre-enrolled students and activate their accounts.</p>
        </div>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'activated'): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-check-circle me-2 fs-5 align-middle"></i>
            <strong>Integration Success!</strong> Student has been officially enrolled and activated. Their records have been successfully cascaded to the Student Information System, Academic History, and Masterlist. An email notification was sent successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'activated_mail_failed'): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Activated with Error!</strong> Student has been officially enrolled and activated, BUT the email notification failed to send.
            <br><strong>Error:</strong> <?= htmlspecialchars($_GET['error'] ?? 'Unknown error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Student No.</th>
                            <th>Name</th>
                            <th>Program</th>
                            <th>Docs Uploaded</th>
                            <th>Date Uploaded</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No pending credential validations at the moment.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($student['student_number']) ?></span></td>
                                    <td class="fw-bold"><?= htmlspecialchars($student['last_name'] . ', ' . $student['first_name']) ?></td>
                                    <td><?= htmlspecialchars($student['program_course'] ?: 'N/A') ?></td>
                                    <td><span class="badge bg-info text-dark rounded-pill"><?= $student['doc_count'] ?> documents</span></td>
                                    <td><?= date('M d, Y h:i A', strtotime($student['last_uploaded'])) ?></td>
                                    <td><span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i><?= htmlspecialchars($student['status']) ?></span></td>
                                    <td>
                                        <a href="view-student-credentials.php?id=<?= $student['id'] ?>" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                            <i class="fas fa-search me-1"></i> View & Act
                                        </a>
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

<?php require_once ROOT_PATH . '/includes/layout-end.php'; ?>
