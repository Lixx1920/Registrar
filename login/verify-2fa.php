<?php
/**
 * SMS 2 – Login step 2: Authenticator code or email OTP
 *
 * Mode toggle:
 *  - authenticator (default when enabled): enter app code; button → email
 *  - email: enter emailed code; button → switch back to authenticator
 *
 * Wrong codes / resends are rate-limited (temporary lock).
 */
require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/includes/totp.php';
require_once ROOT_PATH . '/includes/mail.php';
require_once ROOT_PATH . '/includes/security-workflow.php';
require_once ROOT_PATH . '/includes/security-ui.php';

if (isAuthenticated()) {
    header('Location: ' . smsPostLoginRedirectUrl());
    exit;
}

$pending = $_SESSION['pending_2fa'] ?? null;
if (!is_array($pending) || empty($pending['user_id']) || empty($pending['at']) || ((int) $pending['at'] + 600) < time()) {
    unset($_SESSION['pending_2fa']);
    $_SESSION['flash_login_error'] = 'Your sign-in session expired. Please sign in again.';
    header('Location: ' . BASE_URL . '/login/login.php');
    exit;
}

$userId = (int) $pending['user_id'];
$error = '';
$info = '';
$otpDev = '';
$purpose = (string) ($_SESSION['pending_2fa']['purpose'] ?? 'login_2fa');

if (!empty($_SESSION['flash_2fa_error'])) {
    $error = (string) $_SESSION['flash_2fa_error'];
    unset($_SESSION['flash_2fa_error']);
}
if (!empty($_SESSION['flash_2fa_info'])) {
    $info = (string) $_SESSION['flash_2fa_info'];
    unset($_SESSION['flash_2fa_info']);
}
if (!empty($_SESSION['flash_2fa_otp'])) {
    $otpDev = (string) $_SESSION['flash_2fa_otp'];
    unset($_SESSION['flash_2fa_otp']);
}

$user = null;
$pdo = db();
if ($pdo) {
    $stmt = $pdo->prepare(
        'SELECT u.*, r.label AS role_label
         FROM users u INNER JOIN roles r ON r.role_key = u.role_key
         WHERE u.id = ? LIMIT 1'
    );
    $stmt->execute([$userId]);
    $user = $stmt->fetch() ?: null;
}
if (!$user) {
    unset($_SESSION['pending_2fa']);
    header('Location: ' . BASE_URL . '/login/login.php');
    exit;
}

$authEnabled = smsAuthenticatorIsEnabled($userId);

// Default method: authenticator when available, otherwise email
$method = strtolower(trim((string) ($_SESSION['pending_2fa']['method'] ?? '')));
if ($method !== 'authenticator' && $method !== 'email') {
    $method = $authEnabled ? 'authenticator' : 'email';
    $_SESSION['pending_2fa']['method'] = $method;
}
if ($method === 'authenticator' && !$authEnabled) {
    $method = 'email';
    $_SESSION['pending_2fa']['method'] = 'email';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfVerify()) {
        $_SESSION['flash_2fa_error'] = 'Security check failed. Please try again.';
        header('Location: ' . BASE_URL . '/login/verify-2fa.php');
        exit;
    }

    $action = (string) ($_POST['action'] ?? 'verify');

    if ($action === 'use_email') {
        $_SESSION['pending_2fa']['method'] = 'email';
        $isReg = ($purpose === 'registrar_login') || (($user['role_key'] ?? '') === 'registrar');
        $ttlMinutes = $isReg ? 2 : 10;
        $issued = smsIssueOtpToEmail($userId, $purpose, 'System', $ttlMinutes, 'login verification');
        if (!empty($issued['ok'])) {
            $_SESSION['pending_2fa']['otp_issued_at'] = time();
            if (!empty($issued['show_local']) && !empty($issued['code'])) {
                $_SESSION['flash_2fa_otp'] = (string) $issued['code'];
            }
            $_SESSION['flash_2fa_info'] = !empty($issued['emailed'])
                ? ($isReg
                    ? 'A fresh 2-minute login verification code was sent to ' . $issued['to'] . '.'
                    : 'A login code was emailed to ' . $issued['to'] . '. Enter that email code below.')
                : 'Could not email OTP' . ($issued['error'] !== '' ? ': ' . $issued['error'] : '')
                    . '. Use the on-screen code if shown.';
        } else {
            $_SESSION['flash_2fa_error'] = (string) ($issued['error'] !== '' ? $issued['error'] : 'Could not send email OTP.');
        }
        header('Location: ' . BASE_URL . '/login/verify-2fa.php');
        exit;
    }

    if ($action === 'use_authenticator') {
        if (!$authEnabled) {
            $_SESSION['flash_2fa_error'] = 'Authenticator is not set up on this account.';
            header('Location: ' . BASE_URL . '/login/verify-2fa.php');
            exit;
        }
        $_SESSION['pending_2fa']['method'] = 'authenticator';
        $_SESSION['flash_2fa_info'] = 'Enter the 6-digit code from your Authenticator app.';
        header('Location: ' . BASE_URL . '/login/verify-2fa.php');
        exit;
    }

    if ($action === 'cancel') {
        unset($_SESSION['pending_2fa']);
        header('Location: ' . BASE_URL . '/login/login.php');
        exit;
    }

    $codeGate = smsGetCodeGate($userId, $purpose);
    if (!empty($codeGate['locked'])) {
        $_SESSION['flash_2fa_error'] = $codeGate['message'];
        header('Location: ' . BASE_URL . '/login/verify-2fa.php');
        exit;
    }

    $code = trim((string) ($_POST['code'] ?? ''));
    $activeMethod = strtolower(trim((string) ($_SESSION['pending_2fa']['method'] ?? $method)));
    $ok = false;

    if ($activeMethod === 'email') {
        $ok = smsVerifyOtp($userId, $purpose, $code);
        if (!$ok) {
            $gate = smsRegisterCodeFailure($userId, $purpose);
            $_SESSION['flash_2fa_error'] = smsCodeFailureMessage($gate, 'email');
            header('Location: ' . BASE_URL . '/login/verify-2fa.php');
            exit;
        }
    } else {
        $ok = smsAuthenticatorVerifyLogin($userId, $code);
        if (!$ok) {
            $gate = smsRegisterCodeFailure($userId, $purpose);
            $_SESSION['flash_2fa_error'] = smsCodeFailureMessage($gate, 'authenticator');
            header('Location: ' . BASE_URL . '/login/verify-2fa.php');
            exit;
        }
    }

    smsClearCodeGate($userId, $purpose);

    $username = (string) ($pending['username'] ?? '');
    
    $isRegistrarLogin = ($purpose === 'registrar_login') || (($user['role_key'] ?? '') === 'registrar');

    // Issue the 1-hour trusted device cookie for registrar
    if ($isRegistrarLogin) {
        setcookie('registrar_trusted_' . $userId, '1', time() + 3600, '/');
    }

    $result = smsCompleteLoginSession($user, $username);
    if (!empty($result['ok'])) {
        require_once ROOT_PATH . '/includes/module-controls.php';
        if (smsIsSystemInMaintenance() && !in_array(getCurrentUserRoleKey(), ['superadmin', 'admin'], true)) {
            logout();
            header('Location: ' . BASE_URL . '/account/maintenance.php');
            exit;
        }
        if (!empty($_SESSION['must_change_password'])) {
            header('Location: ' . BASE_URL . '/login/change-password.php');
            exit;
        }
        header('Location: ' . smsPostLoginRedirectUrl());
        exit;
    }

    $_SESSION['flash_2fa_error'] = 'Could not complete sign-in.';
    header('Location: ' . BASE_URL . '/login/verify-2fa.php');
    exit;
}

$isRegistrarLogin = ($purpose === 'registrar_login') || (($user['role_key'] ?? '') === 'registrar');
$codeGate = smsGetCodeGate($userId, $purpose);
$isLocked = !empty($codeGate['locked']);
$isEmailMode = $method === 'email';

// Calculate 2-minute countdown for registrar (or configured TTL)
$otpTotalSeconds = $isRegistrarLogin ? 120 : 600; // 2 minutes (120s) for registrar
$otpSecondsLeft = 0;

if ($isEmailMode && $pdo) {
    $stmtOtp = $pdo->prepare(
        'SELECT expires_at, TIMESTAMPDIFF(SECOND, NOW(), expires_at) AS sec_left 
         FROM security_otps
         WHERE user_id = ? AND purpose = ? AND used_at IS NULL AND expires_at > NOW()
         ORDER BY id DESC LIMIT 1'
    );
    $stmtOtp->execute([$userId, $purpose]);
    $otpRow = $stmtOtp->fetch(PDO::FETCH_ASSOC);
    if ($otpRow && isset($otpRow['sec_left']) && (int) $otpRow['sec_left'] > 0) {
        $otpSecondsLeft = min($otpTotalSeconds, (int) $otpRow['sec_left']);
    } else {
        // Fallback: calculate elapsed from session issuance
        $issuedAt = (int) ($_SESSION['pending_2fa']['otp_issued_at'] ?? $_SESSION['pending_2fa']['at'] ?? time());
        $elapsed  = max(0, time() - $issuedAt);
        $otpSecondsLeft = max(0, $otpTotalSeconds - $elapsed);
    }
}
$pageTitle = 'Verify Sign In';
$bodyClass = 'login-page';
require_once ROOT_PATH . '/includes/header.php';
?>
<link href="<?= BASE_URL ?>/assets/css/auth-pages.css" rel="stylesheet">
<style>
body.login-page {
    background: #071c48 !important;
    background-image: none !important;
    position: relative;
}

.verify-video-bg {
    position: fixed;
    inset: 0;
    z-index: 0;
    overflow: hidden;
    pointer-events: none;
}

.verify-video-bg video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    transform: scale(1.04);
    filter: saturate(1.05) brightness(0.92);
}

/* Keep video visible behind the glassy verify card */
.verify-video-bg::after {
    content: "";
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse 75% 60% at 50% 42%, rgba(4, 16, 42, 0.12) 0%, rgba(4, 16, 42, 0.42) 75%, rgba(2, 8, 24, 0.62) 100%),
        linear-gradient(180deg, rgba(4, 16, 42, 0.28) 0%, rgba(4, 16, 42, 0.18) 45%, rgba(2, 8, 24, 0.55) 100%);
}

.auth-stage {
    position: relative;
    z-index: 1;
}

.auth-footer {
    position: relative;
    z-index: 1;
}

@media (prefers-reduced-motion: reduce) {
    .verify-video-bg video {
        display: none;
    }

    .verify-video-bg {
        background: #071c48;
    }
}

/* ── 2-Minute Email OTP Countdown Styles ─────────────────────────────────── */
.auth-countdown-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 0.95rem 1.15rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    transition: all 0.25s ease;
}

.auth-countdown-card.warning {
    background: #fffbeb;
    border-color: #fde68a;
}

.auth-countdown-card.danger {
    background: #fef2f2;
    border-color: #fecaca;
}

.auth-countdown-card.expired {
    background: #fdf2f2;
    border-color: #f87171;
}

.countdown-pulse-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: #10b981;
    display: inline-block;
    animation: sstDotPulse 1.5s infinite;
}

.countdown-pulse-dot.warning {
    background: #f59e0b;
}

.countdown-pulse-dot.danger {
    background: #ef4444;
}

.countdown-pulse-dot.expired {
    background: #94a3b8;
    animation: none;
}

@keyframes sstDotPulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.4; transform: scale(1.3); }
}

.countdown-label {
    font-size: 0.76rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #334155;
}

.countdown-clock-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.65rem;
    border-radius: 8px;
    background: #0f172a;
    color: #fff;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 0.95rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
    transition: all 0.2s ease;
}

.countdown-clock-badge.warning {
    background: #78350f;
    color: #fef3c7;
}

.countdown-clock-badge.danger {
    background: #7f1d1d;
    color: #fee2e2;
    animation: sstClockShake 0.6s infinite ease-in-out;
}

.countdown-clock-badge.expired {
    background: #ef4444;
    color: #fff;
    animation: none;
}

@keyframes sstClockShake {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.countdown-track {
    height: 6px;
    width: 100%;
    background: #e2e8f0;
    border-radius: 999px;
    overflow: hidden;
}

.countdown-bar {
    height: 100%;
    background: linear-gradient(90deg, #2563eb, #3b82f6);
    border-radius: 999px;
    transition: width 0.3s linear, background 0.3s ease;
}

.countdown-bar.warning {
    background: linear-gradient(90deg, #f59e0b, #fbbf24);
}

.countdown-bar.danger {
    background: linear-gradient(90deg, #ef4444, #f87171);
}

.resend-btn-highlight {
    animation: sstPulseResend 2s infinite ease-in-out;
    border-color: #2563eb !important;
    background: #eff6ff !important;
    color: #1d4ed8 !important;
    font-weight: 700 !important;
}

@keyframes sstPulseResend {
    0%, 100% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.4); }
    50% { box-shadow: 0 0 0 6px rgba(37, 99, 235, 0.15); }
}
</style>

<div class="verify-video-bg" aria-hidden="true">
    <video autoplay muted loop playsinline>
        <source src="<?= BASE_URL ?>/assets/videos/bcp-campus.mp4?v=bcp4" type="video/mp4">
    </video>
</div>

<main class="auth-stage">
    <section class="auth-card" aria-label="Two-factor verification">
        <div class="auth-badge">
            <?php if ($isEmailMode): ?>
                <i class="fas fa-envelope"></i>
                <span>Email</span>
            <?php else: ?>
                <i class="fas fa-shield-alt"></i>
                <span>Authenticator</span>
            <?php endif; ?>
        </div>
        <h1>Verify it’s you</h1>
        <?php if ($isEmailMode): ?>
            <p class="auth-lead">
                Enter the 6-digit code we sent to your <strong>email</strong>.
                This is an email code — not your Authenticator app code.
            </p>
        <?php else: ?>
            <p class="auth-lead">
                Enter the 6-digit code from your <strong>Authenticator</strong> app
                (Google Authenticator / similar).
            </p>
        <?php endif; ?>

        <?php if ($info): ?>
            <div class="alert alert-info"><?= e($info) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php elseif ($isLocked): ?>
            <div class="alert alert-warning"><?= e($codeGate['message']) ?></div>
        <?php endif; ?>
        <?php if ($otpDev !== '' && !$isLocked): ?>
            <div class="alert alert-warning"><strong>Local OTP:</strong> <code><?= e($otpDev) ?></code></div>
        <?php endif; ?>

        <?php if ($isEmailMode && !$isLocked): ?>
            <div class="auth-countdown-card mb-3" id="emailCountdownBox" 
                 data-seconds-left="<?= (int) $otpSecondsLeft ?>" 
                 data-total-seconds="<?= (int) $otpTotalSeconds ?>"
                 aria-live="polite">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="countdown-pulse-dot" id="countdownDot"></span>
                        <span class="countdown-label fw-bold" id="countdownLabel">
                            <?= $isRegistrarLogin ? 'Registrar 2-Minute Security Window' : 'Email Code Validity' ?>
                        </span>
                    </div>
                    <div class="countdown-clock-badge" id="countdownClockBadge">
                        <i class="fas fa-clock me-1 text-warning" id="countdownClockIcon"></i>
                        <span class="countdown-clock-val" id="countdownClock">02:00</span>
                    </div>
                </div>

                <!-- Progress Bar Track -->
                <div class="countdown-track">
                    <div class="countdown-bar" id="countdownProgressBar" style="width: 100%;"></div>
                </div>

                <div class="d-flex align-items-center justify-content-between mt-2" style="font-size:0.75rem;">
                    <span class="text-muted" id="countdownStatusText">
                        <i class="fas fa-shield-alt text-primary me-1"></i>
                        <?= $isRegistrarLogin ? 'Code expires in 2 minutes for security.' : 'Valid for single sign-in attempt.' ?>
                    </span>
                    <span class="text-muted fw-semibold" id="countdownSecText"><?= $otpSecondsLeft ?>s remaining</span>
                </div>

                <div class="alert alert-danger py-2 px-3 mt-2 mb-0 small fw-bold" id="countdownExpiredAlert" style="display:none;">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    This verification code has expired (2-minute limit exceeded). Please click <u>Resend email code</u> below to receive a new code.
                </div>
            </div>
        <?php endif; ?>

        <?php if ($isLocked): ?>
            <p class="text-muted small mb-3">
                Verification is temporarily locked after too many wrong codes.
                Please wait, then try again.
            </p>
            <fieldset disabled>
                <div class="mb-3">
                    <?= smsOtpInput('code', [
                        'id' => 'code',
                        'required' => false,
                        'label' => $isEmailMode ? 'Email code' : 'Authenticator code',
                        'hint' => 'Locked — try again later.',
                    ]) ?>
                </div>
                <button type="button" class="btn btn-secondary w-100 mb-2" disabled>
                    <i class="fas fa-lock me-2"></i>Please try again later
                </button>
            </fieldset>
        <?php else: ?>
        <form method="POST" class="mt-3" novalidate>
            <?= csrfField() ?>
            <input type="hidden" name="action" value="verify">
            <div class="mb-3">
                <?= smsOtpInput('code', [
                    'id' => 'code',
                    'required' => true,
                    'autofocus' => true,
                    'label' => $isEmailMode ? 'Email code' : 'Authenticator code',
                    'hint' => $isEmailMode
                        ? 'Use the 6-digit code from your email inbox.'
                        : 'Use the 6-digit code from your Authenticator app.',
                ]) ?>
            </div>
            <button type="submit" class="btn btn-auth-primary w-100 mb-2" id="verifySubmitBtn">
                <i class="fas fa-check me-2"></i>Verify &amp; sign in
            </button>
        </form>
        <?php endif; ?>

        <?php if (!$isLocked && $isEmailMode): ?>
            <?php if ($authEnabled): ?>
            <form method="POST" class="mb-2">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="use_authenticator">
                <button type="submit" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-mobile-alt me-2"></i>Use Authenticator instead
                </button>
            </form>
            <?php endif; ?>
            <form method="POST" class="mb-2">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="use_email">
                <button type="submit" class="btn btn-outline-secondary w-100" id="resendEmailBtn">
                    <i class="fas fa-paper-plane me-2"></i>Resend email code
                </button>
            </form>
        <?php elseif (!$isLocked): ?>
            <form method="POST" class="mb-2">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="use_email">
                <button type="submit" class="btn btn-outline-secondary w-100" id="resendEmailBtn">
                    <i class="fas fa-envelope me-2"></i>Send code to my email instead
                </button>
            </form>
        <?php endif; ?>

        <form method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="cancel">
            <button type="submit" class="btn btn-link w-100">Back to sign in</button>
        </form>
    </section>
</main>

<footer class="auth-footer">
    <p class="mb-0">&copy; 2026 Bestlink College of the Philippines. All rights reserved.</p>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const box = document.getElementById('emailCountdownBox');
    if (!box) return;

    let secondsLeft = parseInt(box.getAttribute('data-seconds-left') || '0', 10);
    const totalSeconds = parseInt(box.getAttribute('data-total-seconds') || '120', 10);

    const clockEl       = document.getElementById('countdownClock');
    const badgeEl       = document.getElementById('countdownClockBadge');
    const barEl         = document.getElementById('countdownProgressBar');
    const dotEl         = document.getElementById('countdownDot');
    const secTextEl     = document.getElementById('countdownSecText');
    const statusTextEl  = document.getElementById('countdownStatusText');
    const expiredAlert  = document.getElementById('countdownExpiredAlert');
    const submitBtn     = document.getElementById('verifySubmitBtn');
    const codeInput     = document.getElementById('code');
    const resendBtn     = document.getElementById('resendEmailBtn');

    function pad(n) { return String(n).padStart(2, '0'); }

    function formatTime(s) {
        const m = Math.floor(s / 60);
        const rem = s % 60;
        return pad(m) + ':' + pad(rem);
    }

    function updateUI() {
        if (secondsLeft <= 0) {
            // Expired state
            if (clockEl) clockEl.textContent = '00:00';
            if (badgeEl) badgeEl.className = 'countdown-clock-badge expired';
            box.className = 'auth-countdown-card expired mb-3';
            if (dotEl) dotEl.className = 'countdown-pulse-dot expired';
            if (barEl) {
                barEl.style.width = '0%';
                barEl.className = 'countdown-bar danger';
            }
            if (secTextEl) {
                secTextEl.textContent = 'Expired';
                secTextEl.className = 'text-danger fw-bold';
            }
            if (statusTextEl) {
                statusTextEl.innerHTML = '<span class="text-danger fw-bold"><i class="fas fa-times-circle me-1"></i>Code Expired</span>';
            }
            if (expiredAlert) {
                expiredAlert.style.display = 'block';
            }

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-history me-2"></i>Code Expired — Resend Below';
                submitBtn.classList.add('opacity-75');
            }
            if (codeInput) {
                codeInput.classList.add('is-invalid');
                codeInput.setAttribute('title', 'This verification code has expired.');
            }
            if (resendBtn) {
                resendBtn.classList.add('resend-btn-highlight');
                resendBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i><strong>Get New 2-Minute Code</strong>';
            }
            return;
        }

        if (clockEl) clockEl.textContent = formatTime(secondsLeft);
        if (secTextEl) secTextEl.textContent = secondsLeft + 's remaining';

        const percent = Math.min(100, Math.max(0, (secondsLeft / totalSeconds) * 100));
        if (barEl) barEl.style.width = percent + '%';

        if (secondsLeft <= 15) {
            box.className = 'auth-countdown-card danger mb-3';
            if (badgeEl) badgeEl.className = 'countdown-clock-badge danger';
            if (dotEl) dotEl.className = 'countdown-pulse-dot danger';
            if (barEl) barEl.className = 'countdown-bar danger';
            if (secTextEl) secTextEl.className = 'text-danger fw-bold';
            if (statusTextEl) {
                statusTextEl.innerHTML = '<span class="text-danger fw-semibold"><i class="fas fa-exclamation-triangle me-1"></i>Expiring soon!</span>';
            }
        } else if (secondsLeft <= 45) {
            box.className = 'auth-countdown-card warning mb-3';
            if (badgeEl) badgeEl.className = 'countdown-clock-badge warning';
            if (dotEl) dotEl.className = 'countdown-pulse-dot warning';
            if (barEl) barEl.className = 'countdown-bar warning';
            if (secTextEl) secTextEl.className = 'text-warning fw-bold';
            if (statusTextEl) {
                statusTextEl.innerHTML = '<span class="text-warning fw-semibold"><i class="fas fa-clock me-1"></i>Less than a minute</span>';
            }
        } else {
            box.className = 'auth-countdown-card mb-3';
            if (badgeEl) badgeEl.className = 'countdown-clock-badge';
            if (dotEl) dotEl.className = 'countdown-pulse-dot';
            if (barEl) barEl.className = 'countdown-bar';
            if (secTextEl) secTextEl.className = 'text-muted fw-semibold';
            if (statusTextEl) {
                statusTextEl.innerHTML = '<i class="fas fa-shield-alt text-primary me-1"></i><?= $isRegistrarLogin ? "2-minute security window" : "Valid for single sign-in" ?>';
            }
        }
    }

    updateUI();

    const timer = setInterval(function () {
        secondsLeft--;
        updateUI();
        if (secondsLeft <= 0) {
            clearInterval(timer);
        }
    }, 1000);
});
</script>
<?php require_once ROOT_PATH . '/includes/scripts.php'; ?>
