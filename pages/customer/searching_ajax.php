<?php

session_start();

require(realpath(__DIR__ . '/../../services/Model.php'));

$user_type = $_SESSION['usertype'];

$obj = new Model();

$keyword = trim($_POST['keyword'] ?? '');

if ($keyword == '') {
    echo json_encode([
        'success' => true,
        'data' => []
    ]);
    exit;
}


$customers = $obj->rawSql("
    SELECT
        ag_id,
        cus_id,
        ip,
        ag_name,
        ag_mobile_no,
        mb,
        taka
    FROM tbl_agent
    WHERE cus_id LIKE '%$keyword%'
    OR ip LIKE '%$keyword%'
    LIMIT 10
");




header('Content-Type: application/json');

echo json_encode([
    'success' => true,
    'data' => $customers,
    'user_type' => $user_type,
    'userid' => $_SESSION['userid']
]);

exit;