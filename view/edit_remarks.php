<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$token = isset($_GET['token']) ? $_GET['token'] : NULL;
$details = $obj->details_by_cond("tbl_remarks", "tbl_remarks.id='$token'");
extract($details);
if (isset($_POST['update'])) {
    extract($_POST);

    $form_data = array(
        'remarks' => $_POST['remarks'],
    );
    $updateRemarks = $obj->Update_data("tbl_remarks", $form_data, "tbl_remarks.id='$token'");

    if ($updateRemarks) {
        ?>
        <script>
           // window.location = "?q=view_agent";
           var url=window.location.href;
            window.location = url;
        </script>
        <?php
    } else {
        echo $notification = 'Update Failed';
    }
}
?>

<?php if (isset($notification) && !empty($notification)) { ?>
    <div class="alert alert-warning alert-dismissable fade in text-center">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <?php echo isset($notification) ? $notification : NULL; ?>
    </div>
<?php } ?>

<div class="row" style="padding:10px; font-size: 12px;">
    <div class="col-md-12" style="margin-bottom:30px;">
        <div class="bg-teal text-center" style="padding:5px;">
            <h4>Update Remarks</h4>
        </div>
    </div>
    <div class="col-md-6 col-md-offset-3">
        <form method="post">
            <div class="form-group row">
                <div class="text-center">
                    <h5 class="col-md-4">Remarks:</h5>
                    <div class="col-md-8">
                        <textarea class="form-control" name="remarks" id="remarks" rows="2"><?php echo $details['remarks']?></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="text-center">
                    <button type="submit" class="btn btn-primary" name="update"> Update Remarks
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>