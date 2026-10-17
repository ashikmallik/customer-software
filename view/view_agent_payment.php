<script src="asset/js/jwww.js"></script>  
<?php
date_default_timezone_set('Asia/Dhaka');
$date_time =date('Y-m-d g:i:sA');
$date =date('Y-m-d');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid =isset($_SESSION['UserId']) ? $_SESSION['UserId']:NULL;

// ========== Delete Function Start =================
if( $obj->userWorkPermission($acc,'delete')){

    $dltoken = isset($_GET['dltoken'])? $_GET['dltoken']:NULL;
    if(!empty($dltoken)){

        $dele = $obj->Delete_data("tbl_account","acc_id='$dltoken'");

        if(!$dele)
        {$notification = 'Delete Successfull';}
        else
        {$notification = 'Delete Failed';}
    }
}
// ========== Delete Function End =================


//====================Add Funcyion=========================
if(isset ($_POST['submit'])){
       extract($_POST);
       
       $form_data = array(
           
          'agent_id' => $customer_id,
          'acc_amount' => $amount,
          'acc_type' => '2',
          'entry_by' => $userid,       
          'entry_date' => $date_time,
          'update_by' => $userid
           );
       $service_add=$obj->Reg_user_cond("tbl_account", $form_data, " ");
       
       if($service_add){                      
           ?>
           
            <script>
                
              window.location="?q=view_agent_payment";
            </script>   
<?php                    
       }
       else{
           echo $notification = 'Insert Failed';
       }
   }


?>


<!------------------------------------------>

 <script src="auto_serach/js/jquery-ui.min.js"></script>
 <script src="auto_serach/js/jquery.select-to-autocomplete.js"></script>
 <script>
	  (function($){
	    $(function(){
	      $('select').selectToAutocomplete();
	    });
	  })(jQuery);
 </script>
	<link rel="stylesheet" href="auto_serach/js/jquery-ui.css">
  <style>
	  
    .ui-autocomplete {
      padding: 0;
      list-style: none;
      background-color: #fff;
      width: 218px;
      border: 1px solid #B0BECA;
      max-height: 350px;
      overflow-x: hidden;
    }
    .ui-autocomplete .ui-menu-item {
      border-top: 1px solid #B0BECA;
      display: block;
      padding: 4px 6px;
      color: #353D44;
      cursor: pointer;
    }
    .ui-autocomplete .ui-menu-item:first-child {
      border-top: none;
    }
    .ui-autocomplete .ui-menu-item.ui-state-focus {
      background-color: #D5E5F4;
      color: #161A1C;
    }
	</style>
<!------------------------------------------>



 <div class="col-md-12" style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
        <b><?php echo isset($notification)? $notification :NULL; ?></b>
 </div>
<div class="col-md-12" style=" background:#606060; margin-top:20px; margin-bottom: 15px; min-height:45px; padding:8px 0px 0px 15px; font-size:16px; font-family:Lucida Sans Unicode; color:#FFFFFF; font-weight:bold;">
    <div class="col-md-6">
         <b>View Agent Payment Information</b>
    </div>               
</div>
 <div class="col-md-12" style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
        <b><?php echo isset($notification)? $notification :NULL; ?></b>
 </div>
<div class="col-md-12" style=" background:#606060; margin-top:20px; margin-bottom: 15px; min-height:45px; padding:8px 0px 0px 15px; font-size:16px; font-family:Lucida Sans Unicode; color:#FFFFFF; font-weight:bold;">   
        <form  action="" method="POST">
            <div class="col-md-6">
            <div class="form-group">
                <label for="exampleInputEmail1">&nbsp;Agent Name</label>
                <select required="" name="customername" class="form-control" id="country-selector"  placeholder="Agent Name" autofocus="autofocus" autocorrect="off" autocomplete="off">
                    <?php 
                        foreach ($obj->view_all("tbl_agent") as $value_info){
                                if(!empty($value_info)){
                                        extract($value_info);

                                }                        
                                      ?>  
                        <option value="" selected="selected">Select Country</option>

                        <option value="<?php echo isset($value_info['ag_id'])?$value_info['ag_id']:NULL; ?>" data-alternative-spellings="AF"><?php echo isset($value_info['ag_name'])?$value_info['ag_name']:NULL; ?></option>
                    <?php 
                    }
                    ?>
                </select>
                
             </div>
            </div>
            <div class="col-md-6" style="margin-top: 30px;">
               <button type="submit"  name="search" class="btn btn-primary"><i class="fa fa-search"></i>&nbsp;&nbsp;Search</button> 
            </div>
        </form>                  
</div>


 <?php 
    if(isset($_POST['search'])){
      extract($_POST);
?>
<div class="row" style="padding:10px; font-size: 12px;">
        <?php
        
        $total=0;      
        $serviceamount1=0;
       
       
         foreach ($obj->view_all_by_cond("tbl_ticket_sale","t_agent_id='$customername'") as $details1){
            extract($details1);
            $serviceamount1+=($details1['t_charge']);          
            }                               
            foreach ($obj->view_all_by_cond("vw_account","agent_id='$customername' order by acc_id") as $customer_info){
            extract($customer_info);
            $total+=$customer_info['acc_amount'];
            }       
       $dueamount=$serviceamount1-$total;
      
?>
    <div class="col-md-4">
       <div class="form-group">
            <label>Service Amount</label>
            <input value="<?php echo $serviceamount1 ?>" type="text" name="amount" class="form-control" id="ResponsiveTitle"  readonly="" >
       </div>  
    </div>
    <div class="col-md-4">
        
       <div class="form-group">
            <label>Pay Amount</label>
            <input value="<?PHP echo $total; ?>" type="text" name="amount" class="form-control" id="ResponsiveTitle" readonly="" >
       </div>  
    </div>
    <div class="col-md-4">
       <div class="form-group">
            <label>Due Amount</label>
            <input value="<?php echo $dueamount ?>" type="text" name="amount" class="form-control" id="ResponsiveTitle"  readonly="" >
       </div>  
    </div>
</div>
<div class="row" style="padding:10px; font-size: 12px;">                     
    <form role="form" enctype="multipart/form-data" method="post" action=" ">    
        <div class="row" style="padding:10px; font-size: 12px;">

            <div class="col-md-4">
               <div class="form-group">
                    <label class="col-sm-6" >Payment Date</label>
                     <input value="<?php echo $date ?>" class="" type="text"  class="form-control" id="ResponsiveTitle" readonly=""  ></span>
               </div>
                
                <div class="form-group" style="display: none;">
                    <label class="col-sm-6" >Customer Name</label>
                     <input value="<?php echo $customername ?>" class="" type="text" name="customer_id" class="form-control" id="ResponsiveTitle" readonly=""  ></span>
               </div> 
            </div>
            <div class="col-md-6">
               <div class="form-group">
                   <label class="col-sm-5">Payment Amount</label>
                   <input class="col-sm-7" type="text" name="amount" class="form-control" id="ResponsiveTitle"  required="">
               </div>  
            </div>
            <div class="col-md-2">
                <button style="padding: 0px !important;" type="submit" class="btn btn-success" name="submit">Add Payment</button>                  
            </div>
        </div>
    </form>
</div>
<div class="row" style="padding:10px; font-size: 12px;">         
    <div class="col-md-12">       
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped" id="datatable">
                <thead> 
                    <tr>
                        <th>#</th>
                        <th>Date</th>                      
                        <th>Amount</th>                                             
                        <th>Received By</th>                                             
                        <th>Action</th>
                    </tr>
                </thead>                   
                        <?php
                        $i=0;
                        $totalin=0;
                        foreach ($obj->view_all_by_cond("vw_account","agent_id='$customername' order by acc_id") as $customer_info){
                        extract($customer_info);
                        $i++;
                        $totalin+=$customer_info['acc_amount'];
                        ?>
                        <tr>
                            <td><?php echo $i;  ?></td>
                            <td><?php echo isset($customer_info['entry_date'])?$customer_info['entry_date']:NULL; ?></td>
                            <td style="text-align: right;"><?php echo isset($customer_info['acc_amount'])?$customer_info['acc_amount']:NULL; ?></td>
                            <td style="text-align: right;"><?php echo isset($customer_info['FullName'])?$customer_info['FullName']:NULL; ?>(<?php echo isset($customer_info['UserName'])?$customer_info['UserName']:NULL; ?>)</td>
                              
                            <td>                          
                                <div class="btn-group" > 
                                    <?php if( $obj->userWorkPermission($acc,'edit')){ ?>
                                    <a class="btn btn-xs btn-info" style="margin-top: 2px;" href="?q=edit_agent_payment&token=<?php echo isset ($customer_info['acc_id'])?$customer_info['acc_id']:NULL?>">
                                       <span class="glyphicon glyphicon-edit"</span>
                                    </a> 
                                    <?php 
                                    }
                                    if( $obj->userWorkPermission($acc,'delete')){
                                    ?>                             
                                    <a href="?q=view_agent_payment&dltoken=<?php echo isset($customer_info['acc_id'])? $customer_info['acc_id'] :NULL; ?>" class="btn btn-xs btn-danger" style="margin-left: 5px;">
                                       <span class="glyphicon glyphicon-remove"></span>
                                    </a> 
                                    <?php } ?>
                                    <a target="_blank" href="print/print_agent.php?token=<?php echo isset ($customer_info['acc_id'])?$customer_info['acc_id']:NULL?>" class="btn btn-xs " style="margin-left: 5px; background: green;">
                                        <span style="color: black;" class="glyphicon glyphicon-print"></span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php 
                         }
                        ?>
                      <tr >                          
                          <td colspan="2" style="text-align: right;">Total:</td>
                          <td style="text-align: right;"><?PHP echo $totalin; ?></td>                                   
                      </tr>    
                      <tr>
                          <?php
                            $word=$formater -> convert_number_to_words($totalin);
                          ?>
                          
                          <td colspan="5" style="text-align: center;" ><span style="color: green;">Total Ammount In Word::&nbsp;&nbsp;&nbsp;<span style="color: red;"><?PHP echo $word; ?></span></span></td>                          
                      </tr>    

                
                </table>
            </div>
    </div>
</div>
<?php 
    }
?>
