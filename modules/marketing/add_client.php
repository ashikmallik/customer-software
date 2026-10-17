<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

// Generate New ID
$data = $obj->details_by_cond("tbl_marketing_agent", "ag_id!=0 ORDER BY ag_id DESC");
if (($data['ag_id'] + 1) < 10) {
    $STD = "CLNT0000";
} else if (($data['ag_id'] + 1) < 100) {
    $STD = "CLNT000";
} else if (($data['ag_id'] + 1) < 1000) {
    $STD = "CLNT00";
} else if (($data['ag_id'] + 1) < 10000) {
    $STD = "CLNT0";
} else {
    $STD = "CLNT";
}
$STD .= $data['ag_id'] + 1;

//===================Add Function===================
if (isset($_POST['submit'])) {
    extract($_POST);
    
    $form_data_tbl_client = array(
        'link_name'      => str_replace("'", '', $link_name),
        'e_pole_req'     => str_replace("'", '', $e_pole_req),
        'total_epole'    => str_replace("'", '', $total_epole),
        'survey_date'    => date("Y-m-d", strtotime($survey_date)),
        'fiber_req'      => str_replace("'", '', $fiber_req),
        'di_road_cross'  => str_replace("'", '', $di_road_cross),
        'path_address'   => str_replace("'", "", $path_address),
        'others'         => str_replace("'", "", $others),
        'rail_cross'     => str_replace("'", "", $rail_cross),
        'open_cut'       => $open_cut,
        'entry_by'       => $userid,
        'entry_date'     => date('Y-m-d'),
        'ag_status'      => 1,
        'update_by'      => $userid,
        'status'         => $status,
    );

    if ($obj->Insert_data("tbl_marketing_agent", $form_data_tbl_client)) {
        $obj->notificationStore('Survey Stored Successfully', 'success');
        echo '<script>window.location.href = window.location.href;</script>';
    } else {
        $obj->notificationStore('Failed to Store Survey', 'danger');
    }
}
?>
<!--===================End Function===================-->

<script>
    function numbersOnly(e) {
        var unicode = e.charCode ? e.charCode : e.keyCode;
        if (unicode != 8) {
            if ((unicode < 2534 || unicode > 2543) && (unicode < 48 || unicode > 57)) {
                return false;
            }
        }
    }
</script>

<div class="row">
    <div class="col-md-12 padding_5_px">
        <?php $obj->notificationShowRedirect(); ?>
    </div>
</div>

<div class="row panel">
    <div class="panel-body col-md-12 bg-teal-800 text-center" style="margin-bottom:5px;">
        <h4>Add New Survey : <?php echo $STD; ?></h4>
    </div>
</div>

<form role="form" method="post">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Link Name</label>
                <input type="text" name="link_name" class="form-control" placeholder="Enter Link Name" required>
            </div>

            <div class="form-group">
                <label> Pole Req.</label>
                <input type="text" name="e_pole_req" class="form-control" placeholder="Enter E. Pole Requirement">
            </div>

            <div class="form-group">
                <label>TOTAL E.Pole</label>
                <input type="text" name="total_epole" class="form-control" placeholder="Enter Total E.Pole">
            </div>

            <div class="form-group">
                <label>Survey Date</label>
                <input id="datepicker" type="text" name="survey_date" class="form-control" placeholder="dd/mm/yyyy" autocomplete="off" required>
            </div>

            <div class="form-group">
                <label>Path Address</label>
                <textarea class="form-control" name="path_address" rows="5" placeholder="Enter Path Address"></textarea>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label>Fiber Req.</label>
                
                 <input type="text" name="fiber_req" class="form-control" autocomplete="off" required>
                
                
            </div>

            <div class="form-group">
                <label>Road Cross</label>
                 <input type="text" name="di_road_cross" class="form-control" autocomplete="off" required>
            </div>

            <div class="form-group">
                <label>Rail Cross</label>
                <input type="text" name="rail_cross" class="form-control" placeholder="Enter Rail Cross Info">
            </div>

            <div class="form-group">
                <label>Open Cut</label>
               
                 <input type="text" name="open_cut" class="form-control" autocomplete="off" required>
            </div>

            <div class="form-group">
                <label>Others (Text)</label>
                <textarea class="form-control" name="others" rows="5" placeholder="Enter Additional Details"></textarea>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="">Select Status</option>
                    <option value="0">Pending</option>
                    <option value="1">Done</option>
                </select>
            </div>
        </div>
    </div>

    <div class="form-group text-center">
        <button type="submit" class="btn btn-success" name="submit">
            <i class="fa fa-save"></i> Save Survey
        </button>
    </div>
</form>

<link rel="stylesheet" type="text/css" href="asset/css/bootstrap-datepicker.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>

<script>
    $(document).ready(function () {
        $('#datepicker').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true
        });
    });
</script>