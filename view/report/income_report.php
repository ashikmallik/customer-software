<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$dateMonth = "";
$dateYear = "";
$pre_dateMonth = "";
$pre_dateYear = "";

if (isset($_POST['search'])) {
    extract($_POST);
    $pre_date = date_format(date_create($dateYear . "-" . $dateMonth . "-1"), "Y/m/d");
    $pre_dateMonth = date('n', strtotime('-1 months', strtotime($pre_date)));
    $pre_dateYear = date('Y', strtotime('-1 months', strtotime($pre_date)));

} else {
    $pre_dateMonth = date("n", strtotime("-1 months"));
    $pre_dateYear = date("Y", strtotime("-1 months"));

    $dateMonth = date("n");
    $dateYear = date("Y");
}

$get_all_income = $obj->get_all_income($pre_dateMonth, $pre_dateYear);

$get_sum_expense = $obj->get_sum_expense($pre_dateMonth, $pre_dateYear);

$pre_grand_income = $get_all_income['amount'] - $get_sum_expense['amount'];

$dateObj = DateTime::createFromFormat('!m', $dateMonth);

$mon = $dateObj->format('F');

$showDate = $dateObj->format('F') . " " . $dateYear;
?>
<div class="row bg-slate-800">
    <div class="col-md-10">
        <h4> <strong>Monthly Income Sheet of <?php echo $showDate; ?> </strong></h4>
    </div>
    <?php if($obj->hasPermission($ty, 'print_statement')){ ?>
    <div class="col-md-2">
        <button class="btn btn-primary btn-sm pull-right" style="margin-top:3px;" onclick="printDiv('month_table')">Print Statement </button>
    </div>
<?php } ?>
</div>

<div class="row">
    <div class="col-md-12"
         style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
        <b><?php echo isset($notification) ? $notification : NULL; ?></b>
    </div>
</div>


<div class="row">
    <div class="col-md-12 bg-slate-800" style="margin-bottom: 20px;padding:5px 0px">
        <form action="" method="POST">
            <div class="col-md-5">
                <div class="form-group">
                    <label class="control-label col-sm-5" style="padding-top:5px" for="dateMonth">Select Month</label>
                    <div class="col-sm-7">
                        <select class="form-control" name="dateMonth" id="status" required>
                            <?php $formater->monthView($dateMonth); ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label class="control-label col-sm-5" style="padding-top:5px" for="damageRate">Select Year</label>
                    <div class="col-sm-7">
                        <select class="form-control" name="dateYear" id="status" required>
                            <?php $formater->yearView($dateYear); ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" name="search" class="btn btn-default"><span
                            class="glyphicon  glyphicon-search"></span>&nbsp;&nbsp;Search
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row print" id="month_table">
    <div class="col-md-12 text-center">
        <h4>
            <strong>Monthly Income Sheet of <?php echo $showDate; ?> </strong>
        </h4>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped">
            <thead>
            <tr>
                <th>SL. No.</th>
                <th>Date</th>
                <th>Bill Collection</th>
                <th>Connection Charge</th>
                <th>Others Income</th>
                <th class="text-center">Total</th>

            </tr>
            </thead>
            <tbody>
            <?php
            $total_income = 0;
            $k = 1;
            foreach ($obj->view_all_by_cond("vw_comb_income", "MONTH(entry_date)='$dateMonth' and YEAR(entry_date)='$dateYear' order by entry_date") as $customer_info) {
                //initials variables all
                $comb_bill = "";
                $comb_bill_flag = "";
                $bill_c_flag = "";
                $c_charge_flag = "";
                $others_flag = "";
                $bill_c = "-";
                $c_charge = "-";
                $others = "-";
                $row_total_income = 0;
                // End initials

                $comb_bill = $customer_info['comb_amount'];
                $comb_bill_flag = $customer_info['comb_flag'];
                if (strpos($comb_bill_flag, '@') !== false) {
                    $flag_array = explode('@', $comb_bill_flag);
                    $bill_array = explode('@', $comb_bill);
                    $cnt = sizeof($flag_array);
                    $i = 0;
                    //print_r($flag_array);
                    for ($i = 0; $i < $cnt; $i++) {
                        if ($flag_array[$i] == "bill") {
                            $bill_c = number_format($bill_array[$i], 2, '.', '');
                        } else if ($flag_array[$i] == "connection") {
                            $c_charge = number_format($bill_array[$i], 2, '.', '');
                        } else if ($flag_array[$i] == "others") {
                            $others = number_format($bill_array[$i], 2, '.', '');
                        }
                    }
                    //print_r($bill_array);
                    //print_r($flag_array);

                } else if ($comb_bill_flag == "bill") {
                    $bill_c = number_format($comb_bill, 2, '.', '');
                } else if ($comb_bill_flag == "connection") {
                    $c_charge = number_format($comb_bill, 2, '.', '');
                } else if ($comb_bill_flag == "others") {
                    $others = number_format($comb_bill, 2, '.', '');
                }
                //total income count
                if ($others != "-") {
                    $total_income += $others;
                    $row_total_income += $others;
                }
                if ($bill_c != "-") {
                    $total_income += $bill_c;
                    $row_total_income += $bill_c;
                }
                if ($c_charge != "-") {
                    $total_income += $c_charge;
                    $row_total_income += $c_charge;
                }

                ?>
                <tr>
                    <td><?php echo $k++; ?></td>
                    <td><?php echo date("d-m-Y", strtotime(isset($customer_info['entry_date']) ? $customer_info['entry_date'] : "2016-02-1")); ?></td>
                    <td style="text-align: right;"><?php echo $bill_c; ?></td>
                    <td style="text-align: right;"><?php echo $c_charge; ?></td>
                    <td style="text-align: right;"><?php echo $others; ?></td>
                    <td style="text-align: right;"><b><?php echo number_format($row_total_income, 2, '.', ''); ?></b>
                    </td>
                </tr>
                <?php
            }
            ?>
            <tr style='height:33px;'>
                <td colspan="5" style='font-weight:300;font-size:18px;text-align: right;'>Total Income</td>
                <td style='font-weight:300;font-size:18px;text-align: right;'><?php echo number_format($total_income, 2, '.', '') ?></td>
            </tr>
            <tr style='height:33px;'>
                <td colspan="6" style='font-weight:600;font-size:18px;text-align:center;'>
                    <span style="color:red;">In Word: </span>
                    <?php echo $formater->convert_number_to_words($total_income) ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<script>

    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
