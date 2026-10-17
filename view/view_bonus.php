<?php
date_default_timezone_set('Asia/Dhaka');
$date_time =date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid =isset($_SESSION['UserId']) ? $_SESSION['UserId']:NULL;

?>

 <div class="col-md-12" style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
        <b><?php echo isset($notification)? $notification :NULL; ?></b>
 </div>
<div class="col-md-12" style=" background:#606060; margin-top:20px; margin-bottom: 15px; min-height:45px; padding:8px 0px 0px 15px; font-size:16px; font-family:Lucida Sans Unicode; color:#FFFFFF; font-weight:bold;">
    <div class="col-md-6">
         <b>View Discount Information</b>
    </div>           
</div>
<div class="row" style="padding:10px; font-size: 12px;">         
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped" id="datatable">
                <thead> 
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Customer ID</th>
                        <th>Amount</th>
                        <th>Taken By</th>
                    </tr>
                </thead>
                   <?php
                    $i='0';
                    foreach ($obj->view_all_by_cond("bonus","customerID!='0' ") as $bonus){
                        $i++;
                            $agentData = $obj->details_by_cond('tbl_agent', "ag_id = ".$bonus['customerID']."");
                            $userData = $obj->details_by_cond('_createuser', "UserId = ".$bonus['takenBy']."");
                    ?>
                    <tr>
                    <td><?php echo $i;?></td>
                    <td><?php echo date("d-m-Y", strtotime(isset($bonus['date'])?$bonus['date']:"2016-02-1"));?></td>
                    <td><a href="?q=view_customer_payment_individual&token2=<?php echo isset($bonus['customerID'])?$bonus['customerID']:NULL;?>"><?php echo isset($agentData['ag_name']) ? $agentData['ag_name'] : ''; ?></a></td>
                    <td><?php echo isset($bonus['amount'])?$bonus['amount']:NULL;?></td>
                    <td><a href="?q=user_details&token=<?php echo isset($bonus['takenBy'])?$bonus['takenBy']:NULL;?>"><?php echo isset($userData['FullName']) ? $userData['FullName'] : ''; ?> </a></td>
                    </tr>
                    <?php
                    }
                    ?>                   
                </table>
            </div>
    </div>
</div>