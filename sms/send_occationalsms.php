<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$notification = "";
//taking month and years
$day = date('M-Y');

// get every customer dues query by id
//This method calculate all the dues pass by customer numeric id
//return a dues

if( isset($_GET['zone']) && !empty( $_GET['zone']) ){
    $zone = $_GET['zone'];

    if( $zone == 'x'){

        $smsClient = $obj->view_all_by_cond("tbl_agent", "ag_status='1'");

    }else{


        $smsClient = $obj->view_all_by_cond("tbl_agent", "ag_status='1' AND zone = $zone");
    }

}else{


    die();
}

$smsArray =[];
     $smsI=0;
$i = '0';
$total_due_amount = 0;
        foreach ($smsClient as $value) {
        
            $mobile = isset($value['ag_mobile_no']) ? $value['ag_mobile_no'] : NULL;
        
            $value1 = $obj->details_by_cond("sms", "status='2'");
            $smstext = isset($value1['smsbody']) ? $value1['smsbody'] : NULL;
        
            $mobilenum="88".$mobile;
            // $obj->sendsms($smstext, $mobilenum);
        
          $smsArray[$smsI++] = ["to" => $mobilenum, "message" => $smstext];
      }
    
       $obj->sms_send($smsArray);
?>
<script>
    window.location = "?q=occation";
</script>