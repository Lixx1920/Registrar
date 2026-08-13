<?php
/**
 * SMS 2 - Registrar Module Configuration
 *
 * Reuses the shared sms2_db connection (global SMS2 database singleton).
 * Registrar tables live in sms2_db and are created by
 * modules/registrar/database/install.php (CLI, idempotent).
 */
declare(strict_types=1);

// Load global configuration when not already present (defines ROOT_PATH, BASE_URL, $MODULES).
if (!defined('ROOT_PATH')) {
    require_once dirname(__DIR__, 3) . '/config/config.php';
}

/* ── Registrar constants ─────────────────────────────────────────── */
if (!defined('REG_VERSION'))        define('REG_VERSION', '1.0.0');
if (!defined('REG_MODULE_KEY'))     define('REG_MODULE_KEY', 'registrar');
if (!defined('REG_STORAGE_SUBDIR')) define('REG_STORAGE_SUBDIR', 'registrar');
if (!defined('REG_REQUEST_PREFIX')) define('REG_REQUEST_PREFIX', 'DOC-REQ-');
if (!defined('REG_SESSION_SCOPE'))  define('REG_SESSION_SCOPE', 'registrar');

/**
 * Registrar PDO handle: the shared sms2_db singleton.
 *
 * @return PDO|null (null when the database is unavailable)
 */
if (!function_exists('regDb')) {
    function regDb(): ?PDO
    {
        require_once ROOT_PATH . '/config/database.php';
        return function_exists('db') ? db() : null;
    }
}

/**
 * Registrar PDO handle that throws when the database is unavailable.
 *
 * @throws RuntimeException
 */
if (!function_exists('getRegistrarDatabaseConnection')) {
    function getRegistrarDatabaseConnection(): PDO
    {
        $pdo = regDb();
        if (!$pdo instanceof PDO) {
            throw new RuntimeException(
                'Registrar database unavailable. Run modules/registrar/database/install.php or start MySQL in XAMPP.'
            );
        }
        return $pdo;
    }
}
