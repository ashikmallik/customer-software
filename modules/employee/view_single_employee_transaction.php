<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$employeeId = isset($_GET['employeeid']) ? $_GET['employeeid'] : NULL;

if(isset($_POST['search'])){
    $startDate = date('Y-m-d', strtotime($_POST['startDate']));
    $endDate = date('Y-m-d', strtotime($_POST['endDate']));
} else {
    $startDate = date('Y-m-d', strtotime(date('Y').'-'.date('m').'-01' ));
    $endDate = date('Y-m-d');
}

// ========== Delete Function ==========
if (isset($_GET['deletetransactionId']) && !empty($_GET['deletetransactionId']) && !empty($employeeId)) {
    $deleteId = $_GET['deletetransactionId'];
    $deletedData = $obj->details_by_cond('tbl_employee_transaction', "id = $deleteId");
    
    if ($obj->Delete_data('tbl_employee_transaction', "id = $deleteId")) {
        if (!empty($deletedData['accounts_id']) && $deletedData['accounts_id'] != 0) {
            $obj->Delete_data('tbl_account', 'acc_id = ' . $deletedData['accounts_id']);
        }
        $obj->notificationStore('Transaction deleted Successfully', 'success');
    } else {
        $obj->notificationStore('Transaction deleted Failed');
    }
    echo '<script> window.location = "?q=view_single_employee_transaction&employeeid=' . $employeeId . '"; </script>';
}

if (!empty($employeeId)) {
    $employeeData = $obj->details_by_cond("tbl_employee", "id = $employeeId");
    
    $allEmployeeTransactionData = $obj->view_all_by_cond(
        "tbl_employee_transaction", 
        "employee_id = $employeeId AND (created_at >= '$startDate' AND created_at <= '$endDate') 
         ORDER BY created_at DESC"
    );
}
?>

<div class="row">
    <div class="col-md-12 padding_5_px">
        <?php $obj->notificationShowRedirect(); ?>
    </div>
</div>

<div class="col-md-12 bg-slate-700" style="margin-top:20px; margin-bottom: 15px;">
    <h4 class="col-md-9">
        <strong>View Transaction of Employee: <?php echo $employeeData['employee_name']; ?></strong>
    </h4>
    <a class="btn btn-primary btn-sm pull-right" 
       href="?q=print_single_employee_transaction&employeeid=<?php echo $employeeId; ?>&start=<?php echo $startDate; ?>&end=<?php echo $endDate; ?>" 
       target="_blank">
        Print Transaction
    </a>
</div>

<div class="col-md-12 bg-slate-400" style="margin-bottom: 20px;padding:5px 0px">
    <form action="" method="POST">
        <div class="col-md-5">
            <div class="form-group">
                <label class="control-label col-sm-5" style="padding-top:5px">Start Date</label>
                <div class="col-sm-7">
                    <input class="form-control" required value="<?php echo date('d-m-Y', strtotime($startDate)) ?>" 
                           type="text" name="startDate" autocomplete="off">
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label class="control-label col-sm-5" style="padding-top:5px">End Date</label>
                <div class="col-sm-7">
                    <input class="form-control" required value="<?php echo date('d-m-Y', strtotime($endDate)) ?>" 
                           type="text" name="endDate" autocomplete="off">
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <button type="submit" name="search" class="btn btn-default">
                <span class="glyphicon glyphicon-search"></span>&nbsp;Search
            </button>
        </div>
    </form>
</div>

<div id="print_transaction" class="row" style="font-size: 12px;">
    <div class="col-md-12">
        <div class="table-responsive">
            <!-- Employee Info -->
            <table class="table table-bordered table-hover table-striped">
                <tr class="bg-teal-800">
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Designation</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Joining Date</th>
                </tr>
                <tr>
                    <td class="text-center"><?php echo $employeeData['employee_id'] ?? ''; ?></td>
                    <td class="text-center"><?php echo $employeeData['employee_name'] ?? ''; ?></td>
                    <td class="text-center"><?php echo $employeeData['designation'] ?? ''; ?></td>
                    <td class="text-center"><?php echo $employeeData['employee_mobile_no'] ?? ''; ?></td>
                    <td class="text-center"><?php echo $employeeData['employee_email'] ?? ''; ?></td>
                    <td><?php echo $employeeData['employee_address'] ?? ''; ?></td>
                    <td class="text-center"><?php echo !empty($employeeData['joining_date']) ? date('d-m-Y', strtotime($employeeData['joining_date'])) : ''; ?></td>
                </tr>
            </table>

            <!-- Transaction Table -->
            <table class="table table-bordered table-hover table-striped" id="datatable">
                <thead class="bg-teal-800">
                <tr>
                    <th class="text-center">SL</th>
                    <th class="text-center">Date</th>
                    <th class="text-center">Salary Amount</th>
                    <th class="text-center">Bonus</th>
                    <th class="text-center">Deduct</th>
                    <th class="text-center">Received Amount</th>
                    <th class="text-center">Provident Fund</th>
                    <th class="text-center">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = 0;
                $totalSalary = 0;
                $totalConveyance = 0;
                $totalReceived = 0;
                $totalPunishment = 0;
                $totalprovidant = 0;

                foreach ($allEmployeeTransactionData as $transection) {
                    $i++;
                ?>
                    <tr>
                        <td class="text-center"><?php echo $i; ?></td>
                        <td class="text-center">
                            <?php echo !empty($transection['created_at']) ? date('d-M-Y', strtotime($transection['created_at'])) : ''; ?>
                        </td>
                        <td class="text-right">
                            <?php 
                                if ($transection['received_due'] == 0) {
                                    $totalSalary += $transection['salary_amount'];
                                    echo number_format($transection['salary_amount']);
                                } else {
                                    echo 0;
                                }
                            ?>
                        </td>
                        <td class="text-right">
                            <?php 
                                if ($transection['received_due'] == 0) {
                                    $totalConveyance += $transection['conveyance'];
                                    echo number_format($transection['conveyance']);
                                } else {
                                    echo 0;
                                }
                            ?>
                        </td>
                        <td class="text-right">
                            <?php 
                                if ($transection['received_due'] == 0) {
                                    $totalPunishment += $transection['punishment'];
                                    echo number_format($transection['punishment']);
                                } else {
                                    echo 0;
                                }
                            ?>
                        </td>
                        <td class="text-right">
                            <?php 
                                if ($transection['received_due'] == 1) {
                                    $totalReceived += $transection['received_amount'];
                                    echo number_format($transection['received_amount']);
                                } else {
                                    echo 0;
                                }
                            ?>
                        </td>
                        <td class="text-center"><?php
                                    $totalprovidant += $transection['provident_fund'];
                                    echo $transection['provident_fund']; ?>
                        </td>
                        <td class="text-center">
                            <a onclick="return confirm('Delete this transaction?');" 
                               href="?q=view_single_employee_transaction&employeeid=<?php echo $employeeId; ?>&deletetransactionId=<?php echo $transection['id']; ?>" 
                               class="btn btn-danger btn-xs">
                                <span class="glyphicon glyphicon-trash"></span> Delete
                            </a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
                <tfoot>
                <tr class="bg-teal-800">
                    <th colspan="2" class="text-center">Total</th>
                    <th class="text-right"><?php echo number_format($totalSalary); ?></th>
                    <th class="text-right"><?php echo number_format($totalConveyance); ?></th>
                    <th class="text-right"><?php echo number_format($totalPunishment); ?></th>
                    <th class="text-right"><?php echo number_format($totalReceived); ?></th>
                    <th class="text-right"><?php echo number_format($totalprovidant); ?></th>
                    <th></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('input[name="startDate"], input[name="endDate"]').datepicker({
        autoclose: true,
        format: 'dd-mm-yyyy'
    });
});
</script>