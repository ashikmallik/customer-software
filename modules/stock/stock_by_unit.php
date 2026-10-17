<?php
/* =====================================================================
   Unit Wise / Item Wise Available Stock Report
   Requires: $obj (your existing class with raw_sql() method)
   ===================================================================== */

// ---------- Optional Filter: by Warehouse ----------
$filter_warehouse = isset($_GET['warehouse_id']) ? $_GET['warehouse_id'] : '';
$warehouse_condition = '';
if (!empty($filter_warehouse)) {
    $warehouse_condition = " AND s.warehouse_id = '$filter_warehouse' ";
}

/* =====================================================================
   1) UNIT WISE TOTAL AVAILABLE STOCK  (Meter / Pcs)
   ===================================================================== */
$unit_wise_sql = "
    SELECT
        i.unit,
        COALESCE(SUM(CASE WHEN s.status = 1 THEN s.item_qty ELSE 0 END), 0)
        - COALESCE(SUM(CASE WHEN s.status = 0 THEN s.item_qty ELSE 0 END), 0) AS available_qty
    FROM tbl_item i
    LEFT JOIN tbl_stock s ON s.item_id = i.item_id $warehouse_condition
    GROUP BY i.unit
    ORDER BY i.unit
";
$unit_wise_data = $obj->raw_sql($unit_wise_sql);


/* =====================================================================
   2) ITEM WISE AVAILABLE STOCK (with unit, price, code)
   ===================================================================== */
$item_wise_sql = "
    SELECT
        i.item_id,
        i.item_name,
        i.item_price,
        i.codeandnode,
        i.unit,
        COALESCE(SUM(CASE WHEN s.status = 1 THEN s.item_qty ELSE 0 END), 0)
        - COALESCE(SUM(CASE WHEN s.status = 0 THEN s.item_qty ELSE 0 END), 0) AS available_qty
    FROM tbl_item i
    LEFT JOIN tbl_stock s ON s.item_id = i.item_id $warehouse_condition
    GROUP BY i.item_id, i.item_name, i.item_price, i.codeandnode, i.unit
    ORDER BY i.item_name
";
$item_wise_data = $obj->raw_sql($item_wise_sql);


/* =====================================================================
   3) Warehouse dropdown list (for filter)
   ===================================================================== */
$warehouse_list = $obj->view_all("tbl_warehouse");
?>

<style>
    .unit-card {
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 15px;
        text-align: center;
        margin-bottom: 15px;
        background: #f9f9f9;
    }
    .unit-card h3 {
        margin: 5px 0 0 0;
        font-weight: bold;
    }
    .unit-card small {
        color: #777;
    }
</style>

<div class="row">
    <div class="col-md-12 bg-grey-600 padding_10_px">
        <div class="col-md-6">
            <h4>Unit Wise Available Stock</h4>
        </div>
        <div class="col-md-6">
            <form method="get" class="form-inline pull-right">
                <input type="hidden" name="q" value="<?php echo isset($_GET['q']) ? $_GET['q'] : 'stock_by_unit'; ?>">
                <select name="warehouse_id" class="form-control" onchange="this.form.submit()">
                    <option value="">-- All Warehouse --</option>
                    <?php foreach ($warehouse_list as $wh) { ?>
                        <option value="<?php echo $wh['warehouse_id']; ?>"
                            <?php echo ($filter_warehouse == $wh['warehouse_id']) ? 'selected' : ''; ?>>
                            <?php echo $wh['warehouse_name']; ?>
                        </option>
                    <?php } ?>
                </select>
            </form>
        </div>
    </div>
</div>

<!-- ===================== Unit Wise Summary Cards ===================== -->
<div class="row" style="margin-top:15px;">
    <?php foreach ($unit_wise_data as $u) { ?>
        <div class="col-md-3 col-sm-4 col-xs-6">
            <div class="unit-card">
                <small>Unit</small>
                <h3><?php echo htmlspecialchars($u['unit']); ?></h3>
                <span class="label label-primary">
                    <?php echo number_format($u['available_qty']); ?> Available
                </span>
            </div>
        </div>
    <?php } ?>
</div>

<!-- ===================== Item Wise Detailed Table ===================== -->
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped" id="unit_stock_datatable">
                <thead>
                    <tr>
                        <th class="text-center">SL</th>
                        <th class="text-center">Item Code</th>
                        <th class="text-center">Item Name</th>
                        <th class="text-center">Unit</th>
                        <th class="text-center">Price</th>
                        <th class="text-center">Available Qty</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0; foreach ($item_wise_data as $row) { $i++; ?>
                        <tr>
                            <td class="text-center"><?php echo $i; ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($row['codeandnode']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($row['item_name']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($row['unit']); ?></td>
                            <td class="text-center"><?php echo number_format($row['item_price'], 2); ?></td>
                            <td class="text-center">
                                <?php
                                    $qty = $row['available_qty'];
                                    $badge_class = ($qty <= 0) ? 'label-danger' : 'label-success';
                                ?>
                                <span class="label <?php echo $badge_class; ?>">
                                    <?php echo number_format($qty); ?>
                                </span>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#unit_stock_datatable').DataTable();
});
</script>