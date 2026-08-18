<?php
/**
 * SMS 2 - Student ID Generation
 * Module: Registrar
 * STATUS: Placeholder — Phase 6 will implement full ID card generation
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';

regRequireAction('registrar.view');

$pageTitle    = 'Student ID Generation';
$activeModule = 'registrar';
$activePage   = 'student-id-generation';
$breadcrumbs  = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Student ID Generation', 'url' => null],
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
                <i class="fas fa-id-card text-primary me-2"></i>Student ID Generation
            </h1>
            <p class="text-muted mb-0">Generate digital student ID cards with embedded QR codes, RSA signatures, and PDF export</p>
        </div>
        <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill">
            <i class="fas fa-clock me-1"></i> Under Development
        </span>
    </div>

    <!-- Planned Features Card -->
    <div class="card border-start border-warning border-4 reg-shadow mb-4">
        <div class="card-body py-3">
            <h6 class="text-warning fw-semibold mb-3"><i class="fas fa-info-circle me-2"></i>Planned Features — Phase 6</h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-id-card text-primary mt-1"></i>
                        <div><strong>ID Card Preview</strong><br><small class="text-muted">Live HTML preview of student ID card before PDF export</small></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-file-pdf text-primary mt-1"></i>
                        <div><strong>PDF Generation</strong><br><small class="text-muted">Printable ID card PDF using DOMPDF with school branding</small></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-qrcode text-primary mt-1"></i>
                        <div><strong>QR Embed on ID</strong><br><small class="text-muted">Embed RSA-signed QR code directly on the student ID card</small></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-layer-group text-primary mt-1"></i>
                        <div><strong>Bulk Generation</strong><br><small class="text-muted">Generate IDs for multiple students in one batch operation</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Skeleton: ID Card Preview + Issuance History -->
    <div class="row g-4" style="opacity:.35; pointer-events:none;">

        <!-- ID Card Preview -->
        <div class="col-md-4">
            <div class="reg-card">
                <div class="card-header bg-primary text-white">
                    <strong>ID Card Preview</strong>
                </div>
                <div class="reg-card-body p-3">
                    <!-- Skeleton ID Card -->
                    <div class="border rounded p-3 bg-light" style="min-height:200px;">
                        <div class="d-flex gap-3 mb-2">
                            <div class="bg-secondary rounded" style="width:70px;height:90px;flex-shrink:0;"></div>
                            <div class="flex-grow-1">
                                <div class="bg-secondary rounded mb-2" style="height:14px;width:80%;"></div>
                                <div class="bg-secondary rounded mb-1" style="height:12px;width:60%;"></div>
                                <div class="bg-secondary rounded mb-1" style="height:12px;width:70%;"></div>
                                <div class="bg-secondary rounded" style="height:12px;width:50%;"></div>
                            </div>
                            <div class="bg-secondary rounded" style="width:60px;height:60px;flex-shrink:0;"></div>
                        </div>
                        <div class="bg-secondary rounded mt-2" style="height:30px;width:100%;"></div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <div class="bg-primary rounded flex-grow-1" style="height:34px;"></div>
                        <div class="bg-secondary rounded" style="width:80px;height:34px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Issuance History Table -->
        <div class="col-md-8">
            <div class="card reg-shadow">
                <div class="card-header bg-primary" style="height:50px;"></div>
                <div class="table-responsive">
                    <table class="table reg-table mb-0">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>ID Number</th>
                                <th>Issued Date</th>
                                <th>Expiry</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 0; $i < 5; $i++): ?>
                            <tr>
                                <?php for ($j = 0; $j < 6; $j++): ?>
                                <td><div class="bg-secondary rounded" style="height:16px;width:<?php echo 50 + rand(0,40); ?>%;"></div></td>
                                <?php endfor; ?>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Coming Soon Note -->
    <div class="alert alert-light border mt-4 text-center">
        <i class="fas fa-tools text-warning me-2"></i>
        <strong>This page is being developed.</strong> Digital student ID generation with QR embed and PDF export will be available in <strong>Phase 6</strong>.
    </div>

</div>

<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
