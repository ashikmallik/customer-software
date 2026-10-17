<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$day = date('M-Y');
$notification = "";
$month=date('m');
$year=date('Y');
$doubleCollection = $obj->raw_sql("SELECT DISTINCT `agent_id`,COUNT(`agent_id`) AS totalpaid, SUM(`acc_amount`) as totalamount FROM `tbl_account` WHERE (Month(entry_date) = '$month' AND Year(entry_date)='$year') GROUP BY `agent_id` ORDER BY `totalpaid` DESC");

?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<style>

    .progress-label {
        display: none !important;
    }

    .ui-widget-header {
        border: 1px solid #2776f4;
        background: #397fef;
        color: #333333;
        font-weight: bold;
    }

    #datatable_view_paid_filter input {
        height: 30px;
        border-radius: 5px;
        padding: 5px;
    }

</style>

<div class="row">
    <div class="bg-grey-800 col-md-12">
        <h4>View Current Month Double Paid/Collection  Customer List (<?php echo date('M-Y') ?> )</h4>
    </div>
</div>

<div class="col-md-12"
     style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
    <h3><?php echo isset($notification) ? $notification : NULL; ?></h3>
</div>


<div class="row" style="font-size: 12px;">
    <div class="col-md-12 table-responsive">
        <table class="table table-responsive table-bordered table-hover table-striped" id="datatable">
            <thead>
            <tr>

                <th class="col-md-1">Customer Name</th>
                <th class="col-md-1">Customer ID</th>
                <th class="col-md-1">Address</th>
                <th class="col-md-1">Mobile No</th>
                <th class="col-md-1">Speed</th>
                <th class="col-md-1">Monthly</th>
                <th class="col-md-1">IP</th>
                <th class="col-md-1">Time</th>
                <th class="col-md-1">Amount</th>
            </tr>
            </thead>
            <tbody>
                <?php foreach($doubleCollection as $collected){ 
                    if($collected['totalpaid'] > 1){
                        $aginfo = $obj->details_by_cond('tbl_agent', 'ag_id='.$collected['agent_id']);
                        echo '<tr> 
                              <td>'.$aginfo['ag_name'].'</td>
                              <td><a href="?q=view_customer_payment_individual&token2='.$aginfo['ag_id'].'">'.$aginfo['cus_id'].'</a></td>
                              <td>'.$aginfo['ag_office_address'].'</td>
                              <td>'.$aginfo['ag_mobile_no'].'</td>
                              <td>'.$aginfo['mb'].'</td>
                              <td>'.$aginfo['taka'].'</td>
                              <td>'.$aginfo['ip'].'</td>
                              <td>'.$collected['totalpaid'].'</td>
                              <td>'.$collected['totalamount'].'</td>
                              </tr>';
                    }
                    
                } ?>
                
                
                
            </tbody>
        </table>
        
    </div>
</div>
