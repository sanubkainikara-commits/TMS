<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MAYOORAM LLP - TMS</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f6f9;
        }

        .sidebar {
            height: 100vh;
            background: #1f2937;
            color: white;
            position: fixed;
            width: 250px;
        }

        .sidebar .nav-link {
            color: #cbd5e1;
        }

        .sidebar .nav-link:hover {
            background: #374151;
            color: white;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
        }

        .card-dashboard {
            border: none;
            border-radius: 10px;
            color: white;
        }

        .card-blue { background: #2563eb; }
        .card-green { background: #16a34a; }
        .card-orange { background: #f97316; }
        .card-red { background: #dc2626; }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar p-3">
    <h4 class="text-center mb-4">MAYOORAM LLP</h4>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link" href="upload.php">
                <i class="bi bi-upload"></i> Upload Invoices
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" onclick="loadPage('create_lr.html')">
                <i class="bi bi-truck"></i> Create LR
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" onclick="loadPage('reports.html')">
                <i class="bi bi-bar-chart"></i> Reports
            </a>
        </li>
    </ul>
</div>

<!-- Main Content -->
<div class="content">

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
        <div class="container-fluid">
            <span class="navbar-brand">Transport Management System</span>
        </div>
    </nav>

    <!-- Dashboard Cards -->
    <div id="dashboardCards" class="row g-4 mb-4">

        <div class="col-md-3">
            <div class="card card-dashboard card-blue p-3">
                <h6>Today's Freight</h6>
                <h3 id="todayFreight">₹0</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-dashboard card-green p-3">
                <h6>Monthly Freight</h6>
                <h3 id="monthlyFreight">₹0</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-dashboard card-orange p-3">
                <h6>Pending Invoices</h6>
                <h3 id="pendingInvoices">0</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-dashboard card-red p-3">
                <h6>Total LRs</h6>
                <h3 id="totalLRs">0</h3>
            </div>
        </div>

    </div>

    <!-- Dynamic Content Area -->
    <div id="contentArea"></div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function loadPage(page) {
    fetch(page)
    .then(res => res.text())
    .then(data => {
        document.getElementById("dashboardCards").style.display = "none";
        document.getElementById("contentArea").innerHTML = data;
    });
}

async function loadDashboardStats() {
    const res = await fetch("api/dashboard_stats.php");
    const data = await res.json();

    document.getElementById("todayFreight").innerText = "₹" + parseFloat(data.todayFreight || 0).toFixed(2);
    document.getElementById("monthlyFreight").innerText = "₹" + parseFloat(data.monthlyFreight || 0).toFixed(2);
    document.getElementById("pendingInvoices").innerText = data.pendingInvoices;
    document.getElementById("totalLRs").innerText = data.totalLRs;
}

loadDashboardStats();
</script>


</body>
</html>
