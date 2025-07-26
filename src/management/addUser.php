<?php
    // src/management/addUser.php
    // This file is responsible for adding a new user to the system.
    if (! isset($_SESSION)) {
        session_start();
    }
    // Set db path
    $dbPath = isset($_ENV["DB_FILE"]) ? ".." . $_ENV["DB_FILE"] : "../db/shelf.db";

    // Verify if the user is authenticated
    require_once dirname(__FILE__) . "/../db/userAuth.php";
    $dbPath   = isset($_ENV["DB_FILE"]) ? ".." . $_ENV["DB_FILE"] : "../db/shelf.db";
    $userAuth = new UserAuth($dbPath);
    if (! $userAuth->isAuthenticated()) {
        header("Location: login.php");
        exit();
    }
    // Check if the user is authorized to add users
    require_once dirname(__FILE__) . "/../db/userPermissions.php";
    $userPermissions = new UserPermissions($dbPath);
    $userId          = $_SESSION['user_id'] ?? null;
    if (! $userPermissions->checkPermission($userId, 'admin')) {
        header("Location: index.php");
        exit();
    }
    // Handle form submission to add a new user
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $newUserId         = $_POST['user_id'];
        $newUserPassword   = $_POST['password'];
        $newUserPermission = $_POST['permission'];
        try {
            if ($userPermissions->addUser($newUserId, $newUserPassword, $newUserPermission)) {
                $message = "User added successfully.";
            } else {
                $error = "Failed to add user.";
            }
        } catch (Exception $e) {
            die("Error adding user: " . htmlspecialchars($e->getMessage()));
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Add User</h1>
    <form action="addUser.php" method="post">
        <label for="user_id">User ID:</label>
        <input type="text" id="user_id" name="user_id" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <label for="permission">Permission:</label>
        <select id="permission" name="permission" required>
            <option value="">Select Permission</option>
            <option value="user">Read</option>
            <option value="write">Write</option>
            <option value="admin">Admin</option>
        </select>
        <button type="submit">Add User</button>
    </form>
    <?php if (isset($message)) {
            echo "<p class='success'>$message</p>";
        } elseif (isset($error)) {
            echo "<p class='error'>$error</p>";
    }?>
</body>
</html>
<?php
    // Close database connection
    $userPermissions->close();
$userAuth->close();
?>