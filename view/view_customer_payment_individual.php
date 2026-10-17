<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

$token = isset($_GET['token2']) ? $_GET['token2'] : NULL;
$httpReferer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '#';

$agentDetails = $obj->details_by_cond("tbl_agent", "ag_id='$token'");

if ($agentDetails) {

    $billAmount = ($agentDetails['taka']);
    $totalPay = $bill->get_customer_total_pay($token);
    $dueAmount = $bill->get_customer_dues($token);

    if ($dueAmount >= 0) {

        $finalDueAmount = $dueAmount;
    } else {
        $finalDueAmount = 0;
    }


// ========== Delete Function Start =================
    if ($obj->userWorkPermission($acc, 'delete')) {

        $dltoken = isset($_GET['dltoken']) ? $_GET['dltoken'] : NULL;
        if (!empty($dltoken)) {

            $dele = $obj->Delete_data("tbl_account", "acc_id='$dltoken'");

            if (!$dele) {
                $notification = 'Delete Successfull';
            } else {
                $notification = 'Delete Failed';
            }
        }
    }

// echo $bill->get_customer_dues($token);
// ========== Delete Function End =================
    ?>


    <div class="col-md-12"
         style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
        <b><?php echo isset($notification) ? $notification : NULL; ?></b>
    </div>
    <div class="col-md-12 bg-grey-800" style="margin-bottom:20px;">
        <div class="col-md-10">
            <h4>View Customer Payment Information</h4>
        </div>
        <div class="col-md-2">
            <a class="btn btn-success btn-sm pull-right" href="<?php echo $httpReferer; ?>"
               style="margin-top:3px;"><span class="glyphicon glyphicon-circle-arrow-left"></span> Go Back </a>
        </div>

    </div>
    <div class="row" style="font-size: 12px;">
        <div class="col-md-12">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr class="bg-teal-800">
                    <th class="col-md-1">ID</th>
                    <th class="col-md-1">Name</th>
                    <th class="col-md-1">Address</th>
                    <th class="col-md-1">Mobile No</th>
                    <th class="col-md-1">Speed</th>
                    <th class="col-md-1">Taka</th>
                    <th class="col-md-1">C. Date</th>
                    <th class="col-md-1">Zone</th>
                    <th class="col-md-1">IP</th>
                    <th class="col-md-1">Action</th>
                </tr>
                </thead>
                <tr>
                    <td>
                        <?php echo isset($agentDetails['cus_id']) ? $agentDetails['cus_id'] : NULL; ?>
                    </td>
                    <td><?php echo isset($agentDetails['ag_name']) ? $agentDetails['ag_name'] : NULL; ?></td>
                    <td><?php echo isset($agentDetails['ag_office_address']) ? $agentDetails['ag_office_address'] : NULL; ?></td>
                    <td><?php echo isset($agentDetails['ag_mobile_no']) ? $agentDetails['ag_mobile_no'] : NULL; ?></td>
                    <td><?php echo isset($agentDetails['mb']) ? $agentDetails['mb'] : NULL; ?></td>

                    <td><?php echo isset($agentDetails['taka']) ? $agentDetails['taka'] : NULL; ?></td>
                    <td><?php echo isset($agentDetails['connection_date']) ? $agentDetails['connection_date'] : NULL; ?></td>
                    <td class="text-center"><?php
                        if (isset($agentDetails['zone']) && !empty($agentDetails['zone'])) {
                            $zoneDataForUser = $obj->details_by_cond('tbl_zone', "zone_id = " . $agentDetails['zone'] . "");
                            echo $zoneDataForUser['zone_name'];
                        }
                        ?>
                    </td>
                    <td><?php echo isset($agentDetails['ip']) ? $agentDetails['ip'] : NULL; ?></td>
                    <td>
                        <div class="btn-group">
                            <a class="btn btn-xs btn-info" style="padding:5px;"
                               href="?q=edit_agent&token=<?php echo isset ($agentDetails['ag_id']) ? $agentDetails['ag_id'] : NULL ?>">
                                <span class="glyphicon glyphicon-edit"></span> Edit
                            </a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row" style="padding:10px; font-size: 12px;">

        <div class="col-md-4">
            <div class="form-group">
                <label>Bill Amount</label>
                <input value="<?php echo $billAmount ?>" type="text"  class="form-control"
                       readonly="">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Pay Amount</label>
                <input value="<?PHP echo $totalPay; ?>" type="text" class="form-control"
                       readonly="">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Due Amount</label>
                <input value="<?php echo $finalDueAmount ?>" type="text" class="form-control"
                       readonly="">
            </div>
        </div>
    </div>

    <div class="row" style="padding:10px; font-size: 12px;">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                    <tr>
                        <th class="col-md-1">#</th>
                        <th class="col-md-1">Date</th>
                        <th class="col-md-5">Description</th>
                        <th class="col-md-1">Amount</th>
                        <th class="col-md-2">Received By</th>
                        <th class="col-md-2">Action</th>
                    </tr>
                    </thead>
                    <?php
                    $i = 0;
                    $totalin = 0;
                    foreach ($obj->view_all_by_cond("vw_account", "agent_id='$token' order by acc_id") as $customer_info) {
                        extract($customer_info);
                        $i++;
                        $totalin += $customer_info['acc_amount'];
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo date("d-m-Y", strtotime(isset($customer_info['entry_date']) ? $customer_info['entry_date'] : "2016-02-1")); ?></td>
                            <td style="text-align: right;"><?php echo isset($customer_info['acc_description']) ? $customer_info['acc_description'] : NULL; ?></td>
                            <td style="text-align: left;"><?php echo isset($customer_info['acc_amount']) ? $customer_info['acc_amount'] : NULL; ?></td>
                            <td style="text-align: right;"><?php echo isset($customer_info['FullName']) ? $customer_info['FullName'] : NULL; ?>
                                (<?php echo isset($customer_info['UserName']) ? $customer_info['UserName'] : NULL; ?>)
                            </td>

                            <td>
                                <div class="">
                                    <?php if ($obj->userWorkPermission($acc, 'edit')) { ?>
                                        <a class="btn btn-xs btn-info"
                                           href="?q=edit_agent_payment&token=<?php echo isset($customer_info['acc_id']) ? $customer_info['acc_id'] : NULL ?>">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>
                                        <?php
                                    }
                                    if ($obj->userWorkPermission($acc, 'delete')) {
                                        ?>
                                        <a href="?q=add_payment&token1=<?= $token ?>&dltoken=<?php echo isset($customer_info['acc_id']) ? $customer_info['acc_id'] : NULL; ?>"
                                           class="btn btn-xs btn-danger">
                                            <span class="glyphicon glyphicon-remove"></span>
                                        </a>
                                    <?php } ?>
                                    <a target="_blank" href="print/payment_invoice.php?token=<?php
                                    echo isset ($customer_info['acc_id']) ? $customer_info['acc_id'] : NULL
                                    ?>" class="btn btn-xs btn-success">
                                        <span class="glyphicon glyphicon-print"></span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <th colspan="3" style="text-align: right;">Total:</th>
                        <th colspan="3" style="text-align: left;"><?PHP echo $totalin; ?> Taka</th>
                    </tr>
                    <tr>
                        <?php
                        $word = $formater->convert_number_to_words($totalin);
                        ?>

                        <td colspan="6" style="text-align: center;"><span style="color: green;"><b>Total Amount In Word::&nbsp;&nbsp;&nbsp;</b><span
                                        style="color: red;"><b><?PHP echo $word; ?> Taka Only</b></span></span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <?php
} else {
    ?>
    <div class="row">
        <div class="col-md-12">
            <h2>
                <hr>
            </h2>
            <div class="alert alert-danger text-center" role="alert">
                <strong>Sorry! </strong>The customer you want to view the individual payment record is no longer exist.
                Please <a href="<?php echo $httpReferer; ?>" class="alert-link">go back</a>
            </div>
            <h2>
                <hr>
            </h2>
        </div>
    </div>
    <?php
}
?>