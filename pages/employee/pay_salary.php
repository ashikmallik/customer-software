<?php

date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$obj->rawSqlSingle("CALL generate_monthly_salary('$date')");

// Salary SMS template (status 10) & on/off
$salarySmsInfo = $obj->details_by_cond("sms", "status='10'");
$salarySmsEnable = !empty($salarySmsInfo['smsbody']) && ($salarySmsInfo['sms_enable'] ?? 1) == 1;

if (isset($_POST['submit_employee_payment'])) {
    // echo $_POST['employee_id'];
    // exit;

    $employeeId = $_POST['employee_id'];

    $employeData  =  $obj->getSingleData("tbl_employee", ['where' => ['id', '=', $employeeId]]);
    // var_dump($employeData);exit;
    $employeData['employee_id'];
    $employeData['employee_name'];


    $employe_descriptionn = $_POST['employee_payment'];

    if (!empty($_POST['employee_payment'])) {

        $form_tbl_accounts = array(
            // 'acc_description' => "Company give payment to Employee " . $employeData['employee_name'] . " Employee id " . $employeData['employee_id'],
            'acc_description' => $_POST['description'],
            'acc_amount' => $_POST['employee_payment'],
            'acc_type' => 1,
            // 'agent_id' => $_POST['employee_id'],
            'acc_head' => $employeeId,
            // 'acc_sub_head' => $employeeId,
            'entry_by' => $userId,
            'entry_date' => date('Y-m-d'),
            'update_by' => $userId
        );
        $tbl_accounts_add = $obj->Insert_data("tbl_account", $form_tbl_accounts, " ");
        //  var_dump($form_tbl_accounts);
        //   exit;
    }

    $form_tbl_employee_transaction = array(
        'employee_id' => $_POST['employee_id'],
        'salary_amount' => $_POST['employee_conveyance'],
        'conveyance' => $_POST['employee_conveyance'],
        'received_amount' => $_POST['employee_payment'],
        'received_due' => 1,
        'punishment' => $_POST['employee_punishment'],
        'accounts_id' => !empty($_POST['employee_payment']) ? $tbl_accounts_add : 0,
        'created_at' => date('Y-m-d'),
    );

    if ($obj->Insert_data("tbl_employee_transaction", $form_tbl_employee_transaction)) {

        // Salary SMS send
        if ($salarySmsEnable && isset($_POST['smssend']) && $_POST['smssend'] == 'smssend' && !empty($employeData['employee_mobile_no'])) {
            $mobile = "88" . strval($employeData['employee_mobile_no']);

            $totals = $obj->rawSqlSingle("SELECT SUM(salary_amount) as total_salary, SUM(received_amount) as total_paid, SUM(punishment) as total_punishment FROM tbl_employee_transaction WHERE employee_id = '$employeeId'");
            $totalSalary = $totals['total_salary'] ?? 0;
            $totalPaid = $totals['total_paid'] ?? 0;
            $totalPunishment = $totals['total_punishment'] ?? 0;

            $var_replacement = [
                '{EMPLOYEE_NAME}' => $employeData['employee_name'] ?? '',
                '{EMPLOYEE_ID}' => $employeData['id'] ?? $employeeId,
                '{MOBILE_NO}' => $employeData['employee_mobile_no'] ?? '',
                '{EMAIL}' => $employeData['employee_email'] ?? '',
                '{ADDRESS}' => $employeData['employee_address'] ?? '',
                '{PAID_AMOUNT}' => $_POST['employee_payment'],
                '{CONVEYANCE}' => $_POST['employee_conveyance'],
                '{PUNISHMENT}' => $_POST['employee_punishment'],
                '{DESCRIPTION}' => $_POST['description'],
                '{TOTAL_SALARY}' => $totalSalary,
                '{TOTAL_PAID}' => $totalPaid,
                '{TOTAL_PUNISHMENT}' => $totalPunishment,
                '{DUE_AMOUNT}' => $totalSalary - ($totalPaid + $totalPunishment),
                '{PAID_DAY}' => date('d'),
                '{PAID_MONTH}' => date('F'),
                '{PAID_YEAR}' => date('Y'),
                '{PAID_DATE}' => date('Y-m-d'),
            ];

            $sms_b = strtr($salarySmsInfo['smsbody'], $var_replacement);
            $obj->sendsms($mobile, $sms_b);
        }

        $obj->notificationStore('Employee Payment Stored Successfully ', 'success');
        echo '<script>window.location.href=window.location.href;</script>';
    } else {
        $obj->notificationStore('Employee Payment Stored Failed ');
        echo '<script>window.location.href=window.location.href;</script>';
    }
}


?>

<div class="col-md-12">
    <div class="card basic-data-table">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
            <h5 class="card-title mb-0">Account Statement</h5>
        </div>


        <div class="card-body table-responsive">
            <div class="row gy-3" style="margin-bottom: 16px;">
                <div class="col-sm-2">
                    <label for="date-from" class="form-label">From Date</label>
                    <div class="position-relative">
                        <input type="date" id="date-from" value="<?php echo date('Y-m-01') ?>" class="form-control">
                    </div>
                </div>
                <div class="col-sm-2">
                    <label for="date-to" class="form-label">To Date</label>
                    <div class="position-relative">
                        <input type="date" id="date-to" value="<?php echo date('Y-m-d') ?>" class="form-control">
                    </div>
                </div>
            </div>

            <!-- Summary Cards (date filter onujayi) -->
            <?php
            $summaryCards = [
                ['id' => 'sum-salary', 'title' => 'Total Salary', 'icon' => 'mdi:cash-multiple', 'bg' => 'bg-gradient-start-1', 'iconBg' => 'bg-primary-600'],
                ['id' => 'sum-conveyance', 'title' => 'Total Conveyance', 'icon' => 'mdi:car', 'bg' => 'bg-gradient-start-2', 'iconBg' => 'bg-purple'],
                ['id' => 'sum-received', 'title' => 'Total Received (Paid)', 'icon' => 'mdi:cash-check', 'bg' => 'bg-gradient-start-4', 'iconBg' => 'bg-success-main'],
                ['id' => 'sum-punishment', 'title' => 'Total Punishment', 'icon' => 'mdi:alert-circle', 'bg' => 'bg-gradient-start-3', 'iconBg' => 'bg-warning-main'],
                ['id' => 'sum-due', 'title' => 'Total Due (Need to Pay)', 'icon' => 'mdi:cash-clock', 'bg' => 'bg-gradient-start-5', 'iconBg' => 'bg-danger-main'],
            ];
            ?>
            <div class="row gy-3 mb-4">
                <?php foreach ($summaryCards as $card) { ?>
                    <div class="col-xl col-sm-6">
                        <div class="card p-3 shadow-none radius-8 border h-100 <?= $card['bg'] ?>">
                            <div class="card-body p-0">
                                <div class="d-flex align-items-center justify-content-between gap-2">
                                    <span class="w-48-px h-48-px <?= $card['iconBg'] ?> flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6 mb-0">
                                        <iconify-icon icon="<?= $card['icon'] ?>" class="icon"></iconify-icon>
                                    </span>
                                    <div class="text-end flex-grow-1">
                                        <h5 class="fw-semibold mb-1"><span id="<?= $card['id'] ?>">0</span> ৳</h5>
                                        <p class="mb-0 fw-medium text-secondary-light text-sm"><?= $card['title'] ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <table id="employeeTable" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        <th>Mobile No</th>
                        <th>Address</th>
                        <th>Total Salary</th>
                        <th>Total Conveyance</th>
                        <th>Total Received</th>
                        <th>Total Punishment</th>
                        <th>Employee Due</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="con-close-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Pay Employee Salary</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="employee" action="" method="post">
                    <div class="modal-body">

                        <!-- Payment Amount -->
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label">Payment Amount</label>
                            <div class="col-sm-8">
                                <div class="input-group">

                                    <input required type="number" name="employee_payment" class="form-control" value="0" onkeypress="return numbersOnly(event)">
                                    <span class="input-group-text">Taka</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label">Conveyance Amount</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="number" name="employee_conveyance" class="form-control" value="0" onkeypress="return numbersOnly(event)">
                                    <span class="input-group-text">Taka</span>
                                </div>
                            </div>
                        </div>
                        <!-- Punishment Amount -->
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label">Punishment Amount</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="number" name="employee_punishment" class="form-control" value="0" onkeypress="return numbersOnly(event)">
                                    <span class="input-group-text">Taka</span>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label">Description</label>
                            <div class="col-sm-8">
                                <textarea name="description" id="description" rows="4" class="form-control"></textarea>
                            </div>
                        </div>

                        <!-- SMS Send -->
                        <?php if ($salarySmsEnable) { ?>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">Send SMS</label>
                                <div class="col-sm-8 d-flex align-items-center">
                                    <input type="checkbox" name="smssend" value="smssend" class="form-check-input" checked>
                                </div>
                            </div>
                        <?php } ?>

                        <input type="hidden" name="employee_id" id="employee_id">

                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="submit_employee_payment" class="btn btn-primary">Employee Payment</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div><!-- /.modal -->


<?php $obj->start_script(); ?>
<script>
    $(document).ready(function() {
        let table = $('#employeeTable').DataTable({
            ajax: {
                url: "./pages/employee/employee_transaction_ajax.php",
                type: "GET",
                data: function(d) {
                    d.date = $('#date-from').val() + '/' + $('#date-to').val();
                    console.log("Sending Date: ", d.date);
                },
                dataSrc: function(json) {
                    let t = json.totals || {};
                    let fmt = n => Number(n || 0).toLocaleString('en-US');
                    $('#sum-salary').text(fmt(t.salary));
                    $('#sum-conveyance').text(fmt(t.conveyance));
                    $('#sum-received').text(fmt(t.received));
                    $('#sum-punishment').text(fmt(t.punishment));
                    $('#sum-due').text((t.due < 0 ? '(Advance) ' : '') + fmt(Math.abs(t.due || 0)));
                    return json.data;
                }
            },
            columns: [{
                    data: 'employee_id'
                },
                {
                    data: 'employee_name'
                },
                {
                    data: 'employee_mobile_no'
                },
                {
                    data: 'employee_address'
                },
                {
                    data: 'total_salary_amount'
                },
                {
                    data: 'total_conveyance'
                },
                {
                    data: 'total_received_amount'
                },
                {
                    data: 'total_punishment'
                },
                {
                    data: 'employee_due'
                },
                {
                    data: 'action'
                }
            ]
        });

        $('table#employeeTable').on('click', 'button.edit', function() {
            let id = $(this).data('id');
            console.log(id);
            let name = $(this).data('employee');
            $('#employee_id').val(id);
            $('#exampleModalLabel').text("Pay Salary to " + name);
        });

        $('#date-from, #date-to').on('change', function() {
            table.ajax.reload();
        });

        $('table#employeeTable').on('click', 'button.edit', function() {


            //daynamic discrioption 
            let id = $(this).data('id');
            let name = $(this).data('employee');
            let employeeId = $(this).data('employeeid');

            $('#employee_id').val(id);

            $('#exampleModalLabel').text("Pay Salary to " + name);

            // Dynamic Description
            $('#description').val(
                "Company give payment to Employee " +
                name +
                " Employee ID " +
                employeeId
            );
        });

        //daynamic discrioption end
    });
</script>



<?php $obj->end_script(); ?>