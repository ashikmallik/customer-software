<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');

$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;


// ========== Delete Function Start =================
if( $obj->userWorkPermission($acc,'delete')){

    $dltoken = isset($_GET['dltoken']) ? $_GET['dltoken'] : NULL;
    if (!empty($dltoken)) {

        $deleteZone = $obj->Delete_data("tbl_zone", "tbl_zone.zone_id='$dltoken'");

        if ($deleteZone) {
            $notification = 'Delete Successfull';
        } else {
            $notification = 'Delete Failed';
        }
    }
}
// ========== Delete Function End =================

if (isset($_POST['zoneAddSubmit'])) {

    $zone_form_data = array(
        'zone_name' => $_POST['zoneName'],
        'created_by' => $userid
    );
    $addZone = $obj->Reg_user_cond("tbl_zone", $zone_form_data, " ");
    if ($addZone) {
        ?>
        <script>
            window.location = "?q=view_all_zone";
        </script>
        <?php
    } else {
        echo $notification = 'Zone Insert Failed';
    }
}
// ================= Zone Add ==========================
?>

<?php if (isset($notification) && !empty($notification)) { ?>
    <div class="alert alert-warning alert-dismissable fade in text-center">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <?php echo isset($notification) ? $notification : NULL; ?>
    </div>
<?php } ?>

<div class="row" style="padding: 6px;">
    <div class="col-md-12 bg-grey-600 padding_10_px">
        <div class="col-md-6">
            <h4>View zone info</h4>
        </div>
        <div class="col-md-6" style="">
            <?php if ($ty == 'SA') { ?>
                <button type="button" class="btn btn-default pull-right" data-toggle="modal"
                        data-target="#addZoneModal">
                    <span class="glyphicon glyphicon-plus"></span> Add zone
                </button>
            <?php } ?>
        </div>
    </div>
</div>

    <div class="row">

        <div id="div_all" class="row" style="font-size: 12px;">
            <div class="col-md-8 col-md-offset-2">
                <div class="table-responsive">
                    <table style="margin-left:-15px;" class="table table-bordered table-hover table-striped" id="datatable">
                        <thead>
                            <tr> 
                                <th class="text-center">SL</th>
                                <th class="text-center">Zone Name</th>
                                <th class="text-center">Created By</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <?php
                        $i = '0';
                        foreach ($obj->view_all("tbl_zone") as $value) {
                            $i++;
                            ?>
                            <tr>
                                <td class="text-center"><?php echo $i; ?></td>
                                <td class="text-center"><?php echo isset($value['zone_name']) ? $value['zone_name'] : NULL; ?></td>
                                <td class="text-center"><?php
                                    $userData = $obj->details_by_cond('_createuser', "UserId = " . $value['created_by'] . "");
                                    echo $userData['FullName'];
                                    ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <?php if( $obj->userWorkPermission($acc,'edit')){ ?>
                                        <a class="btn btn-xs btn-info" style="padding:5px;"
                                           href="?q=edit_zone&token=<?php echo isset($value['zone_id']) ? $value['zone_id'] : NULL ?>">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>
                                        <?php }
                                        if( $obj->userWorkPermission($acc,'delete')){ ?>
                                        <a href="?q=view_all_zone&dltoken=<?php echo isset($value['zone_id']) ? $value['zone_id'] : NULL; ?>"
                                           class="btn btn-xs btn-danger" onclick="return confirm('Are you sure you want to delete this zone?');" style="padding:5px;">
                                            <span class="glyphicon glyphicon-remove"></span>
                                        </a>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
<!-- Modal Start-->
<div class="modal fade" id="addZoneModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center">Insert New Zone</h4>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="row" style="padding:0 10px;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="row">
                                    <label>Zone Name</label>
                                    <input type="text" name="zoneName" class="form-control" id="ResponsiveTitle" placeholder="Provide Zone Name">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-default" name="zoneAddSubmit"> Insert Zone
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
<!-- Modal End-->