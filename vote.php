<?php
require_once './constants.php';
require_once './connection.php';
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
        return decodeUserInfo($response);
    } else {
        echo 'Error: ' . $httpCode;
    }
}

function hasAlreadyVoted($db_conn, $voter_id)
{
    $stmt = $db_conn->prepare("SELECT * FROM voters WHERE voter_id = ?");
    $stmt->bind_param("s", $voter_id);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}

function isPersonMajor($dateOfBirthString)
{
    $DOB = DateTime::createFromFormat('Y/m/d', $dateOfBirthString);
    $currentDate = new DateTime();

    $age = $currentDate->diff($DOB)->y;

    return $age >= 18;
}

function insertVoter($db_conn, $voter_id)
{
    $stmt = $db_conn->prepare("INSERT INTO voters (voter_id) VALUES (?)");
    $stmt->bind_param("s", $voter_id);
    return $stmt->execute();
}
$accessToken = $_COOKIE['access_token'];
$userInfo = getUserInfo($accessToken);
$individual_id = $userInfo['sub'];

if (!hasAlreadyVoted($db_conn, $individual_id)) {
    $voteAddedToDb = insertVoter($db_conn, $individual_id);
    if ($voteAddedToDb) {
        echo "successfully Enrolled!";
        header('Location: user.php#Enroll');
        exit();
    } else {
        echo "Error Enrolling";
    }
} else {
    echo "Tried Reenrolling";
}
?>