C:\xampp\htdocs\tms\api\
<?php
include("../config/db.php");

$date = $_GET['date'];

$stmt = $conn->prepare("SELECT * FROM lorry_receipts WHERE lr_date = ?");
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
$totalFreight = 0;

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
    $totalFreight += $row['freight_amount'];
}

echo json_encode([
    "records" => $data,
    "totalFreight" => $totalFreight
]);

$stmt->close();
$conn->close();
?>
