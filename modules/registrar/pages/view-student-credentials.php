<?php
/**
 * SMS 2 - Registrar: Student Credential Review Workstation
 * Split-pane live document inspector (PDF + zoomable/rotatable Image viewer),
 * 5-document institutional checklist, deficiency return workflow, and activation modal.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/registrar-service.php';
require_once __DIR__ . '/../includes/storage-service.php';

regRequireAction('registrar.view');

$pdo       = db();
$studentId = (int) ($_GET['id'] ?? 0);

$pageTitle    = 'Student Credential Review';
$activeModule = 'registrar';
$activePage   = 'validate-credentials';

$student   = null;
$documents = [];
$history   = [];

if ($studentId > 0) {
    $stmt = $pdo->prepare("
        SELECT id, student_number, first_name, middle_name, last_name,
               email_address, contact_number, program_course, year_section, status, created_at
        FROM reg_students
        WHERE id = ?
        LIMIT 1
    ");
    $stmt->execute([$studentId]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

if ($student) {
    // Fetch uploaded documents (safe fallback if is_deleted doesn't exist)
    try {
        $docStmt = $pdo->prepare("
            SELECT id, category, original_name, stored_name, mime, size, created_at
            FROM reg_files
            WHERE student_id = ?
              AND (status = 'Active' OR status IS NULL)
              AND (is_deleted = 0 OR is_deleted IS NULL)
            ORDER BY created_at DESC
        ");
        $docStmt->execute([$studentId]);
        $documents = $docStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Throwable $e) {
        $docStmt = $pdo->prepare("
            SELECT id, category, original_name, stored_name, mime, size, created_at
            FROM reg_files
            WHERE student_id = ?
            ORDER BY created_at DESC
        ");
        $docStmt->execute([$studentId]);
        $documents = $docStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    // Fetch validation / status history timeline
    try {
        $hStmt = $pdo->prepare("
            SELECT h.changed_from, h.changed_to, h.reason, h.notes, h.changed_at,
                   COALESCE(u.full_name, u.username, 'System') AS officer_name
            FROM reg_status_history h
            LEFT JOIN users u ON u.id = h.changed_by
            WHERE h.student_id = ?
            ORDER BY h.changed_at DESC
            LIMIT 8
        ");
        $hStmt->execute([$studentId]);
        $history = $hStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Throwable $e) {
        $history = [];
    }
}

$studentFullName = $student
    ? trim((string) ($student['last_name'] ?? '') . ', ' . (string) ($student['first_name'] ?? '') . ' ' . (string) ($student['middle_name'] ?? ''))
    : 'Not Found';

$breadcrumbs = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Validate Credentials', 'url' => BASE_URL . '/modules/registrar/pages/validate-credentials.php'],
    ['label' => $student ? trim((string) ($student['first_name'] ?? '') . ' ' . (string) ($student['last_name'] ?? '')) : 'Review', 'url' => null],
];

// Standard 5 institutional admission requirements
$requiredDocs = [
    'form_138'           => ['short' => 'Form 138',    'label' => 'Form 138 (Report Card)',    'icon' => 'fa-file-invoice'],
    'form_137'           => ['short' => 'Form 137',    'label' => 'Form 137 / SF10',           'icon' => 'fa-file-alt'],
    'good_moral'         => ['short' => 'Good Moral',  'label' => 'Good Moral Certificate',    'icon' => 'fa-award'],
    'psa_birth_cert'     => ['short' => 'PSA Birth',   'label' => 'PSA Birth Certificate',     'icon' => 'fa-id-card'],
    'barangay_clearance' => ['short' => 'Brgy Clear.', 'label' => 'Barangay Clearance',        'icon' => 'fa-shield-alt'],
];

function vscMatchDoc(string $category, string $originalName = ''): ?string
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

function vscFormatBytes(int $bytes): string
{
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    }
    if ($bytes >= 1024) {
        return number_format($bytes / 1024, 1) . ' KB';
    }
    return $bytes . ' B';
}

function vscCategoryLabel(string $cat, array $requiredDocs): string
{
    if (isset($requiredDocs[$cat])) {
        return $requiredDocs[$cat]['label'];
    }
    $clean = str_replace(['_', '-'], ' ', trim($cat));
    return $clean !== '' ? ucwords($clean) : 'Uploaded Credential';
}

// Map matched requirements -> first matching document ID (for one-click jump-to-preview)
$matchedFileByReq = [];
foreach ($documents as $doc) {
    $k = vscMatchDoc((string) ($doc['category'] ?? ''), (string) ($doc['original_name'] ?? ''));
    if ($k !== null && !isset($matchedFileByReq[$k])) {
        $matchedFileByReq[$k] = (int) $doc['id'];
    }
}
$matchedCount   = count($matchedFileByReq);
$completenessPc = (int) round(($matchedCount / 5) * 100);
$isAlreadyActive = $student && ((string) ($student['status'] ?? '') === 'Active');

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<link href="<?php echo BASE_URL; ?>/assets/css/module-process-list.css?v=2" rel="stylesheet">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/modules/registrar/assets/css/registrar.css?v=<?php echo time(); ?>">

<style>
/* ============================================================================
   CREDENTIAL REVIEW WORKSTATION — LIGHT & DARK MODE
   ============================================================================ */
.vsc-header-card {
    background: var(--sms-surface, #ffffff);
    border: 1px solid var(--sms-border, #e2e8f0);
    border-radius: 16px;
    padding: 1.35rem 1.5rem;
    margin-bottom: 1.15rem;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
}
.vsc-header-top {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding-bottom: 1.1rem;
    border-bottom: 1px solid var(--sms-border, #e2e8f0);
}
.vsc-identity {
    display: flex;
    align-items: center;
    gap: 1rem;
    min-width: 0;
}
.vsc-avatar-lg {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
    color: #ffffff;
    font-size: 1.35rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.28);
}
.vsc-name {
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--sms-heading, #0f172a);
    margin: 0;
    line-height: 1.25;
}
.vsc-meta-chips {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.45rem;
    margin-top: 0.35rem;
}
.vsc-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.22rem 0.65rem;
    border-radius: 8px;
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #e2e8f0;
}
.vsc-chip.mono {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    background: #eff6ff;
    color: #1e40af;
    border-color: #bfdbfe;
}
.vsc-chip.status-verified {
    background: #fef3c7;
    color: #b45309;
    border-color: #fde68a;
}
.vsc-chip.status-active {
    background: #dcfce7;
    color: #15803d;
    border-color: #bbf7d0;
}

/* Info Strip inside Header */
.vsc-info-strip {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 1rem;
    padding-top: 1rem;
}
@media (max-width: 991.98px) {
    .vsc-info-strip { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 575.98px) {
    .vsc-info-strip { grid-template-columns: 1fr; }
}
.vsc-info-item label {
    display: block;
    font-size: 0.69rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--sms-text-muted, #64748b);
    margin-bottom: 2px;
}
.vsc-info-item span {
    font-size: 0.86rem;
    font-weight: 600;
    color: var(--sms-heading, #0f172a);
    word-break: break-word;
}

/* 4-Step Pipeline Stepper */
.vsc-stepper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    background: var(--sms-surface, #ffffff);
    border: 1px solid var(--sms-border, #e2e8f0);
    border-radius: 12px;
    padding: 0.75rem 1.1rem;
    margin-bottom: 1.15rem;
    overflow-x: auto;
}
.vsc-step {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8rem;
    font-weight: 700;
    color: #64748b;
    white-space: nowrap;
}
.vsc-step-num {
    width: 24px;
    height: 24px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.72rem;
    font-weight: 800;
    background: #e2e8f0;
    color: #475569;
}
.vsc-step.done { color: #059669; }
.vsc-step.done .vsc-step-num { background: #10b981; color: #ffffff; }
.vsc-step.active { color: #2563eb; }
.vsc-step.active .vsc-step-num { background: #2563eb; color: #ffffff; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2); }
.vsc-step-line {
    flex: 1;
    height: 2px;
    min-width: 24px;
    background: #e2e8f0;
    border-radius: 2px;
}
.vsc-step-line.done { background: #10b981; }

/* 5-Document Institutional Requirements Strip */
.vsc-req-strip {
    background: var(--sms-surface, #ffffff);
    border: 1px solid var(--sms-border, #e2e8f0);
    border-radius: 14px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.25rem;
}
.vsc-req-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 0.65rem;
    margin-top: 0.75rem;
}
@media (max-width: 1199.98px) {
    .vsc-req-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
@media (max-width: 767.98px) {
    .vsc-req-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
.vsc-req-tile {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.65rem 0.8rem;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    text-align: left;
    transition: all 0.15s ease;
    width: 100%;
}
.vsc-req-tile.submitted {
    background: #ecfdf5;
    border-color: #a7f3d0;
    cursor: pointer;
}
.vsc-req-tile.submitted:hover {
    border-color: #10b981;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(16, 185, 129, 0.14);
}
.vsc-req-tile.missing {
    border-style: dashed;
    opacity: 0.85;
}
.vsc-req-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    flex-shrink: 0;
}
.vsc-req-tile.submitted .vsc-req-icon { background: #10b981; color: #ffffff; }
.vsc-req-tile.missing .vsc-req-icon   { background: #e2e8f0; color: #64748b; }
.vsc-req-title {
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--sms-heading, #0f172a);
    line-height: 1.2;
}
.vsc-req-sub {
    font-size: 0.69rem;
    font-weight: 700;
}
.vsc-req-tile.submitted .vsc-req-sub { color: #059669; }
.vsc-req-tile.missing .vsc-req-sub   { color: #94a3b8; }

/* Split-Pane Workstation */
.vsc-pane-card {
    background: var(--sms-surface, #ffffff);
    border: 1px solid var(--sms-border, #e2e8f0);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
    display: flex;
    flex-direction: column;
}
.vsc-pane-head {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.65rem;
    padding: 0.9rem 1.15rem;
    background: var(--sms-surface-muted, #f8fafc);
    border-bottom: 1px solid var(--sms-border, #e2e8f0);
}
.vsc-pane-head h5 {
    margin: 0;
    font-size: 0.94rem;
    font-weight: 800;
    color: var(--sms-heading, #0f172a);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Left File List Items */
.vsc-file-list {
    padding: 0.85rem;
    display: flex;
    flex-direction: column;
    gap: 0.65rem;
    max-height: 540px;
    overflow-y: auto;
}
.vsc-file-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.85rem;
    border-radius: 12px;
    border: 1.5px solid #e2e8f0;
    background: #ffffff;
    cursor: pointer;
    transition: all 0.16s ease;
}
.vsc-file-item:hover {
    border-color: #93c5fd;
    background: #f8fbff;
}
.vsc-file-item.active {
    border-color: #2563eb;
    background: #eff6ff;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.14);
}
.vsc-file-type-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
}
.vsc-file-type-icon.pdf { background: #fee2e2; color: #dc2626; }
.vsc-file-type-icon.img { background: #dbeafe; color: #2563eb; }
.vsc-file-type-icon.doc { background: #f1f5f9; color: #475569; }
.vsc-file-cat {
    font-size: 0.82rem;
    font-weight: 800;
    color: var(--sms-heading, #0f172a);
    line-height: 1.25;
}
.vsc-file-name {
    font-size: 0.76rem;
    color: var(--sms-text-muted, #64748b);
    word-break: break-all;
    margin-top: 2px;
}
.vsc-file-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.4rem;
    font-size: 0.71rem;
    color: #64748b;
}
.vsc-inspect-toggle {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 0.2rem 0.55rem;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #475569;
    cursor: pointer;
    user-select: none;
}
.vsc-inspect-toggle.checked {
    background: #dcfce7;
    border-color: #86efac;
    color: #15803d;
}

/* Right Embedded Document Viewer */
.vsc-viewer-card {
    position: sticky;
    top: 80px;
}
.vsc-viewer-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    padding: 0.7rem 1rem;
    background: #0f172a;
    color: #e2e8f0;
    border-bottom: 1px solid rgba(148, 163, 184, 0.2);
}
.vsc-viewer-body {
    position: relative;
    height: 540px;
    background: #1e293b;
    overflow: auto;
    display: flex;
    align-items: center;
    justify-content: center;
}
.vsc-viewer-body iframe {
    width: 100%;
    height: 100%;
    border: none;
    background: #ffffff;
}
.vsc-viewer-img-stage {
    width: 100%;
    height: 100%;
    overflow: auto;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
}
.vsc-viewer-img-stage img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    border-radius: 6px;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.45);
    transition: transform 0.2s ease;
    background: #ffffff;
}
.vsc-viewer-empty {
    text-align: center;
    color: #94a3b8;
    padding: 3rem 1.5rem;
}
.vsc-viewer-empty i {
    font-size: 3rem;
    margin-bottom: 0.85rem;
    opacity: 0.55;
    display: block;
}

/* History Timeline */
.vsc-timeline {
    padding: 0.9rem 1.15rem;
}
.vsc-timeline-item {
    position: relative;
    padding-left: 1.35rem;
    padding-bottom: 0.9rem;
    border-left: 2px solid #e2e8f0;
}
.vsc-timeline-item:last-child {
    padding-bottom: 0;
    border-left-color: transparent;
}
.vsc-timeline-dot {
    position: absolute;
    left: -6px;
    top: 3px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #2563eb;
    box-shadow: 0 0 0 3px #eff6ff;
}
.vsc-timeline-dot.warn { background: #d97706; box-shadow: 0 0 0 3px #fef3c7; }
.vsc-timeline-dot.ok   { background: #10b981; box-shadow: 0 0 0 3px #dcfce7; }

/* Bottom Decision Action Bar */
.vsc-decision-bar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    background: var(--sms-surface, #ffffff);
    border: 1.5px solid #cbd5e1;
    border-radius: 14px;
    padding: 1.1rem 1.4rem;
    margin-top: 1.25rem;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
}
.vsc-cascade-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.vsc-cascade-list li {
    display: flex;
    align-items: center;
    gap: 0.55rem;
    padding: 0.42rem 0;
    font-size: 0.85rem;
}

/* ── Dark Mode Parity ─────────────────────────────────────────────────────── */
[data-theme="dark"] .vsc-header-card,
[data-theme="dark"] .vsc-stepper,
[data-theme="dark"] .vsc-req-strip,
[data-theme="dark"] .vsc-pane-card,
[data-theme="dark"] .vsc-decision-bar {
    background: #1e293b;
    border-color: rgba(148, 163, 184, 0.22);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.28);
}
[data-theme="dark"] .vsc-header-top,
[data-theme="dark"] .vsc-pane-head {
    background: rgba(15, 23, 42, 0.55);
    border-bottom-color: rgba(148, 163, 184, 0.18);
}
[data-theme="dark"] .vsc-header-top {
    background: transparent;
}
[data-theme="dark"] .vsc-chip {
    background: rgba(148, 163, 184, 0.14);
    color: #cbd5e1;
    border-color: rgba(148, 163, 184, 0.24);
}
[data-theme="dark"] .vsc-chip.mono {
    background: rgba(59, 130, 246, 0.18);
    color: #93c5fd;
    border-color: rgba(59, 130, 246, 0.34);
}
[data-theme="dark"] .vsc-chip.status-verified {
    background: rgba(245, 158, 11, 0.2);
    color: #fcd34d;
    border-color: rgba(245, 158, 11, 0.36);
}
[data-theme="dark"] .vsc-chip.status-active {
    background: rgba(16, 185, 129, 0.2);
    color: #6ee7b7;
    border-color: rgba(16, 185, 129, 0.36);
}
[data-theme="dark"] .vsc-step { color: #94a3b8; }
[data-theme="dark"] .vsc-step-num { background: #334155; color: #cbd5e1; }
[data-theme="dark"] .vsc-step.done { color: #34d399; }
[data-theme="dark"] .vsc-step.done .vsc-step-num { background: #10b981; color: #ffffff; }
[data-theme="dark"] .vsc-step.active { color: #60a5fa; }
[data-theme="dark"] .vsc-step.active .vsc-step-num { background: #3b82f6; color: #ffffff; }
[data-theme="dark"] .vsc-step-line { background: rgba(148, 163, 184, 0.24); }
[data-theme="dark"] .vsc-step-line.done { background: #10b981; }
[data-theme="dark"] .vsc-req-tile {
    background: rgba(15, 23, 42, 0.65);
    border-color: rgba(148, 163, 184, 0.22);
}
[data-theme="dark"] .vsc-req-tile.submitted {
    background: rgba(16, 185, 129, 0.12);
    border-color: rgba(16, 185, 129, 0.38);
}
[data-theme="dark"] .vsc-req-tile.missing .vsc-req-icon {
    background: rgba(148, 163, 184, 0.16);
    color: #94a3b8;
}
[data-theme="dark"] .vsc-file-item {
    background: rgba(15, 23, 42, 0.55);
    border-color: rgba(148, 163, 184, 0.2);
}
[data-theme="dark"] .vsc-file-item:hover {
    background: rgba(59, 130, 246, 0.12);
    border-color: rgba(96, 165, 250, 0.45);
}
[data-theme="dark"] .vsc-file-item.active {
    background: rgba(37, 99, 235, 0.2);
    border-color: #3b82f6;
}
[data-theme="dark"] .vsc-file-type-icon.pdf { background: rgba(239, 68, 68, 0.2); color: #fca5a5; }
[data-theme="dark"] .vsc-file-type-icon.img { background: rgba(59, 130, 246, 0.2); color: #93c5fd; }
[data-theme="dark"] .vsc-file-type-icon.doc { background: rgba(148, 163, 184, 0.16); color: #cbd5e1; }
[data-theme="dark"] .vsc-inspect-toggle {
    background: rgba(15, 23, 42, 0.8);
    border-color: rgba(148, 163, 184, 0.28);
    color: #cbd5e1;
}
[data-theme="dark"] .vsc-inspect-toggle.checked {
    background: rgba(16, 185, 129, 0.2);
    border-color: rgba(16, 185, 129, 0.42);
    color: #6ee7b7;
}
[data-theme="dark"] .vsc-timeline-item {
    border-left-color: rgba(148, 163, 184, 0.22);
}
[data-theme="dark"] .vsc-timeline-dot { box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25); }
[data-theme="dark"] .vsc-timeline-dot.warn { box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.25); }
[data-theme="dark"] .vsc-timeline-dot.ok { box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25); }
[data-theme="dark"] #activateConfirmModal .modal-content,
[data-theme="dark"] #deficiencyModal .modal-content,
[data-theme="dark"] #fullscreenDocModal .modal-content {
    background: #1e293b;
    border: 1px solid rgba(148, 163, 184, 0.24);
    color: #e2e8f0;
}
[data-theme="dark"] #activateConfirmModal .modal-footer,
[data-theme="dark"] #deficiencyModal .modal-footer {
    border-top-color: rgba(148, 163, 184, 0.18);
}
[data-theme="dark"] #activateConfirmModal .form-control,
[data-theme="dark"] #deficiencyModal .form-control {
    background-color: #0f172a;
    border-color: rgba(148, 163, 184, 0.28);
    color: #f1f5f9;
}
[data-theme="dark"] #deficiencyModal .form-check-label {
    color: #e2e8f0;
}
</style>

<?php renderBreadcrumbs($breadcrumbs); ?>

<div class="container-fluid py-4">
<div class="mpl" data-mpl>

<?php if (!$student): ?>
    <div class="mpl-panel p-5 text-center">
        <div class="mb-3 text-danger"><i class="fas fa-user-times fa-3x"></i></div>
        <h3 class="fw-bold mb-2">Student Record Not Found</h3>
        <p class="text-muted mb-4">The requested student ID could not be located in the Registrar database.</p>
        <a href="validate-credentials.php" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i> Return to Validation Queue
        </a>
    </div>
<?php else:
    $firstName  = (string) ($student['first_name'] ?? '');
    $lastName   = (string) ($student['last_name'] ?? '');
    $initials   = strtoupper(substr(trim($firstName), 0, 1) . substr(trim($lastName), 0, 1)) ?: 'ST';
    $studentNo  = (string) ($student['student_number'] ?? '');
    $emailAddr  = (string) ($student['email_address'] ?? '');
    $contactNo  = (string) ($student['contact_number'] ?? '');
    $program    = (string) ($student['program_course'] ?? '');
    $yearSec    = (string) ($student['year_section'] ?? '');
    $statusVal  = (string) ($student['status'] ?? 'Verified');
    $docCount   = count($documents);
?>

    <?php if (!empty($_GET['err'])): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-3" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?php echo htmlspecialchars((string) $_GET['err']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- 1. Executive Student Header Card -->
    <div class="vsc-header-card">
        <div class="vsc-header-top">
            <div class="vsc-identity">
                <div class="vsc-avatar-lg"><?php echo htmlspecialchars($initials); ?></div>
                <div>
                    <h1 class="vsc-name"><?php echo htmlspecialchars($studentFullName); ?></h1>
                    <div class="vsc-meta-chips">
                        <span class="vsc-chip mono"><i class="fas fa-hashtag"></i> <?php echo htmlspecialchars($studentNo); ?></span>
                        <span class="vsc-chip <?php echo $isAlreadyActive ? 'status-active' : 'status-verified'; ?>">
                            <i class="fas <?php echo $isAlreadyActive ? 'fa-check-circle' : 'fa-hourglass-half'; ?>"></i>
                            <?php echo $isAlreadyActive ? 'Officially Enrolled (Active)' : htmlspecialchars($statusVal) . ' — Pending Activation'; ?>
                        </span>
                        <span class="vsc-chip">
                            <i class="fas fa-paperclip text-primary"></i> <?php echo $docCount; ?> Uploaded File<?php echo $docCount === 1 ? '' : 's'; ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-sm btn-outline-primary rounded-3 px-3 fw-semibold" onclick="viewStudent(<?php echo (int) $student['id']; ?>, 'docs')">
                    <i class="fas fa-user-graduate me-1"></i> View 360° Dossier
                </button>
                <a href="validate-credentials.php" class="btn btn-sm btn-outline-secondary rounded-3 px-3 fw-semibold">
                    <i class="fas fa-arrow-left me-1"></i> Back to Queue
                </a>
            </div>
        </div>

        <div class="vsc-info-strip">
            <div class="vsc-info-item">
                <label><i class="fas fa-graduation-cap me-1 text-primary"></i> Program / Course</label>
                <span><?php echo htmlspecialchars($program !== '' ? $program : 'Not Assigned'); ?></span>
            </div>
            <div class="vsc-info-item">
                <label><i class="fas fa-layer-group me-1 text-primary"></i> Year &amp; Section</label>
                <span><?php echo htmlspecialchars($yearSec !== '' ? $yearSec : 'Pre-Enrollment'); ?></span>
            </div>
            <div class="vsc-info-item">
                <label><i class="fas fa-envelope me-1 text-primary"></i> Email Address</label>
                <span><?php echo htmlspecialchars($emailAddr !== '' ? $emailAddr : 'Not Provided'); ?></span>
            </div>
            <div class="vsc-info-item">
                <label><i class="fas fa-phone me-1 text-primary"></i> Contact Number</label>
                <span><?php echo htmlspecialchars($contactNo !== '' ? $contactNo : 'Not Provided'); ?></span>
            </div>
        </div>
    </div>

    <!-- 2. 4-Step Enrollment Validation Pipeline Stepper -->
    <div class="vsc-stepper" aria-label="Enrollment validation stages">
        <div class="vsc-step done">
            <span class="vsc-step-num"><i class="fas fa-check"></i></span>
            <span>1. Pre-Enrolled</span>
        </div>
        <div class="vsc-step-line <?php echo $docCount > 0 ? 'done' : ''; ?>"></div>
        <div class="vsc-step <?php echo $docCount > 0 ? 'done' : 'active'; ?>">
            <span class="vsc-step-num"><?php echo $docCount > 0 ? '<i class="fas fa-check"></i>' : '2'; ?></span>
            <span>2. Credentials Uploaded</span>
        </div>
        <div class="vsc-step-line <?php echo $isAlreadyActive ? 'done' : ''; ?>"></div>
        <div class="vsc-step <?php echo $isAlreadyActive ? 'done' : ($docCount > 0 ? 'active' : ''); ?>">
            <span class="vsc-step-num"><?php echo $isAlreadyActive ? '<i class="fas fa-check"></i>' : '3'; ?></span>
            <span>3. Registrar Review</span>
        </div>
        <div class="vsc-step-line <?php echo $isAlreadyActive ? 'done' : ''; ?>"></div>
        <div class="vsc-step <?php echo $isAlreadyActive ? 'done' : ''; ?>">
            <span class="vsc-step-num"><?php echo $isAlreadyActive ? '<i class="fas fa-check"></i>' : '4'; ?></span>
            <span>4. Officially Enrolled</span>
        </div>
    </div>

    <!-- 3. 5-Document Institutional Requirements Strip -->
    <div class="vsc-req-strip">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="fw-bold small text-uppercase text-muted">
                <i class="fas fa-tasks text-primary me-1"></i> Standard Institutional Requirements Checklist
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="progress" style="width:120px; height:7px; border-radius:999px;">
                    <div class="progress-bar <?php echo $matchedCount >= 5 ? 'bg-success' : 'bg-primary'; ?>" role="progressbar" style="width: <?php echo $completenessPc; ?>%;"></div>
                </div>
                <span class="small fw-bold <?php echo $matchedCount >= 5 ? 'text-success' : 'text-primary'; ?>">
                    <?php echo $matchedCount; ?> / 5 Matched (<?php echo $completenessPc; ?>%)
                </span>
            </div>
        </div>
        <div class="vsc-req-grid">
            <?php foreach ($requiredDocs as $code => $meta):
                $fileId = $matchedFileByReq[$code] ?? null;
                $has    = $fileId !== null;
            ?>
            <button type="button"
                    class="vsc-req-tile <?php echo $has ? 'submitted' : 'missing'; ?>"
                    <?php echo $has ? 'onclick="vscJumpToFile(' . (int) $fileId . ')" title="Click to preview ' . htmlspecialchars($meta['label']) . '"' : 'disabled'; ?>>
                <div class="vsc-req-icon">
                    <i class="fas <?php echo $has ? 'fa-check' : $meta['icon']; ?>"></i>
                </div>
                <div class="min-w-0">
                    <div class="vsc-req-title text-truncate"><?php echo htmlspecialchars($meta['short']); ?></div>
                    <div class="vsc-req-sub"><?php echo $has ? 'Submitted — View' : 'Not Uploaded'; ?></div>
                </div>
            </button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 4. Split-Pane Live Document Review Workstation -->
    <div class="row g-4 align-items-start">
        <!-- LEFT PANE: Uploaded Files + Validation History -->
        <div class="col-lg-5">
            <div class="vsc-pane-card mb-4">
                <div class="vsc-pane-head">
                    <h5><i class="fas fa-folder-open text-primary"></i> Submitted Credentials (<?php echo $docCount; ?>)</h5>
                    <?php if ($docCount > 0): ?>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary-subtle text-primary rounded-pill" id="vscInspectBadge">0 / <?php echo $docCount; ?> Inspected</span>
                        <button type="button" class="btn btn-xs btn-outline-success" onclick="vscMarkAllInspected()" title="Mark all files as inspected">
                            <i class="fas fa-check-double me-1"></i> Verify All
                        </button>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if (empty($documents)): ?>
                    <div class="p-5 text-center text-muted">
                        <i class="fas fa-folder-open fa-2x mb-2 opacity-50"></i>
                        <div class="fw-bold mb-1">No Documents Uploaded Yet</div>
                        <small>This student has not submitted any credential files for validation.</small>
                    </div>
                <?php else: ?>
                    <div class="vsc-file-list" id="vscFileList">
                        <?php foreach ($documents as $idx => $doc):
                            $docId     = (int) $doc['id'];
                            $catRaw    = (string) ($doc['category'] ?? '');
                            $catLabel  = vscCategoryLabel($catRaw, $requiredDocs);
                            $origName  = (string) ($doc['original_name'] ?? ('document-' . $docId));
                            $mime      = strtolower((string) ($doc['mime'] ?? ''));
                            $sizeBytes = (int) ($doc['size'] ?? 0);
                            $isImg     = str_starts_with($mime, 'image/') || preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $origName);
                            $isPdf     = str_contains($mime, 'pdf') || preg_match('/\.pdf$/i', $origName);
                            $iconClass = $isPdf ? 'pdf' : ($isImg ? 'img' : 'doc');
                            $iconFa    = $isPdf ? 'fa-file-pdf' : ($isImg ? 'fa-file-image' : 'fa-file-alt');
                            $viewUrl   = BASE_URL . '/modules/registrar/api/view-document.php?file_id=' . $docId;
                            $dlUrl     = $viewUrl . '&mode=download';
                            $dateFmt   = !empty($doc['created_at']) ? date('M d, Y h:i A', strtotime((string) $doc['created_at'])) : '—';
                        ?>
                        <div class="vsc-file-item <?php echo $idx === 0 ? 'active' : ''; ?>"
                             id="vscFileCard_<?php echo $docId; ?>"
                             data-file-id="<?php echo $docId; ?>"
                             data-url="<?php echo htmlspecialchars($viewUrl); ?>"
                             data-dl="<?php echo htmlspecialchars($dlUrl); ?>"
                             data-title="<?php echo htmlspecialchars($catLabel); ?>"
                             data-filename="<?php echo htmlspecialchars($origName); ?>"
                             data-size="<?php echo htmlspecialchars(vscFormatBytes($sizeBytes)); ?>"
                             data-mime="<?php echo htmlspecialchars($mime !== '' ? $mime : ($isImg ? 'image/*' : 'application/pdf')); ?>"
                             data-is-img="<?php echo $isImg ? '1' : '0'; ?>"
                             onclick="vscSelectDocument(this)">
                            <div class="vsc-file-type-icon <?php echo $iconClass; ?>">
                                <i class="fas <?php echo $iconFa; ?>"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div class="vsc-file-cat"><?php echo htmlspecialchars($catLabel); ?></div>
                                    <div class="d-flex gap-1" onclick="event.stopPropagation();">
                                        <a href="<?php echo htmlspecialchars($dlUrl); ?>" class="btn btn-xs btn-outline-secondary" title="Download File">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="vsc-file-name"><?php echo htmlspecialchars($origName); ?></div>
                                <div class="vsc-file-meta">
                                    <span><i class="far fa-hdd me-1"></i><?php echo htmlspecialchars(vscFormatBytes($sizeBytes)); ?></span>
                                    <span>&bull;</span>
                                    <span><i class="far fa-clock me-1"></i><?php echo htmlspecialchars($dateFmt); ?></span>
                                </div>
                                <div class="mt-2" onclick="event.stopPropagation();">
                                    <label class="vsc-inspect-toggle" id="vscInspectLabel_<?php echo $docId; ?>">
                                        <input type="checkbox" class="form-check-input m-0 vsc-inspect-chk" data-file-id="<?php echo $docId; ?>" onchange="vscToggleInspect(<?php echo $docId; ?>, this.checked)">
                                        <span>Mark Inspected</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Validation & Status Audit Timeline -->
            <div class="vsc-pane-card">
                <div class="vsc-pane-head">
                    <h5><i class="fas fa-history text-primary"></i> Validation &amp; Status History</h5>
                </div>
                <?php if (empty($history)): ?>
                    <div class="p-4 text-center text-muted small">
                        <i class="fas fa-stream me-1"></i> No prior validation or status transitions recorded yet.
                    </div>
                <?php else: ?>
                    <div class="vsc-timeline">
                        <?php foreach ($history as $h):
                            $reasonStr = (string) ($h['reason'] ?? 'Status Update');
                            $isDef = stripos($reasonStr, 'deficiency') !== false;
                            $isAct = ((string) ($h['changed_to'] ?? '') === 'Active');
                            $dotCls = $isDef ? 'warn' : ($isAct ? 'ok' : '');
                        ?>
                        <div class="vsc-timeline-item">
                            <span class="vsc-timeline-dot <?php echo $dotCls; ?>"></span>
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div class="fw-bold small"><?php echo htmlspecialchars($reasonStr); ?></div>
                                <span class="text-muted" style="font-size:0.7rem;white-space:nowrap;">
                                    <?php echo !empty($h['changed_at']) ? date('M d, Y h:i A', strtotime((string) $h['changed_at'])) : ''; ?>
                                </span>
                            </div>
                            <?php if (!empty($h['notes'])): ?>
                                <div class="small text-muted mt-1"><?php echo htmlspecialchars((string) $h['notes']); ?></div>
                            <?php endif; ?>
                            <div class="text-muted mt-1" style="font-size:0.71rem;">
                                <i class="fas fa-user-shield me-1"></i><?php echo htmlspecialchars((string) ($h['officer_name'] ?? 'Registrar')); ?>
                                &bull; <?php echo htmlspecialchars((string) ($h['changed_from'] ?? '—')); ?> &rarr; <?php echo htmlspecialchars((string) ($h['changed_to'] ?? '—')); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- RIGHT PANE: Sticky Live Embedded Document Inspector -->
        <div class="col-lg-7">
            <div class="vsc-pane-card vsc-viewer-card">
                <div class="vsc-viewer-toolbar">
                    <div class="min-w-0">
                        <div class="fw-bold small text-white text-truncate" id="vscViewerTitle">
                            <i class="fas fa-eye text-info me-1"></i> Live Document Inspector
                        </div>
                        <div class="text-white-50 text-truncate" style="font-size:0.72rem;" id="vscViewerMeta">
                            Select a file on the left to inspect
                        </div>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-1">
                        <!-- Image-only controls (shown when inspecting an image) -->
                        <div class="btn-group btn-group-sm me-1" id="vscImgControls" style="display:none;">
                            <button type="button" class="btn btn-outline-light" onclick="vscZoom(-0.2)" title="Zoom Out"><i class="fas fa-search-minus"></i></button>
                            <button type="button" class="btn btn-outline-light" id="vscZoomLabel" onclick="vscResetTransform()" title="Reset Zoom">100%</button>
                            <button type="button" class="btn btn-outline-light" onclick="vscZoom(0.2)" title="Zoom In"><i class="fas fa-search-plus"></i></button>
                            <button type="button" class="btn btn-outline-light" onclick="vscRotate()" title="Rotate 90°"><i class="fas fa-redo"></i></button>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-light" id="vscBtnFullscreen" onclick="vscOpenFullscreen()" style="display:none;" title="Fullscreen Preview">
                            <i class="fas fa-expand"></i>
                        </button>
                        <a href="#" target="_blank" rel="noopener" class="btn btn-sm btn-outline-light" id="vscBtnNewTab" style="display:none;" title="Open in New Tab">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-primary" id="vscBtnDownload" style="display:none;" title="Download Document">
                            <i class="fas fa-download me-1"></i> Download
                        </a>
                    </div>
                </div>
                <div class="vsc-viewer-body" id="vscViewerBody">
                    <div class="vsc-viewer-empty">
                        <i class="fas fa-file-signature"></i>
                        <div class="fw-bold text-white mb-1">No Document Selected</div>
                        <small>Click any uploaded file from the left panel to inspect it directly here.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Executive Decision Action Bar -->
    <div class="vsc-decision-bar">
        <div class="d-flex align-items-center gap-3">
            <?php if ($isAlreadyActive): ?>
                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width:42px;height:42px;flex-shrink:0;">
                    <i class="fas fa-check fa-lg"></i>
                </div>
                <div>
                    <div class="fw-bold text-success">Student is Officially Enrolled &amp; Activated</div>
                    <div class="small text-muted">Their account is already active across Student Directory, Academic History, and Masterlist.</div>
                </div>
            <?php else: ?>
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width:42px;height:42px;flex-shrink:0;">
                    <i class="fas fa-gavel fa-lg"></i>
                </div>
                <div>
                    <div class="fw-bold" id="vscDecisionHeading">Registrar Validation Decision</div>
                    <div class="small text-muted" id="vscDecisionSub">
                        <?php echo $docCount > 0 ? "Inspect the {$docCount} uploaded file(s) above, then approve activation or flag missing/unclear items." : 'No documents uploaded yet — you may still send a deficiency reminder or inspect their dossier.'; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <?php if ($isAlreadyActive): ?>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" onclick="viewStudent(<?php echo (int) $student['id']; ?>, 'overview')">
                    <i class="fas fa-user-graduate me-2"></i> Open 360° Student Dossier
                </button>
            <?php else: ?>
                <button type="button" class="btn btn-outline-warning rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#deficiencyModal">
                    <i class="fas fa-exclamation-triangle me-2"></i> Return / Flag Deficiency
                </button>
                <button type="button" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#activateConfirmModal">
                    <i class="fas fa-user-check me-2"></i> Officially Enroll &amp; Activate
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- ================= MODAL 1: OFFICIAL ACTIVATION CONFIRMATION ================= -->
    <?php if (!$isAlreadyActive): ?>
    <div class="modal fade" id="activateConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-0 py-3 px-4 text-white" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0">
                            <i class="fas fa-user-check me-2"></i>Confirm Official Enrollment &amp; Activation
                        </h5>
                        <small class="text-white-50"><?php echo htmlspecialchars($studentFullName . ' (' . $studentNo . ')'); ?></small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../api/activate-student.php" method="POST" id="activateForm" onsubmit="vscHandleActivateSubmit()">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="action" value="activate">
                    <input type="hidden" name="student_id" value="<?php echo (int) $student['id']; ?>">
                    <div class="modal-body p-4">
                        <p class="small text-muted mb-3">
                            Approving credential validation will immediately execute the following institutional actions:
                        </p>
                        <ul class="vsc-cascade-list mb-3">
                            <li><i class="fas fa-check-circle text-success"></i> Promote student status from <strong><?php echo htmlspecialchars($statusVal); ?></strong> to <strong>Active</strong></li>
                            <li><i class="fas fa-check-circle text-success"></i> Upgrade portal account to <strong>Student</strong> &amp; lift 7-day temporary expiry</li>
                            <li><i class="fas fa-check-circle text-success"></i> Cascade profile into <strong>SIS, Academic History, Digital Storage &amp; Masterlist</strong></li>
                            <li><i class="fas fa-check-circle text-success"></i> Record immutable audit entry in <strong>Status Transition History</strong></li>
                            <li><i class="fas fa-envelope text-primary"></i> Send official enrollment email to <strong><?php echo htmlspecialchars($emailAddr !== '' ? $emailAddr : 'student email'); ?></strong></li>
                        </ul>

                        <?php if ($matchedCount < 5): ?>
                            <div class="alert alert-warning py-2 px-3 small mb-3 rounded-3">
                                <i class="fas fa-info-circle me-1"></i>
                                <strong>Note:</strong> <?php echo $matchedCount; ?> of 5 standard checklist items matched. You may still proceed if conditional enrollment or alternative credentials were verified.
                            </div>
                        <?php endif; ?>

                        <div>
                            <label class="form-label small fw-bold text-uppercase text-muted">Registrar Validation Remarks (Optional)</label>
                            <textarea name="activation_notes" class="form-control form-control-sm" rows="2" placeholder="e.g. All original credentials verified at Registrar counter..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-light border rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold" id="vscActivateSubmitBtn">
                            <i class="fas fa-check-circle me-1"></i> Confirm &amp; Activate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ================= MODAL 2: RETURN / FLAG DEFICIENCY ================= -->
    <div class="modal fade" id="deficiencyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-0 py-3 px-4 text-white" style="background: linear-gradient(135deg, #b45309 0%, #d97706 100%);">
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>Flag Credential Deficiency
                        </h5>
                        <small class="text-white-50">Log deficiency audit note &amp; notify <?php echo htmlspecialchars($studentFullName); ?></small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../api/activate-student.php" method="POST" id="deficiencyForm" onsubmit="vscHandleDeficiencySubmit()">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="action" value="return_deficiency">
                    <input type="hidden" name="student_id" value="<?php echo (int) $student['id']; ?>">
                    <div class="modal-body p-4">
                        <label class="form-label small fw-bold text-uppercase text-muted mb-2">Select Issue(s) Found</label>
                        <div class="d-flex flex-column gap-2 mb-3">
                            <?php
                            $commonIssues = [
                                'Blurry, cropped, or unreadable document scan/photo',
                                'Missing Form 138 (Report Card)',
                                'Missing Form 137 / SF10 Permanent Record',
                                'Missing Certificate of Good Moral Character',
                                'Missing PSA Authenticated Birth Certificate',
                                'Missing Barangay Clearance',
                                'Student name or details do not match enrollment record',
                            ];
                            foreach ($commonIssues as $iIdx => $issueText):
                            ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="deficiency_reasons[]" value="<?php echo htmlspecialchars($issueText); ?>" id="defIssue_<?php echo $iIdx; ?>">
                                <label class="form-check-label small" for="defIssue_<?php echo $iIdx; ?>"><?php echo htmlspecialchars($issueText); ?></label>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div>
                            <label class="form-label small fw-bold text-uppercase text-muted">Additional Instructions for Student</label>
                            <textarea name="deficiency_remarks" class="form-control form-control-sm" rows="3" placeholder="Specify which page or document must be re-uploaded or brought to the Registrar's Office..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-light border rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark" id="vscDefSubmitBtn">
                            <i class="fas fa-paper-plane me-1"></i> Log &amp; Notify Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ================= MODAL 3: FULLSCREEN DOCUMENT MODAL ================= -->
    <div class="modal fade" id="fullscreenDocModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg overflow-hidden" style="height: 90vh;">
                <div class="modal-header bg-dark text-white py-2 px-3">
                    <h6 class="modal-title fw-bold mb-0">
                        <i class="fas fa-file-alt me-2 text-info"></i><span id="vscFsTitle">Document Preview</span>
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 bg-dark" id="vscFsBody" style="overflow: hidden;"></div>
            </div>
        </div>
    </div>

<script>
const VSC_STUDENT_ID = <?php echo (int) $student['id']; ?>;
const VSC_TOTAL_DOCS = <?php echo $docCount; ?>;
const VSC_STORE_KEY  = 'reg_vsc_inspected_' + VSC_STUDENT_ID;

let vscActiveDoc = null;
let vscZoomScale = 1;
let vscRotation  = 0;

function vscGetInspectedSet() {
    try {
        const raw = sessionStorage.getItem(VSC_STORE_KEY);
        return new Set(raw ? JSON.parse(raw) : []);
    } catch (e) {
        return new Set();
    }
}

function vscSaveInspectedSet(set) {
    try {
        sessionStorage.setItem(VSC_STORE_KEY, JSON.stringify(Array.from(set)));
    } catch (e) {}
}

function vscUpdateInspectUI() {
    const inspected = vscGetInspectedSet();
    let count = 0;
    document.querySelectorAll('.vsc-inspect-chk').forEach(chk => {
        const fid = Number(chk.dataset.fileId);
        const isChecked = inspected.has(fid);
        chk.checked = isChecked;
        const lbl = document.getElementById('vscInspectLabel_' + fid);
        if (lbl) {
            lbl.classList.toggle('checked', isChecked);
            const span = lbl.querySelector('span');
            if (span) span.textContent = isChecked ? 'Inspected ✓' : 'Mark Inspected';
        }
        if (isChecked) count++;
    });

    const badge = document.getElementById('vscInspectBadge');
    if (badge) {
        badge.textContent = `${count} / ${VSC_TOTAL_DOCS} Inspected`;
        badge.className = count >= VSC_TOTAL_DOCS && VSC_TOTAL_DOCS > 0
            ? 'badge bg-success text-white rounded-pill'
            : 'badge bg-primary-subtle text-primary rounded-pill';
    }

    const sub = document.getElementById('vscDecisionSub');
    if (sub && VSC_TOTAL_DOCS > 0) {
        sub.textContent = count >= VSC_TOTAL_DOCS
            ? `All ${VSC_TOTAL_DOCS} uploaded document(s) have been inspected — ready for Registrar decision.`
            : `${count} of ${VSC_TOTAL_DOCS} uploaded document(s) inspected. Click files on the left to review.`;
    }
}

function vscToggleInspect(fileId, checked) {
    const set = vscGetInspectedSet();
    if (checked) set.add(Number(fileId));
    else set.delete(Number(fileId));
    vscSaveInspectedSet(set);
    vscUpdateInspectUI();
}

function vscMarkAllInspected() {
    const set = vscGetInspectedSet();
    document.querySelectorAll('.vsc-inspect-chk').forEach(chk => {
        set.add(Number(chk.dataset.fileId));
    });
    vscSaveInspectedSet(set);
    vscUpdateInspectUI();
}

function vscJumpToFile(fileId) {
    const card = document.getElementById('vscFileCard_' + fileId);
    if (card) {
        card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        vscSelectDocument(card);
    }
}

function vscSelectDocument(el) {
    if (!el) return;
    document.querySelectorAll('.vsc-file-item').forEach(item => item.classList.remove('active'));
    el.classList.add('active');

    vscActiveDoc = {
        id: Number(el.dataset.fileId),
        url: el.dataset.url,
        dl: el.dataset.dl,
        title: el.dataset.title,
        filename: el.dataset.filename,
        size: el.dataset.size,
        mime: el.dataset.mime,
        isImg: el.dataset.isImg === '1',
    };

    vscZoomScale = 1;
    vscRotation  = 0;

    const titleEl = document.getElementById('vscViewerTitle');
    const metaEl  = document.getElementById('vscViewerMeta');
    const bodyEl  = document.getElementById('vscViewerBody');
    const imgCtrl = document.getElementById('vscImgControls');
    const btnFs   = document.getElementById('vscBtnFullscreen');
    const btnTab  = document.getElementById('vscBtnNewTab');
    const btnDl   = document.getElementById('vscBtnDownload');

    if (titleEl) titleEl.innerHTML = `<i class="fas fa-eye text-info me-1"></i> ${vscEsc(vscActiveDoc.title)}`;
    if (metaEl)  metaEl.textContent = `${vscActiveDoc.filename} • ${vscActiveDoc.size}`;

    if (imgCtrl) imgCtrl.style.display = vscActiveDoc.isImg ? '' : 'none';
    if (btnFs)   btnFs.style.display = '';
    if (btnTab)  { btnTab.href = vscActiveDoc.url; btnTab.style.display = ''; }
    if (btnDl)   { btnDl.href  = vscActiveDoc.dl;  btnDl.style.display  = ''; }

    if (vscActiveDoc.isImg) {
        bodyEl.innerHTML = `
            <div class="vsc-viewer-img-stage">
                <img id="vscPreviewImg" src="${vscEsc(vscActiveDoc.url)}" alt="${vscEsc(vscActiveDoc.title)}">
            </div>
        `;
        vscApplyImgTransform();
    } else {
        bodyEl.innerHTML = `
            <iframe src="${vscEsc(vscActiveDoc.url)}" title="${vscEsc(vscActiveDoc.title)}"></iframe>
        `;
    }
}

function vscZoom(delta) {
    vscZoomScale = Math.min(3, Math.max(0.4, Number((vscZoomScale + delta).toFixed(2))));
    vscApplyImgTransform();
}

function vscRotate() {
    vscRotation = (vscRotation + 90) % 360;
    vscApplyImgTransform();
}

function vscResetTransform() {
    vscZoomScale = 1;
    vscRotation  = 0;
    vscApplyImgTransform();
}

function vscApplyImgTransform() {
    const img = document.getElementById('vscPreviewImg');
    if (img) {
        img.style.transform = `scale(${vscZoomScale}) rotate(${vscRotation}deg)`;
    }
    const lbl = document.getElementById('vscZoomLabel');
    if (lbl) lbl.textContent = Math.round(vscZoomScale * 100) + '%';
}

function vscOpenFullscreen() {
    if (!vscActiveDoc) return;
    document.getElementById('vscFsTitle').textContent = `${vscActiveDoc.title} — ${vscActiveDoc.filename}`;
    document.getElementById('vscFsBody').innerHTML = `<iframe src="${vscEsc(vscActiveDoc.url)}" style="width:100%;height:100%;border:none;background:#fff;"></iframe>`;
    const modalEl = document.getElementById('fullscreenDocModal');
    const m = bootstrap.Modal.getOrCreateInstance(modalEl);
    m.show();
}

function vscHandleActivateSubmit() {
    const btn = document.getElementById('vscActivateSubmitBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Activating...';
    }
}

function vscHandleDeficiencySubmit() {
    const btn = document.getElementById('vscDefSubmitBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Sending Notice...';
    }
}

function vscEsc(str) {
    const d = document.createElement('div');
    d.textContent = String(str ?? '');
    return d.innerHTML;
}

document.addEventListener('DOMContentLoaded', function() {
    vscUpdateInspectUI();

    // Auto-load first uploaded document in live inspector
    const firstCard = document.querySelector('.vsc-file-item');
    if (firstCard) {
        vscSelectDocument(firstCard);
    }

    const fsModal = document.getElementById('fullscreenDocModal');
    if (fsModal) {
        fsModal.addEventListener('hidden.bs.modal', function () {
            document.getElementById('vscFsBody').innerHTML = '';
        });
    }
});
</script>

<?php endif; ?>

</div>
</div>

<?php require_once __DIR__ . '/../includes/student-dossier-drawer.php'; ?>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
