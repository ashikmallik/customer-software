<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;


$token = isset($_GET['token1']) ? $_GET['token1'] : NULL;
// ========== Delete Function Start =================
$dltoken = isset($_GET['dltoken']) ? $_GET['dltoken'] : NULL;
if (!empty($dltoken)) {
    $dele = $obj->Delete_data("tbl_account", "acc_id='$dltoken'");
    if (!$dele) {
        $notification = 'Delete Successfull';
    } else {
        $notification = 'Delete Failed';
    }
}
//====================Add Funcyion=========================
if (isset($_POST['submit'])) {

    extract($_POST);
    $form_data = array(
        'agent_id' => $token,
        'acc_amount' => $amount,
        'acc_type' => '3',
        'acc_description' => !empty($description) ? $description : "Bill collection",
        'entry_by' => $userid,
        'entry_date' => $date,
        'update_by' => $userid
    );

    $bill_amount = $amount;
    $agentData = $obj->details_by_cond("tbl_agent", "ag_id='$token'");
    $mobile = isset($agentData['ag_mobile_no']) ? $agentData['ag_mobile_no'] : NULL;
     $cus= isset($agentData['ip']) ? $agentData['ip'] : NULL;

    $smsbody = "Dear $cus,Your Internet Bill-$bill_amount taka has been paid successfully. Thank you. ".$obj->getSettingValue('sms', 'company_name'). ".Support-".$obj->getSettingValue('sms', 'support_num').".";
    // in this example, POST request was made using PHP's CURL
    if (isset($sms)) {
        $mobilenum="88".$mobile;
        $smsbody = "Dear $cus,Your Internet Bill-$bill_amount taka has been paid successfully. Thank you. ".$obj->getSettingValue('sms', 'company_name'). ".Support-".$obj->getSettingValue('sms', 'support_num').".";
        $obj->sendsms($smsbody, $mobilenum);
    }

    if (isset($_POST['check'])) {

        $due_list = $obj->Update_data("tbl_agent", ['due_status' => 1], "where ag_id='$token'");
    }
    //bonus
    $service_add = $obj->Reg_user_cond("tbl_account", $form_data, " ");
    if (isset($_POST['bonus']) && !empty($_POST['bonus'])) {
        $form_data_bonus = array(
            'customerID' => $token,
            'amount' => $bonus,
            'takenBy' => $userid
        );
        $service_add2 = $obj->Reg_user_cond("bonus", $form_data_bonus, " ");
    }
    if ($service_add) {
        ?>
        <script>
            window.location = "?q=view_bad_due_payment";
        </script>
        <?php
    } else {
        echo $notification = 'Insert Failed';
    }
}
// ========== Delete Function End =================
//------------------------------------------------------

$agentDetails = $obj->details_by_cond("tbl_agent", "ag_id='$token'");

$serviceamount1 = 0;
$total = 0;
foreach ($obj->view_all_by_cond("vw_account", "agent_id='$token' order by acc_id") as $customer_info) {
    extract($customer_info);
    $total += $customer_info['acc_amount'];
}
$bonus = 0;
foreach ($obj->view_all_by_cond("bonus", "customerID='$token' ") as $customer_bonus) {
    extract($customer_bonus);
    $bonus += $customer_bonus['amount'];
}
$dueamount = $bill->bad_customer_dues($token);
//pay_status=0 means bill paid

if ($dueamount >= 0) {

    $obj->Update_data("tbl_agent", ['pay_status' => 1], "where ag_id='$token'");

    $finalDueAmount = $dueamount;
} else {
    $finalDueAmount = 0;
}

?>


    <div class="col-md-12"
         style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
        <b><?php echo isset($notification) ? $notification : NULL; ?></b>
    </div>
    <div class="col-md-12"
         style=" background:#606060; margin-top:20px; margin-bottom: 15px; min-height:45px; padding:8px 0px 0px 15px; font-size:16px; font-family:Lucida Sans Unicode; color:#FFFFFF; font-weight:bold;">
        <div class="col-md-6">
            <b>View Payment Information for <?php echo $agentDetails['ag_name']; ?></b>
        </div>
    </div>

    <div class="row" style="font-size: 12px;">
        <div class="col-md-3">
            <div class="form-group">
                <label>Service Amount</label>
                <input value="<?php echo $agentDetails['taka']; ?>" type="text" class="form-control"
                       id="ResponsiveTitle" readonly="">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Payment Month </label>
                <select class="form-control" id="payment_month">
                    <option value=""></option>
                    <?php
                    for ($i = 1; $i <= 12; $i++) {
                        $monthName = DateTime::createFromFormat('n', $i)->format('F');
                        echo '<option value="' . $monthName . '">' . $monthName . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Pay Amount</label>
                <input value="<?PHP echo $total; ?>" type="text" class="form-control" id="ResponsiveTitle"
                       readonly="">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Due Amount</label>
                <input value="<?php echo $finalDueAmount ?>" type="text" class="form-control"
                       id="ResponsiveTitle" readonly="">
            </div>
        </div>
    </div>

    <form id="paid_form" role="form" enctype="multipart/form-data" method="post" action=" ">
        <div class="row" style="font-size: 12px;">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Payment Date</label>
                    <input class="form-control" value="<?php echo $date ?>" type="text" id="" readonly="">
                </div>

            </div>
            <div class="col-md-3">
                <div class="form-group" style="margin-top: 30px;">
                    <div class="checkbox">
                        <label><input type="checkbox" name="sms" checked value="1"><b>Send SMS Notification</b> </label>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Payment Amount</label>
                    <input id="in_amount" class="form-control" onkeypress="return numbersOnly(event)"
                           style="height: 30px;" type="text" name="amount" required="">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Discount(optional)</label>
                    <input class="form-control" placeholder="Bonus(optional)" onkeypress="return numbersOnly(event)"
                           style="height: 30px;" type="text" name="bonus">
                </div>
            </div>

        </div>
        <div class="row" style="font-size: 12px;">
            <div class="col-md-10">
                <div class="form-group">
                    <label>Payment Description</label>
                    <textarea class="form-control" name="description" placeholder="Enter Description..."></textarea>
                </div>
            </div>
            <div class="col-md-2">
                <button id="btn_submit" style="padding: 10px !important;margin-top:23%;" type="submit"
                        class="btn btn-success" name="submit">Add Payment
                </button>
            </div>
        </div>

    </form>
    <hr>
        <div class="col-md-2">
        <?php if ($ty == 'SA' || $obj->hasPermission($ty, 'show_change_payment_history')) {
            echo '<a href="?q=printpaymenthistory&chhistoryPrint='.$token.'" id="print_link" target="_blank" class="btn btn-primary btn-xs pull-right" >Show Change Payment History</a>';
        } ?>

    </div>
<div class="row pull-right" id="print_row" style="margin-bottom: 10px;
    margin-right: -140px;">
<!--     <div class="col-md-2">
        <?php if ($ty == 'SA') {
            echo '<a href="?q=printpaymenthistory&historyPrint='.$token.'" id="print_link" target="_blank" class="btn btn-primary btn-xs pull-right" >Print Payment History</a>';
        } ?>

    </div> -->
    <div class="col-md-2">
        <?php if ($ty == 'SA' || $obj->hasPermission($ty, 'print_payment_history')) {
            echo '<a href="?q=printpaymenthistory&historyPrint='.$token.'" id="print_link" target="_blank" class="btn btn-primary btn-xs pull-right" >Print Payment History</a>';
        } ?>

    </div>
</div>
    <div class="row" style="font-size: 12px;">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped" id="datatable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th class="col-md-1">Month</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Received By</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>

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
                            <td><?php if ($customer_info['entry_date']!='0000-00-00 00:00:00'){echo date('h:i A', strtotime($customer_info['entry_date'])).'<br>'.date('d, M Y', strtotime($customer_info['entry_date']));} ?></td>
                            <td><?php echo date("F", strtotime(isset($customer_info['entry_date']) ? $customer_info['entry_date'] : "2016-02-1")); ?></td>
                            <td style="text-align: right;"><?php echo isset($customer_info['acc_description']) ? $customer_info['acc_description'] : NULL; ?></td>
                            <td style="text-align: right;"><?php echo isset($customer_info['acc_amount']) ? $customer_info['acc_amount'] : NULL; ?></td>
                            <td style="text-align: right;"><?php echo isset($customer_info['FullName']) ? $customer_info['FullName'] : NULL; ?>
                                (<?php echo isset($customer_info['UserName']) ? $customer_info['UserName'] : NULL; ?>)
                            </td>

                            <td>
                                <div class="btn-group">
                                    <?php foreach ($acc as $per) {
                                        if ($per == 'edit') {
                                            ?>
                                            <a class="btn btn-xs btn-info" style="margin-top: 2px;"
                                               href="?q=edit_agent_payment&token=<?php echo isset($customer_info['acc_id']) ? $customer_info['acc_id'] : NULL ?>">
                                                <span class="glyphicon glyphicon-edit"></span>
                                            </a>
                                            <?php
                                        }
                                    }
                                    foreach ($acc as $per) {
                                        if ($per == 'delete') {
                                            ?>
                                            <a href="?q=add_payment&token1=<?= $token ?>&dltoken=<?php echo isset($customer_info['acc_id']) ? $customer_info['acc_id'] : NULL; ?>"
                                               class="btn btn-xs btn-danger" style="margin-left: 5px;">
                                                <span class="glyphicon glyphicon-remove"></span>
                                            </a>
                                        <?php }
                                    }
                                    ?>
                                    <a target="_blank"
                                       href="print/payment_invoice.php?token=<?php echo isset($customer_info['acc_id']) ? $customer_info['acc_id'] : NULL ?>"
                                       class="btn btn-xs " style="margin-left: 5px; background: green;">
                                        <span style="color: black;" class="glyphicon glyphicon-print"></span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tbody>

                    <thead>
                    <tr>
                        <th colspan="4">
                            <div class="pull-right">Total</div>
                        </th>
                        <th colspan="3"><b><?php echo $totalin; ?> taka</b></th>
                    </tr>
                    <tr>
                        <th colspan="6" style="text-align: center;"><span style="color: green;">Total Ammount In Word::&nbsp;&nbsp;&nbsp;<span
                                        style="color: red;"><?PHP echo ucwords($formater->convert_number_to_words($totalin)); ?></span></span>
                        </th>
                    </tr>
                    </thead>

                </table>

                <hr>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {

            $('select#payment_month').on('change', function (event) {

                var monthName = $(this).val();
                var descriptionField = $('form#paid_form textarea[name="description"]');
                if ($(this).val()) {
                    descriptionField.val('Additional Payment of month : ' + monthName)
                }else{
                    descriptionField.val('');
                }
            });
            /*$("#print_link").click(function(){
                 $('a#print_link').attr('href','?q=printclient&historyPrint=' + zoneId);
          });
        });*/
        $("#hold").click(function(){
        var hold = <?php echo $token ?>;
        //alert(hold);
            $.ajax({

                type: "get",
                url: 'view/ajax_action/add_ajax_data.php',
                data: 
                {
                    'hold' : hold
                },
                success: function()
                {
                    alert("Bill collection Of this customer has been hold");
                }

              });
           });
    });
        function numbersOnly(e) // Numeric Validation
        {
            var unicode = e.charCode ? e.charCode : e.keyCode
            if (unicode != 8) {
                if ((unicode < 2534 || unicode > 2543) && (unicode < 48 || unicode > 57)) {
                    return false;
                }
            }
        }
    </script>
<?php
//check for full payment or not
$flag = isset($_GET['flag']) ? $_GET['flag'] : "0";
if ($flag == "1") {
    $auto_amount = isset($_GET['amount']) ? $_GET['amount'] : "0";
    ?>
    <script>
        $(document).ready(function () {
            $("#in_amount").val("<?= $auto_amount ?>");
            $("#in_des").val("Bill Collection Full payment");
            $('#btn_submit').click();
        });
    </script>
    <?php
}
?>