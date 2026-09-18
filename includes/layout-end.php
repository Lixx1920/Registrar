<?php
/**
 * SMS 2 - Authenticated Layout End
 */
?>
        </main>
        <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
    </div>
</div>
<?php 
if (function_exists('getCurrentUserRoleKey') && getCurrentUserRoleKey() === 'registrar') {
    $registrarTermsPath = ROOT_PATH . '/modules/registrar/includes/terms-modal.php';
    if (file_exists($registrarTermsPath)) {
        require_once $registrarTermsPath;
    }
}
require_once ROOT_PATH . '/includes/scripts.php'; 
?>
