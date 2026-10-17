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



// ========== Now remove the customer from bill collection =================
// ========== Who paid already or no dues =================
    foreach ($obj->view_all_by_cond("tbl_agent", "ag_status='1'") as $detailsid) {
        extract($detailsid);
        $id = $detailsid['ag_id'];
         $dues = $obj->column_by_cond("total_bill","vw_bill_calc", "ag_id='$id'");
        //customer remove from bill collection list who have advanced paid or no dues
        if ($dues <= 0) {
            $form_data = array(
                'pay_status' => 0, //Customer paid
                'due_status' => 1, //no dues
            );
            $obj->Update_data("tbl_agent", $form_data, "where ag_id='$id'");
        }
        // this condition for if delete any given entry then comes bill list
        if ($dues > 0) {
            $form_data = array(
                'pay_status' => 1, //Customer not paid
                'due_status' => 0, // dues
            );
            $obj->Update_data("tbl_agent", $form_data, "where ag_id='$id'");
        }
    }

    $allDuePayment = $obj->view_all_by_cond("tbl_agent", "ag_status='1' and pay_status='1' AND due_status='0'");

    $duePaymentArrForJson = array();
    $total_due_amount = 0;
    $i = 1;
    foreach ($allDuePayment as $duePayment) {

        $due = $bill->get_customer_dues(isset($duePayment['ag_id']) ? $duePayment['ag_id'] : NULL);

        $check_due = $due - $duePayment['taka'];

        if($check_due < 0){
            $check_due = $due;
        }

        if ($check_due != 0) {
			
			$total_due_amount += $check_due;

			$duePaymentArrForJson[] = array(
				'sl' => $i,
				'agent_id' => $duePayment['ag_id'],
				'agent_name' => $duePayment['ag_name'],
				'agent_email' => $duePayment['ag_email'],
				'customer_id' => $duePayment['cus_id'],
				'agent_address' => $duePayment['ag_office_address'],
				'mobile' => $duePayment['ag_mobile_no'],
				'speed' => $duePayment['mb'],
				'bill' => $duePayment['taka'],
				'due' => $check_due,
				'total_due' => $due,
				'ip' => $duePayment['ip'],
			);

        }

        $i++;
    }// foreach loop ends here



    /* =================================================
     *  Below code is written for show total bill
     * above the table as calculated with this.
     * Datatable not support the additional data without table
     * or not enough time to search for this.
     ===================================================*/
    if(isset($_GET['all_due_total_bill']) && !empty($_GET['all_due_total_bill'])){

        echo json_encode((array('total_bill' => $total_due_amount)));
    }else{

        echo json_encode((array('data' => $duePaymentArrForJson)));
    }

} else {
    header("location: include/login.php");
}
?>