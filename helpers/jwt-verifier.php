<?php
require __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\JWK;

class JwtVerifier {
    private static $jwksCache = null;
    private static $jwksCacheTime = null;
    private const JWKS_CACHE_DURATION = 3600; // 1 hour

    /**
     * Debug logging function
     */
    public static function debugLog($message, $data = null) {
        // Only log if debug mode is enabled
        if (!defined('JWT_DEBUG_MODE') || !JWT_DEBUG_MODE) {
            return;
        }

        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] JWT Debug: $message";
        
        if ($data !== null) {
            $logMessage .= " | Data: " . json_encode($data);
        }
        
        // Log to both error log and display on screen for debugging
        error_log($logMessage);
        
        // Display on screen if we're in a web context (you can disable this in production)
        if (!headers_sent()) {
            echo "<div style='background: #f0f0f0; padding: 10px; margin: 5px; border: 1px solid #ccc; font-family: monospace; font-size: 12px;'>";
            echo htmlspecialchars($logMessage);
            echo "</div>";
            flush();
        }
    }

    /**
     * Normalize JWKS by ensuring all keys have required parameters
     */
    public static function normalizeJwks($jwks) {
        self::debugLog("Normalizing JWKS", ['original_key_count' => count($jwks['keys'])]);
        
        $normalizedKeys = [];
        foreach ($jwks['keys'] as $key) {
            $normalizedKey = $key;
            
            // Add default algorithm if missing
            if (!isset($normalizedKey['alg']) || empty($normalizedKey['alg'])) {
                $normalizedKey['alg'] = ESIGNET_JWT_DEFAULT_ALG;
                self::debugLog("Added default algorithm to key", [
                    'kid' => isset($key['kid']) ? $key['kid'] : 'unknown',
                    'alg' => $normalizedKey['alg']
                ]);
            }
            
            // Ensure use parameter is set (typically 'sig' for signature verification)
            if (!isset($normalizedKey['use'])) {
                $normalizedKey['use'] = 'sig';
            }
            
            // Ensure key type is set
            if (!isset($normalizedKey['kty'])) {
                $normalizedKey['kty'] = 'RSA'; // Default to RSA
            }
            
            $normalizedKeys[] = $normalizedKey;
        }
        
        $normalizedJwks = ['keys' => $normalizedKeys];
        self::debugLog("JWKS normalization completed", ['normalized_key_count' => count($normalizedKeys)]);
        
        return $normalizedJwks;
    }

    /**
     * Fetch JWKS from the well-known URL with caching
     */
    public static function fetchJwks() {
        // Check cache first
        if (self::$jwksCache !== null && 
            self::$jwksCacheTime !== null && 
            (time() - self::$jwksCacheTime) < self::JWKS_CACHE_DURATION) {
            self::debugLog("Using cached JWKS");
            return self::$jwksCache;
        }

        self::debugLog("Fetching JWKS from URL", ['url' => ESIGNET_JWKS_URL]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, ESIGNET_JWKS_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            self::debugLog("CURL error fetching JWKS", ['error' => $error]);
            throw new Exception("Failed to fetch JWKS: " . $error);
        }

        if ($httpCode !== 200) {
            self::debugLog("HTTP error fetching JWKS", ['status_code' => $httpCode, 'response' => $response]);
            throw new Exception("Failed to fetch JWKS: HTTP $httpCode");
        }

        $jwks = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            self::debugLog("JSON decode error for JWKS", ['error' => json_last_error_msg()]);
            throw new Exception("Invalid JWKS JSON: " . json_last_error_msg());
        }

        if (!isset($jwks['keys']) || !is_array($jwks['keys'])) {
            self::debugLog("Invalid JWKS structure", ['jwks' => $jwks]);
            throw new Exception("Invalid JWKS structure: missing keys array");
        }

        // Normalize the JWKS before caching
        $normalizedJwks = self::normalizeJwks($jwks);

        // Cache the result
        self::$jwksCache = $normalizedJwks;
        self::$jwksCacheTime = time();

        self::debugLog("Successfully fetched and cached JWKS", ['key_count' => count($normalizedJwks['keys'])]);
        return $normalizedJwks;
    }

    /**
     * Get JWT header without verification
     */
    public static function getJwtHeader($jwt) {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            throw new Exception("Invalid JWT format");
        }

        $header = json_decode(base64_decode($parts[0]), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JWT header: " . json_last_error_msg());
        }

        return $header;
    }

    /**
     * Verify and decode JWT using JWKS
     */
    public static function verifyAndDecodeJwt($jwt, $validateExpiration = true) {
        try {
            self::debugLog("Starting JWT verification", ['jwt_preview' => substr($jwt, 0, 50) . '...']);

            // Get JWT header to find the key ID
            $header = self::getJwtHeader($jwt);
            self::debugLog("JWT header decoded", ['header' => $header]);

            $kid = isset($header['kid']) ? $header['kid'] : null;
            $alg = isset($header['alg']) ? $header['alg'] : ESIGNET_JWT_DEFAULT_ALG;

            self::debugLog("JWT key info", ['kid' => $kid, 'algorithm' => $alg]);

            // Fetch JWKS
            $jwks = self::fetchJwks();

            // Find the matching key
            $matchingKey = null;
            foreach ($jwks['keys'] as $key) {
                self::debugLog("Checking key", [
                    'key_kid' => isset($key['kid']) ? $key['kid'] : 'N/A',
                    'key_alg' => isset($key['alg']) ? $key['alg'] : 'N/A',
                    'target_kid' => $kid,
                    'target_alg' => $alg
                ]);

                // Match by kid if available, otherwise use the first suitable key
                if ($kid === null || !isset($key['kid']) || $key['kid'] === $kid) {
                    // Check algorithm compatibility
                    if (isset($key['alg']) && $key['alg'] === $alg) {
                        $matchingKey = $key;
                        break;
                    }
                    // Fallback: if no specific algorithm requirement, use the key
                    if (!isset($key['alg']) || $key['alg'] === ESIGNET_JWT_DEFAULT_ALG) {
                        $matchingKey = $key;
                        break;
                    }
                }
            }

            if ($matchingKey === null) {
                self::debugLog("No matching key found", ['kid' => $kid, 'algorithm' => $alg, 'available_keys' => count($jwks['keys'])]);
                throw new Exception("No matching key found for kid: $kid and algorithm: $alg");
            }

            self::debugLog("Found matching key", [
                'key_id' => isset($matchingKey['kid']) ? $matchingKey['kid'] : 'N/A',
                'key_alg' => isset($matchingKey['alg']) ? $matchingKey['alg'] : 'N/A'
            ]);

            // Convert JWKS key to PEM format using Firebase JWT library
            try {
                $keySet = JWK::parseKeySet($jwks);
                self::debugLog("Successfully parsed JWKS keyset");
            } catch (Exception $e) {
                self::debugLog("Failed to parse JWKS keyset", ['error' => $e->getMessage()]);
                throw new Exception("Failed to parse JWKS: " . $e->getMessage());
            }
            
            // Verify the JWT
            try {
                $decoded = JWT::decode($jwt, $keySet);
                self::debugLog("JWT verification successful", ['subject' => isset($decoded->sub) ? $decoded->sub : 'unknown']);
            } catch (Exception $e) {
                self::debugLog("JWT decode failed", ['error' => $e->getMessage()]);
                throw new Exception("JWT signature verification failed: " . $e->getMessage());
            }

            // Convert to array for easier handling
            $payload = json_decode(json_encode($decoded), true);

            // Additional validation
            if ($validateExpiration && isset($payload['exp']) && $payload['exp'] < time()) {
                self::debugLog("JWT expired", ['exp' => $payload['exp'], 'current_time' => time()]);
                throw new Exception("JWT has expired");
            }

            if (isset($payload['iat']) && $payload['iat'] > time() + 300) { // Allow 5 minutes clock skew
                self::debugLog("JWT issued in future", ['iat' => $payload['iat'], 'current_time' => time()]);
                throw new Exception("JWT issued in the future");
            }

            self::debugLog("JWT validation completed successfully");
            return [
                'data' => $payload,
                'error' => null
            ];

        } catch (Exception $e) {
            self::debugLog("JWT verification failed", ['error' => $e->getMessage()]);
            return [
                'data' => null,
                'error' => [
                    'message' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Verify access token response from token endpoint
     */
    public static function verifyTokenResponse($tokenResponse) {
        self::debugLog("Verifying token response");
        
        $tokenData = json_decode($tokenResponse, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            self::debugLog("Invalid token response JSON", ['error' => json_last_error_msg()]);
            return [
                'data' => null,
                'error' => ['message' => 'Invalid token response JSON']
            ];
        }

        self::debugLog("Token response parsed", [
            'has_access_token' => isset($tokenData['access_token']),
            'has_id_token' => isset($tokenData['id_token']),
            'token_type' => isset($tokenData['token_type']) ? $tokenData['token_type'] : 'N/A'
        ]);

        // If there's an ID token, verify it
        if (isset($tokenData['id_token'])) {
            self::debugLog("Verifying ID token from token response");
            $idTokenResult = self::verifyAndDecodeJwt($tokenData['id_token']);
            if ($idTokenResult['error'] !== null) {
                self::debugLog("ID token verification failed", ['error' => $idTokenResult['error']]);
                return $idTokenResult;
            }
            $tokenData['id_token_decoded'] = $idTokenResult['data'];
        }

        // Note: Access tokens are typically opaque and not meant to be verified by clients
        // They should be verified by the resource server (userinfo endpoint)
        
        self::debugLog("Token response verification completed");
        return [
            'data' => $tokenData,
            'error' => null
        ];
    }

    /**
     * Verify userinfo JWT response
     */
    public static function verifyUserInfoJwt($userInfoJwt) {
        self::debugLog("Verifying userinfo JWT");
        return self::verifyAndDecodeJwt($userInfoJwt);
    }
}
?> 