<?php
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

$token = isset($_GET['token']) ? $_GET['token'] : NULL;

$user = $obj->details_by_cond('vw_user_info', "UserId='$token'");

if (isset($_POST['update'])) {

    if (!empty($_FILES["user_photo"]["name"])) {
        $seed = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        $rand = array_rand($seed, 6);
        $convert = array_map(function ($n) {
            global $seed;
            return $seed[$n];
        }, $rand);
        $character = implode('', $convert);

        $seed = str_split('1234567890');
        $rand = array_rand($seed, 4);
        $convert = array_map(function ($n) {
            global $seed;
            return $seed[$n];
        }, $rand);
        $digit = implode('', $convert);

        $rend_code = "BSTL" . "$character" . "$digit";

        $nep = "asset/userphoto/" . $rend_code . ".jpg";

        if ($_FILES["user_photo"]["name"]) {

            if (copy($_FILES["user_photo"]["tmp_name"], $nep)) {

                $photo_path = "asset/userphoto/" . $rend_code . ".jpg";
            }
        }
        $user_photo_path = isset($photo_path) ? $photo_path : NULL;
    } else {
        $user_photo_path = isset($_POST['ph_path']) ? $_POST['ph_path'] : NULL;
    }

    extract($_POST);
    $form_data_create_user = array(
        'FullName' => $full_name,
        'UserName' => $user_name,
        'Email' => $email,
        'MobileNo' => $mobile_no,
        'Address' => $address,
        'PhotoPath' => $user_photo_path,
        'UpdateBy' => $userid
    );
    if ($obj->Update_data("_createuser", $form_data_create_user, "where UserId='$user_edit_id'")) {

        if($_SESSION['UserType'] == 'SA'){
            $form_data_user_access = array(
                'UserType' => $user_type,
                'MenuPermission' => serialize( !empty($menu_permission) ? $menu_permission : []),
                'WorkPermission' => serialize( !empty($workPermission) ? $workPermission : []),
                'UpdateBy' => $userid
            );

            if($obj->Update_data("_useraccess", $form_data_user_access, "where UserId='$user_edit_id'")){

                $obj->notificationStore('User '.$full_name.' Updated Successful', 'success');
                echo '<script>window.location = "?q=user_edit&token='.$user_edit_id.'";</script>';

            }else{

                $obj->notificationStore('User '.$full_name.'Updated Failed' );
                echo '<script>window.location = "?q=user_edit&token='.$user_edit_id.'";</script>';
            }
        }else{

            $obj->notificationStore('User '.$full_name.' Updated Successful. <small>Only Super Admin can change permission</small>', 'success');
            echo '<script>window.location = "?q=user_edit&token='.$user_edit_id.'";</script>';
        }
    }
}
extract($user);
$path="asset/img/default.jpg";
?>

<div class="row">
    <div class="col-md-12 bg-teal-800">
        <h4>Edit Form of <?php echo $FullName; ?></h4>
    </div>
</div>

<div class="row">
    <div class="col-md-12 padding_5_px">
        <?php echo $obj->notificationShowRedirect(); ?>
    </div>
</div>


<form enctype="multipart/form-data" method="POST">
    <div class="row" style="padding:10px; font-size: 12px;">
        <div class="col-md-6">

            <input type="hidden" name="user_edit_id" value="<?php echo isset($token) ? $token : NULL; ?>"/>
            <input type="hidden" name="ph_path"
                   value="<?php echo isset($user['PhotoPath']) ? $user['PhotoPath'] : NULL; ?>"/>
            <div class="form-group">
                <label for="exampleInputEmail1">Full Name</label>
                <input type="text" name="full_name"
                       value="<?php echo isset($user['FullName']) ? $user['FullName'] : NULL; ?>" class="form-control"
                       id="exampleInputEmail1" placeholder="Full Name" required>
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">User Name</label>
                <input type="text" name="user_name"
                       value="<?php echo isset($user['UserName']) ? $user['UserName'] : NULL; ?>" class="form-control"
                       id="exampleInputEmail1" placeholder="User Name" required>
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Email Address</label>
                <input type="email" name="email" value="<?php echo isset($user['Email']) ? $user['Email'] : NULL; ?>"
                       class="form-control" id="exampleInputEmail1" placeholder="Email Address" required>
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Mobile No</label>
                <input type="text" name="mobile_no"
                       value="<?php echo isset($user['MobileNo']) ? $user['MobileNo'] : NULL; ?>" class="form-control"
                       id="exampleInputEmail1" placeholder="Mobile No" required>
            </div>

            <div class="form-group">
                <label for="exampleInputEmail1">Address</label>
                <input type="text" name="address"
                       value="<?php echo isset($user['Address']) ? $user['Address'] : NULL; ?>" class="form-control"
                       id="exampleInputEmail1" placeholder="Address" required>
            </div>

            <div class="form-group">
                <label for="exampleInputEmail1">User Type</label>
                <select name="user_type" class="form-control" style="margin-bottom: 5px;" required>
                    <option>Select</option>
                    <?php if ($ty == 'SA') { ?>
                        <option <?php if ($user['UserType'] == 'SA') echo 'selected'; ?> value="SA">Supper Admin
                        </option>
                        <option <?php if ($user['UserType'] == 'EO') echo 'selected'; ?> value="EO">Entry Operator</option>
                    <?php }  ?>
                </select>
            </div>

            <div class="form-group">
                <label >User Status</label>
                <select name="status" class="form-control" style="margin-bottom: 5px;" required>
                    <option <?php if ($user['Status'] == '1') echo 'selected'; ?> value="1">Active</option>
                    <option <?php if ($user['Status'] == '0') echo 'selected'; ?> value="0">InActive</option>
                </select>
            </div>
            <label >User Permission</label>
            <div class="form-group" style="border: 1px solid #CCCCCC; padding: 5px; border-radius:4px;">
                <label class="checkbox-inline">
                    <input type="checkbox" <?php echo $obj->userHasWorkPermission('add', $token) ? 'checked' : ''; ?> name="workPermission[]" value="add" > Add
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" <?php echo $obj->userHasWorkPermission('view', $token) ? 'checked' : ''; ?>  name="workPermission[]" value="view" > View
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" <?php echo $obj->userHasWorkPermission('edit', $token) ? 'checked' : ''; ?>  name="workPermission[]" value="edit" > Edit
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox"  <?php echo $obj->userHasWorkPermission('delete', $token) ? 'checked' : ''; ?>  name="workPermission[]" value="delete" > Delete
                </label>
                <!-- <label class="checkbox-inline">
                    <input type="checkbox"  <?php echo $obj->userHasWorkPermission('amount', $token) ? 'checked' : ''; ?>  name="workPermission[]" value="amount" > Show All Type Bill Amount
                </label> -->
            </div>
            <?php 
                $stock = $obj->details_by_cond('stock_marketing_enable', "stock=1");
                if ($stock) { ?>
            <div class="form-group">
                <label >Stock Menu Permission</label>

                <div class="form-group" style="border: 1px solid #CCCCCC; padding: 5px; border-radius:4px;">
                    <label class="checkbox-inline">
                        <input type="checkbox" <?php echo ($obj->userHasPermission('view_stock_in', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="view_stock_in"> Stock Item
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" <?php echo ($obj->userHasPermission('all_stock_out', $token)) ? 'checked' : '' ?> name="menu_permission[]"  value="all_stock_out"> All Stock Out
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" <?php echo ($obj->userHasPermission('view_stock_category', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="view_stock_category"> Stock Category
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" <?php echo ($obj->userHasPermission('all_stock_in', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="all_stock_in"> All Stock in
                    </label>
                </div>
                
            </div>
            <?php } ?>
            <div class="form-group">
                <label >Others Permission</label>

                <div class="form-group" style="border: 1px solid #CCCCCC; padding: 5px; border-radius:4px;">
                    <label class="checkbox-inline">
                        <input type="checkbox" <?php echo ($obj->userHasPermission('total_bandwidth', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="total_bandwidth"> Total Bandwidth Sell
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" <?php echo ($obj->userHasPermission('show_change_payment_history', $token)) ? 'checked' : '' ?> name="menu_permission[]"  value="show_change_payment_history"> Show Change Payment History
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" <?php echo ($obj->userHasPermission('print_payment_history', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="print_payment_history"> Print Payment History
                    </label>
                </div>
                
            </div>
            <div class="form-group">
                <label >Others Permission</label>

                <div class="form-group" style="border: 1px solid #CCCCCC; padding: 5px; border-radius:4px;">
                    <label class="checkbox-inline">
                        <input type="checkbox" <?php echo ($obj->userHasPermission('export_excel', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="export_excel"> Export In Excel
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" <?php echo ($obj->userHasPermission('print_client_list', $token)) ? 'checked' : '' ?> name="menu_permission[]"  value="print_client_list"> Print Client List
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" <?php echo ($obj->userHasPermission('print_client_bill', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="print_client_bill"> Print Client Bill
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" <?php echo ($obj->userHasPermission('invoice', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="invoice"> Invoice
                    </label>
                </div>
                
            </div>
        </div>

        <div class="col-md-6">
            <div class="row">
                <div class="col-md-4">
                    <?php if ($user['PhotoPath'] == '0') { ?>
                        <div class="form-group">
                            <img width="140" height="140" src="asset/img/def_img.png" alt="..." class="img-thumbnail"
                                 id="pre_photo">
                        </div>
                        <?php
                    } else {
                        ?>
                        <div class="form-group">
                            <img width="140" height="140"
                                 src="<?php
               if($user['PhotoPath'] == '')
               {
                echo $path;
               }
               else
               {
                echo $user['PhotoPath'];
               }
               ?>" alt="..."
                                 class="img-thumbnail" id="pre_photo">
                        </div>
                    <?php } ?>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="exampleInputFile">Chose Photo</label>
                        <input type="file" name="user_photo" onchange="usershow_photo(this)" id="photo">
                    </div>
                </div>
            </div>

            <label>Menu Permission</label>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('usercreate', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="usercreate">Add User</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('add_agent', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="add_agent">Add Customer</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('view_user', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="view_user">User Info</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('view_agent', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="view_agent">Customer's Info</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('view_new_join_customer', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="view_new_join_customer">New Customer</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('view_other_due', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="view_other_due">Customer of Other Due</label>
                        </div>
                    </div>
                </div>
            </div>

            <hr style="margin: 0px;">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('view_due_payment', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="view_due_payment">Due Customer (Bill Collection)</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('view_unpaid_bill', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="view_unpaid_bill"> Running  Bill Collection</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('view_bill_collection', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="view_bill_collection"> Bill Collection By User</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('print_view_unpaid_bill', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="print_view_unpaid_bill"> Print Running  Bill Collection</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('view_all_dues', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="view_all_dues">Previous Due Customer</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('view_running_paid_customer', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="view_running_paid_customer">Running Paid Customer</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('view_all_paid_client', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="view_all_paid_client">Paid Customer (No Due)</label>
                        </div>

                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('view_conn_charge', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="view_conn_charge">Connection Charge</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('view_income', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="view_income">Others Income</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('view_bad_due_payment', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="view_bad_due_payment">Inactive Collection</label>
                        </div>
                        
                    </div>
                </div>
            </div>

            <hr style="margin: 0px;">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('view_account_head', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="view_account_head">Account Head</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('view_account_sub_head', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="view_account_sub_head">Account Sub Head</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('view_bonus', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="view_bonus">Discount</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('view_expense', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="view_expense">Expense</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('expense_report', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="expense_report">Expense Report</label>
                        </div>

                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">


                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('acc_statement', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="acc_statement">Account Statement</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('monthly_new', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="monthly_new">Monthly
                                Balance Report</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('income_report', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="income_report">Income Report</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('yearly', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="yearly">Yearly Balance
                                Report</label>
                        </div>
                    </div>
                </div>
            </div>

            <hr style="margin: 0px;">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('add_customize_sms', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="add_customize_sms">Add Custome SMS</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('due_sms', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="due_sms">Due SMS</label>
                        </div>
                    </div>
                </div>

                 <div class="col-md-6">
                    <div class="form-group">

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('occation', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="occation">Occational SMS</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('software_setting', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="software_setting">Setting</label>
                        </div>

                    </div>
                </div>
            </div>
            
            <hr style="margin: 0px;">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('add_employee', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="add_employee">Add New Employee</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('view_all_employee', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="view_all_employee">View all Employee</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('zone_search', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="zone_search">Zone Search</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('active_inactive', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="active_inactive">Show Active/Inactive Customer</label>
                        </div>
                        <hr style="margin: 0px;">
                        <?php 
                        $marketing = $obj->details_by_cond('stock_marketing_enable', "marketing=1");
                        if ($marketing) { ?>
                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('add_client', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="add_client">Add Marketing Client</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('view_all_client', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="view_all_client">View All Client</label>
                        </div>
                        <?php } ?>
                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('total_bill', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="total_bill">Total Bill</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('print_expenses', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="print_expenses">Print Expenses</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('expense_report_print', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="expense_report_print">Expense Report Print</label>
                        </div>
                        <div class="checkbox">
                        <label>
                        <input type="checkbox" <?php echo ($obj->userHasPermission('print_statement', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="print_statement"> Print Statement
                    </label>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('add_complain', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="add_complain">Add Complain</label>
                        </div>
 <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('add_complain_imminent_user', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="add_complain_imminent_user">Add Complain For Imminent User</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('view_complains', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="view_complains">View All Complain</label>
                        </div>
                        
                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('complain_template', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="complain_template">Complain Template</label>
                        </div>
                        <hr style="margin: 0px;">
                       <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('show_total_bill', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="show_total_bill">Show Total Bill Generate</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('show_due_bill', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="show_due_bill">Show Total Due Bill Generate</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('billing_person', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="billing_person">Search By Billing Person</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('print_transaction', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="print_transaction">Print Transaction</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('print_due_payment_client', $token)) ? 'checked' : '' ?> name="menu_permission[]" value="print_due_payment_client">Print Due Payment Client</label>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <div class="row" style="padding: 5px 0px 15px 0px; font-size: 12px; text-align: center;">
        <button type="submit" name="update" class="btn btn-success">Update</button>
    </div>
</form>
