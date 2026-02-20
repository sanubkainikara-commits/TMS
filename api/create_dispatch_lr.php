<?php
include("../config/db.php");

$data = json_decode(file_get_contents("php://input"), true);

$route = $data['route'];
$dealer = $data['dealer'];
$vehicle = $data['vehicle'];
$invoice_ids = $data['invoices'];

if (empty($invoice_ids)) {
    echo json_encode(["message"=>"No invoices selected"]);
    exit;
}

$totalInvoices = count($invoice_ids);
$totalBoxes = 0;
$totalWeight = 0;
$totalFreight = 0;

foreach ($invoice_ids as $id) {
    $stmt = $conn->prepare("SELECT * FROM invoices WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    $totalBoxes += $row['no_of_boxes'];
    $totalWeight += $row['gross_weight'];
    $totalFreight += $row['freight_amount'];
}

$lr_number = "MLLP/" . date("Y") . "/" . rand(1000,9999);
$lr_date = date("Y-m-d");

$stmt = $conn->prepare("INSERT INTO lorry_receipts 
(lr_number, dealer_name, route_name, vehicle_number, lr_date, total_invoices, total_boxes, total_weight, total_freight) 
VALUES (?,?,?,?,?,?,?,?,?)");

$stmt->bind_param("sssssiddd",
    $lr_number, $dealer, $route, $vehicle,
    $lr_date, $totalInvoices, $totalBoxes,
    $totalWeight, $totalFreight
);

$stmt->execute();
$lr_id = $stmt->insert_id;

foreach ($invoice_ids as $id) {
    $stmt = $conn->prepare("INSERT INTO lr_invoice_map (lr_id, invoice_id) VALUES (?,?)");
    $stmt->bind_param("ii", $lr_id, $id);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE invoices SET status='assigned' WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

echo json_encode(["message"=>"LR Created Successfully! LR No: ".$lr_number]);
$conn->close();
?>
