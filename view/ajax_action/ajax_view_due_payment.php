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
    
     if (isset($_GET['zone'])) {
        $zone = $_GET['zone'];
        $zoonqery ="AND zone=$zone";
    } else {
        $zoonqery="";
    }

// ==========check AND bill generate ===================================================

           /* =============================================
         * =============================================
         *  Here we check_bill_create_or_not by check in the
         *  "monthly_bill_making_check" table if this month bill is
         *  generated or not. If not generated mean ($cnt!=1)
         *  then make all the customer in a due user by pay_status = 1
         *  and insert new row in "monthly_bill_making_check".
         *  Here we check all the customer one by one
         *  if they paid or not by get_customer_dues() function
         *  if they don't have dues then they are removed by
         *  pay_status and due_status both = 0.
         *  if they have dues then they are pay_status = 1
         *  N.B pay_status = 1 mean customer has to pay.
         *customer remove from bill collection list who have advanced paid or no dues
         =============***********========================
         *** pay status::1=Customer didn't pay yet && 0= Customer already paid
         *** due_status::0=Customer have due && 1=Customer dont have due but may be advance
         *** bill_status::0=bill didnt pay bill && 1=Customer alreay paid  bill current month or advance by previous month
         * =============================================
         * =============================================*/
        $day = date('M-Y');//Insert current month date for future check weather bill is created or notification
        $cnt = $obj->Total_Count("monthly_bill_making_check", "month_year='$day'");
        if ($cnt != 1) {
            
            $form_data = array(
                'pay_status' => 1, //Customer didn't pay yet
                'due_status' => 0, // dues
                'bill_status' => 0 // bill will be collect
            );
            $obj->Update_data("tbl_agent", $form_data, "pay_status!=5");

            $form_data2 = array(
                'month_year' => $day 
            );
            $obj->Insert_data("monthly_bill_making_check", $form_data2);
            
            
            // bill generate 
            foreach ($obj->view_all_by_cond("tbl_agent", "ag_status='1'") as $detailsid) {
                extract($detailsid);
                $id = $detailsid['ag_id'];
                // $dues = $bill->get_customer_dues($id);
                 $dues = $obj->column_by_cond("total_bill","vw_bill_calc", "ag_id='$id'");
                
                if ($dues <= 0) {
                    $form_data = array(
                        'pay_status' => 0, //Customer paid
                        'due_status' => 1, //no dues
                        'bill_status' => 1 //already collected
                    );
                    $obj->Update_data("tbl_agent", $form_data, "where ag_id='$id'");
                }
                // this condition for if delete any given entry then comes bill list
                if ($dues > 0) {
                    $form_data = array(
                        'pay_status' => 1, //Customer not paid
                        'due_status' => 0, // dues
                    );
                    // 'bill_status' => 0, // bill will be collect but its for onetime
                    $obj->Update_data("tbl_agent", $form_data, "where ag_id='$id'");
                }
            }
        }

        // ========== Now remove the customer from bill collection =================
        // ========== Who paid already or no dues =================
        // foreach ($obj->view_all_by_cond("tbl_agent", "ag_status='1'") as $detailsid) {
        //     extract($detailsid);
        //     $id = $detailsid['ag_id'];
        //     // $dues = $bill->get_customer_dues($id);
        //      $dues = $obj->column_by_cond("total_bill","vw_bill_calc", "ag_id='$id'");
            
        //     if ($dues <= 0) {
        //         $form_data = array(
        //             'pay_status' => 0, //Customer paid
        //             'due_status' => 1, //no dues
        //             'bill_status' => 1 //already collected
        //         );
        //         $obj->Update_data("tbl_agent", $form_data, "where ag_id='$id'");
        //     }
        //     // this condition for if delete any given entry then comes bill list
        //     if ($dues > 0) {
        //         $form_data = array(
        //             'pay_status' => 1, //Customer not paid
        //             'due_status' => 0, // dues
        //         );
        //         // 'bill_status' => 0, // bill will be collect but its for onetime
        //         $obj->Update_data("tbl_agent", $form_data, "where ag_id='$id'");
        //     }
        // }
//===========================End check =======================================================================

   
    // if (isset($_GET['zone'])) {
    //     $zone = $_GET['zone'];
    //     $allDuePayment = $obj->view_all_by_cond("vw_bill_calc", "ag_status='1' AND pay_status='1' AND due_status='0' AND zone = $zone  ORDER BY `vw_bill_calc`.`ag_id` DESC");
    // } else {
    //     $allDuePayment = $obj->view_all_by_cond("vw_bill_calc", "ag_status='1' and pay_status='1' AND due_status='0'  ORDER BY `vw_bill_calc`.`ag_id` ASC");
    // }
    
    $allDuePayment = $obj->view_all_by_cond("vw_bill_calc", "ag_status='1' and pay_status='1' AND due_status='0' $zoonqery  ORDER BY `vw_bill_calc`.`ag_id` ASC");
    $duePaymentArrForJson = array();
    $total_due_amount = 0;

    foreach ($allDuePayment as $value) {
         extract($value);
        // $all_due = $bill->get_customer_dues(isset($value['ag_id']) ? $value['ag_id'] : NULL);
        $all_due = (isset($value['total_bill']) ? $value['total_bill'] : 0);

        $previousDue = ( $all_due - (isset($value['taka']) ? $value['taka'] : 0) );

        if( $previousDue < 0) { $previousDue = 0; }

        $total_due_amount += $all_due;
        
        
        $bp =$obj->details_by_cond('_createuser', 'UserId = ' . @$value['billing_person_id'] . '');  

        $duePaymentArrForJson[] = array(
            'agent_id' => $value['ag_id'],
            'agent_name' => $value['ag_name'],
            'customer_id' => $value['cus_id'],
            'agent_address' => $value['ag_office_address'],
            'zone' => $obj->column_by_cond("zone_name","tbl_zone", "zone_id=$zone"),
            // 'zone' => $zone,
            'mobile' => $value['ag_mobile_no'],
            'speed' => $value['mb'],
            'house' => $value['house'],
            'road' => $value['road'],
            'previous_due' => $previousDue,
            'bill' => $value['taka'],
            'bill_date' => $value['bill_date'] . "-" . date("M") . "," . date("y"),
            'total_due' => intval($all_due),
            'ip' => $value['ip'],
            'bp' => @$bp['FullName'],
        );

    }// foreach loop ends here


    echo json_encode((array('data' => $duePaymentArrForJson, 'total_bill' => $total_due_amount)));

} else {
    header("location: include/login.php");
}
?>