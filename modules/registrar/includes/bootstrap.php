<?php
/**
 * SMS 2 - Registrar Bootstrap
 *
 * Applies the shared SMS2 security baseline to every Registrar entry point:
 * hardened session, authentication, module authorization, then the
 * Registrar helper set (validation, CSRF gating, JSON/redirect responses,
 * registrar-scoped audit logging).
 *
 * Safe to include more than once (guard below). Existing SMS2 architecture
 * (layout-start, breadcrumbs, navbar, sidebar) is left untouched.
 */
declare(strict_types=1);

if (defined('REG_BOOTSTRAP')) {
    return;
}
define('REG_BOOTSTRAP', true);

require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/config/session.php';
require_once ROOT_PATH . '/includes/authentication.php';

// Enforce idle session timeout (shared SMS2 behavior)
smsEnforceSessionTimeout();

// Authentication - unauthenticated users are redirected to the login page
if (function_exists('requireAuth')) {
    requireAuth();
}

// Module authorization - reuses the global role_permissions model
if (function_exists('requireModuleAccess')) {
    requireModuleAccess('registrar');
}

require_once __DIR__ . '/helpers.php';
