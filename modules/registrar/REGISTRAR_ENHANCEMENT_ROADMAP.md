# Bestlink College of the Philippines (BCP)
## Student Management System 2.0 — Office of the Registrar
### Research-Based Enhancement Roadmap & Strategic Add-Ons Specification

---

**Document Control:**
- **Author:** System Architecture & Educational Technology Advisory
- **Scope:** Registrar Module Only (`modules/registrar/`)
- **Standards Grounding:** 
  - AACRAO (American Association of Collegiate Registrars and Admissions Officers)
  - CHED (Commission on Higher Education, Philippines — CMO No. 40 s. 2008, RA 9163)
  - Data Privacy Act of 2012 (RA 10173) & National Privacy Commission (NPC)
  - National Archives of the Philippines (NAP General Circulars 1 & 2)
  - W3C Verifiable Credentials & PESC (Postsecondary Electronic Standards Council)
- **Status:** Approved Strategic Roadmap (No code or UI modified)

---

## Executive Summary

This specification provides an actionable, research-backed blueprint designed to transform the BCP Registrar Module into a best-in-class, audit-proof, and highly automated collegiate registrar system. 

The recommendations resolve the primary operational bottlenecks identified in collegiate registrar research:
1. Manual degree and transcript counting errors during graduation periods.
2. Compliance overhead during CHED regional audits and government reporting.
3. Vulnerability to academic credential forgery and manual verification delays.
4. Uncoordinated inter-departmental clearances resulting in premature document releases.
5. Inability to systematically enforce academic retention, residency rules, and data privacy suppressions.

---

## 1. Regulatory & Government Compliance (CHED / DepEd Mandates)

### 1.1 Special Order (S.O.) Number Registry & Tracking
- **Regulatory Standard:** CHED CMO No. 40, s. 2008 (Manual of Regulation for Private Higher Education Institutions) mandates that private HEIs submit graduate lists to CHED for issuance of Special Order numbers prior to the conferment of valid collegiate degrees and official transcript issuance.
- **Current State:** `reg_students` has `status = 'Graduated'`, but lacks fields for Special Order numbers, date issued, and transmittal tracking.
- **Implementation Scope:**
  - Database: Add `so_number` (VARCHAR 80), `so_issued_date` (DATE), `so_status` (ENUM: Pending, Issued, Exempted), and `so_notes` to `reg_students` or a dedicated `reg_graduation_records` table.
  - UI: Batch S.O. number encoder inside `pages/student-status-tracker.php`.
  - Output: Automated inclusion of S.O. details in the header/footer of official Transcripts of Records (TOR) and graduation certification letters.

### 1.2 NSTP (National Service Training Program) Serial Number Registry
- **Regulatory Standard:** Republic Act No. 9163 (NSTP Act of 2001) mandates that students completing CWTS, LTS, or ROTC receive a permanent National Service Reserve Corps (NSRC) or military serial number issued by CHED/DND.
- **Current State:** NSTP subjects exist in `reg_academic_subjects` merely as coursework entries, without serial tracking.
- **Implementation Scope:**
  - Database: Create `reg_nstp_records` containing:
    - `student_id` (FK)
    - `component` (CWTS, LTS, ROTC)
    - `serial_number` (Unique alphanumeric string)
    - `academic_year`, `semester`, `completion_date`
    - `transmittal_no` (CHED regional office transmittal reference)
  - Output: One-click export of CHED NSTP Completion Reports and automatic printing on official transcripts.

### 1.3 CHED Electronic Enrollment List (EEL) & Form IX Generator
- **Regulatory Standard:** CHED Regional Offices require semestral submissions of Form IX (Official Enrollment List) and Form XVIII (Statistical Enrollment Report) categorizing students by program, year level, citizenship, and scholarship grant.
- **Current State:** `pages/student-masterlist-generator.php` exports generic CSV/PDF lists based on active section assignments.
- **Implementation Scope:**
  - Presets: Add standard "CHED Form IX (EEL)" and "CHED Form XVIII" export presets to the Masterlist Generator.
  - Columns: Learner Reference Number (LRN), Program Major, Year Level, Total Academic Units Enrolled, NSTP component status, and Scholarship Classification (UniFAST / TES / TDP / Private).

### 1.4 CAV (Certificate of Authentication and Verification) Packager
- **Regulatory Standard:** Graduates pursuing overseas employment, foreign graduate study, or Department of Foreign Affairs (DFA) Apostille certification require Registrar-issued CAV transmittals to CHED.
- **Implementation Scope:**
  - Add "CAV Endorsement Packet" as an official document type in `pages/document-requests.php`.
  - Automated batch generation of Certified True Copies (CTC) of Transcript of Records and Diploma with cryptographic SHA-256 validation stamps and formal transmittal endorsement to the CHED Regional Director.

---

## 2. Academic Integrity & Degree Audit Engine (AACRAO Benchmarks)

### 2.1 Automated Degree Audit & Curriculum Evaluation Engine
- **Academic Benchmark:** AACRAO Core Competencies emphasize automated, rule-based degree audit engines that compare earned credits against the curriculum prospectus to definitively evaluate graduation eligibility.
- **Current State:** `pages/academic-history.php` displays historical term grades, but requires registrar staff to manually cross-reference curriculum guides.
- **Implementation Scope:**
  - Algorithm: Compare all passed subjects in `reg_academic_subjects` against the official program curriculum requirements.
  - Visualization:
    - Total Units Required vs. Units Earned (e.g., "122 / 146 Units — 83.5% Completed").
    - Categorized buckets: General Education, Major Core, Professional Electives, Physical Education (PE 1-4), and NSTP (1 & 2).
    - Deficiencies Table: Clear highlight of lacking subjects or failed prerequisites blocking upcoming semester enrollment.

### 2.2 Academic Standing & Maximum Residency Rule (MRR) Monitor
- **Academic Benchmark:** Academic retention policies dictate clear classifications for probationary students and enforcement of Maximum Residency Limits ($N + 2$ years, allowing 6 years for a 4-year degree).
- **Implementation Scope:**
  - Automated GWA Calculation Engine: Evaluates term GWA and cumulative GWA (CGWA).
  - Standing Classifications:
    - **President's / Dean's Honor List:** GWA ≤ 1.50, regular load (≥ 18 units), no grade below 2.00, no INC/DRP.
    - **Good Standing:** GWA 1.51 - 2.75.
    - **Academic Warning:** GWA 2.76 - 3.00.
    - **Academic Probation:** Cumulative GWA > 3.00 or failed > 50% of enrolled units.
    - **MRR Overstay Alert:** Triggers when matriculation age exceeds 12 active semesters.
  - Dashboard: Surface retention and alert cards on `pages/profile-dashboard.php`.

### 2.3 Formal "Change of Grade" (COG) & Incomplete (INC) Resolution Workflow
- **Academic Benchmark:** Immutable registrar ledgering — grades once posted cannot be quietly edited without formal dual-authorization and permanent audit notes.
- **Implementation Scope:**
  - Database: Create `reg_grade_modifications`:
    - `id`, `subject_id`, `student_id`
    - `previous_grade`, `new_grade`
    - `change_type` (INC Completion, Removal Exam, Encoding Correction, Grade Dispute)
    - `instructor_name`, `dean_approval_ref`, `authorized_by` (Registrar user ID)
    - `memo_file_id` (Link to uploaded signed Dean's memo in `reg_files`)
    - `created_at`
  - Automated INC Expiry: Marks labeled "INC" automatically trigger a 365-day countdown. If unresolved after one calendar year, the system automatically marks the grade as "5.00 / Expired Incomplete" per standard collegiate policy.

---

## 3. Cryptographic Security & Document Fraud Prevention

### 3.1 Completion of QR Code Authentication & Verification Portal
- **Current State:** `pages/qr-code-authentication.php` is marked "Under Development — Phase 5", despite core RSA-SHA256 signing and hash computation functions already being built in `includes/signing-service.php`.
- **Implementation Scope:**
  - Finalize the public-facing verification route accessible via mobile phone QR scan.
  - When scanned by an employer, embassy, or certifying agency, the portal displays:
    - Verified BCP Institutional Crest and Cryptographic Validity Badge
    - Student Full Name, ID Number, Program & Graduation Date
    - Document Classification (e.g., Official Transcript of Records)
    - Date Issued & Registrar Electronic Signature verification
    - SHA-256 PDF Hash match indicator (confirms the physical/digital document has not been altered by a single pixel or byte)
  - Security: Rate-limit queries to prevent scraping; log verification timestamp and IP into `reg_verification_codes.verified_count`.

### 3.2 High-Fidelity Official Transcript of Records (TOR) Template
- **Current State:** Templates currently exist for COE, Certificate of Grades, Good Moral, and SOA in `modules/registrar/DocuFormat/`.
- **Implementation Scope:**
  - Create `modules/registrar/DocuFormat/transcript-of-records.html`:
    - Multi-page pagination engine with dynamic header repeats ("Page X of Y").
    - Standard collegiate grading system key (1.00 = 98-100% Excellent, 5.00 = Failed).
    - Previous school accreditation, admission credentials, and entrance status.
    - Micro-print border and anti-photocopy background security grid pattern.
    - Official conferred degree, graduation date, Special Order (S.O.) number, and official registrar signature box.

### 3.3 W3C Verifiable Credentials & PDF/A Archival Standards
- **Standard:** PESC XML & W3C Verifiable Credentials recommendation for interoperable, paperless educational credentials.
- **Implementation Scope:**
  - Export all generated registrar certificates and transcripts in standard PDF/A-3 (ISO 19005-3) format for 50+ year permanent archival.
  - Embed cryptographic signature metadata conforming to PAdES (PDF Advanced Electronic Signatures) so that Adobe Acrobat automatically displays "Signed and all signatures are valid".

---

## 4. Operational Automation & Service Delivery (Queue & Clearances)

### 4.1 Cross-Departmental Clearance Hold / Gatekeeper System
- **Operational Benchmark:** Prevent unauthorized issuance of certificates, transcripts, or diplomas to students with outstanding institutional liabilities.
- **Implementation Scope:**
  - Database: Create `reg_student_holds`:
    - `student_id` (FK)
    - `hold_type` (Accounting Balance, Library Unreturned Book, Clinic Medical Deficiency, Disciplinary / Prefect Case, Documentary Deficiency)
    - `issuing_department`
    - `description`, `amount_due` (if financial)
    - `status` (Active, Cleared)
    - `placed_at`, `cleared_at`, `cleared_by`
  - Integration: When an active hold exists, `pages/document-requests.php`, `pages/document-release.php`, and `pages/walk-in-transactions.php` automatically block processing with an explicit modal explaining the required clearance.

### 4.2 Automated Transactional Notifications (SMS / Email)
- **Efficiency Gain:** Eliminates student queueing and overcrowding at the counter by automating status notifications.
- **Implementation Scope:**
  - Event-driven notifications triggered automatically:
    1. *Request Received:* "Your request REQ-2026-0042 has been logged."
    2. *In Review / Payment Verified:* "Your request is currently being processed by the Registrar."
    3. *Ready for Release:* "Your document is printed, sealed, and ready for pickup at Registrar Window 2. Bring your Student ID."
    4. *Released:* "Document REQ-2026-0042 was claimed on [Date] by [Claimant Name]."

### 4.3 Walk-In Queuing Ticket Generator & Counter Display
- **Current State:** `pages/walk-in-transactions.php` enables over-the-counter transaction input, but lacks live queuing management.
- **Implementation Scope:**
  - Queuing Ticket Generator: Issues printable counter slips (e.g., `REG-A012` for Documents, `REG-B005` for Inquiries).
  - Digital Counter Screen View: Lightweight fullscreen view for lobby monitors showing: "Now Serving: Counter 1 &rarr; REG-A012".

---

## 5. Information Governance & Records Management (RA 10173 & NAP)

### 5.1 National Archives of the Philippines (NAP) Records Retention Schedule
- **Compliance Standard:** National Archives of the Philippines (NAP) General Circular No. 1 and 2 (Records Disposition Schedule for Academic Records) specifies:
  - Permanent Retention: Transcripts of Records, Graduation Masterlists, Board Examination Certification.
  - 5-Year Retention: Form 137 / 138 from prior schools, subject drop forms, change of grade requests.
  - 1-Year Retention: Daily document request slips, excuse letters, clinic referral slips.
- **Implementation Scope:**
  - Add retention tags (`retention_period`: Permanent, 5_Years, 1_Year) to `reg_files`.
  - Automated archival routine: Archives files older than their retention threshold into compressed yearly storage volumes, ensuring active database query speeds remain blazing fast.

### 5.2 FERPA / RA 10173 Privacy Suppression ("Directory Information Opt-Out")
- **Compliance Standard:** Philippine Data Privacy Act (RA 10173) and global FERPA standards grant students the right to restrict the publication of their educational records.
- **Implementation Scope:**
  - Add `privacy_restricted` (TINYINT 1) to `reg_students`.
  - When enabled, the student's name, honors, and photos are automatically masked from public masterlists, press releases, social media honor rolls, and public portal searches.

### 5.3 Detailed Field-Level Audit Diff Tracker
- **Audit Standard:** Legal disputes regarding student names, dates of birth, or grade alterations require a verifiable data diff trail.
- **Implementation Scope:**
  - Upgrade `regLog()` into a structured table `reg_audit_diffs`:
    - `table_name`, `record_id`
    - `field_name`, `old_value`, `new_value`
    - `action` (Insert, Update, Delete, Override)
    - `reason`, `authorized_by`, `ip_address`, `timestamp`

---

## Strategic Implementation Roadmap

| Phase | Milestone Focus | Deliverables | Target Beneficiaries |
| :--- | :--- | :--- | :--- |
| **Phase 1** | **Cryptographic Security & Authentic Transcripts** | Complete `qr-code-authentication.php`, build multi-page TOR template with anti-forgery grading key. | Employers, Embassies, Graduating Students |
| **Phase 2** | **Academic Evaluation & Retention Engine** | Degree Audit progress tracker, Prerequisite validation, Honors & Probation GWA engine, Change of Grade ledger. | Academic Deans, Department Heads, Students |
| **Phase 3** | **Regulatory & Government Compliance** | Special Order (S.O.) Number registry, NSTP serial number registry, CHED Form IX / EEL exports, CAV packager. | CHED Regional Office, Accrediting Bodies (PACUCOA / ALCUCOA) |
| **Phase 4** | **Counter Operations & Clearance Integration** | Cross-departmental clearance holds, Automated SMS/Email lifecycle notifications, Lobby queuing screen. | Front-Desk Staff, Walk-In Students |
| **Phase 5** | **Data Governance & Legal Archival** | NAP retention scheduler, Data Privacy suppression toggle, Field-level JSON audit diff tracking. | Data Protection Officer (DPO), Legal Counsel |

---

*This document is maintained as an active operational reference for the development and continuous enhancement of the SMS 2.0 Registrar System.*

