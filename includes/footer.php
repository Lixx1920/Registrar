<?php
/**
 * SMS 2 - Footer
 */
?>
<footer class="sms-footer mt-auto">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <small>&copy; <?= date('Y') ?> <?= htmlspecialchars(INSTITUTION) ?>. All rights reserved.</small>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <?php if (function_exists('getCurrentUserRoleKey') && getCurrentUserRoleKey() === 'registrar'): ?>
                    <style>
                        .footer-terms-link {
                            color: #6c757d;
                            transition: all 0.2s ease-in-out;
                        }
                        .footer-terms-link:hover {
                            color: var(--bs-primary);
                            text-decoration: underline !important;
                        }
                    </style>
                    <small>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#registrarTermsModal" class="text-decoration-none footer-terms-link">Terms and Conditions</a> 
                        <span class="mx-1 text-muted">|</span> 
                        <a href="#" data-bs-toggle="modal" data-bs-target="#registrarTermsModal" onclick="setTimeout(() => document.getElementById('btnNextStep')?.click(), 200);" class="text-decoration-none footer-terms-link">Data Privacy Notice</a>
                    </small>
                <?php else: ?>
                    <small><?= htmlspecialchars(APP_NAME) ?> v<?= APP_VERSION ?></small>
                <?php endif; ?>
            </div>
        </div>
    </div>
</footer>
