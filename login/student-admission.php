<?php
/**
 * SMS 2 - Student Admission (Local Form)
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Student Admission';
$bodyClass = 'admission-local-page';

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = db();
    if ($pdo) {
        $firstName = trim($_POST['first_name'] ?? '');
        $middleName = trim($_POST['middle_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $gender = trim($_POST['gender'] ?? '');
        $dob = trim($_POST['date_of_birth'] ?? '');
        $program = trim($_POST['program_course'] ?? '');
        $yearLevel = trim($_POST['year_level'] ?? 'First Year');
        $email = trim($_POST['email'] ?? '');
        $contactNumber = trim($_POST['contact_number'] ?? '');
        
        // Generate a random student reference code or number (e.g. 2026xxxx)
        $studentNumber = date('Y') . str_pad((string)random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
        
        try {
            $stmt = $pdo->prepare("
                INSERT INTO reg_students 
                (student_number, first_name, middle_name, last_name, email_address, contact_number, gender, date_of_birth, program_course, year_section, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())
            ");
            $stmt->execute([
                $studentNumber, 
                $firstName, 
                $middleName, 
                $lastName, 
                $email,
                $contactNumber,
                $gender, 
                $dob ?: null, 
                $program, 
                $yearLevel
            ]);
            
            $newStudentId = $pdo->lastInsertId();
            
            // Process Parent & Guardian Contact Data (Father, Mother, Guardian)
            $fatherName       = trim($_POST['father_name'] ?? '');
            $fatherContact    = trim($_POST['father_contact'] ?? '');
            $fatherEmail      = trim($_POST['father_email'] ?? '');
            $fatherAddress    = trim($_POST['father_address'] ?? '');

            $motherName       = trim($_POST['mother_name'] ?? '');
            $motherContact    = trim($_POST['mother_contact'] ?? '');
            $motherEmail      = trim($_POST['mother_email'] ?? '');
            $motherAddress    = trim($_POST['mother_address'] ?? '');

            $guardianName     = trim($_POST['guardian_name'] ?? '');
            $guardianRelation = trim($_POST['guardian_relationship'] ?? 'Guardian');
            $guardianContact  = trim($_POST['guardian_contact'] ?? '');
            $guardianEmail    = trim($_POST['guardian_email'] ?? '');
            $guardianAddress  = trim($_POST['guardian_address'] ?? '');

            // Validate that at least one contact has a name and contact number
            $hasFather   = ($fatherName !== '' && $fatherContact !== '');
            $hasMother   = ($motherName !== '' && $motherContact !== '');
            $hasGuardian = ($guardianName !== '' && $guardianContact !== '');

            if (!$hasFather && !$hasMother && !$hasGuardian) {
                if ($fatherName !== '' || $motherName !== '' || $guardianName !== '') {
                    throw new Exception("Please provide a contact number for the parent or guardian.");
                }
                throw new Exception("Please provide contact details for at least one parent or guardian (Father, Mother, or Guardian).");
            }

            // Guardian / Emergency Contact is designated as the primary/prior emergency contact.
            // If Guardian is not specified, fall back to Mother, then Father.
            $primaryRole = 'guardian';
            if ($guardianName === '') {
                $primaryRole = ($motherName !== '') ? 'mother' : 'father';
            }

            $guardiansToInsert = [];

            if ($fatherName !== '') {
                $guardiansToInsert[] = [
                    'name'         => $fatherName,
                    'relationship' => 'Father',
                    'contact'      => $fatherContact ?: null,
                    'email'        => $fatherEmail ?: null,
                    'address'      => $fatherAddress ?: null,
                    'is_primary'   => ($primaryRole === 'father') ? 1 : 0,
                    'is_emergency' => 1,
                ];
            }

            if ($motherName !== '') {
                $guardiansToInsert[] = [
                    'name'         => $motherName,
                    'relationship' => 'Mother',
                    'contact'      => $motherContact ?: null,
                    'email'        => $motherEmail ?: null,
                    'address'      => $motherAddress ?: null,
                    'is_primary'   => ($primaryRole === 'mother') ? 1 : 0,
                    'is_emergency' => 1,
                ];
            }

            if ($guardianName !== '') {
                $guardiansToInsert[] = [
                    'name'         => $guardianName,
                    'relationship' => $guardianRelation ?: 'Guardian',
                    'contact'      => $guardianContact ?: null,
                    'email'        => $guardianEmail ?: null,
                    'address'      => $guardianAddress ?: null,
                    'is_primary'   => ($primaryRole === 'guardian') ? 1 : 0,
                    'is_emergency' => 1,
                ];
            }

            // Insert into reg_guardians so records appear in the Registrar Guardian Emergency Contact module
            $gStmt = $pdo->prepare("
                INSERT INTO reg_guardians 
                (student_id, full_name, relationship, contact, email, address, is_primary, is_emergency, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            foreach ($guardiansToInsert as $g) {
                $gStmt->execute([
                    $newStudentId,
                    $g['name'],
                    $g['relationship'],
                    $g['contact'],
                    $g['email'],
                    $g['address'],
                    $g['is_primary'],
                    $g['is_emergency'],
                ]);
            }
            
            $successMessage = "Admission successful! Your assigned Student Number is: " . $studentNumber;
        } catch (PDOException $e) {
            $errorMessage = "Database error: " . $e->getMessage();
        } catch (Exception $e) {
            $errorMessage = "System error: " . $e->getMessage();
        }
    } else {
        $errorMessage = "Database connection not available.";
    }
}

require_once ROOT_PATH . '/includes/header.php';
?>

<style>
body.admission-local-page {
    margin: 0 !important;
    padding: 0 !important;
    min-height: 100vh !important;
    background: linear-gradient(135deg, #071c48 0%, #1e40af 100%) !important;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    color: #333;
}

.admission-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 2rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.18);
    background: rgba(8, 28, 58, 0.72);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    position: sticky;
    top: 0;
    z-index: 100;
}

.admission-bar h1 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 800;
    color: #fff;
    letter-spacing: 0.5px;
}

.admission-bar .back-link {
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: color 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}
.admission-bar .back-link:hover {
    color: #fff;
}

.form-container {
    max-width: 900px;
    margin: 3rem auto;
    padding: 0 1rem;
}

.form-card {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    overflow: hidden;
}

.form-card-header {
    background: #fff;
    padding: 2.5rem 2.5rem 1.5rem;
    border-bottom: 2px solid #f0f2f5;
    text-align: center;
}

.form-card-header img {
    height: 70px;
    margin-bottom: 1rem;
}

.form-card-header h2 {
    color: #071c48;
    font-weight: 800;
    margin-bottom: 0.25rem;
    font-size: 1.75rem;
}

.form-card-header p {
    color: #6c757d;
    margin: 0;
    font-weight: 500;
}

.form-card-body {
    padding: 2.5rem;
}

.section-title {
    color: #1e40af;
    font-weight: 700;
    font-size: 1.1rem;
    border-bottom: 2px solid #e2e8f0;
    padding-bottom: 0.5rem;
    margin-bottom: 1.5rem;
    margin-top: 2rem;
}
.section-title:first-child {
    margin-top: 0;
}

.form-label {
    font-weight: 600;
    color: #4a5568;
    font-size: 0.9rem;
    margin-bottom: 0.4rem;
}

.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    padding: 0.7rem 1rem;
    font-size: 0.95rem;
    transition: all 0.2s;
}

.form-control:focus, .form-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

.btn-submit {
    background: #071c48;
    color: #fff;
    font-weight: 700;
    font-size: 1.1rem;
    padding: 1rem 2rem;
    border-radius: 12px;
    border: none;
    width: 100%;
    transition: all 0.2s;
    margin-top: 2rem;
}

.btn-submit:hover {
    background: #1e40af;
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(30, 64, 175, 0.2);
}

/* Alert styling */
.alert-custom {
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 2rem;
    border: none;
    font-weight: 600;
}
.alert-success-custom {
    background: #d1fae5;
    color: #065f46;
}
.alert-error-custom {
    background: #fee2e2;
    color: #991b1b;
}

@media (max-width: 768px) {
    .form-card-body {
        padding: 1.5rem;
    }
}

/* 3-Part Guardian Sections */
.guardian-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    transition: all 0.2s ease;
}
.guardian-box:hover {
    border-color: #cbd5e1;
    background: #ffffff;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.04);
}
.guardian-box-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.25rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #edf2f7;
}
.guardian-box-header h4 {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 0.6rem;
}
.guardian-badge-part {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 0.3rem 0.75rem;
    border-radius: 20px;
    background: #e2e8f0;
    color: #475569;
}
</style>

<div class="admission-shell">
    <header class="admission-bar">
        <h1>Student Admission Portal</h1>
        <a href="<?= BASE_URL ?>/login/login.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Sign In
        </a>
    </header>

    <div class="form-container">
        <div class="form-card">
            <div class="form-card-header">
                <h2>Online Registration Form</h2>
                <p>Bestlink College of the Philippines</p>
            </div>
            
            <div class="form-card-body">
                
                <?php if ($successMessage): ?>
                    <div class="alert alert-custom alert-success-custom d-flex align-items-center shadow-sm">
                        <i class="fas fa-check-circle fs-3 me-3"></i>
                        <div><?= htmlspecialchars($successMessage) ?></div>
                    </div>
                <?php endif; ?>
                
                <?php if ($errorMessage): ?>
                    <div class="alert alert-custom alert-error-custom d-flex align-items-center shadow-sm">
                        <i class="fas fa-exclamation-circle fs-3 me-3"></i>
                        <div><?= htmlspecialchars($errorMessage) ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!$successMessage): ?>
                <form method="POST" action="">
                    
                    <h3 class="section-title">Personal Information</h3>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" placeholder="e.g. Juan" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Middle Name</label>
                            <input type="text" name="middle_name" class="form-control" placeholder="e.g. Dela">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" placeholder="e.g. Cruz" required>
                        </div>
                        
                        <div class="col-md-12 mt-3">
                            <label class="form-label">Complete Address</label>
                            <input type="text" name="address" class="form-control" placeholder="House No., Street, Barangay, City, Province">
                        </div>

                        <div class="col-md-4 mt-3">
                            <label class="form-label">Sex <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select" required>
                                <option value="" disabled selected>Select Sex</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="date_of_birth" class="form-control" required>
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label">Age</label>
                            <input type="number" name="age" class="form-control" placeholder="e.g. 18">
                        </div>

                        <div class="col-md-6 mt-3">
                            <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                            <input type="tel" name="contact_number" class="form-control" placeholder="09xxxxxxxxx" required>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="example@gmail.com" required>
                        </div>
                    </div>

                    <h3 class="section-title">Enrollment Information</h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Course / Program <span class="text-danger">*</span></label>
                            <select name="program_course" class="form-select" required>
                                <option value="" disabled selected>Select Course...</option>
                                <option value="BS Information Technology">BS Information Technology</option>
                                <option value="BS Computer Science">BS Computer Science</option>
                                <option value="BS Business Administration">BS Business Administration</option>
                                <option value="BS Criminology">BS Criminology</option>
                                <option value="BS Hospitality Management">BS Hospitality Management</option>
                                <option value="BS Education">BS Education</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Year Level <span class="text-danger">*</span></label>
                            <select name="year_level" class="form-select" required>
                                <option value="First Year" selected>First Year</option>
                                <option value="Second Year">Second Year</option>
                                <option value="Third Year">Third Year</option>
                                <option value="Fourth Year">Fourth Year</option>
                            </select>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label class="form-label">Type of Enrollee <span class="text-danger">*</span></label>
                            <select name="enrollee_type" class="form-select" required>
                                <option value="New Regular">New Regular (Freshman)</option>
                                <option value="Transferee">Transferee</option>
                                <option value="Returnee">Returnee</option>
                            </select>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label class="form-label">Last School Attended</label>
                            <input type="text" name="last_school" class="form-control" placeholder="Name of School">
                        </div>
                    </div>
                    
                    <h3 class="section-title d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-users me-2"></i>Guardian &amp; Emergency Contact</span>
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 fs-6 fw-semibold">3 Parts</span>
                    </h3>
                    <p class="text-muted small mb-4" style="margin-top: -0.75rem;">
                        Please provide contact information for your <strong>Father</strong>, <strong>Mother</strong>, and/or <strong>Guardian</strong>. This data will be automatically recorded in the official Registrar Guardian &amp; Emergency Contact Directory.
                    </p>

                    <!-- Part 1: Father's Information -->
                    <div class="guardian-box">
                        <div class="guardian-box-header">
                            <h4>
                                <i class="fas fa-male text-primary fs-4"></i>
                                <span>Father's Information</span>
                            </h4>
                            <span class="guardian-badge-part"><i class="fas fa-user me-1"></i> Part 1 &bull; Father</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Father's Full Name</label>
                                <input type="text" name="father_name" id="father_name" class="form-control" placeholder="e.g. Juan Dela Cruz Sr.">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact / Mobile Number</label>
                                <input type="tel" name="father_contact" id="father_contact" class="form-control" placeholder="09xxxxxxxxx">
                            </div>
                            <div class="col-md-6 mt-3">
                                <label class="form-label">Email Address <span class="text-muted fw-normal">(Optional)</span></label>
                                <input type="email" name="father_email" id="father_email" class="form-control" placeholder="father@example.com">
                            </div>
                            <div class="col-md-6 mt-3">
                                <label class="form-label">Complete Address / Occupation <span class="text-muted fw-normal">(Optional)</span></label>
                                <input type="text" name="father_address" id="father_address" class="form-control" placeholder="House No., Street, Barangay, City, Province">
                            </div>
                        </div>
                    </div>

                    <!-- Part 2: Mother's Information -->
                    <div class="guardian-box">
                        <div class="guardian-box-header">
                            <h4>
                                <i class="fas fa-female text-danger fs-4" style="color: #ec4899 !important;"></i>
                                <span>Mother's Information</span>
                            </h4>
                            <span class="guardian-badge-part"><i class="fas fa-user me-1"></i> Part 2 &bull; Mother</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Mother's Maiden / Full Name</label>
                                <input type="text" name="mother_name" id="mother_name" class="form-control" placeholder="e.g. Maria Santos Cruz">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact / Mobile Number</label>
                                <input type="tel" name="mother_contact" id="mother_contact" class="form-control" placeholder="09xxxxxxxxx">
                            </div>
                            <div class="col-md-6 mt-3">
                                <label class="form-label">Email Address <span class="text-muted fw-normal">(Optional)</span></label>
                                <input type="email" name="mother_email" id="mother_email" class="form-control" placeholder="mother@example.com">
                            </div>
                            <div class="col-md-6 mt-3">
                                <label class="form-label">Complete Address / Occupation <span class="text-muted fw-normal">(Optional)</span></label>
                                <input type="text" name="mother_address" id="mother_address" class="form-control" placeholder="House No., Street, Barangay, City, Province">
                            </div>
                        </div>
                    </div>

                    <!-- Part 3: Guardian / Authorized Contact -->
                    <div class="guardian-box">
                        <div class="guardian-box-header">
                            <h4>
                                <i class="fas fa-user-shield text-info fs-4" style="color: #0284c7 !important;"></i>
                                <span>Guardian / Emergency Contact</span>
                            </h4>
                            <span class="guardian-badge-part" style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;"><i class="fas fa-shield-alt me-1"></i> Part 3 &bull; Primary Emergency Contact</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Guardian's Full Name</label>
                                <input type="text" name="guardian_name" id="guardian_name" class="form-control" placeholder="e.g. Pedro Santos Dela Cruz">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Relationship to Student</label>
                                <select name="guardian_relationship" id="guardian_relationship" class="form-select">
                                    <option value="Guardian" selected>Legal Guardian</option>
                                    <option value="Grandparent">Grandparent</option>
                                    <option value="Aunt / Uncle">Aunt / Uncle</option>
                                    <option value="Sibling">Sibling (Brother / Sister)</option>
                                    <option value="Spouse">Spouse</option>
                                    <option value="Other">Other Relative / Guardian</option>
                                </select>
                            </div>
                            <div class="col-md-6 mt-3">
                                <label class="form-label">Contact / Mobile Number</label>
                                <input type="tel" name="guardian_contact" id="guardian_contact" class="form-control" placeholder="09xxxxxxxxx">
                            </div>
                            <div class="col-md-6 mt-3">
                                <label class="form-label">Email Address <span class="text-muted fw-normal">(Optional)</span></label>
                                <input type="email" name="guardian_email" id="guardian_email" class="form-control" placeholder="guardian@example.com">
                            </div>
                            <div class="col-md-12 mt-3">
                                <label class="form-label">Complete Address <span class="text-muted fw-normal">(Optional)</span></label>
                                <input type="text" name="guardian_address" id="guardian_address" class="form-control" placeholder="House No., Street, Barangay, City, Province">
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-submit" id="btnSubmitAdmission">
                        <i class="fas fa-paper-plane me-2"></i> Submit Application
                    </button>
                    
                </form>
                <?php else: ?>
                    <div class="text-center mt-4">
                        <a href="<?= BASE_URL ?>/modules/enrollment/pages/online-pre-registration.php" class="btn btn-outline-primary fw-bold px-4 rounded-pill">
                            View Pre-registration List <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                        <div class="mt-3">
                            <a href="<?= BASE_URL ?>/login/student-admission.php" class="text-decoration-none text-muted small">Submit another application</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/includes/scripts.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form submission validation
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const fName = (document.getElementById('father_name')?.value || '').trim();
            const fContact = (document.getElementById('father_contact')?.value || '').trim();

            const mName = (document.getElementById('mother_name')?.value || '').trim();
            const mContact = (document.getElementById('mother_contact')?.value || '').trim();

            const gName = (document.getElementById('guardian_name')?.value || '').trim();
            const gContact = (document.getElementById('guardian_contact')?.value || '').trim();

            // At least one section must be filled with both name and contact
            const hasFather = (fName !== '' && fContact !== '');
            const hasMother = (mName !== '' && mContact !== '');
            const hasGuardian = (gName !== '' && gContact !== '');

            if (!hasFather && !hasMother && !hasGuardian) {
                e.preventDefault();
                if (fName !== '' && fContact === '') {
                    alert("Please enter Father's contact number.");
                    document.getElementById('father_contact')?.focus();
                    return false;
                }
                if (mName !== '' && mContact === '') {
                    alert("Please enter Mother's contact number.");
                    document.getElementById('mother_contact')?.focus();
                    return false;
                }
                if (gName !== '' && gContact === '') {
                    alert("Please enter Guardian's contact number.");
                    document.getElementById('guardian_contact')?.focus();
                    return false;
                }
                alert('Please provide contact details for at least one parent or guardian (Father, Mother, or Guardian).');
                document.getElementById('mother_name')?.focus();
                return false;
            }

            // If a specific section has name filled, enforce its contact
            if (fName !== '' && fContact === '') {
                e.preventDefault();
                alert("Please provide Father's contact number.");
                document.getElementById('father_contact')?.focus();
                return false;
            }
            if (mName !== '' && mContact === '') {
                e.preventDefault();
                alert("Please provide Mother's contact number.");
                document.getElementById('mother_contact')?.focus();
                return false;
            }
            if (gName !== '' && gContact === '') {
                e.preventDefault();
                alert("Please provide Guardian's contact number.");
                document.getElementById('guardian_contact')?.focus();
                return false;
            }
        });
    }
});
</script>

