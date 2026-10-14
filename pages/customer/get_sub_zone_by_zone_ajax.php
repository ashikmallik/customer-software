<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();

$userId = $_SESSION['userid'] ?? null;
$ty = $_SESSION['usertype'] ?? null;

$zoneId = intval($_GET['zoneId'] ?? 0);
$subZones = [];

if ($zoneId){
    $subZones = $obj->rawSql("SELECT zone_id, zone_name FROM tbl_zone WHERE level = 2 AND parent_id = '$zoneId'");
}else {
    $subZones = $obj->rawSql("SELECT zone_id, zone_name FROM tbl_zone WHERE level = 2");
}

echo json_encode([
    "subZones" => $subZones,
    "status" => "success"
], JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
exit();