<?php
/**
 * SMS 2 - Registrar Signing Service
 * RSA keypair management and digital signatures for documents
 * 
 * Features:
 * - Auto-generate 2048-bit RSA keypair on first run (real keys, openssl_pkey_new)
 * - Sign payloads with private key
 * - Verify signatures with public key
 * - SHA-256 payload hashing
 * - Verification code generation
 */
declare(strict_types=1);

if (!defined('ROOT_PATH')) {
    require_once dirname(__DIR__, 3) . '/config/config.php';
}
require_once ROOT_PATH . '/config/database.php';

if (!defined('REG_KEYS_DIR')) {
    define('REG_KEYS_DIR', ROOT_PATH . '/storage/keys');
}
if (!defined('REG_PRIVATE_KEY_FILE')) {
    define('REG_PRIVATE_KEY_FILE', REG_KEYS_DIR . '/registrar_private.pem');
}
if (!defined('REG_PUBLIC_KEY_FILE')) {
    define('REG_PUBLIC_KEY_FILE', REG_KEYS_DIR . '/registrar_public.pem');
}

/**
 * Locate an OpenSSL config file (required on Windows / XAMPP, where
 * openssl_pkey_new() otherwise fails with "No such process").
 */
function regFindOpensslConfig(): ?string
{
    // Common XAMPP locations + typical defaults.
    $candidates = [
        'C:/xampp/php/extras/openssl/openssl.cnf',
        'C:/xampp/apache/conf/openssl.cnf',
        'C:/xampp/php/extras/ssl/openssl.cnf',
        '/etc/ssl/openssl.cnf',
        '/etc/pki/tls/openssl.cnf',
    ];
    if (defined('PHP_OPENSSL_CONF') && PHP_OPENSSL_CONF !== '') {
        array_unshift($candidates, PHP_OPENSSL_CONF);
    }
    foreach ($candidates as $c) {
        if (is_readable($c)) {
            return $c;
        }
    }
    return null;
}

/**
 * Initialize RSA keypair (generates a real 2048-bit keypair on first run).
 * Keys live in storage/keys/ (gitignored). Private key chmod 0600.
 */
function regInitializeKeys(): bool
{
    if (!is_dir(REG_KEYS_DIR)) {
        if (!mkdir(REG_KEYS_DIR, 0700, true) && !is_dir(REG_KEYS_DIR)) {
            error_log('Registrar signing: cannot create keys directory: ' . REG_KEYS_DIR);
            return false;
        }
    }

    // Skip if keys already exist
    if (file_exists(REG_PRIVATE_KEY_FILE) && file_exists(REG_PUBLIC_KEY_FILE)) {
        return true;
    }

    if (!function_exists('openssl_pkey_new')) {
        error_log('Registrar signing: OpenSSL extension is not available.');
        return false;
    }

    // Generate a real RSA-2048 keypair with SHA-256 digest support.
    // On Windows/XAMPP always point at openssl.cnf to avoid "No such process".
    $config = [
        'private_key_bits' => 2048,
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
        'digest_alg'       => 'sha256',
    ];
    $cnf = regFindOpensslConfig();
    if ($cnf !== null) {
        $config['config'] = $cnf;
    }

    $res = openssl_pkey_new($config);
    if ($res === false) {
        error_log('Registrar signing: openssl_pkey_new failed: ' . (string) openssl_error_string());
        return false;
    }

    // Export private key with the same config
    if (!openssl_pkey_export($res, $privKeyPem, null, $config)) {
        error_log('Registrar signing: could not export private key. OpenSSL error: ' . (string) openssl_error_string());
        return false;
    }
    $details = openssl_pkey_get_details($res);
    $pubKeyPem = is_array($details) ? ($details['key'] ?? '') : '';

    if ($privKeyPem === '' || $pubKeyPem === '') {
        error_log('Registrar signing: key export produced empty output.');
        return false;
    }

    // Write private key (mode 0600)
    if (file_put_contents(REG_PRIVATE_KEY_FILE, $privKeyPem, LOCK_EX) === false) {
        error_log('Registrar signing: cannot write private key to: ' . REG_PRIVATE_KEY_FILE);
        return false;
    }
    @chmod(REG_PRIVATE_KEY_FILE, 0600);

    // Write public key
    if (file_put_contents(REG_PUBLIC_KEY_FILE, $pubKeyPem, LOCK_EX) === false) {
        error_log('Registrar signing: cannot write public key to: ' . REG_PUBLIC_KEY_FILE);
        return false;
    }
    @chmod(REG_PUBLIC_KEY_FILE, 0644);

    return true;
}

/**
 * Load private key for signing
 */
function regGetPrivateKey()
{
    if (!file_exists(REG_PRIVATE_KEY_FILE)) {
        return null;
    }
    
    $keyData = file_get_contents(REG_PRIVATE_KEY_FILE);
    return openssl_pkey_get_private($keyData);
}

/**
 * Load public key for verification
 */
function regGetPublicKey()
{
    if (!file_exists(REG_PUBLIC_KEY_FILE)) {
        return null;
    }
    
    $keyData = file_get_contents(REG_PUBLIC_KEY_FILE);
    return openssl_pkey_get_public($keyData);
}

/**
 * Sign a payload with RSA private key
 * 
 * Returns: ['success' => true, 'signature' => base64_encoded_sig] or ['success' => false, 'error' => str]
 */
function regSignPayload(string $payload): array
{
    $privKey = regGetPrivateKey();
    if (!$privKey) {
        return ['success' => false, 'error' => 'Private key not found'];
    }
    
    if (!openssl_sign($payload, $signature, $privKey, OPENSSL_ALGO_SHA256)) {
        return ['success' => false, 'error' => 'Signing failed: ' . openssl_error_string()];
    }
    
    return ['success' => true, 'signature' => base64_encode($signature)];
}

/**
 * Verify a signature with RSA public key
 * 
 * Returns: ['valid' => true] or ['valid' => false, 'error' => str]
 */
function regVerifySignature(string $payload, string $signature): array
{
    $pubKey = regGetPublicKey();
    if (!$pubKey) {
        return ['valid' => false, 'error' => 'Public key not found'];
    }
    
    $sig = base64_decode($signature, true);
    if ($sig === false) {
        return ['valid' => false, 'error' => 'Invalid signature encoding'];
    }
    
    $result = openssl_verify($payload, $sig, $pubKey, OPENSSL_ALGO_SHA256);
    
    if ($result === -1) {
        return ['valid' => false, 'error' => 'Verification error: ' . openssl_error_string()];
    }
    
    return ['valid' => $result === 1];
}

/**
 * Hash a file with SHA-256
 */
function regHashFile(string $filePath): ?string
{
    if (!file_exists($filePath)) {
        return null;
    }
    
    return hash_file('sha256', $filePath);
}

/**
 * Hash content with SHA-256
 */
function regHashContent(string $content): string
{
    return hash('sha256', $content);
}

/**
 * Generate a human-friendly verification code
 * Format: BCP137-XXXXX (college prefix + 5-char alphanumeric)
 */
function regGenerateVerificationCode(string $prefix = 'BCP'): string
{
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // Exclude I, O, 0, 1 for clarity
    $code = '';
    for ($i = 0; $i < 5; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $prefix . substr(date('y'), -2) . '-' . $code;
}

/**
 * Create signed document verification record
 * 
 * Returns: ['success' => true, 'code' => str, 'code_id' => int] or ['success' => false, 'error' => str]
 */
function regCreateVerification(
    string $docHash,
    string $docType,
    ?int $studentId = null,
    ?string $payload = null
): array {
    $db = db();
    
    // Generate verification code
    $verificationCode = regGenerateVerificationCode();
    
    // Sign payload if provided
    $signature = null;
    if ($payload) {
        $signResult = regSignPayload($payload);
        if (!$signResult['success']) {
            return ['success' => false, 'error' => $signResult['error']];
        }
        $signature = $signResult['signature'];
    }
    
    // Store verification record
    try {
        $db = db();
        $stmt = $db->prepare("INSERT INTO `reg_verification_codes` 
            (`doc_hash`, `verification_code`, `signed_payload`, `signature`, `doc_type`, `student_id`) 
            VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$docHash, $verificationCode, $payload, $signature, $docType, $studentId]);
        $codeId = (int)$db->lastInsertId();
    } catch (Throwable $e) {
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
    
    return ['success' => true, 'code' => $verificationCode, 'code_id' => $codeId];
}

/**
 * Get verification record by code
 */
function regGetVerificationByCode(string $code): ?array
{
    $db = db();
    
    $stmt = $db->prepare("SELECT * FROM `reg_verification_codes` WHERE `verification_code` = ?");
    $stmt->execute([$code]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($record) {
        // Update verification stats
        try {
            $stmt = $db->prepare("UPDATE `reg_verification_codes` 
                SET `verified_count` = `verified_count` + 1, `last_verified_at` = NOW() 
                WHERE `id` = ?");
            $stmt->execute([$record['id']]);
        } catch (Throwable) {
            // Ignore stats update errors
        }
    }
    
    return $record;
}

/**
 * Verify a document by code and hash
 * 
 * Returns: ['valid' => true, 'document' => array] or ['valid' => false, 'reason' => str]
 */
function regVerifyDocumentByCode(string $code, string $docHash): array
{
    $record = regGetVerificationByCode($code);
    if (!$record) {
        return ['valid' => false, 'reason' => 'Verification code not found'];
    }
    
    // Verify hash matches
    if (!hash_equals($record['doc_hash'], $docHash)) {
        return ['valid' => false, 'reason' => 'Document hash does not match'];
    }
    
    // Verify signature if present
    if ($record['signed_payload'] && $record['signature']) {
        $verResult = regVerifySignature($record['signed_payload'], $record['signature']);
        if (!$verResult['valid']) {
            return ['valid' => false, 'reason' => 'Signature verification failed: ' . ($verResult['error'] ?? 'Unknown error')];
        }
    }
    
    return ['valid' => true, 'document' => $record];
}
?>
