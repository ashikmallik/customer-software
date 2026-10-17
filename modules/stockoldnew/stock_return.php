<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$token = isset($_GET['token']) ? $_GET['token'] : NULL;

$details = $obj->details_by_cond("tbl_stock", "id='$token'");


if (isset($_POST['update'])) {
    extract($_POST);

    if ($details['item_qty']>=$quantity) {
        $qty = $details['item_qty'] - $quantity;

        $form_data = array(
            'item_qty' => $qty,
            'description' =>$description,
        );
        $details_update = $obj->Update_data("tbl_stock", $form_data, "where id='$token'");


        if ($details_update) {
            ?>
            <script>
                window.location = "?q=all_stock_out";
            </script>
            <?php
        } else {
            echo $notification = 'Update Failed';
        }
    }else{

        $notification = 'Insufficient Return Quantity';
    }
    
}
?>
<!--===================end Function===================-->

<div class="row" >
    <div class="col-md-12 bg-slate-700">
        <h4> Stock Return</h4>
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
                    <label>Stock Name</label>
                    <input value="<?php $item = $obj->details_by_cond('tbl_stock_item', "item_id = " . $details['item_id'] . ""); echo $item['item_name']; ?>" type="text" class="form-control" id="ResponsiveTitle" readonly >

                    
                </div>
                <div class="form-group">
                    <label>Customer</label>
                    <input value="<?php $item = $obj->details_by_cond('tbl_agent', "ag_id = " . $details['ag_id'] . ""); echo $item['ag_name']; ?>" type="text" class="form-control" id="ResponsiveTitle" readonly >
                </div>
                <div class="form-group">
                    <label>Available Qty</label>
                    <input value="<?php echo $details['item_qty'] ? $details['item_qty'] : NULL; ?>" type="text" class="form-control" id="ResponsiveTitle" readonly >
                </div>
                <div class="form-group">
                    <label>Return Qty</label>
                    <input  type="text" onkeypress="return numbersOnly(event)"
                           name="quantity" class="form-control" id="ResponsiveTitle">
                </div>
                <div class="form-group">
                    <label>Details</label>
                    <textarea class="form-control" name="description" rows="6"><?php echo $details['description'] ? $details['description'] : NULL; ?></textarea>
                </div>
                <div class="form-group text-center">
                    <input type="submit" class="btn btn-success" name="update" value="Return"/>
                </div>

                
            </div>
        </div>

        <div class="row" style="padding: 5px 0px 15px 25px; font-size: 12px;">

        </div>
    </form>
</div>
<hr></hr>

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