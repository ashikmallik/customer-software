<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
//$date        = date('Y-m-d');
$Month = date('m', strtotime($date));
$Year = date('Y', strtotime($date));
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

$flag = isset($_GET['key']) ? "1" : "0";
$where = "";
$title = " " . date("M Y");

$where = "MONTH(entry_date)='$Month' and YEAR(entry_date)='$Year'";

$all = $obj->Total_Count("tbl_agent", $where);
$active = $obj->Total_Count("tbl_agent", $where . " AND ag_status='1'");
$inactive = $obj->Total_Count("tbl_agent", $where . " AND ag_status='0'");
$inactive = $obj->Total_Count("vw_agent", "MONTH(inactive_date)='$Month' and YEAR(inactive_date)='$Year' AND ag_status='0'");


?>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">
<style>
    .all-class {
        background: #23659f;
        border-color: #23578d;
        box-shadow: -1px 6px 4px #4e4e4e !important
    }

    .active-class {
        background-color: #308a30;
        border-color: #387c38;
        box-shadow: -1px 6px 4px #4e4e4e !important
    }

    .inactive-class {
        background-color: #bd2a26;
        border-color: #9c2824;
        box-shadow: -1px 6px 4px #4e4e4e !important
    }
</style>
<div class="row">
    <div class="col-md-12">
        <?php $obj->notificationShowRedirect(); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12 bg-grey-800">
        <h4 id="new_joining_title">New Joining Customer Information for <span id="show_date"><?php echo $title ?></span></h4>
    </div>
</div>

<div class="row" id="date_search_field">
    <div class="panel panel-success">
        <div class="panel-heading">
            <b>Please select Date to search Client</b>
        </div>
        <div class="panel-body">
            <div class="col-md-4">
                <?php if ($ty == 'SA' || $obj->hasPermission($ty, 'company')) {
                    ?>
                    <select class="form-control" name="company" required>
                        <option>Compnay Wise Search</option>
                        <?php foreach ($obj->view_all('tbl_company') as $singleZonec) { ?>
                            
                            <option value="<?php echo $singleZonec['company_id'] ?>"> <?php echo $singleZonec['company_name'] ?>- <?php echo $obj->Total_Count('tbl_agent', "company_id = " . $singleZonec['company_id'] . ""); ?></option>
                        <?php }// foreach ?>
                        <option value="x">Reset</option>
                    </select>
                    <?php
                } ?>
            </div>
            <form id="date_search" action="" method="POST">
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="text" class="form-control datepicker" placeholder="Form Date" name="datefrom"
                               required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="text" class="form-control datepicker"
                               placeholder="To Date" name="dateto" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="search" class="btn btn-primary"> Search</button>
                </div>
            </form>
            
        </div>
    </div>
</div>
<div class="row" id="">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div id="client_list" class="btn-group btn-group-justified">

                    <a id="all" class="btn btn-primary all-class">All Client - <span
                                class="badge all_client"><?php echo $all ?></span> </a>
                                <?php if($obj->hasPermission($ty, 'active_inactive')){ ?>
                    <a id="active" class="btn btn-success">Active Client - <span
                                class="badge all_active"><?php echo $active ?></span></a>
                    <a id="inactive" class="btn btn-danger">Inactive Client - <span
                                class="badge all_inactive"><?php echo $inactive ?></span></a>
                   <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="table-responsive" style="font-size:12px;">
            <?php
        if ($ty == 'SA' ||  $obj->hasPermission($ty, 'total_bill')) {
            ?>
            <h4 class="text-teal-800">Total Bill Amount: <span id="totalBillAmount"></span></h4>
           
            <?php
        }
        ?>
            <table class="table table-striped" id="view_new_customer_datatable">
                <thead>
                <tr class="bg-slate-800">

                    <th class="col-md-1">Sl</th>
                    <th class="col-md-1">Link Name</th>
                    <th class="col-md-2">Address</th>
                    <th class="col-md-1">Mobile No</th>
                    <th class="col-md-1">Regular bill</th>
                   
                    <th class="col-md-1">Zone</th>
                   
                    <th class="col-md-1">Connect</th>
                     <th class="col-md-1">Ontime Installation</th>
                    
                    <th class="col-md-2">Action</th>
                </tr>

                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {


        var today = new Date();
        var lastDay = new Date(today.getFullYear(), (today.getMonth() + 1), 0);

        // get first day of month and last day of month
        var dateFrom = today.getFullYear() + '-' + (today.getMonth() + 1) + '-1';
        var dateTo = lastDay.getFullYear() + '-' + (lastDay.getMonth() + 1) + '-' + lastDay.getDate();
        var clientStatus = 'All';
        var title = 'New Joining Customer Information for <?php echo $title ?>';
        
        var table = $('#view_new_customer_datatable').DataTable({
            dom: 'Bflrtip',
            buttons: [
                {
                    extend: 'print',
                    text: 'Print Client',
                    exportOptions: {
                        columns: [1,3,4,5,6,7,8]
                    },
                    title: function () {
                        return title
                    },
                    customize: function ( win ) {
                        $(win.document.body).css( 'font-size', '12px' ),
                        $(win.document.body).find( 'h1' ).addClass( 'text-center' ).css( 'font-size', '20px' );
                        $(win.document.body).find( 'table' ).addClass( 'container' ).css( 'font-size', 'inherit' );
                    }
                }
            ],
            "ajax": 'view/ajax_action/ajax_new_join.php?datefrom=' + dateFrom + '&dateto=' + dateTo + '&stat=all',
            "deferRender": true,
            "order": [[0, 'asc']],
            "columns": [
                {
                    "data": 'sl',
                    render: function (data, type, row) {
                        return '<a href="?q=view_customer_payment_individual&token2=' + row['ag_id'] + '"> ' + data + ' </a>'
                        
                    }
                },
                {
                    "data": 'link_name'
                },
                {
                    "data": 'link_address',
                },
                {
                    "data": 'phone'
                },
            
                {
                    "data": 'taka',
                    "searchable": false,
                },
                {
                    "data": 'zone_name',
                },
                
                {
                    "data": 'connection_date',
                },
                {
                    "data": 'connect_charge',
                },
                
                {
                    "data": 'ag_id',
                    "render": function (data, type, row, meta) {

                        return '<a href="?q=add_payment&token1=' + data + '" class="btn btn-sm btn-success pull-left"  >Advance Payment</a>' +
                            '<?php echo ( $obj->userWorkPermission($acc,'edit')) ? '<a target="_blank" class="btn btn-sm btn-primary pull-right"  href="?q=edit_agent&token=\' + data + \'"> Edit &nbsp; </a>' : ''; ?> '+
                            '<?php echo '<a target="_blank" class="btn btn-xs btn-primary" style="padding:5px;" href="?q=connection_charge&token=\' + data + \'">Connection Charge</a>';?>'
                    }
                },
            ],

            "initComplete": function (settings, json) {
                var stat="all";
                showtotalbillAmount(status);
            },

        });
        function showtotalbillAmount(status){
            $.get('view/ajax_action/ajax_new_join.php?datefrom=' + dateFrom + '&dateto=' + dateTo + '&stat='+status+'', function(data, status){
                $('#totalBillAmount').html(data.total_bill + ' Taka'); 
                $('#totalBillAmountPaid').html(data.total_paid + ' Taka'); 
                $('#totalBillAmountPaidCount').html(data.total_paid_count); 
                 $('#total_connectionFee').html(data.total_connectionFee); 
            }, 'json');


        }
        $('select[name="company"]').on('change', function () {

            var bid = $(this).val();
            if (bid != 'x') {

                table.ajax.url('view/ajax_action/ajax_new_join.php?bid=' + bid).load();
                $('a#print_link').attr('href','?q=printclient&company=' + bid);
            } else {
                table.ajax.url('view/ajax_action/ajax_new_join.php').load();
                $('a#print_link').attr('href','?q=printclient');
            }
        });

        $('div#date_search_field').on('submit', 'form#date_search', function (event) {

            event.preventDefault();
            var fromDateInput = $(this).find('input[name="datefrom"]').val().split('-');
            var toDateInput = $(this).find('input[name="dateto"]').val().split('-');

            dateFrom = inputDateFormt(fromDateInput);
            dateTo = inputDateFormt(toDateInput);

            table.ajax.url('view/ajax_action/ajax_new_join.php?datefrom=' + dateFrom + '&dateto=' + dateTo + '&stat=all').load();
            showDate(dateFrom, dateTo); // to show date in title
            clientCountByDate(dateFrom, dateTo); // to show the number of client in such date.
            changeTitle(dateFrom, dateTo, 'All') // to change the title of print page.
            clientStatus = 'All'; // default client status is all cause showing all client
            showtotalbillAmount(status);

        });

        function clientCountByDate(dateFrm, dateT) {

            // go to several page to get client number and place this data to html place.
            var url = 'view/ajax_action/ajax_count_active_inactive_client.php';
            $.get(url, {datefrom: dateFrom, dateto: dateTo}, function (countData) {

                $('#client_list span.all_client').html(countData.all_client);
                $('#client_list span.all_active').html(countData.active_client);
                $('#client_list span.all_inactive').html(countData.inactive_client);

            }, "json");

        }

        $('#all').click(function () {

            table.ajax.url('view/ajax_action/ajax_new_join.php?datefrom=' + dateFrom + '&dateto=' + dateTo + '&stat=all').load();
            clientStatus = 'All';
            addRemoveClass($(this), 'all-class'); // to selected the button called all
            changeTitle(dateFrom, dateTo, 'All'); // to change the title of the print page
            showtotalbillAmount(status);
        });
        $('#active').click(function () {

            table.ajax.url('view/ajax_action/ajax_new_join.php?datefrom=' + dateFrom + '&dateto=' + dateTo + '&stat=1').load();
            clientStatus = 'Active';
            addRemoveClass($(this), 'active-class'); // to selected the button called active
            changeTitle(dateFrom, dateTo, 'Active'); // to change the title of the print page
            showtotalbillAmount(1);
        });

        $('#inactive').click(function () {

            table.ajax.url('view/ajax_action/ajax_new_join.php?datefrom=' + dateFrom + '&dateto=' + dateTo + '&stat=0').load();
            clientStatus = 'Inactive';
            addRemoveClass($(this), 'inactive-class'); // to selected the button called inactive
            changeTitle(dateFrom, dateTo, 'Inactive'); // to change the title of the print page
            showtotalbillAmount(0);
        });

        function addRemoveClass(thisdata, className) {
            // to show selected the button

            $('#all').removeClass('all-class');
            $('#active').removeClass('active-class');
            $('#inactive').removeClass('inactive-class');
            thisdata.addClass(className);
        }


        function showDate(dateStartInput, dateEndInput) {
            // to convert the date formate and show those in title

            var dateOne = new Date(dateStartInput);
            var dateOneFormated = dateOne.getDate() + "/ " + (dateOne.getMonth() + 1) + "/ " + dateOne.getFullYear();
            var dateTwo = new Date(dateEndInput);
            var dateTwoFormated = dateTwo.getDate() + "/ " + (dateTwo.getMonth() + 1) + "/ " + dateTwo.getFullYear();

            $('span#show_date').html(dateOneFormated + " to " + dateTwoFormated);
        }
        
        function changeTitle(dateFromTitle, dateToTitle, statusTitle){
            // to change the title for print page

            title = 'New ' + statusTitle + ' Customer Information of '+ dateFromTitle +' to ' + dateToTitle ;

        }

        $('.datepicker').datepicker({
            autoclose: true,
            toggleActive: true,
            format: 'dd-mm-yyyy',
        });
    });
</script>
<script type="application/javascript"
        src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script type="application/javascript"
        src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js "></script>