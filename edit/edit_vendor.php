<?php
date_default_timezone_set('Asia/Dhaka');
$date_time =date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add      = $_SERVER['REMOTE_ADDR'];
$userid =isset($_SESSION['UserId']) ? $_SESSION['UserId']:NULL;
$token = isset($_GET['token'])? $_GET['token']:NULL;

$details = $obj->details_by_cond("tbl_vendor","ven_id='$token'");

extract($details);

    if(isset($_POST['update'])){
        extract($_POST);

   $form_data=array(
          'ven_name' => $Name,
          'ven_contactNo' => $Contact,
          'ven_email' => $Email,
          'ven_status' => $status,
       
          'entry_by' => $userid,       
          'entry_date' => $date_time,
          'update_by' => $userid                         
   );
    $branch_id=$obj->Update_data("tbl_vendor",$form_data,"where ven_id='$token'");
   
    if($branch_id){
        
        ?>
<script>
   window.location="?q=view_vendor";
 </script>
<?php                
    }
    else{
        echo $notification = 'Update Failed';
    }             
    }
?>

<div class="col-md-12" style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
    <b><?php echo isset($notification)? $notification :NULL; ?></b>
</div>
<div class="row" style="padding:10px; font-size: 12px;">
    <form role="form" enctype="multipart/form-data" method="post">    
            <div class="row" style="padding:10px; font-size: 12px;">
                <div class="col-md-6">                       
                   <div class="form-group">
                        <label>Vendor Name</label>
                        <input value="<?php echo $details['ven_name']? $details['ven_name']:NULL; ?>" type="text" name="Name" class="form-control" id="ResponsiveTitle"  >
                   </div>
                   <div class="form-group">
                        <label>Vendor Contact</label>
                        <input value="<?php echo $details['ven_contactNo']? $details['ven_contactNo']:NULL; ?>" type="text" name="Contact" class="form-control" id="ResponsiveTitle"  >
                   </div>
                   <div class="form-group">
                        <label>Vendor Email</label>
                        <input value="<?php echo $details['ven_email']? $details['ven_email']:NULL; ?>" type="text" name="Email" class="form-control" id="ResponsiveTitle"  >
                   </div>                    
                    <div class="form-group">                                                         
                        <label>Status</label>
                        <select class="form-control" required="required" name="status" id="status">
                           <option <?php if($details['ven_status']=='1') echo 'selected';  ?>  value="1">Active</option>
                           <option <?php if($details['ven_status']=='0') echo 'selected'  ?> value="0">Inactive</option>
                        </select>                       
                    </div>                       
                </div>
                <div class="col-md-6"></div>
            </div>
            <div class="row" style="padding: 5px 0px 15px 25px; font-size: 12px;">
              <button type="submit" class="btn btn-success" name="update">Submit</button> 
            </div>
    </form>
</div>
<hr></hr>