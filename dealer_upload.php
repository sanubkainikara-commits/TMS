<!DOCTYPE html>
<html>
<head>
    <title>Upload Dealer Master</title>
</head>
<body>

<h2>Upload Dealer Master</h2>

<form action="api/upload_dealer_master.php" method="post" enctype="multipart/form-data">
    <input type="file" name="excel" required>
    <button type="submit">Upload Dealer Master</button>
</form>

</body>
</html>