<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

$firsDayOfMonth = new DateTime('first day of this month');

$expenseType = 1;
$otherIncomeType = 2;
$billcollectionType = 3;
$connectionChargeType = 4;


if (isset($_GET['day']) && !empty($_GET['day'])) {

    if (isset($_GET['action']) && !empty($_GET['action'])) {
        $action = $_GET['action'];

        if ($action == 'bill') {

            $accountData = $obj->view_all_by_cond("vw_account", "entry_date='" . date($_GET['day']) . "' AND acc_type = " . $billcollectionType . " ORDER BY `vw_account`.`entry_date` ASC");
            $previewText = "Only Billing Information";

        } elseif ($action == 'con') {

            $accountData = $obj->view_all_by_cond("vw_account", "entry_date='" . date($_GET['day']) . "' AND acc_type = " . $connectionChargeType . " ORDER BY `vw_account`.`entry_date` ASC");
            $previewText = "Only Connection Charge";

        } elseif ($action == 'other') {

            $accountData = $obj->view_all_by_cond("vw_account", "entry_date='" . date($_GET['day']) . "' AND acc_type = " . $otherIncomeType . " ORDER BY `vw_account`.`entry_date` ASC");
            $previewText = "Only Other Income";

        } else {

            $accountData = $obj->view_all_by_cond("vw_account", "entry_date='" . date($_GET['day']) . "' ORDER BY `vw_account`.`entry_date` ASC");
            $previewText = "";

        }
    }
    $previewDay = $_GET['day'];

} else {


    $accountData = $obj->view_all_by_cond("vw_account", "entry_date='" . date('Y-m-d') . "' ORDER BY `vw_account`.`entry_date` ASC");
    $previewDay = date('Y-m-d');
    $previewText = "";
}
?>
<style>
    .pointer {
        cursor: pointer;

    }

    .pointer:hover {
        text-decoration: underline;
        -webkit-text-decoration-color: #83aff7; /* Safari */
        text-decoration-color: #83aff7;
    }
</style>
<div class="row">

    <div class="col-md-12 bg-teal-800" style="margin-top:20px; margin-bottom: 15px;">
        <div class="col-md-10">
            <h4><strong>The Accounting Statement of <span
                            id="showDate"><?php echo date('d M Y', strtotime($previewDay)); ?></span> </strong>
                <small style="color:#fff;"><?php echo $previewText; ?> </small>
            </h4>
        </div>
        <div class="col-md-2">
            <a class="btn btn-success btn-sm pull-right" href="?q=acc_statement" style="margin-top:3px;">Accounting
                Statement</a>
        </div>
    </div>

    <div id="select_date" class="col-md-12 bg-teal-800" style="margin-bottom: 20px;padding:5px 0px">
        <div class="col-md-2">
            <label class="control-label" style="padding-top:5px" for="dateMonth">Select Date : </label>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary btn-sm pull-right" id="previous_date"><span
                        class="glyphicon glyphicon-chevron-left"></span> Previous Date
            </button>
        </div>
        <div class="col-md-4">
            <input class="form-control" required="required" value="<?php echo date('m-d-Y', strtotime($previewDay)); ?>"
                   type="text" name="date"
                   placeholder="Select Date">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary btn-sm pull-left" id="next_date">Next Date <span
                        class="glyphicon glyphicon-chevron-right"></span></button>
        </div>
        <div class="col-md-2">

        </div>
    </div>
</div>

<div class="row" style="padding:5px; font-size: 12px;">
    <div class="row" id="month_print">

        <div class="col-md-12">
            <h4 id="printtitle" class="text-center"></h4>
            <table class="table table-responsive table-bordered table-striped" id="ajax_table_load">
                <thead>
                <tr>
                    <th class="col-md-1">Sl</th>
                    <th class="col-md-2">Date</th>
                    <th class="col-md-3 text-center">Particular</th>
                    <th class="col-md-1 text-center">Client</th>
                    <th class="col-md-1 text-center">IP</th>
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

                    if ($cus_id != 0) {
                        $agentDetails = $obj->details_by_cond("tbl_agent", "ag_id=$cus_id");
                        if ($agentDetails) {
                            $customerName = $agentDetails['ag_name'];
                            $customerIp = $agentDetails['ip'];
                            $pointerClass = 'pointer';
                            $linkStart = '<a href="?q=view_customer_payment_individual&token2=' . $cus_id . '">';
                            $linkEnd = '</a>';
                        } else {
                            $customerName = '--';
                            $customerIp = '--';
                            $pointerClass = '';
                            $linkStart = '';
                            $linkEnd = '';
                        }
                    } else {
                        $customerName = '--';
                        $customerIp = '--';
                        $pointerClass = '';
                        $linkStart = '';
                        $linkEnd = '';
                    }

                    ?>
                    <tr id="<?php echo $cus_id; ?>" class="<?php echo $pointerClass; ?>">
                        <td><?php echo $i; ?></td>
                        <td><?php echo date("d-m-Y", strtotime(isset($acount['entry_date']) ? $acount['entry_date'] : "2016-02-1")); ?></td>
                        <td class="text-right"><?php echo $linkStart; ?><?php echo isset($acount['acc_description']) ? $acount['acc_description'] : NULL; ?><?php echo $linkEnd; ?></td>
                        <td class="text-center"><?php echo $linkStart; ?><?php echo $customerName . $linkEnd; ?></td>
                        <td class="text-center"><?php echo $linkStart; ?><?php echo $customerIp . $linkEnd; ?></td>
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
                <tr style="font-size:14px;">
                    <td colspan="6" class="text-center"><b>Total</b></td>
                    <td class="text-center"><b><?php echo $credit_total; ?></b></td>
                    <td class="text-center"><b><?php echo $debit_total; ?></b></td>
                    <td class="text-center"><b><?php echo $credit_total - $debit_total; ?></b></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<hr>

<script>

    function formateDate(dateObject) {
        var monthNames = [
            "Jan", "Feb", "March",
            "April", "May", "June", "July",
            "Aug", "Sept", "Oct",
            "Nov", "Dec"
        ];

        return (dateObject.getDate() + " " + monthNames[(dateObject.getMonth())] + " " + dateObject.getFullYear());

    };
    $(document).ready(function () {
        $('input[name="date"]').datepicker({
            autoclose: true,
            toggleActive: true,
            format: 'mm-dd-yyyy'
        });

        $('input[name="date"]').on('change', function () {
            var date = $(this).val().replace(/-/g, '/');// the replace(/-/g,'/') code is given to support firefox date() function
            if (date) {
                var preDateobject = new Date(date);
                getDataFromDate(preDateobject);
                showDateInHeader(preDateobject);
            }

        });

        $('button#previous_date').on('click', function () {

            var oldPresentDate = $('#select_date input[name="date"]').val().replace(/-/g, '/');// the replace(/-/g,'/') code is given to support firefox date() function

            var dateObject = new Date(oldPresentDate);

            dateObject.setDate(dateObject.getDate() - 1); // set (or replace) the dateObject on day plus (next day)

            var previousDate = ((dateObject.getMonth() + 1) + "-" + dateObject.getDate() + "-" + dateObject.getFullYear());
            // formate the previous date for ajax work

            $('#select_date input[name="date"]').val(previousDate);
            getDataFromDate(dateObject);
            showDateInHeader(dateObject);

        });

        $('button#next_date').on('click', function () {

            var oldPresentDate = $('#select_date input[name="date"]').val().replace(/-/g, '/');
            ;// the replace(/-/g,'/') code is given to support firefox date() function
            var dateObject = new Date(oldPresentDate);

            dateObject.setDate(dateObject.getDate() + 1); // set (or replace) the dateObject on day plus (next day)

            var nextDate = ((dateObject.getMonth() + 1) + "-" + dateObject.getDate() + "-" + dateObject.getFullYear());
            // formate the previous date for ajax work

            $('#select_date input[name="date"]').val(nextDate)
            getDataFromDate(dateObject);
            showDateInHeader(dateObject);

        });


        function showDateInHeader(dateObject) {

            $('#showDate').html(formateDate(dateObject));
        }


        function getDataFromDate(preDateobject) {

            var formateDate = (preDateobject.getFullYear() + '-' + (preDateobject.getMonth() + 1) + "-" + preDateobject.getDate());

            $.get('view/ajax_action/ajax_daily_acc_statement.php', {date: formateDate}, function (data) {
                $('table#ajax_table_load tbody').html(data);
            })
        }


    });

    function printDiv(divName) {

        dateObject = new Date($('input[name="date"]').val());

        $('h4#printtitle').html('Print Account Statement for date: ' + formateDate(dateObject));
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        $('h4#printtitle').html('');
    }
    

</script>
<!-- here end table -->



