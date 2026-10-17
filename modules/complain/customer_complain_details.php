<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

$customer = $obj->details_by_cond('tbl_agent','ag_id='.$_GET['id']);

$complains = $obj->view_all_by_cond('tbl_complains','customer_id='.$_GET['id'].' ORDER BY id DESC');

if( isset($_POST['change_status']) ){
    $obj->Update_data('tbl_complains', ['status'=>$_POST['status'],'note'=>$_POST['complain_note']],'id='.$_POST['complain_id']);
    $obj->notificationStore('Status Changed','success');
    echo '<script> window.location="?q=view_complains"; </script>';
}

?>
<div class="content-wrapper">
    <section class="content-header">
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">View All Complains Of Customer <?= $customer['ag_name']?></h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <?php $obj->notificationShowRedirect(); ?>
                    <div class="box-body">
                        <div class="row" style="font-size:12px">
                            <div class="col-md-12">
                                <table class="table table-responsive table-bordered table-hover " id="datatable">
                                    <thead>
                                    <tr class="bg-slate-800">
                                        <th class="col-md-1">SL</th>
                                        <th class="col-md-2">Complain Details</th>
                                        <th class="col-md-2">Complain Date</th>
                                        <th class="col-md-1">Solve By</th>
                                        <th class="col-md-1">Solve Date</th>
                                        <th class="col-md-1">Note</th>
                                        <th class="col-md-1">Status</th>
                                        <th class="col-md-1">Entry Date</th>
                                        <th class="col-md-1">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i = 0;
                                    foreach ($complains as $complain) {
                                        $customer = $obj->details_by_cond('tbl_agent','ag_id='.$complain['customer_id']);
                                        $employee = $obj->details_by_cond('tbl_employee','id='.$complain['solve_by']);
                                        ?>
                                        <tr>
                                            <td class="text-center"> <?php echo ++$i ?> </td>
                                            <td class="text-center"> <?php echo $complain['details'] ?> </td>
                                            <td class="text-center"> <?php echo date('h:i A',strtotime($complain['complain_date'])) ?> <br> <?php echo date('d, M Y',strtotime($complain['complain_date'])) ?></td>
                                            <td class="text-center"> <?= !empty($employee['employee_name']) ? $employee['employee_name']:'N/A';?></td>
                                            <td class="text-center"> <?php if ($complain['solve_date']!='0000-00-00 00:00:00'){echo date('h:i A', strtotime($complain['solve_date'])).'<br>'.date('d, M Y', strtotime($complain['solve_date']));} ?> </td>
                                            <td class="text-center"> <?php echo $complain['note'] ?> </td>
                                            <td class="text-center"> <?php
                                                if ($complain['status']==1){echo '<buttton class="btn-warning btn-xs">Pending</buttton>';}
                                                elseif ($complain['status']==2){echo '<buttton class="btn-warning btn-xs">Reviewed</buttton>';}
                                                elseif ($complain['status']==3){echo '<buttton class="btn-success btn-xs">Solved</buttton>';}
                                                elseif ($complain['status']==4){echo '<buttton class="btn-danger btn-xs">Not Solved</buttton>';}
                                                ?> </td>
                                            <td class="text-center"> <?php echo date('d, M Y', strtotime($complain['entry_date'])) ?> </td>
                                            <td class="text-center action-btn">
                                                <button class="edit btn btn-success btn-xs change-status" data-toggle="modal" data-target="#change-status" data-id="<?php echo $complain['id'] ?>" data-note="<?php echo $complain['note'] ?>">Change Status</button>
                                                <a href="?q=edit_complain&id=<?= $complain['id']?>" class="edit btn btn-success btn-xs change-status">Edit</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <hr>
                    </div>
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
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
                        <label for="inputEmail3" class="col-sm-3 control-label">Change Status</label>
                        <div class="col-sm-7">
                            <div class="form-group">
                                <select class="form-control" name="status">
                                    <option value="">Select</option>
                                    <option value="1">Pending</option>
                                    <option value="2">Reviewed</option>
                                    <option value="3">Solved</option>
                                    <option value="4">Not Solved</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">Status Note</label>
                        <div class="col-sm-7">
                            <div class="form-group">
                                <textarea name="complain_note" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="complain_id" value="">

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

        $('.change-status').on('click',function () {
            var complain_id = $(this).data('id');
            var note = $(this).data('note');

            $('input[name="complain_id"]').val(complain_id);
            $('textarea[name="complain_note"]').html(note);
        })

    });
</script>