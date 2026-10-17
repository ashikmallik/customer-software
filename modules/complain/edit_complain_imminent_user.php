<?php

date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$templates = $obj->view_all("tbl_complain_templates");
$employees = $obj->view_all_by_cond('tbl_employee','employee_status=1');
$complain = $obj->details_by_cond('tbl_complains_new_user','id='.$_GET['id']);

if (isset ($_POST['submit'])) {

    $form_complain_date = array(
            'details'=> $_POST['details'],
            'note'=> $_POST['note'],
            'customer_id'=> $_POST['customer_id'],
            'solve_by'=> $_POST['employee_id'],
            'update_by'=> $userid,
            'complain_date'=> date('Y-m-d H:i:s', strtotime($_POST['complain_date'].' '.$_POST['complain_time'])),
            'solve_date'=> date('Y-m-d H:i:s', strtotime($_POST['solve_date'].' '.$_POST['solve_time'])),
    );

    $obj->Update_data('tbl_complains', $form_complain_date,'id='.$complain['id']);
    $obj->notificationStore('Customer Complain Edited.','success');

    echo '<script> window.location="?q=edit_complain&id='.$complain['id'].'"; </script>';
}
?>


<div class="row">
    <div class="col-md-12 bg-teal-800">
        <h4>Edit Customer Complane of <?= $complain['customer_name']?></h4>
    </div>
</div>

<div class="col-md-12"
     style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
    <b><?php echo isset($notification) ? $notification : NULL; ?></b>
</div>
<?php $obj->notificationShowRedirect();?>

<div class="row">
    <h4 class="text-center">Add Complain</h4>
    <form role="form" enctype="multipart/form-data" method="post">
        <div class="col-md-6 col-md-offset-3">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label> Complain Date</label>
                        <input type="text" name="complain_date" value="<?= date('d-m-Y',strtotime($complain['complain_date']))?>" class="form-control" autocomplete="off">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label> Complain Time</label>
                        <input type="text" name="complain_time" value="<?= date('h:i A',strtotime($complain['complain_date']))?>" class="form-control" autocomplete="off">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Complain Details</label>
                <textarea class="form-control" name="details" id="complain-details" rows="6"><?= $complain['details']?></textarea>
            </div>

            <div class="form-group">
                <label>Note</label>
                <textarea class="form-control" name="note" id="complain-details" rows="6"><?= $complain['note']?></textarea>
            </div>

            <div class="form-group">
                <label>Employee For Solve</label>
                <select name="employee_id" class="form-control select2">
                    <option value="">Select Employee</option>
                    <?php foreach ($employees as $employee) {?>
                        <option value="<?= $employee['id'];?>" <?php
                        if ($employee['id']=$complain['solve_by']) {echo 'selected';}
                        ?>><?= $employee['employee_name'];?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label> Solve Date</label>
                        <input type="text" name="solve_date" value="<?= $complain['solve_date']!='0000-00-00 00:00:00'?date('d-m-Y',strtotime($complain['solve_date'])):'';?>" class="form-control" autocomplete="off">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label> Solve Time</label>
                        <input type="text" name="solve_time" value="<?= $complain['solve_date']!='0000-00-00 00:00:00'?date('h:i A',strtotime($complain['solve_date'])):'';?>" class="form-control" autocomplete="off">
                    </div>
                </div>
            </div>

            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary" name="submit">Edit Complaine</button>
            </div>
        </div>

    </form>
</div>
<hr>

<script>
    $('select[name="customer_id"]').select2({
        placeholder: "Select",
        allowClear: true,
    });

    $('select[name="template"]').on('change',function () {
        var tempalte = $(this).val();
        $('#complain-details').html(tempalte);
    });

    $('input[name="complain_date"]').datepicker({
        autoclose: true,
        toggleActive: true,
        format: 'dd-mm-yyyy'
    });

    $('input[name="solve_date"]').datepicker({
        autoclose: true,
        toggleActive: true,
        format: 'dd-mm-yyyy'
    });
    $('input[name="complain_time"]').timepicker();
    $('input[name="solve_time"]').timepicker();
</script>