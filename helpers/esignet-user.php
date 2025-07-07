<?php
namespace ESignet {
    require_once dirname(__FILE__) . '/jwt-verifier.php';
    
    function decodeUserInfo($encodedUserInfo)
    {
        if (JWT_DEBUG_MODE) {
            \JwtVerifier::debugLog("Starting user info decoding (legacy method)", ['jwt_preview' => substr($encodedUserInfo, 0, 50) . '...']);
        }

        $parts = explode('.', $encodedUserInfo);
    
        if (isset($parts[1])) {
            $payloadJsonStr = base64_decode($parts[1]);
            $payload = json_decode($payloadJsonStr, true);
            
            if (JWT_DEBUG_MODE) {
                \JwtVerifier::debugLog("User info decoded without verification", ['payload_keys' => array_keys($payload)]);
            }
            
            return $payload;
        }
        
        if (JWT_DEBUG_MODE) {
            \JwtVerifier::debugLog("Failed to decode user info - invalid format");
        }
        
        return null;
    }
    
    function getUserInfo($accessToken)
    {
        if (JWT_DEBUG_MODE) {
            \JwtVerifier::debugLog("Starting user info request", ['access_token_preview' => substr($accessToken, 0, 20) . '...']);
        }

        $url = ESIGNET_SERVICE_URL . "/v1/esignet/oidc/userinfo";
        $curl = curl_init();
    
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
        ]);
        // ENABLE IN PROD
        // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    
        if (JWT_DEBUG_MODE) {
            \JwtVerifier::debugLog("Sending user info request", ['url' => $url]);
        }
    
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if (JWT_DEBUG_MODE) {
            \JwtVerifier::debugLog("User info response received", [
                'http_code' => $httpCode, 
                'response_length' => strlen($response),
                'response_preview' => substr($response, 0, 100) . '...'
            ]);
        }

        if ($httpCode == 200) {
            // Check if response is a JWT (contains dots) or plain JSON
            if (strpos($response, '.') !== false) {
                if (JWT_DEBUG_MODE) {
                    \JwtVerifier::debugLog("User info response appears to be JWT, attempting verification");
                }
                
                // Verify the JWT signature
                $verificationResult = \JwtVerifier::verifyUserInfoJwt($response);
                
                if ($verificationResult['error'] !== null) {
                    if (JWT_DEBUG_MODE) {
                        \JwtVerifier::debugLog("User info JWT verification failed, falling back to legacy decode", [
                            'error' => $verificationResult['error'],
                            'verification_status' => 'FAILED'
                        ]);
                    }
                    
                    // Fallback to legacy decoding if verification fails
                    $userData = decodeUserInfo($response);
                    
                    if ($userData === null) {
                        return ["error" => ["statusCode" => 400, "message" => "Failed to decode user info JWT"], "verification_status" => "FAILED"];
                    }
                    
                    // Add verification status to the response
                    $userData['_jwt_verification_status'] = 'FAILED';
                    $userData['_jwt_verification_error'] = $verificationResult['error']['message'];
                    
                    return ["data" => $userData, "error" => null, "verification_status" => "FAILED"];
                } else {
                    if (JWT_DEBUG_MODE) {
                        \JwtVerifier::debugLog("User info JWT verification successful", [
                            'verification_status' => 'SUCCESS',
                            'verified_claims' => array_keys($verificationResult['data'])
                        ]);
                    }
                    
                    // Add verification status to the response
                    $verificationResult['data']['_jwt_verification_status'] = 'SUCCESS';
                    $verificationResult['data']['_jwt_verification_time'] = time();
                    
                    return ["data" => $verificationResult['data'], "error" => null, "verification_status" => "SUCCESS"];
                }
            } else {
                if (JWT_DEBUG_MODE) {
                    \JwtVerifier::debugLog("User info response appears to be plain JSON", [
                        'verification_status' => 'NOT_APPLICABLE'
                    ]);
                }
                
                // Handle plain JSON response
                $userData = json_decode($response, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    if (JWT_DEBUG_MODE) {
                        \JwtVerifier::debugLog("Failed to parse user info JSON", ['error' => json_last_error_msg()]);
                    }
                    return ["error" => ["statusCode" => 400, "message" => "Invalid JSON response"], "verification_status" => "ERROR"];
                }
                
                if (JWT_DEBUG_MODE) {
                    \JwtVerifier::debugLog("User info parsed as JSON", [
                        'user_data_keys' => array_keys($userData),
                        'verification_status' => 'NOT_APPLICABLE'
                    ]);
                }
                
                // Add verification status for plain JSON
                $userData['_jwt_verification_status'] = 'NOT_APPLICABLE';
                $userData['_jwt_verification_note'] = 'Response was plain JSON, not JWT';
                
                return ["data" => $userData, "error" => null, "verification_status" => "NOT_APPLICABLE"];
            }
        }

        if (JWT_DEBUG_MODE) {
            \JwtVerifier::debugLog("User info request failed", [
                'http_code' => $httpCode, 
                'response' => $response,
                'verification_status' => 'ERROR'
            ]);
        }

        return ["error" => ["statusCode" => $httpCode, "message" => "failed to get user from token"], "verification_status" => "ERROR"];
    }
}
