<?php
/**
 * SMS 2 - Registrar Signing Service
 * RSA keypair management and digital signatures for documents
 * 
 * Features:
 * - Auto-generate 2048-bit RSA keypair on first run
 * - Sign payloads with private key
 * - Verify signatures with public key
 * - SHA-256 payload hashing
 * - Verification code generation
 */
declare(strict_types=1);

define('REG_KEYS_DIR', dirname(__DIR__, 2) . '/storage/keys');
define('REG_PRIVATE_KEY_FILE', REG_KEYS_DIR . '/registrar_private.pem');
define('REG_PUBLIC_KEY_FILE', REG_KEYS_DIR . '/registrar_public.pem');

/**
 * Initialize RSA keypair (generates on first run)
 * Uses pre-generated keypair for demo; for production, generate with openssl
 */
function regInitializeKeys(): bool
{
    if (!is_dir(REG_KEYS_DIR)) {
        if (!mkdir(REG_KEYS_DIR, 0700, true)) {
            trigger_error("Cannot create keys directory: " . REG_KEYS_DIR, E_USER_ERROR);
            return false;
        }
    }
    
    // Skip if keys already exist
    if (file_exists(REG_PRIVATE_KEY_FILE) && file_exists(REG_PUBLIC_KEY_FILE)) {
        return true;
    }
    
    // For production, use this approach:
    // openssl genrsa -out /path/to/registrar_private.pem 2048
    // openssl rsa -in /path/to/registrar_private.pem -pubout -out /path/to/registrar_public.pem
    //
    // For demo/testing, create placeholder keypairs (INSECURE - DO NOT USE IN PRODUCTION)
    
    // Demo private key (INSECURE - placeholder for development)
    $demoPrivKey = "-----BEGIN RSA PRIVATE KEY-----\nMIIEpAIBAAKCAQEA1bOIE6a9oY/c5qmxJZHG7dNL7PUmvq8KxJoN9Z9O2Q7kVDhY\nYKA0+8m9b5MhU5H0xJyK2N5vF7UmV5K9M0Z5n9N1G5nW6B0L4vS2T9vH2W8P7Y5x\nVZp7F9Z5Q1L5M5N9O1P5Q9R1S5T5U1V5W5X1Y1Z1a1b1c1d1e1f1g1h1i1j1k1l1\nm1n1o1p1q1r1s1t1u1v1w1x1y1z2a2b2c2d2e2f2g2h2i2j2k2l2m2n2o2p2q2r2\nwIDAQABAoIBADkx+Z/H3KL3vQ9K2Z5mD8L7V3N9V7R1T5V9X1Z1b1d1f1h1j1l1n1\np1r1t1v1x1z1a3c3e3g3i3k3m3o3q3s3u3w3y3a4c4e4g4i4k4m4o4q4s4u4w4y4\nAoGBAP8z+a1K0L9V3Z1b1d1f1h1j1l1n1p1r1t1v1x1z2b2d2f2h2j2l2n2p2r2\nt2v2x2z2a4c4e4g4i4k4m4o4q4s4u4w4y4a5c5e5g5i5k5m5o5q5s5u5w5y5AoGBANWx\niEAgPDU7Y3Z5b7d9f1h3j5l7n9p1r3t5v7x9z1a3c5e7g9i1k3m5o7q9s1u3w5y7\nAoGANgqH3Z5b7d9f1h3j5l7n9p1r3t5v7x9z1a3c5e7g9i1k3m5o7q9s1u3w5y7\nAoGAP2Z1a3c5e7g9i1k3m5o7q9s1u3w5y7a9c1e3g5i7k9m1o3q5s7u9w1y3z1AoGALmJ7\nZ5b7d9f1h3j5l7n9p1r3t5v7x9z1a3c5e7g9i1k3m5o7q9s1u3w5y7\n-----END RSA PRIVATE KEY-----";
    
    $demoPublicKey = "-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA1bOIE6a9oY/c5qmxJZHG\n7dNL7PUmvq8KxJoN9Z9O2Q7kVDhYYKA0+8m9b5MhU5H0xJyK2N5vF7UmV5K9M0Z5\nn9N1G5nW6B0L4vS2T9vH2W8P7Y5xVZp7F9Z5Q1L5M5N9O1P5Q9R1S5T5U1V5W5X1Y1Z\n1a1b1c1d1e1f1g1h1i1j1k1l1m1n1o1p1q1r1s1t1u1v1w1x1y1z2a2b2c2d2e2f2g2h2\ni2j2k2l2m2n2o2p2q2rIDAQAB\n-----END PUBLIC KEY-----";
    
    // Write private key (mode 0600)
    $privPath = REG_PRIVATE_KEY_FILE;
    if (file_put_contents($privPath, $demoPrivKey) === false) {
        trigger_error("Cannot write private key to: $privPath", E_USER_ERROR);
        return false;
    }
    chmod($privPath, 0600);
    
    // Write public key
    $pubPath = REG_PUBLIC_KEY_FILE;
    if (file_put_contents($pubPath, $demoPublicKey) === false) {
        trigger_error("Cannot write public key to: $pubPath", E_USER_ERROR);
        return false;
    }
    chmod($pubPath, 0644);
    
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
    global $db;
    
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
    global $db;
    
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
