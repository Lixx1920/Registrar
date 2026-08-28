<?php
/**
 * SMS 2 - Class Scheduling API: Sections
 * Actions: list (GET), save (POST, create), delete (POST)
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/scheduling-service.php';

$action = schGet('action', 'list');

switch ($action) {
    case 'list':
        $schoolYear = schGet('school_year');
        $semester = schGet('semester');
        schJsonSuccess(schListSections($schoolYear, $semester));
        break;

    case 'save':
        if (!schIsPost()) {
            schJsonError('POST required.', 405);
        }
        schRequireCsrf();
        $data = schJsonBody();
        $result = schCreateSection($data);
        if ($result['success']) {
            schJsonSuccess(['id' => $result['id']], 'Section created.');
        } else {
            schJsonError($result['error'] ?? 'Failed to create section.');
        }
        break;

    case 'delete':
        if (!schIsPost()) {
            schJsonError('POST required.', 405);
        }
        schRequireCsrf();
        $data = schJsonBody();
        $id = (int) ($data['id'] ?? 0);
        if ($id <= 0) {
            schJsonError('Missing section id.');
        }
        $result = schDeleteSection($id);
        if ($result['success']) {
            schJsonSuccess(null, 'Section deleted.');
        } else {
            schJsonError('Section not found.', 404);
        }
        break;

    default:
        schJsonError('Unknown action.', 404);
}