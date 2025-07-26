<?php
    // Validate User Authentication
    if (!isset($_SESSION)) {
        session_start();
    }
    require_once dirname(__FILE__) . "/db/userAuth.php";
try {
    $userAuth = new UserAuth($_ENV['DB_FILE'] ?? 'db/shelf.db');
} catch (Exception $e) {
    die("Database connection failed: " . htmlspecialchars($e->getMessage()));
}
require_once dirname(__FILE__) . "/db/userPermissions.php";
try {
    $userPermissions = new UserPermissions($_ENV['DB_FILE'] ?? 'db/shelf.db');
} catch (Exception $e) {
    die("Database connection failed: " . htmlspecialchars($e->getMessage()));
}
if (! $userAuth->isAuthenticated()) {
        header("Location: login.php");
        exit();
    }
    // Load the Database
    require_once 'db/database.php';
try {
    $db = new AppDatabase($_ENV['DB_FILE'] ?? 'db/shelf.db');
} catch (Exception $e) {
    die("Database connection failed: " . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ShelfDB</title>
        <link rel="stylesheet" href="css/style.css">
    </head>

    <body>
        <header>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="allProducts.php">All Products</a></li>
                <?php if ($userPermissions->checkPermission($_SESSION['user_id'], 'admin')): ?>
                    <li><a href="management/userManagement.php">User Management</a></li>
                    <li><a href="deleteProduct.php">Delete Product</a></li>
                    <li><a href="updateProduct.php">Update Products</a></li>
                <?php endif; ?>
                <?php if ($userPermissions->checkPermission($_SESSION['user_id'], 'write')): ?>
                    <li><a href="addProduct.php">Add Product</a></li>
                <?php endif; ?>
                <li><a href="searchProduct.php">Search Product</a></li>
            </ul>
        </nav>
        </header>
        <h1>Welcome to ShelfDB</h1>
        <p>This is a simple SQLite3 database interface for managing products.</p>
        <footer>
            <p>&copy; <?php echo date("Y"); ?> ShelfDB. All rights reserved.</p>
            <a href="logout.php">Logout</a>
        </footer>
    </body>

    </html>
