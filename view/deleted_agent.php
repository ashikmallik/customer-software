<?php

date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

$data = $obj->details_by_cond("tbl_agent", "ag_status='1' ORDER BY ag_id DESC");

if (($data['ag_id'] + 1) < 10) {
    $STD = "CUS000";
} else if (($data['ag_id'] + 1) < 100) {
    $STD = "CUS000";
} else if (($data['ag_id'] + 1) < 1000) {
    $STD = "CUS00";
} else if (($data['ag_id'] + 1) < 10000) {
    $STD = "CUS0";
} else {
    $STD = "CUS";
}
$STD .= $data['ag_id'] + 1;

$resteAgent = isset($_GET['agent']) ? $_GET['agent'] : NULL;

if (!empty($resteAgent)) {

    $deletedAgent = $obj->details_by_cond("delete_tbl_agent_log", "ag_id='$resteAgent'");


    $restoreAgentFormData = array(
        'ag_id' => $deletedAgent['ag_id'],
        'cus_id' => $STD,
        'ip' => $deletedAgent['ip'],
        'ag_name' => $deletedAgent['ag_name'],
        'ag_mobile_no' => $deletedAgent['ag_mobile_no'],
        'ag_office_address' => $deletedAgent['ag_office_address'],
        'mb' => $deletedAgent['mb'],
        'taka' => $deletedAgent['taka'],
        'connect_charge' => $deletedAgent['connect_charge'],
        'ag_email' => $deletedAgent['ag_email'],
        'blood_group' => "",
        'national_id' => "",
        'occupation' => "",
        'bill_cat' => $deletedAgent['bill_cat'],
        'zone' => $deletedAgent['zone'],
        'ag_status' => 1,
        'pay_status' => 0,
        'due_status' => 0,
        'entry_by' => $deletedAgent['entry_by'],
        'entry_date' => $deletedAgent['entry_date'],
        'connection_date' => $deletedAgent['connection_date'],
        'update_by' => $deletedAgent['update_by'],
        'last_update' => $deletedAgent['last_update'],

    );

    if ($obj->Reg_user_cond("tbl_agent", $restoreAgentFormData, " ")) {

        $obj->Delete_data("delete_tbl_agent_log", "ag_id='$resteAgent'");
        $obj->notificationStore($deletedAgent['ag_name'] . ' data is Restored', 'success');
        echo '<script>window.location = "?q=deleted_agent";</script>';

    } else {
        $obj->notificationStore('Sorry ! Restore Failed');
        echo '<script>window.location = "?q=deleted_agent";</script>';

    }
}

?>


<div class="row">
    <div class="col-md-12 bg-grey-800">
        <h4>View Deleted Customer</h4>
    </div>
</div>

<div class="row">
    <div class="col-md-12 padding_5_px">
        <?php $obj->notificationShowRedirect(); ?>
    </div>
</div>


<!-- all user show -->
<div id="div_all" class="row" style="padding:10px;font-size: 12px;">
    <div class="col-md-12">
        <div class="table-responsive">
            <table style="margin-left:-15px;" class="agentAllData table table-bordered table-hover table-striped"
                   id="datatable_view_agent">
                <thead>
                <tr class="bg-teal-700">
                    <th class="col-md-1">#</th>
                    <th class="col-md-2">Link Name</th>
                    <th class="col-md-1">Address</th>
                    <th class="col-md-1">Mobile No</th>

                    <th class="col-md-1">Bill Amount</th>
                    <th class="col-md-1">Con. Date</th>
                    
                    <th class="col-md-1">
                        <small>Deleted By</small>
                    </th>
                    <th class="col-md-1">
                        <small>Delete Time</small>
                    </th>
                    <th class="col-md-1">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 0;
                foreach ($obj->view_all('delete_tbl_agent_log') as $deleted) {
                    ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $deleted['link_name'] ?></td>
                        <td><?php echo $deleted['link_address'] ?></td>
                        <td><?php echo $deleted['ag_mobile_no'] ?></td>
                        
                        <td><?php echo $deleted['taka'] ?></td>
                        <td><?php echo date('d/m/Y', strtotime($deleted['connection_date'])); ?></td>
                        
                        <td><?php echo $deleted['deleter_user_id'] ?></td>
                        <td><?php echo date('d/m/Y', strtotime($deleted['deleting_time'])); ?></td>
                        <td>
                            <a onclick="return confirm('Are You Sure To Restore This Agent?');"
                               href="?q=deleted_agent&agent=<?php echo isset($deleted['ag_id']) ? $deleted['ag_id'] : NULL; ?>"
                               class="btn btn-xs btn-success">
                                Restore <span class="glyphicon glyphicon-repeat"></span>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
