<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;


$acc_head = isset($_GET['ex_id']) ? $_GET['ex_id'] : NULL;
$dateTo = isset($_GET['dateTo']) ? $_GET['dateTo'] : NULL;
$dateFrom = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : NULL;

$condition = "acc_head='$acc_head' AND acc_type='2' AND entry_date BETWEEN '" . date('Y-m-d', strtotime($dateFrom)) . "' AND '" . date('Y-m-d', strtotime($dateTo)) . "' ORDER BY entry_date ASC";

$otherIncomeDetails = $obj->view_all_by_cond("vw_account", $condition);

?>
<script>

    function printDiv(divName) {
        var title = $('#printTitle').html();
        $('#printtitle').html(title);
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        $('h4#printtitle').html('');
    }
</script>

<div class="row">

    <div class="col-md-12 bg-teal-800" style="margin-top:20px; margin-bottom: 15px;">
        <a class="btn btn-primary btn-sm pull-left" href="?q=view_income" style="margin-top:3px;"><span
                    class="glyphicon glyphicon-chevron-left"></span> Back To Other Income Report</a>
        <h4 class="text-center">
            <span id="printTitle">All Other Income Information of <?php echo isset($_GET['ex_name']) ? $_GET['ex_name'] : NULL ?></span>
            <button class="btn btn-success btn-sm pull-right" style="margin-top: -5px ;"
                    onclick="printDiv('month_table')">Print
                Statement
            </button>
        </h4>
    </div>
    <div class="col-md-1 col-md-offset-10">

    </div>
</div>
<div id="month_table" class="row" style="padding:10px; font-size: 12px;">
    <div class="col-md-10 col-md-offset-1">
        <h4 id="printtitle" class="text-center"></h4>
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th class="col-md-1">#</th>
                <th class="col-md-2">Date</th>
                <th class="col-md-6">Description</th>
                <th class="col-md-1 text-center">Amount</th>
                <th class="col-md-2 text-center">Action</th>
            </tr>
            </thead>
            <?php
            $i = '0';

            $total_expense = 0;

            foreach ($otherIncomeDetails as $otherIncome) {
                $i++;
                $total_expense += isset($otherIncome['acc_amount']) ? $otherIncome['acc_amount'] : 0;
                ?>
                <tr>
                    <td class="text-center"> <?php echo $i; ?> </td>

                    <td><?php echo date("d-m-Y", strtotime(isset($otherIncome['entry_date']) ? $otherIncome['entry_date'] : "2016-02-1")); ?></td>

                    <td><?php echo isset($otherIncome['acc_description']) ? $otherIncome['acc_description'] : NULL; ?></td>

                    <td class="text-right"><?php echo number_format(isset($otherIncome['acc_amount']) ? $otherIncome['acc_amount'] : 0, 2, '.', ''); ?></td>
                    <td>
                        <?php if( $obj->userWorkPermission($acc,'edit')){ ?>
                            <a class="btn btn-xs btn-primary" href="?q=edit_income&token=<?php echo $otherIncome['acc_id'] ?>">
                                Edit <span class="glyphicon glyphicon-edit"</span>
                            </a>
                        <?php }
                        if( $obj->userWorkPermission($acc,'delete')){ ?>
                            <a onclick="return confirm('Are You Sure Delete Income Info')" href="?q=view_income&dltoken=<?php echo $otherIncome['acc_id'] ?>" class="btn btn-xs btn-danger" >
                                Delete <span class="glyphicon glyphicon-remove"></span>
                            </a>
                        <?php } ?>
                    </td>
                </tr>
                <?php
            }
            ?>  
            <tr style='height:33px;'>
                <th colspan="3" class="text-center"><b>Total Expense</b></th>
                <th class="text-right"><?php echo number_format($total_expense, 2, '.', '') ?></th>
            </tr>
        </table>
    </div>
</div>