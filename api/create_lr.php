C:\xampp\htdocs\tms\api\
<?php
include("../config/db.php");

$data = json_decode(file_get_contents("php://input"), true);

$stmt = $conn->prepare("INSERT INTO lorry_receipts 
(lr_number, sender_name, receiver_name, origin, destination, vehicle_number, freight_amount, lr_date) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
    "ssssssds",
    $data['lr_number'],
    $data['sender_name'],
    $data['receiver_name'],
    $data['origin'],
    $data['destination'],
    $data['vehicle_number'],
    $data['freight_amount'],
    $data['lr_date']
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
