<?php
session_start();

require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();

session_write_close();

header('Content-Type: application/json');

if (empty($_SESSION['userid'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'status' => 'Unauthorized']);
    exit;
}

$mikrotikId = intval($_GET['mkid'] ?? 0);
$mk = $obj->getSingleData('mikrotik_user', ['where' => [['id', '=', $mikrotikId]]]);

if (!$mk) {
    echo json_encode(['success' => false, 'status' => 'Mikrotik not found']);
    exit;
}

$mikrotik = new Mikrotik($mk['mik_ip'], $mk['mik_port'], $mk['mik_username'], $mk['mik_password']);

if (!$mikrotik->connected) {
    echo json_encode(['success' => false, 'status' => 'Failed to connect']);
    exit;
}

// Run a print command; returns [] when the command is not supported on this router
function mkPrint($mikrotik, $command, $query = [])
{
    $result = $mikrotik->comm($command, $query);
    return (is_array($result) && !isset($result['!trap'])) ? array_values($result) : [];
}

function mkCount($mikrotik, $command)
{
    $result = $mikrotik->comm($command, ['count-only' => '']);
    return is_numeric($result) ? (int) $result : 0;
}

// Keep only the given keys of each row
function mkPick($rows, $keys)
{
    return array_map(function ($row) use ($keys) {
        $picked = [];
        foreach ($keys as $key) {
            $picked[$key] = $row[$key] ?? '';
        }
        return $picked;
    }, $rows);
}

$resource = mkPrint($mikrotik, '/system/resource/print')[0] ?? [];
$identity = mkPrint($mikrotik, '/system/identity/print')[0] ?? [];
$routerboard = mkPrint($mikrotik, '/system/routerboard/print')[0] ?? [];

$response = [
    'success' => true,
    'login' => [
        'ip' => $mk['mik_ip'],
        'port' => $mk['mik_port'],
        'username' => $mk['mik_username'],
    ],
    'system' => [
        'identity' => $identity['name'] ?? '',
        'board' => $resource['board-name'] ?? '',
        'model' => $routerboard['model'] ?? '',
        'serial' => $routerboard['serial-number'] ?? '',
        'firmware' => $routerboard['current-firmware'] ?? '',
        'upgrade_firmware' => $routerboard['upgrade-firmware'] ?? '',
        'version' => $resource['version'] ?? '',
        'architecture' => $resource['architecture-name'] ?? '',
        'uptime' => $resource['uptime'] ?? '',
        'cpu' => $resource['cpu'] ?? '',
        'cpu_count' => $resource['cpu-count'] ?? '',
        'cpu_frequency' => $resource['cpu-frequency'] ?? '',
        'cpu_load' => (int) ($resource['cpu-load'] ?? 0),
        'total_memory' => (float) ($resource['total-memory'] ?? 0),
        'free_memory' => (float) ($resource['free-memory'] ?? 0),
        'total_hdd' => (float) ($resource['total-hdd-space'] ?? 0),
        'free_hdd' => (float) ($resource['free-hdd-space'] ?? 0),
    ],
    'health' => mkPick(mkPrint($mikrotik, '/system/health/print'), ['name', 'value', 'type']),
    'counts' => [
        'ppp_active' => mkCount($mikrotik, '/ppp/active/print'),
        'ppp_secret' => mkCount($mikrotik, '/ppp/secret/print'),
    ],
    'api_service' => mkPick(
        mkPrint($mikrotik, '/ip/service/print', ['?name' => 'api']),
        ['port', 'disabled']
    )[0] ?? null,
    'ppp_profiles' => mkPick(
        mkPrint($mikrotik, '/ppp/profile/print'),
        ['name', 'local-address', 'remote-address', 'rate-limit', 'dns-server', 'only-one']
    ),
    'packages' => mkPick(
        mkPrint($mikrotik, '/system/package/print'),
        ['name', 'version', 'build-time', 'disabled']
    ),
];

echo json_encode($response);
