<?php
/**
 * SMS2 - Registrar API: Health Records
 * List, save, and delete health record log entries.
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/api-helpers.php';
require_once __DIR__ . '/../includes/registrar-service.php';

regApiHandle([
    'list' => function () {
        $studentId = (int) regApiGet('student_id', 0);

        $db = db();
        $stmt = $db->prepare("SELECT * FROM `reg_health_records` WHERE `student_id` = ? ORDER BY `checkup_date` DESC");
        $stmt->execute([$studentId]);

        regApiJson(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    },

    'save' => function () {
        regApiRequireAccess();
        regApiRequireCsrf();

        $data = regApiBody();
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

        if (empty($data['student_id']) || empty($data['checkup_date'])) {
            regApiJson(['success' => false, 'error' => 'Missing required fields'], 400);
        }

        $db = db();
        try {
            if (!empty($data['id'])) {
                // Update
                $stmt = $db->prepare("UPDATE `reg_health_records` SET
                    `checkup_date` = ?, `complaints` = ?, `findings` = ?,
                    `vital_signs` = ?, `immunization` = ?, `medication` = ?,
                    `physician_nurse` = ?, `notes` = ?, `updated_by` = ?
                    WHERE `id` = ?");
                $stmt->execute([
                    $data['checkup_date'],
                    $data['complaints'] ?? null,
                    $data['findings'] ?? null,
                    !empty($data['vital_signs']) ? json_encode($data['vital_signs']) : null,
                    $data['immunization'] ?? null,
                    $data['medication'] ?? null,
                    $data['physician_nurse'] ?? null,
                    $data['notes'] ?? null,
                    $userId,
                    $data['id'],
                ]);
            } else {
                // Create
                $stmt = $db->prepare("INSERT INTO `reg_health_records`
                    (`student_id`, `checkup_date`, `complaints`, `findings`, `vital_signs`,
                     `immunization`, `medication`, `physician_nurse`, `notes`, `created_by`)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $data['student_id'],
                    $data['checkup_date'],
                    $data['complaints'] ?? null,
                    $data['findings'] ?? null,
                    !empty($data['vital_signs']) ? json_encode($data['vital_signs']) : null,
                    $data['immunization'] ?? null,
                    $data['medication'] ?? null,
                    $data['physician_nurse'] ?? null,
                    $data['notes'] ?? null,
                    $userId,
                ]);
            }

            regApiLog('save_health', 'Saved health record for student ' . $data['student_id']);
            regApiJson(['success' => true, 'message' => 'Health record saved']);
        } catch (Throwable $e) {
            regApiJson(['success' => false, 'error' => $e->getMessage()], 400);
        }
    },

    'delete' => function () {
        regApiRequireAccess();
        regApiRequireCsrf();

        $body = regApiBody();
        $id = (int) ($body['id'] ?? (int) regApiPost('id', 0));

        $db = db();
        try {
            $stmt = $db->prepare("DELETE FROM `reg_health_records` WHERE `id` = ?");
            $stmt->execute([$id]);

            regApiLog('delete_health', 'Deleted health record id ' . $id);
            regApiJson(['success' => true, 'message' => 'Health record deleted']);
        } catch (Throwable $e) {
            regApiJson(['success' => false, 'error' => $e->getMessage()], 400);
        }
    },
]);