<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$token = isset($_GET['token']) ? $_GET['token'] : NULL;
$agent = $obj->view_all_by_cond("tbl_remarks", "ag_id='$token'");

// ========== Delete Function Start =================
if( $obj->userWorkPermission($acc,'delete')){

    $dltoken = isset($_GET['dltoken']) ? $_GET['dltoken'] : NULL;
    if (!empty($dltoken)) {

        $deleteZone = $obj->Delete_data("tbl_remarks", "tbl_remarks.id='$dltoken'");

        if ($deleteZone) {
            $notification = 'Delete Successfull';
            ?>
            <script>
           // window.location = "?q=agent_remarks&token="token;
           window.location = "?q=view_agent";
           </script>
           <?php
        } else {
            $notification = 'Delete Failed';
        }
    }
}
// ========== Delete Function End =================

if (isset($_POST['remarksAddSubmit'])) {
    $form_data_tbl_remarks = array(
            'ag_id' => $token,
            'remarks' => $_POST['remarks'],
            'created_at' => date('Y-m-d', strtotime('first day of this month')),
            'entry_by' => $userid,
            'updated_by' =>$userid
        );
        $remarks_add = $obj->Reg_user_cond("tbl_remarks", $form_data_tbl_remarks, " ");
    if ($remarks_add) {
        ?>
        <script>
        	var url=window.location.href;
            window.location = url;
        </script>
        <?php
    } else {
        echo $notification = 'Remarks Insert Failed';
    }
}

?>
<?php if (isset($notification) && !empty($notification)) { ?>
    <div class="alert alert-warning alert-dismissable fade in text-center">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <?php echo isset($notification) ? $notification : NULL; ?>
    </div>
<?php } ?>
<div class="row" style="padding: 4px;">
    <div class="col-md-12 bg-grey-600 padding_10_px">
        <div class="col-md-6">
            <h4>View Remarks</h4>
        </div>
        <div class="col-md-6" style="">
            <?php if ($ty == 'SA') { ?>
                <button type="button" class="btn btn-default pull-right" data-toggle="modal"
                        data-target="#addRemarkModal">
                    <span class="glyphicon glyphicon-plus"></span> Add Remarks
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
                                <th class="text-center">Remarks</th>
                                <th class="text-center">Created at</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <?php
                        $i = '0';
                        foreach ($agent as $value) {
                            $i++;
                            ?>
                            <tr>
                                <td class="text-center"><?php echo $i; ?></td>
                                <td class="text-center"><?php echo isset($value['remarks']) ? $value['remarks'] : NULL; ?></td>
                                <td class="text-center"><?php
                                    echo date('h:i A', strtotime($value['last_update'])).'<br>'.date('d, M Y', strtotime($value['last_update']));
                                    ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <?php if( $obj->userWorkPermission($acc,'edit')){ ?>
                                        <a class="btn btn-xs btn-info" style="padding:5px;"
                                           href="?q=edit_remarks&token=<?php echo isset($value['id']) ? $value['id'] : NULL ?>">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>
                                        <?php }
                                        if( $obj->userWorkPermission($acc,'delete')){ ?>
                                        <a href="?q=agent_remarks&dltoken=<?php echo isset($value['id']) ? $value['id'] : NULL; ?>"
                                           class="btn btn-xs btn-danger" onclick="return confirm('Are you sure you want to delete this remarks?');" style="padding:5px;">
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

    <div class="modal fade" id="addRemarkModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center">Insert New Remarks</h4>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="row" style="padding:0 10px;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-group">
                      <label>Remarks</label>
                       <textarea class="form-control" name="remarks" id="remarks" rows="2"></textarea>
                          </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-default" name="remarksAddSubmit"> Insert Remarks
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>