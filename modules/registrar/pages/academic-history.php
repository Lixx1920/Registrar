<?php
/**
 * SMS 2 - Academic History
 * Module: Registrar
 * STATUS: Placeholder — Phase 3 will implement full CRUD
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';

regRequireAction('registrar.view');

$pageTitle    = 'Academic History';
$activeModule = 'registrar';
$activePage   = 'academic-history';
$breadcrumbs  = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Academic History', 'url' => null],
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
                <i class="fas fa-history text-primary me-2"></i>Academic History
            </h1>
            <p class="text-muted mb-0">Track and manage student educational background and previous school records</p>
        </div>
        <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill">
            <i class="fas fa-clock me-1"></i> Under Development
        </span>
    </div>

    <!-- Planned Features Card -->
    <div class="card border-start border-warning border-4 reg-shadow mb-4">
        <div class="card-body py-3">
            <h6 class="text-warning fw-semibold mb-3"><i class="fas fa-info-circle me-2"></i>Planned Features — Phase 3</h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-school text-primary mt-1"></i>
                        <div><strong>Previous Schools</strong><br><small class="text-muted">Log all schools attended (Elementary, High School, College)</small></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-award text-primary mt-1"></i>
                        <div><strong>Awards & Honors</strong><br><small class="text-muted">Record academic distinctions, awards, and recognition</small></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-paperclip text-primary mt-1"></i>
                        <div><strong>Document Attachments</strong><br><small class="text-muted">Attach diploma scans with SHA-256 integrity verification</small></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-edit text-primary mt-1"></i>
                        <div><strong>Full CRUD</strong><br><small class="text-muted">Add, edit, and delete academic history records with audit log</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Skeleton: Action Bar -->
    <div class="d-flex justify-content-between align-items-center mb-3" style="opacity:.35; pointer-events:none;">
        <div class="d-flex gap-2">
            <div class="bg-secondary rounded" style="width:200px;height:36px;"></div>
            <div class="bg-secondary rounded" style="width:100px;height:36px;"></div>
        </div>
        <div class="bg-primary rounded" style="width:150px;height:36px;"></div>
    </div>

    <!-- Skeleton: Table -->
    <div class="card reg-shadow" style="opacity:.35; pointer-events:none;">
        <div class="card-header bg-primary" style="height:50px;"></div>
        <div class="table-responsive">
            <table class="table reg-table mb-0">
                <thead>
                    <tr>
                        <th>School Name</th>
                        <th>Level</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Awards / Honors</th>
                        <th>Attached File</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < 4; $i++): ?>
                    <tr>
                        <?php for ($j = 0; $j < 7; $j++): ?>
                        <td><div class="bg-secondary rounded" style="height:18px;width:<?php echo 60 + rand(0,40); ?>%;"></div></td>
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
        <strong>This page is being developed.</strong> Academic history records with file attachments and SHA-256 verification will be available in <strong>Phase 3</strong>.
    </div>

</div>

<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
