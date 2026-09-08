<?php
/**
 * SMS 2 - Cron Script to Cleanup Expired Pre-Enrollee Accounts
 * 
 * This script runs daily to find pre-enrollees who have not submitted their documents
 * within 7 days of verification, and deletes their pre-account and pre-registration data.
 */
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

// Only allow execution from CLI or server itself for security
if (php_sapi_name() !== 'cli') {
    // Basic protection if run via web browser
    if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1') {
        header('HTTP/1.0 403 Forbidden');
        exit('Forbidden');
    }
}

$pdo = db();
if (!$pdo) {
    error_log('Cron Cleanup: Database connection failed.');
    exit("Database connection failed.\n");
}

try {
    // 1. Fetch expired users
    // We target users with role_key = 'pre-enrollee' whose expires_at date has passed.
    $stmt = $pdo->prepare("SELECT id, student_id FROM users WHERE role_key = 'pre-enrollee' AND expires_at < NOW()");
    $stmt->execute();
    $expiredUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $deletedCount = 0;
    
    foreach ($expiredUsers as $user) {
        $pdo->beginTransaction();
        try {
            // Delete the user account
            $delUser = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $delUser->execute([$user['id']]);
            
            // Delete the pre-registration data from reg_students
            if (!empty($user['student_id'])) {
                // Ensure we only delete if they are still 'Verified' (meaning not Activated by Registrar)
                $delReg = $pdo->prepare("DELETE FROM reg_students WHERE student_number = ? AND status = 'Verified'");
                $delReg->execute([$user['student_id']]);
            }
            
            $pdo->commit();
            $deletedCount++;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Cron Cleanup Error for User ID {$user['id']}: " . $e->getMessage());
        }
    }

    $msg = "Cleanup complete. Deleted {$deletedCount} expired pre-enrollee account(s).";
    echo $msg . "\n";
    error_log("Cron Cleanup: " . $msg);

} catch (Exception $e) {
    error_log("Cron Cleanup Fatal Error: " . $e->getMessage());
    exit("Fatal Error: " . $e->getMessage() . "\n");
}
