<?php
/**
 * SMS 2 - Registrar Reusable Helpers
 *
 * - Sanitized request access (POST/GET)
 * - Input validators + central rule validator
 * - Consistent JSON / redirect responses and safe error handling
 * - CSRF + role gating (built on the global SMS2 security helpers)
 * - Registrar-scoped audit logging
 *
 * All functions are Registrar-scoped (reg*) and never override global SMS2 functions.
 */
declare(strict_types=1);

if (defined('REG_HELPERS')) {
    return;
}
define('REG_HELPERS', true);

if (!defined('ROOT_PATH')) {
    require_once __DIR__ . '/../config/config.php';
}

/* ── Request access ─────────────────────────────────────────────── */

function regIsPost(): bool
{
    return (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST');
}

/** Trimmed scalar string from POST with a safe default. */
function regPost(string $key, string $default = ''): string
{
    $v = $_POST[$key] ?? $default;
    return is_scalar($v) ? trim((string) $v) : $default;
}

function regPostInt(string $key, ?int $default = null): ?int
{
    $v = $_POST[$key] ?? null;
    return is_numeric($v) ? (int) $v : $default;
}

function regPostBool(string $key, bool $default = false): bool
{
    return isset($_POST[$key]) && (int) $_POST[$key] === 1;
}

/** Trimmed scalar string from GET with a safe default. */
function regGet(string $key, string $default = ''): string
{
    $v = $_GET[$key] ?? $default;
    return is_scalar($v) ? trim((string) $v) : $default;
}

/* ── Validators (return error message string or null) ───────────── */

function regValidateRequired(string $value): ?string
{
    return trim($value) === '' ? 'This field is required.' : null;
}

function regValidateString(string $value, int $max = 500): ?string
{
    $len = mb_strlen($value);
    if ($len > $max) {
        return 'Must be at most ' . $max . ' characters.';
    }
    if (strpos($value, "\0") !== false) {
        return 'Contains invalid characters.';
    }
    return null;
}

function regValidateEmail(string $value): ?string
{
    if (trim($value) === '') {
        return null;
    }
    return filter_var($value, FILTER_VALIDATE_EMAIL) ? null : 'Enter a valid email address.';
}

function regValidatePhone(string $value): ?string
{
    if (trim($value) === '') {
        return null;
    }
    return preg_match('/^[0-9+()\-\s]{7,18}$/', $value) ? null : 'Enter a valid contact number.';
}

function regValidateDate(string $value): ?string
{
    if (trim($value) === '') {
        return null;
    }
    $d = DateTime::createFromFormat('Y-m-d', $value);
    return ($d && $d->format('Y-m-d') === $value) ? null : 'Enter a valid date (YYYY-MM-DD).';
}

function regValidateAlphaNum(string $value, int $max = 80): ?string
{
    if (trim($value) === '') {
        return null;
    }
    if (mb_strlen($value) > $max) {
        return 'Must be at most ' . $max . ' characters.';
    }
    return preg_match('/^[A-Za-z0-9_\-.\/ ]+$/', $value) ? null : 'Contains invalid characters.';
}

function regValidateStudentId(string $value): ?string
{
    if (trim($value) === '') {
        return null;
    }
    return preg_match('/^[A-Z\-]{1,4}\d{6,}$/', $value) ? null : 'Enter a valid student number (e.g. S230000001).';
}

function regValidateOneOf(string $value, array $allowed, string $label = 'Selection'): ?string
{
    return in_array($value, $allowed, true) ? null : $label . ' is invalid.';
}

function regValidateInt(string $value, ?int $min = null, ?int $max = null): ?string
{
    if (trim($value) === '') {
        return null;
    }
    if (!preg_match('/^-?\d+$/', $value)) {
        return 'Must be a whole number.';
    }
    $n = (int) $value;
    if ($min !== null && $n < $min) {
        return 'Must be at least ' . $min . '.';
    }
    if ($max !== null && $n > $max) {
        return 'Must be at most ' . $max . '.';
    }
    return null;
}

/**
 * Validate a rule set and return cleaned values + errors.
 *
 * $rules = [ field => [ 'required', 'string', 'email', callable, ... ] ]
 * A validator name maps to regValidate<Name>() taking the raw value.
 *
 * @return array{errors:array<string,string>, cleaned:array<string,string>}
 */
function regValidate(array $rules, array $data): array
{
    $errors = [];
    $cleaned = [];

    foreach ($rules as $field => $checks) {
        $raw = $data[$field] ?? '';
        $value = is_string($raw) ? trim($raw) : (is_scalar($raw) ? (string) $raw : '');
        $required = in_array('required', $checks, true);

        if ($value === '') {
            if ($required) {
                $errors[$field] = 'This field is required.';
            } else {
                $cleaned[$field] = $value;
            }
            continue;
        }

        foreach ($checks as $check) {
            if ($check === 'required') {
                continue;
            }
            try {
                // Named validators (regValidate<Name>) take precedence over
                // callables — otherwise names that collide with PHP built-ins
                // (e.g. 'date', 'time') would be invoked directly.
                if (is_string($check) && function_exists('regValidate' . ucfirst($check))) {
                    $err = call_user_func('regValidate' . ucfirst($check), $value);
                } elseif (is_callable($check)) {
                    $err = $check($value);
                } else {
                    continue;
                }
            } catch (Throwable $t) {
                // A validator that needs extra arguments (e.g. regValidateOneOf)
                // must be passed as a closure. Surface a clear error, not a fatal.
                $errors[$field] = 'Invalid validation rule for this field.';
                break;
            }
            if (is_string($err) && $err !== '') {
                $errors[$field] = $err;
                break;
            }
        }

        if (!isset($errors[$field])) {
            $cleaned[$field] = $value;
        }
    }

    return ['errors' => $errors, 'cleaned' => $cleaned];
}

/* ── Output / response helpers ──────────────────────────────────── */

function regE(?string $value): string
{
    return function_exists('e') ? e($value) : htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function regJsonResponse(int $status, array $payload): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function regOk(array $extra = []): void
{
    regJsonResponse(200, array_merge(['ok' => true], $extra));
}

function regFail(string $error, int $status = 400): void
{
    regJsonResponse($status, ['ok' => false, 'error' => $error]);
}

function regHttpError(int $status, string $message): void
{
    regJsonResponse($status, ['ok' => false, 'error' => $message]);
}

/**
 * Run a callable and convert any uncaught exception into a safe JSON 500
 * (the raw message is never leaked to the client).
 */
function regCaptureJson(callable $fn): void
{
    try {
        $fn();
    } catch (Throwable $e) {
        error_log('Registrar error: ' . $e->getMessage());
        regJsonResponse(500, ['ok' => false, 'error' => 'Internal server error.']);
    }
}

function regRedirect(string $url): void
{
    if (headers_sent()) {
        echo 'Redirecting to ' . regE($url);
        exit;
    }
    header('Location: ' . $url);
    exit;
}

function regSuccessRedirect(string $url): void
{
    $_SESSION[REG_SESSION_SCOPE . '_flash'] = ['type' => 'success', 'text' => 'Changes saved successfully.'];
    regRedirect($url);
}

/* ── CSRF + authorization gating ────────────────────────────────── */

/** Require a valid CSRF token (403 JSON on failure). */
function regRequireCsrf(): void
{
    require_once ROOT_PATH . '/includes/security.php';
    requireCsrf();
}

/** Require an authenticated, Registrar-authorized session (401/403 JSON on failure). */
function regRequireAction(string $action = 'general'): void
{
    require_once ROOT_PATH . '/includes/authentication.php';

    if (empty($_SESSION['user_id'])) {
        regHttpError(401, 'Authentication required.');
    }
    if (!function_exists('userCanAccessModule') || !userCanAccessModule('registrar')) {
        regHttpError(403, 'Not authorized to access the Registrar module.');
    }
}

/* ── Session shortcuts ──────────────────────────────────────────── */

function regCurrentUserId(): ?int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function regCurrentUserRole(): string
{
    return (string) ($_SESSION['user_role_key'] ?? '');
}

function regCurrentUserName(): string
{
    return (string) ($_SESSION['user_name'] ?? 'User');
}

/* ── Registrar-scoped audit logging ─────────────────────────────── */

function regLog(string $action, string $detail, ?int $userId = null, ?string $userName = null, ?string $roleKey = null): void
{
    if (!function_exists('logActivity')) {
        require_once ROOT_PATH . '/includes/audit.php';
    }
    logActivity($action, $detail, 'registrar', $userId, $userName, $roleKey);
}