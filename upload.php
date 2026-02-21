<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Upload Invoice Excel File</h5>
    </div>

    <div class="card-body">

        <form action="api/upload_excel.php" method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label class="form-label">Select Excel File (.xlsx)</label>
                <input type="file" name="excel" class="form-control" accept=".xlsx" required>
            </div>

            <button type="submit" class="btn btn-success">
                <i class="bi bi-upload"></i> Upload
            </button>

        </form>

        <hr>

        <h6>Required Excel Format:</h6>

        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Invoice No</th>
                        <th>Dealer Name</th>
                        <th>Route Name</th>
                        <th>No of Boxes</th>
                        <th>Gross Weight</th>
                        <th>Freight Amount</th>
                        <th>Invoice Date (YYYY-MM-DD)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>INV001</td>
                        <td>ABC Traders</td>
                        <td>Chennai Route</td>
                        <td>25</td>
                        <td>1200.50</td>
                        <td>4500</td>
                        <td>2026-02-20</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>
