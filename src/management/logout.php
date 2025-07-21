<?php
// src/management/ChangePermissions.php
// This file is responsible for clearing local user data on logout
session_start();
// Unset all session variables
$_SESSION = array();
// Destroy the session
session_destroy();
// Redirect to the login page after logout
header("Location: login.php");
exit();