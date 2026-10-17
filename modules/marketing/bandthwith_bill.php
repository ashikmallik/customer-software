<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

/*
 * REQUIRED TABLE (create this once in your database):
 *
 * CREATE TABLE bandwidth_bill (
 *   id INT AUTO_INCREMENT PRIMARY KEY,
 *   date DATE NOT NULL,
 *   customer_name VARCHAR(255) NOT NULL,
 *   customer_phone VARCHAR(50) NULL,
 *   billing_month VARCHAR(20) NULL,
 *   items TEXT NULL,
 *   total_quantity DECIMAL(15,2) DEFAULT 0,
 *   total_purchase DECIMAL(15,2) DEFAULT 0,
 *   total_benefit DECIMAL(15,2) DEFAULT 0,
 *   total_sale_price DECIMAL(15,2) DEFAULT 0,
 *   partial_paid DECIMAL(15,2) DEFAULT 0,
 *   due DECIMAL(15,2) DEFAULT 0,
 *   payment_status VARCHAR(20) DEFAULT 'UNPAID',
 *   remarks TEXT NULL,
 *   created_at DATETIME DEFAULT CURRENT_TIMESTAMP
 * );
 */

// Delete Action
if ($obj->userWorkPermission($acc, 'delete')) {
    $dltoken = isset($_GET['dltoken']) ? (int) $_GET['dltoken'] : NULL;
    if (!empty($dltoken)) {
        if ($obj->Delete_data("bandwidth_bill", "id='$dltoken'")) {
            $obj->notificationStore('Bill Deleted Successfully', 'success');
            echo '<script>window.location = "?q=bandthwith_bill";</script>';
        } else {
            $obj->notificationStore('Delete Failed', 'danger');
            echo '<script>window.location = "?q=bandthwith_bill";</script>';
        }
    }
}

/**
 * Build items[] (with all purchase/sale calculations) from posted arrays.
 * Also returns grand totals: total_quantity, total_purchase, total_benefit, total_sale_price
 */
function build_reseller_items_from_post()
{
    $products  = isset($_POST['product']) ? $_POST['product'] : array();
    $quantities= isset($_POST['quantity']) ? $_POST['quantity'] : array();
    $rate_scls = isset($_POST['rate_scl']) ? $_POST['rate_scl'] : array();
    $nttns     = isset($_POST['nttn']) ? $_POST['nttn'] : array();
    $rate_zfcs = isset($_POST['rate_zfc']) ? $_POST['rate_zfc'] : array();
    $item_remk = isset($_POST['item_remarks']) ? $_POST['item_remarks'] : array();

    $items = array();
    $total_quantity   = 0;
    $total_purchase   = 0;
    $total_benefit    = 0;
    $total_sale_price = 0;

    foreach ($products as $key => $product) {
        $product = trim($product);
        if ($product === '') {
            continue; // skip empty rows
        }

        $qty      = isset($quantities[$key]) ? (float) $quantities[$key] : 0;
        $rate_scl = isset($rate_scls[$key]) ? (float) $rate_scls[$key] : 0;
        $nttn     = isset($nttns[$key]) ? (float) $nttns[$key] : 0;
        $rate_zfc = isset($rate_zfcs[$key]) ? (float) $rate_zfcs[$key] : 0;
        $remarks  = isset($item_remk[$key]) ? trim($item_remk[$key]) : '';

        // Purchase side (NTTN is added to RATE-SCL before VAT is applied)
        $base       = $rate_scl + $nttn;
        $vat        = $base * 0.05;
        $with_vat   = $base + $vat;
        $purchase_total = $qty * $with_vat;

        // Sale side
        $profit_per_unit = $rate_zfc - $with_vat;
        $benefit         = $profit_per_unit * $qty;
        $sale_total      = $qty * $rate_zfc;

        $items[] = array(
            'product'          => $product,
            'quantity'         => $qty,
            'rate_scl'         => $rate_scl,
            'nttn'             => $nttn,
            'vat'              => $vat,
            'with_vat'         => $with_vat,
            'purchase_total'   => $purchase_total,
            'rate_zfc'         => $rate_zfc,
            'profit_per_unit'  => $profit_per_unit,
            'benefit'          => $benefit,
            'sale_total'       => $sale_total,
            'remarks'          => $remarks
        );

        $total_quantity   += $qty;
        $total_purchase   += $purchase_total;
        $total_benefit    += $benefit;
        $total_sale_price += $sale_total;
    }

    return array($items, $total_quantity, $total_purchase, $total_benefit, $total_sale_price);
}

// Insert Action
if (isset($_POST['save_data'])) {
    list($items, $total_quantity, $total_purchase, $total_benefit, $total_sale_price) = build_reseller_items_from_post();

    $partial_paid = isset($_POST['partial_paid']) ? (float) $_POST['partial_paid'] : 0;
    $due = $total_sale_price - $partial_paid;

    $form_data = array(
        'date'              => $_POST['date'],
        'customer_name'     => $_POST['customer_name'],
        'customer_phone'    => $_POST['customer_phone'],
        'billing_month'     => $_POST['billing_month'],
        'items'             => json_encode($items),
        'total_quantity'    => $total_quantity,
        'total_purchase'    => $total_purchase,
        'total_benefit'     => $total_benefit,
        'total_sale_price'  => $total_sale_price,
        'partial_paid'      => $partial_paid,
        'due'               => $due,
        'payment_status'    => $_POST['payment_status'],
        'remarks'           => $_POST['remarks']
    );

    if (empty($items)) {
        $obj->notificationStore('Please add at least one product', 'danger');
    } elseif ($obj->Insert_data("bandwidth_bill", $form_data)) {
        $obj->notificationStore('Bill Added Successfully', 'success');
        echo '<script>window.location = "?q=bandthwith_bill";</script>';
    } else {
        $obj->notificationStore('Insertion Failed', 'danger');
    }
}

// Update Action
if (isset($_POST['update_data'])) {
    $token = (int) $_POST['id'];

    list($items, $total_quantity, $total_purchase, $total_benefit, $total_sale_price) = build_reseller_items_from_post();

    $partial_paid = isset($_POST['partial_paid']) ? (float) $_POST['partial_paid'] : 0;
    $due = $total_sale_price - $partial_paid;

    $form_data = array(
        'date'              => $_POST['date'],
        'customer_name'     => $_POST['customer_name'],
        'customer_phone'    => $_POST['customer_phone'],
        'billing_month'     => $_POST['billing_month'],
        'items'             => json_encode($items),
        'total_quantity'    => $total_quantity,
        'total_purchase'    => $total_purchase,
        'total_benefit'     => $total_benefit,
        'total_sale_price'  => $total_sale_price,
        'partial_paid'      => $partial_paid,
        'due'               => $due,
        'payment_status'    => $_POST['payment_status'],
        'remarks'           => $_POST['remarks']
    );

    if (empty($items)) {
        $obj->notificationStore('Please add at least one product', 'danger');
    } elseif ($obj->Update_data("bandwidth_bill", $form_data, "`bandwidth_bill`.`id`='$token'")) {
        $obj->notificationStore('Bill Updated Successfully', 'success');
        echo '<script>window.location = "?q=bandthwith_bill";</script>';
    } else {
        $obj->notificationStore('Update Failed', 'danger');
    }
}
?>

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">
<style>
    #datatable_bandwidth_bill_filter input {
        height: 30px;
        border-radius: 5px;
        padding: 5px;
    }
    #billFormPanel {
        display: none;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 20px;
    }
    #billFormPanel .panel-header {
        color: #fff;
        background-color: #00695c;
        padding: 12px 20px;
        font-weight: bold;
        font-family: 'Lucida Sans Unicode', sans-serif;
    }
    #billFormPanel .panel-body {
        background-color: #f8f9fa;
        padding: 25px;
    }
    .item-row {
        background-color: #fff;
        border: 1px solid #e2e2e2;
        border-radius: 4px;
        padding: 8px 5px;
        margin-bottom: 8px;
    }
    .item-row .col-form {
        padding-left: 4px;
        padding-right: 4px;
    }
    .items-col-header {
        font-size: 11px;
        font-weight: 700;
        color: #555;
        text-align: center;
    }
    .purchase-zone { background-color: #dbe9ff; }
    .sale-zone { background-color: #f3ddf1; }
    .items-list-cell {
        max-height: 100px;
        overflow-y: auto;
        display: block;
        font-size: 11px;
    }
    .items-list-cell.collapsed { max-height: 40px; }
</style>

<div class="row">
    <div class="col-md-12 padding_5_px">
        <?php $obj->notificationShowRedirect(); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12 bg-teal-800" style="margin-bottom: 15px; padding:10px 0px 3px 15px; font-size:16px; font-family:Lucida Sans Unicode;">
        <div class="col-md-6">
            <b>Reseller Purchase - Sale Bill</b>
        </div>
    </div>
</div>

<button type="button" class="btn btn-primary mb-3" id="btnAddNewBill">
    <i class="fa fa-plus"></i> Add New Bill
</button>

<!-- Inline Bill Form Panel (Add & Edit share this — single page, no modal) -->
<div id="billFormPanel">
    <form method="POST" id="billForm">
        <input type="hidden" name="id" id="bill_id">
        <div class="panel-header" id="billFormTitle">
            <i class="fa fa-plus-circle"></i> Add New Bill
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-3 form-group">
                    <label style="font-weight: 600;">Date <span class="text-danger">*</span></label>
                    <input type="date" name="date" id="bill_date" class="form-control" required>
                </div>
                <div class="col-md-3 form-group">
                    <label style="font-weight: 600;">Customer Name <span class="text-danger">*</span></label>
                    <input type="text" name="customer_name" id="customer_name" class="form-control" required>
                </div>
                <div class="col-md-3 form-group">
                    <label style="font-weight: 600;">Phone</label>
                    <input type="text" name="customer_phone" id="customer_phone" class="form-control">
                </div>
                <div class="col-md-3 form-group">
                    <label style="font-weight: 600;">Billing Month</label>
                    <input type="month" name="billing_month" id="billing_month" class="form-control">
                </div>
            </div>

            <hr>
            <label style="font-weight: 600;">Products <span class="text-danger">*</span></label>
            <div class="row" style="margin: 0;">
                <div class="col-md-1 items-col-header purchase-zone">PRODUCT</div>
                <div class="col-md-1 items-col-header purchase-zone">QTY</div>
                <div class="col-md-1 items-col-header purchase-zone">RATE-SCL</div>
                <div class="col-md-1 items-col-header purchase-zone">NTTN</div>
                <div class="col-md-1 items-col-header purchase-zone">VAT-5%</div>
                <div class="col-md-1 items-col-header purchase-zone">WITH VAT</div>
                <div class="col-md-1 items-col-header purchase-zone">TOTAL</div>
                <div class="col-md-1 items-col-header sale-zone">RATE-ZFC</div>
                <div class="col-md-1 items-col-header sale-zone">PROFIT/UNIT</div>
                <div class="col-md-1 items-col-header sale-zone">BENIFIT</div>
                <div class="col-md-1 items-col-header sale-zone">TOTAL/PRICE</div>
                <div class="col-md-1"></div>
            </div>
            <div id="itemsContainer"></div>

            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="btnAddItemRow">
                <i class="fa fa-plus"></i> Add Product
            </button>

            <hr>
            <div class="row">
                <div class="col-md-3 form-group">
                    <label style="font-weight: 600;">Total Purchase</label>
                    <input type="text" id="grand_total_purchase" class="form-control text-right" readonly style="background:#e9ecef; font-weight:bold;">
                </div>
                <div class="col-md-3 form-group">
                    <label style="font-weight: 600;">Total Benefit</label>
                    <input type="text" id="grand_total_benefit" class="form-control text-right" readonly style="background:#e9ecef; font-weight:bold;">
                </div>
                <div class="col-md-3 form-group">
                    <label style="font-weight: 600;">Total Sale Price</label>
                    <input type="text" id="grand_total_sale" class="form-control text-right" readonly style="background:#e9ecef; font-weight:bold;">
                </div>
                <div class="col-md-3 form-group">
                    <label style="font-weight: 600;">Total Quantity</label>
                    <input type="text" id="grand_total_qty" class="form-control text-right" readonly style="background:#e9ecef; font-weight:bold;">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 form-group">
                    <label style="font-weight: 600;">Partial Paid</label>
                    <input type="number" step="any" name="partial_paid" id="partial_paid" class="form-control" value="0">
                </div>
                <div class="col-md-4 form-group">
                    <label style="font-weight: 600;">Due</label>
                    <input type="text" id="due_amount" class="form-control text-right" readonly style="background:#e9ecef; font-weight:bold;">
                </div>
                <div class="col-md-4 form-group">
                    <label style="font-weight: 600;">Payment Status <span class="text-danger">*</span></label>
                    <select name="payment_status" id="payment_status" class="form-control" required>
                        <option value="UNPAID">UNPAID</option>
                        <option value="PARTIAL">PARTIAL</option>
                        <option value="PAID">PAID</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 form-group">
                    <label style="font-weight: 600;">Remarks</label>
                    <textarea name="remarks" id="bill_remarks" class="form-control" rows="2"></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="background:#fff; border-top:1px solid #dee2e6;">
            <button type="button" class="btn btn-secondary" id="btnCancelBillForm">Cancel</button>
            <button type="submit" name="save_data" id="btnSubmitBillForm" class="btn btn-success" style="padding:6px 20px;">
                <i class="fa fa-save"></i> Save Bill
            </button>
        </div>
    </form>
</div>

<!-- Bills Table -->
<div class="row" style="padding:10px; font-size: 12px;">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered table-striped nowrap" id="datatable_bandwidth_bill" style="width:100%">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Billing Month</th>
                        <th>Products</th>
                        <th>Total Purchase</th>
                        <th>Total Benefit</th>
                        <th>Total Sale Price</th>
                        <th>Partial Paid</th>
                        <th>Due</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $sql = "SELECT * FROM bandwidth_bill ORDER BY id DESC";
                $q = $obj->con->prepare($sql);
                $q->execute();
                $i = 1;
                while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
                    $items = json_decode($row['items'], true);
                    if (!is_array($items)) {
                        $items = array();
                    }
                ?>
                    <tr>
                        <td><?= $i++; ?></td>
                        <td><?= $row['date']; ?></td>
                        <td><?= htmlspecialchars($row['customer_name']); ?></td>
                        <td><?= htmlspecialchars($row['customer_phone']); ?></td>
                        <td><?= htmlspecialchars($row['billing_month']); ?></td>
                        <td>
                            <div class="items-list-cell <?= count($items) > 3 ? 'collapsed' : ''; ?>">
                                <?php foreach ($items as $it): ?>
                                    <div>
                                        <b><?= htmlspecialchars($it['product']); ?></b>
                                        &mdash; Qty <?= rtrim(rtrim(number_format((float)$it['quantity'], 2), '0'), '.'); ?>,
                                        Purchase @<?= number_format((float)$it['with_vat'], 2); ?> = <?= number_format((float)$it['purchase_total'], 2); ?>,
                                        Sale @<?= number_format((float)$it['rate_zfc'], 2); ?> = <?= number_format((float)$it['sale_total'], 2); ?>,
                                        Profit/Unit = <?= number_format((float)$it['profit_per_unit'], 2); ?>,
                                        Benefit = <?= number_format((float)$it['benefit'], 2); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($items) > 3): ?>
                                <button type="button" class="btn btn-xs btn-info toggleItemsView">More</button>
                            <?php endif; ?>
                        </td>
                        <td><?= number_format($row['total_purchase'], 2); ?></td>
                        <td><b><?= number_format($row['total_benefit'], 2); ?></b></td>
                        <td><b><?= number_format($row['total_sale_price'], 2); ?></b></td>
                        <td><?= number_format($row['partial_paid'], 2); ?></td>
                        <td><?= number_format($row['due'], 2); ?></td>
                        <td>
                            <span class="badge <?= $row['payment_status'] == 'PAID' ? 'badge-success' : ($row['payment_status'] == 'PARTIAL' ? 'badge-warning' : 'badge-danger'); ?>">
                                <?= $row['payment_status']; ?>
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-success btn-sm editData"
                                data-id="<?= $row['id']; ?>"
                                data-date="<?= $row['date']; ?>"
                                data-customer_name="<?= htmlspecialchars($row['customer_name']); ?>"
                                data-customer_phone="<?= htmlspecialchars($row['customer_phone']); ?>"
                                data-billing_month="<?= htmlspecialchars($row['billing_month']); ?>"
                                data-partial_paid="<?= $row['partial_paid']; ?>"
                                data-payment_status="<?= htmlspecialchars($row['payment_status']); ?>"
                                data-remarks="<?= htmlspecialchars($row['remarks']); ?>"
                                data-items="<?= htmlspecialchars(json_encode($items), ENT_QUOTES); ?>">
                                <i class="fa fa-edit"></i>
                            </button>

                            <a href="?q=bandthwith_bill&dltoken=<?= $row['id']; ?>"
                               onclick="return confirm('Are you sure to delete?')"
                               class="btn btn-danger btn-sm">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

<script>
$('#datatable_bandwidth_bill').DataTable({
    scrollX: true,
    responsive: true,
    dom: 'Bfrtip',
    buttons: [
        { extend: 'excelHtml5', text: 'Excel', className: 'btn btn-success btn-sm' },
        { extend: 'csvHtml5', text: 'CSV', className: 'btn btn-info btn-sm' },
        { extend: 'print', text: 'Print', className: 'btn btn-primary btn-sm' }
    ]
});

function escapeHtml(str) {
    return $('<div>').text(str === undefined || str === null ? '' : str).html();
}

function itemRowHtml(data) {
    data = data || {};
    var product  = (data.product  !== undefined && data.product  !== null) ? data.product  : '';
    var qty      = (data.quantity !== undefined && data.quantity !== null) ? data.quantity  : '';
    var rateScl  = (data.rate_scl !== undefined && data.rate_scl !== null) ? data.rate_scl  : '';
    var nttn     = (data.nttn     !== undefined && data.nttn     !== null) ? data.nttn      : '';
    var rateZfc  = (data.rate_zfc !== undefined && data.rate_zfc !== null) ? data.rate_zfc  : '';
    var remarks  = (data.remarks  !== undefined && data.remarks  !== null) ? data.remarks   : '';

    return '' +
    '<div class="item-row row align-items-center" style="margin:0;">' +
        '<div class="col-md-1 col-form"><input type="text" name="product[]" class="form-control form-control-sm" placeholder="Product" value="' + escapeHtml(product) + '" required></div>' +
        '<div class="col-md-1 col-form"><input type="number" step="any" name="quantity[]" class="form-control form-control-sm calc-field" placeholder="Qty" value="' + escapeHtml(qty) + '" required></div>' +
        '<div class="col-md-1 col-form"><input type="number" step="any" name="rate_scl[]" class="form-control form-control-sm calc-field" placeholder="0.00" value="' + escapeHtml(rateScl) + '" required></div>' +
        '<div class="col-md-1 col-form"><input type="number" step="any" name="nttn[]" class="form-control form-control-sm calc-field" placeholder="0.00" value="' + escapeHtml(nttn) + '"></div>' +
        '<div class="col-md-1 col-form"><input type="text" class="form-control form-control-sm out-vat" readonly></div>' +
        '<div class="col-md-1 col-form"><input type="text" class="form-control form-control-sm out-withvat" readonly></div>' +
        '<div class="col-md-1 col-form"><input type="text" class="form-control form-control-sm out-purchasetotal" readonly></div>' +
        '<div class="col-md-1 col-form"><input type="number" step="any" name="rate_zfc[]" class="form-control form-control-sm calc-field" placeholder="0.00" value="' + escapeHtml(rateZfc) + '" required></div>' +
        '<div class="col-md-1 col-form"><input type="text" class="form-control form-control-sm out-profitunit" readonly></div>' +
        '<div class="col-md-1 col-form"><input type="text" class="form-control form-control-sm out-benefit" readonly></div>' +
        '<div class="col-md-1 col-form"><input type="text" class="form-control form-control-sm out-saletotal" readonly></div>' +
        '<div class="col-md-1 col-form text-center"><button type="button" class="btn btn-danger btn-sm removeItemRow"><i class="fa fa-trash"></i></button></div>' +
        '<div class="col-md-12 col-form mt-1"><input type="text" name="item_remarks[]" class="form-control form-control-sm" placeholder="Item remarks (optional)" value="' + escapeHtml(remarks) + '"></div>' +
    '</div>';
}

function addItemRow(data) {
    var $row = $(itemRowHtml(data));
    $('#itemsContainer').append($row);
    calcRow($row);
}

function calcRow($row) {
    var qty     = parseFloat($row.find('input[name="quantity[]"]').val()) || 0;
    var rateScl = parseFloat($row.find('input[name="rate_scl[]"]').val()) || 0;
    var nttn    = parseFloat($row.find('input[name="nttn[]"]').val()) || 0;
    var rateZfc = parseFloat($row.find('input[name="rate_zfc[]"]').val()) || 0;

    var base = rateScl + nttn;
    var vat = base * 0.05;
    var withVat = base + vat;
    var purchaseTotal = qty * withVat;

    var profitPerUnit = rateZfc - withVat;
    var benefit = profitPerUnit * qty;
    var saleTotal = qty * rateZfc;

    $row.find('.out-vat').val(vat.toFixed(2));
    $row.find('.out-withvat').val(withVat.toFixed(2));
    $row.find('.out-purchasetotal').val(purchaseTotal.toFixed(2));
    $row.find('.out-profitunit').val(profitPerUnit.toFixed(2));
    $row.find('.out-benefit').val(benefit.toFixed(2));
    $row.find('.out-saletotal').val(saleTotal.toFixed(2));

    calcGrandTotals();
}

function calcGrandTotals() {
    var totalQty = 0, totalPurchase = 0, totalBenefit = 0, totalSale = 0;

    $('#itemsContainer .item-row').each(function () {
        totalQty      += parseFloat($(this).find('input[name="quantity[]"]').val()) || 0;
        totalPurchase += parseFloat($(this).find('.out-purchasetotal').val()) || 0;
        totalBenefit  += parseFloat($(this).find('.out-benefit').val()) || 0;
        totalSale     += parseFloat($(this).find('.out-saletotal').val()) || 0;
    });

    $('#grand_total_qty').val(totalQty.toFixed(2));
    $('#grand_total_purchase').val(totalPurchase.toFixed(2));
    $('#grand_total_benefit').val(totalBenefit.toFixed(2));
    $('#grand_total_sale').val(totalSale.toFixed(2));

    calcDue();
}

function calcDue() {
    var totalSale = parseFloat($('#grand_total_sale').val()) || 0;
    var partialPaid = parseFloat($('#partial_paid').val()) || 0;
    $('#due_amount').val((totalSale - partialPaid).toFixed(2));
}

$(document).on('input', '.calc-field', function () {
    calcRow($(this).closest('.item-row'));
});

$(document).on('input', '#partial_paid', function () {
    calcDue();
});

$(document).on('click', '.removeItemRow', function () {
    $(this).closest('.item-row').remove();
    calcGrandTotals();
});

$('#btnAddItemRow').on('click', function () {
    addItemRow();
});

function resetBillForm() {
    $('#billForm')[0].reset();
    $('#bill_id').val('');
    $('#itemsContainer').empty();
    addItemRow();
    calcGrandTotals();
}

function openFormForAdd() {
    resetBillForm();
    $('#billFormTitle').html('<i class="fa fa-plus-circle"></i> Add New Bill');
    $('#btnSubmitBillForm').attr('name', 'save_data').html('<i class="fa fa-save"></i> Save Bill');
    $('#bill_date').val(new Date().toISOString().slice(0, 10));
    $('#partial_paid').val(0);
    $('#billFormPanel').slideDown();
    $('html, body').animate({ scrollTop: $('#billFormPanel').offset().top - 20 }, 300);
}

function openFormForEdit(btn) {
    var $btn = $(btn);

    $('#bill_id').val($btn.attr('data-id'));
    $('#bill_date').val($btn.attr('data-date'));
    $('#customer_name').val($btn.attr('data-customer_name'));
    $('#customer_phone').val($btn.attr('data-customer_phone'));
    $('#billing_month').val($btn.attr('data-billing_month'));
    $('#partial_paid').val($btn.attr('data-partial_paid'));
    $('#payment_status').val($btn.attr('data-payment_status'));
    $('#bill_remarks').val($btn.attr('data-remarks'));

    $('#itemsContainer').empty();

    var items = [];
    var rawItems = $btn.attr('data-items');
    if (rawItems) {
        try { items = JSON.parse(rawItems); } catch (e) { items = []; }
    }
    if (!Array.isArray(items) || items.length === 0) {
        addItemRow();
    } else {
        items.forEach(function (it) { addItemRow(it); });
    }
    calcGrandTotals();

    $('#billFormTitle').html('<i class="fa fa-edit"></i> Update Bill Details');
    $('#btnSubmitBillForm').attr('name', 'update_data').html('<i class="fa fa-save"></i> Update Bill');

    $('#billFormPanel').slideDown();
    $('html, body').animate({ scrollTop: $('#billFormPanel').offset().top - 20 }, 300);
}

$('#btnAddNewBill').on('click', function () { openFormForAdd(); });
$(document).on('click', '.editData', function () { openFormForEdit(this); });

$('#btnCancelBillForm').on('click', function () {
    $('#billFormPanel').slideUp();
    resetBillForm();
});

$('#billForm').on('submit', function (e) {
    if ($('#itemsContainer .item-row').length === 0) {
        e.preventDefault();
        alert('Please add at least one product.');
    }
});

$(document).on('click', '.toggleItemsView', function () {
    var btn = $(this);
    var cell = btn.closest('td').find('.items-list-cell');
    cell.toggleClass('collapsed');
    btn.text(btn.text() === 'More' ? 'Less' : 'More');
});

// Start with one empty row ready
addItemRow();
</script>