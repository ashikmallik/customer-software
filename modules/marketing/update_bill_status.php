<?php
session_start();

//========================================
include '../../model/oop.php';


$obj = new Controller();

if (isset($_POST['id']) && isset($_POST['status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    
    $form_data = array('bill_status' => $status);
    $obj->Update_data("bills", $form_data, "`bills`.`id`='$id'");
    echo "Success";
}

echo 1;

?>
