<?php
require_once __DIR__ . '/config/database.php';

$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get a student
$stmt = $pdo->query("SELECT student_number, first_name, middle_name, last_name, college_department, program_course, year_section, enrollment_date FROM `reg_students` LIMIT 1");
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    echo "No students in database\n";
    exit;
}

echo "✓ Found student: " . $student['first_name'] . " " . $student['last_name'] . "\n";
echo "  Student Number: " . $student['student_number'] . "\n";
echo "  Department: " . ($student['college_department'] ?? 'N/A') . "\n";
echo "  Program: " . ($student['program_course'] ?? 'N/A') . "\n";
echo "  Year/Section: " . ($student['year_section'] ?? 'N/A') . "\n";

// Load template
$templatePath = __DIR__ . '/modules/registrar/DocuFormat/certificate-enrollment.html';
$template = file_get_contents($templatePath);

// Check what placeholders exist
$placeholders = [];
if (preg_match_all('/\{\{[A-Z_]+\}\}/', $template, $matches)) {
    $placeholders = $matches[0];
}

echo "\n✓ Template placeholders found: " . implode(', ', array_unique($placeholders)) . "\n";

// Check if student data fields will fill them
echo "\nData check:\n";
echo "  REGISTRATION_DATE: " . (isset($student['enrollment_date']) ? date('F d, Y', strtotime($student['enrollment_date'])) : 'EMPTY') . "\n";
echo "  DEPARTMENT: " . ($student['college_department'] ?? 'EMPTY') . "\n";
echo "  MAJOR: " . ($student['college_department'] ?? 'EMPTY') . "\n";
?>
