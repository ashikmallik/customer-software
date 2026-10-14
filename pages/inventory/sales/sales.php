<?php
// Fetch data for dropdowns only
$products = $obj->raw_sql("
    SELECT stock.*, p.product_name
    FROM stock
    LEFT JOIN products p ON stock.product_id = p.product_id
    WHERE stock.current_stock > 0
");
$editProducts = $obj->raw_sql("
    SELECT stock.*, p.product_name
    FROM stock
    LEFT JOIN products p ON stock.product_id = p.product_id
    WHERE stock.current_stock >= 0
");
$customers = $obj->raw_sql("SELECT * FROM vw_agent WHERE ag_status=1 AND deleted_at IS NULL");
?>

<div class="card basic-data-table">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
        <h5 class="text-md text-neutral-500">Sales List</h5>
        <button type="button" class="btn btn-success-600 radius-8 px-20 py-11" data-bs-toggle="modal" data-bs-target="#addSaleModal">Add Sale</button>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped bordered-table mb-0" id="saleTable" data-page-length="10">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Product Name</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Return Qty</th>
                    <th scope="col">Batch No</th>
                    <th scope="col">Customer</th>
                    <th scope="col">Sale Date</th>
                    <th scope="col">Created By</th>
                    <?php if ($obj->userWorkPermission('edit') || $obj->userWorkPermission('delete')) { ?>
                        <th scope="col">Action</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Add Sale Modal -->
<div class="modal fade" id="addSaleModal" tabindex="-1" aria-labelledby="saleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="saleModalLabel">Add Sale</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="sale-form">

                    <!-- Customer Type Toggle -->
                    <div class="mb-3">
                        <label class="form-label d-block">Customer Type</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input customer-type-radio" type="radio" name="customer_type" id="customerTypeExisting" value="existing" checked>
                            <label class="form-check-label" for="customerTypeExisting">Existing Customer</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input customer-type-radio" type="radio" name="customer_type" id="customerTypeExternal" value="external">
                            <label class="form-check-label" for="customerTypeExternal">Other / External Customer</label>
                        </div>
                    </div>

                    <div class="mb-3" id="existingCustomerWrap">
                        <label for="customer" class="form-label">Customer</label>
                        <select id="customer" name="customer" class="form-control" style="width: 100%;" required>
                            <option value="">Select</option>
                            <?php foreach ($customers as $customer) { ?>
                                <option value="<?= $customer['ag_id']; ?>"><?= $customer['ag_name'] . " - " . $customer['ag_office_address']; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="externalCustomerWrap">
                        <label for="externalCustomerName" class="form-label">Customer Name / Details</label>
                        <input type="text" id="externalCustomerName" name="external_customer_name" class="form-control" placeholder="e.g. Walk-in customer, other company name">
                    </div>

                    <div class="mb-3">
                        <label for="product" class="form-label">Product</label>
                        <select id="product" name="product" class="form-control" required>
                            <option value="">Select</option>
                            <?php foreach ($products as $product) { ?>
                                <option value="<?= $product['stock_id']; ?>" data-batch='<?= $product['batch_id']; ?>' data-id="<?= $product['product_id']; ?>">
                                    <?= $product['product_name'] . " (" . $product['batch_id'] . ")"; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="batchId" class="form-label">Batch Id</label>
                        <input type="text" id="batchId" name="batch_id" class="form-control" readonly required>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" id="quantity" name="quantity" class="form-control" min="1" required>
                        <small class="text-muted" id="qty"></small>
                    </div>
                    <div class="mb-3">
                        <label for="sale-date" class="form-label">Sale Date</label>
                        <input type="date" id="sale-date" name="sale_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="remark" class="form-label">Remark</label>
                        <textarea class="form-control" id="remark" name="remark"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Sale Modal -->
<div class="modal fade" id="editSaleModal" tabindex="-1" aria-labelledby="editSaleLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSaleLabel">Edit Sale</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editSaleForm">
                    <input type="hidden" id="editSaleId" name="id">

                    <!-- Customer Type Toggle -->
                    <div class="mb-3">
                        <label class="form-label d-block">Customer Type</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input edit-customer-type-radio" type="radio" name="edit_customer_type" id="editCustomerTypeExisting" value="existing" checked>
                            <label class="form-check-label" for="editCustomerTypeExisting">Existing Customer</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input edit-customer-type-radio" type="radio" name="edit_customer_type" id="editCustomerTypeExternal" value="external">
                            <label class="form-check-label" for="editCustomerTypeExternal">Other / External Customer</label>
                        </div>
                    </div>

                    <div class="mb-3" id="editExistingCustomerWrap">
                        <label for="editCustomerId" class="form-label">Customer</label>
                        <select id="editCustomerId" name="customer" class="form-control" required>
                            <option value="">Select</option>
                            <?php foreach ($customers as $customer) { ?>
                                <option value="<?= $customer['ag_id']; ?>"><?= $customer['ag_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="editExternalCustomerWrap">
                        <label for="editExternalCustomerName" class="form-label">Customer Name / Details</label>
                        <input type="text" id="editExternalCustomerName" name="external_customer_name" class="form-control" placeholder="e.g. Walk-in customer, other company name">
                    </div>

                    <div class="mb-3">
                        <label for="editProductId" class="form-label">Product</label>
                        <select id="editProductId" name="product" class="form-control" disabled>
                            <option value="">Select</option>
                            <?php foreach ($editProducts as $product) { ?>
                                <option value="<?= $product['product_id']; ?>" data-id="<?= $product['stock_id']; ?>">
                                    <?= $product['product_name'] . " (" . $product['batch_id'] . ")"; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editBatchId" class="form-label">Batch Id</label>
                        <input type="text" id="editBatchId" name="batch_id" class="form-control" readonly required>
                    </div>
                    <div class="mb-3">
                        <label for="editQuantity" class="form-label">Quantity</label>
                        <input type="number" id="editQuantity" name="quantity" class="form-control" min="1" required>
                        <small class="text-muted" id="editQtyLabel"></small>
                    </div>
                    <div class="mb-3">
                        <label for="editSaleDate" class="form-label">Sale Date</label>
                        <input type="date" id="editSaleDate" name="sale_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="editRemark" class="form-label">Remark</label>
                        <textarea class="form-control" id="editRemark" name="remark"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Return Sale Modal -->
<div class="modal fade" id="returnModal" tabindex="-1" aria-labelledby="returnModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnModalLabel">Return Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="returnForm">
                    <input type="hidden" id="returnSaleId" name="id">
                    <div class="mb-3">
                        <label for="returnCustomerId" class="form-label">Customer</label>
                        <select id="returnCustomerId" name="customer" class="form-control" disabled>
                            <option value="">Select</option>
                            <?php foreach ($customers as $customer) { ?>
                                <option value="<?= $customer['ag_id']; ?>"><?= $customer['ag_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="returnProductId" class="form-label">Product</label>
                        <select id="returnProductId" name="product" class="form-control" disabled>
                            <option value="">Select</option>
                            <?php foreach ($editProducts as $product) { ?>
                                <option value="<?= $product['stock_id']; ?>" data-id="<?= $product['product_id']; ?>">
                                    <?= $product['product_name'] . " (" . $product['batch_id'] . ")"; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="returnBatchId" class="form-label">Batch Id</label>
                        <input type="text" id="returnBatchId" name="batch_id" class="form-control" readonly required>
                    </div>
                    <div class="mb-3">
                        <label for="returnQuantity" class="form-label">Return Quantity</label>
                        <input type="number" id="returnQuantity" name="quantity" class="form-control" min="1" value="0" required>
                        <small class="text-muted" id="rtq"></small>
                    </div>
                    <div class="mb-3">
                        <label for="returnSaleDate" class="form-label">Return Date</label>
                        <input type="date" id="returnSaleDate" name="return_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="returnReason" class="form-label">Reason for Return</label>
                        <textarea id="returnReason" name="reason" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Submit Return</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $obj->start_script(); ?>
<script>
    $(document).ready(function() {
        // Fix aria-hidden issue
        $(document).on('show.bs.modal hidden.bs.modal', '.modal', function() {
            if (document.activeElement) {
                document.activeElement.blur();
            }
            $('main.dashboard-main[aria-hidden="true"]').removeAttr('aria-hidden');
        });

        // Select2 for customer
        $('#customer').select2({
            placeholder: "Choose ...",
            allowClear: false,
            width: '100%',
            dropdownParent: $('#addSaleModal')
        });

        // ---- Customer Type toggle: Add Sale ----
        function applyCustomerType(type) {
            if (type === 'external') {
                $('#existingCustomerWrap').addClass('d-none');
                $('#externalCustomerWrap').removeClass('d-none');
                $('#customer').val(null).trigger('change');
                $('#customer').removeAttr('required');
                $('#externalCustomerName').attr('required', true);
            } else {
                $('#existingCustomerWrap').removeClass('d-none');
                $('#externalCustomerWrap').addClass('d-none');
                $('#customer').attr('required', true);
                $('#externalCustomerName').removeAttr('required').val('');
            }
        }
        $('.customer-type-radio').on('change', function() {
            applyCustomerType($('input[name="customer_type"]:checked').val());
        });

        // ---- Customer Type toggle: Edit Sale ----
        function applyEditCustomerType(type) {
            if (type === 'external') {
                $('#editExistingCustomerWrap').addClass('d-none');
                $('#editExternalCustomerWrap').removeClass('d-none');
                $('#editCustomerId').removeAttr('required');
                $('#editExternalCustomerName').attr('required', true);
            } else {
                $('#editExistingCustomerWrap').removeClass('d-none');
                $('#editExternalCustomerWrap').addClass('d-none');
                $('#editCustomerId').attr('required', true);
                $('#editExternalCustomerName').removeAttr('required').val('');
            }
        }
        $('.edit-customer-type-radio').on('change', function() {
            applyEditCustomerType($('input[name="edit_customer_type"]:checked').val());
        });

        // Initialize DataTable with AJAX (like Purchase)
        const table = $('#saleTable').DataTable({
            ajax: {
                url: './pages/inventory/sales/sales_ajax.php',
                dataSrc: '',
            },
            columns: [
                { data: 'sl' },
                { data: 'product_name' },
                { data: 'quantity' },
                { data: 'return_qty' },
                { data: 'batch_id' },
                {
                    data: 'ag_name',
                    render: function(data, type, row) {
                        if (row.customer_type === 'external') {
                            return `${data ? data : ''} <span class="badge bg-neutral-200 text-neutral-600">External</span>`;
                        }
                        return data;
                    }
                },
                { data: 'sale_date' },
                { data: 'FullName' },
                {
                    data: null,
                    render: function(data, type, row) {
                        const currentTime = new Date().getTime();
                        const createdAt = new Date(row.created_at).getTime();
                        const diffMinutes = (currentTime - createdAt) / (1000 * 60);
                        
                        let buttons = '';
                        
                        // Edit button (within 60 minutes, like Purchase)
                        if (diffMinutes < 60) {
                            buttons += `<button class="btn btn-warning btn-sm edit-btn" 
                                data-productid="${row.product_id}" 
                                data-id="${row.sale_id}" 
                                data-qty="${row.quantity}" 
                                data-customerid="${row.customer_id ?? ''}" 
                                data-customertype="${row.customer_type ?? 'existing'}" 
                                data-externalname="${row.external_customer_name ?? ''}" 
                                data-saledate="${row.sale_date}" 
                                data-batchid="${row.batch_id}" 
                                data-stockid="${row.stock_id}" 
                                data-remark="${row.remark || ''}" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editSaleModal">Edit</button> `;
                        }
                        
                        // Return button
                        if (row.quantity > row.return_qty) {
                            buttons += `<button class="btn btn-info btn-sm return-btn" 
                                data-productid="${row.product_id}" 
                                data-id="${row.sale_id}" 
                                data-customerid="${row.customer_id}" 
                                data-qty="${row.quantity}" 
                                data-batchid="${row.batch_id}" 
                                data-stockid="${row.stock_id}" 
                                data-saledate="${row.sale_date}" 
                                data-bs-toggle="modal" 
                                data-bs-target="#returnModal">Return</button>`;
                        }
                        
                        return buttons;
                    }
                }
            ]
        });

        // Product change - load batch and stock qty
        $('#product').on('change', function() {
            const stockId = $(this).find(":selected").val();
            const batch = $(this).find(":selected").data('batch');
            const productId = $(this).find(":selected").data('id');

            if (!stockId) return;

            $.ajax({
                url: './pages/inventory/sales/sales_batch_ajax.php',
                method: 'GET',
                data: { id: stockId, batch: batch },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        $('#batchId').val(data.batch_id);
                        $('#quantity').attr('max', data.quantity).attr('data-qty', data.quantity);
                        $('#qty').text(`Available Stock: ${data.quantity}`);
                    } else {
                        Swal.fire({ title: 'Error', text: 'Failed to load stock info.', icon: 'error' });
                    }
                },
                error: function() {
                    Swal.fire({ title: 'Error', text: 'An error occurred.', icon: 'error' });
                }
            });
        });

        // Quantity validation
        $('#quantity').on('input', function() {
            const currentQty = parseInt($(this).val()) || 0;
            const maxQty = parseInt($(this).attr('data-qty')) || 0;
            if (currentQty > maxQty) {
                Swal.fire({ title: 'Error', text: 'Quantity exceeds available stock!', icon: 'error', confirmButtonText: 'Ok' });
                $(this).val(maxQty);
            }
        });

        $('#editQuantity').on('input', function() {
            const currentQty = parseInt($(this).val()) || 0;
            const maxQty = parseInt($(this).attr('data-qty')) || 0;
            if (currentQty > maxQty) {
                Swal.fire({ title: 'Error', text: 'Quantity exceeds available stock!', icon: 'error', confirmButtonText: 'Ok' });
                $(this).val(maxQty);
            }
        });

        // Add Sale
        $('#sale-form').submit(function(e) {
            e.preventDefault();
            const customerType = $('input[name="customer_type"]:checked').val();
            const customer = $('#customer').val();
            const externalCustomerName = $('#externalCustomerName').val().trim();
            const stock_id = $('#product').find(":selected").val();
            const product = $('#product').find(":selected").data('id');
            const sale_date = $('#sale-date').val();
            const quantity = parseInt($('#quantity').val()) || 0;
            const batch_id = $('#batchId').val();
            const remark = $('#remark').val();

            if (!product || !sale_date || quantity <= 0 || !batch_id || !stock_id) {
                Swal.fire({ title: 'Error', text: 'Please fill all required fields.', icon: 'error' });
                return;
            }
            if (customerType === 'existing' && !customer) {
                Swal.fire({ title: 'Error', text: 'Please select a customer.', icon: 'error' });
                return;
            }
            if (customerType === 'external' && !externalCustomerName) {
                Swal.fire({ title: 'Error', text: 'Please enter external customer name.', icon: 'error' });
                return;
            }

            $.ajax({
                url: './pages/inventory/sales/sales_ajax.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    product, quantity, customer, customer_type: customerType,
                    external_customer_name: externalCustomerName,
                    sale_date, batch_id, stock_id, remark
                },
                success: function(data) {
                    if (data.success) {
                        $('#addSaleModal').modal('hide');
                        Swal.fire({ title: 'Success', text: 'Sale added successfully!', icon: 'success', confirmButtonText: 'Ok' });
                        table.ajax.reload();
                        $('#sale-form')[0].reset();
                        $('#batchId').val('');
                        $('#qty').text('');
                        applyCustomerType('existing');
                        $('#customerTypeExisting').prop('checked', true);
                    } else {
                        Swal.fire({ title: 'Failed', text: data.status || 'Failed to add sale!', icon: 'error' });
                    }
                },
                error: function(xhr) {
                    Swal.fire({ title: 'Request Failed', text: xhr.responseText ? xhr.responseText.substring(0, 200) : 'Invalid server response', icon: 'error' });
                }
            });
        });

        // Edit Sale - open modal
        $(document).on('click', '.edit-btn', function() {
            const id = $(this).data('id');
            const product_id = $(this).data('productid');
            const quantity = $(this).data('qty');
            const customer_id = $(this).data('customerid');
            const customer_type = $(this).data('customertype') || 'existing';
            const external_name = $(this).data('externalname') || '';
            const sale_date = $(this).data('saledate');
            const batch_id = $(this).data('batchid');
            const stock_id = $(this).data('stockid');
            const remark = $(this).data('remark');

            $('#editSaleId').val(id);
            $('#editProductId').val(product_id);
            $('#editProductId').attr('data-id', stock_id);
            $('#editQuantity').val(quantity);
            $('#editCustomerId').val(customer_id);
            $('#editExternalCustomerName').val(external_name);
            $('#editSaleDate').val(sale_date);
            $('#editBatchId').val(batch_id);
            $('#editRemark').val(remark);

            if (customer_type === 'external') {
                $('#editCustomerTypeExternal').prop('checked', true);
            } else {
                $('#editCustomerTypeExisting').prop('checked', true);
            }
            applyEditCustomerType(customer_type);

            // Load available stock for edit
            $.ajax({
                url: './pages/inventory/sales/sales_batch_ajax.php',
                method: 'GET',
                data: { id: stock_id, batch: batch_id },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        const available = parseInt(data.quantity) + parseInt(quantity);
                        $('#editQuantity').attr('max', available).attr('data-qty', available);
                        $('#editQtyLabel').text(`Available Stock (with current): ${available}`);
                    }
                }
            });
        });

        // Edit Sale
        $('#editSaleForm').submit(function(e) {
            e.preventDefault();
            const id = $('#editSaleId').val();
            const customerType = $('input[name="edit_customer_type"]:checked').val();
            const product = $('#editProductId').find(":selected").val();
            const stock_id = $('#editProductId').find(":selected").data('id');
            const quantity = parseInt($('#editQuantity').val()) || 0;
            const customer = $('#editCustomerId').val();
            const externalCustomerName = $('#editExternalCustomerName').val().trim();
            const sale_date = $('#editSaleDate').val();
            const batch_id = $('#editBatchId').val();
            const remark = $('#editRemark').val();

            if (!id || !product || !sale_date || quantity <= 0 || !batch_id || !stock_id) {
                Swal.fire({ title: 'Error', text: 'Please fill all required fields.', icon: 'error' });
                return;
            }
            if (customerType === 'existing' && !customer) {
                Swal.fire({ title: 'Error', text: 'Please select a customer.', icon: 'error' });
                return;
            }
            if (customerType === 'external' && !externalCustomerName) {
                Swal.fire({ title: 'Error', text: 'Please enter external customer name.', icon: 'error' });
                return;
            }

            $.ajax({
                url: './pages/inventory/sales/sales_ajax.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    id, product, quantity, customer, customer_type: customerType,
                    external_customer_name: externalCustomerName,
                    sale_date, batch_id, stock_id, remark
                },
                success: function(data) {
                    if (data.success) {
                        $('#editSaleModal').modal('hide');
                        Swal.fire({ title: 'Success', text: 'Sale updated successfully!', icon: 'success', showConfirmButton: false, timer: 1500 });
                        table.ajax.reload();
                        $('#editSaleForm')[0].reset();
                    } else {
                        Swal.fire({ title: 'Error', text: data.status || 'Failed to update sale!', icon: 'error', confirmButtonText: 'Ok' });
                    }
                },
                error: function(xhr) {
                    Swal.fire({ title: 'Request Failed', text: xhr.responseText ? xhr.responseText.substring(0, 200) : 'Invalid server response', icon: 'error' });
                }
            });
        });

        // Return Sale - open modal
        $(document).on('click', '.return-btn', function() {
            const id = $(this).data('id');
            const product_id = $(this).data('productid');
            const customer_id = $(this).data('customerid');
            const batch_id = $(this).data('batchid');
            const stock_id = $(this).data('stockid');
            const quantity = $(this).data('qty');

            $('#returnSaleId').val(id);
            $('#returnProductId').val(stock_id);
            $('#returnProductId').attr('data-id', product_id);
            $('#returnCustomerId').val(customer_id);
            $('#returnBatchId').val(batch_id);
            $('#returnQuantity').val(0).attr('data-qty', quantity);
            $('#rtq').text(`Max returnable quantity: ${quantity}`);
        });

        // Reset return modal on close
        $('#returnModal').on('hide.bs.modal', function() {
            $('#returnForm')[0].reset();
            $('#rtq').text('');
        });

        // Return quantity validation
        $('#returnQuantity').on('input', function() {
            const currentQty = parseInt($(this).val()) || 0;
            const maxQty = parseInt($(this).attr('data-qty')) || 0;
            if (currentQty > maxQty) {
                Swal.fire({ title: 'Error', text: 'Quantity exceeds the maximum allowed value!', icon: 'error', confirmButtonText: 'Ok' });
                $(this).val(maxQty);
            }
        });

        // Return Sale submit
        $('#returnForm').submit(function(e) {
            e.preventDefault();
            const id = $('#returnSaleId').val();
            const stock_id = $('#returnProductId').find(":selected").val();
            const quantity = parseInt($('#returnQuantity').val()) || 0;
            const customer = $('#returnCustomerId').val();
            const return_date = $('#returnSaleDate').val();
            const batch_id = $('#returnBatchId').val();
            const product = $('#returnProductId').find(":selected").data('id');
            const reason = $('#returnReason').val();

            if (parseInt(quantity) <= 0) {
                Swal.fire({ title: 'Error', text: 'Please enter a valid return quantity.', icon: 'error' });
                return;
            }

            $.ajax({
                url: './pages/inventory/return/customer_return_ajax.php',
                method: 'POST',
                dataType: 'json',
                data: { id, product, quantity, customer, return_date, batch_id, stock_id, reason },
                success: function(data) {
                    if (data.success) {
                        $('#returnModal').modal('hide');
                        Swal.fire({ title: 'Success', text: 'Return added successfully!', icon: 'success', confirmButtonText: 'Ok' });
                        table.ajax.reload();
                        $('#returnForm')[0].reset();
                    } else {
                        Swal.fire({ title: 'Error', text: data.status || 'Failed to add return!', icon: 'error', confirmButtonText: 'Ok' });
                    }
                },
                error: function(xhr) {
                    Swal.fire({ title: 'Request Failed', text: xhr.responseText ? xhr.responseText.substring(0, 200) : 'Invalid server response', icon: 'error' });
                }
            });
        });
    });
</script>
<?php $obj->end_script(); ?>