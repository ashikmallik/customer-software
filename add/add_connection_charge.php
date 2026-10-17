<?php
session_start();
$date_time = date('Y-m-d g:i:sA');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

include '../model/oop.php';
$obj = new Controller();

$token = isset($_GET['token']) ? $_GET['token'] : NULL;
$agent = $obj->details_by_cond("tbl_agent", "ag_id='$token'");

//===================Add Function===================

   if(isset ($_POST['submit'])){
       extract($_POST);
       
       
        $obj->Update_data("tbl_agent",['connect_charge' => $fee], " ag_id='$token'");

        $form_data_tbl_account = array(
            'cus_id' => $agent['cus_id'],
            'agent_id' => $agent['ag_id'],
            'acc_type' => '5',
            'acc_amount' => $fee,
            'acc_description' => 'Opening and Running Month amount for connection Fee ' ,
            'entry_by' => $userid,
            'entry_date' => $date_time,
            'update_by' => $userid
        );
        $facam =$obj->details_by_cond("tbl_account", "agent_id='$token' and acc_type=5");
         if(isset($facam['acc_id'])){
             $tblacc= $obj->Update_data("tbl_account", $form_data_tbl_account, " acc_id='".$facam['acc_id']."'");
         }else{
             $tblacc= $obj->Insert_data("tbl_account", $form_data_tbl_account);
       }
     
       if($tblacc){                      
           echo '<script>  window.location="?q=view_conn_charge"; </script> ';                    
       } else{
           echo $notification = 'Insert Failed';
       }
       
   }
?>

<!--===================end Function===================-->


<div class="col-md-12" style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
    <b><?php echo isset($notification)? $notification :NULL; ?></b>
</div>
<div class="row" style="padding:10px; font-size: 12px;">
          <form role="form" enctype="multipart/form-data" method="post">    
                <div class="row" style="padding:10px; font-size: 12px;">

                    <div class="col-md-6">
                       <input hidden value="<?php echo $token ?>" name="token" />
                       <div class="form-group">
                            <label> Name</label>
                            <input type="text" readonly value="<?php echo @$agent['link_name'] ?>"class="form-control"  >
                       </div>
                        <div class="form-group">
                            <label>Connection Charge </label>
                            <input type="text" name="fee" value="<?php echo @$agent['connect_charge'] ?>" class="form-control" i>
                         </div>
                        
     
                       
                    </div>
                    <div class="col-md-6"></div>
                </div>

                <div class="row" style="padding: 5px 0px 15px 25px; font-size: 12px;">
                  <button type="submit" class="btn btn-success" name="submit">Update</button> 
                </div>
        </form>
        </div>
