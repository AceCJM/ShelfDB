<?php
    // src/db/UserPermissions.php
    // Login page for user management
    session_start();

    // Load dependencies and create objects BEFORE any output
    require_once dirname(__FILE__) . "/db/UserAuth.php";
    require_once dirname(__FILE__) . "/db/UserPermissions.php";
    $userAuth        = new UserAuth($_ENV['DB_FILE'] ?? 'db/shelf.db');
    $userPermissions = new UserPermissions($_ENV['DB_FILE'] ?? 'db/shelf.db');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the form data
        $username = $_POST['username'];
        $password = $_POST['password'];
        // Attempt to authenticate the user
        try {
            if ($userAuth->authenticate($username, $password)) {
                // Verify user permissions
                if ($userPermissions->checkPermission($username, "manage_users")) {
                    // Set session variable for user ID
                    $_SESSION['user_id'] = $username;
                    // Redirect to the user management page after successful login
                    header("Location: index.php");
                    exit();
                } else {
                    $error = "You do not have permission to manage users.";
                }
            } else {
                $error = "Invalid username or password.";
            }
        } catch (Exception $e) {
            $error = "Error during authentication: " . htmlspecialchars($e->getMessage());
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Login</h1>
    <form action="login.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Login</button>
    </form>
    <?php if (isset($error)) {
            echo "<p class='error'>$error</p>";
        }
    ?>
</body>
</html>
