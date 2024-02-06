<?php
namespace ESignet {
    function decodeUserInfo($encodedUserInfo)
    {
        $parts = explode('.', $encodedUserInfo);
    
        if (isset($parts[1])) {
            $payloadJsonStr = base64_decode($parts[1]);
            $payload = json_decode($payloadJsonStr, true);
            return $payload;
        }
    }
    
    function getUserInfo($accessToken)
    {
        $url = ESIGNET_SERVICE_URL . "/v1/esignet/oidc/userinfo";
        $curl = curl_init();
    
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
        ]);
        // ENAABLE IN PROD
        // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    
    
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($httpCode == 200) {
            return ["data" => decodeUserInfo($response), "error" => null];
        }

        return ["error" => ["statusCode" => $httpCode, "message" => "failed to get user from token"]];
    }
}
