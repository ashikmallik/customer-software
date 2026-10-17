<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;


if( isset($_POST['change_status'])){
    $approved_by = $_POST['approved_by'];
    $approved_date = $_POST['approved_date'];
    $acc_id = $_POST['account_id'];
    $tbl_acc_id = $obj->details_by_cond("tbl_account","acc_id='$acc_id'");
    $status = $_POST['status'];
    if (isset($status)) {
        $obj->Update_data("tbl_account", ['status' => $status,'acc_amount' =>$tbl_acc_id['temp_amount'],'temp_amount' => $tbl_acc_id['acc_amount'],'approved_by' => $approved_by,'approved_date' => $approved_date ], "acc_id=" . intval($acc_id));
    }
    
}

$firsDayOfMonth = new DateTime('first day of this month');

if (isset($_POST['search'])) {
    $dateform = date('Y-m-d', strtotime($_POST['dateform']));
    $dateto = date('Y-m-d', strtotime($_POST['dateto']));
    $expenseDetails = $obj->view_all_by_cond("vw_account", "entry_date BETWEEN '" . date('Y-m-d', strtotime($dateform)) . "' and '" . date('Y-m-d', strtotime($dateto)) . "' AND acc_type='1' ORDER BY entry_date DESC");

} else {

    $dateform = $firsDayOfMonth->format('Y-m-d');;
    $dateto = date('Y-m-d');
    $expenseDetails = $obj->view_all_by_cond("vw_account", "MONTH(entry_date)='" . date('m') . "' and YEAR(entry_date)='" . date('Y') . "'  AND acc_type='1' ORDER BY entry_date DESC");
}
$previewDate = date('d M Y', strtotime($dateform)) . ' to ' . date('d M Y', strtotime($dateto));

if( $obj->userWorkPermission($acc,'delete')){

    if (isset($_GET['dltoken']) && !empty($_GET['dltoken'])) {
        $dltoken = $_GET['dltoken'];

        if ($obj->Delete_data("tbl_account", "acc_id='$dltoken'")) {

            $obj->notificationStore('Delete Successfull', 'success');
        } else {

            $obj->notificationStore('Sorry ! Delete Failed. Try Again');
        }

        echo '<script> window.location = "?q=view_expense"; </script>';
    }
}


// ========== Delete Function End =================

?>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">

<div class="row bg-slate-700">
    <div class="col-md-6">
        <h4 id="preview_date">View Expense Information of <?php echo $previewDate; ?></h4>
    </div>
    <div class="col-md-6 " style="margin-top:5px;">
        <?php if ($ty == 'SA' OR $ty == 'EO') { ?>
            <a class="btn btn-primary pull-right btn-sm" href="?q=add_expense">ADD NEW EXPENSE <span
                        class="glyphicon glyphicon-plus"></span></a>
        <?php } ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?php $obj->notificationShowRedirect(); ?>
    </div>
</div>
<hr>
<div class="row bg-slate-700" style="padding:3px;">
    <div class="col-md-12">
        <form action="" method="POST">
            <div class="col-md-5">
                <div class="form-group">
                    <label class="control-label col-sm-4" style="padding-top:5px" for="dateMonth">Form Date : </label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control datepicker"
                               value="<?php echo date('d-m-Y', strtotime($dateform)); ?>" placeholder="Date"
                               name="dateform"
                               id="new_flight_date" required>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label class="control-label col-sm-4" style="padding-top:5px" for="damageRate">To Date : </label>
                    <div class="col-sm-8">
                        <input style="color: black;" id="old_flight_date"
                               value="<?php echo date('d-m-Y', strtotime($dateto)); ?>" type="text"
                               class="form-control datepicker"
                               placeholder="Date" name="dateto" required>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <input type="submit" name="search" class="btn btn-primary" value="Search">
            </div>
        </form>
    </div>
</div>
<hr>
<div class="row" style="padding:10px; font-size: 12px;">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped" id="datatable_view_expense">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Head</th>
                    <th>Panding Amount</th>
                    <th>Description</th>
                    <th>Last Updated</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <?php
                $i = '0';
                $totalExpense = 0;
                foreach ($expenseDetails as $value) {
                    $i++;
                    $totalExpense += intval($value['temp_amount']);
                    $accc_id = $value['acc_id'];
                    $tbl_acc_id = $obj->details_by_cond("tbl_account","acc_id='$accc_id'");
                    ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo date("d-m-Y", strtotime(isset($value['entry_date']) ? $value['entry_date'] : "2016-02-1")); ?></td>
                        <td><?php echo isset($value['acc_name']) ? $value['acc_name'] : NULL; ?></td>
                        <td><?php echo isset($value['temp_amount']) ? $value['temp_amount'] : NULL; ?></td>
                        <td><?php echo isset($value['acc_description']) ? $value['acc_description'] : NULL; ?></td>
                        <td>
                            <?php
                             if( date('Y-m-d', strtotime($value['entry_date'])) ==  date('Y-m-d', strtotime($value['last_update']))){
                                 echo "Same Date";
                             }else{
                                 echo 'Updated at '.date('d-m-Y', strtotime($value['last_update']));
                             }
                            ?>
                        </td>
                        <td class="text-center"> <?php 
                            if ($value['status']==0){echo '<buttton class="btn-warning btn-xs">Pending</buttton>';}
                            elseif ($value['status']==1){echo '<buttton class="btn-warning btn-xs">Apporobed</buttton>';}
                            ?> </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <?php if( $obj->userWorkPermission($acc,'edit')){ ?>
                                    <button class="edit btn btn-success btn-xs change-status" data-toggle="modal" data-target="#change-status"
                                data-id="<?php echo $value['acc_id'];?>">Change Status
                            </button>
                                    
                                <?php };
                                if( $obj->userWorkPermission($acc,'delete')){ ?>
                                    
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tfoot>
                    <tr>
                        <th colspan="3">Total Expense</th>
                        <th colspan="4"><?php echo $totalExpense; ?> tk</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>



<div class="modal fade" id="change-status" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Change Status</h4>
            </div>

            <form id="editDueForm" action="" method="post" class="form-horizontal">

                <div class="modal-body">

                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-5 control-label">Change Status</label>
                        <div class="col-sm-7">
                            <div class="form-group">
                                <select class="form-control" name="status">
                                    <option value="0">Pending</option>
                                    <option value="1">Approved</option>
                                    
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-5 control-label">Approved date</label>
                        <div class="col-sm-7">
                            <div class="form-group">
                                <input type="date" name="approved_date" value="">
                            </div>
                            
                        </div>
                    </div>
                    <input type="hidden" name="account_id" value="">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-5 control-label">Approved by</label>
                        <div class="col-sm-7">
                            <div class="form-group">
                                <select class="form-control" name="approved_by">
                                <?php foreach ($obj->view_all('_createuser') as $singleUser) { ?>
                            <option
                                    value="<?php echo $singleUser['UserId'] ?>"><?php echo $singleUser['FullName'] ?></option>
                        <?php }// foreach ?>
                    </select>
                            </div>
                            
                        </div>
                    </div>

                </div>
                <div class="modal-footer text-center">
                    <button type="submit" name="change_status" class="btn btn-primary">Change Status</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>


    $(document).ready(function () {

        $('.change-status').on('click',function () {
            var account_id = $(this).data('id');
            console.log(account_id);

            $('input[name="account_id"]').val(account_id);
        });


        var title = $('h4#preview_date').html();
        var table = $('#datatable_view_expense').DataTable({
            <?php if($obj->hasPermission($ty, 'print_expenses')){ ?>
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'print',
                    text: 'Print Expense',
                    exportOptions: {
                        columns: [0,1,2,3,4]
                    },
                    title: function () {
                        return title
                    },
                    customize: function (win) {
                        $(win.document.body).css('font-size', '12px');
                        $(win.document.body).find('h1').addClass('text-center').css('font-size', '20px');
                        $(win.document.body).find('table').addClass('container').css('font-size', 'inherit');
                        $(win.document.body).find('table').removeClass('table-bordered');
                    }
                }
            ],
        <?php } ?>
        });


        $('input[name="dateform"]').datepicker({
            autoclose: true,
            toggleActive: true,
            format: 'dd-mm-yyyy'
        });

        $('input[name="dateto"]').datepicker({
            autoclose: true,
            toggleActive: true,
            format: 'dd-mm-yyyy'
        });
    });

</script>

<script type="application/javascript"
        src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script type="application/javascript"
        src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
