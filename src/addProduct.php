<?php
    // src/addProduct.php
    // This file is responsible for adding a new product to the database.
    if (! isset($_SESSION)) {
        session_start();
    }
    // Validate User Authentication
    require_once dirname(__FILE__) . "/db/userAuth.php";
    $userAuth = new UserAuth($_ENV['DB_FILE'] ?? 'db/shelf.db');
    if (! $userAuth->isAuthenticated()) {
        header("Location: login.php");
        exit();
    }
    // Check User Permissions
    require_once dirname(__FILE__) . "/db/userPermissions.php";
    $userPermissions = new UserPermissions($_ENV['DB_FILE'] ?? 'db/shelf.db');
    if (! $userPermissions->checkPermission($_SESSION['user_id'], 'write') or ! $userPermissions->checkPermission($_SESSION['user_id'], 'admin')) {
        header("Location: index.php");
        exit();
    }
    // Load the Database
    require_once "db/database.php";
    $db = new AppDatabase($_ENV['DB_FILE'] ?? 'db/shelf.db');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Add Product</h1>
    <form action="addProduct.php" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        <label for="department">Department:</label>
        <input type="text" id="department" name="department" required>
        <label for="price">Price:</label>
        <input type="number" id="price" name="price" step="0.01" required>
        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" value="0" required>
        <label for="upc">UPC:</label>
        <input type="number" id="upc" name="upc" required>
        <button type="submit">Add Product</button>
    </form>
    <a href="index.php">Back to Home</a>
    <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get the form data
            $name       = $_POST['name'];
            $department = $_POST['department'];
            $price      = $_POST['price'];
            $quantity   = $_POST['quantity'];
            $upc        = $_POST['upc'];
            // Prepare the data for insertion
            $data = [
                'name'       => $name,
                'department' => $department,
                'price'      => $price,
                'quantity'   => $quantity,
                'upc'        => $upc,
            ];
            try {
                // Insert the product into the database
                $db->insertProduct($data);
                echo "<p>Product added successfully!</p>";
            } catch (Exception $e) {
                echo "<p>Error adding product: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        }
    ?>
</body>
</html>
<?php
    // Close database connection
    $userPermissions->close();
    $userAuth->close();
$db->close();
?>