<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');

$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

//===================Add Function===================
if (isset($_POST['submit'])) {
    extract($_POST);

    $form_data = array(
        'ag_id'        => $ag_id,
        'item_id'      => $item_id,
        'warehouse_id' => $warehouse_id,        // ← নতুন যোগ হয়েছে
        'item_qty'     => $quantity,
        'description'  => str_replace("'", "", $Details),
        'added_by'     => $userid,
        'entry_date'   => date("Y/m/d"),
        'status'       => 0,
    );

    // Warehouse Wise Stock Check
    $qty = $obj->get_available_stock($item_id, $warehouse_id);

    if (!is_numeric($quantity) || $quantity <= 0) {
        $notification = 'Quantity must be greater than 0';
    } elseif ($quantity <= $qty) {
        $service_add = $obj->Insert_data("tbl_stock", $form_data);

        if ($service_add) {
            ?>
            <script>
                window.location = "?q=all_stock_out";
            </script>
            <?php
        } else {
            $notification = 'Insert Failed';
        }
    } else {
        $notification = 'Sorry Insufficient Stock!';
    }
}
?>

<!-- Numeric Validation -->
<script>
function numbersOnly(e) {
    var unicode = e.charCode ? e.charCode : e.keyCode;
    if (unicode != 8) {
        if ((unicode < 2534 || unicode > 2543) && (unicode < 48 || unicode > 57)) {
            return false;
        }
    }
}
</script>

<div class="col-md-12" style="margin-top:5px; margin-bottom:5px; font-size:14px; color:red; font-weight:bold; text-align:center;">
    <b><?php echo isset($notification) ? $notification : NULL; ?></b>
</div>

<div class="row">
    <div class="col-md-12 bg-teal-800" style="margin-bottom:15px; padding:10px 0px 3px 15px; font-size:16px; font-family:Lucida Sans Unicode;">
        <div class="col-md-4">
            <b>Stock Out</b>
        </div>
        <div class="col-md-8">
            <?php if ($ty == 'SA') { ?>
                <a class="btn btn-primary btn-sm pull-right" href="?q=all_stock_out">VIEW ALL</a>
            <?php } ?>
        </div>
    </div>
</div>

<div class="row" style="padding:10px; font-size:12px;">
    <form role="form" method="post">
        <div class="row" style="padding:10px; font-size:12px;">
            <div class="col-md-6 col-md-offset-3">

                <div class="form-group">
                    <label>Warehouse</label>
                    <select class="form-control" required name="warehouse_id" id="warehouse_id">
                        <option value="">Select Warehouse</option>
                        <?php
                        foreach ($obj->view_all("tbl_warehouse") as $wh) {
                            ?>
                            <option value="<?php echo $wh['warehouse_id']; ?>">
                                <?php echo $wh['warehouse_name']; ?>
                            </option>
                            <?php
                        }
                        ?>
                    </select>
                </div>

                    <div class="form-group">
                        <label>Stock Item</label>
                        <select class="form-control" required name="item_id" id="item_id">
                            <option value="">Select Item</option>
                        </select>
                    </div>

                <div class="form-group">
                    <label>Available Quantity</label>
                    <input type="text" id="available_qty" class="form-control" readonly>
                </div>

                <div class="form-group">
                    <label>Quantity</label>
                    <input onkeypress="return numbersOnly(event)" required type="text" 
                           name="quantity" class="form-control" id="quantity">
                </div>

                
                <div class="form-group">
                    <label>Customer</label>
                    <select class="form-control" required name="ag_id">
                        <option value="">Select Customer</option>
                        <?php
                        foreach ($obj->view_all("tbl_agent") as $value) {
                            ?>
                            <option value="<?php echo $value['ag_id']; ?>">
                                <?php echo $value['link_name']; ?>
                            </option>
                            <?php
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Details</label>
                    <textarea class="form-control" name="Details" rows="5"></textarea>
                </div>

            </div>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-success" name="submit">Submit Stock Out</button>
        </div>
    </form>
</div>

<hr>

<script>
$(document).ready(function () {
    $('#item_id').select2({ placeholder: "Select Item", allowClear: true });
    $('#warehouse_id').select2({ placeholder: "Select Warehouse", allowClear: true });
    $('select[name="ag_id"]').select2({ placeholder: "Select Customer", allowClear: true });

    // Available Quantity Auto Update
    $('#item_id, #warehouse_id').change(function () {
        var item_id = $('#item_id').val();
        var warehouse_id = $('#warehouse_id').val();

        if (item_id && warehouse_id) {
            $.ajax({
                url: 'view/ajax_action/ajax_get_available_stock.php',   // ← পরে এই ফাইল তৈরি করতে হবে
                type: 'POST',
                data: { item_id: item_id, warehouse_id: warehouse_id },
                success: function(response) {
                    $('#available_qty').val(response);
                }
            });
        } else {
            $('#available_qty').val('');
        }
    });
});


$('#warehouse_id').change(function () {

    var warehouse_id = $(this).val();

    if (warehouse_id != '') {

        $.ajax({
            url: 'view/ajax_action/fetch_stock_by_warehouse.php',
            type: 'POST',
            data: {
                warehouse_id: warehouse_id
            },
            success: function(response) {

                $('#item_id').html(response);
                $('#item_id').trigger('change');

                $('#available_qty').val('');
            }
        });

    } else {

        $('#item_id').html('<option value="">Select Item</option>');
        $('#available_qty').val('');

    }

});
</script>