<?php
// src/zeroExport.php
use Fpdf\Fpdf;

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
        $upcs = $_Post['upc'] ?? null;
        if (empty($upcs)) {
            die("No UPCs provided");
        }

        // Create barcode PDF for export
        include 'vendor/fpdf/fpdf/src/Fpdf/Fpdf.php';
        $pdf = new Fpdf();
        $pdf->AddPage();
        $barcodeWidth = 60;
        $barcodeHeight = 20;
        $cols = 3;
        $rows = 10;
        include 'vendor/fobiaweb/barcode39/Barcode39.php';
        foreach ($upcs as $i => $upc) {
            $col = $i % $cols;
            $row = intdiv($i, $cols);
            if ($row >= $rows) break; // Only 3x10 grid

            $x = 10 + $col * ($barcodeWidth + 10); // 10 is left margin and spacing
            $y = 10 + $row * ($barcodeHeight + 10); // 10 is top margin and spacing

            // Create barcode
            $barcode = new Barcode39($upc);
            $barcode->barcode_bar_thin = 2;
            $barcode->draw("bc.png");
            $pdf->Image("bc.png", $x, $y, $barcodeWidth, $barcodeHeight);
        }
        $pdf->Output();
        exit();
    } catch (Exception $e) {
        die("Failed to generate zero export: " . $e->getMessage());
    }
}
