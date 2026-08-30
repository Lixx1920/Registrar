<?php
/**
 * SMS 2 - Student ID Generation
 * Module: Registrar
 *
 * Features:
 *  - College / SHS department tab switcher
 *  - Multi-level filter (Batch Year, Course/Strand, Year Level, Section, ID Status)
 *  - Interactive student list with live search
 *  - 3D Flip ID Card preview (Front: Seal upper-right, no QR; Back: no Blood Type)
 *  - Streamlined "Create ID Card" modal (no Dept selector, no Blood Type, auto 1-year expiry)
 *  - Seed sample data support via seed-id-cards.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';

regRequireAction('registrar.view');

$pageTitle    = 'Student ID Generation';
$activeModule = 'registrar';
$activePage   = 'student-id-generation';
$breadcrumbs  = [
    ['label' => 'Registrar', 'url' => BASE_URL . '/modules/registrar/index.php'],
    ['label' => 'Student ID Generation', 'url' => null],
];

$logoUrl = BASE_URL . '/modules/registrar/sealstamp/bestlink.png';
$sealUrl  = BASE_URL . '/modules/registrar/sealstamp/Seal-Display.png';
$apiBase  = BASE_URL . '/modules/registrar/api/id-cards.php';
$autoExpiry = date('Y-m-d', strtotime('+1 year'));
$autoExpiryDisplay = date('F j, Y', strtotime('+1 year'));

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<link href="<?php echo BASE_URL; ?>/assets/css/module-process-list.css?v=2" rel="stylesheet">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/modules/registrar/assets/css/registrar.css">

<style>
/* ── Mockup-matching layout overrides ───────────────────────────────────── */

/* Dept tabs: full-width solid filled pills */
.sid-dept-bar {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
    border: 1.5px solid #2563eb;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 0;
}
.sid-dept-btn {
    padding: .65rem 1rem;
    text-align: center;
    font-size: .88rem;
    font-weight: 700;
    letter-spacing: .03em;
    text-transform: uppercase;
    cursor: pointer;
    transition: background .18s, color .18s;
    user-select: none;
    background: #fff;
    color: #2563eb;
    border: none;
    outline: none;
}
.sid-dept-btn + .sid-dept-btn {
    border-left: 1.5px solid #2563eb;
}
.sid-dept-btn.active {
    background: #2563eb;
    color: #fff;
}
.sid-dept-btn:not(.active):hover {
    background: #eff6ff;
}

/* Filter wrapper card */
.sid-filter-wrap {
    background: #fff;
    border: 1.5px solid #d1d5db;
    border-radius: 8px;
    padding: .85rem 1rem;
}
.sid-filter-label-sm {
    font-size: .73rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #6b7280;
    margin-bottom: 3px;
    display: block;
}
.sid-filter-select {
    background: #2563eb;
    color: #fff;
    border: 1.5px solid #2563eb;
    border-radius: 6px;
    font-size: .82rem;
    font-weight: 600;
    padding: .35rem 2rem .35rem .65rem;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='white'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right .5rem center;
    cursor: pointer;
    width: 100%;
}
.sid-filter-select option { background: #1e40af; color: #fff; }

/* Batch Year select — wider, full row */
.sid-batch-select {
    background: #2563eb;
    color: #fff;
    border: 1.5px solid #2563eb;
    border-radius: 6px;
    font-size: .85rem;
    font-weight: 600;
    padding: .4rem 2.2rem .4rem .85rem;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='white'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right .6rem center;
    cursor: pointer;
    min-width: 200px;
}
.sid-batch-select option { background: #1e40af; color: #fff; }

/* Search input */
.sid-search-input {
    border: 1.5px solid #2563eb;
    border-radius: 6px;
    font-size: .83rem;
    padding: .38rem .75rem;
    width: 100%;
    max-width: 340px;
    color: #374151;
}
.sid-search-input::placeholder { color: #9ca3af; }
.sid-search-input:focus { outline: none; box-shadow: 0 0 0 3px rgba(37,99,235,.15); border-color: #2563eb; }

/* Left panel (tabs + filter + table) outer border */
.sid-left-panel {
    border: 1.5px solid #dbe3f0;
    border-radius: 16px;
    overflow: hidden;
    background: #fff;
    display: flex;
    flex-direction: column;
    box-shadow: 0 8px 22px rgba(15, 33, 88, 0.05);
}

/* Table overrides for row selection */
.mpl-table tbody tr { cursor: pointer; transition: background .1s; }
.mpl-table tbody tr.sid-row-selected { background: #dbeafe !important; }
.mpl-table thead th { position: sticky; top: 0; z-index: 10; }

/* Right preview panel */
.sid-right-panel {
    background: #fff;
    border: 1.5px solid #d1d5db;
    border-radius: 10px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 100%;
}
.sid-right-panel-header {
    background: #f9fafb;
    border-bottom: 1.5px solid #d1d5db;
    padding: .7rem 1rem;
    font-size: .88rem;
    font-weight: 700;
    text-align: center;
    color: #374151;
    letter-spacing: .04em;
    text-transform: uppercase;
}
.sid-right-panel-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    padding: 1rem;
    min-height: 400px;
}

/* Create button style */
.sid-create-btn {
    background: #2563eb;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: .55rem 1.2rem;
    font-size: .88rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: .5rem;
    transition: background .15s, transform .1s, box-shadow .15s;
    white-space: nowrap;
}
.sid-create-btn:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(37,99,235,.35);
    color: #fff;
}

/* Stats row in table footer */
.sid-stats-footer {
    display: flex;
    gap: .5rem;
    flex-wrap: wrap;
    padding: .5rem .85rem;
    border-top: 1px solid #e5e7eb;
    background: #f9fafb;
    align-items: center;
}

/* Reference Card Layout overrides */
.rcard-scene { width: 100%; max-width: 450px; aspect-ratio: 85.6/53.98; margin: auto; perspective: 1400px; }
.rcard { width: 100%; height: 100%; position: relative; transform-style: preserve-3d; transition: transform .8s cubic-bezier(.2,.75,.2,1); }
.rcard.flipped { transform: rotateY(180deg); }
.rcard-face { position: absolute; inset: 0; border-radius: 12px; overflow: hidden; backface-visibility: hidden; -webkit-backface-visibility: hidden; box-shadow: 0 10px 25px rgba(23,35,63,.15); }
.rcard-front { background: linear-gradient(135deg,#fff,#f8faff 60%,#edf2fa); border: 1px solid #d5dce8; }
.rcard-decoration { position: absolute; right: -12%; top: -35%; width: 43%; height: 170%; background: rgba(31,61,121,.055); transform: rotate(18deg); }
.rcard-header { position: relative; z-index: 2; height: 26%; padding: 0 4%; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #dce2eb; }
.rcard-logo-wrap { display: flex; align-items: center; }
.rcard-logo { width: 42px; height: 42px; object-fit: contain; margin-right: 10px; }
.rcard-school { text-align: left; line-height: 1.1; }
.rcard-school strong { display: block; font-size: clamp(11px, 1.8vw, 15px); font-weight: 900; color: #17284f; }
.rcard-school span { display: block; margin-top: 3px; font-size: clamp(6px, .7vw, 8px); font-weight: 700; letter-spacing: .08em; color: #7a8497; }
.rcard-seal { width: 40px; height: 40px; object-fit: contain; }
.rcard-content { position: relative; z-index: 2; height: 74%; padding: 4%; display: grid; grid-template-columns: 24% 1fr; gap: 4%; }
.rcard-photo { width: 100%; aspect-ratio: 1/1.25; border-radius: 6px; overflow: hidden; background: #e6eaf1; border: 2px solid #d0d8e5; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 2rem; }
.rcard-photo img { width: 100%; height: 100%; object-fit: cover; display: none; }
.rcard-details { text-align: left; padding-top: 0; }
.rcard-name { margin-bottom: 6px; font-size: clamp(14px, 2.2vw, 19px); line-height: 1.1; font-weight: 900; text-transform: uppercase; color: #17284f; }
.rcard-detail { margin: 3px 0; display: flex; flex-direction: column; }
.rcard-label { font-size: clamp(6px, .7vw, 8px); font-weight: 800; letter-spacing: .08em; text-transform: uppercase; color: #7b8495; }
.rcard-value { margin-top: 1px; font-size: clamp(9px, 1.1vw, 12px); font-weight: 700; color: #293855; }
.rcard-signature { position: absolute; right: 4%; bottom: 6%; width: 25%; color: #697488; font-size: clamp(6px, .7vw, 8px); text-align: center; }
.rcard-sigline { border-top: 1px solid #7d8798; margin-bottom: 2px; }

.rcard-back { transform: rotateY(180deg); background: linear-gradient(135deg,#f9fbff,#eef3fa); border: 1px solid #d5dce8; padding: 4%; display: flex; flex-direction: column; }
.rcard-back-header { font-size: clamp(10px, 1.5vw, 14px); font-weight: 900; color: #17284f; text-align: center; margin-bottom: 4%; border-bottom: 1px solid #dce2eb; padding-bottom: 2%; }
.rcard-back-body { flex: 1; text-align: left; font-size: clamp(8px, 1vw, 10px); color: #606b80; }
.rcard-back-row { display: grid; grid-template-columns: 1fr 1fr; gap: 4%; margin-bottom: 4px; }
.rcard-back-full { margin-bottom: 4px; }
.rcard-back-label { font-size: clamp(6px, .7vw, 8px); font-weight: 800; text-transform: uppercase; color: #7b8495; margin-bottom: 1px; }
.rcard-back-value { font-size: clamp(9px, 1.1vw, 11px); font-weight: 700; color: #293855; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.rcard-back-value-wrap { white-space: normal; line-height: 1.2; }
.rcard-back-policy { margin-top: 6px; font-size: clamp(6px, .7vw, 7.5px); line-height: 1.3; color: #828ba0; text-align: justify; }
.rcard-back-sigs { display: flex; justify-content: space-between; margin-top: auto; padding-top: 8px; }
.rcard-back-sig { width: 45%; text-align: center; font-size: clamp(6px, .7vw, 8px); color: #17284f; font-weight: 700; border-top: 1px solid #17284f; padding-top: 2px; }
.rcard-back-footer { margin-top: 8px; border-top: 1px solid #d5dce8; padding-top: 4px; text-align: center; }

@media screen {
    #sidPrintArea { display: none !important; }
}
@media print {
    body > :not(#sidPrintArea) {
        display: none !important;
    }
    
    #sidPrintArea {
        display: block !important;
        width: 100%;
        margin: 0;
        padding: 0;
    }

    .rcard-print-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 8mm;
        width: 100%;
        margin: 0 0 8mm 0;
        page-break-inside: avoid;
        break-inside: avoid;
    }

    .rcard-print-wrapper .rcard-face {
        position: relative !important;
        height: 53.98mm !important;
        width: 85.6mm !important;
        min-width: 85.6mm !important;
        min-height: 53.98mm !important;
        box-sizing: border-box !important;
        transform: none !important;
        overflow: hidden !important;
        flex: 0 0 85.6mm;
        box-shadow: none !important;
        border: 1px solid #ccc;
    }

    .rcard-print-wrapper .rcard-back-body {
        width: 100% !important;
        min-width: 0 !important;
        min-height: 0 !important;
        flex: 1 1 auto !important;
        overflow: hidden !important;
    }

    .rcard-print-wrapper .rcard-back-row {
        width: 100% !important;
        min-width: 0 !important;
    }

    .rcard-print-wrapper .rcard-back-row > div {
        min-width: 0 !important;
    }

    .rcard-print-wrapper .rcard-back-full {
        width: 100% !important;
        min-width: 0 !important;
    }

    .rcard-print-wrapper .rcard-back-value {
        min-width: 0 !important;
        max-width: 100% !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }
    
    .rcard-school strong {
        font-size: 11pt !important;
    }

    .rcard-school span {
        font-size: 5.5pt !important;
    }

    .rcard-name {
        font-size: 12pt !important;
    }

    .rcard-label {
        font-size: 5pt !important;
    }

    .rcard-value {
        font-size: 7pt !important;
    }

    .rcard-signature {
        font-size: 5pt !important;
    }

    .rcard-back-header {
        font-size: 9pt !important;
    }

    .rcard-back-body {
        font-size: 6.5pt !important;
    }

    .rcard-back-label {
        font-size: 5pt !important;
    }

    .rcard-back-value {
        font-size: 6.5pt !important;
    }

    .rcard-back-policy {
        font-size: 5pt !important;
    }

    .rcard-back-sig {
        font-size: 5pt !important;
    }

    .rcard-back-footer {
        font-size: 5pt !important;
    }

    .rcard-logo {
        width: 11mm !important;
        height: 11mm !important;
    }

    .rcard-seal {
        width: 10mm !important;
        height: 10mm !important;
    }

    .rcard-print-wrapper .rcard-decoration {
        z-index: 0 !important;
    }

    .rcard-print-wrapper .rcard-back-header,
    .rcard-print-wrapper .rcard-back-body,
    .rcard-print-wrapper .rcard-back-sigs,
    .rcard-print-wrapper .rcard-back-footer {
        position: relative !important;
        z-index: 2 !important;
    }
}
</style>

<?php renderBreadcrumbs($breadcrumbs); ?>

<div class="container-fluid py-3" id="sidPage">

    <!-- ── Page Header ─────────────────────────────────────────────────── -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
            <span id="sidLoadSpinner" class="badge bg-secondary px-3 py-2 rounded-pill" style="display:none!important;">
                <i class="fas fa-spinner fa-spin me-1"></i> Loading…
            </span>
            <button class="sid-create-btn" onclick="sidOpenCreateModal()">
                <i class="fas fa-plus-circle" style="font-size:1rem;"></i>
                Create Sample Student ID
            </button>
        </div>
    </div>

    <!-- ── Main 2-column layout ─────────────────────────────────────────── -->
    <div class="row g-3" style="align-items:stretch;">

        <!-- LEFT: Tabs + Filter + Table -->
        <div class="col-lg-8 col-xl-8 d-flex flex-column">
            <div class="mpl" data-mpl style="display:flex; flex-direction:column; flex: 1 1 auto; min-height: 0;">
            <div class="mpl-panel flex-grow-1 d-flex flex-column">

                <!-- Department Tabs -->
                <div class="p-3 pb-2 border-bottom">
                    <div class="sid-dept-bar">
                        <button class="sid-dept-btn active" id="tabCollege" onclick="sidSetDept('College')">
                            <i class="fas fa-graduation-cap me-1"></i> College
                        </button>
                        <button class="sid-dept-btn" id="tabSHS" onclick="sidSetDept('SHS')">
                            <i class="fas fa-school me-1"></i> Senior Highschool
                        </button>
                    </div>
                </div>

                <!-- Filter Area -->
                <div class="p-3 border-bottom">

                    <!-- Row 1: Year Batch -->
                    <div class="mb-2">
                        <span class="sid-filter-label-sm">Year Batch</span>
                        <select id="sidFilterBatch" class="sid-batch-select" onchange="sidFetch()">
                            <option value="">School Year Batch</option>
                        </select>
                    </div>

                    <!-- Row 2: Course | Year Level | Section | ID Status (inline) -->
                    <div class="row g-2 mb-2">
                        <div class="col">
                            <span class="sid-filter-label-sm" id="sidFilterCourseLabel">Course/Program</span>
                            <select id="sidFilterCourse" class="sid-filter-select" onchange="sidFetch()">
                                <option value="">All Courses</option>
                            </select>
                        </div>
                        <div class="col">
                            <span class="sid-filter-label-sm" id="sidFilterYearLabel">Year Level</span>
                            <select id="sidFilterYear" class="sid-filter-select" onchange="sidFetch()">
                                <option value="">All Year Levels</option>
                            </select>
                        </div>
                        <div class="col">
                            <span class="sid-filter-label-sm">Section</span>
                            <select id="sidFilterSection" class="sid-filter-select" onchange="sidFetch()">
                                <option value="">All Sections</option>
                            </select>
                        </div>
                        <div class="col">
                            <span class="sid-filter-label-sm">ID Status</span>
                            <select id="sidFilterStatus" class="sid-filter-select" onchange="sidFetch()">
                                <option value="">All Statuses</option>
                                <option value="Ready">Ready</option>
                                <option value="Printed">Printed</option>
                                <option value="Released">Released</option>
                                <option value="Not Created">Not Yet Created</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 3: Search (right-aligned) -->
                    <div class="d-flex justify-content-end">
                        <div style="position:relative;max-width:340px;width:100%;">
                            <i class="fas fa-search" style="position:absolute;left:.7rem;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:.8rem;pointer-events:none;"></i>
                            <input type="text" id="sidSearch" class="sid-search-input"
                                   placeholder="SEARCH (Name of Student/Student ID No.)"
                                   style="padding-left:2rem;">
                        </div>
                    </div>

                </div>

                <!-- Student Table -->
                <div class="mpl-table-wrap" style="flex:1 1 auto;min-height:0;height:500px;overflow-y:auto;overflow-x:hidden;">
                    <table class="mpl-table">
                        <thead>
                            <tr>
                                <th style="width:30px;"><input type="checkbox" id="sidCheckAll" onclick="sidToggleAll(this)"></th>
                                <th>Student ID Number</th>
                                <th>Name</th>
                                <th>Course</th>
                                <th>Year/Section</th>
                                <th>Status</th>
                                <th style="width:42px;"></th>
                            </tr>
                        </thead>
                        <tbody id="sidTableBody">
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="fas fa-spinner fa-spin me-2"></i>Loading students…
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Stats Footer -->
                <div class="sid-stats-footer">
                    <span class="text-muted" style="font-size:.75rem;" id="sidRowCount">—</span>
                    <span class="ms-auto d-flex gap-2">
                        <span class="sid-stat-pill" onclick="sidQuickFilter('')" style="font-size:.72rem;padding:.2rem .6rem;">
                            <span class="fw-bold" id="statTotal">—</span> Total
                        </span>
                        <span class="sid-stat-pill" onclick="sidQuickFilter('Ready')" style="font-size:.72rem;padding:.2rem .6rem;color:#065f46;">
                            <span class="fw-bold" id="statReady">—</span> Ready
                        </span>
                        <span class="sid-stat-pill" onclick="sidQuickFilter('Printed')" style="font-size:.72rem;padding:.2rem .6rem;color:#1e3a8a;">
                            <span class="fw-bold" id="statPrinted">—</span> Printed
                        </span>
                        <span class="sid-stat-pill" onclick="sidQuickFilter('Released')" style="font-size:.72rem;padding:.2rem .6rem;color:#155724;">
                            <span class="fw-bold" id="statReleased">—</span> Released
                        </span>
                        <span class="sid-stat-pill" onclick="sidQuickFilter('Not Created')" style="font-size:.72rem;padding:.2rem .6rem;color:#78350f;">
                            <span class="fw-bold" id="statNotCreated">—</span> Not Created
                        </span>
                        <button class="btn btn-sm btn-outline-primary ms-2" onclick="sidBulkPrint()" style="font-size:.72rem;padding:.2rem .65rem;">
                            <i class="fas fa-print me-1"></i>Bulk Print
                        </button>
                        <button class="btn btn-sm btn-info text-white ms-2" onclick="sidPrintView()" style="font-size:.72rem;padding:.2rem .65rem;">
                            <i class="fas fa-file-pdf me-1"></i>Print Preview
                        </button>
                    </span>
                </div>

            </div>
            </div>
        </div>

        <!-- RIGHT: ID Card Preview -->
        <div class="col-lg-4 col-xl-4 d-flex flex-column">
            <div class="sid-right-panel flex-grow-1">

                <div class="sid-right-panel-header">
                    <i class="fas fa-id-card me-1 text-primary"></i> ID Card Preview
                </div>

                <div class="sid-right-panel-body">

                    <!-- Empty state -->
                    <div class="sid-preview-empty" id="sidPreviewEmpty" style="padding-top:4rem;">
                        <i class="fas fa-id-card" style="font-size:3rem;color:#d1d5db;display:block;text-align:center;margin-bottom:.75rem;"></i>
                        <p class="text-center text-muted mb-0" style="font-size:.85rem;">
                            Select a student from the list<br>to preview their ID card
                        </p>
                    </div>

                    <!-- Card preview (hidden until selection) -->
                    <div id="sidPreviewContent" style="display:none;width:100%;">

                        <!-- 3D Flip Scene (Landscape Reference Style) -->
                        <div class="rcard-scene" style="margin-bottom:.75rem;">
                            <div class="rcard" id="sidCardFlipper">

                                <!-- FRONT -->
                                <div class="rcard-face rcard-front" id="sidFrontCard">
                                    <div class="rcard-decoration" id="sidFrontAccent"></div>
                                    <!-- Header: Logo | School Name | Seal (upper-right) -->
                                    <div class="rcard-header">
                                        <div class="rcard-logo-wrap">
                                            <img src="<?php echo $logoUrl; ?>" alt="BCP Logo" class="rcard-logo">
                                            <div class="rcard-school">
                                                <strong>BESTLINK COLLEGE OF THE PHILIPPINES</strong>
                                                <span>OFFICIAL STUDENT IDENTIFICATION CARD</span>
                                            </div>
                                        </div>
                                        <!-- SEAL: Upper Right -->
                                        <img src="<?php echo $sealUrl; ?>" alt="BCP Seal" class="rcard-seal" id="sidFrontSeal">
                                    </div>

                                    <div class="rcard-content">
                                        <div class="rcard-photo">
                                            <i class="fas fa-user" style="font-size:3.5rem;color:#d1d5db;"></i>
                                        </div>
                                        <div class="rcard-details">
                                            <div class="rcard-name" id="sidFrontName">LAST NAME, First M.</div>
                                            <div class="rcard-detail"><span class="rcard-label">Student No.</span><span class="rcard-value" id="sidFrontStudentNo">2024-00000</span></div>
                                            <div class="rcard-detail"><span class="rcard-label">Program / Course</span><span class="rcard-value" id="sidFrontCourse">—</span></div>
                                            <div class="rcard-detail"><span class="rcard-label">Year / Section</span><span class="rcard-value" id="sidFrontYear">—</span></div>
                                            <div class="rcard-detail"><span class="rcard-label">Valid Until</span><span class="rcard-value" id="sidFrontExpiry">—</span></div>
                                        </div>
                                        <div class="rcard-signature">
                                            <div class="rcard-sigline"></div>
                                            Authorized Signature
                                        </div>
                                    </div>
                                </div><!-- /FRONT -->

                                <!-- BACK -->
                                <div class="rcard-face rcard-back">
                                    <div class="rcard-decoration" id="sidBackAccent" style="transform:rotate(-18deg);right:auto;left:-12%;"></div>
                                    <div class="rcard-back-header">STUDENT ID VERIFICATION</div>
                                    <div class="rcard-back-body">
                                        <div class="rcard-back-text mb-2">
                                            <strong>Emergency Contact Information</strong>
                                        </div>
                                        <div class="rcard-back-row">
                                            <div>
                                                <div class="rcard-back-label">Contact Person</div>
                                                <div class="rcard-back-value" id="sidBackEmergencyName">—</div>
                                            </div>
                                            <div>
                                                <div class="rcard-back-label">Relationship</div>
                                                <div class="rcard-back-value" id="sidBackEmergencyRel">—</div>
                                            </div>
                                        </div>
                                        <div class="rcard-back-full">
                                            <div class="rcard-back-label">Contact Number</div>
                                            <div class="rcard-back-value" id="sidBackEmergencyPhone">—</div>
                                        </div>
                                        <div class="rcard-back-full">
                                            <div class="rcard-back-label">Home Address</div>
                                            <div class="rcard-back-value rcard-back-value-wrap" id="sidBackAddress">—</div>
                                        </div>
                                        <div class="rcard-back-policy" style="position:relative;z-index:2;">
                                            This card is non-transferable. If found, please return to the Registrar's Office.
                                            Loss of card must be reported immediately. Tel: (02) 8936-0819
                                        </div>
                                    </div>
                                    <div class="rcard-back-sigs" style="position:relative;z-index:2;">
                                        <div class="rcard-back-sig">College Registrar</div>
                                        <div class="rcard-back-sig">School President</div>
                                    </div>
                                    <div class="rcard-back-footer" style="position:relative;z-index:2;">
                                        <span id="sidFrontIdNo">ID# —</span> &nbsp;|&nbsp; Official Student Identification Card
                                    </div>
                                </div><!-- /BACK -->

                            </div><!-- /flipper -->
                        </div><!-- /scene -->

                        <!-- Flip Button -->
                        <div class="text-center mb-2">
                            <button class="sid-flip-btn mx-auto" id="sidFlipBtn" onclick="sidFlipCard()">
                                <i class="fas fa-sync-alt"></i>
                                <span id="sidFlipLabel">Flip to Back</span>
                            </button>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 justify-content-center flex-wrap">
                            <button class="btn btn-sm btn-success" id="sidBtnPrint" onclick="sidMarkPrinted()">
                                <i class="fas fa-print me-1"></i>Mark Printed
                            </button>
                            <button class="btn btn-sm btn-info text-white" onclick="sidPrintView()">
                                <i class="fas fa-file-pdf me-1"></i>Print View
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="sidOpenCreateModal(true)">
                                <i class="fas fa-edit me-1"></i>Edit
                            </button>
                        </div>

                        <!-- Status -->
                        <div class="text-center mt-2" id="sidCurrentStatus" style="font-size:.75rem;color:#6b7280;"></div>

                    </div><!-- /preview content -->
                </div>
            </div>
        </div><!-- /right col -->

    </div><!-- /row -->
</div><!-- /container -->

<!-- ══════════════════════ CREATE ID CARD MODAL ══════════════════════════ -->
<div class="modal fade" id="sidCreateModal" tabindex="-1" aria-labelledby="sidCreateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px;overflow:hidden;">
            <div class="modal-header">
                <h5 class="modal-title" id="sidCreateModalLabel">
                    <i class="fas fa-id-card-alt me-2"></i>Create Student ID Card
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="sidCreateForm" onsubmit="sidSubmitCreate(event)">
            <div class="modal-body p-4">
                <input type="hidden" id="csidCsrfToken" value="">

                <div class="row g-3">
                    <!-- Photo Section -->
                    <div class="col-12">
                        <div class="d-flex gap-3 align-items-start">
                            <div>
                                <label class="modal-label mb-1">Student Photo (2x2)</label>
                                <div class="sid-photo-preview-box" id="csidPhotoBox" onclick="document.getElementById('csidPhotoInput').click()">
                                    <span class="sid-photo-icon"><i class="fas fa-camera"></i></span>
                                </div>
                                <input type="file" id="csidPhotoInput" accept="image/jpeg,image/png" style="display:none" onchange="sidPreviewPhoto(event)">
                                <div class="mt-1 text-center" style="font-size:.72rem;color:#90a4ae;">Click to upload</div>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-muted mb-2" style="font-size:.82rem;">
                                    <i class="fas fa-info-circle text-primary me-1"></i>
                                    Fill in the student information for the ID card.
                                    <strong>Expiry date</strong> is automatically set to <strong>1 year from today</strong>
                                    (<span id="csidExpiryDisplay"><?php echo $autoExpiryDisplay; ?></span>).
                                </p>
                                <div class="mb-2">
                                    <label class="modal-label">School Year / Batch</label>
                                    <select id="csidBatchYear" class="form-select form-select-sm">
                                        <?php
                                        $cy = (int) date('Y');
                                        for ($y = $cy + 1; $y >= $cy - 2; $y--) {
                                            echo "<option value=\"{$y}-" . ($y+1) . "\">" . $y . "–" . ($y+1) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Student Info -->
                    <div class="col-12">
                        <hr class="my-0">
                        <p class="fw-semibold text-dark mt-2 mb-2" style="font-size:.85rem;">
                            <i class="fas fa-user me-1 text-primary"></i> Student Information
                        </p>
                    </div>
                    <div class="col-md-3">
                        <label class="modal-label">Student No. *</label>
                        <input type="text" id="csidStudentNo" class="form-control form-control-sm" placeholder="e.g. 2024-00123" required>
                    </div>
                    <div class="col-md-3">
                        <label class="modal-label">Last Name *</label>
                        <input type="text" id="csidLastName" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-3">
                        <label class="modal-label">First Name *</label>
                        <input type="text" id="csidFirstName" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-2">
                        <label class="modal-label">Middle Name</label>
                        <input type="text" id="csidMiddleName" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-1">
                        <label class="modal-label">Suffix</label>
                        <input type="text" id="csidSuffix" class="form-control form-control-sm" placeholder="Jr.">
                    </div>

                    <div class="col-md-6">
                        <label class="modal-label" id="csidCourseLabel">Course / Program</label>
                        <select id="csidCourse" class="form-select form-select-sm">
                            <option value="">— Select Course —</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="modal-label" id="csidYearLabel">Year Level</label>
                        <select id="csidYearLevel" class="form-select form-select-sm">
                            <option value="">— Year Level —</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="modal-label">Section</label>
                        <input type="text" id="csidSection" class="form-control form-control-sm" placeholder="e.g. A">
                    </div>

                    <!-- Emergency Contact -->
                    <div class="col-12">
                        <hr class="my-0">
                        <p class="fw-semibold text-dark mt-2 mb-2" style="font-size:.85rem;">
                            <i class="fas fa-phone-alt me-1 text-primary"></i> Emergency Contact (for Back of ID)
                        </p>
                    </div>
                    <div class="col-md-5">
                        <label class="modal-label">Contact Person *</label>
                        <input type="text" id="csidEmergencyName" class="form-control form-control-sm" placeholder="Full name" required>
                    </div>
                    <div class="col-md-4">
                        <label class="modal-label">Contact Number</label>
                        <input type="text" id="csidEmergencyPhone" class="form-control form-control-sm" placeholder="09XX-XXX-XXXX">
                    </div>
                    <div class="col-md-3">
                        <label class="modal-label">Relationship</label>
                        <select id="csidEmergencyRel" class="form-select form-select-sm">
                            <option value="Parent">Parent</option>
                            <option value="Mother">Mother</option>
                            <option value="Father">Father</option>
                            <option value="Guardian">Guardian</option>
                            <option value="Sibling">Sibling</option>
                            <option value="Spouse">Spouse</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="modal-label">Home Address</label>
                        <input type="text" id="csidAddress" class="form-control form-control-sm" placeholder="Street, Barangay, City">
                    </div>

                    <!-- Expiry (auto, read-only) -->
                    <div class="col-md-6">
                        <label class="modal-label">ID Expiry Date (Auto: 1 Year)</label>
                        <input type="date" id="csidExpiry" class="form-control form-control-sm"
                               value="<?php echo $autoExpiry; ?>" readonly
                               style="background:#f8fafc;cursor:not-allowed;">
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <span class="text-muted" style="font-size:.78rem;">
                            <i class="fas fa-clock text-primary me-1"></i>
                            Auto-calculated as 1 year from today
                        </span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="csidSubmitBtn">
                    <i class="fas fa-id-card me-1"></i> Generate ID Card
                </button>
            </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
<script>
(function () {
'use strict';

/* ── Constants ─────────────────────────────────────────────────────────────── */
const API     = '<?php echo $apiBase; ?>';
const SEAL    = '<?php echo $sealUrl; ?>';
const BASE    = '<?php echo BASE_URL; ?>';

const COLLEGE_PROGRAMS = [
    'BS Information Technology','BS Hospitality Management',
    'BS Accounting Information System','BS Tourism Management',
    'BS Office Administration','BS Entrepreneurship',
    'BS Business Administration','Bachelor of Library Information Science',
    'BS Computer Engineering','BS Psychology','BS Criminology',
    'BS Physical Education','BS Technological & Livelihood Education',
    'BS Elementary Education','BS Secondary Education'
];
const COLLEGE_YEAR_LEVELS = [
    {val:'I', label:'1st Year'},
    {val:'II', label:'2nd Year'},
    {val:'III', label:'3rd Year'},
    {val:'IV', label:'4th Year'},
];
const SHS_STRANDS = ['STEM','ABM','HUMSS','GAS','TVL-ICT','TVL-HE'];
const SHS_YEAR_LEVELS = [
    {val:'G11', label:'Grade 11 (1st Year SHS)'},
    {val:'G12', label:'Grade 12 (2nd Year SHS)'},
];

/* ── State ─────────────────────────────────────────────────────────────────── */
let sidDept        = 'College';
let sidFlipped     = false;
let sidSelectedRow = null;
let sidCurrentCard = null;
let sidChecked     = new Set();

/* ── Init ──────────────────────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    sidBuildFilterDropdowns('College');
    sidLoadFilterOpts();
    sidFetch();

    // Live search
    document.getElementById('sidSearch').addEventListener('input', debounce(sidFetch, 300));

    // Inject CSRF token for modal
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) document.getElementById('csidCsrfToken').value = meta.getAttribute('content');
});

/* ── Department Tab ────────────────────────────────────────────────────────── */
function sidSetDept(dept) {
    sidDept = dept;
    document.getElementById('tabCollege').classList.toggle('active', dept === 'College');
    document.getElementById('tabSHS').classList.toggle('active', dept === 'SHS');
    sidBuildFilterDropdowns(dept);
    sidResetPreview();
    sidFetch();
    sidRebuildModalCourseDropdown(dept);
}
window.sidSetDept = sidSetDept;

function sidBuildFilterDropdowns(dept) {
    const courseLabel = document.getElementById('sidFilterCourseLabel');
    const courseSelect = document.getElementById('sidFilterCourse');
    const yearSelect = document.getElementById('sidFilterYear');

    courseLabel.textContent = dept === 'SHS' ? 'Strand' : 'Course / Program';

    courseSelect.innerHTML = dept === 'SHS'
        ? '<option value="">— All Strands —</option>' + SHS_STRANDS.map(s => `<option value="${s}">${s}</option>`).join('')
        : '<option value="">— All Courses —</option>' + COLLEGE_PROGRAMS.map(p => `<option value="${p}">${e(p)}</option>`).join('');

    const yrLevels = dept === 'SHS' ? SHS_YEAR_LEVELS : COLLEGE_YEAR_LEVELS;
    document.getElementById('sidFilterYearLabel').textContent = dept === 'SHS' ? 'Grade Level' : 'Year Level';
    yearSelect.innerHTML = `<option value="">— All Year Levels —</option>` +
        yrLevels.map(y => `<option value="${e(y.val)}">${e(y.label)}</option>`).join('');
}

/* ── Filter Options (school years, sections from API) ─────────────────────── */
function sidLoadFilterOpts() {
    fetch(`${API}?action=filter_opts`)
        .then(r => r.json())
        .then(d => {
            if (!d.success) return;
            const batchSel = document.getElementById('sidFilterBatch');
            d.school_years.forEach(sy => {
                const o = document.createElement('option'); o.value = sy; o.textContent = sy;
                batchSel.appendChild(o);
            });
            const secSel = document.getElementById('sidFilterSection');
            d.sections.forEach(s => {
                const o = document.createElement('option'); o.value = s; o.textContent = s;
                secSel.appendChild(o);
            });
        }).catch(() => {});
}

/* ── Fetch Students ────────────────────────────────────────────────────────── */
function sidFetch() {
    const params = new URLSearchParams({
        action:     'list',
        department: sidDept,
        program:    document.getElementById('sidFilterCourse').value,
        year_level: document.getElementById('sidFilterYear').value,
        section:    document.getElementById('sidFilterSection').value,
        batch_year: document.getElementById('sidFilterBatch').value,
        id_status:  document.getElementById('sidFilterStatus').value,
        q:          document.getElementById('sidSearch').value,
    });

    sidShowSpinner(true);
    fetch(`${API}?${params}`)
        .then(r => r.json())
        .then(d => {
            sidShowSpinner(false);
            if (!d.success) { sidTableError('Failed to load students.'); return; }
            sidRenderStats(d.stats);
            sidRenderTable(d.data);
        }).catch(() => { sidShowSpinner(false); sidTableError('Network error. Please try again.'); });
}
window.sidFetch = sidFetch;

function sidApplyFilters() { sidFetch(); }
window.sidApplyFilters = sidApplyFilters;

function sidClearFilters() {
    ['sidFilterBatch','sidFilterCourse','sidFilterYear','sidFilterSection','sidFilterStatus','sidSearch']
        .forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
    sidFetch();
}
window.sidClearFilters = sidClearFilters;

function sidQuickFilter(status) {
    document.getElementById('sidFilterStatus').value = status;
    sidFetch();
}
window.sidQuickFilter = sidQuickFilter;

/* ── Stats Pills ───────────────────────────────────────────────────────────── */
function sidRenderStats(s) {
    document.getElementById('statTotal').textContent      = s.total;
    document.getElementById('statReady').textContent      = s.ready;
    document.getElementById('statPrinted').textContent    = s.printed;
    document.getElementById('statReleased').textContent   = s.released;
    document.getElementById('statNotCreated').textContent = s.not_created;
}

/* ── Table Rendering ───────────────────────────────────────────────────────── */
const AVATAR_COLORS = ['#1a2744','#22336b','#3498db','#27ae60','#e74c3c','#9b59b6','#e67e22','#1abc9c'];

function sidRenderTable(rows) {
    const tbody = document.getElementById('sidTableBody');
    const count = document.getElementById('sidRowCount');
    sidChecked.clear();
    document.getElementById('sidCheckAll').checked = false;

    if (!rows || rows.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-5">
            <i class="fas fa-search me-2"></i>No students found matching the filters.</td></tr>`;
        count.textContent = '0 students';
        return;
    }

    count.textContent = `${rows.length} student${rows.length !== 1 ? 's' : ''}`;

    tbody.innerHTML = rows.map((row, i) => {
        const initials = ((row.last_name || '').charAt(0) + (row.first_name || '').charAt(0)).toUpperCase() || 'ST';
        const color    = AVATAR_COLORS[i % AVATAR_COLORS.length];
        const badgeClass = sidStatusBadgeClass(row.id_status, row.has_id);
        const badgeText  = row.has_id ? (row.id_status || 'Ready') : 'Not Created';
        const course = (row.program_course || '').length > 18
            ? (row.program_course || '').substring(0, 18) + '…'
            : (row.program_course || '—');
        const idNo = row.id_number || (row.has_id ? '—' : 'Not Created');

        return `<tr data-idx="${i}" data-sid="${row.id}" onclick="sidSelectRow(this, ${JSON.stringify(JSON.stringify(row))})">
            <td onclick="event.stopPropagation()" style="text-align:center;">
                <input type="checkbox" class="sid-row-check" value="${row.id}" onchange="sidToggleCheck(${row.id})">
            </td>
            <td style="font-size:.78rem;font-weight:600;white-space:nowrap;color:#1e3a8a;">${e(idNo)}</td>
            <td>
                <div class="d-flex align-items-center gap-2">
                    <div class="sid-row-avatar" style="background:${color};font-size:.7rem;">${e(initials)}</div>
                    <span class="fw-semibold" style="font-size:.82rem;">${e(row.full_name || '')}</span>
                </div>
            </td>
            <td style="font-size:.78rem;max-width:110px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${e(row.program_course||'')}">${e(course)}</td>
            <td style="font-size:.78rem;white-space:nowrap;">${e(row.year_section || '—')}</td>
            <td><span class="${badgeClass}">${e(badgeText)}</span></td>
            <td style="text-align:center;">
                ${!row.has_id
                    ? `<button class="btn btn-sm btn-warning py-0 px-2" style="font-size:.72rem;" title="Create ID" onclick="event.stopPropagation();sidOpenCreateModalFor(${row.id},'${e(row.student_number||'')}','${e(row.first_name||'')}','${e(row.last_name||'')}','${e(row.program_course||'')}','${e(row.year_section||'')}')">
                        <i class="fas fa-plus"></i></button>`
                    : `<button class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size:.72rem;" title="Preview" onclick="event.stopPropagation();sidLoadPreview(${row.id})">
                        <i class="fas fa-eye"></i></button>`
                }
            </td>
        </tr>`;
    }).join('');
}

function sidStatusBadgeClass(status, hasId) {
    if (!hasId) return 'mpl-status cancelled';
    const map = {
        'Ready':'mpl-status active',
        'Printed':'mpl-status processing',
        'Released':'mpl-status released',
        'Cancelled':'mpl-status cancelled',
    };
    return map[status] || 'mpl-status active';
}

function sidTableError(msg) {
    document.getElementById('sidTableBody').innerHTML =
        `<tr><td colspan="6" class="text-center text-danger py-3"><i class="fas fa-exclamation-triangle me-1"></i>${e(msg)}</td></tr>`;
}

/* ── Row Selection & Preview ───────────────────────────────────────────────── */
function sidSelectRow(tr, rowJson) {
    // deselect previous
    document.querySelectorAll('.sid-row-selected').forEach(r => r.classList.remove('sid-row-selected'));
    tr.classList.add('sid-row-selected');
    sidSelectedRow = tr;

    const row = JSON.parse(rowJson);
    if (row.id) sidLoadPreview(row.id);
}
window.sidSelectRow = sidSelectRow;

function sidLoadPreview(studentId) {
    document.getElementById('sidPreviewEmpty').style.display = 'none';
    document.getElementById('sidPreviewContent').style.display = 'none';

    fetch(`${API}?action=preview&id=${studentId}`)
        .then(r => r.json())
        .then(d => {
            if (!d.success || !d.card) return;
            sidRenderPreview(d.card);
        }).catch(() => {});
}
window.sidLoadPreview = sidLoadPreview;

function sidRenderPreview(card) {
    sidCurrentCard = card;
    const isSHS = (card.year_section || '').startsWith('G1');
    const accent = isSHS ? 'sid-id-accent-shs' : 'sid-id-accent-college';

    // Front
    document.getElementById('sidFrontAccent').className = 'rcard-decoration ' + accent;
    document.getElementById('sidFrontName').textContent    = card.last_first || '';
    document.getElementById('sidFrontStudentNo').textContent = card.student_number || '';
    document.getElementById('sidFrontCourse').textContent  = card.program_course || '—';
    document.getElementById('sidFrontYear').textContent    = card.year_section || '—';
    document.getElementById('sidFrontExpiry').textContent  = card.expiry_display || '—';
    document.getElementById('sidFrontIdNo').textContent    = 'ID# ' + (card.id_number || '—');
    
    // Back
    document.getElementById('sidBackAccent').className    = 'rcard-decoration ' + accent;
    document.getElementById('sidBackEmergencyName').textContent  = card.emergency_name || 'N/A';
    document.getElementById('sidBackEmergencyRel').textContent   = card.emergency_relation || '—';
    document.getElementById('sidBackEmergencyPhone').textContent = card.emergency_phone || '—';
    document.getElementById('sidBackAddress').textContent        = card.emergency_address || 'N/A';

    // Status line
    const statusEl = document.getElementById('sidCurrentStatus');
    statusEl.innerHTML = `Status: <span class="${sidStatusBadgeClass(card.id_status, !!card.id_card_id)} ms-1">${e(card.id_status || 'Not Created')}</span>
        ${card.id_card_id ? `&nbsp;|&nbsp; Card ID: <strong>#${card.id_card_id}</strong>` : ''}`;

    // Show/hide print button
    const printBtn = document.getElementById('sidBtnPrint');
    printBtn.style.display = card.id_card_id && card.id_status !== 'Printed' ? '' : 'none';

    document.getElementById('sidPreviewContent').style.display = '';
    document.getElementById('sidPreviewEmpty').style.display   = 'none';

    // Reset flip state
    if (sidFlipped) sidFlipCard();
}

/* ── 3D Flip ───────────────────────────────────────────────────────────────── */
function sidFlipCard() {
    sidFlipped = !sidFlipped;
    const flipper = document.getElementById('sidCardFlipper');
    const btn     = document.getElementById('sidFlipBtn');
    const label   = document.getElementById('sidFlipLabel');
    flipper.classList.toggle('flipped', sidFlipped);
    btn.classList.toggle('flipped', sidFlipped);
    label.textContent = sidFlipped ? 'Flip to Front' : 'Flip to Back';
}
window.sidFlipCard = sidFlipCard;

/* ── Barcode Generator (decorative CSS) ────────────────────────────────────── */
function sidGenerateBarcode(containerId, text) {
    const el = document.getElementById(containerId);
    if (!el) return;
    let html = '';
    const seed = [...(text || 'BCP')].reduce((a, c) => a + c.charCodeAt(0), 0);
    const widths = [1,1,2,1,3,1,2,1,1,2,1,3,1,1,2,1,1,1,2,3,1,1,2,1,1,1,3,2,1,1,1,2,1,3,1,1,2,1,1,1];
    for (let i = 0; i < 40; i++) {
        const w = widths[(seed + i) % widths.length];
        html += `<span style="width:${w}px;height:${containerId.includes('Back') ? '14px' : '16px'};"></span>`;
    }
    el.innerHTML = html;
}

/* ── Mark Printed ──────────────────────────────────────────────────────────── */
function sidMarkPrinted() {
    if (!sidCurrentCard || !sidCurrentCard.id_card_id) return;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    fetch(`${API}?action=update_status`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
        body: JSON.stringify({ id_card_id: sidCurrentCard.id_card_id, status: 'Printed' }),
    }).then(r => r.json()).then(d => {
        if (d.success) {
            sidCurrentCard.id_status = 'Printed';
            document.getElementById('sidBtnPrint').style.display = 'none';
            document.getElementById('sidCurrentStatus').innerHTML =
                `Status: <span class="mpl-status processing ms-1">Printed</span>`;
            sidFetch();
        } else { alert(d.error || 'Failed to update.'); }
    });
}
window.sidMarkPrinted = sidMarkPrinted;

function sidExecutePrint(cards) {
    let printArea = document.getElementById('sidPrintArea');
    if (!printArea) {
        printArea = document.createElement('div');
        printArea.id = 'sidPrintArea';
        document.body.appendChild(printArea);
    }
    
    printArea.innerHTML = cards.map(card => {
        const isSHS = (card.year_section || '').startsWith('G1');
        const accent = isSHS ? 'sid-id-accent-shs' : 'sid-id-accent-college';
        
        return `
        <div class="rcard-print-wrapper">
            <div class="rcard-face rcard-front">
                <div class="rcard-decoration ${accent}"></div>
                <div class="rcard-header">
                    <div class="rcard-logo-wrap">
                        <img src="<?php echo $logoUrl; ?>" alt="BCP Logo" class="rcard-logo">
                        <div class="rcard-school">
                            <strong>BESTLINK COLLEGE OF THE PHILIPPINES</strong>
                            <span>OFFICIAL STUDENT IDENTIFICATION CARD</span>
                        </div>
                    </div>
                    <img src="<?php echo $sealUrl; ?>" alt="BCP Seal" class="rcard-seal">
                </div>
                <div class="rcard-content">
                    <div class="rcard-photo"><i class="fas fa-user" style="font-size:3.5rem;color:#d1d5db;"></i></div>
                    <div class="rcard-details">
                        <div class="rcard-name">${e(card.last_first || '')}</div>
                        <div class="rcard-detail"><span class="rcard-label">Student No.</span><span class="rcard-value">${e(card.student_number || '')}</span></div>
                        <div class="rcard-detail"><span class="rcard-label">Program / Course</span><span class="rcard-value">${e(card.program_course || '—')}</span></div>
                        <div class="rcard-detail"><span class="rcard-label">Year / Section</span><span class="rcard-value">${e(card.year_section || '—')}</span></div>
                        <div class="rcard-detail"><span class="rcard-label">Valid Until</span><span class="rcard-value">${e(card.expiry_display || '—')}</span></div>
                    </div>
                    <div class="rcard-signature"><div class="rcard-sigline"></div>Authorized Signature</div>
                </div>
            </div>
            <div class="rcard-face rcard-back">
                <div class="rcard-decoration ${accent}" style="transform:rotate(-18deg);right:auto;left:-12%;"></div>
                <div class="rcard-back-header">STUDENT ID VERIFICATION</div>
                <div class="rcard-back-body">
                    <div class="rcard-back-text mb-2"><strong>Emergency Contact Information</strong></div>
                    <div class="rcard-back-row">
                        <div><div class="rcard-back-label">Contact Person</div><div class="rcard-back-value">${e(card.emergency_name || 'N/A')}</div></div>
                        <div><div class="rcard-back-label">Relationship</div><div class="rcard-back-value">${e(card.emergency_relation || '—')}</div></div>
                    </div>
                    <div class="rcard-back-full">
                        <div class="rcard-back-label">Contact Number</div><div class="rcard-back-value">${e(card.emergency_phone || '—')}</div>
                    </div>
                    <div class="rcard-back-full">
                        <div class="rcard-back-label">Home Address</div><div class="rcard-back-value rcard-back-value-wrap">${e(card.emergency_address || 'N/A')}</div>
                    </div>
                    <div class="rcard-back-policy" style="position:relative;z-index:2;">
                        This card is non-transferable. If found, please return to the Registrar's Office.
                        Loss of card must be reported immediately. Tel: (02) 8936-0819
                    </div>
                </div>
                <div class="rcard-back-sigs" style="position:relative;z-index:2;">
                    <div class="rcard-back-sig">College Registrar</div>
                    <div class="rcard-back-sig">School President</div>
                </div>
                <div class="rcard-back-footer" style="position:relative;z-index:2;">
                    ID# ${e(card.id_number || '—')} &nbsp;|&nbsp; Official Student Identification Card
                </div>
            </div>
        </div>`;
    }).join('');
    
    setTimeout(() => { window.print(); }, 500);
}

function sidPrintView() {
    if (sidChecked.size > 0) {
        sidShowSpinner(true);
        Promise.all([...sidChecked].map(id => fetch(`${API}?action=preview&id=${id}`).then(r => r.json())))
            .then(results => {
                sidShowSpinner(false);
                const cards = results.filter(r => r.success && r.card).map(r => r.card);
                if (cards.length === 0) { alert('No valid cards to print.'); return; }
                sidExecutePrint(cards);
            }).catch(() => {
                sidShowSpinner(false);
                alert('Failed to load card data for printing.');
            });
    } else {
        if (!sidCurrentCard) { alert('Please select a student from the list or check multiple students to print.'); return; }
        sidExecutePrint([sidCurrentCard]);
    }
}
window.sidPrintView = sidPrintView;

/* ── Reset Preview ─────────────────────────────────────────────────────────── */
function sidResetPreview() {
    sidCurrentCard = null; sidFlipped = false;
    const flipper = document.getElementById('sidCardFlipper');
    if (flipper) { flipper.classList.remove('flipped'); }
    document.getElementById('sidFlipLabel').textContent = 'Flip to Back';
    document.getElementById('sidPreviewEmpty').style.display = '';
    document.getElementById('sidPreviewContent').style.display = 'none';
}

/* ── Bulk Select ───────────────────────────────────────────────────────────── */
function sidToggleCheck(id) { sidChecked.has(id) ? sidChecked.delete(id) : sidChecked.add(id); }
window.sidToggleCheck = sidToggleCheck;

function sidToggleAll(cb) {
    document.querySelectorAll('.sid-row-check').forEach(c => {
        c.checked = cb.checked;
        sidToggleCheck(parseInt(c.value));
    });
    if (!cb.checked) sidChecked.clear();
}
window.sidToggleAll = sidToggleAll;

function sidSelectAll() {
    document.getElementById('sidCheckAll').checked = true;
    sidToggleAll(document.getElementById('sidCheckAll'));
}
window.sidSelectAll = sidSelectAll;

function sidBulkPrint() {
    if (sidChecked.size === 0) { alert('Select students first.'); return; }
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    fetch(`${API}?action=bulk_generate`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
        body: JSON.stringify({ student_ids: [...sidChecked], status: 'Printed' }),
    }).then(r => r.json()).then(d => {
        if (d.success) { sidChecked.clear(); sidFetch(); alert(d.message); }
        else { alert(d.error || 'Bulk update failed.'); }
    });
}
window.sidBulkPrint = sidBulkPrint;

/* ── Create ID Card Modal ──────────────────────────────────────────────────── */
function sidRebuildModalCourseDropdown(dept) {
    const courseLabel = document.getElementById('csidCourseLabel');
    const courseSelect = document.getElementById('csidCourse');
    const yearLabel = document.getElementById('csidYearLabel');
    const yearSelect = document.getElementById('csidYearLevel');

    if (dept === 'SHS') {
        courseLabel.textContent = 'Strand';
        courseSelect.innerHTML = '<option value="">— Select Strand —</option>' +
            SHS_STRANDS.map(s => `<option value="${s}">${s}</option>`).join('');
        yearLabel.textContent = 'Grade Level';
        yearSelect.innerHTML = SHS_YEAR_LEVELS.map(y => `<option value="${y.val}">${y.label}</option>`).join('');
    } else {
        courseLabel.textContent = 'Course / Program';
        courseSelect.innerHTML = '<option value="">— Select Course —</option>' +
            COLLEGE_PROGRAMS.map(p => `<option value="${p}">${e(p)}</option>`).join('');
        yearLabel.textContent = 'Year Level';
        yearSelect.innerHTML = COLLEGE_YEAR_LEVELS.map(y => `<option value="${y.val}">${y.label}</option>`).join('');
    }
}

function sidOpenCreateModal(forEdit) {
    sidRebuildModalCourseDropdown(sidDept);
    if (!forEdit) {
        document.getElementById('sidCreateForm').reset();
        document.getElementById('csidExpiry').value = '<?php echo $autoExpiry; ?>';
        sidResetPhotoBox();
    }
    const modal = new bootstrap.Modal(document.getElementById('sidCreateModal'));
    modal.show();
}
window.sidOpenCreateModal = sidOpenCreateModal;

function sidOpenCreateModalFor(studentId, sno, fname, lname, program, yearSec) {
    sidRebuildModalCourseDropdown(sidDept);
    document.getElementById('sidCreateForm').reset();
    document.getElementById('csidExpiry').value = '<?php echo $autoExpiry; ?>';
    document.getElementById('csidStudentNo').value = sno;
    document.getElementById('csidFirstName').value = fname;
    document.getElementById('csidLastName').value = lname;
    if (program) document.getElementById('csidCourse').value = program;
    if (yearSec) {
        const prefix = yearSec.split('-')[0];
        document.getElementById('csidYearLevel').value = prefix;
        document.getElementById('csidSection').value = yearSec.split('-').slice(1).join('-');
    }
    sidResetPhotoBox();
    const modal = new bootstrap.Modal(document.getElementById('sidCreateModal'));
    modal.show();
}
window.sidOpenCreateModalFor = sidOpenCreateModalFor;

function sidResetPhotoBox() {
    const box = document.getElementById('csidPhotoBox');
    box.innerHTML = '<span class="sid-photo-icon"><i class="fas fa-camera"></i></span>';
    document.getElementById('csidPhotoInput').value = '';
}

function sidPreviewPhoto(evt) {
    const file = evt.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('csidPhotoBox').innerHTML = `<img src="${e.target.result}" alt="Photo">`;
    };
    reader.readAsDataURL(file);
}
window.sidPreviewPhoto = sidPreviewPhoto;

function sidSubmitCreate(evt) {
    evt.preventDefault();
    const btn = document.getElementById('csidSubmitBtn');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Generating…';

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const yearLevel = document.getElementById('csidYearLevel').value;
    const section   = document.getElementById('csidSection').value;
    const yearSec   = section ? `${yearLevel}-${section}` : yearLevel;

    const fd = new FormData();
    fd.append('action', 'create');
    fd.append('csrf_token', csrf);
    fd.append('student_number', document.getElementById('csidStudentNo').value.trim());
    fd.append('first_name',     document.getElementById('csidFirstName').value.trim());
    fd.append('last_name',      document.getElementById('csidLastName').value.trim());
    fd.append('middle_name',    document.getElementById('csidMiddleName').value.trim());
    fd.append('suffix',         document.getElementById('csidSuffix').value.trim());
    fd.append('program_course', document.getElementById('csidCourse').value);
    fd.append('year_section',   yearSec);
    fd.append('batch_year',     document.getElementById('csidBatchYear').value);
    fd.append('emergency_name', document.getElementById('csidEmergencyName').value.trim());
    fd.append('emergency_phone',document.getElementById('csidEmergencyPhone').value.trim());
    fd.append('address',        document.getElementById('csidAddress').value.trim());

    const photoFile = document.getElementById('csidPhotoInput').files[0];
    if (photoFile) fd.append('photo', photoFile);

    fetch(`${API}?action=create`, {
        method: 'POST',
        headers: { 'X-CSRF-Token': csrf },
        body: fd,
    }).then(r => r.json()).then(d => {
        btn.disabled = false; btn.innerHTML = '<i class="fas fa-id-card me-1"></i> Generate ID Card';
        if (d.success) {
            bootstrap.Modal.getInstance(document.getElementById('sidCreateModal')).hide();
            sidFetch();
            setTimeout(() => sidLoadPreview(d.student_id), 400);
        } else { alert(d.error || 'Failed to create ID card.'); }
    }).catch(() => {
        btn.disabled = false; btn.innerHTML = '<i class="fas fa-id-card me-1"></i> Generate ID Card';
        alert('Network error. Please try again.');
    });
}
window.sidSubmitCreate = sidSubmitCreate;

/* ── Spinner ───────────────────────────────────────────────────────────────── */
function sidShowSpinner(show) {
    const s = document.getElementById('sidLoadSpinner');
    s.style.setProperty('display', show ? 'flex' : 'none', 'important');
}

/* ── Utilities ─────────────────────────────────────────────────────────────── */
function e(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function debounce(fn, ms) {
    let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
}

})();
</script>
