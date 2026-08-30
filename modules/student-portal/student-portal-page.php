<?php
/**
 * SMS 2 - Student Portal Pages
 */
$studentPortalPage = $studentPortalPage ?? 'my-profile';

require_once __DIR__ . '/../../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/modules/crad/config/config.php';
require_once __DIR__ . '/../../includes/breadcrumbs.php';

$studentId = $_SESSION['student_id'] ?? 'S230000001';

$latestTitleApproval = null;
$researchCurrentStatus = 'Not Started';
$researchCurrentTitle = 'No title approval sent yet';
$researchCurrentUpdated = '';
$researchCurrentAdviser = '';
$researchCurrentProgress = 0;

try {
    $cradPdo = getCradDatabaseConnection();
    $titleStmt = $cradPdo->prepare(
        "SELECT proposed_title, status, adviser_name, coordinator_status, crad_status,
                sent_at, reviewed_at, coordinator_reviewed_at, crad_reviewed_at, updated_at
         FROM title_approvals
         WHERE student_id = :student_id
         ORDER BY id DESC
         LIMIT 1"
    );
    $titleStmt->execute([':student_id' => $studentId]);
    $latestTitleApproval = $titleStmt->fetch() ?: null;
} catch (Throwable $e) {
    error_log('Student dashboard title approval status load failed: ' . $e->getMessage());
}

if ($latestTitleApproval) {
    $titleStatus = (string) ($latestTitleApproval['status'] ?? '');
    $coordinatorStatus = (string) ($latestTitleApproval['coordinator_status'] ?? '');
    $cradStatus = (string) ($latestTitleApproval['crad_status'] ?? '');

    if (strcasecmp($titleStatus, 'Returned') === 0 || strcasecmp($coordinatorStatus, 'Returned') === 0 || strcasecmp($cradStatus, 'Returned') === 0) {
        $researchCurrentStatus = 'Returned';
    } elseif (strcasecmp($cradStatus, 'Approved') === 0) {
        $researchCurrentStatus = 'CRAD Approved';
    } elseif (strcasecmp($coordinatorStatus, 'Approved') === 0) {
        $researchCurrentStatus = 'Coordinator Approved';
    } elseif (strcasecmp($titleStatus, 'Approved') === 0) {
        $researchCurrentStatus = 'Adviser Approved';
    } elseif (strcasecmp($titleStatus, 'Pending') === 0 || !empty($latestTitleApproval['sent_at'])) {
        $researchCurrentStatus = 'Document Packet Sent';
    }

    $researchCurrentTitle = trim((string) ($latestTitleApproval['proposed_title'] ?? '')) ?: 'Research title approval';
    $researchCurrentAdviser = trim((string) ($latestTitleApproval['adviser_name'] ?? ''));
    $statusTimeRaw = (string) (
        $latestTitleApproval['crad_reviewed_at']
        ?: $latestTitleApproval['coordinator_reviewed_at']
        ?: $latestTitleApproval['reviewed_at']
        ?: $latestTitleApproval['sent_at']
        ?: $latestTitleApproval['updated_at']
        ?: ''
    );
    $statusTime = $statusTimeRaw !== '' ? strtotime($statusTimeRaw) : false;
    $researchCurrentUpdated = $statusTime ? date('F j, Y h:i A', $statusTime) : '';
}

if (($_GET['ajax'] ?? '') === 'research-status') {
    header('Content-Type: application/json');
    echo json_encode([
        'ok' => true,
        'title' => $researchCurrentTitle,
        'status' => $researchCurrentStatus,
        'adviser' => $researchCurrentAdviser !== '' ? $researchCurrentAdviser : 'For assignment',
        'updated_at' => $researchCurrentUpdated !== '' ? $researchCurrentUpdated : 'No activity yet',
        'progress' => $researchCurrentProgress,
    ]);
    exit;
}

// ── Research Forum payment check ─────────────────────────────────────────────
// In production, query your payments table. Here we check against the
// hardcoded payment history transactions (the "Research Forum" row).
$paymentTransactions = [
    ['ref' => 'OR-2026-0018', 'description' => 'Tuition Down Payment',  'amount' => 5000.00, 'status' => 'Paid', 'date' => 'Jul 5, 2026'],
    ['ref' => 'OR-2026-0009', 'description' => 'Registration Fee',       'amount' => 1500.00, 'status' => 'Paid', 'date' => 'Jun 20, 2026'],
    ['ref' => 'OR-2026-0003', 'description' => 'Laboratory Fee',         'amount' => 2500.00, 'status' => 'Paid', 'date' => 'Jun 15, 2026'],
    ['ref' => 'OR-2026-0001', 'description' => 'Research Forum',         'amount' => 800.00,  'status' => 'Paid', 'date' => 'May 28, 2026'],
];
$researchForumPaid = false;
foreach ($paymentTransactions as $txn) {
    if (
        stripos($txn['description'], 'Research Forum') !== false &&
        strtolower($txn['status']) === 'paid'
    ) {
        $researchForumPaid = true;
        break;
    }
}

$studentProfile = [
    'name' => 'Juan Dela Cruz',
    'student_id' => $studentId,
    'program' => 'Bachelor of Science in Information Technology',
    'year_level' => '2nd Year',
    'section' => 'BSIT 2A',
    'status' => 'Enrolled',
    'email' => 's230000001@bcp.edu.ph',
    'mobile' => '0917 000 0001',
    'address' => 'Novaliches, Quezon City',
    'guardian' => 'Maria Dela Cruz',
];

$studentPages = [
    'dashboard' => [
        'title' => 'Dashboard',
        'icon' => 'fa-tachometer-alt',
        'description' => 'View your enrollment, academic, finance, and research overview.',
    ],
    'my-profile' => [
        'title' => 'My Profile',
        'icon' => 'fa-user',
        'description' => 'Review and maintain your student information.',
    ],
    'student-id' => [
        'title' => 'Student ID',
        'icon' => 'fa-id-card',
        'description' => 'View your official student ID details and request reprint support.',
    ],
    'account-balance' => [
        'title' => 'Account Balance',
        'icon' => 'fa-wallet',
        'description' => 'Track current charges, payments, discounts, and remaining balance.',
    ],
    'class-schedule' => [
        'title' => 'Class Schedule',
        'icon' => 'fa-calendar-alt',
        'description' => 'See your weekly classes, rooms, and time blocks.',
    ],
    'academic-records' => [
        'title' => 'Academic Records',
        'icon' => 'fa-file-alt',
        'description' => 'Check your academic standing, units, and grade summary.',
    ],
    'subjects-professors' => [
        'title' => 'Subject & Professors',
        'icon' => 'fa-chalkboard-teacher',
        'description' => 'View enrolled subjects and assigned professors.',
    ],
    'payment-history' => [
        'title' => 'Payment History',
        'icon' => 'fa-receipt',
        'description' => 'Review official receipt records and payment transactions.',
    ],
    'grades-portal' => [
        'title' => 'Grades Portal',
        'icon' => 'fa-star-half-alt',
        'description' => 'View your official grades per subject, semester, and academic year.',
    ],
    'research-proposal-submission' => [
        'title' => 'Research Proposal Submission',
        'icon' => 'fa-flask',
        'description' => 'Submit your research proposal and track CRAD review status.',
    ],
    'request-documents' => [
        'title' => 'Request Documents',
        'icon' => 'fa-file-signature',
        'description' => 'Request official documents and track your request status.',
    ],
];

if (!isset($studentPages[$studentPortalPage])) {
    $studentPortalPage = 'dashboard';
}

$pageMeta = $studentPages[$studentPortalPage];
$processMessages = [
    'profile-update' => 'Profile update request has been prepared for registrar review.',
    'profile-correction' => 'Correction ticket has been submitted to the student records desk.',
    'id-print' => 'Student ID print request is now queued for validation.',
    'id-replacement' => 'Replacement ID request has been prepared for assessment.',
    'pay-now' => 'Payment process has been opened for the current balance.',
    'soa' => 'Statement of Account request has been generated.',
    'download-schedule' => 'Class schedule download has been prepared.',
    'schedule-conflict' => 'Schedule conflict report has been submitted for checking.',
    'copy-grades' => 'Copy of Grades request has been submitted.',
    'transcript' => 'Transcript request has been submitted to Registrar.',
    'consultation' => 'Consultation request has been prepared for your professor.',
    'subject-details' => 'Subject detail view has been opened.',
    'receipt' => 'Receipt download has been prepared.',
    'payment-issue' => 'Payment issue report has been submitted to Finance.',
    'doc-request-submitted' => 'Your document request has been successfully submitted and is pending payment/approval.',
];
$processKey = $_GET['process'] ?? '';
$processMessage = $processMessages[$processKey] ?? '';
$pageTitle = $pageMeta['title'];
$activeModule = 'student_portal';
$activePage = $studentPortalPage;
$breadcrumbs = [
    ['label' => 'Student Portal', 'url' => BASE_URL . '/modules/student-portal/pages/dashboard.php'],
    ['label' => $pageMeta['title'], 'url' => null],
];
$pageBannerIcon = $pageMeta['icon'];
$pageBannerDescription = $pageMeta['description'];

require_once __DIR__ . '/../../includes/layout-start.php';
?>

<?php renderBreadcrumbs($breadcrumbs); ?>

<div class="student-portal">
    <?php if ($processMessage !== ''): ?>
        <div class="alert alert-success student-process-alert" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($processMessage) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_GET['notice']) && $_GET['notice'] === 'research-forum-required'): ?>
        <div class="alert alert-warning student-process-alert" role="alert">
            <i class="fas fa-lock me-2"></i>You must pay the <strong>Research Forum</strong> fee before submitting research documents. Please complete payment to unlock access.
        </div>
    <?php endif; ?>

    <?php if ($studentPortalPage === 'dashboard'): ?>
        <div class="row g-3 mb-3 dashboard-stats">
            <div class="col-md-3">
                <section class="card stat-card primary">
                    <div class="card-body">
                        <h6 class="text-muted">Enrollment Status</h6>
                        <h4 class="fw-bold mb-0">Enrolled</h4>
                    </div>
                </section>
            </div>
            <div class="col-md-3">
                <section class="card stat-card success">
                    <div class="card-body">
                        <h6 class="text-muted">Current GWA</h6>
                        <h4 class="fw-bold mb-0">1.75</h4>
                    </div>
                </section>
            </div>
            <div class="col-md-3">
                <section class="card stat-card warning">
                    <div class="card-body">
                        <h6 class="text-muted">Balance</h6>
                        <h4 class="fw-bold mb-0">PHP 8,450.00</h4>
                    </div>
                </section>
            </div>
            <div class="col-md-3">
                <section class="card stat-card info">
                    <div class="card-body">
                        <h6 class="text-muted">Current Status</h6>
                        <h4 class="fw-bold mb-0 fs-6"><?= htmlspecialchars($researchCurrentStatus) ?></h4>
                    </div>
                </section>
            </div>
        </div>

        <style>
            .student-research-sent-card {
                border: 1px solid rgba(16,185,129,.28);
                border-radius: 12px;
                background: var(--sms-card-bg);
                box-shadow: 0 12px 34px rgba(15,23,42,.08);
                padding: 1.45rem 1.45rem 1.2rem;
                display: grid;
                grid-template-columns: 46px minmax(0,1fr) auto;
                gap: 1rem;
                align-items: center;
            }
            .student-research-sent-icon {
                width: 46px;
                height: 46px;
                border-radius: 12px;
                background: rgba(16,185,129,.14);
                color: #059669;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 1.2rem;
            }
            .student-research-kicker {
                display: block;
                color: var(--sms-text-muted);
                font-size: .72rem;
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: .04em;
                margin-bottom: .25rem;
            }
            .student-research-sent-card h2 {
                margin: 0;
                color: var(--sms-heading);
                font-size: 1.35rem;
                font-weight: 900;
            }
            .student-research-sent-card p {
                margin: .35rem 0 1rem;
                color: var(--sms-text-muted);
                font-weight: 650;
            }
            .student-research-grid {
                display: grid;
                grid-template-columns: repeat(3,minmax(0,1fr));
                gap: .75rem;
            }
            .student-research-field,
            .student-research-track {
                border: 1px solid var(--sms-border);
                border-radius: 8px;
                background: var(--sms-surface-muted);
                padding: .85rem;
            }
            .student-research-field span,
            .student-research-track span {
                display: block;
                color: var(--sms-text-muted);
                font-size: .72rem;
                font-weight: 900;
                text-transform: uppercase;
                margin-bottom: .3rem;
            }
            .student-research-field strong {
                display: block;
                color: var(--sms-heading);
                font-weight: 900;
                overflow-wrap: anywhere;
            }
            .student-research-track {
                grid-column: 1 / -1;
            }
            .student-research-track-head {
                display: flex;
                justify-content: space-between;
                gap: 1rem;
                align-items: center;
            }
            .student-research-track-head strong {
                color: var(--sms-heading);
                font-weight: 900;
            }
            .student-research-bar {
                height: 9px;
                border-radius: 999px;
                background: #dbe5f0;
                overflow: hidden;
                margin-top: .55rem;
            }
            .student-research-bar span {
                display: block;
                height: 100%;
                width: 0;
                border-radius: inherit;
                background: linear-gradient(90deg,#10b981,#2563eb);
                transition: width .35s ease;
            }
            .student-research-action {
                align-self: center;
                white-space: nowrap;
            }
            @media (max-width: 991px) {
                .student-research-sent-card { grid-template-columns: 1fr; }
                .student-research-grid { grid-template-columns: 1fr; }
                .student-research-action { width: 100%; }
            }
        </style>
        <section class="student-research-sent-card mb-3" data-research-status-card>
            <div class="student-research-sent-icon"><i class="fas fa-paper-plane"></i></div>
            <div>
                <span class="student-research-kicker">Sent to Adviser</span>
                <h2>Document packet sent</h2>
                <p>Your research title approval packet is queued in the current CRAD workflow.</p>
                <div class="student-research-grid">
                    <div class="student-research-field">
                        <span>Research Title</span>
                        <strong id="researchStatusTitle"><?= htmlspecialchars($researchCurrentTitle) ?></strong>
                    </div>
                    <div class="student-research-field">
                        <span>Current Status</span>
                        <strong id="researchStatusText"><?= htmlspecialchars($researchCurrentStatus) ?></strong>
                    </div>
                    <div class="student-research-field">
                        <span>Last Updated</span>
                        <strong id="researchStatusUpdated"><?= htmlspecialchars($researchCurrentUpdated !== '' ? $researchCurrentUpdated : 'No activity yet') ?></strong>
                    </div>
                    <div class="student-research-track">
                        <div class="student-research-track-head">
                            <span>Tracking Progress</span>
                            <strong id="researchStatusProgressText"><?= (int) $researchCurrentProgress ?>%</strong>
                        </div>
                        <div class="student-research-bar"><span id="researchStatusProgressBar" style="width: <?= max(0, min(100, (int) $researchCurrentProgress)) ?>%;"></span></div>
                    </div>
                </div>
            </div>
            <a class="btn btn-sms-primary student-research-action" href="<?= BASE_URL ?>/modules/student-portal/pages/research-proposal-submission.php">
                <i class="fas fa-flask me-2"></i>Research Proposal
            </a>
        </section>
        <script>
        (function () {
            var card = document.querySelector('[data-research-status-card]');
            if (!card) return;
            var title = document.getElementById('researchStatusTitle');
            var status = document.getElementById('researchStatusText');
            var updated = document.getElementById('researchStatusUpdated');
            var progressText = document.getElementById('researchStatusProgressText');
            var progressBar = document.getElementById('researchStatusProgressBar');
            function refreshResearchStatus() {
                var url = new URL(window.location.href);
                url.searchParams.set('ajax', 'research-status');
                fetch(url.toString(), {headers:{'Accept':'application/json'}, credentials:'same-origin', cache:'no-store'})
                    .then(function (res) { return res.ok ? res.json() : null; })
                    .then(function (data) {
                        if (!data || !data.ok) return;
                        var progress = Math.max(0, Math.min(100, Number(data.progress || 0)));
                        if (title) title.textContent = data.title || 'No title approval sent yet';
                        if (status) status.textContent = data.status || 'Not Started';
                        if (updated) updated.textContent = data.updated_at || 'No activity yet';
                        if (progressText) progressText.textContent = progress + '%';
                        if (progressBar) progressBar.style.width = progress + '%';
                    })
                    .catch(function () {});
            }
            refreshResearchStatus();
            window.setInterval(refreshResearchStatus, 5000);
        })();
        </script>

        <div class="row g-3">
            <div class="col-lg-7">
                <section class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-3">Today at a Glance</h5>
                        <div class="student-list">
                            <div><strong>Web Systems and Technologies</strong><span>8:00 AM - 9:30 AM · Lab 204</span><small>Prof. Maria Santos</small></div>
                            <div><strong>Database Management</strong><span>10:00 AM - 11:30 AM · Room 302</span><small>Prof. Carlo Reyes</small></div>
                            <div><strong>Systems Analysis and Design</strong><span>1:00 PM - 4:00 PM · Room 210</span><small>Hybrid session</small></div>
                        </div>
                        <div class="student-process-bar">
                            <a class="btn btn-sms-primary" href="<?= BASE_URL ?>/modules/student-portal/pages/class-schedule.php"><i class="fas fa-calendar-alt me-2"></i>View Schedule</a>
                            <a class="btn btn-outline-primary" href="<?= BASE_URL ?>/modules/student-portal/pages/grades-portal.php"><i class="fas fa-star-half-alt me-2"></i>Check Grades</a>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-lg-5">
                <section class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-3">Quick Actions</h5>
                        <div class="student-process-steps">
                            <div><span>1</span><strong>Submit research proposal</strong><p>Prepare your title proposal for CRAD review.</p></div>
                            <div><span>2</span><strong>Upload required documents</strong><p>Research Forum payment unlocks document submission.</p></div>
                            <div><span>3</span><strong>Monitor records</strong><p>Review balance, receipts, and academic standing.</p></div>
                        </div>
                        <div class="student-process-bar">
                            <a class="btn btn-sms-primary" href="<?= BASE_URL ?>/modules/student-portal/pages/research-proposal-submission.php"><i class="fas fa-flask me-2"></i>Research Proposal</a>
                            <a class="btn btn-outline-primary" href="<?= BASE_URL ?>/modules/student-portal/pages/account-balance.php"><i class="fas fa-wallet me-2"></i>Account Balance</a>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    <?php elseif ($studentPortalPage === 'my-profile'): ?>
        <div class="row g-3">
            <div class="col-lg-4">
                <section class="card student-profile-card h-100">
                    <div class="card-body">
                        <div class="student-avatar mb-3">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h5 class="fw-semibold mb-1"><?= htmlspecialchars($studentProfile['name']) ?></h5>
                        <p class="text-muted mb-3"><?= htmlspecialchars($studentProfile['program']) ?></p>
                        <span class="badge text-bg-success">Active Student</span>
                    </div>
                </section>
            </div>
            <div class="col-lg-8">
                <section class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-3">Student Information</h5>
                        <div class="student-record-grid">
                            <div><span>Student ID</span><strong><?= htmlspecialchars($studentProfile['student_id']) ?></strong></div>
                            <div><span>Program</span><strong><?= htmlspecialchars($studentProfile['program']) ?></strong></div>
                            <div><span>Year Level</span><strong><?= htmlspecialchars($studentProfile['year_level']) ?></strong></div>
                            <div><span>Section</span><strong><?= htmlspecialchars($studentProfile['section']) ?></strong></div>
                            <div><span>Email</span><strong><?= htmlspecialchars($studentProfile['email']) ?></strong></div>
                            <div><span>Mobile</span><strong><?= htmlspecialchars($studentProfile['mobile']) ?></strong></div>
                            <div><span>Address</span><strong><?= htmlspecialchars($studentProfile['address']) ?></strong></div>
                            <div><span>Guardian</span><strong><?= htmlspecialchars($studentProfile['guardian']) ?></strong></div>
                        </div>
                        <div class="student-process-bar">
                            <a class="btn btn-sms-primary" href="?process=profile-update"><i class="fas fa-pen me-2"></i>Request Profile Update</a>
                            <a class="btn btn-outline-primary" href="?process=profile-correction"><i class="fas fa-file-signature me-2"></i>Submit Correction Ticket</a>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    <?php elseif ($studentPortalPage === 'student-id'): ?>
        <div class="row g-3">
            <div class="col-lg-4">
                <section class="card student-id-card h-100">
                    <div class="card-body">
                        <div class="student-id-brand">
                            <i class="fas fa-graduation-cap"></i>
                            <span><?= htmlspecialchars(INSTITUTION) ?></span>
                        </div>
                        <div class="student-id-photo"><i class="fas fa-user-graduate"></i></div>
                        <h5><?= htmlspecialchars($studentProfile['name']) ?></h5>
                        <p><?= htmlspecialchars($studentProfile['program']) ?></p>
                        <div class="student-id-number"><?= htmlspecialchars($studentProfile['student_id']) ?></div>
                    </div>
                </section>
            </div>
            <div class="col-lg-8">
                <section class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-3">ID Process</h5>
                        <div class="student-process-steps">
                            <div><span>1</span><strong>Verify student details</strong><p>Name, program, year level, and section are checked before ID printing.</p></div>
                            <div><span>2</span><strong>Validate enrollment status</strong><p>Status must be Enrolled for the current school year.</p></div>
                            <div><span>3</span><strong>Request print or replacement</strong><p>Use this option for first printing, lost ID, or damaged ID replacement.</p></div>
                        </div>
                        <div class="student-process-bar">
                            <a class="btn btn-sms-primary" href="?process=id-print"><i class="fas fa-print me-2"></i>Request ID Print</a>
                            <a class="btn btn-outline-primary" href="?process=id-replacement"><i class="fas fa-redo me-2"></i>Request Replacement</a>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    <?php elseif ($studentPortalPage === 'account-balance'): ?>
        <div class="row g-3 mb-3 dashboard-stats">
            <div class="col-md-4">
                <section class="card stat-card warning"><div class="card-body"><h6 class="text-muted">Total Assessment</h6><h4 class="fw-bold mb-0">PHP 24,950.00</h4></div></section>
            </div>
            <div class="col-md-4">
                <section class="card stat-card success"><div class="card-body"><h6 class="text-muted">Total Paid</h6><h4 class="fw-bold mb-0">PHP 16,500.00</h4></div></section>
            </div>
            <div class="col-md-4">
                <section class="card stat-card primary"><div class="card-body"><h6 class="text-muted">Balance</h6><h4 class="fw-bold mb-0">PHP 8,450.00</h4></div></section>
            </div>
        </div>
        <section class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-3">Assessment Breakdown</h5>
                <div class="table-responsive">
                    <table class="table student-table align-middle mb-0">
                        <thead><tr><th>Fee</th><th class="text-end">Amount</th><th>Status</th></tr></thead>
                        <tbody>
                            <tr><td>Tuition Fee</td><td class="text-end">PHP 18,000.00</td><td><span class="badge text-bg-warning">Partial</span></td></tr>
                            <tr><td>Miscellaneous Fee</td><td class="text-end">PHP 4,450.00</td><td><span class="badge text-bg-warning">Partial</span></td></tr>
                            <tr><td>Laboratory Fee</td><td class="text-end">PHP 2,500.00</td><td><span class="badge text-bg-success">Paid</span></td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="student-process-bar">
                    <a class="btn btn-sms-primary" href="?process=pay-now"><i class="fas fa-credit-card me-2"></i>Proceed to Payment</a>
                    <a class="btn btn-outline-primary" href="?process=soa"><i class="fas fa-file-invoice me-2"></i>Request Statement of Account</a>
                </div>
            </div>
        </section>
    <?php elseif ($studentPortalPage === 'class-schedule'): ?>
        <section class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-3">Weekly Schedule</h5>
                <div class="table-responsive">
                    <table class="table student-table align-middle mb-0">
                        <thead><tr><th>Subject</th><th>Day</th><th>Time</th><th>Room</th><th>Mode</th></tr></thead>
                        <tbody>
                            <tr><td>Web Systems and Technologies</td><td>Mon / Wed</td><td>8:00 AM - 9:30 AM</td><td>Lab 204</td><td>Face to Face</td></tr>
                            <tr><td>Database Management</td><td>Tue / Thu</td><td>10:00 AM - 11:30 AM</td><td>Room 302</td><td>Face to Face</td></tr>
                            <tr><td>Systems Analysis and Design</td><td>Friday</td><td>1:00 PM - 4:00 PM</td><td>Room 210</td><td>Hybrid</td></tr>
                            <tr><td>Physical Education</td><td>Saturday</td><td>9:00 AM - 11:00 AM</td><td>Gym 1</td><td>Face to Face</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="student-process-bar">
                    <a class="btn btn-sms-primary" href="?process=download-schedule"><i class="fas fa-download me-2"></i>Download Schedule</a>
                    <a class="btn btn-outline-primary" href="?process=schedule-conflict"><i class="fas fa-exclamation-triangle me-2"></i>Report Schedule Conflict</a>
                </div>
            </div>
        </section>
    <?php elseif ($studentPortalPage === 'academic-records'): ?>
        <div class="row g-3">
            <div class="col-lg-5">
                <section class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-3">Academic Summary</h5>
                        <div class="student-record-grid">
                            <div><span>Current Semester</span><strong>1st Semester</strong></div>
                            <div><span>Completed Units</span><strong>54</strong></div>
                            <div><span>Current Units</span><strong>18</strong></div>
                            <div><span>GWA</span><strong>1.75</strong></div>
                            <div><span>Standing</span><strong>Good Standing</strong></div>
                            <div><span>Deficiencies</span><strong>None</strong></div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-lg-7">
                <section class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-3">Recent Grades</h5>
                        <div class="table-responsive">
                            <table class="table student-table align-middle mb-0">
                                <thead><tr><th>Subject</th><th>Units</th><th>Grade</th><th>Remarks</th></tr></thead>
                                <tbody>
                                    <tr><td>Programming 2</td><td>3</td><td>1.50</td><td>Passed</td></tr>
                                    <tr><td>Data Structures</td><td>3</td><td>1.75</td><td>Passed</td></tr>
                                    <tr><td>Discrete Mathematics</td><td>3</td><td>2.00</td><td>Passed</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="student-process-bar">
                            <a class="btn btn-sms-primary" href="?process=copy-grades"><i class="fas fa-file-download me-2"></i>Request Copy of Grades</a>
                            <a class="btn btn-outline-primary" href="?process=transcript"><i class="fas fa-scroll me-2"></i>Request Transcript</a>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    <?php elseif ($studentPortalPage === 'subjects-professors'): ?>
        <section class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-3">Enrolled Subjects and Assigned Professors</h5>
                <div class="student-list student-subject-list">
                    <div><strong>Web Systems and Technologies</strong><span>Prof. Maria Santos</span><small>Consultation: Wednesday, 2:00 PM - 4:00 PM</small></div>
                    <div><strong>Database Management</strong><span>Prof. Carlo Reyes</span><small>Consultation: Thursday, 1:00 PM - 3:00 PM</small></div>
                    <div><strong>Systems Analysis and Design</strong><span>Prof. Ana Lim</span><small>Consultation: Friday, 10:00 AM - 12:00 PM</small></div>
                    <div><strong>Networking Fundamentals</strong><span>Prof. Miguel Cruz</span><small>Consultation: Monday, 3:00 PM - 5:00 PM</small></div>
                </div>
                <div class="student-process-bar">
                    <a class="btn btn-sms-primary" href="?process=consultation"><i class="fas fa-envelope me-2"></i>Send Consultation Request</a>
                    <a class="btn btn-outline-primary" href="?process=subject-details"><i class="fas fa-book-reader me-2"></i>View Subject Details</a>
                </div>
            </div>
        </section>
    <?php elseif ($studentPortalPage === 'payment-history'): ?>
        <section class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-3">Official Payment Transactions</h5>
                <div class="table-responsive">
                    <table class="table student-table align-middle mb-0">
                        <thead><tr><th>Date</th><th>Reference No.</th><th>Description</th><th class="text-end">Amount</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php foreach ($paymentTransactions as $txn): ?>
                            <tr>
                                <td><?= htmlspecialchars($txn['date']) ?></td>
                                <td><?= htmlspecialchars($txn['ref']) ?></td>
                                <td><?= htmlspecialchars($txn['description']) ?></td>
                                <td class="text-end">PHP <?= number_format($txn['amount'], 2) ?></td>
                                <td><span class="badge text-bg-<?= strtolower($txn['status']) === 'paid' ? 'success' : 'warning' ?>"><?= htmlspecialchars($txn['status']) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="student-process-bar">
                    <a class="btn btn-sms-primary" href="?process=receipt"><i class="fas fa-receipt me-2"></i>Download Receipt</a>
                    <a class="btn btn-outline-primary" href="?process=payment-issue"><i class="fas fa-search-dollar me-2"></i>Report Payment Issue</a>
                </div>
            </div>
        </section>
    <?php elseif ($studentPortalPage === 'grades-portal'): ?>
        <div class="row g-3 mb-3 dashboard-stats">
            <div class="col-md-3">
                <section class="card stat-card primary"><div class="card-body"><h6 class="text-muted">Current GWA</h6><h4 class="fw-bold mb-0">1.75</h4></div></section>
            </div>
            <div class="col-md-3">
                <section class="card stat-card success"><div class="card-body"><h6 class="text-muted">Passed Subjects</h6><h4 class="fw-bold mb-0">18</h4></div></section>
            </div>
            <div class="col-md-3">
                <section class="card stat-card warning"><div class="card-body"><h6 class="text-muted">Current Subjects</h6><h4 class="fw-bold mb-0">6</h4></div></section>
            </div>
            <div class="col-md-3">
                <section class="card stat-card info"><div class="card-body"><h6 class="text-muted">Total Units Earned</h6><h4 class="fw-bold mb-0">54</h4></div></section>
            </div>
        </div>
        <section class="card mb-3">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-3">1st Semester — S.Y. 2026-2027 (Current)</h5>
                <div class="table-responsive">
                    <table class="table student-table align-middle mb-0">
                        <thead><tr><th>Subject Code</th><th>Subject Title</th><th>Units</th><th>Prelim</th><th>Midterm</th><th>Final</th><th>Grade</th><th>Remarks</th></tr></thead>
                        <tbody>
                            <tr><td>CS301</td><td>Web Systems and Technologies</td><td>3</td><td>1.50</td><td>1.75</td><td>—</td><td>—</td><td><span class="badge text-bg-secondary">In Progress</span></td></tr>
                            <tr><td>CS302</td><td>Database Management</td><td>3</td><td>1.75</td><td>2.00</td><td>—</td><td>—</td><td><span class="badge text-bg-secondary">In Progress</span></td></tr>
                            <tr><td>CS303</td><td>Systems Analysis and Design</td><td>3</td><td>1.50</td><td>1.50</td><td>—</td><td>—</td><td><span class="badge text-bg-secondary">In Progress</span></td></tr>
                            <tr><td>CS304</td><td>Networking Fundamentals</td><td>3</td><td>2.00</td><td>2.25</td><td>—</td><td>—</td><td><span class="badge text-bg-secondary">In Progress</span></td></tr>
                            <tr><td>PE3</td><td>Physical Education 3</td><td>2</td><td>1.25</td><td>1.50</td><td>—</td><td>—</td><td><span class="badge text-bg-secondary">In Progress</span></td></tr>
                            <tr><td>NSTP3</td><td>NSTP 3</td><td>3</td><td>1.00</td><td>1.25</td><td>—</td><td>—</td><td><span class="badge text-bg-secondary">In Progress</span></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        <section class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-3">2nd Semester — S.Y. 2025-2026</h5>
                <div class="table-responsive">
                    <table class="table student-table align-middle mb-0">
                        <thead><tr><th>Subject Code</th><th>Subject Title</th><th>Units</th><th>Prelim</th><th>Midterm</th><th>Final</th><th>Grade</th><th>Remarks</th></tr></thead>
                        <tbody>
                            <tr><td>CS201</td><td>Object-Oriented Programming</td><td>3</td><td>1.25</td><td>1.50</td><td>1.50</td><td>1.50</td><td><span class="badge text-bg-success">Passed</span></td></tr>
                            <tr><td>CS202</td><td>Data Structures and Algorithms</td><td>3</td><td>1.50</td><td>1.75</td><td>2.00</td><td>1.75</td><td><span class="badge text-bg-success">Passed</span></td></tr>
                            <tr><td>CS203</td><td>Discrete Mathematics</td><td>3</td><td>2.00</td><td>2.00</td><td>2.00</td><td>2.00</td><td><span class="badge text-bg-success">Passed</span></td></tr>
                            <tr><td>GE106</td><td>Ethics</td><td>3</td><td>1.75</td><td>2.00</td><td>1.75</td><td>1.75</td><td><span class="badge text-bg-success">Passed</span></td></tr>
                            <tr><td>PE2</td><td>Physical Education 2</td><td>2</td><td>1.50</td><td>1.75</td><td>1.50</td><td>1.50</td><td><span class="badge text-bg-success">Passed</span></td></tr>
                            <tr><td>NSTP2</td><td>NSTP 2</td><td>3</td><td>1.25</td><td>1.25</td><td>1.00</td><td>1.25</td><td><span class="badge text-bg-success">Passed</span></td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="student-process-bar">
                    <a class="btn btn-sms-primary" href="?process=copy-grades"><i class="fas fa-file-download me-2"></i>Download Grades Report</a>
                    <a class="btn btn-outline-primary" href="?process=transcript"><i class="fas fa-scroll me-2"></i>Request Official Transcript</a>
                </div>
            </div>
        </section>
    <?php elseif ($studentPortalPage === 'request-documents'): ?>
        <style>
            .doc-req-timeline {
                position: relative;
                padding-left: 2rem;
                list-style: none;
                margin-bottom: 0;
            }
            .doc-req-timeline::before {
                content: '';
                position: absolute;
                top: 8px;
                bottom: 8px;
                left: 7px;
                width: 2px;
                background: #e9ecef;
            }
            .doc-req-timeline li {
                position: relative;
                margin-bottom: 1.5rem;
                font-size: 0.9rem;
                color: #6c757d;
            }
            .doc-req-timeline li:last-child {
                margin-bottom: 0;
            }
            .doc-req-timeline li::before {
                content: '';
                position: absolute;
                left: -2rem;
                top: 0.25rem;
                width: 16px;
                height: 16px;
                border-radius: 50%;
                background: #fff;
                border: 3px solid #dee2e6;
                z-index: 1;
            }
            .doc-req-timeline li.completed::before {
                border-color: #0d6efd;
                background: #0d6efd;
            }
            .doc-req-timeline li.completed {
                color: #212529;
            }
            .doc-req-timeline li.current::before {
                border-color: #fd7e14;
                background: #fff;
            }
            .doc-req-timeline li.current {
                color: #fd7e14;
                font-weight: 600;
            }
            .doc-btn-box {
                border: 1px solid #ced4da;
                border-radius: 6px;
                padding: 1.25rem 0.5rem;
                text-align: center;
                cursor: pointer;
                transition: all 0.2s ease-in-out;
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: center;
                background: #fff;
            }
            .doc-btn-box:hover {
                border-color: #0d6efd;
                box-shadow: 0 4px 12px rgba(13, 110, 253, 0.1);
                transform: translateY(-2px);
            }
            .doc-btn-box h6 {
                font-size: 0.85rem;
                margin: 0;
                color: #495057;
                font-weight: 500;
            }
            .doc-btn-box:hover h6 {
                color: #0d6efd;
            }
            .border-dashed {
                border-bottom-style: dashed !important;
            }
        </style>
        
        <?php 
            $step = $_GET['step'] ?? 'select'; 
            $viewReq = $_GET['view_req'] ?? null;
            
            $db = db();
            $stmt = $db->prepare("SELECT id FROM reg_students WHERE student_number = ?");
            $stmt->execute([$studentId]);
            $studentNumericId = $stmt->fetchColumn();
            
            // Fallback for demo/testing: if the session's mock student_number isn't found in DB, use the first available student
            if (!$studentNumericId) {
                $studentNumericId = $db->query("SELECT id FROM reg_students LIMIT 1")->fetchColumn();
            }

            if ($processKey === 'doc-request-submitted' && $studentNumericId) {
                $rawDocType = $_POST['doc_type'] ?? 'coe';
                $dbDocTypes = ['coe' => 'COE', 'tor' => 'TOR', 'gmc' => 'Good Moral', 'cog' => 'COG'];
                $docType = $dbDocTypes[$rawDocType] ?? 'COE';
                
                $purpose = $_POST['purpose'] ?? 'Other';
                $copies = (int)($_POST['copies'] ?? 1);
                $channel = $_POST['receiving_method'] === 'Digital' ? 'email' : 'walk-in';
                $email = $_POST['student_email'] ?? null;
                $reqNo = 'REQ-' . date('Ymd-His');

                $stmt = $db->prepare("INSERT INTO reg_doc_requests (request_no, student_id, purpose, channel, student_email, paid, status) VALUES (?, ?, ?, ?, ?, 1, 'For Review')");
                $stmt->execute([$reqNo, $studentNumericId, $purpose, $channel, $email]);
                $reqId = $db->lastInsertId();

                $stmt = $db->prepare("INSERT INTO reg_doc_request_items (request_id, doc_type, copies) VALUES (?, ?, ?)");
                $stmt->execute([$reqId, $docType, $copies]);

                echo "<script>window.location.href='?view_req={$reqNo}';</script>";
                exit;
            }

            $stmt = $db->prepare("
                SELECT r.*, GROUP_CONCAT(i.doc_type SEPARATOR ', ') as doc_types 
                FROM reg_doc_requests r 
                LEFT JOIN reg_doc_request_items i ON r.id = i.request_id 
                WHERE r.student_id = ? 
                GROUP BY r.id 
                ORDER BY r.created_at DESC
            ");
            $stmt->execute([$studentNumericId]);
            $dbRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $mockRequests = [];
            foreach ($dbRequests as $r) {
                $statusStep = 1;
                if ($r['paid'] == 1) $statusStep = 2;
                if ($r['status'] === 'For Review' || $r['status'] === 'Processing') $statusStep = 3;
                if ($r['status'] === 'For Release') $statusStep = 4;
                if ($r['status'] === 'Released') $statusStep = 5;

                $mockRequests[$r['request_no']] = [
                    'doc' => $r['doc_types'] ?: 'Document',
                    'date' => date('d M Y', strtotime($r['created_at'])),
                    'status' => $r['status'],
                    'status_step' => $statusStep
                ];
            }
        ?>

        <div class="row g-4 mb-4">
            <!-- LEFT COLUMN: Actions & List -->
            <div class="col-lg-8">
                
                <?php if ($step === 'select'): ?>
                <!-- Request Official Documents Box -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold text-dark mb-1">Request Official Documents</h5>
                        <p class="text-muted small mb-4">Select A Document You Need to Request</p>
                        
                        <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <div class="doc-btn-box" onclick="window.location.href='?step=form&doc=coe'">
                                    <h6>Certificate of Enrollment</h6>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="doc-btn-box" onclick="window.location.href='?step=form&doc=tor'">
                                    <h6>Transcript of Records</h6>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="doc-btn-box" onclick="window.location.href='?step=form&doc=gmc'">
                                    <h6>Good Moral Certificate</h6>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="doc-btn-box" onclick="window.location.href='?step=form&doc=cog'">
                                    <h6>Copy of Grades</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- My Document Request Box -->
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold text-dark mb-4">My Document Request</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 student-table text-nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-3">Request No.</th>
                                        <th class="py-3">Document</th>
                                        <th class="py-3">Date Requested</th>
                                        <th class="py-3">Status</th>
                                        <th class="py-3"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($mockRequests as $reqId => $reqData): ?>
                                    <tr style="cursor:pointer;" onclick="window.location.href='?view_req=<?= $reqId ?>'" class="<?= $viewReq === $reqId ? 'table-primary' : '' ?>">
                                        <td class="fw-semibold text-primary"><?= $reqId ?></td>
                                        <td><?= $reqData['doc'] ?></td>
                                        <td><?= $reqData['date'] ?></td>
                                        <td>
                                            <?php if($reqData['status'] === 'Released'): ?>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success"><?= $reqData['status'] ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning"><?= $reqData['status'] ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <i class="fas fa-chevron-right text-muted"></i>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <?php elseif ($step === 'form'): ?>
                    <?php 
                        $docNames = [
                            'coe' => 'Certificate of Enrollment',
                            'tor' => 'Transcript of Records',
                            'gmc' => 'Good Moral Certificate',
                            'cog' => 'Copy of Grades'
                        ];
                        $selectedDoc = $_GET['doc'] ?? 'coe';
                        $docName = $docNames[$selectedDoc] ?? 'Document';
                    ?>
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold text-dark mb-4"><i class="fas fa-file-alt text-primary me-2"></i><?= htmlspecialchars($docName) ?> Request</h5>
                            <form action="?step=payment" method="POST">
                                <input type="hidden" name="doc_type" value="<?= htmlspecialchars($selectedDoc) ?>">
                                <div class="row g-4 mb-5">
                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold">Purpose of Request</label>
                                        <select class="form-select form-select-lg bg-light border-0" name="purpose" required>
                                            <option value="" disabled selected>Select a purpose...</option>
                                            <option value="Scholarship">Scholarship Application</option>
                                            <option value="Employment">Employment</option>
                                            <option value="Transfer">Transfer to another school</option>
                                            <option value="Personal">Personal Record</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Number of Copies</label>
                                        <input type="number" class="form-control form-control-lg bg-light border-0" name="copies" value="1" min="1" max="5" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Receiving Method</label>
                                        <select class="form-select form-select-lg bg-light border-0" name="receiving_method" required>
                                            <option value="Pickup" selected>Pick up at Registrar's Office</option>
                                            <option value="Digital">Digital Copy Through Email</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold">Notification Email Address</label>
                                        <input type="email" class="form-control form-control-lg bg-light border-0" name="student_email" placeholder="e.g. student@domain.com" required>
                                        <small class="text-muted">We will send updates about your request (or the digital copy itself) to this email.</small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <a href="?step=select" class="btn btn-light px-4 py-2 fw-semibold">Back</a>
                                    <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">Proceed to Payment <i class="fas fa-arrow-right ms-2"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>

                <?php elseif ($step === 'payment'): ?>
                    <?php 
                        $docNames = [
                            'coe' => 'Certificate of Enrollment',
                            'tor' => 'Transcript of Records',
                            'gmc' => 'Good Moral Certificate',
                            'cog' => 'Copy of Grades'
                        ];
                        $selectedDoc = $_POST['doc_type'] ?? 'coe';
                        $docName = $docNames[$selectedDoc] ?? 'Document';
                        $copies = (int)($_POST['copies'] ?? 1);
                        $rate = 150.00;
                        $total = $copies * $rate;
                        $purpose = $_POST['purpose'] ?? '';
                        $receivingMethod = $_POST['receiving_method'] ?? '';
                        $studentEmail = $_POST['student_email'] ?? '';
                    ?>
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold text-dark mb-4"><i class="fas fa-wallet text-primary me-2"></i>Mock Payment Summary</h5>
                            
                            <div class="alert alert-info border-0 rounded-3 mb-4">
                                <i class="fas fa-info-circle me-2"></i> You are requesting <strong><?= $copies ?> copy/copies</strong> of <strong><?= htmlspecialchars($docName) ?></strong>.
                            </div>
                            
                            <div class="bg-light p-4 rounded-3 mb-4">
                                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                                    <span class="text-muted">Document Processing Fee (PHP <?= number_format($rate, 2) ?> x <?= $copies ?>)</span>
                                    <span class="fw-bold text-dark">PHP <?= number_format($total, 2) ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fs-5 fw-bold text-dark">Total Amount Due</span>
                                    <span class="fs-4 fw-bold text-primary">PHP <?= number_format($total, 2) ?></span>
                                </div>
                            </div>
                            
                            <div class="text-center p-4 border rounded-3 border-primary bg-primary bg-opacity-10">
                                <h6 class="fw-bold mb-2 text-primary">Demo Payment Gateway</h6>
                                <p class="text-muted small mb-4">This is a mock payment interface for demonstration purposes.</p>
                                <form action="?process=doc-request-submitted" method="POST">
                                    <input type="hidden" name="doc_type" value="<?= htmlspecialchars($selectedDoc) ?>">
                                    <input type="hidden" name="copies" value="<?= $copies ?>">
                                    <input type="hidden" name="purpose" value="<?= htmlspecialchars($purpose) ?>">
                                    <input type="hidden" name="receiving_method" value="<?= htmlspecialchars($receivingMethod) ?>">
                                    <input type="hidden" name="student_email" value="<?= htmlspecialchars($studentEmail) ?>">
                                    <button type="submit" class="btn btn-success btn-lg px-5 shadow-sm fw-bold">
                                        <i class="fas fa-check-circle me-2"></i> Confirm & Pay Mock
                                    </button>
                                </form>
                            </div>
                            
                            <div class="mt-4 pt-3 border-top">
                                <a href="?step=select" class="btn btn-light px-4 fw-semibold text-muted">Cancel Request</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
            </div>

            <!-- RIGHT COLUMN: Details Sidebar -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 text-center">
                        <h6 class="fw-bold text-dark mb-0 fs-6">Document Details</h6>
                    </div>
                    <div class="card-body d-flex flex-column pt-3">
                        <hr class="mt-0 mb-4 text-muted opacity-25">
                        
                        <?php if ($viewReq && isset($mockRequests[$viewReq])): ?>
                            <?php $reqDetails = $mockRequests[$viewReq]; ?>
                            <div class="text-center mb-4">
                                <h6 class="fw-bold text-dark text-uppercase mb-1"><?= $reqDetails['doc'] ?></h6>
                                <p class="text-muted small mb-0">Request Date: <?= date('l, d M Y', strtotime($reqDetails['date'])) ?></p>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-dashed text-muted">
                                <span class="small">Request Track Number</span>
                                <span class="fw-bold text-dark small"><?= $viewReq ?></span>
                            </div>
                            
                            <div class="px-2 mb-4 flex-grow-1">
                                <ul class="doc-req-timeline">
                                    <li class="<?= $reqDetails['status_step'] >= 1 ? 'completed' : 'current' ?>">
                                        Document Request Submit
                                    </li>
                                    <li class="<?= $reqDetails['status_step'] > 2 ? 'completed' : ($reqDetails['status_step'] == 2 ? 'current' : '') ?>">
                                        Payment Process
                                    </li>
                                    <li class="<?= $reqDetails['status_step'] > 3 ? 'completed' : ($reqDetails['status_step'] == 3 ? 'current' : '') ?>">
                                        Registrar Approve
                                    </li>
                                    <li class="<?= $reqDetails['status_step'] > 4 ? 'completed' : ($reqDetails['status_step'] == 4 ? 'current' : '') ?>">
                                        Request Successfully Sent to Email/Pickup
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="mt-auto pt-3">
                                <?php if ($reqDetails['status_step'] == 2): ?>
                                    <button class="btn btn-outline-primary w-100 fw-bold rounded-3 py-2 shadow-sm text-uppercase fs-7" onclick="window.location.href='?step=payment'">PROCEED to PAYMENT PROCESS</button>
                                <?php elseif ($reqDetails['status_step'] == 3): ?>
                                    <div class="text-center text-muted small py-2 border rounded-3 fw-medium">Waiting For Approval. Check later (No Button)</div>
                                <?php elseif ($reqDetails['status_step'] == 4): ?>
                                    <button class="btn btn-outline-warning w-100 fw-bold rounded-3 py-2 shadow-sm" disabled><i class="fas fa-spinner fa-spin me-2"></i>Preparing for Release</button>
                                <?php elseif ($reqDetails['status_step'] >= 5): ?>
                                    <button class="btn btn-outline-success w-100 fw-bold rounded-3 py-2 shadow-sm" disabled><i class="fas fa-check-circle me-2"></i>Completed</button>
                                <?php endif; ?>
                            </div>
                            
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center h-100 flex-column text-muted opacity-50 py-5">
                                <i class="fas fa-hand-pointer fa-3x mb-3 text-secondary"></i>
                                <p class="text-center small fw-medium mb-0 px-4">Select a Documents Request<br>For More Details</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/layout-end.php'; ?>
