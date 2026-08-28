<?php
require 'vendor/autoload.php';

// Test 1: Simple HTML
$simpleHtml = '<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial; }
        h1 { color: blue; }
    </style>
</head>
<body>
    <h1>Test Document</h1>
    <p>This is a simple test.</p>
</body>
</html>';

$dompdf = new \Dompdf\Dompdf();
$dompdf->loadHtml($simpleHtml);
$dompdf->setPaper('A4');
$dompdf->render();
file_put_contents('test-simple.pdf', $dompdf->output());

echo "Test 1: Simple HTML → test-simple.pdf\n";

// Test 2: Your template structure
$complexHtml = file_get_contents('modules/registrar/DocuFormat/good-moral.html');

// Check if file was read
if ($complexHtml === false) {
    die("ERROR: Could not read good-moral.html\n");
}

echo "Original template size: " . strlen($complexHtml) . " bytes\n";

// Replace images with placeholders (remove base64 bloat for testing)
$complexHtml = preg_replace('/src="[^"]*bestlink-logo\.png"/', 'src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=="', $complexHtml);
$complexHtml = preg_replace('/src="[^"]*bestlink-seal\.png"/', 'src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=="', $complexHtml);

// Replace student data
$complexHtml = str_replace('DOMINGO, CHARLENE BUENDIA', 'TEST STUDENT NAME', $complexHtml);

echo "Modified template size: " . strlen($complexHtml) . " bytes\n";

try {
    $dompdf2 = new \Dompdf\Dompdf();
    $dompdf2->loadHtml($complexHtml);
    $dompdf2->setPaper('A4');
    $dompdf2->render();
    file_put_contents('test-template.pdf', $dompdf2->output());

    echo "Test 2: Template HTML → test-template.pdf\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\nOpen test-simple.pdf and test-template.pdf to compare.\n";
?>
