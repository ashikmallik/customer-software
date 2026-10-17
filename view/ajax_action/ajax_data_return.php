<?php

session_start();


if (!empty($_SESSION['UserId'])) {
    /**
     * Created by mehedi
     * Date: 21/02/17
     * Reason: For return total due , its a time consuming process, so we process so page loading not wait for this
     */
    $user_id = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
    $FullName = isset($_SESSION['FullName']) ? $_SESSION['FullName'] : NULL;
    $UserName = isset($_SESSION['UserName']) ? $_SESSION['UserName'] : NULL;
    $PhotoPath = isset($_SESSION['PhotoPath']) ? $_SESSION['PhotoPath'] : NULL;
    $ty = isset($_SESSION['UserType']) ? $_SESSION['UserType'] : NULL;

//========================================
    include '../../model/oop.php';
    include '../../model/Bill.php';

    $obj = new Controller();
    $bill = new Bill();
//========================================

    date_default_timezone_set('Asia/Dhaka');
    $date_time = date('Y-m-d g:i:sA');
    $date = date('Y-m-d');
    $ip_add = $_SERVER['REMOTE_ADDR'];
    $userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
    $notification = "";
//taking month and years
    $day = date('M-Y');


    $allAgentData = $obj->view_all_by_cond("tbl_agent", "ag_status='1' and pay_status='1' AND due_status='0'");

    $total_due_amount = 0;

    foreach ($allAgentData as $value) {
        $all_due = $bill->get_customer_dues(isset($value['ag_id']) ? $value['ag_id'] : NULL);
        $total_due_amount += $all_due;
    }

    $total_bill_for_month = $obj->get_total_bill_amount();
    $totalCus = $obj->Total_Count('tbl_agent', 'ag_status=1');
    $totalPaid = $obj->Total_Count('tbl_agent', 'ag_status=1 and pay_status=0 and due_status=0');
    $totalNonPaid = $obj->Total_Count('tbl_agent', 'ag_status=1 and pay_status=1');

    echo json_encode(array("duePayment" => $total_due_amount, "totalBill" => $total_bill_for_month, "totalCus" => $totalCus, "totalPaid" => $totalPaid, "totalNonPaid" => $totalNonPaid));
}