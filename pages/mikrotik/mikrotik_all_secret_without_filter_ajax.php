<?php
session_start();

require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();

header('Content-Type: application/json');

if (!$obj->userMenuePermission('mikrotik_user_summary')) {
    session_write_close();
    echo json_encode(['connection' => false, 'message' => 'Permission denied', 'users' => []]);
    exit;
}
session_write_close();

$mikrotikId = intval($_GET['mkid'] ?? 0);
$mk = $obj->getSingleData('mikrotik_user', ['where' => [['id', '=', $mikrotikId]]]);

if (!$mk) {
    echo json_encode(['connection' => false, 'message' => 'Mikrotik not found', 'users' => []]);
    exit;
}

if (!$obj->checkConnection($mikrotikId)) {
    echo json_encode(['connection' => false, 'message' => 'Not connected', 'mk_ip' => $mk['mik_ip'], 'users' => []]);
    exit;
}

$allSecretData = $obj->viewAllPppSecret($mikrotikId) ?: [];
$allActiveData = $obj->pppeoActiveSecretList($mikrotikId) ?: [];

// active session lookup by name
$activeList = [];
foreach ($allActiveData as $active) {
    if (isset($active['name'])) {
        $activeList[$active['name']] = $active;
    }
}

$users = [];
foreach ($allSecretData as $secret) {
    if (!isset($secret['name'])) {
        continue;
    }
    $active = $activeList[$secret['name']] ?? null;
    $users[] = [
        'name'            => $secret['name'],
        'profile'         => $secret['profile'] ?? '',
        'service'         => $secret['service'] ?? '',
        'disabled'        => (($secret['disabled'] ?? 'false') === 'true'),
        'online'          => $active !== null,
        'address'         => $active['address'] ?? '',
        'uptime'          => $active['uptime'] ?? '',
        'caller_id'       => $active['caller-id'] ?? '',
        'last_logged_out' => $secret['last-logged-out'] ?? '',
        'comment'         => $secret['comment'] ?? '',
    ];
}

echo json_encode([
    'connection' => true,
    'mk_ip'      => $mk['mik_ip'],
    'users'      => $users,
]);
