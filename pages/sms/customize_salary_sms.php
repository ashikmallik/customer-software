<?php

date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : NULL;

// SMS on/off column (auto add if not exists)
if (!$obj->rawSqlSingle("SHOW COLUMNS FROM sms LIKE 'sms_enable'")) {
    try {
        $obj->rawSql("ALTER TABLE sms ADD sms_enable TINYINT(1) NOT NULL DEFAULT 1");
    } catch (Exception $e) {
    }
}

$value = $obj->details_by_cond("sms", "status='10'");
$smsEnable = isset($value['sms_enable']) ? $value['sms_enable'] : 1;
if (isset($_POST['submit'])) {
    extract($_POST);
    $form_data_sms = array(
        'smsbody' => $sms_body,
        'smshead' => 'salary_sms',
        'sms_enable' => isset($_POST['sms_enable']) ? 1 : 0
    );

    if (isset($value['status']) && $value['status'] == 10) {
        $smsRow = $obj->Update_data("sms", $form_data_sms, " status='10' ");
    } else {
        $form_data_sms['status'] = 10;
        $smsRow = $obj->insertData("sms", $form_data_sms);
    }

    if ($smsRow) {
        echo '<script> window.location="?page=customize_salary_sms"; </script>';
    } else {
        echo $notification = 'Insert Failed';
    }
}
?>

<!--===================end Function===================-->

<div class="d-flex justify-content-center flex-wrap gap-4">
    <!-- Salary SMS Section -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <!-- Form Wizard Start -->
                <div class="form-wizard">
                    <fieldset class="wizard-fieldset show">
                        <div class="text-center">
                            <div class="col-md-12 bg-teal-800">
                                <h6 class="text-md text-neutral-500">Customize Salary Paid SMS</h6>
                            </div>
                            <hr>
                            <b><?php echo isset($notification) ? $notification : NULL; ?></b>
                        </div>

                        <form style="padding: 20px;" role="form" enctype="multipart/form-data" method="post" class="d-flex flex-column align-items-center w-100">
                            <div class="mb-3">
                                <strong style="font-size: 14px" class="badge bg-primary" title="Employee Name">{EMPLOYEE_NAME}</strong>
                                <strong style="font-size: 14px" class="badge bg-success" title="Employee ID">{EMPLOYEE_ID}</strong>
                                <strong style="font-size: 14px" class="badge bg-secondary" title="Employee Mobile No">{MOBILE_NO}</strong>
                                <strong style="font-size: 14px" class="badge bg-info" title="Employee Email">{EMAIL}</strong>
                                <strong style="font-size: 14px" class="badge bg-warning" title="Employee Address">{ADDRESS}</strong>
                                <strong style="font-size: 14px" class="badge bg-success" title="Amount Paid Now">{PAID_AMOUNT}</strong>
                                <strong style="font-size: 14px" class="badge bg-primary" title="Conveyance Amount Now">{CONVEYANCE}</strong>
                                <strong style="font-size: 14px" class="badge bg-danger" title="Punishment Amount Now">{PUNISHMENT}</strong>
                                <strong style="font-size: 14px" class="badge bg-secondary" title="Payment Description">{DESCRIPTION}</strong>
                                <strong style="font-size: 14px" class="badge bg-warning" title="Total Salary (all time)">{TOTAL_SALARY}</strong>
                                <strong style="font-size: 14px" class="badge bg-success" title="Total Received (all time)">{TOTAL_PAID}</strong>
                                <strong style="font-size: 14px" class="badge bg-danger" title="Total Punishment (all time)">{TOTAL_PUNISHMENT}</strong>
                                <strong style="font-size: 14px" class="badge bg-danger" title="Remaining Due (negative = advance)">{DUE_AMOUNT}</strong>
                                <strong style="font-size: 14px" class="badge bg-info">{PAID_DAY}</strong>
                                <strong style="font-size: 14px" class="badge bg-success">{PAID_MONTH}</strong>
                                <strong style="font-size: 14px" class="badge bg-warning">{PAID_YEAR}</strong>
                                <strong style="font-size: 14px" class="badge bg-primary" title="Y-m-d">{PAID_DATE}</strong>
                            </div>

                            <div class="form-check form-switch w-100 mb-3">
                                <input class="form-check-input" type="checkbox" role="switch" name="sms_enable" id="sms_enable" value="1" <?php echo $smsEnable == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="sms_enable" style="font-size:14px">Send SMS when salary is paid</label>
                            </div>

                            <div class="form-group w-100">
                                <label style="font-size:14px">Salary SMS Details</label>
                                <textarea class="form-control" onkeyup="countCharB(this)" name="sms_body" id="ResponsiveDetelis"
                                    rows="4" style="width: 100%;"><?php echo isset($value['smsbody']) ? $value['smsbody'] : NULL; ?></textarea>
                                <div><span id="charNumB"></span></div>
                            </div>

                            <button type="submit" class="btn btn-success mt-2 text-center" name="submit">SAVE SMS</button>
                        </form>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function countCharB(val) {
        var len = val.value.length;
        if (len >= 900) {
            val.value = val.value.substring(0, 900);
        } else {
            $('#charNumB').text(900 - len);
        }
    };
</script>