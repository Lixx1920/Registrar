<?php
$file = 'c:/xampp/htdocs/SMS2/includes/sidebar.php';
$content = file_get_contents($file);
$htmlStart = strpos($content, '<aside class="sms-sidebar"');
if ($htmlStart !== false) {
    $phpPart = substr($content, 0, $htmlStart);
    $newHtml = file_get_contents('c:/xampp/htdocs/SMS2/scratch/replacement.html');
    file_put_contents($file, $phpPart . $newHtml);
    echo "Success\n";
} else {
    echo "Failed to find start\n";
}
?>
