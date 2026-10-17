<?php
session_start();

if (!empty($_SESSION['UserId'])) {
//========================================
include '../../model/oop.php';
include '../../model/Bill.php';
//include '../../model/SSP.php';

$obj = new Controller();
$bill = new Bill();
//========================================

$user_id = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$FullName = isset($_SESSION['FullName']) ? $_SESSION['FullName'] : NULL;
$UserName = isset($_SESSION['UserName']) ? $_SESSION['UserName'] : NULL;
$PhotoPath = isset($_SESSION['PhotoPath']) ? $_SESSION['PhotoPath'] : NULL;
$ty = isset($_SESSION['UserType']) ? $_SESSION['UserType'] : NULL;



/* =============================================
 * =============================================
 *  Here we use raw query only enhance performance
 *  When the agent number is more than 1500+
 *  It take significant time to show them.*
 * =============================================
 * =============================================*/

    date_default_timezone_set('Asia/Dhaka');
    $date_time = date('Y-m-d g:i:sA');
    $ip_add = $_SERVER['REMOTE_ADDR'];
    $userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

    $allAgentData = array();
    $total_bill = 0;
    $totalPaid=0;
    $totalPaidCount=0;

    if (isset($_GET['zone'])) {

        $zone = $_GET['zone'];
       $sql = "SELECT * FROM vw_agent WHERE sub_zone_id='$zone' ORDER BY `vw_agent`.`ag_id` DESC";
       

    }
    elseif (isset($_GET['bid'])) {

        $bid = $_GET['bid'];
        $sql = "SELECT * FROM vw_agent WHERE company_id='$bid' ORDER BY `vw_agent`.`ag_id` DESC";

    }
    elseif (isset($_GET['company'])) {

        $company = $_GET['company'];
        $sql = "SELECT * FROM vw_agent WHERE company_id='$company' ORDER BY `vw_agent`.`ag_id` DESC";

    }



    elseif (isset($_GET['status'])) {

        $status = $_GET['status'];
        $sql = "SELECT * FROM vw_agent WHERE ag_status='$status' ORDER BY `tbl_agent`.`ag_id` DESC";

    }


    elseif (  isset($_GET['datefrom']) && isset($_GET['dateto'])  && isset($_GET['stat']) ) {

        $dateFrom = $_GET['datefrom'];
        $dateto = $_GET['dateto'];
        $stat = $_GET['stat'];

        if($stat === '1' ) {
            $sql = "SELECT * FROM vw_agent WHERE entry_date BETWEEN '" . $dateFrom . "' and '" . $dateto . "' AND  ag_status='".$stat."'  ORDER BY `vw_agent`.`ag_id` DESC";
        }elseif ($stat === '0'){
            $sql = "SELECT * FROM vw_agent WHERE inactive_date BETWEEN '" . $dateFrom . "' and '" . $dateto . "' AND  ag_status='".$stat."'  ORDER BY `vw_agent`.`ag_id` DESC";
        } else{
            $sql = "SELECT * FROM vw_agent WHERE entry_date BETWEEN '" . $dateFrom . "' and '" . $dateto . "'  ORDER BY `vw_agent`.`ag_id` DESC";
        }

    }



    else{
        $sql = "SELECT * FROM vw_agent ORDER BY `vw_agent`.`ag_id` DESC";
        
    }

    $totalconnectionFee=0;
    $q = $obj->con->prepare($sql);
    $q->execute();
    $i = 1;
    while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
        
        if(isset($_GET['stat'])){
           $accData =$obj->raw_sql("SELECT SUM(acc_amount) as paid, COUNT(acc_id) as countid FROM `tbl_account` WHERE agent_id = '".$row['ag_id']."'");
           $totalPaid += $accpaid = isset($accData[0])?$accData[0]['paid'] : 0;
           $totalPaidCount += $accpaidC = isset($accData[0])?$accData[0]['countid'] : 0;
           $row['paid'] = "$accpaid ($accpaidC)";
        }
        $totalconnectionFee += $row['connect_charge'];
        
        
        $bp =$obj->details_by_cond('_createuser', 'UserId = ' . @$row['billing_person_id'] . '');  
        $row['bp'] = @$bp['FullName'];
        $row['sl'] = $i++;
        $allAgentData[] = $row;

        if($row['ag_status'] == 1){ // for calculate active client total bill amount
            $total_bill += $row['taka'];
        }
        if($row['ag_status'] == 0){ // for calculate active client total bill amount
            $total_bill += $row['taka'];
        }
    }

    /* =================================================
     *  Below code is written for show total bill
     * above the table as calculated with this.
     * Datatable not support the additional data without table
     ===================================================*/
    if(isset($_GET['view_agent_total_bill']) && !empty($_GET['view_agent_total_bill'])){

        echo json_encode(['total_bill' => $total_bill],JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }else{

        echo json_encode(['data' => $allAgentData,'total_bill' => $total_bill, 'total_paid'=> $totalPaid, 'total_paid_count'=> $totalPaidCount, 'total_connectionFee'=> $totalconnectionFee],JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }


} else {
    header("location: include/login.php");
}
?>