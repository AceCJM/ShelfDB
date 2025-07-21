<?php
// src/management/UserManagment.php
// This file is responsible for managing user-related operations
if (!isset($_SESSION)) {
    session_start();
}
// Set db path
if (isset($_ENV["DB_FILE"])) {
    $dbPath = ".." . $_ENV["DB_FILE"];
} else {
    $dbPath = "../db/shelf.db";
}

require_once dirname(__FILE__) . "/../db/UserAuth.php";
$userAuth = new UserAuth($dbPath);
// Veryify if the user is authenticated
if (!$userAuth->isAuthenticated()) {
    header("Location: login.php");
    exit();
}
$userAuth->close(); // Close the database connection after use
$userId = $_SESSION['user_id'] ?? null;

// Include the UserPermissions class for permission checks
require_once dirname(__FILE__) . "/../db/UserPermissions.php";
$userPermissions = new UserPermissions($dbPath);
// Verify if the user has the required permissions
if (!$userPermissions->checkPermission($userId, 'manage_users')) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>User Management</title>
        <link rel="stylesheet" href="../css/style.css">
    </head>
    <body>
        <h1>User Management</h1>
        <nav>
            <ul>
                <li><a href="ChangePermissions.php">Change User Permissions</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
    </body>
</html>