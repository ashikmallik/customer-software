<?php
$monthArray = array(
    "1" => 'January',
    "2" => 'February',
    "3" => 'March',
    "4" => 'April',
    "5" => 'May',
    "6" => 'June',
    "7" => 'July',
    "8" => 'August',
    "9" => 'September',
    "10" => 'October',
    "11" => 'November',
    "12" => 'December',
);
$ag_id = intval($_GET['token']);
$activity_logs = $obj->raw_sql("SELECT logs.id, logs.description, logs.created_at, logs.created_by, user.FullName as user_name
        FROM `activity_logs` as logs
        LEFT JOIN _createuser as user ON user.UserId = logs.created_by
        WHERE ag_id = '$ag_id'
        ORDER BY created_at DESC");
$discounds = $obj->raw_sql("SELECT bonus.id, bonus.ag_id, bonus.amount, bonus.entryby,bonus.date , user.FullName as user_name FROM bonus LEFT JOIN _createuser AS user ON bonus.entryby = user.UserId WHERE ag_id = '$ag_id'");
$customer = $obj->raw_sql("SELECT tbl_agent.ag_id,tbl_agent.cus_id,mikrotik.mik_ip ,mikrotik.id  FROM tbl_agent LEFT JOIN mikrotik_user as mikrotik ON tbl_agent.mikrotik_id = mikrotik.id WHERE tbl_agent.ag_id = $ag_id");
$previous_due_log = $obj->raw_sql("SELECT previous_due_log.id, previous_due_log.ag_id, previous_due_log.prevous_due_amount, previous_due_log.enty_by,previous_due_log.entry_date , user.FullName as user_name FROM previous_due_log LEFT JOIN _createuser AS user ON previous_due_log.enty_by = user.UserId WHERE ag_id = '$ag_id'");
?>

<!-- hidden inputs -->
<input hidden type="text" id="token" value="<?= $_GET["token"] ?>">
<style>
    /* === Dashboard Modern Styling === */
    body {
        background-color: #f8fafc;
    }

    .card {
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        border-radius: 16px;
    }

    .card-header {
        background: linear-gradient(135deg, #007bff20, #6c63ff10);
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1rem 1.25rem;
    }

    .card-title {
        font-weight: 600;
        color: #1e293b;
    }

    .table {
        border-radius: 10px;
        overflow: hidden;
        background-color: #fff;
    }

    .table thead {
        background: linear-gradient(135deg, #007bff15, #6c63ff15);
    }

    .table th {
        font-weight: 600;
        color: #495057;
    }

    .table td {
        vertical-align: middle;
    }

    tbody tr {
        transition: all 0.2s ease-in-out;
    }

    tbody tr:hover {
        background-color: #f1f5f9;
        transform: scale(1.01);
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
    }

    .btn-success {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        border: none;
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444, #b91c1c);
        border: none;
    }

    .btn:hover {
        opacity: 0.9;
        transform: scale(1.02);
        transition: 0.2s ease-in-out;
    }

    .nav-pills .nav-link {
        border-radius: 10px;
        font-weight: 500;
        color: #374151;
        background-color: #f9fafb;
        margin-right: 6px;
    }

    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #6366f1, #3b82f6);
        color: #fff;
    }

    .user-grid-card {
        transition: all 0.3s ease-in-out;
    }

    .user-grid-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
    }

    .bg-base {
        background: linear-gradient(135deg, #fafafa, #ffffff);
    }

    .text-primary-light {
        color: #1e293b !important;
    }

    .dataTables_wrapper .dataTables_filter input {
        border-radius: 8px;
        padding: 6px 10px;
        border: 1px solid #cbd5e1;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 6px;
        background: #f8fafc !important;
        color: #1e293b !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: linear-gradient(135deg, #3b82f6, #6366f1) !important;
        color: #fff !important;
    }

    .modal-content {
        border-radius: 14px;
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        color: white;
    }

    .modal-title {
        font-weight: 600;
    }

    #mikrotikSection {
        background: linear-gradient(180deg, #fff 0%, #fbfbfd 100%);
    }

    .btn {
        padding: .5rem .75rem;
        border-radius: .5rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        cursor: pointer;
    }

    .btn-sm {
        font-size: .85rem;
        padding: .25rem .5rem;
    }

    .btn-outline {
        background: transparent;
    }

    .btn-success {
        background: #22c55e;
        color: white;
        border: none;
    }

    .btn-danger {
        background: #ef4444;
        color: white;
        border: none;
    }

    #mtStatus {
        background: rgba(15, 23, 42, 0.03);
    }

    .w-100 {
        width: 100%;
    }

    .fw-semibold {
        font-weight: 600;
    }

    .text-secondary-light {
        color: #94a3b8;
    }

    .gap-8 {
        gap: .5rem;
    }

    .gap-12 {
        gap: .75rem;
    }

    .mt-12 {
        margin-top: 1.25rem;
    }

    .p-16 {
        padding: 1rem;
    }

    .p-10 {
        padding: .625rem;
    }

    .radius-8 {
        border-radius: .5rem;
    }

    .radius-12 {
        border-radius: .75rem;
    }

    .data-card {
        background-color: #ffffff;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
        border-left: 4px solid #e9e9e9;
        border-radius: 0.75rem !important;
        transition: box-shadow 0.3s ease-in-out;
    }

    .data-card:hover {
        box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.1);
    }

    .profile-img {
        width: 96px;
        height: 96px;
    }
</style>


<div class="card h-100 p-0 radius-12">
    <div class="card-body p-24">
        <div class="row gy-4">
            <div class="col-xxl-6 col-md-6 user-grid-card mx-auto">
                <div class="w-100" style="max-width: 800px;">
                    <div class=" rounded-4 p-4 p-sm-5">

                        <div class="d-flex flex-column align-items-center text-center pb-4 mb-4 border-bottom position-relative">
    
                            <!-- Edit button - Top Right -->
                            <a href=""
                                class="position-absolute top-0 end-0 mt-2 me-2 text-decoration-none text-muted  p-4" style="border-color:#0d6efd; border-radius:12px;" id="customer_id_edit_link">
                                <iconify-icon icon="lucide:edit" style="font-size:24px;"></iconify-icon>
                            </a>
    
                            <!-- User Icon instead of Image -->
                            <div class="profile-img rounded-circle d-flex align-items-center justify-content-center
                                        border border-4 border-white shadow-sm mb-3"
                                style="width:90px;height:90px;background:#f1f1f1;">
                                <iconify-icon icon="lucide:user" style="font-size:42px;color:#555;"></iconify-icon>
                            </div>
    
                            <h2 class="h4 fw-bold text-dark" id="agentName"></h2>
                            <p class="small text-muted mb-0">
                                Customer Id: <span id="customer_id"></span>
                            </p>
    
                        </div>

                        <!-- Data Grid Section (Two Columns on Desktop/Tablet, Single Column on Mobile) -->
                        <div class="row g-4 pt-3 p-3">

                            <!-- Card 1: Customer Identification Details -->
                            <div class="col-12 col-lg-6">
                                <div class="data-card p-3">
                                    <h3 class="h6 fw-semibold mb-4 text-secondary">Identification Info</h3>

                                    <div class="d-grid gap-3">
                                        <!-- Field 1: Customer Email -->
                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                                            <span class="small text-muted">Email:</span>
                                            <span class="small fw-semibold text-dark" id="customer_email">jane.doe@example.com</span>
                                        </div>

                                        <!-- Field 2: Customer NID/Passport -->
                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                                            <span class="small text-muted">NID/Passport:</span>
                                            <span class="small fw-semibold text-dark" id="customer_nid_passport">19876543210</span>
                                        </div>

                                        <!-- Field 3: Zone/Area -->
                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                                            <span class="small text-muted">Zone:</span>
                                            <span class="small fw-semibold text-dark" id="customer_zone_area">Gulshan, Dhaka</span>
                                        </div>

                                        <!-- Field 4: Mac Address -->
                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                                            <span class="small text-muted">Mac Address:</span>
                                            <span class="small fw-semibold text-dark" id="mac_address">00:1A:2B:3C:4D:5E</span>
                                        </div>

                                        <!-- Field 5: Disconnected Date -->
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="small text-muted">Disconnected Date:</span>
                                            <span class="small fw-semibold mr-4" id="disconnected_time"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 2: Account and Technical Details -->
                            <div class="col-12 col-lg-6">
                                <div class="data-card p-3">
                                    <h3 class="h6 fw-semibold mb-4 text-secondary">Account & Service Details</h3>

                                    <div class="d-grid gap-3">
                                        <!-- Field 6: Package -->
                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                                            <span class="small text-muted">Service Package:</span>
                                            <span class="small fw-semibold text-dark" id="package">Fiber 100Mbps</span>
                                        </div>

                                        <!-- Field 7: IP Address -->
                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                                            <span class="small text-muted">IP Address:</span>
                                            <span class="small fw-semibold text-dark" id="ipAddress">192.168.1.102</span>
                                        </div>

                                        <!-- Field 8: Taka/Billing -->
                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                                            <span class="small text-muted">Monthly Bill (Taka):</span>
                                            <span class="small fw-semibold text-success" id="taka">1500 BDT</span>
                                        </div>

                                        <!-- Field 9: Address -->
                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                                            <span class="small text-muted">Physical Address:</span>
                                            <span class="small fw-semibold text-dark" id="address">Apt B-4, Road 10, Gulshan 1</span>
                                        </div>

                                        <!-- Field 10: Mobile Number -->
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="small text-muted">Mobile Number:</span>
                                            <span class="small fw-semibold text-dark" id="agentMobile">+880 171XXXXXXX</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-6 col-md-6 user-grid-card mx-auto">
                <div class="mt-12 p-16 bg-base border radius-12" id="mikrotikSection">
                    <h6 class="mb-8" id=""><?php echo $customer[0]['mik_ip']; ?></h6>
                    <div class="d-flex flex-column gap-12">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-secondary-light">MikroTik ID</small>
                                <div id="mtId" class="fw-semibold">N/A</div>
                            </div>
                            <div class="text-end">
                                <small class="text-secondary-light">Profile</small>
                                <div id="mtProfile" class="fw-semibold">N/A</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-secondary-light">Password</small>
                                <!-- password shown obfuscated but toggleable -->
                                <div class="d-flex align-items-center gap-8">
                                    <span id="mtPassword" class="fw-semibold">••••••••</span>
                                    <button type="button" class="btn btn-sm btn-outline" id="togglePassBtn">Show</button>
                                </div>
                            </div>
                            <div class="text-end">
                                <small class="text-secondary-light">Last Logout</small>
                                <div id="mtLastLogout" class="fw-semibold">N/A</div>
                            </div>
                        </div>
                        <div id="" class="mt-8 p-10 radius-8 text-sm">
                            <strong id="microticStatus" class=""></strong>
                        </div>
                        <?php if ($obj->userWorkPermission('edit')): ?>
                            <div class="gap-8 d-flex">
                                <button id="enableBtn" class="btn btn-success w-100">Enable</button>
                                <button id="disableBtn" class="btn btn-danger w-100">Disable</button>
                            </div>
                        <?php endif; ?>
                        <div id="mtStatus" class="mt-8 p-10 radius-8 text-sm">
                            <strong>Status:</strong> <span class="fw-semibold" id="statusText">Enabled</span>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-24 gy-0">
            <div class="col-xxl-3 col-sm-6 pe-0">
                <div class="card-body p-20 bg-base border h-100 d-flex flex-column justify-content-center border-end-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                        <div>
                            <span class="mb-12 w-44-px h-44-px text-primary-600 bg-primary-light border border-primary-light-white flex-shrink-0 d-flex justify-content-center align-items-center radius-8 h6 mb-12">
                                <iconify-icon icon="solar:wallet-bold" class="icon"></iconify-icon>
                            </span>
                            <span class="mb-1 fw-medium text-secondary-light text-md">Bill Amount</span>
                            <h6 class="fw-semibold text-primary-light mb-1" id="bill_amount">0</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6 px-0">
                <div class="card-body p-20 bg-base border h-100 d-flex flex-column justify-content-center border-end-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                        <div>
                            <span class="mb-12 w-44-px h-44-px text-success bg-success-focus border border-success-light-white flex-shrink-0 d-flex justify-content-center align-items-center radius-8 h6 mb-12">
                                <iconify-icon icon="streamline:bag-dollar-solid" class="icon"></iconify-icon>
                            </span>
                            <span class="mb-1 fw-medium text-secondary-light text-md">Total Paid</span>
                            <h6 class="fw-semibold text-primary-light mb-1" id="total_paid_amount">0</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6 px-0">
                <div class="card-body p-20 bg-base border h-100 d-flex flex-column justify-content-center border-end-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                        <div>
                            <span class="mb-12 w-44-px h-44-px text-pink bg-pink-light border border-pink-light-white flex-shrink-0 d-flex justify-content-center align-items-center radius-8 h6 mb-12">
                                <iconify-icon icon="ri:discount-percent-fill" class="icon"></iconify-icon>
                            </span>
                            <span class="mb-1 fw-medium text-secondary-light text-md">Total Discount</span>
                            <h6 class="fw-semibold text-primary-light mb-1" id="total_discount_amount">0</h6>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xxl-3 col-sm-6 ps-0">
                <div class="card-body p-20 bg-base border h-100 d-flex flex-column justify-content-center">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                        <div>
                            <span id="due_advance_icon" class="mb-12 w-44-px h-44-px text-danger bg-danger-focus border border-danger-light-white flex-shrink-0 d-flex justify-content-center align-items-center radius-8 h6 mb-12">
                                <iconify-icon icon="fa6-solid:file-invoice-dollar" class="icon"></iconify-icon>
                            </span>
                            <span id="due_advance_text" class="mb-1 fw-medium text-secondary-light text-md">Due Amount</span>
                            <h6 class="fw-semibold text-primary-light mb-1" id="previous_due">0</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- <div class="card basic-data-table">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
        <h5 class="card-title mb-0">Customer Ledger</h5>
        <button id="advancePayment" class="btn btn-success">Advance Payment</button>
    </div>
    <div class="card-body table-responsive">
        <table
            class="table bordered-table mb-0"
            id="customer-ledger-table"
            data-page-length="10">
            <thead>
                <tr>
                    <th scope="col">SL.</th>
                    <th scope="col">Date</th>
                    <th scope="col">Description</th>
                    <th scope="col">Amount</th>
                    <th scope="col">Received By</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
                <tr>
                    <th scope="col">Total:</th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col" id="total-amount"></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div> -->

<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Customer Ledger</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Discount</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">Activity Log</button>
    </li>
     <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-previous-due-tab" data-bs-toggle="pill" data-bs-target="#pills-previous-due" type="button" role="tab" aria-controls="pills-previous-due" aria-selected="false">Previous due</button>
    </li>
</ul>
<div class="tab-content" id="pills-tabContent">
    <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
        <div class="card basic-data-table">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                <h5 class="card-title mb-0">Customer Ledger</h5>
                <button id="advancePayment" class="btn btn-success">Advance Payment</button>
            </div>
                    <dev class="row px-3">
                        <div class="col-sm-2">
                            <label for="date-from" class="form-label">From Date</label>
                            <div class="position-relative">
                                <input type="date" id="date-from" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <label for="date-to" class="form-label">To Date</label>
                            <div class="position-relative">
                                <input type="date" id="date-to" class="form-control">
                            </div>
                        </div>
                    </dev>
                        
                        
            <div class="card-body table-responsive">
                <table
                    class="table bordered-table mb-0"
                    id="customer-ledger-table"
                    data-page-length="10">
                    <thead>
                        <tr>
                            <th scope="col">SL.</th>
                            <th scope="col">Date</th>
                            <th scope="col">Description</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Received By</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th scope="col">Total:</th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col" id="total-amount"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
        <div class="card basic-data-table">
            <div class="card-body table-responsive">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Discount</h5>
                </div>
                <table class="table bordered-table mb-0"
                    id="expense-table"
                    data-page-length="10">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Description</th>
                            <th scope="col">Date</th>
                            <th scope="col">User</th>
                            <?php if ($obj->userWorkPermission('delete')) { ?>
                            <th scope="col">Action</th>
                            <?php } ?> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($discounds as $index => $value): ?>
                            <tr>
                                <td><?php echo $index + 1 ?></td>
                                <td><?php echo $value['amount'] ?></td>
                                <td><?php echo date("d-m-Y", strtotime($value['date'])) ?></td>
                                <td><?php echo $value['user_name'] ?></td>
                                <?php if ($obj->userWorkPermission('delete')) { ?>
                                <td>
                                    
                                    <button
                                        class="btn btn-danger btn-sm delete-discount"
                                        data-id="<?= $value['id'] ?>"
                                        data-customer-id="<?= $_GET['token'] ?>"
                                        data-amount="<?= $value['amount'] ?>">
                                        Delete
                                    </button>
                                </td>
                                <?php } ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="tab-pane fade" id="pills-previous-due" role="tabpanel" aria-labelledby="pills-previous-due-tab">
        <div class="card basic-data-table">
            <div class="card-body table-responsive">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Previous due</h5>
                </div>
                <table class="table bordered-table mb-0"
                    id="expense-table"
                    data-page-length="10">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Description</th>
                            <th scope="col">Date</th>
                            <th scope="col">User</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($previous_due_log as $index => $value): ?>
                            <tr>
                                <td><?php echo $index + 1 ?></td>
                                <td><?php echo $value['prevous_due_amount'] ?></td>
                                <td><?php echo date("d-m-Y", strtotime($value['entry_date'])) ?></td>
                                <td><?php echo $value['user_name'] ?></td>
                                <td>
                                    <button
                                        class="btn btn-danger btn-sm delete-previous-due"
                                        data-id="<?= $value['id'] ?>"
                                        data-customer-id="<?= $_GET['token'] ?>"
                                        data-amount="<?= $value['prevous_due_amount'] ?>">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
        <div class="card basic-data-table">
            <div class="card-body  table-responsive">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Activity Log</h5>
                </div>
                <table class="table bordered-table mb-0"
                    id="expense-table"
                    data-page-length="10">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Description</th>
                            <th scope="col">Date</th>
                            <th scope="col">User</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activity_logs as $index => $value): ?>
                            <tr>
                                <td><?php echo $index + 1 ?></td>
                                <td><?php echo $value['description'] ?></td>
                                <td><?php echo date("d-m-Y", strtotime($value['created_at'])) ?></td>
                                <td><?php echo $value['user_name'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- advance payment modal start -->
<div class="modal fade" id="advancePaymentModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="exampleModalLabel">Add advance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="advancePaymentForm" method="POST">
                    <input type="hidden" id="edit-id" name="id">
                    <input type="hidden" id="cus_id" name="cus_id">
                    <input type="hidden" id="ag_id" name="ag_id">
                    <input type="hidden" value="<?php echo $_SESSION["userid"]; ?>" id="entry_by" name="entry_by">
                    <div class="mb-3">
                        <label for="advance_amount" class="form-label">Amount</label>
                        <input type="text" class="form-control" id="advance_amount" name="advance_amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <input placeholder="" type="text" class="form-control" id="description" name="description" required>
                    </div>
                    <div class="">
                        <label class="form-label">Entry Date*</label>
                        <input type="date" class="form-control wizard-required" id="entry_date" name="entry_date" value="<?php echo date('Y-m-d'); ?>" required>
                        <div class="wizard-form-error"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success waves-effect waves-light" id="advancePaymentSaveBtn">
                    <span class="btn-label">Save Changes</span>
                    <span class="btn-loader d-none"><i class="fa fa-spinner fa-spin"></i> Processing...</span>
                </button>
            </div>
        </div>
    </div>
</div>
<!-- advance payment modal end -->

<?php $obj->start_script(); ?>

<script>
    let advancePaymentSubmitting = false;

    function setAdvancePaymentLoading(isLoading) {
        const $btn = $("#advancePaymentSaveBtn");
        if (!$btn.length) return;
        $btn.prop("disabled", isLoading);
        $btn.find(".btn-label").toggleClass("d-none", isLoading);
        $btn.find(".btn-loader").toggleClass("d-none", !isLoading);
    }

    $("#advancePayment").on("click", function() {
        advancePaymentSubmitting = false;
        setAdvancePaymentLoading(false);
        $('#advancePaymentModal').modal('show');
    });

    $("#advancePaymentSaveBtn").on("click", function() {
        advancePayment();
    });

    $('#advancePaymentModal').on('hidden.bs.modal', function() {
        advancePaymentSubmitting = false;
        setAdvancePaymentLoading(false);
    });
</script>

<script>
    $(document).ready(function() {
        var table = $('#customer-ledger-table').DataTable({
            dom: `<"row"<"col-sm-6"l><"col-sm-6 text-end"f>>` + // Show entries and search in one row
                `<"row"<"col-sm-12 text-end"B>>` + // Buttons in a separate row
                `<"row dt-layout-row"<"col-sm-12"tr>>` + // Table content
                `<"row"<"col-sm-5"i><"col-sm-7 text-end"p>>`, // Info left, pagination right
            keys: true,
            stateSave: true,
            lengthChange: true,
            responsive: true,
            pagingType: "full_numbers",
            processing: true,
            // serverSide: true,
            ajax: {
                url: "./pages/customer/customer_ledger_ajax.php",
                type: "GET",
                data: function(d) {
                    d.token = $("#token").val();
                    d.date_from = $("#date-from").val();
                    d.date_to   = $("#date-to").val();
                }
            },
            columns: [{
                    data: 'sl'
                },
                {
                    data: 'date'
                },
                {
                    data: 'description'
                },
                {
                    data: 'amount'
                },
                {
                    data: 'received_by'
                },
                {
                    data: 'acc_id',
                    // width: '7%',
                    render: function(data, type, row) {
                        let html = `
                            <a target="_blank"
                               href="pages/print/payment_invoice.php?token=${data}"
                               class="btn btn-xs btn-success">
                                Print
                            </a>
                        `;
                    
                        <?php if ($ty == "SA") { ?>
                    
                            html += `
                                <button class="btn btn-xs btn-danger"
                                        onclick="deletePayment(${data}, ${row.amount}, ${row.acc_type})">
                                    Delete
                                </button>
                            `;
                    
                        <?php } ?>
                    
                        return html;
                    }
                },
            ],
            buttons: [{
                extend: "pdf",
                className: "btn-success",
                text: "Print Pdf",
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                },

                customize: function(doc) {
                    // Initialize the title with a base string
                    var title = 'View Individual Customer Payment';

                    // Update the title in the PDF
                    doc.content.splice(0, 1, {
                        text: title,
                        fontSize: 16,
                        bold: true,
                        alignment: 'center',
                        margin: [0, 0, 0, 20], // [left, top, right, bottom]
                    });
                },
            }],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search customers...",
                lengthMenu: "Show _MENU_ entries",
                emptyTable: "No data available in table"
            },
            lengthMenu: [10, 25, 50, 100, 500],
            order: [
                [1, 'asc']
            ],
            drawCallback: function(settings) {

                $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
                $("#total-amount").text(settings.json?.totalAmount);
                $("#bill_amount").text(settings.json?.billing_info?.monthlybill);
                $("#total_paid_amount").text(settings.json?.billing_info?.totalpaid);
                $("#total_discount_amount").text(settings.json?.billing_info?.totaldiscount);
                const due_advance = settings.json?.billing_info?.dueadvance ?? 0;
                if(due_advance < 0){
                    $("#due_advance_icon").removeClass("text-danger bg-danger-focus").addClass("text-success bg-success-focus");
                    $("#due_advance_text").text("Advance").addClass("text-success fw-bold");
                    $("#previous_due").text(Math.abs(due_advance));
                }else if(due_advance == 0){
                    $("#due_advance_icon").removeClass("text-danger bg-danger-focus").addClass("text-success bg-success-focus");
                     $("#previous_due").text(due_advance);
                }else {
                    $("#due_advance_icon").addClass("text-danger bg-danger-focus").addClass("text-danger bg-danger-focus");
                    $("#previous_due").text(due_advance);
                }
                $("#total_previous_amount").text(settings.json?.billing_info?.previousdue);
                // $("#billig-type-col").text($("#billing-type option:selected").text())

                // agent information
                const agentInfo = settings.json?.agentInfo;



                $("#agentName").text(agentInfo?.ag_name);
                $("#customer_id").text(agentInfo?.cus_id);
                $("#agentMobile").text(agentInfo?.ag_mobile_no);
                $("#package").text(agentInfo?.mb);
                $("#ipAddress").text(agentInfo?.ip);
                $("#taka").text(agentInfo?.taka);
                $("#address").text(agentInfo?.ag_office_address);


                $("#customer_email").text(agentInfo?.ag_email);
                $("#customer_nid_passport").text(agentInfo?.national_id ?? "N/A");
                $("#customer_zone_area").text(agentInfo?.zone_name ?? "N/A");
                $("#mac_address").text(agentInfo?.onumac);
                $("#disconnected_time").text(agentInfo?.mikrotik_disconnect);

                // console.log(agentInfo);
                
                // customer edit link 
                $("#customer_id_edit_link").attr(
                    "href",
                    `?page=customer_edit&token=${agentInfo?.ag_id}`
                );

                // advance payment
                $("#ag_id").val(agentInfo?.ag_id);
                $("#cus_id").val(agentInfo?.cus_id);
            },
            initComplete: function(settings, json) {}
        });

        // Trigger table reload when filters are changed
        $('#select-month, #select-year, #billing-type, #zone-filter').on('change', function() {
            table.ajax.reload();
        });
        $('#date-from, #date-to').on('change', function () {
            $('#customer-ledger-table').DataTable().ajax.reload();
        });
    });

    // delete Customer Payment
    function deletePayment(acc_id, acc_amount, acc_type) {
        if (confirm("Are you sure to delete this payment?")) {
            var customer_id = $("#token").val();
            $.ajax({
                url: "./pages/customer/delete_payment_ajax.php",
                type: "POST",
                data: {
                    customer_id: customer_id,
                    acc_id: acc_id,
                    acc_amount: acc_amount,
                    acc_type: acc_type,
                    cus_id: $("#customer_id").text()
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Payment Deleted Successfully",
                            timer: 1500,
                            showConfirmButton: false,
                            position: "top-end"
                        });
                        window.location.reload();
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: response.message,
                            timer: 1500,
                            showConfirmButton: false,
                            position: "top-end"
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: "error",
                        title: "Error: " + error,
                        timer: 1500,
                        showConfirmButton: false,
                        position: "top-end"
                    });
                }
            })
        }
    }



    // advance Payment
    function advancePayment() {
        if (advancePaymentSubmitting) {
            return false;
        }

        const form = document.getElementById('advancePaymentForm');
        if (form && !form.checkValidity()) {
            form.reportValidity();
            return false;
        }

        advancePaymentSubmitting = true;
        setAdvancePaymentLoading(true);

        const serialize = $("#advancePaymentForm").serialize();
        $.ajax({
            url: "./pages/customer/advance_payment.php",
            type: "POST",
            data: serialize,
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: "success",
                        title: response.message,
                        timer: 1500,
                        showConfirmButton: false,
                        position: "top-end"
                    });
                    $('#advancePaymentModal').modal('hide');
                    window.location.reload();
                } else {
                    advancePaymentSubmitting = false;
                    setAdvancePaymentLoading(false);
                    Swal.fire({
                        icon: "error",
                        title: response.message,
                        timer: 1500,
                        showConfirmButton: false,
                        position: "top-end"
                    });
                }
            },
            error: function(xhr, status, error) {
                advancePaymentSubmitting = false;
                setAdvancePaymentLoading(false);
                Swal.fire({
                    icon: "error",
                    title: "Error: " + error,
                    timer: 1500,
                    showConfirmButton: false,
                    position: "top-end"
                });
            }
        })
    }
</script>
<script>
    $(document).on("click", ".delete-discount", function(e) {
        e.preventDefault();

        let id = $(this).data("id");
        let amount = $(this).data("amount");
        let customerId = $(this).data("customer-id");
        let btn = $(this);

        Swal.fire({
            title: "Are you sure?",
            text: "You are about to delete a discount of " + amount + " Taka.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!",
            background: '#fff',
            color: '#1e293b',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "./pages/customer/delete_discount_ajax.php",
                    type: "POST",
                    data: {
                        bonus_id: id,
                        amount: amount,
                        customerId: customerId,
                    },
                    success: function(response) {
                        const json = JSON.parse(response);
                        if (json.success) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Discount deleted successfully!',
                                showConfirmButton: false,
                                timer: 1500
                            });

                            btn.closest("tr").fadeOut(400, function() {
                                $(this).remove();
                            });

                            setTimeout(() => {
                                location.reload();
                            }, 500);

                        } else {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: 'Failed to delete discount.',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Could not connect to the server!'
                        });
                    }
                });
            }
        });
    });

    $(document).on("click", ".delete-previous-due", function(e) {
        e.preventDefault();

        let id = $(this).data("id");
        let amount = $(this).data("amount");
        let customerId = $(this).data("customer-id");
        let btn = $(this);

        Swal.fire({
            title: "Are you sure?",
            text: "You are about to delete a previous due of " + amount + " Taka.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!",
            background: '#fff',
            color: '#1e293b',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "./pages/customer/delete_previous_due_ajax.php",
                    type: "POST",
                    data: {
                        previous_due_id: id,
                        amount: amount,
                        customerId: customerId,
                    },
                    success: function(response) {
                        const json = JSON.parse(response);
                        if (json.success) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'previous due deleted successfully!',
                                showConfirmButton: false,
                                timer: 1500
                            });

                            btn.closest("tr").fadeOut(400, function() {
                                $(this).remove();
                            });

                            setTimeout(() => {
                                location.reload();
                            }, 500);

                        } else {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: 'Failed to delete previous due.',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Could not connect to the server!'
                        });
                    }
                });
            }
        });
    });
</script>
<script>
    (function() {
        const masked = '••••••••';
        const passEl = document.getElementById('mtPassword');
        const toggleBtn = document.getElementById('togglePassBtn');
        const statusText = document.getElementById('statusText');
        const mtStatus = document.getElementById('mtStatus');
        const microticStatus = $('#microticStatus');
        let enabled = false;

        function updateStatusUI() {
            statusText.textContent = enabled ? 'Enabled' : 'Disabled';
            mtStatus.style.background = enabled ? 'rgba(22,163,74,0.3)' : 'rgba(220,38,38,0.3)';
            mtStatus.style.color = enabled ? '#065f46' : '#7f1d1d';
            document.getElementById('enableBtn').disabled = enabled;
            document.getElementById('disableBtn').disabled = !enabled;
        }

        const agId = $("#token").val();

        $.ajax({
            url: "./pages/customer/get_mikrotik_data.php",
            type: "GET",
            data: {
                ag_id: agId
            },
            success: function(response) {
                console.log("Mikrotik data sss:", response);

                let json;
                try {
                    json = JSON.parse(response);
                } catch (e) {
                    console.error("Invalid JSON:", e, response);
                    microticStatus.text('MikroTik Not Connected').css('color', '#b91c1c');
                    return;
                }

                if (json.success) {
                    const d = json.data;
                    microticStatus.text('✅ MikroTik Is Connected').css('color', '#047857');

                    $("#mtId").text(d.name);
                    $("#mtProfile").text(d.profile);
                    $("#mtPassword").text(masked).data("realPass", d.password);
                    $("#mtLastLogout").text(d['last-logged-out'] || 'N/A');
                    enabled = (d.disabled === 'false');
                    updateStatusUI();
                } else {
                    
                    microticStatus.text('⚠️ MikroTik doesn’t have this user').css('color', '#b91c1c');
                    enabled = false;
                    document.getElementById('enableBtn').classList.add("d-none");
                    document.getElementById('disableBtn').classList.add("d-none");
                    // console.log(document.getElementById('enableBtn'));
                    updateStatusUI();
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching Mikrotik data:", error);
                microticStatus.text('❌ MikroTik Connection Failed').css('color', '#b91c1c');
                enabled = false;
                updateStatusUI();
            }
        });

        toggleBtn.addEventListener('click', () => {
            const realPass = $("#mtPassword").data("realPass");
            if (passEl.textContent === masked) {
                passEl.textContent = realPass || 'N/A';
                toggleBtn.textContent = 'Hide';
            } else {
                passEl.textContent = masked;
                toggleBtn.textContent = 'Show';
            }
        });
        document.getElementById('enableBtn').addEventListener('click', () => {
            const status = 'Disable';
            const secretName = $("#mtId").text();
            const microticIp = "<?php echo $customer[0]['id']; ?>";
            $.ajax({
                type: 'GET',
                url: './pages/customer/update_mikrotik_state.php',
                data: {
                    name: secretName,
                    state: status,
                    selectedMktik: microticIp
                },
                dataType: 'json', // expect JSON response
                success: function(response) {
                    console.log('Mikrotik Response:', response);
                    if (response.success) {
                        alert('✅ ' + response.message);
                    } else {
                        alert('⚠️ ' + (response.message || 'Operation failed.'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    alert('❌ Connection to MikroTik failed.');
                }
            });
            enabled = true;
            updateStatusUI();
        });

        document.getElementById('disableBtn').addEventListener('click', () => {
            const status = 'Enable';
            const secretName = $("#mtId").text();
            const microticIp = "<?php echo $customer[0]['id']; ?>";

            console.log(agId, status, secretName, microticIp);
            $.ajax({
                type: 'GET',
                url: './pages/customer/update_mikrotik_state.php',
                data: {
                    name: secretName,
                    state: status,
                    selectedMktik: microticIp
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Mikrotik Response:', response);
                    if (response.success) {
                        alert('✅ ' + response.message);
                    } else {
                        alert('⚠️ ' + (response.message || 'Operation failed.'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    alert('❌ Connection to MikroTik failed.');
                }
            });
            enabled = false;
            updateStatusUI();
        });
    })();
</script>
<?php $obj->end_script(); ?>
