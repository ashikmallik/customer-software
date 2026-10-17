<?php
session_start();

//========================================
include '../../model/oop.php';


$obj = new Controller();

$id = $_POST['id'];
$status = $_POST['status'];

// toggle
$new_status = ($status == 1) ? 0 : 1;

$sql = "UPDATE work_tracking SET work_status = :status WHERE id = :id";

$stmt = $obj->con->prepare($sql);
$stmt->bindParam(':status', $new_status);
$stmt->bindParam(':id', $id);
$stmt->execute();

echo 1;

?>