<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$token = isset($_GET['token']) ? $_GET['token'] : NULL;

$details = $obj->details_by_cond("tbl_company", "tbl_company.company_id ='$token'");

extract($details);

if (isset($_POST['update'])) {
    extract($_POST);

    $form_data = array(
        'company_name' => $_POST['company_name'],
        'phone' => $_POST['phone'],
        'vat' => $_POST['vat'],
        'billing_type' => $_POST['billing_type'],
        'company_address' => $_POST['company_address'],
    );
    $updateZone = $obj->Update_data("tbl_company", $form_data, "tbl_company.company_id='$token'");

    if ($updateZone) {
        ?>
        <script>
            window.location = "?q=view_all_company";
        </script>
        <?php
    } else {
        echo $notification = 'Update Failed';
    }
}
?>

<?php if (isset($notification) && !empty($notification)) { ?>
    <div class="alert alert-warning alert-dismissable fade in text-center">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <?php echo isset($notification) ? $notification : NULL; ?>
    </div>
<?php } ?>

<div class="row" style="padding:10px; font-size: 12px;">
    <div class="col-md-12" style="margin-bottom:30px;">
        <div class="bg-teal text-center" style="padding:5px;">
            <h4>Edit company</h4>
        </div>
    </div>
    <div class="col-md-6 col-md-offset-3">
        <form method="post">
            
                    
            <div class="form-group row">
                <div class="text-center">
                    <h5 class="col-md-4">Billing Type :</h5>
                    <div class="col-md-8">
                        <select class="form-control" name="billing_type">
                            <option value="">Billing Type</option>
                            <option value="1" <?php echo ($details['billing_type'] == 1) ? 'selected' : ''; ?>>Prepaid</option>
                            <option value="2" <?php echo ($details['billing_type'] == 2) ? 'selected' : ''; ?>>Postpaid</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="text-center">
                    <h5 class="col-md-4">Company Name :</h5>
                    <div class="col-md-8">
                        <input type="text" name="company_name"
                               value="<?php echo $details['company_name'] ? $details['company_name'] : NULL; ?>"
                               class="form-control" id="ResponsiveTitle" placeholder="Provide zone Name">
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="text-center">
                    <h5 class="col-md-4"> Phone :</h5>
                    <div class="col-md-8">
                        <input type="text" name="phone"
                               value="<?php echo $details['phone'] ? $details['phone'] : NULL; ?>"
                               class="form-control" id="ResponsiveTitle" placeholder="Provide zone Name">
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="text-center">
                    <h5 class="col-md-4"> Company VAT (Give the percentage number) :</h5>
                    <div class="col-md-8">
                        <input type="text" name="vat" class="form-control" value="<?php echo $details['vat'] ? $details['vat'] : NULL; ?>" id="ResponsiveTitle" placeholder="Provide percentage number ">
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="text-center">
                    <h5 class="col-md-4"> Company Address :</h5>
                    <div class="col-md-8">
                        <textarea class="form-control" name="company_address" onkeypress="return noSpace(event)"  id="remarks" rows="4"><?php echo $details['company_address'] ? $details['company_address'] : NULL; ?></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="text-center">
                    <button type="submit" class="btn btn-primary" name="update"> Update zone Info
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>