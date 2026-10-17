<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$token = isset($_GET['token']) ? $_GET['token'] : NULL;

$details = $obj->details_by_cond("tbl_account", "acc_id='$token'");

extract($details);

if (isset($_POST['update'])) {
    extract($_POST);

    $form_data = array(

        'acc_head' => !empty($acc_id) ? $acc_id : 0 ,
        'acc_type' => '2',
        'acc_amount' => $amount,
        'acc_description' => str_replace("'", "", $Details),
        'entry_by' => $userid,
        'entry_date' => $date_time,
        'update_by' => $userid

    );
    $branch_id = $obj->Update_data("tbl_account", $form_data, "where acc_id='$token'");

    if ($branch_id) {

        ?>
        <script>
            window.location = "?q=view_income";
        </script>
        <?php
    } else {
        echo $notification = 'Update Failed';
    }
}
?>
<!--===================end Function===================-->


<div class="col-md-12"
     style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
    <b><?php echo isset($notification) ? $notification : NULL; ?></b>
</div>
<div class="row">
    <div class="col-md-12 bg-slate-800">
        <h4><strong>Edit Other Income</strong></h4>
    </div>
</div>
<div class="row" style="padding:10px; font-size: 12px;">
    <form role="form" enctype="multipart/form-data" method="post">

            <div class="col-md-6 col-md-offset-3">

                <div class="form-group">
                    <label>Account Head</label>
                    <select class="form-control" name="acc_id" id="status">
                        <option></option>
                        <?php
                        $i = '0';
                        foreach ($obj->view_all("tbl_accounts_head") as $value) {
                            $i++;
                            ?>
                            <option <?php if ($details['acc_head'] == $value['acc_id']) echo 'selected' ?> value="<?php echo isset($value['acc_id']) ? $value['acc_id'] : NULL; ?>"><?php echo isset($value['acc_name']) ? $value['acc_name'] : NULL; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Amount</label>
                    <input value="<?php echo $details['acc_amount'] ? $details['acc_amount'] : NULL; ?>" type="text"
                           name="amount" class="form-control" id="ResponsiveTitle">
                </div>
                <div class="form-group">
                    <label>Account Details</label>
                    <textarea class="form-control" name="Details" id="ResponsiveDetelis"
                              rows="6"><?php echo $details['acc_description'] ? $details['acc_description'] : NULL; ?></textarea>
                </div>

                <div class="text-center form-group">
                    <button type="submit" class="btn btn-success" name="update">Update</button>
                </div>
            </div>


    </form>
</div>
<hr></hr>