<?php
date_default_timezone_set('Asia/Dhaka');

$q = isset($_GET['q']) ? $_GET['q'] : '';
$selected_month = isset($_GET['month']) && !empty($_GET['month']) ? $_GET['month'] : date('m');
$selected_year  = isset($_GET['year']) && !empty($_GET['year']) ? $_GET['year'] : date('Y');

$month_name = date('F', mktime(0, 0, 0, (int)$selected_month, 1, (int)$selected_year));
$days_in_month = cal_days_in_month(CAL_GREGORIAN, $selected_month, $selected_year);
?>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap.min.css">

<div class="row">
    <div class="col-md-12">
        <div class="panel">
            <div class="panel-body bg-teal-800 text-center">
                <h4>Monthly Detailed Attendance Report - <?= $month_name ?> <?= $selected_year ?></h4>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="col-md-12" style="margin-bottom: 15px;">
    <form method="GET" class="form-inline">
        <input type="hidden" name="q" value="view_employee_attendance">
        
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

<div class="col-md-12" style="margin-bottom:10px;">
    <span class="label label-success">P</span> Present &nbsp;
    <span class="label label-warning">L</span> Late &nbsp;
    <span class="label label-info">LV</span> Leave &nbsp;
    <span class="label label-danger">A</span> Absent &nbsp;
    <span class="label label-default">-</span> Not Marked
</div>

<div class="col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover nowrap" id="detailedTable" width="100%">
            <thead class="bg-teal-700">
                <tr>
                    <th>SL</th>
                    <th>Employee Name</th>
                    <th>Emp ID</th>
                    <?php for($d=1; $d<=$days_in_month; $d++): ?>
                        <th class="text-center">
                            <?= $d ?><br>
                            <small><?= date('D', strtotime("$selected_year-$selected_month-$d")) ?></small>
                        </th>
                    <?php endfor; ?>
                    <th class="text-center">Present</th>
                    <th class="text-center">Late</th>
                    <th class="text-center">Leave</th>
                    <th class="text-center">Absent</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $sql = "SELECT e.id, e.employee_name, e.employee_id,
                    GROUP_CONCAT(CONCAT(a.attendance_date, ':', a.status) SEPARATOR '|') as attendance_data
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
                $att_data = [];
                if(!empty($emp['attendance_data'])){
                    $rows = explode('|', $emp['attendance_data']);
                    foreach($rows as $row){
                        if (strpos($row, ':') === false) continue;
                        list($date, $status) = explode(':', $row);
                        $day = (int)date('j', strtotime($date));
                        $att_data[$day] = $status;
                    }
                }
            ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($emp['employee_name']) ?></td>
                    <td><?= htmlspecialchars($emp['employee_id']) ?></td>
                    
                    <?php for($d=1; $d<=$days_in_month; $d++):
                        $status = $att_data[$d] ?? null;
                        if($status == 'present') $label = '<span class="label label-success">P</span>';
                        elseif($status == 'late') $label = '<span class="label label-warning">L</span>';
                        elseif($status == 'leave') $label = '<span class="label label-info">LV</span>';
                        elseif($status == 'absent') $label = '<span class="label label-danger">A</span>';
                        else $label = '<span class="label label-default">-</span>';
                    ?>
                        <td class="text-center"><?= $label ?></td>
                    <?php endfor; ?>
                    
                    <td class="text-success text-center"><strong><?= count(array_filter($att_data, fn($s) => $s == 'present')) ?></strong></td>
                    <td class="text-warning text-center"><?= count(array_filter($att_data, fn($s) => $s == 'late')) ?></td>
                    <td class="text-info text-center"><?= count(array_filter($att_data, fn($s) => $s == 'leave')) ?></td>
                    <td class="text-danger text-center"><?= count(array_filter($att_data, fn($s) => $s == 'absent')) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
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
$(document).ready(function() {
    $('#detailedTable').DataTable({
        responsive: true,           // Responsive Feature
        scrollX: true,              // Horizontal Scroll
        paging: true,
        searching: true,
        ordering: true,
        pageLength: 20,
        lengthMenu: [[10, 20, 50, -1], [10, 20, 50, "All"]],
        
        dom: 'Bfrtip',
        buttons: [
            { extend: 'excel', text: 'Excel', className: 'btn btn-success btn-sm' },
            { extend: 'pdf', text: 'PDF', className: 'btn btn-danger btn-sm' },
            { extend: 'print', text: 'Print', className: 'btn btn-default btn-sm' },
            { extend: 'copy', text: 'Copy', className: 'btn btn-info btn-sm' }
        ],
        
        language: {
            search: "Search Employee:",
            lengthMenu: "Show _MENU_ records per page"
        }
    });
});
</script>