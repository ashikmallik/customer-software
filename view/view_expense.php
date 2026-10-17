<?php
date_default_timezone_set('Asia/Dhaka');

$date_time = date('Y-m-d g:i:sA');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = $_SESSION['UserId'] ?? NULL;

$firsDayOfMonth = new DateTime('first day of this month');

/* =========================
   FILTER LOGIC (FIXED)
========================= */

if (isset($_POST['search'])) {

    $dateform = date('Y-m-d', strtotime($_POST['dateform']));
    $dateto   = date('Y-m-d', strtotime($_POST['dateto']));

    $where = "entry_date BETWEEN '$dateform' AND '$dateto' AND acc_type='1'";

    // HEAD FILTER
    if (!empty($_POST['head'])) {
        $where .= " AND acc_head='" . intval($_POST['head']) . "'";
    }

    // SUB HEAD FILTER
    if (!empty($_POST['sub_head'])) {
        $where .= " AND acc_sub_head_id='" . intval($_POST['sub_head']) . "'";
    }

    $expenseDetails = $obj->view_all_by_cond(
        "vw_account",
        $where . " ORDER BY entry_date DESC"
    );

} else {

    $dateform = $firsDayOfMonth->format('Y-m-d');
    $dateto   = date('Y-m-d');

    $expenseDetails = $obj->view_all_by_cond(
        "vw_account",
        "MONTH(entry_date)='" . date('m') . "' 
        AND YEAR(entry_date)='" . date('Y') . "' 
        AND acc_type='1'
        ORDER BY entry_date DESC"
    );
}

$previewDate = date('d M Y', strtotime($dateform)) . ' to ' . date('d M Y', strtotime($dateto));

/* ================= DELETE ================= */

if ($obj->userWorkPermission($acc,'delete')) {

    if (isset($_GET['dltoken']) && !empty($_GET['dltoken'])) {

        $dltoken = intval($_GET['dltoken']);

        if ($obj->Delete_data("tbl_account", "acc_id='$dltoken'")) {
            $obj->notificationStore('Delete Successfull', 'success');
        } else {
            $obj->notificationStore('Delete Failed');
        }

        echo '<script>window.location="?q=view_expense";</script>';
    }
}

?>

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">

<!-- ================= HEADER ================= -->

<div class="row bg-slate-700">
    <div class="col-md-6">
        <h4 id="preview_date">
            View Expense Information of <?php echo $previewDate; ?>
        </h4>
    </div>

    <div class="col-md-6" style="margin-top:5px;">
        <?php if ($ty == 'SA' OR $ty == 'EO') { ?>
            <a class="btn btn-primary pull-right btn-sm" href="?q=add_expense">
                ADD NEW EXPENSE <span class="glyphicon glyphicon-plus"></span>
            </a>
        <?php } ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?php $obj->notificationShowRedirect(); ?>
    </div>
</div>

<hr>

<!-- ================= FILTER ================= -->

<div class="row bg-slate-700" style="padding:3px;">
<div class="col-md-12">

<form action="" method="POST">

    <div class="col-md-2">
        <input type="text" class="form-control datepicker"
               name="dateform"
               value="<?php echo date('d-m-Y', strtotime($dateform)); ?>">
    </div>

    <div class="col-md-2">
        <input type="text" class="form-control datepicker"
               name="dateto"
               value="<?php echo date('d-m-Y', strtotime($dateto)); ?>">
    </div>

    <!-- HEAD -->
    <div class="col-md-2">
        <select name="head" class="form-control">
            <option value="">All Head</option>
            <?php
            $heads = $obj->view_all("tbl_accounts_head");
            foreach ($heads as $h) {
                // $sel = (!empty($_POST['head']) && $_POST['head'] == $h['acc_id']) ? 'selected' : '';
                echo "<option value='{$h['acc_id']}'>{$h['acc_name']}</option>";
            }
            ?>
        </select>
    </div>

    <!-- SUB HEAD -->
    <div class="col-md-2">
        <select name="sub_head" class="form-control">
            <option value="">All Sub Head</option>
            <?php
            $subs = $obj->view_all("tbl_accounts_sub_head");
            foreach ($subs as $s) {
                $sel = (!empty($_POST['sub_head']) && $_POST['sub_head'] == $s['id']) ? 'selected' : '';
                echo "<option value='{$s['acc_id']}' $sel>{$s['acc_sub_head_name']}</option>";
            }
            ?>
        </select>
    </div>

    <div class="col-md-2">
        <input type="submit" name="search" class="btn btn-primary" value="Search">
    </div>

</form>

</div>
</div>

<hr>

<!-- ================= TABLE ================= -->

<div class="row" style="padding:10px; font-size:12px;">
<div class="col-md-12">

<div class="table-responsive">

<table class="table table-hover table-bordered table-striped"
       id="datatable_view_expense">

<thead>
<tr>
    <th>#</th>
    <th>Date</th>
    <th>Head</th>
    <th>Sub Head</th>
    <th>Amount</th>
    <th>Pending</th>
    <th>Description</th>
    <th>Status</th>
    <th>Action</th>
</tr>
</thead>

<tbody>

<?php
$i = 0;
$total = 0;
$pending = 0;

foreach ($expenseDetails as $value) {
    $i++;
    $total += $value['acc_amount'];
    $pending += $value['temp_amount'];
?>

<tr>
    <td><?php echo $i; ?></td>
    <td><?php echo date('d-m-Y', strtotime($value['entry_date'])); ?></td>
    <td><?php echo $value['acc_name']; ?></td>
    <td><?php echo $value['acc_sub_head_name']; ?></td>
    <td><?php echo $value['acc_amount']; ?></td>
    <td><?php echo $value['temp_amount']; ?></td>
    <td><?php echo $value['acc_description']; ?></td>

    <td>
        <?php
        if ($value['status'] == 0) echo "Pending";
        else echo "Approved";
        ?>
    </td>

    <td>
        <a href="?q=edit_expense&token=<?php echo $value['acc_id']; ?>" class="btn btn-success btn-xs">Edit</a>
        <a onclick="return confirm('Delete?')"
           href="?q=view_expense&dltoken=<?php echo $value['acc_id']; ?>"
           class="btn btn-danger btn-xs">Delete</a>
    </td>
</tr>

<?php } ?>

</tbody>

<tfoot>
<tr>
    <th colspan="4">Total</th>
    <th><?php echo $total; ?></th>
    <th><?php echo $pending; ?></th>
    <th colspan="3"></th>
</tr>
</tfoot>

</table>

</div>

</div>
</div>

<!-- ================= JS ================= -->

<script>
$(document).ready(function(){

    $('.datepicker').datepicker({
        format:'dd-mm-yyyy',
        autoclose:true
    });

    $('#datatable_view_expense').DataTable({
        dom: 'Bfrtip',
        buttons: ['print']
    });

});
</script>

<script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>