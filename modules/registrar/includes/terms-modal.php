<?php
/**
 * SMS 2 - Registrar Terms and Conditions Modal
 */
if (getCurrentUserRoleKey() !== 'registrar') {
    return;
}

$isAgreed = false;
if (isset($_SESSION['registrar_terms_agreed']) && $_SESSION['registrar_terms_agreed']) {
    $isAgreed = true;
}

$pdo = db();
if (!$pdo) {
    return;
}

$stmt = $pdo->prepare('SELECT registrar_terms_agreed, full_name, digital_signature FROM users WHERE id = ?');
$stmt->execute([getCurrentUserId()]);
$userRow = $stmt->fetch();

if (!$isAgreed && !empty($userRow['registrar_terms_agreed'])) {
    $_SESSION['registrar_terms_agreed'] = true;
    $isAgreed = true;
}

$fullName = $userRow['full_name'] ?? getCurrentUserName();
$signature = $userRow['digital_signature'] ?? null;
?>
<style>
    .terms-section h5 {
        color: var(--bs-primary);
        font-size: 1.15rem;
        margin-bottom: 1rem;
    }
    .terms-section p, .terms-section ul li {
        font-size: 0.95rem;
        line-height: 1.7;
        opacity: 0.9;
    }
    .terms-section ul {
        padding-left: 1.5rem;
    }
    .terms-signature-box {
        background: rgba(var(--bs-primary-rgb), 0.05);
        border-left: 4px solid var(--bs-primary);
    }
    /* Fallback in case css variables fail in dark mode */
    .modal-content {
        color: inherit;
    }
</style>

<div class="modal fade" id="registrarTermsModal" <?= !$isAgreed ? 'data-bs-backdrop="static" data-bs-keyboard="false"' : '' ?> tabindex="-1" aria-labelledby="registrarTermsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold" id="registrarTermsModalLabel">Registrar Staff Terms and Conditions</h5>
                <?php if ($isAgreed): ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                <?php endif; ?>
            </div>
            <div class="modal-body p-4 p-md-5">
                <!-- STEP 1 CONTENT -->
                <div id="step-1-content">
                    <div class="terms-section mb-5">
                        <h5 class="fw-bold">Purpose</h5>
                        <p>These Terms and Conditions establish the responsibilities, acceptable use, security requirements, and professional obligations of authorized Registrar Staff who access and use the Registrar Student Information System (Registrar SIS).</p>
                        <p>The system is intended to support the secure management, verification, processing, generation, and release of official student and registrar documents.</p>
                        <p class="mb-0">By accessing the Registrar SIS, the authorized staff member acknowledges and agrees to comply with these Terms and Conditions and with applicable school policies, information security policies, and Philippine laws and regulations.</p>
                    </div>

                    <div class="terms-section mb-5">
                        <h5 class="fw-bold">1. Authorized System Access</h5>
                        <p>Registrar Staff shall access the system only through their assigned account and only for legitimate duties related to their authorized position.</p>
                        <p>Staff members shall:</p>
                        <ul>
                            <li>Use only their own account credentials.</li>
                            <li>Keep their username and password confidential.</li>
                            <li>Never share account credentials with another person.</li>
                            <li>Log out after completing their work or when leaving an unattended workstation.</li>
                            <li>Access only information necessary for their assigned responsibilities.</li>
                            <li>Immediately report suspected unauthorized access or account compromise to the appropriate school administrator or authorized system administrator.</li>
                        </ul>
                    </div>

                    <div class="terms-section mb-5">
                        <h5 class="fw-bold">2. Confidentiality of Student Information</h5>
                        <p>Registrar Staff may have access to personal and academic information, including student identification information, academic records, document requests, contact information, and other information maintained by the Registrar.</p>
                        <p>Such information shall be treated as confidential and shall only be accessed, processed, or disclosed for authorized school-related purposes.</p>
                        <p>Staff shall not:</p>
                        <ul>
                            <li>Browse student records without a legitimate work-related purpose.</li>
                            <li>Copy, photograph, download, or distribute student information without authorization.</li>
                            <li>Share student records through personal social media, messaging applications, personal email accounts, or unauthorized storage services.</li>
                            <li>Discuss confidential student information with unauthorized individuals.</li>
                            <li>Use student information for personal, commercial, or unrelated purposes.</li>
                        </ul>
                    </div>

                    <div class="terms-section mb-5">
                        <h5 class="fw-bold">3. Document Processing and Verification</h5>
                        <p>Registrar Staff responsible for document processing shall ensure that information contained in official documents is properly verified before a document is released.</p>
                        <p>Staff shall exercise reasonable care when:</p>
                        <ul>
                            <li>Reviewing student information.</li>
                            <li>Verifying document requests.</li>
                            <li>Generating official documents.</li>
                            <li>Approving or authorizing documents.</li>
                            <li>Applying authorized digital signatures.</li>
                            <li>Sending documents to students.</li>
                            <li>Handling physical copies of registrar documents.</li>
                        </ul>
                        <p class="mb-0">Staff shall not knowingly approve, generate, alter, or release a document containing false, unauthorized, or unverified information.</p>
                    </div>

                    <div class="terms-section mb-5">
                        <h5 class="fw-bold">4. Digital Signature and Document Authorization</h5>
                        <p>Where the Registrar SIS provides digital signature functionality, the staff member shall protect the confidentiality and integrity of their digital signature credentials and authorization.</p>
                        <p>A staff member shall not:</p>
                        <ul>
                            <li>Allow another person to use their signature or signing authorization.</li>
                            <li>Apply their signature to a document they are not authorized to approve.</li>
                            <li>Approve a document without performing the required verification.</li>
                            <li>Modify an authorized document after signing without following the school's document-control procedures.</li>
                        </ul>
                        <p class="mb-0">Digital signatures and cryptographic verification mechanisms implemented by the system are intended to support document integrity and authenticity. They do not replace the staff member's responsibility to verify the correctness and legitimacy of the document before authorization.</p>
                    </div>

                    <div class="terms-section mb-5">
                        <h5 class="fw-bold">5. Document Requests</h5>
                        <p>Registrar Staff shall process student document requests according to established school procedures.</p>
                        <p>Staff shall:</p>
                        <ul>
                            <li>Review the request details.</li>
                            <li>Verify the identity and relevant student information.</li>
                            <li>Verify the requested document type.</li>
                            <li>Verify required supporting information or requirements.</li>
                            <li>Process the request according to the applicable workflow.</li>
                            <li>Update the request status accurately.</li>
                            <li>Release the document only through an authorized delivery method.</li>
                        </ul>
                    </div>

                    <div class="terms-section mb-5">
                        <h5 class="fw-bold">6. System Security</h5>
                        <p>Staff members are responsible for maintaining the security of their assigned account and workstation.</p>
                        <p>Staff shall not attempt to:</p>
                        <ul>
                            <li>Bypass system authentication.</li>
                            <li>Access restricted modules without authorization.</li>
                            <li>Modify system security controls.</li>
                            <li>Access another employee's account.</li>
                            <li>Manipulate system records without authorization.</li>
                            <li>Circumvent audit logs or other security controls.</li>
                            <li>Introduce malicious software or unauthorized code into the system.</li>
                        </ul>
                        <p class="mb-0">Any suspected security incident shall be reported immediately to the designated system administrator or appropriate school authority.</p>
                    </div>

                    <div class="terms-section mb-5">
                        <h5 class="fw-bold">7. Accuracy and Responsible Use</h5>
                        <p>Registrar Staff shall provide accurate information when creating, updating, verifying, or processing records.</p>
                        <p class="mb-0">If an error is discovered, staff should follow the school's established correction procedure rather than making unauthorized modifications.</p>
                    </div>

                    <div class="terms-section mb-5">
                        <h5 class="fw-bold">8. Audit and Accountability</h5>
                        <p>System activities may be recorded through appropriate audit logs for security, accountability, troubleshooting, and administrative purposes.</p>
                        <p>Staff members understand that actions performed using their assigned account may be associated with their account.</p>
                        <p class="mb-0">Accordingly, staff members should not allow another person to use their account.</p>
                    </div>

                    <div class="terms-section mb-5">
                        <h5 class="fw-bold">9. Prohibited Activities</h5>
                        <p>The following activities are prohibited:</p>
                        <ul>
                            <li>Unauthorized access to student or staff records.</li>
                            <li>Unauthorized disclosure of confidential information.</li>
                            <li>Sharing account credentials.</li>
                            <li>Unauthorized alteration or deletion of records.</li>
                            <li>Fraudulent document generation.</li>
                            <li>Unauthorized use of another staff member's digital signature.</li>
                            <li>Downloading or transferring confidential records for unauthorized purposes.</li>
                            <li>Using the system for personal or unrelated activities.</li>
                            <li>Attempting to circumvent security controls.</li>
                            <li>Using system information for harassment, discrimination, personal gain, or other unauthorized purposes.</li>
                        </ul>
                    </div>

                    <div class="terms-section mb-5">
                        <h5 class="fw-bold">10. Account Suspension or Revocation</h5>
                        <p>The school may suspend, restrict, or revoke a staff member's system access when necessary for security, administrative, employment, investigation, or policy-compliance purposes, subject to applicable school rules and policies.</p>
                        <p class="mb-0">Access may also be removed when a staff member's role or authorization no longer requires access to the Registrar SIS.</p>
                    </div>

                    <div class="terms-section mb-5">
                        <h5 class="fw-bold">11. Data Privacy Compliance</h5>
                        <p>Registrar Staff shall process personal information in accordance with the school's applicable privacy policies and the Philippine Data Privacy Act of 2012 <strong>(Republic Act No. 10173)</strong>, its implementing rules and regulations, and applicable National Privacy Commission issuances.</p>
                        <p class="mb-0">Personal information shall be processed only for authorized and legitimate purposes and with appropriate safeguards.</p>
                    </div>

                    <div class="terms-section mb-0">
                        <h5 class="fw-bold">12. Acknowledgment</h5>
                        <p>By selecting "I Agree", the Registrar Staff member confirms that they:</p>
                        <ul>
                            <li>Have read and understood these Terms and Conditions.</li>
                            <li>Agree to comply with the rules governing Registrar SIS access.</li>
                            <li>Understand their responsibility to protect confidential information.</li>
                            <li>Understand that their system activities may be logged for security and accountability.</li>
                            <li>Agree to use the system only for authorized school-related purposes.</li>
                            <li>Agree to report suspected security or privacy incidents through the appropriate school channels.</li>
                        </ul>
                        
                        <div class="terms-signature-box p-4 rounded mt-5">
                            <div class="row gy-3 align-items-center">
                                <div class="col-sm-4 fw-bold opacity-75">Staff Name:</div>
                                <div class="col-sm-8 fw-bold fs-5"><?= htmlspecialchars($fullName) ?></div>
                                
                                <div class="col-sm-4 fw-bold opacity-75">Signature:</div>
                                <div class="col-sm-8">
                                    <?php if ($signature): ?>
                                        <img src="<?= htmlspecialchars(BASE_URL . $signature) ?>" alt="Signature" style="max-height: 50px;" class="rounded bg-white p-1 shadow-sm">
                                    <?php else: ?>
                                        <span class="fst-italic opacity-50">No digital signature uploaded</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-sm-4 fw-bold opacity-75">Date:</div>
                                <div class="col-sm-8 fw-medium"><?= date('F j, Y h:i A') ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Data Privacy Notice -->
                <div id="step-2-content" style="display: none;">
                    <div class="terms-section mb-5">
                        <h5 class="fw-bold">Introduction</h5>
                        <p>Bestlink College of the Philippines, Inc. recognizes the importance of protecting the privacy and security of personal information entrusted to the school.</p>
                        <p>This Data Privacy Notice explains how personal information is collected, processed, stored, accessed, disclosed, retained, and protected in connection with the Registrar Student Information System (Registrar SIS).</p>
                        <p>The processing of personal information shall be carried out in accordance with the Philippine Data Privacy Act of 2012 (Republic Act No. 10173), its implementing rules and regulations, and applicable issuances of the National Privacy Commission.</p>
                        <p class="mb-0">The Data Privacy Act establishes requirements for the lawful and responsible processing of personal information and recognizes the rights of individuals whose personal data are processed.</p>
                    </div>

                    <div class="terms-section mb-5">
                        <h5 class="fw-bold">2. Personal Information We May Process</h5>
                        <p>Depending on the transaction and the user's role, the Registrar SIS may process information such as:</p>
                        <div class="row">
                            <div class="col-md-6">
                                <ul>
                                    <li>Student number and identification information</li>
                                    <li>Full name</li>
                                    <li>Date of birth</li>
                                    <li>Contact information</li>
                                    <li>Email address</li>
                                    <li>Program or course</li>
                                    <li>Year level and section</li>
                                    <li>Enrollment information</li>
                                    <li>Academic records</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul>
                                    <li>Student status</li>
                                    <li>Document request information</li>
                                    <li>Registrar transaction information</li>
                                    <li>Uploaded supporting documents</li>
                                    <li>Student identification photographs</li>
                                    <li>Staff account information</li>
                                    <li>Staff identification information</li>
                                    <li>Digital signature information</li>
                                    <li>System activity and audit records</li>
                                </ul>
                            </div>
                        </div>
                        <p class="mb-0 mt-2">Only information reasonably necessary for the applicable legitimate school function or transaction should be collected and processed.</p>
                    </div>

                    <div class="terms-section mb-5">
                        <h5 class="fw-bold">3. Purposes of Processing</h5>
                        <p>Personal information may be processed for legitimate registrar and academic-administrative purposes, including:</p>
                        <ul>
                            <li>Maintaining student records.</li>
                            <li>Verifying student identity and academic information.</li>
                            <li>Processing requests for official documents.</li>
                            <li>Generating official registrar documents.</li>
                            <li>Verifying the authenticity and integrity of generated documents.</li>
                            <li>Managing document requests and their status.</li>
                            <li>Communicating with students regarding registrar transactions.</li>
                            <li>Maintaining staff accounts and access permissions.</li>
                            <li>Maintaining system security and audit records.</li>
                            <li>Preventing unauthorized access, alteration, or disclosure.</li>
                            <li>Complying with applicable legal and institutional requirements.</li>
                        </ul>
                        <p class="mb-0">The National Privacy Commission identifies transparency, legitimate purpose, and proportionality as fundamental principles governing the processing of personal data.</p>
                    </div>

                    <div class="terms-section mb-5">
                        <h5 class="fw-bold">4. How Information Is Protected</h5>
                        <p>The Registrar SIS may implement technical and organizational safeguards designed to protect personal information from unauthorized access, alteration, disclosure, loss, or destruction.</p>
                        <p>Depending on the system implementation, safeguards may include:</p>
                        <div class="row">
                            <div class="col-md-6">
                                <ul>
                                    <li>User authentication.</li>
                                    <li>Role-based access control.</li>
                                    <li>Password protection.</li>
                                    <li>Access restrictions.</li>
                                    <li>Audit logging.</li>
                                    <li>Secure database practices.</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul>
                                    <li>Document integrity verification.</li>
                                    <li>SHA-256 hashing.</li>
                                    <li>RSA-based digital signing or verification.</li>
                                    <li>QR-based document validation.</li>
                                    <li>Controlled document access.</li>
                                    <li>Secure transmission of authorized documents.</li>
                                </ul>
                            </div>
                        </div>
                        <p class="mb-0 mt-2">These security mechanisms are intended to reduce risks associated with unauthorized access and document manipulation.</p>
                    </div>

                    <div class="terms-section mb-5">
                        <h5 class="fw-bold">5. Access to Personal Information</h5>
                        <p>Access to student and staff information shall be limited according to authorized responsibilities.</p>
                        <p>Registrar Staff may only access personal information that is necessary for their assigned duties.</p>
                        <p class="mb-0">Access to personal information does not automatically authorize a staff member to disclose, copy, download, modify, or distribute that information for purposes outside their authorized responsibilities.</p>
                    </div>

                    <div class="terms-section mb-5">
                        <h5 class="fw-bold">6. Disclosure and Sharing</h5>
                        <p>Personal information shall not be disclosed to unauthorized persons.</p>
                        <p>Information may be disclosed or shared when there is a lawful and legitimate basis, when necessary to provide an authorized school service, when required or permitted by applicable law, or when otherwise properly authorized.</p>
                        <p class="mb-0">Where applicable, appropriate safeguards shall be implemented when personal information is shared with authorized service providers or other parties.</p>
                    </div>

                    <div class="terms-section mb-5">
                        <h5 class="fw-bold">7. Document Verification</h5>
                        <p>Official documents generated through the Registrar SIS may contain a QR code or other verification mechanism that allows an authorized person to verify document authenticity or status.</p>
                        <p>The verification system should disclose only the information necessary to confirm the validity and identity of the document.</p>
                        <p class="mb-0">It should not unnecessarily expose confidential student information.</p>
                    </div>

                    <div class="terms-section mb-5">
                        <h5 class="fw-bold">8. Data Retention</h5>
                        <p>Personal information and registrar records shall be retained only for as long as necessary to fulfill the declared purposes of processing, comply with applicable legal or institutional requirements, or satisfy legitimate record-retention requirements.</p>
                        <p>When records are no longer required and may legally be disposed of, appropriate disposal procedures shall be applied to prevent unauthorized recovery or access.</p>
                        <p class="mb-0">The specific retention period should follow the school's official records-retention policy and applicable legal requirements.</p>
                    </div>

                    <div class="terms-section mb-5">
                        <h5 class="fw-bold">9. Rights of Data Subjects</h5>
                        <p>Under the Data Privacy Act of 2012, data subjects have recognized rights concerning their personal information.</p>
                        <p>These include, subject to applicable legal limitations:</p>
                        <ul>
                            <li>Right to be informed about the processing of personal information.</li>
                            <li>Right to access personal information being processed.</li>
                            <li>Right to correct or rectify inaccurate or incomplete information.</li>
                            <li>Right to object to certain forms of processing.</li>
                            <li>Right to erasure or blocking when legally applicable.</li>
                            <li>Right to data portability, where applicable.</li>
                            <li>Right to file a complaint concerning violations of data privacy rights.</li>
                            <li>Right to damages where provided by law.</li>
                        </ul>
                        <p class="mb-0">The National Privacy Commission provides guidance on these data subject rights.</p>
                    </div>

                    <div class="terms-section mb-5">
                        <h5 class="fw-bold">10. Data Privacy Inquiries and Requests</h5>
                        <p>Questions, requests, concerns, or complaints regarding the processing of personal information may be directed to the school's designated Data Protection Officer or authorized privacy office.</p>
                        <ul class="list-unstyled opacity-85 ps-3 border-start border-3 border-primary">
                            <li class="mb-2"><strong>Data Protection Officer:</strong> [Insert Name]</li>
                            <li class="mb-2"><strong>Office:</strong> [Insert Office]</li>
                            <li class="mb-2"><strong>Email:</strong> [Insert Official Email Address]</li>
                            <li class="mb-2"><strong>Telephone:</strong> [Insert Contact Number]</li>
                            <li class="mb-0"><strong>Office Address:</strong> [Insert Official School Address]</li>
                        </ul>
                    </div>

                    <div class="terms-section mb-5">
                        <h5 class="fw-bold">11. Security and Privacy Incidents</h5>
                        <p>Any suspected unauthorized access, disclosure, loss, alteration, or other security incident involving personal information should be reported immediately through the school's designated reporting channel.</p>
                        <p class="mb-0">Prompt reporting allows the appropriate school personnel to investigate and apply appropriate response and mitigation procedures.</p>
                    </div>

                    <div class="terms-section mb-5">
                        <h5 class="fw-bold">12. Updates to This Privacy Notice</h5>
                        <p>The school may update this Data Privacy Notice when there are changes to the Registrar SIS, processing activities, applicable laws, regulations, institutional policies, or security practices.</p>
                        <p class="mb-0">Where required, appropriate notice shall be provided regarding significant changes to the processing of personal information.</p>
                    </div>

                    <div class="terms-section mb-0">
                        <h5 class="fw-bold">13. Acknowledgment</h5>
                        <p>By selecting "I Agree", the user acknowledges that they have read and understood this Data Privacy Notice and have been informed about the nature and purposes of the processing of personal information in connection with the Registrar SIS.</p>
                        
                        <div class="terms-signature-box p-4 rounded mt-5">
                            <div class="row gy-3 align-items-center">
                                <div class="col-sm-4 fw-bold opacity-75">User/Staff Name:</div>
                                <div class="col-sm-8 fw-bold fs-5"><?= htmlspecialchars($fullName) ?></div>
                                
                                <div class="col-sm-4 fw-bold opacity-75">Date:</div>
                                <div class="col-sm-8 fw-medium"><?= date('F j, Y h:i A') ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer border-top px-4 py-3">
                <!-- STEP 1 FOOTER -->
                <div id="step-1-footer" class="w-100">
                    <?php if (!$isAgreed): ?>
                    <div class="form-check mb-4 mt-2">
                        <input class="form-check-input border-secondary" type="checkbox" id="agreeTermsCheck" style="transform: scale(1.2); margin-top: 0.25rem;">
                        <label class="form-check-label user-select-none ms-2 fw-medium" for="agreeTermsCheck">
                            I have read and agree to the Registrar Staff Terms and Conditions.
                        </label>
                    </div>
                    <div class="d-flex justify-content-end gap-3">
                        <button type="button" class="btn btn-outline-secondary px-4 py-2" onclick="window.location.href='<?= BASE_URL ?>/login/logout.php'">Cancel (Log Out)</button>
                        <button type="button" class="btn btn-primary px-5 py-2 fw-bold" id="btnNextStep" disabled>
                            Next <i class="fa-solid fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                    <?php else: ?>
                    <div class="d-flex justify-content-end gap-3">
                        <button type="button" class="btn btn-primary px-5 py-2 fw-bold" id="btnNextStep">
                            Next: Data Privacy Notice <i class="fa-solid fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- STEP 2 FOOTER -->
                <div id="step-2-footer" class="w-100" style="display: none;">
                    <?php if (!$isAgreed): ?>
                    <div class="form-check mb-4 mt-2">
                        <input class="form-check-input border-secondary" type="checkbox" id="agreePrivacyCheck" style="transform: scale(1.2); margin-top: 0.25rem;">
                        <label class="form-check-label user-select-none ms-2 fw-medium" for="agreePrivacyCheck">
                            I have read and understood the Data Privacy Notice.
                        </label>
                    </div>
                    <div class="d-flex justify-content-between gap-3">
                        <button type="button" class="btn btn-outline-secondary px-4 py-2" id="btnPrevStep">
                            <i class="fa-solid fa-arrow-left me-2"></i> Back
                        </button>
                        <button type="button" class="btn btn-primary px-5 py-2 fw-bold" id="btnAgreeTerms" disabled>
                            <i class="fa-solid fa-check me-2"></i> I Agree
                        </button>
                    </div>
                    <?php else: ?>
                    <div class="d-flex justify-content-between gap-3">
                        <button type="button" class="btn btn-outline-secondary px-4 py-2" id="btnPrevStep">
                            <i class="fa-solid fa-arrow-left me-2"></i> Back to Terms
                        </button>
                        <button type="button" class="btn btn-secondary px-5 py-2 fw-bold" data-bs-dismiss="modal">
                            Close
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var termsModalEl = document.getElementById('registrarTermsModal');
    if (!termsModalEl) return;

    var isAgreed = <?= $isAgreed ? 'true' : 'false' ?>;
    
    var modal = new bootstrap.Modal(termsModalEl, {
        backdrop: isAgreed ? true : 'static',
        keyboard: isAgreed
    });
    
    if (!isAgreed) {
        modal.show();
    }

    var checkboxTerms = document.getElementById('agreeTermsCheck');
    var btnNext = document.getElementById('btnNextStep');
    
    var checkboxPrivacy = document.getElementById('agreePrivacyCheck');
    var btnAgree = document.getElementById('btnAgreeTerms');
    var btnPrev = document.getElementById('btnPrevStep');

    var step1Content = document.getElementById('step-1-content');
    var step2Content = document.getElementById('step-2-content');
    var step1Footer = document.getElementById('step-1-footer');
    var step2Footer = document.getElementById('step-2-footer');
    var modalTitle = document.getElementById('registrarTermsModalLabel');
    var modalBody = document.querySelector('#registrarTermsModal .modal-body');

    if (checkboxTerms) {
        checkboxTerms.addEventListener('change', function() {
            if (btnNext) btnNext.disabled = !this.checked;
        });
    }

    if (checkboxPrivacy) {
        checkboxPrivacy.addEventListener('change', function() {
            if (btnAgree) btnAgree.disabled = !this.checked;
        });
    }

    if (btnNext) {
        btnNext.addEventListener('click', function() {
            if (!isAgreed && checkboxTerms && !checkboxTerms.checked) return;
            step1Content.style.display = 'none';
            step1Footer.style.display = 'none';
            step2Content.style.display = 'block';
            step2Footer.style.display = 'block';
            modalTitle.textContent = 'Data Privacy Notice';
            modalBody.scrollTop = 0;
        });
    }

    if (btnPrev) {
        btnPrev.addEventListener('click', function() {
            step2Content.style.display = 'none';
            step2Footer.style.display = 'none';
            step1Content.style.display = 'block';
            step1Footer.style.display = 'block';
            modalTitle.textContent = 'Registrar Staff Terms and Conditions';
            modalBody.scrollTop = 0;
        });
    }

    if (btnAgree) {
        btnAgree.addEventListener('click', function() {
            if (!checkboxPrivacy || !checkboxPrivacy.checked) return;

        btnAgree.disabled = true;
        btnAgree.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';

        fetch('<?= BASE_URL ?>/modules/registrar/api/agree-terms.php', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                modal.hide();
                window.location.reload();
            } else {
                alert('Error: ' + (data.error || 'Failed to save agreement.'));
                btnAgree.disabled = false;
                btnAgree.innerHTML = '<i class="fa-solid fa-check me-2"></i> I Agree';
            }
        })
        .catch(err => {
            console.error('Error saving terms:', err);
            alert('A network error occurred. Please try again.');
            btnAgree.disabled = false;
            btnAgree.innerHTML = '<i class="fa-solid fa-check me-2"></i> I Agree';
        });
    });
}
});
</script>
