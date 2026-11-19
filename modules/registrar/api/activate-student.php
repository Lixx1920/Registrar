<?php
/**
 * SMS 2 - Registrar API: Activate Student / Flag Credential Deficiency
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/includes/security.php';
require_once ROOT_PATH . '/includes/mail.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Invalid request method.');
}

// Verify CSRF token if provided (backwards-compatible when token is present)
if (isset($_POST['csrf_token']) && function_exists('csrfVerify') && !csrfVerify()) {
    header('Location: ../pages/validate-credentials.php?msg=csrf_error');
    exit;
}

$pdo       = db();
$studentId = (int) ($_POST['student_id'] ?? 0);
$userId    = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
$action    = trim((string) ($_POST['action'] ?? 'activate'));

if ($studentId <= 0) {
    header('Location: ../pages/validate-credentials.php?msg=invalid_student');
    exit;
}

// ── Fetch student record ─────────────────────────────────────────────────────
$stmt = $pdo->prepare('SELECT * FROM reg_students WHERE id = ? LIMIT 1');
$stmt->execute([$studentId]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    header('Location: ../pages/validate-credentials.php?msg=not_found');
    exit;
}

$studentName   = trim((string) ($student['first_name'] ?? '') . ' ' . (string) ($student['last_name'] ?? ''));
$studentNumber = (string) ($student['student_number'] ?? '');
$studentEmail  = trim((string) ($student['email_address'] ?? ''));

// ============================================================================
// ACTION 1: RETURN / FLAG CREDENTIAL DEFICIENCY
// ============================================================================
if ($action === 'return_deficiency') {
    $reasons = $_POST['deficiency_reasons'] ?? [];
    if (!is_array($reasons)) {
        $reasons = [];
    }
    $cleanReasons = array_values(array_filter(array_map(static fn($r) => trim((string) $r), $reasons)));
    $remarks      = trim((string) ($_POST['deficiency_remarks'] ?? ''));

    $summaryParts = [];
    if (!empty($cleanReasons)) {
        $summaryParts[] = 'Flagged issues: ' . implode('; ', $cleanReasons);
    }
    if ($remarks !== '') {
        $summaryParts[] = 'Registrar Remarks: ' . $remarks;
    }
    $noteText = !empty($summaryParts)
        ? implode(' | ', $summaryParts)
        : 'Credential submission requires resubmission or additional documents.';

    try {
        $pdo->beginTransaction();

        // Record deficiency audit event in reg_status_history (status remains Verified for resubmission)
        $logStatus = $pdo->prepare("
            INSERT INTO reg_status_history (student_id, changed_from, changed_to, reason, notes, changed_by, changed_at)
            VALUES (?, ?, ?, 'Credential Validation — Deficiency Notice', ?, ?, NOW())
        ");
        $logStatus->execute([
            $studentId,
            (string) ($student['status'] ?? 'Verified'),
            (string) ($student['status'] ?? 'Verified'),
            $noteText,
            $userId,
        ]);

        // Also record in reg_audit_log if available
        try {
            $audit = $pdo->prepare("INSERT INTO reg_audit_log (action, details, user_id) VALUES ('credential_deficiency', ?, ?)");
            $audit->execute(["Student {$studentNumber} ({$studentName}): {$noteText}", $userId ?? 0]);
        } catch (Throwable $ignore) {
            // Optional audit table
        }

        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('Error logging credential deficiency: ' . $e->getMessage());
        header('Location: ../pages/view-student-credentials.php?id=' . $studentId . '&err=' . urlencode('Could not save deficiency notice.'));
        exit;
    }

    // Attempt email notification to student
    $redirectMsg = 'returned';
    if ($studentEmail !== '' && function_exists('smsSendMail')) {
        $listHtml = '';
        if (!empty($cleanReasons)) {
            $listHtml .= '<ul>';
            foreach ($cleanReasons as $r) {
                $listHtml .= '<li>' . htmlspecialchars($r, ENT_QUOTES, 'UTF-8') . '</li>';
            }
            $listHtml .= '</ul>';
        }
        $remarksHtml = $remarks !== ''
            ? '<p><strong>Additional Remarks from the Registrar:</strong><br>' . nl2br(htmlspecialchars($remarks, ENT_QUOTES, 'UTF-8')) . '</p>'
            : '';

        $subject = 'Action Required: Credential Validation Update — BCP Registrar';
        $message = "
            <html>
            <body style='font-family: Arial, sans-serif; color: #1e293b; line-height: 1.6;'>
                <h2 style='color: #1e3a8a;'>Hello " . htmlspecialchars((string) ($student['first_name'] ?? 'Student'), ENT_QUOTES, 'UTF-8') . ",</h2>
                <p>The Office of the Registrar reviewed your submitted enrollment credentials for Student No. <strong>" . htmlspecialchars($studentNumber, ENT_QUOTES, 'UTF-8') . "</strong> and noted the following item(s) requiring attention:</p>
                {$listHtml}
                {$remarksHtml}
                <p>Please upload clear, valid copies of the required documents through your portal or visit the Registrar's Office so we can finalize your official enrollment activation.</p>
                <p style='color: #64748b; font-size: 12px;'>Office of the University Registrar — Bestlink College of the Philippines</p>
            </body>
            </html>
        ";

        $mailResult = smsSendMail($studentEmail, $subject, $message, '', [], 'boyrexar02@gmail.com', 'pqhn jsxr xwmk wpkz');
        if (empty($mailResult['ok'])) {
            error_log('Failed to send deficiency email: ' . ($mailResult['error'] ?? 'unknown'));
            $redirectMsg = 'returned_mail_failed&error=' . urlencode((string) ($mailResult['error'] ?? 'SMTP error'));
        }
    }

    header('Location: ../pages/validate-credentials.php?msg=' . $redirectMsg . '&student=' . urlencode($studentName));
    exit;
}

// ============================================================================
// ACTION 2: OFFICIALLY ENROLL & ACTIVATE STUDENT (Default)
// ============================================================================
$activationNotes = trim((string) ($_POST['activation_notes'] ?? ''));
$notesToStore    = $activationNotes !== ''
    ? $activationNotes
    : 'First time activation from pre-enrollment after Registrar credential validation.';

try {
    $pdo->beginTransaction();

    // 1. Update Student Status
    $updateStudent = $pdo->prepare("UPDATE reg_students SET status = 'Active', updated_by = ? WHERE id = ?");
    $updateStudent->execute([$userId, $studentId]);

    // 2. Update User Role & Expiry
    $updateUser = $pdo->prepare("
        UPDATE users
        SET role_key = 'student', expires_at = NULL
        WHERE student_id = ?
    ");
    $updateUser->execute([$studentNumber]);

    // 3. Log Status Change in the unified tracker table
    $logStatus = $pdo->prepare("
        INSERT INTO reg_status_history (student_id, changed_from, changed_to, reason, notes, changed_by, changed_at)
        VALUES (?, ?, 'Active', 'Activated by Registrar after document validation', ?, ?, NOW())
    ");
    $logStatus->execute([$studentId, (string) ($student['status'] ?? 'Verified'), $notesToStore, $userId]);

    // 4. Optional Registrar audit log
    try {
        $audit = $pdo->prepare("INSERT INTO reg_audit_log (action, details, user_id) VALUES ('activate_student', ?, ?)");
        $audit->execute(["Activated student {$studentNumber} ({$studentName}) after credential validation.", $userId ?? 0]);
    } catch (Throwable $ignore) {
        // Optional audit table
    }

    $pdo->commit();

    // 5. Send Notification Email
    $firstName = htmlspecialchars((string) ($student['first_name'] ?? 'Student'), ENT_QUOTES, 'UTF-8');
    $safeNo    = htmlspecialchars($studentNumber, ENT_QUOTES, 'UTF-8');
    $subject   = 'Welcome! You are Officially Enrolled at BCP';
    $message   = "
        <html>
        <body style='font-family: Arial, sans-serif; color: #1e293b; line-height: 1.6;'>
            <h2 style='color: #1e3a8a;'>Congratulations {$firstName}!</h2>
            <p>Your submitted credentials have been successfully validated by the Office of the Registrar.</p>
            <p>You are now <strong>Officially Enrolled</strong> at Bestlink College of the Philippines.</p>
            <p>Your account is now <strong>Officially Activated</strong> and the 7-day temporary expiration has been removed.</p>
            <br>
            <p><strong>Your Student ID No.:</strong> {$safeNo}</p>
            <p>You may now log in to access your full Student Portal, Class Schedule, and LMS.</p>
        </body>
        </html>
    ";

    $mailStatus = 'activated';
    if ($studentEmail !== '' && function_exists('smsSendMail')) {
        $mailResult = smsSendMail($studentEmail, $subject, $message, '', [], 'boyrexar02@gmail.com', 'pqhn jsxr xwmk wpkz');
        if (empty($mailResult['ok'])) {
            error_log('Failed to send activation email: ' . ($mailResult['error'] ?? 'unknown'));
            $mailStatus = 'activated_mail_failed&error=' . urlencode((string) ($mailResult['error'] ?? 'SMTP error'));
        }
    }

    header('Location: ../pages/validate-credentials.php?msg=' . $mailStatus . '&student=' . urlencode($studentName));
    exit;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Error activating student: ' . $e->getMessage());
    header('Location: ../pages/view-student-credentials.php?id=' . $studentId . '&err=' . urlencode('Failed to activate student: ' . $e->getMessage()));
    exit;
}
