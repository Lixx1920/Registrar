<?php
/**
 * Quick setup script for Registrar Phase 2 services
 */
require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/includes/security.php';

require_once __DIR__ . '/includes/signing-service.php';

echo "🔧 Initializing Registrar Phase 2 Services\n\n";

// Initialize RSA keys
echo "1. Initializing RSA keys...";
if (regInitializeKeys()) {
    echo " ✅\n";
} else {
    echo " ❌\n";
    exit(1);
}

// Verify keys exist
echo "2. Verifying keys...";
if (file_exists(REG_PRIVATE_KEY_FILE) && file_exists(REG_PUBLIC_KEY_FILE)) {
    echo " ✅\n";
} else {
    echo " ❌\n";
    exit(1);
}

echo "\n✅ Phase 2 initialization complete!\n";
?>
