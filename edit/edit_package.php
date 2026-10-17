<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$token = isset($_GET['token']) ? $_GET['token'] : NULL;

$details = $obj->details_by_cond("tbl_package", "tbl_package.package_id='$token'");

extract($details);

if (isset($_POST['update'])) {
    extract($_POST);

    $form_data = array(

        'package_name' => $_POST['packageName'],
        'net_speed' => $_POST['packageNetSpeed'],
        'bill_amount' => $_POST['packageBillAmount'],
    );
    $branch_id = $obj->Update_data("tbl_package", $form_data, "tbl_package.package_id='$token'");

    if ($branch_id) {

        ?>
        <script>
            window.location = "?q=view_all_package";
        </script>
        <?php
    } else {
        echo $notification = 'Update Failed';
    }
}
?>

<div class="col-md-12"
     style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
    <b><?php echo isset($notification) ? $notification : NULL; ?></b>
</div>
<div class="row" style="padding:10px; font-size: 12px;">
    <div class="col-md-12" style="margin-bottom:30px;">
        <div class="bg-teal text-center" style="padding:5px;">
            <h4>Edit Package</h4>
        </div>
    </div>
    <div class="col-md-6 col-md-offset-3">
        <form method="post">
            <div class="row" style="padding:0 10px;">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Package Name</label>
                        <input type="text" name="packageName"
                               value="<?php echo $details['package_name'] ? $details['package_name'] : NULL; ?>"
                               class="form-control" id="ResponsiveTitle" placeholder="Provide Package Name">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Net Speed</label>
                        <input type="text" name="packageNetSpeed"
                               value="<?php echo $details['net_speed'] ? $details['net_speed'] : NULL; ?>"
                               class="form-control" id="ResponsiveTitle" placeholder="Provide Net Speed">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Bill Amount</label>
                        <input type="text" name="packageBillAmount"
                               value="<?php echo $details['bill_amount'] ? $details['bill_amount'] : NULL; ?>"
                               class="form-control" id="ResponsiveTitle" placeholder="Provide Bill Amount">
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary" name="update"> Update Package Info
                </button>
            </div>
        </form>
    </div>
    <!--    <form role="form" enctype="multipart/form-data" method="post">    -->
    <!--                <div class="row" style="padding:10px; font-size: 12px;">-->
    <!---->
    <!--                    <div class="col-md-6">-->
    <!--                       -->
    <!--                        <div class="form-group">-->
    <!--                             <label>Customer Name</label>-->
    <!--                             <input value="-->
    <?php //echo $details['ag_name']? $details['ag_name']:NULL; ?><!--" type="text" name="Name" class="form-control" id="ResponsiveTitle"  >-->
    <!--                        </div>                                                                 -->
    <!--                       <div class="form-group">-->
    <!--                             <label>Mobile No</label>-->
    <!--                             <input value="-->
    <?php //echo $details['ag_mobile_no']? $details['ag_mobile_no']:NULL; ?><!--" type="text" name="mobile" class="form-control" id="ResponsiveTitle"  >-->
    <!--                        </div>-->
    <!--                       <div class="form-group">-->
    <!--                            <label>Address</label>-->
    <!--                             <textarea class="form-control" name="Details" id="ResponsiveDetelis" rows="6">-->
    <?php //echo $details['ag_office_address']? $details['ag_office_address']:NULL; ?><!--</textarea>-->
    <!--                        </div>-->
    <!--                                            -->
    <!--                        <div class="form-group">-->
    <!--                            <label>Amount Of Taka</label>-->
    <!--                            <input value="-->
    <?php //echo $details['taka']? $details['taka']:NULL; ?><!--" type="text" name="taka" class="form-control" id="ResponsiveTitle"  >-->
    <!--                       </div>   -->
    <!--                         <div class="form-group">-->
    <!--                            <label>Connect Charge</label>-->
    <!--                            <input value="-->
    <?php //echo $details['connect_charge']? $details['connect_charge']:NULL; ?><!--" type="text" name="connect_charge" class="form-control" id="ResponsiveTitle"  >-->
    <!--                       </div>-->
    <!--                        <div class="form-group">-->
    <!--                            <label>Amount Of MB</label>-->
    <!--                            <input value="-->
    <?php //echo $details['mb']? $details['mb']:NULL; ?><!--" type="text" name="mb" class="form-control" id="ResponsiveTitle"  >-->
    <!--                       </div> -->
    <!--						<div class="form-group">-->
    <!--                            <label>IP No</label>-->
    <!--                            <input type="text" name="ip" class="form-control" value="-->
    <?php //echo $details['ip']? $details['ip']:NULL; ?><!--" id="ResponsiveTitle" >-->
    <!--                       </div>-->
    <!--                        <div class="form-group">-->
    <!--                            <label>Email</label>-->
    <!--                            <input value="-->
    <?php //echo $details['ag_email']? $details['ag_email']:NULL; ?><!--" type="email" name="email" class="form-control" id="ResponsiveTitle" >-->
    <!--                       </div>                                                                                                         -->
    <!--                        <div class="form-group">                                                         -->
    <!--                            <label>Bill Category</label>-->
    <!--                            <select class="form-control" required="required" name="bill_cat" id="status">-->
    <!--                                <option  value="">---Category Select---</option>-->
    <!--                                <option -->
    <?php //if($details['bill_cat']=='1') echo 'selected';  ?><!-- value="1">Monthly</option>-->
    <!--                                <option -->
    <?php //if($details['bill_cat']=='2') echo 'selected';  ?><!-- value="2">Half Yearly</option>-->
    <!--                                <option -->
    <?php //if($details['bill_cat']=='3') echo 'selected';  ?><!-- value="3">Yearly</option>-->
    <!--                            </select>                       -->
    <!--                         </div>                       -->
    <!--                        <div class="form-group">                                                         -->
    <!--                            <label>Status</label>-->
    <!--                            <select class="form-control" required="required" name="status" id="status">-->
    <!--                               <option -->
    <?php //if($details['ag_status']=='1') echo 'selected';  ?><!--  value="1">Active</option>-->
    <!--                               <option -->
    <?php //if($details['ag_status']=='0') echo 'selected'  ?><!-- value="0">Inactive</option>-->
    <!--                            </select>                       -->
    <!--                        </div>-->
    <!--                        <div class="row" style="text-align: center; padding: 5px 0px 15px 25px; font-size: 12px;">-->
    <!--                            <button type="submit" class="btn btn-success" name="update">Update</button> -->
    <!--                        </div>                                                                                  -->
    <!--                                             -->
    <!--                    </div>-->
    <!--                    <div class="col-md-6"></div>-->
    <!--                </div>-->
    <!--        </form>-->
</div>
<hr></hr>