<?php
/**
 * SMS2 - Registrar API: Guardian & Emergency Contacts
 * List, save, and delete guardian records per student.
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/api-helpers.php';
require_once __DIR__ . '/../includes/registrar-service.php';

regApiHandle([
    'list' => function () {
        $studentId = (int) regApiGet('student_id', 0);

        $db = db();
        $stmt = $db->prepare("SELECT * FROM `reg_guardians` WHERE `student_id` = ? ORDER BY `is_emergency` DESC, `relationship`");
        $stmt->execute([$studentId]);

        regApiJson(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    },

    'save' => function () {
        regApiRequireAccess();
        regApiRequireCsrf();

        $data = regApiBody();
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

        if (empty($data['student_id']) || empty($data['full_name']) || empty($data['relationship'])) {
            regApiJson(['success' => false, 'error' => 'Missing required fields (student_id, full_name, relationship)'], 400);
        }

        $db = db();
        try {
            if (!empty($data['id'])) {
                // Update existing guardian
                $stmt = $db->prepare("UPDATE `reg_guardians` SET
                    `full_name` = ?, `relationship` = ?, `contact` = ?, `email` = ?,
                    `address` = ?, `is_primary` = ?, `is_emergency` = ?, `updated_by` = ?
                    WHERE `id` = ?");
                $stmt->execute([
                    $data['full_name'],
                    $data['relationship'],
                    $data['contact'] ?? null,
                    $data['email'] ?? null,
                    $data['address'] ?? null,
                    (int) ($data['is_primary'] ?? 0),
                    (int) ($data['is_emergency'] ?? 0),
                    $userId,
                    $data['id'],
                ]);
            } else {
                // Create new guardian
                $stmt = $db->prepare("INSERT INTO `reg_guardians`
                    (`student_id`, `full_name`, `relationship`, `contact`, `email`, `address`,
                     `is_primary`, `is_emergency`, `created_by`)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $data['student_id'],
                    $data['full_name'],
                    $data['relationship'],
                    $data['contact'] ?? null,
                    $data['email'] ?? null,
                    $data['address'] ?? null,
                    (int) ($data['is_primary'] ?? 0),
                    (int) ($data['is_emergency'] ?? 0),
                    $userId,
                ]);
            }

            regApiLog('save_guardian', 'Saved guardian for student ' . $data['student_id']);
            regApiJson(['success' => true, 'message' => 'Guardian saved']);
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
            $stmt = $db->prepare("DELETE FROM `reg_guardians` WHERE `id` = ?");
            $stmt->execute([$id]);

            regApiLog('delete_guardian', 'Deleted guardian id ' . $id);
            regApiJson(['success' => true, 'message' => 'Guardian deleted']);
        } catch (Throwable $e) {
            regApiJson(['success' => false, 'error' => $e->getMessage()], 400);
        }
    },
]);