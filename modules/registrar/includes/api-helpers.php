<?php
/**
 * SMS2 - Registrar API Helpers
 * Shared utilities for API endpoints.
 *
 * NOTE: Names are prefixed regApi* to avoid collisions with the existing
 * registrar helpers (regGet/regPost/regHandleActions in helpers.php +
 * actions.php) and the global security helpers (requireCsrf, csrfToken).
 */
declare(strict_types=1);

if (defined('REG_API_HELPERS')) {
    return;
}
define('REG_API_HELPERS', true);

require_once __DIR__ . '/bootstrap.php';
require_once ROOT_PATH . '/includes/security.php';

/**
 * Read a GET parameter.
 */
function regApiGet(string $key, $default = null)
{
    return $_GET[$key] ?? $default;
}

/**
 * Read a POST parameter.
 */
function regApiPost(string $key, $default = null)
{
    return $_POST[$key] ?? $default;
}

/**
 * Read the decoded JSON request body.
 */
function regApiBody(): array
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw ?: '', true);
    return is_array($data) ? $data : [];
}

/**
 * Require a valid CSRF token. Accepts the token from:
 *   1. X-CSRF-Token header (used by registrar.js / fetch)
 *   2. csrf_token POST field
 *   3. csrf_token JSON body field
 */
function regApiRequireCsrf(): void
{
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    if (!is_string($token) || $token === '') {
        $token = $_POST['csrf_token'] ?? null;
    }
    if (!is_string($token) || $token === '') {
        $body = regApiBody();
        $token = $body['csrf_token'] ?? null;
    }
    requireCsrf(is_string($token) ? $token : null);
}

/**
 * Require an authenticated, Registrar-authorized session (JSON 401/403).
 */
function regApiRequireAccess(): void
{
    require_once ROOT_PATH . '/includes/authentication.php';
    if (empty($_SESSION['user_id'])) {
        regApiJson(['success' => false, 'error' => 'Authentication required.'], 401);
    }
    if (!function_exists('userCanAccessModule') || !userCanAccessModule('registrar')) {
        regApiJson(['success' => false, 'error' => 'Not authorized to access the Registrar module.'], 403);
    }
}

/**
 * Send a JSON response and exit.
 */
function regApiJson(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Dispatch an API request. Handlers: actionSlug => callable.
 * Reads the action from ?action= / POST action / JSON body action.
 */
function regApiHandle(array $handlers): void
{
    $action = trim((string)($_GET['action'] ?? ($_POST['action'] ?? '')));
    if ($action === '') {
        $body = regApiBody();
        $action = trim((string)($body['action'] ?? ''));
    }

    if ($action === '' || !isset($handlers[$action])) {
        regApiJson(['success' => false, 'error' => 'Unknown or missing action.'], 404);
    }

    try {
        $handlers[$action]();
    } catch (Throwable $e) {
        error_log('Registrar API error: ' . $e->getMessage());
        regApiJson(['success' => false, 'error' => 'Internal server error.'], 500);
    }
}

/**
 * Registrar-scoped audit logging via the shared regLog().
 */
function regApiLog(string $action, string $detail): void
{
    $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    $userName = $_SESSION['user_name'] ?? null;
    $roleKey = $_SESSION['user_role_key'] ?? null;
    regLog($action, $detail, $userId, $userName, $roleKey);
}
