<?php
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();

header('Content-Type: application/json');

// ?mkid=X returns a single Mikrotik so the page can load each card on its own
$singleId = isset($_GET['mkid']) ? intval($_GET['mkid']) : null;

if ($singleId) {
    $obj->updateData('mikrotik_user', ['status' => 1], ['id' => $singleId]);
    $mikrotiks = $obj->getAllData('mikrotik_user', ['where' => [['id', '=', $singleId], ['status', '=', '1']]]);
} else {
    $obj->raw_sql("update mikrotik_user set status = 1");
    $mikrotiks = $obj->getAllData('mikrotik_user', ['where' => ['status', '=', '1']]);
}

// Print a command with only the given fields (.proplist keeps the response small)
function mkPrintFields($mikrotik, $command, $fields)
{
    $mikrotik->write($command, false);
    $mikrotik->write('=.proplist=' . $fields);
    $rows = $mikrotik->parseResponse($mikrotik->read(false));
    return (is_array($rows) && !isset($rows['!trap'])) ? $rows : [];
}

function pppName($name)
{
    return trim(str_replace(['<pppoe-', '>'], '', trim((string) $name)));
}

// Online / offline / disabled count of this Mikrotik's secrets that belong to our customers
function mikrotikUserStatus($obj, $mk)
{
    $status = ['connected' => false, 'online' => 0, 'offline' => 0, 'disabled' => 0];

    $mikrotik = new Mikrotik($mk['mik_ip'], $mk['mik_port'], $mk['mik_username'], $mk['mik_password']);
    if (empty($mikrotik->connected)) {
        return $status;
    }
    $status['connected'] = true;

    $allSecrets = mkPrintFields($mikrotik, '/ppp/secret/print', 'name,disabled');
    $activeUsers = mkPrintFields($mikrotik, '/ppp/active/print', 'name');

    $activeList = [];
    foreach ($activeUsers as $au) {
        $name = pppName($au['name'] ?? '');
        if ($name !== '') $activeList[$name] = true;
    }

    $agentList = [];
    foreach ($obj->getAllData('tbl_agent', ['where' => ['mikrotik_id', '=', $mk['id']]]) ?: [] as $agent) {
        $ip = trim((string) ($agent['ip'] ?? ''));
        if ($ip !== '') $agentList[$ip] = true;
    }

    foreach ($allSecrets as $secret) {
        $name = pppName($secret['name'] ?? '');
        if (!isset($agentList[$name])) continue;

        if (($secret['disabled'] ?? 'true') == 'true') {
            $status['disabled']++;
        } elseif (isset($activeList[$name])) {
            $status['online']++;
        } else {
            $status['offline']++;
        }
    }

    return $status;
}

$data = [];

foreach ($mikrotiks as $mk) {

    if ($mk['status'] != 1) {
        continue;
    }

    $status = mikrotikUserStatus($obj, $mk);

    $total = $status['online'] + $status['offline'] + $status['disabled'];

    $onlinePercent = $total > 0 ? round(($status['online'] / $total) * 100) : 0;

    $data[] = [
        'id'        => $mk['id'],
        'ip'        => $mk['mik_ip'],
        'username'  => $mk['mik_username'],
        'connected' => $status['connected'],
        'online'    => $status['online'],
        'offline'   => $status['offline'],
        'disabled'  => $status['disabled'],
        'total'     => $total,
        'percent'   => $onlinePercent
    ];
}

echo json_encode($singleId ? ($data[0] ?? null) : $data);
