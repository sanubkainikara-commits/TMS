<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../vendor/autoload.php';
include("../config/db.php");

use PhpOffice\PhpSpreadsheet\IOFactory;

if (!isset($_FILES['excel'])) {
    die("No file uploaded.");
}

$file = $_FILES['excel']['tmp_name'];

$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray();

for ($i = 1; $i < count($rows); $i++) {

    $invoice_number = trim($rows[$i][0]);
    $sales_org = trim($rows[$i][1]);
    $dealer_code = trim($rows[$i][2]);
    $dealer_name = trim($rows[$i][3]);
    $route_name = trim($rows[$i][4]);
    $no_of_boxes = (int)$rows[$i][5];
    $gross_weight = (float)$rows[$i][6];
    $invoice_date = date('Y-m-d', strtotime($rows[$i][7]));
    $total_invoice_value = (float)$rows[$i][8];
    $place_of_delivery = trim($rows[$i][9]);
    $pincode = trim($rows[$i][10]);

    if (!$invoice_number || !$dealer_code) {
        continue;
    }

    // Validate dealer exists
    $stmt = $conn->prepare("SELECT id FROM dealers WHERE dealer_code=?");
    $stmt->bind_param("s", $dealer_code);
    $stmt->execute();
    $dealerCheck = $stmt->get_result()->fetch_assoc();

    if (!$dealerCheck) {
        die("Dealer not found in master: " . $dealer_code);
    }

    // Insert invoice
    $stmt = $conn->prepare("
        INSERT INTO invoices 
        (invoice_number, sales_org, dealer_code, dealer_name, route_name,
         no_of_boxes, gross_weight, invoice_date,
         total_invoice_value, place_of_delivery, pincode, status)
        VALUES (?,?,?,?,?,?,?,?,?,?,?, 'pending')
    ");

    $stmt->bind_param(
        "sssssidssss",
        $invoice_number,
        $sales_org,
        $dealer_code,
        $dealer_name,
        $route_name,
        $no_of_boxes,
        $gross_weight,
        $invoice_date,
        $total_invoice_value,
        $place_of_delivery,
        $pincode
    );

    if (!$stmt->execute()) {
        die("Insert Error: " . $stmt->error);
    }
}

echo "Invoice Upload Completed Successfully!";
$conn->close();
?>