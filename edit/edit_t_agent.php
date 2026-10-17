<link rel="stylesheet" type="text/css" href="build/jquery.datetimepicker.css"/>
      
<?php

date_default_timezone_set('Asia/Dhaka');
$date_time =date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid =isset($_SESSION['UserId']) ? $_SESSION['UserId']:NULL;

$token = isset($_GET['token'])? $_GET['token']:NULL;

$details = $obj->details_by_cond("tbl_ticket_sale","ticket_id='$token'");

extract($details);

//===================Edit Function===================

   if(isset ($_POST['update'])){
       extract($_POST);
     
       
       $form_data = array(
                              
          't_agent_id' => $agent_name,          
          'service_type' => $service_id, 
          't_amount' => $t_amount,
          't_b_charge' => $t_b_price, 
          't_charge' => $t_s_price,           
          't_air_name' => $air_line_id,                        
          'new_date' => $new_flight_date,
          'old_date' => $old_flight_date,
          't_pendind_time' => $t_pending,             
          'sector_from' => $vanue_from_id,
          'sector_to' => $vanue_to_id,           
          'air_line_pnr' => $air_line_pnr,           
          'gds_pnr' => $gds_pnr,                                                 
          'work_status' => $work_status,        
          'status' => 'agent',          
          'entry_by' => $userid,       
          'entry_date' => $date_time,
          'update_by' => $userid
               
          );      
        $service_add=$obj->Update_data("tbl_ticket_sale",$form_data,"where ticket_id='$token'");
       if($service_add){                      
           ?>
            <script>
              window.location="?q=view_t_agent";
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
<link href="asset/css/extra.css" rel="stylesheet" type="text/css" >
 

<form id="frm_area" enctype="multipart/form-data" method="post" action="" name="frm_area">
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
                                <label class="control-label" for="agn_id">Agent Name</label>
                                <div class="controls">
                                    <select class="span9"  validate="Require" name="agent_name">
                                        <option value="">Select</option>                       
                                        <?php                                           
                                            foreach ($obj->view_all("tbl_agent") as $value){                                                                                                            
                                        ?>
                                        <option <?php  if($details['t_agent_id']==$value['ag_id']) echo 'selected'  ?>  value="<?php echo isset($value['ag_id'])?$value['ag_id']:NULL;?>"><?php echo isset($value['ag_name'])?$value['ag_name']:NULL;?></option>
                                         <?php
                                            }
                                            ?> 
                                    </select>
                                    <span class="help-inline"> * </span>
                                </div>
                            </div>
                                                     
                            <div class="control-group">
                                <label class="control-label" for="service_id"> Service </label>
                                <div class="controls">
                                    <select id="service_id" class="span9" onchange="service_std_toure_fnc()" validate="Require" placeholder="Service" name="service_id">
                                        <option value="">Select</option>
                                        <?php
                                            $i='0';
                                            foreach ($obj->view_all("tbl_service") as $value){
                                                $i++;                                                              
                                        ?>
                                        <option <?php  if($details['service_type']==$value['s_id']) echo 'selected'  ?>  value="<?php echo isset($value['s_id'])?$value['s_id']:NULL;?>"><?php echo isset($value['s_name'])?$value['s_name']:NULL;?></option>
                                         <?php
                                            }
                                            ?> 
                                    </select>
                                    <span class="help-inline"> * </span>
                                </div>
                            </div>
                            <div class="control-group div_air_line_id" >
                                <label class="control-label" for="air_line_id">Air Lines</label>
                                <div class="controls">
                                    <select id="air_line_id" class="span9" placeholder="Air Lines" name="air_line_id">
                                        <option value="">Select</option>
                                        <?php
                                            $i='0';
                                            foreach ($obj->view_all("tbl_airlines") as $value){
                                                $i++;                                                              
                                        ?>
                                        <option  <?php  if($details['t_air_name']==$value['air_id']) echo 'selected'  ?> value="<?php echo isset($value['air_id'])?$value['air_id']:NULL;?>" ><?php echo isset($value['air_name'])?$value['air_name']:NULL;?></option>
                                         <?php
                                            }
                                        ?>
                                    </select>
                                    <span class="help-inline"> * </span>
                                </div>
                            </div>
                            <div class="control-group div_new_flight_date" >
                                <label class="control-label" for="new_flight_date"> New Flight Date </label>
                                <div class="controls">
                                    <div class="input-append " >
                                        <input value="<?php echo $details['new_date']? $details['new_date']:NULL; ?>" id="datetimepicker" class="span9 " type="text" placeholder="New Flight Date" name="new_flight_date">
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="control-group div_old_flight_date" >
                                <label class="control-label" for="old_flight_date"> Old Flight Date </label>
                                <div class="controls">
                                    <div class="input-append">
                                        <input value="<?php echo $details['old_date']? $details['old_date']:NULL; ?>" id="datetimepicker1" class="span9 " type="text" placeholder="Old Flight Date" name="old_flight_date">
                                        
                                    </div>
                                </div>
                            </div>
                           
                            <div class="control-group div_sector" >
                                <label class="control-label" for="branch_id"> Sector </label>
                                <div class="controls">
                                    <select id="vanue_from_id" class="span3" placeholder="Sectoer" name="vanue_from_id">
                                        <option value="">Select From</option>
                                        <?php
                                            $i='0';
                                            foreach ($obj->view_all("tbl_sector") as $value){
                                                $i++;                                                              
                                        ?>
                                        <option <?php  if($details['sector_from']==$value['sec_id']) echo 'selected'  ?> value="<?php echo isset($value['sec_id'])?$value['sec_id']:NULL;?>"><?php echo isset($value['sec_name'])?$value['sec_name']:NULL;?></option>
                                         <?php
                                            }
                                            ?> 
                                    </select>
                                    <select id="vanue_to_id" class="span3" placeholder="Sectoer" name="vanue_to_id">
                                        <option value="">Select To</option>
                                        <?php
                                            $i='0';
                                            foreach ($obj->view_all("tbl_sector") as $value){
                                                $i++;                                                              
                                        ?>
                                        <option <?php  if($details['sector_to']==$value['sec_id']) echo 'selected'  ?>  value="<?php echo isset($value['sec_id'])?$value['sec_id']:NULL;?>"><?php echo isset($value['sec_name'])?$value['sec_name']:NULL;?></option>
                                         <?php
                                            }
                                            ?> 
                                    </select>
                                </div>
                            </div>
                            <div class="control-group div_air_line_pnr" >
                                <label class="control-label" for="air_line_pnr"> Air Line PNR </label>
                                <div class="controls">
                                    <input value="<?php echo $details['air_line_pnr']? $details['air_line_pnr']:NULL; ?>" id="air_line_pnr" class="span9" type="text" placeholder="Air Line PNR" name="air_line_pnr">
                                    <span class="help-inline"> * </span>
                                </div>
                            </div>
                            <div class="control-group div_gds_pnr" >
                                <label class="control-label" for="gds_pnr"> GDS PNR </label>
                                <div class="controls">
                                    <input value="<?php echo $details['gds_pnr']? $details['gds_pnr']:NULL; ?>" id="gds_pnr" class="span9" type="text" placeholder="GDS PNR" name="gds_pnr">
                                </div>
                            </div>
                                                                                 
                            <div class="control-group">
                                <label class="control-label" for="t_amount">Ticket Quantity</label>
                                <div class="controls">
                                    <input value="<?php echo $details['t_amount']? $details['t_amount']:NULL; ?>" id="service_charge" class="span9" type="text" onkeypress="return numbersOnly(event)" placeholder="Ticket No" name="t_amount">
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="t_b_price">Ticket Bye Price</label>
                                <div class="controls">
                                    <input value="<?php echo $details['t_b_charge']? $details['t_b_charge']:NULL; ?>" id="service_charge" class="span9" type="text" onkeypress="return numbersOnly(event)" placeholder="Ticket bye Price" name="t_b_price">
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="t_s_price">Ticket Sale Price</label>
                                <div class="controls">
                                    <input value="<?php echo $details['t_charge']? $details['t_charge']:NULL; ?>" id="service_charge" class="span9" type="text" onkeypress="return numbersOnly(event)" placeholder="Ticket Sell Price" name="t_s_price">
                                </div>
                            </div>
                             <div class="control-group div_new_flight_date" >
                                <label class="control-label" for="t_s_price"> Pending Time</label>
                                <div class="controls">
                                    <div class="input-append " >
                                        <input value="<?php echo $details['t_pendind_time']? $details['t_pendind_time']:NULL; ?>" name="t_pending" id="datetimepicker2" class="span9" type="text" placeholder="Pending Time" >
                                        
                                    </div>
                                </div>
                            </div>                          
                            
                           <div class="control-group">
                                <label class="control-label" for="work_status"> Work Status </label>
                                <div class="controls">
                                    <select id="service_id" class="span9" name="work_status">
                                        <option <?php if($details['work_status']=='0') echo 'selected';  ?>  value="0">Pending</option>
                                        <option <?php if($details['work_status']=='1') echo 'selected'  ?> value="1">Done</option>                               
                                    </select>                                   
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

<script src="build/jquery.js"></script>
<script src="build/jquery.datetimepicker.full.js"></script>

<script>
    $.datetimepicker.setLocale('en');
  
    $('#datetimepicker_format').datetimepicker();
    
   
    $('#datetimepicker').datetimepicker();
    
    $('#datetimepicker_format1').datetimepicker();
    
   
    $('#datetimepicker1').datetimepicker();
    $('#datetimepicker_format2').datetimepicker();
    
   
    $('#datetimepicker2').datetimepicker();

    $('.some_class').datetimepicker();

</script>