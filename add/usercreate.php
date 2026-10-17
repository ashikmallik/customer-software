<?php

date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');

//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];

$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

if (isset($_POST['submit'])) {

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

    if ( isset($_FILES["user_photo"]["name"]) && !empty($_FILES["user_photo"]["name"]) ){

        if (copy($_FILES["user_photo"]["tmp_name"], $nep)){

            $photo_path = "asset/userphoto/" . $rend_code . ".jpg";
        }
    }

    if (!empty($_FILES["user_photo"]["name"]) && isset($_FILES["user_photo"]["name"])) {

        $user_photo_path = isset($photo_path) ? $photo_path : NULL;
    } else {

        $user_photo_path = 'asset/img/default.jpg';
    }

    extract($_POST);

    $form_data_create_user = array(
        'FullName' => $full_name,
        'UserName' => $user_name,
        'Password' => MD5($password),
        'Email' => $email,
        'MobileNo' => $mobile_no,
        'Address' => $address,
        'PermanantAddress' => $permanant_address,
        'PhotoPath' => $user_photo_path,
        'Status' => $status,
        'EntryBy' => $userid,
        'EntryDate' => $date_time,
        'UpdateBy' => $userid
    );

    $created_id = $obj->Reg_user_cond("_createuser", $form_data_create_user, "UserName='$user_name'");

    if ($created_id) {

        $form_data_user_access = array(
            'UserId' => $created_id,
            'UserType' => $user_type,
            'MenuPermission' => serialize( !empty($menu_permission) ? $menu_permission : []),
            'WorkPermission' => serialize( !empty($workPermission) ? $workPermission : []),
            'EntryBy' => $userid,
            'EntryDate' => $date_time,
            'UpdateBy' => $userid
        );

        if ($obj->Insert_data("_useraccess", $form_data_user_access)) {

            $obj->notificationStore('User '.$full_name.' Created Successfull', 'success');
        } else {
            $obj->notificationStore('Sorry! User Create Failed, Please Try again');
        }

    } else {
        $obj->notificationStore('This User Name "'.$user_name.'" Already Exist, Try Another User Name.');
    }
}

?>
<div class="row">
    <div class="col-md-12 bg-grey-800">
        <h4>User Create Form</h4>
    </div>
</div>
<hr>

<div class="row">

    <div class="col-md-12 padding_5_px" >
        <?php echo $obj->notificationShow() ?></b>
    </div>
</div>

<form role="form" id="user_create" enctype="multipart/form-data" method="post">
    <div class="row" style="padding:10px; font-size: 12px;">

        <div class="col-md-6">

            <div class="form-group">
                <label for="exampleInputEmail1">Full Name</label>
                <input type="text" name="full_name" class="form-control" placeholder="Full Name"
                       required>
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">User Name</label>
                <input type="text" name="user_name" class="form-control" placeholder="User Name"
                       required>
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">Password</label>
                <input type="password" name="password" class="form-control"
                       placeholder="**********" required>
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Email Address">
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Mobile No</label>
                <input type="text" name="mobile_no" class="form-control" placeholder="Mobile No">
            </div>

            <div class="form-group">
                <label for="exampleInputEmail1">Present Address</label>
                <input type="text" name="address" class="form-control" placeholder="Address">
            </div>

            <div class="form-group">
                <label for="exampleInputEmail1">Permanant Address</label>
                <input type="text" name="permanant_address" class="form-control" placeholder="Permanant Address">
            </div>

            <div class="form-group">
                <label for="exampleInputEmail1">User Type</label>
                <select name="user_type" class="form-control" style="margin-bottom: 5px;" required>
                    <option>Select</option>
                    <?php if ($ty == 'SA') { ?>

                        <option value="SA">Supper Admin</option>

                    <?php }    ?>
                    <option value="EO">Entry Operator</option>
                </select>
            </div>


            <div class="form-group">
                <label >User Status</label>
                <select name="status" class="form-control" style="margin-bottom: 5px;" required>
                    <option value="1">Active</option>
                    <option value="0">InActive</option>
                </select>
            </div>

            <div class="form-group">
                <label >User Permission</label>

                <div class="form-group" style="border: 1px solid #CCCCCC; padding: 5px; border-radius:4px;">
                    <label class="checkbox-inline">
                        <input type="checkbox" name="workPermission[]" value="add" checked onclick="return false;"> Add
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="workPermission[]"  value="view" checked onclick="return false;"> View
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="workPermission[]" value="edit"> Edit
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="workPermission[]" value="delete"> Delete
                    </label>
                </div>

            </div>
            <?php 
                $stock = $obj->details_by_cond('stock_marketing_enable', "stock=1");
                if ($stock) { ?>
            <div class="form-group">
                <label >Stock Menu Permission</label>

                <div class="form-group" style="border: 1px solid #CCCCCC; padding: 5px; border-radius:4px;">
                    <label class="checkbox-inline">
                        <input type="checkbox" name="menu_permission[]" value="view_stock_in"> Stock Item
                    </label><!-- done -->
                    <label class="checkbox-inline">
                        <input type="checkbox" name="menu_permission[]"  value="all_stock_out"> All Stock Out
                    </label><!-- done -->
                    <label class="checkbox-inline">
                        <input type="checkbox" name="menu_permission[]" value="view_stock_category"> Stock Category
                    </label><!-- done -->
                    <label class="checkbox-inline">
                        <input type="checkbox" name="menu_permission[]" value="all_stock_in"> All Stock in
                    </label><!-- done -->
                </div>
                
            </div>
             <?php } ?>
            <div class="form-group">
                <label >Others Permission</label>

                <div class="form-group" style="border: 1px solid #CCCCCC; padding: 5px; border-radius:4px;">
                    <label class="checkbox-inline">
                        <input type="checkbox" name="menu_permission[]" value="total_bandwidth"> Total Bandwidth Sell
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="menu_permission[]"  value="show_change_payment_history"> Show Change Payment History
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="menu_permission[]" value="print_payment_history"> Print Payment History
                    </label>
                </div>
                
            </div>
            <div class="form-group">
                <label >Others Permission</label>

                <div class="form-group" style="border: 1px solid #CCCCCC; padding: 5px; border-radius:4px;">
                    <label class="checkbox-inline">
                        <input type="checkbox" name="menu_permission[]" value="export_excel"> Export In Excel
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="menu_permission[]"  value="print_client_list"> Print Client List
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="menu_permission[]" value="print_client_bill"> Print Client Bill
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="menu_permission[]" value="invoice"> Invoice
                    </label>
                </div>
                
            </div>
            <!-- <div class="form-group">
                <label >Marketing Permission</label>

                <div class="form-group" style="border: 1px solid #CCCCCC; padding: 5px; border-radius:4px;">
                    <label class="checkbox-inline">
                        <input type="checkbox" name="menu_permission[]" value="add" checked onclick="return false;"> Add
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="menu_permission[]"  value="view" checked onclick="return false;"> View
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="menu_permission[]" value="edit"> Edit
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="menu_permission[]" value="delete"> Delete
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="menu_permission[]" value="amount"> Show All Type Bill Amount
                    </label>
                </div>
                
            </div> -->
        </div>

        <div class="col-md-6">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <img width="140" height="140" src="asset/img/def_img.png" alt="..." class="img-thumbnail"
                             id="pre_photo">
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="exampleInputFile">Chose Photo</label>
                        <input type="file" name="user_photo" onchange="usershow_photo(this)" id="photo">
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-6">
                    <label>Menu Permission</label>
                </div>
                <div class="col-md-6">
                    <label class="btn-primary btn-xs pointer">
                        <input type="checkbox" id="select_all_menu_permission"><b> Select All</b>
                    </label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">

                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="usercreate">Add User</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="add_agent">Add Customer</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="view_user">User Info</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">

                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="view_agent">Customer's Info</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="view_new_join_customer">New Customer</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="view_other_due">Customer of Other Due</label>
                        </div>
                    </div>
                </div>
            </div>

            <hr style="margin: 0px;">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        
                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="view_unpaid_bill">Running Bill Collection</label>
                        </div>
                        
                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="view_bill_collection">Bill Collection By User</label>
                        </div>
                        

                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="view_due_payment">Due Customer (Bill Collection)</label>
                        </div>
                        
                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="view_all_dues">Previous Due Customer</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="view_running_paid_customer">Running Paid Customer</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="view_all_paid_client">Paid Customer (No Due)</label>
                        </div>
                        
                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="print_view_unpaid_bill">Print Running Bill Collection</label>
                        </div>

                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">

                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="view_conn_charge">Connection Charge</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="view_income">Others Income</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="view_bad_due_payment">Inactive Collection</label>
                        </div>

                    </div>
                </div>
            </div>

            <hr style="margin: 0px;">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">

                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="view_account_head">Account Head</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="view_account_sub_head">Account Sub Head</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="view_bonus">Discount</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="view_expense">Expense</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="expense_report">Expense Report</label>
                        </div>

                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">


                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="acc_statement">Account Statement</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="monthly_new">Monthly
                                Balance Report</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="income_report">Income Report</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="yearly">Yearly Balance
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
                            <label><input type="checkbox" name="menu_permission[]" value="add_customize_sms">Add Custome SMS</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="due_sms">Due SMS</label>
                        </div>

                    </div>
                </div>
   <div class="col-md-6">
                    <div class="form-group">

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('occation', $userid)) ? 'checked' : '' ?> name="menu_permission[]" value="occation">Occational SMS</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('software_setting', $userid)) ? 'checked' : '' ?> name="menu_permission[]" value="software_setting">Setting</label>
                        </div>

                    </div>
                </div>
            </div>
            
            <hr style="margin: 0px;">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('add_employee', $userid)) ? 'checked' : '' ?> name="menu_permission[]" value="add_employee">Add New Employee</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('view_all_employee', $userid)) ? 'checked' : '' ?> name="menu_permission[]" value="view_all_employee">View all Employee</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="zone_search">Zone Search</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="active_inactive">Show Active/Inactive Customer</label>
                        </div>
                        <hr style="margin: 0px;">
                        <?php 
                        $marketing = $obj->details_by_cond('stock_marketing_enable', "marketing=1");
                        if ($marketing) { ?>
                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="add_client">Add Marketing Client</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="view_all_client">View All Client</label>
                        </div>
                        <?php } ?>
                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="total_bill">Total Bill</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="print_expenses">Print Expenses</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="expense_report_print">Expense Report Print</label>
                        </div>
                        <div class="checkbox">
                          <label>
                        <input type="checkbox" name="menu_permission[]" value="print_statement"> Print Statement
                         </label>
                         </div>
                        
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('add_complain', $userid)) ? 'checked' : '' ?> name="menu_permission[]" value="add_complain">Add Complain</label>
                        </div>
                          <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('add_complain_imminent_user', $userid)) ? 'checked' : '' ?> name="menu_permission[]" value="add_complain_imminent_user">Add Complain For Imminent User</label>
                        </div>

                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('view_complains', $userid)) ? 'checked' : '' ?> name="menu_permission[]" value="view_complains">View All Complain</label>
                        </div>
                        
                        <div class="checkbox">
                            <label><input type="checkbox" <?php echo ($obj->userHasPermission('complain_template', $userid)) ? 'checked' : '' ?> name="menu_permission[]" value="complain_template">Complain Template</label>
                        </div>
                        <hr style="margin: 0px;">
                       <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="show_total_bill">Show Total Bill Generate</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="show_due_bill">Show Total Due Bill Generate</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="billing_person">Search By Billing Person</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="print_transaction">Print Transaction</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" name="menu_permission[]" value="print_due_payment_client">Print Due Payment Client</label>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <div class="row" style="padding: 5px 0px 15px 0px; font-size: 12px; text-align: center;">
        <button type="submit" name="submit" class="btn btn-success">Submit</button>
    </div>
</form>
<script>
    $(document).ready(function () {
        $('form#user_create').on('change', 'input#select_all_menu_permission', function (event) {

            var checked = $(this).prop('checked');

            $('form#user_create').find('input[name="menu_permission[]"]').prop('checked', checked);

        });
        function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function (e) {
                $('#pre_photo').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#photo").change(function(){
        readURL(this);
    });
    });
</script>
