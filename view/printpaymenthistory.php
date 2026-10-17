<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
//$date        = date('Y-m-d');
$Month = date('m', strtotime($date));
$Year = date('Y', strtotime($date));
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

if (isset($_GET['historyPrint'])) {
    $token = $_GET['historyPrint'];
    $dataForPrint = $obj->view_all_by_cond("vw_account", "agent_id='$token' order by acc_id");

}
?>
<div class="col-md-12"
     style=" background:#606060; margin-top:20px; margin-bottom: 15px; min-height:45px; padding:8px 0px 0px 15px; font-size:16px; font-family:Lucida Sans Unicode; color:#FFFFFF; font-weight:bold;">
    <div class="col-md-6">
        <b>Payment History Print</b>
    </div>
</div>

<div class="row" style="padding:10px; font-size: 12px;">

    <div class="col-md-1 col-md-offset-11">
        <button type="submit" class="btn btn-primary pull-right" onclick="printDiv('client_print')">Print Statement
        </button>
    </div>
    <div class="row" id="client_print">

        <div class="col-md-6">
            <b>Payment History Of <?php
                if (isset($_GET['historyPrint'])) {
                    $Data = $obj->details_by_cond('tbl_agent', 'ag_id = ' . $_GET['historyPrint'] . '');
                    echo $Data['ag_name'];
                }
                elseif(isset($_GET['chhistoryPrint']))
                {
                    $name = $obj->details_by_cond('tbl_agent', 'ag_id = ' . $_GET['chhistoryPrint'] . '');
                    echo $name['ag_name'];
                    $Data = $obj->view_all_by_cond('tbl_bill_amount_change', 'agent_id = ' . $_GET['chhistoryPrint'] . '');
                }
                ?></b>
        </div>
  <div class="col-md-12">
    <?php
    if(isset($_GET['chhistoryPrint']))
    {
        ?>
       <table class="table table-responsive table-bordered table-hover table-striped" id="monthly_tbl">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Agent Name</th>
                    <th class="col-md-1">Bill Amount</th>
                    <th>Previous Bill Amount</th>
                    <th>Change Date</th>
                </tr>
                </thead>

                <?php
                    $i = 0;
                    $totalin = 0;
                    foreach ($Data as $customer_info) {
                        extract($customer_info);
                        $i++;
                        //$totalin += $customer_info['acc_amount'];
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $name['ag_name'] ?></td>
                            <td><?php echo $customer_info['bill_amount'] ?></td>
                            <td style="text-align: right;"><?php echo isset($customer_info['previous_bill_amount']) ? $customer_info['previous_bill_amount'] : NULL; ?></td>
                            <td style="text-align: right;"><?php echo date('d, M Y', strtotime($customer_info['created_at'])); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tbody>

                </table>
                <?php
    }
    ?>
    <?php if(isset($_GET['historyPrint']))
    {
        ?>
            <table class="table table-responsive table-bordered table-hover table-striped" id="monthly_tbl">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th class="col-md-1">Month</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Received By</th>
                </tr>
                </thead>

                <?php
                    $i = 0;
                    $totalin = 0;
                    foreach ($obj->view_all_by_cond("vw_account", "agent_id='$token' order by acc_id") as $customer_info) {
                        extract($customer_info);
                        $i++;
                        $totalin += $customer_info['acc_amount'];
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php if ($customer_info['entry_date']!='0000-00-00 00:00:00'){echo date('h:i A', strtotime($customer_info['entry_date'])).'<br>'.date('d, M Y', strtotime($customer_info['entry_date']));} ?></td>
                            <td><?php echo date("F", strtotime(isset($customer_info['entry_date']) ? $customer_info['entry_date'] : "2016-02-1")); ?></td>
                            <td style="text-align: right;"><?php echo isset($customer_info['acc_description']) ? $customer_info['acc_description'] : NULL; ?></td>
                            <td style="text-align: right;"><?php echo isset($customer_info['acc_amount']) ? $customer_info['acc_amount'] : NULL; ?></td>
                            <td style="text-align: right;"><?php echo isset($customer_info['FullName']) ? $customer_info['FullName'] : NULL; ?>
                                (<?php echo isset($customer_info['UserName']) ? $customer_info['UserName'] : NULL; ?>)
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
            <?php } ?>
            </div>
        </div>
    </div>
    <script>
    $(document).ready(function () {
        //$('#monthly_tbl').dataTable();
    });
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>