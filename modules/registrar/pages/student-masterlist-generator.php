<?php
/**
 * SMS 2 - Student Masterlist Generator
 * Module: Registrar
 *
 * Live filter → preview table → CSV export / PDF print view.
 * Enrollment data is pulled cross-module from the Scheduling section-assignment
 * tables via schGetEnrollmentMap() (scheduling-service.php).
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

// Last 5 export log entries (shown in the history card).
// The table is created lazily by the API, so guard against it not existing yet.
$exportHistory = [];
try {
    $exportHistory = db()->query(
        "SELECT * FROM `reg_masterlist_exports` ORDER BY `created_at` DESC LIMIT 5"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable) {
    // Table not yet created — history will be empty on first load.
}

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/modules/registrar/assets/css/registrar.css">
<link href="<?php echo BASE_URL; ?>/assets/css/module-process-list.css?v=2" rel="stylesheet">

<style>
/* ── Masterlist Generator specific styles ─────────────────────────────────── */
.ml-filter-card {
  position: sticky;
  top: 80px;
}
.ml-filter-card .card-header {
  background: linear-gradient(135deg, var(--reg-primary), var(--reg-secondary));
  color: #fff;
  font-weight: 600;
  padding: .75rem 1rem;
  border-radius: 8px 8px 0 0;
}
.ml-filter-card .card-body {
  padding: 1.1rem;
  background: #fff;
}
.ml-filter-card label {
  font-size: .8rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: .4px;
  color: #546e7a;
  margin-bottom: 3px;
}
.ml-filter-card select,
.ml-filter-card input[type=text] {
  font-size: .875rem;
}
.ml-filter-card .btn-generate {
  background: linear-gradient(135deg, var(--reg-secondary), #2980b9);
  border: none;
  color: #fff;
  font-weight: 600;
  letter-spacing: .3px;
  transition: transform .15s, box-shadow .15s;
}
.ml-filter-card .btn-generate:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(52,152,219,.4);
  color: #fff;
}
.ml-filter-card .btn-clear {
  border-color: var(--reg-border);
  color: #546e7a;
  font-size: .85rem;
}

/* Summary bar */
.ml-summary {
  display: flex;
  gap: .5rem;
  flex-wrap: wrap;
  align-items: center;
  padding: .55rem 1rem;
  background: linear-gradient(90deg,#f0f8ff,#e8f5e9);
  border-bottom: 1px solid #dee2e6;
  font-size: .82rem;
}
.ml-summary .badge-stat {
  background: #fff;
  border: 1px solid #dee2e6;
  border-radius: 20px;
  padding: 3px 10px;
  font-weight: 600;
  font-size: .78rem;
  color: #2c3e50;
}
.ml-summary .badge-stat span { color: var(--reg-secondary); }

/* Preview table */
.ml-table-wrap { overflow-x: auto; }
.ml-table { width: 100%; border-collapse: collapse; font-size: .875rem; }
.ml-table thead th {
  background: var(--reg-primary);
  color: #fff;
  padding: .65rem .75rem;
  font-size: .78rem;
  text-transform: uppercase;
  letter-spacing: .4px;
  white-space: nowrap;
  position: sticky;
  top: 0;
  z-index: 1;
}
.ml-table tbody tr { transition: background .12s; }
.ml-table tbody tr:hover { background: #f0f7ff; }
.ml-table tbody td {
  padding: .55rem .75rem;
  border-bottom: 1px solid #f0f0f0;
  vertical-align: middle;
}
.ml-table tbody tr:last-child td { border-bottom: none; }
.ml-num { color: #90a4ae; font-size: .78rem; }
.ml-sno { font-family: monospace; font-size: .8rem; background: #e8f4fd; border-radius: 4px; padding: 2px 6px; }

/* Status badges */
.ml-badge {
  display: inline-block;
  font-size: .73rem;
  font-weight: 700;
  padding: 2px 9px;
  border-radius: 20px;
}
.ml-badge-active    { background: #d4edda; color: #155724; }
.ml-badge-inactive  { background: #fff3cd; color: #856404; }
.ml-badge-graduated { background: #d1ecf1; color: #0c5460; }
.ml-badge-irregular { background: #f3e5f5; color: #6a1b9a; }
.ml-badge-enrolled-yes { background: #d4edda; color: #155724; }
.ml-badge-enrolled-no  { background: #f8d7da; color: #721c24; }
.ml-badge-enrolled-na  { background: #f5f5f5; color: #9e9e9e; }

/* Export toolbar */
.ml-export-bar {
  display: flex;
  gap: .5rem;
  align-items: center;
  padding: .6rem 1rem;
  border-top: 1px solid #f0f0f0;
  background: #fafafa;
  border-radius: 0 0 8px 8px;
}

/* Empty / loading states */
.ml-empty {
  text-align: center;
  padding: 3rem 1.5rem;
  color: #90a4ae;
}
.ml-empty i { font-size: 2.5rem; margin-bottom: .75rem; display: block; }

/* History card */
.ml-history-item {
  display: flex;
  align-items: center;
  gap: .75rem;
  padding: .55rem 0;
  border-bottom: 1px solid #f0f0f0;
  font-size: .82rem;
}
.ml-history-item:last-child { border-bottom: none; }
.ml-history-icon {
  width: 32px; height: 32px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: .85rem;
  flex-shrink: 0;
}
.ml-history-icon.csv { background: #e8f5e9; color: #388e3c; }
.ml-history-icon.pdf { background: #fce4ec; color: #c62828; }
</style>

<?php renderBreadcrumbs($breadcrumbs); ?>

<div class="container-fluid py-4">

  <!-- Page Header -->
  <div class="d-flex justify-content-between align-items-start mb-4">
    <div>
      <h1 class="h3 text-dark mb-1">
        <i class="fas fa-list-alt text-primary me-2"></i>Student Masterlist Generator
      </h1>
      <p class="text-muted mb-0">Filter, preview, and export student masterlists linked with scheduling section data</p>
    </div>
    <div class="d-flex gap-2">
      <span id="mlLiveIndicator" class="badge bg-secondary px-3 py-2 rounded-pill" style="display:none!important;">
        <i class="fas fa-spinner fa-spin me-1"></i> Loading…
      </span>
    </div>
  </div>

  <div class="row g-4">

    <!-- ═══════════════════ FILTER PANEL ═══════════════════ -->
    <div class="col-lg-3 col-md-4">
      <div class="card reg-shadow ml-filter-card">
        <div class="card-header">
          <i class="fas fa-filter me-2"></i>Filter Options
        </div>
        <div class="card-body">

          <div class="mb-3">
            <label for="mlSchoolYear">School Year</label>
            <select id="mlSchoolYear" class="form-select form-select-sm">
              <option value="">— All School Years —</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="mlSemester">Semester</label>
            <select id="mlSemester" class="form-select form-select-sm">
              <option value="">— All Semesters —</option>
              <option>1st Semester</option>
              <option>2nd Semester</option>
              <option>Summer</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="mlProgram">Program / Course</label>
            <select id="mlProgram" class="form-select form-select-sm">
              <option value="">— All Programs —</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="mlYearSection">Year Level / Section</label>
            <select id="mlYearSection" class="form-select form-select-sm">
              <option value="">— All Year Levels —</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="mlStatus">Student Status</label>
            <select id="mlStatus" class="form-select form-select-sm">
              <option value="">— All Statuses —</option>
              <option>Active</option>
              <option>Inactive</option>
              <option>Irregular</option>
              <option>Graduated</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="mlSection">
              Scheduling Section
              <i class="fas fa-info-circle text-muted ms-1" title="Filter by a specific class section from the Section Assignment Tool"></i>
            </label>
            <select id="mlSection" class="form-select form-select-sm">
              <option value="">— All Sections —</option>
            </select>
            <div class="form-text" id="mlSectionHint" style="display:none;">
              Select a school year to filter sections.
            </div>
          </div>

          <div class="d-flex gap-2 mt-3">
            <button id="btnGenerate" class="btn btn-generate flex-grow-1" onclick="mlGenerate()">
              <i class="fas fa-search me-1"></i> Generate
            </button>
            <button class="btn btn-clear btn-outline-secondary" onclick="mlClear()">
              <i class="fas fa-times"></i>
            </button>
          </div>

        </div>
      </div>
    </div><!-- /filter panel -->

    <!-- ═══════════════════ PREVIEW TABLE ═══════════════════ -->
    <div class="col-lg-9 col-md-8">
      <div class="card reg-shadow" id="mlPreviewCard">

        <!-- Card header -->
        <div class="card-header d-flex justify-content-between align-items-center" style="background:var(--reg-primary);">
          <span class="text-white fw-semibold">
            <i class="fas fa-table me-2"></i>Masterlist Preview
          </span>
          <span id="mlResultCount" class="badge bg-white text-dark" style="display:none;">0 students</span>
        </div>

        <!-- Summary bar (hidden until generate) -->
        <div class="ml-summary" id="mlSummaryBar" style="display:none;">
          <i class="fas fa-chart-bar text-primary me-1"></i>
          <span class="badge-stat">Total <span id="sumTotal">0</span></span>
          <span class="badge-stat" id="sumEnrolledWrap">Enrolled <span id="sumEnrolled">0</span></span>
          <span class="badge-stat" id="sumUnassignedWrap">Not Enrolled <span id="sumUnassigned">0</span></span>
          <span class="ms-auto text-muted" id="sumNote" style="font-size:.75rem;"></span>
        </div>

        <!-- Table body -->
        <div class="ml-table-wrap" style="max-height:62vh; overflow-y:auto;">
          <table class="ml-table" id="mlTable">
            <thead>
              <tr>
                <th>#</th>
                <th>Student No.</th>
                <th>Full Name</th>
                <th>Program / Course</th>
                <th>Year &amp; Section</th>
                <th>Sched. Section</th>
                <th>Gender</th>
                <th>Status</th>
                <th>Enrolled</th>
              </tr>
            </thead>
            <tbody id="mlTbody">
              <tr>
                <td colspan="9">
                  <div class="ml-empty">
                    <i class="fas fa-filter text-primary"></i>
                    <p class="mb-1 fw-semibold text-dark">Set your filters and click <strong>Generate</strong></p>
                    <small>Program, year level, status, school year, and scheduling section filters are available.</small>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Export toolbar -->
        <div class="ml-export-bar" id="mlExportBar" style="display:none;">
          <span class="text-muted me-auto" style="font-size:.8rem;">
            <i class="fas fa-download me-1"></i> Export current result:
          </span>
          <button class="btn btn-sm btn-success" onclick="mlExportCsv()" id="btnCsv">
            <i class="fas fa-file-csv me-1"></i> Export CSV
          </button>
          <button class="btn btn-sm btn-danger" onclick="mlPrintPdf()" id="btnPdf">
            <i class="fas fa-print me-1"></i> Print / PDF
          </button>
        </div>

      </div><!-- /card -->

      <!-- ── Export History ── -->
      <div class="card reg-shadow mt-4">
        <div class="card-header" style="background:var(--reg-primary);">
          <span class="text-white fw-semibold">
            <i class="fas fa-history me-2"></i>Recent Exports
          </span>
        </div>
        <div class="card-body p-3" id="mlHistoryBody">
          <?php if (empty($exportHistory)): ?>
          <p class="text-muted mb-0 text-center py-2" style="font-size:.85rem;">
            <i class="fas fa-inbox me-1"></i> No exports yet. Generate and export a masterlist to see history here.
          </p>
          <?php else: ?>
          <?php foreach ($exportHistory as $h):
            $filters = json_decode($h['filters'], true) ?? [];
            $label   = implode(', ', array_filter([
                $filters['school_year'] ?? '',
                $filters['semester'] ?? '',
                $filters['program'] ?? '',
                $filters['status'] ?? '',
            ]));
            $label = $label ?: 'All students';
            $iconClass = $h['export_type'] === 'pdf' ? 'pdf' : 'csv';
            $iconFa    = $h['export_type'] === 'pdf' ? 'fa-file-pdf' : 'fa-file-csv';
          ?>
          <div class="ml-history-item">
            <div class="ml-history-icon <?php echo $iconClass; ?>">
              <i class="fas <?php echo $iconFa; ?>"></i>
            </div>
            <div class="flex-grow-1">
              <div class="fw-semibold" style="font-size:.82rem;"><?php echo htmlspecialchars($label); ?></div>
              <small class="text-muted"><?php echo (int)$h['student_count']; ?> students &middot; <?php echo strtoupper(htmlspecialchars($h['export_type'])); ?></small>
            </div>
            <small class="text-muted"><?php echo date('M j, g:i A', strtotime($h['created_at'])); ?></small>
          </div>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

    </div><!-- /main col -->
  </div><!-- /row -->
</div><!-- /container -->

<script>
const ML_API = '<?php echo BASE_URL; ?>/modules/registrar/api/masterlist.php';
const CSRF   = '<?= e(csrfToken()) ?>';

// ── On load: populate filter dropdowns ───────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
  mlLoadFilterOpts('', '');

  // Re-populate sections when school year or semester changes.
  document.getElementById('mlSchoolYear').addEventListener('change', function () {
    mlLoadSections(this.value, document.getElementById('mlSemester').value);
  });
  document.getElementById('mlSemester').addEventListener('change', function () {
    mlLoadSections(document.getElementById('mlSchoolYear').value, this.value);
  });
});

async function mlLoadFilterOpts(sy, sem) {
  try {
    const url = ML_API + '?action=filter_opts&school_year=' + encodeURIComponent(sy) + '&semester=' + encodeURIComponent(sem);
    const res  = await fetch(url);
    const data = await res.json();
    if (!data.success) return;

    _populateSelect('mlSchoolYear', data.school_years, '— All School Years —');
    _populateSelect('mlProgram',    data.programs,     '— All Programs —');
    _populateSelect('mlYearSection',data.year_sections,'— All Year Levels —');
    _populateSections(data.sections);
  } catch (e) {
    console.warn('Filter opts error:', e);
  }
}

async function mlLoadSections(sy, sem) {
  const hint = document.getElementById('mlSectionHint');
  if (!sy) {
    hint.style.display = '';
    document.getElementById('mlSection').innerHTML = '<option value="">— All Sections —</option>';
    return;
  }
  hint.style.display = 'none';
  try {
    const url = ML_API + '?action=filter_opts&school_year=' + encodeURIComponent(sy) + '&semester=' + encodeURIComponent(sem);
    const res  = await fetch(url);
    const data = await res.json();
    if (!data.success) return;
    _populateSections(data.sections);
  } catch (e) {}
}

function _populateSelect(id, items, placeholder) {
  const el = document.getElementById(id);
  const cur = el.value;
  el.innerHTML = '<option value="">' + placeholder + '</option>';
  (items || []).forEach(function (v) {
    const opt = document.createElement('option');
    opt.value = v;
    opt.textContent = v;
    if (v === cur) opt.selected = true;
    el.appendChild(opt);
  });
}

function _populateSections(sections) {
  const el = document.getElementById('mlSection');
  const cur = el.value;
  el.innerHTML = '<option value="">— All Sections —</option>';
  (sections || []).forEach(function (sec) {
    const opt = document.createElement('option');
    opt.value = sec.id;
    opt.textContent = sec.program_course + ' · ' + sec.year_section + ' · ' + sec.school_year;
    if (String(sec.id) === String(cur)) opt.selected = true;
    el.appendChild(opt);
  });
}

// ── Build query-string from current filter values ────────────────────────────
function mlParams() {
  return new URLSearchParams({
    school_year:  document.getElementById('mlSchoolYear').value,
    semester:     document.getElementById('mlSemester').value,
    program:      document.getElementById('mlProgram').value,
    year_section: document.getElementById('mlYearSection').value,
    status:       document.getElementById('mlStatus').value,
    section_id:   document.getElementById('mlSection').value,
  }).toString();
}

// ── Generate (fetch preview) ─────────────────────────────────────────────────
async function mlGenerate() {
  const btn = document.getElementById('btnGenerate');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Loading…';

  const tbody = document.getElementById('mlTbody');
  tbody.innerHTML = '<tr><td colspan="9"><div class="ml-empty"><i class="fas fa-spinner fa-spin text-primary"></i><p class="mt-2 text-muted">Fetching students…</p></div></td></tr>';

  document.getElementById('mlSummaryBar').style.display = 'none';
  document.getElementById('mlExportBar').style.display  = 'none';
  document.getElementById('mlResultCount').style.display = 'none';

  try {
    const res  = await fetch(ML_API + '?action=fetch&' + mlParams());
    const data = await res.json();

    if (!data.success) {
      tbody.innerHTML = '<tr><td colspan="9"><div class="ml-empty text-danger"><i class="fas fa-exclamation-triangle"></i><p>' + (data.error || 'Failed to load data.') + '</p></div></td></tr>';
      return;
    }

    _renderTable(data.data);
    _renderSummary(data.total, data.enrolled, document.getElementById('mlSchoolYear').value);

    document.getElementById('mlResultCount').textContent = data.total + ' student' + (data.total !== 1 ? 's' : '');
    document.getElementById('mlResultCount').style.display = '';
    document.getElementById('mlExportBar').style.display   = data.total > 0 ? '' : 'none';

  } catch (e) {
    tbody.innerHTML = '<tr><td colspan="9"><div class="ml-empty text-danger"><i class="fas fa-exclamation-triangle"></i><p>Error: ' + e.message + '</p></div></td></tr>';
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-search me-1"></i> Generate';
  }
}

function _renderTable(rows) {
  const tbody = document.getElementById('mlTbody');
  if (!rows || rows.length === 0) {
    tbody.innerHTML = '<tr><td colspan="9"><div class="ml-empty"><i class="fas fa-search-minus text-warning"></i><p class="mb-0">No students match the selected filters.</p></div></td></tr>';
    return;
  }

  const statusClass = { Active:'ml-badge-active', Inactive:'ml-badge-inactive', Graduated:'ml-badge-graduated', Irregular:'ml-badge-irregular' };
  const hasSY = document.getElementById('mlSchoolYear').value !== '';

  let html = '';
  rows.forEach(function (r, i) {
    const sc   = statusClass[r.status] || '';
    let enrolled = '';
    if (hasSY) {
      enrolled = r.enrolled
        ? '<span class="ml-badge ml-badge-enrolled-yes">Yes</span>'
        : '<span class="ml-badge ml-badge-enrolled-no">No</span>';
    } else {
      enrolled = '<span class="ml-badge ml-badge-enrolled-na">—</span>';
    }
    html += '<tr>';
    html += '<td class="ml-num">' + (i + 1) + '</td>';
    html += '<td><span class="ml-sno">' + _esc(r.student_number) + '</span></td>';
    html += '<td><strong>' + _esc(r.full_name) + '</strong></td>';
    html += '<td>' + _esc(r.program_course || '—') + '</td>';
    html += '<td>' + _esc(r.year_section || '—') + '</td>';
    html += '<td>' + (r.section_assigned ? '<span class="badge bg-info text-dark">' + _esc(r.section_assigned) + '</span>' : '<span class="text-muted">—</span>') + '</td>';
    html += '<td>' + _esc(r.gender || '—') + '</td>';
    html += '<td><span class="ml-badge ' + sc + '">' + _esc(r.status) + '</span></td>';
    html += '<td>' + enrolled + '</td>';
    html += '</tr>';
  });
  tbody.innerHTML = html;
}

function _renderSummary(total, enrolled, schoolYear) {
  document.getElementById('sumTotal').textContent    = total;
  document.getElementById('sumEnrolled').textContent = enrolled;
  document.getElementById('sumUnassigned').textContent = (total - enrolled);

  const hasSY = schoolYear !== '';
  document.getElementById('sumEnrolledWrap').style.display   = hasSY ? '' : 'none';
  document.getElementById('sumUnassignedWrap').style.display = hasSY ? '' : 'none';
  document.getElementById('sumNote').textContent = hasSY
    ? 'Enrollment status sourced from Section Assignment Tool'
    : 'Select a school year to see enrollment status';

  document.getElementById('mlSummaryBar').style.display = '';
}

// ── Clear filters ─────────────────────────────────────────────────────────────
function mlClear() {
  ['mlSchoolYear','mlSemester','mlProgram','mlYearSection','mlStatus','mlSection'].forEach(function (id) {
    document.getElementById(id).value = '';
  });
  document.getElementById('mlTbody').innerHTML = '<tr><td colspan="9"><div class="ml-empty"><i class="fas fa-filter text-primary"></i><p class="mb-1 fw-semibold text-dark">Set your filters and click <strong>Generate</strong></p><small>Program, year level, status, school year, and scheduling section filters are available.</small></div></td></tr>';
  document.getElementById('mlSummaryBar').style.display  = 'none';
  document.getElementById('mlExportBar').style.display   = 'none';
  document.getElementById('mlResultCount').style.display = 'none';
}

// ── Export CSV ────────────────────────────────────────────────────────────────
function mlExportCsv() {
  const url = ML_API + '?action=export_csv&' + mlParams();
  const a   = document.createElement('a');
  a.href = url;
  a.download = '';
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  // Refresh history after a brief delay.
  setTimeout(() => location.reload(), 2500);
}

// ── Print / PDF ───────────────────────────────────────────────────────────────
function mlPrintPdf() {
  const url = ML_API + '?action=print_view&' + mlParams();
  window.open(url, '_blank', 'width=1100,height=800,scrollbars=yes,resizable=yes');
  // Refresh history after a brief delay.
  setTimeout(() => location.reload(), 2500);
}

// ── Utility: HTML-escape ─────────────────────────────────────────────────────
function _esc(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g,'&amp;')
    .replace(/</g,'&lt;')
    .replace(/>/g,'&gt;')
    .replace(/"/g,'&quot;');
}
</script>

<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
