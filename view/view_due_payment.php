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
                <b>View Customer Billing Information <?php echo date('M'); ?></b>
            </div>
            <?php if ($ty == 'SA') { ?>
                <div class="col-md-4" style="font-family: Helvetica;">
                    <div class="text-center">
                        <p> <b><span id="billAmount"> </b></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div style="float:right; padding-right:10px">
                        <a id="print_client_bill" class="btn btn-primary btn-sm"
                           href="?q=print_client_bill">Print
                            Client Bill <span class="glyphicon glyphicon-print"></span></a>
                        <a class="btn btn-primary btn-sm" href="?q=add_agent">Add New <span
                                    class="glyphicon glyphicon-plus"></span></a>
                    </div>
                </div>
            <?php } ?>

        </div>
    </div>
</div>

<div class="row" style="margin-bottom:10px;">
    <div class="col-md-4">
        <div class="btn-group" role="group">
            <a class="btn btn-primary btn-sm" id="print_link" href="?q=view_report_paganition&flag=INVOICE"
               target="_blank">Print Invoice</a>
        </div>
    </div>

    <div class="col-md-4">

        <span id="totalZoneWiseBill" class="label bg-slate-800" style="font-size: 13px;"></span>

    </div>

    <div class="col-md-4">
        <?php if ($ty == 'SA' || $ty == 'A') {
            ?>
            <select class="form-control" name="zone" required>
                <?php foreach ($obj->view_all('tbl_zone') as $singleZone) { ?>
                    <option></option>
                    <option value="<?php echo $singleZone['zone_id'] ?>">
                        <?php echo $singleZone['zone_name'] ?> - (<?php
                        echo $obj->Total_Count('tbl_agent', "zone = " . $singleZone['zone_id']
                            . "");
                        ?>)
                    </option>
                <?php }// foreach   ?>
                <option value="x">Reset</option>
            </select>
        <?php }
        ?>
    </div>
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
<div class="row" style="font-size:12px">
    <div class="col-md-12 table-responsive">
        <table class="table table-responsive table-bordered table-hover table-striped" id="datatable_view_due">
            <thead>
            <tr class="bg-teal-800">
                <th class="col-md-1">Invoice</th>
                <th class="col-md-1">Action</th>
                <th class="col-md-1">Customer Name</th>
                <th class="col-md-1">Customer ID</th>
               <th class="col-md-1">Mobile No</th>
                <th class="col-md-1">Billing Date</th>
                <th class="col-md-1">Previous Due</th>
                <th class="col-md-1">Bill</th>
                <th class="col-md-1">Total<br>Bill</th>
                <th class="col-md-1">Zone</th>
                 <th class="col-md-1">Billing Person</th>
                <th class="col-md-1">Address</th>
                
                
                
                
            </tr>
            </thead>
            <tbody>
            <?php
               
                // $allDuePayment = $obj->view_all_by_cond("vw_bill_calc", "ag_status='1'  AND pay_status='1'   AND due_status='0'  ORDER BY `vw_bill_calc`.`ag_id` ASC");

                // foreach ($allDuePayment as $value) {
                //   echo "
                //   <tr>
                //       <td>".'<a target="_blank" href="./pdf/invoice.php?token1='.$value['ag_id'].'" class="btn btn-warning btn-xs"><span style="color:#000;" class="glyphicon glyphicon-print"></span></a><button class="sms btn btn-primary btn-xs pull-right" data-agent='.$value['ag_id'].'><span class="glyphicon glyphicon-envelope"></span></button>'."</td>
                //       <td></td>
                //       <td>".$value['ag_name']."</td>
                //       <td>".'<a href="?q=view_customer_payment_individual&token2='.$value['ag_id'].'">'.$value['cus_id'].'</a>'."</td>
                //       <td>".$value['house']."</td>
                //       <td>".$value['road']."</td>
                //       <td>".$value['ag_office_address']."</td>
                //       <td>".$value['zone_name']."</td>
                //       <td>".$value['ag_mobile_no']."</td>
                //       <td>".($value['total_bill']-$value['taka'])."</td>
                //       <td>".$value['taka']."</td>
                //       <td>".$value['total_bill']."</td>
                     
                     
                //   </tr>
                //   ";
                // }
                ?>
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

            progressbar.progressbar("value", val + 5);

            if (val < 99) {

                setTimeout(progress, <?php // echo $rowCount * 0.5; ?>);
            }
        }

        setTimeout(progress, 100);


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


        // $.post("view/ajax_action/ajax_data_return.php", function (data) {

        //     $('#billAmount').html(data.duePayment.toLocaleString() + " Taka");

        // }, "json");


        // show total bill amount above table
        function showZoneWiseBill(bill) {

            $('#billAmount').html('Total Due Bill : ' + bill.toLocaleString() + ' Taka');

        }


        var table = $('#datatable_view_due').DataTable({
            "processing": false,
             responsive: true,
            "initComplete": function (settings, json) {
                $('#progressbar').hide();
            },
            "deferRender": true,
            "ajax": 'view/ajax_action/ajax_view_due_payment.php',
            "order": [[0, 'asc']],
            "columns": [
                {
                    "data": 'agent_id',
                    render: function (data, type, row) {
                        return '<a target="_blank" href="./pdf/invoice.php?token1=' + data + '" class="btn btn-warning btn-xs"><span style="color:#000;" class="glyphicon glyphicon-print"></span></a>' +
                            '<button class="sms btn btn-primary btn-xs pull-right" data-agent='+ data +'><span class="glyphicon glyphicon-envelope"></span></button>'
                    }
                },
                {
                    "data": 'agent_id',
                    "render": function (data, type, row, meta) {
                        return '<div class="btn-group"><a href="view/ajax_action/add_ajax_data.php?token=' + data + '&amount=' + row['bill'] + '&flag=1" data-name="' + row['agent_name'] + '" data-amount="' + row['bill'] + '" class="btn btn-primary btn-sm paid" style="padding:5px">Pay</a>'+'<a href="?q=add_payment&token1=' + data + '" style="padding:5px" class="btn btn-success btn-sm "> Payment </a></div>';
                    }
                },

                {"data": 'agent_name'},

                {
                    "data": 'customer_id',
                    render: function (data, type, row) {
                        return '<a href="?q=view_customer_payment_individual&token2=' + row['agent_id'] + '">' + data + '</a>'
                    }
                },
               

                {"data": 'mobile'},
                {"data": 'bill_date'},

                {"data": 'previous_due'},

                {"data": 'bill'},

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
                 {"data": 'zone'},
                 {
                    "data": 'bp',
                },
                {"data": 'agent_address'},
                

                

               

                

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

    });
</script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.25/af-2.3.7/b-1.7.1/b-print-1.7.1/cr-1.5.4/date-1.1.0/fc-3.3.3/fh-3.1.9/kt-2.6.2/r-2.2.9/rg-1.1.3/rr-1.2.8/sc-2.0.4/sb-1.1.0/sp-1.3.0/sl-1.3.3/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.25/af-2.3.7/b-1.7.1/b-print-1.7.1/cr-1.5.4/date-1.1.0/fc-3.3.3/fh-3.1.9/kt-2.6.2/r-2.2.9/rg-1.1.3/rr-1.2.8/sc-2.0.4/sb-1.1.0/sp-1.3.0/sl-1.3.3/datatables.min.js"></script>

<!--<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>-->
<!--<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>-->


