<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
//$date        = date('Y-m-d');
$currentYear = date("Y");
$currentMonth = date("m");
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$token = isset($_GET['token1']) ? $_GET['token1'] : NULL;
$total_company =$obj->Total_Count("tbl_agent", "company_id='$token'");
$total_bill =$obj->get_sum_by_cond("taka","tbl_agent","company_id='$token'");
$current_pay_amountt = $obj->get_sum_by_cond("acc_amount","tbl_account","company_id='$token' and acc_type=3 AND MONTH(entry_date) = MONTH(CURRENT_DATE()) AND YEAR(entry_date) = YEAR(CURRENT_DATE())");
$previous_pay_amountt = $obj->get_sum_by_cond("acc_amount","tbl_account","company_id='$token' and acc_type=3 AND MONTH(entry_date) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR(entry_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)");
$previous_pay_amounttAcc = $obj->get_sum_by_cond("acc_amount","tbl_account","company_id='$token' and acc_type=3 AND MONTH(entry_date) = MONTH(CURRENT_DATE() - INTERVAL 2 MONTH) AND YEAR(entry_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)");
$currentMonthTotalAcc = $obj->get_sum_by_cond(
    "taka","tbl_agent","company_id='$token' AND MONTH(entry_date) = MONTH(CURRENT_DATE()) AND YEAR(entry_date) = YEAR(CURRENT_DATE())");
$currentMonthTotal = $currentMonthTotalAcc - $current_pay_amountt;
$previousMonthTotalAcc = $obj->get_sum_by_cond(
    "taka",
    "tbl_agent",
    "company_id='$token' AND MONTH(entry_date) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR(entry_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)"
);
$previousMonthTotal = $previousMonthTotalAcc - $previous_pay_amountt;
$previousMonthTotal2Acc = $obj->get_sum_by_cond(
    "taka",
    "tbl_agent",
    "company_id='$token' AND MONTH(entry_date) = MONTH(CURRENT_DATE() - INTERVAL 2 MONTH) AND YEAR(entry_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)"
);
$previousMonthTotal2 = $previousMonthTotal2Acc - $previous_pay_amounttAcc;
//var_dump($previous_pay_amountt);

$pay_amountt = $obj->get_sum_by_cond("acc_amount","tbl_account","company_id='$token' and acc_type=3");

$ontime= $obj->get_sum_by_cond("acc_amount","tbl_account","company_id='$token' and acc_type=4");
$total_pay = $pay_amountt+$ontime;


$total_unpaid_am = ($total_bill-$pay_amountt);

// ========== Monthly due =================
$previous_pay_amounttAcc3 = $obj->get_sum_by_cond("acc_amount","tbl_account","company_id='$token' and acc_type=3 AND MONTH(entry_date) = MONTH(CURRENT_DATE() - INTERVAL 3 MONTH) AND YEAR(entry_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)");
$previousMonthTotal2Acc3 = $obj->get_sum_by_cond(
    "taka",
    "tbl_agent",
    "company_id='$token' AND MONTH(entry_date) = MONTH(CURRENT_DATE() - INTERVAL 3 MONTH) AND YEAR(entry_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)"
);


$previous_pay_amounttAcc4 = $obj->get_sum_by_cond("acc_amount","tbl_account","company_id='$token' and acc_type=3 AND MONTH(entry_date) = MONTH(CURRENT_DATE() - INTERVAL 4 MONTH) AND YEAR(entry_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)");
$previousMonthTotal2Acc4 = $obj->get_sum_by_cond(
    "taka",
    "tbl_agent",
    "company_id='$token' AND MONTH(entry_date) = MONTH(CURRENT_DATE() - INTERVAL 4 MONTH) AND YEAR(entry_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)"
);



$previous_pay_amounttAcc5 = $obj->get_sum_by_cond("acc_amount","tbl_account","company_id='$token' and acc_type=3 AND MONTH(entry_date) = MONTH(CURRENT_DATE() - INTERVAL 5 MONTH) AND YEAR(entry_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)");
$previousMonthTotal2Acc5 = $obj->get_sum_by_cond(
    "taka",
    "tbl_agent",
    "company_id='$token' AND MONTH(entry_date) = MONTH(CURRENT_DATE() - INTERVAL 5 MONTH) AND YEAR(entry_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)"
);


$previous_pay_amounttAcc6 = $obj->get_sum_by_cond("acc_amount","tbl_account","company_id='$token' and acc_type=3 AND MONTH(entry_date) = MONTH(CURRENT_DATE() - INTERVAL 6 MONTH) AND YEAR(entry_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)");
$previousMonthTotal2Acc6 = $obj->get_sum_by_cond(
    "taka",
    "tbl_agent",
    "company_id='$token' AND MONTH(entry_date) = MONTH(CURRENT_DATE() - INTERVAL 6 MONTH) AND YEAR(entry_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)"
);

$previous_pay_amounttAcc7 = $obj->get_sum_by_cond("acc_amount","tbl_account","company_id='$token' and acc_type=3 AND MONTH(entry_date) = MONTH(CURRENT_DATE() - INTERVAL 7 MONTH) AND YEAR(entry_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)");
$previousMonthTotal2Acc7 = $obj->get_sum_by_cond(
    "taka",
    "tbl_agent",
    "company_id='$token' AND MONTH(entry_date) = MONTH(CURRENT_DATE() - INTERVAL 7 MONTH) AND YEAR(entry_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)"
);

$previous_pay_amounttAcc8 = $obj->get_sum_by_cond("acc_amount","tbl_account","company_id='$token' and acc_type=3 AND MONTH(entry_date) = MONTH(CURRENT_DATE() - INTERVAL 8 MONTH) AND YEAR(entry_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)");
$previousMonthTotal2Acc8 = $obj->get_sum_by_cond(
    "taka",
    "tbl_agent",
    "company_id='$token' AND MONTH(entry_date) = MONTH(CURRENT_DATE() - INTERVAL 8 MONTH) AND YEAR(entry_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)"
);

$previous_pay_amounttAcc9 = $obj->get_sum_by_cond("acc_amount","tbl_account","company_id='$token' and acc_type=3 AND MONTH(entry_date) = MONTH(CURRENT_DATE() - INTERVAL 9 MONTH) AND YEAR(entry_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)");
$previousMonthTotal2Acc9 = $obj->get_sum_by_cond(
    "taka",
    "tbl_agent",
    "company_id='$token' AND MONTH(entry_date) = MONTH(CURRENT_DATE() - INTERVAL 9 MONTH) AND YEAR(entry_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)"
);

$previous_pay_amounttAcc10 = $obj->get_sum_by_cond("acc_amount","tbl_account","company_id='$token' and acc_type=3 AND MONTH(entry_date) = MONTH(CURRENT_DATE() - INTERVAL 10 MONTH) AND YEAR(entry_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)");
$previousMonthTotal2Acc10 = $obj->get_sum_by_cond(
    "taka",
    "tbl_agent",
    "company_id='$token' AND MONTH(entry_date) = MONTH(CURRENT_DATE() - INTERVAL 10 MONTH) AND YEAR(entry_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)"
);


$previous_pay_amounttAcc11 = $obj->get_sum_by_cond("acc_amount","tbl_account","company_id='$token' and acc_type=3 AND MONTH(entry_date) = MONTH(CURRENT_DATE() - INTERVAL 11 MONTH) AND YEAR(entry_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)");
$previousMonthTotal2Acc11 = $obj->get_sum_by_cond(
    "taka",
    "tbl_agent",
    "company_id='$token' AND MONTH(entry_date) = MONTH(CURRENT_DATE() - INTERVAL 11 MONTH) AND YEAR(entry_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)"
);









// ========== Delete Function Start =================
$dltoken = isset($_GET['dltoken']) ? $_GET['dltoken'] : NULL;
if (!empty($dltoken)) {
    $dele = $obj->Delete_data("tbl_account", "acc_id='$dltoken'");
    
    if (!empty($dele)) {
        
        //bill status check::if current month didn't collected any bill 
            if( $obj->Total_Count("tbl_account","company_id =$token AND Month(entry_date) =Date('m')") < 1){
                $obj->Update_data("tbl_agent", ['bill_status' => 0], "where company_id='$token'");
            }
        
         //due status change for check due
        //     $cus_due = $obj->column_by_cond("total_bill","vw_bill_calc", "ag_id='$token'");
        //   if($cus_due > 0){
        //       $obj->Update_data("tbl_agent", ['due_status' => 0,'pay_status' => 1], "ag_id='$token'");
        //   }else{
        //       $obj->Update_data("tbl_agent", ['due_status' => 1,'pay_status' => 0], "ag_id='$token'");
        //   }
        
        $notification = 'Delete Successfull';
    } else {
        $notification = 'Delete Failed';
    }
}

// =========Delete Function End=================
 
//====================Add PAyment Function=========================
if (isset($_POST['submit'])) {

    extract($_POST);

    $form_data = array(
        'agent_id' => $ag_id,

        'company_id' => $token,
        'acc_amount' => $amount,
        'date' => $date,
        'acc_type' => '3',
        'acc_description' => !empty($description) ? $description : "Bill collection",
        'entry_by' => $userid,
        'entry_date' => $pay_month,
        'update_by' => $userid
    );
    
    // if (isset($_POST['pay_month']) && !empty($_POST['pay_month']))  {
        
    //     $from_tbl_bill = array(
    //     'company_id' => $token,
    //     );
    //     $obj->Insert_data("tbl_bill", $from_tbl_bill);
    // }

    $bill_amount = $amount;
    $agentData = $obj->details_by_cond("tbl_agent", "company_id='$token'");
    $mobile = isset($agentData['ag_mobile_no']) ? $agentData['ag_mobile_no'] : NULL;


    $smsbody = "Dear Customer Your Bill-$bill_amount taka has been paid successfully By ".$obj->getSettingValue('sms', 'company_name').".Support-".$obj->getSettingValue('sms', 'support_num').".Thank you.";

    // in this example, POST request was made using PHP's CURL
    if (isset($sms)) {
        $mobilenum="88".$mobile;
        $obj->sendsms($smsbody, $mobilenum);
    }

    
    //bonus
    $service_add = $obj->Insert_data("tbl_account", $form_data);
    if (isset($_POST['bonus']) && !empty($_POST['bonus'])) {
        $form_data_bonus = array(
            'customerID' => $token,
            'amount' => $bonus,
            'takenBy' => $userid
        );
        $service_add2 = $obj->Reg_user_cond("bonus", $form_data_bonus, " ");
    }
    

   //due status change for check due 
    //   $cus_due = $bill->get_customer_dues($token);
    // $cus_due = $obj->column_by_cond("total_bill","vw_bill_calc", "ag_id='$token'");
    //   if($cus_due > 0){
    //       $obj->Update_data("tbl_agent", ['due_status' => 0,'pay_status' => 1], "ag_id='$token'");
    //   }else{
    //       $obj->Update_data("tbl_agent", ['due_status' => 1,'pay_status' => 0], "ag_id='$token'");
    //   }
    
    //collected bill status 


    $obj->Update_data("tbl_agent", ['bill_status' => 1], "where company_id='$token'");
    
    
    
    if ($service_add) {
        ?>
        <script>
           window.location = "?q=add_payment&token1=<?php echo $token?>";
        </script>
        <?php
    } else {
        echo $notification = 'Insert Failed';
    }

    
    
}

$month= Date('M');

     
    
// ========== PAyment Function End =================
//------------------------------------------------------

$agentDetails = $obj->details_by_cond("total_calc", "company_id='$token'");

$serviceamount1 = 0;
$total = 0;
foreach ($obj->view_all_by_cond("vw_account", "company_id='$token' order by acc_id") as $customer_info) {
    extract($customer_info);
    $total += $customer_info['acc_amount'];
}
$bonus = 0;
foreach ($obj->view_all_by_cond("bonus", "customerID='$token' ") as $customer_bonus) {
    extract($customer_bonus);
    $bonus += $customer_bonus['amount'];
}


// $dueamount = $bill->get_customer_dues($token);
 $dueamount = $obj->column_by_cond("taka","total_calc", "company_id='$token'");
if ($dueamount > 0) {
    $obj->Update_data("tbl_agent", ['pay_status' => 1,'due_status' => 0], "where company_id='$token'");
    $finalDueAmount = $dueamount;
} else {
     $obj->Update_data("tbl_agent", ['pay_status' => 0,'due_status' => 1,'bill_status' => 1], "where company_id='$token'");
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
            <b>View Payment Information for <?php echo $agentDetails['company_name']; ?></b>
        </div>
    </div>

    <div class="row" style="font-size: 12px;">
        <div class="col-md-12">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
            
                    <th class="col-md-2">Company</th>
                    <th class="col-md-1">Total link</th>
                    
                    <th class="col-md-3">Address</th>
                    <th class="col-md-1">Mobile No</th>
                    
                    <th class="col-md-1">Taka</th>
              
                    
                   
                </tr>
                </thead>
                <tr>
                    <td><?php echo isset($agentDetails['company_name']) ? $agentDetails['company_name'] : NULL; ?></td>
                    <td><?php echo $total_company; ?></td>
                    
                    <td><?php echo isset($agentDetails['company_address']) ? $agentDetails['company_address'] : NULL; ?></td>
                    <td><?php echo isset($agentDetails['phone']) ? $agentDetails['phone'] : NULL; ?></td>
                   

                    <td><?php echo isset($total_bill) ? $total_bill : NULL; ?></td>
              
                   
                </tr>
            </table>
        </div>
    </div>

    <div class="row" style="font-size: 12px;">
        <div class="col-md-3">
            <div class="form-group">
                <label>Ontime Installations</label>
                <input value="<?php echo $ontime; ?>" type="text" class="form-control"
                       id="ResponsiveTitle" readonly="">
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="form-group">
                <label>Total Paid Amount</label>
                <input value="<?PHP echo $total_pay; ?>" type="text" class="form-control" id="ResponsiveTitle"
                       readonly="">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Due Amount <?php echo date('F', strtotime('-2 month')); ?></label>
                <input value="<?PHP echo $previousMonthTotal2; ?>" type="text" class="form-control" id="ResponsiveTitle"
                       readonly="">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Due Amount <?php echo date('F', strtotime('-1 month')); ?></label>
                <input value="<?PHP echo $previousMonthTotal; ?>" type="text" class="form-control" id="ResponsiveTitle"
                       readonly="">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Due Amount <?php echo date('F'); ?></label>
                <input value="<?PHP echo $currentMonthTotal; ?>" type="text" class="form-control" id="ResponsiveTitle"
                       readonly="">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Total Due Amount</label>
                <input value="<?php echo $total_unpaid_am ?>" type="text" class="form-control"
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
                <div class="form-group">
                    <label>Payment Amount</label>
                    <input id="in_amount" class="form-control" onkeypress="return numbersOnly(event)"
                           style="height: 30px;" type="text" name="amount" required="">
                </div>
            </div>
            <div class="col-md-3">
            <div class="form-group">
                <label>Payment Month </label>
                <input type="text" class="form-control datepicker"
                       id="ResponsiveTitle" name="pay_month" required="">
                
            </div>
        </div>
            <!-- <div class="col-md-3">
                <div class="form-group">
                    <label>Discount(optional)</label>
                    <input class="form-control" placeholder="Bonus(optional)" onkeypress="return numbersOnly(event)"
                           style="height: 30px;" type="text" name="bonus">
                </div>
            </div> -->

        </div>
        <div class="row" style="font-size: 12px;">
            <div class="col-md-10">
                <div class="form-group">
                    <label>Payment Description</label>
                    <textarea class="form-control" name="description" placeholder="Enter Description..."></textarea>
                </div>
            </div>
            <input type="hidden" name="ag_id" value="<?= $agentDetails['ag_id']; ?> ?>">
            <div class="col-md-2">
                <button id="btn_submit" style="padding: 10px !important;margin-top:23%;" type="submit"
                        class="btn btn-success" name="submit">Add Payment
                </button>
            </div>
        </div>

    </form>
    <hr>
    <div class="row" style="font-size: 12px;">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped" id="datatable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Payment Month</th>
                        <th>Entry Date</th>
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
                    foreach ($obj->view_all_by_cond("tbl_account", "company_id='$token' and acc_type=3") as $customer_info) {
                        extract($customer_info);
                        $i++;
                        $totalin += $customer_info['acc_amount'];
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo date("F Y", strtotime(isset($customer_info['entry_date']) ? $customer_info['entry_date'] : "2016-02-1")); ?></td>
                            <td><?php echo date("d-m-Y", strtotime(isset($customer_info['date']) ? $customer_info['date'] : "2016-02-1")); ?></td>
                            <td style="text-align: right;"><?php echo isset($customer_info['acc_description']) ? $customer_info['acc_description'] : NULL; ?></td>
                            <td style="text-align: right;"><?php echo isset($customer_info['acc_amount']) ? $customer_info['acc_amount'] : NULL; ?></td>
                            <?php foreach ($obj->view_all_by_cond('_createuser',"UserId=$userid") as $singleUser) { ?>
                            
                        
                            <td style="text-align: right;"><?php echo isset($singleUser['FullName']) ? $singleUser['FullName'] : NULL; ?>
                                (<?php echo isset($singleUser['UserName']) ? $singleUser['UserName'] : NULL; ?>)
                            </td>
                            <?php } ?>

                            <td>
                                <div class="btn-group">
                                    <?php foreach ($acc as $per) {
                                        if ($per == 'edit') {
                                            ?>
                                            <a class="btn btn-xs btn-info" style="margin-top: 2px;"
                                               href="?q=edit_agent_payment&token=<?php echo isset($customer_info['company_id']) ? $customer_info['company_id'] : NULL ?>">
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
                                       href="print/payment_invoice.php?token=<?php echo isset($customer_info['company_id']) ? $customer_info['company_id'] : NULL ?>"
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
        $('.datepicker').datepicker({
            autoclose: true,
            toggleActive: true,
            format: 'yyyy-mm-dd',
        });
        $(document).ready(function () {

            $('select#payment_month').on('change', function (event) {

                var monthName = $(this).val();
                var descriptionField = $('form#paid_form textarea[name="description"]');
                if ($(this).val()) {
                    descriptionField.val('Additional Payment of month : ' + monthName)
                }else{
                    descriptionField.val('');
                }
            })
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