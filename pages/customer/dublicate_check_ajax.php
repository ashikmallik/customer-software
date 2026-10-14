<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();
header('Content-Type: application/json; charset=utf-8');

// POST input
$input = json_decode(file_get_contents('php://input'), true);

if (isset($_GET['duplicate_check'])) {

    $duplicateData = $obj->rawSql("
        SELECT 
            ag.ag_id,
            ag.cus_id,
            ag.ip,
            ag.ag_name,
            ag.ag_status,
            ag.ag_office_address,
            ag.ag_mobile_no,
            ag.mb,
            parent.zone_name AS zone_name,
            sub.zone_name AS sub_zone_name,
            sub.level AS sub_zone_level,
            ag.mikrotik_disconnect,
            ag.billing_person_id,
            cu.UserId AS billing_user_id,
            cu.FullName AS billing_user_name

        FROM vw_agent ag

        LEFT JOIN tbl_zone parent 
            ON ag.zone = parent.zone_id

        LEFT JOIN tbl_zone sub 
            ON ag.sub_zone = sub.zone_id

        LEFT JOIN `_createuser` cu 
            ON cu.UserId = ag.billing_person_id

        WHERE ag.deleted_at IS NULL

        AND ag.cus_id IN (

            SELECT cus_id
            FROM vw_agent
            WHERE deleted_at IS NULL
            GROUP BY cus_id
            HAVING COUNT(cus_id) > 1

        )

        ORDER BY ag.cus_id ASC
    ");

    echo json_encode([
        "status" => "success",
        "total_duplicate" => count($duplicateData),
        "data" => $duplicateData
    ], JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);

    exit;
}else if (isset($_GET['duplicate_check_ip'])) {
    $duplicateData = $obj->rawSql("
        SELECT 
            ag.ag_id,
            ag.cus_id,
            ag.ip,
            ag.ag_name,
            ag.ag_status,
            ag.ag_office_address,
            ag.ag_mobile_no,
            ag.mb,
            parent.zone_name AS zone_name,
            sub.zone_name AS sub_zone_name,
            sub.level AS sub_zone_level,
            ag.mikrotik_disconnect,
            ag.billing_person_id,
            cu.UserId AS billing_user_id,
            cu.FullName AS billing_user_name
        FROM vw_agent ag
        LEFT JOIN tbl_zone parent ON ag.zone = parent.zone_id
        LEFT JOIN tbl_zone sub ON ag.sub_zone = sub.zone_id
        LEFT JOIN `_createuser` cu ON cu.UserId = ag.billing_person_id
        WHERE ag.deleted_at IS NULL
        AND ag.ip IN (
            SELECT ip
            FROM vw_agent
            WHERE deleted_at IS NULL
            GROUP BY ip
            HAVING COUNT(ip) > 1
        )
        ORDER BY ag.ip ASC
    ");

    echo json_encode([
        "status" => "success",
        "total_duplicate" => count($duplicateData),
        "data" => $duplicateData
    ], JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);

    exit;
} else if (isset($_GET['duplicate_check_mobile'])) {
    $duplicateData = $obj->rawSql("
        SELECT 
            ag.ag_id,
            ag.cus_id,
            ag.ip,
            ag.ag_name,
            ag.ag_status,
            ag.ag_office_address,
            ag.ag_mobile_no,
            ag.mb,
            parent.zone_name AS zone_name,
            sub.zone_name AS sub_zone_name,
            sub.level AS sub_zone_level,
            ag.mikrotik_disconnect,
            ag.billing_person_id,
            cu.UserId AS billing_user_id,
            cu.FullName AS billing_user_name
        FROM vw_agent ag
        LEFT JOIN tbl_zone parent ON ag.zone = parent.zone_id
        LEFT JOIN tbl_zone sub ON ag.sub_zone = sub.zone_id
        LEFT JOIN `_createuser` cu ON cu.UserId = ag.billing_person_id
        WHERE ag.deleted_at IS NULL
        AND ag.ag_mobile_no IN (
            SELECT ag_mobile_no
            FROM vw_agent
            WHERE deleted_at IS NULL
            GROUP BY ag_mobile_no
            HAVING COUNT(ag_mobile_no) > 1
        )
        ORDER BY ag.ag_mobile_no ASC
    ");

    echo json_encode([
        "status" => "success",
        "total_duplicate" => count($duplicateData),
        "data" => $duplicateData
    ], JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);

    exit;
} else if (isset($_GET['customer_billing_duplicate'])) {
    $duplicateData = $obj->rawSql("
        SELECT *
        FROM customer_billing
        WHERE cusid IN (
            SELECT cusid
            FROM customer_billing
            GROUP BY cusid
            HAVING COUNT(*) > 1
        )
        ORDER BY cusid
    ");
    echo json_encode([
        "status" => "success",
        "total_duplicate" => count($duplicateData),
        "data" => $duplicateData
    ], JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    exit;
}


// Fetch customer data
$data = $obj->rawSql("
    SELECT 
        ag.ag_id,
        ag.cus_id,
        ag.ip,
        ag.ag_name,
        ag.ag_status,
        ag.ag_office_address,
        ag.ag_mobile_no,
        ag.mb,
        parent.zone_name AS zone_name,
        sub.zone_name AS sub_zone_name,
        sub.level AS sub_zone_level,
        ag.mikrotik_disconnect,
        ag.billing_person_id,
        cu.UserId AS billing_user_id,
        cu.FullName AS billing_user_name
    FROM vw_agent ag
    LEFT JOIN tbl_zone parent ON ag.zone = parent.zone_id
    LEFT JOIN tbl_zone sub ON ag.sub_zone = sub.zone_id
    LEFT JOIN `_createuser` cu ON cu.UserId = ag.billing_person_id
    WHERE ag.deleted_at IS NULL
");


if (!$data) {
    $data = [];
}

echo json_encode([
    "data" => $data
], JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);

exit;
?>
