<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');

date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$data = $obj->details_by_cond("tbl_stock_item", "item_id!=0 ORDER BY item_id DESC");
if($data){
    $STD = $data['item_id'] + 1;
}else{
    $STD = 1;
}

// ========== Delete Function Start =================
if( $obj->userWorkPermission($acc,'delete')){

    $dltoken = isset($_GET['dltoken']) ? $_GET['dltoken'] : NULL;
    if (!empty($dltoken)) {

        $deleteStock = $obj->Delete_data("tbl_stock_item", "tbl_stock_item.item_id='$dltoken'");

        if ($deleteStock) {
            $notification = 'Delete Successfull';
        } else {
            $notification = 'Delete Failed';
        }
    }
}
// ========== Delete Function End =================


//===================Add Function===================

if (isset ($_POST['submit'])) {
    extract($_POST);

    $form_data = array(
        'item_name' =>$name,
        'cat_id' => $cat_id,
        'unit' => $unit,
        'item_qty' => $quantity,
        'item_price'=>$price,
        'created_by'=>$userid
    );
    $service_add = $obj->Reg_user_cond("tbl_stock_item", $form_data, " ");

    $add_data = array(
        'item_qty' => $quantity,
        'item_id' =>$STD,
        'added_by' =>$userid,
        'entry_date' =>date("Y/m/d"),
        'status' => 1
        );

    $service = $obj->Reg_user_cond("tbl_stock", $add_data, " ");

    if ($service_add) {
        ?>
        <script>
            window.location = "?q=view_stock_in";
        </script>
        <?php
    } else {
        echo $notification = 'Insert Failed';
    }
}
?>




<!--===================end Function===================-->
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




<?php if (isset($notification) && !empty($notification)) { ?>
    <div class="alert alert-warning alert-dismissable fade in text-center">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <?php echo isset($notification) ? $notification : NULL; ?>
    </div>
<?php } ?>


<br>
    

<div class="row">
    <div class="col-md-12 bg-teal-800" style="margin-bottom: 15px; padding:10px 0px 3px 15px; font-size:16px; font-family:Lucida Sans Unicode;">
        <div class="col-md-4">
            <b>View Stock Information</b>
        </div>

        <div class="col-md-8">
            <?php if ($ty == 'SA' || $ty == 'EO') { ?>
                <button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal"
                        data-target="#addStockModal">
                    <span class="glyphicon glyphicon-plus"></span> NEW STOCK
                </button>
            <?php } ?>
        </div>
    </div>
</div>

    <div class="row">

        <div id="div_all" class="row" style="font-size: 12px;">
            <div class="col-md-9 col-md-offset-2">
                <div class="table-responsive">
                    <table style="margin-left:-15px;" class="table table-bordered table-hover table-striped" id="datatable">
                        <thead>
                            <tr> <br>
                                <th class="text-center">SL</th>
                                <th class="text-center">Stock Name</th>
                                <th class="text-center">In Qty</th>
                                <th class="text-center">Out Qty</th>
                                <th class="text-center">Available</th>
                                <th class="text-center">Stock Price</th>
                                <th class="text-center">Category</th>
                                <th class="text-center">Created By</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <?php
                        $total_price = 0;

                        $i = '0';
                        foreach ($obj->view_all("tbl_stock_item") as $value) {

                            $i++;
                            $item_id = $value['item_id'];
                            $out = $obj->get_total_stock_out($item_id);
                            $in =  $obj->get_total_stock_in($item_id);
                            $qty = $in-$out;
                            $price = $value['item_price']* $qty;
                            $total_price += $price;

                            ?>

                            <tr>
                                <td class="text-center"><?php echo $i; ?></td>
                                <td class="text-center"><a href="?q=view_individual_item&token=<?php echo isset($value['item_id']) ? $value['item_id'] : NULL ?>"><?php echo isset($value['item_name']) ? $value['item_name'] : NULL; ?></a></td>
                                <td class="text-center"><?php echo $in ; ?>  <?php echo isset($value['unit']) ? $value['unit'] : NULL ?></td>
                                <td class="text-center"><?php echo isset($out) ? $out : 0; ?>
                                 <?php echo isset($value['unit']) ? $value['unit'] : NULL ?></td>
                                 <td class="text-center"><?php echo $qty ; ?> <?php echo isset($value['unit']) ? $value['unit'] : NULL ?></td>
                                <td class="text-center"><?php echo  $value['item_price']* $qty ?></td>
                                <td class="text-center"><?php
                                    $catData = $obj->details_by_cond('tbl_stock_category', "cat_id = " . $value['cat_id'] . "");
                                    echo $catData['cat_name'];
                                    ?></td>
                                <td class="text-center"><?php
                                    $userData = $obj->details_by_cond('_createuser', "UserId = " . $value['created_by'] . "");
                                    echo $userData['FullName'];
                                    ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <?php if( $obj->userWorkPermission($acc,'edit')){ ?>
                                         <a class="btn btn-xs btn-info" style="padding:5px;"
                                           href="?q=add_stock_item&token=<?php echo isset($value['item_id']) ? $value['item_id'] : NULL ?>">
                                            <span class="glyphicon glyphicon-plus"></span>
                                        </a> 
                                        <?php }?>
                                        <?php if( $obj->userWorkPermission($acc,'edit')){ ?>
                                        <a class="btn btn-xs btn-info" style="padding:5px;"
                                           href="?q=edit_stock_item&token=<?php echo isset($value['item_id']) ? $value['item_id'] : NULL ?>">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>
                                        <?php }
                                        if( $obj->userWorkPermission($acc,'delete')){ ?>
                                        <a href="?q=view_stock_in&dltoken=<?php echo isset($value['item_id']) ? $value['item_id'] : NULL; ?>"
                                           class="btn btn-xs btn-danger" onclick="return confirm('Are you sure you want to delete this stock?');" style="padding:5px;">
                                            <span class="glyphicon glyphicon-remove"></span>
                                        </a>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                            

                            <?php
                        }
                        ?>
                            <tr>
                                <td colspan="5" class="text-right"><b>Total Stock Price</b></td>
                                <td colspan="3" class="text-left"><b><?php echo  $total_price ?> TK</b></td>
                            </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Start-->
<div class="modal fade" id="addStockModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center">Add New Stock</h4>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="row" style="padding:10px; font-size: 12px;">

                        <div class="col-md-6 col-md-offset-3">
                            <div class="form-group">
                                <label>Item Name</label>
                                <input type="text" name="name" class="form-control" required="required">
                            </div>
                            <div class="form-group">
                                <label>Category</label>
                                <select class="form-control" required="required" name="cat_id" id="status" style="width: 250px">
                                    <?php
                                    $i = '0';
                                    foreach ($obj->view_all("tbl_stock_category") as $value) {
                                        $i++;
                                        ?>
                                        <option></option>
                                        <option value="<?php echo isset($value['cat_id']) ? $value['cat_id'] : NULL; ?>"><?php echo isset($value['cat_name']) ? $value['cat_name'] : NULL; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Unit</label>
                                <select class="form-control" required="required" name="unit" id="status" style="width: 250px">
                                    <option></option>
                                    <option>Meter</option>
                                    <option>Pcs</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Quantity</label>
                                <input onkeypress="return numbersOnly(event)" required="required" type="text" name="quantity"
                                       class="form-control" id="ResponsiveTitle">
                            </div>
                            <div class="form-group">
                                <label>Price</label>
                                <input onkeypress="return numbersOnly(event)" required="required" type="text" name="price"
                                       class="form-control" id="ResponsiveTitle">
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-success" name="submit">Submit</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
<!-- Modal End-->




<script >
    $('select[name="cat_id"]').select2({
            placeholder: "Select Category",
            allowClear: true,
        });
    $('select[name="unit"]').select2({
            placeholder: "Select Unit",
            allowClear: true,
        });
</script>


    