<?php
/**
 * SMS2 - Unified Student 360° Dossier (Option D)
 * Shared Slide-Over Drawer / Expanded Modal Component for all Registrar Modules.
 *
 * Usage:
 *   require_once __DIR__ . '/../includes/student-dossier-drawer.php';
 *   Then in JS:
 *   viewStudent(studentId, 'bio' | 'guardians' | 'academics' | 'docs' | 'timeline');
 */
?>
<style>
/* ==========================================================================
   UNIFIED STUDENT 360° DOSSIER (LIGHT & DARK MODE + DRAWER / MODAL TOGGLE)
   ========================================================================== */
.student-360-dialog {
    max-width: 960px;
    transition: all 0.25s ease;
}

/* Slide-Over Right Drawer Mode */
.modal.s360-drawer-mode .student-360-dialog {
    position: fixed;
    right: 0;
    top: 0;
    bottom: 0;
    margin: 0;
    width: 100%;
    max-width: 780px;
    height: 100vh;
    transform: translateX(100%);
    transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1);
}
.modal.s360-drawer-mode.show .student-360-dialog {
    transform: translateX(0);
}
.modal.s360-drawer-mode .modal-content {
    height: 100vh;
    border-radius: 16px 0 0 16px !important;
    border-right: none;
    display: flex;
    flex-direction: column;
}
.modal.s360-drawer-mode .s360-body {
    flex: 1 1 auto;
    max-height: none !important;
    overflow-y: auto;
}

#studentProfileModal .modal-content {
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.28);
    background: #ffffff;
}

.s360-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 60%, #2563eb 100%);
    color: #ffffff;
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
}
.s360-header-main {
    display: flex;
    align-items: center;
    gap: 1.1rem;
}
.s360-avatar {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.18);
    border: 2px solid rgba(255, 255, 255, 0.35);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    font-weight: 800;
    color: #ffffff;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
.s360-chips {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
    margin-bottom: 4px;
}
.s360-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 2px 9px;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.02em;
}
.s360-chip-num {
    background: rgba(15, 23, 42, 0.35);
    color: #e2e8f0;
    border: 1px solid rgba(255, 255, 255, 0.2);
}
.s360-chip-sec {
    background: rgba(255, 255, 255, 0.16);
    color: #ffffff;
}
.s360-name {
    font-size: 1.28rem;
    font-weight: 800;
    margin: 0 0 2px 0;
    color: #ffffff;
    letter-spacing: -0.01em;
}
.s360-program {
    font-size: 0.83rem;
    color: rgba(255, 255, 255, 0.88);
    font-weight: 500;
}
.s360-header-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}
.s360-mode-btn {
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #ffffff;
    border-radius: 8px;
    padding: 5px 10px;
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
}
.s360-mode-btn:hover {
    background: rgba(255, 255, 255, 0.28);
}

/* Tab Navigation */
.s360-nav-tabs {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 0 1.25rem;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    overflow-x: auto;
}
.s360-tab-btn {
    background: transparent;
    border: none;
    border-bottom: 3px solid transparent;
    padding: 0.8rem 0.95rem;
    font-size: 0.81rem;
    font-weight: 700;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.15s ease;
}
.s360-tab-btn:hover {
    color: #1e40af;
    background: rgba(37, 99, 235, 0.04);
}
.s360-tab-btn.active {
    color: #2563eb;
    border-bottom-color: #2563eb;
    background: rgba(37, 99, 235, 0.06);
}

/* Body & Cards */
.s360-body {
    padding: 1.35rem 1.5rem;
    max-height: 68vh;
    overflow-y: auto;
    background: #f8fafc;
}
.s360-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.1rem 1.25rem;
    height: 100%;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
}
.s360-card h6 {
    font-size: 0.82rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #334155;
    margin-bottom: 0.9rem;
    display: flex;
    align-items: center;
    gap: 7px;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 0.6rem;
}
.s360-dl {
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.55rem;
}
.s360-dl > div {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    gap: 12px;
    font-size: 0.83rem;
    padding-bottom: 0.45rem;
    border-bottom: 1px dashed #f1f5f9;
}
.s360-dl > div:last-child {
    border-bottom: none;
    padding-bottom: 0;
}
.s360-dl dt {
    color: #64748b;
    font-weight: 600;
    flex-shrink: 0;
}
.s360-dl dd {
    margin: 0;
    color: #0f172a;
    font-weight: 700;
    text-align: right;
}

/* Guardian Cards */
.s360-guardian-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1rem 1.15rem;
    height: 100%;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
.s360-guardian-card:hover {
    border-color: #93c5fd;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.07);
}
.s360-guardian-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    margin-bottom: 6px;
}
.s360-guardian-role {
    font-size: 0.72rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #2563eb;
}
.s360-guardian-name {
    font-size: 0.98rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 6px;
}
.s360-guardian-detail {
    font-size: 0.8rem;
    color: #475569;
    margin-bottom: 3px;
    display: flex;
    align-items: center;
    gap: 7px;
}

/* Term Grade Accordion / Cards */
.s360-term-block {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 11px;
    margin-bottom: 0.85rem;
    overflow: hidden;
}
.s360-term-head {
    background: #f8fafc;
    padding: 0.7rem 1rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
}
.s360-term-title {
    font-size: 0.84rem;
    font-weight: 800;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
}
.s360-grade-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.79rem;
}
.s360-grade-table th {
    background: #f1f5f9;
    color: #475569;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.69rem;
    letter-spacing: 0.04em;
    padding: 7px 12px;
    text-align: left;
}
.s360-grade-table td {
    padding: 7px 12px;
    border-top: 1px solid #f1f5f9;
    color: #1e293b;
}

/* Document Checklist Grid */
.s360-doc-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 10px;
}
.s360-doc-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 0.85rem 1rem;
    display: flex;
    align-items: center;
    gap: 12px;
}
.s360-doc-icon {
    width: 36px;
    height: 36px;
    border-radius: 9px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    flex-shrink: 0;
}
.s360-doc-icon.uploaded {
    background: #dcfce7;
    color: #15803d;
}
.s360-doc-icon.missing {
    background: #fee2e2;
    color: #dc2626;
}
.s360-doc-info {
    flex: 1;
    min-width: 0;
}
.s360-doc-title {
    font-size: 0.82rem;
    font-weight: 700;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.s360-doc-sub {
    font-size: 0.72rem;
}

/* Timeline list */
.s360-timeline {
    position: relative;
    padding-left: 1.5rem;
    margin: 0;
    list-style: none;
}
.s360-timeline::before {
    content: '';
    position: absolute;
    left: 7px;
    top: 6px;
    bottom: 6px;
    width: 2px;
    background: #cbd5e1;
}
.s360-timeline-item {
    position: relative;
    padding-bottom: 1rem;
}
.s360-timeline-item:last-child {
    padding-bottom: 0;
}
.s360-timeline-dot {
    position: absolute;
    left: -1.5rem;
    top: 4px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #2563eb;
    border: 3px solid #dbeafe;
}
.s360-timeline-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 0.75rem 1rem;
}

/* Standing Pills */
.standing-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 0.74rem;
    font-weight: 700;
}
.standing-pill.summa { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
.standing-pill.magna { background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }
.standing-pill.cumlaude { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
.standing-pill.good { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.standing-pill.warning { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

.s360-footer {
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    padding: 0.85rem 1.5rem;
}

/* ==========================================================================
   DARK MODE OVERRIDES ([data-theme="dark"])
   ========================================================================== */
[data-theme="dark"] #studentProfileModal .modal-content {
    background: #1e293b;
    border-color: #334155;
}
[data-theme="dark"] .s360-nav-tabs {
    background: #0f172a;
    border-bottom-color: #334155;
}
[data-theme="dark"] .s360-tab-btn {
    color: #94a3b8;
}
[data-theme="dark"] .s360-tab-btn:hover {
    color: #60a5fa;
    background: rgba(59, 130, 246, 0.08);
}
[data-theme="dark"] .s360-tab-btn.active {
    color: #60a5fa;
    border-bottom-color: #3b82f6;
    background: rgba(59, 130, 246, 0.14);
}
[data-theme="dark"] .s360-body {
    background: #0f172a;
}
[data-theme="dark"] .s360-card,
[data-theme="dark"] .s360-guardian-card,
[data-theme="dark"] .s360-term-block,
[data-theme="dark"] .s360-doc-card,
[data-theme="dark"] .s360-timeline-card {
    background: #1e293b;
    border-color: #334155;
    color: #f1f5f9;
}
[data-theme="dark"] .s360-card h6 {
    color: #e2e8f0;
    border-bottom-color: #334155;
}
[data-theme="dark"] .s360-dl > div {
    border-bottom-color: #334155;
}
[data-theme="dark"] .s360-dl dt {
    color: #94a3b8;
}
[data-theme="dark"] .s360-dl dd,
[data-theme="dark"] .s360-guardian-name,
[data-theme="dark"] .s360-doc-title,
[data-theme="dark"] .s360-term-title {
    color: #f8fafc;
}
[data-theme="dark"] .s360-guardian-detail {
    color: #cbd5e1;
}
[data-theme="dark"] .s360-term-head {
    background: #0f172a;
    border-bottom-color: #334155;
}
[data-theme="dark"] .s360-grade-table th {
    background: #0f172a;
    color: #94a3b8;
}
[data-theme="dark"] .s360-grade-table td {
    border-top-color: #334155;
    color: #e2e8f0;
}
[data-theme="dark"] .s360-footer {
    background: #1e293b;
    border-top-color: #334155;
}
[data-theme="dark"] .s360-timeline::before {
    background: #334155;
}
[data-theme="dark"] .s360-doc-icon.uploaded {
    background: rgba(22, 163, 74, 0.2);
    color: #4ade80;
}
[data-theme="dark"] .s360-doc-icon.missing {
    background: rgba(220, 38, 38, 0.2);
    color: #f87171;
}
</style>

<!-- Unified Student 360° Dossier Modal / Slide-Over Drawer -->
<div class="modal fade" id="studentProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered student-360-dialog">
        <div class="modal-content">
            <!-- Header -->
            <div class="s360-header">
                <div class="s360-header-main">
                    <span class="s360-avatar" id="s360Avatar">ST</span>
                    <div class="s360-header-info">
                        <div class="s360-chips">
                            <span class="s360-chip s360-chip-num"><i class="fas fa-id-card"></i> <span id="s360Number">—</span></span>
                            <span class="s360-chip bg-success text-white" id="s360StatusBadge">Active</span>
                            <span class="s360-chip s360-chip-sec" id="s360Section">—</span>
                            <span class="s360-chip s360-chip-sec" id="s360HonorsChip" style="display:none;"></span>
                        </div>
                        <h3 class="s360-name" id="s360Name">Student Name</h3>
                        <div class="s360-program"><i class="fas fa-graduation-cap me-1"></i> <span id="s360Program">—</span></div>
                    </div>
                </div>
                <div class="s360-header-actions">
                    <button type="button" class="s360-mode-btn" onclick="toggleS360LayoutMode()" id="s360ModeToggleBtn" title="Switch between Centered Modal and Slide-Over Drawer">
                        <i class="fas fa-columns me-1"></i> <span id="s360ModeToggleLabel">Drawer View</span>
                    </button>
                    <button type="button" class="s360-mode-btn" onclick="printStudentDossier()" title="Print Official 360° Student Dossier">
                        <i class="fas fa-print me-1"></i> Print Dossier
                    </button>
                    <button type="button" class="btn-close btn-close-white ms-1" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <!-- 5-Tab Navigation Bar -->
            <div class="s360-nav-tabs">
                <button type="button" class="s360-tab-btn active" data-s360-tab="bio" onclick="switchS360Tab('bio')">
                    <i class="fas fa-user"></i> Bio &amp; Enrollment
                </button>
                <button type="button" class="s360-tab-btn" data-s360-tab="guardians" onclick="switchS360Tab('guardians')">
                    <i class="fas fa-user-friends"></i> Guardians &amp; Emergency <span class="badge bg-secondary rounded-pill" id="s360GuardianBadge">0</span>
                </button>
                <button type="button" class="s360-tab-btn" data-s360-tab="academics" onclick="switchS360Tab('academics')">
                    <i class="fas fa-award"></i> Grades &amp; Latin Honors <span class="badge bg-primary rounded-pill" id="s360GwaBadge">—</span>
                </button>
                <button type="button" class="s360-tab-btn" data-s360-tab="docs" onclick="switchS360Tab('docs')">
                    <i class="fas fa-folder-open"></i> Document Vault <span class="badge bg-secondary rounded-pill" id="s360DocBadge">0/5</span>
                </button>
                <button type="button" class="s360-tab-btn" data-s360-tab="timeline" onclick="switchS360Tab('timeline')">
                    <i class="fas fa-history"></i> Status &amp; Clearance Timeline <span class="badge bg-secondary rounded-pill" id="s360TimelineBadge">0</span>
                </button>
            </div>

            <!-- Modal Body (Tab Panes) -->
            <div class="modal-body s360-body">
                <!-- TAB 1: BIO & ENROLLMENT PROFILE -->
                <div id="s360TabBio" class="s360-tab-pane">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="s360-card">
                                <h6><i class="fas fa-id-badge text-primary"></i> Personal Information</h6>
                                <dl class="s360-dl">
                                    <div><dt>Full Name:</dt><dd id="s360FullName">—</dd></div>
                                    <div><dt>Date of Birth:</dt><dd id="s360Dob">—</dd></div>
                                    <div><dt>Gender:</dt><dd id="s360Gender">—</dd></div>
                                    <div><dt>Nationality:</dt><dd id="s360Nationality">—</dd></div>
                                    <div><dt>PSA / Birth Cert No:</dt><dd id="s360BirthCertNo">—</dd></div>
                                    <div><dt>Contact Number:</dt><dd id="s360ContactNo">—</dd></div>
                                    <div><dt>Email Address:</dt><dd id="s360Email">—</dd></div>
                                    <div><dt>Home Address:</dt><dd id="s360Address">—</dd></div>
                                </dl>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="s360-card">
                                <h6><i class="fas fa-graduation-cap text-success"></i> Academic &amp; Institutional Standing</h6>
                                <dl class="s360-dl">
                                    <div><dt>Student Number:</dt><dd id="s360StudentNoDetail">—</dd></div>
                                    <div><dt>Degree Program:</dt><dd id="s360AcademicProgram">—</dd></div>
                                    <div><dt>Year &amp; Section:</dt><dd id="s360YearSectionFull">—</dd></div>
                                    <div><dt>College Department:</dt><dd id="s360Department">—</dd></div>
                                    <div><dt>Enrollment Date:</dt><dd id="s360EnrollmentDate">—</dd></div>
                                    <div><dt>Residency Status:</dt><dd id="s360StatusDetail">—</dd></div>
                                    <div><dt>Cumulative GWA:</dt><dd id="s360BioGwa">—</dd></div>
                                    <div><dt>Document Compliance:</dt><dd id="s360BioDocCompliance">—</dd></div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: GUARDIANS & EMERGENCY CONTACTS (WITH INLINE ADD/EDIT) -->
                <div id="s360TabGuardians" class="s360-tab-pane" style="display:none;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <div>
                            <h6 class="mb-0 fw-bold"><i class="fas fa-shield-alt text-primary me-1"></i> Parent, Guardian &amp; Emergency Contact Directory</h6>
                            <span class="text-muted small">Manage family contacts directly inside this dossier or open the full directory.</span>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-primary" onclick="s360ToggleGuardianForm()">
                                <i class="fas fa-plus me-1"></i> Add Contact
                            </button>
                            <a href="javascript:void(0)" id="s360ManageGuardiansLink" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-external-link-alt me-1"></i> Full Directory
                            </a>
                        </div>
                    </div>

                    <!-- Inline Collapsible Add/Edit Guardian Form -->
                    <div id="s360GuardianFormWrap" class="s360-card mb-3" style="display:none; border-left: 4px solid #2563eb;">
                        <h6 id="s360GuardianFormTitle"><i class="fas fa-user-plus text-primary"></i> Add Guardian / Emergency Contact</h6>
                        <form id="s360GuardianForm" onsubmit="return s360SaveGuardian(event)">
                            <input type="hidden" id="s360GId" value="">
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <label class="form-label small fw-bold mb-1">Full Name *</label>
                                    <input type="text" id="s360GName" class="form-control form-control-sm" required placeholder="e.g. Maria C. Santos">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold mb-1">Relationship *</label>
                                    <select id="s360GRelationship" class="form-select form-select-sm" required>
                                        <option value="Mother">Mother</option>
                                        <option value="Father">Father</option>
                                        <option value="Legal Guardian">Legal Guardian</option>
                                        <option value="Spouse">Spouse</option>
                                        <option value="Sibling">Sibling</option>
                                        <option value="Relative">Relative</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold mb-1">Contact Number *</label>
                                    <input type="text" id="s360GContact" class="form-control form-control-sm" required placeholder="e.g. 0917-123-4567">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small fw-bold mb-1">Email Address</label>
                                    <input type="email" id="s360GEmail" class="form-control form-control-sm" placeholder="optional@email.com">
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label small fw-bold mb-1">Residential Address</label>
                                    <input type="text" id="s360GAddress" class="form-control form-control-sm" placeholder="Street, Barangay, City/Municipality">
                                </div>
                                <div class="col-12 d-flex flex-wrap justify-content-between align-items-center pt-2">
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="s360GPrimary" checked>
                                            <label class="form-check-label small fw-semibold" for="s360GPrimary">Primary Guardian</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="s360GEmergency" checked>
                                            <label class="form-check-label small fw-semibold text-danger" for="s360GEmergency">In Case of Emergency (ICE)</label>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-light border" onclick="s360ToggleGuardianForm(false)">Cancel</button>
                                        <button type="submit" class="btn btn-sm btn-primary" id="s360GSaveBtn"><i class="fas fa-save me-1"></i> Save Contact</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div id="s360GuardiansContainer" class="row g-3">
                        <!-- Populated dynamically via JS -->
                    </div>
                </div>

                <!-- TAB 3: COLLEGIATE GRADES, TRANSCRIPT & LATIN HONORS -->
                <div id="s360TabAcademics" class="s360-tab-pane" style="display:none;">
                    <!-- Top Scholastic Summary Strip -->
                    <div class="s360-card mb-3">
                        <div class="row align-items-center g-3">
                            <div class="col-md-3 text-center border-end">
                                <span class="text-muted small text-uppercase fw-bold d-block">Cumulative GWA</span>
                                <div class="fs-2 fw-bold text-primary my-1" id="s360GwaVal">—</div>
                                <span class="standing-pill good" id="s360StandingPill">Good Standing</span>
                            </div>
                            <div class="col-md-3 text-center border-end">
                                <span class="text-muted small text-uppercase fw-bold d-block">Units Completed</span>
                                <div class="fs-4 fw-bold mt-1" id="s360UnitsVal">0.0 Units</div>
                                <small class="text-muted" id="s360TermsVal">0 Term(s) Evaluated</small>
                            </div>
                            <div class="col-md-3 text-center border-end">
                                <span class="text-muted small text-uppercase fw-bold d-block">Latin Honors Track</span>
                                <div class="fw-bold mt-1" id="s360LatinTrackText" style="font-size:0.86rem;">CHED Collegiate Standard</div>
                                <small class="text-muted d-block">Summa ≤1.20 · Magna ≤1.45 · Cum Laude ≤1.75</small>
                            </div>
                            <div class="col-md-3 text-center">
                                <span class="text-muted small text-uppercase fw-bold d-block">Academic Actions</span>
                                <div class="d-flex flex-column gap-1 mt-2 px-2">
                                    <a href="javascript:void(0)" id="s360OpenAcademicHistoryLink" class="btn btn-sm btn-primary">
                                        <i class="fas fa-graduation-cap me-1"></i> Manage Grades
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Term-by-Term Collegiate Grade Tables -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 fw-bold small text-uppercase text-muted"><i class="fas fa-list-ol text-primary me-1"></i> Term-by-Term Collegiate Transcript</h6>
                        <span class="small text-muted" id="s360TranscriptSubCount">0 subjects recorded</span>
                    </div>
                    <div id="s360TranscriptTermsContainer" class="mb-3">
                        <!-- Populated dynamically via JS -->
                    </div>

                    <!-- Previous Educational Background -->
                    <div class="s360-card">
                        <h6><i class="fas fa-school text-warning"></i> Prior Educational Background (Secondary / Transfer History)</h6>
                        <div id="s360PriorSchoolsContainer">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>
                </div>

                <!-- TAB 4: DIGITAL DOCUMENT VAULT & REQUESTS -->
                <div id="s360TabDocs" class="s360-tab-pane" style="display:none;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <div>
                            <h6 class="mb-0 fw-bold"><i class="fas fa-folder-open text-primary me-1"></i> Institutional 5-Document Mandatory Vault</h6>
                            <span class="text-muted small">Form 137, Good Moral, PSA Birth Certificate, Form 138, and Official 2x2 Photo compliance</span>
                        </div>
                        <a href="javascript:void(0)" id="s360ManageDocsLink" class="btn btn-sm btn-primary">
                            <i class="fas fa-cloud-upload-alt me-1"></i> Upload / Manage Vault
                        </a>
                    </div>
                    <div id="s360DocsContainer" class="s360-doc-grid mb-4">
                        <!-- Populated dynamically via JS -->
                    </div>

                    <!-- Recent Document Requests -->
                    <div class="s360-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 border-0 pb-0"><i class="fas fa-file-signature text-info"></i> Official Document Requests &amp; Releases</h6>
                            <a href="document-request-workflow.php" class="btn btn-xs btn-outline-secondary" style="font-size:0.74rem;">View Queue</a>
                        </div>
                        <div id="s360DocRequestsContainer" class="mt-2">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>
                </div>

                <!-- TAB 5: STATUS TIMELINE & CLEARANCE HISTORY -->
                <div id="s360TabTimeline" class="s360-tab-pane" style="display:none;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <div>
                            <h6 class="mb-0 fw-bold"><i class="fas fa-user-clock text-primary me-1"></i> Academic Standing &amp; Residency Audit Timeline</h6>
                            <span class="text-muted small">Chronological ledger of enrollment status changes, LOA residency clocks, and board references.</span>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-primary" onclick="s360ToggleStatusForm()">
                                <i class="fas fa-exchange-alt me-1"></i> Log Status Change
                            </button>
                            <a href="student-status-tracker.php" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-external-link-alt me-1"></i> Status Tracker
                            </a>
                        </div>
                    </div>

                    <!-- Inline Quick Status Transition Form -->
                    <div id="s360StatusFormWrap" class="s360-card mb-3" style="display:none; border-left: 4px solid #7c3aed;">
                        <h6><i class="fas fa-exchange-alt text-primary"></i> Record Official Status Transition</h6>
                        <form id="s360StatusForm" onsubmit="return s360SaveStatusChange(event)">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold mb-1">New Standing *</label>
                                    <select id="s360NewStatus" class="form-select form-select-sm" required>
                                        <option value="Active">Active (Regular)</option>
                                        <option value="Irregular">Irregular</option>
                                        <option value="Leave of Absence">Leave of Absence (LOA)</option>
                                        <option value="Transferred">Transferred (Honorable Dismissal)</option>
                                        <option value="Graduated">Graduated</option>
                                        <option value="Inactive">Inactive</option>
                                        <option value="Dropout">Dropout</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold mb-1">Statutory Category *</label>
                                    <select id="s360ReasonCategory" class="form-select form-select-sm" required>
                                        <option value="Institutional / Administrative Adjustment">Institutional / Administrative Adjustment</option>
                                        <option value="Return from Approved Leave (Re-admission)">Return from Approved Leave (Re-admission)</option>
                                        <option value="Financial / Economic Hardship">Financial / Economic Hardship</option>
                                        <option value="Health / Medical Incapacity">Health / Medical Incapacity</option>
                                        <option value="Academic Deficiency / Prerequisite Failure">Academic Deficiency / Prerequisite Failure</option>
                                        <option value="Transfer to Another HEI (Honorable Dismissal)">Transfer to Another HEI (Honorable Dismissal)</option>
                                        <option value="Completion of All Curricular Requirements">Completion of All Curricular Requirements</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold mb-1">Reference / Memo No.</label>
                                    <input type="text" id="s360StatusRefNo" class="form-control form-control-sm" placeholder="e.g. REG-MEMO-2026-04">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label small fw-bold mb-1">Official Justification / Remarks *</label>
                                    <input type="text" id="s360StatusReason" class="form-control form-control-sm" required placeholder="Specific registrar justification for audit trail">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold mb-1">Effective Term</label>
                                    <input type="text" id="s360EffectiveTerm" class="form-control form-control-sm" placeholder="e.g. 1st Sem AY 2026-2027">
                                </div>
                                <div class="col-12 d-flex justify-content-end gap-2 pt-2">
                                    <button type="button" class="btn btn-sm btn-light border" onclick="s360ToggleStatusForm(false)">Cancel</button>
                                    <button type="submit" class="btn btn-sm btn-primary" id="s360StatusSaveBtn"><i class="fas fa-check-circle me-1"></i> Commit Transition</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div id="s360TimelineContainer">
                        <!-- Populated dynamically via JS -->
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer s360-footer">
                <div class="d-flex flex-wrap justify-content-between w-100 align-items-center gap-2">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="javascript:void(0)" id="s360PrintIdBtn" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-id-card me-1"></i> Student ID Card
                        </a>
                        <a href="javascript:void(0)" id="s360TorBtn" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-scroll me-1"></i> Generate TOR / Grades
                        </a>
                        <button type="button" id="s360EditBtn" class="btn btn-sm btn-outline-primary" style="display:none;">
                            <i class="fas fa-pen me-1"></i> Edit Master Profile
                        </button>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
window.S360_API_BASE = '<?php echo BASE_URL; ?>/modules/registrar/api';
window.S360_CSRF     = '<?php echo e(csrfToken()); ?>';
window.currentS360Student = null;
window.s360CurrentTab = 'bio';

function s360Esc(str) {
    if (str === null || str === undefined) return '';
    const d = document.createElement('div');
    d.textContent = String(str);
    return d.innerHTML;
}

function toggleS360LayoutMode() {
    const modalEl = document.getElementById('studentProfileModal');
    if (!modalEl) return;
    const isDrawer = modalEl.classList.toggle('s360-drawer-mode');
    const lbl = document.getElementById('s360ModeToggleLabel');
    if (lbl) lbl.textContent = isDrawer ? 'Modal View' : 'Drawer View';
    try { localStorage.setItem('s360_layout_mode', isDrawer ? 'drawer' : 'modal'); } catch (_) {}
}

// Restore preferred mode on load
document.addEventListener('DOMContentLoaded', function() {
    try {
        if (localStorage.getItem('s360_layout_mode') === 'drawer') {
            const modalEl = document.getElementById('studentProfileModal');
            if (modalEl) {
                modalEl.classList.add('s360-drawer-mode');
                const lbl = document.getElementById('s360ModeToggleLabel');
                if (lbl) lbl.textContent = 'Modal View';
            }
        }
    } catch (_) {}
});

function switchS360Tab(tabKey) {
    window.s360CurrentTab = tabKey || 'bio';
    document.querySelectorAll('#studentProfileModal .s360-tab-pane').forEach(p => p.style.display = 'none');
    document.querySelectorAll('#studentProfileModal .s360-tab-btn').forEach(b => b.classList.remove('active'));

    const map = {
        bio: 's360TabBio',
        guardians: 's360TabGuardians',
        academics: 's360TabAcademics',
        docs: 's360TabDocs',
        timeline: 's360TabTimeline',
        health: 's360TabTimeline'
    };
    const paneId = map[tabKey] || 's360TabBio';
    const targetPane = document.getElementById(paneId);
    if (targetPane) targetPane.style.display = '';

    const normalizedKey = (tabKey === 'health') ? 'timeline' : tabKey;
    const activeBtn = document.querySelector('#studentProfileModal .s360-tab-btn[data-s360-tab="' + normalizedKey + '"]');
    if (activeBtn) activeBtn.classList.add('active');
}

async function viewStudent(studentId, initialTab = 'bio') {
    if (!studentId) return;
    try {
        const response = await fetch(window.S360_API_BASE + '/students.php?action=get', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id: parseInt(studentId, 10)})
        });

        const result = await response.json();
        if (!result.success || !result.data) {
            if (typeof showRegError === 'function') showRegError(result.error || 'Student record not found');
            else alert(result.error || 'Student record not found');
            return;
        }

        const s = result.data;
        window.currentS360Student = s;
        renderS360Dossier(s);

        switchS360Tab(initialTab || 'bio');
        if (typeof showRegModal === 'function') {
            showRegModal('studentProfileModal');
        } else if (window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(document.getElementById('studentProfileModal')).show();
        }
    } catch (err) {
        console.error(err);
        if (typeof showRegError === 'function') showRegError('Failed to load student 360 dossier: ' + err.message);
    }
}
window.openStudentDossier = viewStudent;

function renderS360Dossier(s) {
    const fullName = [s.first_name, s.middle_name, s.last_name, s.suffix].filter(Boolean).join(' ');
    const initials = ((s.first_name?.[0] || 'S') + (s.last_name?.[0] || 'T')).toUpperCase();

    // Header
    document.getElementById('s360Avatar').textContent = initials;
    document.getElementById('s360Name').textContent = fullName || 'Student Profile';
    document.getElementById('s360Number').textContent = s.student_number || '—';
    document.getElementById('s360Section').textContent = s.year_section || 'Unassigned Section';
    document.getElementById('s360Program').textContent = s.program_course || 'No Program Assigned';

    const statusBadge = document.getElementById('s360StatusBadge');
    const st = s.status || 'Active';
    statusBadge.textContent = st;
    let stColor = 'bg-secondary text-white';
    if (st === 'Active') stColor = 'bg-success text-white';
    else if (st === 'Irregular' || st === 'Leave of Absence') stColor = 'bg-warning text-dark';
    else if (st === 'Graduated') stColor = 'bg-info text-dark';
    else if (st === 'Transferred' || st === 'Dropout' || st === 'Dismissed') stColor = 'bg-danger text-white';
    statusBadge.className = 's360-chip ' + stColor;

    // Scholastic summary & Honors chip in Header
    const gsum = s.grade_summary || {};
    const standing = gsum.academic_standing || 'No Records';
    const honorsChip = document.getElementById('s360HonorsChip');
    if (standing.includes('Cum Laude')) {
        honorsChip.style.display = 'inline-flex';
        honorsChip.className = 's360-chip bg-warning text-dark';
        honorsChip.innerHTML = '<i class="fas fa-medal"></i> ' + s360Esc(standing);
    } else {
        honorsChip.style.display = 'none';
    }
    document.getElementById('s360GwaBadge').textContent = gsum.cumulative_gwa_fmt && gsum.cumulative_gwa_fmt !== '—' ? gsum.cumulative_gwa_fmt : '—';

    // Tab 1: Bio & Enrollment
    document.getElementById('s360FullName').textContent = fullName || '—';
    document.getElementById('s360Dob').textContent = s.date_of_birth || '—';
    document.getElementById('s360Gender').textContent = s.gender || '—';
    document.getElementById('s360Nationality').textContent = s.nationality || 'Filipino';
    document.getElementById('s360BirthCertNo').textContent = s.birth_cert_no || s.lrn || '—';
    document.getElementById('s360ContactNo').textContent = s.contact_number || s.phone || (s.guardians?.[0]?.contact ? s.guardians[0].contact + ' (Guardian)' : '—');
    document.getElementById('s360Email').textContent = s.email || '—';
    document.getElementById('s360Address').textContent = s.address || s.home_address || (s.guardians?.[0]?.address || '—');

    document.getElementById('s360StudentNoDetail').textContent = s.student_number || '—';
    document.getElementById('s360AcademicProgram').textContent = s.program_course || '—';
    document.getElementById('s360YearSectionFull').textContent = s.year_section || '—';
    document.getElementById('s360Department').textContent = s.college_department || 'General Academic Studies';
    document.getElementById('s360EnrollmentDate').textContent = s.enrollment_date || (s.created_at ? s.created_at.substring(0, 10) : '—');
    document.getElementById('s360StatusDetail').textContent = st;
    document.getElementById('s360BioGwa').textContent = (gsum.cumulative_gwa_fmt || '—') + (standing !== 'No Records' ? ' (' + standing + ')' : '');

    // Tab 2: Guardians
    const guardians = s.guardians || [];
    document.getElementById('s360GuardianBadge').textContent = guardians.length;
    document.getElementById('s360ManageGuardiansLink').href = 'guardian-emergency-contact.php?student_id=' + s.id;
    s360ToggleGuardianForm(false);

    const gContainer = document.getElementById('s360GuardiansContainer');
    if (guardians.length === 0) {
        gContainer.innerHTML = `
            <div class="col-12 text-center text-muted py-4">
                <i class="fas fa-user-friends fa-2x mb-2 text-secondary"></i>
                <div class="fw-bold">No Guardian or Emergency Contact on File</div>
                <div class="small mb-2">Click "+ Add Contact" above to register a parent or emergency contact right now.</div>
            </div>`;
    } else {
        gContainer.innerHTML = guardians.map(g => `
            <div class="col-md-6">
                <div class="s360-guardian-card">
                    <div class="s360-guardian-head">
                        <div class="d-flex align-items-center gap-1 flex-wrap">
                            <span class="s360-guardian-role">${s360Esc(g.relationship || 'Guardian')}</span>
                            ${parseInt(g.is_primary, 10) ? '<span class="badge bg-primary text-white" style="font-size:0.65rem;">Primary</span>' : ''}
                            ${parseInt(g.is_emergency, 10) ? '<span class="badge bg-danger text-white" style="font-size:0.65rem;">Emergency (ICE)</span>' : ''}
                        </div>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-light border py-0 px-2" onclick='s360EditGuardian(${JSON.stringify(g).replace(/'/g, "&#39;")})' title="Edit Contact">
                                <i class="fas fa-pen text-primary" style="font-size:0.7rem;"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-light border py-0 px-2" onclick="s360DeleteGuardian(${parseInt(g.id, 10)})" title="Delete Contact">
                                <i class="fas fa-trash text-danger" style="font-size:0.7rem;"></i>
                            </button>
                        </div>
                    </div>
                    <div class="s360-guardian-name">${s360Esc(g.full_name || '—')}</div>
                    <div class="s360-guardian-detail"><i class="fas fa-phone text-primary"></i> ${s360Esc(g.contact || 'No phone recorded')}</div>
                    <div class="s360-guardian-detail"><i class="fas fa-envelope text-muted"></i> ${s360Esc(g.email || 'No email recorded')}</div>
                    ${g.address ? `<div class="s360-guardian-detail"><i class="fas fa-map-marker-alt text-danger"></i> ${s360Esc(g.address)}</div>` : ''}
                </div>
            </div>
        `).join('');
    }

    // Tab 3: Collegiate Grades, Transcript & Latin Honors
    document.getElementById('s360GwaVal').textContent = gsum.cumulative_gwa_fmt || '—';
    document.getElementById('s360UnitsVal').textContent = (gsum.total_units_passed || 0) + ' / ' + (gsum.total_units_enrolled || 0) + ' Units';
    document.getElementById('s360TermsVal').textContent = (gsum.terms_count || 0) + ' Term(s) · ' + (gsum.total_subjects || 0) + ' Subjects';
    document.getElementById('s360TranscriptSubCount').textContent = (gsum.total_subjects || 0) + ' total subjects across ' + (gsum.terms_count || 0) + ' term(s)';

    const standingPill = document.getElementById('s360StandingPill');
    standingPill.textContent = standing;
    let standingClass = 'good';
    if (standing === 'Summa Cum Laude') standingClass = 'summa';
    else if (standing === 'Magna Cum Laude') standingClass = 'magna';
    else if (standing === 'Cum Laude') standingClass = 'cumlaude';
    else if (standing === 'Academic Warning') standingClass = 'warning';
    standingPill.className = 'standing-pill ' + standingClass;

    document.getElementById('s360OpenAcademicHistoryLink').href = 'academic-history.php?student_id=' + s.id;

    // Render Term-by-Term Transcript
    const terms = s.grade_terms || [];
    const termsBox = document.getElementById('s360TranscriptTermsContainer');
    if (terms.length === 0) {
        termsBox.innerHTML = `
            <div class="s360-card text-center text-muted py-4">
                <i class="fas fa-book-open fa-2x mb-2 text-secondary"></i>
                <div class="fw-bold">No Collegiate Subject Grades Recorded Yet</div>
                <div class="small">Open "Manage Grades" to encode semester courses and compute GWA.</div>
            </div>`;
    } else {
        termsBox.innerHTML = terms.map((tm, idx) => {
            const tStats = tm.stats || {};
            const subjs = tm.subjects || [];
            const rowsHtml = subjs.map(sub => {
                const stBadge = (sub.status === 'Passed')
                    ? '<span class="badge bg-success-subtle text-success border border-success-subtle">Passed</span>'
                    : (sub.status === 'Failed'
                        ? '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Failed</span>'
                        : '<span class="badge bg-secondary-subtle text-secondary border">' + s360Esc(sub.status || 'Enrolled') + '</span>');
                return `
                    <tr>
                        <td class="fw-bold text-primary">${s360Esc(sub.subject_code)}</td>
                        <td>${s360Esc(sub.subject_name)}</td>
                        <td class="text-center">${parseFloat(sub.units || 0).toFixed(1)}</td>
                        <td class="fw-bold text-center">${s360Esc(sub.grade || '—')}</td>
                        <td class="text-end">${stBadge}</td>
                    </tr>`;
            }).join('');

            return `
                <div class="s360-term-block">
                    <div class="s360-term-head" onclick="const b=this.nextElementSibling; b.style.display = b.style.display==='none' ? '' : 'none';">
                        <div class="s360-term-title">
                            <i class="fas fa-calendar-alt text-primary"></i>
                            <span>${s360Esc(tm.year_level)} &bull; ${s360Esc(tm.term_display)} <span class="text-muted fw-normal">(AY ${s360Esc(tm.academic_year)})</span></span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-dark border">${subjs.length} Courses · ${tStats.passed_units || 0} Units</span>
                            <span class="badge bg-primary">Term GWA: ${s360Esc(tStats.gwa_formatted || '—')}</span>
                            <i class="fas fa-chevron-down text-muted small"></i>
                        </div>
                    </div>
                    <div style="display:${idx === terms.length - 1 || terms.length <= 2 ? '' : 'none'};">
                        <table class="s360-grade-table">
                            <thead>
                                <tr>
                                    <th style="width:18%;">Course Code</th>
                                    <th style="width:44%;">Descriptive Title</th>
                                    <th style="width:12%;text-align:center;">Units</th>
                                    <th style="width:13%;text-align:center;">Final Grade</th>
                                    <th style="width:13%;text-align:right;">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>${rowsHtml}</tbody>
                        </table>
                    </div>
                </div>`;
        }).join('');
    }

    // Prior Educational Background
    const prior = s.academic_history || [];
    const priorBox = document.getElementById('s360PriorSchoolsContainer');
    if (prior.length === 0) {
        priorBox.innerHTML = '<div class="text-muted small py-2">No prior secondary or transfer school records listed.</div>';
    } else {
        priorBox.innerHTML = `<div class="table-responsive"><table class="s360-grade-table">
            <thead><tr><th>Level</th><th>School / Institution</th><th>Inclusive Years</th><th>Honors / Remarks</th></tr></thead>
            <tbody>` + prior.map(p => `
                <tr>
                    <td class="fw-bold">${s360Esc(p.level || 'Secondary')}</td>
                    <td>${s360Esc(p.school_name || '—')}</td>
                    <td>${s360Esc((p.from_year || '') + (p.to_year ? ' - ' + p.to_year : ''))}</td>
                    <td>${s360Esc(p.honors_awards || p.remarks || 'Graduated')}</td>
                </tr>`).join('') + `</tbody></table></div>`;
    }

    // Tab 4: Digital Document Vault & Requests
    const docs = s.digital_docs || {};
    const docKeys = Object.keys(docs);
    let uploadedCount = 0;
    docKeys.forEach(k => { if (docs[k]?.is_uploaded) uploadedCount++; });
    const totalDocs = docKeys.length || 5;
    document.getElementById('s360DocBadge').textContent = uploadedCount + '/' + totalDocs;
    document.getElementById('s360BioDocCompliance').innerHTML = uploadedCount === totalDocs
        ? `<span class="text-success"><i class="fas fa-check-circle me-1"></i>Complete (${uploadedCount}/${totalDocs})</span>`
        : `<span class="text-warning"><i class="fas fa-exclamation-circle me-1"></i>${uploadedCount} of ${totalDocs} Submitted</span>`;
    document.getElementById('s360ManageDocsLink').href = 'digital-file-storage.php?student_id=' + s.id;

    const docContainer = document.getElementById('s360DocsContainer');
    if (docKeys.length === 0) {
        docContainer.innerHTML = '<div class="text-center text-muted py-4">No document requirements configured.</div>';
    } else {
        docContainer.innerHTML = docKeys.map(k => {
            const item = docs[k];
            const isUp = item?.is_uploaded;
            const title = item?.type?.name || k;
            return `
                <div class="s360-doc-card">
                    <div class="s360-doc-icon ${isUp ? 'uploaded' : 'missing'}">
                        <i class="fas ${isUp ? 'fa-check' : 'fa-times'}"></i>
                    </div>
                    <div class="s360-doc-info">
                        <div class="s360-doc-title" title="${s360Esc(title)}">${s360Esc(title)}</div>
                        <div class="s360-doc-sub">${isUp ? '<span class="text-success fw-bold">Submitted &amp; Verified</span>' : '<span class="text-danger fw-bold">Missing Requirement</span>'}</div>
                    </div>
                    ${isUp ? `
                        <div class="d-flex gap-1">
                            <a href="${window.S360_API_BASE}/storage.php?action=download&id=${item.file.id}" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2" title="View / Download Document">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>` : ''}
                </div>
            `;
        }).join('');
    }

    const reqs = s.doc_requests || [];
    const reqBox = document.getElementById('s360DocRequestsContainer');
    if (reqs.length === 0) {
        reqBox.innerHTML = '<div class="text-muted small py-2">No active or historical document requests for this student.</div>';
    } else {
        reqBox.innerHTML = `<div class="table-responsive"><table class="s360-grade-table">
            <thead><tr><th>Request #</th><th>Document Type</th><th>Purpose</th><th>Date</th><th>Status</th></tr></thead>
            <tbody>` + reqs.map(r => `
                <tr>
                    <td class="fw-bold text-primary">${s360Esc(r.request_number || '#' + r.id)}</td>
                    <td class="fw-semibold">${s360Esc(r.document_type)}</td>
                    <td>${s360Esc(r.purpose || '—')}</td>
                    <td>${s360Esc((r.requested_at || '').substring(0, 10))}</td>
                    <td><span class="badge bg-secondary">${s360Esc(r.status)}</span></td>
                </tr>`).join('') + `</tbody></table></div>`;
    }

    // Tab 5: Status & Clearance Timeline
    const hist = s.status_history || [];
    document.getElementById('s360TimelineBadge').textContent = hist.length;
    s360ToggleStatusForm(false);
    document.getElementById('s360NewStatus').value = s.status || 'Active';

    const tlBox = document.getElementById('s360TimelineContainer');
    if (hist.length === 0) {
        tlBox.innerHTML = `
            <div class="s360-card text-center py-4">
                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                <div class="fw-bold">Continuous Standing: ${s360Esc(st)}</div>
                <div class="small text-muted">No status changes, LOAs, or disciplinary transitions have been logged since initial enrollment.</div>
            </div>`;
    } else {
        tlBox.innerHTML = `<ul class="s360-timeline">` + hist.map(h => `
            <li class="s360-timeline-item">
                <span class="s360-timeline-dot"></span>
                <div class="s360-timeline-card">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-1">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-secondary">${s360Esc(h.changed_from)}</span>
                            <i class="fas fa-arrow-right text-muted small"></i>
                            <span class="badge bg-primary">${s360Esc(h.changed_to)}</span>
                            ${h.ref_no ? `<span class="badge bg-light text-dark border"><i class="fas fa-hashtag me-1"></i>${s360Esc(h.ref_no)}</span>` : ''}
                        </div>
                        <span class="text-muted small"><i class="far fa-clock me-1"></i>${s360Esc(h.changed_at)}</span>
                    </div>
                    <div class="fw-bold small text-primary mb-1">${s360Esc(h.reason_category || 'Official Status Transition')} ${h.effective_term ? '· ' + s360Esc(h.effective_term) : ''}</div>
                    <div class="small mb-1">${s360Esc(h.reason || '—')}</div>
                    <div class="text-muted" style="font-size:0.72rem;"><i class="fas fa-user-shield me-1"></i>Logged by: ${s360Esc(h.changed_by_name || 'Registrar Officer')}</div>
                </div>
            </li>`).join('') + `</ul>`;
    }

    // Footer links
    document.getElementById('s360PrintIdBtn').href = 'student-id-generation.php?student_id=' + s.id;
    document.getElementById('s360TorBtn').href = 'academic-history.php?student_id=' + s.id;

    const editBtn = document.getElementById('s360EditBtn');
    if (typeof openEditModal === 'function') {
        editBtn.style.display = 'inline-block';
        editBtn.onclick = function() {
            if (typeof hideRegModal === 'function') hideRegModal('studentProfileModal');
            setTimeout(() => openEditModal(s.id), 250);
        };
    } else {
        editBtn.style.display = 'none';
    }
}

/* ============ Inline Guardian Add / Edit / Delete inside Dossier ============ */
function s360ToggleGuardianForm(show) {
    const wrap = document.getElementById('s360GuardianFormWrap');
    const form = document.getElementById('s360GuardianForm');
    if (!wrap || !form) return;
    const shouldShow = (show !== undefined) ? show : (wrap.style.display === 'none');
    if (shouldShow) {
        form.reset();
        document.getElementById('s360GId').value = '';
        document.getElementById('s360GuardianFormTitle').innerHTML = '<i class="fas fa-user-plus text-primary"></i> Add Guardian / Emergency Contact';
        wrap.style.display = 'block';
    } else {
        wrap.style.display = 'none';
    }
}

function s360EditGuardian(g) {
    s360ToggleGuardianForm(true);
    document.getElementById('s360GuardianFormTitle').innerHTML = '<i class="fas fa-user-edit text-primary"></i> Edit Guardian / Emergency Contact';
    document.getElementById('s360GId').value = g.id || '';
    document.getElementById('s360GName').value = g.full_name || '';
    document.getElementById('s360GRelationship').value = g.relationship || 'Guardian';
    document.getElementById('s360GContact').value = g.contact || '';
    document.getElementById('s360GEmail').value = g.email || '';
    document.getElementById('s360GAddress').value = g.address || '';
    document.getElementById('s360GPrimary').checked = !!parseInt(g.is_primary, 10);
    document.getElementById('s360GEmergency').checked = !!parseInt(g.is_emergency, 10);
}

async function s360SaveGuardian(e) {
    e.preventDefault();
    if (!window.currentS360Student) return false;
    const payload = {
        id: document.getElementById('s360GId').value || undefined,
        student_id: window.currentS360Student.id,
        full_name: document.getElementById('s360GName').value.trim(),
        relationship: document.getElementById('s360GRelationship').value,
        contact: document.getElementById('s360GContact').value.trim(),
        email: document.getElementById('s360GEmail').value.trim(),
        address: document.getElementById('s360GAddress').value.trim(),
        is_primary: document.getElementById('s360GPrimary').checked ? 1 : 0,
        is_emergency: document.getElementById('s360GEmergency').checked ? 1 : 0
    };
    try {
        const res = await fetch(window.S360_API_BASE + '/guardians.php?action=save', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-Token': window.S360_CSRF},
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Failed to save guardian');
        if (typeof showRegSuccess === 'function') showRegSuccess('Guardian contact saved');
        await viewStudent(window.currentS360Student.id, 'guardians');
    } catch (err) {
        if (typeof showRegError === 'function') showRegError(err.message);
        else alert(err.message);
    }
    return false;
}

async function s360DeleteGuardian(guardianId) {
    if (!confirm('Remove this guardian/emergency contact record?')) return;
    try {
        const res = await fetch(window.S360_API_BASE + '/guardians.php?action=delete', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-Token': window.S360_CSRF},
            body: JSON.stringify({id: guardianId})
        });
        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Failed to delete guardian');
        if (typeof showRegSuccess === 'function') showRegSuccess('Guardian contact removed');
        await viewStudent(window.currentS360Student.id, 'guardians');
    } catch (err) {
        if (typeof showRegError === 'function') showRegError(err.message);
    }
}

/* ============ Inline Status Transition inside Dossier ============ */
function s360ToggleStatusForm(show) {
    const wrap = document.getElementById('s360StatusFormWrap');
    const form = document.getElementById('s360StatusForm');
    if (!wrap || !form) return;
    const shouldShow = (show !== undefined) ? show : (wrap.style.display === 'none');
    if (shouldShow) {
        form.reset();
        if (window.currentS360Student) {
            document.getElementById('s360NewStatus').value = window.currentS360Student.status || 'Active';
        }
        wrap.style.display = 'block';
    } else {
        wrap.style.display = 'none';
    }
}

async function s360SaveStatusChange(e) {
    e.preventDefault();
    if (!window.currentS360Student) return false;
    const payload = {
        student_id: window.currentS360Student.id,
        new_status: document.getElementById('s360NewStatus').value,
        reason_category: document.getElementById('s360ReasonCategory').value,
        ref_no: document.getElementById('s360StatusRefNo').value.trim(),
        reason: document.getElementById('s360StatusReason').value.trim(),
        effective_term: document.getElementById('s360EffectiveTerm').value.trim()
    };
    try {
        const res = await fetch(window.S360_API_BASE + '/status.php?action=change_status', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-Token': window.S360_CSRF},
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Failed to update status');
        if (typeof showRegSuccess === 'function') showRegSuccess('Student standing updated');
        await viewStudent(window.currentS360Student.id, 'timeline');
    } catch (err) {
        if (typeof showRegError === 'function') showRegError(err.message);
        else alert(err.message);
    }
    return false;
}

/* ============ Printable 360° Student Dossier Sheet ============ */
function printStudentDossier() {
    const s = window.currentS360Student;
    if (!s) return;
    const fullName = [s.first_name, s.middle_name, s.last_name, s.suffix].filter(Boolean).join(' ');
    const gsum = s.grade_summary || {};
    const guardians = s.guardians || [];
    const terms = s.grade_terms || [];

    const win = window.open('', '_blank', 'width=900,height=750');
    if (!win) return;

    const guardianRows = guardians.map(g => `
        <tr>
            <td>${s360Esc(g.full_name)}</td>
            <td>${s360Esc(g.relationship)}</td>
            <td>${s360Esc(g.contact || '—')}</td>
            <td>${s360Esc(g.address || '—')}</td>
        </tr>`).join('') || '<tr><td colspan="4">No guardian records on file.</td></tr>';

    const termBlocks = terms.map(tm => `
        <div style="margin-top:12px;">
            <div style="font-weight:700;background:#f1f5f9;padding:6px 10px;border:1px solid #cbd5e1;">
                ${s360Esc(tm.year_level)} - ${s360Esc(tm.term_display)} (AY ${s360Esc(tm.academic_year)}) &bull; Term GWA: ${s360Esc(tm.stats?.gwa_formatted || '—')}
            </div>
            <table>
                <thead><tr><th>Code</th><th>Course Title</th><th>Units</th><th>Grade</th><th>Status</th></tr></thead>
                <tbody>
                    ${(tm.subjects || []).map(sub => `
                        <tr>
                            <td>${s360Esc(sub.subject_code)}</td>
                            <td>${s360Esc(sub.subject_name)}</td>
                            <td>${s360Esc(sub.units)}</td>
                            <td><strong>${s360Esc(sub.grade || '—')}</strong></td>
                            <td>${s360Esc(sub.status)}</td>
                        </tr>`).join('')}
                </tbody>
            </table>
        </div>`).join('');

    win.document.write(`<!DOCTYPE html>
<html>
<head>
    <title>Student 360° Dossier - ${s360Esc(fullName)}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #0f172a; margin: 28px; font-size: 12px; }
        h1 { font-size: 18px; margin: 0 0 4px 0; color: #1e3a8a; }
        h2 { font-size: 13px; text-transform: uppercase; border-bottom: 2px solid #1e3a8a; padding-bottom: 4px; margin-top: 20px; color: #1e3a8a; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 24px; margin-top: 8px; }
        .row-item { display: flex; justify-content: space-between; border-bottom: 1px dashed #cbd5e1; padding: 4px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; font-size: 11px; }
        th, td { border: 1px solid #cbd5e1; padding: 5px 8px; text-align: left; }
        th { background: #f8fafc; }
    </style>
</head>
<body>
    <div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:3px solid #1e3a8a;padding-bottom:10px;">
        <div>
            <h1>OFFICIAL STUDENT 360° DOSSIER</h1>
            <div>Office of the University Registrar &bull; Generated ${new Date().toLocaleDateString()}</div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:15px;font-weight:bold;">${s360Esc(s.student_number)}</div>
            <div>Standing: <strong>${s360Esc(s.status)}</strong></div>
        </div>
    </div>

    <h2>1. Personal &amp; Enrollment Profile</h2>
    <div class="grid">
        <div class="row-item"><span>Full Name:</span><strong>${s360Esc(fullName)}</strong></div>
        <div class="row-item"><span>Degree Program:</span><strong>${s360Esc(s.program_course || '—')}</strong></div>
        <div class="row-item"><span>Date of Birth / Gender:</span><strong>${s360Esc(s.date_of_birth || '—')} / ${s360Esc(s.gender || '—')}</strong></div>
        <div class="row-item"><span>Year &amp; Section:</span><strong>${s360Esc(s.year_section || '—')}</strong></div>
        <div class="row-item"><span>Cumulative GWA:</span><strong>${s360Esc(gsum.cumulative_gwa_fmt || '—')} (${s360Esc(gsum.academic_standing || '—')})</strong></div>
        <div class="row-item"><span>Units Passed:</span><strong>${s360Esc(gsum.total_units_passed || 0)} Units</strong></div>
    </div>

    <h2>2. Family &amp; Emergency Contacts</h2>
    <table>
        <thead><tr><th>Full Name</th><th>Relationship</th><th>Contact Number</th><th>Address</th></tr></thead>
        <tbody>${guardianRows}</tbody>
    </table>

    <h2>3. Collegiate Scholastic Transcript</h2>
    ${termBlocks || '<p>No subject grades recorded.</p>'}

    <script>window.onload = function() { window.print(); };<\/script>
</body>
</html>`);
    win.document.close();
}
</script>

