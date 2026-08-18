<?php
/**
 * SMS 2 - Registrar Document Engine
 * PDF generation for Form 137, Certificates, and Transcripts
 * 
 * Requires FPDF library: https://www.fpdf.org/
 * Install: Place fpdf.php in vendor/
 */
declare(strict_types=1);

// Try to load FPDF if available
$fpdfPath = dirname(__DIR__) . '/vendor/fpdf.php';
if (file_exists($fpdfPath)) {
    require_once $fpdfPath;
}

/**
 * Generate document number with counter
 * Format: PREFIX-YYYY-#### (e.g., FORM137-2024-0001)
 */
function regGenerateDocumentNumber(string $counterKey): string
{
    global $db;
    
    try {
        // Get or create counter
        $stmt = $db->prepare("SELECT `counter_value`, `prefix`, `format_pattern` 
            FROM `reg_counters` WHERE `counter_key` = ?");
        $stmt->execute([$counterKey]);
        $counter = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$counter) {
            // Create new counter
            $stmt = $db->prepare("INSERT INTO `reg_counters` 
                (`counter_key`, `counter_value`, `prefix`, `format_pattern`) 
                VALUES (?, 0, ?, ?)");
            $stmt->execute([$counterKey, $counterKey, 'YYYY-####']);
            $counter = ['counter_value' => 0, 'prefix' => $counterKey, 'format_pattern' => 'YYYY-####'];
        }
        
        // Increment counter
        $newValue = $counter['counter_value'] + 1;
        $stmt = $db->prepare("UPDATE `reg_counters` SET `counter_value` = ? WHERE `counter_key` = ?");
        $stmt->execute([$newValue, $counterKey]);
        
        // Format the number
        $prefix = $counter['prefix'];
        $year = date('Y');
        $padded = str_pad((string)$newValue, 4, '0', STR_PAD_LEFT);
        
        return "$prefix-$year-$padded";
    } catch (Throwable $e) {
        // Fallback to timestamp-based
        return $counterKey . '-' . time();
    }
}

/**
 * Generate a basic PDF (text-based, no FPDF required)
 * Returns: path to generated PDF file
 */
function regGenerateBasicPdf(string $filename, array $content): ?string
{
    $pdfDir = dirname(__DIR__, 2) . '/storage/uploads/registrar/generated';
    if (!is_dir($pdfDir)) {
        mkdir($pdfDir, 0700, true);
    }
    
    $pdfPath = $pdfDir . '/' . $filename . '_' . time() . '.pdf';
    
    // Create a simple text-based PDF
    $pdf = "%PDF-1.4\n";
    $pdf .= "1 0 obj\n<</Type /Catalog /Pages 2 0 R>>\nendobj\n";
    $pdf .= "2 0 obj\n<</Type /Pages /Kids [3 0 R] /Count 1>>\nendobj\n";
    $pdf .= "3 0 obj\n<</Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources <</Font <</F1 5 0 R>>>>>>\nendobj\n";
    
    // Build content stream
    $textContent = "BT\n/F1 12 Tf\n50 750 Td\n";
    foreach ($content as $line) {
        $line = addslashes($line);
        $textContent .= "($line) Tj\n0 -20 Td\n";
    }
    $textContent .= "ET\n";
    
    $pdf .= "4 0 obj\n<</Length " . strlen($textContent) . ">>\nstream\n$textContent\nendstream\nendobj\n";
    $pdf .= "5 0 obj\n<</Type /Font /Subtype /Type1 /BaseFont /Helvetica>>\nendobj\n";
    $pdf .= "xref\n0 6\n";
    $pdf .= "0000000000 65535 f\n";
    
    // Simplified xref (not production-ready)
    $pdf .= "startxref\n100\n%%EOF\n";
    
    if (file_put_contents($pdfPath, $pdf) !== false) {
        chmod($pdfPath, 0600);
        return $pdfPath;
    }
    
    return null;
}

/**
 * Generate Form 137 (Transcript of Records) PDF
 * 
 * Returns: ['success' => true, 'pdf_path' => str, 'file_id' => int] or ['success' => false, 'error' => str]
 */
function regGenerateForm137(int $studentId, array $options = []): array
{
    global $db;
    
    // Fetch student data
    $stmt = $db->prepare("SELECT * FROM `reg_students` WHERE `id` = ?");
    $stmt->execute([$studentId]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$student) {
        return ['success' => false, 'error' => 'Student not found'];
    }
    
    // Fetch academic subjects
    $stmt = $db->prepare("SELECT * FROM `reg_academic_subjects` WHERE `student_id` = ? ORDER BY `academic_year`, `term`");
    $stmt->execute([$studentId]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Build PDF content
    $docNo = regGenerateDocumentNumber('FORM137');
    $content = [
        "BESTLINK COLLEGE OF THE PHILIPPINES",
        "Form 137 - Transcript of Records",
        "",
        "Document No: $docNo",
        "Issue Date: " . date('F d, Y'),
        "",
        "Student Information:",
        "Name: {$student['first_name']} {$student['middle_name']} {$student['last_name']}",
        "Student No: {$student['student_number']}",
        "Program: {$student['program_course']}",
        "Year/Section: {$student['year_section']}",
        "",
        "Academic Records:",
    ];
    
    // Group subjects by academic year
    $byYear = [];
    foreach ($subjects as $subj) {
        $byYear[$subj['academic_year']][] = $subj;
    }
    
    foreach ($byYear as $year => $yearSubjects) {
        $content[] = "A.Y. $year:";
        foreach ($yearSubjects as $subj) {
            $content[] = "  {$subj['subject_code']} - {$subj['subject_name']} ({$subj['units']} units) Grade: {$subj['grade']}";
        }
        $content[] = "";
    }
    
    $content[] = "Authenticated by: Registrar";
    $content[] = "Date: " . date('F d, Y');
    
    // Generate PDF
    $pdfPath = regGenerateBasicPdf("form137-{$student['student_number']}", $content);
    if (!$pdfPath) {
        return ['success' => false, 'error' => 'Cannot generate PDF'];
    }
    
    // Store in database
    try {
        $stmt = $db->prepare("INSERT INTO `reg_files` 
            (`student_id`, `category`, `original_name`, `stored_name`, `mime`, `size`, `sha256_hash`, `uploaded_by`, `status`) 
            VALUES (?, 'documents', ?, ?, 'application/pdf', ?, ?, ?, 'Active')");
        
        $filename = basename($pdfPath);
        $filesize = filesize($pdfPath);
        $hash = hash_file('sha256', $pdfPath);
        
        $stmt->execute([$studentId, "Form 137 - $docNo", $filename, $filesize, $hash, 1]);
        $fileId = (int)$db->lastInsertId();
    } catch (Throwable $e) {
        return ['success' => false, 'error' => 'Cannot store PDF in database: ' . $e->getMessage()];
    }
    
    return ['success' => true, 'pdf_path' => $pdfPath, 'file_id' => $fileId, 'doc_no' => $docNo];
}

/**
 * Generate Certificate of Good Moral Character PDF
 */
function regGenerateGoodMoral(int $studentId, array $options = []): array
{
    global $db;
    
    $stmt = $db->prepare("SELECT * FROM `reg_students` WHERE `id` = ?");
    $stmt->execute([$studentId]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$student) {
        return ['success' => false, 'error' => 'Student not found'];
    }
    
    $docNo = regGenerateDocumentNumber('GMC');
    $content = [
        "BESTLINK COLLEGE OF THE PHILIPPINES",
        "Certificate of Good Moral Character",
        "",
        "Document No: $docNo",
        "Issue Date: " . date('F d, Y'),
        "",
        "This is to certify that:",
        "{$student['first_name']} {$student['middle_name']} {$student['last_name']}",
        "Student Number: {$student['student_number']}",
        "Program: {$student['program_course']}",
        "",
        "has maintained good moral character throughout their enrollment",
        "at Bestlink College of the Philippines.",
        "",
        "Issued this " . date('jS \d\a\y \o\f F Y'),
        "",
        "Registrar",
        "Bestlink College of the Philippines",
    ];
    
    $pdfPath = regGenerateBasicPdf("gmc-{$student['student_number']}", $content);
    if (!$pdfPath) {
        return ['success' => false, 'error' => 'Cannot generate PDF'];
    }
    
    try {
        $stmt = $db->prepare("INSERT INTO `reg_files` 
            (`student_id`, `category`, `original_name`, `stored_name`, `mime`, `size`, `sha256_hash`, `uploaded_by`, `status`) 
            VALUES (?, 'documents', ?, ?, 'application/pdf', ?, ?, ?, 'Active')");
        
        $filename = basename($pdfPath);
        $filesize = filesize($pdfPath);
        $hash = hash_file('sha256', $pdfPath);
        
        $stmt->execute([$studentId, "Good Moral Certificate - $docNo", $filename, $filesize, $hash, 1]);
        $fileId = (int)$db->lastInsertId();
    } catch (Throwable) {
        return ['success' => false, 'error' => 'Cannot store PDF in database'];
    }
    
    return ['success' => true, 'pdf_path' => $pdfPath, 'file_id' => $fileId, 'doc_no' => $docNo];
}

/**
 * Generate generic Certification PDF (for any certification type)
 */
function regGenerateCertification(int $studentId, string $certType = 'Certification', array $details = []): array
{
    global $db;
    
    $stmt = $db->prepare("SELECT * FROM `reg_students` WHERE `id` = ?");
    $stmt->execute([$studentId]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$student) {
        return ['success' => false, 'error' => 'Student not found'];
    }
    
    $docNo = regGenerateDocumentNumber('CERT');
    $content = [
        "BESTLINK COLLEGE OF THE PHILIPPINES",
        $certType,
        "",
        "Document No: $docNo",
        "Issue Date: " . date('F d, Y'),
        "",
        "Student: {$student['first_name']} {$student['middle_name']} {$student['last_name']}",
        "Student No: {$student['student_number']}",
        "Program: {$student['program_course']}",
        "",
    ];
    
    if (!empty($details)) {
        foreach ($details as $key => $value) {
            $content[] = "$key: $value";
        }
        $content[] = "";
    }
    
    $content[] = "Issued this " . date('jS \d\a\y \o\f F Y');
    $content[] = "";
    $content[] = "Registrar";
    $content[] = "Bestlink College of the Philippines";
    
    $pdfPath = regGenerateBasicPdf("cert-{$student['student_number']}", $content);
    if (!$pdfPath) {
        return ['success' => false, 'error' => 'Cannot generate PDF'];
    }
    
    try {
        $stmt = $db->prepare("INSERT INTO `reg_files` 
            (`student_id`, `category`, `original_name`, `stored_name`, `mime`, `size`, `sha256_hash`, `uploaded_by`, `status`) 
            VALUES (?, 'documents', ?, ?, 'application/pdf', ?, ?, ?, 'Active')");
        
        $filename = basename($pdfPath);
        $filesize = filesize($pdfPath);
        $hash = hash_file('sha256', $pdfPath);
        
        $stmt->execute([$studentId, "$certType - $docNo", $filename, $filesize, $hash, 1]);
        $fileId = (int)$db->lastInsertId();
    } catch (Throwable) {
        return ['success' => false, 'error' => 'Cannot store PDF in database'];
    }
    
    return ['success' => true, 'pdf_path' => $pdfPath, 'file_id' => $fileId, 'doc_no' => $docNo];
}
?>
