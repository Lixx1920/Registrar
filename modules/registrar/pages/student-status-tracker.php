<?php
/**
 * SMS 2 - Student Status Tracker
 * Module: Registrar
 *
 * Stat cards by status, searchable student list, and a slide-in offcanvas
 * panel showing the full change history for any student. Registrar staff
 * can update a student's status with a reason and notes — every change is
 * logged to reg_status_history.
 *
 * UI uses the shared mpl- (module-process-list) design tokens to match
 * the rest of the system.
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

$db = db();

// Status distribution for the stat cards (live).
$statsRaw = $db->query(
    "SELECT `status`, COUNT(*) AS cnt FROM `reg_students`
     WHERE `status` != 'Deleted' GROUP BY `status`"
)->fetchAll(PDO::FETCH_KEY_PAIR);

$totalStudents = array_sum($statsRaw);
$statuses = ['Active', 'Inactive', 'Irregular', 'Graduated', 'Leave of Absence', 'Transferred', 'Dismissed', 'Dropout'];

// Programs for the filter dropdown.
$programs = $db->query(
    "SELECT DISTINCT `program_course` FROM `reg_students`
     WHERE `status` != 'Deleted' AND `program_course` IS NOT NULL AND `program_course` != ''
     ORDER BY `program_course`"
)->fetchAll(PDO::FETCH_COLUMN);

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<link href="<?php echo BASE_URL; ?>/assets/css/module-process-list.css?v=2" rel="stylesheet">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/modules/registrar/assets/css/registrar.css">

<?php renderBreadcrumbs($breadcrumbs); ?>

<div id="sstAlertBox"></div>

<div class="container-fluid py-4">
<div class="mpl" data-mpl>

    <!-- Page top bar -->
    <div class="mpl-top">
        <p>Track and manage student enrollment status changes with full audit history.</p>
        <div class="mpl-toolbar">
            <span class="badge bg-success text-white px-3 py-2 rounded-pill" style="font-size:.78rem;">
                <i class="fas fa-circle me-1" style="font-size:.5rem;vertical-align:middle;"></i> Live
            </span>
        </div>
    </div>

    <!-- ── Stat Cards ──────────────────────────────────────────────────────── -->
    <?php
    $statConfig = [
        'all'              => ['blue',   'fa-users',            'All Students',     $totalStudents,              ''],
        'Active'           => ['green',  'fa-check-circle',     'Active',           $statsRaw['Active'] ?? 0,    'Active'],
        'Inactive'         => ['amber',  'fa-pause-circle',     'Inactive',         $statsRaw['Inactive'] ?? 0,  'Inactive'],
        'Irregular'        => ['purple', 'fa-exclamation-circle','Irregular',       $statsRaw['Irregular'] ?? 0, 'Irregular'],
        'Graduated'        => ['teal',   'fa-graduation-cap',   'Graduated',        $statsRaw['Graduated'] ?? 0, 'Graduated'],
        'Leave of Absence' => ['orange', 'fa-calendar-times',   'Leave of Absence', $statsRaw['Leave of Absence'] ?? 0, 'Leave of Absence'],
        'Transferred'      => ['gray',   'fa-exchange-alt',     'Transferred',      $statsRaw['Transferred'] ?? 0,'Transferred'],
        'Dismissed'        => ['red',    'fa-ban',              'Dismissed',        $statsRaw['Dismissed'] ?? 0, 'Dismissed'],
        'Dropout'          => ['red',    'fa-user-slash',       'Dropout',          $statsRaw['Dropout'] ?? 0,   'Dropout'],
    ];
    ?>
    <section class="mpl-stats" aria-label="Status summary" style="grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));">
        <?php foreach ($statConfig as $key => [$color, $icon, $label, $count, $filterVal]): ?>
        <article class="mpl-stat" onclick="sstFilterStatus('<?php echo htmlspecialchars($filterVal); ?>')"
                 style="cursor:pointer;" title="Filter by <?php echo htmlspecialchars($label); ?>">
            <div class="mpl-stat-icon <?php echo $color; ?>">
                <i class="fas <?php echo $icon; ?>"></i>
            </div>
            <div>
                <span><?php echo htmlspecialchars($label); ?></span>
                <strong id="statCard_<?php echo preg_replace('/\W/', '', $key); ?>"><?php echo $count; ?></strong>
            </div>
        </article>
        <?php endforeach; ?>
    </section>

    <!-- ── Filters ─────────────────────────────────────────────────────────── -->
    <div class="mpl-filters">
        <label class="mpl-search">
            <i class="fas fa-search"></i>
            <input type="search" id="sstSearch" placeholder="Search by student number or name…" aria-label="Search students">
        </label>
        <select id="sstStatusFilter" aria-label="Filter by status">
            <option value="">All Statuses</option>
            <?php foreach ($statuses as $st): ?>
            <option value="<?php echo htmlspecialchars($st); ?>"><?php echo htmlspecialchars($st); ?></option>
            <?php endforeach; ?>
        </select>
        <select id="sstProgramFilter" aria-label="Filter by program">
            <option value="">All Programs</option>
            <?php foreach ($programs as $prog): ?>
            <option value="<?php echo htmlspecialchars($prog); ?>"><?php echo htmlspecialchars($prog); ?></option>
            <?php endforeach; ?>
        </select>
        <a class="mpl-refresh" href="javascript:void(0)" onclick="sstPage=1;sstLoad();">
            <i class="fas fa-sync-alt" aria-hidden="true"></i> Refresh
        </a>
    </div>

    <!-- ── Student Table ───────────────────────────────────────────────────── -->
    <section class="mpl-panel">
        <div class="mpl-panel-head">
            <div>
                <h2>Student Records</h2>
                <p>Click a row to view status history and apply changes.</p>
            </div>
        </div>
        <div class="mpl-table-wrap">
            <table class="mpl-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Program / Year &amp; Section</th>
                        <th>Current Status</th>
                        <th>Last Changed</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="sstTbody">
                    <tr>
                        <td colspan="6" style="text-align:center;color:var(--sms-text-muted);padding:2rem;">
                            <i class="fas fa-spinner fa-spin text-primary"></i>
                            <p class="mt-2 mb-0">Loading students…</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="mpl-foot">
            <span class="meta" id="sstPagInfo">—</span>
            <div class="mpl-pager" id="sstPager"></div>
        </div>
    </section>

</div><!-- /mpl -->
</div><!-- /container -->

<!-- ══════════════════ OFFCANVAS: History & Change Status ═══════════════════ -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="sstOffcanvas"
     style="width:430px;" aria-labelledby="sstOffcanvasLabel">

    <!-- Header -->
    <div class="offcanvas-header"
         style="background:linear-gradient(135deg,var(--reg-primary,#2c3e50),#3498db);color:#fff;padding:.9rem 1.15rem;">
        <div>
            <h5 class="offcanvas-title mb-0" id="sstOffcanvasLabel">Student Status</h5>
            <small id="ocStudentSub" style="opacity:.8;font-size:.78rem;"></small>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body p-0" style="overflow-y:auto;">

        <!-- Change Status form -->
        <div style="padding:1rem 1.15rem;border-bottom:1px solid #eee;background:#f9fafb;">
            <div class="fw-semibold mb-2" style="font-size:.85rem;color:var(--reg-primary,#2c3e50);">
                <i class="fas fa-exchange-alt me-1 text-primary"></i>Change Status
            </div>
            <form id="ocChangeForm" onsubmit="return sstSubmitChange(event)">
                <input type="hidden" id="ocStudentId">
                <div class="mb-2">
                    <label class="form-label" style="font-size:.8rem;font-weight:600;color:#546e7a;margin-bottom:3px;">
                        New Status <span class="text-danger">*</span>
                    </label>
                    <select id="ocNewStatus" class="form-select form-select-sm" required>
                        <option value="">— Select status —</option>
                        <?php foreach ($statuses as $st): ?>
                        <option value="<?php echo htmlspecialchars($st); ?>"><?php echo htmlspecialchars($st); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label" style="font-size:.8rem;font-weight:600;color:#546e7a;margin-bottom:3px;">
                        Reason <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="ocReason" class="form-control form-control-sm"
                           required maxlength="255" placeholder="e.g. Completed all requirements">
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-size:.8rem;font-weight:600;color:#546e7a;margin-bottom:3px;">
                        Additional Notes
                    </label>
                    <textarea id="ocNotes" class="form-control form-control-sm"
                              rows="2" maxlength="1000" placeholder="Optional remarks…"></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100 btn-sm" id="ocSubmitBtn">
                    <i class="fas fa-save me-1"></i> Apply Status Change
                </button>
            </form>
        </div>

        <!-- Timeline -->
        <div style="padding:1rem 1.15rem;">
            <div class="fw-semibold mb-3" style="font-size:.85rem;color:var(--reg-primary,#2c3e50);">
                <i class="fas fa-history me-1 text-info"></i>Status History
            </div>
            <div id="ocTimeline">
                <p class="text-muted text-center" style="font-size:.82rem;padding:.75rem 0;">
                    <i class="fas fa-spinner fa-spin text-primary"></i><br>Loading…
                </p>
            </div>
        </div>

    </div>
</div>

<style>
/* ── mpl-pager extension (pagination inside mpl-foot) ─────────────────────── */
.mpl-pager { display:flex; gap:.3rem; align-items:center; flex-wrap:wrap; }
.mpl-pager button {
    padding:3px 10px; border:1px solid #dee2e6; background:#fff;
    border-radius:5px; font-size:.8rem; cursor:pointer; transition:background .12s;
}
.mpl-pager button:hover { background:#f0f7ff; }
.mpl-pager button.active { background:var(--sms-primary,#2c3e50); color:#fff; border-color:var(--sms-primary,#2c3e50); }
.mpl-pager button:disabled { opacity:.4; cursor:not-allowed; }

/* mpl-foot layout */
.mpl-foot { display:flex; align-items:center; justify-content:space-between;
            padding:.55rem 1rem; background:#fafafa; border-top:1px solid var(--sms-border,#e0e0e0);
            border-radius:0 0 10px 10px; flex-wrap:wrap; gap:.5rem; }
.mpl-foot .meta { font-size:.8rem; color:var(--sms-text-muted,#78909c); }

/* Status badge pills reusing mpl-status tokens */
.oc-badge { display:inline-block; font-size:.72rem; font-weight:700; padding:2px 9px; border-radius:20px; }
.oc-badge-Active         { background:#d4edda; color:#155724; }
.oc-badge-Inactive       { background:#fff3cd; color:#856404; }
.oc-badge-Irregular      { background:#f3e5f5; color:#6a1b9a; }
.oc-badge-Graduated      { background:#d1ecf1; color:#0c5460; }
.oc-badge-LeaveofAbsence { background:#ffe5d0; color:#7d3502; }
.oc-badge-Transferred    { background:#e9ecef; color:#495057; }
.oc-badge-Dismissed      { background:#f8d7da; color:#721c24; }
.oc-badge-Dropout        { background:#f8d7da; color:#721c24; }

/* Timeline */
.oc-timeline { position:relative; padding-left:2rem; }
.oc-timeline::before {
    content:''; position:absolute; left:11px; top:0; bottom:0;
    width:2px; background:linear-gradient(to bottom,#3498db,#e0e0e0); border-radius:2px;
}
.oc-tl-item { position:relative; margin-bottom:1.15rem; }
.oc-tl-item:last-child { margin-bottom:0; }
.oc-tl-dot {
    position:absolute; left:-1.85rem; top:3px;
    width:22px; height:22px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:.65rem; border:2px solid #fff; box-shadow:0 1px 4px rgba(0,0,0,.15);
}
.oc-tl-dot.green  { background:#27ae60; color:#fff; }
.oc-tl-dot.amber  { background:#f39c12; color:#fff; }
.oc-tl-dot.purple { background:#9b59b6; color:#fff; }
.oc-tl-dot.teal   { background:#17a2b8; color:#fff; }
.oc-tl-dot.orange { background:#e67e22; color:#fff; }
.oc-tl-dot.gray   { background:#95a5a6; color:#fff; }
.oc-tl-dot.red    { background:#e74c3c; color:#fff; }
.oc-tl-card {
    background:#fafafa; border:1px solid #ececec;
    border-radius:8px; padding:.6rem .85rem;
}
.oc-tl-card .tl-arrow { font-size:.8rem; font-weight:700; }
.oc-tl-card .tl-reason { font-size:.77rem; color:#546e7a; margin-top:4px; }
.oc-tl-card .tl-meta   { font-size:.72rem; color:#90a4ae; margin-top:3px; }

/* Table row pointer */
.mpl-table tbody tr { cursor:pointer; }
</style>

<script>
const SST_API = '<?php echo BASE_URL; ?>/modules/registrar/api/status.php';
const CSRF    = '<?= e(csrfToken()) ?>';

// ── State ──────────────────────────────────────────────────────────────────
let sstPage  = 1;
let sstTotal = 0;
let sstPages = 1;
const LIMIT  = 25;
let sstDebounceT;

// ── Startup ────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    sstLoad();

    document.getElementById('sstSearch').addEventListener('input', function () {
        clearTimeout(sstDebounceT);
        sstDebounceT = setTimeout(() => { sstPage = 1; sstLoad(); }, 320);
    });
    document.getElementById('sstStatusFilter').addEventListener('change',  () => { sstPage = 1; sstLoad(); });
    document.getElementById('sstProgramFilter').addEventListener('change', () => { sstPage = 1; sstLoad(); });
});

// ── Load student list ──────────────────────────────────────────────────────
async function sstLoad() {
    const q       = document.getElementById('sstSearch').value.trim();
    const status  = document.getElementById('sstStatusFilter').value;
    const program = document.getElementById('sstProgramFilter').value;

    const params = new URLSearchParams({ action:'list', page:sstPage, limit:LIMIT, q, status, program });

    document.getElementById('sstTbody').innerHTML =
        '<tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--sms-text-muted)">' +
        '<i class="fas fa-spinner fa-spin text-primary"></i>' +
        '<p class="mt-2 mb-0">Loading…</p></td></tr>';

    try {
        const res  = await fetch(SST_API + '?' + params);
        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Load failed');

        sstTotal = data.pagination.total;
        sstPages = data.pagination.pages;

        _renderTable(data.data);
        _renderPager();
        _renderPagInfo();
    } catch (e) {
        document.getElementById('sstTbody').innerHTML =
            '<tr><td colspan="6" style="text-align:center;padding:2rem;color:#e74c3c;">' +
            '<i class="fas fa-exclamation-triangle"></i><p class="mt-2 mb-0">' + _esc(e.message) + '</p></td></tr>';
    }
}

// ── Render table rows ──────────────────────────────────────────────────────
function _renderTable(rows) {
    const tbody  = document.getElementById('sstTbody');
    const offset = (sstPage - 1) * LIMIT;

    if (!rows || rows.length === 0) {
        tbody.innerHTML =
            '<tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--sms-text-muted)">' +
            '<i class="fas fa-search-minus fa-2x"></i>' +
            '<p class="mt-2 mb-0">No students match the current filters.</p></td></tr>';
        return;
    }

    const statusClass = {
        'Active':'completed', 'Inactive':'cancelled', 'Irregular':'scheduled',
        'Graduated':'completed', 'Leave of Absence':'pending',
        'Transferred':'progress', 'Dismissed':'cancelled', 'Dropout':'cancelled',
    };

    let html = '';
    rows.forEach(function (r, i) {
        const pillCls = 'mpl-status ' + (statusClass[r.status] || 'pending');
        const since   = r.last_changed_at ? _dateShort(r.last_changed_at) : '—';
        const initials = ((r.first_name?.[0] ?? '') + (r.last_name?.[0] ?? '')).toUpperCase() || 'ST';
        const progYear = _esc(r.program_course || '—') + (r.year_section ? ' / ' + _esc(r.year_section) : '');

        html +=
            '<tr onclick="sstOpenPanel(' + r.id + ')" title="View history & change status">' +
            '<td style="font-size:.78rem;color:var(--sms-text-muted)">' + (offset + i + 1) + '</td>' +
            '<td><div class="mpl-person">' +
            '<span class="mpl-avatar">' + initials + '</span>' +
            '<div><strong>' + _esc(r.last_name + ', ' + r.first_name) + '</strong>' +
            '<small>' + _esc(r.student_number) + '</small></div>' +
            '</div></td>' +
            '<td>' + progYear + '</td>' +
            '<td><span class="' + pillCls + '">' + _esc(r.status) + '</span></td>' +
            '<td><small style="color:var(--sms-text-muted)">' + since + '</small></td>' +
            '<td><div class="mpl-actions">' +
            '<a href="javascript:void(0)" onclick="event.stopPropagation();sstOpenPanel(' + r.id + ')" title="History &amp; Change Status" aria-label="History">' +
            '<i class="fas fa-history"></i></a>' +
            '</div></td>' +
            '</tr>';
    });
    tbody.innerHTML = html;
}

// ── Pagination ─────────────────────────────────────────────────────────────
function _renderPager() {
    const el = document.getElementById('sstPager');
    if (sstPages <= 1) { el.innerHTML = ''; return; }

    let html = '<button onclick="sstGoPage(' + Math.max(1, sstPage - 1) + ')" ' + (sstPage <= 1 ? 'disabled' : '') + '>‹ Prev</button>';
    const start = Math.max(1, sstPage - 2);
    const end   = Math.min(sstPages, sstPage + 2);
    for (let p = start; p <= end; p++) {
        html += '<button onclick="sstGoPage(' + p + ')" class="' + (p === sstPage ? 'active' : '') + '">' + p + '</button>';
    }
    html += '<button onclick="sstGoPage(' + Math.min(sstPages, sstPage + 1) + ')" ' + (sstPage >= sstPages ? 'disabled' : '') + '>Next ›</button>';
    el.innerHTML = html;
}

function _renderPagInfo() {
    const offset = (sstPage - 1) * LIMIT;
    const end    = Math.min(offset + LIMIT, sstTotal);
    document.getElementById('sstPagInfo').textContent =
        sstTotal === 0 ? 'No results' : ('Showing ' + (offset + 1) + '–' + end + ' of ' + sstTotal + ' students');
}

function sstGoPage(p) { sstPage = p; sstLoad(); }

// ── Stat card click filter ─────────────────────────────────────────────────
function sstFilterStatus(st) {
    document.getElementById('sstStatusFilter').value = st;
    sstPage = 1;
    sstLoad();
}

// ── Open offcanvas with student history ────────────────────────────────────
async function sstOpenPanel(studentId) {
    document.getElementById('ocStudentId').value = studentId;
    document.getElementById('ocChangeForm').reset();
    document.getElementById('ocStudentId').value = studentId;

    document.getElementById('sstOffcanvasLabel').textContent = 'Student Status';
    document.getElementById('ocStudentSub').textContent = 'Loading…';
    document.getElementById('ocTimeline').innerHTML =
        '<p class="text-muted text-center" style="font-size:.82rem;padding:.75rem 0;">' +
        '<i class="fas fa-spinner fa-spin text-primary"></i><br>Loading history…</p>';

    new bootstrap.Offcanvas(document.getElementById('sstOffcanvas')).show();

    try {
        const res  = await fetch(SST_API + '?action=history&id=' + studentId);
        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Failed to load');

        const s = data.student;
        document.getElementById('sstOffcanvasLabel').textContent = s.last_name + ', ' + s.first_name;
        document.getElementById('ocStudentSub').textContent =
            s.student_number + ' · ' + (s.program_course || '') + (s.year_section ? ' / ' + s.year_section : '');

        // Mark current status in the dropdown.
        const sel = document.getElementById('ocNewStatus');
        Array.from(sel.options).forEach(o => {
            o.disabled = (o.value === s.status);
            o.textContent = o.value === s.status ? o.value + ' (current)' : o.value;
        });
        sel.value = '';

        _renderTimeline(data.history);
    } catch (e) {
        document.getElementById('ocTimeline').innerHTML =
            '<p class="text-danger" style="font-size:.82rem;">Error: ' + _esc(e.message) + '</p>';
    }
}

// ── Render timeline ────────────────────────────────────────────────────────
function _renderTimeline(history) {
    const el = document.getElementById('ocTimeline');
    if (!history || history.length === 0) {
        el.innerHTML = '<p class="text-muted text-center" style="font-size:.82rem;padding:.5rem 0;">No status changes recorded yet.</p>';
        return;
    }

    const dotColor = {
        'Active':'green', 'Inactive':'amber', 'Irregular':'purple', 'Graduated':'teal',
        'Leave of Absence':'orange', 'Transferred':'gray', 'Dismissed':'red', 'Dropout':'red',
    };
    const dotIcon = {
        'Active':'fa-check', 'Inactive':'fa-pause', 'Irregular':'fa-exclamation',
        'Graduated':'fa-graduation-cap', 'Leave of Absence':'fa-calendar-times',
        'Transferred':'fa-exchange-alt', 'Dismissed':'fa-ban', 'Dropout':'fa-user-slash',
    };

    let html = '<div class="oc-timeline">';
    history.forEach(function (h) {
        const col  = dotColor[h.changed_to]  || 'gray';
        const icon = dotIcon[h.changed_to]   || 'fa-circle';
        const by   = h.changed_by_name ? 'by ' + _esc(h.changed_by_name) : 'system';
        const fromKey = h.changed_from.replace(/\s/g, '');
        const toKey   = h.changed_to.replace(/\s/g, '');

        html +=
            '<div class="oc-tl-item">' +
            '<div class="oc-tl-dot ' + col + '"><i class="fas ' + icon + '" style="font-size:.55rem;"></i></div>' +
            '<div class="oc-tl-card">' +
            '<div class="tl-arrow">' +
            '<span class="oc-badge oc-badge-' + fromKey + '">' + _esc(h.changed_from) + '</span>' +
            ' <i class="fas fa-arrow-right" style="color:#90a4ae;font-size:.65rem;"></i> ' +
            '<span class="oc-badge oc-badge-' + toKey + '">' + _esc(h.changed_to) + '</span>' +
            '</div>' +
            (h.reason ? '<div class="tl-reason"><i class="fas fa-comment-alt me-1" style="font-size:.65rem;"></i>' + _esc(h.reason) + '</div>' : '') +
            (h.notes  ? '<div class="tl-reason text-muted">' + _esc(h.notes) + '</div>' : '') +
            '<div class="tl-meta">' + _esc(_dateShort(h.changed_at)) + ' · ' + by + '</div>' +
            '</div></div>';
    });
    html += '</div>';
    el.innerHTML = html;
}

// ── Submit status change ───────────────────────────────────────────────────
async function sstSubmitChange(e) {
    e.preventDefault();
    const btn       = document.getElementById('ocSubmitBtn');
    const studentId = parseInt(document.getElementById('ocStudentId').value);
    const newStatus = document.getElementById('ocNewStatus').value;
    const reason    = document.getElementById('ocReason').value.trim();
    const notes     = document.getElementById('ocNotes').value.trim();

    if (!newStatus || !reason) {
        sstAlert('danger', 'Please select a new status and provide a reason.');
        return false;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving…';

    try {
        const res  = await fetch(SST_API + '?action=change_status', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF },
            body:    JSON.stringify({ student_id: studentId, new_status: newStatus, reason, notes }),
        });
        const data = await res.json();

        if (!data.success) {
            sstAlert('danger', data.error || 'Failed to update status.');
        } else {
            bootstrap.Offcanvas.getInstance(document.getElementById('sstOffcanvas'))?.hide();
            sstAlert('success', data.message || 'Status updated successfully.');
            sstPage = 1;
            sstLoad();
            sstRefreshStats();
        }
    } catch (err) {
        sstAlert('danger', 'Error: ' + err.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i> Apply Status Change';
    }
    return false;
}

// ── Refresh stat card counts ───────────────────────────────────────────────
async function sstRefreshStats() {
    try {
        const res  = await fetch(SST_API + '?action=stats');
        const data = await res.json();
        if (!data.success) return;

        const idMap = {
            'all': null, // total is computed from sum
            'Active':'Active', 'Inactive':'Inactive', 'Irregular':'Irregular',
            'Graduated':'Graduated', 'Leave of Absence':'LeaveofAbsence',
            'Transferred':'Transferred', 'Dismissed':'Dismissed', 'Dropout':'Dropout',
        };

        let total = 0;
        Object.entries(data.stats).forEach(([k, v]) => { total += parseInt(v); });

        const allEl = document.getElementById('statCard_all');
        if (allEl) allEl.textContent = total;

        Object.entries(idMap).forEach(([statusKey, cardId]) => {
            if (!cardId) return;
            const el = document.getElementById('statCard_' + cardId);
            if (el) el.textContent = data.stats[statusKey] ?? 0;
        });
    } catch (_) {}
}

// ── Global alert banner ────────────────────────────────────────────────────
function sstAlert(type, msg) {
    const box = document.getElementById('sstAlertBox');
    box.innerHTML =
        '<div class="alert alert-' + type + ' alert-dismissible fade show mx-3 mt-2" role="alert">' +
        msg +
        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    window.scrollTo({ top: 0, behavior: 'smooth' });
    if (type === 'success') setTimeout(() => { const a = box.querySelector('.alert'); if (a) a.remove(); }, 4000);
}

// ── Utilities ──────────────────────────────────────────────────────────────
function _esc(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function _dateShort(dt) {
    if (!dt) return '—';
    const d = new Date(dt);
    return d.toLocaleDateString('en-PH', { month:'short', day:'numeric', year:'numeric' })
         + ', ' + d.toLocaleTimeString('en-PH', { hour:'2-digit', minute:'2-digit' });
}
</script>

<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
