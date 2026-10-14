<?php
include('customer.php');
?>
<div class="col-md-12">
    <div class="card basic-data-table">
        <!-- Header -->
        <div class="card-header d-flex flex-wrap align-items-start justify-content-between">
            <!-- Left Buttons -->
            <div class="d-flex flex-wrap align-items-center gap-3">
                <div>
                    <button class="btn btn-success-600 btn-sm" id="customerDuplicateCheck">
                        Customer Id Duplicate Check
                    </button>
                    <p class="mb-0 text-muted">
                        <span class="total_duplicate_count_cus_id"></span>
                    </p>
                </div>
                <div>
                    <button class="btn btn-success-600 btn-sm" id="customerDuplicateCheckCusIP">
                        Customer IP Duplicate Check
                    </button>
                    <p class="mb-0 text-muted">
                        <span class="total_duplicate_count_cus_ip"></span>
                    </p>
                </div>
                <div>
                    <button class="btn btn-success-600 btn-sm" id="customerDuplicateCheckCusMobile">
                        Customer Mobile Duplicate Check
                    </button>
                    <p class="mb-0 text-muted">
                        <span class="total_duplicate_count_cus_mobile"></span>
                    </p>
                </div>
                <div>
                    <button class="btn btn-success-600 btn-sm" id="customerDuplicateCheckCusBilling">
                        Customer Billing Duplicate Check
                    </button>
                    <p class="mb-0 text-muted">
                        <span class="total_duplicate_count_cus_billing"></span>
                    </p>
                </div>

            </div>
        </div>
        <!-- Main Customer Table -->
        <div class="card-body table-responsive" id="customerTableWrapper">

            <table class="table table-striped bordered-table mb-0"
                   id="customer-datatable"
                   data-page-length="10">

                <thead>
                    <tr>
                        <th>SL.</th>
                        <th>ID</th>
                        <th>IP</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Disconnect Date</th>
                        <th>Address</th>
                        <th>Mobile No</th>
                        <th>Package</th>
                        <th>Zone</th>
                        <th>Sub Zone</th>
                        <th>B Person</th>
                    </tr>
                </thead>

                <tbody id="customer_body"></tbody>

            </table>

        </div>

        <!-- Duplicate Billing Table (Default Hidden) -->
        <div class="card-body table-responsive d-none" id="duplicateTableWrapper">

            <table class="table table-bordered table-striped table-hover">

                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>AGID</th>
                        <th>Customer ID</th>
                        <th>Monthly Bill</th>
                        <th>Total Generate</th>
                    </tr>
                </thead>

                <tbody id="duplicateTableBody"></tbody>

            </table>

        </div>

    </div>
</div>

<?php $obj->start_script(); ?>
<script>

    function loadAlert(data) {
        let alertData = JSON.parse(data);
        Swal.fire({
            icon: `${alertData.icon ?? "error"}`,
            title: alertData.title,
            text: alertData.text,
            timer: 1500,
            showConfirmButton: false,
            position: "top-center"
        });
    }
    $(document).on('click', '#customerDuplicateCheckCusBilling', function () {
         $("#customerTableWrapper").addClass("d-none");

        $("#duplicateTableWrapper").removeClass("d-none");

        $.ajax({
            url: "./pages/customer/dublicate_check_ajax.php",
            type: 'GET',
            dataType: 'json',
            data: {
                customer_billing_duplicate: true
            },

            success: function (res) {
                console.log(res);
                $("#customerDuplicateCheck")
                    .addClass("btn-success-600")
                    .removeClass("btn-danger-600");
                $("#customerDuplicateCheckCusMobile")
                    .addClass("btn-success-600")
                    .removeClass("btn-danger-600");
                $("#customerDuplicateCheckCusIP")
                    .addClass("btn-success-600")
                    .removeClass("btn-danger-600");
                $("#customerDuplicateCheckCusBilling")
                    .addClass("btn-danger-600")
                    .removeClass("btn-success-600");
                $(".total_duplicate_count_cus_billing").text(`${res.total_duplicate} duplicates found`);
                let rows = "";
                res.data.forEach((item, index) => {

                rows += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.id ?? 'N/A'}</td>
                    <td>${item.agid ?? 'N/A'}</td>
                    <td>${item.cusid ?? 'N/A'}</td>
                    <td>${item.monthlybill ?? 'N/A'}</td>
                    <td>${item.totalgenerate ?? 'N/A'}</td>
                </tr>`;
            });

            $("#duplicateTableBody").html(rows);
            },

            error: function (xhr) {
                console.log(xhr.responseText);
            }
        });

    });

   $(document).on('click', '#customerDuplicateCheck', function () {

        console.log("clicked");
        $("#duplicateTableWrapper").addClass("d-none");

        $("#customerTableWrapper").removeClass("d-none");

        $.ajax({
            url: "./pages/customer/dublicate_check_ajax.php",
            type: 'GET',
            dataType: 'json',
            data: {
                duplicate_check: true
            },

            success: function (res) {
                
                $("#customerDuplicateCheck")
                    .addClass("btn-danger-600")
                    .removeClass("btn-success-600");
                $("#customerDuplicateCheckCusMobile")
                    .addClass("btn-success-600")
                    .removeClass("btn-danger-600");
                $("#customerDuplicateCheckCusIP")
                    .addClass("btn-success-600")
                    .removeClass("btn-danger-600");
                $("#customerDuplicateCheckCusBilling")
                    .addClass("btn-success-600")
                    .removeClass("btn-danger-600");
                $(".total_duplicate_count_cus_id").text(`${res.total_duplicate} duplicates found`);
                let rows = "";
                let zoneSet = new Set();
                let subZoneSet = new Set();
                let billingPersonSet = new Set();

                // ====== DESTROY OLD DATATABLE ======
                if ($.fn.DataTable.isDataTable('#customer-datatable')) {
                    $('#customer-datatable').DataTable().clear().destroy();
                }

                // ====== CLEAR OLD TABLE BODY ======
                $("#customer_body").empty();

                res.data.forEach((item, index) => {

                    subZoneSet.add(item.sub_zone_name ?? "N/A");
                    zoneSet.add(item.zone_name ?? "N/A");

                    billingPersonSet.add(
                        JSON.stringify({
                            id: item.billing_user_id,
                            name: item.billing_user_name
                        })
                    );

                    rows += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.cus_id}</td>
                        <td>${item.ip ?? 'N/A'}</td>
                        <td>${item.ag_name ?? 'N/A'}</td>

                        <td>
                            ${
                                item.ag_status == 0
                                ? '<span class="badge bg-danger">Inactive</span>'
                                : item.ag_status == 1
                                ? '<span class="badge bg-success">Active</span>'
                                : item.ag_status == 2
                                ? '<span class="badge bg-primary">Free</span>'
                                : item.ag_status == 3
                                ? '<span class="badge bg-secondary">Discontinued</span>'
                                : '<span class="badge bg-light text-dark">N/A</span>'
                            }
                        </td>

                        <td>${item.mikrotik_disconnect ?? 'N/A'}</td>
                        <td>${item.ag_office_address ?? 'N/A'}</td>
                        <td>${item.ag_mobile_no ?? 'N/A'}</td>
                        <td>${item.mb ?? 'N/A'}</td>
                        <td>${item.zone_name ?? 'N/A'}</td>
                        <td>${item.sub_zone_name ?? 'N/A'}</td>
                        <td>${item.billing_user_name ?? 'N/A'}</td>

                    </tr>`;
                });

                // ====== INSERT NEW ROWS ======
                $("#customer_body").html(rows);

                // ====== FILTER OPTIONS ======

                let zoneOptions = `<option value="">All Zones</option>`;
                zoneSet.forEach(z => {
                    zoneOptions += `<option value="${z}">${z}</option>`;
                });
                $("#zone_filter").html(zoneOptions);

                let subzoneOptions = `<option value="">All Sub Zones</option>`;
                subZoneSet.forEach(sz => {
                    subzoneOptions += `<option value="${sz}">${sz}</option>`;
                });
                $("#subzone_filter").html(subzoneOptions);

                let billingPOptions = `<option value="">Billing Person</option>`;

                billingPersonSet.forEach(bl => {
                    let user = JSON.parse(bl);

                    billingPOptions += `
                        <option value="${user.name}">
                            ${user.name}
                        </option>
                    `;
                });

                $("#billing_filter").html(billingPOptions);
                $("#billing_person_select").html(billingPOptions);

                // ====== DAY FILTER ======

                let dayOptions = `<option value="">Disconnect Date</option>`;

                for (let i = 0; i <= 30; i++) {
                    dayOptions += `<option value="${i}">${i} Days</option>`;
                }

                $("#disconnect_filter").html(dayOptions);

                // ====== CREATE NEW DATATABLE ======

                customerTable = $('#customer-datatable').DataTable({
                    pageLength: 100,
                    responsive: true,
                    destroy: true,
                    lengthMenu: [
                        [10, 25, 100, 500, 1000],
                        [10, 25, 100, 500, 1000]
                    ],
                    columnDefs: [
                        {
                            targets: 0,
                            orderable: false
                        }
                    ]
                });

            },

            error: function (xhr) {
                console.log(xhr.responseText);
            }
        });

    });
   $(document).on('click', '#customerDuplicateCheckCusIP', function () {
        $("#duplicateTableWrapper").addClass("d-none");

        $("#customerTableWrapper").removeClass("d-none");
        console.log("clicked");

        $.ajax({
            url: "./pages/customer/dublicate_check_ajax.php",
            type: 'GET',
            dataType: 'json',
            data: {
                duplicate_check_ip: true
            },

            success: function (res) {
                $("#customerDuplicateCheck")
                    .addClass("btn-success-600")
                    .removeClass("btn-danger-600");
                $("#customerDuplicateCheckCusMobile")
                    .addClass("btn-success-600")
                    .removeClass("btn-danger-600");
                $("#customerDuplicateCheckCusIP")
                    .addClass("btn-danger-600")
                    .removeClass("btn-success-600");
                $("#customerDuplicateCheckCusBilling")
                    .addClass("btn-success-600")
                    .removeClass("btn-danger-600");

                $(".total_duplicate_count_cus_ip").text(`${res.total_duplicate} duplicates found`);
                let rows = "";
                let zoneSet = new Set();
                let subZoneSet = new Set();
                let billingPersonSet = new Set();

                // ====== DESTROY OLD DATATABLE ======
                if ($.fn.DataTable.isDataTable('#customer-datatable')) {
                    $('#customer-datatable').DataTable().clear().destroy();
                }

                // ====== CLEAR OLD TABLE BODY ======
                $("#customer_body").empty();

                res.data.forEach((item, index) => {

                    subZoneSet.add(item.sub_zone_name ?? "N/A");
                    zoneSet.add(item.zone_name ?? "N/A");

                    billingPersonSet.add(
                        JSON.stringify({
                            id: item.billing_user_id,
                            name: item.billing_user_name
                        })
                    );

                    rows += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.cus_id}</td>
                        <td>${item.ip ?? 'N/A'}</td>
                        <td>${item.ag_name ?? 'N/A'}</td>

                        <td>
                            ${
                                item.ag_status == 0
                                ? '<span class="badge bg-danger">Inactive</span>'
                                : item.ag_status == 1
                                ? '<span class="badge bg-success">Active</span>'
                                : item.ag_status == 2
                                ? '<span class="badge bg-primary">Free</span>'
                                : item.ag_status == 3
                                ? '<span class="badge bg-secondary">Discontinued</span>'
                                : '<span class="badge bg-light text-dark">N/A</span>'
                            }
                        </td>

                        <td>${item.mikrotik_disconnect ?? 'N/A'}</td>
                        <td>${item.ag_office_address ?? 'N/A'}</td>
                        <td>${item.ag_mobile_no ?? 'N/A'}</td>
                        <td>${item.mb ?? 'N/A'}</td>
                        <td>${item.zone_name ?? 'N/A'}</td>
                        <td>${item.sub_zone_name ?? 'N/A'}</td>
                        <td>${item.billing_user_name ?? 'N/A'}</td>

                    </tr>`;
                });

                // ====== INSERT NEW ROWS ======
                $("#customer_body").html(rows);

                // ====== FILTER OPTIONS ======

                let zoneOptions = `<option value="">All Zones</option>`;
                zoneSet.forEach(z => {
                    zoneOptions += `<option value="${z}">${z}</option>`;
                });
                $("#zone_filter").html(zoneOptions);

                let subzoneOptions = `<option value="">All Sub Zones</option>`;
                subZoneSet.forEach(sz => {
                    subzoneOptions += `<option value="${sz}">${sz}</option>`;
                });
                $("#subzone_filter").html(subzoneOptions);

                let billingPOptions = `<option value="">Billing Person</option>`;

                billingPersonSet.forEach(bl => {
                    let user = JSON.parse(bl);

                    billingPOptions += `
                        <option value="${user.name}">
                            ${user.name}
                        </option>
                    `;
                });

                $("#billing_filter").html(billingPOptions);
                $("#billing_person_select").html(billingPOptions);

                // ====== DAY FILTER ======

                let dayOptions = `<option value="">Disconnect Date</option>`;

                for (let i = 0; i <= 30; i++) {
                    dayOptions += `<option value="${i}">${i} Days</option>`;
                }

                $("#disconnect_filter").html(dayOptions);

                // ====== CREATE NEW DATATABLE ======

                customerTable = $('#customer-datatable').DataTable({
                    pageLength: 100,
                    responsive: true,
                    destroy: true,
                    lengthMenu: [
                        [10, 25, 100, 500, 1000],
                        [10, 25, 100, 500, 1000]
                    ],
                    columnDefs: [
                        {
                            targets: 0,
                            orderable: false
                        }
                    ]
                });

            },

            error: function (xhr) {
                console.log(xhr.responseText);
            }
        });

    });
   $(document).on('click', '#customerDuplicateCheckCusMobile', function () {
        $("#duplicateTableWrapper").addClass("d-none");

        $("#customerTableWrapper").removeClass("d-none");
            $.ajax({
                url: "./pages/customer/dublicate_check_ajax.php",
                type: 'GET',
                dataType: 'json',
                data: {
                    duplicate_check_mobile: true
                },

                success: function (res) {
                    $("#customerDuplicateCheck")
                        .addClass("btn-success-600")
                        .removeClass("btn-danger-600");
                    $("#customerDuplicateCheckCusIP")
                        .addClass("btn-success-600")
                        .removeClass("btn-danger-600");
                    $("#customerDuplicateCheckCusMobile")
                        .addClass("btn-danger-600")
                        .removeClass("btn-success-600");
                    $("#customerDuplicateCheckCusBilling")
                    .addClass("btn-success-600")
                    .removeClass("btn-danger-600");
                    $(".total_duplicate_count_cus_mobile").text(`${res.total_duplicate} duplicates found`);
                    let rows = "";
                    let zoneSet = new Set();
                    let subZoneSet = new Set();
                    let billingPersonSet = new Set();

                    // ====== DESTROY OLD DATATABLE ======
                    if ($.fn.DataTable.isDataTable('#customer-datatable')) {
                        $('#customer-datatable').DataTable().clear().destroy();
                    }

                    // ====== CLEAR OLD TABLE BODY ======
                    $("#customer_body").empty();

                    res.data.forEach((item, index) => {

                        subZoneSet.add(item.sub_zone_name ?? "N/A");
                        zoneSet.add(item.zone_name ?? "N/A");

                        billingPersonSet.add(
                            JSON.stringify({
                                id: item.billing_user_id,
                                name: item.billing_user_name
                            })
                        );

                        rows += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.cus_id}</td>
                            <td>${item.ip ?? 'N/A'}</td>
                            <td>${item.ag_name ?? 'N/A'}</td>

                            <td>
                                ${
                                    item.ag_status == 0
                                    ? '<span class="badge bg-danger">Inactive</span>'
                                    : item.ag_status == 1
                                    ? '<span class="badge bg-success">Active</span>'
                                    : item.ag_status == 2
                                    ? '<span class="badge bg-primary">Free</span>'
                                    : item.ag_status == 3
                                    ? '<span class="badge bg-secondary">Discontinued</span>'
                                    : '<span class="badge bg-light text-dark">N/A</span>'
                                }
                            </td>

                            <td>${item.mikrotik_disconnect ?? 'N/A'}</td>
                            <td>${item.ag_office_address ?? 'N/A'}</td>
                            <td>${item.ag_mobile_no ?? 'N/A'}</td>
                            <td>${item.mb ?? 'N/A'}</td>
                            <td>${item.zone_name ?? 'N/A'}</td>
                            <td>${item.sub_zone_name ?? 'N/A'}</td>
                            <td>${item.billing_user_name ?? 'N/A'}</td>

                        </tr>`;
                    });

                    // ====== INSERT NEW ROWS ======
                    $("#customer_body").html(rows);

                    // ====== FILTER OPTIONS ======

                    let zoneOptions = `<option value="">All Zones</option>`;
                    zoneSet.forEach(z => {
                        zoneOptions += `<option value="${z}">${z}</option>`;
                    });
                    $("#zone_filter").html(zoneOptions);

                    let subzoneOptions = `<option value="">All Sub Zones</option>`;
                    subZoneSet.forEach(sz => {
                        subzoneOptions += `<option value="${sz}">${sz}</option>`;
                    });
                    $("#subzone_filter").html(subzoneOptions);

                    let billingPOptions = `<option value="">Billing Person</option>`;

                    billingPersonSet.forEach(bl => {
                        let user = JSON.parse(bl);

                        billingPOptions += `
                            <option value="${user.name}">
                                ${user.name}
                            </option>
                        `;
                    });

                    $("#billing_filter").html(billingPOptions);
                    $("#billing_person_select").html(billingPOptions);

                    // ====== DAY FILTER ======

                    let dayOptions = `<option value="">Disconnect Date</option>`;

                    for (let i = 0; i <= 30; i++) {
                        dayOptions += `<option value="${i}">${i} Days</option>`;
                    }

                    $("#disconnect_filter").html(dayOptions);

                    // ====== CREATE NEW DATATABLE ======

                    customerTable = $('#customer-datatable').DataTable({
                        pageLength: 100,
                        responsive: true,
                        destroy: true,
                        lengthMenu: [
                            [10, 25, 100, 500, 1000],
                            [10, 25, 100, 500, 1000]
                        ],
                        columnDefs: [
                            {
                                targets: 0,
                                orderable: false
                            }
                        ]
                    });

                },

                error: function (xhr) {
                    console.log(xhr.responseText);
                }
            });

        });
    
    function loadCustomers() {
        $.ajax({
            url: "./pages/customer/dublicate_check_ajax.php",
            type: "GET",
            dataType: "json",
            data: {
                duplicate_check: true
            },
            success: function(res) {
                $(".total_duplicate_count_cus_id").text(`${res.total_duplicate} duplicates found`);
                $("#customerDuplicateCheck")
                    .addClass("btn-danger-600")
                    .removeClass("btn-success-600");
                let rows = "";
                let zoneSet = new Set();
                let subZoneSet = new Set();
                let billingPersonSet = new Set();
                res.data.forEach((item, index) => {
                        subZoneSet.add(item.sub_zone_name ?? "N/A");
                        zoneSet.add(item.zone_name ?? "N/A");
                        billingPersonSet.add(
                            JSON.stringify({
                                id: item.billing_user_id,
                                name: item.billing_user_name
                            })
                        );
                    rows += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.cus_id}</td>
                    <td>${item.ip ?? 'N/A'}</td>
                    <td>${item.ag_name ?? 'N/A'}</td>
                    <td>
                        ${
                            item.ag_status == 0 
                            ? '<span class="badge bg-danger">Inactive</span>' 
                            : item.ag_status == 1
                                ? '<span class="badge bg-success">Active</span>'
                                : item.ag_status == 2
                                ? '<span class="badge bg-primary">Free</span>'
                                : item.ag_status == 3
                                    ? '<span class="badge bg-secondary">Discontinued</span>'
                                    : '<span class="badge bg-light text-dark">N/A</span>'
                        }
                    </td>

                    <td>${item.mikrotik_disconnect ?? 'N/A'}</td>
                    <td>${item.ag_office_address ?? 'N/A'}</td>
                    <td>${item.ag_mobile_no ?? 'N/A'}</td>
                    <td>${item.mb ?? 'N/A'}</td>
                    <td>${item.zone_name ?? 'N/A'}</td>
                    <td>${item.sub_zone_name ?? 'N/A'}</td>
                    <td>${item.billing_user_name ?? 'N/A'}</td>

                </tr>`;
                });

                $("#customer_body").html(rows);

                // Populate zone dropdown
                let zoneOptions = `<option value="">All Zones</option>`;
                zoneSet.forEach(z => zoneOptions += `<option value="${z}">${z}</option>`);
                $("#zone_filter").html(zoneOptions);

                let subzoneOptions = `<option value="">All Sub Zones</option>`; 
                subZoneSet.forEach(sz => subzoneOptions += `<option value="${sz}">${sz}</option>`);
                $("#subzone_filter").html(subzoneOptions);
                
                //Billing person
                
                let billingPOptions = `<option value="">Billing Person</option>`; 
                billingPersonSet.forEach(bl => billingPOptions += `<option value="${bl}">${bl}</option>`);
                $("#billing_filter").html(billingPOptions);
                
                let billingPOptionsModal = `<option value="">Billing Person</option>`;

                billingPersonSet.forEach(bl => {
                    let user = JSON.parse(bl);
                    billingPOptionsModal += `
                        <option value="${user.id}">
                            ${user.name}
                        </option>
                    `;
                });
                
                $("#billing_person_select").html(billingPOptionsModal);


                // Disconnect day filter options (1-30)
                let dayOptions = `<option value="">Disconnect Date </option>`;
                for (let i = 0; i <= 30; i++) {
                    dayOptions += `<option value="${i}">${i} Days</option>`;
                }
                $("#disconnect_filter").html(dayOptions);

                // Destroy old DataTable
                if ($.fn.DataTable.isDataTable('#customer-datatable')) {
                    customerTable.destroy();
                }

                // New DataTable
                customerTable = $('#customer-datatable').DataTable({
                    pageLength: 100,
                    responsive: true,
                    lengthMenu: [
                        [10, 25, 100, 500, 1000],
                        [10, 25, 100, 500, 1000]
                    ],
                    columnDefs: [
                        { targets: 0, orderable: false },
                        
                    ]
                });

                // Status Filter
                $('#status_filter').on('change', function() {
                    let val = $(this).val();
                    let status = "";

                    if (val === "1") status = "Active";
                    else if (val === "0") status = "Inactive";
                    else if (val === "2") status = "Free";
                    else if (val === "3") status = "Discontinued";
                    else status = "";
                    if(status === ""){
                        customerTable.column(5).search("").draw();
                        return;
                    }else{
                        customerTable.column(5).search("^" + status + "$", true, false).draw();
                    }
                });


                // Zone Filter
                $('#zone_filter').on('change', function() {
                    customerTable.column(10).search($(this).val()).draw();
                });



                $('#subzone_filter').on('change', function() {
                    customerTable.column(11).search($(this).val()).draw();
                });
                
                $('#billing_filter').on('change', function() {
                    customerTable.column(12).search($(this).val()).draw();
                });

                // Disconnect Days Filter (Final working version)
                $('#disconnect_filter').on('change', function () {
                    let selectedValue = $(this).val();
                    if (selectedValue === "") {
                        customerTable.column(6).search("").draw();
                    } else {
                        customerTable.column(6).search("^" + selectedValue + "$", true, false).draw();
                    }
                });
            }
        });
    }


    $(document).ready(function() {
        loadCustomers();
    });
</script>
<?php $obj->end_script(); ?>