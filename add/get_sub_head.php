<?php
session_start();
require(realpath(__DIR__ . '/../model/oop.php'));

$obj = new Controller();

$userId = $_SESSION['userid'] ?? null;
$ty = $_SESSION['usertype'] ?? null;

$acc_id = intval($_POST['acc_id'] ?? 0);

$SubHead = [];

if ($acc_id > 0){

    $SubHead = $obj->raw_sql("SELECT * FROM tbl_accounts_sub_head WHERE acc_head_id=$acc_id");

}

echo json_encode([
    "SubHead" => $SubHead,
    "status" => "success"
], JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);

exit();