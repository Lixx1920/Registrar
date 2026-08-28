<?php
/**
 * SMS 2 - Masterlist Demo Data Seeder (CLI only, additive).
 *
 * Generates a large, realistic student body for testing the Masterlist
 * Generator: 15 real BCP programs x 4 year levels x 1 section each,
 * ~30-38 students per section (randomized), student numbers starting at
 * 230000001.
 *
 * This is INTENTIONALLY separate from database/seed.php -- it does not
 * touch the original 10 demo students or their guardians/academic
 * history/persona files. Only bare reg_students rows are created here
 * (student number, name, program, year & section, status = Active).
 *
 * Idempotency: refuses to run twice. If student_number 230000001 already
 * exists, it assumes this seeder already ran and exits without inserting
 * anything, so re-running by accident can't create duplicates.
 *
 * CLI:
 *   C:\xampp\php\php.exe modules/registrar/database/seed-masterlist-demo.php
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

// ── Idempotency guard ───────────────────────────────────────────────────────
$startingNumber = 230000001;
$check = $pdo->prepare("SELECT 1 FROM `reg_students` WHERE `student_number` = ?");
$check->execute([(string) $startingNumber]);
if ($check->fetchColumn()) {
    echo "This seeder has already run (student number {$startingNumber} exists). Nothing to do.\n";
    exit(0);
}

// ── The 15 real BCP programs (no majors split, no SHS -- per current scope) ─
$programs = [
    'BS Information Technology',
    'BS Hospitality Management',
    'BS Accounting Information System',
    'BS Tourism Management',
    'BS Office Administration',
    'BS Entrepreneurship',
    'BS Business Administration',
    'Bachelor of Library Information Science',
    'BS Computer Engineering',
    'BS Psychology',
    'BS Criminology',
    'BS Physical Education',
    'BS Technological & Livelihood Education',
    'BS Elementary Education',
    'BS Secondary Education',
];

$yearLevels = ['I' => 1, 'II' => 2, 'III' => 3, 'IV' => 4]; // label => numeric offset for DOB/enrollment math

// ── Name pools (real, varied Filipino names) ─────────────────────────────────
$maleFirstNames = [
    'Juan','Jose','Miguel','Carlos','Antonio','Manuel','Pedro','Ramon','Ricardo','Fernando',
    'Alfredo','Roberto','Eduardo','Rafael','Gabriel','Daniel','Marco','Paulo','Vicente','Rodrigo',
    'Emmanuel','Christian','Joshua','Mark','John','Paul','Michael','Kevin','Aldrin','Bryan',
    'Dennis','Edgar','Francis','Gerald','Henry','Ivan','Jerome','Kristian','Leo','Marvin',
    'Nathaniel','Oliver','Patrick','Quennie','Raymond','Samuel','Timothy','Victor','Warren','Xander',
    'Angelo','Benedict','Cesar','Diego','Enrico','Felix','Gregorio','Hector','Isaac','Julius',
];
$femaleFirstNames = [
    'Maria','Rosa','Angela','Lucia','Sofia','Carmen','Teresa','Josefina','Cristina','Beatriz',
    'Consuelo','Dolores','Esperanza','Gloria','Isabel','Juana','Leonora','Mercedes','Natividad','Pilar',
    'Andrea','Bianca','Camille','Diana','Elaine','Faith','Grace','Hannah','Irene','Janine',
    'Katrina','Liza','Michelle','Nicole','Olivia','Patricia','Queenie','Rachel','Samantha','Trisha',
    'Angelica','Bernadette','Carmela','Dianne','Erica','Francesca','Gemma','Honeylet','Imelda','Jasmine',
    'Karen','Loraine','Marites','Norma','Precious','Rowena','Shiela','Vanessa','Winnie','Yolanda',
];
$lastNames = [
    'Santos','Cruz','Reyes','Lopez','Mendoza','Pascual','Torres','Fernandez','Garcia','Alvarez',
    'Villanueva','Bautista','Ramos','Aquino','Del Rosario','Castro','Flores','Rivera','Salazar','Gonzales',
    'Hernandez','Diaz','Morales','Navarro','Domingo','Aguilar','Marquez','Ocampo','Pantoja','Quiambao',
    'Rodriguez','Soriano','Tolentino','Uy','Valdez','Ventura','Yap','Zamora','Abad','Bernardo',
    'Cabrera','David','Esguerra','Feliciano','Gutierrez','Ignacio','Jimenez','Lacson','Manalo','Nepomuceno',
    'Ortega','Perez','Quirino','Roque','Sarmiento','Tan','Umali','Vergara','Wong','Yabut',
    'Antonio','Buenaventura','Concepcion','Dizon','Espiritu','Franco','Guevarra','Herrera','Ilagan','Jose',
    'Katigbak','Lim','Macaraeg','Nolasco','Olivar','Padilla','Quisumbing','Ramirez','Sison','Trinidad',
    'Unson','Vasquez','Wenceslao','Ye','Zaragoza','Angeles','Bagayaua','Custodio','De Leon','Espino',
];

/**
 * Deterministic-ish unique name picker with a small retry loop, so we don't
 * chase perfection but avoid the obvious duplicate at this scale.
 */
function pickUniqueName(array &$usedNames, array $maleFirst, array $femaleFirst, array $last): array
{
    for ($attempt = 0; $attempt < 25; $attempt++) {
        $gender = (mt_rand(0, 1) === 0) ? 'Male' : 'Female';
        $first  = $gender === 'Male' ? $maleFirst[array_rand($maleFirst)] : $femaleFirst[array_rand($femaleFirst)];
        $lastN  = $last[array_rand($last)];
        $key    = $first . '|' . $lastN;

        if (!isset($usedNames[$key])) {
            $usedNames[$key] = true;
            return [$first, $lastN, $gender];
        }
    }
    // Extremely unlikely fallback: accept the last generated combo anyway.
    return [$first, $lastN, $gender];
}

// ── Generate and insert ──────────────────────────────────────────────────────
$insert = $pdo->prepare("INSERT INTO `reg_students`
    (`student_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`,
     `gender`, `nationality`, `program_course`, `year_section`, `status`, `enrollment_date`)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Active', ?)");

$usedNames    = [];
$studentNum   = $startingNumber;
$totalCreated = 0;
$currentYear  = (int) date('Y');

$pdo->beginTransaction();

foreach ($programs as $program) {
    foreach ($yearLevels as $label => $levelNum) {
        $yearSection = $label . '-A';
        $sectionSize = random_int(30, 38);

        for ($i = 0; $i < $sectionSize; $i++) {
            [$firstName, $lastName, $gender] = pickUniqueName($usedNames, $maleFirstNames, $femaleFirstNames, $lastNames);
            $middleInitial = chr(random_int(65, 90)) . '.'; // e.g. "M."

            // Plausible age for the year level: 1st Year ~18, up to 4th Year ~21+.
            $age = 17 + $levelNum + random_int(0, 1);
            $birthYear  = $currentYear - $age;
            $birthMonth = random_int(1, 12);
            $birthDay   = random_int(1, 28);
            $dob = sprintf('%04d-%02d-%02d', $birthYear, $birthMonth, $birthDay);

            // Enrollment date roughly matches how many years they've been in college.
            $enrollYear = $currentYear - ($levelNum - 1);
            $enrollDate = sprintf('%04d-06-01', $enrollYear);

            $insert->execute([
                (string) $studentNum,
                $firstName,
                $middleInitial,
                $lastName,
                $dob,
                $gender,
                'Filipino',
                $program,
                $yearSection,
                $enrollDate,
            ]);

            $studentNum++;
            $totalCreated++;
        }

        echo "  {$program} / {$yearSection}: {$sectionSize} students\n";
    }
}

$pdo->commit();

echo "\nDone. Created {$totalCreated} students (student numbers {$startingNumber} to " . ($studentNum - 1) . ").\n";
echo "Next: re-run modules/scheduling/database/seed.php to auto-create matching sections + assignments.\n";