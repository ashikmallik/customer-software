<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');

$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

// Get selected warehouse
$selected_warehouse = isset($_GET['warehouse_id']) ? (int)$_GET['warehouse_id'] : 0;

// ========== Delete Function Start =================
if ($obj->userWorkPermission($acc, 'delete')) {
    if (isset($_GET['dltoken']) && !empty($_GET['dltoken'])) {
        $dltoken = (int)$_GET['dltoken'];

        if ($obj->Delete_data("tbl_stock", "id='$dltoken'")) {
            $obj->notificationStore('Delete Successfull', 'success');
        } else {
            $obj->notificationStore('Sorry ! Delete Failed. Try Again');
        }

        $redirect = "?q=all_stock_out";
        if (!empty($selected_warehouse)) {
            $redirect .= "&warehouse_id=" . $selected_warehouse;
        }

        echo '<script>window.location="' . $redirect . '";</script>';
        exit;
    }
}
?>

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">

<div class="row">
    <div class="col-md-12 bg-teal-800" style="margin-bottom:15px;padding:10px 0px 3px 15px;font-size:16px;font-family:Lucida Sans Unicode;">
        <div class="col-md-4">
            <b>Link Wise Stock Out Information</b>
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
                        <th>Date</th>
                        <th>Link Name</th>
                        <th>Total Used Qty</th>
                        
                    </tr>
                </thead>
                <tbody>

                <?php
                $i = 0;

                $sql = "
                    SELECT s.*
                    FROM tbl_stock s
                    INNER JOIN (
                        SELECT ag_id, MAX(id) AS max_id
                        FROM tbl_stock
                        WHERE status = '0'
                          AND item_qty > 0
                        GROUP BY ag_id
                    ) t ON s.id = t.max_id
                    INNER JOIN tbl_agent a ON a.ag_id = s.ag_id
                    ";
                    
                    if (!empty($selected_warehouse)) {
                        $sql .= " WHERE s.warehouse_id = " . (int)$selected_warehouse;
                    }
                    
                    $sql .= " ORDER BY s.id DESC";
                    
                    $expense_detail = $obj->raw_sql($sql);

                foreach ($expense_detail as $value) {

                    $i++;

                    $item = $obj->details_by_cond(
                        'tbl_item',
                        "item_id=" . (int)$value['item_id']
                    );

                    $agent = $obj->details_by_cond(
                        'tbl_agent',
                        "ag_id=" . (int)$value['ag_id']
                    );
                        $row = $obj->raw_sql("
    SELECT SUM(item_qty) AS total_qty
    FROM tbl_stock
    WHERE status = 0
    AND ag_id = 50
");

                    ?>

                    <tr>
                        <td><?php echo $i; ?></td>

                        <td>
                            <?php
                            echo !empty($value['entry_date'])
                                ? date("d-m-Y", strtotime($value['entry_date']))
                                : '';
                            ?>
                        </td>

                        <td>
                            <a href="?q=view_individual_cus&token=<?php echo $value['ag_id']; ?>">
                                <?php
                                echo !empty($agent['link_name'])
                                    ? $agent['link_name']
                                    : $agent['ag_name'];
                                ?>
                            </a>
                        </td>
                        <td><?php echo $row[0]['total_qty'];; ?></td>

                    </tr>

                <?php } ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#example').DataTable({
        dom: 'Bfrtip',
        buttons: ['print'],
        order: [[1, 'desc']]
    });
});
</script>

<script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>