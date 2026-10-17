<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$alldue1 = isset($_SESSION['alldue']) ? $_SESSION['alldue'] : NULL;

//=====================start==============================

$dateMonth = "";
$dateYear = "";
$pre_dateMonth = "";
$pre_dateYear = "";

if (isset($_POST['search'])) {
    extract($_POST);
    $pre_date = date_format(date_create($dateYear . "-" . $dateMonth . "-1"), "Y/m/d");
    $pre_dateMonth = date('n', strtotime('-1 months', strtotime($pre_date)));
    $pre_dateYear = date('Y', strtotime('-1 months', strtotime($pre_date)));

    $showDate = date('M Y', strtotime('1-'.$dateMonth.'-'.$dateYear));

} else {
    $pre_dateMonth = date("n", strtotime("-1 months"));
    $pre_dateYear = date("Y", strtotime("-1 months"));

    $dateMonth = date("n");
    $dateYear = date("Y");

    $showDate = date('M Y');
}
$dateObj = DateTime::createFromFormat('!m', $dateMonth);
$mon = $dateObj->format('F');


//=======================end============================

?>
<script>

    function printDiv(divName) {
        $("table").find('a').each(function () {
            $(this).attr('href', '');
        });
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
    function marginchange() {
        $(".print").css("margin-left", "-30px");
    }
</script>


<div class="row bg-slate-800">
    <div class="col-md-6">
        <h4><strong>View <?php echo $showDate; ?> Expense Report Sheet </strong></h4>
    </div>
    <div class="col-md-6">
        <a class="btn btn-success btn-sm pull-right" href="?q=daily_expense_report" style="margin-top:3px;">Daywise Expense Report</a>
        <a class="btn btn-primary btn-sm pull-right" href="?q=add_expense" style="margin-top:3px;">Add New Expense</a>
        <?php if($obj->hasPermission($ty, 'expense_report_print')){ ?>
        <button class="btn btn-primary btn-sm pull-right"  style="margin-top:3px;" onclick="printDiv('month_table')" >Click to Print</button><?php } ?>
    </div>
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
            <input type="hidden" name="q" value="expense_report">
            <input type="hidden" name="action" value="search">
            <div class="col-md-2">
                <button type="submit" name="search" class="btn btn-default"><span
                            class="glyphicon  glyphicon-search"></span>&nbsp;&nbsp;Search
                </button>
            </div>
        </form>
    </div>
</div>


<div id="month_table" onmouseover="marginchange()">
    <div class="row" style="font-size:14px;">
        <div class="col-md-12">
            <table class="table table-responsive table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>SL No.</th>
                        <th>Account Head</th>
                        <th>Expense</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $total_expense = 0;
                        $i = 1;
                        foreach ($obj->get_all_expense($dateMonth, $dateYear) as $customer_info) {

                            $total_expense += $customer_info['payments'];
                            ?>
                            <tr>
                                <td><?php echo $i++ ?></td>
                                <td>
                                    <a href="?q=view_expense_details&ex_id=<?php echo $customer_info['acc_head'] ?>&ex_name=<?php echo $customer_info['name'] ?>&dateTo=<?php echo $dateYear ?>&dateFrom=<?php echo $dateMonth ?>">
                                        <?php echo $customer_info['name'] ?>
                                    </a>
                                </td>
                                <td><?php echo number_format($customer_info['payments'], 2, '.', ''); ?></td>
                            </tr>
                        <?php
                        }
                    ?>
                    <tr>
                        <td colspan="2" class="text-center"><b>Total Expense</b></td>
                        <td><b><?php echo number_format($total_expense, 2, '.', '') ?></b></td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>