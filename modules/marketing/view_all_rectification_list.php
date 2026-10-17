<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$intoken = isset($_GET['intoken']) ? $_GET['intoken'] : NULL;
$actoken = isset($_GET['actoken']) ? $_GET['actoken'] : NULL;

// Folder jekhane image upload hobe. Na thakle create kore nao.
$upload_dir = 'uploads/rectification/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if ($obj->userWorkPermission($acc, 'delete')) {
    $dltoken = isset($_GET['dltoken']) ? $_GET['dltoken'] : NULL;
    if (!empty($dltoken)) {
        if ($obj->Delete_data("work_tracking", "id='$dltoken'")) {
            $obj->notificationStore('Survey Deleted Successfully', 'success');
            echo '<script>window.location = "?q=view_all_rectification_list";</script>';
        } else {
            $obj->notificationStore('Delete Failed', 'danger');
            echo '<script>window.location = "?q=view_all_rectification_list";</script>';
        }
    }
}

// Helper function: image upload kore file path return korbe
function uploadRectificationImage($file, $upload_dir)
{
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        return false; // invalid file type
    }

    $newName   = uniqid('img_') . '_' . time() . '.' . $ext;
    $targetPath = $upload_dir . $newName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $targetPath;
    }

    return false;
}

if (isset($_POST['save_data'])) {

    $picture_code = '';

    if (isset($_FILES['picture_code']) && $_FILES['picture_code']['error'] == 0) {
        $uploaded = uploadRectificationImage($_FILES['picture_code'], $upload_dir);
        if ($uploaded !== false) {
            $picture_code = $uploaded;
        }
    }

    $form_data_tbl_client = array(
        'work_date'       => $_POST['work_date'],
        'path_name'       => $_POST['path_name'],
        'hope_to_hope'    => $_POST['hope_to_hope'],
        'event_lat_long'  => $_POST['event_lat_long'],
        'kind_of_work'    => $_POST['kind_of_work'],
        'work_authority'  => $_POST['work_authority'],
        'contact_no'      => $_POST['contact_no'],
        'picture_code'    => $picture_code,
        'tc_name'         => $_POST['tc_name'],
        'remark'          => $_POST['remark'],
        'last_check_date' => $_POST['last_check_date']
    );

    $obj->Insert_data("work_tracking", $form_data_tbl_client);
}


if (isset($_POST['update_data'])) {

    $token = $_POST['id'];

    // Age theke thaka image path (hidden field theke)
    $picture_code = isset($_POST['old_picture_code']) ? $_POST['old_picture_code'] : '';

    // Notun image upload hoyle purono ta replace hobe
    if (isset($_FILES['picture_code']) && $_FILES['picture_code']['error'] == 0) {
        $uploaded = uploadRectificationImage($_FILES['picture_code'], $upload_dir);
        if ($uploaded !== false) {
            // purono image file thakle delete kore dao (optional cleanup)
            if (!empty($picture_code) && file_exists($picture_code)) {
                @unlink($picture_code);
            }
            $picture_code = $uploaded;
        }
    }

    $form_data = array(
        'work_date'       => $_POST['work_date'],
        'path_name'       => $_POST['path_name'],
        'hope_to_hope'    => $_POST['hope_to_hope'],
        'event_lat_long'  => $_POST['event_lat_long'],
        'kind_of_work'    => $_POST['kind_of_work'],
        'work_authority'  => $_POST['work_authority'],
        'contact_no'      => $_POST['contact_no'],
        'picture_code'    => $picture_code,
        'tc_name'         => $_POST['tc_name'],
        'remark'          => $_POST['remark'],
        'last_check_date' => $_POST['last_check_date']
    );

    $obj->Update_data(
        "work_tracking",
        $form_data,
        "`work_tracking`.`id`='$token'"
    );
}


?>





<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">
<style>
    #datatable_view_agent_filter input {
        height: 30px;
        border-radius: 5px;
        padding: 5px;
    }

    .rectification-thumb {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
        cursor: pointer;
        border: 1px solid #ddd;
    }

    .no-image-text {
        color: #999;
        font-size: 11px;
    }
</style>

<div class="row">
    <div class="col-md-12 padding_5_px">
        <?php $obj->notificationShowRedirect(); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12 bg-teal-800" style="margin-bottom: 15px; padding:10px 0px 3px 15px; font-size:16px; font-family:Lucida Sans Unicode;">
        <div class="col-md-4">
            <b>View All Rectification</b>
        </div>

    </div>
</div>
<button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addModal">
    <i class="fa fa-plus"></i> Add New
</button>


<!-- All Surveys Table -->
<div id="" class="row" style="padding:10px; font-size: 12px;">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="datatable_view_client">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Date</th>
                         <th>Last Check Date</th>
                        <th>Path Name</th>
                        <th>Hope To Hope</th>
                        <th>Event Lat/Long</th>
                        <th>Kind Of Work</th>
                        <th>Work Authority</th>
                        <th>Contact No</th>
                        <th>Picture</th>
                        <th>T/C Name</th>
                        <th>Remark</th>
                        <th>Work Status</th>
                        <?php if ($obj->userWorkPermission($acc, 'edit') || $obj->userWorkPermission($acc, 'delete')) {
                        echo '<th>Action</th>';
                    } ?>
                    </tr>
            </thead>
            <tbody>

            <?php

            $sql = "SELECT * FROM work_tracking ORDER BY id DESC";

            $q = $obj->con->prepare($sql);
            $q->execute();

            $i = 1;

            while ($row = $q->fetch(PDO::FETCH_ASSOC)) {

            ?>

                <tr>
                    <td><?= $i++; ?></td>
                    <td><?= $row['work_date']; ?></td>
                    <td><?= $row['last_check_date']; ?></td>
                    <td><?= $row['path_name']; ?></td>

                    <td><?= $row['hope_to_hope']; ?></td>
                    <td><?= $row['event_lat_long']; ?></td>
                    <td><?= $row['kind_of_work']; ?></td>
                    <td><?= $row['work_authority']; ?></td>
                    <td><?= $row['contact_no']; ?></td>
                    <td>
                        <?php if (!empty($row['picture_code']) && file_exists($row['picture_code'])) { ?>
                            <img src="<?= htmlspecialchars($row['picture_code']); ?>"
                                 class="rectification-thumb viewImage"
                                 data-image="<?= htmlspecialchars($row['picture_code']); ?>"
                                 alt="picture">
                        <?php } else { ?>
                            <span class="no-image-text">No Image</span>
                        <?php } ?>
                    </td>
                    <td><?= $row['tc_name']; ?></td>
                    <td>
                        <?php
                        $remarks = htmlspecialchars($row['remark']);
                        if (strlen($remarks) > 20) {
                            echo substr($remarks, 0, 20) . '... ';
                            echo '<button type="button" class="btn btn-xs btn-info viewRemarks" data-remarks="'.$remarks.'">More</button>';
                        } else {
                            echo $remarks;
                        }
                        ?>
                    </td>
                    <td>
                        <button type="button" onclick="return confirm('Are you sure?')"
                            class="btn btn-sm toggleStatus <?= $row['work_status'] == 1 ? 'btn-success' : 'btn-warning'; ?>"
                            data-id="<?= $row['id']; ?>"
                            data-status="<?= $row['work_status']; ?>">

                            <?= $row['work_status'] == 1 ? 'Done' : 'Pending'; ?>
                        </button>
                    </td>
                    <?php if ($obj->userWorkPermission($acc, 'edit') || $obj->userWorkPermission($acc, 'delete')) { ?>
                        <td>
                        <?php if ($obj->userWorkPermission($acc, 'edit')) { ?>
                        <button type="button"
                            class="btn btn-success btn-sm editData"

                            data-id="<?= $row['id']; ?>"
                            data-work_date="<?= $row['work_date']; ?>"
                            data-path_name="<?= htmlspecialchars($row['path_name']); ?>"
                            data-work_status="<?= htmlspecialchars($row['work_status']); ?>"
                            data-hope_to_hope="<?= htmlspecialchars($row['hope_to_hope']); ?>"
                            data-event_lat_long="<?= htmlspecialchars($row['event_lat_long']); ?>"
                            data-kind_of_work="<?= htmlspecialchars($row['kind_of_work']); ?>"
                            data-work_authority="<?= htmlspecialchars($row['work_authority']); ?>"
                            data-contact_no="<?= htmlspecialchars($row['contact_no']); ?>"
                            data-picture_code="<?= htmlspecialchars($row['picture_code']); ?>"
                            data-tc_name="<?= htmlspecialchars($row['tc_name']); ?>"
                            data-remark="<?= htmlspecialchars($row['remark']); ?>"
                            data-last_check_date="<?= $row['last_check_date']; ?>">

                            <i class="fa fa-edit"></i>
                        </button>
                        <?php } ?>
                        
                         <?php if ($obj->userWorkPermission($acc, 'delete')) { ?>

                            <a href="?q=view_all_rectification_list&dltoken=<?= $row['id']; ?>"
                               onclick="return confirm('Are you sure?')"
                               class="btn btn-danger btn-sm">
                                <i class="fa fa-trash"></i>
                            </a>
                        <?php } ?>
                    </td>
                    <?php } ?>
                    
                    
                </tr>

            <?php } ?>

    </tbody>
        </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="POST" enctype="multipart/form-data">

                <div class="modal-header">
                    <h4 class="modal-title">Add Rectification</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-4 mb-2">
                            <label>Date</label>
                            <input type="date" name="work_date" value="<?php echo date('Y-m-d'); ?>" class="form-control">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Path Name</label>
                            <input type="text" name="path_name" class="form-control">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Hope To Hope</label>
                            <input type="text" name="hope_to_hope" class="form-control">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Event Lat/Long</label>
                            <input type="text" name="event_lat_long" class="form-control">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Kind Of Work</label>
                            <input type="text" name="kind_of_work" class="form-control">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Work Authority</label>
                            <input type="text" name="work_authority" class="form-control">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Contact No</label>
                            <input type="text" name="contact_no" class="form-control">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Picture</label>
                            <input type="file" name="picture_code" accept="image/*" class="form-control">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>T/C Name</label>
                            <input type="text" name="tc_name" class="form-control">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Last Check Date</label>
                            <input type="date" name="last_check_date"
                               value="<?php echo date('Y-m-d'); ?>"
                               class="form-control">
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Remark</label>
                            <textarea name="remark" class="form-control"></textarea>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" name="save_data" class="btn btn-success">
                        Save
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>



<div class="modal fade" id="editModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="POST" enctype="multipart/form-data">

                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="old_picture_code" id="edit_old_picture_code">

                <div class="modal-header">
                    <h4 class="modal-title">Update Rectification</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-4 mb-2">
                            <label>Date</label>
                            <input type="date" name="work_date" id="edit_work_date" class="form-control">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Path Name</label>
                            <input type="text" name="path_name" id="edit_path_name" class="form-control">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Work Status</label>
                            <input type="text" name="work_status" id="edit_work_status" class="form-control">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Hope To Hope</label>
                            <input type="text" name="hope_to_hope" id="edit_hope_to_hope" class="form-control">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Event Lat/Long</label>
                            <input type="text" name="event_lat_long" id="edit_event_lat_long" class="form-control">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Kind Of Work</label>
                            <input type="text" name="kind_of_work" id="edit_kind_of_work" class="form-control">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Work Authority</label>
                            <input type="text" name="work_authority" id="edit_work_authority" class="form-control">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Contact No</label>
                            <input type="text" name="contact_no" id="edit_contact_no" class="form-control">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Picture</label>
                            <div class="mb-1">
                                <img id="edit_picture_preview" src="" alt="current picture"
                                     style="width:60px;height:60px;object-fit:cover;border-radius:4px;border:1px solid #ddd;display:none;">
                                <span id="edit_picture_none" class="no-image-text">No Image</span>
                            </div>
                            <input type="file" name="picture_code" id="edit_picture_code" accept="image/*" class="form-control">
                            <small class="text-muted">You can Change The Image</small>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>T/C Name</label>
                            <input type="text" name="tc_name" id="edit_tc_name" class="form-control">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Last Check Date</label>
                            <input type="date" name="last_check_date" id="edit_last_check_date" class="form-control">
                        </div>

                        <div class="col-md-12 mb-2">
                            <label>Remark</label>
                            <textarea name="remark" id="edit_remark" class="form-control"></textarea>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" name="update_data" class="btn btn-primary">
                        Update
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>


<!-- Remarks View Modal -->
<div class="modal fade" id="remarksModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" style="color:black"><i class="fa fa-comment"></i> Full Remarks</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="white-space: pre-wrap; word-wrap: break-word; font-size: 14px; padding: 20px;">
                <p id="full_remarks_text"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Image View Modal -->
<div class="modal fade" id="imageViewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" style="color:black"><i class="fa fa-image"></i> Picture</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="full_image_view" src="" alt="picture" style="max-width:100%; max-height:70vh;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>
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
<script>
    $(document).on('click','.editData',function(){

    $('#edit_id').val($(this).data('id'));
    $('#edit_work_date').val($(this).data('work_date'));
    $('#edit_path_name').val($(this).data('path_name'));
    $('#edit_work_status').val($(this).data('work_status'));
    $('#edit_hope_to_hope').val($(this).data('hope_to_hope'));
    $('#edit_event_lat_long').val($(this).data('event_lat_long'));
    $('#edit_kind_of_work').val($(this).data('kind_of_work'));
    $('#edit_work_authority').val($(this).data('work_authority'));
    $('#edit_contact_no').val($(this).data('contact_no'));
    $('#edit_tc_name').val($(this).data('tc_name'));
    $('#edit_remark').val($(this).data('remark'));
    $('#edit_last_check_date').val($(this).data('last_check_date'));

    // Picture field reset + old value hidden field e set
    var pictureCode = $(this).data('picture_code');
    $('#edit_old_picture_code').val(pictureCode);
    $('#edit_picture_code').val('');

    if (pictureCode) {
        $('#edit_picture_preview').attr('src', pictureCode).show();
        $('#edit_picture_none').hide();
    } else {
        $('#edit_picture_preview').hide();
        $('#edit_picture_none').show();
    }

    $('#editModal').modal('show');

});

// Edit modal e notun file select korle live preview dekhabe
$(document).on('change', '#edit_picture_code', function(){
    var file = this.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(e){
            $('#edit_picture_preview').attr('src', e.target.result).show();
            $('#edit_picture_none').hide();
        };
        reader.readAsDataURL(file);
    }
});


$('#datatable_view_client').DataTable({
        dom: 'Bfrtip',
        scrollX: true,
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Excel',
                className: 'btn btn-success btn-sm'
            },
            {
                extend: 'csvHtml5',
                text: 'CSV',
                className: 'btn btn-info btn-sm'
            },
            {
                extend: 'print',
                text: 'Print',
                className: 'btn btn-primary btn-sm'
            }
        ]
    });




$(document).on('click', '.toggleStatus', function () {

    var btn = $(this);
    var id = btn.data('id');
    var status = btn.data('status');

    $.ajax({
        url: 'modules/marketing/update_status.php',
        type: 'POST',
        data: {
            id: id,
            status: status
        },
        success: function (res) {

            if (status == 1) {
                btn.removeClass('btn-success').addClass('btn-warning');
                btn.text('Pending');
                btn.data('status', 0);
            } else {
                btn.removeClass('btn-warning').addClass('btn-success');
                btn.text('Done');
                btn.data('status', 1);
            }

        }
    });

});

// View Full Remarks Modal Action
$(document).on('click', '.viewRemarks', function(){
    var fullRemarks = $(this).data('remarks');
    $('#full_remarks_text').text(fullRemarks);
    $('#remarksModal').modal('show');
});

// View Full Image Modal Action
$(document).on('click', '.viewImage', function(){
    var imgSrc = $(this).data('image');
    $('#full_image_view').attr('src', imgSrc);
    $('#imageViewModal').modal('show');
});

</script>