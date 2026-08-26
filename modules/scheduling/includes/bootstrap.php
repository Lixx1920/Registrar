<?php
/**
 * SMS 2 - Class Scheduling Bootstrap
 *
 * Applies the shared SMS2 security baseline to Section Assignment Tool
 * entry points: hardened session, authentication, module authorization,
 * then the scheduling helper set. Mirrors modules/registrar/includes/bootstrap.php.
 *
 * Safe to include more than once (guard below).
 */
declare(strict_types=1);

if (defined('SCH_BOOTSTRAP')) {
    return;
}
define('SCH_BOOTSTRAP', true);

if (!defined('ROOT_PATH')) {
    require_once __DIR__ . '/../../../config/config.php';
}
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
    requireModuleAccess('scheduling');
}

require_once __DIR__ . '/helpers.php';