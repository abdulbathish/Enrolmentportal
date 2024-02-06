<?php
require_once './constants.php';

$db_conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($db_conn->connect_error) {
    die("Connection failed: " . $db_conn->connect_error);
}


function initEnrolTable($db_conn)
{

    $sql = "CREATE TABLE IF NOT EXISTS voters (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    voting_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    voter_id VARCHAR(255) UNIQUE
)";

    if (!$db_conn->query($sql)) {
        die("Error creating voters table: " . $db_conn->error);
    }

}

function initLocalTable($db_conn){
    $sql = "CREATE TABLE IF NOT EXISTS users (
        voter_ID INT PRIMARY KEY AUTO_INCREMENT,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(255) NOT NULL,
        gender VARCHAR(10) NOT NULL,
        birthdate DATE NOT NULL
    )AUTO_INCREMENT=112233;";
    
        if (!$db_conn->query($sql)) {
            die("Error creating users table: " . $db_conn->error);
        }
}

initEnrolTable($db_conn);
initLocalTable($db_conn);