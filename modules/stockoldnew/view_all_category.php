<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');

$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;


// ========== Delete Function Start =================
if( $obj->userWorkPermission($acc,'delete')){

    $dltoken = isset($_GET['dltoken']) ? $_GET['dltoken'] : NULL;
    if (!empty($dltoken)) {

        $deleteCategory = $obj->Delete_data("tbl_stock_category", "tbl_stock_category.cat_id='$dltoken'");

        if ($deleteCategory) {
            $notification = 'Delete Successfull';
        } else {
            $notification = 'Delete Failed';
        }
    }
}
// ========== Delete Function End =================

if (isset($_POST['categoryAddSubmit'])) {

    $category_form_data = array(
        'cat_name' => $_POST['categoryName'],
        'created_by' => $userid
    );
    $addCategory = $obj->Insert_data("tbl_stock_category", $category_form_data);
    if ($addCategory) {
        ?>
        <script>
            window.location = "?q=view_stock_category";
        </script>
        <?php
    } else {
        echo $notification = 'Category Insert Failed';
    }
}
// ================= Category Add ==========================
?>

<?php if (isset($notification) && !empty($notification)) { ?>
    <div class="alert alert-warning alert-dismissable fade in text-center">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <?php echo isset($notification) ? $notification : NULL; ?>
    </div>
<?php } ?>

<div class="row">
    <div class="col-md-12 bg-grey-600 padding_10_px">
        <div class="col-md-6">
            <h4>View Stock Category info</h4>
        </div>
        <div class="col-md-6" style="">
            <?php if ($ty == 'SA' || $ty == 'EO') { ?>
                <button type="button" class="btn btn-default pull-right" data-toggle="modal"
                        data-target="#addZoneModal">
                    <span class="glyphicon glyphicon-plus"></span> Add Category
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
                                <br>
                                <th class="text-center">SL</th>
                                <th class="text-center">Category Name</th>
                                <th class="text-center">Created By</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <?php
                        $i = '0';
                        foreach ($obj->view_all("tbl_stock_category") as $value) {
                            $i++;
                            ?>
                            <tr>
                                <td class="text-center"><?php echo $i; ?></td>
                                <td class="text-center"><?php echo isset($value['cat_name']) ? $value['cat_name'] : NULL; ?></td>
                                <td class="text-center"><?php
                                    $userData = $obj->details_by_cond('_createuser', "UserId = " . $value['created_by'] . "");
                                    echo $userData['FullName'];
                                    ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <?php if( $obj->userWorkPermission($acc,'edit')){ ?>
                                        <a class="btn btn-xs btn-info" style="padding:5px;"
                                           href="?q=edit_stock_category&token=<?php echo isset($value['cat_id']) ? $value['cat_id'] : NULL ?>">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>
                                        <?php }
                                        if( $obj->userWorkPermission($acc,'delete')){ ?>
                                        <a href="?q=view_stock_category&dltoken=<?php echo isset($value['cat_id']) ? $value['cat_id'] : NULL; ?>"
                                           class="btn btn-xs btn-danger" onclick="return confirm('Are you sure you want to delete this category?');" style="padding:5px;">
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
                <h4 class="modal-title text-center">Insert New Category</h4>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="row" style="padding:0 10px;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="row">
                                    <label>Category Name</label>
                                    <input type="text" name="categoryName" class="form-control" id="ResponsiveTitle" placeholder="Provide Category Name">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-default" name="categoryAddSubmit"> Insert Category
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