<?php
require_once './constants.php';

$db_conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($db_conn->connect_error) {
    die("Connection failed: " . $db_conn->connect_error);
}


function initSchema($db_conn)
{

    $sql = "CREATE TABLE IF NOT EXISTS voters (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    voting_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    voter_id VARCHAR(255) UNIQUE
)";

    if (!$db_conn->query($sql)) {
        die("Error creating table: " . $db_conn->error);
    }
}

initSchema($db_conn);

?>