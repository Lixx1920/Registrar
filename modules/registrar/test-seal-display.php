<?php
/**
 * Test Seal Display in All Document Templates
 * 
 * This script tests if the seal (Seal-Display.png) is properly embedded
 * in all document templates when converted to PDF.
 */

declare(strict_types=1);

// Load required files
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/includes/template-engine.php';

echo "========================================\n";
echo "SEAL DISPLAY TEST\n";
echo "========================================\n\n";

// Check if seal file exists
$sealPath = __DIR__ . '/sealstamp/Seal-Display.png';
if (!file_exists($sealPath)) {
    die("❌ ERROR: Seal file not found at: $sealPath\n");
}
echo "✓ Seal file found: $sealPath\n";
echo "  Size: " . number_format(filesize($sealPath)) . " bytes\n\n";

// Test templates
$templates = [
    'good-moral.html' => 'Certificate of Good Moral Character',
    'certificate-enrollment.html' => 'Certificate of Enrollment',
    'certificate-grades.html' => 'Certificate of Grades',
    'statement-of-account.html' => 'Statement of Account'
];

// Sample student data for testing
$sampleStudent = [
    'student_number' => 'TEST-2024-001',
    'first_name' => 'Juan',
    'middle_name' => 'Cruz',
    'last_name' => 'Dela Cruz',
    'program_course' => 'Bachelor of Science in Information Technology',
    'year_section' => '4-A',
    'department' => 'College Department'
];

$testResults = [];

foreach ($templates as $templateFile => $docName) {
    echo "Testing: $docName ($templateFile)\n";
    echo str_repeat("-", 60) . "\n";
    
    // Check if template exists
    $templatePath = REG_TEMPLATE_DIR . '/' . $templateFile;
    if (!file_exists($templatePath)) {
        echo "  ❌ Template file not found!\n\n";
        $testResults[$docName] = 'Template Missing';
        continue;
    }
    
    // Load template
    $html = regLoadTemplate($templateFile, $sampleStudent, [
        'verification_code' => 'TEST-' . time(),
        'doc_number' => 'TEST-DOC-001'
    ]);
    
    if (empty($html)) {
        echo "  ❌ Failed to load template!\n\n";
        $testResults[$docName] = 'Load Failed';
        continue;
    }
    
    // Check if seal base64 is embedded
    $hasBase64Seal = (strpos($html, 'data:image/png;base64,') !== false);
    
    // Check if seal class exists in HTML
    $hasSealClass = (strpos($html, 'class="school-seal"') !== false || 
                     strpos($html, 'class="seal-stamp"') !== false);
    
    // Check if seal CSS exists
    $hasSealCss = (strpos($html, '.school-seal') !== false || 
                   strpos($html, '.seal-stamp') !== false);
    
    echo "  Seal Base64 Embedded: " . ($hasBase64Seal ? "✓ YES" : "❌ NO") . "\n";
    echo "  Seal HTML Element: " . ($hasSealClass ? "✓ YES" : "❌ NO") . "\n";
    echo "  Seal CSS Styling: " . ($hasSealCss ? "✓ YES" : "❌ NO") . "\n";
    
    // Save debug HTML
    $debugDir = ROOT_PATH . '/storage/uploads/registrar/generated';
    if (!is_dir($debugDir)) {
        mkdir($debugDir, 0700, true);
    }
    
    $debugFile = $debugDir . '/test_' . str_replace([' ', '.html'], ['_', ''], strtolower($templateFile)) . '_debug.html';
    file_put_contents($debugFile, $html);
    echo "  Debug HTML saved: $debugFile\n";
    
    // Try to generate PDF
    try {
        $pdfPath = regRenderTemplateToPdf($html, 'test_' . str_replace([' ', '.html'], ['_', ''], strtolower($templateFile)));
        
        if ($pdfPath && file_exists($pdfPath)) {
            echo "  ✓ PDF Generated: $pdfPath\n";
            echo "    PDF Size: " . number_format(filesize($pdfPath)) . " bytes\n";
            $testResults[$docName] = 'SUCCESS';
        } else {
            echo "  ⚠ PDF generation failed (fallback to HTML)\n";
            $testResults[$docName] = 'HTML Fallback';
        }
    } catch (Throwable $e) {
        echo "  ❌ Error: " . $e->getMessage() . "\n";
        $testResults[$docName] = 'Error: ' . $e->getMessage();
    }
    
    echo "\n";
}

// Summary
echo "\n========================================\n";
echo "TEST SUMMARY\n";
echo "========================================\n\n";

$successCount = 0;
foreach ($testResults as $docName => $result) {
    $icon = ($result === 'SUCCESS') ? '✓' : (strpos($result, 'HTML') !== false ? '⚠' : '❌');
    echo "$icon $docName: $result\n";
    if ($result === 'SUCCESS') $successCount++;
}

echo "\n";
echo "Success Rate: $successCount / " . count($testResults) . " documents\n";
echo "\n";

// Instructions
echo "========================================\n";
echo "NEXT STEPS\n";
echo "========================================\n\n";
echo "1. Open the generated PDF files to verify seal visibility\n";
echo "2. Check if seal opacity and positioning look correct\n";
echo "3. If seal is not visible, check the debug HTML files\n";
echo "4. Test through the actual system by generating documents\n";
echo "\n";
echo "Generated files location:\n";
echo "$debugDir\n";
echo "\n";

?>
