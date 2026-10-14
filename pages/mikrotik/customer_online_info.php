<?php


$token = isset($_GET['token']) ? (int)$_GET['token'] : 0;

$agentInfo = null;
if ($token) {
    $agentInfo = $obj->rawSqlSingle("
        SELECT ag_id, cus_id, ag_name, ip, ag_mobile_no, mikrotik_id
        FROM tbl_agent
        WHERE ag_id = {$token}
        LIMIT 1
    ");
}
?>

<div class="container-fluid py-3">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h4 class="mb-1">Customer Live Speed</h4>
            <div class="text-muted" style="font-size:12px;">
                Customer ID: <b class="bg-success p-1"><?php echo htmlspecialchars($agentInfo['cus_id'] ?? ''); ?></b>
                <?php if($agentInfo): ?>
                    | Customer: <b><?php echo htmlspecialchars($agentInfo['ag_name'] ?? ''); ?></b>
                    | IP/Secret: <b><?php echo htmlspecialchars($agentInfo['ip'] ?? ''); ?></b>
                <?php endif; ?>
            </div>
        </div>

        <span id="liveStatus" class="badge bg-secondary">Idle</span>
    </div>

    <?php if(!$token): ?>
        <div class="alert alert-danger">
            Token missing:
            <b>?page=customer_live_speed&token=AG_ID</b>
        </div>
    <?php elseif(!$agentInfo): ?>
        <div class="alert alert-danger">Customer not found.</div>
    <?php else: ?>

        <div class="row g-3">

            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-semibold">Realtime Speed</div>
                    <div class="card-body">

                        <div class="d-flex gap-2 flex-wrap mb-2">
                            <span class="badge bg-success" id="downText">Download: 0.00 Mbps</span>
                            <span class="badge bg-info text-dark" id="upText">Upload: 0.00 Mbps</span>
                        </div>

                        <div class="text-muted" style="font-size:12px;">
                            <div>Address: <span id="addrText">-</span></div>
                        </div>

                        <hr>


                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-semibold">Live Graph (Last 60s)</div>
                    <div class="card-body">
                        <canvas id="speedChart" height="120"></canvas>

                        <div class="text-muted mt-2" style="font-size:12px;">
                            Graph 0  → customer offline  MikroTik response no rx-bytes/tx-bytes ।
                        </div>
                    </div>
                </div>
            </div>

        </div>

    <?php endif; ?>
</div>


<?php $obj->start_script(); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
(function(){
    const token = <?php echo (int)$token; ?>;

    const statusEl = document.getElementById('liveStatus');
    const downText = document.getElementById('downText');
    const upText   = document.getElementById('upText');
    const uptimeText = document.getElementById('uptimeText');
    const addrText   = document.getElementById('addrText');
    const callerText = document.getElementById('callerText');

    let labels = [];
    let downData = [];
    let upData = [];

    const ctx = document.getElementById('speedChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                { label: 'Download (Mbps)', data: downData, tension: 0.3 },
                { label: 'Upload (Mbps)', data: upData, tension: 0.3 }
            ]
        },
        options: {
            animation: false,
            responsive: true,
            plugins: { legend: { display: true } },
            scales: { y: { beginAtZero: true } }
        }
    });

    function setStatus(cls, text){
        statusEl.className = "badge " + cls;
        statusEl.textContent = text;
    }

    function pushPoint(down, up){
        const nowLabel = new Date().toLocaleTimeString();

        labels.push(nowLabel);
        downData.push(Number(down.toFixed(2)));
        upData.push(Number(up.toFixed(2)));

        if(labels.length > 60){
            labels.shift();
            downData.shift();
            upData.shift();
        }

        chart.update('none');

        downText.textContent = `Download: ${down.toFixed(2)} Mbps`;
        upText.textContent   = `Upload: ${up.toFixed(2)} Mbps`;
    }

    async function poll(){
        if(!token) return;

        try{
            setStatus('bg-warning text-dark', 'Polling...');

            const url = `./pages/mikrotik/customer_live_speed.php?action=speed&token=${token}&_=${Date.now()}`;
            const res = await fetch(url);
            const j = await res.json();
            

            if(!j.ok){
                setStatus('bg-danger', j.msg || 'Offline');
                pushPoint(0,0);
                return;
            }

            const down = Number(j.down_mbps || 0);
            const up   = Number(j.up_mbps || 0);

            setStatus('bg-success', 'Live');

            addrText.textContent   = j.name || '-';

            pushPoint(down, up);

        } catch(e){
            console.error(e);
            setStatus('bg-danger', 'Error');
            pushPoint(0,0);
        }
    }

    poll();
    setInterval(poll, 1000);

})();
</script>


<?php $obj->end_script(); ?>
