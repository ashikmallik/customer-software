<?php

session_start();

$userid = $user_id = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$FullName = isset($_SESSION['FullName']) ? $_SESSION['FullName'] : NULL;
$UserName = isset($_SESSION['UserName']) ? $_SESSION['UserName'] : NULL;
$PhotoPath = isset($_SESSION['PhotoPath']) ? $_SESSION['PhotoPath'] : NULL;
$ty = isset($_SESSION['UserType']) ? $_SESSION['UserType'] : NULL;

if (!empty($_SESSION['UserId'])) {

//========================================
    include '../../model/oop.php';
    include '../../model/Bill.php';
    include('../../model/Mikrotik.php');
    $obj = new Controller();
    $bill = new Bill();


    // $mikrotikConnect = false;

    // if ($obj->tableExists('mikrotik_user')) {

    //     $mikrotikLoginData = $obj->details_by_cond('mikrotik_user', 'id = 1');
    //     $mikrotik = new Mikrotik($mikrotikLoginData['mik_ip'], $mikrotikLoginData['mik_username'], $mikrotikLoginData['mik_password']);

    //     if ($mikrotik->checkConnection()) {

    //         $mikrotikConnect = true;
    //     }
    // }
    
    if(isset($_GET['pre'])){

        if(date('m')==1 ){
            $year=date('Y')-1;
            $premonth=12;
        }else{
            $year = date('Y');
            $premonth=date('m')-1;
        }
       $month= date('M',strtotime(" $year-$premonth-01"));

    }else{
        $month= date('M');
    }

//======= Object Created from Class ======
//========================================
    date_default_timezone_set('Asia/Dhaka');
    $date_time = date('Y-m-d g:i:sA');
    $ip_add = $_SERVER['REMOTE_ADDR'];
if (isset($_GET['hold'])) {
    $hold = $_GET['hold'];
    //$agentDetails = $obj->details_by_cond("tbl_agent", "ag_id='$hold'");
    $holdMoney = $obj->Update_data("tbl_agent", ['hold_money_status' => 1,'bill_status' => 1], "where ag_id='$hold'");
} 
    if (isset($_GET['token']) && isset($_GET['amount'])) {
        $token = $_GET['token'];
        $bill_amount = $_GET['amount'];

        $agentDetails = $obj->details_by_cond("tbl_agent", "ag_id='$token'");
        $mobile = isset($agentDetails['ag_mobile_no']) ? $agentDetails['ag_mobile_no'] : NULL;
        $cus = isset($agentDetails['ip']) ? $agentDetails['ip'] : NULL;
        $form_data = array(
            'agent_id' => $token,
            'acc_amount' => $_GET['amount'],
            'acc_type' => '3',
            'acc_description' => "$month  Months Bill collection of " . $agentDetails['ag_name'] . ' IP: ' . $agentDetails['ip'] . '',
            'entry_by' => $userid,
            'entry_date' => $date_time,
            'update_by' => $userid
        );
        $accounts_add = $obj->Reg_user_cond("tbl_account", $form_data, " ");
      
        // due status change for check due 
    //   $cus_due = $bill->get_customer_dues($token);
        $cus_due = $obj->column_by_cond("total_bill","vw_bill_calc", "ag_id='$token'");
        
        if ($accounts_add) {
            
            $paidAgent = $obj->Update_data("tbl_agent", ['pay_status' => 0,'bill_status' => 1], "ag_id='$token'");

          //due status change for check due 
         
           if($cus_due > 0){
               $obj->Update_data("tbl_agent", ['due_status' => 0,'pay_status' => 1], "ag_id='$token'");
           }else{
               $obj->Update_data("tbl_agent", ['due_status' => 1,'pay_status' => 0], "ag_id='$token'");
           }
       
            $cus_name = $agentDetails['ag_name'];
            $smsbody = "Dear $cus_name,Your Internet Bill-$bill_amount taka has been paid successfully.Your due amount is $cus_due. Thank you. ".$obj->getSettingValue('sms', 'company_name'). ".Support-".$obj->getSettingValue('sms', 'support_num').".";
             $mobilenum="88".$mobile;
             $obj->sendsms($smsbody, $mobilenum);

            // if ($bill->get_customer_dues($token) <= 0) {

            //     if (isset($mikrotik)) {

            //         if ($mikrotikConnect) {

            //             if (!$mikrotik->checkSecretActive($agentDetails['ip'])) {

            //                 $mikrotik->enableSingleSecret($agentDetails['ip']);
            //             }
            //         }
            //     }
            // }

        } else {

            echo false;
        }
    }

    // db user exit check 
    if (isset($_POST['check'])) {

        $checkIp = $_POST['check'];
        $findData = $obj->Total_Count('tbl_agent', "`ip` = '$checkIp'");
        if ($findData == 0) {
            echo '<p class="bg-success"><span  class="glyphicon glyphicon-check text-success"></span> OK, You can proceed.<small>(software)</small> </p>';
        } else {
            echo '<p class="bg-danger"><span  class="glyphicon glyphicon-remove text-danger"></span> Sorry this Customer IP/Secret name is already exists. </p>';
        }
    }
    
    
    // mk user exit check 
     if (isset($_POST['checkmsecret'])) {
         $checkmkid = $_POST['checkmkid'];
         $checkmsecret = $_POST['checkmsecret'];
         $data = '<p class="bg-danger"><span  class="glyphicon glyphicon-remove text-danger"></span> Sorry this Customer IP/Secret name is already exists. </p>';
         if ($obj->tableExists('mikrotik_user')) {
            $mikrotikLoginData = $obj->details_by_cond('mikrotik_user',"id = $checkmkid");
            $mikrotik = new Mikrotik($mikrotikLoginData['mik_ip'], $mikrotikLoginData['mik_username'], $mikrotikLoginData['mik_password']);
             if ($mikrotik->checkConnection()) {
                if(empty($mikrotik->showSingleSecret(trim($checkmsecret)))){
                  $data = '<p class="bg-success"><span  class="glyphicon glyphicon-check text-success"></span> OK, You can proceed.<small>('.$mikrotikLoginData['mik_ip'].')</small> </p>';
                }
             }
            echo $data;
        }
    }
    
      
    // mk user profile 
     if (isset($_POST['profileGetmkid'])) {
         $profileGetmkid = $_POST['profileGetmkid'];
         $data = '<select class="form-control" required name="uprofile"> <option value="">Select Package</option>';
         if ($obj->tableExists('mikrotik_user')) {
            $mikrotikLoginData = $obj->details_by_cond('mikrotik_user',"id = $profileGetmkid");
            $mikrotik = new Mikrotik($mikrotikLoginData['mik_ip'], $mikrotikLoginData['mik_username'], $mikrotikLoginData['mik_password']);
             if ($mikrotik->checkConnection()) {
               foreach ($mikrotik->profileStatus() as $singlePackage) { 
                         $data .='<option value="'.$singlePackage['name'].'">'.$singlePackage['name'].'</option>';                       
                 }
             }
           $data .= '</select>';  
            echo $data;
        }
    }

} else {
    header("location: include/login.php");
}