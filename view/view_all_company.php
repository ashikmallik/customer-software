<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');

$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;


// ========== Delete Function Start =================
if( $obj->userWorkPermission($acc,'delete')){

    $dltoken = isset($_GET['dltoken']) ? $_GET['dltoken'] : NULL;
    if (!empty($dltoken)) {

        $deleteZone = $obj->Delete_data("tbl_company", "tbl_company.company_id='$dltoken'");

        if ($deleteZone) {
            $notification = 'Delete Successfull';
        } else {
            $notification = 'Delete Failed';
        }
    }
}
// ========== Delete Function End =================

if (isset($_POST['companyAddSubmit'])) {

    $zone_form_data = array(
        'company_name' => $_POST['company_name'],
        'phone' => $_POST['phone'],
        'company_address' => $_POST['company_address'],
        'vat' => $_POST['vat'],
        'billing_type' => $_POST['billing_type'],
        'entry_by' => $userid
    );
    $addZone = $obj->Insert_data("tbl_company", $zone_form_data);
    if ($addZone) {
        ?>
        <script>
            window.location = "?q=view_all_company";
        </script>
        <?php
    } else {
        echo $notification = 'Company Insert Failed';
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
            <h4>View company info</h4>
        </div>
        <div class="col-md-6" style="">
            <?php if ($ty == 'SA') { ?>
                <button type="button" class="btn btn-default pull-right" data-toggle="modal"
                        data-target="#addCompanyModal">
                    <span class="glyphicon glyphicon-plus"></span> Add Compny
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
                                <th class="text-center">Company Name</th>
                                <th class="text-center">Company Phone</th>
                                <th class="text-center">Created By</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <?php
                        $i = '0';
                        foreach ($obj->view_all("tbl_company") as $value) {
                            $i++;
                            ?>
                            <tr>
                                <td class="text-center"><?php echo $i; ?></td>
                                <td class="text-center"><?php echo isset($value['company_name']) ? $value['company_name'] : NULL; ?></td>
                                <td class="text-center"><?php echo isset($value['phone']) ? $value['phone'] : NULL; ?></td>
                                <td class="text-center"><?php
                                    $userData = $obj->details_by_cond('_createuser', "UserId = " . $value['entry_by'] . "");
                                    echo $userData['FullName'];
                                    ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <?php if( $obj->userWorkPermission($acc,'edit')){ ?>
                                        <a class="btn btn-xs btn-info" style="padding:5px;"
                                           href="?q=edit_company&token=<?php echo isset($value['company_id']) ? $value['company_id'] : NULL ?>">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>
                                        <?php }
                                        if( $obj->userWorkPermission($acc,'delete')){ ?>
                                        <a href="?q=view_all_company&dltoken=<?php echo isset($value['company_id']) ? $value['company_id'] : NULL; ?>"
                                           class="btn btn-xs btn-danger" onclick="return confirm('Are you sure you want to delete this company?');" style="padding:5px;">
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
<div class="modal fade" id="addCompanyModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center">Insert New Company</h4>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="row" style="padding:0 10px;">
                        <div class="col-md-12">
                    
                            <div class="form-group">
                                <div class="row" style="padding-bottom: 10px;">
                                    <label>Billing Type</label>
                                    <select class="form-control" name="billing_type" required="required">
                                        <option value="">Billing Type</option>
                                         <option value="1">Prepaid</option>
                                         <option value="2">Postpaid</option>
                                    </select>
                                </div>
                                <div class="row" style="padding-bottom: 10px;">
                                    <label>Company Name</label>
                                    <input type="text" name="company_name" class="form-control" id="ResponsiveTitle" placeholder="Provide Company Name">
                                </div>
                                <div class="row" style="padding-bottom: 10px;">
                                    <label>Company phone</label>
                                    <input type="text" name="phone" class="form-control" id="ResponsiveTitle" placeholder="Provide phone number ">
                                </div>
                                <div class="row" style="padding-bottom: 10px;">
                                    <label>Company Address</label>
                                    <textarea class="form-control" name="company_address" onkeypress="return noSpace(event)"  id="remarks" rows="4"></textarea>
                                </div>
                                <div class="row" style="padding-bottom: 10px;">
                                    <label>Company VAT (Give the percentage number)</label>
                                    <input type="text" name="vat" class="form-control" id="ResponsiveTitle" placeholder="Provide percentage number ">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-default" name="companyAddSubmit"> Insert Company
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