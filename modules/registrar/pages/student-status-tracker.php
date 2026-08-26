<?php
/**
 * SMS 2 - Student Status Tracker
 * Module: Registrar
 *
 * Stat cards by status, searchable student list, and a slide-in timeline
 * panel showing the full change history for any student. Registrar staff
 * can update a student's status with a reason and notes — every change is
 * logged to reg_status_history.
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

$totalStudents  = array_sum($statsRaw);
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

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/modules/registrar/assets/css/registrar.css">
<link href="<?php echo BASE_URL; ?>/assets/css/module-process-list.css?v=2" rel="stylesheet">

<style>
/* ── Status Tracker specific styles ─────────────────────────────────────────*/

/* Stat cards */
.sst-stat {
  background: #fff;
  border-radius: 10px;
  padding: 1rem 1.25rem;
  box-shadow: 0 2px 8px rgba(0,0,0,.07);
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: transform .15s, box-shadow .15s;
  cursor: default;
}
.sst-stat:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,.1); }
.sst-stat-icon {
  width: 48px; height: 48px;
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.25rem;
  flex-shrink: 0;
}
.sst-stat-icon.green   { background: #d4edda; color: #155724; }
.sst-stat-icon.amber   { background: #fff3cd; color: #856404; }
.sst-stat-icon.blue    { background: #cce5ff; color: #004085; }
.sst-stat-icon.teal    { background: #d1ecf1; color: #0c5460; }
.sst-stat-icon.purple  { background: #f3e5f5; color: #6a1b9a; }
.sst-stat-icon.gray    { background: #e9ecef; color: #495057; }
.sst-stat-icon.red     { background: #f8d7da; color: #721c24; }
.sst-stat-icon.orange  { background: #ffe5d0; color: #7d3502; }
.sst-stat-count { font-size: 1.65rem; font-weight: 700; line-height: 1; color: #2c3e50; }
.sst-stat-label { font-size: .75rem; color: #78909c; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; margin-top: 2px; }

/* Student list table */
.sst-table { width: 100%; border-collapse: collapse; font-size: .875rem; }
.sst-table thead th {
  background: var(--reg-primary, #2c3e50);
  color: #fff;
  padding: .65rem .85rem;
  font-size: .78rem;
  text-transform: uppercase;
  letter-spacing: .4px;
  white-space: nowrap;
  position: sticky; top: 0; z-index: 1;
}
.sst-table tbody tr { transition: background .1s; cursor: pointer; }
.sst-table tbody tr:hover { background: #f0f8ff; }
.sst-table tbody tr.sst-active-row { background: #e8f4fd; }
.sst-table tbody td { padding: .5rem .85rem; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
.sst-table tbody tr:last-child td { border-bottom: none; }

/* Status badge pill */
.sst-badge {
  display: inline-block; font-size: .72rem; font-weight: 700;
  padding: 2px 9px; border-radius: 20px;
}
.sst-badge-Active         { background: #d4edda; color: #155724; }
.sst-badge-Inactive       { background: #fff3cd; color: #856404; }
.sst-badge-Irregular      { background: #f3e5f5; color: #6a1b9a; }
.sst-badge-Graduated      { background: #d1ecf1; color: #0c5460; }
.sst-badge-Leave          { background: #ffe5d0; color: #7d3502; }
.sst-badge-Transferred    { background: #e9ecef; color: #495057; }
.sst-badge-Dismissed      { background: #f8d7da; color: #721c24; }
.sst-badge-Dropout        { background: #f8d7da; color: #721c24; }

/* Timeline panel (right side) */
.sst-panel {
  display: none;
  position: sticky;
  top: 80px;
  max-height: calc(100vh - 120px);
  overflow-y: auto;
}
.sst-panel.visible { display: block; }
.sst-panel-header {
  background: linear-gradient(135deg, var(--reg-primary, #2c3e50), #3498db);
  color: #fff;
  padding: 1rem 1.1rem .9rem;
  border-radius: 10px 10px 0 0;
}
.sst-panel-header h5 { margin: 0; font-size: .95rem; font-weight: 700; }
.sst-panel-header small { opacity: .8; font-size: .78rem; }
.sst-panel-body { background: #fff; border: 1px solid #e0e0e0; border-top: none; border-radius: 0 0 10px 10px; padding: 1.1rem; }

/* Timeline */
.sst-timeline { position: relative; padding-left: 2rem; }
.sst-timeline::before {
  content: '';
  position: absolute; left: 11px; top: 0; bottom: 0;
  width: 2px; background: linear-gradient(to bottom, #3498db, #e0e0e0);
  border-radius: 2px;
}
.sst-tl-item { position: relative; margin-bottom: 1.25rem; }
.sst-tl-item:last-child { margin-bottom: 0; }
.sst-tl-dot {
  position: absolute;
  left: -1.85rem;
  top: 3px;
  width: 22px; height: 22px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: .7rem;
  border: 2px solid #fff;
  box-shadow: 0 1px 4px rgba(0,0,0,.15);
}
.sst-tl-dot.green   { background: #27ae60; color: #fff; }
.sst-tl-dot.amber   { background: #f39c12; color: #fff; }
.sst-tl-dot.blue    { background: #3498db; color: #fff; }
.sst-tl-dot.red     { background: #e74c3c; color: #fff; }
.sst-tl-dot.gray    { background: #95a5a6; color: #fff; }
.sst-tl-dot.purple  { background: #9b59b6; color: #fff; }
.sst-tl-card {
  background: #fafafa;
  border: 1px solid #ececec;
  border-radius: 8px;
  padding: .65rem .85rem;
}
.sst-tl-card .arrow {
  font-size: .8rem;
  font-weight: 700;
}
.sst-tl-card .reason { font-size: .78rem; color: #546e7a; margin-top: 4px; }
.sst-tl-card .meta   { font-size: .73rem; color: #90a4ae; margin-top: 3px; }

/* Change-status form */
.sst-change-form select, .sst-change-form input, .sst-change-form textarea {
  font-size: .875rem;
}
.sst-change-form label { font-size: .8rem; font-weight: 600; color: #546e7a; margin-bottom: 3px; }

/* Pagination */
.sst-pager { display: flex; gap: .35rem; align-items: center; flex-wrap: wrap; }
.sst-pager button {
  padding: 4px 10px;
  border: 1px solid #dee2e6;
  background: #fff;
  border-radius: 5px;
  font-size: .8rem;
  cursor: pointer;
  transition: background .12s;
}
.sst-pager button:hover { background: #f0f7ff; }
.sst-pager button.active { background: var(--reg-primary,#2c3e50); color: #fff; border-color: var(--reg-primary,#2c3e50); }
.sst-pager button:disabled { opacity: .4; cursor: not-allowed; }

/* Empty/loading */
.sst-empty { text-align: center; padding: 2.5rem 1rem; color: #90a4ae; }
.sst-empty i { font-size: 2rem; margin-bottom: .6rem; display: block; }

/* Toolbar */
.sst-toolbar { display: flex; gap: .5rem; align-items: center; flex-wrap: wrap; }
</style>

<?php renderBreadcrumbs($breadcrumbs); ?>

<div id="sstAlertBox"></div>

<div class="container-fluid py-4">

  <!-- Page Header -->
  <div class="d-flex justify-content-between align-items-start mb-4">
    <div>
      <h1 class="h3 text-dark mb-1">
        <i class="fas fa-user-check text-info me-2"></i>Student Status Tracker
      </h1>
      <p class="text-muted mb-0">Track and manage student enrollment status changes with full audit history</p>
    </div>
    <span class="badge bg-success text-white px-3 py-2 rounded-pill">
      <i class="fas fa-circle me-1" style="font-size:.55rem;vertical-align:middle;"></i> Live
    </span>
  </div>

  <!-- ── Stat Cards ─────────────────────────────────────────────────────── -->
  <?php
  $statConfig = [
    'Active'          => ['green',  'fa-check-circle'],
    'Inactive'        => ['amber',  'fa-pause-circle'],
    'Irregular'       => ['purple', 'fa-exclamation-circle'],
    'Graduated'       => ['teal',   'fa-graduation-cap'],
    'Leave of Absence'=> ['orange', 'fa-calendar-times'],
    'Transferred'     => ['gray',   'fa-exchange-alt'],
    'Dismissed'       => ['red',    'fa-ban'],
    'Dropout'         => ['red',    'fa-user-slash'],
  ];
  ?>
  <div class="row g-3 mb-4">
    <div class="col-6 col-sm-4 col-lg-2">
      <div class="sst-stat" onclick="sstFilterStatus('')" style="cursor:pointer;">
        <div class="sst-stat-icon blue"><i class="fas fa-users"></i></div>
        <div>
          <div class="sst-stat-count" id="statTotal"><?php echo $totalStudents; ?></div>
          <div class="sst-stat-label">All Students</div>
        </div>
      </div>
    </div>
    <?php foreach ($statConfig as $st => [$color, $icon]): ?>
    <div class="col-6 col-sm-4 col-lg-2">
      <div class="sst-stat" onclick="sstFilterStatus('<?php echo $st; ?>')" style="cursor:pointer;" title="Click to filter">
        <div class="sst-stat-icon <?php echo $color; ?>"><i class="fas <?php echo $icon; ?>"></i></div>
        <div>
          <div class="sst-stat-count" id="stat<?php echo preg_replace('/\W/', '', $st); ?>"><?php echo (int)($statsRaw[$st] ?? 0); ?></div>
          <div class="sst-stat-label"><?php echo htmlspecialchars($st); ?></div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- ── Main Layout: List + Timeline Panel ────────────────────────────── -->
  <div class="row g-4">

    <!-- Student list (left / full when no student selected) -->
    <div id="sstListCol" class="col-12 col-lg-12">
      <div class="card reg-shadow">

        <!-- Toolbar -->
        <div class="card-header" style="background:var(--reg-primary);padding:.75rem 1rem;">
          <div class="sst-toolbar">
            <span class="text-white fw-semibold me-auto">
              <i class="fas fa-list me-1"></i>Students
              <span id="sstCountBadge" class="badge bg-white text-dark ms-2" style="font-size:.75rem;">—</span>
            </span>
            <!-- Search -->
            <input type="search" id="sstSearch" class="form-control form-control-sm"
              style="max-width:220px;font-size:.82rem;" placeholder="Search name / number…">
            <!-- Status filter -->
            <select id="sstStatusFilter" class="form-select form-select-sm" style="max-width:170px;font-size:.82rem;">
              <option value="">All Statuses</option>
              <?php foreach ($statuses as $st): ?>
              <option value="<?php echo htmlspecialchars($st); ?>"><?php echo htmlspecialchars($st); ?></option>
              <?php endforeach; ?>
            </select>
            <!-- Program filter -->
            <select id="sstProgramFilter" class="form-select form-select-sm" style="max-width:200px;font-size:.82rem;">
              <option value="">All Programs</option>
              <?php foreach ($programs as $prog): ?>
              <option value="<?php echo htmlspecialchars($prog); ?>"><?php echo htmlspecialchars($prog); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Table -->
        <div style="max-height:60vh; overflow-y:auto;">
          <table class="sst-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Student</th>
                <th>Program / Year</th>
                <th>Current Status</th>
                <th>Last Changed</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="sstTbody">
              <tr><td colspan="6">
                <div class="sst-empty"><i class="fas fa-spinner fa-spin text-primary"></i><p class="mt-2 text-muted">Loading students…</p></div>
              </td></tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination footer -->
        <div class="card-footer d-flex align-items-center justify-content-between" style="padding:.55rem 1rem;background:#fafafa;">
          <small class="text-muted" id="sstPagInfo">—</small>
          <div class="sst-pager" id="sstPager"></div>
        </div>

      </div>
    </div><!-- /list col -->

  </div><!-- /row -->
</div><!-- /container -->

<!-- ═══════════════════ TIMELINE / CHANGE MODAL ════════════════════ -->
<!-- Student detail offcanvas panel -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="sstOffcanvas" style="width:420px;" aria-labelledby="sstOffcanvasLabel">
  <div class="offcanvas-header" style="background:linear-gradient(135deg,#2c3e50,#3498db);color:#fff;padding:.9rem 1.1rem;">
    <div>
      <h5 class="offcanvas-title mb-0" id="sstOffcanvasLabel">Student Status</h5>
      <small id="ocStudentSub" style="opacity:.8;font-size:.78rem;"></small>
    </div>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body p-0">

    <!-- Change Status form -->
    <div style="padding:1rem 1.1rem; border-bottom:1px solid #eee; background:#f9fafb;">
      <div class="fw-semibold mb-2" style="font-size:.85rem;color:#2c3e50;">
        <i class="fas fa-exchange-alt me-1 text-primary"></i>Change Status
      </div>
      <form id="ocChangeForm" class="sst-change-form" onsubmit="return sstSubmitChange(event)">
        <input type="hidden" id="ocStudentId">
        <div class="mb-2">
          <label>New Status</label>
          <select id="ocNewStatus" class="form-select form-select-sm" required>
            <option value="">— Select —</option>
            <?php foreach ($statuses as $st): ?>
            <option value="<?php echo htmlspecialchars($st); ?>"><?php echo htmlspecialchars($st); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-2">
          <label>Reason <span class="text-danger">*</span></label>
          <input type="text" id="ocReason" class="form-control form-control-sm" required maxlength="255"
            placeholder="e.g. Completed all requirements">
        </div>
        <div class="mb-2">
          <label>Additional Notes</label>
          <textarea id="ocNotes" class="form-control form-control-sm" rows="2" maxlength="1000"
            placeholder="Optional details…"></textarea>
        </div>
        <button type="submit" class="btn btn-sm btn-primary w-100" id="ocSubmitBtn">
          <i class="fas fa-save me-1"></i> Apply Status Change
        </button>
      </form>
    </div>

    <!-- Timeline -->
    <div style="padding:1rem 1.1rem;">
      <div class="fw-semibold mb-3" style="font-size:.85rem;color:#2c3e50;">
        <i class="fas fa-history me-1 text-info"></i>Status History
      </div>
      <div id="ocTimeline">
        <div class="sst-empty" style="padding:1.5rem 0;">
          <i class="fas fa-spinner fa-spin text-primary"></i>
          <p class="mt-2 text-muted" style="font-size:.82rem;">Loading history…</p>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
const SST_API  = '<?php echo BASE_URL; ?>/modules/registrar/api/status.php';
const CSRF     = '<?= e(csrfToken()) ?>';

// ── State ─────────────────────────────────────────────────────────────────────
let sstPage    = 1;
let sstTotal   = 0;
let sstPages   = 1;
const LIMIT    = 25;
let sstDebounceT;

// ── Startup ───────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
  sstLoad();

  // Live search debounce
  document.getElementById('sstSearch').addEventListener('input', function () {
    clearTimeout(sstDebounceT);
    sstDebounceT = setTimeout(() => { sstPage = 1; sstLoad(); }, 320);
  });

  // Dropdown filters — immediate
  document.getElementById('sstStatusFilter').addEventListener('change',  function () { sstPage = 1; sstLoad(); });
  document.getElementById('sstProgramFilter').addEventListener('change', function () { sstPage = 1; sstLoad(); });
});

// ── Load student list ─────────────────────────────────────────────────────────
async function sstLoad() {
  const q       = document.getElementById('sstSearch').value.trim();
  const status  = document.getElementById('sstStatusFilter').value;
  const program = document.getElementById('sstProgramFilter').value;

  const params = new URLSearchParams({
    action: 'list',
    page:   sstPage,
    limit:  LIMIT,
    q, status, program,
  });

  document.getElementById('sstTbody').innerHTML =
    '<tr><td colspan="6"><div class="sst-empty"><i class="fas fa-spinner fa-spin text-primary"></i><p class="mt-2 text-muted">Loading…</p></div></td></tr>';

  try {
    const res  = await fetch(SST_API + '?' + params);
    const data = await res.json();
    if (!data.success) throw new Error(data.error || 'Load failed');

    sstTotal = data.pagination.total;
    sstPages = data.pagination.pages;

    document.getElementById('sstCountBadge').textContent = sstTotal + ' student' + (sstTotal !== 1 ? 's' : '');
    _renderTable(data.data);
    _renderPager();
    _renderPagInfo();
  } catch (e) {
    document.getElementById('sstTbody').innerHTML =
      '<tr><td colspan="6"><div class="sst-empty text-danger"><i class="fas fa-exclamation-triangle"></i><p>' + _esc(e.message) + '</p></div></td></tr>';
  }
}

function _renderTable(rows) {
  const tbody = document.getElementById('sstTbody');
  const offset = (sstPage - 1) * LIMIT;

  if (!rows || rows.length === 0) {
    tbody.innerHTML = '<tr><td colspan="6"><div class="sst-empty"><i class="fas fa-search-minus text-warning"></i><p class="mb-0">No students match the current filters.</p></div></td></tr>';
    return;
  }

  const statusDotColor = {
    'Active':'green','Inactive':'amber','Irregular':'purple','Graduated':'teal',
    'Leave of Absence':'orange','Transferred':'gray','Dismissed':'red','Dropout':'red',
  };

  let html = '';
  rows.forEach(function (r, i) {
    const badgeCls = 'sst-badge sst-badge-' + (r.status.replace(/\s/g, '') || 'gray');
    const since    = r.last_changed_at ? _dateShort(r.last_changed_at) : '—';
    html += '<tr onclick="sstOpenPanel(' + r.id + ')" title="View history &amp; change status">'
          + '<td class="text-muted" style="font-size:.78rem;">' + (offset + i + 1) + '</td>'
          + '<td><div style="display:flex;align-items:center;gap:.5rem;">'
          + '<span style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#2c3e50,#3498db);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.7rem;font-weight:700;flex-shrink:0;">' + _initials(r.first_name, r.last_name) + '</span>'
          + '<div><strong>' + _esc(r.last_name + ', ' + r.first_name) + '</strong>'
          + '<br><small style="font-family:monospace;font-size:.75rem;color:#546e7a;">' + _esc(r.student_number) + '</small></div></div></td>'
          + '<td><small>' + _esc(r.program_course || '—') + (r.year_section ? ' / ' + r.year_section : '') + '</small></td>'
          + '<td><span class="' + badgeCls + '">' + _esc(r.status) + '</span></td>'
          + '<td><small class="text-muted">' + since + '</small></td>'
          + '<td><button class="btn btn-sm btn-outline-primary" style="font-size:.75rem;padding:2px 8px;" onclick="event.stopPropagation();sstOpenPanel(' + r.id + ')">'
          + '<i class="fas fa-history me-1"></i>History</button></td>'
          + '</tr>';
  });
  tbody.innerHTML = html;
}

// ── Pagination ────────────────────────────────────────────────────────────────
function _renderPager() {
  const el = document.getElementById('sstPager');
  if (sstPages <= 1) { el.innerHTML = ''; return; }

  let html = '';
  html += '<button onclick="sstGoPage(' + Math.max(1, sstPage - 1) + ')" ' + (sstPage <= 1 ? 'disabled' : '') + '>‹ Prev</button>';

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
    sstTotal === 0 ? 'No results' : ('Showing ' + (offset + 1) + '–' + end + ' of ' + sstTotal);
}

function sstGoPage(p) { sstPage = p; sstLoad(); }

// ── Status card click filter ──────────────────────────────────────────────────
function sstFilterStatus(st) {
  document.getElementById('sstStatusFilter').value = st;
  sstPage = 1;
  sstLoad();
}

// ── Offcanvas panel: load history ─────────────────────────────────────────────
async function sstOpenPanel(studentId) {
  document.getElementById('ocStudentId').value = studentId;
  document.getElementById('ocChangeForm').reset();
  document.getElementById('ocStudentId').value = studentId;

  document.getElementById('sstOffcanvasLabel').textContent = 'Student Status';
  document.getElementById('ocStudentSub').textContent = 'Loading…';
  document.getElementById('ocTimeline').innerHTML =
    '<div class="sst-empty" style="padding:1.5rem 0;"><i class="fas fa-spinner fa-spin text-primary"></i><p class="mt-2 text-muted" style="font-size:.82rem;">Loading history…</p></div>';

  // Pre-set the current status as the "from" label in the select (disable that option).
  new bootstrap.Offcanvas(document.getElementById('sstOffcanvas')).show();

  try {
    const res  = await fetch(SST_API + '?action=history&id=' + studentId);
    const data = await res.json();
    if (!data.success) throw new Error(data.error || 'Failed to load');

    const s = data.student;
    document.getElementById('sstOffcanvasLabel').textContent = s.last_name + ', ' + s.first_name;
    document.getElementById('ocStudentSub').textContent = s.student_number + ' · ' + (s.program_course || '') + (s.year_section ? ' / ' + s.year_section : '');

    // Pre-select current status in the "new status" dropdown — highlight it.
    const sel = document.getElementById('ocNewStatus');
    Array.from(sel.options).forEach(o => {
      o.disabled = (o.value === s.status);
      if (o.value === s.status) o.textContent = o.value + ' (current)';
      else o.textContent = o.value;
    });
    sel.value = '';

    _renderTimeline(data.history);
  } catch (e) {
    document.getElementById('ocTimeline').innerHTML =
      '<p class="text-danger" style="font-size:.82rem;">Error: ' + _esc(e.message) + '</p>';
  }
}

function _renderTimeline(history) {
  const el = document.getElementById('ocTimeline');
  if (!history || history.length === 0) {
    el.innerHTML = '<p class="text-muted" style="font-size:.82rem;text-align:center;padding:.5rem 0;">No status changes recorded yet.</p>';
    return;
  }

  const dotColor = {
    'Active':'green','Inactive':'amber','Irregular':'purple','Graduated':'teal',
    'Leave of Absence':'orange','Transferred':'gray','Dismissed':'red','Dropout':'red',
  };
  const dotIcon = {
    'Active':'fa-check','Inactive':'fa-pause','Irregular':'fa-exclamation',
    'Graduated':'fa-graduation-cap','Leave of Absence':'fa-calendar-times',
    'Transferred':'fa-exchange-alt','Dismissed':'fa-ban','Dropout':'fa-user-slash',
  };

  let html = '<div class="sst-timeline">';
  history.forEach(function (h) {
    const col  = dotColor[h.changed_to] || 'gray';
    const icon = dotIcon[h.changed_to]  || 'fa-circle';
    const by   = h.changed_by_name ? 'by ' + _esc(h.changed_by_name) : 'system';
    html += '<div class="sst-tl-item">'
          + '<div class="sst-tl-dot ' + col + '"><i class="fas ' + icon + '" style="font-size:.55rem;"></i></div>'
          + '<div class="sst-tl-card">'
          + '<div class="arrow">'
          + '<span class="sst-badge sst-badge-' + h.changed_from.replace(/\s/g,'') + '" style="font-size:.68rem;">' + _esc(h.changed_from) + '</span>'
          + ' <i class="fas fa-arrow-right" style="color:#90a4ae;font-size:.65rem;"></i> '
          + '<span class="sst-badge sst-badge-' + h.changed_to.replace(/\s/g,'') + '" style="font-size:.68rem;">' + _esc(h.changed_to) + '</span>'
          + '</div>'
          + (h.reason ? '<div class="reason"><i class="fas fa-comment-alt" style="font-size:.65rem;margin-right:3px;"></i>' + _esc(h.reason) + '</div>' : '')
          + (h.notes  ? '<div class="reason text-muted">' + _esc(h.notes) + '</div>' : '')
          + '<div class="meta">' + _esc(_dateShort(h.changed_at)) + ' · ' + by + '</div>'
          + '</div></div>';
  });
  html += '</div>';
  el.innerHTML = html;
}

// ── Submit status change ──────────────────────────────────────────────────────
async function sstSubmitChange(e) {
  e.preventDefault();
  const btn      = document.getElementById('ocSubmitBtn');
  const studentId = parseInt(document.getElementById('ocStudentId').value);
  const newStatus = document.getElementById('ocNewStatus').value;
  const reason    = document.getElementById('ocReason').value.trim();
  const notes     = document.getElementById('ocNotes').value.trim();

  if (!newStatus || !reason) { sstAlert('danger', 'Please select a new status and provide a reason.'); return false; }

  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving…';

  try {
    const res = await fetch(SST_API + '?action=change_status', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF },
      body: JSON.stringify({ student_id: studentId, new_status: newStatus, reason, notes }),
    });
    const data = await res.json();

    if (!data.success) {
      sstAlert('danger', data.error || 'Failed to update status.');
    } else {
      // Close offcanvas, show global success, reload.
      bootstrap.Offcanvas.getInstance(document.getElementById('sstOffcanvas'))?.hide();
      sstAlert('success', data.message || 'Status updated successfully.');
      sstPage = 1;
      sstLoad();
      // Refresh stat cards.
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

// ── Refresh stat counts after a change ───────────────────────────────────────
async function sstRefreshStats() {
  try {
    const res  = await fetch(SST_API + '?action=stats');
    const data = await res.json();
    if (!data.success) return;

    document.getElementById('statTotal').textContent = data.total;
    const map = {
      'Active':'Active','Inactive':'Inactive','Irregular':'Irregular','Graduated':'Graduated',
      'Leave of Absence':'LeaveofAbsence','Transferred':'Transferred','Dismissed':'Dismissed','Dropout':'Dropout',
    };
    Object.entries(map).forEach(([k, id]) => {
      const el = document.getElementById('stat' + id);
      if (el) el.textContent = data.stats[k] ?? 0;
    });
  } catch (_) {}
}

// ── Global alert ─────────────────────────────────────────────────────────────
function sstAlert(type, msg) {
  const box = document.getElementById('sstAlertBox');
  box.innerHTML = '<div class="alert alert-' + type + ' alert-dismissible fade show mx-3 mt-2" role="alert">'
    + msg + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
  window.scrollTo({ top: 0, behavior: 'smooth' });
  if (type === 'success') setTimeout(() => { const a = box.querySelector('.alert'); if (a) a.remove(); }, 4000);
}

// ── Utilities ─────────────────────────────────────────────────────────────────
function _esc(str) {
  if (!str) return '';
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function _initials(fn, ln) {
  return ((fn?.[0] ?? '') + (ln?.[0] ?? '')).toUpperCase() || 'ST';
}
function _dateShort(dt) {
  if (!dt) return '—';
  const d = new Date(dt);
  return d.toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' })
       + ', ' + d.toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit' });
}
</script>

<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
