<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/modules/registrar/includes/template-engine.php';
require_once __DIR__ . '/modules/registrar/includes/document-engine.php';

// Get database connection
$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get first student
$stmt = $pdo->query("SELECT id, student_number, first_name, middle_name, last_name, college_department, program_course, year_section, enrollment_date FROM `reg_students` LIMIT 1");
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    echo "No students found\n";
    exit;
}

echo "Student: " . $student['first_name'] . " " . $student['last_name'] . "\n";
echo "Number: " . $student['student_number'] . "\n";
echo "Program: " . $student['program_course'] . "\n";
echo "Department: " . $student['college_department'] . "\n\n";

// Load template directly
$templatePath = __DIR__ . '/modules/registrar/DocuFormat/certificate-enrollment.html';
if (!file_exists($templatePath)) {
    echo "Template not found: $templatePath\n";
    exit;
}

$html = file_get_contents($templatePath);

// Get base64 images
$logoPath = __DIR__ . '/images/bestlink.png';
$sealPath = __DIR__ . '/modules/registrar/sealstamp/Seal-Display.png';

function getBase64($path) {
    if (!file_exists($path)) return '';
    $data = file_get_contents($path);
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $path);
    finfo_close($finfo);
    return "data:$mime;base64," . base64_encode($data);
}

$logoBase64 = getBase64($logoPath);
$sealBase64 = getBase64($sealPath);

// Replace images
if (!empty($logoBase64)) {
    $html = preg_replace('/src=["\']bestlink-logo\.png["\']/', 'src="' . $logoBase64 . '"', $html);
}
if (!empty($sealBase64)) {
    $html = preg_replace('/src=["\']Seal-Display\.png["\']/', 'src="' . $sealBase64 . '"', $html);
}

// Replace student data
$fullName = strtoupper(trim(($student['last_name'] ?? '') . ', ' . ($student['first_name'] ?? '') . ' ' . ($student['middle_name'] ?? '')));
$academicYear = date('Y') . '-' . (date('Y') + 1);
$enrollmentDate = isset($student['enrollment_date']) ? date('F d, Y', strtotime($student['enrollment_date'])) : date('F d, Y');

$replacements = [
    '{{REGISTRATION_DATE}}' => $enrollmentDate,
    '{{DEPARTMENT}}' => $student['college_department'] ?? 'N/A',
    '{{MAJOR}}' => $student['college_department'] ?? 'N/A',
    'DOMINGO, CHARLENE BUENDIA' => $fullName,
    '2021-00123' => $student['student_number'],
    'Bachelor of Science in Computer Science' => $student['program_course'] ?? 'N/A',
    '4th Year' => regGetYearLevel($student['year_section'] ?? ''),
    '2026-2027' => $academicYear,
];

foreach ($replacements as $search => $replace) {
    $html = str_replace($search, $replace, $html);
}

// Save debug HTML
$debugFile = __DIR__ . '/storage/uploads/registrar/generated/real_student_coe_debug.html';
file_put_contents($debugFile, $html);
echo "✓ Debug HTML saved\n";

// Check replacements
if (strpos($html, '{{REGISTRATION_DATE}}') === false) {
    echo "✓ {{REGISTRATION_DATE}} replaced\n";
} else {
    echo "✗ {{REGISTRATION_DATE}} NOT replaced\n";
}

if (strpos($html, '{{DEPARTMENT}}') === false) {
    echo "✓ {{DEPARTMENT}} replaced\n";
} else {
    echo "✗ {{DEPARTMENT}} NOT replaced\n";
}

if (strpos($html, $fullName) !== false) {
    echo "✓ Student name populated: $fullName\n";
}

if (strpos($html, $student['student_number']) !== false) {
    echo "✓ Student number populated\n";
}

echo "\nDebug file: $debugFile\n";

function regGetYearLevel($yearSection) {
    if (empty($yearSection)) return 'N/A';
    $year = (int) substr($yearSection, 0, 1);
    $suffix = match($year) {
        1 => '1st', 2 => '2nd', 3 => '3rd',
        default => $year . 'th'
    };
    return $suffix . ' Year';
}
?>
