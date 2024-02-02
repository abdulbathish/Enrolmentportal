<?php
include_once "./constants.php";
require __DIR__ . '/vendor/autoload.php';
use Firebase\JWT\JWT;

function handleAuthorizationCode(
    $authorizationCode
) {
    $tokenEndPoint = ESIGNET_SERVICE_URL . "/v1/esignet/oauth/token";
    echo ($tokenEndPoint);
    $clientId = CLIENT_ID;
    $private_key_resource = openssl_get_privatekey(CLIENT_PRIVATE_KEY);
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

    $jwt = JWT::encode($payload, $private_key_resource, 'RS256', null, $header);
    $data = array(
        "code" => $authorizationCode,
        "client_id" => $clientId,
        "redirect_uri" => CALLBACK_URL,
        "grant_type" => "authorization_code",
        "client_assertion_type" => "urn:ietf:params:oauth:client-assertion-type:jwt-bearer",
        "client_assertion" => $jwt
    );

    $ch = curl_init($tokenEndPoint);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    $tokenResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $tokenData = json_decode($tokenResponse, true);
        $accessToken = $tokenData['access_token'];
        $expiresIn = time() + $tokenData['expires_in'];
        $cookieName = 'access_token';
        setcookie($cookieName, $accessToken, $expiresIn, '/');
        $accessToken = $tokenData['access_token'];
        session_start();
        $_SESSION['access_token'] = $accessToken;
        header('Location: user.php');
        exit();
    } else {
        echo "$tokenEndPoint";
        echo ("http code error" . "$httpCode");
    }
}


if (isset($_GET['code'])) {
    $authorizationCode = $_GET['code'];
    handleAuthorizationCode($authorizationCode);
} else {
    echo "Error Receving Error Code";
}
