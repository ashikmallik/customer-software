<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$notification = "";
//taking month and years
$day = date('M-Y');

$rowCount = $obj->Total_Count("tbl_agent", "ag_status='1' and pay_status='1' AND due_status='0'");

// ========== Full Due Function End =================
?>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<style>

    .progress-label {
        display: none;
    !important;
    }

    .ui-widget-header {
        border: 1px solid #812cea;
        background: #893ce8;
        color: #333333;
        font-weight: bold;
    }

    #datatable_view_all_due_filter input {
        height: 30px;
        border-radius: 5px;
        padding: 5px;
    }

</style>

<div class="row bg-grey-800"
     style="margin-top:10px; margin-bottom: 15px; min-height:45px; padding:8px 0px 0px 15px; font-size:16px; font-family:Lucida Sans Unicode;font-weight:bold;">
    <div class="col-md-12">
        <b>Previous Due Customer List</b>
    </div>
</div>

<?php
$msg = isset($_GET['msg']) ? $_GET['msg'] : null;
if ($msg != null) {
    ?>
    <div class="col-md-12">
        <div class="alert alert-info">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Message: </strong><?php echo $msg ?>
        </div>
    </div>
    <?php
}
?>
<div class="row" style="font-size: 12px;">
    <div class="col-md-12 table-responsive">
        <?php
        if ($ty == 'SA' || $obj->hasPermission($ty, 'total_bill')) {
            ?>
            <h4 class="text-teal-800">Total Bill Amount: <span id="totalBillAmount"></span></h4>
            <?php
        }
        ?>
        <table class="table table-responsive table-bordered table-hover table-striped" id="datatable_view_all_due">
            <thead>
            <tr>
                <th class="col-md-1">SL</th>
                <th class="col-md-2">Customer Name</th>
                <th class="col-md-1">Customer ID</th>
                <th class="col-md-2">Address</th>
                <th class="col-md-1">Mobile No</th>
                <th class="col-md-1">Speed</th>
                <th class="col-md-1">C. Bill</th>
                <th class="col-md-1">Pre. Due</th>
                <th class="col-md-1">Total Due</th>
                <th class="col-md-1">IP</th>

            </tr>
            </thead>
            <tbody>

            </tbody>

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
                setTimeout(progress, <?php echo $rowCount*0.80; ?>);
            }
        }

        setTimeout(progress, 500);

        var table = $('#datatable_view_all_due').DataTable({
            <?php if($obj->hasPermission($ty, 'print_due_payment_client')){ ?>
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'print',
                    text: 'Print Due Payment Client',
                    exportOptions: {
                        columns: [0,1,3,4,5,6,7,8]
                    },
                    title: 'All Due Payment Customer',
                    customize: function ( win ) {
                        $(win.document.body).css( 'font-size', '12px' );
                        $(win.document.body).find( 'h1' ).addClass( 'text-center' ).css( 'font-size', '20px' );
                        $(win.document.body).find( 'table' ).removeClass( 'table-bordered' ).css( 'font-size', 'inherit' );
                    }
                }
            ],
            <?php } ?>
            "processing": false,
            "deferRender": true,
            "ajax": 'view/ajax_action/ajax_view_all_due.php',
            "order": [[ 0, "asc" ]],
            "columns": [
                {"data": 'sl'},
                {"data": 'agent_name'},

                {
                    "data": 'customer_id',
                    render: function (data, type, row) {
                        return '<a href="?q=view_customer_payment_individual&token2=' + row['agent_id'] + '">' + data + '</a>'
                    }
                },

                {"data": 'agent_address'},

                {"data": 'mobile'},

                {"data": 'speed'},

                {"data": 'bill'},
                
                {"data": 'due'},

                { "data": 'total_due' },

                {"data": 'ip'},


            ],

            "initComplete": function (settings, json) {
                $('#progressbar').hide();
                showtotalbillAmount();
            },

        });

        function showtotalbillAmount(){
            $.get("view/ajax_action/ajax_view_all_due.php", {all_due_total_bill:'111'}, function(data, status){

                $('#totalBillAmount').html(data.total_bill + ' Taka');

            }, 'json');


        }
    });

</script>

<script type="application/javascript"
        src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script type="application/javascript"
        src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js "></script>
