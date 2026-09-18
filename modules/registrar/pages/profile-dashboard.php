<?php
/**
 * SMS 2 - Dedicated Registrar Profile Dashboard
 * Module: Registrar
 * Features: View Profile details and change password with security constraints.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../includes/bootstrap.php';

$pageTitle    = 'Registrar Profile';
$activeModule = 'registrar';
$activePage   = 'profile-dashboard';

$userId = (int)getCurrentUserId();
$pdo = db();

$error = '';
$success = '';

if (!empty($_SESSION['flash_registrar_success'])) {
    $success = (string)$_SESSION['flash_registrar_success'];
    unset($_SESSION['flash_registrar_success']);
}
if (!empty($_SESSION['flash_registrar_error'])) {
    $error = (string)$_SESSION['flash_registrar_error'];
    unset($_SESSION['flash_registrar_error']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfVerify()) {
        $_SESSION['flash_registrar_error'] = 'Security check failed. Please refresh and try again.';
        header('Location: ' . BASE_URL . '/modules/registrar/pages/profile-dashboard.php');
        exit;
    }

    $action = (string)($_POST['action'] ?? '');
    
    if ($action === 'change_password') {
        $currentPassword = (string)($_POST['current_password'] ?? '');
        $newPassword = (string)($_POST['new_password'] ?? '');
        $confirmPassword = (string)($_POST['confirm_password'] ?? '');

        // Validate security constraints: 8 chars, 1 Capital, 1 symbol
        $valid = true;
        $msg = '';
        if (strlen($newPassword) < 8) {
            $valid = false;
            $msg = 'Password must be at least 8 characters long.';
        } elseif (!preg_match('/[A-Z]/', $newPassword)) {
            $valid = false;
            $msg = 'Password must contain at least one capital letter.';
        } elseif (!preg_match('/[^a-zA-Z\d]/', $newPassword)) {
            $valid = false;
            $msg = 'Password must contain at least one symbol.';
        } elseif ($newPassword !== $confirmPassword) {
            $valid = false;
            $msg = 'New password and confirm password do not match.';
        }

        if (!$valid) {
            $_SESSION['flash_registrar_error'] = $msg;
            header('Location: ' . BASE_URL . '/modules/registrar/pages/profile-dashboard.php');
            exit;
        }

        // Check current password
        $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE id = ? AND role_key = \'registrar\' LIMIT 1');
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || !password_verify($currentPassword, (string)$row['password_hash'])) {
            $_SESSION['flash_registrar_error'] = 'Current password is incorrect.';
            header('Location: ' . BASE_URL . '/modules/registrar/pages/profile-dashboard.php');
            exit;
        }

        // Update password
        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([$newHash, $userId]);
        
        $_SESSION['flash_registrar_success'] = 'Password has been changed successfully.';
        header('Location: ' . BASE_URL . '/modules/registrar/pages/profile-dashboard.php');
        exit;
    }
}

// Fetch current user info for display
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
$stmt->execute([$userId]);
$userProfile = $stmt->fetch(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../../../includes/breadcrumbs.php';
require_once __DIR__ . '/../../../includes/layout-start.php';
?>

<style>
/* Stunning & Premium Dashboard Styles */
.reg-profile-wrapper {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    background: transparent;
    min-height: calc(100vh - 150px);
    padding-bottom: 2rem;
}

.reg-profile-header {
    background: linear-gradient(135deg, var(--sms-primary-dark) 0%, var(--sms-primary) 100%);
    padding: 3rem 2rem;
    border-radius: 12px;
    color: #fff;
    position: relative;
    overflow: hidden;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.reg-profile-header::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: url('data:image/svg+xml;utf8,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="40" stroke="rgba(255,255,255,0.05)" stroke-width="2" fill="none"/></svg>') repeat;
    opacity: 0.5;
    pointer-events: none;
}

.reg-profile-avatar-container {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    position: relative;
    z-index: 1;
}

.reg-profile-avatar {
    width: 100px;
    height: 100px;
    background: rgba(255, 255, 255, 0.2);
    border: 3px solid rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    font-weight: 700;
    color: #fff;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}

.reg-profile-info h1 {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    letter-spacing: -0.5px;
}

.reg-profile-info p {
    margin: 0.5rem 0 0;
    font-size: 1.1rem;
    opacity: 0.9;
}

.reg-badge {
    background: rgba(255, 255, 255, 0.15);
    padding: 0.35rem 0.8rem;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    margin-top: 0.8rem;
    border: 1px solid rgba(255,255,255,0.3);
}

/* Glassmorphic Cards */
.reg-glass-card {
    background: var(--sms-surface);
    border-radius: 16px;
    border: 1px solid var(--sms-border);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    padding: 2rem;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
}

.reg-glass-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
}

.reg-card-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--sms-heading);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.reg-card-title i {
    color: var(--sms-primary);
}

/* Form Styles */
.reg-form-group {
    margin-bottom: 1.5rem;
}

.reg-form-label {
    display: block;
    font-weight: 500;
    color: var(--sms-text);
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.reg-form-control {
    width: 100%;
    padding: 0.8rem 1rem;
    border: 1px solid var(--sms-border);
    border-radius: 8px;
    background: var(--sms-surface-muted);
    color: var(--sms-text);
    font-size: 1rem;
    transition: all 0.2s ease;
}

.reg-form-control:focus {
    outline: none;
    border-color: var(--sms-primary);
    background: var(--sms-surface);
    box-shadow: 0 0 0 3px var(--sms-primary-xlight);
}

.reg-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.8rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
}

.reg-btn-primary {
    background: linear-gradient(135deg, var(--sms-primary) 0%, var(--sms-primary-dark) 100%);
    color: #fff;
    box-shadow: 0 4px 15px var(--sms-primary-xlight);
}

.reg-btn-primary:hover {
    background: var(--sms-primary-dark);
    box-shadow: 0 6px 20px var(--sms-primary-xlight);
    transform: translateY(-2px);
    color: #fff;
}

/* Security Requirements List */
.reg-security-req {
    background: var(--sms-surface-muted);
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    border-left: 4px solid var(--sms-primary);
}

.reg-security-req h6 {
    margin: 0 0 0.5rem 0;
    color: var(--sms-heading);
    font-size: 0.9rem;
    font-weight: 600;
}

.reg-security-req ul {
    margin: 0;
    padding-left: 1.2rem;
    color: var(--sms-text);
    font-size: 0.85rem;
}
.reg-security-req ul li {
    margin-bottom: 0.25rem;
}

/* Details list */
.reg-detail-item {
    display: flex;
    flex-direction: column;
    margin-bottom: 1.25rem;
    padding-bottom: 1.25rem;
    border-bottom: 1px solid var(--sms-border-soft);
}

.reg-detail-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.reg-detail-label {
    font-size: 0.85rem;
    color: var(--sms-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.reg-detail-value {
    font-size: 1.05rem;
    font-weight: 500;
    color: var(--sms-heading);
}
</style>

<div class="reg-profile-wrapper">
    <div class="container-fluid py-4">
        
        <!-- Alerts are now handled via popup (toast) at the bottom -->

        <!-- Header -->
        <div class="reg-profile-header">
            <div class="reg-profile-avatar-container">
                <div class="reg-profile-avatar">
                    <?php 
                        $initials = 'R';
                        if (!empty($userProfile['full_name'])) {
                            $parts = explode(' ', $userProfile['full_name']);
                            if (count($parts) > 1) {
                                $initials = strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
                            } else {
                                $initials = strtoupper(substr($parts[0], 0, 2));
                            }
                        }
                        echo htmlspecialchars($initials);
                    ?>
                </div>
                <div class="reg-profile-info">
                    <h1><?= htmlspecialchars($userProfile['full_name'] ?? 'Registrar User') ?></h1>
                    <p><?= htmlspecialchars($userProfile['email'] ?? 'registrar@example.com') ?></p>
                    <span class="reg-badge">
                        <i class="fas fa-shield-alt"></i> <?= htmlspecialchars(ucfirst($userProfile['role_key'] ?? 'Registrar')) ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="row g-4">
            <!-- Account Details Card -->
            <div class="col-lg-5">
                <div class="reg-glass-card">
                    <h2 class="reg-card-title"><i class="fas fa-id-card"></i> Account Information</h2>
                    
                    <div class="reg-detail-item">
                        <span class="reg-detail-label">Full Name</span>
                        <span class="reg-detail-value"><?= htmlspecialchars($userProfile['full_name'] ?? '—') ?></span>
                    </div>
                    
                    <div class="reg-detail-item">
                        <span class="reg-detail-label">Email Address</span>
                        <span class="reg-detail-value"><?= htmlspecialchars($userProfile['email'] ?? '—') ?></span>
                    </div>

                    <div class="reg-detail-item">
                        <span class="reg-detail-label">Account Role</span>
                        <span class="reg-detail-value"><?= htmlspecialchars(ucfirst($userProfile['role_key'] ?? '—')) ?></span>
                    </div>
                    
                    <div class="reg-detail-item">
                        <span class="reg-detail-label">Status</span>
                        <span class="reg-detail-value">
                            <span class="badge bg-success" style="font-size:0.85rem;"><i class="fas fa-check me-1"></i>Active</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Security / Password Card -->
            <div class="col-lg-7">
                <div class="reg-glass-card">
                    <h2 class="reg-card-title"><i class="fas fa-lock"></i> Security Settings</h2>
                    
                    <div class="reg-security-req">
                        <h6><i class="fas fa-info-circle me-1"></i> Password Requirements</h6>
                        <ul>
                            <li>Must be at least <strong>8 characters</strong> long</li>
                            <li>Must contain at least <strong>one capital letter</strong> (A-Z)</li>
                            <li>Must contain at least <strong>one symbol</strong> (!@#$%^&*)</li>
                        </ul>
                    </div>

                    <form action="" method="POST">
                        <?= csrfField() ?>
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="reg-form-group">
                            <label for="current_password" class="reg-form-label">Current Password</label>
                            <input type="password" id="current_password" name="current_password" class="reg-form-control" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label for="new_password" class="reg-form-label">New Password</label>
                                    <input type="password" id="new_password" name="new_password" class="reg-form-control" required>
                                    
                                    <div class="mt-2">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="text-muted" style="font-size:0.75rem;">Security Criteria Met</small>
                                            <small id="pw-percentage" class="fw-bold" style="font-size:0.75rem; color:#94a3b8;">0%</small>
                                        </div>
                                        <div class="progress" style="height: 6px; border-radius: 3px; background-color: #e2e8f0; overflow: hidden;">
                                            <div id="pw-progress-bar" class="progress-bar" role="progressbar" style="width: 0%; transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.4s ease;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="reg-form-group">
                                    <label for="confirm_password" class="reg-form-label">Confirm New Password</label>
                                    <input type="password" id="confirm_password" name="confirm_password" class="reg-form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="reg-btn reg-btn-primary">
                                <i class="fas fa-key"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
    </div>
</div>

<style>
/* Custom Toast Notification */
.reg-toast-container {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 10px;
    align-items: center;
}
.reg-toast {
    min-width: 320px;
    background: var(--sms-surface);
    border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 24px;
    transform: translateY(-150%);
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border-left: 4px solid var(--sms-border);
}
.reg-toast.show {
    transform: translateY(0);
    opacity: 1;
}
.reg-toast.success {
    border-left-color: #10b981;
}
.reg-toast.error {
    border-left-color: #ef4444;
}
.reg-toast-icon {
    font-size: 1.25rem;
}
.reg-toast.success .reg-toast-icon {
    color: #10b981;
}
.reg-toast.error .reg-toast-icon {
    color: #ef4444;
}
.reg-toast-message {
    font-weight: 500;
    color: var(--sms-heading);
    font-size: 0.95rem;
    flex-grow: 1;
}
.reg-toast-close {
    cursor: pointer;
    color: #94a3b8;
    background: none;
    border: none;
    padding: 0;
    font-size: 1.1rem;
    transition: color 0.2s;
}
.reg-toast-close:hover {
    color: #475569;
}
</style>

<div class="reg-toast-container" id="reg-toast-container"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const newPwInput = document.getElementById('new_password');
    const pwProgressBar = document.getElementById('pw-progress-bar');
    const pwPercentage = document.getElementById('pw-percentage');

    if(newPwInput) {
        newPwInput.addEventListener('input', function() {
            const val = this.value;
            let metCount = 0;
            
            // Criteria 1: At least 8 characters
            if (val.length >= 8) metCount++;
            // Criteria 2: At least one uppercase letter
            if (/[A-Z]/.test(val)) metCount++;
            // Criteria 3: At least one symbol
            if (/[^a-zA-Z0-9]/.test(val)) metCount++;

            let percentage = Math.round((metCount / 3) * 100);
            
            pwProgressBar.style.width = percentage + '%';
            pwProgressBar.setAttribute('aria-valuenow', percentage);
            pwPercentage.textContent = percentage + '%';

            // Update colors based on percentage
            if (percentage === 0) {
                pwProgressBar.style.backgroundColor = 'transparent';
                pwPercentage.style.color = '#94a3b8';
            } else if (percentage < 50) {
                pwProgressBar.style.backgroundColor = '#ef4444'; // Red
                pwPercentage.style.color = '#ef4444';
            } else if (percentage < 100) {
                pwProgressBar.style.backgroundColor = '#f59e0b'; // Amber/Orange
                pwPercentage.style.color = '#f59e0b';
            } else {
                pwProgressBar.style.backgroundColor = '#10b981'; // Green
                pwPercentage.style.color = '#10b981';
            }
        });
    }

    // Custom Toast Function
    window.showToast = function(type, message) {
        const container = document.getElementById('reg-toast-container');
        const toast = document.createElement('div');
        toast.className = 'reg-toast ' + type;
        
        const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        toast.innerHTML = `
            <div class="reg-toast-icon"><i class="fas ${iconClass}"></i></div>
            <div class="reg-toast-message">${message}</div>
            <button class="reg-toast-close" aria-label="Close"><i class="fas fa-times"></i></button>
        `;
        
        container.appendChild(toast);
        
        // Trigger reflow for animation
        toast.offsetHeight;
        toast.classList.add('show');
        
        const closeBtn = toast.querySelector('.reg-toast-close');
        
        const removeToast = () => {
            toast.classList.remove('show');
            setTimeout(() => {
                if(toast.parentNode === container) {
                    container.removeChild(toast);
                }
            }, 400); // Wait for transition
        };
        
        closeBtn.addEventListener('click', removeToast);
        
        // Auto-dismiss after 5 seconds
        setTimeout(removeToast, 5000);
    };

    <?php if ($error): ?>
    showToast('error', <?= json_encode($error) ?>);
    <?php endif; ?>

    <?php if ($success): ?>
    showToast('success', <?= json_encode($success) ?>);
    <?php endif; ?>
});
</script>

<?php require_once __DIR__ . '/../../../includes/layout-end.php'; ?>
