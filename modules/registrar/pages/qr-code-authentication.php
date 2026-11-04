<?php
/**
 * SMS 2 - QR Code Authentication
 * Module: Registrar
 * STATUS: Placeholder — Phase 5 will implement full QR + RSA signing
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';

regRequireAction('registrar.view');

$pageTitle    = 'QR Code Authentication';
$activeModule = 'registrar';
$activePage   = 'qr-code-authentication';
$breadcrumbs  = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'QR Code Authentication', 'url' => null],
];

$hideModulePageBanner = true;
require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/modules/registrar/assets/css/registrar.css">

<?php renderBreadcrumbs($breadcrumbs); ?>

<style>
/* ── Viewport Lock: qr-code-authentication ───────────────────────── */
.module-page-banner, .qr-top-header { display: none !important; }
@media (min-width: 992px) {
    html:has(.qr-wrap), body:has(.qr-wrap) {
        height: 100vh !important; overflow: hidden !important;
        scrollbar-gutter: auto !important; width: 100% !important; max-width: 100% !important;
    }
    body:has(.qr-wrap) .sms-wrapper {
        height: 100vh !important; min-height: 100vh !important; width: 100% !important; max-width: 100% !important;
        overflow: hidden !important; padding-top: var(--sms-navbar-height, 60px) !important; box-sizing: border-box !important;
    }
    body:has(.qr-wrap) .sms-content {
        height: calc(100vh - var(--sms-navbar-height, 60px)) !important;
        width: calc(100% - var(--sms-sidebar-width, 262px)) !important;
        max-width: calc(100% - var(--sms-sidebar-width, 262px)) !important;
        overflow: hidden !important; display: flex !important; flex-direction: column !important; box-sizing: border-box !important;
    }
    body.sidebar-collapsed:has(.qr-wrap) .sms-content {
        margin-left: var(--sms-sidebar-collapsed-width, 76px) !important;
        width: calc(100% - var(--sms-sidebar-collapsed-width, 76px)) !important;
        max-width: calc(100% - var(--sms-sidebar-collapsed-width, 76px)) !important;
    }
    body:has(.qr-wrap) .sms-main {
        flex: 1 1 0% !important; min-height: 0 !important; height: 100% !important;
        width: 100% !important; max-width: 100% !important; align-items: stretch !important; overflow: hidden !important;
        padding: 0.4rem 1.25rem 0.35rem 1.25rem !important; display: flex !important; flex-direction: column !important; box-sizing: border-box !important;
    }
    body:has(.qr-wrap) .sms-footer {
        display: block !important; flex: 0 0 auto !important; width: 100% !important; max-width: 100% !important;
        padding: 0.35rem 1.25rem !important; font-size: 0.74rem !important;
        background: var(--sms-surface, #0d1e3d) !important;
        border-top: 1px solid var(--sms-border, rgba(59, 130, 246, 0.2)) !important; z-index: 10 !important;
    }
    .qr-wrap, .sms-main > .container-fluid, .mpl[data-mpl], .mpl-stats, .mpl-filters, .mpl-panel, .mpl-table-wrap, .mpl-table, .sms-main > .container-fluid > .card, #secModuleRoot, #panel-logs {
        width: 100% !important; max-width: 100% !important; margin-left: 0 !important; margin-right: 0 !important; box-sizing: border-box !important;
    }
    .qr-wrap > .row, .reg-profile-wrapper > .container-fluid > .row {
        width: calc(100% + var(--bs-gutter-x, 1.5rem)) !important;
        max-width: calc(100% + var(--bs-gutter-x, 1.5rem)) !important;
        margin-left: calc(-0.5 * var(--bs-gutter-x, 1.5rem)) !important;
        margin-right: calc(-0.5 * var(--bs-gutter-x, 1.5rem)) !important;
    }
    .container-fluid.qr-wrap { flex: 1 1 0% !important; min-height: 0 !important; height: 100% !important; padding: 0 !important; display: flex !important; flex-direction: column !important; overflow: hidden !important; }
    nav[aria-label="breadcrumb"] { flex: 0 0 auto !important; margin-bottom: 0.25rem !important; }
    nav[aria-label="breadcrumb"] .breadcrumb { margin-bottom: 0 !important; padding: 0 !important; font-size: 0.78rem !important; }
}
</style>
<div class="container-fluid qr-wrap">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4 qr-top-header">
        <div>
            <h1 class="h3 text-dark mb-1">
                <i class="fas fa-qrcode text-success me-2"></i>QR Code Authentication
            </h1>
            <p class="text-muted mb-0">Generate, manage, and verify student QR codes with SHA-256 integrity and RSA digital signatures</p>
        </div>
        <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill">
            <i class="fas fa-clock me-1"></i> Under Development
        </span>
    </div>

    <!-- Planned Features Card -->
    <div class="card border-start border-warning border-4 reg-shadow mb-4">
        <div class="card-body py-3">
            <h6 class="text-warning fw-semibold mb-3"><i class="fas fa-info-circle me-2"></i>Planned Features — Phase 5</h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-qrcode text-success mt-1"></i>
                        <div><strong>QR Code Generator</strong><br><small class="text-muted">Per-student unique QR with SHA-256 hash embedded</small></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-key text-success mt-1"></i>
                        <div><strong>RSA Digital Signature</strong><br><small class="text-muted">QR payload signed with RSA private key for authenticity proof</small></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-search text-success mt-1"></i>
                        <div><strong>QR Verifier</strong><br><small class="text-muted">Scan and validate QR authenticity in real time</small></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-redo text-success mt-1"></i>
                        <div><strong>Revoke & Regenerate</strong><br><small class="text-muted">Invalidate compromised QR codes and issue new ones with audit trail</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Skeleton: QR Panel + Scan History -->
    <div class="row g-4" style="opacity:.35; pointer-events:none;">

        <!-- QR Preview Card -->
        <div class="col-md-4">
            <div class="reg-card text-center">
                <div class="card-header bg-success text-white">
                    <strong>Student QR Code</strong>
                </div>
                <div class="reg-card-body py-4">
                    <div class="mx-auto bg-secondary rounded mb-3" style="width:180px;height:180px;"></div>
                    <div class="bg-secondary rounded mb-2 mx-auto" style="height:20px;width:60%;"></div>
                    <div class="bg-secondary rounded mx-auto" style="height:14px;width:40%;"></div>
                    <div class="d-flex gap-2 justify-content-center mt-3">
                        <div class="bg-primary rounded" style="width:90px;height:34px;"></div>
                        <div class="bg-danger rounded" style="width:90px;height:34px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scan History Table -->
        <div class="col-md-8">
            <div class="card reg-shadow">
                <div class="card-header bg-success" style="height:50px;"></div>
                <div class="table-responsive">
                    <table class="table reg-table mb-0">
                        <thead>
                            <tr>
                                <th>Scan Date & Time</th>
                                <th>Scanned By</th>
                                <th>Location</th>
                                <th>Signature Valid</th>
                                <th>Result</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 0; $i < 5; $i++): ?>
                            <tr>
                                <?php for ($j = 0; $j < 5; $j++): ?>
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
        <strong>This page is being developed.</strong> QR code generation with SHA-256 + RSA digital signature will be available in <strong>Phase 5</strong>.
    </div>

</div>

<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>

