<?php
require_once dirname(__FILE__) . '/esignet-user.php';
require_once dirname(__FILE__) . '/local-user.php';


function getLoggedInUser($db_conn)
{
    if (isset($_SESSION['esignet_access_token'])) {
        $resp = ESignet\getUserInfo($_SESSION['esignet_access_token']);
        if ($resp['error'] !== null)
            return null;
        return ['login_type' => 'esignet', 'user' => $resp['data']];
    }

    if (isset($_SESSION['local_ID_login'])) {
        $resp = getVoterDetails($_SESSION['local_ID_login'], $db_conn);
        if ($resp['error'] !== null)
            return null;
        return ['login_type' => 'local', 'user' => $resp['data']];
    }
    return null;
}