<?php
/**
 * SMS 2 - Health Record Log
 * Module: Registrar
 * STATUS: Placeholder — Phase 4 will implement full CRUD
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';

regRequireAction('registrar.view');

$pageTitle    = 'Health Record Log';
$activeModule = 'registrar';
$activePage   = 'health-record-log';
$breadcrumbs  = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Health Record Log', 'url' => null],
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
                <i class="fas fa-heartbeat text-danger me-2"></i>Health Record Log
            </h1>
            <p class="text-muted mb-0">Manage student medical information, health summaries, and physical examination records</p>
        </div>
        <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill">
            <i class="fas fa-clock me-1"></i> Under Development
        </span>
    </div>

    <!-- Planned Features Card -->
    <div class="card border-start border-warning border-4 reg-shadow mb-4">
        <div class="card-body py-3">
            <h6 class="text-warning fw-semibold mb-3"><i class="fas fa-info-circle me-2"></i>Planned Features — Phase 4</h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-notes-medical text-danger mt-1"></i>
                        <div><strong>Health Summary</strong><br><small class="text-muted">Blood type, height, weight, allergies, chronic conditions</small></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-calendar-check text-danger mt-1"></i>
                        <div><strong>Physical Exam Log</strong><br><small class="text-muted">Date-stamped physical examination records with physician info</small></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-pills text-danger mt-1"></i>
                        <div><strong>Medications & Conditions</strong><br><small class="text-muted">Track ongoing medications and diagnosed conditions</small></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-ambulance text-danger mt-1"></i>
                        <div><strong>Emergency Medical Notes</strong><br><small class="text-muted">Critical health notes for emergency responders</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Skeleton: Health Summary Cards -->
    <div class="row g-3 mb-4" style="opacity:.35; pointer-events:none;">
        <?php
        $healthCards = [
            ['fa-tint','Blood Type','A+'],
            ['fa-ruler-vertical','Height','168 cm'],
            ['fa-weight','Weight','65 kg'],
            ['fa-exclamation-triangle','Allergies','None'],
        ];
        foreach ($healthCards as $card):
        ?>
        <div class="col-6 col-md-3">
            <div class="reg-card text-center">
                <div class="reg-card-body py-3">
                    <i class="fas <?php echo $card[0]; ?> fa-2x text-muted mb-2"></i>
                    <h6 class="text-muted mb-1"><?php echo $card[1]; ?></h6>
                    <h5 class="fw-bold mb-0"><?php echo $card[2]; ?></h5>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Skeleton: Records Table -->
    <div class="card reg-shadow" style="opacity:.35; pointer-events:none;">
        <div class="card-header bg-danger" style="height:50px;"></div>
        <div class="table-responsive">
            <table class="table reg-table mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Condition / Medication</th>
                        <th>Physician</th>
                        <th>Remarks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < 4; $i++): ?>
                    <tr>
                        <?php for ($j = 0; $j < 6; $j++): ?>
                        <td><div class="bg-secondary rounded" style="height:18px;width:<?php echo 50 + rand(0,40); ?>%;"></div></td>
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
        <strong>This page is being developed.</strong> Full health record management with medical history tracking will be available in <strong>Phase 4</strong>.
    </div>

</div>

<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
