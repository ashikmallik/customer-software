<?php
session_start();
$date_time = date('Y-m-d g:i:sA');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
if (!empty($_SESSION['UserId'])) {

include '../model/oop.php';
$obj = new Controller();

// CustomerID Generate 
    $data = $obj->details_by_cond("tbl_agent", "ag_status='1' ORDER BY ag_id DESC");
    if(!$data){$data['ag_id'] = 0; }
    if (($data['ag_id'] + 1) < 10) {
        $STD = "CUS000";
    } else if (($data['ag_id'] + 1) < 100) {
        $STD = "CUS000";
    } else if (($data['ag_id'] + 1) < 1000) {
        $STD = "CUS00";
    } else if (($data['ag_id'] + 1) < 10000) {
        $STD = "CUS0";
    } else {
        $STD = "CUS";
    }
    $STD .= $data['ag_id'] + 1;



    if (isset ($_POST['submit'])) {
        extract($_POST);
        
        // $shareLink = implode(',',$link_share);
        
        
        $form_data_tbl_agent = array(
            'comment' => $remarks,
            'link_name' => str_replace("'", '', $linkName),
            'map_link' => str_replace("'", '', $map_link),
            'link_address' => str_replace("'", '', $link_address),
            'actual_link' => str_replace("'", '', $actual_link),
            'company_id' => str_replace("'", '', $company_name),
            'ag_mobile_no' => str_replace("'", '', $mobile),
            
            'district' => str_replace("'", '', $district),
            'customar_latitude' => str_replace("'", "", $cus_lat),
            // 'customar_longitude' => str_replace("'", "", $customar_longitude),
            'bts_lat_long' => str_replace("'", "", $bts_lat_long),
            'total_distant' => str_replace("'", "", $total_distance),
            'share_distance' => str_replace("'", "", $share_distance),
            'actual_distance' => str_replace("'", "", $actual_distance),
            'share_link' => str_replace("'", "", $link_share),
            'taka' => str_replace("'", '', $taka),
            'cus_id' => $STD,
            'bill_cat' => 1,
            'ag_status' => str_replace("'", '', $status),
            'connect_charge' => str_replace("'", '', $connect_charge),            
            'zone' => $zone,
            'sub_zone_name' => $sub_zone,
            'connection_date' => date('Y-m-d', strtotime($con_date)),
            'entry_date' => date('Y-m-d', strtotime('first day of this month')),
            'entry_by' => $userid,
            'update_by' => $userid,
            'billing_person_id' => $billingperson,
            'bill_date' => $bill_date,
    
        );

        $agent_add = $obj->Insert_data("tbl_agent", $form_data_tbl_agent);

        if (isset($agent_add) && $agent_add) {

            if (isset($opening_amount) && !empty($opening_amount)) {

                $form_data_tbl_account = array(
                    'cus_id' => $agent_add,
                    'agent_id' => $agent_add,
                    'acc_type' => '5',
                    'acc_amount' => $opening_amount,
                    'acc_description' => 'Opening and Running Month amount for connection of ' . $agentName,
                    'entry_by' => $userid,
                    'entry_date' => $date_time,
                    'update_by' => $userid
                );

                $obj->Insert_data("tbl_account", $form_data_tbl_account);
            }
          
           
            
            if (isset($connect_charge) && !empty($connect_charge)) {

                $form_data_tbl_account2 = array(
                    'cus_id' => $agent_add,
                    'agent_id' => $agent_add,
                    'company_id' => $company_name,
                    'acc_amount' => $connect_charge,
                    'acc_type' => '4', //connection charge sent tbl_account
                   
                   
                    'entry_by' => $userid,
                    'entry_date' => $date_time,
                    'update_by' => $userid
                );

                $obj->Insert_data("tbl_account", $form_data_tbl_account2);
            }
            $year = Date('Y');
            $month = Date('M');

            $corrent_month = $obj->details_by_cond("tbl_bill", "year='$year' and company_id='$company_name'");

           if($corrent_month){
            $runn_amount =$corrent_month[$month];
           }else{
            $runn_amount =0;
            
           }
        if(!empty($corrent_month)){
            $obj->Update_data("tbl_bill",[$month =>($taka+$runn_amount)], "company_id='$company_name'");
        }else{
            
            $obj->Insert_data("tbl_bill",['company_id'=>$company_name,'year'=>$year,$month=>($taka+$runn_amount)]);
        }


            $obj->notificationStore('Customer Created Successfully', 'success');

            if (!empty($connect_charge) || !empty($opening_amount) || !empty($running_month_due_amount) || !empty($connection_charge_due_amount)) {
                if (isset($print_invoice)) {

                    echo "<script>window.open('../pdf/new_cus_invoice.php?agent=$agent_add','_blank');</script>";

                }
                echo '<script>window.location = "../index.php?q=view_agent";</script>';
            } else {

                echo '<script>window.location = "../index.php?q=view_agent";</script>';

            }


        } else {

            $obj->notificationStore('Insert Failed');

          //  echo '<script>window.location = "../index.php?q=view_agent";</script>';
        }

    }
}