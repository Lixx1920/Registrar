<?php
/**
 * SMS 2 - Registrar API: Activate Student
 */
require_once __DIR__ . '/../../../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/includes/mail.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method.");
}

$pdo = db();
$studentId = $_POST['student_id'] ?? null;
$userId = $_SESSION['user_id'] ?? null;

if (!$studentId) {
    die("Student ID required.");
}

try {
    $pdo->beginTransaction();

    // 1. Get student info
    $stmt = $pdo->prepare("SELECT * FROM reg_students WHERE id = ?");
    $stmt->execute([$studentId]);
    $student = $stmt->fetch();

    if (!$student) {
        throw new Exception("Student not found.");
    }

    // 2. Update Student Status
    $updateStudent = $pdo->prepare("UPDATE reg_students SET status = 'Active', updated_by = ? WHERE id = ?");
    $updateStudent->execute([$userId, $studentId]);

    // 3. Update User Role & Expiry (assuming user's student_id matches student_number as done in verify-student.php)
    $updateUser = $pdo->prepare("
        UPDATE users 
        SET role_key = 'student', expires_at = NULL 
        WHERE student_id = ?
    ");
    $updateUser->execute([$student['student_number']]);

    // 4. Log Status Change in the unified tracker table
    $logStatus = $pdo->prepare("
        INSERT INTO reg_status_history (student_id, changed_from, changed_to, reason, notes, changed_by, changed_at)
        VALUES (?, ?, 'Active', 'Activated by Registrar after document validation', 'First time activation from pre-enrollment.', ?, NOW())
    ");
    $logStatus->execute([$studentId, $student['status'], $userId]);

    $pdo->commit();
    
    // 5. Send Notification Email
    $email = $student['email_address'];
    $name = $student['first_name'];
    $studentNumber = $student['student_number'];
    
    $subject = "Welcome! You are Officially Enrolled at BCP";
    $message = "
        <html>
        <body>
            <h2>Congratulations $name!</h2>
            <p>Your submitted credentials have been successfully validated by the Registrar.</p>
            <p>You are now <strong>Officially Enrolled</strong> at Bestlink College of the Philippines.</p>
            <p>Your account is now <strong>Officially Activated</strong> and the 7-day expiration has been removed.</p>
            <br>
            <p><strong>Your Student ID No.:</strong> $studentNumber</p>
            <p>You may now log in to access your full Student Portal, Class Schedule, and LMS.</p>
        </body>
        </html>
    ";
    
    // Send using Registrar specific Dept Email Config password
    $mailStatus = 'activated';
    error_log("Attempting to send activation email to: " . $email);
    if (function_exists('smsSendMail')) {
        $mailResult = smsSendMail($email, $subject, $message, '', [], 'boyrexar02@gmail.com', 'pqhn jsxr xwmk wpkz');
        error_log("Mail result: " . print_r($mailResult, true));
        if (!$mailResult['ok']) {
            error_log("Failed to send activation email: " . $mailResult['error']);
            $mailStatus = 'activated_mail_failed&error=' . urlencode($mailResult['error']);
        }
    } else {
        error_log("smsSendMail function does NOT exist!");
    }

    // Redirect back to validate-credentials list
    header('Location: ../pages/validate-credentials.php?msg=' . $mailStatus);
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Error activating student: " . $e->getMessage());
    die("Failed to activate student: " . $e->getMessage());
}
