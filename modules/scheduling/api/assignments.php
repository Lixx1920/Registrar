<?php
/**
 * SMS 2 - Class Scheduling API: Section Assignments
 * Actions: list (GET, per section), assign (POST), unassign (POST)
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/scheduling-service.php';

$action = schGet('action', 'list');

switch ($action) {
    case 'list':
        $sectionId = (int) schGet('section_id', '0');
        if ($sectionId <= 0) {
            schJsonError('Missing section_id.');
        }
        schJsonSuccess(schListAssignmentsForSection($sectionId));
        break;

    case 'assign':
        if (!schIsPost()) {
            schJsonError('POST required.', 405);
        }
        schRequireCsrf();
        $data = schJsonBody();
        $sectionId = (int) ($data['section_id'] ?? 0);
        $studentId = (int) ($data['student_id'] ?? 0);
        if ($sectionId <= 0 || $studentId <= 0) {
            schJsonError('Missing section_id or student_id.');
        }
        $result = schAssignStudent($sectionId, $studentId);
        if ($result['success']) {
            schJsonSuccess(['id' => $result['id']], 'Student assigned.');
        } else {
            schJsonError($result['error'] ?? 'Failed to assign student.');
        }
        break;

    case 'unassign':
        if (!schIsPost()) {
            schJsonError('POST required.', 405);
        }
        schRequireCsrf();
        $data = schJsonBody();
        $id = (int) ($data['id'] ?? 0);
        if ($id <= 0) {
            schJsonError('Missing assignment id.');
        }
        $result = schUnassignStudent($id);
        if ($result['success']) {
            schJsonSuccess(null, 'Student removed from section.');
        } else {
            schJsonError('Assignment not found.', 404);
        }
        break;

    default:
        schJsonError('Unknown action.', 404);
}