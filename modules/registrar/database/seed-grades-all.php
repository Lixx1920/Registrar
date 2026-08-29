<?php
/**
 * SMS 2 - Seed Complete Collegiate Grade History & Educational Background for ALL Students
 *
 * Populates:
 * 1. reg_academic_subjects: Multi-term collegiate grades from 1st Year 1st Sem up to current standing
 * 2. reg_academic_history: Elementary, JHS, and SHS educational background
 *
 * CLI: php modules/registrar/database/seed-grades-all.php
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
    fwrite(STDERR, "ERROR: Cannot connect to sms2_db.\n");
    exit(1);
}

echo "🌱 Seeding Grade History and Academic Records for ALL students...\n\n";

// ── Program Curricula Map ───────────────────────────────────────────────────
$curricula = [
    'TECH' => [
        [
            'year_level' => '1st Year', 'term' => '1st', 'ay_offset' => -3,
            'subjects' => [
                ['CC101', 'Introduction to Computing', 3.0, '1.25', 'Prof. R. Cruz'],
                ['CC102', 'Fundamentals of Programming', 3.0, '1.50', 'Engr. J. Santos'],
                ['GE101', 'Understanding the Self', 3.0, '1.25', 'Dr. A. Reyes'],
                ['GE102', 'Purposive Communication', 3.0, '1.75', 'Prof. M. Ramos'],
                ['MATH101', 'Mathematics in the Modern World', 3.0, '1.50', 'Prof. D. Garcia'],
                ['NSTP101', 'National Service Training Program 1', 3.0, '1.25', 'Capt. V. Luna'],
                ['PE101', 'Physical Fitness and Wellness', 2.0, '1.00', 'Coach B. Diaz'],
            ]
        ],
        [
            'year_level' => '1st Year', 'term' => '2nd', 'ay_offset' => -3,
            'subjects' => [
                ['CC103', 'Intermediate Computer Programming', 3.0, '1.50', 'Engr. J. Santos'],
                ['CC104', 'Data Structures and Algorithms', 3.0, '1.75', 'Prof. R. Cruz'],
                ['GE103', 'Readings in Philippine History', 3.0, '1.25', 'Dr. A. Reyes'],
                ['GE104', 'The Contemporary World', 3.0, '1.50', 'Prof. M. Ramos'],
                ['GE105', 'Art Appreciation', 3.0, '1.25', 'Prof. C. Flores'],
                ['NSTP102', 'National Service Training Program 2', 3.0, '1.00', 'Capt. V. Luna'],
                ['PE102', 'Rhythmic Activities', 2.0, '1.00', 'Coach B. Diaz'],
            ]
        ],
        [
            'year_level' => '2nd Year', 'term' => '1st', 'ay_offset' => -2,
            'subjects' => [
                ['IT201', 'Object-Oriented Programming', 3.0, '1.50', 'Engr. J. Santos'],
                ['IT202', 'Information Management / DBMS', 3.0, '1.25', 'Prof. L. Mendoza'],
                ['IT203', 'Discrete Mathematics', 3.0, '1.75', 'Prof. D. Garcia'],
                ['GE106', 'Ethics', 3.0, '1.50', 'Dr. A. Reyes'],
                ['GE107', 'Science, Technology and Society', 3.0, '1.75', 'Prof. K. Perez'],
                ['PE103', 'Individual and Dual Sports', 2.0, '1.25', 'Coach B. Diaz'],
            ]
        ],
        [
            'year_level' => '2nd Year', 'term' => '2nd', 'ay_offset' => -2,
            'subjects' => [
                ['IT204', 'Web Systems and Technologies', 3.0, '1.25', 'Prof. E. Ramos'],
                ['IT205', 'Operating Systems and Architecture', 3.0, '1.75', 'Prof. L. Mendoza'],
                ['IT206', 'Networking 1 (Fundamentals)', 3.0, '1.50', 'Engr. M. Tolentino'],
                ['GE108', 'Life and Works of Rizal', 3.0, '1.25', 'Dr. A. Reyes'],
                ['PE104', 'Team Sports', 2.0, '1.00', 'Coach B. Diaz'],
            ]
        ],
        [
            'year_level' => '3rd Year', 'term' => '1st', 'ay_offset' => -1,
            'subjects' => [
                ['IT301', 'Systems Analysis and Design', 3.0, '1.50', 'Prof. L. Mendoza'],
                ['IT302', 'Advanced Web Development', 3.0, '1.25', 'Prof. E. Ramos'],
                ['IT303', 'Networking 2 (Routing & Switching)', 3.0, '1.75', 'Engr. M. Tolentino'],
                ['IT304', 'Information Assurance & Security', 3.0, '1.50', 'Prof. S. Navarro'],
                ['ITE101', 'IT Elective 1 (Cloud Computing)', 3.0, '1.25', 'Prof. R. Cruz'],
            ]
        ],
        [
            'year_level' => '3rd Year', 'term' => '2nd', 'ay_offset' => -1,
            'subjects' => [
                ['IT305', 'Mobile Application Development', 3.0, '1.50', 'Prof. E. Ramos'],
                ['IT306', 'Capstone Project & Research 1', 3.0, '1.25', 'Dr. V. Aquino'],
                ['IT307', 'Quantitative Methods & Analytics', 3.0, '1.75', 'Prof. D. Garcia'],
                ['ITE102', 'IT Elective 2 (Cybersecurity)', 3.0, '1.50', 'Prof. S. Navarro'],
            ]
        ],
        [
            'year_level' => '4th Year', 'term' => '1st', 'ay_offset' => 0,
            'subjects' => [
                ['IT401', 'Capstone Project & Research 2', 3.0, '1.25', 'Dr. V. Aquino'],
                ['IT402', 'Systems Administration & Maintenance', 3.0, '1.50', 'Engr. M. Tolentino'],
                ['IT403', 'Practicum / Industry Internship (300 hrs)', 6.0, '1.00', 'Dr. V. Aquino'],
            ]
        ],
    ],
    'BUSINESS' => [
        [
            'year_level' => '1st Year', 'term' => '1st', 'ay_offset' => -3,
            'subjects' => [
                ['BA101', 'Principles of Management', 3.0, '1.25', 'Prof. M. Castro'],
                ['BA102', 'Financial Accounting Fundamentals', 3.0, '1.50', 'Prof. J. Dizon'],
                ['GE101', 'Understanding the Self', 3.0, '1.25', 'Dr. A. Reyes'],
                ['GE102', 'Purposive Communication', 3.0, '1.50', 'Prof. M. Ramos'],
                ['MATH101', 'Mathematics in the Modern World', 3.0, '1.75', 'Prof. D. Garcia'],
                ['NSTP101', 'National Service Training Program 1', 3.0, '1.00', 'Capt. V. Luna'],
                ['PE101', 'Physical Fitness and Wellness', 2.0, '1.00', 'Coach B. Diaz'],
            ]
        ],
        [
            'year_level' => '1st Year', 'term' => '2nd', 'ay_offset' => -3,
            'subjects' => [
                ['BA103', 'Business Organization and Environment', 3.0, '1.50', 'Prof. M. Castro'],
                ['BA104', 'Managerial Accounting', 3.0, '1.75', 'Prof. J. Dizon'],
                ['GE103', 'Readings in Philippine History', 3.0, '1.25', 'Dr. A. Reyes'],
                ['GE104', 'The Contemporary World', 3.0, '1.50', 'Prof. M. Ramos'],
                ['GE105', 'Art Appreciation', 3.0, '1.25', 'Prof. C. Flores'],
                ['NSTP102', 'National Service Training Program 2', 3.0, '1.00', 'Capt. V. Luna'],
                ['PE102', 'Rhythmic Activities', 2.0, '1.00', 'Coach B. Diaz'],
            ]
        ],
        [
            'year_level' => '2nd Year', 'term' => '1st', 'ay_offset' => -2,
            'subjects' => [
                ['BA201', 'Business Law and Obligations', 3.0, '1.50', 'Atty. R. Tan'],
                ['BA202', 'Marketing Management', 3.0, '1.25', 'Prof. E. Morales'],
                ['BA203', 'Microeconomics', 3.0, '1.75', 'Prof. F. Gomez'],
                ['GE106', 'Ethics', 3.0, '1.50', 'Dr. A. Reyes'],
                ['GE107', 'Science, Technology and Society', 3.0, '1.75', 'Prof. K. Perez'],
                ['PE103', 'Individual and Dual Sports', 2.0, '1.25', 'Coach B. Diaz'],
            ]
        ],
        [
            'year_level' => '2nd Year', 'term' => '2nd', 'ay_offset' => -2,
            'subjects' => [
                ['BA204', 'Financial Management', 3.0, '1.50', 'Prof. J. Dizon'],
                ['BA205', 'Human Resource Management', 3.0, '1.25', 'Prof. M. Castro'],
                ['BA206', 'Macroeconomics', 3.0, '1.75', 'Prof. F. Gomez'],
                ['GE108', 'Life and Works of Rizal', 3.0, '1.25', 'Dr. A. Reyes'],
                ['PE104', 'Team Sports', 2.0, '1.00', 'Coach B. Diaz'],
            ]
        ],
        [
            'year_level' => '3rd Year', 'term' => '1st', 'ay_offset' => -1,
            'subjects' => [
                ['BA301', 'Operations Management and TQM', 3.0, '1.50', 'Prof. E. Morales'],
                ['BA302', 'Business Analytics and Forecasting', 3.0, '1.25', 'Prof. D. Garcia'],
                ['BA303', 'Taxation (Income and Business)', 3.0, '1.75', 'Atty. R. Tan'],
                ['BA304', 'Corporate Governance and Ethics', 3.0, '1.50', 'Prof. M. Castro'],
            ]
        ],
        [
            'year_level' => '3rd Year', 'term' => '2nd', 'ay_offset' => -1,
            'subjects' => [
                ['BA305', 'Strategic Management', 3.0, '1.25', 'Prof. M. Castro'],
                ['BA306', 'Business Feasibility and Research 1', 3.0, '1.50', 'Dr. H. Santos'],
                ['BA307', 'International Business and Trade', 3.0, '1.50', 'Prof. E. Morales'],
            ]
        ],
        [
            'year_level' => '4th Year', 'term' => '1st', 'ay_offset' => 0,
            'subjects' => [
                ['BA401', 'Business Feasibility and Research 2', 3.0, '1.25', 'Dr. H. Santos'],
                ['BA402', 'Corporate Internship / Practicum (300 hrs)', 6.0, '1.00', 'Prof. M. Castro'],
            ]
        ],
    ]
];

// Helper to determine curriculum track from program
function getCurriculumCategory(string $program): string {
    $p = strtolower($program);
    if (str_contains($p, 'information') || str_contains($p, 'computer') || str_contains($p, 'tech')) {
        return 'TECH';
    }
    return 'BUSINESS';
}

$students = $pdo->query("SELECT `id`, `student_number`, `first_name`, `last_name`, `program_course`, `year_section` FROM `reg_students` WHERE `status` != 'Deleted'")->fetchAll(PDO::FETCH_ASSOC);

echo "Found " . count($students) . " students in the database.\n";

$gradeVariations = ['1.00', '1.25', '1.50', '1.75', '2.00', '2.25', '2.50', '2.75', '3.00'];

// Clear existing subject grades and academic history so all students are fresh & clean
$pdo->exec("DELETE FROM `reg_academic_subjects` WHERE 1=1");
$pdo->exec("DELETE FROM `reg_academic_history` WHERE 1=1");

$currentYear = (int)date('Y');
$stmtSubject = $pdo->prepare("INSERT INTO `reg_academic_subjects`
    (`student_id`, `subject_code`, `subject_name`, `units`, `year_level`, `term`, `academic_year`, `grade`, `remarks`, `status`, `instructor`)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmtHistory = $pdo->prepare("INSERT INTO `reg_academic_history`
    (`student_id`, `school_name`, `level`, `from_year`, `to_year`, `awards`, `remarks`)
    VALUES (?, ?, ?, ?, ?, ?, ?)");

$pdo->beginTransaction();

$totalSubjectCount = 0;
$totalHistoryCount = 0;

$priorSchools = [
    'Elementary' => ['San Jose Elementary School', 'Bulacan Central Elementary School', 'St. Joseph Academy Elementary', 'Bagong Silang Elementary School', 'Caloocan Elementary School'],
    'JHS'        => ['Bulacan National High School', 'Quezon City High School', 'St. Mary\'s High School', 'Caloocan National High School', 'Novalliches High School'],
    'SHS'        => ['Bestlink College of the Philippines - SHS', 'Arellano University Senior High', 'AMA Computer College SHS', 'University of Caloocan City SHS']
];

foreach ($students as $sIdx => $st) {
    $sid = (int)$st['id'];
    $prog = $st['program_course'] ?? 'BS Information Technology';
    $yearSec = strtoupper((string)($st['year_section'] ?? 'I-A'));

    // Determine how many semesters based on year level
    $semestersToSeed = 2; // Default 1st Year (I-A)
    if (str_starts_with($yearSec, 'IV') || str_starts_with($yearSec, '4')) {
        $semestersToSeed = 7;
    } elseif (str_starts_with($yearSec, 'III') || str_starts_with($yearSec, '3')) {
        $semestersToSeed = 5;
    } elseif (str_starts_with($yearSec, 'II') || str_starts_with($yearSec, '2')) {
        $semestersToSeed = 3;
    }

    $category = getCurriculumCategory($prog);
    $curriculumBlocks = $curricula[$category];

    // Seed Collegiate Subject Grades
    for ($bIdx = 0; $bIdx < $semestersToSeed && $bIdx < count($curriculumBlocks); $bIdx++) {
        $block = $curriculumBlocks[$bIdx];
        $ayStart = $currentYear + $block['ay_offset'];
        $ayString = $ayStart . '-' . ($ayStart + 1);

        foreach ($block['subjects'] as $subIdx => $subj) {
            // Pick grade with slight variation
            $varIndex = ($sIdx + $subIdx + $bIdx) % count($gradeVariations);
            $grade = $gradeVariations[$varIndex];
            $status = ((float)$grade <= 3.00) ? 'Passed' : 'Failed';

            $stmtSubject->execute([
                $sid,
                $subj[0],
                $subj[1],
                $subj[2],
                $block['year_level'],
                $block['term'],
                $ayString,
                $grade,
                $status,
                $status,
                $subj[4]
            ]);
            $totalSubjectCount++;
        }
    }

    // Seed Prior Educational Background (Elementary, JHS, SHS)
    $elemSchool = $priorSchools['Elementary'][$sIdx % count($priorSchools['Elementary'])];
    $jhsSchool  = $priorSchools['JHS'][$sIdx % count($priorSchools['JHS'])];
    $shsSchool  = $priorSchools['SHS'][$sIdx % count($priorSchools['SHS'])];

    // Elementary
    $stmtHistory->execute([
        $sid, $elemSchool, 'Elementary', $currentYear - 12, $currentYear - 6,
        ($sIdx % 3 === 0) ? 'Honor Pupil' : null, 'Completed'
    ]);
    // Junior High
    $stmtHistory->execute([
        $sid, $jhsSchool, 'Junior High School', $currentYear - 6, $currentYear - 2,
        ($sIdx % 4 === 0) ? 'With Honors' : null, 'Completed'
    ]);
    // Senior High
    $stmtHistory->execute([
        $sid, $shsSchool, 'Senior High School', $currentYear - 2, $currentYear,
        ($sIdx % 2 === 0) ? 'With Honors' : 'Good Standing', 'Graduated'
    ]);

    $totalHistoryCount += 3;

    if ($sIdx % 200 === 0) {
        echo "  Processed {$sIdx} / " . count($students) . " students...\n";
    }
}

$pdo->commit();

echo "\n✅ Successfully seeded:\n";
echo "   - {$totalSubjectCount} collegiate subject grade records\n";
echo "   - {$totalHistoryCount} prior educational background records\n";
echo "   - Across ALL " . count($students) . " students in the system.\n";
