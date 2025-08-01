<?php
// src/logout.php
// Logout page to handle user session termination
session_start();
// Unset all session variables
$_SESSION = array();
// Destroy the session
session_destroy();
// Redirect to the login page after logout
header("Location: login.php");
exit();