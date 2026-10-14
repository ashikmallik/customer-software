<?php
$id        = $_GET['id'] ?? null;
$vendor    = $_GET['vendor'] ?? null;
$ip        = $_GET['ip'] ?? null;
$community = $_GET['community'] ?? null;
$olt_source_domain = $obj->getSettingValue('olt_source_domain', 'olt_source_domain') ?? "";


$agents = $obj->getAllData(
    'tbl_agent',
    [],
    'onumac, ip, ag_id, cus_id'
);


$agentMap = [];

foreach ($agents as $agent) {
    if(!empty($agent['onumac'])){
        $agentMap[strtoupper(trim($agent['onumac']))] = $agent;
    }
}
// var_dump($agentMap);
// exit;

?>


<style>
    #loadercontainer{
        height: 600px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .loader {
        width: 100px;
        aspect-ratio: 1;
        display: grid;
        border: 4px solid #0000;
        border-radius: 50%;
        border-color: #ccc #0000;
        animation: l16 1s infinite linear;
    }

    .loader::before,
    .loader::after {
        content: "";
        grid-area: 1/1;
        margin: 2px;
        border: inherit;
        border-radius: 50%;
    }

    .loader::before {
        border-color: #f03355 #0000;
        animation: inherit;
        animation-duration: .5s;
        animation-direction: reverse;
    }

    .loader::after {
        margin: 8px;
    }

    @keyframes l16 {
        100% {
            transform: rotate(1turn);
        }
    }
</style>

<div class="container py-4">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary fw-bold">List</h5>

            <div>
                <span class="badge bg-info text-dark me-3">
                    Total:
                    <span id="totalOnu">0</span>
                </span>

                <button class="btn btn-sm btn-outline-primary" onclick="loadData()">
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">

                <table class="table table-hover table-striped table-bordered align-middle mb-0">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>SL</th>
                            <th>Interface Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <?php if($vendor != 4 && $vendor != 7){ ?>
                                <th>Vendor</th>
                            <?php } ?>
                            <?php if($vendor != 2){ ?>
                                <th>Serial Number</th>
                            <?php } ?>
                            <th>Uptime</th>
                        </tr>
                    </thead>

                    <tbody id="onuTableBody">
                    </tbody>

                </table>

            </div>
        </div>
    </div>
</div>



<div id="loadercontainer" style="height:600px;">
    <div class="loader"></div>
</div>

<script>
const id = <?= json_encode($id) ?>;
const vendor = <?= json_encode($vendor) ?>;
const ip = <?= json_encode($ip) ?>;
const community = <?= json_encode($community) ?>;
const olt_source_domain = <?= json_encode($olt_source_domain) ?>;
const url = `${olt_source_domain}/pages/olt/api_interface_state.php?id=${id}&vendor=${vendor}&ip=${ip}&community=${community}`;

async function loadData() {
    try {
        const agents = <?= json_encode($agentMap) ?>;
        const response = await fetch(url);

        if (!response.ok) {
            throw new Error("HTTP " + response.status);
        }

        const onuPorts = await response.json();

        const totalCount = onuPorts.length;
        document.getElementById("totalOnu").innerText = totalCount;
        let html = "";

        onuPorts.forEach((onu, index) => {

            const serial = (onu.serial_number || "").trim().toUpperCase();
        
            const agent = agents[serial] || {};
        
            const customerName = agent.ag_name || "-";
            const customerIp   = agent.ip || "-";
            const agentId      = agent.ag_id || "";
            const customerId = agent.cus_id || "";
            
            let badgeClass;
            const status = onu.status ?? "";
            switch (status) {
                case 'Up':
                    badgeClass = 'bg-success';
                    break;
                    
                case 'Connected':
                    badgeClass = 'bg-success';
                    break;
            
                case 'Down':
                    badgeClass = 'bg-danger';
                    break;
            
                case 'Testing':
                    badgeClass = 'bg-warning text-dark';
                    break;
            
                case 'Dormant':
                case 'Lower Layer Down':
                    badgeClass = 'bg-secondary';
                    break;
            
                default:
                    badgeClass = 'bg-dark';
            }
        
            
            const serialColumn = (vendor != 2)  ? `<td><code>${onu?.serial_number ?? ""}</code></td>` : "";
            const vendorIdColumn = (vendor != 4 && vendor != 7)  ? `<td>${onu.vendor_id ?? ""}</td>` : "";
            
            html += `
                <tr class="text-center">
                    <td>${index + 1}</td>
                
                    <td>
                        <code>${onu.name ?? ""}</code>
                    </td>
                
                    <td class="text-center align-middle">
                
                        <a href="?page=customer_ledger&token=${encodeURIComponent(agentId)}"
                           class="text-decoration-none fw-semibold">
                            ${customerName}
                        </a>
                
                        <br>
                
                        <small class="text-muted d-block mb-1">
                            <a href="?page=customer_ledger&token=${encodeURIComponent(agentId)}"
                               class="text-decoration-none">
                                ${customerIp}
                            </a>
                        </small>
                
                        ${
                            customerId
                                ? `
                                    <a href="?page=customer_ledger&token=${encodeURIComponent(agentId)}"
                                       class="btn btn-info btn-sm px-2 py-1 waves-effect waves-light">
                                        View
                                    </a>
                                  `
                                : `
                                    <button class="btn btn-secondary btn-sm px-2 py-1" disabled>
                                        No Customer
                                    </button>
                                  `
                        }
                
                    </td>
                    <td>
                        <span class="badge ${badgeClass}">${onu?.status}</span>
                    </td>
                    ${vendorIdColumn}
                    ${serialColumn}
                    <td>${onu.uptime ?? ""}</td>
                </tr>
                `;
        });
        
        const tbody = document.getElementById("onuTableBody");
        
        if (!tbody) {
            console.error("tbody not found");
            return;
        }

tbody.innerHTML = html;
document.getElementById("loadercontainer").style.display = "none";

    } catch (e) {
        console.error(e);
    }
}

loadData();
</script>