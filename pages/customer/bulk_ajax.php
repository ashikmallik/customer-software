<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();
header('Content-Type: application/json; charset=utf-8');

// POST input
$input = json_decode(file_get_contents('php://input'), true);

if (isset($_POST['selectedCustomerIds']) && is_array($_POST['selectedCustomerIds'])) {
    $selectedCustomerIds = $_POST['selectedCustomerIds'];

    // Zone update
    if(isset($_POST['selectedZone'])){
        $selectedZone = $_POST['selectedZone'];
        $selectedSubZone = $_POST['selectedSubZone'] ?? null;
        foreach ($selectedCustomerIds as $value) {
            $obj->rawSql("UPDATE tbl_agent SET zone = '$selectedZone' WHERE ag_id = '$value'");
            $obj->rawSql("UPDATE tbl_agent SET sub_zone = '$selectedSubZone' WHERE ag_id = '$value'");
         
        }
        echo json_encode([
            'status'=>'success',
            'zone'=>$selectedZone,
            'sub_zone'=>$selectedSubZone,
            'ids'=>$selectedCustomerIds
        ]);
        exit;   
    }



    // Package update
    if(isset($_POST['selectedPackage'])){
        $selectedPackage = $_POST['selectedPackage'];
        $expackage = $obj->getSingleData('tbl_package', ['where' => ['package_id', '=', $selectedPackage]]);
        $netspeed = $expackage['net_speed'] ;
        foreach ($selectedCustomerIds as $value) {
            $obj->rawSql("UPDATE tbl_agent SET mb = '$netspeed' WHERE ag_id = '$value'");
            $obj->rawSql("CALL billUpdate(" . $value . ", 'package', " . $expackage['bill_amount'] . ", 0, 'change', 0, 1,'')");
        }
        echo json_encode([
            'status'=>'success',
            'package'=>$selectedPackage,
            'ids'=>$selectedCustomerIds
        ]);
        exit;
    }

    if(isset($_POST['newStatus'])){
        $status = $_POST['newStatus'];
        foreach ($selectedCustomerIds as $value) {
            $obj->rawSql("UPDATE tbl_agent SET ag_status = '$status' WHERE ag_id = '$value'");
        }
        echo json_encode([
            'status'=>'success',
            'package'=>$status,
            'ids'=>$selectedCustomerIds
        ]);
        exit;
    }

    // Mikrotik Disconnect Day update
    if(isset($_POST['disconnectDay'])){
        $disconnectDay = intval($_POST['disconnectDay']); // sudhu number
        foreach ($selectedCustomerIds as $value) {
            $obj->rawSql("UPDATE tbl_agent SET mikrotik_disconnect = $disconnectDay WHERE ag_id = '$value'");
        }
        echo json_encode([
            'status'=>'success',
            'disconnectDay'=>$disconnectDay,
            'ids'=>$selectedCustomerIds
        ]);
        exit;
    }

    if(isset($_POST['disconnectDay'])){
        $disconnectDay = intval($_POST['disconnectDay']); // sudhu number
        foreach ($selectedCustomerIds as $value) {
            $obj->rawSql("UPDATE tbl_agent SET mikrotik_disconnect = $disconnectDay WHERE ag_id = '$value'");
        }
        echo json_encode([
            'status'=>'success',
            'disconnectDay'=>$disconnectDay,
            'ids'=>$selectedCustomerIds
        ]);
        exit;
    }
    //Change billing person
    
    if(isset($_POST['billingPersonSelect'])){
        $billingPersonSelect = intval($_POST['billingPersonSelect']); // sudhu number
        foreach ($selectedCustomerIds as $value) {
            $obj->rawSql("UPDATE tbl_agent SET billing_person_id = $billingPersonSelect WHERE ag_id = '$value'");
        }
        echo json_encode([
            'status'=>'success',
            'billingPersonSelect'=>$billingPersonSelect,
            'ids'=>$selectedCustomerIds
        ]);
        exit;
    }
    
     // custom Delete
        if (isset($_POST['customer_bulk_delete'])) {
            $selectedCustomerIds = $_POST['selectedCustomerIds'];
            $dateTime = date('Y-m-d H:i:s');


            foreach ($selectedCustomerIds as $value) {
            //    $obj->rawSql("UPDATE  tbl_agent SET deleted_at = '$dateTime' WHERE ag_id = ''");
               $obj->updateData('tbl_agent', ['deleted_at' => date('Y-m-d H:i:m')], ['ag_id' => $value]);
            }
            $obj->createActivityLog($_SESSION['userid'], '4', '2', 'tbl_agent', null, null, null, 'Temporary Deleted '.count($selectedCustomerIds).' Customers' , null, null, false, false, null, null);

            echo json_encode([
                'status' => 'success',
                'ids' => $selectedCustomerIds
            ]);
            exit;
        }
        
        
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
