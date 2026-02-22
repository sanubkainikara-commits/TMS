<?php
header("Content-Type: application/json");
include("../config/db.php");

$dealer = $_GET['dealer'] ?? '';

if (!$dealer) {
    echo json_encode(["rate" => 0]);
    exit;
}

$stmt = $conn->prepare("SELECT per_kg_rate FROM dealers WHERE dealer_code=?");
$stmt->bind_param("s", $dealer);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

echo json_encode([
    "rate" => $row['per_kg_rate'] ?? 0
]);
?>