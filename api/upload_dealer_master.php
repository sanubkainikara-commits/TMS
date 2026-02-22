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

    $sales_org = trim($rows[$i][0]);
    $depot_code = trim($rows[$i][1]);
    $dealer_code = trim($rows[$i][2]);
    $dealer_name = trim($rows[$i][3]);
    $city = trim($rows[$i][4]);
    $route = trim($rows[$i][5]);
    $km_slab = trim($rows[$i][6]);
    $distance_from_wh = (int)$rows[$i][7];
    $per_kg_rate = (float)$rows[$i][8];

    if (!$dealer_code) continue;

    $stmt = $conn->prepare("
        INSERT INTO dealers
        (sales_org, depot_code, dealer_code, dealer_name, city, route, km_slab, distance_from_wh, per_kg_rate)
        VALUES (?,?,?,?,?,?,?,?,?)
        ON DUPLICATE KEY UPDATE
        sales_org=VALUES(sales_org),
        depot_code=VALUES(depot_code),
        dealer_name=VALUES(dealer_name),
        city=VALUES(city),
        route=VALUES(route),
        km_slab=VALUES(km_slab),
        distance_from_wh=VALUES(distance_from_wh),
        per_kg_rate=VALUES(per_kg_rate)
    ");

    $stmt->bind_param(
        "sssssssdi",
        $sales_org,
        $depot_code,
        $dealer_code,
        $dealer_name,
        $city,
        $route,
        $km_slab,
        $distance_from_wh,
        $per_kg_rate
    );

    if (!$stmt->execute()) {
        die("Dealer Insert Error: " . $stmt->error);
    }
}

echo "Dealer Master Uploaded Successfully!";
$conn->close();
?>