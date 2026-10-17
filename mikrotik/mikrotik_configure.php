<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$mikrotikData = $obj->view_all('mikrotik_user');
$users = $obj->view_all_by_cond('vw_user_info',"UserType='SA'");

if (isset($_POST['addMikrotikAdmin'])) {
    extract($_POST);
    $form_data_mikrotik = array(
        'mik_username' => $mikrotik_Name,
        'mik_password' => $password,
        'mik_ip' => $mikrotik_IP,
        'entry_by' => $userid,
        'entry_date' => $date,
        'update_by' => $userid
    );

    $mikrotik_table_count = $obj->Total_Count('mikrotik_user','id != 0');

    $mikrotik_admin_add = $obj->Reg_user_cond("mikrotik_user", $form_data_mikrotik, "");
    $notification = "Admin User added Successfully";

    if(isset($mikrotik_admin_add) ||isset($mikrotik_admin_update)){ ?>
        <script>window.location = "?q=mikrotik_configure";</script>
        <?php
    }
}
// ========== Delete Function Start =================
$dltoken = isset($_GET['dltoken']) ? $_GET['dltoken'] : NULL;
if (!empty($dltoken)) {

    $dele = $obj->Delete_data("mikrotik_user", "mikrotik_user.id='$dltoken'");

    if ($dele) {
        $notification = 'Delete Successfull';
        ?>
        <script>window.location = "?q=mikrotik_configure";</script>
        <?php
    } else {
        $notification = 'Delete Failed';
    }
}
// ========== Delete Function End =================

if (isset($_POST['UserId'])){
    $mik_user_add_data = $obj->details_by_cond('mikrotik_user','id='.$_POST['mik_id']);

    $form_mik_user_add = array(
        'user_id' => $_POST['UserId'],
        'mik_username' => $mik_user_add_data['mik_username'],
        'mik_password' => $mik_user_add_data['mik_password'],
        'mik_ip' => $mik_user_add_data['mik_ip'],
        'entry_by' => $userid,
        'entry_date' => $date,
        'update_by' => $userid

    );

    $obj->Insert_data('mikrotik_user',$form_mik_user_add);

    echo '<script>window.location = "?q=mikrotik_configure";</script>';
}
?>
<!--===================end Function===================-->

<div class="col-md-12">

    <?php if(isset($notification)){ ?>
        <div class="alert alert-warning alert-dismissable fade in text-center">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong><?php echo $notification; ?></strong>
        </div>
    <?php } ?>
</div>
<div class="row" style="padding:10px; font-size: 12px;">
    <h2 class="text-center text-slate">Welcome to Mikrotik Panel</h2>
    <p class="text-center text-slate-700"><strong>Please Enter Your Mikrotik Login Information</strong></p>
    <hr>
    <div class="col-md-8 col-md-offset-2">
        <div class="table-responsive">
            <table style="margin-left:-15px;" class="table table-bordered table-hover table-striped" id="example">
                <thead>
                <tr>
                    <th>SL</th>
                    <th>Mikrotik User</th>
                    <th>Connection IP</th>
                    <th>Entry Date</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $serial=1;
                foreach ($mikrotikData as $data){ ?>
                <tr>
                    <td><?= $serial++ ?></td>
                    <td><?= $data['mik_username'] ?></td>
                    <td><?= $data['mik_ip'] ?></td>
                    <td><?= date('d, M Y', strtotime($data['entry_date'])) ?></td>
                    <td>
                        <a href="?q=mikrotik_edit&mik_user_id=<?=$data['id']?>" class="btn btn-primary btn-xs">Edit</a>
                        <!--<button id="add-user" data-toggle="modal" data-target="#exampleModal" data-mik-id="<?= $data['id']?>" data-whatever="@mdo" class="btn btn-primary btn-xs">Add User</button>-->
                        <?php if($data['id'] != 1){ ?>
                        <!--<a href="?q=mikrotik_configure&dltoken=<?php echo isset($data['id']) ? $data['id'] : NULL; ?>"
                                   class="btn btn-xs btn-danger" onclick="return confirm('Are you sure you want to delete this item?');" style="padding:5px;">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </a>-->
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
                </tbody></table>
        </div>
    </div>

    <hr class="col-md-12">

    <div class="col-md-6 col-md-offset-3">
        <h4 class="text-center text-slate-700"><strong>Add New Mikrotik</strong></h4>
        <form role="form" enctype="multipart/form-data" method="post">
            <div class="row" style="font-size: 12px;">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Mikrotik Login Name</label>
                        <input type="text" name="mikrotik_Name" class="form-control" id="ResponsiveTitle" value="<?php echo isset($mikrotikData['mik_username'])?$mikrotikData['mik_username']:null; ?>" placeholder="Provide Mikrotik Admin Name" required="required">
                    </div>
                    <div class="form-group">
                        <label>Mikrotik Password</label>
                        <input type="password" name="password" class="form-control" id="ResponsiveTitle" value="<?php echo isset($mikrotikData['mik_password'])?$mikrotikData['mik_password']:null;?>" placeholder="Provide Login Password">
                    </div>
                    <div class="form-group">
                        <label>Connection IP of Mikrotik</label>
                        <input type="text" name="mikrotik_IP" class="form-control" id="clientId" placeholder="Provide Connection IP" value="<?php echo isset($mikrotikData['mik_ip'])?$mikrotikData['mik_ip']:null; ?>" required="required">
                        <div id="checkavailablityClientId" class="text-center">
                        </div>
                    </div>
                </div>
            </div>
            <!--            row-->
            <div class="row">
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary" name="addMikrotikAdmin">Add New</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-2 padding_10_px">
        <div class="alert alert-warning">
            <strong>Please!</strong> Check Your MIKROTIK API port is enable.
        </div>
        <div class="alert alert-warning">
            <strong>Enable API port </strong> <br> IP -> Services -> Enable api port from WinBox .
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">User Add</h4>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="recipient-name" class="control-label">User:</label>
                        <select class="form-control" name="UserId">
                            <option value="">Select</option>
                            <?php foreach ($users as $user) {
                                $continue = true;
                                foreach ($obj->view_all_by_cond('mikrotik_user','user_id!=0') as $mik_exist_user) {
                                    if ($user['UserId']==$mik_exist_user['user_id']){
                                        $continue = false;
                                    }
                                }

                                if (!$continue){continue;}
                                ?>
                                <option value="<?= $user['UserId']?>"><?= $user['FullName']?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <input type="hidden" name="mik_id" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Send message</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('button#add-user').on('click', function () {
        var mik_id = $(this).data('mik-id');

        $('input[name="mik_id"]').val(mik_id);
    });
</script>
