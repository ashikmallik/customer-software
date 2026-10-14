<?php
include('dashboard.php');
$stock = $obj->raw_sql("
    SELECT stock.*,
    p.product_name,
    s.supplier_name,
     _createuser.FullName
    FROM stock
    LEFT JOIN products p ON stock.product_id = p.product_id
    LEFT JOIN suppliers s ON stock.supplier_id = s.supplier_id
    LEFT JOIN _createuser ON stock.created_by = _createuser.UserId
    where stock.deleted_at is null order by stock.stock_id desc limit 5 offset 0");
$i = 1; // Initialize counter
$minimum_threshold = 0;
foreach ($stock as &$row) {
    $row['sl'] = $i++; // Add the row number
    $row['minimum_threshold'] = $minimum_threshold;
}


?>


<!--<div class="row" id="mikrotikLoader">-->
<!--    <div class="col-xxl-3 col-lg-4 col-md-6 mb-4">-->
<!--        <div class="card border-0 shadow-lg h-100">-->

<!--            <div class="card-header placeholder-glow">-->
<!--                <span class="placeholder col-6"></span>-->
<!--                <span class="placeholder col-4"></span>-->
<!--            </div>-->

<!--            <div class="card-body">-->
<!--                <p class="placeholder-glow">-->
<!--                    <span class="placeholder col-7"></span>-->
<!--                    <span class="placeholder col-5"></span>-->
<!--                </p>-->

<!--                <p class="placeholder-glow">-->
<!--                    <span class="placeholder col-6"></span>-->
<!--                    <span class="placeholder col-4"></span>-->
<!--                </p>-->

<!--                <div class="progress">-->
<!--                    <div class="progress-bar placeholder col-12"></div>-->
<!--                </div>-->
<!--            </div>-->

<!--            <div class="card-footer">-->
<!--                <span class="placeholder col-5"></span>-->
<!--                <span class="placeholder col-5"></span>-->
<!--            </div>-->

<!--        </div>-->
<!--    </div>-->
<!--</div>-->


<!--<div class="row d-none" id="mikrotikCards"></div>-->



<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-3">
        <a href="?page=customer_view">
            <div class="card-body p-0 d-flex align-items-center justify-content-between">
                <!-- Icon Section -->
                <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                    <span class="w-40-px h-40-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                        <iconify-icon icon="flowbite:users-group-solid" class="icon"></iconify-icon>
                    </span>
                </div>
                <!-- Text Section -->
                <div class="text-end">
                    <h6 class="fw-semibold my-1 text-neutral-600">
                        <?php echo $obj->Total_Count("tbl_agent", "deleted_at IS NULL") ?? 0; ?>
                    </h6>
                    <span class="mb-0 fw-medium text-secondary-light text-md">Total Customers</span>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-3">
        <a href="?page=customer_view&ag_status=active">
            <div class="card-body p-0 d-flex align-items-center justify-content-between">
                <!-- Icon Section -->
                <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                    <span class="w-40-px h-40-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                        <iconify-icon icon="flowbite:users-group-solid" class="icon"></iconify-icon>
                    </span>
                </div>
                <!-- Text Section -->
                <div class="text-end">
                    <h6 class="fw-semibold my-1 text-neutral-600">
                        <?php echo $obj->Total_Count("tbl_agent", "deleted_at IS NULL AND ag_status='1'") ?? 0; ?>
                    </h6>
                    <span class="mb-0 fw-medium text-secondary-light text-md">Total Active Customer</span>
                </div>
            </div>
        </a>
    </div>
</div>



<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-5">
        <a href='?page=customer_view&ag_status=inactive'>
            <div class="card-body p-0 d-flex align-items-center justify-content-between">
                <!-- Icon Section -->
                <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                    <span class="w-40-px h-40-px bg-red flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                        <iconify-icon icon="mdi:account-off" class="text-white text-2xl"></iconify-icon>
                    </span>
                </div>
                <!-- Text Section -->
                <div class="text-end">
                    <h6 class="fw-semibold my-1 text-neutral-600">
                        <?php echo $obj->Total_Count("tbl_agent", "deleted_at IS NULL AND ag_status='0'") ?? 0; ?>
                    </h6>
                    <span class="mb-0 fw-medium text-secondary-light text-md">
                        Total Inactive Customer
                    </span>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-5">
        <a href='?page=customer_view&ag_status=discontinue'>
            <div class="card-body p-0 d-flex align-items-center justify-content-between">
                <!-- Icon Section -->
                <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                    <span class="w-40-px h-40-px bg-red flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0" style="padding: 15px;">
                        <!-- <iconify-icon icon="mdi:account-off" class="text-white text-2xl"></iconify-icon> -->
                        <i class="fa-solid fa-user-xmark" style="font-size: 20px;"></i>
                    </span>
                </div>
                <!-- Text Section -->
                <div class="text-end">
                    <h6 class="fw-semibold my-1 text-neutral-600">
                        <?php echo $obj->Total_Count("tbl_agent", "deleted_at IS NULL AND ag_status='3'") ?? 0; ?>
                    </h6>
                    <span class="mb-0 fw-medium text-secondary-light text-md">
                        Total Discontinue Customer
                    </span>
                </div>
            </div>
        </a>
    </div>
</div>


<!-- free customers -->
<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-2">
        <a href='?page=customer_view&ag_status=free'>
            <div class="card-body p-0 d-flex align-items-center justify-content-between">
                <!-- Icon Section -->
                <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                    <span class="w-40-px h-40-px bg-purple flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                        <iconify-icon icon="mdi-account-tag-outline" class="text-white text-2xl"></iconify-icon>
                    </span>
                </div>
                <!-- Text Section -->
                <div class="text-end">
                    <h6 class="fw-semibold my-1 text-neutral-600">
                        <?php echo $obj->Total_Count("tbl_agent", "deleted_at IS NULL AND ag_status='2'") ?? 0; ?>
                    </h6>
                    <span class="mb-0 fw-medium text-secondary-light text-md">
                        Total Free Customer
                    </span>
                </div>
            </div>
        </a>
    </div>
</div>


<!-- running month customer -->
<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-4">
        <a href='?page=running_month_customers'>
            <div class="card-body p-0 d-flex align-items-center justify-content-between">
                <!-- Icon Section -->
                <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                    <span class="w-40-px h-40-px bg-success-main flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                        <iconify-icon icon="mdi-account-plus-outline" class="text-white text-2xl"></iconify-icon>
                    </span>
                </div>
                <!-- Text Section -->
                <div class="text-end">
                    <h6 class="fw-semibold my-1 text-neutral-600">
                        <?php echo $obj->Total_Count("tbl_agent", "deleted_at IS NULL AND MONTH(entry_date)='$currentNumericMonth' AND YEAR(entry_date)='$currentNumericYear'"); ?>
                    </h6>
                    <span class="mb-0 fw-medium text-secondary-light text-md">
                        <?= date('F') . " New Customers"; ?>
                    </span>
                </div>
            </div>
        </a>
    </div>
</div>



<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-5">
        <a href='?page=monthly_inactive_customer'>
            <div class="card-body p-0 d-flex align-items-center justify-content-between">
                <!-- Icon Section -->
                <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                    <span class="w-40-px h-40-px bg-red flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                        <iconify-icon icon="mdi:account-off" class="text-white text-2xl"></iconify-icon>
                    </span>
                </div>
                <!-- Text Section -->
                <div class="text-end">
                    <h6 class="fw-semibold" id="">
                        <?php 
                            echo $obj->rawSqlSingle("
                                SELECT COUNT(*) as TotalUsers 
                                FROM tbl_agent 
                                WHERE deleted_at IS NULL
                                  AND ag_status = '0'
                                  AND MONTH(inactive_date) = MONTH(CURDATE())
                                  AND YEAR(inactive_date) = YEAR(CURDATE())
                            ")["TotalUsers"]; 
                        ?>
                    </h6>
                    <span class="mb-0 fw-medium text-secondary-light text-md">
                        <?= date("F"); ?> Inactive Customer
                    </span>
                </div>
            </div>
        </a>
    </div>
</div>


<!-- total bill collectoin -->
<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-3">
        <div class="card-body p-0 d-flex align-items-center justify-content-between">
            <!-- Icon Section -->
            <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                <span class="w-40-px h-40-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                    <iconify-icon icon="hugeicons:invoice-03" class="icon"></iconify-icon>
                </span>
            </div>
            <!-- Text Section -->
            <div class="text-end">
                <h6 class="fw-semibold my-1 text-neutral-600">
                    <i class="fa-solid fa-bangladeshi-taka-sign"></i>
                    <?php
                    // echo $obj->rawSqlSingle("SELECT SUM(totalpaid) as TotalCollectedBill FROM `customer_billing`")["TotalCollectedBill"];
                    echo $obj->rawSqlSingle("SELECT SUM(acc_amount) as TotalCollectedBill FROM `tbl_account` WHERE acc_type NOT IN (1,22)")["TotalCollectedBill"];
                    ?>
                </h6>
                </h6>
                <span class="mb-2 fw-medium text-secondary-light text-sm">Total Collected Bill as of <span class="text-primary"><?php echo date('F') . " " . date("Y"); ?></span> </span>
            </div>
        </div>
    </div>
</div>



<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-2">
        <div class="card-body p-0 d-flex align-items-center justify-content-between">
            <!-- Icon Section -->
            <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                <span class="w-40-px h-40-px bg-purple flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                    <iconify-icon icon="solar:wallet-bold" class="text-white text-2xl"></iconify-icon>
                </span>
            </div>
            <!-- Text Section -->
            <div class="text-end">
                <h6 class="fw-semibold my-1 text-neutral-600">
                    <i class="fa-solid fa-bangladeshi-taka-sign"></i>
                    <?php echo $totalBillSummary['dueAdvacne'] ?? 0; ?>
                </h6>
                <span class="mb-0 fw-medium text-secondary-light text-md">
                    <?php echo "Total Due Bill"; ?>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-3">
        <div class="card-body p-0 d-flex align-items-center justify-content-between">
            <!-- Icon Section -->
            <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                <span class="w-40-px h-40-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                    <iconify-icon icon="mdi:lightning-bolt" class="text-white text-2xl"></iconify-icon>
                </span>
            </div>
            <!-- Text Section -->
            <div class="text-end">
                <h6 class="fw-semibold my-1 text-neutral-600">
                    <i class="fa-solid fa-bangladeshi-taka-sign"></i>
                    <?php echo $totalBillSummary['totalPreviousDue'] ?? 0; ?>
                </h6>
                <span class="mb-0 fw-medium text-secondary-light text-md">
                    <?php echo  "Total Previous Due"; ?>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-5">
        <div class="card-body p-0 d-flex align-items-center justify-content-between">
            <!-- Icon Section -->
            <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                <span class="w-40-px h-40-px bg-red flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                    <iconify-icon icon="fa6-solid:file-invoice-dollar" class="text-white text-2xl"></iconify-icon>
                </span>
            </div>
            <!-- Text Section -->
            <div class="text-end">
                <h6 class="fw-semibold my-1 text-neutral-600">
                    <i class="fa-solid fa-bangladeshi-taka-sign"></i>
                    <?php
                    echo $totalBillSummary['runningMonthDue'] ?? 0;
                    ?>
                </h6>
                <span class="mb-0 fw-medium text-secondary-light text-md">
                    <?php echo date('F') . " Due Bill"; ?>
                </span>
            </div>
        </div>
    </div>
</div>


<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-4">
        <div class="card-body p-0 d-flex align-items-center justify-content-between">
            <!-- Icon Section -->
            <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                <span class="w-40-px h-40-px bg-success-main flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                    <iconify-icon icon="fa6-solid:file-invoice-dollar" class="text-white text-2xl"></iconify-icon>
                </span>
            </div>
            <!-- Text Section -->
            <div class="text-end">
                <h6 class="fw-semibold my-1 text-neutral-600">
                    <i class="fa-solid fa-bangladeshi-taka-sign"></i>
                    <?php echo $get_all_collection['amount'] === null ? "0" : $get_all_collection['amount']; ?>
                </h6>
                <span class="mb-0 fw-medium text-secondary-light text-md">
                    <?php echo date('F') . " Total Collection"; ?>
                </span>
            </div>
        </div>
    </div>
</div>



<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-4">
        <a href='?page=all_paid'>
            <div class="card-body p-0 d-flex align-items-center justify-content-between">
                <!-- Icon Section -->
                <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                    <span class="w-40-px h-40-px bg-success-main flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                        <iconify-icon icon="mdi-account-plus-outline" class="text-white text-2xl"></iconify-icon>
                    </span>
                </div>
                <!-- Text Section -->
                <div class="text-end">
                    <h6 class="fw-semibold my-1 text-neutral-600">
                        <i class="fa-solid fa-bangladeshi-taka-sign"></i>
                        <?php echo $obj->rawSqlSingle(
                            "SELECT COUNT(id) as totalDueCustomers 
                            FROM `customer_billing` AS CB
                            JOIN `tbl_agent` ON `CB`.`agid` = `tbl_agent`.`ag_id`
                            WHERE CB.dueadvance <= 0 AND tbl_agent.deleted_at IS NULL AND tbl_agent.ag_status = 1 AND CB.totalpaid > 0 AND CB.generate_at != '2024-01-01'
                            "
                        )["totalDueCustomers"];
                        ?>
                    </h6>
                    <span class="mb-0 fw-medium text-secondary-light text-md">
                        Total Paid Customers
                    </span>
                </div>
            </div>
        </a>
    </div>
</div>


<div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-5">
        <a href='?page=billcollection_view'>
            <div class="card-body p-0 d-flex align-items-center justify-content-between">
                <!-- Icon Section -->
                <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
                    <span class="w-40-px h-40-px bg-red flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                        <iconify-icon icon="mdi:account-off" class="text-white text-2xl"></iconify-icon>
                    </span>
                </div>
                <!-- Text Section -->
                <div class="text-end">
                    <h6 class="fw-semibold my-1 text-neutral-600">
                        <?php echo $obj->rawSqlSingle(
                            "SELECT COUNT(id) as totalDueCustomers 
                            FROM `customer_billing` AS CB
                            JOIN `tbl_agent` ON `CB`.`agid` = `tbl_agent`.`ag_id`
                            WHERE CB.dueadvance > 0 AND tbl_agent.deleted_at IS NULL AND tbl_agent.ag_status = 1 AND CB.generate_at != '2024-01-01'
                            "
                        )["totalDueCustomers"];
                        ?>
                    </h6>
                    <span class="mb-0 fw-medium text-secondary-light text-md">
                        Total Due Customers
                    </span>
                </div>
            </div>
        </a>
    </div>
</div>



<!-- <div class="col-xxl-3 col-sm-6">
    <div class="card px-16 py-12 shadow-none radius-8 border h-auto bg-gradient-start-3">
        <div class="card-body p-0 d-flex align-items-center justify-content-between">
<div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-16">
    <span class="w-40-px h-40-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
        <iconify-icon icon="mdi:lightning-bolt" class="text-white text-2xl"></iconify-icon>
    </span>
</div>
<div class="text-end">
    <h6 class="fw-semibold my-1 text-neutral-600">
        <i class="fa-solid fa-bangladeshi-taka-sign"></i>
        <?php echo $get_connection_charge['amount'] === null ? "0" : $get_connection_charge['amount']; ?>
    </h6>
    <span class="mb-0 fw-medium text-secondary-light text-md">
        <?php echo  date('F') . " C.Charge"; ?>
    </span>
</div>
</div>
</div>
</div> -->


<!-- ==================== User Wise Collection ==================== -->
<div class="col-12">
    <div class="card shadow-none border radius-8 h-100">
        <div class="card-header border-bottom bg-base py-16 px-24 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h6 class="text-lg fw-semibold mb-0">User Wise Collection</h6>
                <span class="text-sm text-secondary-light" id="uwcRangeLabel">Today</span>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <select id="uwcPreset" class="form-select form-select-sm w-auto">
                    <option value="today" selected>Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="last7">Last 7 Days</option>
                    <option value="this_month">This Month</option>
                    <option value="last_month">Last Month</option>
                    <option value="custom">Custom Range</option>
                </select>
                <input type="date" id="uwcFrom" class="form-control form-control-sm w-auto" value="<?php echo date('Y-m-d'); ?>">
                <span class="text-secondary-light">to</span>
                <input type="date" id="uwcTo" class="form-control form-control-sm w-auto" value="<?php echo date('Y-m-d'); ?>">
                <button type="button" id="uwcFilterBtn" class="btn btn-primary-600 btn-sm px-16">Filter</button>
            </div>
        </div>
        <div class="card-body p-24">
            <div class="row g-3 mb-3">
                <div class="col-6 col-md-3">
                    <div class="border radius-8 p-12 text-center">
                        <div class="text-sm text-secondary-light">Users</div>
                        <h6 class="mb-0 fw-semibold" id="uwcSumUsers">0</h6>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="border radius-8 p-12 text-center">
                        <div class="text-sm text-secondary-light">Customers</div>
                        <h6 class="mb-0 fw-semibold" id="uwcSumCustomers">0</h6>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="border radius-8 p-12 text-center">
                        <div class="text-sm text-secondary-light">Transactions</div>
                        <h6 class="mb-0 fw-semibold" id="uwcSumTrx">0</h6>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="border radius-8 p-12 text-center">
                        <div class="text-sm text-secondary-light">Total Collection</div>
                        <h6 class="mb-0 fw-semibold text-success-main"><i class="fa-solid fa-bangladeshi-taka-sign"></i> <span id="uwcSumAmount">0</span></h6>
                    </div>
                </div>
            </div>
            <style>
                .uwc-table-wrap {
                    --uwc-border: #e8eef7;
                    --uwc-head-bg: #f5f8ff;
                    --uwc-head-text: #64748b;
                    --uwc-text: #1e293b;
                    --uwc-muted: #94a3b8;
                    --uwc-row-hover: #f8fbff;
                    --uwc-foot-bg: #f1f5ff;
                    --uwc-bar-bg: #eef2f7;
                    --uwc-pill-bg: #eef4ff;
                    --uwc-pill-text: #2563eb;
                    border: 1px solid var(--uwc-border);
                    border-radius: 12px;
                    overflow-x: auto;
                }
                [data-theme="dark"] .uwc-table-wrap,
                html.dark .uwc-table-wrap,
                body.dark .uwc-table-wrap {
                    --uwc-border: #273047;
                    --uwc-head-bg: #1a2133;
                    --uwc-head-text: #94a3b8;
                    --uwc-text: #e2e8f0;
                    --uwc-muted: #64748b;
                    --uwc-row-hover: #1c2438;
                    --uwc-foot-bg: #1a2133;
                    --uwc-bar-bg: #273047;
                    --uwc-pill-bg: #1e2a47;
                    --uwc-pill-text: #93b4ff;
                }
                .uwc-table { width: 100%; min-width: 760px; border-collapse: separate; border-spacing: 0; margin: 0; color: var(--uwc-text); }
                .uwc-table th {
                    background: var(--uwc-head-bg); color: var(--uwc-head-text);
                    font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .04em;
                    padding: 12px 16px; border-bottom: 1px solid var(--uwc-border); white-space: nowrap;
                }
                .uwc-table td { padding: 12px 16px; border-bottom: 1px solid var(--uwc-border); vertical-align: middle; font-size: 14px; }
                .uwc-table tbody tr { transition: background .15s ease; }
                .uwc-table tbody tr:hover { background: var(--uwc-row-hover); }
                .uwc-table tbody tr:last-child td { border-bottom: 0; }
                .uwc-table tfoot td { background: var(--uwc-foot-bg); font-weight: 700; border-top: 1px solid var(--uwc-border); border-bottom: 0; }

                .uwc-rank {
                    width: 28px; height: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;
                    font-size: 12px; font-weight: 700; color: var(--uwc-muted); background: var(--uwc-bar-bg);
                }
                .uwc-rank.r1 { background: linear-gradient(135deg, #fde68a, #f59e0b); color: #fff; }
                .uwc-rank.r2 { background: linear-gradient(135deg, #e2e8f0, #94a3b8); color: #fff; }
                .uwc-rank.r3 { background: linear-gradient(135deg, #fed7aa, #c2410c); color: #fff; }

                .uwc-user { display: flex; align-items: center; gap: 10px; }
                .uwc-avatar {
                    width: 36px; height: 36px; border-radius: 10px; flex-shrink: 0;
                    display: flex; align-items: center; justify-content: center;
                    color: #fff; font-weight: 700; font-size: 13px;
                }
                .uwc-user-name { font-weight: 600; line-height: 1.2; }
                .uwc-user-sub { font-size: 12px; color: var(--uwc-muted); }

                .uwc-pill {
                    display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 999px;
                    background: var(--uwc-pill-bg); color: var(--uwc-pill-text); font-weight: 600; font-size: 13px;
                }
                .uwc-amount { font-variant-numeric: tabular-nums; white-space: nowrap; }
                .uwc-amount.muted { color: var(--uwc-muted); }
                .uwc-total { color: #16a34a; font-weight: 700; }

                .uwc-share { min-width: 140px; }
                .uwc-share-bar { height: 6px; border-radius: 999px; background: var(--uwc-bar-bg); overflow: hidden; }
                .uwc-share-bar span { display: block; height: 100%; border-radius: 999px; background: linear-gradient(90deg, #60a5fa, #2563eb); }
                .uwc-share-text { font-size: 12px; color: var(--uwc-muted); margin-top: 4px; }

                .uwc-empty { padding: 40px 16px !important; text-align: center; color: var(--uwc-muted); }
                .uwc-empty iconify-icon { font-size: 40px; display: block; margin: 0 auto 8px; opacity: .6; }
            </style>
            <div class="uwc-table-wrap">
                <table class="uwc-table">
                    <thead>
                        <tr>
                            <th style="width:60px">#</th>
                            <th>User</th>
                            <th class="text-center">Customers</th>
                            <th class="text-center">Transactions</th>
                            <th class="text-end">Bill Collection</th>
                            <th>Share</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody id="uwcBody">
                        <tr><td colspan="7" class="uwc-empty">Loading...</td></tr>
                    </tbody>
                    <tfoot id="uwcFoot"></tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        const $preset = document.getElementById('uwcPreset');
        const $from = document.getElementById('uwcFrom');
        const $to = document.getElementById('uwcTo');
        const $body = document.getElementById('uwcBody');

        const fmt = d => {
            const p = n => String(n).padStart(2, '0');
            return `${d.getFullYear()}-${p(d.getMonth() + 1)}-${p(d.getDate())}`;
        };
        const money = n => Number(n || 0).toLocaleString('en-US', { maximumFractionDigits: 2 });
        const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

        function applyPreset(val) {
            const now = new Date();
            let from = new Date(now), to = new Date(now);
            switch (val) {
                case 'yesterday':
                    from.setDate(now.getDate() - 1);
                    to = new Date(from);
                    break;
                case 'last7':
                    from.setDate(now.getDate() - 6);
                    break;
                case 'this_month':
                    from = new Date(now.getFullYear(), now.getMonth(), 1);
                    break;
                case 'last_month':
                    from = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                    to = new Date(now.getFullYear(), now.getMonth(), 0);
                    break;
                case 'custom':
                    return;
            }
            $from.value = fmt(from);
            $to.value = fmt(to);
        }

        const $foot = document.getElementById('uwcFoot');
        const avatarColors = ['#6366f1', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6'];
        const initials = name => String(name || '?').trim().split(/\s+/).slice(0, 2).map(w => w[0]).join('').toUpperCase();
        const colorFor = name => avatarColors[[...String(name)].reduce((a, c) => a + c.charCodeAt(0), 0) % avatarColors.length];
        const tk = n => `<i class="fa-solid fa-bangladeshi-taka-sign" style="font-size:.85em"></i> ${money(n)}`;
        const emptyRow = (msg, icon) => `<tr><td colspan="7" class="uwc-empty"><iconify-icon icon="${icon}"></iconify-icon>${msg}</td></tr>`;

        function load() {
            $foot.innerHTML = '';
            $body.innerHTML = emptyRow('Loading...', 'svg-spinners:3-dots-fade');
            fetch(`./pages/dashbaord/user_wise_collection_ajax.php?from=${encodeURIComponent($from.value)}&to=${encodeURIComponent($to.value)}`)
                .then(r => r.json())
                .then(res => {
                    document.getElementById('uwcRangeLabel').textContent =
                        res.from === res.to ? res.from : `${res.from} to ${res.to}`;
                    document.getElementById('uwcSumUsers').textContent = res.summary.users;
                    document.getElementById('uwcSumCustomers').textContent = res.summary.customers;
                    document.getElementById('uwcSumTrx').textContent = res.summary.transactions;
                    document.getElementById('uwcSumAmount').textContent = money(res.summary.amount);

                    if (!res.data.length) {
                        $body.innerHTML = emptyRow('No collection found for this period', 'mdi:cash-remove');
                        return;
                    }
                    const grand = res.summary.amount || 0;
                    let billSum = 0;
                    $body.innerHTML = res.data.map(r => {
                        billSum += r.bill_collection;
                        const pct = grand > 0 ? (r.total_amount / grand * 100) : 0;
                        return `
                        <tr>
                            <td><span class="uwc-rank ${r.sl <= 3 ? 'r' + r.sl : ''}">${r.sl}</span></td>
                            <td>
                                <div class="uwc-user">
                                    <div class="uwc-avatar" style="background:${colorFor(r.user_name)}">${esc(initials(r.user_name))}</div>
                                    <div>
                                        <div class="uwc-user-name">${esc(r.user_name)}</div>
                                        <div class="uwc-user-sub">${r.total_customers} customer${r.total_customers === 1 ? '' : 's'}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center"><span class="uwc-pill"><iconify-icon icon="mdi:account-group"></iconify-icon>${r.total_customers}</span></td>
                            <td class="text-center">${r.total_transactions}</td>
                            <td class="text-end uwc-amount ${r.bill_collection ? '' : 'muted'}">${tk(r.bill_collection)}</td>
                            <td class="uwc-share">
                                <div class="uwc-share-bar"><span style="width:${pct.toFixed(1)}%"></span></div>
                                <div class="uwc-share-text">${pct.toFixed(1)}%</div>
                            </td>
                            <td class="text-end uwc-amount uwc-total">${tk(r.total_amount)}</td>
                        </tr>`;
                    }).join('');
                    $foot.innerHTML = `
                        <tr>
                            <td></td>
                            <td>Total</td>
                            <td class="text-center">${res.summary.customers}</td>
                            <td class="text-center">${res.summary.transactions}</td>
                            <td class="text-end uwc-amount">${tk(billSum)}</td>
                            <td>100%</td>
                            <td class="text-end uwc-amount uwc-total">${tk(grand)}</td>
                        </tr>`;
                })
                .catch(() => {
                    $body.innerHTML = emptyRow('Failed to load data', 'mdi:alert-circle-outline');
                });
        }

        $preset.addEventListener('change', () => {
            applyPreset($preset.value);
            if ($preset.value !== 'custom') load();
        });
        [$from, $to].forEach(el => el.addEventListener('change', () => { $preset.value = 'custom'; }));
        document.getElementById('uwcFilterBtn').addEventListener('click', load);

        load();
    })();
</script>

<!-- ==================== Disconnect Day Calendar (Due Customers) ==================== -->
<?php
$todayDate = date('Y-m-d');
$startDate = date('Y-m-d', strtotime('-5 days'));
$endDate   = date('Y-m-d', strtotime('+24 days'));

$rawBillData = $obj->rawSql(
    "SELECT 
        CAST(vw_agent.mikrotik_disconnect AS UNSIGNED) AS day_num,
        COUNT(*) AS total
     FROM vw_agent
     LEFT JOIN customer_billing ON customer_billing.agid = vw_agent.ag_id
     WHERE vw_agent.deleted_at IS NULL
       AND vw_agent.ag_status = 1
       AND customer_billing.dueadvance > 0
       AND vw_agent.mikrotik_disconnect IS NOT NULL
       AND vw_agent.mikrotik_disconnect != ''
       AND CAST(vw_agent.mikrotik_disconnect AS UNSIGNED) BETWEEN 1 AND 31
     GROUP BY CAST(vw_agent.mikrotik_disconnect AS UNSIGNED)
     ORDER BY day_num ASC"
);

$billingData = [];
foreach ($rawBillData as $single) {
    $billingData[(int)$single['day_num']] = (int)$single['total'];
}

$startMonth = date('F Y', strtotime($startDate));
$endMonth   = date('F Y', strtotime($endDate));
$monthTitle = ($startMonth == $endMonth) ? $startMonth : $startMonth . " - " . $endMonth;

$totalDueInRange = array_sum($billingData);
?>

<style>
  /* ---- LIGHT MODE (default) VARIABLES ---- */
  .rolling-card {
    --cal-bg: linear-gradient(160deg, #ffffff 0%, #f6f9ff 55%, #f0f7ff 100%);
    --cal-border: #e2e8f5;
    --cal-shadow: rgba(15, 23, 42, 0.10);
    --cal-title: #0f172a;
    --cal-subtitle: #2563eb;
    --cal-range-text: #64748b;
    --cal-weekday-text: #94a3b8;
    --cell-bg: #ffffff;
    --cell-border: #e8eef7;
    --cell-hover-bg: #eef6ff;
    --cell-hover-border: #bfdbfe;
    --day-number-color: #1e293b;
    --badge-due-bg: linear-gradient(135deg, #fee2e2, #fecaca);
    --badge-due-text: #b91c1c;
    --badge-due-border: #fca5a5;
    --badge-empty-bg: #f1f5f9;
    --badge-empty-text: #94a3b8;
    --badge-empty-border: #e2e8f0;
    --high-risk-color: #b91c1c;

    border-radius: 20px;
    box-shadow: 0 20px 45px var(--cal-shadow), 0 2px 6px rgba(15,23,42,0.06);
    border: 1px solid var(--cal-border);
    background: var(--cal-bg);
    overflow: hidden;
    position: relative;
    transition: background 0.3s ease, border-color 0.3s ease;
  }

  /* ---- DARK MODE OVERRIDES ---- */
  [data-theme="dark"] .rolling-card,
  html.dark .rolling-card,
  body.dark .rolling-card {
    --cal-bg: linear-gradient(160deg, #1a2032 0%, #161c2c 55%, #131826 100%);
    --cal-border: #2a3348;
    --cal-shadow: rgba(0, 0, 0, 0.35);
    --cal-title: #f1f5f9;
    --cal-subtitle: #60a5fa;
    --cal-range-text: #94a3b8;
    --cal-weekday-text: #64748b;
    --cell-bg: #1e2536;
    --cell-border: #2c3550;
    --cell-hover-bg: #263050;
    --cell-hover-border: #3b4c78;
    --day-number-color: #e2e8f0;
    --badge-due-bg: linear-gradient(135deg, #4c1d1d, #5c1f1f);
    --badge-due-text: #fca5a5;
    --badge-due-border: #7f2b2b;
    --badge-empty-bg: #262e42;
    --badge-empty-text: #64748b;
    --badge-empty-border: #333d57;
    --high-risk-color: #f87171;
  }

  .rolling-card::before {
    content: "";
    position: absolute;
    top: -60px;
    right: -60px;
    width: 180px;
    height: 180px;
    background: radial-gradient(circle, rgba(37,99,235,0.10), transparent 70%);
    border-radius: 50%;
  }

  /* ---- Header ---- */
  .calendar-topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 18px;
    position: relative;
    z-index: 1;
  }
  .calendar-header h6 {
    font-weight: 800;
    font-size: 17px;
    color: var(--cal-title);
    margin-bottom: 2px;
    letter-spacing: 0.2px;
  }
  .calendar-header .month-title {
    font-size: 13px;
    font-weight: 700;
    color: var(--cal-subtitle);
  }
  .range-text {
    font-size: 12px;
    color: var(--cal-range-text);
    font-weight: 600;
    margin-top: 2px;
  }
  .total-due-pill {
    background: linear-gradient(135deg, #ef4444, #b91c1c);
    color: #fff;
    padding: 8px 16px;
    border-radius: 30px;
    font-size: 13px;
    font-weight: 700;
    box-shadow: 0 8px 18px rgba(185,28,28,0.30);
    white-space: nowrap;
  }

  /* ---- Weekday row ---- */
  .weekday-row {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    margin-bottom: 8px;
    position: relative;
    z-index: 1;
  }
  .weekday-row div {
    text-align: center;
    font-size: 11px;
    font-weight: 700;
    color: var(--cal-weekday-text);
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  /* ---- Calendar grid ---- */
  .rolling-table {
    table-layout: fixed;
    width: 100%;
    border-collapse: separate;
    border-spacing: 6px;
    position: relative;
    z-index: 1;
  }
  .rolling-table td {
    width: 14.28%;
    height: 92px;
    text-align: center;
    vertical-align: middle;
    border: 1px solid var(--cell-border);
    background: var(--cell-bg);
    border-radius: 14px;
    transition: all 0.25s ease;
    padding: 0;
  }
  .rolling-table td:hover {
    background: var(--cell-hover-bg);
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(37,99,235,0.15);
    border-color: var(--cell-hover-border);
  }
  .rolling-table a {
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: inherit;
    padding: 8px 4px;
  }

  .day-number {
    font-weight: 800;
    font-size: 18px;
    color: var(--day-number-color);
    line-height: 1;
  }

  .badge-count {
    font-size: 11px;
    font-weight: 700;
    padding: 4px 9px;
    border-radius: 20px;
    margin-top: 8px;
    display: inline-block;
    background: var(--badge-due-bg);
    color: var(--badge-due-text);
    border: 1px solid var(--badge-due-border);
  }
  .badge-count.empty {
    background: var(--badge-empty-bg);
    color: var(--badge-empty-text);
    border: 1px solid var(--badge-empty-border);
    font-weight: 600;
  }

  .high-risk .day-number {
    color: var(--high-risk-color);
  }

  /* ---- Today highlight (same in both modes, self-contained) ---- */
  .today-box {
    background: linear-gradient(135deg, #0891b2, #2563eb) !important;
    border-color: transparent !important;
    box-shadow: 0 10px 24px rgba(37,99,235,0.35);
    animation: pulseToday 2.4s ease-in-out infinite;
  }
  .today-box .day-number,
  .today-box .badge-count {
    color: #ffffff !important;
  }
  .today-box .badge-count {
    background: rgba(255,255,255,0.20) !important;
    border: 1px solid rgba(255,255,255,0.35) !important;
  }
  .today-box .badge-count.empty {
    background: rgba(255,255,255,0.15) !important;
    color: #e0f2fe !important;
  }
  @keyframes pulseToday {
    0%, 100% { box-shadow: 0 10px 24px rgba(37,99,235,0.35); }
    50% { box-shadow: 0 10px 30px rgba(37,99,235,0.55); }
  }

  @media (max-width: 576px) {
    .rolling-table td { height: 72px; }
    .day-number { font-size: 15px; }
    .badge-count { font-size: 9.5px; padding: 3px 6px; }
    .weekday-row div { font-size: 9px; }
  }
</style>

<div class="card p-4 rolling-card">

  <div class="calendar-topbar">
    <div class="calendar-header">
      <h6 class="mb-1">Customers with Upcoming Disconnects (Due)</h6>
      <div class="month-title" id="dcMonth">...</div>
      <div class="range-text" id="dcRange"></div>
    </div>
    <div class="total-due-pill" id="dcTotal">0 Due Customers (30 Days)</div>
  </div>

  <div class="weekday-row">
    <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
  </div>

  <table class="table rolling-table">
    <tbody id="dcBody">
      <tr><td colspan="7" style="height:80px;">Loading...</td></tr>
    </tbody>
  </table>
</div>


<!-- bill collection comparison chart start -->
<div class="col-12">
    <div class="graphicalChart">
        <div class="card h-100 p-0">
            <div class="card-header border-bottom bg-base py-16 px-24" style="display: flex; justify-content: space-between;">
                <h6 class="card-title text-lg fw-semibold mb-0">Bill Collection Comparison <span id="comparisonYear"><?php echo date("Y") . " vs " . (intval(date("Y")) - 1); ?></span></h6>
                <div class="">
                    <select id="twoYearsBillCollectionChart" class="form-select form-select-sm w-auto bg-base border text-secondary-light">
                        <?php for ($i = date("Y"); $i >= 2020; $i--): ?>
                            <option value="<?= $i; ?>"><?= $i . " - " . ($i - 1); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div class="card-body p-24">
                <div id="bil_collection_view_line_chart"></div>
            </div>
        </div>
    </div>
</div>
<!-- bill collection comparison chart end -->



<div class="row" style="margin-top: 50px;">
    <!-- Dashboard Widget End -->
    

    <!-- Revenue Statistics End -->

    <!-- Statistics Start -->
    <div class="col-xxl-4">
        <div class="card h-100 radius-8 border-0">
            <div class="card-body p-24 row">
                <h6 class="mb-2 fw-bold text-lg text-neutral-600 col-8">
                    Revenue Statistics Rate – <span id="dateYearRevenueStatistics" class="text-success"><?= date("F") . " " . date("Y") ?></span>
                </h6>
                <div class="col-4">
                    <div class="" style="margin-left: 50px;">
                        <?php $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']; ?>
                        <select id="revenueExpenseYear" class=" form-select form-select-sm w-auto bg-base border text-secondary-light">
                            <?php for ($i = date("Y"); $i >= 2020; $i--): ?>
                                <option <?php echo $i == date("Y") ? "selected" : "" ?> value="<?= $i; ?>"><?= $i; ?></option>
                            <?php endfor; ?>
                        </select>
                        <select id="revenueExpenseMonth" style="margin-top: 10px;" class="form-select form-select-sm w-auto bg-base border text-secondary-light">
                            <?php for ($i = 0; $i < 12; $i++): ?>
                                <option <?php echo ($i + 1) == date("n") ? "selected" : "" ?> value="<?= $i + 1 ?>"><?= $months[$i]; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="mt-24">
                    <div id="revenue_statistics_chart" width="500" height="500">

                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <div class="card h-100 col-xxl-8">
        <div class="card-body p-24 mb-8">
            <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                <h6 class="mb-2 fw-bold text-lg text-neutral-600 mb-0">Revenue Comparison: <span id="revenueDisplayYear"><?php echo date('Y') . " vs " . (date('Y') - 1); ?></span></h6>

                <div class="">
                    <select id="yearRangeForRevenueChart" class="form-select form-select-sm w-auto bg-base border text-secondary-light">
                        <?php for ($i = date("Y"); $i >= 2020; $i--): ?>
                            <option value="<?= $i; ?>"><?= $i . " - " . ($i - 1); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            <ul class="d-flex flex-wrap align-items-center justify-content-center my-3 gap-24">
                <li class="d-flex flex-column gap-1">
                    <div class="d-flex align-items-center gap-2">
                        <span class="w-8-px h-8-px rounded-pill bg-primary-600"></span>
                        <span class="text-secondary-light text-sm fw-semibold">Previous Year Revenue </span>
                    </div>
                    <div class="d-flex align-items-center gap-8">
                        <h6 class="mb-0" id="previous-revenue">0 BDT</h6>

                    </div>
                </li>
                <li class="d-flex flex-column gap-1">
                    <div class="d-flex align-items-center gap-2">
                        <span class="w-8-px h-8-px rounded-pill bg-warning-600"></span>
                        <span class="text-secondary-light text-sm fw-semibold">Current Year Revenue </span>
                    </div>
                    <div class="d-flex align-items-center gap-8">
                        <h6 class="mb-0" id="current-revenue">0 BDT</h6>
                    </div>
                </li>
            </ul>
            <div id="revenueChart" class="apexcharts-tooltip-style-1"></div>
        </div>
    </div>
</div>


<!-- <div class="col-md-8">
    <div class="card h-100 p-0">
        <div class="card-header border-bottom bg-base py-16 px-24">
            <h6 class="text-lg fw-semibold mb-0">Column Charts</h6>
        </div>
        <div class="card-body">
            <div id="dashboard_view_combination_chart"></div>
        </div>
    </div>
</div> -->


<div class="col-xxl-12">
    
</div>
<!-- Statistics End -->
<div class="col-xxl-12">
    <div class="card h-100">
        <div class="card-body p-24">
            <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-20">
                <h6 class="mb-2 fw-bold text-lg text-neutral-600 mb-0">Latest Transactions</h6>
            </div>
            <div class="table-responsive scroll-sm">
                <table class="table table-striped bordered-table mb-0" id="transection-table">
                    <thead>
                        <tr>
                            <th scope="col">SL.</th>
                            <th scope="col">Name</th>
                            <th scope="col">Phone </th>
                            <th scope="col">Amount</th>
                            <th scope="col" class="text-center">Type</th>
                            <th scope="col">Date</th>
                            <th scope="col">Created By</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="col-xxl-7">
    <div class="card h-100">
        <div class="card-body p-24">
            <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-20">
                <h6 class="mb-2 fw-bold text-lg text-neutral-600 mb-0">Latest Complains</h6>
            </div>
            <div class="table-responsive scroll-sm">
                <table class="table table-striped bordered-table mb-0" id="data-table">
                    <thead>
                        <tr>
                            <th scope="col">SL.</th>
                            <th scope="col">Name</th>
                            <th scope="col">Phone </th>
                            <th scope="col">Complain</th>
                            <th scope="col">Date</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="col-xxl-5">
    <div class="card h-100">
        <div class="card-body p-24">
            <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-20">
                <h6 class="mb-2 fw-bold text-lg text-neutral-600 mb-0">Stock Report</h6>
                <a href="?page=stock" class="text-primary-600 hover-text-primary d-flex align-items-center gap-1">
                    View All
                    <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                </a>
            </div>
            <div class="table-responsive scroll-sm">
                <table class="table table-striped bordered-table mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Items</th>
                            <th scope="col">Batch No</th>
                            <th scope="col">
                                <div class="max-w-112 mx-auto">
                                    <span>Stock</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stock as $item): ?>
                            <?php
                            $qty = $item['current_stock'];
                            $percentage = ($qty / 50) * 100;
                            ?>
                            <tr>
                                <td><?= $item['product_name'] ?></td>
                                <td><?= $item['batch_id'] ?></td>
                                <td>
                                    <div class="max-w-112 mx-auto">
                                        <div class="w-100">
                                            <div class="progress progress-sm rounded-pill" role="progressbar" aria-label="Success example" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                                <div
                                                    <?php if ($percentage < 20): ?>
                                                    class="progress-bar bg-danger-main rounded-pill"
                                                    <?php elseif ($percentage < 50 &&  $percentage > 20): ?>
                                                    class="progress-bar bg-warning-main rounded-pill"
                                                    <?php elseif ($percentage == 0): ?>
                                                    class="progress-bar bg-secondary-light rounded-pill"
                                                    <?php else: ?>
                                                    class="progress-bar bg-success-main rounded-pill"
                                                    <?php endif; ?>

                                                    style="width: <?= $percentage ?>%;"></div>
                                            </div>
                                            <?php if ($percentage < 20): ?>
                                                <span class="mt-12 text-secondary-main text-sm fw-medium">Low Stock</span>
                                            <?php elseif ($percentage < 50): ?>
                                                <span class="mt-12 text-secondary-main text-sm fw-medium">Medium Stock</span>
                                            <?php elseif ($percentage == 0): ?>
                                                <span class="mt-12 text-secondary-light text-sm fw-medium">Out of Stock</span>
                                            <?php else: ?>
                                                <span class="mt-12 text-secondary-main text-sm fw-medium">High Stock</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="centermodal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fs-4" id="myCenterModalLabel">Change Status</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="mb-3 col-md-12">
                        <input type="hidden" id="modalComplainId">
                        <label for="ComplainStatus" class="form-label">Status</label>
                        <select id="ComplainStatus" class="form-control">
                            <option value="">Choose</option>
                            <option value="1">Pending</option>
                            <option value="2">Processing</option>
                            <option value="3">Solved</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="button" id="changedStatus" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

<?php $obj->start_script(); ?>
<script>
    // let chart;
    let incomeExpenseChart = null;

    function IncomeExpenseChartLoad(selectedYear, changeYear = false) {
        fetch(`./pages/dashbaord/up-down_chart_data_ajax.php?selectedYear=${selectedYear}`)
            .then(response => response.json())
            .then(data => {
                // // Update Total Income and Expense
                // document.getElementById('dynamicIncome').textContent = `$${data.totalIncome.toLocaleString()}`;
                // document.getElementById('dynamicExpenses').textContent = `$${data.totalExpense.toLocaleString()}`;
                const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

                const incomedata = data.incomeData.split(',').map(Number);
                const expensedata = data.expenseData.split(',').map(Number);
                const sumIncome = incomedata.reduce((accumulator, currentValue) => accumulator + currentValue, 0)
                const sumExpense = expensedata.reduce((accumulator, currentValue) => accumulator + currentValue, 0)

                document.getElementById('dynamicIncome').textContent = `${sumIncome.toLocaleString()} BDT`;
                document.getElementById('dynamicExpenses').textContent = `${sumExpense.toLocaleString()} BDT`;
                let expenseNegative = expensedata.map(value => -Math.abs(value));

                // Generate Up-Down Bar Chart
                var options = {
                    series: [{
                            name: "Income",
                            data: incomedata,
                        },
                        {
                            name: "Expenses",
                            data: expenseNegative,
                        },
                    ],
                    chart: {
                        stacked: true,
                        type: "bar",
                        height: 263,
                        fontFamily: "Poppins, sans-serif",
                        toolbar: {
                            show: false,
                        },
                    },
                    colors: ["#487FFF", "#EF4A00"],
                    plotOptions: {
                        bar: {
                            columnWidth: "8",
                            borderRadius: [2],
                            borderRadiusWhenStacked: "all",
                        },
                    },
                    stroke: {
                        width: [5, 5]
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    legend: {
                        show: true,
                        position: "top",
                    },
                    yaxis: {
                        show: false,
                        title: {
                            text: undefined,
                        },
                        labels: {
                            formatter: function(y) {
                                return y.toFixed(0) + "";
                            },
                        },
                    },
                    xaxis: {
                        show: false,
                        categories: months,
                        axisBorder: {
                            show: false,
                        },
                        axisTicks: {
                            show: false,
                        },
                        labels: {
                            show: true,
                            style: {
                                colors: "#d4d7d9",
                                fontSize: "10px",
                                fontWeight: 500,
                            },
                        },
                    },
                    tooltip: {
                        enabled: true,
                        shared: true,
                        intersect: false,
                        theme: "dark",
                        x: {
                            show: false,
                        },
                    },
                };

                if (changeYear && incomeExpenseChart) {
                    incomeExpenseChart.updateOptions(options);
                } else {
                    // Destroy old chart if exists
                    if (incomeExpenseChart) {
                        incomeExpenseChart.destroy();
                    }
                    incomeExpenseChart = new ApexCharts(document.querySelector("#income_view_column"), options);
                    incomeExpenseChart.render();
                }
            })
            .catch(error => console.error("Error fetching chart data:", error));
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Fetch data from the server
        IncomeExpenseChartLoad(new Date().getFullYear());

        $("#yearRangeForIncomeExpenseChart").on("change", function() {
            const selectedYear = $(this).val();
            IncomeExpenseChartLoad(selectedYear, true);

            $("#revenueYear").text(selectedYear);
        })
    });
</script>
<style>
    .apexcharts-track path {
        stroke: #FF8042 !important;
    }
</style>


<!-- bill collecton monthly chart start -->
<script>
    $(document).ready(function() {
        function monthlyConnectionChargeChartLoad(selectedYear) {
            $.ajax({
                type: "GET",
                url: `./pages/dashbaord/monthly_bill_collection_chart_ajax.php?selectedYear=${selectedYear}`,
                dataType: "json",
                success: function(response) {
                    console.log('sadi', response);

                    $('#loading-spinner').hide();
                    // Call the function to render the line chart with the data from the server
                    monthlyConnectionChargeChart(response.currentData, response.currentYear, response.maxData);
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching data:", error);
                }
            });
        }

        monthlyConnectionChargeChartLoad(new Date().getFullYear());

        $("#monthlyOthersIncomeSelect").on("change", function() {
            monthlyConnectionChargeChartLoad($(this).val());
            $("#monthlyOthersIncomeText").text(`${$(this).val()}`);
        })
    });

    let monthlyConnectionChargeInstance = null

    function monthlyConnectionChargeChart(currentData, currentYear, maxData) {
        const data = currentData.split(',').map(Number);
        console.log('sadi-data', data);

        var options = {
            series: [{
                name: 'Free Cash Flow',
                data
            }],
            colors: ['#9195F6'],
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '20%',
                    borderRadius: 5,
                    borderRadiusApplication: 'end'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', "Nov", "Dec"],
            },
            yaxis: {
                title: {
                    text: '$ (thousands)'
                },
                min: 0,
                max: maxData,
                tickAmount: 5,
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return "$ " + val + " thousands"
                    }
                }
            }
        };

        if (monthlyConnectionChargeInstance) {
            monthlyConnectionChargeInstance.updateOptions(options);
        } else {
            if (monthlyConnectionChargeInstance) {
                monthlyConnectionChargeInstance.destroy();
            }

            monthlyConnectionChargeInstance = new ApexCharts(document.querySelector("#monthly_others_income_chart"), options);
            monthlyConnectionChargeInstance.render();
        }
    }
    // monthly connection charge chart end
</script>
<!-- bill collecton monthly chart end -->


<script>
    let bill_collection_comparison_chart = null;

    function bill_collection_view_line_chart(previousData, previousYear, currentData, currentYear, maxData) {
        // Define colors
        let colors = ["#6658dd", "#1abc9c"];
        const dataColors = $("#apex-line-test").data("colors");
        if (dataColors) {
            colors = dataColors.split(",");
        }

        // Static month names for both years
        const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        // Convert the comma-separated strings to arrays of numbers
        const previousDataArray = previousData.split(',').map(Number);
        const currentDataArray = currentData.split(',').map(Number);


        // Calculate the tick amount for y-axis dynamically
        const tickAmount = Math.ceil(maxData / 1000); // Adjust this based on your maxData

        // Chart options
        const options = {
            chart: {
                height: 380,
                type: "line",
                zoom: {
                    enabled: false
                },
                toolbar: {
                    show: false
                }
            },
            colors: colors,
            dataLabels: {
                enabled: true
            },
            stroke: {
                width: [3, 3],
                curve: "smooth"
            },
            series: [{
                name: `Previous - ${previousYear}`,
                data: previousDataArray // Use the actual array of numbers
            }, {
                name: `Current - ${currentYear}`,
                data: currentDataArray // Use the actual array of numbers
            }],
            grid: {
                row: {
                    colors: ["transparent", "transparent"],
                    opacity: 0.2
                },
                borderColor: "#f1f3fa"
            },
            markers: {
                style: "inverted",
                size: 6
            },
            xaxis: {
                categories: months, // Use static month names
                title: {
                    text: "Month"
                }
            },
            yaxis: {
                title: {
                    text: "Bill Count"
                },
                min: 0, // Start y-axis from 0 for better readability
                max: maxData, // Use maxData to dynamically set the maximum value of the y-axis
                tickAmount: 10 // Calculate dynamic ticks based on maxData
            },
            legend: {
                position: "top",
                horizontalAlign: "right",
                floating: true,
                offsetY: -25,
                offsetX: -25
            },
            responsive: [{
                breakpoint: 600,
                options: {
                    chart: {
                        toolbar: {
                            show: false
                        }
                    },
                    legend: {
                        show: false
                    }
                }
            }]
        };

        // Render the chart


        if (bill_collection_comparison_chart) {
            bill_collection_comparison_chart.updateOptions(options);
        } else {
            if (bill_collection_comparison_chart) {
                bill_collection_comparison_chart.destroy();
            }

            bill_collection_comparison_chart = new ApexCharts(document.querySelector("#bil_collection_view_line_chart"), options);
            bill_collection_comparison_chart.render();
        }
    }


    // two years bill collection chart start
    function twoYearsBillCollectionChartLoad(selectedYear) {
        $.ajax({
            type: "GET",
            url: `./pages/dashbaord/bill_collection_charts_ajax.php?selectedYear=${selectedYear}`,
            dataType: "json",
            success: function(response) {
                $('#loading-spinner').hide();
                // Call the function to render the line chart with the data from the server
                bill_collection_view_line_chart(response?.previousData, response?.previousYear, response?.currentData, response?.currentYear, response?.maxData);
            },
            error: function(xhr, status, error) {
                console.error("Error fetching data:", error);
            }
        });
    }

    $(document).ready(function() {
        twoYearsBillCollectionChartLoad(new Date().getFullYear());
        $("#twoYearsBillCollectionChart").on("change", function() {
            const selectedYear = $(this).val();
            twoYearsBillCollectionChartLoad(selectedYear);
            $("#comparisonYear").text(selectedYear + " vs " + (selectedYear - 1));
        })
    })
    // two years bill collection chart end
</script>


<script>
    $(document).ready(function() {
        // Make sure the AJAX call fetches data successfully
        $.ajax({
            url: './pages/dashbaord/dashboard_view_redial_chart_ajax.php', // Your PHP script URL
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                // Validate the response and calculate the percentage

                var totalBill = response.totalBill || 100;


                var totalBillCollection = response.totalBill > 0 ? response.totalBillCollection : 0;


                var percentage = Math.round((totalBillCollection / totalBill) * 100);

                if (percentage > 100) {
                    percentage = 100;
                }

                var barTextColor = '#45B369'; // Bar color
                // Render the chart
                var options = {
                    series: [percentage], // Dynamically set the percentage
                    chart: {
                        height: 350,
                        type: 'radialBar',
                    },
                    plotOptions: {
                        radialBar: {
                            hollow: {
                                size: '50%', // Set hollow area
                            },
                        },
                    },
                    colors: [barTextColor], // Radial bar color
                    labels: ['Total Collection'], // Label
                };

                var chart = new ApexCharts(document.querySelector("#dashboad_view_redial_chart"), options);
                chart.render();

            },
            error: function(xhr, status, error) {
                console.error("Error fetching data:", error);
            }
        });


        // monthly revenue and expense statistics start
        let expenseRevenueMonthlyChart = null;

        function monthlyRevenueExpenseChartLoad(year, month) {
            $.ajax({
                url: `./pages/dashbaord/monthly_revenue_expense_redial_chart_ajax.php?year=${year}&month=${month}`, // Your PHP script URL
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    let toalExpense = parseInt(response?.totalExpense);
                    let toalRevenue = parseInt(response?.totalRevenue);
                    var options = {
                        // series: [(toalExpense == 0 && toalRevenue == 0) ? 1 : toalRevenue, toalExpense],
                        series: [(toalExpense == 0 && toalRevenue == 0) ? 1 : toalRevenue, toalExpense],
                        chart: {
                            width: 380,
                            type: 'pie',
                        },
                        labels: ['Income', 'Expense'],
                        colors: ['#28a745', '#dc3545'],
                        responsive: [{
                            breakpoint: 480,
                            options: {
                                chart: {
                                    width: 200
                                },
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }]
                    };


                    if (expenseRevenueMonthlyChart) {
                        expenseRevenueMonthlyChart.updateOptions(options);
                    } else {
                        if (expenseRevenueMonthlyChart) {
                            expenseRevenueMonthlyChart.destroy();
                        }

                        expenseRevenueMonthlyChart = new ApexCharts(document.querySelector("#revenue_statistics_chart"), options);
                        expenseRevenueMonthlyChart.render();
                    }

                },
                error: function(xhr, status, error) {
                    console.error("Error fetching data:", error);
                }
            });
        }

        monthlyRevenueExpenseChartLoad(new Date().getFullYear(), new Date().getMonth() + 1)

        $("#revenueExpenseYear, #revenueExpenseMonth").on("change", function() {
            monthlyRevenueExpenseChartLoad($("#revenueExpenseYear").val(), $("#revenueExpenseMonth").val());
            let months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

            $("#dateYearRevenueStatistics").text(`${months[$("#revenueExpenseMonth").val() - 1]} ${$("#revenueExpenseYear").val()}`)
        })
        // monthly revenue and expense statistics end



        // $.ajax({
        //     url: './pages/dashbaord/dashboard_view_combination_chart_ajax.php', // Your PHP script URL
        //     method: 'GET',
        //     dataType: 'json',
        //     success: function(response) {


        //         const countIds = [];
        //         const totalTakas = [];
        //         // Static month names for both years
        //         const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        //         // Iterate over totalData to separate countId and total_taka
        //         response.totalData.forEach(data => {
        //             if (typeof data === 'object') {
        //                 // If data is an object, push the respective values into the arrays
        //                 countIds.push(data.countId);
        //                 totalTakas.push(data.total_taka);
        //             } else {
        //                 // If the data is 0, push 0 for consistency
        //                 countIds.push(0);
        //                 totalTakas.push(0);
        //             }
        //         });
        //         let maxData = Math.max(...totalTakas.map(value => Number(value)));
        //         let maxIds = Math.max(...countIds.map(value => Number(value)));
        //         maxData = maxData + (maxData * 0.50);
        //         maxIds = maxIds + (maxIds * 0.50);


        //         var options = {
        //             series: [{
        //                     name: "Revenue",
        //                     type: "column", // Bar chart for revenue
        //                     data: totalTakas, // Revenue data
        //                 },
        //                 {
        //                     name: "Sales",
        //                     type: "line", // Line chart for sales
        //                     data: countIds, // Sales data
        //                 },
        //             ],
        //             chart: {
        //                 height: 350,
        //                 type: "line",
        //                 stacked: false,
        //             },
        //             stroke: {
        //                 width: [0, 4], // No stroke for bars, 4px stroke for the line
        //                 curve: "smooth", // Smooth line for sales data
        //             },
        //             plotOptions: {
        //                 bar: {
        //                     columnWidth: "50%", // Width of the bars
        //                 },
        //             },
        //             dataLabels: {
        //                 enabled: true,
        //                 style: {
        //                     fontSize: "12px",
        //                     colors: ["#000000"],
        //                 },
        //                 offsetY: -5, // Position data labels above the bars
        //             },
        //             labels: months,
        //             xaxis: {
        //                 categories: months
        //             },
        //             yaxis: [{
        //                     title: {
        //                         text: "Net Revenue",
        //                     },
        //                     min: 0,
        //                     max: maxData,
        //                 },
        //                 {
        //                     opposite: true,
        //                     title: {
        //                         text: "Number of Sales",
        //                     },
        //                     min: 0,
        //                     max: maxIds,
        //                 },
        //             ],
        //             colors: ["#00E396", "#007BFF"], // Green for revenue (bars) and blue for sales (line)
        //             tooltip: {
        //                 shared: true,
        //                 intersect: false,
        //                 y: {
        //                     formatter: function(value) {
        //                         return value.toFixed(0);
        //                     },
        //                 },
        //             },
        //         };

        //         var chart = new ApexCharts(document.querySelector("#dashboard_view_combination_chart"), options);
        //         chart.render();
        //     }
        // });

        // two years revenue comparison chart start
        let twoYearsRevenueChart = null;

        function TwoYearsRevenueComparisonChartLoad(selectedYear = 2024) {
            $.ajax({
                url: `./pages/dashbaord/dashboard_view_revenue_chart_ajax.php?selectedYear=${selectedYear}`,
                method: 'GET',
                dataType: 'json',
                success: function(res) {

                    let colors = ["#6658dd", "#1abc9c"];
                    const dataColors = $("#apex-line-test").data("colors");
                    if (dataColors) {
                        colors = dataColors.split(",");
                    }


                    // Static month names for both years
                    const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

                    const previousDataArray = res.previousData.split(',').map(Number);
                    const currentDataArray = res.currentData.split(',').map(Number);

                    const sumPreviousIncome = previousDataArray.reduce((accumulator, currentValue) => accumulator + currentValue, 0)
                    const sumCurrentIncome = currentDataArray.reduce((accumulator, currentValue) => accumulator + currentValue, 0)

                    document.getElementById('previous-revenue').textContent = `${sumPreviousIncome.toLocaleString()} BDT`;
                    document.getElementById('current-revenue').textContent = `${sumCurrentIncome.toLocaleString()} BDT`;
                    const tickAmount = Math.ceil(res.maxData / 1000);
                    const max = res.maxData;
                    var options = {
                        series: [{
                            name: `Previous - ${res.previousYear}`,
                            data: previousDataArray
                        }, {
                            name: `Current - ${res.currentYear}`,
                            data: currentDataArray
                        }],
                        legend: {
                            show: false
                        },
                        chart: {
                            type: 'area',
                            width: '100%',
                            height: 270,
                            toolbar: {
                                show: false
                            },
                            padding: {
                                left: 0,
                                right: 0,
                                top: 0,
                                bottom: 0
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 3,
                            colors: ["#487FFF", "#FF9F29"], // Use two colors for the lines
                            lineCap: 'round'
                        },
                        grid: {
                            show: true,
                            borderColor: '#D1D5DB',
                            strokeDashArray: 1,
                            position: 'back',
                            xaxis: {
                                lines: {
                                    show: false
                                }
                            },
                            yaxis: {
                                lines: {
                                    show: true
                                }
                            },
                            row: {
                                colors: undefined,
                                opacity: 0.5
                            },
                            column: {
                                colors: undefined,
                                opacity: 0.5
                            },
                            padding: {
                                top: -20,
                                right: 0,
                                bottom: -10,
                                left: 0
                            },
                        },
                        fill: {
                            type: 'gradient',
                            colors: ["487FFF", "#FF9F29"], // Use two colors for the gradient
                            gradient: {
                                shade: 'light',
                                type: 'vertical',
                                shadeIntensity: 0.5,
                                gradientToColors: [undefined, `#FF9F2900`], // Apply transparency to both colors
                                inverseColors: false,
                                opacityFrom: [0.4, 0.6], // Starting opacity for both colors
                                opacityTo: [0.3, 0.3], // Ending opacity for both colors
                                stops: [0, 100],
                            },
                        },
                        markers: {
                            colors: ["487FFF", "#FF9F29"], // Use two colors for the markers
                            strokeWidth: 3,
                            size: 0,
                            hover: {
                                size: 10
                            }
                        },
                        xaxis: {
                            labels: {
                                show: false
                            },
                            categories: months,
                            tooltip: {
                                enabled: false
                            },
                            labels: {
                                formatter: function(value) {
                                    return value;
                                },
                                style: {
                                    fontSize: "14px"
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                formatter: function(value) {
                                    return (value / 1000).toFixed(0) + "k";
                                },
                                style: {
                                    fontSize: "14px"
                                }
                            },
                        },
                        tooltip: {
                            x: {
                                format: 'dd/MM/yy HH:mm'
                            }
                        }
                    };



                    if (twoYearsRevenueChart) {
                        twoYearsRevenueChart.updateOptions(options);
                    } else {
                        // Destroy old chart if exists
                        if (twoYearsRevenueChart) {
                            twoYearsRevenueChart.destroy();
                        }
                        twoYearsRevenueChart = new ApexCharts(document.querySelector("#revenueChart"), options);
                        twoYearsRevenueChart.render();
                    }
                }
            });
        }

        TwoYearsRevenueComparisonChartLoad(new Date().getFullYear());

        $("#yearRangeForRevenueChart").on("change", function() {
            const selectedYear = $(this).val();
            TwoYearsRevenueComparisonChartLoad(selectedYear);
            $("#revenueDisplayYear").text(selectedYear + " vs " + (selectedYear - 1));
        })
        // two years revenue comparison chart end


        var transectionTable = $('#transection-table').DataTable({
            paging: false,
            searching: false,
            ordering: false,
            responsive: true,
            autoWidth: false,
            ajax: {
                url: './pages/dashbaord/get_transection_ajax.php', // Replace with your API endpoint
                dataSrc: 'data', // This points to the part of the response where your data is located
            },
            columns: [{
                    data: 'sl',
                    defaultContent: 'N/A'
                },
                {
                    data: 'customer_name',
                    defaultContent: 'N/A'
                },
                {
                    data: 'phone',
                    defaultContent: 'N/A',

                },
                {
                    data: 'amount',
                    defaultContent: 'N/A'
                },
                {
                    data: 'type',
                    className: 'text-center',

                    render: function(data, type, row) {
                        let statusClass = '';
                        let statusLabel = '';
                        if (data == 1) {
                            statusClass = 'bg-danger';
                            statusLabel = 'Expenses';
                        } else if (data == 2) {
                            statusClass = 'bg-primary';
                            statusLabel = 'Other Income';
                        } else if (data == 3) {
                            statusClass = 'bg-success';
                            statusLabel = 'Bill Collection';
                        } else if (data == 4) {
                            statusClass = 'bg-success';
                            statusLabel = 'Connection Charge';
                        } else if (data == 5) {
                            statusClass = 'bg-secondary';
                            statusLabel = 'Opening Income';
                        }
                        return `<span class="${statusClass} text-white px-24 py-4 rounded-pill fw-medium text-sm">${statusLabel}</span>`;
                    }
                },
                {
                    data: 'entry_date',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString('en-US', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        }).replace(',', '') : '';
                    }


                },
                {
                    data: 'entry_by',
                    defaultContent: 'N/A'
                },
                // {
                //     data: null,
                //     render: function(data) {
                //         return `<a href="#centermodal" data-bs-toggle="modal" data-id="${data.id}" class="btn btn-xs btn-light status-modal"><i class="mdi mdi-pencil"></i></a>`;
                //     }
                // }
            ]
        });
        var dataTable = $('#data-table').DataTable({
            paging: false,
            searching: false,
            ordering: false,
            responsive: true,
            autoWidth: false,
            ajax: {
                url: './pages/dashbaord/get_complain_ajax.php', // Replace with your API endpoint
                dataSrc: 'data', // This points to the part of the response where your data is located
            },
            columns: [{
                    data: 'sl',
                    defaultContent: 'N/A'
                },
                {
                    data: 'customer_name',
                    defaultContent: 'N/A'
                },
                {
                    data: 'phone',
                    defaultContent: 'N/A',

                },
                {
                    data: 'complain_date',
                    defaultContent: 'N/A',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString('en-US', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        }).replace(',', '') : '';
                    }
                },
                {
                    data: 'complain_name',
                    defaultContent: 'N/A'
                },
                {
                    data: 'status_name',
                    render: function(data, type, row) {
                        let statusClass = '';
                        let statusLabel = '';
                        switch (data) {
                            case '1':
                                statusClass = 'bg-danger';
                                statusLabel = 'Pending';
                                break;
                            case '2':
                                statusClass = 'bg-primary';
                                statusLabel = 'Processing';
                                break;
                            case '3':
                                statusClass = 'bg-success';
                                statusLabel = 'Resolved';
                                break;
                            default:
                                statusClass = 'bg-secondary';
                                statusLabel = 'Unknown';
                                break;
                        }
                        return `<span class="${statusClass} text-white px-24 py-4 rounded-pill fw-medium text-sm">${statusLabel}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data) {
                        return `<a href="#centermodal" data-bs-toggle="modal" data-id="${data.id}" class="btn btn-xs btn-light status-modal"><i class="mdi mdi-pencil"></i></a>`;
                    }
                }
            ]
        });

        // Handle status update when the button is clicked
        $('#changedStatus').on('click', function() {
            // Collect data
            const complainId = $('#modalComplainId').val();
            const statusId = $('#ComplainStatus').val();

            // Validate status selection
            if (!statusId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    text: 'Please select a status'
                });
                return;
            }

            // Prepare AJAX data
            const formData = {
                id: complainId,
                status: statusId
            };

            // AJAX request to update the status
            $.ajax({
                url: './pages/complain/update_status.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formData),
                dataType: 'json',
                success: function(response) {
                    // Hide the modal
                    $('#centermodal').modal('hide');
                    // Reload the DataTable to reflect the status update
                    dataTable.ajax.reload();
                },
                error: function(xhr) {
                    console.log(xhr.responseJSON ? xhr.responseJSON.message : 'An unexpected error occurred');
                }
            });
        });

        // Open the modal when a status is clicked
        $(document).on('click', '.status-modal', function() {
            // Get the ID from the clicked button
            const complainId = $(this).data('id');

            // Set the ID in a hidden input in the modal
            $('#modalComplainId').val(complainId);

            // Reset the status dropdown
            $('#ComplainStatus').val('');
        });
    });
</script>
<!--<script>-->
// mirkotik offline online
    
<!--    let isLoading = false;-->

<!--    async function loadMikrotikStatus() {-->

<!--    if (isLoading) return;-->
<!--    isLoading = true;-->

<!--    try {-->
<!--        const res = await fetch('/pages/dashbaord/mikrotik_status_ajax.php');-->
<!--        const data = await res.json();-->

<!--        let html = '';-->

<!--         data.forEach(mk => {-->
    
<!--                html += `-->
<!--                <div class="col-xxl-3 col-lg-4 col-md-6 mb-4">-->
<!--                    <div class="card border-0 shadow-lg h-100 mikrotik-card">-->
    
<!--                        <div class="card-header text-white"-->
<!--                             style="background: linear-gradient(135deg, #4e73df, #224abe);">-->
<!--                            <div class="d-flex justify-content-between align-items-center">-->
<!--                                <div>-->
<!--                                    <h6 class="mb-0">${mk.ip}</h6>-->
<!--                                    <small>${mk.username}</small>-->
<!--                                </div>-->
<!--                                <span class="badge bg-light text-dark">MikroTik</span>-->
<!--                            </div>-->
<!--                        </div>-->
    
<!--                        <div class="card-body">-->
    
<!--                            <div class="d-flex justify-content-between mb-2">-->
<!--                                <span class="text-success fw-semibold">-->
<!--                                    ● Online: ${mk.online}-->
<!--                                </span>-->
<!--                                <span class="text-warning fw-semibold">-->
<!--                                    ● Offline: ${mk.offline}-->
<!--                                </span>-->
<!--                            </div>-->
    
<!--                            <div class="d-flex justify-content-between mb-3">-->
<!--                                <span class="text-danger fw-semibold">-->
<!--                                    ● Disabled: ${mk.disabled}-->
<!--                                </span>-->
<!--                                <span class="text-muted small">-->
<!--                                    Total: ${mk.total}-->
<!--                                </span>-->
<!--                            </div>-->
    
<!--                            <div class="mb-2">-->
<!--                                <div class="progress" style="height: 6px;">-->
<!--                                    <div class="progress-bar bg-success"-->
<!--                                         style="width: ${mk.percent}%"></div>-->
<!--                                </div>-->
<!--                                <small class="text-muted">${mk.percent}% users online</small>-->
<!--                            </div>-->
    
<!--                        </div>-->
                            
<!--                        <div class="card-footer bg-transparent border-0 d-flex justify-content-between">-->
<!--                            <a href="?page=mikrotik_online_secret&mikrotik_id=${mk.id}"-->
<!--                               class="btn btn-sm btn-outline-success">-->
<!--                                Online List-->
<!--                            </a>-->
<!--                            <a href="?page=mikrotik_all_secret&mikrotik_id=${mk.id}&status=1"-->
<!--                               class="btn btn-sm btn-outline-danger">-->
<!--                                Disabled-->
<!--                            </a>-->
<!--                            <a href="?page=mikrotik_offline&mikrotik_id=${mk.id}"-->
<!--                               class="btn btn-sm btn-outline-danger">-->
<!--                                Offline-->
<!--                            </a>-->
<!--                        </div>-->
                        
                        
    
<!--                    </div>-->
<!--                </div>-->
<!--                `;-->
<!--            });-->

        // ✅ Loader hide + Data show
<!--        document.getElementById('mikrotikLoader').classList.add('d-none');-->
<!--        document.getElementById('mikrotikCards').classList.remove('d-none');-->
<!--        document.getElementById('mikrotikCards').innerHTML = html;-->

<!--    } catch (err) {-->
<!--        console.error(err);-->
<!--    } finally {-->
<!--        isLoading = false;-->
<!--    }-->
<!--}-->

<!--    async function autoRefresh() {-->
<!--        await loadMikrotikStatus();-->
<!--        setTimeout(autoRefresh, 10000); -->
<!--    }-->
    
<!--    document.addEventListener("DOMContentLoaded", () => {-->
<!--        autoRefresh();-->
<!--    });-->
<!--</script>-->


<script>
(function () {
    const $body  = document.getElementById('dcBody');
    const blank  = `<td style="background:transparent;border:none;box-shadow:none;"></td>`;

    function render(res) {
        document.getElementById('dcMonth').textContent = res.month_title;
        document.getElementById('dcRange').textContent = res.range_label;
        document.getElementById('dcTotal').textContent = `${res.total_due} Due Customers (30 Days)`;

        let cells = [];
        // prothom din er weekday onujayi blank cell
        for (let i = 0; i < res.days[0].weekday; i++) cells.push(blank);

        res.days.forEach(d => {
            const cls = [d.is_today ? 'today-box' : '', d.count >= 5 ? 'high-risk' : ''].join(' ').trim();
            const word = d.count > 1 ? 'Customers' : 'Customer';
            const badge = d.count > 0
                ? `<span class="badge-count">${d.count} ${word}</span>`
                : `<span class="badge-count empty">No Due</span>`;
            cells.push(`
                <td class="${cls}">
                    <a href="?page=billcollection_view&disconnect_day=${d.day}">
                        <span class="day-number">${d.day}</span>
                        ${badge}
                    </a>
                </td>`);
        });

        // 7 ta kore row-e bhag
        let html = '';
        for (let i = 0; i < cells.length; i += 7) {
            html += '<tr>' + cells.slice(i, i + 7).join('') + '</tr>';
        }
        $body.innerHTML = html;
    }

    function loadCalendar() {
        fetch('./pages/dashbaord/disconnect_calendar_ajax.php')
            .then(r => r.json())
            .then(render)
            .catch(() => {
                $body.innerHTML = `<tr><td colspan="7" style="height:80px;">Failed to load data</td></tr>`;
            });
    }

    loadCalendar();
})();
</script>
<?php $obj->end_script(); ?>