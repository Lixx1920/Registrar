<?php
/**
 * SMS 2 - Registrar Agree Terms API
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/authentication.php';
requireAuth();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (getCurrentUserRoleKey() !== 'registrar') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized role']);
    exit;
}

$pdo = db();
if (!$pdo) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

try {
    $stmt = $pdo->prepare('UPDATE users SET registrar_terms_agreed = 1 WHERE id = ?');
    $stmt->execute([getCurrentUserId()]);

    echo json_encode(['success' => true]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred while saving agreement']);
}
