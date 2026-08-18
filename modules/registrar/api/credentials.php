<?php
/**
 * SMS2 - Registrar API: Credentials (RFID / QR Code binding)
 * Uses reg_credentials (credential_type + token_value).
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/api-helpers.php';
require_once __DIR__ . '/../includes/registrar-service.php';

regApiHandle([
    'get' => function () {
        $studentId = (int) regApiGet('student_id', 0);
        $type = (string) regApiGet('type', '');

        $db = db();
        $sql = "SELECT * FROM `reg_credentials` WHERE `student_id` = ?";
        $params = [$studentId];
        if ($type !== '') {
            $sql .= " AND `credential_type` = ?";
            $params[] = $type;
        }
        $sql .= " ORDER BY `credential_type`";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $creds = $stmt->fetchAll(PDO::FETCH_ASSOC);

        regApiJson(['success' => true, 'data' => $creds]);
    },

    'save' => function () {
        regApiRequireAccess();
        regApiRequireCsrf();

        $data = regApiBody();
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

        if (empty($data['student_id']) || empty($data['credential_type']) || empty($data['token_value'])) {
            regApiJson(['success' => false, 'error' => 'Missing student_id, credential_type, or token_value'], 400);
        }

        $db = db();
        try {
            // Upsert: one active token per type per student
            $stmt = $db->prepare("SELECT `id` FROM `reg_credentials`
                WHERE `student_id` = ? AND `credential_type` = ? LIMIT 1");
            $stmt->execute([$data['student_id'], $data['credential_type']]);
            $exists = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($exists) {
                $stmt = $db->prepare("UPDATE `reg_credentials` SET
                    `token_value` = ?, `status` = ?, `notes` = ?, `activated_at` = COALESCE(`activated_at`, NOW())
                    WHERE `id` = ?");
                $stmt->execute([
                    $data['token_value'],
                    $data['status'] ?? 'Active',
                    $data['notes'] ?? null,
                    $exists['id'],
                ]);
                $msg = 'Credentials updated';
            } else {
                $stmt = $db->prepare("INSERT INTO `reg_credentials`
                    (`student_id`, `credential_type`, `token_value`, `status`, `activated_at`, `notes`, `created_by`)
                    VALUES (?, ?, ?, ?, NOW(), ?, ?)");
                $stmt->execute([
                    $data['student_id'],
                    $data['credential_type'],
                    $data['token_value'],
                    $data['status'] ?? 'Active',
                    $data['notes'] ?? null,
                    $userId,
                ]);
                $msg = 'Credentials created';
            }

            regApiLog('save_credential', 'Saved ' . $data['credential_type'] . ' for student ' . $data['student_id']);
            regApiJson(['success' => true, 'message' => $msg]);
        } catch (Throwable $e) {
            regApiJson(['success' => false, 'error' => $e->getMessage()], 400);
        }
    },
]);