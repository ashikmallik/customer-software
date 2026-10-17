<?php
date_default_timezone_set('Asia/Dhaka');
$date_time =date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid =isset($_SESSION['UserId']) ? $_SESSION['UserId']:NULL;

// ========== Delete Function Start =================

if( $obj->userWorkPermission($acc,'delete')){

    $dltoken = isset($_GET['dltoken'])? $_GET['dltoken']:NULL;

    if(!empty($dltoken)){

        $dele = $obj->Delete_data("tbl_customer_info","id='$dltoken'");

        if($dele)
        {$notification = 'Delete Successfull';}
        else
        {$notification = 'Delete Failed';}
    }
}
// ========== Delete Function End =================

?>

 <div class="col-md-12" style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
        <b><?php echo isset($notification)? $notification :NULL; ?></b>
 </div>
<div class="col-md-12" style=" background:#606060; margin-top:20px; margin-bottom: 15px; min-height:45px; padding:8px 0px 0px 15px; font-size:16px; font-family:Lucida Sans Unicode; color:#FFFFFF; font-weight:bold;">
    <div class="col-md-6">
         <b>View Customer Information</b>
    </div>           
    <div class="col-md-6" style="">
       <?php if($ty=='SA'){ ?>
        <a class="addbutton" href="?q=add_customer">ADD NEW<span class="glyphicon glyphicon-plus"></span></a>
        <?php } ?> 
    </div>

</div>
<div class="row" style="padding:10px; font-size: 12px;">         
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped" id="datatable">
                <thead> 
                    <tr>
                        <th>#</th>
                        <th>Entry Date</th>
                        <th>CID</th>
                        <th>Customer Name</th> 
                        <th>Branch</th>
                        <th>SMS No</th>
                        <th>Refferal Type</th>                                            
                        <th>Done By</th>
                        <th>Work Status</th>                                          
                        <th>Action</th>
                    </tr>
                </thead>
                   <?php
                    $i='0';
                    foreach ($obj->view_all("vw_customer_info") as $value){
                        $i++;                                                              
                    ?>
                    <tr>
                        <td><?php echo $i;?></td>
                        <td><?php echo isset($value['entry_date'])?$value['entry_date']:NULL;?></td>
                        <td><?php echo isset($value['cus_id'])?$value['cus_id']:NULL;?></td>
                        <td><?php echo isset($value['pax_f_name'])?$value['pax_f_name']:NULL;?><?php echo isset($value['pax_l_name'])?$value['pax_l_name']:NULL;?></td>
                        <td><?php echo isset($value['br_name'])?$value['br_name']:NULL;?></td>
                        <td><?php echo isset($value['sms_no'])?$value['sms_no']:NULL;?></td>
                        <td><?php echo isset($value['refferal_type'])?$value['refferal_type']:NULL;?></td>
                        <td><?php echo isset($value['done_by'])?$value['done_by']:NULL;?></td>
                        <td>                      
                                <?php
                                  if ($value['work_status']=='0')
                                    {echo '<p style="color:red;" >pending</p>';}
                                  elseif($value['work_status']=='1')
                                    {echo 'Done';} 
                                ?>                      
                        </td>   
                        <td>
                            <div class="btn-group" > 
                                <?php if( $obj->userWorkPermission($acc,'edit')){ ?>
                                <a class="btn btn-xs btn-info" style="margin-top: 2px;" href="?q=edit_customer&token=<?php echo isset ($value['id'])?$value['id']:NULL?>">
                                   <span class="glyphicon glyphicon-edit"</span>
                                </a> 
                                <?php 
                                }
                                if( $obj->userWorkPermission($acc,'delete')){
                                ?>                             
                                <a href="?q=view_customer&dltoken=<?php echo isset($value['id'])? $value['id'] :NULL; ?>" class="btn btn-xs btn-danger" style="margin-left: 5px;">
                                   <span class="glyphicon glyphicon-remove"></span>
                                </a> 
                                <?php } ?>
                            </div>                              
                        </td>
                    </tr>
                    <?php
                    }
                    ?>                   
                </table>
            </div>
    </div>
</div>
