<!DOCTYPE html>
<html>
<head>
    <title>Create LR - Advanced</title>
    <style>
        body { font-family: Arial; padding:20px; }
        table { width:100%; border-collapse: collapse; margin-top:15px;}
        th, td { border:1px solid #ccc; padding:8px; text-align:center;}
        select, input { padding:6px; margin:5px; }
        .summary { margin-top:15px; font-weight:bold; }
        button { padding:8px 15px; cursor:pointer; }
    </style>
</head>
<body>

<h2>Create LR (Professional Version)</h2>

<label>Client:</label>
<select id="clientSelect"></select>

<label>Sales Org:</label>
<select id="salesOrgSelect">
    <option value="">Select Sales Org</option>
    <option value="AUTO">AUTO</option>
    <option value="INDL">INDL</option>
</select>

<label>Dealer:</label>
<select id="dealerSelect"></select>

<label>Vehicle No:</label>
<input type="text" id="vehicleNo">

<table id="invoiceTable">
    <thead>
        <tr>
            <th>Select</th>
            <th>Invoice</th>
            <th>Boxes</th>
            <th>Gross Weight</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<div class="summary">
    Total Invoices: <span id="totalInvoices">0</span> |
    Total Boxes: <span id="totalBoxes">0</span> |
    Total Weight: <span id="totalWeight">0</span> |
    Total Freight: ₹<span id="totalFreight">0</span>
</div>

<button onclick="createLR()">Create LR</button>

<script>
async function loadClients() {
    const res = await fetch("api/fetch_lr_data.php?type=clients");
    const clients = await res.json();
    const select = document.getElementById("clientSelect");
    select.innerHTML = "<option value=''>Select Client</option>";
    clients.forEach(c=>{
        select.innerHTML += `<option value="${c.client_code}">${c.client_name}</option>`;
    });
}

async function loadDealers() {
    const client = document.getElementById("clientSelect").value;
    const salesOrg = document.getElementById("salesOrgSelect").value;
    if (!client || !salesOrg) return;

    const res = await fetch(`api/fetch_lr_data.php?type=dealers&client=${client}&sales_org=${salesOrg}`);
    const dealers = await res.json();
    const select = document.getElementById("dealerSelect");
    select.innerHTML = "<option value=''>Select Dealer</option>";
    dealers.forEach(d=>{
        select.innerHTML += `<option value="${d.dealer_code}">${d.dealer_name}</option>`;
    });
}

async function loadInvoices() {
    const client = document.getElementById("clientSelect").value;
    const dealer = document.getElementById("dealerSelect").value;
    if (!client || !dealer) return;

    const res = await fetch(`api/fetch_lr_data.php?type=invoices&client=${client}&dealer=${dealer}`);
    const invoices = await res.json();
    const tbody = document.querySelector("#invoiceTable tbody");
    tbody.innerHTML = "";

    invoices.forEach(inv=>{
        tbody.innerHTML += `
        <tr>
            <td><input type="checkbox" value="${inv.id}" 
                data-weight="${inv.gross_weight}" 
                data-boxes="${inv.no_of_boxes}"
                onchange="calculateTotals()"></td>
            <td>${inv.invoice_number}</td>
            <td>${inv.no_of_boxes}</td>
            <td>${inv.gross_weight}</td>
        </tr>`;
    });
}

async function calculateTotals() {
    let totalInv=0,totalBoxes=0,totalWeight=0;

    document.querySelectorAll("input[type=checkbox]:checked").forEach(cb=>{
        totalInv++;
        totalBoxes += parseInt(cb.dataset.boxes);
        totalWeight += parseFloat(cb.dataset.weight);
    });

    document.getElementById("totalInvoices").innerText = totalInv;
    document.getElementById("totalBoxes").innerText = totalBoxes;
    document.getElementById("totalWeight").innerText = totalWeight.toFixed(2);

    // Fetch rate per kg from dealer master
    const dealer = document.getElementById("dealerSelect").value;
    const res = await fetch(`api/get_dealer_rate.php?dealer=${dealer}`);
    const data = await res.json();
    const freight = totalWeight * data.rate;

    document.getElementById("totalFreight").innerText = freight.toFixed(2);
}

async function createLR() {
    const selected = Array.from(document.querySelectorAll("input[type=checkbox]:checked")).map(cb=>cb.value);
    if(selected.length==0){ alert("Select invoices"); return; }

    const payload = {
        client: document.getElementById("clientSelect").value,
        sales_org: document.getElementById("salesOrgSelect").value,
        dealer: document.getElementById("dealerSelect").value,
        vehicle: document.getElementById("vehicleNo").value,
        invoices: selected
    };

    const res = await fetch("api/create_dispatch_lr.php", {
        method:"POST",
        headers:{"Content-Type":"application/json"},
        body: JSON.stringify(payload)
    });

    const result = await res.json();
    if(result.success){
        window.location.href = "print_lr.php?id="+result.lr_id;
    } else {
        alert(result.message);
    }
}

document.getElementById("clientSelect").addEventListener("change", loadDealers);
document.getElementById("salesOrgSelect").addEventListener("change", loadDealers);
document.getElementById("dealerSelect").addEventListener("change", loadInvoices);

loadClients();
</script>

</body>
</html>