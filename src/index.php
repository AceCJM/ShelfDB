<?php
    // Validate User Authentication
    session_start();
    require_once dirname(__FILE__) . "/db/UserAuth.php";
    $userAuth = new UserAuth($_ENV['DB_FILE'] ?? 'db/shelf.db');
    if (! $userAuth->isAuthenticated()) {
        header("Location: login.php");
        exit();
    }
    // Load the Database
    require_once 'db/Database.php';
    $db = new AppDatabase($_ENV['DB_FILE'] ?? 'db/shelf.db');
?>
<DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ShelfDB</title>
        <link rel="stylesheet" href="css/style.css">
    </head>

    <body>
        <h1>Welcome to ShelfDB</h1>
        <p>This is a simple SQLite3 database interface for managing products.</p>
        <footer>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="allProducts.php">All Products</a></li>
                    <li><a href="addProduct.php">Add Product</a></li>
                    <li><a href="searchProduct.php">Search Product</a></li>
                </ul>
            </nav>
        </footer>
    </body>

    </html>
