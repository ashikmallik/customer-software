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


$item_id      = $_POST['item_id'];
$warehouse_id = $_POST['warehouse_id'];

$in  = $obj->get_total_stock_in($item_id, $warehouse_id);
$out = $obj->get_total_stock_out($item_id, $warehouse_id);
$available = $in - $out;

echo $available;

}
?>