<?php
/**
 * SMS 2 - Persona File Database
 * Module: Registrar
 * Manage identity documents (SSS, TIN, Driver's License, etc.)
 *
 * If no ?student_id= is given (e.g. clicked from the sidebar), shows a
 * student search picker instead of a hard error.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/registrar-service.php';
require_once __DIR__ . '/../includes/storage-service.php';

regRequireAction('registrar.view');

$pageTitle    = 'Persona File Database';
$activeModule = 'registrar';
$activePage   = 'persona-file-database';

// Get student from query parameter (may be absent, e.g. clicked from sidebar)
$studentId = (int)($_GET['student_id'] ?? 0);
$student   = null;
$notFound  = false;

if ($studentId > 0) {
    $student = regGetStudent($studentId);
    if (!$student) {
        $notFound = true;
        $studentId = 0; // fall through to picker view
    }
}

// Get files for this student in 'identity' category
$files = [];
if ($student) {
    $files = regListStudentFiles($studentId, 'identity');
}

$breadcrumbs = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Persona File Database', 'url' => $student ? (BASE_URL . '/modules/registrar/pages/persona-file-database.php') : null],
];
if ($student) {
    $breadcrumbs[] = ['label' => $student['first_name'] . ' ' . $student['last_name'], 'url' => null];

    // Shows a "Back" pill on the right end of the dark page-title banner.
    $pageBannerBackUrl   = BASE_URL . '/modules/registrar/pages/persona-file-database.php';
    $pageBannerBackLabel = 'Back to Search';
}

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/modules/registrar/assets/css/registrar.css">

<?php renderBreadcrumbs($breadcrumbs); ?>

<div class="container-fluid py-4">

<?php if (!$student): ?>

    <!-- ============ STUDENT PICKER (no student_id in URL, or invalid one) ============ -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 text-dark mb-1">
                <i class="fas fa-folder-open text-primary me-2"></i>Persona File Database
            </h1>
            <p class="text-muted mb-0">Search for a student to view or manage their identity documents</p>
        </div>
    </div>

    <?php if ($notFound): ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i> Student #<?php echo (int)($_GET['student_id'] ?? 0); ?> was not found. Search below to continue.
    </div>
    <?php endif; ?>

    <div class="reg-card mb-4">
        <div class="reg-card-body">
            <div class="reg-form-group mb-0">
                <label>Search by student number or name</label>
                <input type="text" id="studentSearchInput" class="form-control form-control-lg"
                       placeholder="e.g., 2024001 or Juan Santos" autocomplete="off" autofocus>
            </div>
        </div>
    </div>

    <div id="studentSearchResults">
        <div class="alert reg-search-hint text-center">
            <i class="fas fa-search"></i> Start typing to find a student.
        </div>
    </div>

<?php else: ?>

    <!-- ============ IDENTITY DOCUMENTS FOR THE SELECTED STUDENT ============ -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-dark">Persona File Database</h1>
            <p class="text-muted">Manage identity documents for <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></p>
        </div>
        <button class="btn btn-primary" onclick="showRegModal('uploadModal')">
            <i class="fas fa-upload"></i> Upload Document
        </button>
    </div>

    <!-- Student Summary Card -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="reg-card">
                <div class="reg-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Student Number:</strong> <?php echo htmlspecialchars($student['student_number']); ?></p>
                            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($student['first_name'] . ' ' . ($student['middle_name'] ?? '') . ' ' . $student['last_name']); ?></p>
                            <p><strong>Program:</strong> <?php echo htmlspecialchars($student['program_course'] ?? '-'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Year & Section:</strong> <?php echo htmlspecialchars($student['year_section'] ?? '-'); ?></p>
                            <p><strong>Status:</strong> <?php echo htmlspecialchars($student['status'] ?? '-'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Identity Documents Table -->
    <div class="card reg-shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Identity Documents (<?php echo count($files); ?> files)</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover reg-table mb-0">
                <thead>
                    <tr>
                        <th>Document Type</th>
                        <th>File Name</th>
                        <th>Size</th>
                        <th>Uploaded Date</th>
                        <th>SHA-256 Hash</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($files)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <p class="text-muted mb-0">No identity documents uploaded yet</p>
                            <small class="text-muted">Click "Upload Document" to add files</small>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($files as $file): ?>
                    <tr>
                        <td>
                            <strong><?php
                                $docTypes = [
                                    'passport' => '🛂 Passport',
                                    'birth_cert' => '📋 Birth Certificate',
                                    'tin' => '💼 TIN',
                                    'sss' => '💼 SSS',
                                    'drivers' => '🚗 Driver\'s License',
                                    'nbi' => '🏛️ NBI Clearance',
                                    'prc' => '📜 PRC License'
                                ];
                                echo $docTypes[$file['category']] ?? 'Identity Document';
                            ?></strong>
                        </td>
                        <td>
                            <code><?php echo htmlspecialchars(basename($file['file_path'])); ?></code>
                        </td>
                        <td>
                            <small><?php echo formatFileSize($file['file_size']); ?></small>
                        </td>
                        <td>
                            <small><?php echo formatDate($file['created_at']); ?></small>
                        </td>
                        <td>
                            <code style="font-size: 0.75rem;"><?php echo substr($file['file_hash'], 0, 16) . '...'; ?></code>
                        </td>
                        <td>
                            <span class="badge bg-success">✓ Verified</span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="verifyFile(<?php echo $file['id']; ?>)" title="Verify">
                                <i class="fas fa-check-circle"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteFile(<?php echo $file['id']; ?>)" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sample Identity Document Types Reference -->
    <div class="alert alert-light mt-4">
        <h6 class="mb-3"><strong>Accepted Identity Document Types:</strong></h6>
        <div class="row">
            <div class="col-md-6">
                <ul class="mb-0">
                    <li><strong>Passport</strong> - International travel document</li>
                    <li><strong>Birth Certificate</strong> - Vital record</li>
                    <li><strong>TIN</strong> - Tax Identification Number</li>
                    <li><strong>SSS</strong> - Social Security System ID</li>
                </ul>
            </div>
            <div class="col-md-6">
                <ul class="mb-0">
                    <li><strong>Driver's License</strong> - Valid government ID</li>
                    <li><strong>NBI Clearance</strong> - National Bureau of Investigation</li>
                    <li><strong>PRC License</strong> - Professional Regulation Commission</li>
                    <li><strong>Other</strong> - Supporting documents</li>
                </ul>
            </div>
        </div>
    </div>

<?php endif; ?>

</div>

<?php if ($student): ?>
<!-- Upload Document Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="reg-modal-header">
                <h5 class="modal-title">Upload Identity Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="uploadForm" onsubmit="return handleUpload(event)">
                <div class="modal-body">
                    <div class="reg-form-group">
                        <label>Document Type *</label>
                        <select name="document_type" class="form-select" required>
                            <option value="">Select Document Type</option>
                            <option value="passport">🛂 Passport</option>
                            <option value="birth_cert">📋 Birth Certificate</option>
                            <option value="tin">💼 TIN</option>
                            <option value="sss">💼 SSS</option>
                            <option value="drivers">🚗 Driver's License</option>
                            <option value="nbi">🏛️ NBI Clearance</option>
                            <option value="prc">📜 PRC License</option>
                        </select>
                    </div>

                    <div class="reg-form-group">
                        <label>Upload File *</label>
                        <div class="reg-dropzone" id="dropzone">
                            <div class="reg-dropzone-icon">📄</div>
                            <p class="reg-dropzone-text">Drag file here or click to browse</p>
                            <small class="reg-dropzone-hint">PDF, JPG, PNG (Max 5MB)</small>
                            <input type="file" id="fileInput" style="display: none;" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>
                    </div>

                    <div class="reg-form-group">
                        <label>Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Additional notes about this document"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-reg-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script src="<?php echo BASE_URL; ?>/modules/registrar/assets/js/registrar.js"></script>
<script>
const API_BASE = '<?php echo BASE_URL; ?>/modules/registrar/api';

<?php if (!$student): ?>
/* ============ Student search picker (shown when no student is selected) ============ */
const searchInput = document.getElementById('studentSearchInput');
const resultsBox = document.getElementById('studentSearchResults');

const runSearch = debounce(async function () {
    const q = searchInput.value.trim();
    if (q.length < 2) {
        resultsBox.innerHTML = '<div class="alert reg-search-hint text-center">' +
            '<i class="fas fa-search"></i> Keep typing (at least 2 characters).</div>';
        return;
    }

    resultsBox.innerHTML = '<div class="text-center text-muted py-3"><i class="fas fa-spinner fa-spin"></i> Searching...</div>';

    try {
        const response = await fetch(API_BASE + '/students.php?action=search&q=' + encodeURIComponent(q) + '&limit=10');
        const result = await response.json();

        if (!result.success || !result.data || result.data.length === 0) {
            resultsBox.innerHTML = '<div class="alert reg-search-hint reg-search-hint--static text-center">' +
                '<i class="fas fa-info-circle"></i> No matching students found.</div>';
            return;
        }

        let html = '<div class="card reg-shadow"><div class="table-responsive"><table class="table reg-table mb-0"><thead><tr>' +
            '<th>Student No.</th><th>Name</th><th>Program</th><th>Year/Section</th><th></th></tr></thead><tbody>';

        result.data.forEach(function (s) {
            html += '<tr>' +
                '<td><span class="badge bg-primary">' + escapeHtml(s.student_number) + '</span></td>' +
                '<td><strong>' + escapeHtml(s.last_name + ', ' + s.first_name) + '</strong></td>' +
                '<td>' + escapeHtml(s.program_course || '-') + '</td>' +
                '<td>' + escapeHtml(s.year_section || '-') + '</td>' +
                '<td><a class="btn btn-sm btn-primary" href="persona-file-database.php?student_id=' + encodeURIComponent(s.id) + '">' +
                'Select <i class="fas fa-arrow-right"></i></a></td>' +
                '</tr>';
        });

        html += '</tbody></table></div></div>';
        resultsBox.innerHTML = html;
    } catch (error) {
        console.error(error);
        resultsBox.innerHTML = '<div class="alert alert-danger">Error searching students: ' + error.message + '</div>';
    }
}, 350);

searchInput.addEventListener('input', runSearch);

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str == null ? '' : String(str);
    return div.innerHTML;
}
<?php else: ?>
/* ============ Identity document upload/verify/delete (unchanged behavior) ============ */
const studentId = <?php echo (int)$studentId; ?>;

// Initialize dropzone
document.getElementById('dropzone').addEventListener('click', function() {
    document.getElementById('fileInput').click();
});

document.getElementById('fileInput').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        document.querySelector('.reg-dropzone-text').textContent = `Selected: ${file.name}`;
        document.querySelector('.reg-dropzone-hint').textContent = `${formatFileSize(file.size)}`;
    }
});

async function handleUpload(e) {
    e.preventDefault();

    const form = document.getElementById('uploadForm');
    const file = document.getElementById('fileInput').files[0];
    const docType = form.querySelector('select[name="document_type"]').value;

    if (!file) {
        showRegError('Please select a file');
        return false;
    }

    try {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('student_id', studentId);
        formData.append('category', docType);

        const response = await fetch(API_BASE + '/files.php?action=upload', {
            method: 'POST',
            headers: {'X-CSRF-Token': '<?= e(csrfToken()) ?>'},
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            showRegSuccess('Document uploaded successfully');
            form.reset();
            document.querySelector('.reg-dropzone-text').textContent = 'Drag file here or click to browse';
            document.querySelector('.reg-dropzone-hint').textContent = 'PDF, JPG, PNG (Max 5MB)';
            hideRegModal('uploadModal');
            setTimeout(() => location.reload(), 1500);
        } else {
            showRegError(result.error || 'Upload failed');
        }
    } catch (error) {
        console.error(error);
        showRegError('Error: ' + error.message);
    }

    return false;
}

async function verifyFile(fileId) {
    try {
        const response = await fetch(API_BASE + '/files.php?action=verify&file_id=' + fileId);
        const result = await response.json();

        if (result.valid) {
            showRegSuccess('✓ File integrity verified!');
        } else {
            showRegError('⚠️ File integrity check failed: ' + (result.reason || 'Hash mismatch'));
        }
    } catch (error) {
        showRegError('Verification error: ' + error.message);
    }
}

async function deleteFile(fileId) {
    if (!confirm('Delete this document?')) return;

    try {
        const response = await fetch(API_BASE + '/files.php?action=delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?= e(csrfToken()) ?>'
            },
            body: JSON.stringify({file_id: fileId})
        });

        const result = await response.json();

        if (result.success) {
            showRegSuccess('Document deleted');
            setTimeout(() => location.reload(), 1500);
        } else {
            showRegError(result.error || 'Delete failed');
        }
    } catch (error) {
        showRegError('Error: ' + error.message);
    }
}
<?php endif; ?>
</script>

<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>