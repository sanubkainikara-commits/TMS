<?php
$message = "";

if (isset($_GET['success'])) {
    $message = "Excel Uploaded Successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Invoices</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f4f6f9;
            padding: 50px;
        }

        .upload-box {
            width: 500px;
            margin: auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 6px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }

        input[type="file"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #2c3e50;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 15px;
        }

        button:hover {
            background-color: #1a252f;
        }

        .note {
            margin-top: 15px;
            font-size: 13px;
            color: #666;
        }

        .success {
            margin-top: 15px;
            padding: 10px;
            background: #eafaf1;
            border: 1px solid #2ecc71;
            color: #1e8449;
            border-radius: 4px;
        }

        .sample-link {
            display: inline-block;
            margin-top: 10px;
            font-size: 13px;
        }
    </style>
</head>
<body>

<div class="upload-box">

    <h2>Upload Invoice Excel</h2>

    <form action="api/upload_excel.php" method="POST" enctype="multipart/form-data">
        
        <label>Select Excel File (.xlsx)</label>
        <input type="file" name="excel" required>

        <button type="submit">Upload File</button>

    </form>

    <?php if ($message): ?>
        <div class="success"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="note">
        Please ensure the Excel file matches the required format.
    </div>

    <a href="download_sample.php" class="sample-link">
        Download Sample Format
    </a>

</div>

</body>
</html>