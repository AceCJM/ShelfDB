<?php
// src/management/ChangePermissions.php
// This file is responsible for changing user permissions
if (!isset($_SESSION)) {
    session_start();
}
// Set db path
if (isset($_ENV["DB_FILE"])) {
    $dbPath = ".." . $_ENV["DB_FILE"];
} else {
    $dbPath = "../db/shelf.db";
}

require_once dirname(__FILE__) . "/../db/UserPermissions.php";
$userPermissions = new UserPermissions($dbPath);

// Check if the user is authorized to change permissions
$userId = $_SESSION['user_id'] ?? null;
if (!$userPermissions->checkPermission($userId, 'manage_users')) {
    header("Location: index.php");
    exit();
}
// Handle form submission to change permissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetUserId = $_POST['user_id'];
    $newPermission = $_POST['permission'];
    $userPermissions->changePermission($targetUserId, $newPermission);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change User Permissions</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Change User Permissions</h1>
    <form action="ChangePermissions.php" method="post">
        <label for="user_id">User ID:</label>
        <input type="text" id="user_id" name="user_id" required>
        <label for="permission">New Permission:</label>
        <input type="text" id="permission" name="permission" required>
        <button type="submit">Change Permission</button>
    </form>
    <?php if (isset($error)) {
        echo "<p class='error'>$error</p>";
    } ?>
</body>
</html>