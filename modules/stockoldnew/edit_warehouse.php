<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$token = isset($_GET['token']) ? $_GET['token'] : NULL;

$details = $obj->details_by_cond("tbl_warehouse", "tbl_warehouse.warehouse_id='$token'");

extract($details);

if (isset($_POST['update'])) {
    extract($_POST);

    $form_data = array(
        'warehouse_name' => $_POST['warehouse_name'],
        'Address' => $_POST['address'],
    );
    $updateCategory = $obj->Update_data("tbl_warehouse", $form_data, "tbl_warehouse.warehouse_id='$token'");

    if ($updateCategory) {
        ?>
        <script>
            window.location = "?q=view_all_warehouse";
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
            <h4>Edit Category</h4>
        </div>
    </div>
    <div class="col-md-6 col-md-offset-3">
        <form method="post">
            <div class="form-group row">
                <div class="text-center">
                    <h5 class="col-md-4">Warehouse Name :</h5>
                    <div class="col-md-8">
                        <input type="text" name="warehouse_name"
                               value="<?php echo $details['warehouse_name'] ? $details['warehouse_name'] : NULL; ?>"
                               class="form-control" id="ResponsiveTitle" placeholder="Provide Warehouse Name">
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="text-center">
                    <h5 class="col-md-4">Warehouse Name :</h5>
                    <div class="col-md-8">
                        <input type="text" name="address"
                               value="<?php echo $details['address'] ? $details['address'] : NULL; ?>"
                               class="form-control" id="ResponsiveTitle" placeholder="Provide Warehouse Name">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="text-center">
                    <button type="submit" class="btn btn-primary" name="update"> Update Category Info
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>