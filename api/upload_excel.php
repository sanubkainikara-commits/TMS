<?php
require '../vendor/autoload.php';
include("../config/db.php");

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excelFile'])) {

    $fileTmpPath = $_FILES['excelFile']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($fileTmpPath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $insertedCount = 0;

        // Skip header row (row 0)
        for ($i = 1; $i < count($rows); $i++) {

            if (empty($rows[$i][0])) {
                continue; // skip empty rows
            }

            $lr_number = trim($rows[$i][0]);
            $sender = trim($rows[$i][1]);
            $receiver = trim($rows[$i][2]);
            $origin = trim($rows[$i][3]);
            $destination = trim($rows[$i][4]);
            $vehicle = trim($rows[$i][5]);
            $freight = floatval($rows[$i][6]);
            $date = trim($rows[$i][7]);

            $stmt = $conn->prepare("INSERT INTO lorry_receipts 
            (lr_number, sender_name, receiver_name, origin, destination, vehicle_number, freight_amount, lr_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param(
                "ssssssds",
                $lr_number,
                $sender,
                $receiver,
                $origin,
                $destination,
                $vehicle,
                $freight,
                $date
            );

            if ($stmt->execute()) {
                $insertedCount++;
            }

            $stmt->close();
        }

        echo "<h2>Excel Uploaded Successfully!</h2>";
        echo "<p>Total LRs Inserted: " . $insertedCount . "</p>";
        echo "<a href='../index.html'>Go Back</a>";

    } catch (Exception $e) {
        echo "Error reading Excel file: " . $e->getMessage();
    }

} else {
    echo "Invalid Request";
}
?>
