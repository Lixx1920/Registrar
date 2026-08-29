<?php
/**
 * SMS 2 - Student ID Cards Demo Data Seeder
 * Registrar Module — id-cards.php companion seeder
 *
 * Populates realistic College + SHS student ID card test records:
 *   - College: 10 "Ready", 5 "Printed", 5 "Not Yet Created"
 *   - SHS: 8 "Ready" (Grade 11 & 12, mixed strands), 4 "Not Yet Created"
 *
 * Idempotency: checks for the sentinel student number 'IDC-SEED-001'.
 * If already present, exits without inserting.
 *
 * CLI usage:
 *   C:\xampp\php\php.exe modules/registrar/database/seed-id-cards.php
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo "Forbidden. Run from CLI only.\n";
    exit(1);
}

require_once dirname(__DIR__, 3) . '/config/config.php';
require_once dirname(__DIR__, 3) . '/config/database.php';

$pdo = db();
if (!$pdo instanceof PDO) {
    fwrite(STDERR, "ERROR: Cannot connect to database.\n");
    exit(1);
}

// ── Idempotency guard ────────────────────────────────────────────────────────
$check = $pdo->prepare("SELECT 1 FROM `reg_students` WHERE `student_number` = 'IDC-SEED-001'");
$check->execute();
if ($check->fetchColumn()) {
    echo "ID card seeder has already run (IDC-SEED-001 exists). Nothing to do.\n";
    exit(0);
}

echo "🎴 Seeding Student ID Card demo data...\n\n";

// ── Name pools ───────────────────────────────────────────────────────────────
$maleFirst   = ['Juan','Jose','Carlos','Miguel','Pedro','Ramon','Ricardo','Fernando','Edgar','Leo',
                 'Kevin','Patrick','Samuel','Victor','Angelo','Marco','Daniel','Joshua','Mark','Ivan'];
$femaleFirst = ['Maria','Rosa','Angela','Sofia','Carmen','Beatriz','Nicole','Faith','Grace','Janine',
                'Patricia','Samantha','Diana','Elaine','Karen','Rachel','Jasmine','Michelle','Trisha','Bianca'];
$lastNames   = ['Santos','Cruz','Reyes','Lopez','Mendoza','Pascual','Torres','Garcia','Alvarez','Bautista',
                'Ramos','Castro','Flores','Rivera','Salazar','Gonzales','Hernandez','Diaz','Morales','Navarro',
                'Domingo','Aguilar','Marquez','Ocampo','Rodriguez','Soriano','Tolentino','Valdez','Tan','Lim'];
$middleInit  = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T'];

function pickName(array $maleFirst, array $femaleFirst, array $lastNames, array $middleInit, &$seed): array
{
    $gender  = ($seed % 2 === 0) ? 'Male' : 'Female';
    $first   = $gender === 'Male' ? $maleFirst[$seed % count($maleFirst)] : $femaleFirst[$seed % count($femaleFirst)];
    $last    = $lastNames[$seed % count($lastNames)];
    $mid     = $middleInit[$seed % count($middleInit)];
    $seed++;
    return compact('first', 'last', 'mid', 'gender');
}

// ── College programs ─────────────────────────────────────────────────────────
$collegePrograms = [
    'BS Information Technology',
    'BS Business Administration',
    'BS Hospitality Management',
    'BS Accounting Information System',
    'BS Computer Engineering',
    'BS Criminology',
    'BS Psychology',
    'BS Tourism Management',
    'BS Elementary Education',
    'BS Secondary Education',
];
$yearLevels = ['I-A', 'II-B', 'III-A', 'IV-C', 'II-A', 'III-B', 'I-B', 'IV-A', 'II-C', 'III-C'];
$departments = [
    'College of Engineering',
    'College of Business',
    'College of Hospitality',
    'College of Arts and Sciences',
    'College of Education',
    'College of Criminal Justice',
];

// ── SHS strands & year levels ─────────────────────────────────────────────────
$shsData = [
    ['strand' => 'STEM',    'grade' => 'G11-STEM-1',  'dept' => 'Senior High School'],
    ['strand' => 'ABM',     'grade' => 'G12-ABM-1',   'dept' => 'Senior High School'],
    ['strand' => 'HUMSS',   'grade' => 'G11-HUMSS-1', 'dept' => 'Senior High School'],
    ['strand' => 'GAS',     'grade' => 'G12-GAS-1',   'dept' => 'Senior High School'],
    ['strand' => 'TVL-ICT', 'grade' => 'G11-TVL-1',   'dept' => 'Senior High School'],
    ['strand' => 'STEM',    'grade' => 'G12-STEM-2',  'dept' => 'Senior High School'],
    ['strand' => 'ABM',     'grade' => 'G11-ABM-2',   'dept' => 'Senior High School'],
    ['strand' => 'HUMSS',   'grade' => 'G12-HUMSS-2', 'dept' => 'Senior High School'],
];

// Emergency contacts pool
$emergencyNames    = ['Maria Santos','Jose Cruz','Elena Reyes','Ricardo Lopez','Corazon Mendoza',
                      'Arturo Pascual','Lorna Torres','Eduardo Garcia','Remedios Alvarez','Rafael Bautista',
                      'Carmen Ramos','Armando Castro','Gloria Flores','Rodrigo Rivera','Pilar Salazar',
                      'Felicitas Gonzales','Hernando Diaz','Natividad Morales','Roberto Navarro','Consuelo Domingo'];
$relationships     = ['Mother','Father','Mother','Father','Mother','Father','Mother','Father','Mother','Father',
                      'Mother','Father','Mother','Father','Mother','Father','Mother','Father','Mother','Father'];
$phones            = ['09171234567','09281234567','09391234567','09501234567','09121234567',
                      '09231234567','09341234567','09451234567','09561234567','09671234567',
                      '09781234567','09891234567','09901234567','09011234567','09121234568',
                      '09231234568','09341234568','09451234568','09561234568','09671234568'];
$addresses         = [
    '123 Quirino Highway, Novaliches, Quezon City',
    '456 Commonwealth Ave, Brgy. Batasan Hills, QC',
    '789 Mindanao Ave, Brgy. Bagong Pag-Asa, QC',
    '12 Kalayaan St., Brgy. Sta. Monica, Novaliches, QC',
    '34 Sanciangko St., Cebu City',
    '56 Gonzales St., Brgy. Kaunlaran, Manila',
    '78 Aguinaldo Blvd., Brgy. San Agustin, QC',
    '90 Batangas Rd., Brgy. Makati, Metro Manila',
    '22 Maharlika Highway, Brgy. Poblacion, Bulacan',
    '44 Rizal Ave., Brgy. San Isidro, Caloocan City',
    '66 P. Tuazon Blvd., Cubao, Quezon City',
    '88 Espana Blvd., Sampaloc, Manila',
    '100 E. Rodriguez Sr. Ave., Quezon City',
    '200 Timog Ave., Brgy. South Triangle, QC',
    '300 Aurora Blvd., Brgy. Doña Imelda, QC',
    '400 Katipunan Ave., Brgy. Loyola Heights, QC',
    '500 C.P. Garcia Ave., UP Campus, Diliman, QC',
    '600 Magsaysay Blvd., Sta. Mesa, Manila',
    '700 Bonny Serrano Ave., Brgy. Kamuning, QC',
    '800 EDSA, Brgy. Cubao, Quezon City',
];

$nameIdx = 0;
$insertedStudents = [];

/**
 * Insert a student and return the new ID.
 */
function insertStudent(PDO $pdo, string $sn, string $first, string $mid, string $last,
                        string $gender, string $program, string $yearSec, string $dept,
                        string $dob, string $enrollDate): int
{
    $stmt = $pdo->prepare(
        "INSERT INTO `reg_students`
         (`student_number`,`first_name`,`middle_name`,`last_name`,
          `gender`,`program_course`,`year_section`,`college_department`,
          `date_of_birth`,`enrollment_date`,`nationality`,`status`)
         VALUES (?,?,?,?,?,?,?,?,?,?,'Filipino','Active')"
    );
    $stmt->execute([$sn, $first, $mid, $last, $gender, $program, $yearSec, $dept, $dob, $enrollDate]);
    return (int) $pdo->lastInsertId();
}

/**
 * Insert a guardian / emergency contact for a student.
 */
function insertGuardian(PDO $pdo, int $sid, string $name, string $rel, string $phone, string $addr): void
{
    $stmt = $pdo->prepare(
        "INSERT INTO `reg_guardians`
         (`student_id`,`full_name`,`relationship`,`contact`,`address`,`is_primary`,`is_emergency`)
         VALUES (?,?,?,?,?,0,1)"
    );
    $stmt->execute([$sid, $name, $rel, $phone, $addr]);
}

/**
 * Insert an ID card record.
 */
function insertIDCard(PDO $pdo, int $sid, string $idNum, string $status, string $batchNo, ?string $printedAt = null): int
{
    $notes = 'Seeded via seed-id-cards.php';
    $stmt = $pdo->prepare(
        "INSERT INTO `reg_student_ids`
         (`student_id`,`batch_no`,`template_name`,`id_number`,`status`,`notes`,`printed_at`)
         VALUES (?,?,'standard',?,?,?,?)"
    );
    $stmt->execute([$sid, $batchNo, $idNum, $status, $notes, $printedAt]);
    return (int) $pdo->lastInsertId();
}

$batchCurrent  = 'BATCH-2025-2026';
$batchPrevious = 'BATCH-2024-2025';

// ── COLLEGE STUDENTS: 10 Ready ───────────────────────────────────────────────
echo "  Seeding College — Ready ID cards...\n";
for ($i = 0; $i < 10; $i++) {
    $n = pickName($maleFirst, $femaleFirst, $lastNames, $middleInit, $nameIdx);
    $sn = 'IDC-SEED-' . str_pad((string)($i + 1), 3, '0', STR_PAD_LEFT);
    $dob = date('Y-m-d', mktime(0, 0, 0, rand(1, 12), rand(1, 28), rand(2000, 2004)));
    $prog = $collegePrograms[$i % count($collegePrograms)];
    $yr   = $yearLevels[$i % count($yearLevels)];
    $dept = $departments[$i % count($departments)];
    $enroll = date('Y-m-d', mktime(0, 0, 0, 6, 1, 2022 + ($i % 3)));

    $sid = insertStudent($pdo, $sn, $n['first'], $n['mid'], $n['last'], $n['gender'], $prog, $yr, $dept, $dob, $enroll);
    insertGuardian($pdo, $sid, $emergencyNames[$i], $relationships[$i], $phones[$i], $addresses[$i]);
    $idNum = 'IDC' . date('Y') . str_pad((string)($i + 1), 5, '0', STR_PAD_LEFT);
    insertIDCard($pdo, $sid, $idNum, 'Ready', $batchCurrent);
    $insertedStudents[] = $sid;
    echo "    ✓ Ready: {$n['first']} {$n['last']} ({$sn}) — {$prog}\n";
}

// ── COLLEGE STUDENTS: 5 Printed ──────────────────────────────────────────────
echo "\n  Seeding College — Printed ID cards...\n";
for ($i = 0; $i < 5; $i++) {
    $n = pickName($maleFirst, $femaleFirst, $lastNames, $middleInit, $nameIdx);
    $sn = 'IDC-SEED-P' . str_pad((string)($i + 1), 3, '0', STR_PAD_LEFT);
    $dob = date('Y-m-d', mktime(0, 0, 0, rand(1, 12), rand(1, 28), rand(1999, 2003)));
    $prog = $collegePrograms[($i + 3) % count($collegePrograms)];
    $yr   = $yearLevels[($i + 5) % count($yearLevels)];
    $dept = $departments[($i + 2) % count($departments)];
    $enroll = date('Y-m-d', mktime(0, 0, 0, 6, 1, 2021 + ($i % 3)));

    $sid = insertStudent($pdo, $sn, $n['first'], $n['mid'], $n['last'], $n['gender'], $prog, $yr, $dept, $dob, $enroll);
    insertGuardian($pdo, $sid, $emergencyNames[($i + 10) % count($emergencyNames)], $relationships[($i + 10) % count($relationships)], $phones[($i + 10) % count($phones)], $addresses[($i + 10) % count($addresses)]);
    $idNum = 'IDC' . (date('Y') - 1) . str_pad((string)($i + 200), 5, '0', STR_PAD_LEFT);
    insertIDCard($pdo, $sid, $idNum, 'Printed', $batchPrevious, date('Y-m-d H:i:s', strtotime('-' . ($i + 1) . ' months')));
    $insertedStudents[] = $sid;
    echo "    ✓ Printed: {$n['first']} {$n['last']} ({$sn}) — {$prog}\n";
}

// ── COLLEGE STUDENTS: 5 Not Yet Created ──────────────────────────────────────
echo "\n  Seeding College — Not Yet Created (no ID card)...\n";
for ($i = 0; $i < 5; $i++) {
    $n = pickName($maleFirst, $femaleFirst, $lastNames, $middleInit, $nameIdx);
    $sn = 'IDC-SEED-N' . str_pad((string)($i + 1), 3, '0', STR_PAD_LEFT);
    $dob = date('Y-m-d', mktime(0, 0, 0, rand(1, 12), rand(1, 28), rand(2001, 2005)));
    $prog = $collegePrograms[($i + 6) % count($collegePrograms)];
    $yr   = $yearLevels[($i + 2) % count($yearLevels)];
    $dept = $departments[($i + 3) % count($departments)];
    $enroll = date('Y-m-d', mktime(0, 0, 0, 6, 1, 2024));

    // No guardian / emergency contact to simulate incomplete data from Enrollment module
    $sid = insertStudent($pdo, $sn, $n['first'], $n['mid'], $n['last'], $n['gender'], $prog, $yr, $dept, $dob, $enroll);
    // No ID card inserted — represents "Not Yet Created"
    $insertedStudents[] = $sid;
    echo "    ✓ Not Created: {$n['first']} {$n['last']} ({$sn}) — {$prog}\n";
}

// ── SHS STUDENTS: 8 Ready ────────────────────────────────────────────────────
echo "\n  Seeding SHS — Ready ID cards...\n";
for ($i = 0; $i < 8; $i++) {
    $n = pickName($maleFirst, $femaleFirst, $lastNames, $middleInit, $nameIdx);
    $sn = 'IDC-SHS-' . str_pad((string)($i + 1), 3, '0', STR_PAD_LEFT);
    $dob = date('Y-m-d', mktime(0, 0, 0, rand(1, 12), rand(1, 28), rand(2006, 2009)));
    $shs = $shsData[$i % count($shsData)];
    $enroll = date('Y-m-d', mktime(0, 0, 0, 6, 1, 2024));

    $sid = insertStudent($pdo, $sn, $n['first'], $n['mid'], $n['last'], $n['gender'],
                          $shs['strand'], $shs['grade'], $shs['dept'], $dob, $enroll);
    insertGuardian($pdo, $sid, $emergencyNames[($i + 5) % count($emergencyNames)], $relationships[($i + 5) % count($relationships)], $phones[($i + 5) % count($phones)], $addresses[($i + 5) % count($addresses)]);
    $idNum = 'SHS' . date('Y') . str_pad((string)($i + 1), 5, '0', STR_PAD_LEFT);
    insertIDCard($pdo, $sid, $idNum, 'Ready', $batchCurrent);
    $insertedStudents[] = $sid;
    echo "    ✓ Ready SHS: {$n['first']} {$n['last']} ({$sn}) — {$shs['strand']} | {$shs['grade']}\n";
}

// ── SHS STUDENTS: 4 Not Yet Created ──────────────────────────────────────────
echo "\n  Seeding SHS — Not Yet Created...\n";
for ($i = 0; $i < 4; $i++) {
    $n = pickName($maleFirst, $femaleFirst, $lastNames, $middleInit, $nameIdx);
    $sn = 'IDC-SHS-N' . str_pad((string)($i + 1), 3, '0', STR_PAD_LEFT);
    $dob = date('Y-m-d', mktime(0, 0, 0, rand(1, 12), rand(1, 28), rand(2007, 2009)));
    $shs = $shsData[($i + 4) % count($shsData)];
    $enroll = date('Y-m-d', mktime(0, 0, 0, 6, 1, 2025));

    $sid = insertStudent($pdo, $sn, $n['first'], $n['mid'], $n['last'], $n['gender'],
                          $shs['strand'], $shs['grade'], $shs['dept'], $dob, $enroll);
    // No ID card inserted
    $insertedStudents[] = $sid;
    echo "    ✓ Not Created SHS: {$n['first']} {$n['last']} ({$sn}) — {$shs['strand']} | {$shs['grade']}\n";
}

$total = count($insertedStudents);
echo "\n✅ Done! Seeded {$total} students ({$total} College + SHS, mixed status).\n";
echo "   Run the Student ID Generation page to view the data.\n";
