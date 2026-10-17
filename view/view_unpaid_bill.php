<?php
function get_client_ip()
{
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;


?>


<style>

    #datatable_view_agent_filter input {
        height: 30px;
        border-radius: 5px;
        padding: 5px;
    }
</style>

<div class="row">
    <div class="col-md-12 padding_5_px">
        <?php $obj->notificationShowRedirect(); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12 bg-teal-800" style="margin-bottom: 15px; padding:10px 0px 3px 15px; font-size:16px; font-family:Lucida Sans Unicode;">
        <div class="col-md-6">
            <b><?php echo date('M') ?> Unpaid Bill Collection Customer List</b>
        </div>
        <div class="col-md-6" style="margin-left: -230px;margin-bottom: -24px;">
        <?php if ($ty == 'SA' || $obj->hasPermission($ty, 'print_client_list')) {
            echo '<a href="?q=print_view_unpaid_bill" id="print_link" target="_blank" class="btn btn-primary btn-xs pull-right" >Print Unpaid Biller List</a>';
        } ?>

    </div>
        
    </div>
</div>

<div class="row" id="print_row" style="margin-bottom:10px;">

	<div class="row">
    <div class="col-md-4 text-center" style="margin-left: 178px;">
        <?php if ($ty == 'SA' || $obj->hasPermission($ty, 'zone_search')) {
            ?>
            <select class="form-control" name="zone" required>
                <?php foreach ($obj->view_all('tbl_zone') as $singleZone) { ?>
                    <option></option>
                    <option value="<?php echo $singleZone['zone_id'] ?>"> <?php echo $singleZone['zone_name'] ?> - <?php echo $obj->Total_Count('tbl_agent', "zone = " . $singleZone['zone_id'] . ""); ?></option>
                <?php }// foreach ?>
                <option value="x">Reset</option>
            </select>
            <?php
        } ?>
    </div>
    <div class="col-md-4 text-center" style="margin-top: 7px;margin-left: 178px;">
        <?php if ($ty == 'SA' || $obj->hasPermission($ty, 'billing_person')) {
            ?>
            <select class="form-control" name="billingperson" required>
                <option></option>
                <?php foreach ($obj->view_all_distinct('billing_person_id','tbl_agent') as $singleagent) { ?>
                    <option value="<?php echo $singleagent['billing_person_id'] ?>"> 
                       <?php $bid=$singleagent['billing_person_id'];
                         $person = $obj->details_by_cond("_createuser", "UserId='$bid'"); echo $person['FullName'] ?>
                            
                        </option>
                <?php }// foreach ?>
                <option value="x">Reset</option>
            </select>
            <?php
        } ?>
    </div>
    </div>
</div>

<!-- all user show -->
<div id="div_all" class="row" style="padding:10px;font-size: 12px;">
    <div class="col-md-12">
        <div class="table-responsive">
           
            <table style="margin-left:-15px;" class="agentAllData table table-bordered table-hover table-striped"
                   id="datatable_view_agent">
                <thead>
                <tr class="bg-grey-800">
                    <th class="col-md-1">Invoice</th>
                    <th class="d col-md-1">Action</th>
                    <th class="col-md-1">Customer ID</th>
                    <th class="col-md-1">Customer Name</th>
                    <th class="col-md-1">Mobile No</th>
                    <th class="col-md-1">Bill Amount</th>
                    <th class="col-md-1">Address</th>
                    <th class="col-md-1">Zone</th>
                    <th class="col-md-1">Billing Person</th>
                    <th class="col-md-1">Billing Date</th>
                    
                   
                   

                    

                </tr>
                </thead>

            </table>
        </div>
    </div>
</div>




<script type="text/javascript">

  
    $(document).ready(function () {
        
          $('#datatable_view_agent').on('click', 'tbody td div.btn-group a.paid', function (e) {

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

        $('.agentAllData').on('click', '[data-target="#zoneAdd"]', function () {

            var companyId = $(this).data('companyid');
            var companyName = $(this).data('name');
            $('#modalCompanyName').html(companyName);
            $('#zoneAdd input[name="zoneId"]').val(companyId);

        });


        var table = $('#datatable_view_agent').DataTable({
            "ajax": 'view/ajax_action/ajax_view_unpaid_bill.php',
            "deferRender": true,
             responsive: true,
            "order": [[ 0, 'asc' ]],
            "columns": [
               {
                    "data": 'ag_id',
                    render: function (data, type, row) {
                        return '<?php if($obj->hasPermission($ty, 'invoice')){ ?><a target="_blank" href="./pdf/invoice.php?token1=' + data + '" class="btn btn-warning btn-xs"><span style="color:#000;" class="glyphicon glyphicon-print"></span></a><?php } ?>' +
                            '<button class="sms btn btn-primary btn-xs pull-right" data-agent='+ data +'><span class="glyphicon glyphicon-envelope"></span></button>'
                    }
                },
                 {
                    "data": 'ag_id',
                    "render": function (data, type, row, meta) {
                        return '<div class="btn-group"> <a href="view/ajax_action/add_ajax_data.php?token=' + data + '&amount=' + row['taka'] + '&flag=1" data-name="' + row['ag_name'] + '" data-amount="' + row['taka'] + '" class="btn btn-primary btn-sm paid" style="padding:5px">Pay</a>'+'<a href="?q=add_payment&token1=' + data + '" style="padding:5px" class="btn btn-success btn-sm "> Payment </a></div>';
                    }
                }, 
                {
                    "data": 'cus_id',
                    render: function (data, type, row) {
                        return '<a href="?q=view_customer_payment_individual&token2=' + row['ag_id'] + '"> ' + data + ' </a>'
                    
                    }
                },{
                    "data": 'ag_name',
                    render: function (data, type, row) {
                        return '<a href="?q=view_customer_details&token2=' + row['ag_id'] + '"> ' + data + ' </a>'
                    }
                },
                {
                    "data": 'ag_mobile_no'
                },
                
                {
                    "data": 'taka',
                    "searchable": false,
                },
                {
                    "data": 'ag_office_address',
                },
                 
                {
                    "data": 'zone_name',
                },
                {
                    "data": 'bp',
                },
                 {
                    "data": 'bill_date',
                },
               
                
            ],
            "fnDrawCallback": function (oSettings) {

                if (oSettings.json) {
                    var loadData = oSettings.json;
                    // showZoneWiseBill(loadData.total_bill);
                }
            },

            // "initComplete": function (settings, json) {

            //     // showtotalbillAmount(json.total_bill);
            // },


        });

        // show total bill amount above table
        // function showtotalbillAmount(bill){

        //     $('#totalBillAmount').html(bill + ' Taka');

        // }

        $('select[name="zone"]').on('change', function () {

            var zoneId = $(this).val();
            if (zoneId != 'x') {

                table.ajax.url('view/ajax_action/ajax_view_unpaid_bill.php?zone=' + zoneId).load();
                $('a#print_link').attr('href','?q=print_view_unpaid_bill&zonePrint=' + zoneId);
            } else {
                table.ajax.url('view/ajax_action/ajax_view_unpaid_bill.php').load();
                $('a#print_link').attr('href','?q=print_view_unpaid_bill');
            }
        });

        $('select[name="billingperson"]').on('change', function () {

            var bid = $(this).val();
            if (bid != 'x') {

                table.ajax.url('view/ajax_action/ajax_view_unpaid_bill.php?bid=' + bid).load();
                $('a#print_link').attr('href','?q=print_view_unpaid_bill&billingperson=' + bid);
            } else {
                table.ajax.url('view/ajax_action/ajax_view_unpaid_bill.php').load();
                $('a#print_link').attr('href','?q=print_view_unpaid_bill');
            }
        });
		
		$('div#active_inactive_list button').on('click', function () {

            var status = $(this).data('status');
            if (status != 'x') {

                table.ajax.url('view/ajax_action/ajax_view_unpaid_bill.php?status=' + status).load();
                $('a#print_link').attr('href','?q=print_view_unpaid_bill&fieldPrint=' + status);

            } else {
                table.ajax.url('view/ajax_action/ajax_view_unpaid_bill.php').load();
                $('a#print_link').attr('href','?q=print_view_unpaid_bill');
            }
        });
        $('#datatable_view_agent').on('click', 'tbody td button.sms', function (e) {

            var agentId = $(this).data('agent');

            var paidButton = $(this).closest('tr').find('td div.btn-group a.paid');

            $(this).removeClass('btn-primary');
            $(this).addClass('disabled btn-default');

            $.get("view/ajax_action/send_customer_info_sms.php", { agent: agentId }, function (result) {

                alert('An SMS sent to Your Custom SMS');

            });


        });

    });
</script>