<?php
$file = "sample_invoice_format.xlsx";

if (!file_exists($file)) {
    die("Sample file not found.");
}

header("Content-Description: File Transfer");
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
header("Expires: 0");
header("Cache-Control: must-revalidate");
header("Pragma: public");
header("Content-Length: " . filesize($file));

readfile($file);
exit;
?>