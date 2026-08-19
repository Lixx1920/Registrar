<?php
/**
 * SMS 2 - Student Status Tracker
 * Module: Registrar
 * STATUS: Placeholder — Phase 8 will implement full status timeline
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';

regRequireAction('registrar.view');

$pageTitle    = 'Student Status Tracker';
$activeModule = 'registrar';
$activePage   = 'student-status-tracker';
$breadcrumbs  = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Student Status Tracker', 'url' => null],
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
                <i class="fas fa-user-check text-info me-2"></i>Student Status Tracker
            </h1>
            <p class="text-muted mb-0">Track and manage student enrollment status changes throughout their academic lifecycle</p>
        </div>
        <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill">
            <i class="fas fa-clock me-1"></i> Under Development
        </span>
    </div>

    <!-- Planned Features Card -->
    <div class="card border-start border-warning border-4 reg-shadow mb-4">
        <div class="card-body py-3">
            <h6 class="text-warning fw-semibold mb-3"><i class="fas fa-info-circle me-2"></i>Planned Features — Phase 8</h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-stream text-info mt-1"></i>
                        <div><strong>Status Timeline</strong><br><small class="text-muted">Chronological history of all student status changes</small></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-exchange-alt text-info mt-1"></i>
                        <div><strong>Status Transitions</strong><br><small class="text-muted">Active, LOA, Dropout, Graduated, Transferred, Dismissed</small></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-filter text-info mt-1"></i>
                        <div><strong>Filter by Status</strong><br><small class="text-muted">Browse and search students by current or historical status</small></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-file-export text-info mt-1"></i>
                        <div><strong>Status Report Export</strong><br><small class="text-muted">Export student status summary as PDF or CSV</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Skeleton: Status Summary Badges -->
    <div class="row g-3 mb-4" style="opacity:.35; pointer-events:none;">
        <?php
        $statuses = [
            ['Active','bg-success','38'],
            ['Leave of Absence','bg-warning','4'],
            ['Graduated','bg-primary','12'],
            ['Transferred','bg-secondary','2'],
            ['Dropout','bg-danger','1'],
        ];
        foreach ($statuses as $s):
        ?>
        <div class="col-6 col-md">
            <div class="reg-card text-center">
                <div class="reg-card-body py-3">
                    <span class="badge <?php echo $s[1]; ?> mb-2"><?php echo $s[0]; ?></span>
                    <h3 class="fw-bold mb-0"><?php echo $s[2]; ?></h3>
                    <small class="text-muted">Students</small>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Skeleton: Students Table + Timeline -->
    <div class="row g-4" style="opacity:.35; pointer-events:none;">
        <!-- Students List -->
        <div class="col-md-7">
            <div class="card reg-shadow">
                <div class="card-header bg-info" style="height:50px;"></div>
                <div class="table-responsive">
                    <table class="table reg-table mb-0">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Current Status</th>
                                <th>Since</th>
                                <th>Changed By</th>
                                <th>Actions</th>
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

        <!-- Status Timeline -->
        <div class="col-md-5">
            <div class="reg-card">
                <div class="card-header bg-info" style="height:50px;"></div>
                <div class="reg-card-body p-3">
                    <?php for ($i = 0; $i < 4; $i++): ?>
                    <div class="d-flex gap-3 mb-3">
                        <div class="bg-secondary rounded-circle flex-shrink-0" style="width:32px;height:32px;"></div>
                        <div class="flex-grow-1">
                            <div class="bg-secondary rounded mb-1" style="height:14px;width:70%;"></div>
                            <div class="bg-secondary rounded" style="height:12px;width:50%;"></div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Coming Soon Note -->
    <div class="alert alert-light border mt-4 text-center">
        <i class="fas fa-tools text-warning me-2"></i>
        <strong>This page is being developed.</strong> Full status timeline tracking and status change management will be available in <strong>Phase 8</strong>.
    </div>

</div>

<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
