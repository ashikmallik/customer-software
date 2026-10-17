<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

if (isset($_POST['packageAddSubmit'])) {
    $package_form_data = array(
        'package_name' => $_POST['packageName'],
        'net_speed' => $_POST['packageNetSpeed'],
        'bill_amount' => $_POST['packageBillAmount'],
        'type' => $_POST['type'],
        'created_by' => $userid
    );
    $addPackage = $obj->Insert_data("tbl_package", $package_form_data);
}


// ========== Delete Function Start =================
$dltoken = isset($_GET['dltoken']) ? $_GET['dltoken'] : NULL;
if (!empty($dltoken)) {
    $dele = $obj->Delete_data("tbl_package", "tbl_package.package_id='$dltoken'");
    if ($dele) {
        $notification = 'Delete Successfull';
    } else {
        $notification = 'Delete Failed';
    }
}
// ========== Delete Function End =================

?>
<script>
    function numbersOnly(e) // Numeric Validation
    {
        var unicode = e.charCode ? e.charCode : e.keyCode
        if (unicode != 8) {
            if ((unicode < 2534 || unicode > 2543) && (unicode < 48 || unicode > 57)) {
                return false;
            }
        }
    }
</script>
<div class="col-md-12" style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
    <b><?php echo isset($notification) ? $notification : NULL; ?></b>
</div>
<div class="col-md-12" style=" background:#606060; margin-top:20px; margin-bottom: 15px; min-height:45px; padding:8px 0px 0px 15px; font-size:16px; font-family:Lucida Sans Unicode; color:#FFFFFF; font-weight:bold;">
    <div class="col-md-6">
        <b>View Package info</b>
    </div>
    <div class="col-md-6">
        <?php if ($ty == 'SA' || $ty == 'EO' && $obj->userWorkPermission($acc, 'add')) { ?>
            <button type="button" style="padding-top: 0px;padding-bottom : 0px;" class="btn btn-default pull-right" data-toggle="modal" data-target="#addPackageModalPPPoE">
                <span class="glyphicon glyphicon-plus"></span> Add PPPoE Package
            </button>
            <button type="button" style="padding-top: 0px;padding-bottom : 0px;" class="btn btn-default pull-right" data-toggle="modal" data-target="#addPackageModalQueue">
                <span class="glyphicon glyphicon-plus"></span> Add Queue Package
            </button>
        <?php } ?>
    </div>
</div>
<!-- all user show -->
<div id="div_all" class="row" style="padding:10px;font-size: 12px;">

    <div class="col-md-6">
        
        <?php if ($ty == 'SA' || $ty == 'EO' && $obj->userWorkPermission($acc, 'add')) { ?>
            <button type="button"  class="btn btn-primary btn-lg pull-right" data-toggle="modal" data-target="#addPackageModalPPPoE">
                <span class="glyphicon glyphicon-plus"></span> Add PPPoE Package
            </button>
        <?php } ?>
        <h2>PPPoE Package</h2>
        <div class="table-responsive">
            <table style="margin-left:-15px;" class="table table-bordered table-hover table-striped" id="example">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Package Name</th>
                        <th>Speed</th>
                        <th>Bill Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <?php
                $i = '0';
                foreach ($obj->view_all_by_cond("tbl_package",'type=1') as $value) {
                    $i++;
                ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo isset($value['package_name']) ? $value['package_name'] : NULL; ?></td>
                        <td><?php echo isset($value['net_speed']) ? $value['net_speed'] : NULL; ?></td>
                        <td><?php echo isset($value['bill_amount']) ? $value['bill_amount'] : NULL; ?></td>
                        <td>
                            <div class="btn-group">
                                <a class="btn btn-xs btn-primary" style="padding:5px;" href="?q=edit_package&token=<?php echo isset($value['package_id']) ? $value['package_id'] : NULL ?>">
                                    <span class="glyphicon glyphicon-edit"></span>
                                </a>
                                <a href="?q=view_all_package&dltoken=<?php echo isset($value['package_id']) ? $value['package_id'] : NULL; ?>" class="btn btn-xs btn-danger" onclick="return confirm('Are you sure you want to delete this item?');" style="padding:5px;">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </table>
        </div>
    </div>

    <div class="col-md-6">
       
        <?php if ($ty == 'SA' || $ty == 'EO' && $obj->userWorkPermission($acc, 'add')) { ?>
            <button type="button" class="btn btn-info btn-lg pull-right" data-toggle="modal" data-target="#addPackageModalQueue">
                <span class="glyphicon glyphicon-plus"></span> Add Queue Package
            </button>
        <?php } ?>
        <h2>Queue Package</h2>
        <div class="table-responsive">
            <table style="margin-left:-15px;" class="table table-bordered table-hover table-striped" id="example">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Package Name</th>
                        <th>Speed</th>
                        <th>Bill Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <?php
                $i = '0';
                foreach ($obj->view_all_by_cond("tbl_package","type=2") as $value) {
                    $i++;
                ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo isset($value['package_name']) ? $value['package_name'] : NULL; ?></td>
                        <td><?php echo isset($value['net_speed']) ? $value['net_speed'] : NULL; ?></td>
                        <td><?php echo isset($value['bill_amount']) ? $value['bill_amount'] : NULL; ?></td>
                        <td>
                            <div class="btn-group">
                                <a class="btn btn-xs btn-info" style="padding:5px;" href="?q=edit_package&token=<?php echo isset($value['package_id']) ? $value['package_id'] : NULL ?>">
                                    <span class="glyphicon glyphicon-edit"></span>
                                </a>
                                <a href="?q=view_all_package&dltoken=<?php echo isset($value['package_id']) ? $value['package_id'] : NULL; ?>" class="btn btn-xs btn-danger" onclick="return confirm('Are you sure you want to delete this item?');" style="padding:5px;">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </a>
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



<!-- Modal PPPoE Start-->
<div class="modal fade" id="addPackageModalPPPoE" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center">New PPPoE Package</h4>
            </div>
            <form method="post">
                <input type="hidden" name="type" value="1">
                <div class="modal-body">
                    <div class="row" style="padding:0 10px;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Package Name*</label>
                                <input type="text" name="packageName" class="form-control"  placeholder="Provide Package Name" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Net Speed*</label>
                                <input type="text" name="packageNetSpeed" class="form-control" placeholder="Mikrotik Profile name" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Bill Amount*</label>
                                <input onkeypress="return numbersOnly(event)" type="text" name="packageBillAmount" class="form-control"  placeholder="Provide Bill Amount" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer text-center">
                    <button type="submit" class="btn btn-success pull-left" name="packageAddSubmit"> Save Package
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>

    </div>
</div>
<!-- Modal PPPoE End-->

<!-- Modal Queue Start-->
<div class="modal fade" id="addPackageModalQueue" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center">New Queue Package</h4>
            </div>
            <form method="post">
                <input type="hidden" name="type" value="2">
                <div class="modal-body">
                    <div class="row" style="padding:0 10px;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Package Name*</label>
                                <input type="text" name="packageName" class="form-control"  placeholder="Provide Package Name" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Net Speed*</label>
                                <input type="text" name="packageNetSpeed" class="form-control" placeholder="max-limit :  1M/1M" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Bill Amount*</label>
                                <input onkeypress="return numbersOnly(event)" type="text" name="packageBillAmount" class="form-control"  placeholder="Provide Bill Amount" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer text-center">
                    <button type="submit" class="btn btn-success pull-left" name="packageAddSubmit"> Save Package
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>

    </div>
</div>
<!-- Modal Queue End-->