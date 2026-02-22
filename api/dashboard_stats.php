<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

include("../config/db.php");

$response = [
    "todayFreight" => 0,
    "monthlyFreight" => 0,
    "pendingInvoices" => 0,
    "totalLRs" => 0
];

// Pending invoices
$result = $conn->query("SELECT COUNT(*) as count FROM invoices WHERE status='pending'");
if ($result) {
    $row = $result->fetch_assoc();
    $response['pendingInvoices'] = $row['count'];
}

// Total LRs
$result = $conn->query("SELECT COUNT(*) as count FROM lorry_receipts");
if ($result) {
    $row = $result->fetch_assoc();
    $response['totalLRs'] = $row['count'];
}

// Today's Freight
$result = $conn->query("
    SELECT IFNULL(SUM(total_freight),0) as total 
    FROM lorry_receipts 
    WHERE DATE(lr_date)=CURDATE()
");
if ($result) {
    $row = $result->fetch_assoc();
    $response['todayFreight'] = $row['total'];
}

// Monthly Freight
$result = $conn->query("
    SELECT IFNULL(SUM(total_freight),0) as total 
    FROM lorry_receipts 
    WHERE MONTH(lr_date)=MONTH(CURDATE()) 
    AND YEAR(lr_date)=YEAR(CURDATE())
");
if ($result) {
    $row = $result->fetch_assoc();
    $response['monthlyFreight'] = $row['total'];
}

echo json_encode($response);
$conn->close();
?>