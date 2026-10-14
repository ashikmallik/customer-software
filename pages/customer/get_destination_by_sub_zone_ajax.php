<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();

$userId = $_SESSION['userid'] ?? null;
$ty = $_SESSION['usertype'] ?? null;

$subZoneId = intval($_GET['subZone'] ?? 0);
$destinations = [];

if ($subZoneId){
    $destinations = $obj->rawSql("SELECT zone_id, zone_name FROM tbl_zone WHERE level = 3 AND parent_id = '$subZoneId'");
} else {
    $destinations = $obj->rawSql("SELECT zone_id, zone_name FROM tbl_zone WHERE level = 3");
}

echo json_encode([
    "destinations" => $destinations,
    "status" => "success"
], JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
exit();