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

        $dele = $obj->Delete_data("tbl_service","s_id='$dltoken'");

        if(!$dele)
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
         <b>View Service Information</b>
    </div>           
    <div class="col-md-6" style="">
       <?php if($ty=='SA'){ ?>
        <a class="addbutton" href="?q=add_service">ADD NEW<span class="glyphicon glyphicon-plus"></span></a>
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
                        <th>Service Name</th>
                        <th>Service Details</th>
                        <th>Status</th>                       
                        <th>Action</th>
                    </tr>
                </thead>
                   <?php
                    $i='0';
                    foreach ($obj->view_all("tbl_service") as $value){
                        $i++;                                                              
                    ?>
                    <tr>
                    <td><?php echo $i;?></td>
                    <td><?php echo isset($value['s_name'])?$value['s_name']:NULL;?></td>
                    <td><?php echo isset($value['s_desc'])?$value['s_desc']:NULL;?></td>
                    <td><?php if($value['s_status']=='1'){echo 'Active';}else{echo 'InActive';}?></td>                      
                    <td>
                        <div class="btn-group" > 
                            <?php if( $obj->userWorkPermission($acc,'edit')){ ?>
                            <a class="btn btn-xs btn-info" style="margin-top: 2px;" href="?q=edit_service&token=<?php echo isset ($value['s_id'])?$value['s_id']:NULL?>">
                               <span class="glyphicon glyphicon-edit"</span>
                            </a> 
                            <?php 
                            }
                            if( $obj->userWorkPermission($acc,'delete')){
                                ?>
                            <a href="?q=view_service&dltoken=<?php echo isset($value['s_id'])? $value['s_id'] :NULL; ?>" class="btn btn-xs btn-danger" style="margin-left: 5px;">
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
