<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');

$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

// Get selected warehouse
$selected_warehouse = isset($_GET['warehouse_id']) ? (int)$_GET['warehouse_id'] : 0;

// ========== Link + Product wise stock out (one query, grouped) =================
$sql = "
    SELECT s.ag_id, s.item_id,
           SUM(s.item_qty)   AS total_qty,
           MAX(s.entry_date) AS last_date
    FROM tbl_stock s
    INNER JOIN tbl_agent a ON a.ag_id = s.ag_id
    WHERE s.status = '0'
      AND s.item_qty > 0
";
if (!empty($selected_warehouse)) {
    $sql .= " AND s.warehouse_id = " . $selected_warehouse;
}
$sql .= " GROUP BY s.ag_id, s.item_id";

$items = array();
foreach ($obj->view_all("tbl_item") as $it) {
    $items[$it['item_id']] = $it;
}

$links = array();
foreach ($obj->raw_sql($sql) as $row) {
    $ag_id = $row['ag_id'];
    if (!isset($links[$ag_id])) {
        $links[$ag_id] = array('last_date' => $row['last_date'], 'total_qty' => 0, 'products' => array());
    }
    if ($row['last_date'] > $links[$ag_id]['last_date']) {
        $links[$ag_id]['last_date'] = $row['last_date'];
    }
    $links[$ag_id]['total_qty'] += (float)$row['total_qty'];
    $links[$ag_id]['products'][] = array(
        'name' => isset($items[$row['item_id']]) ? $items[$row['item_id']]['item_name'] : 'Unknown Item',
        'unit' => isset($items[$row['item_id']]) ? $items[$row['item_id']]['unit'] : '',
        'qty'  => (float)$row['total_qty'],
    );
}

$grand_total_qty = 0;
?>

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">

<div class="row">
    <div class="col-md-12 bg-teal-800" style="margin-bottom:15px;padding:10px 0px 3px 15px;font-size:16px;font-family:Lucida Sans Unicode;">
        <div class="col-md-4">
            <b>Link Wise Stock Out Information</b>
        </div>
        <div class="col-md-4">
            <form method="get" style="margin:0;">
                <input type="hidden" name="q" value="link_wise_stock_out">
                <select name="warehouse_id" class="form-control" onchange="this.form.submit()" style="width: 280px;">
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
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?php $obj->notificationShowRedirect(); ?>
    </div>
</div>

<div class="row" style="padding:10px;font-size:12px;">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped" id="example">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Last Date</th>
                        <th>Link Name</th>
                        <th>Products (Used Qty)</th>
                        <th>Total Used Qty</th>
                    </tr>
                </thead>
                <tbody>

                <?php
                $i = 0;
                foreach ($links as $ag_id => $link) {
                    $i++;
                    $grand_total_qty += $link['total_qty'];

                    $agent = $obj->details_by_cond('tbl_agent', "ag_id=" . (int)$ag_id);
                    ?>

                    <tr>
                        <td><?php echo $i; ?></td>

                        <td data-order="<?php echo date('Y-m-d', strtotime($link['last_date'])); ?>">
                            <?php echo !empty($link['last_date']) ? date("d-m-Y", strtotime($link['last_date'])) : ''; ?>
                        </td>

                        <td>
                            <a href="?q=view_individual_cus&token=<?php echo $ag_id; ?>">
                                <?php echo !empty($agent['link_name']) ? $agent['link_name'] : $agent['ag_name']; ?>
                            </a>
                        </td>

                        <td>
                            <?php foreach ($link['products'] as $p) { ?>
                                <div><b><?php echo htmlspecialchars($p['name']); ?></b> : <?php echo $p['qty'] . ' ' . htmlspecialchars($p['unit']); ?></div>
                            <?php } ?>
                        </td>

                        <td><b><?php echo $link['total_qty']; ?></b></td>
                    </tr>

                <?php } ?>

                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-right">Grand Total Used Qty</th>
                        <th><?php echo $grand_total_qty; ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#example').DataTable({
        dom: 'lBfrtip',
        buttons: ['print'],
        order: [[1, 'desc']]
    });
});
</script>

<script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
