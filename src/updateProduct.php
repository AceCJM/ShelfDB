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

// Include database connection
require_once dirname(__FILE__) . "/db/database.php";
try {
    $db = new AppDatabase($dbPath);
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if the user has permission to update products
if (!$userPermissions->checkPermission($_SESSION['user_id'], 'write')) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'];
    $productUPC = $_POST['product_upc'];
    $productName = $_POST['product_name'];
    $productPrice = $_POST['product_price'];
    $productDepartment = $_POST['product_department'];
    $productQuantity = $_POST['product_quantity'];

    // Validate input
    if (empty($productUPC) || empty($productName) || empty($productPrice) || empty($productDescription) || empty($productQuantity)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: updateProduct.php?product_upc=" . urlencode($productUPC));
        exit();
    }

    // Update product in the database
    try {
        $db->updateProduct($productUPC, [
            'upc' => $productUPC,
            'name' => $productName,
            'price' => $productPrice,
            'department' => $productDepartment,
            'quantity' => $productQuantity
        ]);
    } catch (Exception $e) {
        die("Failed to update product: " . $e->getMessage());
    }
    // Redirect to the product list page with success message
    $_SESSION['success'] = "Product updated successfully.";
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Product</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Update Product</h1>
        <?php
        // Display error message if any
        if (isset($_SESSION['error'])) {
            echo '<div class="error">' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']);
        }
        // Display success message if any
        if (isset($_SESSION['success'])) {
            echo '<div class="success">' . htmlspecialchars($_SESSION['success']) . '</div>';
            unset($_SESSION['success']);
        }
        ?>
        <?php
        // Fetch product details using the provided UPC
        if (isset($_GET['product_upc'])) {
            $productUPC = $_GET['product_upc'];
            try {
                $product = $db->queryUPC($productUPC);
            } catch (Exception $e) {
                die("Failed to fetch product: " . $e->getMessage());
            }
            if (empty($product)) {
                echo '<div class="error">Product not found.</div>';
            } else {
                $product = $product[0]; // Get the first (and only) product
            }
        }
        ?>
        <form method="POST" action="updateProduct.php">
            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
            <div class="form-group">
                <label for="product_upc">UPC:</label>
                <input type="text" id="product_upc" name="product_upc" value="<?php echo htmlspecialchars($product['upc']); ?>" required>
            </div>
            <div class="form-group">
                <label for="product_name">Name:</label>
                <input type="text" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="product_price">Price:</label>
                <input type="number" id="product_price" name="product_price" value="<?php echo htmlspecialchars($product['price']); ?>" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="product_department">Department:</label>
                <input type="text" id="product_department" name="product_department" value="<?php echo htmlspecialchars($product['department']); ?>" required>
            </div>
            <div class="form-group">
                <label for="product_quantity">Quantity:</label>
                <input type="number" id="product_quantity" name="product_quantity" value="<?php echo htmlspecialchars($product['quantity']); ?>" required>
            </div>
            <button type="submit">Update Product</button>
        </form>