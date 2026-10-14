<?php
include('customer.php');
?>
<div class="col-md-12">
    <div class="card basic-data-table">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
            <h5 class="card-title mb-0">Customer List</h5>

            <div>
                <select id="status_filter" class="form-select" style="width:100px; display:inline-block;">
                    <option value="">Status All</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                    <option value="2">Free</option>
                    <option value="3">Discontinued</option>
                </select>
            </div>
            <div>
                <select id="zone_filter" class="form-select" style="width:150px; display:inline-block;">
                    <option value="">All Zones</option>
                </select>
            </div>
            <div>
                <select id="subzone_filter" class="form-select" style="width:150px; display:inline-block;">
                    <option value="">Sub Zones</option>
                </select>
            </div>
            <!--<div>-->
            <!--    <select id="billing_filter" class="form-select" style="width:150px; display:inline-block;">-->
            <!--        <option value="">Billing P</option>-->
            <!--    </select>-->
            <!--</div>-->
            
            <div>
                <select id="disconnect_filter" class="form-select" style="width:150px; display:inline-block;">
                    <option value="">Disconnect Date </option>
                </select>
            </div>


            <div>
                <button class="btn btn-success-600 btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#statusModal">Bulk Status Change</button>
                <button class="btn btn-success-600 btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#changeBPersonModal">Change B Person</button>
                <button class="btn btn-success-600 btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#zoneModal">Bulk Zone Select</button>
                <button class="btn btn-success-600 btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#packageModal">Bulk Package Select</button>
                <button class="btn btn-danger-600 btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#disconnectModal">Bulk Disconnect Date</button>
                <button class="btn btn-danger-600 btn-sm" type="button" id="customerBulkDelete">Bulk Delete</button>
            </div>


        </div>
        <div class="card-body table-responsive">
            <table
                class="table table-striped bordered-table mb-0"
                id="customer-datatable"
                data-page-length="10">
                <thead>
                    <tr>
                        <th scope="col">
                            <input class="form-check-input me-1" type="checkbox" value="" id="check_all">
                            <label for="check_all">Check Box</label>
                        </th>
                        <th scope="col">SL.</th>
                        <th scope="col">ID</th>
                        <th scope="col">IP</th>
                        <th scope="col">Name</th>
                        <th scope="col">Status</th>
                        <th scope="col">Disconnect Date</th>
                        <th scope="col">Address</th>
                        <th scope="col">Mobile No</th>
                        <th scope="col">Package</th>
                        <th scope="col">Zone</th>
                        <th scope="col">Sub Zone</th>
                        <th scope="col">B Person</th>

                    </tr>
                </thead>
                <tbody id="customer_body">
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Customer Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="statusForm" method="post">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Status</label>
                        <select id="status_select" name="status_select" class="form-select">
                            <option value="">Select Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                            <option value="2">Free</option>
                            <option value="3">Discontinued</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Update Status
                    </button>

                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="changeBPersonModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Billing Person</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="billingPersonForm" method="post">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Status</label>
                        <select id="billing_person_select" name="billing_person_select" class="form-select">
                            <option value="">Select Billing person</option>
                                <option value=""></option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Update 
                    </button>

                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="zoneModal" tabindex="-1" aria-labelledby="saleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5">Select zone</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form action="" method="post" class="p-3 bg-light rounded shadow-sm">
                            <div class="row g-3 align-items-end">
                                <!-- Zone select -->
                                <div class="col-md-8">
                                    <label for="zone-filter" class="form-label fw-bold text-dark">Select Zone</label>
                                    <select id="zone_id" name="zone_id" class="form-select">
                                        <option value="">All Zones</option>
                                        <?php $conditions = "level = 1"; ?>
                                        <?php foreach ($obj->view_all_by_cond('tbl_zone', $conditions) as $zone): ?>
                                            <option value="<?= $zone['zone_id']; ?>"><?= $zone['zone_name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Zone select -->
                                <div class="col-md-8">
                                    <label for="zone-filter" class="form-label fw-bold text-dark">Sub Zone</label>
                                    <select id="sub_zone_id" name="sub_zone_id" class="form-select">
                                        <option value="">Select Sub Zone</option>
                                    </select>
                                </div>

                                <!-- Submit button -->
                                <div class="col-md-4 d-flex">
                                    <button type="submit" name="zone_select" id="zone_select" class="btn btn-success w-100 mt-3 mt-md-0">
                                        <i class="bi bi-check2-square me-1"></i>Save
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="disconnectModal" tabindex="-1" aria-labelledby="disconnectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5">Bulk Disconnect Date</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="disconnectForm" class="p-3 bg-light rounded shadow-sm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label for="disconnect-date" class="form-label fw-bold text-dark">Select Disconnect Date</label>
                            <input type="number" min="1" id="disconnect-day" name="disconnect_day" class="form-control" placeholder="Enter days" required>
                        </div>
                        <div class="col-md-4 d-flex">
                            <button type="submit" class="btn btn-danger w-100 mt-3 mt-md-0">
                                <i class="bi bi-check2-square me-1"></i>Update Date
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="packageModal" tabindex="-1" aria-labelledby="saleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5">Select zone</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form action="" method="post" class="p-3 bg-light rounded shadow-sm">
                            <div class="row g-3 align-items-end">
                                <!-- Zone select -->
                                <div class="col-md-8">
                                    <label for="zone-filter" class="form-label fw-bold text-dark">Select Packages</label>
                                    <select id="package-modal-filter" name="zone_filter" class="form-select">
                                        <option value="">All Packages</option>
                                        <?php foreach ($obj->rawsql('SELECT * from tbl_package') as $package): ?>
                                            <option value="<?= $package['package_id']; ?>"><?= $package['package_name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Submit button -->
                                <div class="col-md-4 d-flex">
                                    <button type="submit" name="zone_select" id="zone_select" class="btn btn-success w-100 mt-3 mt-md-0">
                                        <i class="bi bi-check2-square me-1"></i>Packages Select
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
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
    
    
    $(document).on('submit', '#packageModal', function(e) {
        e.preventDefault();
        let selectedCustomerIds = [];
        $("#customer_body input[type='checkbox']:checked").each(function() {
            selectedCustomerIds.push($(this).val());
        });
        let selectedPackage = $('#package-modal-filter').val();
        console.log('Selected Package:', selectedPackage);
        console.log('Selected Customers:', selectedCustomerIds);
        $.ajax({
            url: "./pages/customer/bulk_ajax.php",
            type: 'POST',
            data: {
                selectedCustomerIds: selectedCustomerIds,
                selectedPackage: selectedPackage
            },
            success: function(res) {
                console.log(res);
                $('#packageModal').modal('hide');
                setTimeout(function() {
                    window.location.reload();
                }, 500);
            },
            error: function(err) {
                console.log('Error:', err);
            }
        });
    });
    //Status Change
    $(document).on('submit', '#statusForm', function(e) {
        e.preventDefault();

        let selectedCustomerIds = [];

        $("#customer_body input[type='checkbox']:checked").each(function() {
            selectedCustomerIds.push($(this).val());
        });

        if (selectedCustomerIds.length === 0) {
            loadAlert(JSON.stringify({
                title: "No Customer Selected",
                text: "Please select at least one customer to change status.",
                icon: "error"
            }));
            return;
        }

        let newStatus = $('#status_select').val();

        if (newStatus === "") {
            loadAlert(JSON.stringify({
                title: "No Status Selected",
                text: "Please select a status to change.",
                icon: "error"
            }));
            return;
        }

        console.log('Selected Customers:', selectedCustomerIds);
        console.log('New Status:', newStatus);

        $.ajax({
            url: "./pages/customer/bulk_ajax.php",
            type: "POST",
            data: {
                selectedCustomerIds: selectedCustomerIds,
                newStatus: newStatus
            },
            success: function(res) {
                $('#statusModal').modal('hide');
                setTimeout(() => window.location.reload(), 500);
            },
            error: function(err) {
                console.log("Error:", err);
            }
        });
    });
    
    //Change Billing Person
    $(document).on('submit', '#billingPersonForm', function(e) {
        e.preventDefault();

        let selectedCustomerIds = [];
        
        
        

        $("#customer_body input[type='checkbox']:checked").each(function() {
            selectedCustomerIds.push($(this).val());
        });
        
        

        if (selectedCustomerIds.length === 0) {
            loadAlert(JSON.stringify({
                title: "No Customer Selected",
                text: "Please select at least one customer to change status.",
                icon: "error"
            }));
            return;
        }

        let billingPersonSelect = $('#billing_person_select').val();

        if (billingPersonSelect === "") {
            loadAlert(JSON.stringify({
                title: "No Selected Any Person",
                text: "Please select a Billing Person to change.",
                icon: "error"
            }));
            return;
        }

        console.log('Selected Customers:', selectedCustomerIds);
        console.log('Billing Selected:', billingPersonSelect);

        $.ajax({
            url: "./pages/customer/bulk_ajax.php",
            type: "POST",
            data: {
                selectedCustomerIds: selectedCustomerIds,
                billingPersonSelect: billingPersonSelect
            },
            success: function(res) {
                $('#changeBPersonModal').modal('hide');
                setTimeout(() => window.location.reload(), 500);
            },
            error: function(err) {
                console.log("Error:", err);
            }
        });
    });

    //Zone Change 
    $(document).on('submit', '#zoneModal', function(e) {
        e.preventDefault();
        let selectedZone = $('#zone_id').val();
        let selectedSubZone = $('#sub_zone_id').val();


        let selectedCustomerIds = [];

        $("#customer_body input[type='checkbox']:checked").each(function() {
            selectedCustomerIds.push($(this).val());
        });


        $.ajax({
            url: "./pages/customer/bulk_ajax.php",
            type: 'POST',
            dataType: 'json',
            data: {
                selectedCustomerIds: selectedCustomerIds,
                selectedZone: selectedZone,
                selectedSubZone: selectedSubZone
            },
            success: function(res) {
                console.log(res);
                $('#zoneModal').modal('hide');
                setTimeout(function() {
                    window.location.reload();
                }, 500);
            },
            error: function(err) {
                console.log('Error:', err);
            }
        });
    });

    //Disconnect Date
    $(document).on('submit', '#disconnectForm', function(e) {
        e.preventDefault();
        let selectedCustomerIds = [];
        $("#customer_body input[type='checkbox']:checked").each(function() {
            selectedCustomerIds.push($(this).val());
        });
        let disconnectDay = $('#disconnect-day').val();

        if (selectedCustomerIds.length === 0) {
            alert("Please select at least one customer!");
            return;
        }

        $.ajax({
            url: "./pages/customer/bulk_ajax.php",
            type: "POST",
            data: {
                selectedCustomerIds: selectedCustomerIds,
                disconnectDay: disconnectDay
            },
            success: function(res) {
                console.log(res);
                $('#disconnectModal').modal('hide');
                setTimeout(function() {
                    window.location.reload();
                }, 500);
            },
            error: function(err) {
                console.log("Error:", err);
            }
        });
    });

    $(document).on('change', '#check_all', function() {
        let checked = $(this).prop('checked');
        $("#customer_body input[type='checkbox']").prop('checked', checked);
    });
    
     //Delete Customer Function
    $(document).on('click', '#customerBulkDelete', function() {
        let selectedCustomerIds = [];

        $("#customer_body input[type='checkbox']:checked").each(function() {
            selectedCustomerIds.push($(this).val());
        });

        if (selectedCustomerIds.length === 0) {
            Swal.fire('No Customer Selected', 'Please select at least one customer.', 'warning');
            return;
        }

        //  Swal confirmation
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "./pages/customer/bulk_ajax.php",
                    type: "POST",
                    dataType: "json",
                    data: {
                        selectedCustomerIds: selectedCustomerIds,
                        customer_bulk_delete: 'customer_bulk_delete'
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire(
                                'Deleted!',
                                'Your customers have been deleted.',
                                'success'
                            ).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                res.message ?? 'Something went wrong.',
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Server Error!',
                            'Please try again later.',
                            'error'
                        );
                    }
                });
            }
        });
    });


    // Global variable for DataTable instance
    $('#zone_id').on('change', function () {
        let zone_id = $(this).val();

        console.log('Selected Zone ID:', zone_id);

        $('#sub_zone_id').html('<option>Loading...</option>');

        $.ajax({
            url: './pages/customer/get_subzone.php',
            type: 'POST',
            data: { zone_id: zone_id },
            success: function (response) {

                console.log(response); // 🔥 important debug
                $('#sub_zone_id').html(response);
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });


    function loadCustomers() {
        $.ajax({
            url: "./pages/customer/bulk_ajax.php",
            type: "GET",
            dataType: "json",
            success: function(res) {

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
                    <td>
                            <label class="px-3 m-1 bg-info bg-opacity-10 border border-info border-start-0 rounded-2" for="firstCheckbox${index}"> <input class="form-check-input me-1" type="checkbox" value="${item.ag_id}" id="firstCheckbox${index}"> Select</label>
                        </td>
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