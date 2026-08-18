<?php
/**
 * SMS 2 - Document Requests
 * Module: Registrar
 * STATUS: Placeholder — Phase 7 will implement full request workflow
 * Documents: Form 137, Good Moral Certificate, TOR, COE
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';

regRequireAction('registrar.view');

$pageTitle    = 'Document Requests';
$activeModule = 'registrar';
$activePage   = 'document-requests';
$breadcrumbs  = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Document Requests', 'url' => null],
];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/modules/registrar/assets/css/registrar.css">

<?php renderBreadcrumbs($breadcrumbs); ?>

<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 text-dark mb-1">
                <i class="fas fa-file-signature text-primary me-2"></i>Document Requests
            </h1>
            <p class="text-muted mb-0">Process and track student document requests — Form 137, Good Moral Certificate, TOR, Certificate of Enrollment</p>
        </div>
        <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill">
            <i class="fas fa-clock me-1"></i> Under Development
        </span>
    </div>

    <!-- Planned Features Card -->
    <div class="card border-start border-warning border-4 reg-shadow mb-4">
        <div class="card-body py-3">
            <h6 class="text-warning fw-semibold mb-3"><i class="fas fa-info-circle me-2"></i>Planned Features — Phase 7</h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-file-alt text-primary mt-1"></i>
                        <div><strong>Document Types</strong><br><small class="text-muted">Form 137, Good Moral, TOR, Certificate of Enrollment</small></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-tasks text-primary mt-1"></i>
                        <div><strong>Status Workflow</strong><br><small class="text-muted">Submitted → For Review → Processing → For Release → Released</small></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-stamp text-primary mt-1"></i>
                        <div><strong>Digital Generation & Signing</strong><br><small class="text-muted">Auto-generate PDF and apply RSA digital signature</small></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-store text-primary mt-1"></i>
                        <div><strong>Walk-in Transactions</strong><br><small class="text-muted">Flag and process over-the-counter document requests</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Skeleton: Stats Row -->
    <div class="row g-3 mb-4" style="opacity:.35; pointer-events:none;">
        <?php
        $statCards = [
            ['fa-inbox','Submitted','12'],
            ['fa-search','For Review','5'],
            ['fa-cog','Processing','3'],
            ['fa-check-circle','Released Today','8'],
        ];
        foreach ($statCards as $stat):
        ?>
        <div class="col-6 col-md-3">
            <div class="reg-card text-center">
                <div class="reg-card-body py-3">
                    <i class="fas <?php echo $stat[0]; ?> fa-2x text-muted mb-2"></i>
                    <h3 class="fw-bold mb-0"><?php echo $stat[2]; ?></h3>
                    <small class="text-muted"><?php echo $stat[1]; ?></small>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Skeleton: Requests Queue Table -->
    <div class="card reg-shadow" style="opacity:.35; pointer-events:none;">
        <div class="card-header bg-primary" style="height:50px;"></div>
        <div class="table-responsive">
            <table class="table reg-table mb-0">
                <thead>
                    <tr>
                        <th>Request No.</th>
                        <th>Student</th>
                        <th>Document Type</th>
                        <th>Purpose</th>
                        <th>Requested</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $statuses = ['Submitted','For Review','Processing','For Release','Released'];
                    for ($i = 0; $i < 5; $i++): ?>
                    <tr>
                        <?php for ($j = 0; $j < 7; $j++): ?>
                        <td><div class="bg-secondary rounded" style="height:16px;width:<?php echo 50 + rand(0,40); ?>%;"></div></td>
                        <?php endfor; ?>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Coming Soon Note -->
    <div class="alert alert-light border mt-4 text-center">
        <i class="fas fa-tools text-warning me-2"></i>
        <strong>This page is being developed.</strong> Full document request workflow with Form 137, Good Moral, and RSA-signed PDFs will be available in <strong>Phase 7</strong>.
    </div>

</div>

<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
