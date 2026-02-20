<?php
include("../config/db.php");

$type = $_GET['type'] ?? '';

if ($type === 'routes') {
    $result = $conn->query("SELECT DISTINCT route_name FROM invoices WHERE status='pending' ORDER BY route_name");
    $routes = [];
    while ($row = $result->fetch_assoc()) {
        $routes[] = $row['route_name'];
    }
    echo json_encode($routes);
}

elseif ($type === 'dealers') {
    $route = $_GET['route'] ?? '';
    $stmt = $conn->prepare("SELECT DISTINCT dealer_name FROM invoices WHERE route_name=? AND status='pending' ORDER BY dealer_name");
    $stmt->bind_param("s", $route);
    $stmt->execute();
    $result = $stmt->get_result();
    $dealers = [];
    while ($row = $result->fetch_assoc()) {
        $dealers[] = $row['dealer_name'];
    }
    echo json_encode($dealers);
}

elseif ($type === 'invoices') {
    $route = $_GET['route'] ?? '';
    $dealer = $_GET['dealer'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM invoices WHERE route_name=? AND dealer_name=? AND status='pending'");
    $stmt->bind_param("ss", $route, $dealer);
    $stmt->execute();
    $result = $stmt->get_result();

    $invoices = [];
    while ($row = $result->fetch_assoc()) {
        $invoices[] = $row;
    }
    echo json_encode($invoices);
}

$conn->close();
?>
