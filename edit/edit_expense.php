<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$token = isset($_GET['token']) ? $_GET['token'] : NULL;

$expense = $obj->details_by_cond("tbl_account", "acc_id='$token'");

if (isset($_POST['update'])) {
    extract($_POST);

    $form_data = array(

        'acc_head' => $acc_id,
        'acc_sub_head_id'=>$acc_sub_head_id,
        'date' => $date,
        'vouchar_number' => $vouchar_number,
        'payment_method' => $payment_method,
        'acc_type' => '1',
        'acc_amount' => $amount,
        'acc_description' => str_replace("'", "", $expense_details),
        'entry_by' => $userid,
        'update_by' => $userid

    );
    $expense_update = $obj->Update_data("tbl_account", $form_data, "where acc_id='$token'");

    if ($expense_update) {
        ?>
        <script>
             window.location = "?q=view_expense";
        </script>
        <?php
    } else {
        echo $notification = 'Update Failed';
    }
}
?>
<!--===================end Function===================-->

<div class="row" >
    <div class="col-md-12 bg-slate-700">
        <h4>Edit Expense</h4>
    </div>
</div>
<div class="col-md-12"
     style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
    <b><?php echo isset($notification) ? $notification : NULL; ?></b>
</div>

<hr>
<div class="row" style="padding:10px; font-size: 12px;">
    <form role="form" enctype="multipart/form-data" method="post">

        <div class="row">
            <div class="col-md-6 col-md-offset-3">
            <div class="form-group">
                    <label>Expance Date</label>
                    <input value="<?php echo $expense['date'] ? $expense['date'] : NULL; ?>" required="required" type="date" name="date"
                           class="form-control" id="ResponsiveTitle">
                </div>
                <div class="form-group">
                    <label>Vouchar Number </label>
                    <input " required="required" type="text" value="<?php echo $expense['vouchar_number'] ? $expense['vouchar_number'] : NULL; ?>" required="required" type="date" name="vouchar_number"
                           class="form-control" id="ResponsiveTitle">
                </div>
                <div class="form-group">
                    <label>Payment method </label>
                    <select class="form-control" required="required" name="payment_method" id="status">
                        <option >Select payment method</option>
                        
                          
                            <option <?php if ($expense['payment_method'] == $expense['payment_method']) echo 'selected' ?> value="<?php echo isset($expense['payment_method']) ? $expense['payment_method'] : NULL; ?>"><?php echo isset($expense['payment_method']) ? $expense['payment_method'] : NULL ;?></option>
                            
                            
                    
                    </select>
                </div>
                <div class="form-group">
                    <label>Account Head</label>
                    <select class="form-control" required="required" name="acc_id" id="status">
                        <option value="">select</option>
                        <?php
                        $i = '0';
                        foreach ($obj->view_all("tbl_accounts_head") as $value) {
                            $i++;
                            ?>
                            <option <?php if ($expense['acc_head'] == $value['acc_id']) echo 'selected' ?> value="<?php echo isset($value['acc_id']) ? $value['acc_id'] : NULL; ?>"><?php echo isset($value['acc_name']) ? $value['acc_name'] : NULL; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Account Sub Head</label>
                    <select class="form-control" required="required" name="acc_sub_head_id" id="">
                        <option value="">select</option>
                        <?php
                        $i = '0';
                        foreach ($obj->view_all("tbl_accounts_sub_head") as $value) {
                            $i++;
                            ?>
                            <option <?php if ($expense['acc_sub_head_id'] == $value['acc_id']) echo 'selected' ?> value="<?php echo isset($value['acc_id']) ? $value['acc_id'] : NULL; ?>"><?php echo isset($value['acc_sub_head_name']) ? $value['acc_sub_head_name'] : NULL; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Amount</label>
                    <input value="<?php echo $expense['acc_amount'] ? $expense['acc_amount'] : NULL; ?>" type="text"
                           name="amount" class="form-control" id="ResponsiveTitle">
                </div>
                <div class="form-group">
                    <label>Account Details</label>
                    <textarea class="form-control" name="expense_details" rows="6"><?php echo $expense['acc_description'] ? $expense['acc_description'] : NULL; ?></textarea>
                </div>

                <div class="form-group text-center">
                    <input type="submit" class="btn btn-success" name="update" value="Update"/>
                </div>

            </div>
            <div class="col-md-6"></div>
        </div>

        <div class="row" style="padding: 5px 0px 15px 25px; font-size: 12px;">

        </div>
    </form>
</div>
<hr></hr>