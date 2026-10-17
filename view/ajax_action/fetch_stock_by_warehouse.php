<?php
    include '../../model/oop.php';
    include '../../model/Bill.php';
    include('../../model/Mikrotik.php');
    $obj = new Controller();

$warehouse_id = $_POST['warehouse_id'];

echo '<option value="">Select Item</option>';
foreach ($obj->view_all_by_cond("tbl_stock_item", "warehouse_id='$warehouse_id'") as $item) {
    $cat = $obj->details_by_cond('tbl_stock_category', "cat_id=".$item['cat_id']);
    $codenote = $obj->details_by_cond('tbl_item', "item_id=".$item['item_id']);
    echo "<option value='{$codenote['item_id']}'>{$codenote['item_name']}:: {$codenote['codeandnode']}</option>";
}
?>