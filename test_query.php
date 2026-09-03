<?php
require 'C:/xampp/htdocs/SMS2/config/config.php';
require 'C:/xampp/htdocs/SMS2/config/database.php';
$db = db();
$stmt = $db->prepare('SELECT i.*, r.request_no, r.channel, r.student_id as req_student_id, r.student_email, s.first_name, s.last_name, f.stored_name, f.original_name, f.mime FROM reg_doc_request_items i JOIN reg_doc_requests r ON i.request_id = r.id LEFT JOIN reg_students s ON (r.student_id = s.id) LEFT JOIN reg_files f ON i.generated_file_id = f.id LIMIT 1');
$stmt->execute();
var_dump($stmt->fetch(PDO::FETCH_ASSOC));
