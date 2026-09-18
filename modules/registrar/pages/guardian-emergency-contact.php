<?php
/**
 * SMS 2 - Guardian & Emergency Contact
 * Module: Registrar
 * Manage student 3-part parent and primary emergency contact records.
 *
 * Part 1: Father's Information
 * Part 2: Mother's Information
 * Part 3: Guardian / Primary Emergency Contact
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/registrar-service.php';

regRequireAction('registrar.view');

$pageTitle    = 'Guardian & Emergency Contact';
$activeModule = 'registrar';
$activePage   = 'guardian-emergency-contact';

$db = db();

// Get student from query parameter (may be absent, e.g. clicked from sidebar)
$studentId = (int)($_GET['student_id'] ?? 0);
$student   = null;
$notFound  = false;

if ($studentId > 0) {
    $student = regGetStudent($studentId);
    if (!$student) {
        $notFound = true;
        $studentId = 0; // fall through to dashboard view
    }
}

$guardians       = [];
$fatherRecord    = null;
$motherRecord    = null;
$guardianRecord  = null;
$otherRecords    = [];
$allStudentsList = [];

if ($student) {
    // Single-student view: fetch this student's guardians ordered by primary & emergency status
    $stmt = $db->prepare("SELECT * FROM `reg_guardians` WHERE `student_id` = ? ORDER BY `is_primary` DESC, `is_emergency` DESC, `id` ASC");
    $stmt->execute([$studentId]);
    $guardians = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group into 3 parts:
    // Part 1: Father
    // Part 2: Mother
    // Part 3: Guardian / Primary Emergency Contact
    // Others: Any remaining relatives/siblings/contacts
    foreach ($guardians as $g) {
        $rel = trim($g['relationship'] ?? '');
        if (strcasecmp($rel, 'Father') === 0 && !$fatherRecord) {
            $fatherRecord = $g;
        } elseif (strcasecmp($rel, 'Mother') === 0 && !$motherRecord) {
            $motherRecord = $g;
        } elseif ((strcasecmp($rel, 'Guardian') === 0 || (int)($g['is_primary'] ?? 0) === 1) && !$guardianRecord) {
            $guardianRecord = $g;
        } else {
            $otherRecords[] = $g;
        }
    }
} else {
    // Dashboard view: every guardian record system-wide, joined with student info.
    $guardians = $db->query("
        SELECT g.*, s.student_number, s.first_name, s.last_name, s.program_course, s.status AS student_status
        FROM `reg_guardians` g
        JOIN `reg_students` s ON s.id = g.student_id
        WHERE s.status != 'Deleted'
        ORDER BY g.created_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $totalStudents          = (int)$db->query("SELECT COUNT(*) FROM `reg_students` WHERE `status` != 'Deleted'")->fetchColumn();
    $studentsWithGuardians   = (int)$db->query("
        SELECT COUNT(DISTINCT g.student_id) FROM `reg_guardians` g 
        JOIN `reg_students` s ON s.id = g.student_id 
        WHERE s.status != 'Deleted'
    ")->fetchColumn();
    $studentsWithoutGuardians = max(0, $totalStudents - $studentsWithGuardians);
    $emergencyCount          = count(array_filter($guardians, fn($g) => (int)($g['is_emergency'] ?? 0) === 1));

    // Active students for quick student selector modal
    $allStudentsList = $db->query("
        SELECT s.id, s.student_number, s.first_name, s.last_name, s.program_course, s.status AS student_status
        FROM `reg_students` s
        WHERE s.status != 'Deleted'
        ORDER BY s.last_name, s.first_name
        LIMIT 300
    ")->fetchAll(PDO::FETCH_ASSOC);
}

$breadcrumbs = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Guardian & Emergency Contact', 'url' => $student ? (BASE_URL . '/modules/registrar/pages/guardian-emergency-contact.php') : null],
];
if ($student) {
    $breadcrumbs[] = ['label' => $student['first_name'] . ' ' . $student['last_name'], 'url' => null];

    // Shows a "Back" pill on the right end of the dark page-title banner.
    $pageBannerBackUrl   = BASE_URL . '/modules/registrar/pages/guardian-emergency-contact.php';
    $pageBannerBackLabel = 'Back to Dashboard';
}

if (!function_exists('regInitials')) {
    function regInitials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $letters = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $letters .= strtoupper(substr($part, 0, 1));
        }
        return $letters !== '' ? $letters : 'ST';
    }
}

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<link href="<?php echo BASE_URL; ?>/assets/css/module-process-list.css?v=2" rel="stylesheet">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/modules/registrar/assets/css/registrar.css">

<style>
/* 3-Part Guardian Layout & Registrar UI Polish */
.guardian-parts-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1.25rem;
    padding: 1.25rem;
}
@media (max-width: 1024px) {
    .guardian-parts-grid {
        grid-template-columns: 1fr;
    }
}
.guardian-part-card {
    background: var(--sms-surface, rgba(15, 28, 52, 0.7));
    border: 1px solid var(--sms-border, rgba(255, 255, 255, 0.08));
    border-radius: 14px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}
.guardian-part-card:hover {
    border-color: rgba(59, 130, 246, 0.4);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}
.guardian-part-card.is-primary-part {
    border-color: rgba(245, 158, 11, 0.45);
    background: linear-gradient(180deg, rgba(245, 158, 11, 0.04) 0%, var(--sms-surface, rgba(15, 28, 52, 0.7)) 100%);
    box-shadow: 0 0 0 1px rgba(245, 158, 11, 0.2);
}
.guardian-part-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--sms-border, rgba(255, 255, 255, 0.06));
    background: rgba(255, 255, 255, 0.02);
}
.part-pill {
    display: inline-flex;
    align-items: center;
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 0.2rem 0.6rem;
    border-radius: 999px;
    background: rgba(59, 130, 246, 0.15);
    color: #60a5fa;
    border: 1px solid rgba(59, 130, 246, 0.25);
}
.part-pill.part-pill-primary {
    background: rgba(245, 158, 11, 0.18);
    color: #fbbf24;
    border-color: rgba(245, 158, 11, 0.35);
}
.part-title {
    font-size: 0.88rem;
    font-weight: 700;
    color: var(--sms-heading, #f8fafc);
    display: flex;
    align-items: center;
    gap: 0.45rem;
}
.guardian-part-body {
    padding: 1.25rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.guardian-person-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 0.75rem;
    margin-bottom: 1rem;
}
.guardian-person-info h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--sms-heading, #f8fafc);
    line-height: 1.3;
}
.guardian-person-info .rel-badge {
    font-size: 0.75rem;
    color: var(--sms-text-muted, #94a3b8);
    margin-top: 0.2rem;
    display: block;
}
.guardian-info-rows {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
}
.guardian-info-row {
    display: flex;
    align-items: flex-start;
    gap: 0.65rem;
    font-size: 0.84rem;
    color: var(--sms-text, #cbd5e1);
}
.guardian-info-row i {
    color: var(--sms-text-muted, #94a3b8);
    width: 16px;
    margin-top: 3px;
    flex-shrink: 0;
}
.guardian-info-row strong {
    font-weight: 600;
    color: var(--sms-heading, #f8fafc);
    word-break: break-word;
}
.guardian-part-footer {
    margin-top: auto;
    padding-top: 0.85rem;
    border-top: 1px solid var(--sms-border, rgba(255, 255, 255, 0.06));
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}
.btn-part-edit {
    width: 100%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.45rem;
    padding: 0.55rem 1rem;
    font-size: 0.82rem;
    font-weight: 600;
    border-radius: 9px;
    border: 1px solid var(--sms-border, rgba(255, 255, 255, 0.12));
    background: rgba(255, 255, 255, 0.04);
    color: var(--sms-text, #cbd5e1);
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
}
.btn-part-edit:hover {
    background: rgba(59, 130, 246, 0.12);
    border-color: rgba(59, 130, 246, 0.3);
    color: #60a5fa;
}
.btn-part-edit.btn-part-edit-primary {
    background: rgba(245, 158, 11, 0.12);
    border-color: rgba(245, 158, 11, 0.3);
    color: #fbbf24;
}
.btn-part-edit.btn-part-edit-primary:hover {
    background: rgba(245, 158, 11, 0.2);
    border-color: rgba(245, 158, 11, 0.45);
    color: #f59e0b;
}

/* Empty state cards */
.guardian-part-empty {
    padding: 2.25rem 1.25rem;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex: 1;
    background: rgba(255, 255, 255, 0.015);
}
.guardian-empty-icon {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.04);
    border: 1px dashed var(--sms-border, rgba(255, 255, 255, 0.15));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    color: var(--sms-text-muted, #94a3b8);
    margin-bottom: 0.85rem;
}
.guardian-empty-icon.primary {
    background: rgba(245, 158, 11, 0.08);
    border-color: rgba(245, 158, 11, 0.28);
    color: #f59e0b;
}
.guardian-part-empty h4 {
    margin: 0 0 0.35rem;
    font-size: 0.96rem;
    font-weight: 700;
    color: var(--sms-heading, #f8fafc);
}
.guardian-part-empty p {
    margin: 0 0 1.15rem;
    font-size: 0.8rem;
    color: var(--sms-text-muted, #94a3b8);
    max-width: 250px;
    line-height: 1.45;
}

/* Badges and pills */
.guardian-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 0.22rem 0.6rem;
    border-radius: 999px;
    white-space: nowrap;
}
.guardian-pill.primary, .guardian-pill.guardian-pill-primary {
    background: rgba(245, 158, 11, 0.16);
    color: #fbbf24;
    border: 1px solid rgba(245, 158, 11, 0.32);
}
.guardian-pill.emergency {
    background: rgba(239, 68, 68, 0.14);
    color: #f87171;
    border: 1px solid rgba(239, 68, 68, 0.28);
}
.guardian-pill.neutral {
    background: rgba(148, 163, 184, 0.12);
    color: #94a3b8;
    border: 1px solid rgba(148, 163, 184, 0.2);
}

/* 3-Parts Modal Sections */
.modal-part-section {
    border: 1px solid var(--sms-border, rgba(255, 255, 255, 0.08));
    border-radius: 12px;
    padding: 1.15rem;
    margin-bottom: 1.15rem;
    background: rgba(255, 255, 255, 0.02);
}
.modal-part-section.is-primary {
    border-color: rgba(245, 158, 11, 0.35);
    background: rgba(245, 158, 11, 0.025);
}
.modal-part-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.9rem;
    padding-bottom: 0.6rem;
    border-bottom: 1px solid var(--sms-border, rgba(255, 255, 255, 0.06));
}
.modal-part-header h6 {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--sms-heading, #f8fafc);
    display: flex;
    align-items: center;
    gap: 0.45rem;
}

/* Filter pills bar */
.guardian-filter-pills {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-bottom: 1rem;
}
.guardian-filter-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.4rem 0.85rem;
    font-size: 0.82rem;
    font-weight: 600;
    border-radius: 999px;
    border: 1px solid var(--sms-border, rgba(255, 255, 255, 0.1));
    background: transparent;
    color: var(--sms-text-muted, #94a3b8);
    cursor: pointer;
    transition: all 0.15s ease;
}
.guardian-filter-btn:hover {
    color: var(--sms-heading, #f8fafc);
    border-color: var(--sms-border-soft, rgba(255, 255, 255, 0.2));
}
.guardian-filter-btn.active {
    background: rgba(59, 130, 246, 0.15);
    border-color: #3b82f6;
    color: #60a5fa;
    font-weight: 700;
}

/* Status tags in table */
.guardian-status {
    display: inline-flex;
    align-items: center;
    padding: 2px 8px;
    border-radius: 999px;
    font-size: 0.74rem;
    font-weight: 600;
}
.guardian-status.primary {
    background: rgba(245, 158, 11, 0.16);
    color: #fbbf24;
    border: 1px solid rgba(245, 158, 11, 0.28);
}
.guardian-status.emergency {
    background: rgba(239, 68, 68, 0.14);
    color: #f87171;
    border: 1px solid rgba(239, 68, 68, 0.25);
}
.guardian-status.neutral {
    background: rgba(148, 163, 184, 0.12);
    color: #94a3b8;
    border: 1px solid rgba(148, 163, 184, 0.2);
}
.guardian-role {
    font-weight: 600;
    color: var(--sms-heading, #f8fafc);
}
</style>

<?php renderBreadcrumbs($breadcrumbs); ?>

<div class="container-fluid py-4<?php echo $student ? ' guardian-student-page' : ''; ?>">
<div class="mpl" data-mpl>

<?php if (!$student): ?>

    <!-- ================= DASHBOARD VIEW ================= -->
    <div class="mpl-top">
        <div>
            <h1 style="font-size:1.5rem;font-weight:800;color:var(--sms-heading);margin-bottom:0.25rem;">
                <i class="fas fa-shield-alt text-primary me-2"></i> Guardian &amp; Emergency Contact Directory
            </h1>
            <p>Manage 3-part parent and primary emergency contact records for enrolled students.</p>
        </div>
        <div class="mpl-toolbar">
            <button type="button" class="mpl-btn mpl-btn-primary" onclick="openSelectStudentModal()">
                <i class="fas fa-user-edit" aria-hidden="true"></i> Manage Student Contacts
            </button>
            <a class="mpl-btn mpl-btn-ghost" href="<?php echo BASE_URL; ?>/modules/registrar/pages/student-information-system.php">
                <i class="fas fa-arrow-left" aria-hidden="true"></i> Back to Student Records
            </a>
        </div>
    </div>

    <?php if ($notFound): ?>
    <div class="mpl-alert mpl-alert-auto" style="border-color: rgba(217, 119, 6, 0.28); background: rgba(245, 158, 11, 0.08); color: #92400e;">
        <i class="fas fa-exclamation-triangle"></i> Student #<?php echo (int)($_GET['student_id'] ?? 0); ?> was not found.
    </div>
    <?php endif; ?>

    <section class="mpl-stats" aria-label="Guardian summary">
        <article class="mpl-stat">
            <div class="mpl-stat-icon blue"><i class="fas fa-user-friends"></i></div>
            <div>
                <span>Total Guardians</span>
                <strong><?php echo count($guardians); ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon green"><i class="fas fa-user-check"></i></div>
            <div>
                <span>Students With Guardians</span>
                <strong><?php echo $studentsWithGuardians; ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon amber"><i class="fas fa-exclamation-circle"></i></div>
            <div>
                <span>Students Without Guardians</span>
                <strong><?php echo $studentsWithoutGuardians; ?></strong>
            </div>
        </article>
        <article class="mpl-stat">
            <div class="mpl-stat-icon purple"><i class="fas fa-bell"></i></div>
            <div>
                <span>Emergency Contacts</span>
                <strong><?php echo $emergencyCount; ?></strong>
            </div>
        </article>
    </section>

    <div class="mpl-filters">
        <label class="mpl-search">
            <i class="fas fa-search"></i>
            <input type="search" id="dashboardFilter" placeholder="Search by student number, name, guardian, or contact..." aria-label="Search guardian records">
        </label>
        <a class="mpl-refresh" href="?"><i class="fas fa-sync-alt" aria-hidden="true"></i> Refresh</a>
    </div>

    <!-- Role Filter Pills -->
    <div class="guardian-filter-pills">
        <button type="button" class="guardian-filter-btn active" onclick="filterDashboardRole('all', this)">
            <i class="fas fa-list"></i> All Records (<?php echo count($guardians); ?>)
        </button>
        <button type="button" class="guardian-filter-btn" onclick="filterDashboardRole('Father', this)">
            <i class="fas fa-user-tie"></i> Fathers
        </button>
        <button type="button" class="guardian-filter-btn" onclick="filterDashboardRole('Mother', this)">
            <i class="fas fa-female"></i> Mothers
        </button>
        <button type="button" class="guardian-filter-btn" onclick="filterDashboardRole('Guardian', this)">
            <i class="fas fa-shield-alt"></i> Guardians (Primary)
        </button>
        <button type="button" class="guardian-filter-btn" onclick="filterDashboardRole('other', this)">
            <i class="fas fa-users"></i> Other Contacts
        </button>
    </div>

    <section class="mpl-panel">
        <div class="mpl-panel-head">
            <div>
                <h2>All Contact Records</h2>
                <p><?php echo count($guardians); ?> total record(s) on file</p>
            </div>
        </div>
        <div class="mpl-table-wrap">
            <table class="mpl-table" id="dashboardTable">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Contact Name</th>
                        <th>Relationship</th>
                        <th>Phone Number</th>
                        <th>Email</th>
                        <th>Priority Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($guardians)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center;color:var(--sms-text-muted);padding:2rem;">
                            <i class="fas fa-info-circle"></i> No guardian records in the system yet.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($guardians as $g): ?>
                    <tr class="dashboard-row" 
                        data-relationship="<?php echo htmlspecialchars($g['relationship'] ?? ''); ?>"
                        data-search="<?php echo htmlspecialchars(strtolower(($g['student_number'] ?? '') . ' ' . ($g['last_name'] ?? '') . ' ' . ($g['first_name'] ?? '') . ' ' . ($g['full_name'] ?? '') . ' ' . ($g['relationship'] ?? '') . ' ' . ($g['contact'] ?? '') . ' ' . ($g['email'] ?? ''))); ?>">
                        <td>
                            <div class="mpl-person">
                                <span class="mpl-avatar"><?php echo htmlspecialchars(substr(($g['first_name'] ?? 'S'), 0, 1) . substr(($g['last_name'] ?? 'T'), 0, 1)); ?></span>
                                <div>
                                    <strong><?php echo htmlspecialchars(($g['last_name'] ?? '') . ', ' . ($g['first_name'] ?? '')); ?></strong>
                                    <div style="display:flex;align-items:center;gap:0.35rem;margin-top:2px;">
                                        <small><?php echo htmlspecialchars($g['student_number'] ?? ''); ?></small>
                                        <?php if (!empty($g['student_status'])): ?>
                                            <span class="guardian-status <?php echo strtolower($g['student_status']) === 'active' ? 'primary' : 'neutral'; ?>" style="font-size:0.68rem;padding:1px 6px;"><?php echo htmlspecialchars($g['student_status']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <strong style="color:var(--sms-heading);"><?php echo htmlspecialchars($g['full_name'] ?? '—'); ?></strong>
                        </td>
                        <td>
                            <span class="guardian-role">
                                <?php
                                $rel = $g['relationship'] ?? 'Guardian';
                                if ($rel === 'Father') echo '<i class="fas fa-user-tie text-info me-1"></i> Father';
                                elseif ($rel === 'Mother') echo '<i class="fas fa-female text-danger me-1"></i> Mother';
                                elseif ($rel === 'Guardian') echo '<i class="fas fa-shield-alt text-warning me-1"></i> Guardian';
                                else echo '<i class="fas fa-user text-muted me-1"></i> ' . htmlspecialchars($rel);
                                ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($g['contact'])): ?>
                                <span><i class="fas fa-phone text-muted me-1"></i> <?php echo htmlspecialchars($g['contact']); ?></span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($g['email'])): ?>
                                <span><i class="fas fa-envelope text-muted me-1"></i> <?php echo htmlspecialchars($g['email']); ?></span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ((int)($g['is_primary'] ?? 0) === 1): ?>
                                <span class="guardian-status primary"><i class="fas fa-star me-1"></i> Primary Contact</span>
                            <?php elseif ((int)($g['is_emergency'] ?? 0) === 1): ?>
                                <span class="guardian-status emergency"><i class="fas fa-bell me-1"></i> Emergency</span>
                            <?php else: ?>
                                <span class="guardian-status neutral">Additional</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="mpl-actions">
                                <a href="guardian-emergency-contact.php?student_id=<?php echo (int)$g['student_id']; ?>" title="Manage student 3-part contacts" aria-label="Manage student 3-part contacts">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

<?php else: ?>

    <!-- ================= SINGLE STUDENT 3-PART VIEW ================= -->
    <div class="mpl-top">
        <div>
            <h1 style="font-size:1.5rem;font-weight:800;color:var(--sms-heading);margin-bottom:0.25rem;">
                <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
            </h1>
            <p>
                Student #<?php echo htmlspecialchars($student['student_number']); ?> &bull; 
                <?php echo htmlspecialchars($student['program_course'] ?? 'No Program Assigned'); ?> &bull; 
                3-Part Emergency Contacts
            </p>
        </div>
        <div class="mpl-toolbar">
            <button type="button" class="mpl-btn mpl-btn-primary" onclick="openThreePartsModal()">
                <i class="fas fa-edit" aria-hidden="true"></i> Update 3 Parts Together
            </button>
            <button type="button" class="mpl-btn mpl-btn-ghost" onclick="openAddModal()">
                <i class="fas fa-plus" aria-hidden="true"></i> Add Other Contact
            </button>
            <a class="mpl-btn mpl-btn-ghost" href="guardian-emergency-contact.php">
                <i class="fas fa-arrow-left" aria-hidden="true"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Student Snapshot Overview -->
    <section class="mpl-panel" style="margin-bottom: 1.25rem;">
        <div class="mpl-panel-head">
            <div>
                <h2>Student Information Snapshot</h2>
                <p>Enrolled student details linked to emergency contacts.</p>
            </div>
            <div>
                <span class="guardian-status <?php echo strtolower($student['status'] ?? '') === 'active' ? 'primary' : 'neutral'; ?>">
                    <?php echo htmlspecialchars($student['status'] ?? 'Active'); ?>
                </span>
            </div>
        </div>
        <div class="guardian-summary">
            <div>
                <span>Student Number &amp; Name</span>
                <strong><?php echo htmlspecialchars($student['student_number'] . ' - ' . $student['first_name'] . ' ' . $student['last_name']); ?></strong>
            </div>
            <div>
                <span>Program / Degree</span>
                <strong><?php echo htmlspecialchars($student['program_course'] ?? '—'); ?></strong>
            </div>
            <div>
                <span>Year &amp; Section</span>
                <strong><?php echo htmlspecialchars($student['year_section'] ?? '—'); ?></strong>
            </div>
            <div>
                <span>Total Recorded Contacts</span>
                <strong><?php echo count($guardians); ?> Contact(s)</strong>
            </div>
        </div>
    </section>

    <!-- 3-PART GUARDIAN & EMERGENCY CONTACT SECTION -->
    <section class="mpl-panel" style="margin-bottom: 1.25rem;">
        <div class="mpl-panel-head">
            <div>
                <h2><i class="fas fa-user-shield text-primary me-2"></i> 3-Part Emergency Contacts</h2>
                <p>Standardized contact records: Father, Mother, and designated Primary Guardian / Emergency Contact.</p>
            </div>
            <div>
                <button type="button" class="mpl-btn mpl-btn-sm mpl-btn-primary" onclick="openThreePartsModal()">
                    <i class="fas fa-sliders-h"></i> Manage 3 Parts
                </button>
            </div>
        </div>

        <div class="guardian-parts-grid">

            <!-- PART 1: FATHER'S INFORMATION -->
            <article class="guardian-part-card" id="part-father">
                <div class="guardian-part-header">
                    <div class="d-flex align-items-center gap-2">
                        <span class="part-pill">Part 1</span>
                        <span class="part-title"><i class="fas fa-user-tie text-info"></i> Father's Info</span>
                    </div>
                    <span class="guardian-pill neutral">Parent</span>
                </div>

                <?php if ($fatherRecord): ?>
                <div class="guardian-part-body">
                    <div class="guardian-person-top">
                        <div class="guardian-person-info">
                            <h3><?php echo htmlspecialchars($fatherRecord['full_name']); ?></h3>
                            <span class="rel-badge"><i class="fas fa-check-circle text-success me-1"></i> Recorded Father</span>
                        </div>
                        <div class="mpl-actions">
                            <a href="javascript:void(0)" onclick="openEditModal(<?php echo (int)$fatherRecord['id']; ?>)" title="Edit Father" aria-label="Edit Father"><i class="fas fa-pen"></i></a>
                            <a class="danger" href="javascript:void(0)" onclick="deleteGuardian(<?php echo (int)$fatherRecord['id']; ?>)" title="Delete Record" aria-label="Delete Record"><i class="fas fa-trash"></i></a>
                        </div>
                    </div>

                    <div class="guardian-info-rows">
                        <div class="guardian-info-row">
                            <i class="fas fa-phone"></i>
                            <div>
                                <small class="text-muted d-block">Contact Number</small>
                                <strong><?php echo htmlspecialchars($fatherRecord['contact'] ?: 'Not provided'); ?></strong>
                            </div>
                        </div>
                        <div class="guardian-info-row">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <small class="text-muted d-block">Email Address</small>
                                <strong><?php echo htmlspecialchars($fatherRecord['email'] ?: 'Not provided'); ?></strong>
                            </div>
                        </div>
                        <div class="guardian-info-row">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <small class="text-muted d-block">Address</small>
                                <strong><?php echo htmlspecialchars($fatherRecord['address'] ?: 'Not provided'); ?></strong>
                            </div>
                        </div>
                    </div>

                    <div class="guardian-part-footer">
                        <span class="guardian-pill emergency"><i class="fas fa-bell"></i> Emergency Contact</span>
                        <button type="button" class="btn-part-edit" style="width:auto;padding:0.35rem 0.75rem;" onclick="openEditModal(<?php echo (int)$fatherRecord['id']; ?>)">
                            <i class="fas fa-pen"></i> Edit
                        </button>
                    </div>
                </div>
                <?php else: ?>
                <div class="guardian-part-empty">
                    <div class="guardian-empty-icon"><i class="fas fa-user-tie"></i></div>
                    <h4>No Father on File</h4>
                    <p>Contact information for father has not been registered yet.</p>
                    <button type="button" class="mpl-btn mpl-btn-sm mpl-btn-ghost" onclick="openAddModalFor('Father')">
                        <i class="fas fa-plus"></i> Add Father
                    </button>
                </div>
                <?php endif; ?>
            </article>

            <!-- PART 2: MOTHER'S INFORMATION -->
            <article class="guardian-part-card" id="part-mother">
                <div class="guardian-part-header">
                    <div class="d-flex align-items-center gap-2">
                        <span class="part-pill">Part 2</span>
                        <span class="part-title"><i class="fas fa-female text-danger"></i> Mother's Info</span>
                    </div>
                    <span class="guardian-pill neutral">Parent</span>
                </div>

                <?php if ($motherRecord): ?>
                <div class="guardian-part-body">
                    <div class="guardian-person-top">
                        <div class="guardian-person-info">
                            <h3><?php echo htmlspecialchars($motherRecord['full_name']); ?></h3>
                            <span class="rel-badge"><i class="fas fa-check-circle text-success me-1"></i> Recorded Mother</span>
                        </div>
                        <div class="mpl-actions">
                            <a href="javascript:void(0)" onclick="openEditModal(<?php echo (int)$motherRecord['id']; ?>)" title="Edit Mother" aria-label="Edit Mother"><i class="fas fa-pen"></i></a>
                            <a class="danger" href="javascript:void(0)" onclick="deleteGuardian(<?php echo (int)$motherRecord['id']; ?>)" title="Delete Record" aria-label="Delete Record"><i class="fas fa-trash"></i></a>
                        </div>
                    </div>

                    <div class="guardian-info-rows">
                        <div class="guardian-info-row">
                            <i class="fas fa-phone"></i>
                            <div>
                                <small class="text-muted d-block">Contact Number</small>
                                <strong><?php echo htmlspecialchars($motherRecord['contact'] ?: 'Not provided'); ?></strong>
                            </div>
                        </div>
                        <div class="guardian-info-row">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <small class="text-muted d-block">Email Address</small>
                                <strong><?php echo htmlspecialchars($motherRecord['email'] ?: 'Not provided'); ?></strong>
                            </div>
                        </div>
                        <div class="guardian-info-row">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <small class="text-muted d-block">Address</small>
                                <strong><?php echo htmlspecialchars($motherRecord['address'] ?: 'Not provided'); ?></strong>
                            </div>
                        </div>
                    </div>

                    <div class="guardian-part-footer">
                        <span class="guardian-pill emergency"><i class="fas fa-bell"></i> Emergency Contact</span>
                        <button type="button" class="btn-part-edit" style="width:auto;padding:0.35rem 0.75rem;" onclick="openEditModal(<?php echo (int)$motherRecord['id']; ?>)">
                            <i class="fas fa-pen"></i> Edit
                        </button>
                    </div>
                </div>
                <?php else: ?>
                <div class="guardian-part-empty">
                    <div class="guardian-empty-icon"><i class="fas fa-female"></i></div>
                    <h4>No Mother on File</h4>
                    <p>Contact information for mother has not been registered yet.</p>
                    <button type="button" class="mpl-btn mpl-btn-sm mpl-btn-ghost" onclick="openAddModalFor('Mother')">
                        <i class="fas fa-plus"></i> Add Mother
                    </button>
                </div>
                <?php endif; ?>
            </article>

            <!-- PART 3: GUARDIAN / PRIMARY EMERGENCY CONTACT -->
            <article class="guardian-part-card is-primary-part" id="part-guardian">
                <div class="guardian-part-header">
                    <div class="d-flex align-items-center gap-2">
                        <span class="part-pill part-pill-primary">Part 3</span>
                        <span class="part-title"><i class="fas fa-shield-alt text-warning"></i> Guardian Contact</span>
                    </div>
                    <span class="guardian-pill guardian-pill-primary">
                        <i class="fas fa-star"></i> Primary Contact
                    </span>
                </div>

                <?php if ($guardianRecord): ?>
                <div class="guardian-part-body">
                    <div class="guardian-person-top">
                        <div class="guardian-person-info">
                            <h3><?php echo htmlspecialchars($guardianRecord['full_name']); ?></h3>
                            <span class="rel-badge" style="color:#fbbf24;">
                                <i class="fas fa-shield-alt me-1"></i> <?php echo htmlspecialchars($guardianRecord['relationship'] ?? 'Guardian'); ?>
                            </span>
                        </div>
                        <div class="mpl-actions">
                            <a href="javascript:void(0)" onclick="openEditModal(<?php echo (int)$guardianRecord['id']; ?>)" title="Edit Guardian" aria-label="Edit Guardian"><i class="fas fa-pen"></i></a>
                            <a class="danger" href="javascript:void(0)" onclick="deleteGuardian(<?php echo (int)$guardianRecord['id']; ?>)" title="Delete Record" aria-label="Delete Record"><i class="fas fa-trash"></i></a>
                        </div>
                    </div>

                    <div class="guardian-info-rows">
                        <div class="guardian-info-row">
                            <i class="fas fa-phone text-warning"></i>
                            <div>
                                <small class="text-muted d-block">Primary Contact Number</small>
                                <strong style="color:#fbbf24;"><?php echo htmlspecialchars($guardianRecord['contact'] ?: 'Not provided'); ?></strong>
                            </div>
                        </div>
                        <div class="guardian-info-row">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <small class="text-muted d-block">Email Address</small>
                                <strong><?php echo htmlspecialchars($guardianRecord['email'] ?: 'Not provided'); ?></strong>
                            </div>
                        </div>
                        <div class="guardian-info-row">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <small class="text-muted d-block">Address</small>
                                <strong><?php echo htmlspecialchars($guardianRecord['address'] ?: 'Not provided'); ?></strong>
                            </div>
                        </div>
                    </div>

                    <div class="guardian-part-footer">
                        <span class="guardian-pill guardian-pill-primary">
                            <i class="fas fa-bolt"></i> 1st Priority for Emergency
                        </span>
                        <button type="button" class="btn-part-edit btn-part-edit-primary" style="width:auto;padding:0.35rem 0.75rem;" onclick="openEditModal(<?php echo (int)$guardianRecord['id']; ?>)">
                            <i class="fas fa-pen"></i> Edit
                        </button>
                    </div>
                </div>
                <?php else: ?>
                <div class="guardian-part-empty">
                    <div class="guardian-empty-icon primary"><i class="fas fa-shield-alt"></i></div>
                    <h4>No Guardian Assigned</h4>
                    <p>This contact is designated as the <strong>primary priority</strong> during emergencies.</p>
                    <button type="button" class="mpl-btn mpl-btn-sm mpl-btn-primary" onclick="openAddModalFor('Guardian')">
                        <i class="fas fa-plus"></i> Add Guardian (Primary)
                    </button>
                </div>
                <?php endif; ?>
            </article>

        </div>
    </section>

    <!-- ADDITIONAL CONTACTS (IF ANY) -->
    <?php if (!empty($otherRecords)): ?>
    <section class="mpl-panel" style="margin-bottom: 1.25rem;">
        <div class="mpl-panel-head">
            <div>
                <h2>Additional Emergency Contacts</h2>
                <p><?php echo count($otherRecords); ?> secondary relative or family contact(s) on file.</p>
            </div>
            <div>
                <button type="button" class="mpl-btn mpl-btn-sm mpl-btn-ghost" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> Add Another Contact
                </button>
            </div>
        </div>
        <div class="mpl-table-wrap">
            <table class="mpl-table">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Relationship</th>
                        <th>Contact Number</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($otherRecords as $o): ?>
                    <tr>
                        <td><strong class="text-white"><?php echo htmlspecialchars($o['full_name']); ?></strong></td>
                        <td><span class="guardian-role"><?php echo htmlspecialchars($o['relationship'] ?? 'Relative'); ?></span></td>
                        <td><?php echo htmlspecialchars($o['contact'] ?? '—'); ?></td>
                        <td><?php echo htmlspecialchars($o['email'] ?? '—'); ?></td>
                        <td><?php echo htmlspecialchars($o['address'] ?? '—'); ?></td>
                        <td>
                            <?php if ((int)($o['is_emergency'] ?? 0) === 1): ?>
                                <span class="guardian-status emergency">Emergency</span>
                            <?php else: ?>
                                <span class="guardian-status neutral">Secondary</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="mpl-actions">
                                <a href="javascript:void(0)" onclick="openEditModal(<?php echo (int)$o['id']; ?>)" title="Edit" aria-label="Edit"><i class="fas fa-pen"></i></a>
                                <a class="danger" href="javascript:void(0)" onclick="deleteGuardian(<?php echo (int)$o['id']; ?>)" title="Delete" aria-label="Delete"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php endif; ?>

<?php endif; ?>

</div>
</div>

<?php if ($student): ?>
<!-- ================= MODAL: UPDATE 3 PARTS TOGETHER ================= -->
<div class="modal fade" id="threePartsModal" tabindex="-1" aria-labelledby="threePartsModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="reg-modal-header">
                <div>
                    <h5 class="modal-title" id="threePartsModalTitle"><i class="fas fa-tasks text-primary me-2"></i> Update 3-Part Emergency Contacts</h5>
                    <small class="text-muted"><?php echo htmlspecialchars($student['student_number'] . ' - ' . $student['first_name'] . ' ' . $student['last_name']); ?></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="threePartsForm" onsubmit="return handleThreePartsForm(event)">
                <div class="modal-body">
                    <p class="text-muted mb-3" style="font-size:0.86rem;">
                        Update the 3 standard emergency contacts for this student. Leave any part blank if not applicable.
                    </p>

                    <!-- Part 1: Father -->
                    <div class="modal-part-section">
                        <div class="modal-part-header">
                            <h6><span class="part-pill me-2">Part 1</span> <i class="fas fa-user-tie text-info me-1"></i> Father's Information</h6>
                            <span class="guardian-pill neutral">Parent</span>
                        </div>
                        <input type="hidden" name="father_id" value="<?php echo (int)($fatherRecord['id'] ?? 0); ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:0.84rem;font-weight:600;">Full Name</label>
                                <input type="text" name="father_name" class="form-control" placeholder="e.g., Juan Santos Sr." value="<?php echo htmlspecialchars($fatherRecord['full_name'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:0.84rem;font-weight:600;">Contact Number</label>
                                <input type="tel" name="father_contact" class="form-control" placeholder="09XX XXX XXXX" value="<?php echo htmlspecialchars($fatherRecord['contact'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:0.84rem;font-weight:600;">Email Address</label>
                                <input type="email" name="father_email" class="form-control" placeholder="father@example.com" value="<?php echo htmlspecialchars($fatherRecord['email'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:0.84rem;font-weight:600;">Address</label>
                                <input type="text" name="father_address" class="form-control" placeholder="Street, City, Province" value="<?php echo htmlspecialchars($fatherRecord['address'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Part 2: Mother -->
                    <div class="modal-part-section">
                        <div class="modal-part-header">
                            <h6><span class="part-pill me-2">Part 2</span> <i class="fas fa-female text-danger me-1"></i> Mother's Information</h6>
                            <span class="guardian-pill neutral">Parent</span>
                        </div>
                        <input type="hidden" name="mother_id" value="<?php echo (int)($motherRecord['id'] ?? 0); ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:0.84rem;font-weight:600;">Full Name</label>
                                <input type="text" name="mother_name" class="form-control" placeholder="e.g., Maria Santos" value="<?php echo htmlspecialchars($motherRecord['full_name'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:0.84rem;font-weight:600;">Contact Number</label>
                                <input type="tel" name="mother_contact" class="form-control" placeholder="09XX XXX XXXX" value="<?php echo htmlspecialchars($motherRecord['contact'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:0.84rem;font-weight:600;">Email Address</label>
                                <input type="email" name="mother_email" class="form-control" placeholder="mother@example.com" value="<?php echo htmlspecialchars($motherRecord['email'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:0.84rem;font-weight:600;">Address</label>
                                <input type="text" name="mother_address" class="form-control" placeholder="Street, City, Province" value="<?php echo htmlspecialchars($motherRecord['address'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Part 3: Guardian / Primary Emergency Contact -->
                    <div class="modal-part-section is-primary">
                        <div class="modal-part-header">
                            <h6>
                                <span class="part-pill part-pill-primary me-2">Part 3</span> 
                                <i class="fas fa-shield-alt text-warning me-1"></i> Guardian / Emergency Contact
                            </h6>
                            <span class="guardian-pill guardian-pill-primary"><i class="fas fa-star"></i> Primary Emergency Contact</span>
                        </div>
                        <input type="hidden" name="guardian_id" value="<?php echo (int)($guardianRecord['id'] ?? 0); ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:0.84rem;font-weight:600;">Full Name *</label>
                                <input type="text" name="guardian_name" class="form-control" placeholder="e.g., Teresa Santos" value="<?php echo htmlspecialchars($guardianRecord['full_name'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:0.84rem;font-weight:600;">Relationship</label>
                                <select name="guardian_relationship" class="form-select">
                                    <option value="Guardian" <?php echo (($guardianRecord['relationship'] ?? '') === 'Guardian') ? 'selected' : ''; ?>>Guardian</option>
                                    <option value="Grandmother" <?php echo (($guardianRecord['relationship'] ?? '') === 'Grandmother') ? 'selected' : ''; ?>>Grandmother</option>
                                    <option value="Grandfather" <?php echo (($guardianRecord['relationship'] ?? '') === 'Grandfather') ? 'selected' : ''; ?>>Grandfather</option>
                                    <option value="Aunt" <?php echo (($guardianRecord['relationship'] ?? '') === 'Aunt') ? 'selected' : ''; ?>>Aunt</option>
                                    <option value="Uncle" <?php echo (($guardianRecord['relationship'] ?? '') === 'Uncle') ? 'selected' : ''; ?>>Uncle</option>
                                    <option value="Sibling" <?php echo (($guardianRecord['relationship'] ?? '') === 'Sibling') ? 'selected' : ''; ?>>Sibling</option>
                                    <option value="Other" <?php echo (!empty($guardianRecord['relationship']) && !in_array($guardianRecord['relationship'], ['Guardian','Grandmother','Grandfather','Aunt','Uncle','Sibling','Father','Mother'])) ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:0.84rem;font-weight:600;">Contact Number *</label>
                                <input type="tel" name="guardian_contact" class="form-control" placeholder="09XX XXX XXXX" value="<?php echo htmlspecialchars($guardianRecord['contact'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:0.84rem;font-weight:600;">Email Address</label>
                                <input type="email" name="guardian_email" class="form-control" placeholder="guardian@example.com" value="<?php echo htmlspecialchars($guardianRecord['email'] ?? ''); ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label" style="font-size:0.84rem;font-weight:600;">Address</label>
                                <input type="text" name="guardian_address" class="form-control" placeholder="Street, City, Province" value="<?php echo htmlspecialchars($guardianRecord['address'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="mpl-btn mpl-btn-primary" id="btnSaveThreeParts">
                        <i class="fas fa-save me-1"></i> Save All 3 Contacts
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL: SINGLE GUARDIAN / EDIT ================= -->
<div class="modal fade" id="guardianModal" tabindex="-1" aria-labelledby="guardianModalTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="reg-modal-header">
                <h5 class="modal-title" id="guardianModalTitle">Add Guardian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="guardianForm" onsubmit="return handleGuardianForm(event)">
                <div class="modal-body">
                    <input type="hidden" name="id" id="guardianId" value="">
                    <div class="reg-form-section">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="reg-form-group mb-3">
                                    <label class="form-label" style="font-size:0.84rem;font-weight:600;">Full Name *</label>
                                    <input type="text" name="full_name" id="guardianFullName" class="form-control" required placeholder="e.g., Maria Dela Cruz">
                                </div>
                            </div>
                        </div>

                        <div class="reg-form-group mb-3">
                            <label class="form-label" style="font-size:0.84rem;font-weight:600;">Relationship *</label>
                            <select name="relationship" id="guardianRelationship" class="form-select" required>
                                <option value="">Select Relationship</option>
                                <option value="Father">👨 Father</option>
                                <option value="Mother">👩 Mother</option>
                                <option value="Guardian">👥 Guardian</option>
                                <option value="Grandmother">👵 Grandmother</option>
                                <option value="Grandfather">👴 Grandfather</option>
                                <option value="Aunt">👩 Aunt</option>
                                <option value="Uncle">👨 Uncle</option>
                                <option value="Sibling">👫 Sibling</option>
                                <option value="Relative">👪 Relative</option>
                                <option value="Other">📋 Other</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="reg-form-group mb-3">
                                    <label class="form-label" style="font-size:0.84rem;font-weight:600;">Contact Number *</label>
                                    <input type="tel" name="contact" id="guardianContact" class="form-control" required placeholder="09XX XXX XXXX">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="reg-form-group mb-3">
                                    <label class="form-label" style="font-size:0.84rem;font-weight:600;">Email</label>
                                    <input type="email" name="email" id="guardianEmail" class="form-control" placeholder="guardian@example.com">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="reg-form-group mb-3">
                                    <label class="form-label" style="font-size:0.84rem;font-weight:600;">Primary Contact</label>
                                    <select name="is_primary" id="guardianIsPrimary" class="form-select">
                                        <option value="0">No</option>
                                        <option value="1">Yes (Primary Emergency Contact)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="reg-form-group mb-3">
                                    <label class="form-label" style="font-size:0.84rem;font-weight:600;">Emergency Contact</label>
                                    <select name="is_emergency" id="guardianIsEmergency" class="form-select">
                                        <option value="1" selected>Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="reg-form-group">
                            <label class="form-label" style="font-size:0.84rem;font-weight:600;">Address</label>
                            <textarea name="address" id="guardianAddress" class="form-control" rows="2" placeholder="Street, City, Province"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="mpl-btn mpl-btn-primary">Save Contact</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ================= MODAL: SELECT STUDENT (DASHBOARD) ================= -->
<?php if (!$student): ?>
<div class="modal fade" id="selectStudentModal" tabindex="-1" aria-labelledby="selectStudentModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="reg-modal-header">
                <div>
                    <h5 class="modal-title" id="selectStudentModalTitle"><i class="fas fa-users text-primary me-2"></i> Select Student to Manage Emergency Contacts</h5>
                    <small class="text-muted">Choose any student to configure their 3-part parent and guardian records</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3 border-bottom" style="background: rgba(255,255,255,0.02);">
                    <input type="search" id="studentPickerSearch" class="form-control" placeholder="Search by student number, name, or program..." autocomplete="off">
                </div>
                <div class="student-picker-list" style="max-height: 420px; overflow-y: auto;">
                    <?php if (empty($allStudentsList)): ?>
                    <div class="p-4 text-center text-muted">No students available.</div>
                    <?php else: ?>
                    <?php foreach ($allStudentsList as $s): ?>
                    <div class="student-picker-item d-flex align-items-center justify-content-between p-3 border-bottom" 
                         style="border-color: var(--sms-border, rgba(255,255,255,0.06)) !important;"
                         data-search="<?php echo htmlspecialchars(strtolower($s['student_number'] . ' ' . $s['last_name'] . ' ' . $s['first_name'] . ' ' . ($s['program_course'] ?? ''))); ?>">
                        <div class="d-flex align-items-center gap-3">
                            <span class="mpl-avatar"><?php echo htmlspecialchars(substr($s['first_name'], 0, 1) . substr($s['last_name'], 0, 1)); ?></span>
                            <div>
                                <strong class="d-block" style="color:var(--sms-heading);font-size:0.95rem;"><?php echo htmlspecialchars($s['last_name'] . ', ' . $s['first_name']); ?></strong>
                                <small class="text-muted"><?php echo htmlspecialchars($s['student_number']); ?> &bull; <?php echo htmlspecialchars($s['program_course'] ?? '—'); ?></small>
                            </div>
                        </div>
                        <a href="guardian-emergency-contact.php?student_id=<?php echo (int)$s['id']; ?>" class="mpl-btn mpl-btn-sm mpl-btn-primary">
                            Manage Contacts <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script src="<?php echo BASE_URL; ?>/modules/registrar/assets/js/registrar.js"></script>
<script>
const API_BASE = '<?php echo BASE_URL; ?>/modules/registrar/api';
const CSRF = '<?= e(csrfToken()) ?>';

window.setTimeout(function () {
    document.querySelectorAll('.mpl-alert-auto').forEach(function (alert) {
        alert.classList.add('mpl-alert-hide');
        window.setTimeout(function () { alert.remove(); }, 350);
    });
}, 2000);

function postJson(action, payload) {
    return fetch(API_BASE + '/guardians.php?action=' + action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': CSRF
        },
        body: JSON.stringify(payload)
    }).then(function (r) { return r.json(); });
}

<?php if (!$student): ?>
/* ============ DASHBOARD VIEW LOGIC ============ */
const dashboardFilter = document.getElementById('dashboardFilter');
let activeRoleFilter = 'all';

function filterDashboard() {
    const q = dashboardFilter ? dashboardFilter.value.trim().toLowerCase() : '';
    document.querySelectorAll('#dashboardTable tbody tr.dashboard-row').forEach(function (row) {
        const textMatch = !q || (row.dataset.search || row.textContent || '').toLowerCase().includes(q);
        const rel = (row.dataset.relationship || '').toLowerCase();
        let roleMatch = true;
        if (activeRoleFilter === 'father') {
            roleMatch = rel === 'father';
        } else if (activeRoleFilter === 'mother') {
            roleMatch = rel === 'mother';
        } else if (activeRoleFilter === 'guardian') {
            roleMatch = rel === 'guardian';
        } else if (activeRoleFilter === 'other') {
            roleMatch = !['father', 'mother', 'guardian'].includes(rel);
        }
        row.style.display = (textMatch && roleMatch) ? '' : 'none';
    });
}

if (dashboardFilter) {
    dashboardFilter.addEventListener('input', debounce(filterDashboard, 150));
}

function filterDashboardRole(role, btn) {
    activeRoleFilter = role.toLowerCase();
    document.querySelectorAll('.guardian-filter-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
    filterDashboard();
}

function openSelectStudentModal() {
    showRegModal('selectStudentModal');
}

const studentPickerSearch = document.getElementById('studentPickerSearch');
if (studentPickerSearch) {
    studentPickerSearch.addEventListener('input', debounce(function () {
        const q = studentPickerSearch.value.trim().toLowerCase();
        document.querySelectorAll('.student-picker-item').forEach(function (item) {
            const match = (item.dataset.search || item.textContent || '').toLowerCase();
            item.style.display = match.includes(q) ? '' : 'none';
        });
    }, 150));
}

<?php else: ?>
/* ============ SINGLE-STUDENT 3-PART VIEW LOGIC ============ */
const studentId = <?php echo (int)$studentId; ?>;
const guardianRecords = <?php echo json_encode($guardians, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

function openThreePartsModal() {
    showRegModal('threePartsModal');
}

async function handleThreePartsForm(e) {
    e.preventDefault();
    const form = document.getElementById('threePartsForm');
    const submitBtn = document.getElementById('btnSaveThreeParts');
    const originalText = submitBtn.innerHTML;

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';

    const payload = {
        student_id: studentId,
        father: {
            id: form.father_id.value ? parseInt(form.father_id.value, 10) : 0,
            full_name: form.father_name.value.trim(),
            contact: form.father_contact.value.trim(),
            email: form.father_email.value.trim(),
            address: form.father_address.value.trim()
        },
        mother: {
            id: form.mother_id.value ? parseInt(form.mother_id.value, 10) : 0,
            full_name: form.mother_name.value.trim(),
            contact: form.mother_contact.value.trim(),
            email: form.mother_email.value.trim(),
            address: form.mother_address.value.trim()
        },
        guardian: {
            id: form.guardian_id.value ? parseInt(form.guardian_id.value, 10) : 0,
            full_name: form.guardian_name.value.trim(),
            relationship: form.guardian_relationship.value.trim() || 'Guardian',
            contact: form.guardian_contact.value.trim(),
            email: form.guardian_email.value.trim(),
            address: form.guardian_address.value.trim()
        }
    };

    try {
        const result = await postJson('save_three_parts', payload);
        if (result.success) {
            showRegSuccess(result.message || '3-Part emergency contacts updated successfully');
            hideRegModal('threePartsModal');
            setTimeout(() => location.reload(), 600);
        } else {
            showRegError(result.error || 'Failed to save contacts');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    } catch (err) {
        console.error(err);
        showRegError('Error: ' + err.message);
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }

    return false;
}

function resetGuardianForm() {
    document.getElementById('guardianForm').reset();
    document.getElementById('guardianId').value = '';
    document.getElementById('guardianIsPrimary').value = '0';
    document.getElementById('guardianIsEmergency').value = '1';
}

function openAddModal() {
    resetGuardianForm();
    document.getElementById('guardianModalTitle').textContent = 'Add Emergency Contact';
    showRegModal('guardianModal');
}

function openAddModalFor(role) {
    resetGuardianForm();
    document.getElementById('guardianRelationship').value = role;
    if (role === 'Guardian') {
        document.getElementById('guardianModalTitle').textContent = 'Add Guardian (Primary Contact)';
        document.getElementById('guardianIsPrimary').value = '1';
        document.getElementById('guardianIsEmergency').value = '1';
    } else {
        document.getElementById('guardianModalTitle').textContent = 'Add ' + role + '\'s Information';
        document.getElementById('guardianIsPrimary').value = '0';
        document.getElementById('guardianIsEmergency').value = '1';
    }
    showRegModal('guardianModal');
}

function openEditModal(guardianId) {
    const record = guardianRecords.find(g => parseInt(g.id, 10) === guardianId);
    if (!record) {
        showRegError('Guardian record not found');
        return;
    }

    document.getElementById('guardianModalTitle').textContent = 'Edit ' + (record.relationship || 'Guardian') + ' Information';
    document.getElementById('guardianId').value = record.id;
    document.getElementById('guardianFullName').value = record.full_name || '';
    document.getElementById('guardianRelationship').value = record.relationship || '';
    document.getElementById('guardianContact').value = record.contact || '';
    document.getElementById('guardianEmail').value = record.email || '';
    document.getElementById('guardianIsPrimary').value = String(parseInt(record.is_primary, 10) || 0);
    document.getElementById('guardianIsEmergency').value = String(parseInt(record.is_emergency, 10) || 1);
    document.getElementById('guardianAddress').value = record.address || '';

    showRegModal('guardianModal');
}

async function handleGuardianForm(e) {
    e.preventDefault();

    const form = document.getElementById('guardianForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    data.student_id = studentId;

    if (!data.id) {
        delete data.id;
    }

    try {
        const result = await postJson('save', data);
        if (result.success) {
            showRegSuccess(result.message || 'Contact saved successfully');
            hideRegModal('guardianModal');
            setTimeout(() => location.reload(), 600);
        } else {
            showRegError(result.error || 'Save failed');
        }
    } catch (error) {
        console.error(error);
        showRegError('Error: ' + error.message);
    }

    return false;
}

async function deleteGuardian(guardianId) {
    if (!confirm('Are you sure you want to delete this contact record?')) return;

    try {
        const result = await postJson('delete', { id: guardianId });
        if (result.success) {
            showRegSuccess('Contact record deleted');
            setTimeout(() => location.reload(), 600);
        } else {
            showRegError(result.error || 'Delete failed');
        }
    } catch (error) {
        console.error(error);
        showRegError('Error: ' + error.message);
    }
}
<?php endif; ?>
</script>

<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>