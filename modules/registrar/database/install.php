<?php
/**
 * SMS 2 - Registrar Foundation Schema Installer (CLI only).
 *
 * Creates the reg_* foundation tables inside the shared sms2_db.
 * Idempotent (CREATE TABLE IF NOT EXISTS); safe to run repeatedly.
 *
 * CLI:
 *   C:\xampp\php\php.exe modules/registrar/database/install.php
 *   C:\xampp\php\php.exe modules/registrar/database/install.php --sql   (print DDL only)
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Forbidden. Run from CLI only:\n  C:\\xampp\\php\\php.exe modules/registrar/database/install.php\n";
    exit(1);
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/schema.php';

$printSqlOnly = in_array('--sql', $argv ?? [], true);

if ($printSqlOnly) {
    foreach (registrarSchemaStatements() as $name => $sql) {
        echo "-- {$name}\n{$sql};\n\n";
    }
    exit(0);
}

$pdo = regDb();
if (!$pdo instanceof PDO) {
    fwrite(STDERR, "ERROR: Cannot connect to sms2_db. Is MySQL running?\n");
    exit(1);
}

$ok = 0;
$fail = 0;
foreach (registrarSchemaStatements() as $name => $sql) {
    try {
        $pdo->exec($sql);
        echo "OK   {$name}\n";
        $ok++;
    } catch (Throwable $e) {
        echo "FAIL {$name}: " . $e->getMessage() . "\n";
        $fail++;
    }
}

echo "\nRegistrar foundation schema ready (tables live in sms2_db). {$ok} ok, {$fail} failed.\n";
exit($fail > 0 ? 1 : 0);
