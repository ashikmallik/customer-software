<?php
session_start();

if (!empty($_SESSION['UserId'])) {

    include '../../model/oop.php';
    include '../../model/Bill.php';
    $obj = new Controller();

    if(isset($_GET['datefrom']) && isset($_GET['dateto']) ){

        $where="entry_date BETWEEN '".$_GET['datefrom']."' and '".$_GET['dateto']."'";
    }else{

        $where = "MONTH(entry_date)='".date('m')."' and YEAR(entry_date)='".date('Y')."'";
    }


    $all = $obj->Total_Count("tbl_agent", $where);
    $active = $obj->Total_Count("tbl_agent", $where . " AND ag_status='1'");
    $inactive = $obj->Total_Count("tbl_agent", $where . " AND ag_status='0'");

    echo json_encode(array('all_client' => $all, 'active_client' => $active, 'inactive_client' => $inactive));

} else {

    header("location: include/login.php");
}
?>