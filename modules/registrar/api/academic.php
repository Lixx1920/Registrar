<?php
/**
 * SMS2 - Registrar API: Academic History
 * List, save, and delete school history + subject-level grades.
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/api-helpers.php';
require_once __DIR__ . '/../includes/registrar-service.php';

regApiHandle([
    'list' => function () {
        $studentId = (int) regApiGet('student_id', 0);

        $db = db();
        $stmt = $db->prepare("SELECT * FROM `reg_academic_history` WHERE `student_id` = ? ORDER BY `from_year` DESC, `to_year` DESC");
        $stmt->execute([$studentId]);

        regApiJson(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    },

    'subjects' => function () {
        $studentId = (int) regApiGet('student_id', 0);
        $term = (string) regApiGet('term', '');

        $db = db();
        $sql = "SELECT * FROM `reg_academic_subjects` WHERE `student_id` = ?";
        $params = [$studentId];

        if ($term !== '') {
            $sql .= " AND `term` = ?";
            $params[] = $term;
        }

        $stmt = $db->prepare($sql . " ORDER BY `academic_year`, `term`, `subject_code`");
        $stmt->execute($params);

        regApiJson(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    },

    'save' => function () {
        regApiRequireAccess();
        regApiRequireCsrf();

        $data = regApiBody();
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

        if (empty($data['student_id']) || empty($data['school_name'])) {
            regApiJson(['success' => false, 'error' => 'Missing required fields'], 400);
        }

        $db = db();
        try {
            if (!empty($data['id'])) {
                // Update
                $stmt = $db->prepare("UPDATE `reg_academic_history` SET
                    `school_name` = ?, `level` = ?, `from_year` = ?, `to_year` = ?,
                    `awards` = ?, `remarks` = ?, `updated_by` = ?
                    WHERE `id` = ?");
                $stmt->execute([
                    $data['school_name'],
                    $data['level'] ?? null,
                    $data['from_year'] ?? null,
                    $data['to_year'] ?? null,
                    $data['awards'] ?? null,
                    $data['remarks'] ?? null,
                    $userId,
                    $data['id'],
                ]);
            } else {
                // Create
                $stmt = $db->prepare("INSERT INTO `reg_academic_history`
                    (`student_id`, `school_name`, `level`, `from_year`, `to_year`, `awards`, `remarks`, `created_by`)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $data['student_id'],
                    $data['school_name'],
                    $data['level'] ?? null,
                    $data['from_year'] ?? null,
                    $data['to_year'] ?? null,
                    $data['awards'] ?? null,
                    $data['remarks'] ?? null,
                    $userId,
                ]);
            }

            regApiLog('save_academic', 'Saved academic history for student ' . $data['student_id']);
            regApiJson(['success' => true, 'message' => 'Academic record saved']);
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
            $stmt = $db->prepare("DELETE FROM `reg_academic_history` WHERE `id` = ?");
            $stmt->execute([$id]);

            regApiLog('delete_academic', 'Deleted academic history id ' . $id);
            regApiJson(['success' => true, 'message' => 'Academic record deleted']);
        } catch (Throwable $e) {
            regApiJson(['success' => false, 'error' => $e->getMessage()], 400);
        }
    },
]);