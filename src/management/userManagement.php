<?php
    // src/management/userManagement.php
    // This file is responsible for managing user-related operations
    if (! isset($_SESSION)) {
        session_start();
    }
    // Set db path
    $dbPath = isset($_ENV["DB_FILE"]) ? ".." . $_ENV["DB_FILE"] : "../db/shelf.db";

    // Veryify if the user is authenticated
    require_once dirname(__FILE__) . "/../db/userAuth.php";
    $userAuth = new UserAuth($dbPath);
    if (! $userAuth->isAuthenticated()) {
        header("Location: login.php");
        exit();
    }
    $userAuth->close(); // Close the database connection after use
    $userId = $_SESSION['user_id'] ?? null;

    // Include the UserPermissions class for permission checks
    require_once dirname(__FILE__) . "/../db/userPermissions.php";
    $userPermissions = new UserPermissions($dbPath);
    // Verify if the user has the required permissions
    if (! $userPermissions->checkPermission($userId, 'admin')) {
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
                <li><a href="addUser.php">Add User</a></li>
                <li><a href="deleteUser.php">Delete User</a></li>
                <li><a href="updateUser.php">Update User</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
    </body>
</html>
<?php
    // Close database connection
    $userPermissions->close();
$userAuth->close();
?>