<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

$firsDayOfMonth = new DateTime('first day of this month');

if (isset($_POST['searchSubmit'])) {

    $dateform = date('Y-m-d', strtotime($_POST['dateform']));
    $dateto = date('Y-m-d', strtotime($_POST['dateto']));
    $user = $_POST['user'];

    if( $user == '0'){ // if user id not given query not search in the user only date
        $userSearch = '';

    }else{

        $userSearch = " and UserName='".$_POST['user']."'";
    }

    $accountData = $obj->view_all_by_cond("vw_account", "entry_date BETWEEN '" . date('Y-m-d', strtotime($dateform)) . "' and '" . date('Y-m-d', strtotime($dateto)) . "' ".$userSearch." ORDER BY `vw_account`.`entry_date` ASC");

} else {

    $dateform =$firsDayOfMonth->format('Y-m-d');;
    $dateto = date('Y-m-d');

    $accountData = $obj->view_all_by_cond("vw_account", "MONTH(entry_date)='" . date('m') . "' and YEAR(entry_date)='" . date('Y') . "' ORDER BY `vw_account`.`entry_date` ASC");
}
?>

<style>
    .pointer{
        cursor: pointer;

    }
    .pointer:hover{
        text-decoration:underline;
        -webkit-text-decoration-color: #83aff7; /* Safari */
        text-decoration-color: #83aff7;
    }
</style>
<div class="row">

    <div class="col-md-12 bg-teal-800" style="margin-top:20px; margin-bottom: 15px;">
        <div class="col-md-8">

            <?php if (isset($_POST['searchSubmit'])) {

                echo '<h4 style="text-align:center;"><strong><span id="showDate">The Accounting Statement Between ' . date('d-M-y', strtotime($_POST['dateform'])) . ' to ' . date('d-M-y', strtotime($_POST['dateto'])) . '</span></strong></h4>';

            } else {

                echo '<h4><strong><span id="showDate">The Accounting Statement of ' . date('M Y') . '</span></strong></h4>';

            }
            ?>
        </div>
        <div class="col-md-2">
            <a class="btn btn-success btn-sm pull-left" href="?q=daily_acc_statement" style="margin-top:3px;">Daily Accounting Statement</a>
        </div>
        <?php if($obj->hasPermission($ty, 'print_statement')){ ?>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary btn-sm pull-right" style="margin-top:3px;" onclick="printDiv('month_print')">Print Statement
            </button>
        </div>
         <?php } ?>

    </div>
    <form action="" method="POST" class="form-horizontal">
        <div id="select_date" class="col-md-12 bg-teal-800" style="margin-bottom: 20px;padding:5px 0px">
            <div class="col-md-1">
                <label class="control-label" style="padding-top:5px" for="dateMonth">Form : </label>
            </div>
            <div class="col-md-3">
                <input class="form-control" value="<?php echo date('d-m-Y', strtotime($dateform)); ?>" type="text"
                       placeholder="Select Date" name="dateform" required="required">
            </div>
            <div class="col-md-1">
                <label class="control-label pull-right" style="padding-top:5px" for="dateMonth">To : </label>
            </div>
            <div class="col-md-3">
                <input class="form-control" required="required" value="<?php echo date('d-m-Y', strtotime($dateto)) ?>" type="text"
                       name="dateto" placeholder="Select Date">
            </div>
            <div class="col-md-2">
                <select class="form-control" type="text" placeholder="User ID" required="required" name="user">
                        <option value="0">Select User</option>
                    <?php
                    foreach ($obj->view_all("_createuser") as $user_name) {
                        extract($user_name);
                        ?>
                        <option value="<?php echo isset($user_name['UserName']) ? $user_name['UserName'] : NULL; ?>"><?php echo isset($user_name['UserName']) ? $user_name['UserName'] : NULL; ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-2 text-center">
                <input type="submit" class="btn btn-success" name="searchSubmit" value="Search">
            </div>
        </div>
    </form>
</div>


<div class="row" style="padding:5px; font-size: 12px;">
    <div class="row" id="month_print">

        <div class="col-md-12">
            <h4 id="printtitle" class="text-center"></h4>
            <table class="table table-responsive table-bordered table-striped" id="monthly_tbl">
                <thead>
                <tr>
                    <th class="col-md-1">Sl</th>
                    <th class="col-md-2">Date</th>
                    <th class="col-md-3 text-center">Particular</th>
                    <th class="col-md-1 text-center">Client</th>
                    <th class="col-md-1 text-center">Ip</th>
                    <th class="col-md-2 text-center">User</th>
                    <th class="col-md-1 text-right">Credit</th>
                    <th class="col-md-1 text-right">Debit</th>
                    <th class="col-md-1 text-right">Balance</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = 0;
                $totalin = 0;
                $balance = 0;
                $debit_total = 0;
                $credit_total = 0;
                foreach ($accountData as $acount) {
                    extract($acount);
                    $i++;
                    $totalin += $acount['acc_amount'];

					$cus_id = isset($acount['agent_id']) ? $acount['agent_id'] : 0;
                    if($cus_id != 0){
                        $agentDetails = $obj->details_by_cond("tbl_agent", "ag_id=$cus_id");
                        if($agentDetails){
                            $customerName = $agentDetails['ag_name'];
                            $customerIp = $agentDetails['ip'];
                            $pointerClass = 'pointer';
                        }else{
                            $customerName = '--';
                            $customerIp = '--';
                            $pointerClass = '';
                        }
                    }else{
                        $customerName = '--';
                        $customerIp = '--';
                        $pointerClass = '';
                    }


                    ?>
                    <tr id="<?php echo $cus_id;?>" class="<?php echo  $pointerClass;?>">
                        <td><?php echo $i; ?></td>
                        <td><?php echo date("d-m-Y", strtotime(isset($acount['entry_date']) ? $acount['entry_date'] : "2016-02-1")); ?></td>
                        <td class="text-right"><?php echo isset($acount['acc_description']) ? $acount['acc_description'] : NULL; ?></td>
                        <td class="text-center"><?php echo $customerName ?></td>
                        <td class="text-center"><?php echo $customerIp ?></td>
                        <td style="text-align: right;"><?php echo isset($acount['FullName']) ? $acount['FullName'] : NULL; ?></td>
                        <?php
                        $get_acc_type = isset($acount['acc_type']) ? $acount['acc_type'] : NULL;
                        $amount = isset($acount['acc_amount']) ? $acount['acc_amount'] : NULL;
                        $debit = 0;
                        $credit = 0;

                        if ($get_acc_type == 1) {
                            $debit = $amount;
                            $balance -= $debit;
                            $debit_total += $debit;
                        } else {
                            $credit = $amount;
                            $balance += $credit;
                            $credit_total += $credit;
                        }

                        ?>
                        <td style="text-align: right;"><?php echo $credit; ?></td>
                        <td style="text-align: right;"><?php echo $debit; ?></td>
                        <td style="text-align: right;"><?php echo $balance; ?></td>

                    </tr>
                    <?php
                }
                ?>

                <?php
                $word = $formater->convert_number_to_words($balance);
                ?>

                </tr>
                </tbody>
                <tfoot>
                <tr style="font-size:14px;">
                    <td colspan="6" class="text-center"><b>Total</b></td>
                    <td class="text-center"><b><?php echo $credit_total; ?></b></td>
                    <td class="text-center"><b><?php echo $debit_total; ?></b></td>
                    <td class="text-center"><b><?php echo $credit_total - $debit_total; ?></b></td>
                </tr>
                <tr>
                    <th class="col-md-1">Sl</th>
                    <th class="col-md-2">Date</th>
                    <th class="col-md-3 text-center">Particular</th>
                    <th class="col-md-1 text-center">Client</th>
                    <th class="col-md-1 text-center">Ip</th>
                    <th class="col-md-2 text-center">User</th>
                    <th class="col-md-1 text-right">Credit</th>
                    <th class="col-md-1 text-right">Debit</th>
                    <th class="col-md-1 text-right">Balance</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

<div class="row">

    <div class="col-md-12 bg-slate-800">
        <h4 class="text-center">
            <strong><small>Total Balance : </small><?php echo $balance; ?> Taka</strong>
        </h4>
    </div>
    <div class="col-md-12 bg-grey-800">
        <h4 class="text-center">
            <strong><small>Balance Ammount In Word :  </small><?php echo $word; ?></strong>
        </h4>
    </div>

</div>

<hr>

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

    $(document).ready(function () {
        $("tbody tr").on('click', function () {
            var id = this.id;
            if (id != "0") { window.location = "?q=view_customer_payment_individual&token2=" + id; }
        });
    });
</script>
<!-- here end table -->

	

