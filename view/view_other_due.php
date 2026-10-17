<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

$allOtherDue = $obj->view_all_by_cond_left_join('tbl_due_opening_amount_and_con_charge', 'tbl_agent', 'tbl_agent_id', 'ag_id', 'ag_name, ag_office_address, taka, ag_mobile_no, ip');

if( isset($_POST['submit_due_payment']) ){

    $dueData = $obj->details_by_cond("tbl_due_opening_amount_and_con_charge", "tbl_agent_id = ".$_POST['agent_id']);
    $agentData = $obj->details_by_cond("tbl_agent", "ag_id = ".$_POST['agent_id']);


    if($dueData['connection_charge_due'] > intval($_POST['connection_charge_due_payment'])){

        $connectionChargeDue = $dueData['connection_charge_due'] - intval($_POST['connection_charge_due_payment']);

    }else{

        $connectionChargeDue = 0;
    }

    if($dueData['running_month_due'] > intval($_POST['running_month_due_payment'])){

        $runningMonthDue = $dueData['running_month_due'] - intval($_POST['running_month_due_payment']);

    }else{

        $runningMonthDue = 0;
    }


    if( ($connectionChargeDue == 0) && ($runningMonthDue == 0) ){

        $obj->Delete_data('tbl_due_opening_amount_and_con_charge', 'id = '.$dueData['id'] );
    }else{

        $form_data_due = array(
            'connection_charge_due' => $connectionChargeDue,
            'running_month_due' =>  $runningMonthDue,
            'entry_by' => $userid
        );

        $obj->Update_data("tbl_due_opening_amount_and_con_charge", $form_data_due, "id = ".$dueData['id']);
    }


    // account table part

    if (isset($_POST['running_month_due_payment']) && !empty($_POST['running_month_due_payment'])) {
        /*add balance problem in monthly statement*/
         $rundate = $obj->details_by_cond("tbl_account", "acc_type=5 and agent_id = ".$_POST['agent_id']);
         $month = date("m",strtotime($rundate['entry_date'] ));
         $Mdate = new DateTime();
         $Mdate->modify('+1 month');
         $AddMon = $Mdate->format('Y-m-d H:i:s');

         if($month != date('m'))
         {
           $form_data_running_month = array(
            'cus_id' => $_POST['agent_id'],
            'agent_id' => $_POST['agent_id'],
            'acc_type' => '5',
            'acc_amount' => $_POST['running_month_due_payment'],
            'acc_description' => 'Repayment of Opening / Running Month Amount Due of ' . $agentData['ag_name'],
            'entry_by' => $userid,
            'entry_date' => $date_time,
            'update_by' => $userid
            );
         }
         elseif($month == date('m'))
         {
            $form_data_running_month = array(
            'cus_id' => $_POST['agent_id'],
            'agent_id' => $_POST['agent_id'],
            'acc_type' => '5',
            'acc_amount' => $_POST['running_month_due_payment'],
            'acc_description' => 'Repayment of Opening / Running Month Amount Due of ' . $agentData['ag_name'],
            'entry_by' => $userid,
            'entry_date' => $AddMon,
            'update_by' => $userid
            );
         }
         /*End add balance problem in monthly statement*/
        $obj->Reg_user_cond("tbl_account", $form_data_running_month, " ");
        $obj->notificationStore('Running Month Payment Successfully','success');
    }

    if (isset($_POST['connection_charge_due_payment']) && !empty($_POST['connection_charge_due_payment'])) {

        $form_data_con_charge = array(
            'cus_id' => $_POST['agent_id'],
            'agent_id' => $_POST['agent_id'],
            'acc_type' => '4', //connection charge sent tbl_account
            'acc_amount' => $_POST['connection_charge_due_payment'],
            'acc_description' => 'Repayment of Connection Charge Amount Due of ' . $agentData['ag_name'],
            'entry_by' => $userid,
            'entry_date' => $date_time,
            'update_by' => $userid
        );

        $obj->Reg_user_cond("tbl_account", $form_data_con_charge, " ");
        $obj->notificationStore('Connection Charge Payment Successfully','success');
    }

    echo '<script>window.location = "?q=view_other_due";</script>';
}

?>

<div class="row">
    <div class="col-md-12 padding_5_px">
        <?php $obj->notificationShowRedirect(); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12 bg-slate-800">
        <h4>View Running Month And Connection Charge Due List</h4>
    </div>
</div>
<hr>
<div class="row" style="font-size:12px">
    <div class="col-md-12">
        <table class="table table-responsive table-bordered table-hover " id="datatable">
            <thead>
            <tr class="bg-slate-800">
                <th class="col-md-1">SL</th>
                <th class="col-md-1">Customer Name</th>
                <th class="col-md-1">Address</th>
                <th class="col-md-1">Mobile No</th>
                <th class="col-md-1">IP</th>
                <th class="col-md-1">Bill</th>
                <th class="col-md-2">
                    <small>Running Month Due</small>
                </th>
                <th class="col-md-2">
                    <small>Connection Charge Due</small>
                </th>
                <th class="col-md-1">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $i = 0;
            foreach ($allOtherDue as $dueClient) { ?>
                <tr>
                    <td class="text-center"> <?php echo ++$i ?> </td>
                    <td class="text-center"> <?php echo $dueClient['ag_name'] ?> </td>
                    <td class="text-center"> <?php echo $dueClient['ag_office_address'] ?> </td>
                    <td class="text-center"> <?php echo $dueClient['ag_mobile_no'] ?> </td>
                    <td class="text-center"> <?php echo $dueClient['ip'] ?> </td>
                    <td class="text-center"> <?php echo $dueClient['taka'] ?> </td>
                    <td class="text-center"> <?php echo $dueClient['running_month_due'] ?> </td>
                    <td class="text-center"> <?php echo $dueClient['connection_charge_due'] ?> </td>
                    <td class="text-center action-btn">
                        <button class="edit btn btn-success btn-sm" data-toggle="modal" data-target="#editdue"
                                data-id="<?php echo $dueClient['tbl_agent_id'] ?>"
                                data-running_month="<?php echo $dueClient['running_month_due'] ?>"
                                data-connection_charge="<?php echo $dueClient['connection_charge_due'] ?>">Payment
                        </button>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<hr>

<div class="modal fade" id="editdue" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Payment of Running Month & Connection Charge Due</h4>
            </div>

            <form id="editDueForm" action="" method="post" class="form-horizontal">

                <div class="modal-body">

                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-5 control-label">Running Month Due Payment</label>
                        <div class="col-sm-7">
                            <div class="input-group">
                                <input onkeypress="return numbersOnly(event)" type="number" value="0" name="running_month_due_payment" class="form-control" >
                                <span class="input-group-addon runningMonthDue"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-5 control-label">Conn. Charge Due Payment</label>
                        <div class="col-sm-7">
                            <div class="input-group">
                                <input onkeypress="return numbersOnly(event)" type="number" value="0" name="connection_charge_due_payment" class="form-control">
                                <span class="input-group-addon connectionChargeDue" ></span>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="agent_id" value="">

                </div>
                <div class="modal-footer text-center">

                    <button type="submit" name="submit_due_payment" class="btn btn-primary">Save changes</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>


    function numbersOnly(e) // Numeric Validation
    {
        var unicode = e.charCode ? e.charCode : e.keyCode
        if (unicode != 8) {
            if ((unicode < 2534 || unicode > 2543) && (unicode < 48 || unicode > 57)) {
                return false;
            }
        }
    }


    $(document).ready(function () {

        $('table#datatable').on('click', 'td.action-btn button.edit', function () {

            var agentId = $(this).data('id');
            var runningMonthDue = $(this).data('running_month');
            var connectionChargeDue = $(this).data('connection_charge');


            $('div#editdue form#editDueForm div.modal-body span.runningMonthDue').html("Pre. Due " + runningMonthDue + " taka");
            $('div#editdue form#editDueForm div.modal-body span.connectionChargeDue').html("Pre. Due " + connectionChargeDue + " taka");
            $('div#editdue form#editDueForm div.modal-body input[name="agent_id"]').val(agentId);

        });

        $('#editdue').on('hidden.bs.modal', function (e) {

            $('form#editDueForm').trigger("reset");
        })

    });
</script>