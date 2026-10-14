<?php
session_start();
header('Content-Type: application/json');

require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();

if (isset($_POST['ip'])) {

    $ip = trim($_POST['ip']);
    $status = 'expire'; // default

    $ipSafe = addslashes($ip);

    $data = $obj->rawSql("SELECT * FROM tbl_agent WHERE ip = '$ipSafe' LIMIT 1");
    $data = !empty($data) ? $data[0] : [];

    if (!empty($data)) {

        $mkid = $data['mikrotik_id'];

        // ✅ Mikrotik connection check
        if ($obj->checkConnection($mkid)) {

            $secrets = $obj->viewAllPppSecret($mkid);

            if (!empty($secrets)) {
                foreach ($secrets as $secret) {
                    if (isset($secret['name']) && $secret['name'] == $ip) {
                        $status = ($secret['disabled'] == 'false') ? 'enable' : 'expire';
                        break;
                    }
                }
            }

        } else {
            // ❗ only when NOT connected
            $status = 'no_connection';
        }

    } else {
        $status = 'not_found';
    }

    echo json_encode(['status' => $status]);
    exit;
}