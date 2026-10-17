<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
//taking month and years
$day = date('M-Y');
$notification = "";
//check_bill_create_or_not
$cnt = $obj->Total_Count("monthly_bill_making_check", "month_year='$day'");
$rowCount = $obj->Total_Count("tbl_agent", "ag_status='1' AND pay_status='0'");
if ($cnt != 1) {
    ?>
    <script> window.location = "?q=view_due_payment"; </script>
    <?php
}

?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<style>

    .progress-label {
        display: none !important;
    }

    .ui-widget-header {
        border: 1px solid #2776f4;
        background: #397fef;
        color: #333333;
        font-weight: bold;
    }

    #datatable_view_paid_filter input {
        height: 30px;
        border-radius: 5px;
        padding: 5px;
    }

</style>

<div class="row">
    <div class="bg-grey-800 col-md-12">
        <h4>ALL Paid Customer List  (No Due)</h4>
    </div>
</div>

<div class="col-md-12"
     style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
    <h3><?php echo isset($notification) ? $notification : NULL; ?></h3>
</div>


<?php
$msg = isset($_GET['msg']) ? $_GET['msg'] : null;
if ($msg != null) {
    ?>
    <div class="col-md-12">
        <div class="alert alert-info">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Message: </strong><?= $msg ?>
        </div>
    </div>
    <?php
}
?>
<div class="row" style="font-size: 12px;">
    <div class="col-md-12 table-responsive">
        <table class="table table-responsive table-bordered table-hover table-striped" id="datatable_view_paid">
            <thead>
            <tr>

                <th class="col-md-1">Customer Name</th>
                <th class="col-md-1">Customer ID</th>
                <th class="col-md-1">Email</th>
                <th class="col-md-1">Address</th>
                <th class="col-md-1">Mobile No</th>
                <th class="col-md-1">Speed</th>
                <th class="col-md-1">Bill</th>
                <th class="col-md-1">IP</th>

            </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div class="progress" id="progressbar">
            <div class="progress-label">Calculating...</div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {

        var progressbar = $("#progressbar");

        progressbar.progressbar({
            value: false,

            change: function () {

                progressbar.progressbar("value") + "%";
            },
            complete: function () {

            }
        });

        function progress() {
            var val = progressbar.progressbar("value") || 0;
            progressbar.progressbar("value", val + 2);
            if (val < 99) {
                setTimeout(progress, <?php echo $rowCount/10; ?>);
            }
        }

        setTimeout(progress, 500);

        var table = $('#datatable_view_paid').DataTable({

            "processing": false,
            "initComplete": function (settings, json) {
                $('#progressbar').hide();
            },
//            "serverSide": true,
            "deferRender": true,
            "ajax": 'view/ajax_action/ajax_view_all_paid_client.php',
            "columns": [
                {"data": 'agent_name'},

                {
                    "data": 'customer_id',
                    render: function (data, type, row) {
                        return '<a href="?q=view_customer_payment_individual&token2=' + row['agent_id'] + '">' + data + '</a>'
                    }
                },

                {"data": 'agent_email'},

                {"data": 'agent_address'},

                {"data": 'mobile'},

                {"data": 'speed'},

                {"data": 'bill'},

                {"data": 'ip'},


            ],

        });
    });

</script>