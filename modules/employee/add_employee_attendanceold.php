<?php
date_default_timezone_set('Asia/Dhaka');

// ==================== Office Settings ====================
$settings = $obj->details_by_cond("office_settings", "id=1");
$office_start   = $settings['start_time'] ?? '09:00:00';
$office_end     = $settings['end_time'] ?? '18:00:00';
$late_threshold = $settings['late_threshold_minutes'] ?? 15;

$attendance_date = date('Y-m-d');

// ==================== Calculate Function ====================
function calculateAttendance($in_time, $out_time, $office_start, $office_end, $late_threshold) {
    $data = [
        'status' => 'absent',
        'late_minutes' => 0,
        'early_leave_minutes' => 0,
        'worked_hours' => 0.00
    ];

    if (empty($in_time)) return $data;

    $in    = new DateTime($in_time);
    $start = new DateTime($office_start);
    $end   = new DateTime($office_end);

    // Late Calculation
    if ($in > $start) {
        $late = $in->diff($start);
        $data['late_minutes'] = ($late->h * 60) + $late->i;
        $data['status'] = ($data['late_minutes'] >= $late_threshold) ? 'late' : 'present';
    } else {
        $data['status'] = 'present';
    }

    // Out Time Calculation
    if (!empty($out_time)) {
        $out = new DateTime($out_time);
        
        // Early Leave
        if ($out < $end) {
            $early = $end->diff($out);
            $data['early_leave_minutes'] = ($early->h * 60) + $early->i;
        }

        // Worked Hours
        $interval = $in->diff($out);
        $data['worked_hours'] = round($interval->h + ($interval->i / 60), 2);
    }

    // Half Day Check
    if ($data['worked_hours'] > 0 && $data['worked_hours'] < 4) {
        $data['status'] = 'half_day';
    }

    return $data;
}

// ==================== Form Submit ====================
if (isset($_POST['submit_in']) || isset($_POST['submit_out'])) {
    
    $employee_id = $_POST['employee_id'] ?? null;
    
    if (!$employee_id) {
        $obj->notificationStore('Please select employee!', 'error');
    } else {
        
        $condition = "employee_id = '$employee_id' AND attendance_date = '$attendance_date'";
        $existing = $obj->details_by_cond("employee_attendance", $condition);

        // Existing values রাখার জন্য
        $in_time  = $existing ? $existing['in_time']  : null;
        $out_time = $existing ? $existing['out_time'] : null;

        // নতুন যেটা সাবমিট হয়েছে সেটা আপডেট করো
        if (isset($_POST['submit_in']) && !empty($_POST['in_time'])) {
            $in_time = $_POST['in_time'];
        }
        if (isset($_POST['submit_out']) && !empty($_POST['out_time'])) {
            $out_time = $_POST['out_time'];
        }

        $calc = calculateAttendance($in_time, $out_time, $office_start, $office_end, $late_threshold);

        $form_data = [
            'employee_id'         => $employee_id,
            'attendance_date'     => $attendance_date,
            'in_time'             => $in_time,
            'out_time'            => $out_time,
            'status'              => $calc['status'],
            'late_minutes'        => $calc['late_minutes'],
            'early_leave_minutes' => $calc['early_leave_minutes'],
            'worked_hours'        => $calc['worked_hours'],
            'month'               => date('M'),
            'year'                => date('Y')
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
?>

<div class="row">
    <div class="col-md-12"><?php $obj->notificationShowRedirect(); ?></div>
</div>

<div class="row panel">
    <div class="panel-body col-md-12 bg-teal-800 text-center">
        <h4>Automated Employee Attendance</h4>
    </div>
</div>

<div class="col-md-8 col-md-offset-2">

    <form method="post">
        
        <div class="form-group">
            <label>Employee Name</label>
            <select class="form-control" name="employee_id" required>
                <option value="">Select Employee</option>
                <?php foreach ($obj->view_all("tbl_employee") as $emp): ?>
                    <option value="<?= $emp['id'] ?>">
                        <?= $emp['employee_name'] ?> - <?= $emp['employee_id'] ?? '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- ==================== IN TIME ==================== -->
        <div class="panel panel-success">
            <div class="panel-heading"><strong>IN Time Entry</strong></div>
            <div class="panel-body">
                <div class="form-group">
                    <label>IN Time</label>
                    <input type="time" name="in_time" class="form-control" step="60">
                </div>
                <button type="submit" name="submit_in" class="btn btn-success btn-block">
                    Save / Update IN Time
                </button>
            </div>
        </div>

        <!-- ==================== OUT TIME ==================== -->
        <div class="panel panel-primary">
            <div class="panel-heading"><strong>OUT Time Entry</strong></div>
            <div class="panel-body">
                <div class="form-group">
                    <label>OUT Time</label>
                    <input type="time" name="out_time" class="form-control" step="60">
                </div>
                <button type="submit" name="submit_out" class="btn btn-primary btn-block">
                    Save / Update OUT Time
                </button>
            </div>
        </div>

    </form>
</div>