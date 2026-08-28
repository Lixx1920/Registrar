<?php
/**
 * SMS 2 - Class Scheduling Section-Assignment Seeder (CLI only).
 *
 * Creates a handful of demo sections and assigns some of the existing
 * Registrar students (reg_students) into them, so the Section Assignment
 * Tool and the Registrar Masterlist Generator both have real data to show
 * immediately after install.
 *
 * Safe to re-run: sections are matched by (program_course, year_section,
 * school_year, semester) before inserting, and assignments use INSERT
 * IGNORE against the unique (section_id, student_id) key.
 *
 * CLI:
 *   C:\xampp\php\php.exe modules/scheduling/database/seed.php
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Forbidden. Run from CLI only.\n";
    exit(1);
}

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';

$pdo = db();
if (!$pdo instanceof PDO) {
    fwrite(STDERR, "ERROR: Cannot connect to sms2_db. Is MySQL running?\n");
    exit(1);
}

$schoolYear = '2026-2027';
$semester   = '1st Semester';

function schFindOrCreateSection(PDO $pdo, string $program, string $yearSection, string $schoolYear, string $semester): int
{
    $stmt = $pdo->prepare("SELECT `id` FROM `sch_sections`
        WHERE `program_course` = ? AND `year_section` = ? AND `school_year` = ? AND `semester` = ?");
    $stmt->execute([$program, $yearSection, $schoolYear, $semester]);
    $id = $stmt->fetchColumn();
    if ($id) {
        return (int) $id;
    }

    $stmt = $pdo->prepare("INSERT INTO `sch_sections`
        (`program_course`, `year_section`, `school_year`, `semester`, `section_code`)
        VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$program, $yearSection, $schoolYear, $semester, $yearSection]);
    return (int) $pdo->lastInsertId();
}

// Pull real students already seeded by the Registrar module.
$students = $pdo->query("
    SELECT `id`, `program_course`, `year_section` FROM `reg_students`
    WHERE `status` = 'Active' AND `program_course` IS NOT NULL AND `program_course` != ''
")->fetchAll(PDO::FETCH_ASSOC);

if (empty($students)) {
    echo "No active reg_students with a program found -- run the Registrar seeder first (modules/registrar/database/seed.php).\n";
    exit(0);
}

$assigned = 0;
$sectionsCreated = [];

foreach ($students as $s) {
    $program = trim((string) $s['program_course']);
    $yearSection = trim((string) $s['year_section']) ?: 'I-A';

    $sectionId = schFindOrCreateSection($pdo, $program, $yearSection, $schoolYear, $semester);
    $sectionsCreated[$sectionId] = true;

    // INSERT IGNORE relies on the unique (section_id, student_id) key -- safe to re-run.
    $stmt = $pdo->prepare("INSERT IGNORE INTO `sch_section_assignments`
        (`section_id`, `student_id`, `status`) VALUES (?, ?, 'Enrolled')");
    $stmt->execute([$sectionId, (int) $s['id']]);
    if ($stmt->rowCount() > 0) {
        $assigned++;
    }
}

echo "Sections in use: " . count($sectionsCreated) . "\n";
echo "New assignments created: {$assigned}\n";
echo "Done.\n";