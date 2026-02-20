<?php
include("../config/db.php");

$type = $_GET['type'];
$date = $_GET['date'];

if($type=="daily"){
    $condition = "DATE(lr_date)=?";
}
elseif($type=="weekly"){
    $condition = "YEARWEEK(lr_date,1)=YEARWEEK(?,1)";
}
else{
    $condition = "MONTH(lr_date)=MONTH(?) AND YEAR(lr_date)=YEAR(?)";
}

if($type=="monthly"){
    $stmt = $conn->prepare("
        SELECT route_name,dealer_name,
        COUNT(*) total_lrs,
        SUM(total_boxes) total_boxes,
        SUM(total_weight) total_weight,
        SUM(total_freight) total_freight
        FROM lorry_receipts
        WHERE $condition
        GROUP BY route_name,dealer_name
    ");
    $stmt->bind_param("ss",$date,$date);
}else{
    $stmt = $conn->prepare("
        SELECT route_name,dealer_name,
        COUNT(*) total_lrs,
        SUM(total_boxes) total_boxes,
        SUM(total_weight) total_weight,
        SUM(total_freight) total_freight
        FROM lorry_receipts
        WHERE $condition
        GROUP BY route_name,dealer_name
    ");
    $stmt->bind_param("s",$date);
}

$stmt->execute();
$result=$stmt->get_result();

$data=[];
while($row=$result->fetch_assoc()){
    $data[]=$row;
}

echo json_encode($data);
$conn->close();
?>
