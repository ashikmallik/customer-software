<?php
session_start();

if (!empty($_SESSION['UserId'])) {

//========================================
    include '../../model/oop.php';
    include '../../model/Bill.php';

    $obj = new Controller();
    $bill = new Bill();

    date_default_timezone_set('Asia/Dhaka');
    $date_time = date('Y-m-d g:i:sA');
    $date = date('Y-m-d');
    $userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;


    if (isset($_GET['agent']) && !empty($_GET['agent'])) {

        $agentId = $_GET['agent'];

        $agentData = $obj->details_by_cond('tbl_agent', 'ag_id = ' . $agentId);

    } else {
        die();
    }

    $mobile = isset($agentData['ag_mobile_no']) ? $agentData['ag_mobile_no'] : NULL;
    $agentDue = $bill->get_customer_dues(isset($agentData['ag_id']) ? $agentData['ag_id'] : NULL);
    $smsData = $obj->details_by_cond("sms", "status='2'");


    $smsHeader = isset($smsData['smshead']) ? $smsData['smshead'] : NULL;
    $smsBody = isset($smsData['smsbody']) ? $smsData['smsbody'] : NULL;
    $cus= isset($agentData ['ip']) ? $agentData ['ip'] : NULL;
    $mainSms = "$smsHeader $cus your Due Internet Bill Is: $agentDue Tk, $smsBody ";
    $mobilenum="88".$mobile;
   $obj->sendsms($mainSms, $mobilenum);
    
}
?>