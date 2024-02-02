<?php
require_once './constants.php';


$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to clear the "voters" table
$sql = "DELETE FROM voters";

if ($conn->query($sql) === TRUE) {
    echo "Table 'voters' has been cleared successfully.";
} else {
    echo "Error clearing table: " . $conn->error;
}

// Close connection
$conn->close();

?>