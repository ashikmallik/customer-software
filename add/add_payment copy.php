<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$token = isset($_GET['token1']) ? $_GET['token1'] : NULL;
$total_company =$obj->Total_Count("tbl_agent", "company_id=".$token);
$pay_amountt = $obj->get_sum_by_cond("connect_charge","tbl_agent","company_id=".$token);
$cus_due = $obj->get_sum_by_cond("acc_amount","tbl_account", "company_id='$token'");
$bill_paid = $obj->get_sum_by_cond("acc_amount","tbl_account","acc_type=3", "company_id='$token'");
$all_paid =$cus_due-$bill_paid ;
echo $all_paid;



        
$total_bill =$obj->get_sum_by_cond("taka","tbl_agent","company_id=".$token);



//   ***pay status::1=Customer didn't pay yet && 0= Customer already paid
//          ***due_status::0=Customer have due && 1=Customer dont have due but may be advance
//          *** bill_status::0=bill didnt pay bill && 1=Customer alreay paid  bill current month or advance by previous month


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
        'total_pay_bill' => $amount,
        
        'acc_type' => '3',
        'acc_description' => !empty($description) ? $description : "Bill collection",
        'entry_by' => $userid,
        'entry_date' => $date,
        'update_by' => $userid
    );

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
    $service_add = $obj->Reg_user_cond("tbl_account", $form_data, " ");
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
                    <th class="col-md-1">ID</th>
                    <th class="col-md-1">Company</th>
                    <th class="col-md-1">total link</th>
                    
                    <th class="col-md-1">Address</th>
                    <th class="col-md-1">Mobile No</th>
                    
                    <th class="col-md-1">Taka</th>
                    <th class="col-md-1">C. Date</th>
                    <th class="col-md-1">Zone</th>
                    
                    <th class="col-md-1">Action</th>
                </tr>
                </thead>
                <tr>
                    <td>
                        <?php echo isset($agentDetails['cus_id']) ? $agentDetails['cus_id'] : NULL; ?>
                    </td>
                    <td><?php echo isset($agentDetails['company_name']) ? $agentDetails['company_name'] : NULL; ?></td>
                    <td><?php echo $total_company; ?></td>
                    
                    <td><?php echo isset($agentDetails['link_address']) ? $agentDetails['link_address'] : NULL; ?></td>
                    <td><?php echo isset($agentDetails['phone']) ? $agentDetails['phone'] : NULL; ?></td>
                   

                    <td><?php echo isset($agentDetails['taka']) ? $agentDetails['taka'] : NULL; ?></td>
                    <td><?php echo isset($agentDetails['connection_date']) ? $agentDetails['connection_date'] : NULL; ?></td>
                    <td class="text-center"><?php
                        if (isset($agentDetails['zone']) && !empty($agentDetails['zone'])) {
                            $zoneDataForUser = $obj->details_by_cond('tbl_zone', "zone_id = " . $agentDetails['zone'] . "");
                            echo $zoneDataForUser['zone_name'];
                        }
                        ?>
                    </td>
                   <td>
                        <div class="btn-group">
                            <a class="btn btn-xs btn-info" style="padding:5px;"
                               href="?q=edit_agent&token=<?php echo isset ($agentDetails['company_id']) ? $agentDetails['company_id'] : NULL ?>">
                                <span class="glyphicon glyphicon-edit"></span> Edit
                            </a>
                        </div>
                    </td>
                </tr>
            </table>
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
                    
                     <option value="January">January</option>
                      <option value="February">February</option>
                       <option value="March">March</option>
                        <option value="April">April</option>
                         <option value="May">May</option>
                          <option value="June">June</option>
                           <option value="July">July</option>
                            <option value="August">August</option>
                             <option value="September">September</option>
                              <option value="October">October</option>
                               <option value="November">November</option>
                                <option value="December">December</option>
                                
                 
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Pay Amount</label>
                <input value="<?PHP echo $pay_amountt; ?>" type="text" class="form-control" id="ResponsiveTitle"
                       readonly="">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Due Amount</label>
                <input value="<?php echo $pay_amountt ?>" type="text" class="form-control"
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
                        <th>Date</th>
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
                    foreach ($obj->view_all_by_cond("vw_account", "company_id='$token' order by acc_id") as $customer_info) {
                        extract($customer_info);
                        $i++;
                        $totalin += $customer_info['total_pay_bill']
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo date("d-m-Y", strtotime(isset($customer_info['entry_date']) ? $customer_info['entry_date'] : "2016-02-1")); ?></td>
                            <td style="text-align: right;"><?php echo isset($customer_info['acc_description']) ? $customer_info['acc_description'] : NULL; ?></td>
                            <td style="text-align: right;"><?php echo isset($customer_info['total_pay_bill']) ? $customer_info['total_pay_bill'] : NULL; ?></td>
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
                        <th colspan="3">
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