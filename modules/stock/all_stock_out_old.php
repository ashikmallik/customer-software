<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');

$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

// Get selected warehouse
$selected_warehouse = isset($_GET['warehouse_id']) ? $_GET['warehouse_id'] : '';

// ========== Delete Function Start =================
if ($obj->userWorkPermission($acc, 'delete')) {
    if (isset($_GET['dltoken']) && !empty($_GET['dltoken'])) {
        $dltoken = $_GET['dltoken'];
        
        if ($obj->Delete_data("tbl_stock", "id='$dltoken'")) {
            $obj->notificationStore('Delete Successfull', 'success');
        } else {
            $obj->notificationStore('Sorry ! Delete Failed. Try Again');
        }

        $redirect = "?q=all_stock_out";
        if (!empty($selected_warehouse)) {
            $redirect .= "&warehouse_id=" . $selected_warehouse;
        }
        echo '<script> window.location = "' . $redirect . '"; </script>';
        exit;
    }
}
// ========== Delete Function End =================

?>

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">

<div class="row">
    <div class="col-md-12 bg-teal-800" style="margin-bottom: 15px; padding:10px 0px 3px 15px; font-size:16px; font-family:Lucida Sans Unicode;">
        <div class="col-md-4">
            <b>Stock Out Information</b>
        </div>
        <div class="col-md-3">
            <form method="get" style="margin:0;">
                <input type="hidden" name="q" value="all_stock_out">
                <select name="warehouse_id" class="form-control" onchange="this.form.submit()" style="width: 100%;">
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
        <div class="col-md-5">
            <?php if ($ty == 'SA' || $ty == 'EO') { ?>
                <a class="btn btn-primary btn-sm pull-right" href="?q=stock_out">
                    STOCK OUT <span class="glyphicon glyphicon-plus"></span>
                </a>
            <?php } ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?php $obj->notificationShowRedirect(); ?>
    </div>
</div>

<div class="row" style="padding:10px; font-size: 12px;">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped" id="example">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Warehouse</th>
                    <th>Date</th>
                    <th>Link Name</th>
                    <th>Stock Name</th>
                    <th>Quantity</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
                </thead>
                <?php
                $i = 0;
                $totalExpense = 0;

                // Safe Condition
                $condition = "status = '0' AND item_qty > 0";
                if (!empty($selected_warehouse)) {
                    $condition .= " AND warehouse_id = " . (int)$selected_warehouse;
                }
                $condition .= " ORDER BY id DESC";

                $expense_detail = $obj->view_all_by_cond("tbl_stock", $condition);

                foreach ($expense_detail as $value) {
                    $i++;
                    $totalExpense += intval($value['item_qty']);

                    $item   = $obj->details_by_cond('tbl_stock_item', "item_id = " . (int)$value['item_id']);
                    $agent  = $obj->details_by_cond('tbl_agent', "ag_id = " . (int)$value['ag_id']);
                    $whData = $obj->details_by_cond('tbl_warehouse', "warehouse_id = " . (int)$value['warehouse_id']);
                    $cat    = $obj->details_by_cond('tbl_stock_category', "cat_id = " . (int)$item['cat_id']);
                ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo isset($whData['warehouse_name']) ? $whData['warehouse_name'] : 'N/A'; ?></td>
                        <td><?php echo date("d-m-Y", strtotime($value['entry_date'] ?? "2016-02-01")); ?></td> 
                        <td>
                            <a href="?q=view_individual_cus&token=<?php echo $value['ag_id']; ?>">
                                <?php echo isset($agent['link_name']) ? $agent['link_name'] : (isset($agent['ag_name']) ? $agent['ag_name'] : ''); ?>
                            </a>
                        </td>
                        <td><?php echo isset($item['item_name']) ? $item['item_name'] : ''; ?></td>
                        <td><?php echo $value['item_qty']; ?> <?php echo isset($item['unit']) ? $item['unit'] : ''; ?></td>
                        <td><?php echo isset($cat['cat_name']) ? $cat['cat_name'] : ''; ?></td>
                        <td><?php echo isset($value['description']) ? $value['description'] : ''; ?></td>
                        <td class="text-center">
                            <div class="btn-group">
                                <?php if ($obj->userWorkPermission($acc, 'edit')) { ?>
                                    <a class="btn btn-xs btn-success"
                                       href="?q=edit_stock_out&token=<?php echo $value['id']; ?>">
                                        Edit <span class="glyphicon glyphicon-edit"></span>
                                    </a>
                                <?php } ?>
                                <?php if ($obj->userWorkPermission($acc, 'delete')) { ?>
                                    <a onclick="return confirm('Are You Sure To Delete This Stock Out?');"
                                       href="?q=all_stock_out&dltoken=<?php echo $value['id']; ?>&warehouse_id=<?php echo $selected_warehouse; ?>"
                                       class="btn btn-xs btn-danger">
                                        Delete <span class="glyphicon glyphicon-remove"></span>
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

<script>
$(document).ready(function () {
    $('#example').DataTable({
        dom: 'Bfrtip',
        buttons: ['print'],
        order: [[2, 'desc']]   // Date column descending
    });
});
</script>

<script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>