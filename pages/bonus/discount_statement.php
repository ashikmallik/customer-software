<?php
// EntryBy dropdown list
$entryUsers = $obj->rawSql("SELECT UserId, FullName FROM _createuser ORDER BY FullName ASC");
?>

<style>
  .filter-card { border: 1px solid #eee; border-radius: 12px; }
  .stat-card { border-radius: 14px; border: 1px solid #eee; }
  .stat-card .icon-wrap { width: 44px; height: 44px; display:flex; align-items:center; justify-content:center; border-radius: 50%; }
  .table thead th { white-space: nowrap; }
  .dt-buttons .btn { margin-left: 6px; }
  .badge { font-weight: 600; }

  /* Print: only table */
  @media print {
    body * { visibility: hidden; }
    #printArea, #printArea * { visibility: visible; }
    #printArea { position: absolute; top: 0; left: 0; width: 100%; }
    .dt-buttons, .dataTables_filter, .dataTables_length, .dataTables_paginate, .dataTables_info, .no-print { display:none !important; }
    table { font-size: 12px; }
  }
  
/* wrapper */
.card-table{
  border: 1px solid #eef0f4;
  border-radius: 14px;
  box-shadow: 0 10px 26px rgba(16,24,40,.06);
  overflow: hidden;
  background: #fff;
}

/* header */
.card-table .card-header{
  padding: 14px 18px;
  background: linear-gradient(180deg,#ffffff 0%, #fbfcff 100%);
  border-bottom: 1px solid #eef0f4;
}
.card-table .card-title{
  font-weight: 700;
  letter-spacing: .2px;
}

/* table base */
#customer-datatable{
  margin: 0 !important;
  border-collapse: separate;
  border-spacing: 0;
  font-size: 14px;
}

/* thead modern dark */
#customer-datatable thead.table-dark th{
  background: #0b1220 !important;
  color: #e5e7eb !important;
  border: 0 !important;
  padding: 14px 12px !important;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: .05em;
  white-space: nowrap;
}

/* sticky header (optional) */
#customer-datatable thead th{
  position: sticky;
  top: 0;
  z-index: 2;
}

/* body cells */
#customer-datatable tbody td{
  padding: 14px 12px !important;
  border-top: 0 !important;
  border-bottom: 1px solid #f1f3f6 !important;
  vertical-align: middle;
  color: #111827;
}

/* zebra (soft) */
#customer-datatable tbody tr:nth-child(even){
  background: #fcfcfd;
}

/* hover */
#customer-datatable tbody tr{
  transition: background .18s ease, transform .12s ease;
}
#customer-datatable tbody tr:hover{
  background: #f9fafb;
}

/* bordered-table class override (clean) */
.bordered-table{
  border: none !important;
}

/* rounded corners for header */
#customer-datatable thead tr th:first-child{
  border-top-left-radius: 12px;
}
#customer-datatable thead tr th:last-child{
  border-top-right-radius: 12px;
}

/* DataTables controls (search/length/buttons) */
.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input{
  border: 1px solid #e5e7eb !important;
  border-radius: 10px !important;
  padding: 7px 10px !important;
  outline: none !important;
  box-shadow: none !important;
}

.dataTables_wrapper .dataTables_filter input:focus,
.dataTables_wrapper .dataTables_length select:focus{
  border-color: #2563eb !important;
}

/* Buttons */
.dt-buttons .btn{
  border-radius: 10px !important;
  border: 1px solid #e5e7eb !important;
  padding: 7px 10px !important;
  font-size: 13px !important;
}

/* Pagination modern */
.dataTables_wrapper .pagination{
  gap: 6px;
}
.dataTables_wrapper .page-item .page-link{
  border-radius: 10px !important;
  border: 1px solid #e5e7eb !important;
  color: #344054 !important;
  padding: 6px 10px;
}
.dataTables_wrapper .page-item.active .page-link{
  background: #2563eb !important;
  border-color: #2563eb !important;
  color: #fff !important;
}
.dataTables_wrapper .page-item.disabled .page-link{
  opacity: .6;
}

/* badges (status) if you use) */
.badge{
  padding: 6px 10px;
  border-radius: 999px;
  font-weight: 700;
  font-size: 12px;
}
.bg-success-main{
  background: #e7f8f0 !important;
  color: #067647 !important;
}
.bg-danger-main{
  background: #fdecec !important;
  color: #b42318 !important;
}
</style>


<div class="container-fluid">

  <!-- Filters -->
  <div class="card filter-card mb-3">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
        <h5 class="mb-0">Discount Statement</h5>
        <div class="no-print d-flex gap-2">
          <button class="btn btn-outline-secondary btn-sm" id="btnReset">
            <i class="fa-solid fa-rotate"></i> Reset
          </button>
          <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
            <i class="fa-solid fa-print"></i> Print
          </button>
        </div>
      </div>

      <div class="row g-2 align-items-end">
        <div class="col-md-3">
          <label class="form-label">From</label>
          <input type="date" id="date-from" class="form-control">
        </div>

        <div class="col-md-3">
          <label class="form-label">To</label>
          <input type="date" id="date-to" class="form-control">
        </div>

        <div class="col-md-4">
          <label class="form-label">Entry By</label>
          <select id="entry-by" class="form-control">
            <option value="">All</option>
            <?php foreach($entryUsers as $u){ ?>
              <option value="<?php echo (int)$u['UserId']; ?>">
                <?php echo htmlspecialchars($u['FullName']); ?>
              </option>
            <?php } ?>
          </select>
        </div>

        <div class="col-md-2 no-print">
          <button class="btn btn-primary w-100" id="btnApply">
            <i class="fa-solid fa-filter"></i> Apply
          </button>
        </div>
      </div>

      <div class="mt-2 text-muted small">
        
      </div>
    </div>
  </div>

  <!-- Summary cards -->
  <div class="row g-3 mb-3">
    <div class="col-md-3">
      <div class="card stat-card h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="icon-wrap bg-primary text-white">
            <i class="fa-solid fa-bangladeshi-taka-sign"></i>
          </div>
          <div>
            <div class="text-muted small">Total Discount (Filtered)</div>
            <div class="h5 mb-0"><span id="totalDiscountShow">0</span></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card stat-card h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="icon-wrap bg-success text-white">
            <i class="fa-solid fa-users"></i>
          </div>
          <div>
            <div class="text-muted small">Beneficiaries (Filtered)</div>
            <div class="h5 mb-0"><span id="totalRecipientsShow">0</span></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card stat-card h-100">
        <div class="card-body">
          <div class="text-muted small">Current Filter</div>
          <div class="fw-semibold" id="filterInfo">All data</div>
          <div class="text-muted small mt-1">Sorting: Active first → Amount high → Latest</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Table -->
 <div class="card modern-table-card" id="printArea">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h6 class="mb-0 fw-semibold">Discount Statement</h6>
    <span class="text-muted small">Updated automatically</span>
  </div>

  <div class="card-body ">
    <div class="table-responsive">
      <table id="customer-datatable"
             class="table table-striped bordered-table mb-0 datatable">
        <thead class="table-light">
          <tr>
            <th style="width:60px">#</th>
            <th>Date</th>
            <th>Customer</th>
            <th>Mobile</th>
            <th>Discount</th>
            <th>Description</th>
            <th>Given By</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>


</div>

<?php $obj->start_script(); ?>

<script>
$(function () {

  function updateFilterInfo(){
    const df = $('#date-from').val();
    const dt = $('#date-to').val();
    const entryName = $('#entry-by option:selected').text();
    const entryVal  = $('#entry-by').val();

    let parts = [];
    if(df && dt) parts.push(`Date: ${df} → ${dt}`);
    if(entryVal) parts.push(`Entry By: ${entryName}`);

    $('#filterInfo').text(parts.length ? parts.join(' | ') : 'All data');
  }

  let table = $('#customer-datatable').DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    stateSave: true,
    lengthMenu: [10, 25, 50, 100, 500],
    pageLength: 10,
    pagingType: "full_numbers",

    dom: `<"row"<"col-sm-6"l><"col-sm-6 text-end"f>>` +
         `<"row"<"col-sm-12 text-end"B>>` +
         `<"row"<"col-sm-12"tr>>` +
         `<"row"<"col-sm-5"i><"col-sm-7 text-end"p>>`,

    buttons: [
      { extend:"copy", className:"btn btn-light btn-sm" },
      { extend:"print", className:"btn btn-light btn-sm" },
      { extend:"pdf", className:"btn btn-light btn-sm" }
    ],

    ajax: {
      url: "./pages/bonus/ajax_discount_statement.php",
      type: "GET",
      data: function(d){
        d.datefrom = $('#date-from').val();
        d.dateto   = $('#date-to').val();
        d.entryby  = $('#entry-by').val();
      },
      error: function(xhr){
        console.log("AJAX ERROR:", xhr.status, xhr.responseText);
      }
    },

    columns: [
      { data:'sl', orderable:false },
      { data:'date' },
      { data:'customer_name', render: d => d ? d : 'N/A' },
      { data:'customer_mobile', render: d => d ? d : 'N/A' },
      { data:'amount', render: d => `<span class="fw-semibold text-primary">৳ ${d}</span>` },
      { data:'description', render: d => d ? d : '—' },
      { data:'given_by_name', render: d => d ? d : 'N/A' },
      {
        data:'customer_status',
        render: function(d){
          const ok = (d==1 || d==2);
          return `<span class="badge ${ok?'bg-success':'bg-danger'}">${ok?'Active':'Inactive'}</span>`;
        }
      }
    ],

    order: [[1,'desc']], 

    drawCallback: function(){
      $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
    }
  });

  // server response থেকে totals update
  $('#customer-datatable').on('xhr.dt', function(e, settings, json){
    if(!json) return;
    if(typeof json.totalDiscount !== "undefined") {
      $('#totalDiscountShow').text(json.totalDiscount);
    }
    if(typeof json.totalRecipients !== "undefined") {
      $('#totalRecipientsShow').text(json.totalRecipients);
    }
    updateFilterInfo();
  });

  // Auto reload on change
  $('#date-from, #date-to, #entry-by').on('change', function(){
    table.ajax.reload();
  });

  // Apply button
  $('#btnApply').on('click', function(){
    table.ajax.reload();
  });

  // Reset
  $('#btnReset').on('click', function(){
    $('#date-from').val('');
    $('#date-to').val('');
    $('#entry-by').val('');
    table.state.clear(); 
    table.ajax.reload();
  });

});
</script>

<?php $obj->end_script(); ?>
