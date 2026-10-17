<?php

date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;


//===================Add Function===================

if (isset ($_POST['submit'])) {
    extract($_POST);

    $form_data = array(

        'acc_head' => $acc_id,
        'acc_type' => '2',
        'acc_amount' => $amount,
        'acc_description' => str_replace("'", "", $Details),

        'entry_by' => $userid,
        'entry_date' => $date_time,
        'update_by' => $userid
    );
    $service_add = $obj->Reg_user_cond("tbl_account", $form_data, " ");

    if ($service_add) {
        ?>
        <script>
            window.location = "?q=view_income";
        </script>
        <?php
    } else {
        echo $notification = 'Insert Failed';
    }
}
?>

<!--===================end Function===================-->
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

</script>

<div class="col-md-12"
     style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
    <b><?php echo isset($notification) ? $notification : NULL; ?></b>
</div>
<div class="row" style="padding:10px; font-size: 12px;">
    <form role="form" enctype="multipart/form-data" method="post">
        <div class="col-md-6 col-md-offset-3">

            <div class="form-group">
                <label>Account Head</label>
                <select class="form-control" required="required" name="acc_id" id="status">
                    <option></option>
                    <?php
                    $i = '0';
                    foreach ($obj->view_all("tbl_accounts_head") as $value) {
                        $i++;
                        ?>
                        <option value="<?php echo isset($value['acc_id']) ? $value['acc_id'] : NULL; ?>"><?php echo isset($value['acc_name']) ? $value['acc_name'] : NULL; ?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">

                <label>Amount</label>
                <input onkeypress="return numbersOnly(event)" required="required" type="text" name="amount"
                       class="form-control" id="ResponsiveTitle">
            </div>
            <div class="form-group">

                <label>Account Details</label>
                <textarea class="form-control" name="Details" id="ResponsiveDetelis" rows="6"></textarea>
            </div>

            <div class="text-center">
                <input type="submit" class="btn btn-success" name="submit" value="Submit">
            </div>

        </div>

    </form>
</div>
<hr>
<script>
    $(document).ready(function () {
        $('select[name="acc_id"]').select2({
            placeholder: "Select a Income",
            allowClear: true,
        });
    });
</script>