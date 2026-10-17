<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');

$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

// Get selected warehouse
$selected_warehouse = isset($_GET['warehouse_id']) ? $_GET['warehouse_id'] : '';

// ========== Delete Function Start =================
if ($obj->userWorkPermission($acc, 'delete')) {
    $dltoken = isset($_GET['dltoken']) ? $_GET['dltoken'] : NULL;
    if (!empty($dltoken)) {
        $deleteStock = $obj->Delete_data("tbl_stock_item", "tbl_stock_item.item_id='$dltoken'");
        if ($deleteStock) {
            $notification = 'Delete Successfull';
        } else {
            $notification = 'Delete Failed';
        }
    }
}
// ========== Delete Function End =================

//===================Add Function===================
if (isset($_POST['submit'])) {
    extract($_POST);

    $form_data = array(
        'item_name'    => $name,
        'warehouse_id' => $warehouse_id,
        'cat_id'       => $cat_id,
        'unit'         => $unit,
        'item_qty'     => $quantity,
        'item_price'   => $price,
        'created_by'   => $userid
    );

    $service_add = $obj->Insert_data("tbl_stock_item", $form_data);

    $add_data = array(
        'item_qty'    => $quantity,
        'warehouse_id'=> $warehouse_id,
        'item_id'     => $service_add,
        'added_by'    => $userid,
        'entry_date'  => date("Y/m/d"),
        'status'      => 1
    );

    $obj->Insert_data("tbl_stock", $add_data);

    if ($service_add) {
        ?>
        <script>
            window.location = "?q=view_stock_in";
        </script>
        <?php
    } else {
        $notification = 'Insert Failed';
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

<?php if (isset($notification) && !empty($notification)) { ?>
    <div class="alert alert-warning alert-dismissable fade in text-center">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <?php echo $notification; ?>
    </div>
<?php } ?>

<br>

<div class="row">
    <div class="col-md-12 bg-teal-800" style="margin-bottom: 15px; padding:10px 0px 3px 15px; font-size:16px; font-family:Lucida Sans Unicode;">
        <div class="col-md-4">
            <b>View Stock Information</b>
        </div>
        <div class="col-md-4">
            <form method="get" style="margin:0;">
                <input type="hidden" name="q" value="view_stock_in">
                <select name="warehouse_id" class="form-control" onchange="this.form.submit()" style="width: 280px; display:inline-block;">
                    <option value="">All Warehouses</option>
                    <?php
                    foreach ($obj->view_all("tbl_warehouse") as $wh) {
                        $selected = ($selected_warehouse == $wh['warehouse_id']) ? 'selected' : '';
                        echo "<option value='{$wh['warehouse_id']}' {$selected}>{$wh['warehouse_name']}</option>";
                    }
                    ?>
                </select>
            </form>
        </div>
        <div class="col-md-4">
            <?php if ($ty == 'SA' || $ty == 'EO') { ?>
                <button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#addStockModal">
                    <span class="glyphicon glyphicon-plus"></span> NEW STOCK
                </button>
            <?php } ?>
        </div>
    </div>
</div>

<div class="row">
    <div id="div_all" class="row" style="font-size: 12px;">
        <div class="col-md-10 col-md-offset-1">
            <div class="table-responsive">
                <table style="margin-left:-15px;" class="table table-bordered table-hover table-striped" id="datatable">
                    <thead>
                        <tr>
                            <th class="text-center">SL</th>
                            <th class="text-center">Warehouse</th>
                            <th class="text-center">Stock Name</th>
                            <!-- <th class="text-center">In Qty</th>
                            <th class="text-center">Out Qty</th>
                            <th class="text-center">Available</th> -->
                            <th class="text-center">Category</th>
                            <th class="text-center">Created By</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <?php
                    $i = 0;

                    $sql = "SELECT 
                                item_name,
                                cat_id,
                                unit,
                                warehouse_id,
                                created_by,
                                SUM(item_qty) as total_qty,
                                MIN(item_id) as first_item_id,
                                GROUP_CONCAT(item_id) as all_item_ids
                            FROM tbl_stock_item 
                            WHERE item_id != 0";

                    if (!empty($selected_warehouse)) {
                        $sql .= " AND warehouse_id = " . (int)$selected_warehouse;
                    }

                    $sql .= " GROUP BY item_name, cat_id, unit, warehouse_id 
                              ORDER BY warehouse_id, item_name";

                    // raw_sql ফাংশন ব্যবহার
                    $stock_items = $obj->raw_sql($sql);

                    foreach ($stock_items as $group) {
                        $i++;
                        $warehouse_id = $group['warehouse_id'];
                        $first_item_id = $group['first_item_id'];

                        $whData  = $obj->details_by_cond('tbl_warehouse', "warehouse_id = " . $warehouse_id);
                        $catData = $obj->details_by_cond('tbl_stock_category', "cat_id = " . $group['cat_id']);
                        $userData = $obj->details_by_cond('_createuser', "UserId = " . $group['created_by']);

                        // In & Out Calculation
                        $in  = $obj->get_total_stock_in($first_item_id, $warehouse_id);
                        $out = $obj->get_total_stock_out($first_item_id, $warehouse_id);
                        $available = $group['total_qty'];
                    ?>
                        <tr>
                            <td class="text-center"><?php echo $i; ?></td>
                            <td class="text-center"><?php echo $whData['warehouse_name']; ?></td>
                            <td class="text-center">
                                <a href="?q=view_individual_item&token=<?php echo $group['item_name'] ?>">
                                    <?php echo htmlspecialchars($group['item_name']); ?>
                                </a>
                            </td>
                            <!-- <td class="text-center"><?php echo $in; ?> <?php echo $group['unit']; ?></td>
                            <td class="text-center"><?php echo $out; ?> <?php echo $group['unit']; ?></td>
                            <td class="text-center"><strong><?php echo $available; ?></strong> <?php echo $group['unit']; ?></td> -->
                            <td class="text-center"><?php echo $catData['cat_name']; ?></td>
                            <td class="text-center"><?php echo $userData['FullName']; ?></td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <?php if ($obj->userWorkPermission($acc, 'edit')) { ?>
                                        <a class="btn btn-xs btn-info" href="?q=add_stock_item&token=<?php echo $first_item_id ?>">
                                            <span class="glyphicon glyphicon-plus"></span>
                                        </a>
                                        <a class="btn btn-xs btn-info" href="?q=edit_stock_item&token=<?php echo $first_item_id ?>">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>
                                    <?php } ?>
                                    <?php if ($obj->userWorkPermission($acc, 'delete')) { ?>
                                        <a href="?q=view_stock_in&dltoken=<?php echo $first_item_id ?>&warehouse_id=<?php echo $selected_warehouse; ?>"
                                           class="btn btn-xs btn-danger" onclick="return confirm('Are you sure you want to delete this stock?');">
                                            <span class="glyphicon glyphicon-remove"></span>
                                        </a>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</div>
    <!-- Modal Start-->
<div class="modal fade" id="addStockModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center">Add New Stock</h4>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="row" style="padding:10px; font-size: 12px;">

                        <div class="col-md-6 col-md-offset-3">
                            <div class="form-group">
                                <label>Item Name</label>
                                <input type="text" name="name" class="form-control" required="required">
                            </div>
                            <div class="form-group">
                                <label>Category</label>
                                <select class="form-control" required="required" name="cat_id" id="status" style="width: 250px">
                                    <?php
                                    $i = '0';
                                    foreach ($obj->view_all("tbl_stock_category") as $value) {
                                        $i++;
                                        ?>
                                        <option></option>
                                        <option value="<?php echo isset($value['cat_id']) ? $value['cat_id'] : NULL; ?>"><?php echo isset($value['cat_name']) ? $value['cat_name'] : NULL; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Warehouse</label>
                                <select class="form-control" required="required" name="warehouse_id" id="status" style="width: 250px">
                                    <?php
                                    $i = '0';
                                    foreach ($obj->view_all("tbl_warehouse") as $value) {
                                        $i++;
                                        ?>
                                        <option></option>
                                        <option value="<?php echo isset($value['warehouse_id']) ? $value['warehouse_id'] : NULL; ?>"><?php echo isset($value['warehouse_name']) ? $value['warehouse_name'] : NULL; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Unit</label>
                                <select class="form-control" required="required" name="unit" id="status" style="width: 250px">
                                    <option></option>
                                    <option>Meter</option>
                                    <option>Pcs</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Quantity</label>
                                <input onkeypress="return numbersOnly(event)" required="required" type="text" name="quantity"
                                       class="form-control" id="ResponsiveTitle">
                            </div>
                            <div class="form-group">
                                <label>Price</label>
                                <input onkeypress="return numbersOnly(event)" required="required" type="text" name="price"
                                       class="form-control" id="ResponsiveTitle">
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-success" name="submit">Submit</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
<!-- Modal End-->

<script>
    $('select[name="cat_id"]').select2({ placeholder: "Select Category", allowClear: true });
    $('select[name="unit"]').select2({ placeholder: "Select Unit", allowClear: true });
</script>