<?php
/**
 * SMS 2 - Digital File Storage
 * Module: Registrar
 * STATUS: Placeholder — Phase 9 will implement full file browser
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';

regRequireAction('registrar.view');

$pageTitle    = 'Digital File Storage';
$activeModule = 'registrar';
$activePage   = 'digital-file-storage';
$breadcrumbs  = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Digital File Storage', 'url' => null],
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
                <i class="fas fa-folder-open text-warning me-2"></i>Digital File Storage
            </h1>
            <p class="text-muted mb-0">Central repository for all student documents — browse, verify, and manage files with SHA-256 integrity checks</p>
        </div>
        <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill">
            <i class="fas fa-clock me-1"></i> Under Development
        </span>
    </div>

    <!-- Planned Features Card -->
    <div class="card border-start border-warning border-4 reg-shadow mb-4">
        <div class="card-body py-3">
            <h6 class="text-warning fw-semibold mb-3"><i class="fas fa-info-circle me-2"></i>Planned Features — Phase 9</h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-folder text-warning mt-1"></i>
                        <div><strong>Category Browser</strong><br><small class="text-muted">Browse files by category: Identity, Academic, Health, Documents, Generated</small></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-shield-alt text-warning mt-1"></i>
                        <div><strong>SHA-256 Verification</strong><br><small class="text-muted">One-click integrity check for any stored file</small></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-eye text-warning mt-1"></i>
                        <div><strong>File Preview</strong><br><small class="text-muted">Inline PDF viewer and image thumbnail previews</small></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-download text-warning mt-1"></i>
                        <div><strong>Bulk Download</strong><br><small class="text-muted">Select multiple files and download as a ZIP archive</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Skeleton: Storage Summary -->
    <div class="row g-3 mb-4" style="opacity:.35; pointer-events:none;">
        <?php
        $categories = [
            ['fa-id-card','Identity Docs','24 files'],
            ['fa-graduation-cap','Academic','18 files'],
            ['fa-heartbeat','Health','9 files'],
            ['fa-file-alt','Requests','37 files'],
            ['fa-cog','Generated','15 files'],
        ];
        foreach ($categories as $cat):
        ?>
        <div class="col-6 col-md">
            <div class="reg-card text-center">
                <div class="reg-card-body py-3">
                    <i class="fas <?php echo $cat[0]; ?> fa-2x text-muted mb-2"></i>
                    <p class="fw-semibold mb-0"><?php echo $cat[1]; ?></p>
                    <small class="text-muted"><?php echo $cat[2]; ?></small>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Skeleton: File Grid / List -->
    <div class="card reg-shadow" style="opacity:.35; pointer-events:none;">
        <div class="card-header d-flex justify-content-between align-items-center bg-warning" style="height:50px;"></div>
        <div class="card-body p-3">
            <div class="row g-3">
                <?php for ($i = 0; $i < 8; $i++): ?>
                <div class="col-6 col-md-3">
                    <div class="border rounded p-3 text-center">
                        <div class="bg-secondary rounded mx-auto mb-2" style="width:48px;height:60px;"></div>
                        <div class="bg-secondary rounded mb-1" style="height:12px;width:80%;margin:0 auto;"></div>
                        <div class="bg-secondary rounded" style="height:10px;width:50%;margin:0 auto;"></div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <!-- Coming Soon Note -->
    <div class="alert alert-light border mt-4 text-center">
        <i class="fas fa-tools text-warning me-2"></i>
        <strong>This page is being developed.</strong> Full digital file browser with SHA-256 verification and bulk operations will be available in <strong>Phase 9</strong>.
    </div>

</div>

<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
