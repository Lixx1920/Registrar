<?php
/**
 * SMS2 - Registrar API: Academic History & Collegiate Grades
 * List, save, and delete school history + semester-by-semester subject grades.
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/api-helpers.php';
require_once __DIR__ . '/../includes/registrar-service.php';

regApiHandle([
    // List previous school history records
    'list' => function () {
        $studentId = (int) regApiGet('student_id', 0);

        $db = db();
        $stmt = $db->prepare("SELECT * FROM `reg_academic_history` WHERE `student_id` = ? ORDER BY `from_year` DESC, `to_year` DESC");
        $stmt->execute([$studentId]);

        regApiJson(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    },

    // Get flat collegiate subjects
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

    // Get structured semester-by-semester grade tree with computed GWA & summary
    'grades_tree' => function () {
        $studentId = (int) regApiGet('student_id', 0);
        if ($studentId <= 0) {
            regApiJson(['success' => false, 'error' => 'Valid student_id is required'], 400);
        }

        $history = regGetStudentGradeHistory($studentId);
        regApiJson(['success' => true, 'data' => $history]);
    },

    // Save previous school educational background
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

    // Delete previous school educational background
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

    // Save or update a collegiate subject grade
    'save_subject' => function () {
        regApiRequireAccess();
        regApiRequireCsrf();

        $data = regApiBody();
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

        $res = regSaveAcademicSubject($data, $userId);
        if ($res['success']) {
            regApiJson(['success' => true, 'message' => 'Subject grade saved successfully', 'id' => $res['id'] ?? null]);
        } else {
            regApiJson(['success' => false, 'error' => $res['error'] ?? 'Save failed'], 400);
        }
    },

    // Batch save subjects for a term
    'batch_save_subjects' => function () {
        regApiRequireAccess();
        regApiRequireCsrf();

        $data = regApiBody();
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

        $studentId    = (int)($data['student_id'] ?? 0);
        $yearLevel    = trim((string)($data['year_level'] ?? '1st Year'));
        $term         = trim((string)($data['term'] ?? '1st'));
        $academicYear = trim((string)($data['academic_year'] ?? date('Y') . '-' . (date('Y') + 1)));
        $subjects     = $data['subjects'] ?? [];

        if ($studentId <= 0 || empty($subjects) || !is_array($subjects)) {
            regApiJson(['success' => false, 'error' => 'Student ID and subjects array are required.'], 400);
        }

        $savedCount = 0;
        foreach ($subjects as $s) {
            if (empty($s['subject_code']) || empty($s['subject_name'])) {
                continue;
            }
            $payload = [
                'id'            => $s['id'] ?? null,
                'student_id'    => $studentId,
                'subject_code'  => $s['subject_code'],
                'subject_name'  => $s['subject_name'],
                'units'         => $s['units'] ?? 3.0,
                'year_level'    => $yearLevel,
                'term'          => $term,
                'academic_year' => $academicYear,
                'grade'         => $s['grade'] ?? null,
                'remarks'       => $s['remarks'] ?? null,
                'status'        => $s['status'] ?? null,
                'instructor'    => $s['instructor'] ?? null,
            ];
            $res = regSaveAcademicSubject($payload, $userId);
            if ($res['success']) {
                $savedCount++;
            }
        }

        regApiJson(['success' => true, 'message' => "Saved {$savedCount} subjects for {$yearLevel} {$term} Sem"]);
    },

    // Delete a collegiate subject grade
    'delete_subject' => function () {
        regApiRequireAccess();
        regApiRequireCsrf();

        $body = regApiBody();
        $id = (int) ($body['id'] ?? (int) regApiPost('id', 0));
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

        if ($id <= 0) {
            regApiJson(['success' => false, 'error' => 'Invalid subject ID'], 400);
        }

        $res = regDeleteAcademicSubject($id, $userId);
        if ($res['success']) {
            regApiJson(['success' => true, 'message' => 'Subject grade record deleted']);
        } else {
            regApiJson(['success' => false, 'error' => $res['error'] ?? 'Delete failed'], 400);
        }
    },
]);