<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

$firsDayOfMonth = new DateTime('first day of this month');

if (isset($_POST['search'])) {
    $dateform = date('Y-m-d', strtotime($_POST['dateform']));
    $dateto = date('Y-m-d', strtotime($_POST['dateto']));
    $allOtherIncome = $obj->view_selected_field_by_cond("vw_account", "entry_date, acc_id, acc_name, acc_head, SUM(acc_amount) as acc_amount", "entry_date BETWEEN '" . date('Y-m-d', strtotime($dateform)) . "' and '" . date('Y-m-d', strtotime($dateto)) . "' AND acc_type='2' GROUP BY `acc_head` ORDER BY `vw_account`.`entry_date` ASC");

} else {

    $dateform = $firsDayOfMonth->format('Y-m-d');;
    $dateto = date('Y-m-d');

    $allOtherIncome = $obj->view_selected_field_by_cond("vw_account", 'entry_date, acc_id, acc_name, acc_head, SUM(acc_amount) as acc_amount', "MONTH(entry_date)='" . date('m') . "' and YEAR(entry_date)='" . date('Y') . "' AND acc_type='2' GROUP BY `acc_head` ORDER BY `vw_account`.`entry_date` ASC");
}
$previewDate = date('d M Y', strtotime($dateform)) . ' to ' . date('d M Y', strtotime($dateto));

if( $obj->userWorkPermission($acc,'delete')){

    if (isset($_GET['dltoken']) && !empty($_GET['dltoken'])) {
        $dltoken = $_GET['dltoken'];

        if ($obj->Delete_data("tbl_account", "acc_id='$dltoken'")) {

            $obj->notificationStore('Delete Successfull', 'success');
        } else {

            $obj->notificationStore('Sorry ! Delete Failed. Try Again');
        }

        echo '<script> window.location = "?q=view_income"; </script>';
    }
}

?>
<style> a {
        text-decoration: none;
    !important;
    }</style>


<div class="row" >

    <div class="col-md-10 bg-teal-800">
        <h4><b>View Others Income for <?php echo $previewDate; ?></b></h4>
    </div>
    <div class="col-md-2">
        <?php if ($ty == 'SA' OR $ty == 'EO') { ?>
            <a href="?q=add_income" class="btn btn-sm btn-primary" style="margin-top:5px;"><span
                        class="glyphicon glyphicon-plus">&nbsp;Add New</a>
        <?php } ?>
    </div>

</div>
<hr>
<div class="row">
    <div class="col-md-12">
        <?php $obj->notificationShowRedirect(); ?>
    </div>
</div>

<div class="row bg-teal-800" style="margin-bottom: 20px;padding:5px 0px">
    <form action="" method="POST">
        <div class="col-md-5">
            <div class="form-group">
                <label class="control-label col-sm-4" style="padding-top:5px" for="dateMonth">Form Date</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control datepicker"
                           value="<?php echo date('d-m-Y', strtotime($dateform)); ?>" placeholder="Date" name="dateform"
                           id="new_flight_date" required>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label class="control-label col-sm-4" style="padding-top:5px" for="damageRate">To Date</label>
                <div class="col-sm-8">
                    <input style="color: black;" id="old_flight_date"
                           value="<?php echo date('d-m-Y', strtotime($dateto)); ?>" type="text"
                           class="form-control datepicker"
                           placeholder="Date" name="dateto" required>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <input type="submit" name="search" class="btn btn-primary" value="Search">
        </div>
    </form>
</div>
<div class="row" style="padding:10px; font-size: 12px;">
    <div class="col-md-12">
        <div class="table-responsive">

            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Head</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
                </thead>
                <?php
                $i = '0';
                $totalAmount = 0;
                foreach ($allOtherIncome as $otherIncome) {
                    $i++;

                    $amount = isset($otherIncome['acc_amount']) ? $otherIncome['acc_amount'] : 0;

                    if ($amount == 0) {
                        continue;
                    }

                    $totalAmount += $amount;
                    ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo date("d-m-Y", strtotime(isset($otherIncome['entry_date']) ? $otherIncome['entry_date'] : "2016-02-1")); ?></td>
                        <td><?php
                            if (isset($otherIncome['acc_name']) && !empty($otherIncome['acc_name'])) {

                                echo '<a href="?q=view_other_income_details&ex_id=' . $otherIncome['acc_head'] . '&ex_name=' . $otherIncome['acc_name'] . '&dateFrom=' . $dateform . '&dateTo=' . $dateto . '&action=income" ><b>' . $otherIncome['acc_name'] . '</b></a>';

                            } else {

                                echo '<a href="?q=view_other_income_details&ex_id=' . $otherIncome['acc_head'] . '&ex_name=' . $otherIncome['acc_name'] . '&dateFrom=' . $dateform . '&dateTo=' . $dateto . '&action=income" ><b>Opening or Other</b></a>';
                            } ?>
                        </td>
                        <td><?php echo $amount ?></td>
                        <td class="text-center">
                            <?php echo '<a class="btn btn-xs btn-primary" href="?q=view_other_income_details&ex_id=' . $otherIncome['acc_head'] . '&ex_name=' . $otherIncome['acc_name'] . '&dateFrom=' . $dateform . '&dateTo=' . $dateto . '&action=income" ><b>View Details</b></a>'; ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr class="bg-info">
                    <th colspan="3" class="text-center"><b>Total Other Income</b></th>
                    <th colspan="2"><?php echo $totalAmount; ?> Taka</th>
                </tr>
                <tr class="bg-warning">
                    <th colspan="5" class="text-center"><b>In Word
                            : <?php echo $formater->convert_number_to_words($totalAmount) ?></b></th>
                </tr>

            </table>
        </div>
    </div>
</div>

<script>

    $(document).ready(function () {
        $('input[name="dateform"]').datepicker({
            autoclose: true,
            toggleActive: true,
            format: 'dd-mm-yyyy'
        });

        $('input[name="dateto"]').datepicker({
            autoclose: true,
            toggleActive: true,
            format: 'dd-mm-yyyy'
        });
    });
    function printDiv(divName) {
        var title = $('#showDate').html();
        $('#printtitle').html(title);
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        $('h4#printtitle').html('');
    }

</script>