<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : NULL;
$notification = "";
$day = date('M-Y');
$whereCond = '';

$obj->Update_data("sms", ['smshead' => isset($_GET['excPartialPaid']) && $_GET['excPartialPaid'] == 'active' ? 'active' : 'inactive'], " status='1' ");

if (isset($_GET['excPartialPaid']) && $_GET['excPartialPaid'] == 'active') {
    $whereCond = 'AND tbl_agent.bill_status != 1';
}

// =======================
// Disconnect filter check
// =======================
$disconnectFilter = "";
if (isset($_GET['mikrotik_disconnect']) && $_GET['mikrotik_disconnect'] != 0) {
    $disconnectDay = $_GET['mikrotik_disconnect'];
    $disconnectFilter = " AND tbl_agent.mikrotik_disconnect = '$disconnectDay' ";
}

// =======================
// Zone wise or All zones
// =======================
if (isset($_GET['zone']) && !empty($_GET['zone'])) {
    $zone = $_GET['zone'];

    if ($zone == 'x') {
        $smsClient = $obj->rawSql(
            "SELECT * 
            FROM tbl_agent 
            LEFT JOIN customer_billing ON tbl_agent.ag_id = customer_billing.agid 
            WHERE tbl_agent.ag_status='1' 
            AND customer_billing.dueadvance > 0 
            AND tbl_agent.deleted_at IS NULL 
            $whereCond $disconnectFilter"
        );
    } else {
        $zone = (int)$zone; // নিরাপত্তার জন্য cast
        $smsClient = $obj->rawSql(
            "SELECT * 
            FROM tbl_agent 
            LEFT JOIN customer_billing ON tbl_agent.ag_id = customer_billing.agid 
            WHERE tbl_agent.ag_status='1' 
            AND customer_billing.dueadvance > 0 
            AND tbl_agent.deleted_at IS NULL 
            AND zone = $zone 
            $whereCond $disconnectFilter"
        );
    }
} else {
    die("Zone not selected");
}

// var_dump($smsClient);

// =======================
// SMS Sending
// =======================
if (isset($_GET['submitbtn']) && $_GET['randcheck'] == $_SESSION['rand']) {
    $_SESSION['rand'] = 2;
    $smsArray = [];
    $smsI = 0;
    

    foreach ($smsClient as $value) {
        $mobile = $value['ag_mobile_no'] ?? NULL;
        $ip     = $value['ip'] ?? NULL;
        $cid    = $value['cus_id'] ?? NULL;
        $agid   = $value['ag_id'] ?? NULL;
        $ag_name= $value['ag_name'] ?? NULL;

        $all_duee = $obj->details_by_cond("customer_billing", "agid='$agid'");
        $all_d    = $all_duee['dueadvance'];

        $value1 = $obj->details_by_cond("sms", "status='1'");
        $sms_b  = $value1['smsbody'] ?? "";

        $var_replacement = [
            '{CUSTOMER_NAME}' => $ag_name,
            '{DUE_AMOUNT}'    => $all_d,
            '{CUSTOMER_ID}'   => $cid,
            '{IP_ADDRESS}'    => $ip,
        ];

        $sms_b = strtr($sms_b, $var_replacement);
        $mobilenum = "88" . $mobile;

        $smsArray[$smsI++] = ["to" => $mobilenum, "message" => $sms_b];
    }
    
        
    $result = $obj->sms_send($smsArray);

    if (isset($result['response_code']) && $result['response_code'] == '1007') {
        echo "<script>
            alert('❌ Something went wrong! error code (1007)!');
            window.location='?page=due_sms';
        </script>";
        exit;
    } elseif (isset($result['response_code']) && $result['response_code'] == '1000') {
        echo "<script>
            alert('✅ SMS Successfully Sent!');
            window.location='?page=due_sms';
        </script>";
        exit;
    }
}
?>

<script>
    window.location = "?page=due_sms";
</script>
