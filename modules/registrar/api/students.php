<?php
/**
 * SMS2 - Registrar API: Students
 * Endpoints for student CRUD operations.
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/api-helpers.php';
require_once __DIR__ . '/../includes/registrar-service.php';

regApiHandle([
    'search' => function () {
        $query = (string) regApiGet('q', '');
        $limit = max(1, (int) regApiGet('limit', '20'));

        $results = regSearchStudents($query, $limit);

        regApiJson(['success' => true, 'data' => $results]);
    },

    'get' => function () {
        $body = regApiBody();
        $studentId = (int) ($body['id'] ?? (int) regApiGet('id', 0));
        $student = regGetStudent($studentId);

        if (!$student) {
            regApiJson(['success' => false, 'error' => 'Student not found'], 404);
        }

        regApiJson(['success' => true, 'data' => $student]);
    },

    'save' => function () {
        regApiRequireAccess();
        regApiRequireCsrf();

        $data = regApiBody();
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

        // Validate required fields
        if (empty($data['student_number']) || empty($data['first_name']) || empty($data['last_name'])) {
            regApiJson(['success' => false, 'error' => 'Missing required fields'], 400);
        }

        $result = regSaveStudent($data, $userId);

        if (!$result['success']) {
            regApiJson(['success' => false, 'error' => $result['error']], 400);
        }

        regApiLog('save_student', 'Saved student ' . $data['student_number'] . ' (id ' . ($result['student_id'] ?? '?') . ')');

        regApiJson([
            'success' => true,
            'message' => 'Student ' . $result['action'],
            'student_id' => $result['student_id'],
        ]);
    },

    'delete' => function () {
        regApiRequireAccess();
        regApiRequireCsrf();

        $body = regApiBody();
        $studentId = (int)($body['id'] ?? 0);
        if ($studentId <= 0) {
            regApiJson(['success' => false, 'error' => 'Invalid student id'], 400);
        }

        $stmt = db()->prepare("UPDATE `reg_students` SET `status` = 'Deleted' WHERE `id` = ? AND `status` != 'Deleted'");
        $stmt->execute([$studentId]);
        if ($stmt->rowCount() === 0) {
            regApiJson(['success' => false, 'error' => 'Student not found'], 404);
        }

        regApiLog('delete_student', 'Soft-deleted student id ' . $studentId);
        regApiJson(['success' => true, 'message' => 'Student deleted']);
    },

    'list' => function () {
        $page = max(1, (int) regApiGet('page', '1'));
        $limit = min(100, max(1, (int) regApiGet('limit', '20')));
        $offset = ($page - 1) * $limit;

        $db = db();
        $stmt = $db->prepare("SELECT * FROM `reg_students`
            WHERE `status` != 'Deleted'
            ORDER BY `last_name`, `first_name`
            LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = (int) $db->query("SELECT COUNT(*) FROM `reg_students` WHERE `status` != 'Deleted'")->fetchColumn();

        regApiJson([
            'success' => true,
            'data' => $students,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => (int) ceil($total / $limit),
            ],
        ]);
    },
]);