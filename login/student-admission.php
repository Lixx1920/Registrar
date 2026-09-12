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
            
            // Insert Guardian Contact Data
            $gName = trim($_POST['guardian_name'] ?? '');
            $gRelation = trim($_POST['guardian_relationship'] ?? '');
            $gContact = trim($_POST['guardian_contact'] ?? '');
            $gEmail = trim($_POST['guardian_email'] ?? '');
            $gAddress = trim($_POST['guardian_address'] ?? '');
            
            if ($gName && $gRelation && $gContact) {
                $gStmt = $pdo->prepare("
                    INSERT INTO reg_guardians 
                    (student_id, full_name, relationship, contact, email, address, is_primary, is_emergency, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, 1, 1, NOW())
                ");
                $gStmt->execute([
                    $newStudentId,
                    $gName,
                    $gRelation,
                    $gContact,
                    $gEmail ?: null,
                    $gAddress ?: null
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
                    
                    <h3 class="section-title">Guardian / Emergency Contact</h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Guardian's Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="guardian_name" class="form-control" placeholder="e.g. Maria Dela Cruz" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Relationship <span class="text-danger">*</span></label>
                            <select name="guardian_relationship" class="form-select" required>
                                <option value="" disabled selected>Select Relationship...</option>
                                <option value="Mother">Mother</option>
                                <option value="Father">Father</option>
                                <option value="Sibling">Sibling</option>
                                <option value="Legal Guardian">Legal Guardian</option>
                                <option value="Spouse">Spouse</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                            <input type="tel" name="guardian_contact" class="form-control" placeholder="09xxxxxxxxx" required>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label class="form-label">Email Address (Optional)</label>
                            <input type="email" name="guardian_email" class="form-control" placeholder="guardian@example.com">
                        </div>
                        <div class="col-md-12 mt-3">
                            <label class="form-label">Complete Address (Optional)</label>
                            <input type="text" name="guardian_address" class="form-control" placeholder="House No., Street, Barangay, City, Province">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-submit">
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

