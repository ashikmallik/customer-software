<?php
session_start();
session_destroy();
header("location: login.php");



// //========================================
// include '../controller/oop.php';
// $obj = new Controller();
// //========================================

// date_default_timezone_set('Asia/Dhaka');
// $current_date_time   = date("Y-m-d h:m:s");

// $last_log_id = isset($_SESSION['last_log_id']) ?   $_SESSION['last_log_id']  :NULL;
  
// $logup_sql = "UPDATE `_tbl_login_log` SET `logout_status` = 'Success',
//                                           `logout_date_time` =? 
//                                     WHERE `login_log_id` =?";    
// $logup_q = $con->prepare($logup_sql);
// $logup_q->execute(array($current_date_time,$last_log_id));

?>