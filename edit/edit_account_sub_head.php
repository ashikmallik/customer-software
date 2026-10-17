<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$token = isset($_GET['token']) ? $_GET['token'] : NULL;

$details = $obj->details_by_cond(" tbl_accounts_sub_head", "acc_id='$token'");

extract($details);

if (isset($_POST['update'])) {
    extract($_POST);

    $form_data = array(
        'acc_head_id'=>$account_head,
         'acc_sub_head_name' => $Name,
        'acc_desc' => str_replace("'", "", $Details),
        'acc_status' => $status,

        'entry_by' => $userid,
        'entry_date' => $date_time,
        'update_by' => $userid
    );
    $branch_id = $obj->Update_data("tbl_accounts_sub_head", $form_data, "where acc_id='$token'");

    if ($branch_id) {

        ?>
        <script>
            window.location = "?q=view_account_sub_head";
        </script>
        <?php
    } else {
        echo $notification = 'Update Failed';
    }
}
?>

<div class="col-md-12"
     style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
    <b><?php echo isset($notification) ? $notification : NULL; ?></b>
</div>
<div class="row" style="padding:10px; font-size: 12px;">
    <form role="form" enctype="multipart/form-data" method="post">
        <div class="row" style="padding:10px; font-size: 12px;">
            <div class="col-md-6 col-md-offset-3">
                <div class="form-group">                                                         
                    <label>Account Head</label>
                
                    <select class="form-control" required="required" name="account_head" id="status">
                        <option value="">Select One</option>
                        <?php foreach($obj->view_all("tbl_accounts_head") as $item){ ?>
                            <option value="<?php echo $item['acc_id']; ?>"
                                <?php 
                                    if($details['acc_head_id'] == $item['acc_id']){
                                        echo "selected";
                                    }
                                ?>
                            >
                                <?php echo $item['acc_name']; ?>
                            </option>
                
                        <?php } ?>
                    </select>                       
                </div>
                <div class="form-group">
                    <label>Account sub Head Name</label>
                    <input value="<?php echo $details['acc_sub_head_name'] ? $details['acc_sub_head_name'] : NULL; ?>" type="text"
                           name="Name" class="form-control" id="ResponsiveTitle">
                </div>
                <!--<div class="form-group">-->
                <!--    <label>Account Head Type</label>-->
                <!--    <select class="form-control" required="required" name="type" id="status">-->
                <!--        <option <?php if ($details['acc_type'] == '1') echo 'selected'; ?> value="1">Expense</option>-->
                <!--        <option <?php if ($details['acc_type'] == '2') echo 'selected' ?> value="2">Income</option>-->
                <!--    </select>-->
                <!--</div>-->
                <div class="form-group">
                    <label>Account Sub Head Details</label>
                    <textarea class="form-control" name="Details" id="ResponsiveDetelis"
                              rows="6"><?php echo $details['acc_desc'] ? $details['acc_desc'] : NULL; ?></textarea>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" required="required" name="status" id="status">
                        <option <?php if ($details['acc_status'] == '1') echo 'selected'; ?> value="1">Active</option>
                        <option <?php if ($details['acc_status'] == '0') echo 'selected' ?> value="0">Inactive</option>
                    </select>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-success" name="update">Submit</button>
                </div>
            </div>

        </div>
    </form>
</div>
<hr>