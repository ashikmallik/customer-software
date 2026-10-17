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

    include('../../model/Mikrotik.php');
    $obj = new Controller();
    
   
        $dltoken = isset($_GET['agentId']) ? $_GET['agentId'] : NULL;
    
        if (!empty($dltoken)) { // entry a tbl agent copy for keep delete log
    
            // $formDataAgentDataBeforeDelete = $obj->details_by_cond("tbl_agent", "ag_id='$dltoken'");
            // $formDataStoreAgentData = array(
    
            //     'ag_id' => $formDataAgentDataBeforeDelete['ag_id'],
            //     'ip' => $formDataAgentDataBeforeDelete['ip'],
            //     'ag_name' => $formDataAgentDataBeforeDelete['ag_name'],
            //     'ag_mobile_no' => $formDataAgentDataBeforeDelete['ag_mobile_no'],
            //     'ag_office_address' => $formDataAgentDataBeforeDelete['ag_office_address'],
            //     'mb' => $formDataAgentDataBeforeDelete['mb'],
            //     'taka' => $formDataAgentDataBeforeDelete['taka'],
            //     'connect_charge' => $formDataAgentDataBeforeDelete['connect_charge'],
            //     'ag_email' => $formDataAgentDataBeforeDelete['ag_email'],
            //     'bill_cat' => $formDataAgentDataBeforeDelete['bill_cat'],
            //     'zone' => $formDataAgentDataBeforeDelete['zone'],
            //     'entry_by' => $formDataAgentDataBeforeDelete['entry_by'],
            //     'entry_date' => $formDataAgentDataBeforeDelete['entry_date'],
            //     'connection_date' => $formDataAgentDataBeforeDelete['connection_date'],
            //     'update_by' => $formDataAgentDataBeforeDelete['update_by'],
            //     'last_update' => $formDataAgentDataBeforeDelete['last_update'],
            //     'deleter_ip' => get_client_ip(),
            //     'deleter_user_id' => $userid,
            //     'deleting_time' => date('Y/m/d H:i:s'),
    
            // );
    
            // $obj->Reg_user_cond("delete_tbl_agent_log", $formDataStoreAgentData, " ");
    
            if ($obj->Delete_data("tbl_agent", "ag_id='$dltoken'")) {
                $msg = 'Delete Successful';
            } else {
                 $msg ='Delete Failed';
            }
            
        }else{
             $msg = 'Somthing is worng';
        }
        
        echo $msg;
    
}
?>