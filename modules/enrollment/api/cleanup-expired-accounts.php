<?php
/**
 * SMS 2 - Cleanup Expired Pre-Accounts
 * 
 * This script is intended to be run by a cron job (daily) to clear out
 * pre-enrollees who have not submitted their documents within 7 days.
 */

// If running from web, ensure we have some form of authorization.
// In a real production system, this should check for a secret key or be CLI only.
if (php_sapi_name() !== 'cli' && empty($_GET['cron_key'])) {
    http_response_code(403);
    die('Forbidden.');
}

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

$pdo = db();

try {
    $pdo->beginTransaction();

    // 1. Find expired pre-enrollees
    $stmt = $pdo->prepare("
        SELECT id, student_id 
        FROM users 
        WHERE role_key = 'pre-enrollee' 
          AND expires_at IS NOT NULL 
          AND expires_at < NOW()
    ");
    $stmt->execute();
    $expiredUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $deletedCount = 0;
    $updatedCount = 0;

    if (count($expiredUsers) > 0) {
        $userIds = array_column($expiredUsers, 'id');
        $studentIds = array_column($expiredUsers, 'student_id');

        // 2. Delete from users table
        $inQueryUserIds = implode(',', array_fill(0, count($userIds), '?'));
        $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id IN ($inQueryUserIds)");
        $deleteStmt->execute($userIds);
        $deletedCount = $deleteStmt->rowCount();

        // 3. Update reg_students status to 'Cancelled'
        if (count($studentIds) > 0) {
            $inQueryStudentIds = implode(',', array_fill(0, count($studentIds), '?'));
            $updateStmt = $pdo->prepare("
                UPDATE reg_students 
                SET status = 'Cancelled' 
                WHERE student_number IN ($inQueryStudentIds)
            ");
            $updateStmt->execute($studentIds);
            $updatedCount = $updateStmt->rowCount();
        }
    }

    $pdo->commit();
    echo "Cleanup complete. Deleted $deletedCount expired accounts and cancelled $updatedCount registrations.\n";

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Error during cleanup: " . $e->getMessage() . "\n";
}
