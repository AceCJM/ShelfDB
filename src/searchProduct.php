<?php
    // src/searchProduct.php
    // This file is responsible for searching for a product by UPC in the database.
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
    require_once "db/database.php";
    // Initialize the database connection
    $db = new AppDatabase($_ENV['DB_FILE'] ?? 'db/shelf.db');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Product</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Search Product</h1>
    <form action="searchProduct.php" method="get">
        <label for="upc">UPC:</label>
        <input type="number" id="upc" name="upc" required>
        <button type="submit">Search</button>
    </form>
    <a href="index.php">Back to Home</a>
    <?php
        if (isset($_GET['upc'])) {
            $upc = $_GET['upc'];
            try {
                // Query the database for the product with the given UPC
                $result = $db->queryUPC($upc);
                if (empty($result)) {
                    echo "<p>No product found with UPC: " . htmlspecialchars($upc) . "</p>";
                } else {
                    echo "<h2>Product Details</h2>";
                    echo "<table>";
                    echo "<tr><th>ID</th><th>Name</th><th>Department</th><th>Price</th><th>UPC</th></tr>";
                    foreach ($result as $row) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['department']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['price']) . "</td>";
                        echo "<td>" . htmlspecialchars($row["quantity"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row['upc']) . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                }
            } catch (Exception $e) {
                echo "<p>Error searching for product: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        }
    ?>
</body>
</html>
<?php
    // Close the database connection
$db->close();
?>