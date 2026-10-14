<?php
// Fetch Purchases from database
$products = $obj->raw_sql("SELECT * FROM products WHERE deleted_at IS NULL");
$suppliers = $obj->raw_sql("SELECT * FROM suppliers WHERE deleted_at IS NULL");
?>
<div class="card basic-data-table">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
        <h5 class="text-md text-neutral-500">Purchase List</h5>
        <button type="button" class="btn btn-success-600 radius-8 px-20 py-11" data-bs-toggle="modal" data-bs-target="#addPurchaseModal">Add Purchase</button>
    </div>
    <div class="card-body table-responsive">
        <table
            class="table table-striped bordered-table mb-0"
            id="purchaseTable"
            data-page-length="10">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Product Name</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Batch No</th>
                    <th scope="col">Supplier</th>
                    <th scope="col">Purchase Date</th>
                    <th scope="col">Created By</th>
                    <?php if ($obj->userWorkPermission('edit') || $obj->userWorkPermission('delete')) { ?>
                        <th scope="col">Action</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
<!-- Add Purchase Modal -->
<div class="modal fade" id="addPurchaseModal" tabindex="-1" aria-labelledby="purchaseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="purchaseModalLabel">Add Purchase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="purchase-form">
                    <div class="mb-3">
                        <label for="product" class="form-label">Product</label>
                        <select id="product" name="product" class="form-control wizard-required" required>
                            <option value="">Select</option>
                            <?php foreach ($products as $product) { ?>
                                <option value="<?= $product['product_id']; ?>"><?= $product['product_name']; ?></option>
                            <?php } ?>
                        </select>
                        <div class="wizard-form-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" id="quantity" name="quantity" class="form-control wizard-required" required>
                        <div class="wizard-form-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="supplier" class="form-label">Supplier</label>
                        <select id="supplier" name="supplier" class="form-control">
                            <option value="">Select</option>
                            <?php foreach ($suppliers as $supplier) { ?>
                                <option value="<?= $supplier['supplier_id']; ?>"><?= $supplier['supplier_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="purchase-date" class="form-label">Purchase Date</label>
                        <input type="date" id="purchase-date" name="purchase_date" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Purchase Modal (Modal for Editing Purchase)-->
<div class="modal fade" id="editPurchaseModal" tabindex="-1" aria-labelledby="editPurchaseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs" id="editPurchaseModalLabel">Edit Purchase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit-purchase-form">
                    <input type="hidden" id="edit-purchase-id" name="id">
                    <div class="mb-3">
                        <label for="edit-product" class="form-label">Product</label>
                        <select id="edit-product" name="product" class="form-control">
                            <option value="">Select</option>
                            <?php foreach ($products as $product) { ?>
                                <option value="<?= $product['product_id']; ?>"><?= $product['product_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-quantity" class="form-label">Quantity</label>
                        <input type="number" id="edit-quantity" name="quantity" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-supplier" class="form-label">Supplier</label>
                        <select id="edit-supplier" name="supplier" class="form-control">
                            <option value="">Select</option>
                            <?php foreach ($suppliers as $supplier) { ?>
                                <option value="<?= $supplier['supplier_id']; ?>"><?= $supplier['supplier_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-purchase-date" class="form-label">Purchase Date</label>
                        <input type="date" id="edit-purchase-date" name="purchase_date" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>

        </div>
    </div>
</div>


<!-- Return Purchase Modal -->
<div class="modal fade" id="returnModal" tabindex="-1" aria-labelledby="returnModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnModalLabel">Return Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="returnForm">
                    <div class="mb-3">
                        <label for="returnProductId" class="form-label">Product</label>
                        <select id="returnProductId" name="returnProductId" class="form-control" disabled>
                            <option value="">Select</option>
                            <?php foreach ($products as $product) { ?>
                                <option value="<?= $product['product_id']; ?>"><?= $product['product_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <input type="hidden" id="returnPurchaseId">
                    <div class="mb-3">
                        <label for="returnSupplierId" class="form-label">Supplier</label>
                        <select id="returnSupplierId" name="returnSupplierId" class="form-control" disabled>
                            <option value="">Select</option>
                            <?php foreach ($suppliers as $supplier) { ?>
                                <option value="<?= $supplier['supplier_id']; ?>"><?= $supplier['supplier_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="returnQuantity" class="form-label">Return Quantity</label>
                        <input type="number" id="returnQuantity" name="returnQuantity" value="0" min="0" class="form-control" required>
                        <small class="text-muted" id="rtq"></small>
                    </div>

                    <div class="mb-3">
                        <label for="ReturnDate" class="form-label">Return Date</label>
                        <input type="date" id="returnDate" name="returnDate" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="returnReason" class="form-label">Reason for Return</label>
                        <textarea id="returnReason" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">submit Return</button>
                </form>
            </div>
        </div>
    </div>

</div>

<?php $obj->start_script(); ?>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        const table = $('#purchaseTable').DataTable({
            ajax: {
                url: './pages/inventory/purchase/purchase_ajax.php',
                dataSrc: '',
            },
            columns: [{
                    data: 'sl'
                },
                {
                    data: 'product_name'
                },
                {
                    data: 'quantity'
                },
                {
                    data: 'batch_id'
                },
                {
                    data: 'supplier_name'
                },
                {
                    data: 'purchase_date',
                },
                {
                    data: 'FullName'
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        const currentTime = new Date().getTime();
                        const createdAt = new Date(row.created_at).getTime();

                        const diffMinutes = (currentTime - createdAt) / (1000 * 60);
                        return `
                        ${diffMinutes <60 ? `
                <button class="btn btn-warning btn-sm edit-btn"data-productId=${row.product_id} data-id="${row.purchase_id}"  data-qty="${row.quantity}" data-supplierId="${row.supplier_id}" data-purchaseDate="${row.purchase_date}" data-rtq="${row.return_qty}"  data-bs-toggle="modal" data-bs-target="#editPurchaseModal">Edit</button>` : ''}
${row.quantity > row.return_qty ? `
        <button class="btn btn-info btn-sm return-btn" 
            data-productId="${row.product_id}" 
            data-id="${row.purchase_id}" 
            data-supplierId="${row.supplier_id}" 
            data-rtq="${row.return_qty}"
            data-qty="${row.quantity}"
            data-bs-toggle="modal"
            data-bs-target="#returnModal">
            Return                    
        </button>
    ` : ''}
            `;
                    }
                },
            ],
        });

        // Return Purchase - open modal
        $(document).on('click', '.return-btn', function() {
            const id = $(this).attr('data-id');
            const product_id = $(this).attr('data-productId');
            const quantity = $(this).attr('data-qty');
            const supplier_id = $(this).attr('data-supplierId');
            const rtq = $(this).attr('data-rtq');
            const maxReturnable = quantity - rtq;

            $('#returnQuantity').val(0);
            $('#returnQuantity').attr('data-qty', maxReturnable);
            $('#returnPurchaseId').val(id);
            $('#returnProductId').val(product_id);
            $('#returnSupplierId').val(supplier_id);
            $('#rtq').text(`Max returnable quantity: ${maxReturnable}`);
        });

        $('#returnModal').on('hide.bs.modal', function() {
            $('#returnQuantity').val(0);
            $('#rtq').text('');
        });

        $('#returnQuantity').on('input', function() {
            var currentQty = $(this).val();
            var maxQty = $(this).attr('data-qty');

            if (parseInt(currentQty) > parseInt(maxQty)) {
                Swal.fire({
                    title: 'Error',
                    text: 'Quantity exceeds the maximum allowed value!',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
                $(this).val(maxQty);
            }
        });

        // Return Purchase Logic
        $('#returnForm').submit(function(e) {
            e.preventDefault();
            const id = $('#returnPurchaseId').val();
            const product = $('#returnProductId').val();
            const quantity = $('#returnQuantity').val();
            const supplier = $('#returnSupplierId').val();
            const return_date = $('#returnDate').val();
            const reason = $('#returnReason').val();

            if (!quantity || parseInt(quantity) <= 0) {
                Swal.fire({
                    title: 'Error',
                    text: 'Please enter a valid return quantity.',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
                return;
            }

            $.ajax({
                url: './pages/inventory/return/supplier_return_ajax.php',
                method: 'POST',
                data: {
                    id,
                    product,
                    quantity,
                    supplier,
                    return_date,
                    reason
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        $('#returnModal').modal('hide');
                        Swal.fire({
                            title: 'Success',
                            text: 'Return added successfully!',
                            icon: 'success',
                            confirmButtonText: 'Ok'
                        });
                        table.ajax.reload();
                        $('#returnForm')[0].reset();
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.status || 'Failed to add return!',
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                    }
                },
            });
        });

        // Add Purchase Logic
        $('#purchase-form').submit(function(e) {
            e.preventDefault();
            const product = $('#product').val();
            const quantity = parseInt($('#quantity').val(), 10) || 0;
            const supplier = $('#supplier').val();
            const purchase_date = $('#purchase-date').val();
            const batch_id = 'batch_' + (Date.now() % 1000000) + Math.floor(Math.random() * 1000);

            if (!product || !supplier || !purchase_date || quantity <= 0) {
                Swal.fire({
                    title: 'Error',
                    text: 'Please complete all purchase fields.',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
                return;
            }

            $.ajax({
                url: './pages/inventory/purchase/purchase_ajax.php',
                method: 'POST',
                data: {
                    product,
                    quantity,
                    supplier,
                    purchase_date,
                    batch_id
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        $('#addPurchaseModal').modal('hide');
                        Swal.fire({
                            title: 'Success',
                            text: 'Purchase added successfully!',
                            icon: 'success',
                            confirmButtonText: 'Ok'
                        });
                        table.ajax.reload();
                        $('#purchase-form')[0].reset();
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.status || 'Failed to add purchase!',
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                    }
                },
            });
        });

        // Edit Purchase - open modal
        $('#purchaseTable').on('click', '.edit-btn', function() {
            const productId = $(this).data('productid');
            const purchaseId = $(this).data('id');
            const qty = $(this).data('qty');
            const supplierId = $(this).data('supplierid');
            const purchaseDate = $(this).data('purchasedate');

            $('#edit-product').val(productId);
            $('#edit-quantity').val(qty);
            $('#edit-supplier').val(supplierId);
            $('#edit-purchase-date').val(purchaseDate);
            $('#edit-purchase-id').val(purchaseId);
        });

        // Edit Purchase Logic
        $('#edit-purchase-form').submit(function(e) {
            e.preventDefault();
            const id = $('#edit-purchase-id').val();
            const product = $('#edit-product').val();
            const quantity = $('#edit-quantity').val();
            const supplier = $('#edit-supplier').val();
            const purchase_date = $('#edit-purchase-date').val();

            $.ajax({
                url: './pages/inventory/purchase/purchase_ajax.php',
                method: 'POST',
                data: {
                    id,
                    product,
                    quantity,
                    supplier,
                    purchase_date,
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        $('#editPurchaseModal').modal('hide');
                        Swal.fire({
                            title: 'Success',
                            text: 'Purchase updated successfully!',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500,
                        });
                        table.ajax.reload();
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.status || 'Failed to update purchase!',
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                    }
                },
            });
        });
    });
</script>
<?php $obj->end_script(); ?>