<?php
require 'C:/xampp/htdocs/SMS2/config/config.php';
require 'C:/xampp/htdocs/SMS2/config/database.php';
require 'C:/xampp/htdocs/SMS2/includes/security.php';
require 'C:/xampp/htdocs/SMS2/includes/crypto.php';

$pdo = db();

$settings = [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => '587',
    'smtp_encryption' => 'tls',
    'smtp_username' => 'boyrexar02@gmail.com',
    'smtp_password' => 'pqhn jsxr xwmk wpkz',
    'mail_from_email' => 'boyrexar02@gmail.com',
    'mail_from_name' => 'Registrar Office'
];

foreach ($settings as $k => $v) {
    if (smsIsEncryptedSettingKey($k)) {
        $v = smsSecretEncrypt($v);
    }
    
    $stmt = $pdo->prepare('INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?');
    $stmt->execute([$k, $v, $v]);
}

echo "Settings updated successfully.\n";
