<?php
/**
 * SMS2 - Registrar API: Document Requests
 * Create, list, update status, generate signed PDFs, release, verify.
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/api-helpers.php';
require_once __DIR__ . '/../includes/registrar-service.php';
require_once __DIR__ . '/../includes/signing-service.php';

regApiHandle([
    'create' => function () {
        regApiRequireAccess();
        regApiRequireCsrf();

        $data = regApiBody();
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

        if (empty($data['student_id']) || empty($data['doc_types'])) {
            regApiJson(['success' => false, 'error' => 'Missing student_id or doc_types'], 400);
        }

        $result = regCreateDocumentRequest(
            (int) $data['student_id'],
            (array) $data['doc_types'],
            (string) ($data['purpose'] ?? 'Document request'),
            (string) ($data['channel'] ?? 'walk-in'),
            $userId,
            (int) ($data['paid'] ?? 0),
            isset($data['payment_ref']) ? (string) $data['payment_ref'] : null,
            isset($data['student_email']) ? (string) $data['student_email'] : null
        );

        if (!$result['success']) {
            regApiJson(['success' => false, 'error' => $result['error']], 400);
        }

        regApiLog('create_request', 'Created request ' . $result['request_no']);

        regApiJson([
            'success' => true,
            'message' => 'Document request created',
            'request_id' => $result['request_id'],
            'request_no' => $result['request_no'],
        ]);
    },

    'get' => function () {
        $requestId = (int) regApiGet('id', '0');

        if ($requestId === 0) {
            regApiJson(['success' => false, 'error' => 'Missing request ID'], 400);
        }

        $db = db();

        // Get request with student info
        $stmt = $db->prepare("
            SELECT dr.*, s.student_number, s.first_name, s.last_name, s.program_course
            FROM reg_doc_requests dr
            INNER JOIN reg_students s ON s.id = dr.student_id
            WHERE dr.id = ?
        ");
        $stmt->execute([$requestId]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$request) {
            regApiJson(['success' => false, 'error' => 'Request not found'], 404);
        }

        // Get request items
        $stmt = $db->prepare("
            SELECT * FROM reg_doc_request_items WHERE request_id = ? ORDER BY id
        ");
        $stmt->execute([$requestId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        regApiJson([
            'success' => true,
            'data' => $request,
            'items' => $items,
        ]);
    },

    'list' => function () {
        $status = (string) regApiGet('status', 'Submitted');
        $limit = min(100, max(1, (int) regApiGet('limit', '50')));

        $queue = regGetRequestQueue($status, $limit);

        regApiJson(['success' => true, 'data' => $queue]);
    },

    'update_status' => function () {
        regApiRequireAccess();
        regApiRequireCsrf();

        $data = regApiBody();
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

        if (empty($data['request_id']) || empty($data['status'])) {
            regApiJson(['success' => false, 'error' => 'Missing request_id or status'], 400);
        }

        $result = regUpdateRequestStatus((int) $data['request_id'], (string) $data['status'], $userId);

        if (!$result['success']) {
            regApiJson(['success' => false, 'error' => $result['error']], 400);
        }

        regApiLog('update_request_status', 'Request ' . $data['request_id'] . ' -> ' . $data['status']);
        regApiJson(['success' => true, 'message' => 'Request status updated']);
    },

    'generate' => function () {
        regApiRequireAccess();
        regApiRequireCsrf();

        $data = regApiBody();
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

        // Debug logging
        error_log('Generate API called - item_id: ' . ($data['item_id'] ?? 'missing') . ', doc_type: ' . ($data['doc_type'] ?? 'missing'));

        if (empty($data['item_id']) || empty($data['doc_type'])) {
            error_log('Generate API validation failed - missing fields');
            regApiJson(['success' => false, 'error' => 'Missing item_id or doc_type', 'received' => $data], 400);
        }

        // Ensure RSA keys exist before signing
        regInitializeKeys();

        $result = regGenerateRequestDocument((int) $data['item_id'], (string) $data['doc_type'], $userId);

        if (!$result['success']) {
            error_log('Generate document failed: ' . ($result['error'] ?? 'unknown'));
            regApiJson(['success' => false, 'error' => $result['error']], 400);
        }

        regApiLog('generate_document', 'Generated ' . $data['doc_type'] . ' ' . $result['doc_no']);

        regApiJson([
            'success' => true,
            'message' => 'Document generated',
            'verification_code' => $result['verification_code'],
            'doc_no' => $result['doc_no'],
        ]);
    },

    'serve_preview' => function () {
        regApiRequireAccess();
        
        $itemId = (int) regApiGet('item_id', '0');
        $docType = (string) regApiGet('doc_type', '');
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

        if ($itemId === 0 || $docType === '') {
            echo "<div style='padding: 20px; color: red;'>Missing item_id or doc_type</div>";
            exit;
        }

        $result = regPreviewRequestDocument($itemId, $docType, $userId);

        if (!$result['success']) {
            echo "<div style='padding: 20px; color: red;'>Error: " . htmlspecialchars($result['error']) . "</div>";
            exit;
        }

        $pdfPath = $result['pdf_path'] ?? '';
        if (empty($pdfPath) || !file_exists($pdfPath)) {
            echo "<div style='padding: 20px; color: red;'>Generated file not found.</div>";
            exit;
        }

        $mime = (strpos($pdfPath, '.pdf') !== false) ? 'application/pdf' : 'text/html';
        header('Content-Type: ' . $mime);
        
        readfile($pdfPath);
        @unlink($pdfPath);
        exit;
    },

    'notify_student' => function () {
        regApiRequireAccess();
        regApiRequireCsrf();

        $data = regApiBody();
        $itemId = (int) ($data['item_id'] ?? 0);
        $actionType = (string) ($data['action_type'] ?? '');
        
        if ($itemId === 0 || !in_array($actionType, ['email', 'notify'])) {
            regApiJson(['success' => false, 'error' => 'Invalid parameters'], 400);
        }

        $db = db();
        
        $stmt = $db->prepare("
            SELECT i.*, r.request_no, r.channel, r.student_id as req_student_id, 
                   r.student_email, s.first_name, s.last_name,
                   f.stored_name, f.original_name, f.mime
            FROM reg_doc_request_items i
            JOIN reg_doc_requests r ON i.request_id = r.id
            LEFT JOIN reg_students s ON (r.student_id = s.id)
            LEFT JOIN reg_files f ON i.generated_file_id = f.id
            WHERE i.id = ?
        ");
        $stmt->execute([$itemId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            regApiJson(['success' => false, 'error' => 'Request item not found'], 404);
        }
        
        if (empty($item['student_email'])) {
            regApiJson(['success' => false, 'error' => 'Student does not have an email address on file'], 400);
        }

        require_once ROOT_PATH . '/includes/mail.php';
        
        $studentName = $item['first_name'] . ' ' . $item['last_name'];
        $subject = '';
        $body = '';
        $attachments = [];
        
        if ($actionType === 'email') {
            if (empty($item['stored_name'])) {
                regApiJson(['success' => false, 'error' => 'Generated document not found'], 400);
            }
            $filePath = ROOT_PATH . '/storage/uploads/registrar/generated/' . $item['stored_name'];
            if (!file_exists($filePath)) {
                regApiJson(['success' => false, 'error' => 'Generated document file is missing'], 400);
            }
            
            $portalUrl = rtrim(smsSetting('app_url', 'http://localhost/SMS2'), '/') . '/modules/student-portal/student-portal-page.php?step=select';
            $subject = 'Your Requested Document is Ready: ' . $item['doc_type'];
            $body = "Dear $studentName,\n\nYour requested document (" . $item['doc_type'] . ") has been successfully generated and is attached to this email.\n\nYou can also view and download your digital copy from your Student Portal:\n$portalUrl\n\nThank you,\nOffice of the Registrar";
            $attachments[] = $filePath;
            
        } else {
            $subject = 'Your Requested Document is Ready for Pickup: ' . $item['doc_type'];
            $body = "Dear $studentName,\n\nYour requested document (" . $item['doc_type'] . ") is now ready for pickup at the Registrar's Office.\n\nYour Request Reference Number is: " . $item['request_no'] . "\n\nPlease bring a valid ID when claiming your document.\n\nThank you,\nOffice of the Registrar";
        }
        
        $mailResult = smsSendMail($item['student_email'], $subject, str_replace("\n", "<br>", $body), $body, $attachments);
        
        if (!$mailResult['ok']) {
            regApiJson(['success' => false, 'error' => 'Failed to send email. Please check your SMTP configuration. Error: ' . $mailResult['error']], 500);
        }
        
        // Update item status to Released (since it was delivered via email or notified)
        $updateStmt = $db->prepare("UPDATE reg_doc_request_items SET status = 'Released' WHERE id = ?");
        $updateStmt->execute([$itemId]);
        
        // Update parent request status
        $updateReqStmt = $db->prepare("UPDATE reg_doc_requests SET status = 'Released' WHERE id = ?");
        $updateReqStmt->execute([$item['request_id']]);
        
        regApiLog('notify_student', "Sent $actionType for item #$itemId to {$item['student_email']}");

        regApiJson([
            'success' => true,
            'message' => 'Notification sent successfully'
        ]);
    },

    'release' => function () {
        regApiRequireAccess();
        regApiRequireCsrf();

        $data = regApiBody();
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

        if (empty($data['item_id']) || empty($data['claimant_name'])) {
            regApiJson(['success' => false, 'error' => 'Missing required fields'], 400);
        }

        $result = regReleaseDocument(
            (int) $data['item_id'],
            (string) $data['claimant_name'],
            isset($data['claimant_id']) ? (string) $data['claimant_id'] : null,
            $userId
        );

        if (!$result['success']) {
            regApiJson(['success' => false, 'error' => $result['error']], 400);
        }

        regApiLog('release_document', 'Released slip ' . $result['release_slip_no']);
        regApiJson([
            'success' => true,
            'message' => 'Document released',
            'release_slip_no' => $result['release_slip_no'],
        ]);
    },

    'verify' => function () {
        $code = (string) regApiGet('code', '');
        $hash = (string) regApiGet('hash', '');

        if ($code === '' || $hash === '') {
            regApiJson(['success' => false, 'error' => 'Missing code or hash'], 400);
        }

        $result = regVerifyDocumentByCode($code, $hash);

        regApiJson([
            'success' => $result['valid'] ?? false,
            'valid' => $result['valid'] ?? false,
            'reason' => $result['reason'] ?? null,
            'document' => $result['document'] ?? null,
        ]);
    },

    'stats' => function () {
        $stats = regGetDashboardStats();
        regApiJson(['success' => true, 'data' => $stats]);
    },
]);