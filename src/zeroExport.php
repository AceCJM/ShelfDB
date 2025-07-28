<?php
// src/zeroExport.php
use Fpdf\Fpdf;
use Picqer\Barcode\BarcodeGenerator;
use Picqer\Barcode\BarcodeGeneratorPNG;

if (!isset($_SESSION)) {
    session_start();
}
// Set db path
$dbPath = $_ENV["DB_FILE"] ?? "db/shelf.db";

// Verify if the user is authenticated
require_once dirname(__FILE__) . "/db/userAuth.php";
$userAuth = new UserAuth($dbPath);
if (! $userAuth->isAuthenticated()) {
    header("Location: login.php");
    exit();
}
$userAuth->close(); // Close the database connection after use
$userId = $_SESSION['user_id'] ?? null;

// Include the UserPermissions class for permission checks
require_once dirname(__FILE__) . "/db/userPermissions.php";
$userPermissions = new UserPermissions($dbPath);
// Verify if the user has the required permissions
if (! $userPermissions->checkPermission($userId, 'admin')) {
    header("Location: login.php");
    exit();
}

// Include the AppDatabase class for database operations
require_once dirname(__FILE__) . "/db/database.php";
try {
    $db = new AppDatabase($dbPath);
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle zero export request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Fetch zero export data
        $upcs = [];
        //echo $_POST['upc']; // upcs seperated by spaces or new lines
        if (isset($_POST['upc'])) {
            // Split the input by new lines or spaces and filter out empty values
            $upcs = array_filter(array_map('trim', preg_split('/[\s,]+/', $_POST['upc'])));
        }
        if (empty($upcs)) {
            die("No UPCs provided");
        }

        // Create barcode PDF for export
        include '../vendor/autoload.php'; // Ensure you have the autoload file from Composer
        $pdf = new Fpdf();
        $pdf->AddPage();
        $barcodeWidth = 50;
        $barcodeHeight = 20;
        $cols = 3;
        $rows = 10;
        foreach ($upcs as $i => $upc) {
            if ($i > 0 && $i % ($cols * $rows) === 0) {
                $pdf->AddPage();

            }
            $indexOnPage = $i % ($cols * $rows);
            $col = $indexOnPage % $cols;
            $row = intdiv($indexOnPage, $cols);

            $x = 10 + $col * ($barcodeWidth + 20);
            $y = 10 + $row * ($barcodeHeight + 6);

            // Debug: Output placement info
            // error_log("i=$i, upc=$upc, page=" . (intdiv($i, $cols * $rows) + 1) . ", col=$col, row=$row, x=$x, y=$y");


            $barcode = (new BarcodeGeneratorPNG())->getBarcode($upc, BarcodeGenerator::TYPE_CODE_39);
            $barcodeImage = sys_get_temp_dir() . '/bc_' . uniqid() . '.png';
            file_put_contents($barcodeImage, $barcode);

            $pdf->Image($barcodeImage, $x, $y, $barcodeWidth, $barcodeHeight);
            $pdf->SetXY($x, $y + $barcodeHeight - 2);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell($barcodeWidth, 10, $upc, 0, 'C');

            unlink($barcodeImage);
        }
        $pdf->Output('D', ''); // Download the PDF file
        exit();
    } catch (Exception $e) {
        die("Failed to generate zero export: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Zero Export</title>
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <h1>Zero Export</h1>
        <form action="zeroExport.php" method="post">
            <label for="upc">Enter UPCs (line separated):</label>
            <textarea id="upc" name="upc" required></textarea>
            <button type="submit">Generate Export</button>
        </form>
        <a href="index.php">Back to Home</a>
    </body>
</html>
