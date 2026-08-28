<?php
/**
 * SMS 2 - Class Scheduling Reusable Helpers
 *
 * Minimal request/validation/response helper set, mirroring the pattern in
 * modules/registrar/includes/helpers.php. All functions are scheduling-scoped
 * (sch*) and never override global SMS2 functions.
 */
declare(strict_types=1);

if (defined('SCH_HELPERS')) {
    return;
}
define('SCH_HELPERS', true);

if (!defined('ROOT_PATH')) {
    require_once __DIR__ . '/../../../config/config.php';
}
require_once ROOT_PATH . '/includes/security.php';

function schIsPost(): bool
{
    return (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST');
}

function schPost(string $key, string $default = ''): string
{
    $v = $_POST[$key] ?? $default;
    return is_scalar($v) ? trim((string) $v) : $default;
}

function schPostInt(string $key, ?int $default = null): ?int
{
    $v = $_POST[$key] ?? null;
    return is_numeric($v) ? (int) $v : $default;
}

function schGet(string $key, string $default = ''): string
{
    $v = $_GET[$key] ?? $default;
    return is_scalar($v) ? trim((string) $v) : $default;
}

function schValidateRequired(string $value): ?string
{
    return trim($value) === '' ? 'This field is required.' : null;
}

/** Reads a JSON request body into an assoc array (used by the fetch()-based UI). */
function schJsonBody(): array
{
    $raw = file_get_contents('php://input') ?: '';
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function schJsonSuccess($data = null, ?string $message = null): void
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => true, 'data' => $data, 'message' => $message]);
    exit;
}

function schJsonError(string $error, int $httpCode = 400): void
{
    http_response_code($httpCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => $error]);
    exit;
}

/** CSRF check for POST endpoints, reusing the global SMS2 CSRF token (X-CSRF-Token header). */
function schRequireCsrf(): void
{
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    requireCsrf(is_string($token) ? $token : null);
}