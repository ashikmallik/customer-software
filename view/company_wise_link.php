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

$intoken = isset($_GET['intoken']) ? $_GET['intoken'] : NULL;
$actoken = isset($_GET['actoken']) ? $_GET['actoken'] : NULL;

 
 
if (!empty($intoken)) {
    $form_data = array('ag_status' => '0', 'update_by' => $userid);

    $obj->Update_data("tbl_agent", $form_data, "where ag_id='$intoken'");
}

if (!empty($actoken)) {
    $form_data = array('ag_status' => '1', 'update_by' => $userid);

    $obj->Update_data("tbl_agent", $form_data, "where ag_id='$actoken'");
}


// ========== Delete Function Start =================

if( $obj->userWorkPermission($acc,'delete')){

    $dltoken = isset($_GET['dltoken']) ? $_GET['dltoken'] : NULL;

    if (!empty($dltoken)) { // entry a tbl agent copy for keep delete log

        $formDataAgentDataBeforeDelete = $obj->details_by_cond("tbl_agent", "ag_id='$dltoken'");
        $formDataStoreAgentData = array(

            'ag_id' => $formDataAgentDataBeforeDelete['ag_id'],
            'ip' => $formDataAgentDataBeforeDelete['ip'],
            'ag_name' => $formDataAgentDataBeforeDelete['ag_name'],
            'ag_mobile_no' => $formDataAgentDataBeforeDelete['ag_mobile_no'],
            'ag_office_address' => $formDataAgentDataBeforeDelete['ag_office_address'],
            'mb' => $formDataAgentDataBeforeDelete['mb'],
            'taka' => $formDataAgentDataBeforeDelete['taka'],
            'connect_charge' => $formDataAgentDataBeforeDelete['connect_charge'],
            'ag_email' => $formDataAgentDataBeforeDelete['ag_email'],
            'bill_cat' => $formDataAgentDataBeforeDelete['bill_cat'],
            'zone' => $formDataAgentDataBeforeDelete['zone'],
            'entry_by' => $formDataAgentDataBeforeDelete['entry_by'],
            'entry_date' => $formDataAgentDataBeforeDelete['entry_date'],
            'connection_date' => $formDataAgentDataBeforeDelete['connection_date'],
            'update_by' => $formDataAgentDataBeforeDelete['update_by'],
            'last_update' => $formDataAgentDataBeforeDelete['last_update'],
            'deleter_ip' => get_client_ip(),
            'deleter_user_id' => $userid,
            'deleting_time' => date('Y/m/d H:i:s'),

        );
        
        

        $obj->Reg_user_cond("delete_tbl_agent_log", $formDataStoreAgentData, " ");
        
        if ($obj->tableExists('mikrotik_user')) {
            require('./model/Mikrotik.php');
            if($formDataAgentDataBeforeDelete['mikrotik_id'] > 0){
                $mikrod = $obj->details_by_cond('mikrotik_user', "id =".$formDataAgentDataBeforeDelete['mikrotik_id']);
                $mikrotik = new Mikrotik($mikrod['mik_ip'], $mikrod['mik_username'], $mikrod['mik_password']);
                if ($mikrotik->checkConnection()) {
                    $mikrotik->removeSecret(trim($formDataAgentDataBeforeDelete['ip']));
                }
            }         
       }
        
        

        if ($obj->Delete_data("tbl_agent", "ag_id='$dltoken'")) {

            $obj->notificationStore($formDataAgentDataBeforeDelete['ag_name'].' data is Deleted','success');
            echo '<script>window.location = "?q=view_agent";</script>';

        } else {
            $obj->notificationStore('Delete Failed');
            echo '<script>window.location = "?q=view_agent";</script>';

        }
    }
}


// view all/ Active / Inactie and zone wise search
if (isset($_POST['company'])) {
    $company = $_POST['company'];
    $allAgentData = $obj->view_all_by_cond("tbl_agent", "company_id='$company'");
    

} elseif (isset($_GET['key'])) {

    $key = $_GET['key'];
    if ($key == "all") {
        $allAgentData = $obj->view_all("tbl_agent");

    } elseif ($key == "active") {
        $allAgentData = $obj->view_all_by_cond("tbl_agent", "ag_status='1'");

    } elseif ($key == "inactive") {
        $allAgentData = $obj->view_all_by_cond("tbl_agent", "ag_status='0'");

    } else {
        $allAgentData = $obj->view_all("tbl_agent");
    }
} else {
    $allAgentData = $obj->view_all_ordered_by("tbl_agent", '`tbl_agent`.`ag_id` DESC LIMIT 50');
}


$permissionLinkEdit = ''; // for ajax datatable link
$permissionLinkDelete = ''; // for ajax datatable link
$showremarks = '';

if( $obj->userWorkPermission($acc,'edit')){
    $permissionLinkEdit = '<a target="_blank" class="btn btn-xs btn-primary" style="padding:5px;" href="?q=edit_agent&token=\' + data + \'"><span class="glyphicon glyphicon-edit"></span>';
}

if( $obj->userWorkPermission($acc,'delete')){
    $permissionLinkDelete = '<a onclick="return confirmDelete()"  href="?q=view_agent&dltoken=\' + data + \'" class="btn btn-xs btn-danger" style="padding:5px;"><span class="glyphicon glyphicon-remove"></span></a>';
}
//$showremarks='<a target="_blank" class="btn btn-xs btn-primary" style="padding:5px;" href="?q=agent_remarks&token=\' + data + \'"><span class="glyphicon glyphicon-paperclip"></span>Remarks</a>';
$showremarks='<a target="_blank" class="btn btn-xs btn-primary" style="padding:5px;" href="?q=agent_remarks&token=\' + data + \'"><span class="glyphicon glyphicon-paperclip"></span>Remarks</a>';

$connectionFee = '<a target="_blank" class="" style="padding:5px;" href="?q=connection_charge&token=\' + data + \'"></a>';

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
        <div class="col-md-4">
            <b>View Customer Information</b>
        </div>
        <?php if($obj->hasPermission($ty, 'active_inactive')){ ?>
        <div class="col-md-4 text-center" style="color: #dbdbdb;">
            
            <small class="padding_3_10_px btn-success">Active : <span class="badge"><?php echo $obj->Total_Count('tbl_agent', 'ag_status=1')?></span> Incative : <span class="badge"><?php echo $obj->Total_Count('tbl_agent', 'ag_status=0')?></span> </small>
            
        </div>
        <?php } ?>
        <div class="col-md-3" style="margin-left: -230px;margin-bottom: -24px;">
        

    </div>
    <?php if( $obj->userWorkPermission($acc,'add')){ ?>
        <div class="col-md-4">
           
                <a class="btn btn-primary btn-sm pull-right" href="?q=add_agent">ADD NEW<span class="glyphicon
        glyphicon-plus"></span></a>
          
        </div>
        <?php } ?>
    </div>
</div>

<div class="row" id="print_row" style="margin-bottom:10px;">
	<?php if($obj->hasPermission($ty, 'active_inactive')){ ?>
	<div class="col-md-6 text-center">
        <?php if($ty == 'SA' || $ty == 'EO') { ?><div class="btn-group" id="active_inactive_list" role="group" style="margin-left: 233px;">
            <button class="btn btn-default" data-status ="x">View All</button>
            <button class="btn btn-default" data-status ="1">Only Active</button>
            <button class="btn btn-default" data-status ="0">Only Inactive</button>
        </div><?php } ?>
    </div>
<?php } ?>
	<div class="row">

    <div class="col-md-4 text-center" style="margin-left: 178px;">
        <?php if ($ty == 'SA' || $obj->hasPermission($ty, 'company_search')) {
            ?>
            <select class="form-control" name="company" required>
                <option>Compnay Wise Search</option>
                <?php foreach ($obj->view_all('tbl_company') as $singleZonec) { ?>
                    
                    <option value="<?php echo $singleZonec['company_id'] ?>"> <?php echo $singleZonec['company_name'] ?> - <?php echo $obj->Total_Count('tbl_agent', "company_id = " . $singleZonec['company_id'] . ""); ?></option>
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
            <?php if($ty == 'SA' || $obj->hasPermission($ty, 'total_bill')) { ?><h4 class="text-teal-800">Total Bill Amount: <span id="totalBillAmount"></span></h4><?php } ?>
            <table style="margin-left:-15px;" class="agentAllData table table-bordered table-hover table-striped"
                   id="datatable_view_agent">
                <thead>
                <tr class="bg-grey-800">
                    <th class="col-md-1">SL</th>
                    
                    <th class="col-md-2">Link Name</th>
                    <th class="col-md-1">Address</th>
                    <th class="col-md-1">company Mobile</th>
                   
                    <th class="col-md-1">Regullar Bill</th>
                    <th class="col-md-1">Connection Date</th>
                    <th class="col-md-1">Zone</th>
                    <!-- <th class="col-md-1">IP</th>
                    <th class="col-md-1">Billing Person</th> -->
                    <!--<th class="col-md-1">Remarks</th>-->
                    <th class="col-md-1">Status</th>
                    <?php if( $obj->userWorkPermission($acc,'edit') || $obj->userWorkPermission($acc,'delete') ) {
                        echo '<th class="col-md-1">Action</th>';
                    } ?>

                </tr>
                </thead>

            </table>
        </div>
    </div>
</div>


<!-- Modal Start-->
<div class="modal fade" id="zoneAdd" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center">Insert New Zone for <span id="modalCompanyName"></span></h4>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="row" style="padding:0 10px;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="row">
                                    <label>Zone Name</label>
                                    <input type="hidden" name="zoneId" value="" class="form-control">
                                    <select class="form-control" name="zoneName" required>
                                        <option value="">---Provide Zone Name---</option>
                                        <?php foreach ($obj->view_all('tbl_company') as $tbl_company) { ?>
                                            <option value="<?php echo $tbl_company['company_id'] ?>"><?php echo $tbl_company['company_name'] ?></option>
                                        <?php }// foreach ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-default" name="zoneUpdateSubmit"> Update Zone
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>


<script type="text/javascript">

    function confirmDelete() {

        return confirm('Agent will be delete,\n Are You sure? ');

    }
    $(document).ready(function () {

        $('.agentAllData').on('click', '[data-target="#zoneAdd"]', function () {

            var companyId = $(this).data('companyid');
            var companyName = $(this).data('name');
            $('#modalCompanyName').html(companyName);
            $('#zoneAdd input[name="zoneId"]').val(companyId);

        });


        var table = $('#datatable_view_agent').DataTable({
            "ajax": 'view/ajax_action/ajax_view_agent.php',
            "deferRender": true,
            "order": [[ 0, 'asc' ]],
            "columns": [
                {
                    "data": 'sl' 
                },
                
                {
                    "data": 'link_name',
                    render: function (data, type, row) {
                        return '<a href="?q=view_customer_details&token2=' + row['ag_id'] + '"> ' + data + ' </a>'
                    }
                },
                {
                    "data": 'link_address',
                },
                {
                    "data": 'ag_mobile_no'
                },
                
                {
                    "data": 'taka',
                    "searchable": false,
                },
                {
                    "data": 'connection_date',
                },
                {
                    "data": 'zone',
                },
                // {
                //     "data": 'ip',
                // },
                //  {
                //     "data": 'bp',
                // },
                // {
                //     "data": 'remark',
                //     "render": function (data, type, row, meta) {

                //         return '<?php //echo $showremarks ; ?>';
                //     }
                // },
                
                {
                    "data": 'ag_status',
                    "render": function (data, type, row, meta) {

                        if (data == 1) {
                            return '<buttton class="padding_5_px btn-success btn-xs">Active</span></button>';

                        } else {
                            return '<buttton class="padding_5_px btn-danger btn-xs">Inactive</span></button>';

                        }
                    }
                },
                <?php if( $obj->userWorkPermission($acc,'edit') || $obj->userWorkPermission($acc,'delete') ) { ?>
                {
                    "data": 'ag_id',
                    "render": function (data, type, row, meta) {

                        return '<?php echo $permissionLinkEdit ; ?>' +
                            '<?php echo $permissionLinkDelete ; ?>'+
                            '<?php echo $connectionFee ; ?>';
                    }
                },
                <?php } ?>
            ],

            "fnDrawCallback": function( oSettings ) {

                if(oSettings.json){
                    var loadData = oSettings.json;
                    showtotalbillAmount(loadData.total_bill);
                }
            },

            "initComplete": function (settings, json) {

                showtotalbillAmount(json.total_bill);
            },


        });

        // show total bill amount above table
        function showtotalbillAmount(bill){

            $('#totalBillAmount').html(bill + ' Taka');

        }

        $('select[name="company"]').on('change', function () {

            var zoneId = $(this).val();
            if (zoneId != 'x') {

                table.ajax.url('view/ajax_action/ajax_view_agent.php?company=' + zoneId).load();
                
            } else {
                table.ajax.url('view/ajax_action/ajax_view_agent.php').load();
               
            }
        });

        $('select[name="billingperson"]').on('change', function () {

            var bid = $(this).val();
            if (bid != 'x') {

                table.ajax.url('view/ajax_action/ajax_view_agent.php?bid=' + bid).load();
                $('a#print_link').attr('href','?q=printclient&billingperson=' + bid);
            } else {
                table.ajax.url('view/ajax_action/ajax_view_agent.php').load();
                $('a#print_link').attr('href','?q=printclient');
            }
        });
		
		$('div#active_inactive_list button').on('click', function () {

            var status = $(this).data('status');
            if (status != 'x') {

                table.ajax.url('view/ajax_action/ajax_view_agent.php?status=' + status).load();
                $('a#print_link').attr('href','?q=printclient&fieldPrint=' + status);

            } else {
                table.ajax.url('view/ajax_action/ajax_view_agent.php').load();
                $('a#print_link').attr('href','?q=printclient');
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