<?php
/**
 * SMS 2 - Class Scheduling: Section Assignment service layer.
 *
 * Real CRUD against sch_sections / sch_section_assignments, plus a lookup
 * helper (schGetEnrollmentMap) that the Registrar Masterlist Generator will
 * call later to pull a real "Enrolled" fact instead of a mock value.
 */
declare(strict_types=1);

/** All sections, optionally filtered by school year / semester. */
function schListSections(string $schoolYear = '', string $semester = ''): array
{
    $db = db();
    $sql = "SELECT sec.*,
                (SELECT COUNT(*) FROM `sch_section_assignments` a WHERE a.section_id = sec.id AND a.status = 'Enrolled') AS student_count
            FROM `sch_sections` sec WHERE 1=1";
    $params = [];

    if ($schoolYear !== '') {
        $sql .= " AND sec.school_year = ?";
        $params[] = $schoolYear;
    }
    if ($semester !== '') {
        $sql .= " AND sec.semester = ?";
        $params[] = $semester;
    }

    $sql .= " ORDER BY sec.program_course, sec.year_section";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function schGetSection(int $id): ?array
{
    $db = db();
    $stmt = $db->prepare("SELECT * FROM `sch_sections` WHERE `id` = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

/** Create a section. Returns ['success' => bool, 'id' => int|null, 'error' => string|null]. */
function schCreateSection(array $data): array
{
    $program = trim((string) ($data['program_course'] ?? ''));
    $yearSection = trim((string) ($data['year_section'] ?? ''));
    $schoolYear = trim((string) ($data['school_year'] ?? ''));
    $semester = trim((string) ($data['semester'] ?? '1st Semester'));
    $sectionCode = trim((string) ($data['section_code'] ?? '')) ?: null;
    $adviser = trim((string) ($data['adviser_name'] ?? '')) ?: null;
    $maxStudents = isset($data['max_students']) && $data['max_students'] !== ''
        ? (int) $data['max_students'] : null;

    if ($program === '' || $yearSection === '' || $schoolYear === '') {
        return ['success' => false, 'error' => 'Program, Year & Section, and School Year are required.'];
    }

    $db = db();
    $stmt = $db->prepare("INSERT INTO `sch_sections`
        (`program_course`, `year_section`, `school_year`, `semester`, `section_code`, `adviser_name`, `max_students`, `created_by`)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$program, $yearSection, $schoolYear, $semester, $sectionCode, $adviser, $maxStudents, getCurrentUserId()]);

    return ['success' => true, 'id' => (int) $db->lastInsertId()];
}

function schDeleteSection(int $id): array
{
    $db = db();
    // FK is ON DELETE CASCADE, so assignments under this section are removed too.
    $stmt = $db->prepare("DELETE FROM `sch_sections` WHERE `id` = ?");
    $stmt->execute([$id]);
    return ['success' => $stmt->rowCount() > 0];
}

/** Assignments for one section, joined with student info for display. */
function schListAssignmentsForSection(int $sectionId): array
{
    $db = db();
    $stmt = $db->prepare("
        SELECT a.*, s.student_number, s.first_name, s.last_name, s.program_course, s.year_section AS student_year_section
        FROM `sch_section_assignments` a
        JOIN `reg_students` s ON s.id = a.student_id
        WHERE a.section_id = ?
        ORDER BY s.last_name, s.first_name
    ");
    $stmt->execute([$sectionId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/** Assign a student into a section. Returns ['success' => bool, 'error' => string|null]. */
function schAssignStudent(int $sectionId, int $studentId): array
{
    $db = db();

    // Confirm both sides exist (clear error instead of a raw FK violation).
    $stmt = $db->prepare("SELECT 1 FROM `sch_sections` WHERE `id` = ?");
    $stmt->execute([$sectionId]);
    if (!$stmt->fetchColumn()) {
        return ['success' => false, 'error' => 'Section not found.'];
    }

    $stmt = $db->prepare("SELECT 1 FROM `reg_students` WHERE `id` = ?");
    $stmt->execute([$studentId]);
    if (!$stmt->fetchColumn()) {
        return ['success' => false, 'error' => 'Student not found.'];
    }

    $stmt = $db->prepare("SELECT `id` FROM `sch_section_assignments` WHERE `section_id` = ? AND `student_id` = ?");
    $stmt->execute([$sectionId, $studentId]);
    if ($stmt->fetchColumn()) {
        return ['success' => false, 'error' => 'This student is already assigned to this section.'];
    }

    $stmt = $db->prepare("INSERT INTO `sch_section_assignments` (`section_id`, `student_id`, `status`, `created_by`)
        VALUES (?, ?, 'Enrolled', ?)");
    $stmt->execute([$sectionId, $studentId, getCurrentUserId()]);

    return ['success' => true, 'id' => (int) $db->lastInsertId()];
}

function schUnassignStudent(int $assignmentId): array
{
    $db = db();
    $stmt = $db->prepare("DELETE FROM `sch_section_assignments` WHERE `id` = ?");
    $stmt->execute([$assignmentId]);
    return ['success' => $stmt->rowCount() > 0];
}

/**
 * Lookup map of student_id => true for students with an active ('Enrolled')
 * assignment in the given school year / semester. Intended for the Registrar
 * Masterlist Generator to determine a real "Enrolled" column.
 */
function schGetEnrollmentMap(string $schoolYear, string $semester = ''): array
{
    $db = db();
    $sql = "SELECT DISTINCT a.student_id
            FROM `sch_section_assignments` a
            JOIN `sch_sections` sec ON sec.id = a.section_id
            WHERE a.status = 'Enrolled' AND sec.school_year = ?";
    $params = [$schoolYear];

    if ($semester !== '') {
        $sql .= " AND sec.semester = ?";
        $params[] = $semester;
    }

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    return array_fill_keys(array_map('intval', $ids), true);
}