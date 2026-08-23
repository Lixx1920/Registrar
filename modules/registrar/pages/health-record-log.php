<?php
/**
 * SMS 2 - Health Record Log
 * Module: Registrar
 * Manage student medical checkup and health record entries.
 */
declare(strict_types=1);
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/registrar-service.php';
regRequireAction('registrar.view');
$pageTitle = 'Health Record Log';
$activeModule = 'registrar';
$activePage = 'health-record-log';
$db = db();
$studentId = (int)($_GET['student_id'] ?? 0);
$student = $studentId > 0 ? regGetStudent($studentId) : null;
$records = [];
$healthProfile = null;
if ($student) {
    $stmt = $db->prepare("SELECT * FROM reg_health_records WHERE student_id = ? ORDER BY checkup_date DESC, id DESC");
    $stmt->execute([$studentId]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = $db->prepare("SELECT * FROM reg_health_profiles WHERE student_id = ? LIMIT 1");
    $stmt->execute([$studentId]);
    $healthProfile = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
} else {
    $records = $db->query("SELECT hr.*, s.student_number, s.first_name, s.last_name, s.program_course,
        hp.blood_type, hp.height, hp.weight, hp.allergies
        FROM reg_health_records hr
        JOIN reg_students s ON s.id = hr.student_id
        LEFT JOIN reg_health_profiles hp ON hp.student_id = hr.student_id
        ORDER BY hr.checkup_date DESC, hr.id DESC")->fetchAll(PDO::FETCH_ASSOC);
}
$totalStudents = (int)$db->query("SELECT COUNT(*) FROM reg_students WHERE status = 'Active'")->fetchColumn();
$studentsWithRecords = (int)$db->query("SELECT COUNT(DISTINCT student_id) FROM reg_health_records")->fetchColumn();
$studentsWithoutRecords = max(0, $totalStudents - $studentsWithRecords);
$medicationCount = (int)$db->query("SELECT COUNT(*) FROM reg_health_records WHERE medication IS NOT NULL AND medication <> ''")->fetchColumn();
$profileCounts = ['blood_type' => 0, 'height' => 0, 'weight' => 0, 'allergies' => 0];
if (!$student) {
    $profileCounts = $db->query("SELECT
        COUNT(NULLIF(blood_type, '')) AS blood_type,
        COUNT(NULLIF(height, '')) AS height,
        COUNT(NULLIF(weight, '')) AS weight,
        COUNT(NULLIF(allergies, '')) AS allergies
        FROM reg_health_profiles")->fetch(PDO::FETCH_ASSOC) ?: $profileCounts;
}
$breadcrumbs = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Health Record Log', 'url' => $student ? BASE_URL . '/modules/registrar/pages/health-record-log.php' : null],
];
if ($student) {
    $breadcrumbs[] = ['label' => $student['first_name'] . ' ' . $student['last_name'], 'url' => null];
}
require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/modules/registrar/assets/css/registrar.css?v=health-record-4">
<?php renderBreadcrumbs($breadcrumbs); ?>
<div class="container-fluid py-4 health-record-page">
    <div class="d-flex justify-content-between align-items-start mb-4 health-page-header">
        <div><h1 class="h3 text-dark mb-1"><i class="fas fa-heartbeat text-danger me-2"></i>Health Record Log</h1><p class="text-muted mb-0">Manage student medical information, health summaries, and physical examination records</p></div>
        <div class="d-flex gap-2 align-items-center">
            <?php if ($student): ?><a class="btn btn-reg-secondary" href="javascript:void(0)" onclick="openProfileModal()"><i class="fas fa-user-edit me-1"></i> Edit Health Profile</a><a class="btn btn-reg-primary" href="javascript:void(0)" onclick="openAddModal()"><i class="fas fa-plus me-1"></i> Add Record</a><?php endif; ?>
        </div>
    </div>

    <div class="card border-start border-danger border-4 reg-shadow mb-4 health-feature-panel">
        <div class="card-body py-3"><h6 class="text-danger fw-semibold mb-3"><i class="fas fa-notes-medical me-2"></i>Health Record Overview</h6><div class="row g-3 health-feature-grid">
            <div class="col-md-3"><div class="d-flex align-items-start gap-2 health-feature-item"><i class="fas fa-heartbeat text-danger mt-1"></i><div><strong>Checkup History</strong><br><small class="text-muted">Record and review student medical examinations</small></div></div></div>
            <div class="col-md-3"><div class="d-flex align-items-start gap-2 health-feature-item"><i class="fas fa-stethoscope text-danger mt-1"></i><div><strong>Medical Findings</strong><br><small class="text-muted">Keep complaints, findings, and vital signs together</small></div></div></div>
            <div class="col-md-3"><div class="d-flex align-items-start gap-2 health-feature-item"><i class="fas fa-pills text-danger mt-1"></i><div><strong>Medications</strong><br><small class="text-muted">Track prescribed or ongoing medication entries</small></div></div></div>
            <div class="col-md-3"><div class="d-flex align-items-start gap-2 health-feature-item"><i class="fas fa-file-medical text-danger mt-1"></i><div><strong>Health Notes</strong><br><small class="text-muted">Keep important immunization and emergency notes</small></div></div></div>
        </div></div>
    </div>

    <div class="row g-3 mb-4 health-summary-grid">
        <?php $healthProfileCards = $student ? [['fa-tint', 'Blood Type', $healthProfile['blood_type'] ?? 'Not recorded'], ['fa-ruler-vertical', 'Height', $healthProfile['height'] ?? 'Not recorded'], ['fa-weight', 'Weight', $healthProfile['weight'] ?? 'Not recorded'], ['fa-exclamation-triangle', 'Allergies', $healthProfile['allergies'] ?? 'Not recorded']] : [['fa-tint', 'Blood Type', (int)$profileCounts['blood_type'] . ' recorded'], ['fa-ruler-vertical', 'Height', (int)$profileCounts['height'] . ' recorded'], ['fa-weight', 'Weight', (int)$profileCounts['weight'] . ' recorded'], ['fa-exclamation-triangle', 'Allergies', (int)$profileCounts['allergies'] . ' recorded']]; foreach ($healthProfileCards as $card): ?>
        <div class="col-6 col-md-3"><div class="reg-card text-center health-summary-card"><div class="reg-card-body py-3"><i class="fas <?php echo $card[0]; ?> fa-2x text-danger mb-2"></i><h6 class="text-muted mb-1"><?php echo $card[1]; ?></h6><h5 class="fw-bold mb-0"><?php echo $card[2]; ?></h5></div></div></div>
        <?php endforeach; ?>
    </div>

    <?php if ($student): ?>
    <div class="card reg-shadow mb-4 health-student-card"><div class="card-body"><div class="row"><div class="col-md-6"><p><strong>Student Number:</strong> <?php echo htmlspecialchars($student['student_number']); ?></p><p><strong>Full Name:</strong> <?php echo htmlspecialchars(trim($student['first_name'] . ' ' . ($student['middle_name'] ?? '') . ' ' . $student['last_name'])); ?></p></div><div class="col-md-6"><p><strong>Program:</strong> <?php echo htmlspecialchars($student['program_course'] ?? '-'); ?></p><p><strong>Year &amp; Section:</strong> <?php echo htmlspecialchars($student['year_section'] ?? '-'); ?></p></div></div></div></div>
    <?php endif; ?>

    <div class="card reg-shadow health-records-panel">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center"><strong><i class="fas fa-notes-medical me-2"></i><?php echo $student ? 'Student Health Records' : 'All Health Records'; ?></strong><span><?php echo count($records); ?> record(s)</span></div>
        <div class="health-table-tools"><div class="reg-form-group mb-0"><input type="search" id="healthSearch" class="form-control" placeholder="Search records..." aria-label="Search health records"></div><?php if ($student): ?><a class="btn btn-reg-primary" href="health-record-log.php"><i class="fas fa-arrow-left me-1"></i> Dashboard</a><?php endif; ?></div>
        <div class="table-responsive"><table class="table reg-table mb-0" id="healthRecordsTable"><thead><tr><?php if (!$student): ?><th>Student</th><?php endif; ?><th>Date</th><th>Type</th><th>Condition / Medication</th><th>Physician</th><th>Remarks</th><th>Actions</th></tr></thead><tbody>
        <?php if (empty($records)): ?><tr><td colspan="<?php echo $student ? 6 : 7; ?>" class="text-center py-4 text-muted"><i class="fas fa-info-circle me-1"></i>No health records found.</td></tr><?php else: foreach ($records as $record): $search = strtolower(implode(' ', [$record['student_number'] ?? '', $record['first_name'] ?? '', $record['last_name'] ?? '', $record['complaints'] ?? '', $record['findings'] ?? '', $record['medication'] ?? '', $record['physician_nurse'] ?? '', $record['notes'] ?? '', $record['blood_type'] ?? '', $record['allergies'] ?? ''])); $conditionMedication = trim(($record['allergies'] ?? '') . (!empty($record['allergies']) && (!empty($record['findings']) || !empty($record['medication'])) ? ' / ' : '') . ($record['findings'] ?? '') . ((!empty($record['findings']) && !empty($record['medication'])) ? ' / ' : '') . ($record['medication'] ?? '')); $remarks = trim(($record['complaints'] ?? '') . (!empty($record['complaints']) && !empty($record['notes']) ? ' | ' : '') . ($record['notes'] ?? '')); ?>
        <tr data-search="<?php echo htmlspecialchars($search); ?>"><?php if (!$student): ?><td><strong><?php echo htmlspecialchars(($record['last_name'] ?? '') . ', ' . ($record['first_name'] ?? '')); ?></strong><small class="d-block text-muted"><?php echo htmlspecialchars($record['student_number'] ?? ''); ?></small></td><?php endif; ?><td><?php echo htmlspecialchars($record['checkup_date']); ?></td><td><span class="badge bg-danger-subtle text-danger"><?php echo htmlspecialchars($student ? ($healthProfile['blood_type'] ?? 'Not recorded') : ($record['blood_type'] ?? 'Not recorded')); ?></span></td><td><?php echo htmlspecialchars($conditionMedication ?: '—'); ?></td><td><?php echo htmlspecialchars($record['physician_nurse'] ?? '—'); ?></td><td><small class="text-muted"><?php echo htmlspecialchars($remarks ?: '—'); ?></small></td><td><?php if ($student): ?><button class="btn btn-sm btn-primary" onclick="openEditModal(<?php echo (int)$record['id']; ?>)" title="Edit"><i class="fas fa-pen"></i></button> <button class="btn btn-sm btn-danger" onclick="deleteRecord(<?php echo (int)$record['id']; ?>)" title="Delete"><i class="fas fa-trash"></i></button><?php else: ?><a class="btn btn-sm btn-info" href="health-record-log.php?student_id=<?php echo (int)$record['student_id']; ?>" title="Information"><i class="fas fa-info-circle"></i></a> <a class="btn btn-sm btn-primary" href="health-record-log.php?student_id=<?php echo (int)$record['student_id']; ?>&open=add" title="Add health record"><i class="fas fa-plus"></i></a> <a class="btn btn-sm btn-secondary" href="health-record-log.php?student_id=<?php echo (int)$record['student_id']; ?>&open=profile" title="Edit health profile"><i class="fas fa-user-edit"></i></a><?php endif; ?></td></tr>
        <?php endforeach; endif; ?></tbody></table></div>
    </div>
</div>

<?php if ($student): ?>
<div class="modal fade" id="healthProfileModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="reg-modal-header"><h5 class="modal-title">Edit Health Profile</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form id="healthProfileForm" onsubmit="return saveHealthProfile(event)"><div class="modal-body"><input type="hidden" name="student_id" value="<?php echo (int)$studentId; ?>"><div class="reg-form-section"><div class="row"><div class="col-md-6"><div class="reg-form-group"><label>Blood Type</label><select name="blood_type" id="profileBloodType" class="form-select"><option value="">Not recorded</option><option>A+</option><option>A-</option><option>B+</option><option>B-</option><option>AB+</option><option>AB-</option><option>O+</option><option>O-</option></select></div></div><div class="col-md-6"><div class="reg-form-group"><label>Height</label><input type="text" name="height" id="profileHeight" class="form-control" placeholder="e.g. 168 cm"></div></div></div><div class="row"><div class="col-md-6"><div class="reg-form-group"><label>Weight</label><input type="text" name="weight" id="profileWeight" class="form-control" placeholder="e.g. 65 kg"></div></div><div class="col-md-6"><div class="reg-form-group"><label>Allergies</label><input type="text" name="allergies" id="profileAllergies" class="form-control" placeholder="None known or list allergies"></div></div></div></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-reg-primary">Save Health Profile</button></div></form></div></div></div>
<div class="modal fade" id="healthModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="reg-modal-header"><h5 class="modal-title" id="healthModalTitle">Add Health Record</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form id="healthForm" onsubmit="return saveHealth(event)"><div class="modal-body"><input type="hidden" name="id" id="healthId"><input type="hidden" name="student_id" value="<?php echo (int)$studentId; ?>"><div class="reg-form-section"><div class="row"><div class="col-md-6"><div class="reg-form-group"><label>Checkup Date *</label><input type="date" name="checkup_date" id="healthDate" class="form-control" required></div></div><div class="col-md-6"><div class="reg-form-group"><label>Physician / Nurse</label><input type="text" name="physician_nurse" id="healthPhysician" class="form-control"></div></div></div><div class="row"><div class="col-md-6"><div class="reg-form-group"><label>Complaints</label><textarea name="complaints" id="healthComplaints" class="form-control" rows="2"></textarea></div></div><div class="col-md-6"><div class="reg-form-group"><label>Findings</label><textarea name="findings" id="healthFindings" class="form-control" rows="2"></textarea></div></div></div><div class="row"><div class="col-md-4"><div class="reg-form-group"><label>Blood Pressure</label><input type="text" id="healthBloodPressure" class="form-control"></div></div><div class="col-md-4"><div class="reg-form-group"><label>Temperature</label><input type="text" id="healthTemperature" class="form-control"></div></div><div class="col-md-4"><div class="reg-form-group"><label>Pulse Rate</label><input type="text" id="healthPulse" class="form-control"></div></div></div><div class="row"><div class="col-md-6"><div class="reg-form-group"><label>Immunization</label><input type="text" name="immunization" id="healthImmunization" class="form-control"></div></div><div class="col-md-6"><div class="reg-form-group"><label>Medication</label><input type="text" name="medication" id="healthMedication" class="form-control"></div></div></div><div class="reg-form-group"><label>Notes</label><textarea name="notes" id="healthNotes" class="form-control" rows="2"></textarea></div></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-reg-primary">Save Health Record</button></div></form></div></div></div>
<?php endif; ?>
<script src="<?php echo BASE_URL; ?>/modules/registrar/assets/js/registrar.js"></script><script>
const HEALTH_API = '<?php echo BASE_URL; ?>/modules/registrar/api/health.php'; const HEALTH_CSRF = '<?= e(csrfToken()) ?>';
const healthRecords = <?php echo json_encode($records, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
const healthProfile = <?php echo json_encode($healthProfile, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
document.getElementById('healthSearch')?.addEventListener('input', function () { const q = this.value.toLowerCase(); document.querySelectorAll('#healthRecordsTable tbody tr[data-search]').forEach(row => row.style.display = row.dataset.search.includes(q) ? '' : 'none'); });
<?php if ($student): ?>
function openProfileModal() { document.getElementById('profileBloodType').value = healthProfile?.blood_type || ''; document.getElementById('profileHeight').value = healthProfile?.height || ''; document.getElementById('profileWeight').value = healthProfile?.weight || ''; document.getElementById('profileAllergies').value = healthProfile?.allergies || ''; showRegModal('healthProfileModal'); }
function saveHealthProfile(e) { e.preventDefault(); const data = Object.fromEntries(new FormData(document.getElementById('healthProfileForm'))); healthRequest('save_profile', data).then(r => { if (!r.success) throw new Error(r.error || 'Unable to save health profile'); showRegSuccess(r.message); setTimeout(() => location.reload(), 700); }).catch(e => showRegError(e.message)); return false; }
function openAddModal() { document.getElementById('healthForm').reset(); document.getElementById('healthId').value = ''; document.getElementById('healthDate').value = new Date().toISOString().slice(0, 10); document.getElementById('healthModalTitle').textContent = 'Add Health Record'; showRegModal('healthModal'); }
function openEditModal(id) { const r = healthRecords.find(item => parseInt(item.id, 10) === id); if (!r) return; const v = r.vital_signs ? JSON.parse(r.vital_signs) : {}; document.getElementById('healthId').value = r.id; document.getElementById('healthDate').value = r.checkup_date || ''; document.getElementById('healthPhysician').value = r.physician_nurse || ''; document.getElementById('healthComplaints').value = r.complaints || ''; document.getElementById('healthFindings').value = r.findings || ''; document.getElementById('healthBloodPressure').value = v.blood_pressure || ''; document.getElementById('healthTemperature').value = v.temperature || ''; document.getElementById('healthPulse').value = v.pulse || ''; document.getElementById('healthImmunization').value = r.immunization || ''; document.getElementById('healthMedication').value = r.medication || ''; document.getElementById('healthNotes').value = r.notes || ''; document.getElementById('healthModalTitle').textContent = 'Edit Health Record'; showRegModal('healthModal'); }
function healthRequest(action, data) { return fetch(HEALTH_API + '?action=' + action, {method: 'POST', headers: {'Content-Type': 'application/json', 'X-CSRF-Token': HEALTH_CSRF}, body: JSON.stringify(data)}).then(r => r.json()); }
function saveHealth(e) { e.preventDefault(); const data = Object.fromEntries(new FormData(document.getElementById('healthForm'))); data.vital_signs = {blood_pressure: document.getElementById('healthBloodPressure').value, temperature: document.getElementById('healthTemperature').value, pulse: document.getElementById('healthPulse').value}; healthRequest('save', data).then(r => { if (!r.success) throw new Error(r.error || 'Unable to save record'); showRegSuccess(r.message); setTimeout(() => location.reload(), 700); }).catch(e => showRegError(e.message)); return false; }
function deleteRecord(id) { if (!confirm('Delete this health record?')) return; healthRequest('delete', {id: id}).then(r => { if (!r.success) throw new Error(r.error || 'Unable to delete record'); showRegSuccess(r.message); setTimeout(() => location.reload(), 700); }).catch(e => showRegError(e.message)); }
<?php if (in_array($_GET['open'] ?? '', ['add', 'profile'], true)): ?>document.addEventListener('DOMContentLoaded', function () { <?php echo ($_GET['open'] ?? '') === 'profile' ? 'openProfileModal();' : 'openAddModal();'; ?> });<?php endif; ?>
<?php endif; ?></script>
<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
