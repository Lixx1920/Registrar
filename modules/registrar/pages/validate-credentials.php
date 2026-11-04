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

<style>
/* ── Viewport Lock: validate-credentials ───────────────────────── */
.module-page-banner, .vc-top-header { display: none !important; }
@media (min-width: 992px) {
    html:has(.vc-wrap), body:has(.vc-wrap) {
        height: 100vh !important; overflow: hidden !important;
        scrollbar-gutter: auto !important; width: 100% !important; max-width: 100% !important;
    }
    body:has(.vc-wrap) .sms-wrapper {
        height: 100vh !important; min-height: 100vh !important; width: 100% !important; max-width: 100% !important;
        overflow: hidden !important; padding-top: var(--sms-navbar-height, 60px) !important; box-sizing: border-box !important;
    }
    body:has(.vc-wrap) .sms-content {
        height: calc(100vh - var(--sms-navbar-height, 60px)) !important;
        width: calc(100% - var(--sms-sidebar-width, 262px)) !important;
        max-width: calc(100% - var(--sms-sidebar-width, 262px)) !important;
        overflow: hidden !important; display: flex !important; flex-direction: column !important; box-sizing: border-box !important;
    }
    body.sidebar-collapsed:has(.vc-wrap) .sms-content {
        margin-left: var(--sms-sidebar-collapsed-width, 76px) !important;
        width: calc(100% - var(--sms-sidebar-collapsed-width, 76px)) !important;
        max-width: calc(100% - var(--sms-sidebar-collapsed-width, 76px)) !important;
    }
    body:has(.vc-wrap) .sms-main {
        flex: 1 1 0% !important; min-height: 0 !important; height: 100% !important;
        width: 100% !important; max-width: 100% !important; align-items: stretch !important; overflow: hidden !important;
        padding: 0.4rem 1.25rem 0.35rem 1.25rem !important; display: flex !important; flex-direction: column !important; box-sizing: border-box !important;
    }
    body:has(.vc-wrap) .sms-footer {
        display: block !important; flex: 0 0 auto !important; width: 100% !important; max-width: 100% !important;
        padding: 0.35rem 1.25rem !important; font-size: 0.74rem !important;
        background: var(--sms-surface, #0d1e3d) !important;
        border-top: 1px solid var(--sms-border, rgba(59, 130, 246, 0.2)) !important; z-index: 10 !important;
    }
    .vc-wrap, .sms-main > .container-fluid, .mpl[data-mpl], .mpl-stats, .mpl-filters, .mpl-panel, .mpl-table-wrap, .mpl-table, .sms-main > .container-fluid > .card, #secModuleRoot, #panel-logs {
        width: 100% !important; max-width: 100% !important; margin-left: 0 !important; margin-right: 0 !important; box-sizing: border-box !important;
    }
    .vc-wrap > .row, .reg-profile-wrapper > .container-fluid > .row {
        width: calc(100% + var(--bs-gutter-x, 1.5rem)) !important;
        max-width: calc(100% + var(--bs-gutter-x, 1.5rem)) !important;
        margin-left: calc(-0.5 * var(--bs-gutter-x, 1.5rem)) !important;
        margin-right: calc(-0.5 * var(--bs-gutter-x, 1.5rem)) !important;
    }
    .container-fluid.vc-wrap { flex: 1 1 0% !important; min-height: 0 !important; height: 100% !important; padding: 0 !important; display: flex !important; flex-direction: column !important; overflow: hidden !important; gap: 0.5rem !important; }
    .container-fluid.vc-wrap > .card { flex: 1 1 0% !important; min-height: 0 !important; display: flex !important; flex-direction: column !important; overflow: hidden !important; margin-bottom: 0 !important; }
    .container-fluid.vc-wrap > .card > .card-body { flex: 1 1 0% !important; min-height: 0 !important; display: flex !important; flex-direction: column !important; overflow: hidden !important; padding: 0.75rem !important; }
    .container-fluid.vc-wrap .table-responsive { flex: 1 1 0% !important; min-height: 0 !important; overflow-y: auto !important; }
}
</style>
<div class="container-fluid vc-wrap">
    <div class="d-flex justify-content-between align-items-center mb-4 vc-top-header">
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

