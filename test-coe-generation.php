<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/modules/registrar/includes/template-engine.php';
require_once __DIR__ . '/modules/registrar/includes/document-engine.php';

$db = db();

// Get first student from database
$stmt = $db->query("SELECT id, student_number, first_name, last_name FROM `reg_students` LIMIT 1");
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    echo "No students found in database\n";
    exit;
}

echo "Testing COE generation for: " . $student['first_name'] . " " . $student['last_name'] . "\n";
echo "Student Number: " . $student['student_number'] . "\n\n";

// Generate COE
$result = regGenerateCertification($student['id'], 'Certificate of Enrollment', ['verification_code' => 'TEST-' . time()]);

if ($result['success']) {
    echo "✓ COE Generated Successfully\n";
    echo "  PDF Path: " . $result['pdf_path'] . "\n";
    echo "  File ID: " . $result['file_id'] . "\n";
    echo "  Doc No: " . $result['doc_no'] . "\n";
    echo "  Size: " . number_format(filesize($result['pdf_path'])) . " bytes\n";
} else {
    echo "✗ Failed: " . $result['error'] . "\n";
}
?>
