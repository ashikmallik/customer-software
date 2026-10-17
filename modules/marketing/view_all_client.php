<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

// ========== Status Update Function (AJAX, same page) =================
if (isset($_POST['update_status'])) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    $stoken     = isset($_POST['stoken']) ? $_POST['stoken'] : NULL;
    $new_status = isset($_POST['status_val']) ? $_POST['status_val'] : NULL;

    if (!empty($stoken) && ($new_status === '0' || $new_status === '1')) {
        $status_update_data = array(
            'status'    => $new_status,
            'update_by' => $userid
        );
        if ($obj->Update_data("tbl_marketing_agent", $status_update_data, "where ag_id='$stoken'")) {
            echo 'success';
        } else {
            echo 'fail';
        }
    } else {
        echo 'fail';
    }
    exit;
}

// ========== Delete Function Start =================
if ($obj->userWorkPermission($acc, 'delete')) {
    $dltoken = isset($_GET['dltoken']) ? $_GET['dltoken'] : NULL;
    if (!empty($dltoken)) {
        if ($obj->Delete_data("tbl_marketing_agent", "ag_id='$dltoken'")) {
            $obj->notificationStore('Survey Deleted Successfully', 'success');
            echo '<script>window.location = "?q=view_all_client";</script>';
        } else {
            $obj->notificationStore('Delete Failed', 'danger');
            echo '<script>window.location = "?q=view_all_client";</script>';
        }
    }
}

// ========== Fetch Data Directly (No AJAX) =================
if (isset($_GET['zone']) && !empty($_GET['zone'])) {
    $zone = $_GET['zone'];
    $sql = "SELECT * FROM vw_marketing_agent WHERE zone='$zone' ORDER BY `ag_id` ASC";
} else {
    $sql = "SELECT * FROM vw_marketing_agent ORDER BY `ag_id` ASC";
}
$q = $obj->con->prepare($sql);
$q->execute();
$allAgentData = $q->fetchAll(PDO::FETCH_ASSOC);
$sl = 1;

$showAction = $obj->userWorkPermission($acc, 'edit') || $obj->userWorkPermission($acc, 'delete');
$totalCols = $showAction ? 13 : 12;
?>

<style>
    .status-badge {
        display: inline-block;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        cursor: pointer;
        border: none;
        min-width: 85px;
        text-align: center;
        transition: all 0.25s ease;
    }
    .status-badge.status-pending {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    .status-badge.status-done {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .status-badge:hover {
        opacity: 0.85;
        transform: scale(1.05);
    }
    .status-badge:disabled {
        cursor: not-allowed;
        opacity: 0.6;
    }
    .truncate-cell {
        max-width: 150px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    },
    .truncate-cell-wrap {
    max-width: 200px;
    word-wrap: break-word;
    white-space: normal;
}
.toggle-text-btn {
    color: #007bff;
    font-weight: bold;
    font-size: 11px;
    text-decoration: none;
    margin-left: 3px;
}
.toggle-text-btn:hover {
    text-decoration: underline;
}
.full-text {
    word-break: break-word;
}
    
</style>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">

<div class="row">
    <div class="col-md-12 padding_5_px">
        <?php $obj->notificationShowRedirect(); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12 bg-teal-800" style="margin-bottom: 15px; padding:10px 0px 3px 15px; font-size:16px; font-family:Lucida Sans Unicode;">
        <div class="col-md-4">
            <b>View All Surveys</b>
        </div>
        <div class="col-md-8">
            <?php if ($ty == 'SA' || $ty == 'EO') { ?>
                <a class="btn btn-primary btn-sm pull-right" href="?q=add_client">
                    ADD NEW SURVEY <span class="glyphicon glyphicon-plus"></span>
                </a>
            <?php } ?>
        </div>
    </div>
</div>

<!-- All Surveys Table -->
<div id="div_all" class="row" style="padding:10px; font-size: 12px;">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped" id="datatable_view_client">
                <thead>
                <tr class="bg-grey-800">
                    <th>Survey ID</th>
                    <th>Survey Date</th>
                    <th>Link Name</th>
                    <th>Path Address</th>
                    <th>Pole Req.</th>
                    <th>TOTAL E.Pole</th>
                    <th>Others</th>
                    <th>Fiber Req.</th>
                    <th>Road Cross</th>
                    <th>Rail Cross</th>
                    <th>Open Cut</th>
                    <th>Status</th>
                    <?php if ($showAction) { echo '<th>Action</th>'; } ?>
                </tr>
                </thead>
                <tbody>
                <?php if (count($allAgentData) > 0) { ?>
                    <?php foreach ($allAgentData as $row) {
                        $isDone = ($row['status'] == '1');
                        $statusClass = $isDone ? 'status-done' : 'status-pending';
                        $statusText  = $isDone ? 'Done' : 'Pending';
                    ?>
                    <tr>
                        <td><?php echo $sl++; ?></td>
                        <td><?php echo htmlspecialchars($row['survey_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['link_name']); ?></td>
                        <td class="truncate-cell-wrap">
                            <?php
                                $pathFull = htmlspecialchars($row['path_address']);
                                $pathLimit = 30;
                                if (mb_strlen($row['path_address']) > $pathLimit) {
                                    $pathShort = htmlspecialchars(mb_substr($row['path_address'], 0, $pathLimit));
                                    echo '<span class="short-text">' . $pathShort . '... 
                                            <a href="javascript:void(0)" class="toggle-text-btn" onclick="toggleText(this)">more</a>
                                          </span>';
                                    echo '<span class="full-text" style="display:none;" data-full="' . $pathFull . '">
                                            ' . $pathFull . '
                                            <a href="javascript:void(0)" class="toggle-text-btn" onclick="toggleText(this)">less</a>
                                          </span>';
                                } else {
                                    echo $pathFull;
                                }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['e_pole_req']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_epole']); ?></td>
                        <td class="truncate-cell-wrap">
                                <?php
                                    $othersFull = htmlspecialchars($row['others']);
                                    $othersLimit = 30;
                                    if (mb_strlen($row['others']) > $othersLimit) {
                                        $othersShort = htmlspecialchars(mb_substr($row['others'], 0, $othersLimit));
                                        echo '<span class="short-text">' . $othersShort . '... 
                                                <a href="javascript:void(0)" class="toggle-text-btn" onclick="toggleText(this)">more</a>
                                              </span>';
                                        echo '<span class="full-text" style="display:none;">
                                                ' . $othersFull . '
                                                <a href="javascript:void(0)" class="toggle-text-btn" onclick="toggleText(this)">less</a>
                                              </span>';
                                    } else {
                                        echo $othersFull;
                                    }
                                ?>
                            </td>
                        <td><?php echo htmlspecialchars($row['fiber_req']); ?></td>
                        <td><?php echo htmlspecialchars($row['di_road_cross']); ?></td>
                        <td><?php echo htmlspecialchars($row['rail_cross']); ?></td>
                        <td><?php echo htmlspecialchars($row['open_cut']); ?></td>
                        <td>
                            <button type="button" 
                                    class="status-badge <?php echo $statusClass; ?>" 
                                    data-ag-id="<?php echo $row['ag_id']; ?>"
                                    data-current-status="<?php echo $row['status']; ?>"
                                    onclick="toggleSurveyStatus(this)">
                                <span class="status-text"><?php echo $statusText; ?></span>
                            </button>
                        </td>
                        <?php if ($showAction) { ?>
                        <td>
                            <?php if ($obj->userWorkPermission($acc, 'edit')) { ?>
                            <a target="_blank" class="btn btn-xs btn-primary" style="padding:5px;"
                               href="?q=edit_client&token=<?php echo $row['ag_id']; ?>">
                                <span class="glyphicon glyphicon-edit"></span>
                            </a>
                            <?php } ?>
                            <?php if ($obj->userWorkPermission($acc, 'delete')) { ?>
                            <a onclick="return confirmDelete()"
                               href="?q=view_all_client&dltoken=<?php echo $row['ag_id']; ?>"
                               class="btn btn-xs btn-danger" style="padding:5px;">
                                <span class="glyphicon glyphicon-remove"></span>
                            </a>
                            <?php } ?>
                        </td>
                        <?php } ?>
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="<?php echo $totalCols; ?>" class="text-center">No survey data found.</td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script type="text/javascript">
    function confirmDelete() {
        return confirm('Are you sure you want to delete this survey?');
    }

    // Toggle status on button click (Pending <-> Done)
    function toggleSurveyStatus(btnEl) {
        var agId = $(btnEl).data('ag-id');
        var currentStatus = $(btnEl).data('current-status');
        var newStatus = (currentStatus == '1') ? '0' : '1';
        var $btn = $(btnEl);

        $btn.prop('disabled', true);

        $.ajax({
            url: '?q=view_all_client',
            type: 'POST',
            data: {
                update_status: 1,
                stoken: agId,
                status_val: newStatus
            },
            success: function (response) {
                if ($.trim(response).indexOf('success') !== -1) {
                    $btn.removeClass('status-pending status-done');
                    if (newStatus == '1') {
                        $btn.addClass('status-done');
                        $btn.find('.status-text').text('Done');
                    } else {
                        $btn.addClass('status-pending');
                        $btn.find('.status-text').text('Pending');
                    }
                    $btn.data('current-status', newStatus);
                } else {
                    alert('Failed to update status. Please try again.');
                }
            },
            error: function () {
                alert('Failed to update status. Please try again.');
            },
            complete: function () {
                $btn.prop('disabled', false);
            }
        });
    }

    // Zone Filter
    $('select[name="zone"]').on('change', function () {
        var zoneId = $(this).val();
        if (zoneId != 'x' && zoneId != '') {
            window.location = '?q=view_all_client&zone=' + zoneId;
        } else {
            window.location = '?q=view_all_client';
        }
    });
    
    $('#datatable_view_client').DataTable({
        dom: 'Bfrtip',
        scrollX: true,
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Excel',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function (data, row, column, node) {
                            // Status column hole shudhu text export hobe
                            var statusText = $(node).find('.status-text').text().trim();
                            if (statusText) return statusText;
                            // Baki column er jonno HTML remove kore text return
                            return $(node).text().trim();
                        }
                    }
                }
            },
            {
                extend: 'csvHtml5',
                text: 'CSV',
                className: 'btn btn-info btn-sm',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function (data, row, column, node) {
                            var statusText = $(node).find('.status-text').text().trim();
                            if (statusText) return statusText;
                            return $(node).text().trim();
                        }
                    }
                }
            },
            {
                extend: 'print',
                text: 'Print',
                className: 'btn btn-primary btn-sm',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function (data, row, column, node) {
                            var statusText = $(node).find('.status-text').text().trim();
                            if (statusText) return statusText;
                            return $(node).text().trim();
                        }
                    }
                }
            }
        ]
    });
    
    function toggleText(el) {
    var $wrap = $(el).closest('.truncate-cell-wrap');
    $wrap.find('.short-text').toggle();
    $wrap.find('.full-text').toggle();
}
</script>