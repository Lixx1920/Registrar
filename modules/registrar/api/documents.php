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
            $userId
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

        if (empty($data['item_id']) || empty($data['doc_type'])) {
            regApiJson(['success' => false, 'error' => 'Missing item_id or doc_type'], 400);
        }

        // Ensure RSA keys exist before signing
        regInitializeKeys();

        $result = regGenerateRequestDocument((int) $data['item_id'], (string) $data['doc_type'], $userId);

        if (!$result['success']) {
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