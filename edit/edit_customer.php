<?php

date_default_timezone_set('Asia/Dhaka');
$date_time =date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid =isset($_SESSION['UserId']) ? $_SESSION['UserId']:NULL;
$token = isset($_GET['token'])? $_GET['token']:NULL;


$details = $obj->details_by_cond("tbl_customer_info","id='$token'");


//===================Add Function===================

   if(isset ($_POST['update'])){
       extract($_POST);
            
       $form_data = [
          'co_no' => '',
          'branch' => $branch_id,
          'pax_f_name' => $first_name,
          'pax_l_name' => $last_name,
          'refferal_type' => $referral_type,
          'agent_name' => $agent_id,
          'refarral_name' => $referral,
          'refarral_co_no' => $referral_contact_no,
          'pax_contact_no' => $contact_no,
          'sms_no' => $sms_no,
          'email' => $email,
                                                
          'served_by' => $userid,
          'work_status' => '0',
          'update_status' => '0',
          'delivery_status' => '0',
          'done_by' => $done_by,           
          'remarks' => str_replace("'", "", $remark), 
           
          'entry_by' => $userid,       
          'entry_date' => $date_time,
          'update_by' => $userid
          ];
       
       $service_add=$obj->Update_data("tbl_customer_info",$form_data,"where id='$token'");
       
       if($service_add){                      
           ?>
            <script>
                window.location="?q=view_customer";
            </script>   
<?php                    
       }
       else{
           echo $notification = 'Update Failed';
       }
   }
?>

<script type="text/javascript" src="asset/js/form2.js"></script>
<script type="text/javascript" src="asset/js/form.js"></script>
<link href="asset/css/extra.css" rel="stylesheet" type="text/css">
  

<form id="frm_area" enctype="multipart/form-data" method="post" action="" name="frm_area">
    <input id="rowID" type="hidden" value="" name="rowID">
    <div id="info_msg" class="" name="info_msg"></div>
    <div id="loader_div" class="form_loader" style="display: none;"></div>
    <div id="list_rec" class="rec_view" style="display: none;"></div>
<div id="new_rec" class="rec_view" style="display: block; margin-top: 30px;">
    <div class="row-fluid">
        <div class="span12">
            <div class="widget span">
                <div class="widget-header">
                    <div class="form-horizontal no-margin">
                        <div class="widget-body">
                            <div class="control-group">
                                <label class="control-label" for="branch_id"> Branch </label>
                                <div class="controls">
                                    <select id="branch_id" class="span9" validate="Require" placeholder="Service" name="branch_id">
                                        <option value="">select</option>
                                        <?php
                                            $i='0';
                                            foreach ($obj->view_all("tbl_branch") as $value){
                                                $i++;                                                              
                                        ?>
                                        <option <?php  if($details['branch']==$value['br_id']) echo 'selected'  ?> value="<?php echo isset($value['br_id'])?$value['br_id']:NULL;?>"><?php echo isset($value['br_name'])?$value['br_name']:NULL;?></option>
                                         <?php
                                            }
                                            ?> 
                                     
                                    </select>
                                    <span class="help-inline"> * </span>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="first_name"> Pax First Name </label>
                                <div class="controls">
                                    <input value="<?php echo $details['pax_f_name']? $details['pax_f_name']:NULL; ?>" id="first_name" class="span9" type="text" validate="Require" placeholder="Pax First Name" name="first_name">
                                    <span class="help-inline"> * </span>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="last_name"> Pax Last Name </label>
                                <div class="controls">
                                    <input value="<?php echo $details['pax_l_name']? $details['pax_l_name']:NULL; ?>" id="last_name" class="span9" type="text" placeholder="Pax Last Name" name="last_name">
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="agent_id"> Referrals Type </label>
                                <div class="controls">
                                    <select id="referral_type" class="span9" validate="Require" onchange="referrals_type_fnc()" placeholder="Agent" name="referral_type">
                                        <option  value="Select">Select</option>
                                        <option <?php if($details['refferal_type']=='Agent') echo 'selected';  ?> value="Agent">Agent</option>
                                        <option <?php if($details['refferal_type']=='Referrals') echo 'selected';  ?> value="Referrals">Referrals</option>
                                        <option <?php if($details['refferal_type']=='Office') echo 'selected';  ?> value="Office">Office</option>
                                    </select>
                                    <span class="help-inline"> * </span>
                                </div>
                            </div>
                          
                           
                            
                            <div class="control-group div_agent" >
                                <label class="control-label" for="agent_id"> Agent </label>
                                <div class="controls">
                                    <select id="agent_id" class="span9" onchange="sms_number_fnc()" placeholder="Agent" name="agent_id">
                                        <option value="">Select</option>                       
                                        <?php
                                            $i='0';
                                            foreach ($obj->view_all("tbl_agent") as $value){
                                                $i++;                                                              
                                        ?>
                                        <option <?php  if($details['agent_name']==$value['ag_id']) echo 'selected'  ?>  value="<?php echo isset($value['ag_id'])?$value['ag_id']:NULL;?>"><?php echo isset($value['ag_name'])?$value['ag_name']:NULL;?></option>
                                         <?php
                                            }
                                            ?> 
                                    </select>
                                </div>
                            </div>
                            
                            <div class="control-group div_referrals" >
                                <label class="control-label" for="referral"> Referrals </label>
                                <div class="controls">
                                    <input value="<?php echo $details['refarral_name']? $details['refarral_name']:NULL; ?>" id="referral" class="span9" type="text" placeholder="Referrals" name="referral">
                                </div>
                            </div>
                            <div class="control-group div_referrals" >
                                <label class="control-label" for="referral_contact_no"> Referrals Contact No </label>
                                <div class="controls">
                                    <input value="<?php echo $details['refarral_co_no']? $details['refarral_co_no']:NULL; ?>" id="referral_contact_no" class="span9" type="text" onkeypress="return numbersOnly(event)" placeholder="Referrals Contact No" name="referral_contact_no">
                                </div>
                            </div>
                             <div class="control-group div_pax_contact" >
                                <label class="control-label" for="contact_no"> Pax Contact No </label>
                                <div class="controls">
                                    <input value="<?php echo $details['pax_contact_no']? $details['pax_contact_no']:NULL; ?>" id="contact_no" class="span9" type="text" onblur="sms_number_fnc()" onkeypress="return numbersOnly(event)" placeholder="Pax Contact No" name="contact_no">
                                </div>
                            </div>
                            
                            <div class="control-group">
                                <label class="control-label" for="contact_no"> SMS Number </label>
                                <div class="controls">
                                    <input value="<?php echo $details['sms_no']? $details['sms_no']:NULL; ?>" id="sms_no" class="span9" type="text" onkeypress="return numbersOnly(event)" placeholder="SMS Contact No" name="sms_no" >
                                </div>
                            </div>                             
                             <div class="control-group div_email_address">
                                <label class="control-label" for="email_address"> E-mail Address </label>
                                <div class="controls">
                                    <input value="<?php echo $details['email']? $details['email']:NULL; ?>" id="email_address" class="span9" type="text" placeholder="E-mail Address" name="email">
                                </div>
                            </div>
                            
                            <div class="control-group " >
                                <label class="control-label" for="work"> Work Status </label>
                                <div class="controls">
                                    <select class="span9" placeholder="Work Status" name="work_status">
                                        <option <?php  if($details['work_status']=='0') echo 'selected'  ?> value="0">Pending</option>
                                        <option <?php  if($details['work_status']=='1') echo 'selected'  ?> value="1">Done</option>                                    
                                    </select>
                                </div>
                            </div>                           
                            <div class="control-group">
                                <label class="control-label" for="done_by"> Done By </label>
                                <div class="controls">
                                    <input value="<?php echo $details['done_by']? $details['done_by']:NULL; ?>" id="done_by" class="span9" type="text" placeholder="Done By" name="done_by">
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="remark"> Remark's </label>
                                <div class="controls">
                                    <textarea id="remark" class="span9" placeholder="Remark's" name="remark"><?php echo $details['remarks']? $details['remarks']:NULL; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="edit_rec" class="rec_view" style="display: none;"></div>
<div id="details_rec" class="rec_view" style="display: none;"></div>

<div class="row" style="padding: 5px 0px 15px 25px; font-size: 12px; text-align: center;">
  <button type="submit" class="btn btn-success" name="update">Update</button> 
</div>
</form>
