<?php
/**
 * SMS 2 - Persona File Database (Deprecated / Consolidated)
 * Module: Registrar
 * Note: Persona File Database has been consolidated into Digital File Storage (digital-file-storage.php)
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';

$studentId = (int)($_GET['student_id'] ?? 0);
$targetUrl = BASE_URL . '/modules/registrar/pages/digital-file-storage.php' . ($studentId > 0 ? ('?student_id=' . $studentId) : '');

header('Location: ' . $targetUrl, true, 302);
exit;