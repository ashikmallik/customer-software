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
        $sql = "SELECT * FROM vw_marketing_agent WHERE zone='$zone' ORDER BY `vw_marketing_agent`.`ag_id` ASC";

    }

    else{
        $sql = "SELECT * FROM vw_marketing_agent ORDER BY `vw_marketing_agent`.`ag_id` ASC";
    }


    $q = $obj->con->prepare($sql);
    $q->execute();
    $i = 1;
    while ($row = $q->fetch(PDO::FETCH_ASSOC)) {

        $row['sl'] = $i++;
        $allAgentData[] = $row;

    }

    /* =================================================
     *  Below code is written for show total bill
     * above the table as calculated with this.
     * Datatable not support the additional data without table
     ===================================================*/
    if(isset($_GET['view_agent_total_bill']) && !empty($_GET['view_agent_total_bill'])){

        echo json_encode((array('total_bill' => $total_bill)));
    }else{

        echo json_encode(array('data' => $allAgentData,'total_bill' => $total_bill));
    }


} else {
    header("location: include/login.php");
}
?>