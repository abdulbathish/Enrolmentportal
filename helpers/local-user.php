<?php

function verfiyLogin($voter_ID, $password, $db_conn)
{
    $stmt = $db_conn->prepare("SELECT * FROM users WHERE voter_ID = ?");
    $stmt->bind_param("s", $voter_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    $error_message = null;

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stored_password = $row['password'];

        if (password_verify($password, $stored_password)) {
            // Successful login
            $response = [
                "data" => [
                    "voter_ID" => $voter_ID,
                ],
                "error" => null
            ];
                return $response;
            }
        $error_message = "password dont match";
    } else {
        $error_message = "no user found";
    }

    $response = [
        "data" => null,
        "error" => [
            "message" => $error_message
        ]
    ];
    return $response;
}

function getVoterDetails($voter_ID, $db_conn){
    $stmt = $db_conn->prepare("SELECT * FROM users WHERE voter_ID = ?");
    $stmt->bind_param("s", $voter_ID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $response = [
            "data" => $result->fetch_assoc(),
            "error" => null
        ];
        return $response; 
    } else {
        $response = [
            "data" => null,
            "error" => [
                "message" => "details not found"
            ]
        ];
        return $response;
    }
}