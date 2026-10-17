      
<?php

date_default_timezone_set('Asia/Dhaka');
$date_time =date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid =isset($_SESSION['UserId']) ? $_SESSION['UserId']:NULL;


//Start Random code Generator


$data= $obj->details_by_cond("tbl_customer_info","status='0' ORDER BY id DESC");

 
   if(($data['id']+1)<10){
       $STD = "CUS000";
   }
   else if(($data['id']+1)<100){
       $STD = "CUS000";
   }
   else if(($data['id']+1)<1000){
       $STD = "CUS00";
   }
   else if(($data['id']+1)<10000){
       $STD = "CUS0";
   }
   else{
       $STD = "CUS";
   }
   $STD .= $data['id']+1;
   
//End Random code Generator   

//===================Add Function===================

   if(isset ($_POST['submit'])){
       extract($_POST);
           
       $form_data = array(
           
          'cus_id' => $STD,
       
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
           );
       $service_add=$obj->Reg_user_cond("tbl_customer_info", $form_data, " ");
       
       if($service_add){                      
           ?>
            <script>
              window.location="?q=view_customer";
            </script>   
<?php                    
       }
       else{
           echo $notification = 'Insert Failed';
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
   <span style="color:#F00; font-weight:bold"><?php  echo $STD?></span> 
  
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
                                        <option  value="<?php echo isset($value['br_id'])?$value['br_id']:NULL;?>"><?php echo isset($value['br_name'])?$value['br_name']:NULL;?></option>
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
                                    <input id="first_name" class="span9" type="text" validate="Require" placeholder="Pax First Name" name="first_name" />
                                    <span class="help-inline"> * </span>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="last_name"> Pax Last Name </label>
                                <div class="controls">
                                    <input id="last_name" class="span9" type="text" placeholder="Pax Last Name" name="last_name">
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="agent_id"> Referrals Type </label>
                                <div class="controls">
                                    <select id="referral_type" class="span9" validate="Require" onchange="referrals_type_fnc()" placeholder="Agent" name="referral_type">
                                        <option value="..">Select</option>
                                        <option value="Agent">Agent</option>
                                        <option value="Referrals">Referrals</option>
                                        <option value="Office">Office</option>
                                    </select>
                                    <span class="help-inline"> * </span>
                                </div>
                            </div>
                            <div class="control-group div_agent" style="display: none;">
                                <label class="control-label" for="agent_id"> Agent </label>
                                <div class="controls">
                                    <select id="agent_id" class="span9" onchange="sms_number_fnc()" placeholder="Agent" name="agent_id">
                                        <option value="">Select</option>                       
                                        <?php
                                            $i='0';
                                            foreach ($obj->view_all("tbl_agent") as $value){
                                                $i++;                                                              
                                        ?>
                                        <option  value="<?php echo isset($value['ag_id'])?$value['ag_id']:NULL;?>"><?php echo isset($value['ag_name'])?$value['ag_name']:NULL;?></option>
                                         <?php
                                            }
                                            ?> 
                                    </select>
                                </div>
                            </div>
                            <div class="control-group div_referrals" style="display: none;">
                                <label class="control-label" for="referral"> Referrals </label>
                                <div class="controls">
                                    <input id="referral" class="span9" type="text" placeholder="Referrals" name="referral">
                                </div>
                            </div>
                            <div class="control-group div_referrals" style="display: none;">
                                <label class="control-label" for="referral_contact_no"> Referrals Contact No </label>
                                <div class="controls">
                                    <input id="referral_contact_no" class="span9" type="text" onkeypress="return numbersOnly(event)" placeholder="Referrals Contact No" name="referral_contact_no">
                                </div>
                            </div>
                            <div class="control-group div_pax_contact" style="display: none;">
                                <label class="control-label" for="contact_no"> Pax Contact No </label>
                                <div class="controls">
                                    <input id="contact_no" class="span9" type="text" onblur="sms_number_fnc()" onkeypress="return numbersOnly(event)" placeholder="Pax Contact No" name="contact_no">
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="contact_no"> SMS Number </label>
                                <div class="controls">
                                    <input id="sms_no" class="span9" type="text" onkeypress="return numbersOnly(event)" placeholder="SMS Contact No" name="sms_no" >
                                </div>
                            </div>                                                     
                            <div class="control-group div_email_address">
                                <label class="control-label" for="email_address"> E-mail Address </label>
                                <div class="controls">
                                    <input id="email_address" class="span9" type="text" placeholder="E-mail Address" name="email">
                                </div>
                            </div>
                          
                            <div class="control-group">
                                <label class="control-label" for="done_by"> Done By </label>
                                <div class="controls">
                                    <input id="done_by" class="span9" type="text" placeholder="Done By" name="done_by">
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="remark"> Remark's </label>
                                <div class="controls">
                                    <textarea id="remark" class="span9" placeholder="Remark's" name="remark"></textarea>
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
  <button type="submit" class="btn btn-success" name="submit">Submit</button> 
</div>
</form>

