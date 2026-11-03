<?php
require 'config/database.php';
$db = db();
$stmt = $db->query('SHOW COLUMNS FROM reg_students');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
