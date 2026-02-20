<?php
include("config/db.php");

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM lorry_receipts WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$lr = $stmt->get_result()->fetch_assoc();

if(!$lr){ die("Invalid LR"); }

$inv_stmt = $conn->prepare("
    SELECT i.* FROM invoices i
    JOIN lr_invoice_map m ON i.id = m.invoice_id
    WHERE m.lr_id=?
");
$inv_stmt->bind_param("i",$id);
$inv_stmt->execute();
$invoices = $inv_stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>LR Print</title>
<style>
body { font-family: Arial; width:210mm; margin:auto; }
table { width:100%; border-collapse:collapse; margin-top:10px; }
th,td { border:1px solid black; padding:6px; text-align:center; }
.header { text-align:center; border-bottom:2px solid black; padding-bottom:10px; }
.summary { margin-top:10px; font-weight:bold; }
</style>
</head>

<body onload="window.print()">

<div class="header">
    <h2>MAYOORAM LLP</h2>
    <p>Kochal ITC Warehouse, Alston Builders</p>
    <p>GST: 3E2XXXXXXX | Phone: 950000000</p>
</div>

<p><strong>LR No:</strong> <?= $lr['lr_number'] ?> 
&nbsp;&nbsp;&nbsp;
<strong>Date:</strong> <?= $lr['lr_date'] ?></p>

<p><strong>Route:</strong> <?= $lr['route_name'] ?> 
&nbsp;&nbsp;&nbsp;
<strong>Dealer:</strong> <?= $lr['dealer_name'] ?></p>

<p><strong>Vehicle:</strong> <?= $lr['vehicle_number'] ?></p>

<table>
<tr>
<th>Invoice No</th>
<th>Boxes</th>
<th>Gross Weight</th>
<th>Freight</th>
</tr>

<?php while($row=$invoices->fetch_assoc()): ?>
<tr>
<td><?= $row['invoice_number'] ?></td>
<td><?= $row['no_of_boxes'] ?></td>
<td><?= $row['gross_weight'] ?></td>
<td><?= $row['freight_amount'] ?></td>
</tr>
<?php endwhile; ?>
</table>

<div class="summary">
Total Invoices: <?= $lr['total_invoices'] ?> |
Total Boxes: <?= $lr['total_boxes'] ?> |
Total Weight: <?= number_format($lr['total_weight'],2) ?> |
Total Freight: ₹<?= number_format($lr['total_freight'],2) ?>
</div>

</body>
</html>
