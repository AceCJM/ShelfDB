<?php
    // src/management/userManagement.php
    // This file is responsible for managing user-related operations
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
    $userAuth->close(); // Close the database connection after use
    $userId = $_SESSION['user_id'] ?? null;

    // Include the UserPermissions class for permission checks
    require_once dirname(__FILE__) . "/../db/userPermissions.php";
    $userPermissions = new UserPermissions($dbPath);
    // Verify if the user has the required permissions
    if (! $userPermissions->checkPermission($userId, 'admin')) {
        header("Location: login.php");
        exit();
    }

    // Display any messages or errors
    if (isset($_GET['message'])) {
        $message = htmlspecialchars($_GET['message']);
    } elseif (isset($_GET['error'])) {
        $error = htmlspecialchars($_GET['error']);
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
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
        <?php
        if (isset($message)) {
            echo "<p class='success'>" . htmlspecialchars($message) . "</p>";
        } elseif (isset($error)) {
            echo "<p class='error'>" . htmlspecialchars($error) . "</p>";
        }
        ?>
        <h2>Users</h2>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Permissions</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch and display users
                $users = $userPermissions->getAllUsers();
                foreach ($users as $user) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($user['user_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['permission']) . "</td>";
                    // hyperlink to update user permissions
                    echo "<td><a href='updateUser.php?user_id=" . htmlspecialchars($user['user_id']) . "'>Update</a> <a href='deleteUser.php?user_id=" . htmlspecialchars($user['user_id']) . "'>Delete</a></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
    </body>
</html>
<?php
    // Close database connection
    $userPermissions->close();
$userAuth->close();
?>