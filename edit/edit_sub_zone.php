<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$token = isset($_GET['token']) ? $_GET['token'] : NULL;

$details = $obj->details_by_cond("tbl_sub_zone", "tbl_sub_zone.sub_zone_id ='$token'");

extract($details);

if (isset($_POST['update'])) {
    extract($_POST);

    $form_data = array(
        'zone_id' => $_POST['zone'],
        'sub_zone_name' => $_POST['subZoneName'],
    );
    $updateZone = $obj->Update_data("tbl_sub_zone", $form_data, "tbl_sub_zone.sub_zone_id='$token'");

    if ($updateZone) {
        ?>
        <script>
            window.location = "?q=view_all_sub_zone";
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
            <h4>Edit zone</h4>
        </div>
    </div>
    <div class="col-md-6 col-md-offset-3">
        <form method="post">
            <div class="form-group row">
                <div class="text-center">
                    <h5 class="col-md-4">Zone Name :</h5>
                    <div class="col-md-8">
                    <select class="form-control" name="zone" required="required" >
                                <option value='' >Select Zone</option>
                                <?php foreach ($obj->view_all('tbl_zone') as $singleZone) { ?>
                                    <option value="<?php echo $singleZone['zone_id'] ?>" <?php  echo ($details['zone_id'] == $singleZone['zone_id']) ? 'selected' : ''; ?> ><?php  echo $singleZone['zone_name'] ?></option>
                                <?php }// foreach ?>
                            </select>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="text-center">
                    <h5 class="col-md-4">Sub Zone Name :</h5>
                    <div class="col-md-8">
                        <input type="text" name="subZoneName"
                               value="<?php echo $details['sub_zone_name'] ? $details['sub_zone_name'] : NULL; ?>"
                               class="form-control" id="ResponsiveTitle" placeholder="Provide zone Name">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="text-center">
                    <button type="submit" class="btn btn-primary" name="update"> Update zone Info
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>