<?php

session_start();
unset($_SESSION['access_token']);
session_destroy();
header("location: index.php");

?>