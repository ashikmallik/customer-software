<?php 


if (!isset($obj) || !is_object($obj)) {
    require_once realpath(__DIR__ . '/../../services/Model.php');
    $obj = new Model();
}


if (isset($_GET['action']) && $_GET['action'] === 'speed') {
    header('Content-Type: application/json; charset=utf-8');

    $token = isset($_GET['token']) ? (int)$_GET['token'] : 0;
    if (!$token) {
        echo json_encode(['ok' => false, 'msg' => 'Token missing']);
        exit;
    }

    // customer info
    $agent = $obj->rawSqlSingle("
        SELECT ag_id, ip, mikrotik_id
        FROM tbl_agent
        WHERE ag_id = {$token}
        LIMIT 1
    ");

    if (!$agent) {
        echo json_encode(['ok' => false, 'msg' => 'Customer not found']);
        exit;
    }

    $mkid = (int)$agent['mikrotik_id'];

    $secretName = trim((string)$agent['ip']);

    if (!$mkid || $secretName === '') {
        echo json_encode(['ok' => false, 'msg' => 'Mikrotik_id or secretName missing']);
        exit;
    }

    $traf = $obj->monitorTraficSingle($mkid, $secretName);

    if (!$traf) {
        echo json_encode(['ok' => false, 'msg' => 'No traffic / customer offline / interface not found']);
        exit;
    }

    $t = $traf['traffic'] ?? [];

    $rxBps = isset($t['rx-bits-per-second']) ? (float)$t['rx-bits-per-second'] : 0;
    $txBps = isset($t['tx-bits-per-second']) ? (float)$t['tx-bits-per-second'] : 0;

    echo json_encode([
        'ok' => true,
        't' => time(),
        'name' => $secretName,
        'interface' => $traf['interface'] ?? '',
        'down_mbps' => round($rxBps / 1000000, 2),
        'up_mbps'   => round($txBps / 1000000, 2),
        'raw' => [
            'rx_bps' => $rxBps,
            'tx_bps' => $txBps
        ]
    ]);
    exit;
}
