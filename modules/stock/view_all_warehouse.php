<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');

$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

if(isset($_POST['warehouseTransferSubmit'])){

    $from_warehouse = $_POST['from_warehouse'];
    $to_warehouse   = $_POST['to_warehouse'];
    $item_id        = $_POST['item_id'];
    $qty            = $_POST['qty'];

    $out = $obj->get_total_stock_out($item_id,$from_warehouse);
    $in  = $obj->get_total_stock_in($item_id,$from_warehouse);

    $available_qty = $in - $out;

    $stock_itemDetails = $obj->details_by_cond("tbl_stock_item", "item_id='$item_id'");
    $itemDetails = $obj->details_by_cond("tbl_item", "item_id='$item_id'");
    
    $wirehousedatafrom = $obj->details_by_cond("tbl_warehouse", "warehouse_id='$from_warehouse'");
    $wirehousenamefrom = $wirehousedatafrom['warehouse_name'];
    
    $wirehousedatato = $obj->details_by_cond("tbl_warehouse", "warehouse_id='$to_warehouse'");
    $wirehousenameto = $wirehousedatato['warehouse_name'];
    // echo $wirehousenameto; exit;

    if($qty <= $available_qty){

        // Source Warehouse Stock Out
        $form_data = [
            'warehouse_id' => $from_warehouse,
            'item_id'      => $item_id,
            'item_qty'     => $qty,
            'status'       => 0,
            'added_by'     => $userid,
            'description'  => "Warehouse Transfer Out from $wirehousenamefrom",
            'entry_date'   => date('Y-m-d')
        ];

        $obj->Insert_data('tbl_stock',$form_data);

        // Destination Warehouse Stock In
        $form_data = [
            'warehouse_id' => $to_warehouse,
            'item_id'      => $item_id,
            'item_qty'     => $qty,
            'status'       => 1,
            'added_by'     => $userid,
            'description'  => "Warehouse Transfer In $wirehousenameto",
            'entry_date'   => date('Y-m-d')
        ];

        $obj->Insert_data('tbl_stock',$form_data);


        $form_data = array(
        'item_id'    => $item_id,
        'warehouse_id' => $to_warehouse,
        'cat_id'       => $stock_itemDetails['cat_id'] ?? '',
        'unit'         => $itemDetails['unit'] ?? '',
        'item_qty'     => $qty,
        'item_price'   => $itemDetails['item_price'] ?? '',
        'created_by'   => $userid
    );

    $service_add = $obj->Insert_data("tbl_stock_item", $form_data);

        echo "<script>window.location='?q=view_all_warehouse';</script>";

    }else{
        $notification = "Insufficient Stock!";
    }
}


// ========== Delete Function Start =================
if( $obj->userWorkPermission($acc,'delete')){

    $dltoken = isset($_GET['dltoken']) ? $_GET['dltoken'] : NULL;
    if (!empty($dltoken)) {

        $deleteCategory = $obj->Delete_data("tbl_warehouse", "tbl_warehouse.warehouse_id='$dltoken'");

        if ($deleteCategory) {
            $notification = 'Delete Successfull';
        } else {
            $notification = 'Delete Failed';
        }
    }
}
// ========== Delete Function End =================

if (isset($_POST['warehouseAddSubmit'])) {

    $warehouse_form_data = array(
        'warehouse_name' => $_POST['warehouseName'],
        'Address' => $_POST['address'],
    );
    $addWarehouse = $obj->Insert_data("tbl_warehouse", $warehouse_form_data);
    if ($addWarehouse) {
        ?>
        <script>
            window.location = "?q=view_all_warehouse";
        </script>
        <?php
    } else {
        echo $notification = 'Warehouse Insert Failed';
    }
}
// ================= Warehouse   Add ==========================



?>

<style>
    .select2-container{
    width:100% !important;
}

.select2-selection{
    width:100% !important;
}
</style>


<?php if (isset($notification) && !empty($notification)) { ?>
    <div class="alert alert-warning alert-dismissable fade in text-center">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <?php echo isset($notification) ? $notification : NULL; ?>
    </div>
<?php } ?>

<div class="row">
    <div class="col-md-12 bg-grey-600 padding_10_px">
        <div class="col-md-6">
            <h4>View Warehouse info</h4>
        </div>
        <div class="col-md-3" style="">
            <?php if ($ty == 'SA' || $ty == 'EO') { ?>
                <button type="button" class="btn btn-default pull-right" data-toggle="modal"
                        data-target="#addZoneModal">
                    <span class="glyphicon glyphicon-plus"></span> Add Warehouse
                </button>
            <?php } ?>
        </div>
        <div class="col-md-3" style="">
            <button type="button" class="btn btn-success pull-left" data-toggle="modal" data-target="#warehouseTransferModal">
                <span class="glyphicon glyphicon-transfer"></span> Warehouse Transfer
            </button>
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
                                <th class="text-center">Warehouse Name</th>
                               
                                <th class="text-center">Address</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <?php
                        $i = '0';
                        foreach ($obj->view_all("tbl_warehouse") as $value) {
                                                        $warehouse_id = $value['warehouse_id'];

                                                        $insql = "
                                                            SELECT COALESCE(SUM(item_qty),0) AS total
                                                            FROM tbl_stock
                                                            WHERE warehouse_id = '$warehouse_id'
                                                            AND status = 1
                                                        ";
                                                        $indata = $obj->raw_sql($insql);
                                                        
                                                        $outsql = "
                                                            SELECT COALESCE(SUM(item_qty),0) AS total
                                                            FROM tbl_stock
                                                            WHERE warehouse_id = '$warehouse_id'
                                                            AND status = 0
                                                        ";
                                                        $outdata = $obj->raw_sql($outsql);
                                                        
                                                        $total_in  = $indata[0]['total'];
                                                        $total_out = $outdata[0]['total'];
                                                        
                                                        $total_qty = $total_in - $total_out;
                            $i++;

                            
                               
                            
                                 
                        

                            $total_qty = $indata[0]['total'] - $outdata[0]['total'];
                            ?>
                            <tr>
                                <td class="text-center"><?php echo $i; ?></td>
                                <td class="text-center"><?php echo isset($value['warehouse_name']) ? $value['warehouse_name'] : NULL; ?></td>
                                
                                <td class="text-center"><?php echo isset($value['address']) ? $value['address'] : NULL; ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <?php if( $obj->userWorkPermission($acc,'edit')){ ?>
                                        <a class="btn btn-xs btn-info" style="padding:5px;"
                                           href="?q=edit_warehouse&token=<?php echo isset($value['warehouse_id']) ? $value['warehouse_id'] : NULL ?>">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>
                                        <?php }
                                        if( $obj->userWorkPermission($acc,'delete')){ ?>
                                        <a href="?q=view_all_warehouse&dltoken=<?php echo isset($value['warehouse_id']) ? $value['warehouse_id'] : NULL; ?>"
                                           class="btn btn-xs btn-danger" onclick="return confirm('Are you sure you want to delete this warehouse?');" style="padding:5px;">
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
                <h4 class="modal-title text-center">Insert New Warehouse</h4>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="row" style="padding:0 10px;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="row">
                                    <label>Warehouse Name</label>
                                    <input type="text" name="warehouseName" class="form-control" id="ResponsiveTitle" placeholder="Provide Warehouse Name">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="row">
                                    <label>Warehouse Address</label>
                                    <input type="text" name="address" class="form-control" id="ResponsiveTitle" placeholder="Provide Address">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-default" name="warehouseAddSubmit"> Insert Warehouse
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
 <div class="modal fade" id="warehouseTransferModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="post">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Warehouse Transfer</h4>
                </div>
                <input type="hidden" name="cat_id" id="hidden_cat_id" value="">

                <div class="modal-body">

                    <div class="form-group">
                        <label>From Warehouse</label>
                        <select class="form-control" name="from_warehouse" id="from_warehouse" required>
                            <option value="">Select Warehouse</option>

                            <?php
                            foreach($obj->view_all("tbl_warehouse") as $wh){
                            ?>
                            <option value="<?php echo $wh['warehouse_id']; ?>">
                                <?php echo $wh['warehouse_name']; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Item</label>
                        <select class="form-control" name="item_id" id="transfer_item_id" required>
                            <option value="">Select Item</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Available Qty</label>
                        <input type="text" class="form-control" id="transfer_available_qty" readonly>
                    </div>

                    <div class="form-group">
                        <label>Transfer Qty</label>
                        <input type="number" class="form-control" name="qty" required>
                    </div>

                    <div class="form-group">
                        <label>To Warehouse</label>
                        <select class="form-control" name="to_warehouse" required>

                            <option value="">Select Warehouse</option>

                            <?php
                            foreach($obj->view_all("tbl_warehouse") as $wh){
                            ?>
                            <option value="<?php echo $wh['warehouse_id']; ?>">
                                <?php echo $wh['warehouse_name']; ?>
                            </option>
                            <?php } ?>

                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" name="warehouseTransferSubmit" class="btn btn-success">
                        Transfer Stock
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>

$('#from_warehouse').change(function(){

    $.ajax({
        url:'view/ajax_action/fetch_stock_by_warehouse.php',
        type:'POST',
        data:{
            warehouse_id:$(this).val()
        },
        success:function(response){
            $('#transfer_item_id').html(response);
        }
    });

});

$('#transfer_item_id').change(function(){

    $.ajax({
        url:'view/ajax_action/ajax_get_available_stock.php',
        type:'POST',
        data:{
            warehouse_id:$('#from_warehouse').val(),
            item_id:$(this).val()
        },
        success:function(response){
            $('#transfer_available_qty').val(response);
        }
    });

});


$('#transfer_item_id').change(function(){
    var selectedOption = $(this).find(':selected');
    var cat_id  = selectedOption.data('cat-id');
    // console.log("Category ID:", cat_id);
    
    // Hidden field এ রাখতে চাইলে
    $('#hidden_cat_id').val(cat_id);
});
</script>



