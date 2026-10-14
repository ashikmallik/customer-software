<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();

$ag_id = intval($_GET['ag_id']);

// get mikrotik id
$agent = $obj->raw_sql("SELECT mikrotik_id FROM tbl_agent WHERE ag_id = '$ag_id'");
if (empty($agent)) {
    echo json_encode(['success' => false, 'message' => 'Agent not found']);
    exit;
}

$mikrotik_id = $agent[0]['mikrotik_id'];


// match agent ip/name with mikrotik secret name
$agentInfo = $obj->raw_sql("SELECT ip FROM tbl_agent WHERE ag_id = '$ag_id'");
$secretName = $agentInfo[0]['ip'] ?? '';


$secret = $obj->showSingleSecret($mikrotik_id,$secretName);

if (empty($secret)) {
    echo json_encode(['success' => false, 'message' => 'No secret found']);
    exit;
}

echo json_encode([
    'success' => true,
    'data' => $secret
    
]);