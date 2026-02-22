<?php
header("Content-Type: application/json");
include("../config/db.php");

$type = $_GET['type'] ?? '';

if ($type == "clients") {
    $result = $conn->query("SELECT client_code, client_name FROM clients ORDER BY client_name");
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit;
}

if ($type == "dealers") {
    $client = $_GET['client'] ?? '';
    $sales_org = $_GET['sales_org'] ?? '';

    $stmt = $conn->prepare("SELECT dealer_code, dealer_name FROM dealers WHERE client_code=? AND sales_org=? ORDER BY dealer_name");
    $stmt->bind_param("ss", $client, $sales_org);
    $stmt->execute();
    $res = $stmt->get_result();

    $data = [];
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit;
}

if ($type == "invoices") {
    $client = $_GET['client'] ?? '';
    $dealer = $_GET['dealer'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM invoices WHERE client_code=? AND dealer_code=? AND status='pending'");
    $stmt->bind_param("ss", $client, $dealer);
    $stmt->execute();
    $res = $stmt->get_result();

    $data = [];
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit;
}

echo json_encode([]);
?>