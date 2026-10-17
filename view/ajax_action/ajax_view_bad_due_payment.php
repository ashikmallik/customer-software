<?php
session_start();

if (!empty($_SESSION['UserId'])) {

    //========================================
    include '../../model/oop.php';
    include '../../model/Bill.php';
    $obj = new Controller();
    $bill = new Bill();
//========================================

    date_default_timezone_set('Asia/Dhaka');
    $date_time = date('Y-m-d g:i:sA');

    $user_id = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
    $FullName = isset($_SESSION['FullName']) ? $_SESSION['FullName'] : NULL;
    $UserName = isset($_SESSION['UserName']) ? $_SESSION['UserName'] : NULL;
    $PhotoPath = isset($_SESSION['PhotoPath']) ? $_SESSION['PhotoPath'] : NULL;
    $ty = isset($_SESSION['UserType']) ? $_SESSION['UserType'] : NULL;

    //taking month and years
    $day = date('M-Y');

        //$BadData = $obj->details_by_cond('tbl_bad_collection', "ag_status=0");

    $allPayment = $obj->view_all_by_cond("vw_agent", "ag_status='0' ORDER BY ag_id ASC");
    //$allPaymentInfo = $obj->view_all_by_cond("tbl_bad_collection", "ag_status='0' ORDER BY ag_id ASC");
    $duePaymentArrForJson = array();
    $total_due_amount = 0;
    foreach ($allPayment as $value) {

        $id = $value['ag_id'];
        $minusam = $obj->details_by_cond("tbl_agent_activity", "agent_id=$id");
        $minus_amount = 0;
        if($minusam)
        {
            if($minusam['inactive_ammount'] > 0 )
            {
               $minus_amount = $minusam['inactive_ammount'];
            }
        }
        //$taka = ($value['taka']*$value['in_diff_month'])-$minus_amount;
        $taka = $bill->bad_customer_dues($id);
        $duePaymentArrForJson[] = array(
            'agent_id' => $value['ag_id'],
            'agent_name' => $value['ag_name'],
            'customer_id' => $value['cus_id'],
            'agent_address' => $value['ag_office_address'],
            'zone' => $value['zone_name'],
            'mobile' => $value['ag_mobile_no'],
            'speed' => $value['mb'],
            'connection_date' => $value['connection_date'],
            'total_due' => $taka,
            'ip' => $value['ip'],
        );
     $total_due_amount += $taka;
    }// foreach loop ends here


    echo json_encode((array('data' => $duePaymentArrForJson, 'total_bill' => $total_due_amount)));

} else {
    header("location: include/login.php");
}
?>