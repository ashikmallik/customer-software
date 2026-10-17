<?php
date_default_timezone_set('Asia/Dhaka');

// ==================== Update Settings ====================
if (isset($_POST['update_settings'])) {
    
    $form_data = [
        'start_time'             => $_POST['start_time'],
        'end_time'               => $_POST['end_time'],
        'late_threshold_minutes' => (int)$_POST['late_threshold_minutes'],
        'half_day_hours'         => (float)$_POST['half_day_hours'],
        'full_day_hours'         => (float)$_POST['full_day_hours']
    ];

    $condition = "id=1";
    $existing = $obj->details_by_cond("office_settings", $condition);

    if ($existing) {
        $obj->Update_data("office_settings", $form_data, $condition);
        $obj->notificationStore('Office Settings Updated Successfully', 'success');
    } else {
        $obj->Insert_data("office_settings", $form_data);
        $obj->notificationStore('Office Settings Saved Successfully', 'success');
    }
    
    echo '<script>window.location.href = window.location.href;</script>';
}

// ==================== Fetch Current Settings ====================
$settings = $obj->details_by_cond("office_settings", "id=1");
?>

<div class="row">
    <div class="col-md-12">
        <?php $obj->notificationShowRedirect(); ?>
    </div>
</div>

<div class="row panel">
    <div class="panel-body col-md-12 bg-teal-800 text-center">
        <h4>Office Attendance Settings</h4>
    </div>
</div>

<div class="col-md-6 col-md-offset-3">
    <form method="post">
        <div class="panel panel-default">
            <div class="panel-body">
                
                <div class="form-group">
                    <label>Office Start Time <span class="text-danger">*</span></label>
                    <input type="time" name="start_time" class="form-control" 
                           value="<?= $settings['start_time'] ?? '09:00' ?>" required>
                    <small class="text-muted">Example: 09:00</small>
                </div>

                <div class="form-group">
                    <label>Office End Time <span class="text-danger">*</span></label>
                    <input type="time" name="end_time" class="form-control" 
                           value="<?= $settings['end_time'] ?? '18:00' ?>" required>
                    <small class="text-muted">Example: 18:00</small>
                </div>

                <div class="form-group">
                    <label>Late Threshold (Minutes)</label>
                    <input type="number" name="late_threshold_minutes" class="form-control" 
                           value="<?= $settings['late_threshold_minutes'] ?? 15 ?>" min="0">
                    <small class="text-muted">কত মিনিট পরে Late ধরা হবে</small>
                </div>

                <div class="form-group">
                    <label>Half Day Hours</label>
                    <input type="number" step="0.5" name="half_day_hours" class="form-control" 
                           value="<?= $settings['half_day_hours'] ?? 4.0 ?>" min="1">
                </div>

                <div class="form-group">
                    <label>Full Day Hours</label>
                    <input type="number" step="0.5" name="full_day_hours" class="form-control" 
                           value="<?= $settings['full_day_hours'] ?? 8.0 ?>" min="1">
                </div>

                <div class="form-group text-center" style="margin-top: 25px;">
                    <button type="submit" name="update_settings" class="btn btn-success btn-lg">
                        <i class="fa fa-save"></i> Update Settings
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>