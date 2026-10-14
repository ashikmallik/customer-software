<?php
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();

$mkid = $_GET['mkid'] ?? 0;

$data = [];

if ($mkid > 0) {
    $data = $obj->profilelist($mkid);
}

echo json_encode($data);
