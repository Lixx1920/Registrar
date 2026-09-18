<?php
/**
 * SMS 2 – Per-module Security Settings (sidebar only)
 *
 * Staff / student:
 *   - Activity logs for this module
 *   - Change password (current + new + confirm) with Authenticator / email OTP
 *   - OR request password reset from Super Admin
 *
 * Super Admin viewing a module:
 *   - Activity logs for this module
 *   - Pending password reset requests for this module
 *   - Reset a user’s password (admin action)
 *   - No personal “change my password” form here
 */
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';
require_once ROOT_PATH . '/includes/module-security-catalog.php';
require_once ROOT_PATH . '/includes/security-workflow.php';
require_once ROOT_PATH . '/includes/authenticator-ui.php';
require_once ROOT_PATH . '/includes/passkey.php';
require_once ROOT_PATH . '/includes/security-ui.php';
requireAuth();

smsEnsureSecurityTables();
smsEnsureAuthenticatorTable();

// Super Admin: security lives only under User Management → Module Security
if (in_array(getCurrentUserRoleKey(), ['admin', 'superadmin'], true)) {
    $mod = (string) ($_GET['focus'] ?? $_GET['sec_mod'] ?? $_GET['mod'] ?? $_GET['module'] ?? $_GET['m'] ?? '');
    if ($mod === 'student-portal') {
        $mod = 'student_portal';
    }
    if (in_array(strtolower($mod), ['crud', 'crowd'], true)) {
        $mod = 'crad';
    }
    $hub = BASE_URL . '/modules/user-management/pages/module-security.php';
    if ($mod !== '') {
        header('Location: ' . $hub . '?focus=' . rawurlencode($mod));
    } else {
        header('Location: ' . $hub . '?picker=1');
    }
    exit;
}

$moduleKey = (string) ($_GET['module'] ?? '');
if ($moduleKey === 'student-portal') {
    $moduleKey = 'student_portal';
}

$info = smsModuleSecurityInfo($moduleKey);
if ($info === null && !isset($MODULES[$moduleKey]) && $moduleKey !== 'student_portal') {
    header('Location: ' . BASE_URL . '/dashboard/index.php');
    exit;
}

if ($moduleKey === 'user-management' || !userCanAccessModule($moduleKey)) {
    header('Location: ' . BASE_URL . '/dashboard/index.php');
    exit;
}

$isAdmin = false; // staff/student page only
$moduleLabel = is_array($info) ? (string) ($info['label'] ?? smsModuleLabel($moduleKey)) : smsModuleLabel($moduleKey);
$moduleIconFallback = 'fa-shield-alt';
if (is_array($info) && !empty($info['icon'])) {
    $moduleIconFallback = (string) $info['icon'];
}
$securityRoleKey = getCurrentUserRoleKey();
$accountContext = [
    'student' => ['module' => 'student_portal', 'label' => 'Student Portal', 'icon' => 'fa-user-graduate'],
    'admission' => ['module' => 'enrollment', 'label' => 'Admission Account', 'icon' => 'fa-user-plus'],
    'adviser' => ['module' => 'faculty', 'label' => 'Adviser Account', 'icon' => 'fa-user-tie'],
    'panel' => ['module' => 'faculty', 'label' => 'Panel Account', 'icon' => 'fa-users'],
    'research_director' => ['module' => 'faculty', 'label' => 'Research Director Account', 'icon' => 'fa-user-shield'],
    'hr' => ['module' => 'faculty', 'label' => 'HR Account', 'icon' => 'fa-chalkboard-teacher'],
    'research_coordinator' => ['module' => 'crad', 'label' => 'Research Coordinator', 'icon' => 'fa-microscope'],
    'crad_officer' => ['module' => 'crad', 'label' => 'CRAD Officer', 'icon' => 'fa-flask'],
    'research_grant' => ['module' => 'crad_grant', 'label' => 'Research Grant Account', 'icon' => 'fa-hand-holding-usd'],
    'finance' => ['module' => 'payment', 'label' => 'Finance Account', 'icon' => 'fa-credit-card'],
    'registrar' => ['module' => $moduleKey, 'label' => 'Registrar Account', 'icon' => 'fa-folder-open'],
    'osa' => ['module' => 'cocurricular', 'label' => 'OSA Account', 'icon' => 'fa-users'],
    'it_office' => ['module' => 'lms', 'label' => 'IT Office Account', 'icon' => 'fa-laptop'],
    'qa' => ['module' => 'accreditation', 'label' => 'QA Account', 'icon' => 'fa-award'],
];
if (isset($accountContext[$securityRoleKey])) {
    $context = $accountContext[$securityRoleKey];
    if (($context['module'] ?? $moduleKey) === $moduleKey || $securityRoleKey === 'registrar') {
        $moduleLabel = (string) $context['label'];
        $moduleIconFallback = (string) $context['icon'];
    }
}
$tab = 'logs';

$error = '';
$success = '';
$otpDevCode = '';

if (!empty($_SESSION['flash_sec_success'])) {
    $success = (string) $_SESSION['flash_sec_success'];
    unset($_SESSION['flash_sec_success']);
}
if (!empty($_SESSION['flash_sec_error'])) {
    $error = (string) $_SESSION['flash_sec_error'];
    unset($_SESSION['flash_sec_error']);
}
if (!empty($_SESSION['flash_sec_otp'])) {
    $otpDevCode = (string) $_SESSION['flash_sec_otp'];
    unset($_SESSION['flash_sec_otp']);
}

$userId = (int) getCurrentUserId();
$minLen = (int) smsSetting('min_password_length', '8');

/* ── POST handlers ─────────────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfVerify()) {
        $_SESSION['flash_sec_error'] = 'Security check failed. Please try again.';
        header('Location: ' . BASE_URL . '/account/module-security.php?module=' . urlencode($moduleKey) . '&tab=' . urlencode($tab));
        exit;
    }

    $action = (string) ($_POST['action'] ?? '');

    $authActions = [
        'auth_turn_on', 'auth_request_setup', 'auth_send_setup_otp', 'auth_confirm_enable',
        'auth_cancel_setup', 'auth_disable', 'auth_turn_off_start', 'auth_disable_show_code', 'auth_disable_cancel',
    ];
    if (in_array($action, $authActions, true)) {
        $result = smsHandleAuthenticatorPost($userId, $action, $_POST, (string) ($_SESSION['user_email'] ?? ''));
        if ($result['success'] !== '') {
            $_SESSION['flash_sec_success'] = $result['success'];
        }
        if ($result['error'] !== '') {
            $_SESSION['flash_sec_error'] = $result['error'];
        }
        if ($result['otp_dev'] !== '') {
            $_SESSION['flash_sec_otp'] = $result['otp_dev'];
        }
        $extra = $result['redirect_extra'] !== '' ? '&' . $result['redirect_extra'] : '';
        header('Location: ' . BASE_URL . '/account/module-security.php?module=' . urlencode($moduleKey) . '&tab=authenticator' . $extra);
        exit;
    }

    // Staff: send OTP / Authenticator step for password change
    if ($action === 'send_otp' && !$isAdmin) {
        $current = (string) ($_POST['current_password'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['password_confirm'] ?? '');

        $pdo = db();
        $row = null;
        if ($pdo) {
            $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE id = ? LIMIT 1');
            $stmt->execute([$userId]);
            $row = $stmt->fetch();
        }

        if (!$row || !password_verify($current, (string) $row['password_hash'])) {
            $_SESSION['flash_sec_error'] = 'Current password is incorrect.';
        } else {
            $strength = smsValidatePasswordStrength($password);
            if (!$strength['ok']) {
                $_SESSION['flash_sec_error'] = $strength['message'];
            } elseif ($password !== $confirm) {
                $_SESSION['flash_sec_error'] = 'New passwords do not match.';
            } else {
                $_SESSION['pending_pw_change'] = [
                    'hash' => password_hash($password, PASSWORD_DEFAULT),
                    'module' => $moduleKey,
                    'at' => time(),
                ];
                $authOn = smsAuthenticatorIsEnabled($userId);
                $issued = smsIssueOtpToEmail($userId, 'password_change', $moduleKey, 10, 'password change');
                if (!empty($issued['ok']) || $authOn) {
                    if (!empty($issued['ok']) && !empty($issued['show_local']) && !empty($issued['code'])) {
                        $_SESSION['flash_sec_otp'] = (string) $issued['code'];
                    }
                    if ($authOn && empty($issued['ok'])) {
                        $_SESSION['flash_sec_success'] = 'Enter the 6-digit code from your Authenticator app to confirm the password change.';
                    } elseif ($authOn && !empty($issued['emailed'])) {
                        $_SESSION['flash_sec_success'] = 'Enter your Authenticator code, or the 6-digit email code sent to '
                            . (string) $issued['to']
                            . '.';
                    } elseif (!empty($issued['emailed'])) {
                        $_SESSION['flash_sec_success'] = 'OTP sent to '
                            . (string) $issued['to']
                            . '. Enter the 6-digit code from your email to confirm.';
                    } elseif (!empty($issued['ok'])) {
                        $_SESSION['flash_sec_success'] = 'OTP generated, but email could not be sent'
                            . (!empty($issued['error']) ? ' (' . $issued['error'] . ')' : '')
                            . '. Use the on-screen code if shown, or your Authenticator app.';
                    } else {
                        $_SESSION['flash_sec_success'] = 'Enter the 6-digit code from your Authenticator app to confirm.';
                    }
                    logActivity(
                        'password_reset_request',
                        $authOn
                            ? 'Password change awaiting Authenticator / email OTP'
                            : (!empty($issued['emailed'])
                                ? 'OTP emailed for password change'
                                : 'OTP generated for password change (email failed)'),
                        $moduleKey
                    );
                    header('Location: ' . BASE_URL . '/account/module-security.php?module=' . urlencode($moduleKey) . '&tab=account&step=otp');
                    exit;
                }
                unset($_SESSION['pending_pw_change']);
                $_SESSION['flash_sec_error'] = !empty($issued['error'])
                    ? (string) $issued['error']
                    : 'Could not send a verification code. Set up Authenticator or configure email in System Settings.';
            }
        }
        header('Location: ' . BASE_URL . '/account/module-security.php?module=' . urlencode($moduleKey) . '&tab=account');
        exit;
    }

    // Staff: confirm OTP / Authenticator + apply new password
    if ($action === 'confirm_otp' && !$isAdmin) {
        $otp = trim((string) ($_POST['otp_code'] ?? ''));
        $pending = $_SESSION['pending_pw_change'] ?? null;
        $pwPurpose = 'password_change';

        if (!is_array($pending) || empty($pending['hash']) || (($pending['module'] ?? '') !== $moduleKey)) {
            $_SESSION['flash_sec_error'] = 'Password change session expired. Start again.';
        } else {
            $gate = smsGetCodeGate($userId, $pwPurpose);
            if (!empty($gate['locked'])) {
                $_SESSION['flash_sec_error'] = $gate['message'];
            } else {
                $otpOk = smsVerifyOtp($userId, $pwPurpose, $otp);
                $authOk = !$otpOk && smsAuthenticatorIsEnabled($userId) && smsAuthenticatorVerifyLogin($userId, $otp);
                if (!$otpOk && !$authOk) {
                    $gate = smsRegisterCodeFailure($userId, $pwPurpose);
                    $_SESSION['flash_sec_error'] = smsCodeFailureMessage($gate, 'code');
                } else {
                    smsClearCodeGate($userId, $pwPurpose);
                    $pdo = db();
                    if ($pdo) {
                        $pdo->prepare(
                            'UPDATE users SET password_hash = ?, must_change_password = 0, password_changed_at = NOW(),
                             failed_login_attempts = 0, locked_until = NULL WHERE id = ?'
                        )->execute([$pending['hash'], $userId]);
                        unset($_SESSION['pending_pw_change']);
                        logActivity('password_change', 'Password changed with OTP verification', $moduleKey);
                        $_SESSION['flash_sec_success'] = 'Password updated successfully.';
                        header('Location: ' . BASE_URL . '/account/module-security.php?module=' . urlencode($moduleKey) . '&tab=account');
                        exit;
                    }
                    $_SESSION['flash_sec_error'] = 'Database error while saving password.';
                }
            }
        }
        header('Location: ' . BASE_URL . '/account/module-security.php?module=' . urlencode($moduleKey) . '&tab=account&step=otp');
        exit;
    }

    // Staff: request admin reset (reason + chosen password required)
    if ($action === 'request_admin_reset' && !$isAdmin) {
        $reason = trim((string) ($_POST['reason'] ?? ''));
        $newPassword = (string) ($_POST['requested_password'] ?? '');
        $confirm = (string) ($_POST['requested_password_confirm'] ?? '');

        if ($reason === '') {
            $_SESSION['flash_sec_error'] = 'Reason is required.';
        } elseif ($newPassword !== $confirm) {
            $_SESSION['flash_sec_error'] = 'Requested passwords do not match.';
        } else {
            $strength = smsValidatePasswordStrength($newPassword);
            if (!$strength['ok']) {
                $_SESSION['flash_sec_error'] = $strength['message'];
            } else {
                $result = smsCreatePasswordResetRequest($userId, $moduleKey, $reason, $newPassword);
                if ($result['ok']) {
                    $_SESSION['flash_sec_success'] = 'Request sent to Super Admin with your chosen password. Wait for approval.';
                } else {
                    $_SESSION['flash_sec_error'] = $result['error'] ?? 'Could not submit request.';
                }
            }
        }
        header('Location: ' . BASE_URL . '/account/module-security.php?module=' . urlencode($moduleKey) . '&tab=account');
        exit;
    }

    // Admin: approve request (applies user's chosen password — admin does not type a password)
    if ($action === 'approve_request' && $isAdmin) {
        $reqId = (int) ($_POST['request_id'] ?? 0);
        $result = smsApprovePasswordRequest($reqId, $userId);
        if ($result['ok']) {
            $_SESSION['flash_sec_success'] = 'Approved. The user’s chosen password is now active.';
        } else {
            $_SESSION['flash_sec_error'] = $result['error'] ?? 'Approve failed.';
        }
        header('Location: ' . BASE_URL . '/account/module-security.php?module=' . urlencode($moduleKey) . '&tab=requests');
        exit;
    }

    // Admin: reject request
    if ($action === 'reject_request' && $isAdmin) {
        $reqId = (int) ($_POST['request_id'] ?? 0);
        if (smsRejectPasswordRequest($reqId, $userId, trim((string) ($_POST['admin_note'] ?? '')))) {
            $_SESSION['flash_sec_success'] = 'Request rejected.';
        } else {
            $_SESSION['flash_sec_error'] = 'Could not reject request.';
        }
        header('Location: ' . BASE_URL . '/account/module-security.php?module=' . urlencode($moduleKey) . '&tab=requests');
        exit;
    }

    // Admin: direct reset password for a user (auto-generated temp only)
    if ($action === 'admin_reset_user' && $isAdmin) {
        $targetId = (int) ($_POST['target_user_id'] ?? 0);
        $temp = 'Temp@' . random_int(100000, 999999);
        if ($targetId <= 0) {
            $_SESSION['flash_sec_error'] = 'Select a user.';
        } elseif ($targetId === $userId) {
            $_SESSION['flash_sec_error'] = 'You cannot reset your own password on this screen.';
        } elseif (!smsSetUserPassword($targetId, $temp, true)) {
            $_SESSION['flash_sec_error'] = 'Could not reset password.';
        } else {
            logActivity('password_reset', 'Admin reset password for user #' . $targetId, $moduleKey);
            $_SESSION['flash_sec_success'] = 'Password reset. Temporary password: ' . $temp . ' (user must change on next login).';
        }
        header('Location: ' . BASE_URL . '/account/module-security.php?module=' . urlencode($moduleKey) . '&tab=reset');
        exit;
    }

    header('Location: ' . BASE_URL . '/account/module-security.php?module=' . urlencode($moduleKey) . '&tab=' . urlencode($tab));
    exit;
}

$step = (string) ($_GET['step'] ?? '');

// Record that this security page was opened (so Activity Logs always has module traffic)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($_SESSION['sec_view_' . $moduleKey])) {
    logActivity('view', 'Opened Security Settings', $moduleKey);
    $_SESSION['sec_view_' . $moduleKey] = time();
}

$logs = smsModuleActivityLogs($moduleKey, 200, $userId);
$pendingRequests = $isAdmin ? smsPendingPasswordRequests($moduleKey) : [];

// Users for admin reset (active accounts)
$resetUsers = [];
if ($isAdmin) {
    $pdo = db();
    if ($pdo) {
        $resetUsers = $pdo->query(
            'SELECT id, full_name, username, email, role_key, status
             FROM users WHERE status IN (\'active\',\'locked\') AND role_key <> \'admin\'
             ORDER BY full_name ASC'
        )->fetchAll() ?: [];
    }
}

// Staff pending / latest rejected request (so they can see admin note)
$myPending = null;
$myRejected = null;
if (!$isAdmin) {
    $pdo = db();
    if ($pdo) {
        $stmt = $pdo->prepare(
            'SELECT * FROM password_reset_requests
             WHERE user_id = ? AND status = \'pending\' ORDER BY id DESC LIMIT 1'
        );
        $stmt->execute([$userId]);
        $myPending = $stmt->fetch() ?: null;

        if (!$myPending) {
            $stmt = $pdo->prepare(
                'SELECT * FROM password_reset_requests
                 WHERE user_id = ? AND module_key = ? AND status = \'rejected\'
                 ORDER BY resolved_at DESC, id DESC LIMIT 1'
            );
            $stmt->execute([$userId, $moduleKey]);
            $myRejected = $stmt->fetch() ?: null;
        }
    }
}

$pageTitle    = $moduleLabel . ' – Security';
$activeModule = $moduleKey === 'student_portal' ? 'student_portal' : $moduleKey;
$activePage   = 'security-settings';
$breadcrumbs  = [
    [
        'label' => $moduleLabel,
        'url'   => $moduleKey === 'student_portal'
            ? BASE_URL . '/modules/student-portal/pages/my-profile.php'
            : BASE_URL . '/modules/' . $moduleKey . '/index.php',
    ],
    ['label' => 'Security Settings', 'url' => null],
];
$pageBannerIcon = $moduleIconFallback;
$pageBannerDescription = 'Activity logs for ' . $moduleLabel . '.';

require_once ROOT_PATH . '/includes/breadcrumbs.php';
require_once ROOT_PATH . '/includes/layout-start.php';

// Navbar/sidebar foreach must not overwrite this page's module key.
$moduleKey = (string) ($_GET['module'] ?? $moduleKey ?? '');
if ($moduleKey === 'student-portal') {
    $moduleKey = 'student_portal';
}

$baseUrl = BASE_URL . '/account/module-security.php?module=' . urlencode($moduleKey);
$moduleIcon = $moduleIconFallback;
$initialPanel = 'logs';

$logActions = [];
foreach ($logs as $log) {
    $a = (string) ($log['action'] ?? '');
    if ($a !== '') {
        $logActions[$a] = true;
    }
}
ksort($logActions);
?>
<?php renderBreadcrumbs($breadcrumbs); ?>

<div class="container-fluid py-4">
    <div id="secModuleRoot" class="sms-sec-root" data-initial-panel="<?= e($initialPanel) ?>" data-url-mode="staff" data-module="<?= e($moduleKey) ?>">
        <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?= e($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?= e($error) ?></div>
    <?php endif; ?>
    <?php if ($otpDevCode !== ''): ?>
        <div class="alert alert-warning">
            <strong>Local OTP (email not delivered):</strong>
            <code class="fs-5 ms-1"><?= e($otpDevCode) ?></code>
            <span class="d-block small mt-1">Configure SMTP in System Settings so the code is emailed to your Gmail instead.</span>
        </div>
    <?php endif; ?>

    <!-- Tabs removed: Password and Authenticator are now managed via the Profile Dashboard -->

    <div id="panel-logs" class="sec-panel sms-sec-panel" role="tabpanel" data-sec-panel="logs">
        <section class="card sms-sec-card">
            <div class="card-body">
                <div class="sms-sec-card-head">
                    <div class="sms-sec-card-title">
                        <span class="sms-sec-icon"><i class="fas fa-history" aria-hidden="true"></i></span>
                        <div>
                            <h2 class="h5 fw-bold mb-0"><?= e($moduleLabel) ?> — Activity Logs</h2>
                            <p class="sms-sec-lead mb-0 mt-1">Filter by user, action, or date. Scroll to browse.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="small text-muted" id="modLogCount"><?= count($logs) ?> shown</span>
                        <button type="button" class="btn btn-sm btn-outline-primary"
                                data-sms-export-csv="#modLogTable"
                                data-sms-export-rows="tbody tr.mod-log-row"
                                data-sms-export-filename="<?= e($moduleKey) ?>-activity-logs.csv">
                            <i class="fas fa-file-export me-1"></i>Export CSV
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="modLogClear">Clear filters</button>
                    </div>
                </div>
                <div class="row g-2 g-md-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label small mb-1" for="modLogUser">User</label>
                        <input type="text" id="modLogUser" class="form-control form-control-sm" placeholder="Name…">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small mb-1" for="modLogAction">Action type</label>
                        <select id="modLogAction" class="form-select form-select-sm">
                            <option value="">All actions</option>
                            <?php foreach (array_keys($logActions) as $actionName): ?>
                                <option value="<?= e($actionName) ?>"><?= e(ucfirst(str_replace('_', ' ', $actionName))) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label small mb-1" for="modLogDateFrom">Date from</label>
                        <input type="date" id="modLogDateFrom" class="form-control form-control-sm">
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label small mb-1" for="modLogDateTo">Date to</label>
                        <input type="date" id="modLogDateTo" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="mod-log-scroll table-responsive border rounded-3">
                    <table class="table table-hover align-middle mb-0" id="modLogTable">
                        <thead class="mod-log-thead">
                            <tr>
                                <th>Time</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Detail</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$logs): ?>
                                <tr><td colspan="5" class="text-center text-muted py-4">No activity logs for this module yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($logs as $log): ?>
                                    <tr class="mod-log-row"
                                        data-action="<?= e((string) ($log['action'] ?? '')) ?>"
                                        data-user="<?= e(strtolower((string) ($log['user_name'] ?? ''))) ?>"
                                        data-date="<?= e((string) ($log['log_date'] ?? '')) ?>">
                                        <td class="small text-nowrap"><?= e($log['time']) ?></td>
                                        <td>
                                            <div class="fw-semibold"><?= e($log['user_name'] ?? 'System') ?></div>
                                            <div class="small text-muted"><?= e($log['role_key'] ?? '') ?></div>
                                        </td>
                                        <td><span class="badge text-bg-light"><?= e($log['action']) ?></span></td>
                                        <td class="small"><?= e($log['detail']) ?></td>
                                        <td class="small text-muted"><?= e($log['ip_address'] ?? '—') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="mod-log-empty-filter" hidden>
                                    <td colspan="5" class="text-center text-muted py-4">No logs match the selected filters.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <!-- Password and Authenticator panels removed -->
    </div>
</div>

<link href="<?= BASE_URL ?>/assets/css/password-strength.css" rel="stylesheet">
<script src="<?= BASE_URL ?>/assets/js/password-strength.js"></script>
<script src="<?= BASE_URL ?>/modules/user-management/assets/js/module-security.js?v=20260723e"></script>
<?php require_once ROOT_PATH . '/includes/layout-end.php'; ?>
