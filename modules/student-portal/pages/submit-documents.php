<?php
/**
 * SMS 2 - Student Portal: Submit Documents
 */
require_once __DIR__ . '/../../../config/config.php';
require_once ROOT_PATH . '/includes/authentication.php';

// Only Pre-Enrollees should ideally see this specific page as their main landing
$isPreEnrollee = ($_SESSION['role_key'] ?? '') === 'pre-enrollee';

$pageTitle = 'Document Upload Portal';
$activeModule = 'student_portal';
$activePage = 'submit-documents';

require_once ROOT_PATH . '/includes/layout-start.php';
require_once ROOT_PATH . '/includes/uploads.php';

$pdo = db();
$studentNumber = $_SESSION['student_id'] ?? '';
$userId = $_SESSION['user_id'] ?? 0;

$studentId = null;
if ($studentNumber) {
    $stmt = $pdo->prepare("SELECT id FROM reg_students WHERE student_number = ?");
    $stmt->execute([$studentNumber]);
    $studentId = $stmt->fetchColumn();
}

// Check if already uploaded
$alreadyUploaded = false;
if ($studentId) {
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM reg_files WHERE student_id = ?");
    $checkStmt->execute([$studentId]);
    if ($checkStmt->fetchColumn() > 0) {
        $alreadyUploaded = true;
    }
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$alreadyUploaded && $studentId) {
    $filesToUpload = [
        'form_138' => 'form_138',
        'good_moral' => 'good_moral',
        'birth_certificate' => 'psa_birth_cert',
        'id_picture' => 'id_picture'
    ];
    
    $success = true;
    
    foreach ($filesToUpload as $inputName => $category) {
        if (!empty($_FILES[$inputName]['name'])) {
            $uploadRes = smsSecureUpload($_FILES[$inputName], [
                'subdir' => 'credentials/' . $studentNumber
            ]);
            
            if ($uploadRes['ok']) {
                $insertFile = $pdo->prepare("
                    INSERT INTO reg_files (student_id, category, original_name, stored_name, mime, size, uploaded_by, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'Active')
                ");
                $insertFile->execute([
                    $studentId,
                    $category,
                    $uploadRes['original_name'],
                    $uploadRes['stored_name'],
                    $uploadRes['mime'],
                    $uploadRes['size'],
                    $userId
                ]);
            } else {
                $success = false;
                $errorMsg = $uploadRes['error'];
                break;
            }
        }
    }
    
    if ($success) {
        $alreadyUploaded = true;
    }
}
?>

<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-primary text-white p-4 rounded-top-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 rounded-circle p-3 me-3">
                            <i class="fas fa-file-upload fa-2x"></i>
                        </div>
                        <div>
                            <h3 class="mb-1 fw-bold">Document Upload Portal</h3>
                            <p class="mb-0 text-white-50">Upload your required credentials to complete enrollment.</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4 p-md-5">
                    <?php if ($alreadyUploaded): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle text-success mb-3" style="font-size: 4rem;"></i>
                            <h4 class="fw-bold text-dark mb-3">Documents Successfully Uploaded</h4>
                            <p class="text-muted mb-0" style="font-size: 1.1rem;">Please wait. The Registrar is currently validating your information and will notify you via email with more details.</p>
                        </div>
                    <?php else: ?>
                        <?php if ($isPreEnrollee): ?>
                            <div class="alert alert-warning border-warning shadow-sm rounded-3 mb-4">
                                <h5 class="fw-bold text-dark"><i class="fas fa-clock text-warning me-2"></i>Action Required</h5>
                                <p class="mb-0 text-dark">You have <strong>7 days</strong> to upload these documents. Failure to do so will result in the cancellation of your registration and automatic deletion of this pre-account.</p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($errorMsg)): ?>
                            <div class="alert alert-danger mb-4"><?= htmlspecialchars($errorMsg) ?></div>
                        <?php endif; ?>

                        <form action="" method="POST" enctype="multipart/form-data">
                            <h5 class="text-secondary fw-bold border-bottom pb-2 mb-4">Required Documents</h5>
                            
                            <!-- Form 138 -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Form 138 (Report Card) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-id-card text-muted"></i></span>
                                    <input type="file" class="form-control" name="form_138" required accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                <div class="form-text">Please upload a clear scanned copy. Max 5MB.</div>
                            </div>

                            <!-- Good Moral Character -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Certificate of Good Moral Character <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-certificate text-muted"></i></span>
                                    <input type="file" class="form-control" name="good_moral" required accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                <div class="form-text">Must be originally signed by your previous school's principal or guidance counselor.</div>
                            </div>

                            <!-- PSA Birth Certificate -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">PSA Birth Certificate <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-file-signature text-muted"></i></span>
                                    <input type="file" class="form-control" name="birth_certificate" required accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                            </div>

                            <!-- 2x2 Picture -->
                            <div class="mb-5">
                                <label class="form-label fw-bold">2x2 ID Picture <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-camera text-muted"></i></span>
                                    <input type="file" class="form-control" name="id_picture" required accept=".jpg,.jpeg,.png">
                                </div>
                                <div class="form-text">White background, formal attire.</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm">
                                    <i class="fas fa-cloud-upload-alt me-2"></i> Submit Documents
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/includes/layout-end.php'; ?>
