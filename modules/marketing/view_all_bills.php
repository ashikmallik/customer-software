<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$intoken = isset($_GET['intoken']) ? $_GET['intoken'] : NULL;
$actoken = isset($_GET['actoken']) ? $_GET['actoken'] : NULL;

// Delete Action
if ($obj->userWorkPermission($acc, 'delete')) {
    $dltoken = isset($_GET['dltoken']) ? $_GET['dltoken'] : NULL;
    if (!empty($dltoken)) {
        if ($obj->Delete_data("bills", "id='$dltoken'")) {
            $obj->notificationStore('Bill Deleted Successfully', 'success');
            echo '<script>window.location = "?q=view_all_bills";</script>';
        } else {
            $obj->notificationStore('Delete Failed', 'danger');
            echo '<script>window.location = "?q=view_all_bills";</script>';
        }
    }
}




// Month filter (1-12), applies to the list and the Paid / Due cards
$month_names = array(1 => 'January', 'February', 'March', 'April', 'May', 'June',
                     'July', 'August', 'September', 'October', 'November', 'December');
$filter_month = isset($_GET['month']) ? (int) $_GET['month'] : 0;
if (!isset($month_names[$filter_month])) {
    $filter_month = 0;
}

/**
 * Format a number with up to $max decimals, trailing zeros removed, but at least $min decimals.
 * Used so that "qty x unit price = total" shown on screen/PDF always matches.
 */
function bill_num($value, $min = 2, $max = 4)
{
    $s = number_format((float) $value, $max, '.', ',');
    $s = rtrim($s, '0');
    $dot = strpos($s, '.');
    $decimals = strlen($s) - $dot - 1;
    if ($decimals < $min) {
        $s .= str_repeat('0', $min - $decimals);
    }
    return rtrim($s, '.');
}

/**
 * Grand total of a bill = sum of each item's total rounded to 2 decimals
 * (same way the form calculates it), falls back to stored total_price.
 */
function bill_grand_total($items, $stored_total)
{
    if (empty($items)) {
        return round((float) $stored_total, 2);
    }
    $sum = 0;
    foreach ($items as $it) {
        $line = isset($it['total_price']) ? (float) $it['total_price'] : ((float) $it['quantity'] * (float) $it['unit_price']);
        $sum += round($line, 2);
    }
    return round($sum, 2);
}

/**
 * Helper: build the items[] array (with computed totals) from posted arrays
 * and return [$items, $grand_total, $total_quantity, $avg_unit_price]
 */
function build_items_from_post()
{
    $item_names  = isset($_POST['item_name']) ? $_POST['item_name'] : array();
    $quantities  = isset($_POST['quantity']) ? $_POST['quantity'] : array();
    $units       = isset($_POST['unit']) ? $_POST['unit'] : array();
    $unit_prices = isset($_POST['unit_price']) ? $_POST['unit_price'] : array();

    $items = array();
    $grand_total = 0;
    $total_quantity = 0;

    foreach ($item_names as $key => $name) {
        $name = trim($name);
        if ($name === '') {
            continue; // skip empty rows
        }

        $qty   = isset($quantities[$key]) ? (float) $quantities[$key] : 0;
        $unit  = isset($units[$key]) ? trim($units[$key]) : '';
        $price = isset($unit_prices[$key]) ? (float) $unit_prices[$key] : 0;
        // Round each line to 2 decimals; grand total is the sum of rounded lines,
        // exactly like the form shows it, so the figures always match.
        $total = round($qty * $price, 2);

        $items[] = array(
            'item_name'   => $name,
            'quantity'    => $qty,
            'unit'        => $unit,
            'unit_price'  => $price,
            'total_price' => $total
        );

        $grand_total    += $total;
        $total_quantity += $qty;
    }

    $grand_total = round($grand_total, 2);

    // Average unit price across all items (protects against division by zero)
    $avg_unit_price = ($total_quantity > 0) ? ($grand_total / $total_quantity) : 0;

    return array($items, $grand_total, $total_quantity, $avg_unit_price);
}

// Insert Action
if (isset($_POST['save_data'])) {
    list($items, $grand_total, $total_quantity, $avg_unit_price) = build_items_from_post();

    $form_data = array(
        'date'              => $_POST['date'],
        'reference_link'    => $_POST['reference_link'],
        'link_type'         => $_POST['link_name'],
        'phone_number'      => $_POST['phone_number'],
        'customer_name'     => $_POST['customer_name'],
        'customer_address'  => $_POST['customer_address'],
        'bill_type'         => $_POST['bill_type'],
        'items'             => json_encode($items),
        'quantity'          => $total_quantity,
        'unit_price'        => $avg_unit_price,
        'total_price'       => $grand_total,
        'remarks'           => $_POST['remarks'],
        'bill_status'       => $_POST['bill_status']
    );

    if (empty($items)) {
        $obj->notificationStore('Please add at least one item', 'danger');
    } elseif ($obj->Insert_data("bills", $form_data)) {
        $obj->notificationStore('Bill Added Successfully', 'success');
        echo '<script>window.location = "?q=view_all_bills";</script>';
    } else {
        $obj->notificationStore('Insertion Failed', 'danger');
    }
}

// Update Action
if (isset($_POST['update_data'])) {
    $token = $_POST['id'];

    list($items, $grand_total, $total_quantity, $avg_unit_price) = build_items_from_post();

    $form_data = array(
        'date'              => $_POST['date'],
        'reference_link'    => $_POST['reference_link'],
        'link_type'         => $_POST['link_name'],
        'customer_name'     => $_POST['customer_name'],
        'phone_number'      => $_POST['phone_number'],
        'customer_address'  => $_POST['customer_address'],
        'bill_type'         => $_POST['bill_type'],
        'items'             => json_encode($items),
        'quantity'          => $total_quantity,
        'unit_price'        => $avg_unit_price,
        'total_price'       => $grand_total,
        'remarks'           => $_POST['remarks'],
        'bill_status'       => $_POST['bill_status']
    );

    if (empty($items)) {
        $obj->notificationStore('Please add at least one item', 'danger');
    } elseif ($obj->Update_data("bills", $form_data, "`bills`.`id`='$token'")) {
        $obj->notificationStore('Bill Updated Successfully', 'success');
        echo '<script>window.location = "?q=view_all_bills";</script>';
    } else {
        $obj->notificationStore('Update Failed', 'danger');
    }
}

// Load bills (filtered by month) and calculate Total Paid / Due Amount from the same rows
$sql = "SELECT * FROM bills";
if ($filter_month) {
    $sql .= " WHERE MONTH(`date`) = " . $filter_month;
}
$sql .= " ORDER BY id DESC";
$q = $obj->con->prepare($sql);
$q->execute();

$bill_rows  = array();
$paid_total = 0;
$due_total  = 0;
while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
    $items = json_decode($row['items'], true);
    $row['items_arr']   = is_array($items) ? $items : array();
    $row['grand_total'] = bill_grand_total($row['items_arr'], $row['total_price']);

    if (strtoupper($row['bill_status']) == 'PAID') {
        $paid_total += $row['grand_total'];
    } elseif (strtoupper($row['bill_status']) == 'UNPAID') {
        $due_total += $row['grand_total'];
    }
    $bill_rows[] = $row;
}
?>

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">
<style>
    #datatable_view_bills_filter input {
        height: 30px;
        border-radius: 5px;
        padding: 5px;
    }
    .select2-container{
        width: 339px !important;
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
        padding: 10px 5px;
        margin-bottom: 8px;
    }
    .items-list-cell {
        max-height: 90px;
        overflow-y: auto;
        display: block;
    }
    .items-list-cell.collapsed {
        max-height: 40px;
    }
    
    
    .amount-card {
    display: flex;
    align-items: center;
    border-radius: 8px;
    padding: 15px 20px;
    color: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}
.amount-card-icon {
    font-size: 32px;
    margin-right: 15px;
    opacity: 0.9;
}
.amount-card-label {
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    opacity: 0.9;
}
.amount-card-value {
    font-size: 22px;
    font-weight: bold;
}
.paid-card {
    background-color: #28a745; /* green */
}
.due-card {
    background-color: #dc3545; /* red */
}
</style>

<div class="row">
    <div class="col-md-12 padding_5_px">
        <?php $obj->notificationShowRedirect(); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12 bg-teal-800" style="margin-bottom: 15px; padding:10px 0px 3px 15px; font-size:16px; font-family:Lucida Sans Unicode;">
        <div class="col-md-4">
            <b>View All Bills</b>
        </div>
        <div class="col-md-4">
            <form method="get" style="margin:0;">
                <input type="hidden" name="q" value="view_all_bills">
                <select name="month" class="form-control" onchange="this.form.submit()" style="width: 220px;">
                    <option value="">All Months</option>
                    <?php foreach ($month_names as $m_num => $m_name) { ?>
                        <option value="<?= $m_num; ?>" <?= $filter_month == $m_num ? 'selected' : ''; ?>><?= $m_name; ?></option>
                    <?php } ?>
                </select>
            </form>
        </div>
    </div>
</div>

<div class="row" style="margin-bottom: 20px;">
    <div class="col-md-3">
        <div class="amount-card paid-card">
            <div class="amount-card-icon"><i class="fa fa-check-circle"></i></div>
            <div class="amount-card-info">
                <div class="amount-card-label">Total Paid Amount</div>
                <div class="amount-card-value"><?= number_format($paid_total, 2); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="amount-card due-card">
            <div class="amount-card-icon"><i class="fa fa-exclamation-circle"></i></div>
            <div class="amount-card-info">
                <div class="amount-card-label">Total Due Amount</div>
                <div class="amount-card-value"><?= number_format($due_total, 2); ?></div>
            </div>
        </div>
    </div>
</div>

<button type="button" class="btn btn-primary mb-3" id="btnAddNewBill">
    <i class="fa fa-plus"></i> Add New Bill
</button>


<!-- Inline Bill Form Panel (used for both Add & Edit, no modal, same page) -->
<div id="billFormPanel">
    <form method="POST" id="billForm">
        <input type="hidden" name="id" id="bill_id">
        <div class="panel-header" id="billFormTitle">
            <i class="fa fa-plus-circle"></i> Add New Bill
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-4 form-group">
                    <label style="font-weight: 600; color: #333;">Date <span class="text-danger">*</span></label>
                    <input type="date" name="date" id="bill_date" class="form-control" required style="border-radius: 4px;">
                </div>
                <div class="col-md-4 form-group">
                    <label style="font-weight: 600; color: #333;">Reference <span class="text-danger">*</span></label>
                    <input type="text" name="reference_link" id="bill_reference_link" class="form-control" placeholder="Enter Reference" required style="border-radius: 4px;">
                </div>
                <div class="col-md-4 form-group">
                    <label style="font-weight: 600; color: #333;">Link Name <span class="text-danger">*</span></label>
                    <input type="text" name="link_name" id="bill_link_name" class="form-control" placeholder="Enter Link Name" required style="border-radius: 4px;">
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-3 form-group">
                    <label style="font-weight: 600; color: #333;">Customer Name <span class="text-danger">*</span></label>
                    <input type="text" name="customer_name" id="bill_customer_name" class="form-control" placeholder="Enter Customer Name" required style="border-radius: 4px;">
                </div>
                <div class="col-md-3 form-group">
                    <label style="font-weight: 600; color: #333;">Phone Number <span class="text-danger">*</span></label>
                    <input type="text" name="phone_number" id="phone_number" class="form-control" placeholder="Enter phone number" required style="border-radius: 4px;">
                </div>
                <div class="col-md-6 form-group">
                    <label style="font-weight: 600; color: #333;">Customer Address</label>
                    <input type="text" name="customer_address" id="bill_customer_address" class="form-control" placeholder="Enter Customer Address" style="border-radius: 4px;">
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-6 form-group">
                    <label style="font-weight: 600; color: #333;">Bill Type <span class="text-danger">*</span></label>
                    <select name="bill_type" id="bill_type" class="form-control" required style="border-radius: 4px;">
                        <option value="">Select Bill Type</option>
                        <option value="IMPLEMENTATION BILL">IMPLEMENTATION BILL</option>
                        <option value="MONTHLY CONSUMABLE BILL">MONTHLY CONSUMABLE BILL</option>
                        <option value="MONTHLY CORE READINESS BILL">MONTHLY CORE READINESS BILL</option>
                        <option value="MONTHLY MAINTANANCEBILL(UG)">MONTHLY MAINTANANCEBILL(UG)</option>
                        <option value="MONTHLY MAINTANANCEBILL(OH)">MONTHLY MAINTANANCEBILL(OH)</option>
                        <option value="OTHERS BILL">OTHERS BILL</option>
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label style="font-weight: 600; color: #333;">Bill Status <span class="text-danger">*</span></label>
                    <select name="bill_status" id="bill_status" class="form-control" required style="border-radius: 4px; font-weight: bold;">
                        <option value="UNPAID">UNPAID</option>
                        <option value="PAID">PAID</option>
                    </select>
                </div>
            </div>

            <hr>

            <label style="font-weight: 600; color: #333;">Items <span class="text-danger">*</span></label>
            <div class="row" style="font-size:12px; font-weight:600; color:#555; padding: 0 5px;">
                <div class="col-md-3">Item Name</div>
                <div class="col-md-2">Quantity</div>
                <div class="col-md-2">Unit</div>
                <div class="col-md-2">Unit Price</div>
                <div class="col-md-2">Total</div>
                <div class="col-md-1"></div>
            </div>
            <div id="itemsContainer"></div>

            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="btnAddItemRow">
                <i class="fa fa-plus"></i> Add Item
            </button>

            <div class="row mt-3">
                <div class="col-md-8"></div>
                <div class="col-md-4 form-group text-right">
                    <label style="font-weight: 600; color: #333;">Grand Total</label>
                    <input type="text" id="bill_grand_total" class="form-control text-right" readonly style="background-color: #e9ecef; font-weight: bold; border-radius: 4px; border: 1px solid #ced4da;">
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-12 form-group">
                    <label style="font-weight: 600; color: #333;">Remarks</label>
                    <textarea name="remarks" id="bill_remarks" class="form-control" rows="2" placeholder="Write remarks here..." style="border-radius: 4px; resize: none;"></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="background-color: #fff; border-top: 1px solid #dee2e6;">
            <button type="button" class="btn btn-secondary" id="btnCancelBillForm" style="border-radius: 4px;">Cancel</button>
            <button type="submit" name="save_data" id="btnSubmitBillForm" class="btn btn-success" style="border-radius: 4px; padding: 6px 20px;">
                <i class="fa fa-save"></i> Save Bill
            </button>
        </div>
    </form>
</div>

<!-- Bills Table -->
<div class="row" style="padding:10px; font-size: 12px;">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered table-striped nowrap" id="datatable_view_bills" style="width:100%">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Date</th>
                        <th>Reference</th>
                        <th>Link Name</th>
                        <th>Customer Details</th>
                        <th>Bill Type</th>
                        <th>Items</th>
                        <th>Total Price</th>
                        <th>Remarks</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach ($bill_rows as $row) {
                    $items = $row['items_arr'];
                ?>
                    <tr>
                        <td><?= $i++; ?></td>
                        <td><?= $row['date']; ?></td>
                        <td><?= htmlspecialchars($row['reference_link']); ?></td>
                        <td><?= htmlspecialchars($row['link_type']); ?></td>
                        <td>
                            <b>Name:</b> <?= htmlspecialchars($row['customer_name']); ?><br>
                            <small><b>Add:</b> <?= htmlspecialchars($row['customer_address']); ?></small>
                        </td>
                        <td><?= htmlspecialchars($row['bill_type']); ?></td>
                        <td>
                            <div class="items-list-cell <?= count($items) > 3 ? 'collapsed' : ''; ?>">
                                <?php foreach ($items as $it): ?>
                                    <div>
                                        <b><?= htmlspecialchars($it['item_name']); ?></b>
                                        &mdash; <?= bill_num($it['quantity'], 0); ?> <?= htmlspecialchars($it['unit']); ?>
                                        × <?= bill_num($it['unit_price']); ?>
                                        = <?= number_format(round((float)$it['total_price'], 2), 2); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($items) > 3): ?>
                                <button type="button" class="btn btn-xs btn-info toggleItemsView">More</button>
                            <?php endif; ?>
                        </td>
                        <td><b><?= number_format($row['grand_total'], 2); ?></b></td>
                        <td>
                            <?php
                            $remarks = htmlspecialchars($row['remarks']);
                            if (strlen($remarks) > 20) {
                                echo '<span class="short-text">' . substr($remarks, 0, 20) . '...</span>';
                                echo '<span class="full-text" style="display:none;">' . $remarks . '</span>';
                                echo ' <button type="button" class="btn btn-xs btn-info toggleText">More</button>';
                            } else {
                                echo $remarks;
                            }
                            ?>
                        </td>
                        <td>
                            <button type="button" onclick="return confirm('Are you sure to change status?')"
                                    class="btn btn-sm toggleStatus <?= $row['bill_status'] == 'PAID' ? 'btn-success' : 'btn-danger'; ?>"
                                    data-id="<?= $row['id']; ?>"
                                    data-status="<?= $row['bill_status']; ?>">
                                <?= $row['bill_status']; ?>
                            </button>
                        </td>
                        <td>
                            <a href="/pdf/generate_bill_pdf.php?id=<?= $row['id']; ?>" target="_blank" class="btn btn-secondary btn-sm" title="Print PDF">
                                <i class="fa fa-file-pdf-o"></i>
                            </a>
                            <button type="button" class="btn btn-success btn-sm btnSharePdf"
                                data-id="<?= $row['id']; ?>"
                                data-phone="<?= htmlspecialchars($row['phone_number']); ?>"
                                data-customer="<?= htmlspecialchars($row['customer_name']); ?>"
                                title="Share PDF via WhatsApp">
                                <i class="fa fa-whatsapp"></i>
                            </button>
                            <button type="button" class="btn btn-success btn-sm editData"
                                data-id="<?= $row['id']; ?>"
                                data-date="<?= $row['date']; ?>"
                                data-reference_link="<?= htmlspecialchars($row['reference_link']); ?>"
                                data-link_type="<?= htmlspecialchars($row['link_type']); ?>"
                                data-customer_name="<?= htmlspecialchars($row['customer_name']); ?>"
                                data-phone_number="<?= htmlspecialchars($row['phone_number']); ?>" 
                                data-customer_address="<?= htmlspecialchars($row['customer_address']); ?>"
                                data-bill_type="<?= htmlspecialchars($row['bill_type']); ?>"
                                data-remarks="<?= htmlspecialchars($row['remarks']); ?>"
                                data-bill_status="<?= htmlspecialchars($row['bill_status']); ?>"
                                data-items="<?= htmlspecialchars(json_encode($items), ENT_QUOTES); ?>">
                                <i class="fa fa-edit"></i>
                            </button>

                            <a href="?q=view_all_bills&dltoken=<?= $row['id']; ?>"
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
// Strip "More/Less" toggle buttons and truncated previews out of exported cell text
function cleanExportText($node) {
    var $clone = $node.clone();
    $clone.find('.toggleText, .toggleItemsView').remove();
    $clone.find('.full-text').css('display', '');
    $clone.find('.short-text').remove();

    var lines = [];
    $clone.find('.items-list-cell > div').each(function () {
        lines.push($(this).text().replace(/\s+/g, ' ').trim());
    });
    if (lines.length) {
        return lines.join(' | ');
    }
    return $clone.text().replace(/\s+/g, ' ').trim();
}

var exportOptions = {
    columns: ':not(:last-child)', // exclude the Action column (Edit/Delete buttons)
    format: {
        body: function (data, row, column, node) {
            return cleanExportText($(node));
        }
    }
};

// DataTables Setup
$('#datatable_view_bills').DataTable({
    scrollX: true,
    responsive: true,
    dom: 'lBfrtip',
    buttons: [
        { extend: 'excelHtml5', text: 'Excel', className: 'btn btn-success btn-sm', exportOptions: exportOptions },
        { extend: 'csvHtml5', text: 'CSV', className: 'btn btn-info btn-sm', exportOptions: exportOptions },
        { extend: 'print', text: 'Print', className: 'btn btn-primary btn-sm', exportOptions: exportOptions }
    ]
});

// ---------- Item Row Template ----------
function itemRowHtml(data) {
    data = data || {};
    var itemName  = (data.item_name !== undefined && data.item_name !== null) ? data.item_name : '';
    var qty       = (data.quantity !== undefined && data.quantity !== null) ? data.quantity : '';
    var unit      = (data.unit !== undefined && data.unit !== null) ? data.unit : '';
    var unitPrice = (data.unit_price !== undefined && data.unit_price !== null) ? data.unit_price : '';

    var unitOptions = ['PCS', 'MTR'];
    var unitSelect = '<select name="unit[]" class="form-control" required>';
    unitSelect += '<option value="">Unit</option>';
    unitSelect += '<option value="PCS"' + (unit === 'PCS' ? ' selected' : '') + '>PCS</option>';
    unitSelect += '<option value="MTR"' + (unit === 'MTR' ? ' selected' : '') + '>METER</option>';
    unitSelect += '</select>';

    return '' +
    '<div class="item-row row align-items-center">' +
        '<div class="col-md-3"><input type="text" name="item_name[]" class="form-control" placeholder="Item Name" value="' + escapeHtml(itemName) + '" required></div>' +
        '<div class="col-md-2"><input type="number" step="any" name="quantity[]" class="form-control item-qty" placeholder="0.00" value="' + escapeHtml(qty) + '" required></div>' +
        '<div class="col-md-2">' + unitSelect + '</div>' +
        '<div class="col-md-2"><input type="number" step="any" name="unit_price[]" class="form-control item-price" placeholder="0.00" value="' + escapeHtml(unitPrice) + '" required></div>' +
        '<div class="col-md-2"><input type="text" class="form-control item-total" readonly></div>' +
        '<div class="col-md-1 text-center"><button type="button" class="btn btn-danger btn-sm removeItemRow"><i class="fa fa-trash"></i></button></div>' +
    '</div>';
}

function escapeHtml(str) {
    return $('<div>').text(str).html();
}

function addItemRow(data) {
    var $row = $(itemRowHtml(data));
    $('#itemsContainer').append($row);
    calcRowTotal($row);
}

// Round to 2 decimals the same way PHP round() does (avoids 1.005 -> 1.00 float issue)
function round2(n) {
    return Math.round((n + Number.EPSILON) * 100) / 100;
}

function calcRowTotal($row) {
    var qty = parseFloat($row.find('.item-qty').val()) || 0;
    var price = parseFloat($row.find('.item-price').val()) || 0;
    $row.find('.item-total').val(round2(qty * price).toFixed(2));
    calcGrandTotal();
}

function calcGrandTotal() {
    var total = 0;
    $('#itemsContainer .item-row').each(function () {
        var t = parseFloat($(this).find('.item-total').val()) || 0;
        total += t;
    });
    $('#bill_grand_total').val(round2(total).toFixed(2));
}

$(document).on('input', '.item-qty, .item-price', function () {
    calcRowTotal($(this).closest('.item-row'));
});

$(document).on('click', '.removeItemRow', function () {
    $(this).closest('.item-row').remove();
    calcGrandTotal();
});

$('#btnAddItemRow').on('click', function () {
    addItemRow();
});

// ---------- Show / Reset Form Panel ----------
function resetBillForm() {
    $('#billForm')[0].reset();
    $('#bill_id').val('');
    $('#itemsContainer').empty();
    addItemRow();
    calcGrandTotal();
}

function openFormForAdd() {
    resetBillForm();
    $('#billFormTitle').html('<i class="fa fa-plus-circle"></i> Add New Bill');
    $('#billForm').attr('data-mode', 'add');
    $('#btnSubmitBillForm').attr('name', 'save_data').html('<i class="fa fa-save"></i> Save Bill');
    $('#bill_date').val(new Date().toISOString().slice(0, 10));
    $('#billFormPanel').slideDown();
    $('html, body').animate({ scrollTop: $('#billFormPanel').offset().top - 20 }, 300);
}

function openFormForEdit(btn) {
    var $btn = $(btn);

    $('#bill_id').val($btn.attr('data-id'));
    $('#bill_date').val($btn.attr('data-date'));
    $('#bill_reference_link').val($btn.attr('data-reference_link'));
    $('#bill_link_name').val($btn.attr('data-link_type'));
    $('#bill_customer_name').val($btn.attr('data-customer_name'));
    $('#bill_customer_address').val($btn.attr('data-customer_address'));
    $('#bill_type').val($btn.attr('data-bill_type'));
    $('#bill_status').val($btn.attr('data-bill_status'));
    $('#bill_remarks').val($btn.attr('data-remarks'));
    $('#phone_number').val($btn.attr('data-phone_number'));

    $('#itemsContainer').empty();

    var items = [];
    var rawItems = $btn.attr('data-items');
    if (rawItems) {
        try {
            items = JSON.parse(rawItems);
        } catch (e) {
            items = [];
        }
    }
    if (!Array.isArray(items) || items.length === 0) {
        addItemRow();
    } else {
        items.forEach(function (it) {
            addItemRow(it);
        });
    }
    calcGrandTotal();

    $('#billFormTitle').html('<i class="fa fa-edit"></i> Update Bill Details');
    $('#billForm').attr('data-mode', 'edit');
    $('#btnSubmitBillForm').attr('name', 'update_data').html('<i class="fa fa-save"></i> Update Bill');

    $('#billFormPanel').slideDown();
    $('html, body').animate({ scrollTop: $('#billFormPanel').offset().top - 20 }, 300);
}

$('#btnAddNewBill').on('click', function () {
    openFormForAdd();
});

$(document).on('click', '.editData', function () {
    openFormForEdit(this);
});

$('#btnCancelBillForm').on('click', function () {
    $('#billFormPanel').slideUp();
    resetBillForm();
});

// Prevent submit if no item rows left
$('#billForm').on('submit', function (e) {
    if ($('#itemsContainer .item-row').length === 0) {
        e.preventDefault();
        alert('Please add at least one item.');
    }
});

// Status Toggle via Ajax
$(document).on('click', '.toggleStatus', function () {
    var btn = $(this);
    var id = btn.data('id');
    var status = btn.data('status');
    var nextStatus = (status === 'PAID') ? 'UNPAID' : 'PAID';

    $.ajax({
        url: 'modules/marketing/update_bill_status.php',
        type: 'POST',
        data: {
            id: id,
            status: nextStatus
        },
        success: function (res) {
            if (nextStatus === 'PAID') {
                btn.removeClass('btn-danger').addClass('btn-success');
                btn.text('PAID');
                btn.data('status', 'PAID');
            } else {
                btn.removeClass('btn-success').addClass('btn-danger');
                btn.text('UNPAID');
                btn.data('status', 'UNPAID');
            }
        }
    });
});

// Generic inline "More/Less" toggle (used for Remarks & Items list — replaces the old modal)
$(document).on('click', '.toggleText', function () {
    var btn = $(this);
    var wrapper = btn.closest('td');
    wrapper.find('.short-text, .full-text').toggle();
    btn.text(btn.text() === 'More' ? 'Less' : 'More');
});

$(document).on('click', '.toggleItemsView', function () {
    var btn = $(this);
    var cell = btn.closest('td').find('.items-list-cell');
    cell.toggleClass('collapsed');
    btn.text(btn.text() === 'More' ? 'Less' : 'More');
});

// Init: start with one empty item row ready on page load (in case Add panel opened manually)
addItemRow();




// ---------- Share PDF via WhatsApp (wa.me link, no API key needed) ----------

// Base URL of your PDF generator — adjust to your actual live path
var PDF_BASE_URL = window.location.origin + '/pdf/generate_bill_pdf.php?id=';

function formatWhatsAppNumber(rawNumber) {
    // Keep digits only
    var digits = (rawNumber || '').replace(/\D/g, '');

    if (!digits) return null;

    // Bangladeshi number handling:
    // 01XXXXXXXXX (11 digits, starts with 0)  -> 8801XXXXXXXXX
    // 1XXXXXXXXX  (10 digits, no leading 0)   -> 8801XXXXXXXXX
    // 8801XXXXXXXXX (already has country code) -> keep as is
    if (digits.length === 11 && digits.startsWith('0')) {
        digits = '880' + digits.substring(1);
    } else if (digits.length === 10 && digits.startsWith('1')) {
        digits = '880' + digits;
    }
    // If it already starts with 880 and has correct length, leave it untouched.

    return digits;
}

$(document).on('click', '.btnSharePdf', function () {
    var $btn = $(this);
    var id = $btn.data('id');
    var phone = formatWhatsAppNumber($btn.data('phone'));
    var customer = $btn.data('customer') || '';

    if (!phone) {
        alert('এই বিলের জন্য কোনো সঠিক WhatsApp নম্বর পাওয়া যায়নি।');
        return;
    }

    var pdfUrl = PDF_BASE_URL + id;

    var message =
        'হ্যালো ' + customer + ',\n' +
        'আপনার বিলের PDF কপি এখানে দেখুন:\n' +
        pdfUrl;

    var waLink = 'https://wa.me/' + phone + '?text=' + encodeURIComponent(message);

    window.open(waLink, '_blank');
});
</script>