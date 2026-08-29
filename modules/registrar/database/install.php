<?php
/**
 * SMS 2 - Registrar Foundation Schema Installer (CLI only).
 *
 * Creates the reg_* foundation tables inside the shared sms2_db.
 * Idempotent (CREATE TABLE IF NOT EXISTS); safe to run repeatedly.
 *
 * CLI:
 *   C:\xampp\php\php.exe modules/registrar/database/install.php
 *   C:\xampp\php\php.exe modules/registrar/database/install.php --force   (drop and recreate tables)
 *   C:\xampp\php\php.exe modules/registrar/database/install.php --sql     (print DDL only)
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
$forceRecreate = in_array('--force', $argv ?? [], true);

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

// If --force, drop all existing registrar tables first
if ($forceRecreate) {
    echo "🔄 Dropping existing registrar tables (--force)...\n";
    $tableList = ['reg_counters', 'reg_doc_templates', 'reg_academic_subjects', 'reg_verification_codes',
                  'reg_doc_releases', 'reg_student_ids', 'reg_credentials', 'reg_health_profiles', 'reg_health_records',
                  'reg_doc_request_items', 'reg_doc_requests', 'reg_student_statuses',
                  'reg_academic_history', 'reg_guardians', 'reg_persona_files', 'reg_files', 'reg_students'];
    foreach ($tableList as $tbl) {
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

// Ensure new columns exist on reg_academic_subjects (non-destructive migration)
$requiredCols = [
    'year_level' => "ALTER TABLE `reg_academic_subjects` ADD COLUMN `year_level` VARCHAR(20) NOT NULL DEFAULT '1st Year' AFTER `units`",
    'status'     => "ALTER TABLE `reg_academic_subjects` ADD COLUMN `status` VARCHAR(30) NOT NULL DEFAULT 'Passed' AFTER `remarks`",
    'instructor' => "ALTER TABLE `reg_academic_subjects` ADD COLUMN `instructor` VARCHAR(150) NULL DEFAULT NULL AFTER `status`"
];
foreach ($requiredCols as $cname => $alterSql) {
    try {
        $check = $pdo->query("SHOW COLUMNS FROM `reg_academic_subjects` LIKE '$cname'")->fetch();
        if (!$check) {
            $pdo->exec($alterSql);
            echo "MIGRATED added $cname to reg_academic_subjects\n";
        }
    } catch (Throwable $e) {
        // Table might not exist yet or other issue, ignore
    }
}

echo "\nRegistrar foundation schema ready (tables live in sms2_db). {$ok} ok, {$fail} failed.\n";
exit($fail > 0 ? 1 : 0);
