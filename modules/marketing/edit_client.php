<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$token = isset($_GET['token']) ? $_GET['token'] : NULL;

$cusInfo = $obj->details_by_cond("tbl_marketing_agent", "ag_id='$token'");

//===================Update Function===================
if (isset($_POST['update'])) {
    extract($_POST);
    
    $form_data_tbl_client = array(
        'link_name'      => str_replace("'", '', $link_name),
        'e_pole_req'     => str_replace("'", '', $e_pole_req),
        'total_epole'    => str_replace("'", '', $total_epole),
        'survey_date'    => date("Y-m-d", strtotime($survey_date)),
        'fiber_req'      => str_replace("'", '', $fiber_req),
        'di_road_cross'  => str_replace("'", '', $di_road_cross),
        'path_address'   => str_replace("'", "", $path_address),
        'rail_cross'     => str_replace("'", "", $rail_cross),
        'open_cut'       => $open_cut,
        'others'         => str_replace("'", "", $others),
        'update_by'      => $userid
    );

    $customer_update = $obj->Update_data("tbl_marketing_agent", $form_data_tbl_client, "where ag_id='$token'");
    
    if ($customer_update) {
        $obj->notificationStore('Survey Updated Successfully', 'success');
        echo '<script>window.location.href = window.location.href;</script>';
    } else {
        $obj->notificationStore('Failed to Update Survey', 'danger');
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
        <h4>Edit Survey : <?php echo $cusInfo['ag_id']; ?></h4>
    </div>
</div>

<form role="form" method="post">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Link Name</label>
                <input type="text" name="link_name" value="<?php echo isset($cusInfo['link_name']) ? $cusInfo['link_name'] : ''; ?>" 
                       class="form-control" placeholder="Enter Link Name" required>
            </div>

            <div class="form-group">
                <label>E. Pole Req.</label>
                <input type="text" name="e_pole_req" value="<?php echo isset($cusInfo['e_pole_req']) ? $cusInfo['e_pole_req'] : ''; ?>" 
                       onkeypress="return numbersOnly(event)" class="form-control" placeholder="Enter E. Pole Requirement">
            </div>

            <div class="form-group">
                <label>E. TOTAL E.Pole</label>
                <input type="text" name="total_epole" value="<?php echo isset($cusInfo['total_epole']) ? $cusInfo['total_epole'] : ''; ?>" 
                       class="form-control" placeholder="Enter Total E.Pole">
            </div>

            <div class="form-group">
                <label>Survey Date</label>
                <input id="" type="date" name="survey_date" value="<?php echo $cusInfo['survey_date'] ?>" 
                       class="form-control" autocomplete="off" required>
            </div>

            <div class="form-group">
                <label>Path Address</label>
                <textarea class="form-control" name="path_address" rows="5"><?php echo isset($cusInfo['path_address']) ? $cusInfo['path_address'] : ''; ?></textarea>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label>Fiber Req.</label>
                <input type="text" name="fiber_req" value="<?php echo isset($cusInfo['fiber_req']) ? $cusInfo['fiber_req'] : ''; ?>" 
                       class="form-control" placeholder="Enter Rail Cross Info">
            </div>

            <div class="form-group">
                <label>DI Road Cross</label>
                <input type="text" name="di_road_cross" value="<?php echo isset($cusInfo['di_road_cross']) ? $cusInfo['di_road_cross'] : ''; ?>" 
                       class="form-control" placeholder="Enter Rail Cross Info">
            </div>

            <div class="form-group">
                <label>Rail Cross</label>
                <input type="text" name="rail_cross" value="<?php echo isset($cusInfo['rail_cross']) ? $cusInfo['rail_cross'] : ''; ?>" 
                       class="form-control" placeholder="Enter Rail Cross Info">
            </div>

            <div class="form-group">
                <label>Open Cut</label>
                
                <input type="text" name="open_cut" value="<?php echo isset($cusInfo['open_cut']) ? $cusInfo['open_cut'] : ''; ?>" 
                       class="form-control" placeholder="Enter Rail Cross Info">
            </div>

            <div class="form-group">
                <label>Others (Text)</label>
                <textarea class="form-control" name="others" rows="5"><?php echo isset($cusInfo['others']) ? $cusInfo['others'] : ''; ?></textarea>
            </div>
        </div>
    </div>

    <div class="form-group text-center">
        <button type="submit" class="btn btn-success" name="update">
            <i class="fa fa-save"></i> Update Survey
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