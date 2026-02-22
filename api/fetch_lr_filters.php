<?php
header("Content-Type: application/json");
include("../config/db.php");

$routes = [];
$dealers = [];

$routeRes = $conn->query("SELECT DISTINCT route_name FROM lorry_receipts");
while ($row = $routeRes->fetch_assoc()) {
    $routes[] = $row['route_name'];
}

$dealerRes = $conn->query("SELECT DISTINCT dealer_name FROM lorry_receipts");
while ($row = $dealerRes->fetch_assoc()) {
    $dealers[] = $row['dealer_name'];
}

echo json_encode([
    "routes" => $routes,
    "dealers" => $dealers
]);
?>