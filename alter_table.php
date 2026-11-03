<?php
require 'config/database.php';
$db = db();
try {
    $db->exec('ALTER TABLE reg_students ADD COLUMN student_type VARCHAR(50) NULL');
    echo 'DONE';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
