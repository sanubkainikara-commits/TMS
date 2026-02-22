<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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

$totalInvoices = count($invoice_ids);
$totalBoxes = 0;
$totalWeight = 0;
$totalFreight = 0;
$totalInvoiceValue = 0;

foreach ($invoice_ids as $id) {

    // 1️⃣ Fetch Invoice
    $stmt = $conn->prepare("SELECT * FROM invoices WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $invoice = $stmt->get_result()->fetch_assoc();

    if (!$invoice) continue;

    $gross_weight = $invoice['gross_weight'];
    $dealer_code = $invoice['dealer_code'];

    $totalBoxes += $invoice['no_of_boxes'];
    $totalWeight += $gross_weight;
    $totalInvoiceValue += $invoice['total_invoice_value'];

    // 2️⃣ Fetch Dealer Rate from Dealer Master
    $stmt = $conn->prepare("SELECT `Per Kg Rate` FROM dealers WHERE `Dealer code`=?");
    $stmt->bind_param("s", $dealer_code);
    $stmt->execute();
    $dealerData = $stmt->get_result()->fetch_assoc();

    if (!$dealerData) {
        echo json_encode(["success"=>false, "message"=>"Dealer not found: ".$dealer_code]);
        exit;
    }

    $rate = $dealerData['Per Kg Rate'];

    // 3️⃣ Calculate Freight
    $freight = $gross_weight * $rate;
    $totalFreight += $freight;
}

$year = date("Y");

// 4️⃣ Generate LR Serial
$stmt = $conn->prepare("SELECT MAX(lr_serial) as last_serial FROM lorry_receipts WHERE YEAR(lr_date)=?");
$stmt->bind_param("i", $year);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$next_serial = $row['last_serial'] ? $row['last_serial'] + 1 : 1;

$lr_number = "MLLP/" . $year . "/" . str_pad($next_serial, 4, "0", STR_PAD_LEFT);
$lr_date = date("Y-m-d");

// 5️⃣ Insert LR
$stmt = $conn->prepare("
    INSERT INTO lorry_receipts 
    (lr_serial, lr_number, dealer_name, route_name, vehicle_number, lr_date,
     total_invoices, total_boxes, total_weight, total_invoice_value, total_freight)
    VALUES (?,?,?,?,?,?,?,?,?,?,?)
");

$stmt->bind_param(
    "isssssiiddd",
    $next_serial,
    $lr_number,
    $dealer,
    $route,
    $vehicle,
    $lr_date,
    $totalInvoices,
    $totalBoxes,
    $totalWeight,
    $totalInvoiceValue,
    $totalFreight
);

if (!$stmt->execute()) {
    echo json_encode(["success"=>false, "message"=>$stmt->error]);
    exit;
}

$lr_id = $stmt->insert_id;

// 6️⃣ Map Invoices + Mark Assigned
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