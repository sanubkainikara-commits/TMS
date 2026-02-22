<?php
header("Content-Type: application/json");
include("../config/db.php");

$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$route = $_GET['route'] ?? '';
$dealer = $_GET['dealer'] ?? '';

$query = "SELECT * FROM lorry_receipts WHERE 1=1";

if ($from && $to) {
    $query .= " AND lr_date BETWEEN '$from' AND '$to'";
}
if ($route) {
    $query .= " AND route_name='$route'";
}
if ($dealer) {
    $query .= " AND dealer_name='$dealer'";
}

$query .= " ORDER BY lr_date DESC";

$result = $conn->query($query);

$lrs = [];
while ($row = $result->fetch_assoc()) {
    $lrs[] = $row;
}

echo json_encode($lrs);
?>