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
    $paidday=date('m');
   $paidyear=date('y');

    $user_id = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
    $FullName = isset($_SESSION['FullName']) ? $_SESSION['FullName'] : NULL;
    $UserName = isset($_SESSION['UserName']) ? $_SESSION['UserName'] : NULL;
    $PhotoPath = isset($_SESSION['PhotoPath']) ? $_SESSION['PhotoPath'] : NULL;
    $ty = isset($_SESSION['UserType']) ? $_SESSION['UserType'] : NULL;

        $allPaidPayment = $obj->view_all_by_cond("tbl_agent", "ag_status='1' AND pay_status='0' AND taka !='0' AND (month(entry_date) !=$paidday AND year(entry_date) !=$paidyear) ");
    //$allPaidPayment = $obj->view_all_by_cond("tbl_agent", "ag_status='1' AND pay_status='0' AND taka !='0' AND month(entry_date) !='7' ");

    $paidPaymentArrForJson = array();
    $total_due_amount = 0;

    foreach ($allPaidPayment as $paidPayment) {

        $paidPaymentArrForJson[] = array(
            'agent_id' => $paidPayment['ag_id'],
            'agent_name' => $paidPayment['ag_name'],
            'agent_email' => $paidPayment['ag_email'],
            'customer_id' => $paidPayment['cus_id'],
            'agent_address' => $paidPayment['ag_office_address'],
            'mobile' => $paidPayment['ag_mobile_no'],
            'speed' => $paidPayment['mb'],
            'bill' => $paidPayment['taka'],
            // 'total_due' => $bill->get_customer_dues(isset($paidPayment['ag_id']) ? $paidPayment['ag_id'] : NULL),
            'ip' => $paidPayment['ip'],
        );

    }// foreach loop ends here


    echo json_encode((array('data' => $paidPaymentArrForJson)));

} else {
    header("location: include/login.php");
}
?>