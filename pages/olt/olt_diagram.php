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

?>

<style>
    #container{
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

<div id="container" style="height:600px;">
    <div class="loader"></div>
</div>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/networkgraph.js"></script>


<script>
const id = <?= json_encode($id) ?>;
const vendor = <?= json_encode($vendor) ?>;
const ip = <?= json_encode($ip) ?>;
const community = <?= json_encode($community) ?>;
const olt_source_domain = <?= json_encode($olt_source_domain) ?>;
const url = `${olt_source_domain}/pages/olt/api_olt_diagram.php?id=${id}&vendor=${vendor}&ip=${ip}&community=${community}`;

async function loadData() {

    try {

        const agents = <?= json_encode($agentMap) ?>;

        const response = await fetch(url);
        console.log('response', response)

        if (!response.ok) {
            throw new Error("HTTP " + response.status);
        }

        const res = await response.json();

        if (!res.success) {
            alert("No data found");
            return;
        }

        Highcharts.chart('container', {

            chart: {
                type: 'networkgraph',
                marginTop: 80
            },

            title: {
                text: res.title || 'OLT → GPON → ONU Network Diagram'
            },

            plotOptions: {
                networkgraph: {
                    keys: ['from', 'to'],
                    layoutAlgorithm: {
                        enableSimulation: false,
                        integration: 'verlet',
                        linkLength: 100
                    }
                }
            },

            series: [{
                marker: {
                    radius: 10
                },
                dataLabels: {
                    enabled: true
                },
                data: res.links,
                nodes: res.nodes
            }]

        });

    } catch (e) {

        console.error(e);

    }

}

loadData();
</script>