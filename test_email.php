<?php
require 'C:/xampp/htdocs/SMS2/config/config.php';
require 'C:/xampp/htdocs/SMS2/config/database.php';
require 'C:/xampp/htdocs/SMS2/includes/security.php';
require 'C:/xampp/htdocs/SMS2/includes/mail.php';

$res = smsSendMail('boyrexar02@gmail.com', 'Test from SMS2', '<p>Hello world</p>');
var_dump($res);
