<?php
date_default_timezone_set('Asia/Dhaka');

// ==================== URL থেকে প্যারামিটার ====================
$q = isset($_GET['q']) ? $_GET['q'] : '';
$selected_month = isset($_GET['month']) && !empty($_GET['month']) ? $_GET['month'] : date('m');
$selected_year  = isset($_GET['year']) && !empty($_GET['year']) ? $_GET['year'] : date('Y');
$month_name = date('F', mktime(0, 0, 0, (int)$selected_month, 1, (int)$selected_year));
?>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap.min.css">

<div class="row">
    <div class="col-md-12">
        <div class="panel">
            <div class="panel-body bg-teal-800 text-center">
                <h4>Overall Attendance Report - <?= $month_name ?> <?= $selected_year ?></h4>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="col-md-12" style="margin-bottom: 15px;">
    <form method="GET" class="form-inline">
        <input type="hidden" name="q" value="attendance_report">
        
        <div class="form-group">
            <label>Select Month &nbsp;</label>
            <select name="month" class="form-control">
                <?php for($m=1; $m<=12; $m++): ?>
                    <option value="<?= str_pad($m,2,'0',STR_PAD_LEFT) ?>" <?= $m == $selected_month ? 'selected' : '' ?>>
                        <?= date('F', mktime(0,0,0,$m,1)) ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>&nbsp; Select Year &nbsp;</label>
            <select name="year" class="form-control">
                <?php for($y=2023; $y<=2027; $y++): ?>
                    <option value="<?= $y ?>" <?= $y == $selected_year ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Show Report</button>
    </form>
</div>

<!-- Report Table -->
<div class="col-md-12">
    <table class="table table-bordered table-striped table-hover" id="attendanceTable">
        <thead class="bg-teal-700">
            <tr>
                <th>SL</th>
                <th>Employee Name</th>
                <th>Employee ID</th>
                <th>Present</th>
                <th>Late</th>
                <th>Leave</th>
                <th>Absent</th>
                <th>Total Days Marked</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT 
                    e.id,
                    e.employee_name,
                    e.employee_id,
                    COUNT(CASE WHEN a.status = 'present' THEN 1 END) as present,
                    COUNT(CASE WHEN a.status = 'late' THEN 1 END) as late,
                    COUNT(CASE WHEN a.status = 'leave' THEN 1 END) as leave_count,
                    COUNT(CASE WHEN a.status = 'absent' THEN 1 END) as absent,
                    COUNT(a.id) as total_marked
                FROM tbl_employee e
                LEFT JOIN employee_attendance a 
                    ON e.id = a.employee_id 
                    AND MONTH(a.attendance_date) = '$selected_month' 
                    AND YEAR(a.attendance_date) = '$selected_year'
                GROUP BY e.id
                ORDER BY e.employee_name ASC";

        $employees = $obj->raw_sql($sql);

        $i = 1;
        foreach($employees as $emp):
        ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($emp['employee_name']) ?></td>
                <td><?= htmlspecialchars($emp['employee_id']) ?></td>
                <td class="text-success"><strong><?= $emp['present'] ?></strong></td>
                <td class="text-warning"><?= $emp['late'] ?></td>
                <td class="text-info"><?= $emp['leave_count'] ?></td>
                <td class="text-danger"><?= $emp['absent'] ?></td>
                <td><?= $emp['total_marked'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    $('#attendanceTable').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                className: 'btn btn-success btn-sm'
            },
            {
                extend: 'pdf',
                text: '<i class="fa fa-file-pdf-o"></i> PDF',
                className: 'btn btn-danger btn-sm'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Print',
                className: 'btn btn-default btn-sm'
            },
            {
                extend: 'copy',
                text: 'Copy',
                className: 'btn btn-info btn-sm'
            }
        ],
        "language": {
            "search": "Search Employee:",
            "lengthMenu": "Show _MENU_ records"
        }
    });
});
</script>