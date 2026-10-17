<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');

$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

// ================= Item Add ==========================
if (isset($_POST['itemAddSubmit'])) {
    $item_form_data = array(
        'item_name' => $_POST['itemName'],
        'item_price' => $_POST['itemPrice'],
        'codeandnode' => $_POST['codeandnode'],
        'unit' => $_POST['itemUnit'] ?? NULL,
    );
    $addItem = $obj->Insert_data("tbl_item", $item_form_data);
    if ($addItem) {
        echo "<script>window.location = '?q=view_all_items';</script>";
    } else {
        $notification = 'Item Insert Failed';
    }
}

// ================= Item Edit ==========================
if (isset($_POST['itemEditSubmit'])) {
    $item_id = $_POST['item_id'];
    $item_name = $_POST['itemName'];
    $item_price = $_POST['itemPrice'];
    $item_unit = $_POST['itemUnit'] ?? NULL;

    $update_data = array(
        'item_name' => $item_name,
        'item_price' => $item_price,
        'codeandnode' => $_POST['codeandnode'],
        'unit' => $item_unit
    );
    $update = $obj->Update_data("tbl_item", $update_data,"item_id='$item_id'");
    
    if ($update) {
        echo "<script>window.location = '?q=view_all_items';</script>";
    } else {
        $notification = 'Item Update Failed';
    }
}

// ================= Delete ==========================
if ($obj->userWorkPermission($acc, 'delete')) {
    $dltoken = isset($_GET['dltoken']) ? $_GET['dltoken'] : NULL;
    if (!empty($dltoken)) {
        $delete = $obj->Delete_data("tbl_item", "item_id='$dltoken'");
        if ($delete) {
            $notification = 'Delete Successful';
        } else {
            $notification = 'Delete Failed';
        }
    }
}
?>

<style>
    .select2-container { width:100% !important; }
    .select2-selection { width:100% !important; }
</style>

<?php if (isset($notification) && !empty($notification)) { ?>
    <div class="alert alert-warning alert-dismissable fade in text-center">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <?php echo $notification; ?>
    </div>
<?php } ?>

<div class="row">
    <div class="col-md-12 bg-grey-600 padding_10_px">
        <div class="col-md-6">
            <h4>View Item Info</h4>
        </div>
        <div class="col-md-3">
            <?php if ($ty == 'SA' || $ty == 'EO') { ?>
                <button type="button" class="btn btn-default pull-right" data-toggle="modal" data-target="#addItemModal">
                    <span class="glyphicon glyphicon-plus"></span> Add Item
                </button>
            <?php } ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped" id="datatable">
                <thead>
                    <tr>
                        <th class="text-center">SL</th>
                        <th class="text-center">Item Name</th>
                        <th class="text-center">Code And Note</th>
                        <th class="text-center">Item Price</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;
                    foreach ($obj->view_all("tbl_item") as $value) {
                        $i++;
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $i; ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($value['item_name']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($value['codeandnode']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($value['item_price']); ?></td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <?php if ($obj->userWorkPermission($acc, 'edit')) { ?>
                                        <button type="button" class="btn btn-default btn-sm edit-btn"
                                            data-item_id="<?php echo $value['item_id']; ?>"
                                            data-item_name="<?php echo htmlspecialchars($value['item_name']); ?>"
                                            data-item_price="<?php echo $value['item_price']; ?>"
                                             data-item_unit="<?php echo $value['unit']; ?>"
                                            data-codeandnode="<?php echo htmlspecialchars($value['codeandnode']); ?>">
                                            <span class="glyphicon glyphicon-edit"></span> Edit
                                        </button>
                                    <?php } ?>

                                    <?php if ($obj->userWorkPermission($acc, 'delete')) { ?>
                                        <a href="?q=view_all_items&dltoken=<?php echo $value['item_id']; ?>"
                                           class="btn btn-xs btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this item?');">
                                            <span class="glyphicon glyphicon-remove"></span>
                                        </a>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================= Add Item Modal ================= -->
<div class="modal fade" id="addItemModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center">Add New Item</h4>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Item Name</label>
                        <input type="text" name="itemName" class="form-control" placeholder="Enter Item Name" required>
                    </div>
                    <div class="form-group">
                        <label>Item Price</label>
                        <input type="number" name="itemPrice" class="form-control" placeholder="Enter Item Price" required>
                    </div>
                    <div class="form-group">
                        <label>Code And Note</label>
                        <input type="text" name="codeandnode" class="form-control" placeholder="Enter code and Note" required>
                    </div>
                    <div class="form-group">
                        <label>Item Unit</label>
                        <select name="itemUnit" class="form-control" required>
                            <option value="">Select Unit</option>
                            <option value="Meter">Meter</option>
                            <option value="Pcs">Pcs</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="itemAddSubmit">Save Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= Edit Item Modal ================= -->
<div class="modal fade" id="editItemModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center">Edit Item</h4>
            </div>
            <form method="post">
                <input type="hidden" name="item_id" id="edit_item_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Item Name</label>
                        <input type="text" name="itemName" id="edit_item_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Item Price</label>
                        <input type="number" name="itemPrice" id="edit_item_price" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Code And Note</label>
                        <input type="text"
                           name="codeandnode"
                           id="edit_codeandnode"
                           class="form-control"
                           required>
                    </div>
                    <div class="form-group">
                        <label>Item Unit</label>
                        <select name="itemUnit" id="edit_item_unit" class="form-control" required>
    <option value="">Select Unit</option>
    <option value="Meter">Meter</option>
    <option value="Pcs">Pcs</option>
</select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="itemEditSubmit">Update Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for Edit Modal -->
<script>
$('.edit-btn').click(function () {

    $('#edit_item_id').val($(this).data('item_id'));
    $('#edit_item_name').val($(this).data('item_name'));
    $('#edit_item_price').val($(this).data('item_price'));
    $('#edit_codeandnode').val($(this).data('codeandnode'));

    // Unit Selected
    $('#edit_item_unit').val($(this).data('item_unit')).trigger('change');

    $('#editItemModal').modal('show');
});
</script>