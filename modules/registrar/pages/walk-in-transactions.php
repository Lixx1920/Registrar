<?php
/**
 * SMS2 - Registrar: Walk-in Transaction Processing
 * Quick document request form for over-the-counter transactions
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/registrar-auth.php';
require_once __DIR__ . '/../includes/registrar-service.php';

regRequireAuth();

$pageTitle = "Walk-in Transactions";
require_once __DIR__ . '/../../../layouts/header.php';
?>

<style>
.quick-request-form {
    max-width: 800px;
    margin: 0 auto;
}
.doc-type-checkbox {
    display: flex;
    align-items: center;
    padding: 12px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    margin-bottom: 8px;
    cursor: pointer;
    transition: all 0.2s;
}
.doc-type-checkbox:hover {
    border-color: #0d6efd;
    background-color: #f8f9fa;
}
.doc-type-checkbox input[type="checkbox"] {
    width: 20px;
    height: 20px;
    margin-right: 12px;
}
.doc-type-checkbox.checked {
    border-color: #0d6efd;
    background-color: #e7f1ff;
}
.doc-type-info {
    flex: 1;
}
.doc-type-name {
    font-weight: 600;
    color: #212529;
}
.doc-type-desc {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 2px;
}
.quick-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}
.quick-stat-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 16px;
    text-align: center;
}
.quick-stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #0d6efd;
}
.quick-stat-label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 4px;
}
</style>

<div class="mpl" data-mpl>
    <div class="mpl-top">
        <div>
            <h1><?= e($pageTitle) ?></h1>
            <p>Quick document request processing for walk-in students</p>
        </div>
        <div class="mpl-toolbar">
            <a class="mpl-add" href="document-requests.php">
                <i class="fas fa-list"></i> View All Requests
            </a>
            <a class="mpl-add" href="document-release.php">
                <i class="fas fa-hand-holding"></i> Release Queue
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="quick-stats">
        <div class="quick-stat-card">
            <div class="quick-stat-value" id="statProcessedToday">0</div>
            <div class="quick-stat-label">Processed Today</div>
        </div>
        <div class="quick-stat-card">
            <div class="quick-stat-value" id="statWalkInQueue">0</div>
            <div class="quick-stat-label">Walk-in Queue</div>
        </div>
        <div class="quick-stat-card">
            <div class="quick-stat-value" id="statAvgTime">-</div>
            <div class="quick-stat-label">Avg. Processing Time</div>
        </div>
    </div>

    <!-- Quick Request Form -->
    <div class="mpl-panel">
        <div class="mpl-panel-header">
            <h2><i class="fas fa-bolt"></i> Quick Request Form</h2>
        </div>
        <div class="mpl-panel-body">
            <div class="quick-request-form">
                <form id="quickRequestForm">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <input type="hidden" name="channel" value="walk-in">

                    <!-- Student Search -->
                    <div class="mb-4">
                        <label class="form-label">Student <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input
                                type="text"
                                id="studentSearch"
                                class="form-control form-control-lg"
                                placeholder="Search by student number or name..."
                                autocomplete="off"
                                required>
                            <input type="hidden" name="student_id" id="studentId" required>
                            <div id="studentResults" class="dropdown-menu w-100" style="max-height: 300px; overflow-y: auto;"></div>
                        </div>
                        <div id="selectedStudentInfo" class="alert alert-info mt-2" style="display: none;"></div>
                    </div>

                    <!-- Document Types -->
                    <div class="mb-4">
                        <label class="form-label">Select Documents <span class="text-danger">*</span></label>
                        <div id="docTypesList">
                            <label class="doc-type-checkbox">
                                <input type="checkbox" name="doc_types[]" value="Form 137">
                                <div class="doc-type-info">
                                    <div class="doc-type-name">Form 137 - Transcript of Records</div>
                                    <div class="doc-type-desc">Complete academic records</div>
                                </div>
                            </label>

                            <label class="doc-type-checkbox">
                                <input type="checkbox" name="doc_types[]" value="Good Moral">
                                <div class="doc-type-info">
                                    <div class="doc-type-name">Good Moral Certificate</div>
                                    <div class="doc-type-desc">Certification of good conduct</div>
                                </div>
                            </label>

                            <label class="doc-type-checkbox">
                                <input type="checkbox" name="doc_types[]" value="TOR">
                                <div class="doc-type-info">
                                    <div class="doc-type-name">TOR - Transcript of Records</div>
                                    <div class="doc-type-desc">Official academic transcript</div>
                                </div>
                            </label>

                            <label class="doc-type-checkbox">
                                <input type="checkbox" name="doc_types[]" value="COE">
                                <div class="doc-type-info">
                                    <div class="doc-type-name">Certificate of Enrollment</div>
                                    <div class="doc-type-desc">Proof of current enrollment</div>
                                </div>
                            </label>

                            <label class="doc-type-checkbox">
                                <input type="checkbox" name="doc_types[]" value="COG">
                                <div class="doc-type-info">
                                    <div class="doc-type-name">Certificate of Grades</div>
                                    <div class="doc-type-desc">Official grade certification</div>
                                </div>
                            </label>

                            <label class="doc-type-checkbox">
                                <input type="checkbox" name="doc_types[]" value="Diploma">
                                <div class="doc-type-info">
                                    <div class="doc-type-name">Diploma Copy</div>
                                    <div class="doc-type-desc">Certified true copy of diploma</div>
                                </div>
                            </label>

                            <label class="doc-type-checkbox">
                                <input type="checkbox" name="doc_types[]" value="Honorable Dismissal">
                                <div class="doc-type-info">
                                    <div class="doc-type-name">Honorable Dismissal</div>
                                    <div class="doc-type-desc">Transfer credential</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Purpose -->
                    <div class="mb-4">
                        <label class="form-label">Purpose <span class="text-danger">*</span></label>
                        <select class="form-select" name="purpose" required>
                            <option value="">Select purpose...</option>
                            <option value="Employment">Employment</option>
                            <option value="Transfer">Transfer to another school</option>
                            <option value="Scholarship">Scholarship application</option>
                            <option value="Board Exam">Board exam application</option>
                            <option value="Further Studies">Further studies/Graduate school</option>
                            <option value="Personal Record">Personal record keeping</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <!-- Payment Status -->
                    <div class="mb-4">
                        <label class="form-label">Payment Status</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="paid" value="1" id="paidCheck">
                            <label class="form-check-label" for="paidCheck">
                                Payment received
                            </label>
                        </div>
                        <div class="mt-2" id="paymentRefGroup" style="display: none;">
                            <input type="text" class="form-control" name="payment_ref" placeholder="Payment reference number">
                        </div>
                    </div>

                    <!-- Student Email (optional) -->
                    <div class="mb-4">
                        <label class="form-label">Student Email (for notifications)</label>
                        <input type="email" class="form-control" name="student_email" placeholder="student@example.com">
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane"></i> Submit Request
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                            <i class="fas fa-redo"></i> Reset Form
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const API_BASE = '<?= API_BASE ?>/registrar/api';
let searchTimeout = null;

// Document type checkbox styling
document.querySelectorAll('.doc-type-checkbox').forEach(label => {
    const checkbox = label.querySelector('input[type="checkbox"]');
    checkbox.addEventListener('change', function() {
        if (this.checked) {
            label.classList.add('checked');
        } else {
            label.classList.remove('checked');
        }
    });
});

// Payment checkbox toggle
document.getElementById('paidCheck').addEventListener('change', function() {
    document.getElementById('paymentRefGroup').style.display = this.checked ? 'block' : 'none';
});

// Student search
const studentSearch = document.getElementById('studentSearch');
const studentResults = document.getElementById('studentResults');
const studentId = document.getElementById('studentId');
const selectedStudentInfo = document.getElementById('selectedStudentInfo');

studentSearch.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const query = this.value.trim();

    if (query.length < 2) {
        studentResults.classList.remove('show');
        return;
    }

    searchTimeout = setTimeout(async () => {
        try {
            const res = await fetch(`${API_BASE}/students.php?action=search&q=${encodeURIComponent(query)}`);
            const data = await res.json();

            if (!data.success) throw new Error(data.error || 'Search failed');

            const students = data.data || [];

            if (students.length === 0) {
                studentResults.innerHTML = '<div class="dropdown-item disabled">No students found</div>';
            } else {
                studentResults.innerHTML = students.map(s => `
                    <a href="#" class="dropdown-item" data-student-id="${s.id}"
                       data-student-name="${escapeHtml(s.first_name + ' ' + s.last_name)}"
                       data-student-number="${escapeHtml(s.student_number)}"
                       data-student-program="${escapeHtml(s.program_course || '')}">
                        <strong>${escapeHtml(s.first_name + ' ' + s.last_name)}</strong><br>
                        <small>${escapeHtml(s.student_number)} - ${escapeHtml(s.program_course || 'N/A')}</small>
                    </a>
                `).join('');
            }

            studentResults.classList.add('show');
        } catch (err) {
            studentResults.innerHTML = `<div class="dropdown-item disabled text-danger">Error: ${escapeHtml(err.message)}</div>`;
            studentResults.classList.add('show');
        }
    }, 300);
});

// Select student from results
studentResults.addEventListener('click', function(e) {
    e.preventDefault();
    const item = e.target.closest('.dropdown-item');
    if (!item || item.classList.contains('disabled')) return;

    const id = item.dataset.studentId;
    const name = item.dataset.studentName;
    const number = item.dataset.studentNumber;
    const program = item.dataset.studentProgram;

    studentId.value = id;
    studentSearch.value = name;
    studentResults.classList.remove('show');

    selectedStudentInfo.innerHTML = `
        <strong>${name}</strong><br>
        Student No: ${number}<br>
        Program: ${program}
    `;
    selectedStudentInfo.style.display = 'block';
});

// Hide results when clicking outside
document.addEventListener('click', function(e) {
    if (!studentSearch.contains(e.target) && !studentResults.contains(e.target)) {
        studentResults.classList.remove('show');
    }
});

// Form submission
document.getElementById('quickRequestForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const docTypes = formData.getAll('doc_types[]');

    if (docTypes.length === 0) {
        alert('Please select at least one document type.');
        return;
    }

    if (!formData.get('student_id')) {
        alert('Please select a student.');
        return;
    }

    const payload = {
        csrf_token: formData.get('csrf_token'),
        student_id: parseInt(formData.get('student_id')),
        doc_types: docTypes,
        purpose: formData.get('purpose'),
        channel: 'walk-in',
        paid: formData.get('paid') ? 1 : 0,
        payment_ref: formData.get('payment_ref') || null,
        student_email: formData.get('student_email') || null
    };

    try {
        const res = await fetch(`${API_BASE}/documents.php?action=create`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (!data.success) throw new Error(data.error || 'Request failed');

        alert(`Request created successfully!\nRequest No: ${data.request_no}\n\nThe documents will be processed shortly.`);
        resetForm();
        loadStats();
    } catch (err) {
        alert('Error: ' + err.message);
    }
});

function resetForm() {
    document.getElementById('quickRequestForm').reset();
    document.getElementById('studentId').value = '';
    document.getElementById('selectedStudentInfo').style.display = 'none';
    document.querySelectorAll('.doc-type-checkbox').forEach(label => {
        label.classList.remove('checked');
    });
    document.getElementById('paymentRefGroup').style.display = 'none';
}

async function loadStats() {
    try {
        const res = await fetch(`${API_BASE}/documents.php?action=stats`);
        const data = await res.json();

        if (data.success && data.data) {
            // Stats would need to be calculated from the API - placeholder for now
            document.getElementById('statProcessedToday').textContent = data.data.released_today || 0;
            document.getElementById('statWalkInQueue').textContent = data.data.pending_requests || 0;
        }
    } catch (err) {
        console.error('Failed to load stats:', err);
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Load initial stats
loadStats();
</script>

<?php require_once __DIR__ . '/../../../layouts/footer.php'; ?>
