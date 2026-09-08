<?php
/**
 * SMS 2 - Verify Student API
 */
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/includes/mail.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

$studentId = $_POST['student_id'] ?? null;

if (!$studentId) {
    echo json_encode(['success' => false, 'error' => 'Student ID is required.']);
    exit;
}

$pdo = db();

try {
    $pdo->beginTransaction();

    // 1. Fetch Student Data
    $stmt = $pdo->prepare("SELECT * FROM reg_students WHERE id = ?");
    $stmt->execute([$studentId]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        throw new Exception('Student not found.');
    }

    if ($student['status'] === 'Verified') {
        throw new Exception('Student is already verified.');
    }

    // 2. Update Student Status
    $updateStmt = $pdo->prepare("UPDATE reg_students SET status = 'Verified' WHERE id = ?");
    $updateStmt->execute([$studentId]);

    // 3. Generate Pre-Account
    $lastName = preg_replace('/[^a-zA-Z]/', '', $student['last_name'] ?? '');
    $prefix = ucfirst(strtolower(substr($lastName, 0, 2)));
    if (empty($prefix)) {
        $prefix = 'St'; // Fallback
    }
    
    $baseUsername = $prefix . '8080#';
    $username = $baseUsername;
    $password = $baseUsername;
    
    // Check for uniqueness
    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $counter = 1;
    while (true) {
        $checkStmt->execute([$username]);
        if (!$checkStmt->fetch()) {
            break;
        }
        $username = $baseUsername . $counter;
        $password = $username; // Keep pass same as user
        $counter++;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $fullName = trim(($student['first_name'] ?? '') . ' ' . ($student['last_name'] ?? ''));
    
    // Check if reg_students has email column
    $colStmt = $pdo->query("SHOW COLUMNS FROM reg_students LIKE 'email_address'");
    $hasEmailCol = $colStmt->fetch();
    $email = 'placeholder_' . $student['student_number'] . '@bcp.edu.ph';
    $actualEmail = null;
    if ($hasEmailCol && !empty($student['email_address'])) {
        $email = $student['email_address'];
        $actualEmail = $student['email_address'];
    }
    
    // Check if email already exists in users table
    $checkEmail = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmail->execute([$email]);
    if ($checkEmail->fetch()) {
        $parts = explode('@', $email);
        if (count($parts) === 2) {
            $email = $parts[0] . '+' . $student['student_number'] . '@' . $parts[1];
        } else {
            $email = $email . '_' . $student['student_number'];
        }
    }
    
    // 4. Insert into Users
    $expiresAt = date('Y-m-d H:i:s', strtotime('+7 days'));
    
    $insertUser = $pdo->prepare("
        INSERT INTO users (username, password_hash, email, full_name, role_key, student_id, expires_at) 
        VALUES (?, ?, ?, ?, 'pre-enrollee', ?, ?)
    ");
    $insertUser->execute([
        $username,
        $passwordHash,
        $email,
        $fullName,
        $student['student_number'],
        $expiresAt
    ]);

    // 5. Send Email
    $emailSubject = 'BCP Admission Verification - Important Next Steps';
    $htmlBody = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; color: #333;'>
            <h2 style='color: #071c48;'>Admission Verified</h2>
            <p>Dear {$fullName},</p>
            <p>Congratulations! Your admission pre-registration has been successfully verified.</p>
            
            <div style='background: #f4f6f9; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                <h3 style='margin-top: 0;'>Your PRE ACCOUNT Credentials</h3>
                <p><strong>Student ID No.:</strong> {$student['student_number']}</p>
                <p><strong>Username:</strong> {$username}</p>
                <p><strong>Password:</strong> {$password}</p>
            </div>
            
            <p style='color: #d9534f; font-weight: bold;'>ACTION REQUIRED: You have exactly 7 days to complete your enrollment.</p>
            <p>You must log into the Student Portal using these credentials and upload your required Document Credentials.</p>
            <p>If you fail to do so within 7 days (by " . date('F j, Y', strtotime($expiresAt)) . "), your registration will be cancelled and this pre-account will be automatically deleted.</p>
            
            <p>Best regards,<br>Bestlink College of the Philippines</p>
        </div>
    ";
    
    try {
        $mailTarget = $actualEmail ?: $email;
        if (function_exists('smsSendMail') && strpos($mailTarget, 'placeholder') === false) {
            $mailResult = smsSendMail($mailTarget, $emailSubject, $htmlBody, strip_tags($htmlBody), [], 'lixx1920@gmail.com', 'teaz ujvs fhsa jtnb');
            if (!$mailResult['ok']) {
                error_log("Enrollment Mail Error: " . $mailResult['error']);
            }
        }
    } catch (Exception $mailEx) {
        // Log mail error, but continue transaction
    }

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Student verified and PRE ACCOUNT generated successfully.']);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
