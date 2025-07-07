<?php
require_once dirname(__FILE__) . '/esignet-user.php';
require_once dirname(__FILE__) . '/local-user.php';
require_once dirname(__FILE__) . '/jwt-verifier.php';

function getLoggedInUser($db_conn)
{
    if (JWT_DEBUG_MODE) {
        JwtVerifier::debugLog("Checking for logged in user", [
            'has_esignet_token' => isset($_SESSION['esignet_access_token']),
            'has_local_id' => isset($_SESSION['local_ID_login'])
        ]);
    }

    if (isset($_SESSION['esignet_access_token'])) {
        if (JWT_DEBUG_MODE) {
            JwtVerifier::debugLog("Found eSignet access token, attempting to get user info");
        }

        $resp = ESignet\getUserInfo($_SESSION['esignet_access_token']);
        
        if ($resp['error'] !== null) {
            if (JWT_DEBUG_MODE) {
                JwtVerifier::debugLog("eSignet user info failed", ['error' => $resp['error']]);
            }
            return null;
        }

        if (JWT_DEBUG_MODE) {
            JwtVerifier::debugLog("eSignet user info retrieved successfully", [
                'user_sub' => isset($resp['data']['sub']) ? $resp['data']['sub'] : 'unknown',
                'user_email' => isset($resp['data']['email']) ? $resp['data']['email'] : 'not provided'
            ]);
        }

        return ['login_type' => 'esignet', 'user' => $resp['data']];
    }

    if (isset($_SESSION['local_ID_login'])) {
        if (JWT_DEBUG_MODE) {
            JwtVerifier::debugLog("Found local ID login, attempting to get voter details", [
                'voter_id' => $_SESSION['local_ID_login']
            ]);
        }

        $resp = getVoterDetails($_SESSION['local_ID_login'], $db_conn);
        
        if ($resp['error'] !== null) {
            if (JWT_DEBUG_MODE) {
                JwtVerifier::debugLog("Local voter details failed", ['error' => $resp['error']]);
            }
            return null;
        }

        if (JWT_DEBUG_MODE) {
            JwtVerifier::debugLog("Local voter details retrieved successfully", [
                'voter_id' => $resp['data']['voter_ID']
            ]);
        }

        return ['login_type' => 'local', 'user' => $resp['data']];
    }

    if (JWT_DEBUG_MODE) {
        JwtVerifier::debugLog("No logged in user found");
    }

    return null;
}