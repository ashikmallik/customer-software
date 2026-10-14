
<!-- chart-2 end -->
<style>
    .table-responsive {
        overflow-x: auto;
    }
</style>

<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <!-- Form Wizard Start -->
            <div class="form-wizard">
                <fieldset class="wizard-fieldset show">
                    <div class="row gy-3">
                        
                        <div class="col-sm-2">
                            <label for="billing-person-filter" class="form-label">Year</label>
                            <div class="position-relative">
                                <select id="year-filter" class="form-control">
                                    <option value="">All Years</option>
                                    <?php
                                        $currentYear = date("Y");
                                        for($i = $currentYear; $i > $currentYear - 3; $i--){
                                            $selected = ($i == $currentYear) ? "selected" : "";
                                            echo "<option value='$i' $selected>$i</option>";
                                        }
                                    ?>
                               </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <label for="month-filter" class="form-label">Month</label>
                            <div>
                                <select id="month-filter" class="form-control">
                                    <option value="">All Months</option>
                                    <?php
                                    $currentMonth = date("n");
                                    $months = [
                                        1 => "January",
                                        2 => "February",
                                        3 => "March",
                                        4 => "April",
                                        5 => "May",
                                        6 => "June",
                                        7 => "July",
                                        8 => "August",
                                        9 => "September",
                                        10 => "October",
                                        11 => "November",
                                        12 => "December"
                                    ];
                        
                                    foreach ($months as $num => $name) {
                                        $selected = ($num == $currentMonth) ? "selected" : "";
                                        echo "<option value='$num' $selected>$name</option>";
                                    }
                                    ?>
                                </select>
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
                                            <span class="mb-2 fw-medium text-secondary-light text-sm">Total Due Customer</span>
                                            <!-- <h6 class="fw-semibold" id="totalCustomers">0</h6> -->
                                            <h6 class="fw-semibold" style="display: flex; justify-content: start; align-items: center;">
                                                <span style="font-size: 23px;" id="totalCustomers">0</span>
                                                <span style="font-size: 18px; margin: 0px 5px;"> => </span>
                                                <span style="font-size: 18px;" id="totalCustomerBill">৳0</span>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

</div>
<div class="col-md-12">
    <div class="card basic-data-table">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
            <h5 class="card-title mb-0">Previouse Log</h5>
         <span class ="bg-warning ">Note: This is for reference only and will not be included in your current billing.</span> 
            
        </div>
        <div class="card-body table-responsive">
            <table
                class="table table-striped bordered-table mb-0"
                id="customer-datatable"
                data-page-length="10">
                <thead>
                    <tr>
                        <th scope="col">SL.</th>
                        <th scope="col">Cus Id</th>
                        <th scope="col">Name</th>
                        <th scope="col">Previouse Amount</th>
                        <th scope="col">Date</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $obj->start_script(); ?>


<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#customer-datatable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "pages/myBill/ajax_previouse_due_log.php",
            "type": "POST",
            "data": function(d) {
                d.year = $('#year-filter').val();   // year filter
                d.month = $('#month-filter').val(); // month filter
            },
            "dataSrc": function(json) {
                // check for error
                if(json.error) {
                    alert('Error: ' + json.error);
                    return [];
                }

                // update summary cards
                $('#totalCustomers').text(json.summary.total_customers ?? 0);
                $('#totalCustomerBill').text('৳' + (json.summary.total_due ?? 0));

                // return data array for DataTable
                return json.data ?? [];
            }
        },
        "columns": [
            {
                "data": null,
                "render": function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
           {
                "data": "cus",
                "defaultContent": "-",
                "render": function(data, type, row) {
                   return '<a class="btn btn-success btn-sm" href="?page=customer_ledger&token=' + row.ag_id + '">' + data + '</a>';
                }
            },
            { "data": "agent_name", "defaultContent": "-" },
            { "data": "prevous_due_amount", 
              "render": function(data, type, row) {
                  return '৳' + parseFloat(data).toFixed(2);
              } 
            },
            { "data": "created_at", 
              "render": function(data, type, row) {
                  if(!data) return '-';
                  var date = new Date(data);
                  return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
              } 
            }
        ],
        "order": [[2, "desc"]],
        "pageLength": 10,
        "lengthMenu": [10, 25, 50, 100],
        "language": {
            "emptyTable": "No records available"
        }
    });

    // reload table on filter change
    $('#year-filter, #month-filter').on('change', function() {
        table.ajax.reload();
    });
});
</script>

<?php $obj->end_script(); ?>