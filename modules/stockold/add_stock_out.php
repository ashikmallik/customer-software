


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

        'ag_id' => $ag_id,
        'item_id' => $item_id,
        'item_qty' => $quantity,
        'description' => str_replace("'", "", $Details),
        'added_by' => $userid,
        'entry_date' => date("Y/m/d"),
        'status' => 0,
    );

    $out = $obj->get_total_stock_out($item_id);
    $in =  $obj->get_total_stock_in($item_id);
    $qty = $in-$out; 

    if ($quantity<$qty) {
        $service_add = $obj->Reg_user_cond("tbl_stock", $form_data, " ");
     
        if ($service_add) {
            ?>
            <script>
                window.location = "?q=all_stock_out";
            </script>
            <?php
        } else {
            echo $notification = 'Insert Failed';
        }
    }else{
        $notification = 'Sorry Insufficient Stock!';
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

<div class="row">
    <div class="col-md-12 bg-teal-800" style="margin-bottom: 15px; padding:10px 0px 3px 15px; font-size:16px; font-family:Lucida Sans Unicode;">
        <div class="col-md-4">
            <b> Stock Out </b>
        </div>
        <div class="col-md-8">
            <?php if ($ty == 'SA') { ?>
                <a class="btn btn-primary btn-sm pull-right" href="?q=all_stock_out">VIEW ALL</a>
            <?php } ?>
        </div>
    </div>
</div>
<div class="row" style="padding:10px; font-size: 12px;">
    <form role="form" enctype="multipart/form-data" method="post">
        <div class="row" style="padding:10px; font-size: 12px;">

            <div class="col-md-6 col-md-offset-3">

                <div class="form-group">
                    <label>Customer</label>
                    <select class="form-control" required="required" name="ag_id" id="status">
                        <option></option>
                        <?php
                        $i = '0';
                        foreach ($obj->view_all("tbl_agent") as $value) {
                            $i++;
                            ?>
                            <option value="<?php echo isset($value['ag_id']) ? $value['ag_id'] : NULL; ?>"><?php echo isset($value['ag_name']) ? $value['ag_name'] : NULL; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Stock Item</label>
                    <select class="form-control" required="required" name="item_id" id="status">
                        <option></option>
                        <?php
                        $i = '0';
                        foreach ($obj->view_all("tbl_stock_item") as $value) {
                            $i++;
                            ?>
                            <option value="<?php echo isset($value['item_id']) ? $value['item_id'] : NULL; ?>">
                            <?php $category = $obj->details_by_cond('tbl_stock_category', "cat_id = " . $value['cat_id'] . ""); echo $category['cat_name'];
                              ?>::
                              <?php echo isset($value['item_name']) ? $value['item_name'] : NULL; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Quantity</label>
                    <input onkeypress="return numbersOnly(event)" required="required" type="text" name="quantity"
                           class="form-control" id="ResponsiveTitle">
                </div>
                <div class="form-group">
                    <label>Details</label>
                    <textarea class="form-control" name="Details" id="ResponsiveDetelis" rows="6"></textarea>
                </div>

            </div>
            <div class="col-md-6"></div>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-success" name="submit">Submit</button>
        </div>
    </form>
</div>
<hr>
<script>
    $(document).ready(function () {
        $('select[name="item_id"]').select2({
            placeholder: "Select Item",
            allowClear: true,
        });
    });
    $(document).ready(function () {
        $('select[name="ag_id"]').select2({
            placeholder: "Select Customer",
            allowClear: true,
        });
    });
</script>