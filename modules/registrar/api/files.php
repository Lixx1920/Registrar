<?php
/**
 * SMS2 - Registrar API: File Operations
 * Upload, list, verify integrity (SHA-256), and soft-delete student files.
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/api-helpers.php';
require_once __DIR__ . '/../includes/storage-service.php';

regApiHandle([
    'list' => function () {
        $studentId = (int) regApiGet('student_id', 0);
        $category = (string) regApiGet('category', '');

        $files = regListStudentFiles($studentId, $category !== '' ? $category : null);
        regApiJson(['success' => true, 'data' => $files]);
    },

    'student_docs' => function () {
        $studentId = (int) regApiGet('student_id', 0);
        if ($studentId <= 0) {
            regApiJson(['success' => false, 'error' => 'Invalid student ID'], 400);
        }

        $docs = regGetStudentDigitalDocuments($studentId);
        regApiJson(['success' => true, 'data' => $docs]);
    },

    'get_file' => function () {
        $fileId = (int) regApiGet('file_id', 0);
        $file = regGetFile($fileId);
        if (!$file) {
            regApiJson(['success' => false, 'error' => 'File not found'], 404);
        }
        regApiJson(['success' => true, 'data' => $file]);
    },

    'upload' => function () {
        regApiRequireAccess();
        regApiRequireCsrf();

        $studentId = (int) regApiPost('student_id', 0);
        $category = (string) regApiPost('category', 'general');
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

        if (!isset($_FILES['file'])) {
            regApiJson(['success' => false, 'error' => 'No file provided'], 400);
        }

        $result = regStoreUploadedFile(
            $studentId,
            $category,
            $_FILES['file'],
            $userId,
            5242880 // 5MB max
        );

        if (!$result['success']) {
            regApiJson(['success' => false, 'error' => $result['error']], 400);
        }

        regApiLog('upload_file', 'Uploaded ' . ($_FILES['file']['name'] ?? 'file') . ' (category ' . $category . ', student ' . $studentId . ')');

        regApiJson([
            'success' => true,
            'message' => 'File uploaded',
            'file_id' => $result['file_id'],
            'filename' => $result['hash'] ?? '',
        ]);
    },

    'verify' => function () {
        $fileId = (int) regApiGet('file_id', 0);

        $result = regVerifyFileIntegrity($fileId);

        regApiJson([
            'success' => $result['valid'] ?? false,
            'valid' => $result['valid'] ?? false,
            'reason' => $result['error'] ?? null,
            'hash' => $result['hash'] ?? null,
        ]);
    },

    'delete' => function () {
        regApiRequireAccess();
        regApiRequireCsrf();

        $body = regApiBody();
        $fileId = (int) ($body['file_id'] ?? (int) regApiPost('file_id', 0));
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

        $result = regDeleteFile($fileId, $userId);

        if (!$result) {
            regApiJson(['success' => false, 'error' => 'File not found or already deleted'], 400);
        }

        regApiLog('delete_file', 'Soft-deleted file id ' . $fileId);
        regApiJson(['success' => true, 'message' => 'File deleted']);
    },
]);