<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$token = isset($_GET['token']) ? $_GET['token'] : NULL;

$details = $obj->details_by_cond("tbl_stock_item", "tbl_stock_item.item_id='$token'");

extract($details);

$item = $details['item_qty'];

if (isset($_POST['update'])) {
    extract($_POST);

    $form_data = array(
        'item_qty' => $_POST['itemQuantity']+ $item,
    );
    $add_data = array(
        'item_qty' => $_POST['itemQuantity'],
        'item_id' =>$token,
        'added_by' =>$userid,
        'entry_date' =>date("Y/m/d"),
        'status' => 1
        );
    $updateStock = $obj->Update_data("tbl_stock_item", $form_data, "tbl_stock_item.item_id='$token'");

    $service_add = $obj->Insert_data("tbl_stock", $add_data);


    if ($updateStock) {
        ?>
        <script>
            window.location = "?q=view_stock_in";
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
            <h4>Add to <?php echo $details['item_name']?></h4>
        </div>
    </div>
    <div class="col-md-6 col-md-offset-3">
        <form method="post">
            <div class="form-group row">
                <div class="text-center">
                    <h5 class="col-md-4">Stock Quantity :</h5>
                    <div class="col-md-8">
                        <input type="text" name="itemQuantity"
                               class="form-control" id="ResponsiveTitle" placeholder="Provide Stock Quantity">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="text-center">
                <div class="col-md-12">
                	<button type="submit" class="btn btn-primary" name="update"> Add Stock 
                    </button>
                </div>
                    
                </div>
            </div>
        </form>
    </div>
</div>