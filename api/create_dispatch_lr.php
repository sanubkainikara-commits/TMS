<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header("Content-Type: application/json");

include("../config/db.php");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success"=>false, "message"=>"Invalid JSON request"]);
    exit;
}

$route = $data['route'] ?? '';
$dealer = $data['dealer'] ?? '';
$vehicle = $data['vehicle'] ?? '';
$invoice_ids = $data['invoices'] ?? [];

if (empty($invoice_ids)) {
    echo json_encode(["success"=>false, "message"=>"No invoices selected"]);
    exit;
}

$route = $data['route'] ?? '';
$dealer = $data['dealer'] ?? '';
$vehicle = $data['vehicle'] ?? '';
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

$year = date("Y");

// Get last serial for current year
$stmt = $conn->prepare("SELECT MAX(lr_serial) as last_serial FROM lorry_receipts WHERE YEAR(lr_date)=?");
$stmt->bind_param("i", $year);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$next_serial = $row['last_serial'] ? $row['last_serial'] + 1 : 1;

// Format LR Number
$lr_number = "MLLP/" . $year . "/" . str_pad($next_serial, 4, "0", STR_PAD_LEFT);
$lr_date = date("Y-m-d");

// Correct INSERT
$stmt = $conn->prepare("
    INSERT INTO lorry_receipts 
    (lr_serial, lr_number, dealer_name, route_name, vehicle_number, lr_date, total_invoices, total_boxes, total_weight, total_freight)
    VALUES (?,?,?,?,?,?,?,?,?,?)
");

// Correct bind types
$stmt->bind_param("isssssiidd",
    $next_serial,      // i
    $lr_number,        // s
    $dealer,           // s
    $route,            // s
    $vehicle,          // s
    $lr_date,          // s
    $totalInvoices,    // i
    $totalBoxes,       // i
    $totalWeight,      // d
    $totalFreight      // d
);

if (!$stmt->execute()) {
    echo json_encode(["success"=>false, "message"=>$stmt->error]);
    exit;
}

$lr_id = $stmt->insert_id;
foreach ($invoice_ids as $id) {
    $stmt = $conn->prepare("INSERT INTO lr_invoice_map (lr_id, invoice_id) VALUES (?,?)");
    $stmt->bind_param("ii", $lr_id, $id);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE invoices SET status='assigned' WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

echo json_encode([
    "success" => true,
    "lr_id" => $lr_id,
    "lr_number" => $lr_number,
    "message" => "LR Created Successfully!"
]);
$conn->close();
?>
