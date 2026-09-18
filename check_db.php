<?php
require 'config/database.php';
$pdo = getDatabaseConnection();
$pdo->exec('ALTER TABLE users ADD COLUMN registrar_terms_agreed TINYINT(1) NOT NULL DEFAULT 0 AFTER welcome_message_shown');
echo "Column added successfully.";
