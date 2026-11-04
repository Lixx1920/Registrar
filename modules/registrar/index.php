<?php
/**
 * SMS 2 - Registrar - Overview
 */
$pageTitle    = 'Registrar';
$activeModule = 'registrar';
$activePage   = '';
$breadcrumbs  = [
    ['label' => 'Registrar', 'url' => null],
];
$hideModulePageBanner = true;

require_once __DIR__ . '/../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../includes/layout-start.php';
?>
<style>
/* ── Viewport Lock: registrar overview ───────────────────────── */
.module-page-banner { display: none !important; }
@media (min-width: 992px) {
    html:has(.module-button-grid), body:has(.module-button-grid) {
        height: 100vh !important; overflow: hidden !important;
        scrollbar-gutter: auto !important; width: 100% !important; max-width: 100% !important;
    }
    body:has(.module-button-grid) .sms-wrapper {
        height: 100vh !important; min-height: 100vh !important; width: 100% !important; max-width: 100% !important;
        overflow: hidden !important; padding-top: var(--sms-navbar-height, 60px) !important; box-sizing: border-box !important;
    }
    body:has(.module-button-grid) .sms-content {
        height: calc(100vh - var(--sms-navbar-height, 60px)) !important;
        width: calc(100% - var(--sms-sidebar-width, 262px)) !important;
        max-width: calc(100% - var(--sms-sidebar-width, 262px)) !important;
        overflow: hidden !important; display: flex !important; flex-direction: column !important; box-sizing: border-box !important;
    }
    body.sidebar-collapsed:has(.module-button-grid) .sms-content {
        margin-left: var(--sms-sidebar-collapsed-width, 76px) !important;
        width: calc(100% - var(--sms-sidebar-collapsed-width, 76px)) !important;
        max-width: calc(100% - var(--sms-sidebar-collapsed-width, 76px)) !important;
    }
    body:has(.module-button-grid) .sms-main {
        flex: 1 1 0% !important; min-height: 0 !important; height: 100% !important;
        width: 100% !important; max-width: 100% !important; overflow: hidden !important;
        padding: 0.4rem 1.25rem 0.35rem 1.25rem !important; display: flex !important; flex-direction: column !important; box-sizing: border-box !important;
    }
    body:has(.module-button-grid) .sms-footer {
        display: block !important; flex: 0 0 auto !important; width: 100% !important; max-width: 100% !important;
        padding: 0.35rem 1.25rem !important; font-size: 0.74rem !important;
        background: var(--sms-surface, #0d1e3d) !important;
        border-top: 1px solid var(--sms-border, rgba(59, 130, 246, 0.2)) !important; z-index: 10 !important;
    }
    .sms-main > .row.module-button-grid {
        width: calc(100% + var(--bs-gutter-x, 1rem)) !important;
        max-width: calc(100% + var(--bs-gutter-x, 1rem)) !important;
        margin-left: calc(-0.5 * var(--bs-gutter-x, 1rem)) !important;
        margin-right: calc(-0.5 * var(--bs-gutter-x, 1rem)) !important;
    }
    nav[aria-label="breadcrumb"] { flex: 0 0 auto !important; margin-bottom: 0.25rem !important; }
    nav[aria-label="breadcrumb"] .breadcrumb { margin-bottom: 0 !important; padding: 0 !important; font-size: 0.78rem !important; }
}
</style>
<?php
require_once __DIR__ . '/../../includes/module-index-grid.php';
require_once __DIR__ . '/../../includes/layout-end.php';
