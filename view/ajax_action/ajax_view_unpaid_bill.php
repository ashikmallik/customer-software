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

    if (isset($_GET['zone'])) {

        $zone = $_GET['zone'];
        $sql = "SELECT * FROM vw_agent WHERE bill_status=0 and taka > 0 and ag_status='1' and pay_status='1' AND due_status='0'   AND zone='$zone' ORDER BY `vw_agent`.`ag_id` ";

    }
    elseif (isset($_GET['bid'])) {

        $bid = $_GET['bid'];
        $sql = "SELECT * FROM vw_agent WHERE bill_status=0 and taka > 0 and ag_status='1' and pay_status='1' AND due_status='0'   AND billing_person_id='$bid' ORDER BY `vw_agent`.`ag_id` ";

    }
    elseif (isset($_GET['fieldPrint'])) {

        $status = $_GET['fieldPrint'];
        if($status !='x'){
        $sql = "SELECT * FROM vw_agent WHERE bill_status=0 AND and taka > 0 ag_status='$status' and pay_status='1' AND due_status='0'   ORDER BY `vw_agent`.`ag_id` ";
        }
        else
        {
            $sql = "SELECT * FROM vw_agent WHERE bill_status=0 and taka > 0 and ag_status='1' and pay_status='1' AND due_status='0'   ORDER BY `vw_agent`.`ag_id` ";
        }

    }



    elseif (isset($_GET['status'])) {

        $status = $_GET['status'];
        $sql = "SELECT * FROM vw_agent WHERE  bill_status=0 and taka > 0 AND ag_status='$status' and pay_status='1' AND due_status='0'   ORDER BY `vw_agent`.`ag_id` ";

    }


    elseif (  isset($_GET['datefrom']) && isset($_GET['dateto'])  && isset($_GET['stat']) ) {

        $dateFrom = $_GET['datefrom'];
        $dateto = $_GET['dateto'];
        $stat = $_GET['stat'];

        if($stat === '1' ) {
            $sql = "SELECT * FROM vw_agent WHERE bill_status=0 and taka > 0 and ag_status='1' and pay_status='1' AND due_status='0'   AND entry_date BETWEEN '" . $dateFrom . "' and '" . $dateto . "' AND  ag_status='".$stat."'  ORDER BY `vw_agent`.`ag_id` ";
        }elseif ($stat === '0'){
            $sql = "SELECT * FROM vw_agent WHERE bill_status=0 and taka > 0 and ag_status='1' and pay_status='1' AND due_status='0'   AND inactive_date BETWEEN '" . $dateFrom . "' and '" . $dateto . "' AND  ag_status='".$stat."'  ORDER BY `vw_agent`.`ag_id` ";
        } else{
            $sql = "SELECT * FROM vw_agent WHERE bill_status=0 and taka > 0 and ag_status='1' and pay_status='1' AND due_status='0'   AND entry_date BETWEEN '" . $dateFrom . "' and '" . $dateto . "'  ORDER BY `vw_agent`.`ag_id` ";
        }

    }



    else{
        $sql = "SELECT * FROM vw_agent Where bill_status=0 and taka > 0  and ag_status='1' and pay_status='1' AND due_status='0'   ORDER BY `vw_agent`.`ag_id` ";
    }


    $q = $obj->con->prepare($sql);
    $q->execute();
    $i = 1;
    while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
        
        $bp =$obj->details_by_cond('_createuser', 'UserId = ' . @$row['billing_person_id'] . '');  
        $row['bp'] = @$bp['FullName'];

        $row['sl'] = $i++;
        $row['bill_date'] = $row['bill_date'] . "-" . date("M") . "," . date("y");
        $allAgentData[] = $row;

      

        $token = $row['ag_id'];
        // $previous_due = $obj->details_by_cond("tbl_previous_due", "tbl_agent_id='$token'");
    }

    /* =================================================
     *  Below code is written for show total bill
     * above the table as calculated with this.
     * Datatable not support the additional data without table
     ===================================================*/
    if(isset($_GET['view_agent_total_bill']) && !empty($_GET['view_agent_total_bill'])){

        echo json_encode((array('total_bill' => $total_bill)));
    }else{

        echo json_encode(array('data' => $allAgentData));
    }


} else {
    header("location: include/login.php");
}
?>