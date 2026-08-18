<?php
/**
 * SMS 2 - Student Masterlist Generator
 * Module: Registrar
 * STATUS: Placeholder — Phase 10 will implement full export and filtering
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';

regRequireAction('registrar.view');

$pageTitle    = 'Student Masterlist Generator';
$activeModule = 'registrar';
$activePage   = 'student-masterlist-generator';
$breadcrumbs  = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Student Masterlist Generator', 'url' => null],
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
                <i class="fas fa-list-alt text-primary me-2"></i>Student Masterlist Generator
            </h1>
            <p class="text-muted mb-0">Generate, filter, and export student masterlists by program, year level, status, and school year</p>
        </div>
        <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill">
            <i class="fas fa-clock me-1"></i> Under Development
        </span>
    </div>

    <!-- Planned Features Card -->
    <div class="card border-start border-warning border-4 reg-shadow mb-4">
        <div class="card-body py-3">
            <h6 class="text-warning fw-semibold mb-3"><i class="fas fa-info-circle me-2"></i>Planned Features — Phase 10</h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-filter text-primary mt-1"></i>
                        <div><strong>Smart Filter Panel</strong><br><small class="text-muted">Filter by program, year level, section, status, school year</small></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-table text-primary mt-1"></i>
                        <div><strong>Live Preview Table</strong><br><small class="text-muted">See matching students in real time before exporting</small></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-file-export text-primary mt-1"></i>
                        <div><strong>PDF & CSV Export</strong><br><small class="text-muted">Export masterlist as a printable PDF or spreadsheet CSV</small></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-history text-primary mt-1"></i>
                        <div><strong>Export History</strong><br><small class="text-muted">Re-download previously generated masterlists</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Skeleton: Filter Panel + Preview -->
    <div class="row g-4" style="opacity:.35; pointer-events:none;">

        <!-- Filter Panel -->
        <div class="col-md-3">
            <div class="reg-card">
                <div class="card-header bg-primary text-white">
                    <strong><i class="fas fa-filter me-1"></i> Filter Options</strong>
                </div>
                <div class="reg-card-body p-3">
                    <?php
                    $filters = ['Program / Course','Year Level','Section','Status','School Year'];
                    foreach ($filters as $f):
                    ?>
                    <div class="mb-3">
                        <div class="bg-secondary rounded mb-1" style="height:12px;width:60%;"></div>
                        <div class="bg-secondary rounded" style="height:34px;width:100%;"></div>
                    </div>
                    <?php endforeach; ?>
                    <div class="d-flex gap-2 mt-2">
                        <div class="bg-primary rounded flex-grow-1" style="height:36px;"></div>
                        <div class="bg-secondary rounded" style="width:60px;height:36px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Table -->
        <div class="col-md-9">
            <div class="card reg-shadow">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary" style="height:50px;">
                </div>
                <div class="table-responsive">
                    <table class="table reg-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student No.</th>
                                <th>Full Name</th>
                                <th>Program</th>
                                <th>Year & Section</th>
                                <th>Status</th>
                                <th>Enrolled</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 0; $i < 6; $i++): ?>
                            <tr>
                                <?php for ($j = 0; $j < 7; $j++): ?>
                                <td><div class="bg-secondary rounded" style="height:16px;width:<?php echo 50 + rand(0,40); ?>%;"></div></td>
                                <?php endfor; ?>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Export Buttons -->
                <div class="card-footer d-flex gap-2 justify-content-end">
                    <div class="bg-danger rounded" style="width:120px;height:34px;"></div>
                    <div class="bg-success rounded" style="width:120px;height:34px;"></div>
                </div>
            </div>
        </div>

    </div>

    <!-- Coming Soon Note -->
    <div class="alert alert-light border mt-4 text-center">
        <i class="fas fa-tools text-warning me-2"></i>
        <strong>This page is being developed.</strong> Full student masterlist generation with PDF/CSV export will be available in <strong>Phase 10</strong>.
    </div>

</div>

<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
