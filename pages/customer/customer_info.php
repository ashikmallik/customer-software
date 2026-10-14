<?php

include('customer.php');

if (isset($_POST['csvUpload'])) {
    $csvFile = $_FILES['csv-upload']['tmp_name'];
    if ($_FILES["csv-upload"]["size"] > 0) {
        $file = fopen($csvFile, "r");
        fgetcsv($file); // Skip the header row
        while (($row = fgetcsv($file)) !== false) {
            $data = $obj->rawSql("SELECT ag_id FROM tbl_agent ORDER BY ag_id DESC LIMIT 1");
            $ag_id = $data ? $data[0]["ag_id"] + 1 : 1;
            $STD = "CUS" . str_pad($ag_id, 5, '0', STR_PAD_LEFT);

            $entry_date = isset($row[12]) && strtotime($row[12]) ? date('Y-m-d', strtotime($row[12])) : date('Y-m-d');
            $connection_date = isset($row[13]) && strtotime($row[13]) ? date('Y-m-d', strtotime($row[13])) : null;
            // $last_zone_id =$obj->zoneCreate($row[16]);
            $fromInsert = [
                'cus_id' => $STD,
                'ag_name' => $row[0],
                'ip' => $row[1],
                'type' => 1,
                'mikrotik_disconnect' => $row[2],
                'taka' => $obj->convertBanglaToEnglishNumbers($row[3]),
                'mb' => $row[4],
                'int_mb' => intval($row[4]),
                'ag_status' => $row[5],
                'ag_mobile_no' => $obj->convertBanglaToEnglishNumbers($row[6]),
                'regular_mobile' => $obj->convertBanglaToEnglishNumbers($row[7]),
                'ag_office_address' => $row[8],
                'ag_email' => $row[9],
                'national_id' => $row[10],
                'gender' => $row[11],
                'billing_person_id' => $userId,
                'entry_by' => $userId,
                'entry_date' => $entry_date,
                'connection_date' => $connection_date,
                'zone' => $row[16],
            ];

            // if ($mikrotikConnect) {
            $fromInsert['mikrotik_id'] = $row[17];
            $fromInsert['queue_password'] = $row[14];
            // }

            $lastinsert = $obj->insertData('tbl_agent', $fromInsert);
            if ($lastinsert) {
                $obj->insertData('customer_billing', [
                    'agid' => $lastinsert,
                    'cusid' => $STD,
                    'monthlybill' => $obj->convertBanglaToEnglishNumbers($row[3]),
                    'previousdue' => $obj->convertBanglaToEnglishNumbers($row[15]),
                    'dueadvance' => $obj->convertBanglaToEnglishNumbers($row[15]),
                    'generate_at' => '2024-01-01'
                ]);

                // if ($mikrotikConnect) {
                $obj->createNewSecret(1, $row[1], $row[14], $row[4]);
                if (($row[5] == 0) || ($row[5] == 3)) {
                    $obj->disableSingleSecret(1, $row[1]);
                }
                // }
            }


            // create package
            $obj->packageCreate($row[4], $obj->convertBanglaToEnglishNumbers($row[3]));
        }

        $obj->notificationStore("New Customer Add Successful", 'success');
        echo '<script>window.location="?page=customer_view";</script>';
    }
}


?>
<?php

if($ty == 'EO'){


$active_customer = $obj->Total_Count("tbl_agent", "deleted_at IS NULL AND ag_status='1' AND billing_person_id=$userId") ?? 0; 
$total_customer = $obj->Total_Count("tbl_agent", "deleted_at IS NULL AND billing_person_id=$userId");
$totalInactiveCustomer = $obj->Total_Count("tbl_agent", "deleted_at IS NULL AND ag_status='0' AND billing_person_id=$userId ");
$totalFree = $obj->Total_Count("tbl_agent", "deleted_at IS NULL AND ag_status='2'");

}else{
    // প্রতি পেজে কত রেকর্ড দেখাবেন

    
    $active_customer = $obj->Total_Count("tbl_agent", "deleted_at IS NULL AND ag_status='1'") ?? 0;
    $total_customer = $obj->Total_Count("tbl_agent", "deleted_at IS NULL");
    $totalInactiveCustomer = $obj->Total_Count("tbl_agent", "deleted_at IS NULL AND ag_status='0'");
    $totalFree = $obj->Total_Count("tbl_agent", "deleted_at IS NULL AND ag_status='2'");
}

?>

<!-- chart-2 end -->
<style>
    .table-responsive {
        overflow-x: auto;
    }
    .dt-info{
        visibility: hidden;
    }
    .spinner {
    width: 30px;
    height: 30px;
    border: 4px solid #ccc;
    border-top-color: #000;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: auto;
    }
    @keyframes spin {
    to { transform: rotate(360deg); }
    }
</style>
<input type="hidden" id="package-filter" value="<?= $_GET['package'] ?? 0 ?>">
<input type="hidden" id="ag_status_filter" value="<?= $_GET['ag_status'] ?? 0 ?>">
<input type="hidden" id="zone_filter" value="<?= $_GET['zone'] ?? 0 ?>">
<input type="hidden" id="sub_zone_filter" value="<?= $_GET['sub_zone'] ?? 0 ?>">
<input type="hidden" id="isBillGenerate" value="<?= $_GET['billGenerate'] ?? 0 ?>">

<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <!-- Form Wizard Start -->
            <div class="form-wizard">
                <fieldset class="wizard-fieldset show">
                    <div class="row gy-3">
                        <div class="col">
                            <label for="zone-filter" class="form-label">Zone</label>
                            <div>
                                <select id="zone-filter" class="form-control">
                                    <option value="">All Zones</option>
                                    <?php 
                                        $conditions = "level = 1";
                                       ?>
                                    <?php foreach ($obj->view_all_by_cond('tbl_zone',$conditions) as $zone): ?>
                                        <option value="<?php echo $zone['zone_id']; ?>"><?php echo $zone['zone_name']; ?> </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col">
                            <label for="sub-zone-filter" class="form-label">Sub Zone</label>
                            <div>
                                <select id="sub-zone-filter" class="form-control">
                                    <option value="">All Subzone</option>
                                    <?php 
                                        $conditions = "level = 2";
                                       ?>
                                    <?php foreach ($obj->view_all_by_cond('tbl_zone', $conditions) as $sub_zone): ?>
                                        <option value="<?php echo $sub_zone['zone_id']; ?>"><?php echo $sub_zone['zone_name']; ?> </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col">
                            <label for="billing-person-filter" class="form-label">Billing Person</label>
                            <div class="position-relative">
                                <select id="billing-person-filter" class="form-control">
                                    <option value="">All Billing Persons</option>
                                    <?php foreach ($obj->getAllData('vw_user_info') as $bp): ?>
                                        <option value="<?php echo $bp['UserId']; ?>"><?php echo $bp['FullName']; ?> </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <label for="select-package-filter" class="form-label">Package</label>
                            <div>
                                <select id="select-package-filter" class="form-control">
                                    <option value="">All Packages</option>
                                    <?php foreach ($obj->getAllData('tbl_package') as $package): ?>
                                        <option value="<?php echo $package['net_speed']; ?>"><?php echo $package['net_speed']; ?> </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <label for="status-filter" class="form-label">Status</label>
                            <div class="position-relative">
                                <select id="status-filter" class="form-control">
                                    <option value="">All</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                    <option value="2">Free</option>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <label for="date-from" class="form-label">From Date</label>
                            <div class="position-relative">
                                <input type="date" id="date-from" class="form-control">
                            </div>
                        </div>
                        <div class="col">
                            <label for="date-to" class="form-label">To Date</label>
                            <div class="position-relative">
                                <input type="date" id="date-to" class="form-control">
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            <!-- Form Wizard End -->
        </div>
    </div>
</div>

<div class="col-md-12">
    <div class="card h-100 p-0 radius-12">
        <div class="card-body p-24">
            <div class="mt-24">
                <div class="row mt-24 gy-4">
                    <!-- total customer -->
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card p-3 shadow-none radius-8 border h-100 bg-gradient-end-1">
                            <div class="card-body p-0">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">

                                    <div class="d-flex align-items-center gap-2">
                                        <span class="mb-0 w-48-px h-48-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                            <iconify-icon icon="flowbite:users-group-solid" class="icon"></iconify-icon>
                                        </span>
                                        <div>
                                            <span class="mb-2 fw-medium text-secondary-light text-sm">Total Customers</span>
                                            <!-- <h6 class="fw-semibold" id="totalCustomers">0</h6> -->
                                            <h6 class="fw-semibold" style="display: flex; justify-content: start; align-items: center;">
                                                <span style="font-size: 23px;" id="totalCustomers"><?php echo $total_customer ?></span>
                                                
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- total active customers -->
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card p-3 shadow-none radius-8 border h-100 bg-gradient-end-1">
                            <div class="card-body p-0">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">

                                    <div class="d-flex align-items-center gap-2">
                                        <span class="mb-0 w-48-px h-48-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                            <iconify-icon icon="flowbite:users-group-solid" class="icon"></iconify-icon>
                                        </span>
                                        <div>
                                            <span class="mb-2 fw-medium text-secondary-light text-sm">Total Active Customers</span>
                                            <h6 class="fw-semibold" style="display: flex; justify-content: start; align-items: center;">
                                                <span style="font-size: 23px;" id="totalActiveCustomers"><?php echo $active_customer; ?></span>
                                                
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- total inactive customers -->
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card p-3 shadow-none radius-8 border h-100 bg-gradient-start-5">
                            <div class="card-body p-0">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">

                                    <div class="d-flex align-items-center gap-2">
                                        <span class="mb-0 w-48-px h-48-px border-danger-light-white bg-danger flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                            <iconify-icon icon="flowbite:users-group-solid" class="icon"></iconify-icon>
                                        </span>
                                        <div>
                                            <span class="mb-2 fw-medium text-secondary-light text-sm">Total Inactive Customers</span>
                                            <h6 class="fw-semibold" style="display: flex; justify-content: start; align-items: center;">
                                                <span style="font-size: 23px;" id="totalInActiveCustomers"><?php echo $totalInactiveCustomer ?></span>
                                                
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- total free customers -->
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card p-3 shadow-none radius-8 border h-100 bg-gradient-start-2">
                            <div class="card-body p-0">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">

                                    <div class="d-flex align-items-center gap-2">
                                        <span class="mb-0 w-48-px h-48-px bg-purple flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                            <iconify-icon icon="flowbite:users-group-solid" class="icon"></iconify-icon>
                                        </span>
                                        <div>
                                            <span class="mb-2 fw-medium text-secondary-light text-sm">Total Free Customers</span>
                                            <h6 class="fw-semibold" style="display: flex; justify-content: start; align-items: center;">
                                                <span style="font-size: 23px;" id="totalFreeCustomers"><?php echo $totalFree ?></span>
                                 
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Running Month New customers -->
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card p-3 shadow-none radius-8 border h-100 bg-gradient-start-4">
                            <div class="card-body p-0">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">

                                    <div class="d-flex align-items-center gap-2">
                                        <span class="mb-0 w-48-px h-48-px bg-success-main flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                            <iconify-icon icon="flowbite:users-group-solid" class="icon"></iconify-icon>
                                        </span>
                                        <div>
                                            <span class="mb-2 fw-medium text-secondary-light text-sm"><?= date("F") ?> New Customers</span>
                                            <h6 class="fw-semibold" id="">

                                            </h6>

                                            <h6 class="fw-semibold" style="display: flex; justify-content: start; align-items: center;">
                                                <?php
                                                $newCustomersInfo = $obj->rawSqlSingle("SELECT COUNT(*) as TotalUsers, SUM(taka) as TotalBill FROM `tbl_agent` WHERE deleted_at is NULL AND MONTH(entry_date) = MONTH(CURDATE()) AND YEAR(entry_date) = YEAR(CURDATE());");
                                                ?>
                                                <span style="font-size: 23px;" id=""><?= $newCustomersInfo["TotalUsers"] ?? 0 ?></span>
                                                <span style="font-size: 18px; margin: 0px 5px;"> => </span>
                                                <span style="font-size: 18px;" id="">৳<?= $newCustomersInfo["TotalBill"] ?? 0 ?></span>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Running Month Inactive customers -->
                    <div class="col-xxl-3 col-sm-6">
                        <div class="card p-3 shadow-none radius-8 border h-100 bg-gradient-start-5">
                            <div class="card-body p-0">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">

                                    <div class="d-flex align-items-center gap-2">
                                        <span class="mb-0 w-48-px h-48-px bg-danger flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                            <iconify-icon icon="flowbite:users-group-solid" class="icon"></iconify-icon>
                                        </span>
                                        <div>
                                            <span class="mb-2 fw-medium text-secondary-light text-sm"><?= date("F") ?> Inactive Customers</span>
                                            <h6 class="fw-semibold" style="display: flex; justify-content: start; align-items: center;">
                                                <?php
                                                $runningMonthInactive = $obj->rawSqlSingle("SELECT COUNT(*) as TotalUsers, SUM(taka) as TotalBill FROM `tbl_agent` WHERE deleted_at is NULL  AND `ag_status` = '0' AND MONTH(inactive_date) = MONTH(CURDATE());");
                                                ?>
                                                <span style="font-size: 23px;" id=""><?= $runningMonthInactive["TotalUsers"] ?? 0 ?></span>
                                                <span style="font-size: 18px; margin: 0px 5px;"> => </span>
                                                <span style="font-size: 18px;" id="">৳<?= $runningMonthInactive["TotalBill"] ?? 0 ?></span>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


</div>
<div class="modal fade" id="import" tabindex="-1" aria-labelledby="saleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="saleModalLabel">Upload CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5 class="modal-title fs-5" id="saleModalLabel">CSV Structure</h5>
                <div class="card-body table-responsive">

                    <table class="table bordered-table mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>IP</th>
                                <th>Dis. day</th>
                                <th>Bill</th>
                                <th>Package</th>
                                <th>Status</th>
                                <th>Mobile</th>
                                <th>Mobile(Opt)</th>
                                <th>Address</th>
                                <th>Email</th>
                                <th>National ID</th>
                                <th>gender</th>
                                <th>Entry Date</th>
                                <th>Con. Date</th>
                                <th>Queue Password</th>
                                <th>Previous Due</th>
                                <th>Zone</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <form class="flex-row" action="" enctype="multipart/form-data" method="post">
                    <div class="col-xxl-4">
                        <label for="csv-upload" class="form-label">Upload CSV</label>
                        <div class="position-relative">
                            <input type="file" id="csv-upload" class="form-control" name="csv-upload">
                        </div>
                    </div>
                    <div class="col-md-2" style="margin-top: 22px;">

                        <button type="submit" name="csvUpload" class="btn btn-success">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="col-md-12">

    <div class="card basic-data-table">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
            <h5 class="card-title mb-0">Customer List</h5>

            <button type="button" class="btn btn-success-600 radius-8 px-20 py-11" data-bs-toggle="modal" data-bs-target="#import">
                Import Customer <i class="fa-solid fa-arrow-up"></i> </button>
        </div>
        <div class="card-body table-responsive">
        <div class="row mb-4">
            <div class="col-6 d-flex gap-2">
                <select id="per_page" onchange="changePerPage()" class="form-control" style="width: 90px;">
                    <option value="20">20</option>
                    <option value="35">35</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="500">500</option>
                    <option value="700">700</option>
                    <option value="1000">1000</option>
                    <option value="4000">4000</option>
                    <!--<option value="4100" <?= ($_POST['per_page'] ?? 200) == 'all' ? 'selected' : '' ?>>All</option>-->
                </select>
                <button type="button" class="btn btn-success" onclick="exportPDF()">Export PDF</button>
                <!-- Excel Export Button -->
                <!--<a href="../excel/cus_list.php?ag_status=<?= $_GET['ag_status'] ?>&per_page=<?= $_POST['per_page'] ?? 20 ?>&pg=<?= $page ?? 1 ?>" class="btn btn-success">Export Excel</a>-->
                <button type="button" class="btn btn-success" onclick="exportExcel()">Export Excel</button>
            </div>
            <div class="col-6 text-end">
                <input type="text" id="search-input" style="width: 200px;" class="form-control d-inline-block" placeholder="Search">
            </div>
        </div>
        
<table class="table table-striped bordered-table mb-0" id="customer-table">
    <thead>
        <tr>
            <th>SL.</th>
            <th>Activity</th>
            <?php if ($obj->userWorkPermission('edit') || $obj->userWorkPermission('delete')) { ?>
                <th>Action</th>
            <?php } ?>
            <th>ID</th>
            <th>IP</th>
            <th>Name</th>
            <th>Address</th>
            <th>Mobile No</th>
            <th>Package</th>
            <th>Bill Amount</th>
            <th>Con.Date</th>
            <th>Zone</th>
            <th>B.Person</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody id="customer-datatable">
      
    </tbody>
    
</table>
 <div id="loader" style="display:none;">
        <div class="spinner" ></div>
    </div>
<?php
// ধরুন এগুলো আপনার pagination variable
// $page = বর্তমান পেজ
// $total_pages = মোট কতগুলো পেজ আছে
$adjacents = 2; // current page এর চারপাশে কয়টা number দেখাবে
?>


        </div>
    </div>
</div>


<!-- end row-->
<button class="btn btn-outline-success waves-effect waves-light btn-sm"
    id="graphicalViewButton">Graphical View</button>
<!-- chart start -->
<!-- Loading Bar (hidden by default) -->
<div style="display: none;" id="chart1Visibility">
    <div class="row gy-3 gx-3 mb-5" style="display: flex; justify-content: space-between;">
        <!-- customer count by month chart start -->
        <div class="col-12 col-xl-7 card p-2">
            <div class="card-header border-bottom bg-base py-16 px-24" style="display: flex; justify-content: space-between;">
                <h6 class="card-title text-lg fw-semibold mb-0">Customer Growth: <span id="customerCountYear"><?php echo date("Y") . " vs " . (intval(date("Y")) - 1); ?></span> Trend</h6>
                <div class="">
                    <select id="twoYearsCustomerTrendYearSelect" class="form-select form-select-sm w-auto bg-base border text-secondary-light">
                        <?php for ($i = date("Y"); $i >= 2020; $i--): ?>
                            <option value="<?= $i; ?>"><?= $i . " - " . ($i - 1); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <!-- <div class="card-body p-24"> -->
            <div id="customer_view_line_chart"></div>
            <!-- </div> -->
        </div>
        <!-- customer count by month chart end -->

        <div class="col-12 col-xl-4 card p-2">
            <h6 style="padding: 10px;">Customer Statistics by Type</h6>
            <div id="customerStatisticsByStatusChart" class="apex-charts mt-3"></div>
        </div>

    </div>


    <div class="row gap-2" style="display: flex; justify-content: space-between;">
        <div class="col-12 col-xl-5 card p-2">
            <h6 style="padding: 10px;">Customer Statistics by Zone</h6>
            <div id="zoneWiseCustomerViewPieChart" class="apex-charts mt-3"></div>
        </div>

        <div class="col-12 col-xl-5 card p-2">
            <h6 style="padding: 10px;">Customer Statistics by Package</h6>
            <div id="packageWiseCustomerPieChart" class=" apex-charts pt-3" data-colors="#6658dd"></div>
        </div>
    </div>

    <div class="row" style="display: flex; justify-content: space-between; margin-top: 30px;">
        <div class="col-12 col-xl-12 card p-2">
            <div class="card-header border-bottom bg-base py-16 px-24" style="display: flex; justify-content: space-between;">
                <h6 class="card-title text-lg fw-semibold mb-0">Inactive Customers: <span id="customerInactiveCountYear"><?php echo date("Y") . " vs " . (intval(date("Y")) - 1); ?></span> Trend</h6>
                <div class="">
                    <select id="twoYearsInactiveCustomerTrendYearSelect" class="form-select form-select-sm w-auto bg-base border text-secondary-light">
                        <?php for ($i = date("Y"); $i >= 2020; $i--): ?>
                            <option value="<?= $i; ?>"><?= $i . " - " . ($i - 1); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            <div id="inactiveCustomerTrendingChart" class=""></div>
        </div>



    </div>

</div>
<?php $obj->start_script(); ?>
<!-- customer line chart -->
<script>

var currentPage = 1;

function loadData(page = 1) {
    currentPage = page; // <-- update currentPage
    if(event) event.preventDefault();

    var zone = $('#zone-filter').val();
    var sub_zone = $('#sub-zone-filter').val();
    var bp = $('#billing-person-filter').val();
    var package = $('#select-package-filter').val();
    var status = $('#status-filter').val();
    var dateFrom = $('#date-from').val();
    var dateTo = $('#date-to').val();
    var search_input = $('#search-input').val();
    var per_page = $('#per_page').val();
    var ag_status_filter = $('#ag_status_filter').val();
    var package_filter = $('#package-filter').val();
    var zone_filter = $('#zone_filter').val();
    var sub_zone_filter = $('#sub_zone_filter').val();
    var isBillGenerate = $('#isBillGenerate').val();
    // console.log(ag_status_filter);

    $.ajax({
        url: './pages/customer/filter_customer_ajax_pagi.php',
        type: 'POST',
        data: {
            zone: zone,
            sub_zone: sub_zone,
            billing_person: bp,
            package: package,
            status: status,
            date_from: dateFrom,
            date_to: dateTo,
            search: search_input,
            pg: page,
            per_page: per_page,
            ag_status_filter: ag_status_filter,
            package_filter: package_filter,
            zone_filter: zone_filter,
            sub_zone_filter: sub_zone_filter,
            isBillGenerate: isBillGenerate
        },
         beforeSend: function() {
                                $('#loader').show();
                                $('#customer-datatable').html('');
                            },
        success: function(response){
            console.log(response);
            $('#customer-datatable').html(response);
        },
        complete: function() {
                                $("#loader").hide();
                                $("#customer-datatable").show();
                            }
    });
}

// per page change হলে প্রথম পেজ থেকে load হবে
function changePerPage() {
    loadData(1);
}

// Filter change এ call দিন
$('#zone-filter,#sub-zone-filter, #billing-person-filter, #select-package-filter, #status-filter, #date-from, #date-to, #search-input,#ag_status_filter')
    .on('change keyup', function(e){
        loadData(1, e);
    });

// Page load হলে default load
$(document).ready(function(){
    loadData(1);
});


function exportPDF() {
    var zone = $('#zone-filter').val();
    var sub_zone = $('#sub-zone-filter').val();
    var bp = $('#billing-person-filter').val();
    var package = $('#select-package-filter').val();
    var status = $('#status-filter').val();
    var dateFrom = $('#date-from').val();
    var dateTo = $('#date-to').val();
    var search_input = $('#search-input').val();
    var per_page = $('#per_page').val();
    var ag_status_filter = $('#ag_status_filter').val();

    window.open("./pages/customer/export_customer_pdf.php?zone="+zone+
        "&sub_zone="+sub_zone+
        "&billing_person="+bp+
        "&package="+package+
        "&status="+status+
        "&date_from="+dateFrom+
        "&date_to="+dateTo+
        "&ag_status_filter="+ag_status_filter+
        "&search="+search_input+
        "&per_page="+per_page+
        "&pg="+currentPage, "_blank"); // <-- send page number also
}

function exportExcel() {
    var per_page = $('#per_page').val(); // current per page value
    // var currentPage = 1; // remove this line

    var zone = $('#zone-filter').val();
    var sub_zone = $('#sub-zone-filter').val();
    var bp = $('#billing-person-filter').val();
    var package = $('#select-package-filter').val();
    var status = $('#status-filter').val();
    var dateFrom = $('#date-from').val();
    var dateTo = $('#date-to').val();
    var search_input = $('#search-input').val();
    var ag_status_filter = $('#ag_status_filter').val();

    // use global currentPage variable
    window.open("./excel/cus_list.php?zone="+zone+
        "&sub_zone="+sub_zone+
        "&billing_person="+bp+
        "&package="+package+
        "&status="+status+
        "&date_from="+dateFrom+
        "&date_to="+dateTo+
        "&ag_status_filter="+ag_status_filter+
        "&search="+search_input+
        "&per_page="+per_page+
        "&pg="+currentPage, "_blank");
}


</script>

<script>
    $(document).ready(function() {
        
        // Add event listener for the button click
        $('#graphicalViewButton').click(function() {
            $('#loading-spinner').show()
            $("#chart1Visibility").show();
            // Perform AJAX request when the button is clicked

            // two years customer trending chart start
            function twoYearsCustomerSelectChart(selectedYear) {
                $.ajax({
                    type: "GET",
                    url: `./pages/customer/customer_view_line_chart_ajax.php?selectedYear=${selectedYear}`,
                    dataType: "json",
                    success: function(response) {
                        $('#loading-spinner').hide();
                        // Call the function to render the line chart with the data from the server
                        cutomer_view_line_chart(response.previousData, response.previousYear, response.currentData, response.currentYear, response.maxData);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching data:", error);
                    }
                });
            }

            twoYearsCustomerSelectChart(new Date().getFullYear());
            $("#twoYearsCustomerTrendYearSelect").on("change", function() {
                const selectedYear = $(this).val();
                twoYearsCustomerSelectChart(selectedYear);
                $("#customerCountYear").text(selectedYear + " vs " + (selectedYear - 1));
            })
            // two years customer trending chart end


            $.ajax({
                type: "GET",
                url: "./pages/customer/TypeWiseCustomerPieChart_ajax.php",
                dataType: "json",
                success: function(res) {
                    $('#loading-spinner').hide();
                    // Call the function to render the line chart with the data from the server
                    TypeWiseCustomerPieChart(res.customerType, res.customerCountData);
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching data:", error);
                }
            });

            $.ajax({
                type: "GET",
                url: "./pages/customer/packageWiseCustomerPieChart_ajax.php",
                dataType: "json",
                success: function(res) {
                    $('#loading-spinner').hide();
                    // Call the function to render the line chart with the data from the server
                    packageWiseCustomerPieChart(res.pakageCountData, res.mbbsCountData);
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching data:", error);
                }
            });

            function inactiveCustomerTrendingChartLoad(selectedYear) {
                $.ajax({
                    type: "GET",
                    url: `./pages/customer/inactive_customer_view_column_chart_ajax.php?selectedYear=${selectedYear}`,
                    dataType: "json",
                    success: function(response) {
                        $('#loading-spinner').hide();
                        // Call the function to render the line chart with the data from the server
                        inactiveCustomerTrendingChart(response.previousData, response.previousYear, response.currentData, response.currentYear, response.maxData);
                        // console.log(res.pakageCountData); // To check the response
                        // alert(response); // To check the full response
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching data:", error);
                    }
                });
            }
            inactiveCustomerTrendingChartLoad(new Date().getFullYear());
            $("#twoYearsInactiveCustomerTrendYearSelect").on("change", function() {
                const selectedYear = $(this).val();
                inactiveCustomerTrendingChartLoad(selectedYear);
                $("#customerInactiveCountYear").text(selectedYear + " vs " + (selectedYear - 1));
            })

            $.ajax({
                type: "GET",
                url: "./pages/customer/zone_wise_customer_view_pie_chart_ajax.php",
                dataType: "json",
                success: function(response) {
                    $('#loading-spinner').hide();
                    // Call the function to render the line chart with the data from the server
                    zoneWiseCustomerViewPieChart(response.agCountData, response.zoneCountData);
                    // console.log(res.pakageCountData); // To check the response
                    // alert(response); // To check the full response
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching data:", error);
                }
            });
        });
    });



    let twoYearsCustomerSelectChartInstance = null;

    function cutomer_view_line_chart(previousData, previousYear, currentData, currentYear, maxData) {
        // Static month names for both years
        const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        // Convert the comma-separated strings to arrays of numbers
        const previousDataArray = previousData.split(',').map(Number);
        const currentDataArray = currentData.split(',').map(Number);

        // Calculate the tick amount for y-axis dynamically
        const tickAmount = Math.ceil(maxData / 1000); // Adjust this based on your maxData

        // Chart options
        var options = {
            series: [{
                name: `Previous - ${previousYear}`,
                data: previousDataArray // Use the actual array of numbers
            }, {
                name: `Current - ${currentYear}`,
                data: currentDataArray // Use the actual array of numbers
            }],
            chart: {
                height: 350,
                type: 'area'
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            // xaxis: {
            //     type: 'datetime',
            //     categories: ["2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z", "2018-09-19T02:30:00.000Z", "2018-09-19T03:30:00.000Z", "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z", "2018-09-19T06:30:00.000Z"]
            // },
            xaxis: {
                categories: months, // Use static month names
                title: {
                    text: "Month"
                }
            },
            tooltip: {
                x: {
                    format: 'dd/MM/yy HH:mm'
                },
            },
        };


        if (twoYearsCustomerSelectChartInstance) {
            twoYearsCustomerSelectChartInstance.updateOptions(options);
        } else {
            if (twoYearsCustomerSelectChartInstance) {
                twoYearsCustomerSelectChartInstance.destroy();
            }

            twoYearsCustomerSelectChartInstance = new ApexCharts(document.querySelector("#customer_view_line_chart"), options);
            twoYearsCustomerSelectChartInstance.render();
        }
    }
    //end line chart 


    function TypeWiseCustomerPieChart(customerType, customerCountData) {
        colors = ["#6658dd"];
        (dataColors = $("#customerStatisticsByStatusChart").data("colors")) &&
        (colors = dataColors.split(","));

        var options = {
            series: customerCountData.map(Number),
            chart: {
                width: 450,
                type: 'pie',
            },
            labels: customerType,
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

        var chart = new ApexCharts(document.querySelector("#customerStatisticsByStatusChart"), options);
        chart.render();

    }


    function packageWiseCustomerPieChart(pakageCountData, mbbsCountData) {
        colors = ["#6658dd"];
        // console.log(pakageCountData);
        // const mbbsCountDataArr = Array.from(mbbsCountData);
        // const normalizedData = pakageCountData.map(item => parseFloat(item.mb.trim()) || 0).filter(value => value > 0);
        const mbbsCountDataprocess = mbbsCountData.map(item => item?.mb || "N/A");


        (dataColors = $("#packageWiseCustomerPieChart").data("colors")) &&
        (colors = dataColors.split(","));

        var options = {
            series: pakageCountData.map((item) => Number(item)),
            chart: {
                width: 450,
                type: 'pie',
            },
            labels: mbbsCountDataprocess,
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

        var chart = new ApexCharts(document.querySelector("#packageWiseCustomerPieChart"), options);
        chart.render();

    }

    function zoneWiseCustomerViewPieChart(agCountData, zoneCountData) {
        var options = {
            series: agCountData.map(item => Number(item)),
            chart: {
                width: 450,
                type: 'pie',
            },
            labels: zoneCountData.map((item) => "Zone: " + item),
            responsive: [{
                breakpoint: 480,
                options: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        var chart = new ApexCharts(document.querySelector("#zoneWiseCustomerViewPieChart"), options);
        chart.render();
    }


    inactiveCustomerTrendingChartInstance = null;

    function inactiveCustomerTrendingChart(previousData, previousYear, currentData, currentYear, maxData) {
        // Static month names for both years
        const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        // Convert the comma-separated strings to arrays of numbers
        const previousDataArray = previousData.split(',').map(Number);
        const currentDataArray = currentData.split(',').map(Number);

        var options = {
            series: [{
                name: `Current - ${currentYear}`,
                data: currentDataArray
            }, {
                name: `Previous - ${previousYear}`,
                data: previousDataArray
            }],
            chart: {
                height: 350,
                type: 'area'
            },
            colors: ['#FF9B17', '#FF0000'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
                type: 'month',
                categories: months
            },
            tooltip: {
                x: {
                    format: 'dd/MM/yy HH:mm'
                },
            },
        };


        if (inactiveCustomerTrendingChartInstance) {
            inactiveCustomerTrendingChartInstance.updateOptions(options);
        } else {
            if (inactiveCustomerTrendingChartInstance) {
                inactiveCustomerTrendingChartInstance.destroy();
            }
            inactiveCustomerTrendingChartInstance = new ApexCharts(document.querySelector("#inactiveCustomerTrendingChart"), options);
            inactiveCustomerTrendingChartInstance.render();
        }
    }
    

    
    
</script>




<?php $obj->end_script(); ?>