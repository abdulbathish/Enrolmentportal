<?php

function hasAlreadyVoted($db_conn, $voter_id)
{
    $stmt = $db_conn->prepare("SELECT * FROM voters WHERE voter_id = ?");
    $stmt->bind_param("s", $voter_id);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}

function isPersonMajor($dateOfBirthString, $dobFmt)
{
    $DOB = DateTime::createFromFormat($dobFmt, $dateOfBirthString);
    $currentDate = new DateTime();

    $age = $currentDate->diff($DOB)->y;

    return $age >= 18;
}

function insertVoter($db_conn, $voter_id)
{
    if (hasAlreadyVoted($db_conn, $voter_id)) {
        return false;
    }
    $stmt = $db_conn->prepare("INSERT INTO voters (voter_id) VALUES (?)");
    $stmt->bind_param("s", $voter_id);
    return $stmt->execute();
}

