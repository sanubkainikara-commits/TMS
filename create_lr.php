<h2 class="mb-4">Create LR (Dispatch Module)</h2>

<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">Route</label>
        <select id="routeSelect" class="form-select"></select>
    </div>

    <div class="col-md-4">
        <label class="form-label">Dealer</label>
        <select id="dealerSelect" class="form-select"></select>
    </div>

    <div class="col-md-4">
        <label class="form-label">Vehicle No</label>
        <input type="text" id="vehicleNo" class="form-control">
    </div>
</div>

<table class="table table-bordered">
    <thead class="table-light">
        <tr>
            <th>Select</th>
            <th>Invoice No</th>
            <th>Boxes</th>
            <th>Gross Weight</th>
        </tr>
    </thead>
    <tbody id="invoiceTable"></tbody>
</table>

<div class="fw-bold mb-3">
    Total Invoices: <span id="totalInvoices">0</span> |
    Total Boxes: <span id="totalBoxes">0</span> |
    Total Weight: <span id="totalWeight">0</span>
</div>

<button class="btn btn-success" onclick="createLR()">Create LR</button>

<script>

async function loadRoutes() {
    const res = await fetch("api/fetch_data.php?type=routes");
    const routes = await res.json();
    const routeSelect = document.getElementById("routeSelect");
    routeSelect.innerHTML = "<option value=''>Select Route</option>";
    routes.forEach(r => {
        routeSelect.innerHTML += `<option value="${r}">${r}</option>`;
    });
}

async function loadDealers(route) {
    const res = await fetch(`api/fetch_data.php?type=dealers&route=${route}`);
    const dealers = await res.json();
    const dealerSelect = document.getElementById("dealerSelect");
    dealerSelect.innerHTML = "<option value=''>Select Dealer</option>";
    dealers.forEach(d => {
        dealerSelect.innerHTML += `<option value="${d}">${d}</option>`;
    });
}

async function loadInvoices(route, dealer) {
    const res = await fetch(`api/fetch_data.php?type=invoices&route=${route}&dealer=${dealer}`);
    const invoices = await res.json();
    const tbody = document.getElementById("invoiceTable");
    tbody.innerHTML = "";

    invoices.forEach(inv => {
        tbody.innerHTML += `
        <tr>
            <td><input type="checkbox" value="${inv.id}" 
                data-boxes="${inv.no_of_boxes}" 
                data-weight="${inv.gross_weight}"
                onchange="calculateTotals()"></td>
            <td>${inv.invoice_number}</td>
            <td>${inv.no_of_boxes}</td>
            <td>${inv.gross_weight}</td>
        </tr>`;
    });
}

function calculateTotals() {
    let totalInvoices=0, totalBoxes=0, totalWeight=0;
    document.querySelectorAll("input[type=checkbox]:checked").forEach(cb => {
        totalInvoices++;
        totalBoxes += parseInt(cb.dataset.boxes);
        totalWeight += parseFloat(cb.dataset.weight);
    });

    document.getElementById("totalInvoices").innerText = totalInvoices;
    document.getElementById("totalBoxes").innerText = totalBoxes;
    document.getElementById("totalWeight").innerText = totalWeight.toFixed(2);
}

async function createLR() {
    const selected = Array.from(document.querySelectorAll("input[type=checkbox]:checked"))
        .map(cb => cb.value);

    if (selected.length === 0) {
        alert("Select at least one invoice");
        return;
    }

    const data = {
        route: document.getElementById("routeSelect").value,
        dealer: document.getElementById("dealerSelect").value,
        vehicle: document.getElementById("vehicleNo").value,
        invoices: selected
    };

    const res = await fetch("api/create_dispatch_lr.php", {
        method: "POST",
        headers: {"Content-Type":"application/json"},
        body: JSON.stringify(data)
    });

    const result = await res.json();

    if(result.success){
        alert("LR Created: " + result.lr_number);
    } else {
        alert(result.message);
    }

    loadRoutes();
}

document.getElementById("routeSelect").addEventListener("change", e=>{
    loadDealers(e.target.value);
});

document.getElementById("dealerSelect").addEventListener("change", e=>{
    loadInvoices(
        document.getElementById("routeSelect").value,
        e.target.value
    );
});

loadRoutes();

</script>