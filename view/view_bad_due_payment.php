<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$notification = "";
//taking month and years
$day = date('M-Y');

$rowCount = $obj->Total_Count("vw_agent", "ag_status='1' and pay_status='1' AND due_status='0'");

?>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<!--<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>-->
<script src="asset/js/jquery-ui.min.js"></script>
<style>

    .ui-progressbar {
        position: relative;
    }

    .progress-label {
        text-align: center;
        position: absolute;
        left: 50%;
        top: 4px;
        font-weight: bold;
        /*text-shadow: 1px 1px 0 #fff;*/
    }

    .ui-widget-header {
        border: 1px solid #2f7d2f;
        background: #5cb85c;
        color: #333333;
        font-weight: bold;
    }

    #datatable_view_due_filter input {
        height: 30px;
        border-radius: 5px;
        padding: 5px;
    }

</style>

<div class="row">
    <div class="col-md-12 bg-grey-800"
         style="margin-top:10px; margin-bottom: 15px; min-height:45px; padding:8px 0px 0px 15px; font-size:16px; font-family:Lucida Sans Unicode; font-weight:bold;">
        <div class="row">
            <div class="col-md-4">
                <b>View Customer Bad Collection</b>
            </div>

        </div>
    </div>
</div>
<?php if($obj->hasPermission($ty, 'show_due_bill')){ ?>
    <div class="col-md-4" style="margin-left: 353px;">
       <span id="totalZoneWiseBill" class="label bg-slate-800" style="font-size: 13px;"></span>
       
    </div>
   <?php } ?>
<?php
$msg = isset($_GET['msg']) ? $_GET['msg'] : null;
if ($msg != null) { ?>
    <div class="col-md-12">
        <div class="alert alert-info">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Message: </strong><?= $msg ?>
        </div>
    </div>
    <?php } ?>
<div class="row" style="font-size:12px">
    <div class="col-md-12 table-responsive">
        <table class="table table-responsive table-bordered table-hover table-striped" id="datatable_view_due">
            <thead>
            <tr class="bg-teal-800">
                <th class="col-md-1">Customer Name</th>
                <th class="col-md-1">Customer ID</th>
                <th class="col-md-1">Address</th>
                <th class="col-md-1">Zone</th>
                <th class="col-md-1">Mobile No</th>
                <th class="col-md-1">Speed</th>
                <th class="col-md-1">C. Date</th>
                <th class="col-md-1">Due<br>Amount</th>
                <th class="col-md-1">IP</th>
                <th class="d col-md-1">Action</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
        <div class="progress" id="progressbar">
            <div class="progress-label">Calculating Your Bill Data...</div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {

        var progressbar = $("#progressbar");
        var progressLabel = $(".progress-label");

        progressbar.progressbar({

            value: false,

            change: function () {

                progressLabel.text(progressbar.progressbar("value") + "%");
            },
            complete: function () {

            }
        });

        function progress() {

            var val = progressbar.progressbar("value") || 0;

            progressbar.progressbar("value", val + 2);

            if (val < 99) {

                setTimeout(progress, <?php echo $rowCount * 0.1; ?>);
            }
        }

        setTimeout(progress, 500);


        $('#datatable_view_due').on('click', 'tbody td div.btn-group a.paid', function (e) {

            e.preventDefault();
            $(this).removeClass('btn-primary');
            $(this).addClass('disabled btn-default');
            var agentName = $(this).data('name');
            var amountTaka = $(this).data('amount');

            url = $(this).attr('href');

            $.get(url, function (data, status) {

                if (status == 'success') {
                    alert('The Bill of ' + amountTaka + 'tk has been paid for ' + agentName);
                } else {
                    alert('Sorry can\'t add the bill info. Error occurred');
                }

            });
        });


        $('#datatable_view_due').on('click', 'tbody td button.sms', function (e) {

            var agentId = $(this).data('agent');

            var paidButton = $(this).closest('tr').find('td div.btn-group a.paid');

            $(this).removeClass('btn-primary');
            $(this).addClass('disabled btn-default');

            $.get("view/ajax_action/send_custom_sms.php", { agent: agentId }, function (result) {

                alert('An SMS sent to ' + paidButton.data('name') + ' As Your Custom SMS');

            });


        });


        // show total bill amount above table
        function showZoneWiseBill(bill) {

            $('#totalZoneWiseBill').html('For This Clients Total Amount : ' + bill.toLocaleString() + ' Taka');

        }


        var table = $('#datatable_view_due').DataTable({
            "processing": false,
            "initComplete": function (settings, json) {
                $('#progressbar').hide();
            },
            "deferRender": true,
            "ajax": 'view/ajax_action/ajax_view_bad_due_payment.php',
            "order": [[0, 'asc']],
            "columns": [

                {"data": 'agent_name'},

                {
                    "data": 'customer_id',
                    render: function (data, type, row) {
                        return '<a href="?q=view_customer_payment_individual&token2=' + row['agent_id'] + '">' + data + '</a>'
                    }
                },

                {"data": 'agent_address'},

                {
                	"data": 'zone',
                	render: function (data, type, row) {
                        	return '<small>' + data + '</small>'
                    	}
                },

                {"data": 'mobile'},

                {"data": 'speed'},

                {"data": 'connection_date'},

                {
                    "data": 'total_due',
                    render: function (data, type, row) {
                        if (row['previous_due'] > 0) {
                            return '<span class="text-danger" style="font-weight:800; font-size:12px">' + data + '</span>'
                        } else {
                            return '<span>' + data + '</span>'
                        }

                    }
                },

                {"data": 'ip'},
                {
                    "data": 'agent_id',
                    "render": function (data, type, row, meta) {
                        return '<div class="btn-group"><a href="?q=add_bad_payment&token1=' + data + '" style="padding:5px" class="btn btn-success btn-sm "> Payment </a></div>';
                    }
                },

            ],
            "fnDrawCallback": function (oSettings) {

                if (oSettings.json) {
                    var loadData = oSettings.json;
                    showZoneWiseBill(loadData.total_bill);
                }
            },

        });

        $('select[name="zone"]').on('change', function () {

            var zoneId = $(this).val();

            if (zoneId != 'x') {

                table.ajax.url('view/ajax_action/ajax_view_due_payment.php?zone=' + zoneId).load();
                $('a#print_link').attr('href', './pdf/index.php?zonePrint=' + zoneId);
                $('a#print_client_bill').attr('href', '?q=print_client_bill&zonePrint=' + zoneId);

            } else {

                table.ajax.url('view/ajax_action/ajax_view_due_payment.php').load();
                $('a#print_link').attr('href', '?q=view_report_paganition&flag=INVOICE');
                $('a#print_client_bill').attr('href', '?q=print_client_bill');
            }
        });

        $('select[name="billingperson"]').on('change', function () {

            var bid = $(this).val();

            if (bid != 'x') {

                table.ajax.url('view/ajax_action/ajax_view_due_payment.php?bid=' + bid).load();
                $('a#print_link').attr('href', './pdf/index.php?bid=' + bid);
                $('a#print_client_bill').attr('href', '?q=print_client_bill&bid=' + bid);

            } else {

                table.ajax.url('view/ajax_action/ajax_view_due_payment.php').load();
                $('a#print_link').attr('href', '?q=view_report_paganition&flag=INVOICE');
                $('a#print_client_bill').attr('href', '?q=print_client_bill');
            }
        });

    });
</script>