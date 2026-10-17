<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
// echo $_GET['status'] ;
if (isset($_GET['status']) && ($_GET['status'] == '0' || $_GET['status'] == '1')) {

    $status = $_GET['status'];
    $employees = $obj->view_all_by_cond(
        'tbl_employee',
        "employee_status='$status'"
    );

} else {

    $employees = $obj->view_all('tbl_employee');

}



?>

<div class="row">
    <div class="col-md-12 padding_5_px">
        <?php $obj->notificationShowRedirect(); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12 bg-teal-800">
        <h4 class="col-md-9 col-xs-8">View All Employee</h4>
        <button type="submit" class="btn btn-primary btn-sm pull-right" onclick="printDiv('print_transaction')">
            Print Transaction
        </button>
    </div>
</div>
<hr>

<div class="row">
    <div class="col-md-12">
        <!-- Improved Responsive Scrollable Table -->
        <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch; margin-top: 10px;">
            <table class="table table-bordered table-hover" id="datatable" style="width: 100%; min-width: 1200px;">
                <thead>
                    <tr class="bg-teal-800">
                        <th>SL</th>
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        <th>Mobile</th>
                        <th>Note</th>
                        <th>Email</th>
                        <th>National ID</th>
                        <th>Designation</th>
                        <th>Address</th>
                        <th>Joining Date</th>
                        <th>Image</th>
                        <th>Details</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php $i = 0;
                foreach ($employees as $employee) {
                    $i++;

                    // Truncate helper for long text fields (Note / Address)
                    $note_full = $employee['employee_details'];
                    $addr_full = $employee['employee_address'];
                    $truncate_len = 25;

                    $note_is_long = mb_strlen($note_full) > $truncate_len;
                    $addr_is_long = mb_strlen($addr_full) > $truncate_len;

                    $note_short = $note_is_long ? mb_substr($note_full, 0, $truncate_len) . '...' : $note_full;
                    $addr_short = $addr_is_long ? mb_substr($addr_full, 0, $truncate_len) . '...' : $addr_full;
                ?>
                    <tr>
                        <td class="text-center"><?php echo $i; ?></td>
                        <td class="text-center"><?php echo $employee['employee_id']; ?></td>
                        <td><?php echo $employee['employee_name']; ?></td>
                        <td class="text-center"><?php echo $employee['employee_mobile_no']; ?></td>

                        <!-- Note column with truncation + More modal -->
                        <td>
                            <?php echo htmlspecialchars($note_short); ?>
                            <?php if ($note_is_long) { ?>
                                <br>
                                <button type="button" class="btn btn-xs btn-info"
                                        data-toggle="modal"
                                        data-target="#noteModal<?php echo $employee['id']; ?>">
                                    More
                                </button>

                                <div class="modal fade" id="noteModal<?php echo $employee['id']; ?>" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                                <h4 class="modal-title">Note - <?php echo htmlspecialchars($employee['employee_name']); ?></h4>
                                            </div>
                                            <div class="modal-body">
                                                <p><?php echo nl2br(htmlspecialchars($note_full)); ?></p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </td>

                        <td><?php echo $employee['employee_email']; ?></td>
                        <td class="text-center"><?php echo $employee['employee_national_id']; ?></td>
                        <td><?php echo $employee['designation']; ?></td>

                        <!-- Address column with truncation + More modal -->
                        <td>
                            <?php echo htmlspecialchars($addr_short); ?>
                            <?php if ($addr_is_long) { ?>
                                <br>
                                <button type="button" class="btn btn-xs btn-info"
                                        data-toggle="modal"
                                        data-target="#addressModal<?php echo $employee['id']; ?>">
                                    More
                                </button>

                                <div class="modal fade" id="addressModal<?php echo $employee['id']; ?>" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                                <h4 class="modal-title">Address - <?php echo htmlspecialchars($employee['employee_name']); ?></h4>
                                            </div>
                                            <div class="modal-body">
                                                <p><?php echo nl2br(htmlspecialchars($addr_full)); ?></p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </td>

                        <td class="text-center"><?php echo date('d, M Y', strtotime($employee['joining_date'])); ?></td>
                        <td class="text-center">
                            <img src="<?php echo $employee['PhotoPath']; ?>" 
                                 class="img-thumbnail" 
                                 style="height: 50px; width: 50px; object-fit: cover;" alt="">
                        </td>
                        <td class="text-center">
                            <a href="?q=employe_details&token=<?php echo $employee['id']; ?>">
                                <span class="glyphicon glyphicon-eye-open"></span>
                            </a>
                        </td>
                        <td class="text-center">
                            <?php echo ($employee['employee_status'] == 1) ? "Active" : "Resign"; ?>
                        </td>
                        <td class="text-center">
                            <a href="?q=edit_employee&token=<?= $employee['id']?>" 
                               class="btn btn-success btn-sm">Edit</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Extra CSS for Better Mobile Scrolling -->
<style>
    .table-responsive {
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    #datatable {
        margin-bottom: 0;
    }

    #datatable th, 
    #datatable td {
        white-space: nowrap !important;
        vertical-align: middle;
    }

    /* Note / Address cells need to wrap since text is now short + a button */
    #datatable td:nth-child(5),
    #datatable td:nth-child(9) {
        white-space: normal !important;
        min-width: 150px;
    }

    /* Mobile Scroll Improvement */
    @media (max-width: 768px) {
        .table-responsive {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
            touch-action: pan-y pan-x;
        }
        
        #datatable th, 
        #datatable td {
            font-size: 13px;
            padding: 8px 5px;
        }

        /* Hide some columns on very small screen */
        @media (max-width: 480px) {
            #datatable th:nth-child(6), #datatable td:nth-child(6),   /* Email */
            #datatable th:nth-child(7), #datatable td:nth-child(7) {  /* NID */
                display: none;
            }
        }
    }
</style>

<link rel="stylesheet" type="text/css" href="asset/css/bootstrap-datepicker.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>