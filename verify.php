<?php
/**
 * SMS2 - Document Verification System
 * Validates document authenticity via QR code scan and displays masked student data.
 */
declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$token = $_GET['token'] ?? '';
$isValid = false;
$documentData = null;
$maskedName = '';
$maskedNumber = '';
$dateIssued = '';

if (!empty($token)) {
    $db = db();
    
    // We use the sha256_hash from reg_files as our secure verification token
    $stmt = $db->prepare("
        SELECT 
            f.category AS document_type, 
            f.created_at AS issue_date,
            s.first_name, 
            s.last_name, 
            s.student_number 
        FROM reg_files f
        JOIN reg_students s ON f.student_id = s.id
        WHERE f.verification_token = ? AND f.status = 'Active'
    ");
    $stmt->execute([$token]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $isValid = true;
        $documentData = $result;

        // Masking logic for Privacy
        // e.g., "John" -> "J***"
        $maskString = function($str) {
            $str = trim((string)$str);
            if (empty($str)) return '';
            if (strlen($str) <= 2) return $str . '*';
            return substr($str, 0, 1) . str_repeat('*', strlen($str) - 2) . substr($str, -1);
        };

        // Mask Name
        $fNameMasked = $maskString($result['first_name']);
        $lNameMasked = $maskString($result['last_name']);
        $maskedName = strtoupper($fNameMasked . ' ' . $lNameMasked);

        // Mask Student Number (e.g., 2021-12345 -> 2021-***45)
        $sNum = (string)$result['student_number'];
        if (strlen($sNum) > 6) {
            $maskedNumber = substr($sNum, 0, 5) . str_repeat('*', strlen($sNum) - 7) . substr($sNum, -2);
        } else {
            $maskedNumber = $maskString($sNum);
        }

        $dateIssued = date('F d, Y', strtotime($result['issue_date']));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Verification | BCP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0047AB;
            --primary-light: #e6f0fa;
            --success: #10B981;
            --success-light: #D1FAE5;
            --error: #EF4444;
            --error-light: #FEE2E2;
            --text-main: #1F2937;
            --text-muted: #6B7280;
            --bg-color: #F3F4F6;
            --card-bg: #FFFFFF;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .verification-container {
            background-color: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 480px;
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header {
            background-color: var(--primary);
            padding: 30px 20px;
            text-align: center;
            color: white;
            position: relative;
        }

        .logo {
            width: 80px;
            height: 80px;
            background-color: white;
            border-radius: 50%;
            padding: 10px;
            margin: 0 auto 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .header h1 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 0.875rem;
            opacity: 0.9;
        }

        .content {
            padding: 30px;
            text-align: center;
        }

        .status-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
        }

        .status-valid {
            background-color: var(--success-light);
            color: var(--success);
            box-shadow: 0 0 0 10px rgba(16, 185, 129, 0.1);
        }

        .status-invalid {
            background-color: var(--error-light);
            color: var(--error);
            box-shadow: 0 0 0 10px rgba(239, 68, 68, 0.1);
        }

        .status-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .status-valid-text {
            color: var(--success);
        }

        .status-invalid-text {
            color: var(--error);
        }

        .status-desc {
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.5;
            margin-bottom: 25px;
        }

        .data-card {
            background-color: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            padding: 20px;
            text-align: left;
        }

        .data-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #E5E7EB;
        }

        .data-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .data-row:first-child {
            padding-top: 0;
        }

        .data-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .data-value {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-main);
            text-align: right;
            max-width: 60%;
        }

        .privacy-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .privacy-note svg {
            width: 14px;
            height: 14px;
        }
    </style>
</head>
<body>

    <div class="verification-container">
        <div class="header">
            <div class="logo">
                <img src="images/bestlink.png" alt="BCP Logo" onerror="this.src='modules/registrar/sealstamp/Seal-Display.png'">
            </div>
            <h1>Bestlink College of the Philippines</h1>
            <p>Official Document Verification</p>
        </div>

        <div class="content">
            <?php if (empty($token)): ?>
                <div class="status-icon status-invalid">
                    &#9888;
                </div>
                <h2 class="status-title status-invalid-text">Invalid Request</h2>
                <p class="status-desc">No verification token was provided. Please scan the QR code directly from the document.</p>
            
            <?php elseif ($isValid): ?>
                <div class="status-icon status-valid">
                    &#10003;
                </div>
                <h2 class="status-title status-valid-text">Document Authentic</h2>
                <p class="status-desc">This document is Authentic and was officially generated via BCP SCHOOL.</p>

                <div class="data-card">
                    <div class="data-row">
                        <span class="data-label">Document Type</span>
                        <span class="data-value"><?= htmlspecialchars(strtoupper($documentData['document_type'])) ?></span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">Issue Date</span>
                        <span class="data-value"><?= $dateIssued ?></span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">Student Name</span>
                        <span class="data-value"><?= htmlspecialchars($maskedName) ?></span>
                    </div>
                    <div class="data-row">
                        <span class="data-label">Student No.</span>
                        <span class="data-value"><?= htmlspecialchars($maskedNumber) ?></span>
                    </div>
                </div>

                <div class="privacy-note">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <span>For privacy, student details are masked.</span>
                </div>

            <?php else: ?>
                <div class="status-icon status-invalid">
                    &#10007;
                </div>
                <h2 class="status-title status-invalid-text">Authentication Failed</h2>
                <p class="status-desc">We could not find a record of this document in our system. The document may be invalid or forged.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
