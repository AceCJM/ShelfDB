<?php
    // src/management/updateuser.php
    // This file is responsible for updating user details
    if (! isset($_SESSION)) {
        session_start();
    }
    // Set db path
    $dbPath = isset($_ENV["DB_FILE"]) ? ".." . $_ENV["DB_FILE"] : "../db/shelf.db";

    // Verify if the user is authenticated
    require_once dirname(__FILE__) . "/../db/userAuth.php";
    $userAuth = new UserAuth($dbPath);
    if (! $userAuth->isAuthenticated()) {
        header("Location: login.php");
        exit();
    }

    // Check if the user is authorized to change permissions
    require_once dirname(__FILE__) . "/../db/userPermissions.php";
    $userPermissions = new UserPermissions($dbPath);
    $userId          = $_SESSION['user_id'] ?? null;
    if (! $userPermissions->checkPermission($userId, 'admin')) {
        header("Location: index.php");
        exit();
    }
    // Handle form submission to change permissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $targetUserId  = $_POST['user_id'];
        $newPermission = $_POST['permission'];
        $userPermissions->changePermission($targetUserId, $newPermission);
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Update User Details</h1>
    <form action="ChangePermissions.php" method="post">
        <label for="user_id">User ID:</label>
        <input type="text" id="user_id" name="user_id" required>
        <label for="permission">New Permission:</label>
        <select id="permission" name="permission" required>
            <option value="">Select Permission</option>
            <option value="user">Read</option>
            <option value="write">Write</option>
            <option value="admin">Admin</option>
        </select>
        <button type="submit">Change Permission</button>
    </form>
    <?php if (isset($error)) {
            echo "<p class='error'>$error</p>";
    }?>
</body>
</html>