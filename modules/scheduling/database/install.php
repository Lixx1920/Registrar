<?php
/**
 * SMS 2 - Class Scheduling Section-Assignment Schema Installer (CLI only).
 *
 * Creates the sch_* tables inside the shared sms2_db. Depends on reg_students
 * and users already existing (run Registrar's installer first if this is a
 * fresh database).
 *
 * Idempotent (CREATE TABLE IF NOT EXISTS); safe to run repeatedly.
 *
 * CLI:
 *   C:\xampp\php\php.exe modules/scheduling/database/install.php
 *   C:\xampp\php\php.exe modules/scheduling/database/install.php --force   (drop and recreate tables)
 *   C:\xampp\php\php.exe modules/scheduling/database/install.php --sql     (print DDL only)
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Forbidden. Run from CLI only:\n  C:\\xampp\\php\\php.exe modules/scheduling/database/install.php\n";
    exit(1);
}

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/schema.php';

$printSqlOnly  = in_array('--sql', $argv ?? [], true);
$forceRecreate = in_array('--force', $argv ?? [], true);

if ($printSqlOnly) {
    foreach (schedulingSchemaStatements() as $name => $sql) {
        echo "-- {$name}\n{$sql};\n\n";
    }
    exit(0);
}

$pdo = db();
if (!$pdo instanceof PDO) {
    fwrite(STDERR, "ERROR: Cannot connect to sms2_db. Is MySQL running?\n");
    exit(1);
}

// If --force, drop existing section-assignment tables first (child table first, for the FK).
if ($forceRecreate) {
    echo "🔄 Dropping existing section-assignment tables (--force)...\n";
    foreach (['sch_section_assignments', 'sch_sections'] as $tbl) {
        try {
            $pdo->exec("DROP TABLE IF EXISTS `$tbl`");
            echo "  ✓ Dropped $tbl\n";
        } catch (Throwable $e) {
            echo "  ⚠ Could not drop $tbl: " . $e->getMessage() . "\n";
        }
    }
    echo "\n";
}

$ok = 0;
$fail = 0;
foreach (schedulingSchemaStatements() as $name => $sql) {
    try {
        $pdo->exec($sql);
        echo "OK   {$name}\n";
        $ok++;
    } catch (Throwable $e) {
        echo "FAIL {$name}: " . $e->getMessage() . "\n";
        $fail++;
    }
}

echo "\nSection-assignment schema ready (tables live in sms2_db). {$ok} ok, {$fail} failed.\n";
exit($fail > 0 ? 1 : 0);