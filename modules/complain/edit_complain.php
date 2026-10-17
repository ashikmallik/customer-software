<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');

$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

$complain          = $obj->details_by_cond('tbl_complains', 'id='.$_GET['id']);
$complain_customer = $obj->details_by_cond('tbl_agent', 'ag_id='.$complain['customer_id']);

// Company Info for selected complain
$complain_company  = $obj->details_by_cond('tbl_agent', 'company_id='.$complain['company_id']);

$customers   = $obj->view_all_by_cond("vw_agent", "ag_status=1");
$templates   = $obj->view_all("tbl_complain_templates");
$employees   = $obj->view_all_by_cond('tbl_employee','employee_status=1');
$warehouses  = $obj->view_all("tbl_warehouse");

$image_upload_dir = 'modules/complain/images/'; // adjust path as per your project structure
if (!is_dir($image_upload_dir)) {
    mkdir($image_upload_dir, 0755, true);
}
// ==================== Form Submit ====================
if (isset($_POST['submit'])) {
    
    $image_name = $complain['image'] ?? '';
 
    // ---- Handle Image Upload/Update ----
    if (isset($_FILES['complain_image']) && $_FILES['complain_image']['error'] == 0) {
 
        $allowed_ext  = array('jpg', 'jpeg', 'png', 'gif', 'webp');
        $max_size     = 2 * 1024 * 1024; // 2MB
        $file_tmp     = $_FILES['complain_image']['tmp_name'];
        $file_size    = $_FILES['complain_image']['size'];
        $file_ext     = strtolower(pathinfo($_FILES['complain_image']['name'], PATHINFO_EXTENSION));
 
        if (in_array($file_ext, $allowed_ext) && $file_size <= $max_size) {
 
            // Delete old image if exists, before saving new one
            if (!empty($complain['image']) && file_exists($image_upload_dir.$complain['image'])) {
                unlink($image_upload_dir.$complain['image']);
            }
 
            $new_image_name = 'complain_'.$complain['id'].'_'.time().'.'.$file_ext;
            if (move_uploaded_file($file_tmp, $image_upload_dir.$new_image_name)) {
                $image_name = $new_image_name;
            }
        } else {
            $obj->notificationStore('Invalid image file. Only JPG, JPEG, PNG, GIF, WEBP allowed (max 2MB).', 'danger');
            echo '<script> window.location="?q=edit_complain&id='.$complain['id'].'"; </script>';
            exit;
        }
    }
 
    // ---- Handle Image Removal (optional checkbox) ----
    if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
        if (!empty($complain['image']) && file_exists($image_upload_dir.$complain['image'])) {
            unlink($image_upload_dir.$complain['image']);
        }
        $image_name = '';
    }
    
    
    

    $form_complain = array(
        'details'       => $_POST['details'],
        'note'          => $_POST['note'],
        'customer_id'   => $_POST['customer_id'],
        'solve_by'      => $_POST['employee_id'],
        'update_by'     => $userid,
        'complain_date' => date('Y-m-d H:i:s', strtotime($_POST['complain_date'].' '.$_POST['complain_time'])),
        // solve_date is set only when status changes to solved (view_complains), never on edit
        'image'         => $image_name,
    );

    $obj->Update_data('tbl_complains', $form_complain, "id=".$complain['id']);

    $notification = 'Complain Updated Successfully.';

    // Multiple Stock Out
    if (isset($_POST['warehouse_id']) && is_array($_POST['warehouse_id'])) {
        foreach ($_POST['warehouse_id'] as $key => $warehouse_id) {
            $item_id  = $_POST['item_id'][$key] ?? '';
            $quantity = (int)($_POST['quantity'][$key] ?? 0);

            if (!empty($warehouse_id) && !empty($item_id) && $quantity > 0) {
                $in  = $obj->get_total_stock_in($item_id, $warehouse_id);
                $out = $obj->get_total_stock_out($item_id, $warehouse_id);
                $available = $in - $out;

                if ($quantity <= $available) {
                    $stock_out_data = array(
                        'ag_id'        => $_POST['customer_id'],
                        'item_id'      => $item_id,
                        'warehouse_id' => $warehouse_id,
                        'item_qty'     => $quantity,
                        'description'  => "Edited Complain #".$complain['id']." - ".$_POST['details'],
                        'added_by'     => $userid,
                        'entry_date'   => date("Y/m/d"),
                        'status'       => 0,
                    );
                    $obj->Insert_data("tbl_stock", $stock_out_data);
                }
            }
        }
        $notification .= " & New Stock Issued.";
    }

    $obj->notificationStore($notification, 'success');
    echo '<script> window.location="?q=edit_complain&id='.$complain['id'].'"; </script>';
    exit;
}
?>

<div class="row">
    <div class="col-md-12 bg-teal-800">
        <h4>Edit Complain of <?= $complain_customer['link_name'] ?? '' ?></h4>
    </div>
</div>

<?php $obj->notificationShowRedirect(); ?>

<div class="row">
    <form method="post" enctype="multipart/form-data">
        <div class="col-md-6 col-md-offset-3">

            <!-- Complain Edit Fields -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Complain Date</label>
                        <input type="text" name="complain_date" value="<?= date('d-m-Y', strtotime($complain['complain_date'])) ?>" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Complain Time</label>
                        <input type="text" name="complain_time" value="<?= date('h:i A', strtotime($complain['complain_date'])) ?>" class="form-control">
                    </div>
                </div>
            </div>

            <!-- Company Select - Fixed -->
            <div class="form-group">
                <label>Select Company</label>
                <select name="company_id" id="companySelect" class="form-control select2">
                    <option value="">Select Company</option>
                    <?php 
                    foreach ($customers as $customer) { 
                        $selected = ($customer['company_id'] == $complain['company_id']) ? 'selected' : '';
                    ?>
                        <option value="<?= $customer['company_id'] ?>" <?= $selected ?>>
                            <?= $customer['company_name']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label>Select Link</label>
                <select name="customer_id" class="form-control select2">
                    <?php foreach ($customers as $c) { ?>
                        <option value="<?= $c['ag_id'] ?>" <?= ($c['ag_id'] == $complain['customer_id']) ? 'selected' : '' ?>>
                            <?= $c['link_name'] ?> (<?= $c['cus_id'] ?? '' ?>)
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label>Complain Details</label>
                <textarea class="form-control" name="details" rows="5"><?= htmlspecialchars($complain['details'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>Note</label>
                <textarea class="form-control" name="note" rows="4"><?= htmlspecialchars($complain['note'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>Employee For Solve</label>
                <select name="employee_id" class="form-control select2">
                    <option value="">Select Employee</option>
                    <?php foreach ($employees as $emp) { ?>
                        <option value="<?= $emp['id'] ?>" <?= ($emp['id'] == $complain['solve_by']) ? 'selected' : '' ?>>
                            <?= $emp['employee_name'] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            
             <!-- ============ Image Add/Update Section ============ -->
            <div class="panel panel-default" style="margin-top:15px;">
                <div class="panel-heading">
                    <strong>Complain Image</strong>
                </div>
                <div class="panel-body">
                    <?php if (!empty($complain['image']) && file_exists($image_upload_dir.$complain['image'])) { ?>
                        <div class="form-group">
                            <label>Current Image</label><br>
                            <img src="<?= $image_upload_dir.$complain['image'] ?>" alt="Complain Image" style="max-width:200px; max-height:200px; border:1px solid #ddd; padding:4px;">
                            <div class="checkbox" style="margin-top:8px;">
                                <label>
                                    <input type="checkbox" name="remove_image" value="1"> Remove current image
                                </label>
                            </div>
                        </div>
                    <?php } ?>
 
                    <div class="form-group">
                        <label><?= !empty($complain['image']) ? 'Replace Image' : 'Upload Image' ?></label>
                        <input type="file" name="complain_image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                        <p class="help-block">Allowed: JPG, JPEG, PNG, GIF, WEBP. Max size 2MB.</p>
                    </div>
                </div>
            </div>

            <!-- Multiple Stock Issue Section -->
            <div class="panel panel-info" style="margin-top:25px;">
                <div class="panel-heading">
                    <strong>Issue New Stock for this Complain (Optional)</strong>
                    <button type="button" class="btn btn-xs btn-success pull-right" id="addMoreStock">
                        <span class="glyphicon glyphicon-plus"></span> Add More
                    </button>
                </div>
                <div class="panel-body" id="stockItemsContainer">
                    <div class="stock-row row" style="margin-bottom:15px;">
                        <div class="col-md-4">
                            <label>Warehouse</label>
                            <select name="warehouse_id[]" class="form-control select2 warehouse-select">
                                <option value="">Select Warehouse</option>
                                <?php foreach ($warehouses as $wh) { ?>
                                    <option value="<?= $wh['warehouse_id'] ?>"><?= $wh['warehouse_name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Stock Item</label>
                            <select name="item_id[]" class="form-control select2 item-select">
                                <option value="">Select Item</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Available</label>
                            <input type="text" class="form-control available-qty" readonly>
                        </div>
                        <div class="col-md-2">
                            <label>Issue Qty</label>
                            <input type="text" name="quantity[]" onkeypress="return numbersOnly(event)" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <button type="submit" name="submit" class="btn btn-primary btn-lg">Update Complain & Issue Stock</button>
            </div>
        </div>
    </form>
</div>


<script>
// Add More Stock Row
$('#addMoreStock').click(function() {
    var newRow = `
        <div class="stock-row row" style="margin-bottom:15px; border-top:1px solid #ddd; padding-top:15px;">
            <div class="col-md-4">
                <label>Warehouse</label>
                <select name="warehouse_id[]" class="form-control select2 warehouse-select">
                    <option value="">Select Warehouse</option>
                    <?php foreach ($warehouses as $wh) { ?>
                        <option value="<?= $wh['warehouse_id'] ?>"><?= $wh['warehouse_name'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-4">
                <label>Stock Item</label>
                <select name="item_id[]" class="form-control select2 item-select">
                    <option value="">Select Item</option>
                </select>
            </div>
            <div class="col-md-2">
                <label>Available</label>
                <input type="text" class="form-control available-qty" readonly>
            </div>
            <div class="col-md-2">
                <label>Issue Qty</label>
                <input type="text" name="quantity[]" onkeypress="return numbersOnly(event)" class="form-control">
                <button type="button" class="btn btn-xs btn-danger remove-row" style="margin-top:5px;">Remove</button>
            </div>
        </div>`;
    
    $('#stockItemsContainer').append(newRow);
    $('.select2').select2();
});

// Remove Row
$(document).on('click', '.remove-row', function() {
    $(this).closest('.stock-row').remove();
});

// Warehouse → Item Load
$(document).on('change', '.warehouse-select', function() {
    var wh = $(this).val();
    var $row = $(this).closest('.stock-row');
    if (wh) {
        $.post('view/ajax_action/fetch_stock_by_warehouse.php', {warehouse_id: wh}, function(res) {
            $row.find('.item-select').html(res);
        });
    }
});

// Item → Available Qty
$(document).on('change', '.item-select', function() {
    var $row = $(this).closest('.stock-row');
    var item = $(this).val();
    var wh   = $row.find('.warehouse-select').val();
    if (item && wh) {
        $.post('view/ajax_action/ajax_get_available_stock.php', {item_id: item, warehouse_id: wh}, function(res) {
            $row.find('.available-qty').val(res);
        });
    }
});

function numbersOnly(e) {
    var unicode = e.charCode ? e.charCode : e.keyCode;
    if (unicode != 8) {
        if ((unicode < 2534 || unicode > 2543) && (unicode < 48 || unicode > 57)) return false;
    }
}
</script>

<script>
    $('.select2').select2();
    $('input[name="complain_date"]').datepicker({autoclose:true, format:'dd-mm-yyyy'});
    $('input[name="complain_time"]').timepicker();
</script>