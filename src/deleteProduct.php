<?php
// src/deleteProduct.php
if (!isset($_SESSION)) {
    session_start();
}
// Set db path
$dbPath = $_ENV['DB_FILE'] ?? 'db/shelf.db';

// Verify user authentication
require_once dirname(__FILE__) . "/db/userAuth.php";
try {
    $userAuth = new UserAuth($dbPath);
} catch (Exception $e) {
    die("Database connection failed: " . htmlspecialchars($e->getMessage()));
}
if (!$userAuth->isAuthenticated()) {
    header("Location: login.php");
    exit();
}

// Verify user permissions
require_once dirname(__FILE__) . "/db/userPermissions.php";
try {
    $userPermissions = new UserPermissions($dbPath);
} catch (Exception $e) {
    die("Database connection failed: " . htmlspecialchars($e->getMessage()));
}
if (!$userPermissions->checkPermission($_SESSION['user_id'], 'admin')) {
    header("Location: index.php");
    exit();
}

// Load the Database
require_once dirname(__FILE__) . "/db/database.php";
try {
    $db = new AppDatabase($dbPath);
} catch (Exception $e) {
    die("Database connection failed: " . htmlspecialchars($e->getMessage()));
}

// Handle form submission to delete a product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'];
    try {
        $db->deleteProduct($productId);
        $message = "Product deleted successfully!";
    } catch (Exception $e) {
        $message = "Error deleting product: " . htmlspecialchars($e->getMessage());
    }
}

