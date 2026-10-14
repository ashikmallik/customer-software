<?php
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();


$obj->raw_sql("update mikrotik_user set status = 1");

$mikrotiks = $obj->getAllData('mikrotik_user', ['where' => ['status', '=', '1']]);

$data = [];

foreach ($mikrotiks as $mk) {

    if ($mk['status'] != 1) {
        continue;
    }

    $status = $obj->getMikrotikUserStatus($mk['id']);

    $total = ($status['online'] ?? 0) + ($status['offline'] ?? 0) + ($status['disabled'] ?? 0);

    $onlinePercent = $total > 0 ? round(($status['online'] / $total) * 100) : 0;
    
 


    $data[] = [
        'id'        => $mk['id'],
        'ip'        => $mk['mik_ip'],
        'username'  => $mk['mik_username'],
        'online'    => $status['online'] ?? 0,
        'offline'   => $status['offline'] ?? 0,
        'disabled'  => $status['disabled'] ?? 0,
        'total'     => $total,
        'percent'   => $onlinePercent
    ];
}

echo json_encode($data);