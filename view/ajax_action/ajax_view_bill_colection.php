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
    $currentYear = date("Y");
    $currentMonth = date("m");

  

    if (isset($_GET['zone'])) {
        $zone = $_GET['zone'];
        $allDuePayment = $obj->view_all_by_cond("total_calc", "ag_status='1' AND pay_status='1' AND due_status='0' AND bill_status='0' AND zone = '$zone'  ORDER BY `vw_bill_calc`.`ag_id` ASC");

    }elseif (isset($_GET['customer'])) {
        $customer = $_GET['customer'];
        $allDuePayment = $obj->view_all_by_cond("total_calc", "ag_status='1' AND pay_status='1' AND due_status='0' AND bill_status='0' AND  ag_id = '$customer'  ORDER BY `vw_bill_calc`.`ag_id` ASC");

    } elseif (isset($_GET['user'])) {
        $user = $_GET['user'];
        $allDuePayment = $obj->view_all_by_cond("total_calc", "ag_status='1' AND pay_status='1' AND due_status='0' AND bill_status='0' AND billing_person_id = '$user'  ORDER BY `vw_bill_calc`.`ag_id` ASC");

    } else {
        if($ty == "EO"){
            $allDuePayment = $obj->view_all_by_cond("total_calc", "ag_status='1'  AND due_status='0' AND bill_status='0' AND billing_person_id = '$user_id'  ORDER BY `vw_bill_calc`.`ag_id` ASC");
        }else{
            
            $allDuePayment = $obj->view_all_by_cond("total_calc", "ag_status='1'  AND due_status='0' GROUP BY `total_calc`.`company_id` ORDER BY `total_calc`.`ag_id` ASC");
            
            
        }
        
    }
    
    
    
    $duePaymentArrForJson = array();
    $total_due_amount = 0;




    foreach ($allDuePayment as $value) {
        
        $all_due = (isset($value['total_bill']) ? $value['total_bill'] : 0);
        $previousDue = ( $all_due - (isset($value['taka']) ? $value['taka'] : 0) );
        if( $previousDue < 0) { $previousDue = 0; }
        $total_due_amount += $all_due;
        $cid = (isset($value['company_id']) ? $value['company_id'] : 0);
        

        $total_company =$obj->Total_Count("tbl_agent", "company_id='$cid'");

        
        $total_bill =$obj->get_sum_by_cond("taka","tbl_agent","company_id='$cid'");
        
        $tblzone = $obj->details_by_cond("tbl_zone", "zone_id=".$value['zone']);
        $value['zonename'] = $tblzone['zone_name'];

        $duePaymentArrForJson[] = array(
            'agent_id' => $value['ag_id'],
            'company_id' => $value['company_id'],
            'company_name' => $value['company_name'],
            
            'company_address' => $value['company_address'],
            'zone' => $value['zone_name'],
            'sub_zone_name' => $value['s_zone_name'],
            'phone' => $value['phone'],
            'district' => $value['district'],
            'total_company' => $total_company,
            'print_excel' => 'Download excel file',
            'print_pdf' => 'Download pdf file',
            'previous_due' => $previousDue,
            'bill' => $value['taka'],
            'total_due' => intval($all_due),
            'total_bill' => intval($total_bill),
        );

    }// foreach loop ends here


    echo json_encode((array('data' => $duePaymentArrForJson, 'total_bill' => $total_due_amount,'get'=>$_GET)));

} else {
    header("location: include/login.php");
}
?>