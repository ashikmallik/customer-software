<?php
date_default_timezone_set('Asia/Dhaka');

$attendance_date = date('Y-m-d');

// ==================== Status List ====================
$status_list = ['present', 'absent', 'late', 'leave'];

// ==================== Form Submit (Save/Update Attendance) ====================
if (isset($_POST['submit_attendance'])) {

    $employee_id = $_POST['employee_id'] ?? null;
    $status      = $_POST['status'] ?? null;
    $date        = $_POST['attendance_date'] ?? $attendance_date;

    if (!$employee_id || !$status) {
        $obj->notificationStore('Please select employee and status!', 'error');
    } else {

        $condition = "employee_id = '$employee_id' AND attendance_date = '$date'";
        $existing  = $obj->details_by_cond("employee_attendance", $condition);

        $form_data = [
            'employee_id'     => $employee_id,
            'attendance_date' => $date,
            'status'          => $status,
            'month'           => date('M', strtotime($date)),
            'year'            => date('Y', strtotime($date))
        ];

        if ($existing) {
            $obj->Update_data("employee_attendance", $form_data, $condition);
            $obj->notificationStore('Attendance Updated Successfully', 'success');
        } else {
            $obj->Insert_data("employee_attendance", $form_data);
            $obj->notificationStore('Attendance Added Successfully', 'success');
        }

        echo '<script>window.location.href = window.location.href;</script>';
    }
}

// ==================== Report Filter ====================
$report_data   = [];
$filter_month  = $_POST['filter_month']  ?? date('m');
$filter_year   = $_POST['filter_year']   ?? date('Y');
$filter_emp_id = $_POST['filter_employee_id'] ?? '';

if (isset($_POST['generate_report'])) {

    $month_name = date('M', mktime(0, 0, 0, (int)$filter_month, 1));

    $condition = "month = '$month_name' AND year = '$filter_year'";
    if (!empty($filter_emp_id)) {
        $condition .= " AND employee_id = '$filter_emp_id'";
    }

    $report_data = $obj->view_all_by_cond("employee_attendance", $condition, "attendance_date ASC");
}
?>

<div class="row">
    <div class="col-md-12"><?php $obj->notificationShowRedirect(); ?></div>
</div>

<div class="row panel">
    <div class="panel-body col-md-12 bg-teal-800 text-center">
        <h4>Employee Attendance (Present / Absent / Late / Leave)</h4>
    </div>
</div>

<!-- ==================== ATTENDANCE ENTRY FORM ==================== -->
<div class="col-md-8 col-md-offset-2">

    <div class="panel panel-success">
        <div class="panel-heading"><strong>Mark Attendance</strong></div>
        <div class="panel-body">
            <form method="post">

                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="attendance_date" class="form-control"
                           value="<?= $attendance_date ?>" required>
                </div>

                <div class="form-group">
                    <label>Employee Name</label>
                    <select class="form-control" name="employee_id" required>
                        <option value="">Select Employee</option>
                        <?php foreach ($obj->view_all_by_cond("tbl_employee","employee_status=1") as $emp): ?>
                            <option value="<?= $emp['id'] ?>">
                                <?= $emp['employee_name'] ?> - <?= $emp['employee_id'] ?? '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" name="status" required>
                        <option value="">Select Status</option>
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="late">Late</option>
                        <option value="leave">Leave</option>
                    </select>
                </div>

                <button type="submit" name="submit_attendance" class="btn btn-success btn-block">
                    Save / Update Attendance
                </button>

            </form>
        </div>
    </div>

</div>

<!-- ==================== REPORT SECTION ==================== -->
<div class="col-md-12" style="margin-top:30px;">

    <div class="panel panel-primary">
        <div class="panel-heading"><strong>Attendance Report</strong></div>
        <div class="panel-body">

            <form method="post" class="form-inline" style="margin-bottom:20px;">

                <div class="form-group" style="margin-right:10px;">
                    <label style="margin-right:5px;">Employee</label>
                    <select class="form-control" name="filter_employee_id">
                        <option value="">All Employees</option>
                        <?php foreach ($obj->view_all("tbl_employee") as $emp): ?>
                            <option value="<?= $emp['id'] ?>" <?= ($filter_emp_id == $emp['id']) ? 'selected' : '' ?>>
                                <?= $emp['employee_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="margin-right:10px;">
                    <label style="margin-right:5px;">Month</label>
                    <select class="form-control" name="filter_month">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?= sprintf('%02d', $m) ?>" <?= ($filter_month == sprintf('%02d', $m)) ? 'selected' : '' ?>>
                                <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="form-group" style="margin-right:10px;">
                    <label style="margin-right:5px;">Year</label>
                    <select class="form-control" name="filter_year">
                        <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                            <option value="<?= $y ?>" <?= ($filter_year == $y) ? 'selected' : '' ?>>
                                <?= $y ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <button type="submit" name="generate_report" class="btn btn-primary">
                    Generate Report
                </button>
            </form>

            <?php if (isset($_POST['generate_report'])): ?>

                <?php
                    // Summary counts
                    $summary = ['present' => 0, 'absent' => 0, 'late' => 0, 'leave' => 0];
                    foreach ($report_data as $row) {
                        if (isset($summary[$row['status']])) {
                            $summary[$row['status']]++;
                        }
                    }
                ?>

                <div class="row" style="margin-bottom:15px;">
                    <div class="col-md-3">
                        <div class="panel panel-success text-center">
                            <div class="panel-body">
                                <h4><?= $summary['present'] ?></h4>
                                <span>Present</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-danger text-center">
                            <div class="panel-body">
                                <h4><?= $summary['absent'] ?></h4>
                                <span>Absent</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-warning text-center">
                            <div class="panel-body">
                                <h4><?= $summary['late'] ?></h4>
                                <span>Late</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-info text-center">
                            <div class="panel-body">
                                <h4><?= $summary['leave'] ?></h4>
                                <span>Leave</span>
                            </div>
                        </div>
                    </div>
                </div>

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Employee</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($report_data)): ?>
                            <?php $sl = 1; foreach ($report_data as $row): ?>
                                <?php
                                    $emp = $obj->details_by_cond("tbl_employee", "id='{$row['employee_id']}'");
                                    $badge_class = [
                                        'present' => 'label-success',
                                        'absent'  => 'label-danger',
                                        'late'    => 'label-warning',
                                        'leave'   => 'label-info'
                                    ][$row['status']] ?? 'label-default';
                                ?>
                                <tr>
                                    <td><?= $sl++ ?></td>
                                    <td><?= $row['attendance_date'] ?></td>
                                    <td><?= $emp['employee_name'] ?? 'N/A' ?></td>
                                    <td><span class="label <?= $badge_class ?>"><?= ucfirst($row['status']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center">No records found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>

            <?php endif; ?>

        </div>
    </div>

</div>