<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

if(isset($_POST['search'])){
    $startDate = date('Y-m-d', strtotime($_POST['startDate']));
    $endDate = date('Y-m-d', strtotime($_POST['endDate']));
} else {
    $startDate = date('Y-m-d', strtotime(date('Y').'-'.date('m').'-01' ));
    $endDate = date('Y-m-d');
}

// Updated query - only necessary fields + employee info
$allEmployeeTransaction = $obj->view_selected_field_by_cond_left_join(
    "tbl_employee_transaction", 
    'tbl_employee', 
    'employee_id', 
    'id', 
    'SUM(tbl_employee_transaction.salary_amount) as total_salary_amount,
     SUM(tbl_employee_transaction.conveyance) as total_conveyance,
     SUM(tbl_employee_transaction.received_amount) as total_received_amount,
     SUM(tbl_employee_transaction.provident_fund) as total_pf,
     SUM(tbl_employee_transaction.punishment) as total_punishment',
     
    '*', 
    "tbl_employee_transaction.id != 0 AND (created_at >= '$startDate' AND created_at <= '$endDate') 
     GROUP BY tbl_employee_transaction.employee_id"
);

$employeeSalaryReceiveType = 1;
$company_give_payment_to_employee = 1;



if (isset($_POST['submit_employee_payment'])) {
    $employee_data = $obj->details_by_cond("tbl_employee", "id = " . $_POST['employee_id']);
    // var_dump($employee_data);exit;

    if (!empty($_POST['employee_payment'])) {
        $form_tbl_accounts = array(
            'acc_description' => "Company give payment to Employee - " . $employee_data['employee_name'] . ". " . $_POST['description'],
            'acc_amount' => $_POST['employee_payment'],
            'acc_type' => $company_give_payment_to_employee,
            'acc_head' => $_POST['account_head'],
            'entry_by' => $userid,
            'entry_date' => date('Y-m-d'),
            'update_by' => $userid
        );
        $tbl_accounts_add = $obj->Insert_data("tbl_account", $form_tbl_accounts);
    }

    // Simplified insert - only allowed fields
    $form_tbl_employee_transaction = array(
        'employee_id'     => $_POST['employee_id'],
        'received_amount' => $_POST['employee_payment'] ?? 0,
        'received_due'    => $employeeSalaryReceiveType,
        'accounts_id'     => !empty($_POST['employee_payment']) ? $tbl_accounts_add : 0,
        'created_at'      => date('Y-m-d'),
    );

    if ($obj->Insert_data("tbl_employee_transaction", $form_tbl_employee_transaction)) {
        $cmobile = $employee_data['employee_mobile_no'];
        $cname   = $employee_data['employee_name'];
        $salary  = $_POST['employee_payment'];

        $smsbody = "Dear $cname, You have been received $salary Taka from monthly Salary";
        $mobilenum = "88" . $cmobile;
        // $obj->sendsms($smsbody, $mobilenum);

        $obj->notificationStore('Employee Payment Stored Successfully', 'success');
        echo '<script>window.location.href=window.location.href;</script>';
    } else {
        $obj->notificationStore('Employee Payment Stored Failed');
        echo '<script>window.location.href=window.location.href;</script>';
    }
}
?>

<style>
    .select2-container {
    width: 360px !important;
}
</style>


<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap.min.css">

<div class="row">
    <div class="col-md-12 padding_5_px">
        <?php $obj->notificationShowRedirect(); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12 bg-teal-800">
        <h4 class="col-md-9">View Employee's Transaction</h4>
    </div>
</div>

<div class="col-md-12 bg-slate-400" style="margin-bottom: 20px;padding:5px 0px">
    <form action="" method="POST">
        <div class="col-md-5">
            <div class="form-group">
                <label class="control-label col-sm-5" style="padding-top:5px">Select Start Date</label>
                <div class="col-sm-7">
                    <input class="form-control" required value="<?php echo date('d-m-Y', strtotime($startDate)) ?>" 
                           type="text" name="startDate" autocomplete="off">
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label class="control-label col-sm-5" style="padding-top:5px">Select End Date</label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" value="<?php echo date('d-m-Y', strtotime($endDate)) ?>" 
                           required name="endDate" autocomplete="off">
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <button type="submit" name="search" class="btn btn-default">
                <span class="glyphicon glyphicon-search"></span>&nbsp;&nbsp;Search
            </button>
        </div>
    </form>
</div>

<hr>

<div class="row" style="font-size:12px;">
    <div class="col-md-12">
        <table class="table table-responsive table-bordered table-hover" id="employeeTransactionTable">
            <thead>
            <tr class="bg-teal-800">
                <th class="col-md-1">SL</th>
                <th class="col-md-2">Employee Name</th>
                <th class="col-md-1">Mobile No</th>
                <th class="col-md-2">Address</th>
                <th class="col-md-1">Total Salary</th>
                <th class="col-md-1">Bonus</th>
                <th class="col-md-1">Deduct</th>
                <th class="col-md-1">Total Received</th>
                <th class="col-md-1">Provident Fund</th>
                <th class="col-md-1">Due / Advance</th>
                <th class="col-md-1">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php 
            $i = 0;
            $total_salary = 0; 
            $total_conveyance = 0; 
            $total_received = 0; 
            $total_pf = 0;
            $total_punishment = 0; 
            $total_due = 0;

            foreach ($allEmployeeTransaction as $emp) { 
                $total_salary     += $emp['total_salary_amount'];
                $total_conveyance += $emp['total_conveyance'];
                $total_received   += $emp['total_received_amount'];
                $total_pf         += $emp['total_pf'];
                $total_punishment += $emp['total_punishment'];

                $due = ($emp['total_salary_amount'])- ($emp['total_received_amount']);
                $total_due += $due;
            ?>
                <tr>
                    <td class="text-center"><?php echo ++$i; ?></td>
                    <td><a href="?q=view_single_employee_transaction&employeeid=<?php echo $emp['id']; ?>">
                        <?php echo $emp['employee_name']; ?>
                    </a></td>
                    <td class="text-center"><?php echo $emp['employee_mobile_no']; ?></td>
                    <td><?php echo $emp['employee_address']; ?></td>
                    <td class="text-right"><?php echo number_format($emp['total_salary_amount']); ?></td>
                    <td class="text-right"><?php echo number_format($emp['total_conveyance']); ?></td>
                    <td class="text-right"><?php echo number_format($emp['total_punishment']); ?></td>
                    <td class="text-right"><?php echo number_format($emp['total_received_amount']); ?></td>
                    <td class="text-right"><?php echo number_format($emp['total_pf']); ?></td>
                    <?php 
                    $due = ($emp['total_salary_amount']) - ($emp['total_received_amount']);
                    // $total_due += $due;
                    ?>
                        <td class="text-right">
                            <?php echo ($due < 0) ? '(Advance) ' : ''; echo number_format(abs($due)); ?>
                        </td>
                        <td class="text-center">
                            <button class="edit btn btn-success btn-sm" data-toggle="modal" 
                                    data-target="#employee_payment"
                                    data-id="<?php echo $emp['id']; ?>" 
                                    data-employee="<?php echo $emp['employee_name']; ?>"
                                    data-due="<?php echo $due; ?>">
                                Payment
                            </button>
                        </td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr>
                <th colspan="4" class="text-center">Total</th>
                <th class="text-right"><?php echo number_format($total_salary); ?></th>
                <th class="text-right"><?php echo number_format($total_conveyance); ?></th>
                <th class="text-right"><?php echo number_format($total_punishment); ?></th>
                <th class="text-right"><?php echo number_format($total_received); ?></th>
                <th class="text-right"><?php echo number_format($total_pf); ?></th>
                <th class="text-right"><?php echo number_format($total_due); ?></th>
                <th></th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Print Version -->
<div class="row" style="font-size:12px; display: none" id="print_transaction">
    <!-- Similar simplified print table (you can copy and adjust if needed) -->
</div>

<!-- Payment Modal (Simplified) -->
<div class="modal fade" id="employee_payment" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="width:600px">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Payment for <span id="person_name"></span></h4>
            </div>
            <form id="employee" action="" method="post" class="form-horizontal">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="customer_id">Account Head :</label>
                        <div class="col-sm-8">
                            <select class="form-control" required="required" name="account_head" id="status">
                                <option value="">Select One</option>
                                <?php foreach($obj->view_all("tbl_accounts_head") as $item){ ?>
                                <option value="<?php echo $item['acc_id']; ?>">
                                                <?php echo $item['acc_name']; ?>
                                </option>
                                <?php } ?>
                            </select>           
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Pay Amount</label>
                        <div class="col-sm-8">
                            <input type="number" name="employee_payment" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Description</label>
                        <div class="col-sm-8">
                            <textarea name="description" rows="4" class="form-control"></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="employee_id" value="">
                </div>
                <div class="modal-footer">
                    <button type="submit" name="submit_employee_payment" class="btn btn-primary">Save Payment</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ==================== DataTables Scripts ==================== -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>


$(document).ready(function () {

    $('#employeeTransactionTable').DataTable({
        "pageLength": 20,
        "lengthMenu": [[20, 100, 500, -1], [20, 100, 500, "All"]],
        dom: 'lBfrtip',
        buttons: [
            {
                extend: 'print',
                text: 'Export Print',
                footer: true,
                exportOptions: { columns: [0,1,2,3,4,5,6,7,8,9] }
            },
            {
                extend: 'excel',
                text: 'Export Excel',
                footer: true,
                exportOptions: { columns: [0,1,2,3,4,5,6,7,8,9] }
            },
            {
                extend: 'csv',
                text: 'Export CSV',
                footer: true,
                exportOptions: { columns: [0,1,2,3,4,5,6,7,8,9] }
            }
        ],
        "columnDefs": [
            { "orderable": false, "targets": 10 }   // Disable sorting on Action column
        ]
    });



    $('input[name="startDate"], input[name="endDate"]').datepicker({
        autoclose: true,
        format: 'dd-mm-yyyy'
    });
    
    $('#employeeTransactionTable').on('click', 'button.edit', function () {
        var id = $(this).data('id');
        var name = $(this).data('employee');
        var due = $(this).data('due');
        
        $('#person_name').text(name);
        $('input[name="employee_id"]').val(id);
    
        // Due positive হলে ওটাই বসবে, Advance (negative) হলে input খালি/0 থাকবে
        $('input[name="employee_payment"]').val(due > 0 ? due : '');
        
        console.log('Employee ID:', id);
    });

     $('#employeeTransactionTable').on('click', 'button.edit', function () {
        var id = $(this).data('id');
        var name = $(this).data('employee');
        
        $('#person_name').text(name);
        $('input[name="employee_id"]').val(id);
        
        console.log('Employee ID:', id); // ржбрж┐ржмрж╛ржЧ ржХрж░рж╛рж░ ржЬржирзНржп
    });

    $('#employee_payment').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
    });
});
</script>