<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

$acc_head = isset($_GET['ex_id']) ? $_GET['ex_id'] : NULL;
$acc_head_name = isset($_GET['ex_name']) ? $_GET['ex_name'] : NULL;
$dateTo = isset($_GET['dateTo']) ? $_GET['dateTo'] : NULL;
$dateFrom = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : NULL;


if( $obj->userWorkPermission($acc,'delete')){

    if (isset($_GET['dltoken']) && !empty($_GET['dltoken'])) {
        $dltoken = $_GET['dltoken'];

        if ($obj->Delete_data("tbl_account", "acc_id='$dltoken'")) {

            $obj->notificationStore('Delete Successfull', 'success');
        } else {

            $obj->notificationStore('Sorry ! Delete Failed. Try Again');
        }

        echo '<script> window.location = "?q=view_expense_details&ex_id='.$acc_head.'&ex_name='.$acc_head_name.'&dateTo='.$dateTo.'&dateFrom='.$dateFrom.'"; </script>';
    }
}
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

    <div class="col-md-12 bg-slate-800" style="margin-top:20px; margin-bottom: 15px;">
        <a class="btn btn-primary btn-sm pull-left" href="?q=expense_report" style="margin-top:3px;"><span
                    class="glyphicon glyphicon-chevron-left"></span> Back To expense Report</a>
        <h4 class="text-center">
            <span id="printTitle">All Expense Information of <?php echo $acc_head_name ?></span>
            <button class="btn btn-success btn-sm pull-right" style="margin-top: -5px ;"
                    onclick="printDiv('month_table')">Print
                Statement
            </button>
        </h4>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?php $obj->notificationShowRedirect(); ?>
    </div>
</div>
<div id="month_table" class="row" style="padding:10px; font-size: 12px;">
    <div class="col-md-8 col-md-offset-2">
        <h4 id="printtitle" class="text-center"></h4>
        <table class="table table-bordered table-hover table-striped" id="example4">
            <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Description</th>
                <th class="text-center">Amount</th>
                <th class="text-center">Action</th>
            </tr>
            </thead>
            <?php
            $i = '0';
            $total_expense = 0;
            $condition = "acc_head='$acc_head' AND acc_type='1' AND MONTH(entry_date)='$dateFrom' AND YEAR(entry_date)='$dateTo' ORDER BY entry_date ASC";
            foreach ($obj->view_all_by_cond("vw_account", $condition) as $value) {
                $i++;
                $total_expense += isset($value['acc_amount']) ? $value['acc_amount'] : 0;
                ?>
                <tr>
                    <td class="text-center">
                        <?php echo $i; ?>
                    </td>
                    <td><?php echo date("d-m-Y", strtotime(isset($value['entry_date']) ? $value['entry_date'] : "2016-02-1")); ?></td>
                    <td><?php echo isset($value['acc_description']) ? $value['acc_description'] : NULL; ?></td>
                    <td class="text-right"><?php echo number_format(isset($value['acc_amount']) ? $value['acc_amount'] : 0, 2, '.', ''); ?></td>
                    <td class="text-center">
                        <div class="btn-group">
                            <?php if( $obj->userWorkPermission($acc,'edit')){ ?>
                                <a class="btn btn-xs btn-success"
                                   href="?q=edit_expense&token=<?php echo isset ($value['acc_id']) ? $value['acc_id'] : NULL ?>">
                                    Edit <span class="glyphicon glyphicon-edit"></span>
                                </a>
                            <?php };
                            if( $obj->userWorkPermission($acc,'delete')){ ?>
                                <a onclick="return confirm('Are You Sure To Delete This Expense ?');"
                                   href="?q=view_expense_details&ex_id=<?php echo $acc_head; ?>&ex_name=<?php echo $acc_head_name; ?>&dateTo=<?php echo $dateTo; ?>&dateFrom=<?php echo $dateFrom; ?>&dltoken=<?php echo isset($value['acc_id']) ? $value['acc_id'] : NULL; ?>"
                                   class="btn btn-xs btn-danger">
                                    Delete <span class="glyphicon glyphicon-remove"></span>
                                </a>
                            <?php } ?>
                        </div>
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