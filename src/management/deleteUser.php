<?php
    // src/management/deleteUser.php
    // This file is responsible for deleting a user
    if (! isset($_SESSION)) {
        session_start();
    }
    // Set db path
    $dbPath = isset($_ENV["DB_FILE"]) ? ".." . $_ENV["DB_FILE"] : "../db/shelf.db";
    // Verify user authentication
    require_once dirname(__FILE__) . "/../db/userAuth.php";
    $userAuth = new UserAuth($dbPath);
    if (! $userAuth->isAuthenticated()) {
        header("Location: login.php");
        exit();
    }
    // Check if the user is authorized to delete users
    require_once dirname(__FILE__) . "/../db/userPermissions.php";
    $userPermissions = new UserPermissions($dbPath);
    $userId          = $_SESSION['user_id'] ?? null;
    if (! $userPermissions->checkPermission($userId, 'admin')) {
        header("Location: index.php");
        exit();
    }
    // Handle form submission to delete a user
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $targetUserId = $_POST['user_id'];
        if (empty($targetUserId)) {
            $error = "User ID is required.";
        } elseif ($targetUserId === $userId) {
            $error = "You cannot delete your own account.";
        } elseif ($targetUserId === "admin") {
            $error = "Cannot delete the admin user.";
        }
        if ($userPermissions->deleteUser($targetUserId)) {
            $message = "User deleted successfully.";
            header("Location: userManagement.php?message=" . $message);
            exit();
        } else {
            $error = "Failed to delete user.";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete User</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Delete User</h1>
    <form action="deleteUser.php" method="post">
        <label for="user_id">User ID:</label>
        <input type="text" id="user_id" name="user_id" value="<?php if (isset($_GET['user_id'])) { echo $_GET['user_id']; }?>" required>
        <button type="submit">Delete User</button>
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