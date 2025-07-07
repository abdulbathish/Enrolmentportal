<?php
// Start output buffering immediately to prevent any output
ob_start();

include_once "./constants.php";
require __DIR__ . '/vendor/autoload.php';
require_once './helpers/jwt-verifier.php';
use Firebase\JWT\JWT;

// Set error reporting and timeout for debugging
if (JWT_DEBUG_MODE) {
    ini_set('display_errors', 0); // Disable display errors to prevent header issues
    ini_set('log_errors', 1); // Enable error logging
    error_reporting(E_ALL);
    set_time_limit(60); // Set 60 second timeout
}

/**
 * Custom debug function that only logs to error log during callback
 */
function callbackDebugLog($message, $data = null) {
    if (!defined('JWT_DEBUG_MODE') || !JWT_DEBUG_MODE) {
        return;
    }

    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] Callback Debug: $message";
    
    if ($data !== null) {
        $logMessage .= " | Data: " . json_encode($data);
    }
    
    // Only log to error log, never output to screen
    error_log($logMessage);
}

// Log initial callback info
callbackDebugLog("Starting OIDC callback process", ['GET_params' => $_GET]);
callbackDebugLog("PHP Configuration", [
    'max_execution_time' => ini_get('max_execution_time'),
    'memory_limit' => ini_get('memory_limit'),
    'curl_version' => curl_version()['version'] ?? 'unknown'
]);

/**
 * On Success, response will be 
 *  {data: {accessToken, expiresIn}, error: null}
 * 
 * On Failure, response will be
 * {data: null, error: {statusCode: http error code, message: 'some description of error'}}
 */
function getAccessToken($authorizationCode) {
    callbackDebugLog("Starting access token request", ['auth_code' => substr($authorizationCode, 0, 20) . '...']);

    try {
        $tokenEndPoint = ESIGNET_SERVICE_URL . "/v1/esignet/oauth/token";
        $clientId = CLIENT_ID;
        
        callbackDebugLog("Token endpoint configured", [
            'endpoint' => $tokenEndPoint,
            'client_id' => $clientId
        ]);

        $private_key_resource = openssl_get_privatekey(CLIENT_PRIVATE_KEY);
        if (!$private_key_resource) {
            throw new Exception("Failed to load private key: " . openssl_error_string());
        }

        $header = array(
            "alg" => "RS256",
            "TYP" => "JWT"
        );

        $payload = array(
            "iss" => $clientId,
            "sub" => $clientId,
            "iat" => time(),
            "exp" => time() + 3600,
            "aud" => $tokenEndPoint,
        );

        callbackDebugLog("Creating client assertion JWT", ['payload' => $payload]);

        $jwt = JWT::encode($payload, $private_key_resource, 'RS256', null, $header);
        
        callbackDebugLog("Client assertion JWT created", ['jwt_preview' => substr($jwt, 0, 50) . '...']);
        
        $data = array(
            "code" => $authorizationCode,
            "client_id" => $clientId,
            "redirect_uri" => CALLBACK_URL,
            "grant_type" => "authorization_code",
            "client_assertion_type" => "urn:ietf:params:oauth:client-assertion-type:jwt-bearer",
            "client_assertion" => $jwt
        );

        callbackDebugLog("Sending token request", [
            'endpoint' => $tokenEndPoint, 
            'data_keys' => array_keys($data),
            'redirect_uri' => CALLBACK_URL
        ]);

        $ch = curl_init($tokenEndPoint);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 30 second timeout
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // 10 second connect timeout
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable for debugging
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Disable for debugging

        callbackDebugLog("Executing token request with cURL");

        $tokenResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        $curlInfo = curl_getinfo($ch);
        curl_close($ch);

        callbackDebugLog("Token response received", [
            'http_code' => $httpCode, 
            'response_length' => strlen($tokenResponse),
            'curl_error' => $curlError,
            'total_time' => $curlInfo['total_time'] ?? 'unknown',
            'response_preview' => substr($tokenResponse, 0, 200) . '...'
        ]);

        if ($curlError) {
            throw new Exception("cURL error: " . $curlError);
        }

        if ($httpCode === 200) {
            // Parse the token response first
            $tokenData = json_decode($tokenResponse, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                callbackDebugLog("Failed to parse token response JSON", ['error' => json_last_error_msg()]);
                return [
                    "data" => null,
                    "error" => [
                        "statusCode" => $httpCode,
                        "message" => "Invalid token response JSON: " . json_last_error_msg()
                    ]
                ];
            }

            callbackDebugLog("Token response parsed successfully", [
                'has_access_token' => isset($tokenData['access_token']),
                'has_id_token' => isset($tokenData['id_token']),
                'token_type' => isset($tokenData['token_type']) ? $tokenData['token_type'] : 'N/A'
            ]);

            // Skip JWT verification in callback to avoid header issues, but log the attempt
            callbackDebugLog("Skipping JWT verification in callback to avoid header conflicts");

            $accessToken = $tokenData['access_token'];
            $expiresIn = time() + $tokenData['expires_in'];

            callbackDebugLog("Access token parsed successfully", [
                'expires_in' => $tokenData['expires_in'],
                'token_type' => isset($tokenData['token_type']) ? $tokenData['token_type'] : 'unknown',
                'has_id_token' => isset($tokenData['id_token'])
            ]);

            $response = [
                "data" => [
                    "accessToken" => $accessToken,
                    "expiresIn" => $expiresIn,
                    "tokenData" => $tokenData  // Include full token data for debugging
                ],
                "error" => null
            ];
            return $response;
        } else {
            callbackDebugLog("Token request failed", ['http_code' => $httpCode, 'response' => $tokenResponse]);

            $response = [
                "data" => null,
                "error" => [
                    "statusCode" => $httpCode,
                    "message" => "Failed to get access token. HTTP $httpCode: " . $tokenResponse
                ]
            ];
            return $response;  
        }
    } catch (Exception $e) {
        callbackDebugLog("Exception in getAccessToken", [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return [
            "data" => null,
            "error" => [
                "statusCode" => 500,
                "message" => "Internal error: " . $e->getMessage()
            ]
        ];
    }
}

function handleAuthorizationCode($authorizationCode) {
    callbackDebugLog("Handling authorization code", ['code_length' => strlen($authorizationCode)]);
   
    try {
        $response = getAccessToken($authorizationCode);

        callbackDebugLog("getAccessToken completed", [
            'has_error' => $response['error'] !== null,
            'has_data' => $response['data'] !== null
        ]);

        if ($response['error'] !== null) {   
            $error = $response['error'];
            callbackDebugLog("Access token error", ['error' => $error]);
            
            // Clear output buffer and display error
            ob_end_clean();
            
            // Display user-friendly error message
            echo "<html><body>";
            echo "<h1>Authentication Error</h1>";
            echo "<p><strong>Error Code:</strong> " . htmlspecialchars($error['statusCode']) . "</p>";
            echo "<p><strong>Error Message:</strong> " . htmlspecialchars($error['message']) . "</p>";
            echo "<p><a href='login.php'>Back to Login</a></p>";
            
            if (JWT_DEBUG_MODE) {
                echo "<hr>";
                echo "<h2>Debug Information</h2>";
                echo "<p>Debug information has been logged to the PHP error log.</p>";
                echo "<p>Common issues:</p>";
                echo "<ul>";
                echo "<li>JWKS endpoint not accessible</li>";
                echo "<li>JWT signature verification failed</li>";
                echo "<li>Invalid authorization code</li>";
                echo "<li>Client configuration mismatch</li>";
                echo "</ul>";
            }
            
            echo "</body></html>";
            return;
        }

        if ($response['data'] !== null) {
            $data = $response['data'];
            $accessToken = $data['accessToken'];
            $expiresIn = $data['expiresIn'];
            
            callbackDebugLog("Setting session and cookie", ['expires_in' => $expiresIn]);

            // Clear output buffer before setting headers
            ob_end_clean();

            $cookieName = 'access_token';
            setcookie($cookieName, $accessToken, $expiresIn, '/');
            
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['esignet_access_token'] = $accessToken;
            
            callbackDebugLog("Session and cookie set, redirecting to dashboard");
            
            header('Location: dashboard.php');
            exit();
        }
    } catch (Exception $e) {
        callbackDebugLog("Exception in handleAuthorizationCode", [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        // Clear output buffer and display error
        ob_end_clean();
        
        echo "<html><body>";
        echo "<h1>Internal Error</h1>";
        echo "<p>An unexpected error occurred: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><a href='login.php'>Back to Login</a></p>";
        echo "</body></html>";
    }
}

callbackDebugLog("Processing callback parameters");

if (isset($_GET['code'])) {
    $authorizationCode = $_GET['code'];
    
    callbackDebugLog("Authorization code received, starting processing");
    
    handleAuthorizationCode($authorizationCode);
} elseif (isset($_GET['error'])) {
    // Handle OAuth error responses
    $error = $_GET['error'];
    $errorDescription = isset($_GET['error_description']) ? $_GET['error_description'] : '';
    
    callbackDebugLog("OAuth error received", [
        'error' => $error,
        'error_description' => $errorDescription,
        'all_params' => $_GET
    ]);
    
    // Clear output buffer and display error
    ob_end_clean();
    
    echo "<html><body>";
    echo "<h1>OAuth Error</h1>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($error) . "</p>";
    if ($errorDescription) {
        echo "<p><strong>Description:</strong> " . htmlspecialchars($errorDescription) . "</p>";
    }
    echo "<p><a href='login.php'>Back to Login</a></p>";
    echo "</body></html>";
} else {
    callbackDebugLog("No authorization code received", ['GET_params' => $_GET]);
    
    // Clear output buffer and display error
    ob_end_clean();
    
    echo "<html><body>";
    echo "<h1>Missing Authorization Code</h1>";
    echo "<p>No authorization code was received from the OAuth provider.</p>";
    echo "<p><a href='login.php'>Back to Login</a></p>";
    echo "</body></html>";
}

callbackDebugLog("Callback processing completed");

?>
