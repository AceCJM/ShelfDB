<?php
    // Validate User Authentication
    session_start();
    require_once dirname(__FILE__) . "/db/userAuth.php";
try {
    $userAuth = new UserAuth($_ENV['DB_FILE'] ?? 'db/shelf.db');
} catch (Exception $e) {
    die("Error initializing user authentication: " . htmlspecialchars($e->getMessage()));
}
if (! $userAuth->isAuthenticated()) {
        header("Location: login.php");
        exit();
    }
    // Load the Database
    require_once "db/database.php";
    // pull database location from .env file
    // or use a default value
try {
    $db = new AppDatabase($_ENV['DB_FILE'] ?? 'db/shelf.db');
} catch (Exception $e) {
    die("Error initializing database: " . htmlspecialchars($e->getMessage()));
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>All Products</title>
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <h1>All Products</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>UPC</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    // Fetch all products from the database
                try {
                    $result = $db->fetchAllProducts();
                } catch (Exception $e) {
                    die("Error fetching products: " . htmlspecialchars($e->getMessage()));
                }
                /**
                 * @param array $result
                 * @return void
                 */
                function echoProduct(array $result)
                {
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
                }

                if (!$result) {
                        echo "<tr><td colspan='5'>No products found.</td></tr>";
                    } else {
                    echoProduct($result);
                }
                ?>
            </tbody>
        </table>
        <a href="index.php">Back to Home</a>
    </body>
</html>