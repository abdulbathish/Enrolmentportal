<?php
require_once './constants.php';
require_once './helpers/jwt-verifier.php';

// Force debug mode for this test
define('JWT_TEST_DEBUG_MODE', true);

echo "<!DOCTYPE html>";
echo "<html><head><title>JWT Verification Test</title></head><body>";
echo "<h1>JWT Verification Test</h1>";
echo "<p>This page tests the JWT verification functionality with the eSignet JWKS.</p>";

// Test 1: Fetch JWKS
echo "<h2>Test 1: Fetching JWKS</h2>";
try {
    echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
    JwtVerifier::debugLog("Starting JWKS fetch test");
    
    $jwks = JwtVerifier::fetchJwks();
    
    echo "<p><strong>JWKS fetched successfully!</strong></p>";
    echo "<p>Number of keys: " . count($jwks['keys']) . "</p>";
    
    // Display key information
    foreach ($jwks['keys'] as $index => $key) {
        echo "<div style='background: #f9f9f9; padding: 5px; margin: 5px 0;'>";
        echo "<strong>Key " . ($index + 1) . ":</strong><br>";
        echo "Kid: " . (isset($key['kid']) ? $key['kid'] : 'N/A') . "<br>";
        echo "Algorithm: " . (isset($key['alg']) ? $key['alg'] : 'N/A') . "<br>";
        echo "Key Type: " . (isset($key['kty']) ? $key['kty'] : 'N/A') . "<br>";
        echo "Use: " . (isset($key['use']) ? $key['use'] : 'N/A') . "<br>";
        echo "</div>";
    }
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='border: 1px solid #f00; padding: 10px; margin: 10px 0; background: #ffe6e6;'>";
    echo "<p><strong>JWKS fetch failed:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

// Test 2: Test configuration
echo "<h2>Test 2: Configuration Check</h2>";
echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
echo "<p><strong>JWKS URL:</strong> " . ESIGNET_JWKS_URL . "</p>";
echo "<p><strong>Default Algorithm:</strong> " . ESIGNET_JWT_DEFAULT_ALG . "</p>";
echo "<p><strong>Debug Mode:</strong> " . (JWT_DEBUG_MODE ? 'Enabled' : 'Disabled') . "</p>";
echo "<p><strong>eSignet Service URL:</strong> " . ESIGNET_SERVICE_URL . "</p>";
echo "<p><strong>Client ID:</strong> " . CLIENT_ID . "</p>";
echo "</div>";

// Test 3: Manual JWT test (if provided)
echo "<h2>Test 3: Manual JWT Verification</h2>";
echo "<form method='post' style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
echo "<p>You can test JWT verification by pasting a JWT token here:</p>";
echo "<textarea name='test_jwt' rows='5' cols='80' placeholder='Paste JWT token here...'>" . (isset($_POST['test_jwt']) ? htmlspecialchars($_POST['test_jwt']) : '') . "</textarea><br><br>";
echo "<input type='submit' value='Verify JWT' style='padding: 10px 20px; background: #007cba; color: white; border: none; cursor: pointer;'>";
echo "</form>";

if (isset($_POST['test_jwt']) && !empty($_POST['test_jwt'])) {
    $testJwt = trim($_POST['test_jwt']);
    echo "<h3>JWT Verification Result:</h3>";
    echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
    
    JwtVerifier::debugLog("Testing manual JWT verification");
    $result = JwtVerifier::verifyAndDecodeJwt($testJwt);
    
    if ($result['error'] !== null) {
        echo "<p><strong>JWT Verification Failed:</strong> " . htmlspecialchars($result['error']['message']) . "</p>";
    } else {
        echo "<p><strong>JWT Verification Successful!</strong></p>";
        echo "<pre>" . htmlspecialchars(json_encode($result['data'], JSON_PRETTY_PRINT)) . "</pre>";
    }
    echo "</div>";
}

// Test 4: Show recent debug logs from error log
echo "<h2>Test 4: Recent Debug Logs</h2>";
echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
echo "<p>Debug information has been logged to the PHP error log and displayed above in real-time.</p>";
echo "<p>Check your PHP error log for persistent debug information.</p>";
echo "</div>";

echo "<h2>Usage Instructions</h2>";
echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
echo "<ol>";
echo "<li><strong>Login Flow:</strong> Use the normal login process via <a href='login.php'>login.php</a> to see JWT verification in action.</li>";
echo "<li><strong>eSignet Login:</strong> When you click 'Sign in with e-Signet', the system will:</li>";
echo "<ul>";
echo "<li>Create and sign a client assertion JWT</li>";
echo "<li>Exchange authorization code for access token</li>";
echo "<li>Verify ID token if present</li>";
echo "<li>Fetch and verify user info JWT</li>";
echo "</ul>";
echo "<li><strong>Debug Mode:</strong> Set JWT_DEBUG_MODE to false in constants.php to disable debug output in production.</li>";
echo "<li><strong>JWKS Caching:</strong> JWKS are cached for 1 hour to improve performance.</li>";
echo "</ol>";
echo "</div>";

echo "<h2>Security Notes</h2>";
echo "<div style='border: 1px solid #f90; padding: 10px; margin: 10px 0; background: #fff9e6;'>";
echo "<ul>";
echo "<li><strong>Debug Mode:</strong> Disable JWT_DEBUG_MODE in production to prevent sensitive data exposure.</li>";
echo "<li><strong>SSL/TLS:</strong> Ensure all OIDC endpoints use HTTPS in production.</li>";
echo "<li><strong>Key Rotation:</strong> The system automatically fetches fresh JWKS and handles key rotation.</li>";
echo "<li><strong>Token Expiration:</strong> All JWTs are validated for expiration and issue time.</li>";
echo "</ul>";
echo "</div>";

echo "</body></html>";
?> 