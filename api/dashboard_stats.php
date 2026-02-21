<?php
include("../config/db.php");

$today = date("Y-m-d");
$currentMonth = date("m");
$currentYear = date("Y");

// Today's Freight
$stmt = $conn->prepare("SELECT SUM(total_freight) as total FROM lorry_receipts WHERE lr_date=?");
$stmt->bind_param("s", $today);
$stmt->execute();
$todayFreight = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

// Monthly Freight
$stmt = $conn->prepare("SELECT SUM(total_freight) as total FROM lorry_receipts WHERE MONTH(lr_date)=? AND YEAR(lr_date)=?");
$stmt->bind_param("ss", $currentMonth, $currentYear);
$stmt->execute();
$monthlyFreight = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

// Pending Invoices
$result = $conn->query("SELECT COUNT(*) as count FROM invoices WHERE status='pending'");
$pendingInvoices = $result->fetch_assoc()['count'];

// Total LRs
$result = $conn->query("SELECT COUNT(*) as count FROM lorry_receipts");
$totalLRs = $result->fetch_assoc()['count'];

echo json_encode([
    "todayFreight" => $todayFreight,
    "monthlyFreight" => $monthlyFreight,
    "pendingInvoices" => $pendingInvoices,
    "totalLRs" => $totalLRs
]);

$conn->close();
?>
