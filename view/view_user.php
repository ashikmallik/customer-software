<?php
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

$intoken = isset($_GET['intoken']) ? $_GET['intoken'] : NULL;
$actoken = isset($_GET['actoken']) ? $_GET['actoken'] : NULL;


if (!empty($intoken)) {
    $form_data = array('Status' => '0', 'UpdateBy' => $userid);

    $obj->Update_data("_createuser", $form_data, "where UserId='$intoken'");
}

if (!empty($actoken)) {
    $form_data = array('Status' => '1', 'UpdateBy' => $userid);

    $obj->Update_data("_createuser", $form_data, "where UserId='$actoken'");
}
//---------------------------------------------



?>

<div class="row">
    <div class="col-md-12 bg-teal-700">
        <h4>View All User Information</h4>
    </div>
</div>

<div class="col-md-12"
     style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
    <b><?php echo isset($notification) ? $notification : NULL; ?></b>
</div>
<hr>

<div class="row" style="padding:10px; font-size: 12px;">

    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped" id="datatable">
                <thead>
                <tr class="info">
                    <th style="width: 20px;">#</th>
                    <th style="text-align: center;">Full Name</th>
                    <th style="text-align: center;">User Name</th>
                    <th style="text-align: center;">Email Address</th>
                    <th style="text-align: center;">Mobile No</th>
                    <th style="text-align: center;">User Type</th>
                    <?php if ($ty == 'SA') { ?>
                    <th style="text-align: center;">Status</th>
                    <?php } ?>
                    <th style="text-align: center;">Details</th>
                    <?php if ($ty == 'SA') { ?>
                        <th style="text-align: center;">Edit</th>
                    <?php } ?>
                        <th style="text-align: center;">Change Pass</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = '0';
                foreach ($obj->view_all("vw_user_info") as $value) {
                    extract($value);
                    $i++;
                    ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo isset($value['FullName']) ? $value['FullName'] : NULL; ?></td>
                        <td><?php echo isset($value['UserName']) ? $value['UserName'] : NULL; ?></td>
                        <td><?php echo isset($value['Email']) ? $value['Email'] : NULL; ?></td>
                        <td><?php echo isset($value['MobileNo']) ? $value['MobileNo'] : NULL; ?></td>
                        <td>
                            <?php
                            if ($value['UserType'] == 'SA') {
                                echo 'Supper Admin';
                            } elseif ($value['UserType'] == 'A') {
                                echo 'Admin';
                            } elseif ($value['UserType'] == 'EO') {
                                echo 'Entry Operator';
                            } elseif ($value['UserType'] == 'E') {
                                echo 'Editor';
                            } elseif ($value['UserType'] == 'SU') {
                                echo 'Supper User';
                            } elseif ($value['UserType'] == 'U') {
                                echo 'User';
                            }
                            ?>
                        </td>
                        <?php if ($ty == 'SA') { ?>
                        <td style="text-align: center;">
                            <?php
                            if ((isset($value['Status']) ? $value['Status'] : NULL) == '1') {
                                ?>
                                <a href="?q=view_user&intoken=<?php echo isset($value['UserId']) ? $value['UserId'] : NULL; ?>"><span
                                            class="glyphicon glyphicon-ok"></span></a>

                                <?php
                            }
                             else {
                                ?>
                                <a href="?q=view_user&actoken=<?php echo isset($value['UserId']) ? $value['UserId'] : NULL; ?>"><span
                                            class="glyphicon glyphicon-remove btn-danger"></span></a>
                            <?php } 
                            ?>
                        </td>
                    <?php } ?>
                        <td style="text-align: center;"><a
                                    href="?q=user_details&token=<?php echo isset($value['UserId']) ? $value['UserId'] : NULL; ?>"><span
                                        class="glyphicon glyphicon-eye-open"></span></a></td>
                        <?php if ($ty == 'SA') { ?>
                            <td style="text-align: center;"><a
                                        href="?q=user_edit&token=<?php echo isset($value['UserId']) ? $value['UserId'] : NULL; ?>"><span
                                            class="glyphicon glyphicon-edit"></span></a></td> <?php } ?>
                            <td style="text-align: center;"><a
                                        href="?q=user_ch_pass&token=<?php echo isset($value['UserId']) ? $value['UserId'] : NULL; ?>">Change</a>
                            </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>