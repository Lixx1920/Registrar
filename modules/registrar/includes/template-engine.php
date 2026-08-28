<?php
/**
 * SMS2 - Registrar Template Engine
 * HTML template-based document generation with student data auto-fill
 */
declare(strict_types=1);

if (!defined('ROOT_PATH')) {
    require_once dirname(__DIR__, 3) . '/config/config.php';
}

// Template directory
define('REG_TEMPLATE_DIR', dirname(__DIR__) . '/DocuFormat');
define('REG_SEAL_PATH', dirname(__DIR__) . '/sealstamp/Seal-Display.png');
define('REG_LOGO_PATH', ROOT_PATH . '/images/bestlink.png');

/**
 * Load and parse HTML template with student data
 *
 * @param string $templateName Template filename (e.g., 'good-moral.html')
 * @param array $studentData Student information from database
 * @param array $options Additional options (verification_code, etc.)
 * @return string Rendered HTML with student data
 */
function regLoadTemplate(string $templateName, array $studentData, array $options = []): string
{
    $templatePath = REG_TEMPLATE_DIR . '/' . $templateName;

    if (!file_exists($templatePath)) {
        return '';
    }

    $html = file_get_contents($templatePath);
    if ($html === false) {
        return '';
    }

    // Get base64 images
    $logoBase64 = regGetBase64Image(REG_LOGO_PATH);
    $sealBase64 = regGetBase64Image(REG_SEAL_PATH);

    // Replace logo and seal src attributes properly
    if (!empty($logoBase64)) {
        $html = preg_replace('/src=["\']([^"\']*bestlink-logo\.png)["\']/', 'src="' . $logoBase64 . '"', $html);
    }

    if (!empty($sealBase64)) {
        // Replace seal image sources with base64 data
        $html = preg_replace('/src=["\']([^"\']*bestlink-seal\.png|[^"\']*Seal-Display\.png)["\']/', 'src="' . $sealBase64 . '"', $html);
        
        // Also handle case variations
        $html = preg_replace('/src=["\']([^"\']*Seal-display\.png|[^"\']*seal-display\.png)["\']/', 'src="' . $sealBase64 . '"', $html);

        // Add seal display at bottom left if not already present in the template
        if (strpos($html, 'class="school-seal"') === false && strpos($html, 'class="seal-stamp"') === false) {
            // Inject seal CSS and element - using absolute positioning for better Dompdf compatibility
            $sealCss = '
            .document {
                position: relative;
            }
            .seal-stamp {
                position: absolute;
                bottom: 15mm;
                left: 15mm;
                width: 60mm;
                height: 60mm;
                z-index: 0;
                opacity: 0.15;
                pointer-events: none;
            }
            .seal-stamp img {
                width: 100%;
                height: 100%;
                object-fit: contain;
            }
            ';

            $sealHtml = '<div class="seal-stamp"><img src="' . $sealBase64 . '" alt="Official Seal"></div>';

            // Add seal CSS to style tag (before closing style tag)
            $html = preg_replace('/<\/style>/', $sealCss . '</style>', $html, 1);

            // Add seal element inside document div (before closing document div)
            // Try to find closing </div> for .document class
            if (preg_match('/<div[^>]*class=["\'][^"\']*document[^"\']*["\'][^>]*>/', $html)) {
                // Find the last </div> before </body> which should be the document div
                $html = preg_replace('/(<\/div>\s*<\/body>)/', $sealHtml . '$1', $html, 1);
            } else {
                // Fallback: add before closing body tag
                $html = preg_replace('/<\/body>/', $sealHtml . '</body>', $html, 1);
            }
        }
    }

    // Fix CSS for Dompdf compatibility
    // Dompdf has issues with complex padding and min-height, simplify it
    $html = preg_replace(
        '/(\.document\s*\{[^}]*width:\s*)210mm/i',
        '$1100%',
        $html
    );
    $html = preg_replace(
        '/(\.document\s*\{[^}]*min-height:\s*)297mm/i',
        '$1auto',
        $html
    );

    // Format student name (LAST, FIRST MIDDLE)
    $fullName = strtoupper(trim(
        ($studentData['last_name'] ?? '') . ', ' .
        ($studentData['first_name'] ?? '') . ' ' .
        ($studentData['middle_name'] ?? '')
    ));

    // Get current academic year
    $currentYear = date('Y');
    $academicYear = $currentYear . '-' . ($currentYear + 1);

    // Format enrollment date
    $enrollmentDate = isset($studentData['enrollment_date']) ? date('F d, Y', strtotime($studentData['enrollment_date'])) : date('F d, Y');

    // Get year level from year_section (e.g., "4-A" -> "4th Year")
    $yearLevel = regGetYearLevel($studentData['year_section'] ?? '');

    // Get department/college
    $department = $studentData['college_department'] ?? '';
    
    // Get major
    $major = $studentData['major'] ?? $department ?? '';

    // Prepare replacement data - using both exact matches and template placeholders
    $replacements = [
        // Template placeholders (these will always match)
        '{{REGISTRATION_DATE}}' => $enrollmentDate,
        '{{DEPARTMENT}}' => $department,
        '{{MAJOR}}' => $major,
        '{{DATE}}' => date('F d, Y'),
        '{{CURRENT_DATE}}' => date('F d, Y'),
        '{{VERIFICATION_CODE}}' => $options['verification_code'] ?? 'PENDING',
        '{{DOC_NUMBER}}' => $options['doc_number'] ?? 'N/A',
        
        // Sample data replacements (for backward compatibility)
        'DOMINGO, CHARLENE BUENDIA' => $fullName,
        '2021-00123' => $studentData['student_number'] ?? 'N/A',
        'BACHELOR OF SCIENCE IN COMPUTER SCIENCE' => strtoupper($studentData['program_course'] ?? 'N/A'),
        'Bachelor of Science in Computer Science' => $studentData['program_course'] ?? 'N/A',
        '4th Year' => $yearLevel,
        '2026-2027' => $academicYear,
    ];

    // Replace placeholders
    foreach ($replacements as $search => $replace) {
        $html = str_replace($search, (string)$replace, $html);
    }

    return $html;
}

/**
 * Convert image to base64 data URI for embedding in HTML
 */
function regGetBase64Image(string $imagePath): string
{
    if (!file_exists($imagePath)) {
        return '';
    }

    $imageData = file_get_contents($imagePath);
    if ($imageData === false) {
        return '';
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $imagePath);
    finfo_close($finfo);

    $base64 = base64_encode($imageData);
    return "data:$mimeType;base64,$base64";
}

/**
 * Extract year level from year_section (e.g., "4-A" -> "4th Year")
 */
function regGetYearLevel(string $yearSection): string
{
    if (empty($yearSection)) {
        return 'N/A';
    }

    $year = (int) substr($yearSection, 0, 1);

    $suffix = match($year) {
        1 => '1st',
        2 => '2nd',
        3 => '3rd',
        default => $year . 'th'
    };

    return $suffix . ' Year';
}

/**
 * Render HTML template to PDF using Dompdf
 */
function regRenderTemplateToPdf(string $html, string $outputFilename): ?string
{
    if (!defined('ROOT_PATH')) {
        require_once dirname(__DIR__, 3) . '/config/config.php';
    }

    $pdfDir = ROOT_PATH . '/storage/uploads/registrar/generated';
    if (!is_dir($pdfDir)) {
        mkdir($pdfDir, 0700, true);
    }

    $pdfPath = $pdfDir . '/' . $outputFilename . '_' . time() . '.pdf';

    // DEBUG: Save the HTML being sent to Dompdf
    $debugHtmlPath = $pdfDir . '/' . $outputFilename . '_DEBUG.html';
    file_put_contents($debugHtmlPath, $html);

    // Try to use Dompdf
    $autoloadPath = ROOT_PATH . '/vendor/autoload.php';
    if (file_exists($autoloadPath)) {
        require_once $autoloadPath;

        try {
            // Clean HTML: remove excessive blank lines and normalize whitespace
            $html = preg_replace('/^\s+$/m', '', $html); // Remove lines with only whitespace
            $html = preg_replace('/\n{3,}/', "\n\n", $html); // Max 2 consecutive newlines

            $options = new \Dompdf\Options();
            $options->set('isRemoteEnabled', false);
            $options->set('isHtml5ParserEnabled', false); // Disable HTML5 parser for compatibility
            $options->set('isFontSubsettingEnabled', true);
            $options->set('defaultFont', 'Arial');
            $options->set('debugCss', false);
            $options->set('debugLayout', false);
            $options->set('debugLayoutLines', false);
            $options->set('debugLayoutBlocks', false);
            $options->set('debugLayoutInline', false);
            $options->set('debugLayoutPaddingBox', false);

            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            file_put_contents($pdfPath, $dompdf->output());
            chmod($pdfPath, 0600);

            return $pdfPath;
        } catch (Throwable $e) {
            error_log('Dompdf error: ' . $e->getMessage());
            error_log('Dompdf trace: ' . $e->getTraceAsString());
            // Fall through to HTML fallback
        }
    }

    // Fallback: Save as HTML for browser print-to-PDF
    $htmlPath = $pdfDir . '/' . $outputFilename . '_' . time() . '.html';

    $printableHtml = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @media print {
            body { margin: 0; }
            @page { size: A4; margin: 0; }
        }
    </style>
</head>
<body>
' . $html . '
</body>
</html>';

    if (file_put_contents($htmlPath, $printableHtml) === false) {
        return null;
    }

    chmod($htmlPath, 0600);
    return $htmlPath;
}

/**
 * Generate document from HTML template
 *
 * @param int $studentId Student ID
 * @param string $templateName Template filename
 * @param string $docType Document type label
 * @param array $options Additional options
 * @return array ['success' => bool, 'pdf_path' => string, 'file_id' => int, 'doc_no' => string] or error
 */
function regGenerateFromTemplate(
    int $studentId,
    string $templateName,
    string $docType,
    array $options = []
): array {
    $db = db();

    // Fetch student data
    $stmt = $db->prepare("SELECT * FROM `reg_students` WHERE `id` = ?");
    $stmt->execute([$studentId]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        return ['success' => false, 'error' => 'Student not found'];
    }

    // Generate document number
    $counterKey = strtoupper(str_replace([' ', '-'], '_', $docType));
    $docNo = regGenerateDocumentNumber($counterKey);
    $options['doc_number'] = $docNo;

    // Load and render template
    $html = regLoadTemplate($templateName, $student, $options);

    if (empty($html)) {
        return ['success' => false, 'error' => 'Template not found or empty'];
    }

    // Convert to PDF
    $filename = strtolower(str_replace([' ', '-'], '_', $docType)) . '-' . $student['student_number'];
    $pdfPath = regRenderTemplateToPdf($html, $filename);

    if (!$pdfPath) {
        return ['success' => false, 'error' => 'Failed to generate PDF'];
    }

    // Store in database
    try {
        // Detect mime type based on file extension
        $mimeType = (strpos($pdfPath, '.pdf') !== false) ? 'application/pdf' : 'text/html';

        $stmt = $db->prepare("INSERT INTO `reg_files`
            (`student_id`, `category`, `original_name`, `stored_name`, `mime`, `size`, `sha256_hash`, `uploaded_by`, `status`)
            VALUES (?, 'documents', ?, ?, ?, ?, ?, ?, 'Active')");

        $filename = basename($pdfPath);
        $filesize = filesize($pdfPath);
        $hash = hash_file('sha256', $pdfPath);

        $stmt->execute([$studentId, "$docType - $docNo", $filename, $mimeType, $filesize, $hash, 1]);
        $fileId = (int)$db->lastInsertId();
    } catch (Throwable $e) {
        return ['success' => false, 'error' => 'Cannot store PDF in database: ' . $e->getMessage()];
    }

    return ['success' => true, 'pdf_path' => $pdfPath, 'file_id' => $fileId, 'doc_no' => $docNo];
}
?>
