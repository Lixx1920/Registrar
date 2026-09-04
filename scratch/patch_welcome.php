<?php
$prepend = "
if (!function_exists('smsWelcomeHeroImageUrl')) {
    function smsWelcomeHeroImageUrl() {
        return BASE_URL . '/images/school2.png';
    }
}
if (!function_exists('smsBrandLogoUrl')) {
    function smsBrandLogoUrl() {
        return BASE_URL . '/images/bcp-crest.png';
    }
}
if (!function_exists('smsIcon')) {
    function smsIcon(\$icon, \$attrs = []) {
        \$attrStr = '';
        foreach (\$attrs as \$k => \$v) {
            \$attrStr .= ' ' . htmlspecialchars(\$k) . '=\"' . htmlspecialchars(\$v) . '\"';
        }
        \$map = [
            'shield' => 'shield-alt',
            'building' => 'building',
            'circle-check' => 'check-circle',
            'user-plus' => 'user-plus',
            'book' => 'book',
            'chalkboard' => 'chalkboard',
            'wallet' => 'wallet',
            'chevron-right' => 'chevron-right',
            'login' => 'sign-in-alt',
            'id' => 'id-card',
        ];
        \$faIcon = \$map[\$icon] ?? \$icon;
        return '<i class=\"fas fa-' . htmlspecialchars(\$faIcon) . '\"' . \$attrStr . '></i>';
    }
}
";

$content = file_get_contents('c:/xampp/htdocs/SMS2/scratch/new_welcome.php');
$content = preg_replace('/<\?php/', "<?php\n" . $prepend, $content, 1);
file_put_contents('c:/xampp/htdocs/SMS2/welcome/index.php', $content);
echo "Patched and copied!";
?>
