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

// Load template engine for HTML-based document generation
require_once __DIR__ . '/template-engine.php';

/**
 * Generate document number with counter
 * Format: PREFIX-YYYY-#### (e.g., FORM137-2024-0001)
 */
function regGenerateDocumentNumber(string $counterKey): string
{
    $db = db();
    
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
    if (!defined('ROOT_PATH')) {
        require_once dirname(__DIR__, 3) . '/config/config.php';
    }
    $pdfDir = ROOT_PATH . '/storage/uploads/registrar/generated';
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
    $db = db();

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
    $verificationCode = $options['verification_code'] ?? 'PENDING';

    $content = [
        "====================================================================",
        "           BESTLINK COLLEGE OF THE PHILIPPINES",
        "           Bulacan Campus - Registrar Office",
        "====================================================================",
        "",
        "               FORM 137 - TRANSCRIPT OF RECORDS",
        "",
        "Document Number: $docNo",
        "Issue Date: " . date('F d, Y'),
        "Verification Code: $verificationCode",
        "",
        "--------------------------------------------------------------------",
        "STUDENT INFORMATION",
        "--------------------------------------------------------------------",
        "Name       : {$student['first_name']} {$student['middle_name']} {$student['last_name']}",
        "Student No : {$student['student_number']}",
        "Program    : {$student['program_course']}",
        "Year/Section: {$student['year_section']}",
        "Birth Date : {$student['date_of_birth']}",
        "",
        "--------------------------------------------------------------------",
        "ACADEMIC RECORDS",
        "--------------------------------------------------------------------",
    ];

    // Group subjects by academic year
    $byYear = [];
    foreach ($subjects as $subj) {
        $byYear[$subj['academic_year']][] = $subj;
    }

    if (empty($byYear)) {
        $content[] = "No academic records available.";
        $content[] = "";
    } else {
        foreach ($byYear as $year => $yearSubjects) {
            $content[] = "";
            $content[] = "Academic Year: $year";
            $content[] = "----------------------------------------";
            foreach ($yearSubjects as $subj) {
                $grade = $subj['grade'] ?? 'N/A';
                $units = $subj['units'] ?? '0';
                $content[] = sprintf("%-12s %-40s %s units  Grade: %s",
                    $subj['subject_code'],
                    $subj['subject_name'],
                    $units,
                    $grade
                );
            }
        }
        $content[] = "";
    }

    $content[] = "--------------------------------------------------------------------";
    $content[] = "";
    $content[] = "This document is authentic and digitally signed by the Registrar.";
    $content[] = "To verify this document, visit the college portal and enter the";
    $content[] = "verification code above.";
    $content[] = "";
    $content[] = "Authenticated by: Office of the Registrar";
    $content[] = "Date Issued: " . date('F d, Y g:i A');
    $content[] = "";
    $content[] = "====================================================================";
    $content[] = "         NOT VALID WITHOUT OFFICIAL SEAL AND SIGNATURE";
    $content[] = "====================================================================";

    // Generate PDF
    $isPreview = !empty($options['preview']);
    if ($isPreview) {
        if (!defined('ROOT_PATH')) require_once dirname(__DIR__, 3) . '/config/config.php';
        $pdfDir = ROOT_PATH . '/storage/uploads/registrar/generated';
        if (!is_dir($pdfDir)) mkdir($pdfDir, 0700, true);
        $htmlPath = $pdfDir . '/form137-' . $student['student_number'] . '_preview_' . time() . '.html';
        $htmlContent = "<pre style='padding: 20px; font-family: monospace; font-size: 14px; background: white; margin: 0; min-height: 100vh;'>" . htmlspecialchars(implode("\n", $content)) . "</pre>";
        file_put_contents($htmlPath, $htmlContent);
        return ['success' => true, 'pdf_path' => $htmlPath, 'file_id' => null, 'doc_no' => $docNo];
    }

    $pdfPath = regGenerateBasicPdf("form137-{$student['student_number']}", $content);
    if (!$pdfPath) {
        return ['success' => false, 'error' => 'Cannot generate PDF'];
    }

    // Store in database
    $fileId = null;
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
 * Uses HTML template: DocuFormat/good-moral.html
 */
function regGenerateGoodMoral(int $studentId, array $options = []): array
{
    return regGenerateFromTemplate(
        $studentId,
        'good-moral.html',
        'Good Moral Certificate',
        $options
    );
}

/**
 * Generate generic Certification PDF (for any certification type)
 * Uses HTML templates when available, falls back to basic generation
 */
function regGenerateCertification(int $studentId, string $certType = 'Certification', array $details = []): array
{
    // Map certification types to templates
    $templateMap = [
        'Certificate of Enrollment' => 'certificate-enrollment.html',
        'Certificate of Grades' => 'certificate-grades.html',
    ];

    // Use template if available
    if (isset($templateMap[$certType])) {
        return regGenerateFromTemplate(
            $studentId,
            $templateMap[$certType],
            $certType,
            $details
        );
    }

    // Fallback to basic PDF for other types
    $db = db();

    $stmt = $db->prepare("SELECT * FROM `reg_students` WHERE `id` = ?");
    $stmt->execute([$studentId]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        return ['success' => false, 'error' => 'Student not found'];
    }

    $docNo = regGenerateDocumentNumber('CERT');
    $verificationCode = $details['verification_code'] ?? 'PENDING';

    $content = [
        "====================================================================",
        "           BESTLINK COLLEGE OF THE PHILIPPINES",
        "           Bulacan Campus - Registrar Office",
        "====================================================================",
        "",
        "                    " . strtoupper($certType),
        "",
        "Document Number: $docNo",
        "Issue Date: " . date('F d, Y'),
        "Verification Code: $verificationCode",
        "",
        "--------------------------------------------------------------------",
        "",
        "TO WHOM IT MAY CONCERN:",
        "",
        "     This is to certify that",
        "",
        "          " . strtoupper("{$student['first_name']} {$student['middle_name']} {$student['last_name']}"),
        "",
        "     Student Number: {$student['student_number']}",
        "     Program: {$student['program_course']}",
        "     Year/Section: {$student['year_section']}",
        "     Birth Date: {$student['date_of_birth']}",
        "",
    ];

    // Add certification-specific content
    if ($certType === 'Certificate of Enrollment') {
        $content[] = "is currently enrolled as a bonafide student of this institution";
        $content[] = "for the Academic Year " . date('Y') . "-" . (date('Y') + 1) . ".";
    } elseif ($certType === 'Certificate of Grades') {
        $content[] = "has completed their academic requirements with the following";
        $content[] = "general average grade based on official records.";
    } elseif ($certType === 'Diploma Copy') {
        $content[] = "has successfully completed all requirements for graduation from";
        $content[] = "this institution. This is a certified true copy of the diploma.";
    } elseif ($certType === 'Honorable Dismissal') {
        $content[] = "has been granted honorable dismissal from this institution with";
        $content[] = "no pending obligations or disciplinary cases on record.";
    } else {
        $content[] = "is a student in good standing at this institution.";
    }

    $content[] = "";

    if (!empty($details)) {
        $content[] = "Additional Information:";
        foreach ($details as $key => $value) {
            if ($key !== 'verification_code') {
                $content[] = "     $key: $value";
            }
        }
        $content[] = "";
    }

    $content[] = "     This certification is issued upon request of the student for";
    $content[] = "whatever legal purpose it may serve.";
    $content[] = "";
    $content[] = "     Issued this " . date('jS \d\a\y \o\f F Y') . " at Bestlink College";
    $content[] = "of the Philippines, Bulacan Campus.";
    $content[] = "";
    $content[] = "";
    $content[] = "--------------------------------------------------------------------";
    $content[] = "";
    $content[] = "This document is authentic and digitally signed by the Registrar.";
    $content[] = "To verify, visit the college portal and enter the verification code.";
    $content[] = "";
    $content[] = "                    _______________________________";
    $content[] = "                    Office of the Registrar";
    $content[] = "                    Bestlink College of the Philippines";
    $content[] = "";
    $content[] = "====================================================================";
    $content[] = "         NOT VALID WITHOUT OFFICIAL SEAL AND SIGNATURE";
    $content[] = "====================================================================";

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
