<?php

date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

$customers = $obj->view_all_by_cond("tbl_agent", "ag_status=1");
$templates = $obj->view_all("tbl_complain_templates");
$employees = $obj->view_all_by_cond('tbl_employee','employee_status=1');

if (isset ($_POST['submit'])) {

    if (isset($_POST['sms_check'])){
        $sms_check_cus = $_POST['sms_check'];
        $sms_check_emp = $_POST['sms_check'];
    }

    $employee_id         = $_POST['employee_id'];
    $solve_employee_info = $obj->details_by_cond('tbl_employee','id='.$employee_id);
    $employee_mobile     = $solve_employee_info['employee_mobile_no'];

    $customer_mobile     = $_POST['customer_mobile'];
    $customer_name       = $_POST['customer_name'];
    $customer_address    = $_POST['customer_address'];

    $complain            = $_POST['details'];
    $complain_date       = $_POST['complain_date'];
    $customer_id         = "NAG".$customer_mobile;




    $form_complain_date = array(
        'details'=> $_POST['details'],
        'note'=> $_POST['note'],
        'customer_id'=> $customer_id,
        'customer_name'=> $customer_name,
        'customer_address'=> $customer_address,
        'customer_mobile'=> $customer_mobile,
        'status'=> 1,
        'solve_by'=> $_POST['employee_id'],
        'entry_by'=> $userid,
        'update_by'=> $userid,
        'complain_date'=> date('Y-m-d H:i:s', strtotime($_POST['complain_date'].' '.$_POST['complain_time'])),
        'entry_date'=> date('Y-m-d h:i:s')
    );

    $obj->Insert_data('tbl_complains_new_user',$form_complain_date);
    
    $smsbody_cus = "Welcome to ".$obj->getSettingValue('sms', 'company_name'). ". Cust.Name : ".$customer_name. ", Complain : ".$complain." on has been noted and will be solved as very soon. Assigned to Support - ".$employee_mobile.". Thank you.";


    if (isset($sms_check_cus)) { // sms will not send if the sms notification checkbox is unchecked

        $mobilenum="88".$customer_mobile;
        $obj->sendsms($smsbody_cus, $mobilenum);

    }

    //for employee who will solve
    $smsbody_emp = $obj->getSettingValue('sms', 'company_name'). " Support. Cust.Name : $customer_ip, Complain : ".$complain." has been taken. Customer Mobile : ".$customer_mobile.". Thanks.";


    if (isset($sms_check_emp)) { // sms will not send if the sms notification checkbox is unchecked

        $mobilenum="88".$employee_mobile;
        $obj->sendsms($smsbody_emp, $mobilenum);

    }
    
    $obj->notificationStore('Customer Complain Added.','success');
    echo '<script> window.location="?q=add_complain_imminent_user"; </script>';
}
?>


<div class="row">
    <div class="col-md-12 bg-teal-800">
        <h4>Add Imminent Customers Complane</h4>
    </div>

</div>

<div class="col-md-12"
style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
<b><?php echo isset($notification) ? $notification : NULL; ?></b>
</div>
<?php $obj->notificationShowRedirect();?>

<div class="row">
    <h4 class="text-center">Add Imminent Customers Complain</h4>
    <form role="form" enctype="multipart/form-data" method="post">
        <div class="col-md-6 col-md-offset-3">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label> Complain Date</label>
                        <input type="text" name="complain_date" class="form-control" autocomplete="off" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label> Complain Time</label>
                        <input type="text" value="<?php echo date("h:i:s A");?>" name="complain_time" class="form-control" autocomplete="off">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Customer Name</label>
                <input type="text" name="customer_name" class="form-control" autocomplete="off" placeholder="Name" required>
            </div>

            <div class="form-group">
                <label>Customer Address</label>
                <input type="text" name="customer_address" class="form-control" autocomplete="off" placeholder="Address">
            </div>

             <div class="form-group">
                <label>Customer Cell No.</label>
                <input type="text" name="customer_mobile" class="form-control" autocomplete="off" placeholder="Ex: 01XXXXXXXXX" pattern="[0-9]{11}"/>
            </div>

            <div class="form-group">
                <label>Complain Template</label>
                <select name="template" class="form-control select2" required>
                    <option value="">Select Template</option>
                    <?php foreach ($templates as $template) {?>
                        <option><?= $template['template'];?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label>Complain Details</label>
                <textarea class="form-control" name="details" id="complain-details" rows="6"></textarea>
            </div>

            <div class="form-group">
                <label>Note</label>
                <textarea class="form-control" name="note" id="complain-details" rows="6"></textarea>
            </div>

            <div class="form-group">
                <label>Employee For Solve</label>
                <select name="employee_id" class="form-control select2">
                    <option value="">Select Employee</option>
                    <?php foreach ($employees as $employee) {?>
                        <option value="<?= $employee['id'];?>"><?= $employee['employee_name'];?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="sms_check" checked value="1"><b>SMS Notification</b>
                    </label>
                </div>
            </div>

            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary" name="submit">Add Complaine</button>
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
    $('input[name="complain_time"]').timepicker();
</script>

