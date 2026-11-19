<?php
/**
 * SMS 2 - Registrar: Validate Credentials Console
 * Enterprise admission-to-enrollment credential verification queue,
 * awaiting-upload tracker, and activation audit log.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/registrar-service.php';
require_once __DIR__ . '/../includes/storage-service.php';

regRequireAction('registrar.view');

$pageTitle    = 'Validate Credentials';
$activeModule = 'registrar';
$activePage   = 'validate-credentials';
$breadcrumbs  = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Validate Credentials', 'url' => null],
];

$pdo = db();

// Standard 5 institutional admission requirements
$requiredDocs = [
    'form_138'           => ['short' => 'F138', 'label' => 'Form 138 (Report Card)',    'icon' => 'fa-file-invoice'],
    'form_137'           => ['short' => 'F137', 'label' => 'Form 137 / SF10',           'icon' => 'fa-file-alt'],
    'good_moral'         => ['short' => 'GM',   'label' => 'Good Moral Certificate',    'icon' => 'fa-award'],
    'psa_birth_cert'     => ['short' => 'PSA',  'label' => 'PSA Birth Certificate',     'icon' => 'fa-id-card'],
    'barangay_clearance' => ['short' => 'BC',   'label' => 'Barangay Clearance',        'icon' => 'fa-shield-alt'],
];

/**
 * Map a reg_files category/original_name onto one of the 5 standard requirement keys.
 */
function vcMatchStandardDoc(string $category, string $originalName = ''): ?string
{
    $hay = strtolower(trim($category . ' ' . $originalName));
    if ($hay === '') {
        return null;
    }
    if (str_contains($hay, 'form_138') || str_contains($hay, 'form 138') || str_contains($hay, 'f138') || str_contains($hay, 'report card') || str_contains($hay, 'sf9')) {
        return 'form_138';
    }
    if (str_contains($hay, 'form_137') || str_contains($hay, 'form 137') || str_contains($hay, 'f137') || str_contains($hay, 'sf10') || str_contains($hay, 'permanent record')) {
        return 'form_137';
    }
    if (str_contains($hay, 'good_moral') || str_contains($hay, 'good moral') || str_contains($hay, 'gmc')) {
        return 'good_moral';
    }
    if (str_contains($hay, 'psa') || str_contains($hay, 'birth') || str_contains($hay, 'nso')) {
        return 'psa_birth_cert';
    }
    if (str_contains($hay, 'barangay') || str_contains($hay, 'brgy') || str_contains($hay, 'clearance')) {
        return 'barangay_clearance';
    }
    return null;
}

function vcInitials(string $first, string $last): string
{
    $f = strtoupper(substr(trim($first), 0, 1));
    $l = strtoupper(substr(trim($last), 0, 1));
    return ($f . $l) !== '' ? ($f . $l) : 'ST';
}

function vcRelativeTime(?string $datetime): array
{
    if (!$datetime) {
        return ['label' => '—', 'days' => 0];
    }
    $ts = strtotime($datetime);
    if (!$ts) {
        return ['label' => '—', 'days' => 0];
    }
    $diff = max(0, time() - $ts);
    $days = (int) floor($diff / 86400);
    if ($diff < 3600) {
        $mins = max(1, (int) floor($diff / 60));
        return ['label' => $mins . 'm ago', 'days' => 0];
    }
    if ($diff < 86400) {
        $hrs = (int) floor($diff / 3600);
        return ['label' => $hrs . 'h ago', 'days' => 0];
    }
    return ['label' => $days . 'd waiting', 'days' => $days];
}

// 1. Fetch all pre-enrolled candidates (Verified & Pending)
$candidates = [];
try {
    $stmt = $pdo->query("
        SELECT s.id, s.student_number, s.first_name, s.middle_name, s.last_name,
               s.email_address, s.contact_number, s.program_course, s.year_section,
               s.status, s.created_at
        FROM reg_students s
        WHERE s.status IN ('Verified', 'Pending')
        ORDER BY s.created_at DESC
    ");
    $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (Throwable $e) {
    error_log('Validate credentials query error: ' . $e->getMessage());
}

// 2. Fetch active files for those candidate students in one batched query
$filesByStudent = [];
if (!empty($candidates)) {
    $ids = array_map(static fn($r) => (int) $r['id'], $candidates);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    try {
        $fStmt = $pdo->prepare("
            SELECT id, student_id, category, original_name, mime, size, created_at
            FROM reg_files
            WHERE student_id IN ($placeholders)
              AND (status = 'Active' OR status IS NULL)
              AND (is_deleted = 0 OR is_deleted IS NULL)
            ORDER BY created_at DESC
        ");
        $fStmt->execute($ids);
        foreach ($fStmt->fetchAll(PDO::FETCH_ASSOC) as $f) {
            $sid = (int) $f['student_id'];
            $filesByStudent[$sid][] = $f;
        }
    } catch (Throwable $e) {
        // Fallback if is_deleted column does not exist
        $fStmt = $pdo->prepare("
            SELECT id, student_id, category, original_name, mime, size, created_at
            FROM reg_files
            WHERE student_id IN ($placeholders)
            ORDER BY created_at DESC
        ");
        $fStmt->execute($ids);
        foreach ($fStmt->fetchAll(PDO::FETCH_ASSOC) as $f) {
            $sid = (int) $f['student_id'];
            $filesByStudent[$sid][] = $f;
        }
    }
}

// 3. Partition into Validation Queue (Verified + has docs) vs Awaiting Documents
$pendingQueue   = [];
$awaitingDocs   = [];
$programsList   = [];
$readyComplete  = 0;
$overdueCount   = 0;

foreach ($candidates as $s) {
    $sid   = (int) $s['id'];
    $files = $filesByStudent[$sid] ?? [];
    $matched = [];
    $lastUploaded = null;

    foreach ($files as $f) {
        if ($lastUploaded === null && !empty($f['created_at'])) {
            $lastUploaded = (string) $f['created_at'];
        }
        $key = vcMatchStandardDoc((string) ($f['category'] ?? ''), (string) ($f['original_name'] ?? ''));
        if ($key !== null) {
            $matched[$key] = true;
        }
    }

    $s['files']          = $files;
    $s['doc_count']      = count($files);
    $s['matched_docs']   = $matched;
    $s['matched_count']  = count($matched);
    $s['last_uploaded']  = $lastUploaded;
    $s['is_complete']    = ($s['matched_count'] >= 5 || ($s['doc_count'] >= 5));

    $prog = trim((string) ($s['program_course'] ?? ''));
    if ($prog !== '' && !in_array($prog, $programsList, true)) {
        $programsList[] = $prog;
    }

    if ((string) $s['status'] === 'Verified' && $s['doc_count'] > 0) {
        $pendingQueue[] = $s;
        if ($s['is_complete']) {
            $readyComplete++;
        }
        $rel = vcRelativeTime($lastUploaded);
        if ($rel['days'] >= 3) {
            $overdueCount++;
        }
    } else {
        $awaitingDocs[] = $s;
    }
}

// Sort Validation Queue by most recently uploaded first
usort($pendingQueue, static function ($a, $b) {
    return strcmp((string) ($b['last_uploaded'] ?? ''), (string) ($a['last_uploaded'] ?? ''));
});
sort($programsList);

// 4. Fetch Recently Activated students (from reg_status_history)
$recentActivated = [];
$activatedToday  = 0;
try {
    $actStmt = $pdo->query("
        SELECT h.id AS history_id, h.student_id, h.changed_from, h.changed_to, h.reason, h.notes, h.changed_at,
               s.student_number, s.first_name, s.last_name, s.program_course, s.year_section, s.email_address, s.status,
               COALESCE(u.full_name, u.username, 'Registrar') AS officer_name
        FROM reg_status_history h
        INNER JOIN reg_students s ON s.id = h.student_id
        LEFT JOIN users u ON u.id = h.changed_by
        WHERE h.changed_to = 'Active'
        ORDER BY h.changed_at DESC
        LIMIT 30
    ");
    $recentActivated = $actStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    $todayStr = date('Y-m-d');
    foreach ($recentActivated as $ra) {
        if (str_starts_with((string) ($ra['changed_at'] ?? ''), $todayStr)) {
            $activatedToday++;
        }
    }
} catch (Throwable $e) {
    error_log('Recent activations query error: ' . $e->getMessage());
}

$avatarPalette = ['#1e3a8a', '#2563eb', '#0d9488', '#7c3aed', '#d97706', '#059669', '#dc2626', '#4f46e5'];

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<link href="<?php echo BASE_URL; ?>/assets/css/module-process-list.css?v=2" rel="stylesheet">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/modules/registrar/assets/css/registrar.css?v=<?php echo time(); ?>">

<style>
/* ============================================================================
   VALIDATE CREDENTIALS CONSOLE — LIGHT & DARK MODE DESIGN SYSTEM
   ============================================================================ */
.vc-hero {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1.25rem;
}
.vc-hero-title {
    display: flex;
    align-items: center;
    gap: 0.85rem;
}
.vc-hero-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.28);
    flex-shrink: 0;
}
.vc-hero h1 {
    font-size: 1.45rem;
    font-weight: 800;
    margin: 0;
    color: var(--sms-heading, #0f172a);
    letter-spacing: -0.02em;
}
.vc-hero p {
    margin: 0.15rem 0 0;
    font-size: 0.86rem;
    color: var(--sms-text-muted, #64748b);
}

/* Standalone Box Switcher Tabs (Consistent with Status Tracker) */
.vc-tab-nav {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.65rem;
    margin-bottom: 1.15rem;
}
.vc-tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    padding: 0.58rem 1.15rem;
    border-radius: 10px;
    font-size: 0.84rem;
    font-weight: 700;
    color: #475569;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    cursor: pointer;
    transition: all 0.16s ease;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
}
.vc-tab-btn:hover {
    color: #0f172a;
    background: #f8fafc;
    border-color: #94a3b8;
    transform: translateY(-1px);
}
.vc-tab-btn.active {
    background: #2563eb;
    color: #ffffff;
    border-color: #2563eb;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.28);
}
.vc-tab-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 22px;
    height: 20px;
    padding: 0 0.45rem;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 800;
    background: #eff6ff;
    color: #1d4ed8;
}
.vc-tab-btn.active .vc-tab-count {
    background: rgba(255, 255, 255, 0.24);
    color: #ffffff;
}
.vc-tab-count.amber { background: #fef3c7; color: #b45309; }
.vc-tab-count.emerald { background: #dcfce7; color: #15803d; }

/* Filter Bar */
.vc-filter-bar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.65rem;
    padding: 0.9rem 1.15rem;
    border-bottom: 1px solid var(--sms-border, #e2e8f0);
    background: var(--sms-surface-muted, #f8fafc);
}
.vc-search-wrap {
    position: relative;
    flex: 1 1 260px;
    max-width: 380px;
}
.vc-search-wrap i {
    position: absolute;
    left: 0.85rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 0.85rem;
    pointer-events: none;
}
.vc-search-input {
    width: 100%;
    padding: 0.48rem 0.85rem 0.48rem 2.3rem;
    border-radius: 9px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #0f172a;
    font-size: 0.84rem;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.vc-search-input:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14);
}
.vc-select {
    padding: 0.48rem 2rem 0.48rem 0.8rem;
    border-radius: 9px;
    border: 1px solid #cbd5e1;
    background-color: #ffffff;
    color: #0f172a;
    font-size: 0.83rem;
    font-weight: 600;
    cursor: pointer;
}

/* Student Cell */
.vc-student-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.vc-avatar {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    color: #ffffff;
    font-size: 0.8rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    letter-spacing: 0.03em;
}
.vc-student-name {
    font-weight: 700;
    color: var(--sms-heading, #0f172a);
    font-size: 0.88rem;
    line-height: 1.25;
}
.vc-student-meta {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    flex-wrap: wrap;
    margin-top: 2px;
    font-size: 0.74rem;
    color: var(--sms-text-muted, #64748b);
}
.vc-sno {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 0.73rem;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 5px;
    background: #eff6ff;
    color: #1e40af;
    border: 1px solid #bfdbfe;
}

/* 5-Doc Mini Checklist Pills */
.vc-checklist-wrap {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    min-width: 195px;
}
.vc-doc-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}
.vc-doc-chip {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    font-size: 0.67rem;
    font-weight: 800;
    padding: 2px 6px;
    border-radius: 5px;
    letter-spacing: 0.02em;
}
.vc-doc-chip.ok {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
}
.vc-doc-chip.missing {
    background: #f1f5f9;
    color: #94a3b8;
    border: 1px dashed #cbd5e1;
}
.vc-progress-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.72rem;
    color: var(--sms-text-muted, #64748b);
    font-weight: 600;
}
.vc-progress-bar-bg {
    flex: 1;
    height: 5px;
    border-radius: 999px;
    background: #e2e8f0;
    overflow: hidden;
    max-width: 90px;
}
.vc-progress-bar-fill {
    height: 100%;
    border-radius: 999px;
    background: linear-gradient(90deg, #3b82f6, #10b981);
}
.vc-progress-bar-fill.partial {
    background: linear-gradient(90deg, #f59e0b, #fbbf24);
}

/* Status & Age Badges */
.vc-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.26rem 0.7rem;
    border-radius: 999px;
    font-size: 0.74rem;
    font-weight: 700;
    white-space: nowrap;
}
.vc-status-badge.for-validation {
    background: #fef3c7;
    color: #b45309;
    border: 1px solid #fde68a;
}
.vc-status-badge.awaiting {
    background: #f3e8ff;
    color: #6b21a8;
    border: 1px solid #e9d5ff;
}
.vc-status-badge.activated {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
}
.vc-age-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 1px 7px;
    border-radius: 999px;
    background: #f1f5f9;
    color: #475569;
    margin-top: 3px;
}
.vc-age-chip.overdue {
    background: #fef2f2;
    color: #b91c1c;
    border: 1px solid #fecaca;
}

/* Empty State */
.vc-empty-state {
    text-align: center;
    padding: 3.5rem 1.5rem;
}
.vc-empty-icon {
    width: 64px;
    height: 64px;
    border-radius: 18px;
    background: #eff6ff;
    color: #2563eb;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.65rem;
    margin-bottom: 1rem;
}
.vc-empty-state h4 {
    font-size: 1.05rem;
    font-weight: 800;
    color: var(--sms-heading, #0f172a);
    margin-bottom: 0.35rem;
}
.vc-empty-state p {
    font-size: 0.85rem;
    color: var(--sms-text-muted, #64748b);
    max-width: 420px;
    margin: 0 auto;
}

/* Flash Banner */
.vc-banner {
    display: flex;
    align-items: flex-start;
    gap: 0.85rem;
    padding: 1rem 1.25rem;
    border-radius: 12px;
    margin-bottom: 1.25rem;
    border: 1px solid transparent;
    font-size: 0.88rem;
}
.vc-banner.success {
    background: #ecfdf5;
    border-color: #a7f3d0;
    color: #065f46;
}
.vc-banner.warning {
    background: #fffbeb;
    border-color: #fde68a;
    color: #92400e;
}
.vc-banner.info {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #1e40af;
}
.vc-banner.danger {
    background: #fef2f2;
    border-color: #fecaca;
    color: #991b1b;
}

/* ── Dark Mode Parity ─────────────────────────────────────────────────────── */
[data-theme="dark"] .vc-tab-btn {
    background: rgba(15, 23, 42, 0.72);
    border-color: rgba(148, 163, 184, 0.22);
    color: #cbd5e1;
}
[data-theme="dark"] .vc-tab-btn:hover {
    background: rgba(30, 41, 59, 0.9);
    border-color: rgba(148, 163, 184, 0.38);
    color: #f8fafc;
}
[data-theme="dark"] .vc-tab-btn.active {
    background: #2563eb;
    border-color: #3b82f6;
    color: #ffffff;
}
[data-theme="dark"] .vc-tab-count {
    background: rgba(59, 130, 246, 0.2);
    color: #93c5fd;
}
[data-theme="dark"] .vc-tab-count.amber {
    background: rgba(245, 158, 11, 0.22);
    color: #fcd34d;
}
[data-theme="dark"] .vc-tab-count.emerald {
    background: rgba(16, 185, 129, 0.22);
    color: #6ee7b7;
}
[data-theme="dark"] .vc-tab-btn.active .vc-tab-count {
    background: rgba(255, 255, 255, 0.22);
    color: #ffffff;
}
[data-theme="dark"] .vc-filter-bar {
    background: rgba(15, 23, 42, 0.65);
    border-bottom-color: rgba(148, 163, 184, 0.18);
}
[data-theme="dark"] .vc-search-input,
[data-theme="dark"] .vc-select {
    background-color: #0f172a;
    border-color: rgba(148, 163, 184, 0.26);
    color: #f1f5f9;
}
[data-theme="dark"] .vc-sno {
    background: rgba(59, 130, 246, 0.18);
    color: #93c5fd;
    border-color: rgba(59, 130, 246, 0.32);
}
[data-theme="dark"] .vc-doc-chip.ok {
    background: rgba(16, 185, 129, 0.2);
    color: #6ee7b7;
    border-color: rgba(16, 185, 129, 0.35);
}
[data-theme="dark"] .vc-doc-chip.missing {
    background: rgba(148, 163, 184, 0.1);
    color: #64748b;
    border-color: rgba(148, 163, 184, 0.24);
}
[data-theme="dark"] .vc-progress-bar-bg {
    background: rgba(148, 163, 184, 0.2);
}
[data-theme="dark"] .vc-status-badge.for-validation {
    background: rgba(245, 158, 11, 0.2);
    color: #fcd34d;
    border-color: rgba(245, 158, 11, 0.35);
}
[data-theme="dark"] .vc-status-badge.awaiting {
    background: rgba(139, 92, 246, 0.2);
    color: #d8b4fe;
    border-color: rgba(139, 92, 246, 0.35);
}
[data-theme="dark"] .vc-status-badge.activated {
    background: rgba(16, 185, 129, 0.2);
    color: #6ee7b7;
    border-color: rgba(16, 185, 129, 0.35);
}
[data-theme="dark"] .vc-age-chip {
    background: rgba(148, 163, 184, 0.14);
    color: #cbd5e1;
}
[data-theme="dark"] .vc-age-chip.overdue {
    background: rgba(239, 68, 68, 0.2);
    color: #fca5a5;
    border-color: rgba(239, 68, 68, 0.35);
}
[data-theme="dark"] .vc-empty-icon {
    background: rgba(59, 130, 246, 0.16);
    color: #60a5fa;
}
[data-theme="dark"] .vc-banner.success {
    background: rgba(16, 185, 129, 0.15);
    border-color: rgba(16, 185, 129, 0.35);
    color: #6ee7b7;
}
[data-theme="dark"] .vc-banner.warning {
    background: rgba(245, 158, 11, 0.15);
    border-color: rgba(245, 158, 11, 0.35);
    color: #fcd34d;
}
[data-theme="dark"] .vc-banner.info {
    background: rgba(59, 130, 246, 0.15);
    border-color: rgba(59, 130, 246, 0.35);
    color: #93c5fd;
}
[data-theme="dark"] .vc-banner.danger {
    background: rgba(239, 68, 68, 0.15);
    border-color: rgba(239, 68, 68, 0.35);
    color: #fca5a5;
}
</style>

<?php renderBreadcrumbs($breadcrumbs); ?>

<div class="container-fluid py-4">
<div class="mpl" data-mpl>

    <!-- Page Header -->
    <div class="vc-hero">
        <div class="vc-hero-title">
            <div class="vc-hero-icon">
                <i class="fas fa-file-signature"></i>
            </div>
            <div>
                <h1>Credential Validation Console</h1>
                <p>Verify submitted admission credentials, inspect uploaded files, and officially activate pre-enrolled students.</p>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?php echo BASE_URL; ?>/modules/registrar/pages/digital-file-storage.php" class="btn btn-sm btn-outline-secondary rounded-3 px-3 fw-semibold">
                <i class="fas fa-folder-open me-1"></i> Digital File Storage
            </a>
            <a href="<?php echo BASE_URL; ?>/modules/registrar/pages/student-information-system.php" class="btn btn-sm btn-primary rounded-3 px-3 fw-semibold">
                <i class="fas fa-users me-1"></i> Student Directory
            </a>
        </div>
    </div>

    <!-- Flash Status Banners -->
    <?php
    $msg = (string) ($_GET['msg'] ?? '');
    $flashStudent = trim((string) ($_GET['student'] ?? ''));
    $studentPrefix = $flashStudent !== '' ? '<strong>' . htmlspecialchars($flashStudent) . '</strong> has been ' : 'Student has been ';
    ?>
    <?php if ($msg === 'activated'): ?>
        <div class="vc-banner success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle fa-lg mt-1"></i>
            <div class="flex-grow-1">
                <strong>Official Enrollment &amp; Activation Complete!</strong><br>
                <?php echo $studentPrefix; ?>officially enrolled and activated. Records cascaded to Student Directory, Academic History, Digital File Storage, and Masterlist, and a welcome email was dispatched.
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($msg === 'activated_mail_failed'): ?>
        <div class="vc-banner warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle fa-lg mt-1"></i>
            <div class="flex-grow-1">
                <strong>Activated — Email Notification Pending</strong><br>
                <?php echo $studentPrefix; ?>officially enrolled and activated in the database, but the email notification could not be sent
                (<code><?php echo htmlspecialchars((string) ($_GET['error'] ?? 'SMTP error')); ?></code>).
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($msg === 'returned'): ?>
        <div class="vc-banner info alert-dismissible fade show" role="alert">
            <i class="fas fa-paper-plane fa-lg mt-1"></i>
            <div class="flex-grow-1">
                <strong>Deficiency Notice Logged &amp; Sent</strong><br>
                The credential deficiency remarks for <?php echo $flashStudent !== '' ? '<strong>' . htmlspecialchars($flashStudent) . '</strong>' : 'the student'; ?> have been recorded in the audit log and emailed to the student.
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($msg === 'returned_mail_failed'): ?>
        <div class="vc-banner warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle fa-lg mt-1"></i>
            <div class="flex-grow-1">
                <strong>Deficiency Notice Logged (Email Failed)</strong><br>
                The deficiency notice was saved to the student's audit history, but email delivery failed
                (<code><?php echo htmlspecialchars((string) ($_GET['error'] ?? 'SMTP error')); ?></code>).
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($msg === 'csrf_error'): ?>
        <div class="vc-banner danger alert-dismissible fade show" role="alert">
            <i class="fas fa-shield-alt fa-lg mt-1"></i>
            <div class="flex-grow-1">
                <strong>Security Token Expired.</strong> Please refresh the page and submit your action again.
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- 4 Executive KPI Cards -->
    <section class="mpl-stats" aria-label="Credential validation metrics">
        <article class="mpl-stat" onclick="vcSwitchTab('queue')" style="cursor:pointer;" title="View pending validation queue">
            <div class="mpl-stat-icon amber"><i class="fas fa-hourglass-half"></i></div>
            <div>
                <span>Pending Validation</span>
                <strong><?php echo count($pendingQueue); ?></strong>
            </div>
        </article>
        <article class="mpl-stat" onclick="vcSwitchTab('queue'); document.getElementById('vcCompletenessFilter').value='complete'; vcFilterTable();" style="cursor:pointer;" title="Filter complete 5/5 submissions">
            <div class="mpl-stat-icon green"><i class="fas fa-check-double"></i></div>
            <div>
                <span>Complete Submissions</span>
                <strong><?php echo $readyComplete; ?></strong>
            </div>
        </article>
        <article class="mpl-stat" onclick="vcSwitchTab('awaiting')" style="cursor:pointer;" title="View students awaiting document upload">
            <div class="mpl-stat-icon purple"><i class="fas fa-cloud-upload-alt"></i></div>
            <div>
                <span>Awaiting Upload</span>
                <strong><?php echo count($awaitingDocs); ?></strong>
            </div>
        </article>
        <article class="mpl-stat" onclick="vcSwitchTab('activated')" style="cursor:pointer;" title="View recently activated students">
            <div class="mpl-stat-icon blue"><i class="fas fa-user-check"></i></div>
            <div>
                <span>Activated (Today / Recent)</span>
                <strong><?php echo $activatedToday; ?> <span class="text-muted fw-semibold" style="font-size:0.85rem;">/ <?php echo count($recentActivated); ?></span></strong>
            </div>
        </article>
    </section>

    <!-- Standalone Box Queue Switcher -->
    <div class="vc-tab-nav" role="tablist" aria-label="Validation views">
        <button type="button" class="vc-tab-btn active" id="tabBtnQueue" onclick="vcSwitchTab('queue')">
            <i class="fas fa-clipboard-check"></i> Validation Queue
            <span class="vc-tab-count amber"><?php echo count($pendingQueue); ?></span>
        </button>
        <button type="button" class="vc-tab-btn" id="tabBtnAwaiting" onclick="vcSwitchTab('awaiting')">
            <i class="fas fa-clock"></i> Awaiting Upload
            <span class="vc-tab-count"><?php echo count($awaitingDocs); ?></span>
        </button>
        <button type="button" class="vc-tab-btn" id="tabBtnActivated" onclick="vcSwitchTab('activated')">
            <i class="fas fa-history"></i> Recently Activated
            <span class="vc-tab-count emerald"><?php echo count($recentActivated); ?></span>
        </button>
    </div>

    <!-- ================= TAB 1: VALIDATION QUEUE ================= -->
    <section class="mpl-panel" id="panelQueue">
        <div class="vc-filter-bar">
            <div class="vc-search-wrap">
                <i class="fas fa-search"></i>
                <input type="search" id="vcQueueSearch" class="vc-search-input" placeholder="Search by student no., name, email, or program..." oninput="vcFilterTable()">
            </div>
            <select id="vcProgramFilter" class="vc-select" onchange="vcFilterTable()">
                <option value="">All Programs</option>
                <?php foreach ($programsList as $prog): ?>
                    <option value="<?php echo htmlspecialchars(strtolower($prog)); ?>"><?php echo htmlspecialchars($prog); ?></option>
                <?php endforeach; ?>
            </select>
            <select id="vcCompletenessFilter" class="vc-select" onchange="vcFilterTable()">
                <option value="all">All Submissions</option>
                <option value="complete">Complete (5/5 Docs)</option>
                <option value="partial">Partial (1–4 Docs)</option>
            </select>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-3 px-3" onclick="vcResetFilters()">
                <i class="fas fa-undo-alt me-1"></i> Reset
            </button>
            <span class="ms-auto small text-muted fw-semibold" id="vcQueueCounter">
                Showing <?php echo count($pendingQueue); ?> of <?php echo count($pendingQueue); ?> candidate(s)
            </span>
        </div>

        <div class="mpl-table-wrap">
            <table class="mpl-table" id="vcQueueTable">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Program &amp; Section</th>
                        <th>Submitted Credentials</th>
                        <th>Submitted On</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pendingQueue)): ?>
                        <tr class="vc-empty-row">
                            <td colspan="6">
                                <div class="vc-empty-state">
                                    <div class="vc-empty-icon"><i class="fas fa-check-circle"></i></div>
                                    <h4>Validation Queue is All Caught Up</h4>
                                    <p>There are no pre-enrolled students with pending uploaded credentials right now. Check the <strong>Awaiting Upload</strong> or <strong>Recently Activated</strong> tabs for full visibility.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pendingQueue as $idx => $s):
                            $first = (string) ($s['first_name'] ?? '');
                            $last  = (string) ($s['last_name'] ?? '');
                            $sno   = (string) ($s['student_number'] ?? '');
                            $prog  = (string) ($s['program_course'] ?? '');
                            $sec   = (string) ($s['year_section'] ?? '');
                            $email = (string) ($s['email_address'] ?? '');
                            $avCol = $avatarPalette[$idx % count($avatarPalette)];
                            $rel   = vcRelativeTime($s['last_uploaded']);
                            $pct   = min(100, (int) round(($s['matched_count'] / 5) * 100));
                            $searchKey = strtolower(trim("$sno $last $first $email $prog $sec"));
                            $compKey   = $s['is_complete'] ? 'complete' : 'partial';
                        ?>
                        <tr class="vc-queue-row"
                            data-search="<?php echo htmlspecialchars($searchKey); ?>"
                            data-program="<?php echo htmlspecialchars(strtolower($prog)); ?>"
                            data-completeness="<?php echo $compKey; ?>">
                            <td>
                                <div class="vc-student-cell">
                                    <div class="vc-avatar" style="background: <?php echo $avCol; ?>;">
                                        <?php echo htmlspecialchars(vcInitials($first, $last)); ?>
                                    </div>
                                    <div>
                                        <div class="vc-student-name"><?php echo htmlspecialchars($last . ', ' . $first); ?></div>
                                        <div class="vc-student-meta">
                                            <span class="vc-sno"><?php echo htmlspecialchars($sno); ?></span>
                                            <?php if ($email !== ''): ?>
                                                <span><i class="far fa-envelope me-1"></i><?php echo htmlspecialchars($email); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold" style="font-size:0.84rem;"><?php echo htmlspecialchars($prog !== '' ? $prog : 'Not assigned'); ?></div>
                                <div class="small text-muted"><?php echo htmlspecialchars($sec !== '' ? $sec : 'Pre-Enrollment'); ?></div>
                            </td>
                            <td>
                                <div class="vc-checklist-wrap">
                                    <div class="vc-doc-pills">
                                        <?php foreach ($requiredDocs as $code => $meta):
                                            $has = !empty($s['matched_docs'][$code]);
                                        ?>
                                            <span class="vc-doc-chip <?php echo $has ? 'ok' : 'missing'; ?>" title="<?php echo htmlspecialchars($meta['label'] . ($has ? ' — Uploaded' : ' — Missing')); ?>">
                                                <i class="fas <?php echo $has ? 'fa-check' : 'fa-minus'; ?>" style="font-size:0.6rem;"></i>
                                                <?php echo $meta['short']; ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="vc-progress-row">
                                        <div class="vc-progress-bar-bg">
                                            <div class="vc-progress-bar-fill <?php echo $s['is_complete'] ? '' : 'partial'; ?>" style="width: <?php echo max(15, $pct); ?>%;"></div>
                                        </div>
                                        <span><?php echo (int) $s['doc_count']; ?> file<?php echo $s['doc_count'] === 1 ? '' : 's'; ?> (<?php echo (int) $s['matched_count']; ?>/5 req)</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold" style="font-size:0.82rem;">
                                    <?php echo $s['last_uploaded'] ? date('M d, Y', strtotime((string) $s['last_uploaded'])) : '—'; ?>
                                </div>
                                <div class="small text-muted">
                                    <?php echo $s['last_uploaded'] ? date('h:i A', strtotime((string) $s['last_uploaded'])) : ''; ?>
                                </div>
                                <span class="vc-age-chip <?php echo $rel['days'] >= 3 ? 'overdue' : ''; ?>">
                                    <i class="far fa-clock"></i> <?php echo htmlspecialchars($rel['label']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="vc-status-badge for-validation">
                                    <i class="fas fa-hourglass-half"></i> For Validation
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a href="view-student-credentials.php?id=<?php echo (int) $s['id']; ?>" class="btn btn-sm btn-primary rounded-3 px-3 fw-semibold shadow-sm">
                                        <i class="fas fa-clipboard-check me-1"></i> Review &amp; Act
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-3 px-2" onclick="viewStudent(<?php echo (int) $s['id']; ?>, 'docs')" title="Open 360° Student Dossier">
                                        <i class="fas fa-user-graduate"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- ================= TAB 2: AWAITING UPLOAD ================= -->
    <section class="mpl-panel" id="panelAwaiting" style="display:none;">
        <div class="vc-filter-bar">
            <div class="vc-search-wrap">
                <i class="fas fa-search"></i>
                <input type="search" id="vcAwaitingSearch" class="vc-search-input" placeholder="Search pre-enrolled students awaiting documents..." oninput="vcFilterAwaiting()">
            </div>
            <span class="ms-auto small text-muted fw-semibold" id="vcAwaitingCounter">
                Showing <?php echo count($awaitingDocs); ?> student(s) without uploaded credentials
            </span>
        </div>
        <div class="mpl-table-wrap">
            <table class="mpl-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Program &amp; Section</th>
                        <th>Registered Date</th>
                        <th>Current Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($awaitingDocs)): ?>
                        <tr>
                            <td colspan="5">
                                <div class="vc-empty-state">
                                    <div class="vc-empty-icon"><i class="fas fa-check-double"></i></div>
                                    <h4>No Students Awaiting Upload</h4>
                                    <p>All pre-enrolled students have submitted their required enrollment documents.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($awaitingDocs as $idx => $s):
                            $first = (string) ($s['first_name'] ?? '');
                            $last  = (string) ($s['last_name'] ?? '');
                            $sno   = (string) ($s['student_number'] ?? '');
                            $prog  = (string) ($s['program_course'] ?? '');
                            $sec   = (string) ($s['year_section'] ?? '');
                            $email = (string) ($s['email_address'] ?? '');
                            $avCol = $avatarPalette[($idx + 3) % count($avatarPalette)];
                            $searchKey = strtolower(trim("$sno $last $first $email $prog $sec"));
                        ?>
                        <tr class="vc-awaiting-row" data-search="<?php echo htmlspecialchars($searchKey); ?>">
                            <td>
                                <div class="vc-student-cell">
                                    <div class="vc-avatar" style="background: <?php echo $avCol; ?>;">
                                        <?php echo htmlspecialchars(vcInitials($first, $last)); ?>
                                    </div>
                                    <div>
                                        <div class="vc-student-name"><?php echo htmlspecialchars($last . ', ' . $first); ?></div>
                                        <div class="vc-student-meta">
                                            <span class="vc-sno"><?php echo htmlspecialchars($sno); ?></span>
                                            <?php if ($email !== ''): ?>
                                                <span><i class="far fa-envelope me-1"></i><?php echo htmlspecialchars($email); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold" style="font-size:0.84rem;"><?php echo htmlspecialchars($prog !== '' ? $prog : 'Not assigned'); ?></div>
                                <div class="small text-muted"><?php echo htmlspecialchars($sec !== '' ? $sec : 'Pre-Enrollment'); ?></div>
                            </td>
                            <td class="small">
                                <?php echo !empty($s['created_at']) ? date('M d, Y h:i A', strtotime((string) $s['created_at'])) : '—'; ?>
                            </td>
                            <td>
                                <span class="vc-status-badge awaiting">
                                    <i class="fas fa-cloud-upload-alt"></i> <?php echo htmlspecialchars((string) ($s['status'] ?? 'Pending')); ?> (0 files)
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a href="view-student-credentials.php?id=<?php echo (int) $s['id']; ?>" class="btn btn-sm btn-outline-primary rounded-3 px-3 fw-semibold">
                                        <i class="fas fa-eye me-1"></i> Inspect
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-3 px-2" onclick="viewStudent(<?php echo (int) $s['id']; ?>, 'docs')" title="Open 360° Student Dossier">
                                        <i class="fas fa-user-graduate"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- ================= TAB 3: RECENTLY ACTIVATED ================= -->
    <section class="mpl-panel" id="panelActivated" style="display:none;">
        <div class="vc-filter-bar">
            <div class="vc-search-wrap">
                <i class="fas fa-search"></i>
                <input type="search" id="vcActivatedSearch" class="vc-search-input" placeholder="Search activated students or validating officer..." oninput="vcFilterActivated()">
            </div>
            <span class="ms-auto small text-muted fw-semibold" id="vcActivatedCounter">
                Showing last <?php echo count($recentActivated); ?> official activation(s)
            </span>
        </div>
        <div class="mpl-table-wrap">
            <table class="mpl-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Program</th>
                        <th>Activated On</th>
                        <th>Validated By</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentActivated)): ?>
                        <tr>
                            <td colspan="6">
                                <div class="vc-empty-state">
                                    <div class="vc-empty-icon"><i class="fas fa-history"></i></div>
                                    <h4>No Recent Activations Yet</h4>
                                    <p>Once pre-enrolled students are validated and activated, their official activation audit records will appear here.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentActivated as $idx => $ra):
                            $first   = (string) ($ra['first_name'] ?? '');
                            $last    = (string) ($ra['last_name'] ?? '');
                            $sno     = (string) ($ra['student_number'] ?? '');
                            $prog    = (string) ($ra['program_course'] ?? '');
                            $officer = (string) ($ra['officer_name'] ?? 'Registrar');
                            $avCol   = $avatarPalette[($idx + 5) % count($avatarPalette)];
                            $searchKey = strtolower(trim("$sno $last $first $prog $officer"));
                        ?>
                        <tr class="vc-activated-row" data-search="<?php echo htmlspecialchars($searchKey); ?>">
                            <td>
                                <div class="vc-student-cell">
                                    <div class="vc-avatar" style="background: <?php echo $avCol; ?>;">
                                        <?php echo htmlspecialchars(vcInitials($first, $last)); ?>
                                    </div>
                                    <div>
                                        <div class="vc-student-name"><?php echo htmlspecialchars($last . ', ' . $first); ?></div>
                                        <div class="vc-student-meta">
                                            <span class="vc-sno"><?php echo htmlspecialchars($sno); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-size:0.84rem;"><?php echo htmlspecialchars($prog !== '' ? $prog : '—'); ?></td>
                            <td class="small">
                                <div class="fw-semibold"><?php echo !empty($ra['changed_at']) ? date('M d, Y', strtotime((string) $ra['changed_at'])) : '—'; ?></div>
                                <div class="text-muted"><?php echo !empty($ra['changed_at']) ? date('h:i A', strtotime((string) $ra['changed_at'])) : ''; ?></div>
                            </td>
                            <td class="small">
                                <div class="fw-semibold"><?php echo htmlspecialchars($officer); ?></div>
                                <?php if (!empty($ra['reason'])): ?>
                                    <div class="text-muted" style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo htmlspecialchars((string) $ra['reason']); ?>">
                                        <?php echo htmlspecialchars((string) $ra['reason']); ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="vc-status-badge activated">
                                    <i class="fas fa-check-circle"></i> Officially Enrolled
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-3 px-3 fw-semibold" onclick="viewStudent(<?php echo (int) $ra['student_id']; ?>, 'docs')">
                                        <i class="fas fa-user-graduate me-1"></i> View Dossier
                                    </button>
                                    <a href="digital-file-storage.php?student_id=<?php echo (int) $ra['student_id']; ?>" class="btn btn-sm btn-outline-secondary rounded-3 px-2" title="Open Student Vault (Digital File Storage)">
                                        <i class="fas fa-folder-open"></i>
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

</div>
</div>

<script>
function vcSwitchTab(tab) {
    const panels = {
        queue:     { panel: 'panelQueue',     btn: 'tabBtnQueue' },
        awaiting:  { panel: 'panelAwaiting',  btn: 'tabBtnAwaiting' },
        activated: { panel: 'panelActivated', btn: 'tabBtnActivated' },
    };
    Object.keys(panels).forEach(k => {
        const p = document.getElementById(panels[k].panel);
        const b = document.getElementById(panels[k].btn);
        if (p) p.style.display = (k === tab) ? '' : 'none';
        if (b) b.classList.toggle('active', k === tab);
    });
}

function vcFilterTable() {
    const q    = (document.getElementById('vcQueueSearch')?.value || '').trim().toLowerCase();
    const prog = (document.getElementById('vcProgramFilter')?.value || '').trim().toLowerCase();
    const comp = (document.getElementById('vcCompletenessFilter')?.value || 'all');
    const rows = document.querySelectorAll('.vc-queue-row');
    let visible = 0;

    rows.forEach(row => {
        const matchQ    = !q || (row.dataset.search || '').includes(q);
        const matchProg = !prog || (row.dataset.program || '') === prog;
        const matchComp = comp === 'all' || (row.dataset.completeness || '') === comp;
        const show = matchQ && matchProg && matchComp;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    const counter = document.getElementById('vcQueueCounter');
    if (counter) {
        counter.textContent = `Showing ${visible} of ${rows.length} candidate(s)`;
    }
}

function vcResetFilters() {
    const s = document.getElementById('vcQueueSearch');
    const p = document.getElementById('vcProgramFilter');
    const c = document.getElementById('vcCompletenessFilter');
    if (s) s.value = '';
    if (p) p.value = '';
    if (c) c.value = 'all';
    vcFilterTable();
}

function vcFilterAwaiting() {
    const q = (document.getElementById('vcAwaitingSearch')?.value || '').trim().toLowerCase();
    const rows = document.querySelectorAll('.vc-awaiting-row');
    let visible = 0;
    rows.forEach(r => {
        const show = !q || (r.dataset.search || '').includes(q);
        r.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    const counter = document.getElementById('vcAwaitingCounter');
    if (counter) counter.textContent = `Showing ${visible} of ${rows.length} student(s) awaiting upload`;
}

function vcFilterActivated() {
    const q = (document.getElementById('vcActivatedSearch')?.value || '').trim().toLowerCase();
    const rows = document.querySelectorAll('.vc-activated-row');
    let visible = 0;
    rows.forEach(r => {
        const show = !q || (r.dataset.search || '').includes(q);
        r.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    const counter = document.getElementById('vcActivatedCounter');
    if (counter) counter.textContent = `Showing ${visible} of ${rows.length} official activation(s)`;
}
</script>

<?php require_once __DIR__ . '/../includes/student-dossier-drawer.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
