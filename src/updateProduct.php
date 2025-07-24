<?php
// src/updateProduct.php
// This file is responsible for updating product information in the database.
if (!isset($_SESSION)) {
    session_start();
}
// Set db path
$dbPath = $_ENV["DB_FILE"] ?? 'db/shelf.db';
require_once dirname(__FILE__) . "/db/userAuth.php";
$userAuth = new UserAuth($dbPath);
require_once dirname(__FILE__) . "/db/userPermissions.php";
$userPermissions = new UserPermissions($dbPath);
// Check if the user is authenticated
if (!$userAuth->isAuthenticated()) {
    header("Location: login.php");
    exit();
}
// Check if the user has permission to update products
if (!$userPermissions->checkPermission($_SESSION['user_id'], 'write')) {
    header("Location: index.php");
    exit();
}

