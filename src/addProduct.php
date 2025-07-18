<?php
    require_once "db/Database.php";
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
        <label for="upc">UPC:</label>
        <input type="text" id="upc" name="upc" required>
        <button type="submit">Add Product</button>
    </form>
    <a href="index.php">Back to Home</a>
    <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get the form data
            $name       = $_POST['name'];
            $department = $_POST['department'];
            $price      = $_POST['price'];
            $upc        = $_POST['upc'];
            // Prepare the data for insertion
            $data = [
                'name'       => $name,
                'department' => $department,
                'price'      => $price,
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
    // Close the database connection
$db->close();
?>