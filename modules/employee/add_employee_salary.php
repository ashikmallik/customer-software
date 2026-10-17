<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$current_month = date('Y-m'); // running month

//===================Update Function===================

if (isset($_POST['update_salary'])) {

    $transaction_id  = $_POST['transaction_id'];
    $basic_salary    = (float)$_POST['basic_salary'];
    $pf              = (float)$_POST['provident_fund'];
    $bonus           = (float)$_POST['conveyance'];
    $deduct          = (float)$_POST['punishment'];

    // Net Salary = Basic Salary - PF + Bonus - Deduct
    $netSalary = $basic_salary - $pf + $bonus - $deduct;

    // ==== Security Check: শুধু চলতি মাসের transaction-ই update হবে ====
    $check_query = "SELECT id FROM tbl_employee_transaction 
                     WHERE id = '$transaction_id' 
                     AND DATE_FORMAT(created_at, '%Y-%m') = '$current_month'";
    $check_result = $obj->raw_sql($check_query);

    if (count($check_result) > 0) {

        $form_tbl_employee_transaction = array(
                                                'provident_fund'   => $pf,
                                                'conveyance'        => $bonus,
                                                'punishment'        => $deduct,
                                                'salary_amount'     => $netSalary,
                                                'update_by'         => $userid,
                                            );

        if ($obj->Update_data("tbl_employee_transaction", $form_tbl_employee_transaction, "id = '$transaction_id'")) {

            $obj->notificationStore('Employee Salary Updated Successfully', 'success');
            echo '<script>window.location.href=window.location.href;</script>';
        } else {

            $obj->notificationStore('Employee Salary Update Failed');
            $notification = '<div class="alert alert-danger">Update Failed</div>';
        }
    } else {
        $obj->notificationStore('Only Current Month Salary Can Be Edited');
        echo '<script>window.location.href=window.location.href;</script>';
    }
}
?>

<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>

<script>
    function numbersOnly(e) {
        var unicode = e.charCode ? e.charCode : e.keyCode
        if (unicode != 8) {
            if ((unicode < 2534 || unicode > 2543) && (unicode < 46 || unicode > 57)) {
                return false;
            } else if (unicode == 47) {
                return false;
            }
        }
    }
</script>

<div class="row">
    <div class="col-md-12 bg-grey-800 text-center">
        <h4>Edit Employee Salary (Deduct / Bonus / Provident Fund) - <?php echo date('F Y'); ?></h4>
    </div>
</div>
<div class="row">
    <div class="col-md-12 padding_5_px">
        <?php $obj->notificationShowRedirect(); ?>
    </div>
</div>
<hr>

<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-striped" id="salary_table" style="font-size: 12px;">
            <thead>
            <tr>
                <th>#</th>
                <th>Employee</th>
                <th>Basic Salary</th>
                <th>Provident Fund</th>
                <th>Bonus</th>
                <th>Deduct</th>
                <th>Net Salary</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $sl = 0;
            $query = "SELECT t.*, e.employee_name, e.employee_id AS emp_code, e.em_salary
                       FROM tbl_employee_transaction t
                       LEFT JOIN tbl_employee e ON e.id = t.employee_Id
                       WHERE DATE_FORMAT(t.created_at, '%Y-%m') = '$current_month'
                       ORDER BY t.id DESC";
            foreach ($obj->raw_sql($query) as $row) {
                $sl++;
                ?>
                <tr>
                    <td><?php echo $sl; ?></td>
                    <td><?php echo $row['employee_name']; ?> - <?php echo $row['emp_code']; ?></td>
                    <td><?php echo number_format($row['em_salary'], 2); ?></td>
                    <td><?php echo number_format($row['provident_fund'], 2); ?></td>
                    <td><?php echo number_format($row['conveyance'], 2); ?></td>
                    <td><?php echo number_format($row['punishment'], 2); ?></td>
                    <td><strong><?php echo number_format($row['salary_amount'], 2); ?></strong></td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td>
                        <button type="button" class="btn btn-xs btn-primary"
                                data-toggle="modal"
                                data-target="#editModal<?php echo $row['id']; ?>">
                            Edit
                        </button>
                    </td>
                </tr>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form method="post">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Edit Salary - <?php echo $row['employee_name']; ?></h4>
                                </div>
                                <div class="modal-body">

                                    <input type="hidden" name="transaction_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="basic_salary" value="<?php echo $row['em_salary']; ?>">

                                    <div class="form-group">
                                        <label>Basic Salary (tk):</label>
                                        <input type="text" class="form-control"
                                               value="<?php echo number_format($row['em_salary'], 2); ?>" disabled>
                                    </div>

                                    <div class="form-group">
                                        <label>Provident Fund Amount (tk):</label>
                                        <input type="text" style="height:32px"
                                               onkeypress="return numbersOnly(event)"
                                               name="provident_fund" class="form-control"
                                               value="<?php echo $row['provident_fund']; ?>">
                                    </div>

                                    <div class="form-group">
                                        <label>Bonus Amount (tk):</label>
                                        <input type="text" style="height:32px"
                                               onkeypress="return numbersOnly(event)"
                                               name="conveyance" class="form-control"
                                               value="<?php echo $row['conveyance']; ?>">
                                    </div>

                                    <div class="form-group">
                                        <label>Deduct Amount (tk):</label>
                                        <input type="text" style="height:32px"
                                               onkeypress="return numbersOnly(event)"
                                               name="punishment" class="form-control"
                                               value="<?php echo $row['punishment']; ?>">
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="submit" name="update_salary" class="btn btn-success">Update Salary</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#salary_table').DataTable();
    });
</script>