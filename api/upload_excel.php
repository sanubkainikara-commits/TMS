<?php
require '../vendor/autoload.php';
include("../config/db.php");

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_FILES['excel']['name']) {

    $fileName = $_FILES['excel']['tmp_name'];
    $spreadsheet = IOFactory::load($fileName);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    // Remove header row
    array_shift($rows);

    foreach ($rows as $row) {

        $invoice_number = $row[0];
        $dealer_name = $row[1];
        $route_name = $row[2];
        $no_of_boxes = $row[3];
        $gross_weight = $row[4];
        $freight_amount = $row[5];
        $invoice_date = $row[6];

        if (!$invoice_number) continue;

        $stmt = $conn->prepare("
            INSERT INTO invoices 
            (invoice_number, dealer_name, route_name, no_of_boxes, gross_weight, freight_amount, invoice_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "sssidds",
            $invoice_number,
            $dealer_name,
            $route_name,
            $no_of_boxes,
            $gross_weight,
            $freight_amount,
            $invoice_date
        );

        $stmt->execute();
    }

    echo "Excel Uploaded Successfully!";
} else {
    echo "No file selected.";
}

$conn->close();
?>
header("Location: ../upload.php?success=1");
exit;