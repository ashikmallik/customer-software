<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$alldue1 = isset($_SESSION['alldue']) ? $_SESSION['alldue'] : NULL;


$expenseType = 1;
$otherIncomeType = 2;
$billcollectionType = 3;
$connectionChargeType = 4;
$opningAmountType = 5;


$dateMonth = "";
$dateYear = "";
$pre_dateMonth = "";
$pre_dateYear = "";

if (isset($_POST['search'])) {

    extract($_POST);
    $pre_date = date_format(date_create($dateYear . "-" . $dateMonth . "-1"), "Y/m/d");
    $pre_dateMonth = date('n', strtotime($pre_date)) - 1;
    $pre_dateYear = date('Y', strtotime($pre_date));

    $showDate = date('M Y', strtotime('1-' . $dateMonth . '-' . $dateYear));
    $showMonth = date('m', strtotime($pre_date));

    $pdate = $_POST['dateYear'].'-'.$_POST['dateMonth'].'-01';

} else {
    $pre_dateMonth = date("n") - 1;
    $pre_dateYear = date("Y");
    $pdate = date('Y').'-'.date('m').'-01';
    if (isset($_GET['month']) && isset($_GET['year']) && !empty($_GET['month']) && !empty($_GET['year'])) {

        $dateYear = $_GET['year'];
        $dateMonth = $_GET['month'];

        $showDate = date('M Y', strtotime($dateYear . '-' . $dateMonth . '-' . '1'));
        $showMonth = date('m', strtotime($dateYear . '-' . $dateMonth . '-' . '1'));
    } else {

        $dateMonth = date("n");
        $dateYear = date("Y");

        $showDate = date('M Y');
        $showMonth = date('m');
    }

}

//echo $pre_dateYear;
$all_transection_till_previous_month = $obj->get_all_income_till_previous_month($pre_dateMonth, $pre_dateYear);

$all_expense_till_previous_month = $obj->get_all_expense_till_previous_month($pre_dateMonth, $pre_dateYear);
 $all_opening_till_previous_month= $obj->get_all_opening_till_previous_month($pre_dateMonth, $pre_dateYear);

$all_Income_till_previous_month =$all_opening_till_previous_month['amount']+ $all_transection_till_previous_month['amount'] - $all_expense_till_previous_month['amount'];

$get_all_income = $obj->get_all_income_with_condition($pre_dateMonth, $pre_dateYear, 'AND (acc_type=2 OR acc_type=3 OR acc_type=5 )');

$get_connection_charge_amount = $obj->details_selected_field_by_cond('tbl_agent','SUM(connect_charge) as connection_charge',"MONTH(entry_date)='$pre_dateMonth' and YEAR(entry_date)='$pre_dateYear' ");


$get_sum_expense = $obj->get_sum_expense($pre_dateMonth, $pre_dateYear);

//$total_opening_amount = $obj->raw_sql("SELECT SUM(acc_amount) AS amount FROM tbl_account WHERE acc_type='5' AND entry_date<'$pdate'");
//$total_opening_amount = $obj->raw_sql("SELECT SUM(acc_amount) AS amount FROM tbl_account WHERE acc_type='5' AND entry_date='$pdate'");

//echo $all_Income_till_previous_month;
//echo $get_all_income['amount'];
//echo $get_connection_charge_amount['connection_charge'];
//echo $total_opening_amount[0]['amount'];
 //echo $pdate;
$pre_grand_income = $get_all_income['amount'] + $get_connection_charge_amount['connection_charge'] + $all_Income_till_previous_month - $get_sum_expense['amount'];


$dateObj = DateTime::createFromFormat('!m', $dateMonth);

$mon = $dateObj->format('F');



?>
<style>
    .pointer {
        cursor: pointer;

    }

    .pointer:hover {
        color: #0082a4;
        font-weight: bold;
    }
</style>
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>

<div class="col-md-12 bg-grey-800" style="margin-top:20px; margin-bottom: 15px;">
    <div class="col-md-6">
        <h4><strong>The Monthly Statement of <?php echo $showDate; ?></strong></h4>
    </div>
     <?php if($obj->hasPermission($ty, 'print_statement')){ ?>
    <div class="col-md-6" style="padding-top:5px;">
        <button type="submit" class="btn btn-primary bg-teal btn-sm pull-right"
                onclick="printDiv('month_table')">Print Statement
        </button>
    </div>
    <?php } ?>
</div>

<div class="col-md-12 bg-slate-800" style="margin-bottom: 20px;padding:5px 0px">
    <form action="" method="POST">
        <div class="col-md-5">
            <div class="form-group">
                <label class="control-label col-sm-4" style="padding-top:5px" for="dateMonth">Select Month</label>
                <div class="col-sm-8">
                    <select class="form-control" required="required" name="dateMonth" id="status">
                        <?php $formater->monthView($showMonth); ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label class="control-label col-sm-4" style="padding-top:5px" for="damageRate">Select Year</label>
                <div class="col-sm-8">
                    <select class="form-control" required="required" name="dateYear" id="status">
                        <?php for ($i = 2024; $i <= 2035; $i++) {
                            echo '<option value="' . $i . '"';
                            echo (date('Y') == $i) ? " selected " : "";
                            echo '>' . $i . '</option>';
                        } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <button type="submit" name="search" class="btn btn-primary"><span
                        class="glyphicon  glyphicon-search"></span>&nbsp;&nbsp;Search
            </button>
        </div>
    </form>
</div>

<div class="row" id="month_table">
    <div class="col-md-12" style="font-size:12px;">
        <div class="text-center"><h3 id="printTitle"></h3></div>
        <table class="table table-responsive table-bordered table-hover table-striped" id="monthlyTable">
            <thead>
            <tr>
                <th class="bg-success text-center col-md-2" colspan="1" rowspan="2"><b>Date</b></th>
                <th class="bg-info text-center  col-md-6" colspan="4"><b>Income</b></th>
                <th class="bg-success text-center  col-md-2" rowspan="2"><b>Expense</b></th>
            </tr>
            <tr>
                <td class="text-center bg-info col-md-2"><b>Bill Collection</b></td>
                <td class="text-center bg-info col-md-2"><b>Connection Charge</b></td>
                <td class="text-center bg-info col-md-2"><b>Others Income</b></td>
                <td class="text-center bg-info col-md-2"><b>Opening Amount</b></td>

            </tr>
            </thead>
            <tbody>
            <?php
            $total_income = 0;
            $max = 31;
            $totalOpeningBalance = 0;
            $totalBillCollection = 0;
            $totalConnectionCharge = 0;
            $totalOtherIncome = 0;
            $totalOpeningAmount = 0;
            $totalExpense = 0;

            for ($day = 1;
            $day <= $max;
            $day++) {

            $date = $dateYear . '-' . $dateMonth . '-' . $day;
            $date = date('Y-m-d', strtotime($date));
            $get_month = date('m', strtotime($date));
            if ($get_month>$dateMonth){continue;}
            $billCollection = $obj->get_dayWise_total_bill_collection($date);
            $connectionCharge = $obj->get_dayWise_total_connection_charge($date);
            $othersIncome = $obj->get_dayWise_total_other_income($date);
            $openingAmount = $obj->get_dayWise_total_opening_amount($date);

            $expense = $obj->get_daywise_total_expense($date);

            if ($billCollection == 0 && $connectionCharge == 0 && $othersIncome == 0 && $expense == 0 && $openingAmount == 0) {

                continue;
            }

            $totalBillCollection += $billCollection;
            $totalConnectionCharge += $connectionCharge;
            $totalOtherIncome += $othersIncome;
            $totalOpeningAmount += $openingAmount;
            $totalExpense += $expense;

            $totalIncome = $totalBillCollection + $totalConnectionCharge + $totalOtherIncome + $totalOpeningAmount;

            $cashInHand = $pre_grand_income + $totalIncome - $totalExpense;

            ?>
            <tr class="digitData">
                <td class="text-center"><?php echo date('d M Y', strtotime($date)); ?></td>

                <td class="text-center bill <?php echo ($billCollection != 0) ? 'pointer" data-day="' . $date . '">' . number_format($billCollection) : '"data-day="0">--'; ?></td>
                    <td class=" text-center con
                <?php echo ($connectionCharge != 0) ? 'pointer" data-day="' . $date . '">' . number_format($connectionCharge) : '"data-day="0">--'; ?></td>
                <td class="text-center other <?php echo ($othersIncome != 0) ? 'pointer" data-day="' . $date . '">' . number_format($othersIncome) : '"data-day="0">--'; ?></td>
                    <td class=" text-center other
                "><?php echo ($openingAmount != 0) ? number_format($openingAmount) : '--'; ?></td>
                <td class="text-center expense <?php echo ($expense != 0) ? 'pointer" data-day="' . $date . '">' . number_format($expense) : '"data-day="0">--'; ?></td>
                </tr>

                <?php
                }
                ?>

            <tr class=" bg-warning
                ">
                <th class="text-center">Total</th>
                <th class="text-center"><?php echo ($totalBillCollection != 0) ? number_format($totalBillCollection) : '-'; ?></th>
                <th class="text-center"><?php echo ($totalConnectionCharge != 0) ? number_format($totalConnectionCharge) : '-'; ?></th>
                <th class="text-center"><?php echo ($totalOtherIncome != 0) ? number_format($totalOtherIncome) : '-'; ?></th>
                <th class="text-center"><?php echo ($totalOpeningAmount != 0) ? number_format($totalOpeningAmount) : '-'; ?></th>
                <th class="text-center"><?php echo ($totalExpense != 0) ? number_format($totalExpense) : '-'; ?></th>
            </tr>
            <tr>
                <th colspan="3" class="text-right" style='font-size:15px;'>Opening Balance</th>
                <th class="text-center"
                    style='font-size:15px;'><?php echo ($pre_grand_income) ? number_format($pre_grand_income, 2, '.', ',') : '' ?>
                    Taka
                </th>

                <th class="text-right" style='font-size:15px;'>Cash In Hand</th>
                <th class="text-center"
                    style='font-size:15px;'><?php echo (isset($cashInHand)) ? number_format($cashInHand, 2, '.', ',') : '' ?>
                    Taka
                </th>

            </tr>
            <tr>
                <th colspan="3" class="text-right" style='font-size:15px;'>Total Income</th>
                <th class="text-center"
                    style='font-size:15px;'><?php echo (isset($totalIncome)) ? number_format($totalIncome, 2, '.', ',') : '' ?>
                    Taka
                </th>

                <th class="text-right" style='font-size:15px;'>Total Expense</th>
                <th class="text-center"
                    style='font-size:15px;'><?php echo (isset($totalExpense)) ? number_format($totalExpense, 2, '.', ',') : '' ?>
                    Taka
                </th>

            </tr>

            </tbody>
        </table>
    </div>

    <div class="col-md-4 col-md-offset-4">
        <div>
            <table class="table">
                <tr>
                    <td><b>Opening Balance:</b></td>
                    <th style="text-align: right;"><?php echo (isset($pre_grand_income)) ? number_format($pre_grand_income, 2, '.', ',') : '' ?>
                        Taka
                    </th>
                </tr>
                <tr>
                    <td><b>Total Income:</b></td>
                    <th style="text-align: right;"><?php echo (isset($totalIncome)) ? number_format($totalIncome, 2, '.', ',') : '' ?>
                        Taka
                    </th>
                </tr>
                <tr>
                    <td><b>Total Expense:</b></td>
                    <th style="text-align: right;"><?php echo (isset($totalExpense)) ? number_format($totalExpense, 2, '.', ',') : '' ?>
                        Taka
                    </th>
                </tr>
                <tr>
                    <td><b>Cash In Hand:</b></td>
                    <th style="text-align: right;"><?php echo (isset($cashInHand)) ? number_format($cashInHand, 2, '.', ',') : '' ?>
                        Taka
                    </th>
                </tr>
            </table>
        </div>
    </div>
    <hr>
    <div class="col-md-12 bg-danger">
        <h4 class="text-center" style="padding-bottom:30px">Cash In Hand
            Word: <?php echo (isset($cashInHand)) ? ucwords($formater->convert_number_to_words($cashInHand)) : 'Zero Taka' ?></h4>
        <h6 class="pull-right text-center" style="border-top:2px solid black;width:40%">Authorized Signature<h3>
    </div>
</div>

<script>

    $(document).ready(function () {
        $('select').selectpicker();

        $('table#monthlyTable tbody tr.digitData td.bill').on('click', function () {
            if ($(this).hasClass('pointer')) {
                var date = $(this).data('day');
                window.open('?q=daily_acc_statement&action=bill&day=' + date + '', '_blank');
            }
        });

        $('table#monthlyTable tbody tr.digitData td.con').on('click', function () {
            if ($(this).hasClass('pointer')) {
                var date = $(this).data('day');
                window.open('?q=daily_acc_statement&action=con&day=' + date + '', '_blank');
            }
        });

        $('table#monthlyTable tbody tr.digitData td.other').on('click', function () {
            if ($(this).hasClass('pointer')) {
                var date = $(this).data('day');
                window.open('?q=daily_acc_statement&action=other&day=' + date + '', '_blank');
            }
        });
        $('table#monthlyTable tbody tr.digitData td.expense').on('click', function () {
            if ($(this).hasClass('pointer')) {
                var date = $(this).data('day');
                window.open('?q=daily_expense_report&day=' + date + '', '_blank');
            }
        });

    });

    function printDiv(divName) {
        $(".print").css("margin-left", "0px");
        $('h3#printTitle').html('The Monthly Statement of <?php echo $showDate; ?>');
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        $('h3#printTitle').html('');
    }

</script>

