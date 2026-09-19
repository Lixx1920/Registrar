<?php
/**
 * SMS 2 - Scripts
 */
?>
<!-- Bootstrap JS (local) -->
<script src="<?= BASE_URL ?>/assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>
<!-- SMS 2 App -->
<?php if (empty($omitThemeJs)): ?>
<script src="<?= BASE_URL ?>/assets/js/theme.js"></script>
<?php endif; ?>
<?php if (empty($omitAppChromeJs)): ?>
<script src="<?= BASE_URL ?>/assets/js/ph-clock.js"></script>
<script src="<?= BASE_URL ?>/assets/js/sidebar.js"></script>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
<script src="<?= BASE_URL ?>/assets/js/sms-security-ui.js?v=5"></script>
<!-- Global Search -->
<script src="<?= BASE_URL ?>/assets/js/search.js"></script>
<?php endif; ?>
<?php if (function_exists('getCurrentUserRoleKey') && getCurrentUserRoleKey() === 'registrar'): ?>
<script>
(function() {
    let timeout;
    function logout() {
        window.location.href = '<?= BASE_URL ?>/login/logout.php';
    }
    function resetTimer() {
        clearTimeout(timeout);
        timeout = setTimeout(logout, 300000);
    }
    window.onload = resetTimer;
    document.onmousemove = resetTimer;
    document.onkeydown = resetTimer;
    document.onscroll = resetTimer;
    document.onclick = resetTimer;
})();
</script>
<?php endif; ?>
</body>
</html>
