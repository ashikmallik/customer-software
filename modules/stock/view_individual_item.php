<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

$token = isset($_GET['token']) ? $_GET['token'] : NULL;
$warehouse_id = isset($_GET['warehouse_id']) ? $_GET['warehouse_id'] : NULL;
$httpReferer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '#';

$itemDetails = $obj->details_by_cond("tbl_stock_item", "item_id='$token' AND warehouse_id='$warehouse_id'");

// ========== Delete Function Start =================
    if ($obj->userWorkPermission($acc, 'delete')) {

        $dltoken = isset($_GET['dltoken']) ? $_GET['dltoken'] : NULL;
        if (!empty($dltoken)) {

            $dltoken = (int)$dltoken;
            $stockRow = $obj->details_by_cond("tbl_stock", "id='$dltoken'");

            // Deleting a stock-in that is already used in stock out would make stock negative
            if ($stockRow && $stockRow['status'] == 1
                && $obj->get_available_stock($stockRow['item_id'], $stockRow['warehouse_id'], $stockRow['id']) < 0) {
                $notification = 'Delete not possible! This stock is already used in Stock Out. Stock would become negative.';
            } elseif ($obj->Delete_data("tbl_stock", "id='$dltoken'")) {
                $notification = 'Delete Successfull';
                ?>
            <script>
                window.location = history.back();
            </script>
            <?php 
            } else {
                $notification = 'Delete Failed';
            }
        }
    }




// ========== Delete Function End =================

if ($itemDetails) {

    



    ?>


    <div class="col-md-12"
         style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
        <b><?php echo isset($notification) ? $notification : NULL; ?></b>
    </div>
    <div class="col-md-12 bg-grey-800" style="margin-bottom:20px;">
        <div class="col-md-10">
            <h4>View Stock Item Information</h4>
        </div>
        <div class="col-md-2">
            <a class="btn btn-success btn-sm pull-center" href="<?php echo $httpReferer; ?>"
               style="margin-top:3px;"><span class="glyphicon glyphicon-circle-arrow-center"></span> Go Back </a>
        </div>

    </div>

    <div class="row" style="font-size: 12px;">
        <div class="col-md-12">
            <table class="table table-bordered table-hover table-striped" >
                        <thead>
                            <tr> <br>
                                <th class="text-center">SL</th>
                                <th class="text-center">Stock Name</th>
                                <th class="text-center">Stock Item Price</th>
                                <th class="text-center">In Qty</th>
                                <th class="text-center">Out Qty</th>
                                <th class="text-center">Available</th>
                                <th class="text-center">Stock In Amount</th>
                                <th class="text-center">Stock Out Amount</th>
                                <th class="text-center">Available stock Amount</th>
                                <th class="text-center">Category</th>
                                <th class="text-center">Created By</th>
                            </tr>
                        </thead>
                        <?php

                            $item_id = $itemDetails['item_id'];
                             $itemmm   = $obj->details_by_cond('tbl_item', "item_id = $item_id");
                            $out = $obj->get_total_stock_out($item_id);
                            $in = $obj->get_total_stock_in($item_id);
                            
                            $itemprice = "SELECT SUM(item_price) as total FROM `tbl_stock_item` WHERE item_id='$item_id' AND warehouse_id='$warehouse_id'";
                            $totalItemPrise = $obj->raw_sql($itemprice);

                            $itemInQty = "SELECT SUM(item_qty) as total_qty FROM `tbl_stock` WHERE item_id='$item_id' AND status = 1 AND warehouse_id='$warehouse_id'";
                            $totalItemInQty = $obj->raw_sql($itemInQty);

                            $itemOutQty = "SELECT SUM(item_qty) as total_qty FROM `tbl_stock` WHERE item_id='$item_id' AND status = 0 AND warehouse_id='$warehouse_id'";
                            $totalItemOutQty = $obj->raw_sql($itemOutQty);


                            $qty = (int)$totalItemInQty[0]['total_qty'] -  (int)$totalItemOutQty[0]['total_qty'];

                            // ---- Price / Value calculation ----
                            // NOTE: Using tbl_item.item_price as the unit price (same value already shown
                            // in the "Stock Item Price" column above). If your unit price should instead
                            // come from tbl_stock_item (per-warehouse price), change this line to:
                            // $unitPrice = isset($item['item_price']) ? (float)$item['item_price'] : 0;
                            // where $item = $obj->details_by_cond('tbl_stock_item', "item_id = $item_id AND warehouse_id='$warehouse_id'");
                            $unitPrice = isset($itemmm['item_price']) ? (float)$itemmm['item_price'] : 0;

                            $summaryInValue = (int)$totalItemInQty[0]['total_qty'] * $unitPrice;
                            $summaryOutValue = (int)$totalItemOutQty[0]['total_qty'] * $unitPrice;
                            $summaryAvailableValue = $qty * $unitPrice;
                            ?>

                            <tr>
                                <td class="text-center"><?php echo isset($itemDetails['item_id']) ? $itemDetails['item_id'] : NULL; ?></td>
                                <td class="text-center"><?php echo isset($itemmm['item_name']) ? $itemmm['item_name'] : NULL; ?></td>
                                <td class="text-center"><?php echo isset($itemmm['item_price']) ? $itemmm['item_price'] : NULL; ?></td>
                                
                                <td class="text-center"><?php echo  !empty($totalItemInQty) ? (int)$totalItemInQty[0]['total_qty'] : 0; ?> <?php $item = $obj->details_by_cond('tbl_stock_item', "item_id = " . $itemDetails['item_id'] . ""); echo $item['unit'];
                                ?></td>
                                <td class="text-center"><?php echo  !empty($totalItemOutQty) ? (int)$totalItemOutQty[0]['total_qty'] : 0; ?> <?php $item = $obj->details_by_cond('tbl_stock_item', "item_id = " . $itemDetails['item_id'] . ""); echo $item['unit'];
                                ?></td>
                                <td class="text-center"><?php echo $qty ; ?> <?php $item = $obj->details_by_cond('tbl_stock_item', "item_id = " . $itemDetails['item_id'] . ""); echo $item['unit'];
                                ?>
                              </td>
                                <td class="text-center"><?php echo number_format($summaryInValue, 2); ?></td>
                                <td class="text-center"><?php echo number_format($summaryOutValue, 2); ?></td>
                                <td class="text-center"><?php echo number_format($summaryAvailableValue, 2); ?></td>
                                <td class="text-center"><?php
                                    $catData = $obj->details_by_cond('tbl_stock_category', "cat_id = " . $itemDetails['cat_id'] . "");
                                    echo $catData['cat_name'];
                                    ?></td>
                                <td class="text-center"><?php
                                    $userData = $obj->details_by_cond('_createuser', "UserId = " . $itemDetails['created_by'] . "");
                                    echo $userData['FullName'];
                                    ?></td>
                            </tr>      
                    </table>
        </div>
    </div>



    <div class="row" style="padding:10px; font-size: 12px;">
        <div class="col-md-12">
                <h4 style="text-align: center; color: green"> Stock In Information</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                    <tr>
                        <th class="col-md-1">SL</th>
                        <th class="col-md-1">Date</th>
                        <th class="col-md-1">Quantity</th>
                        <th class="col-md-1">Unit Price</th>
                        <th class="col-md-1">Stock In Amount</th>
                        <th class="col-md-1">Added By</th>
                        <th class="col-md-1">Action</th>
                    </tr>
                    </thead>
                    <?php
                    $i = 0;
                    $totalin = 0;
                    $totalInValue = 0;
                    foreach ($obj->view_all_by_cond("tbl_stock", "item_id='$token' AND status='1' AND warehouse_id='$warehouse_id' order by id") as $item_info) {
                        extract($item_info);
                        $i++;
                        $totalin += $item_info['item_qty'];
                        $rowValue = $item_info['item_qty'] * $unitPrice;
                        $totalInValue += $rowValue;
                        ?>
                        <tr>
                            <td style="text-align: center;"><?= $i; ?></td>
                            <td style="text-align: center;"><?php echo date("d-m-Y", strtotime(isset($item_info['entry_date']) ? $item_info['entry_date'] : "2016-02-1")); ?></td>
    
                            <td style="text-align: center;"><?php echo isset($item_info['item_qty']) ? $item_info['item_qty'] : NULL; ?> <?php $item = $obj->details_by_cond('tbl_stock_item', "item_id = " . $itemDetails['item_id'] . ""); echo $item['unit'];
                                ?></td>
                            <td style="text-align: center;"><?php echo number_format($unitPrice, 2); ?></td>
                            <td style="text-align: center;"><?php echo number_format($rowValue, 2); ?></td>
                            <td style="text-align: center;"><?php
                                    $userData = $obj->details_by_cond('_createuser', "UserId = " . $item_info['added_by'] . "");
                                    echo $userData['FullName'];
                                    ?>
                            </td>

                            <td>
                                <div class="">
                                    <?php if ($obj->userWorkPermission($acc, 'edit')) { ?>
                                        <a class="btn btn-xs btn-info"
                                           href="?q=edit_stock_in&token=<?php echo isset($item_info['item_id']) ? $item_info['item_id'] : NULL ?>">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>
                                        <?php
                                    }
                                    if ($obj->userWorkPermission($acc, 'delete')) {
                                        ?>
                                    <a onclick="return confirm('Are You Sure To Delete This Information ?');"
                                       href="?q=view_individual_item&dltoken=<?php echo isset($item_info['id']) ? $item_info['id'] : NULL; ?>"
                                       class="btn btn-xs btn-danger">
                                        Delete <span class="glyphicon glyphicon-remove"></span>
                                    </a>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <th colspan="2" style="text-align: right;">Total:</th>
                        <th colspan="5" style="text-align: left;"><?PHP echo $totalin; ?> <?php echo $itemDetails['unit']; ?> &nbsp;&nbsp; (Value: <?php echo number_format($totalInValue, 2); ?>)</th>
                    </tr>
                    <tr>
                        <?php
                        $word = $formater->convert_number_to_words($totalin);
                        ?>

                        <td colspan="7" style="text-align: center;"><span style="color: green;"><b>Total Quantity In Word::&nbsp;&nbsp;&nbsp;</b><span
                                        style="color: red;"><b><?PHP echo $word; ?> <?php echo $itemDetails['unit']; ?>  Only</b></span></span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="row" style="padding:10px; font-size: 12px;">
        <div class="col-md-12">
                <h4 style="text-align: center; color: green">Stock Out Information</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                    <tr>
                        <th class="col-md-1 text-center">SL</th>
                        <th class="col-md-1 text-center">Date</th>
                        <th class="col-md-2 text-center">Link Name</th>
                        <th class="col-md-1 text-center">Quantity</th>
                        <th class="col-md-1 text-center">Unit Price</th>
                        <th class="col-md-1 text-center">Stock Out Amount</th>
                        <th class="col-md-2 text-center">Description</th>
                        <th class="col-md-1 text-center">Added By</th>
                        <th class="col-md-1 text-center">Action</th>
                    </tr>
                    </thead>
                    <?php
                    $i = 0;
                    $totalin = 0;
                    $totalOutValue = 0;
                    foreach ($obj->view_all_by_cond("tbl_stock", "item_id='$token' AND status='0' AND warehouse_id='$warehouse_id' AND item_qty!='0' order by id") as $item_info) {
                        extract($item_info);
                        $i++;
                        $totalin += $item_info['item_qty'];
                        $rowValue = $item_info['item_qty'] * $unitPrice;
                        $totalOutValue += $rowValue;
                        ?>
                        <tr>
                            <td style="text-align: center;"><?php echo $i; ?></td>
                            <td style="text-align: center;"><?php echo date("d-m-Y", strtotime(isset($item_info['entry_date']) ? $item_info['entry_date'] : "2016-02-1")); ?></td>
                            <td style="text-align: center;"><?php
                                    $userData = $obj->details_by_cond('tbl_agent', "ag_id = " . $item_info['ag_id'] . "");
                                    echo $userData['link_name'] ?? 'N/A';
                                    ?>
                            </td>
                            <td style="text-align: center;"><?php echo isset($item_info['item_qty']) ? $item_info['item_qty'] : NULL; ?><?php $item = $obj->details_by_cond('tbl_stock_item', "item_id = " . $itemDetails['item_id'] . ""); echo $item['unit'];
                                ?></td>
                            <td style="text-align: center;"><?php echo number_format($unitPrice, 2); ?></td>
                            <td style="text-align: center;"><?php echo number_format($rowValue, 2); ?></td>
                            <td style="text-align: center;"><?php echo isset($item_info['description']) ? $item_info['description'] : NULL; ?></td>
                            <td style="text-align: center;"><?php
                                    $userData = $obj->details_by_cond('_createuser', "UserId = " . $item_info['added_by'] . "");
                                    echo $userData['FullName'];
                                    ?>
                            </td>

                            <td>
                                <div class="">
                                    <?php if ($obj->userWorkPermission($acc, 'edit')) { ?>
                                        <a class="btn btn-xs btn-info"
                                           href="?q=edit_stock_out&token=<?php echo isset($item_info['id']) ? $item_info['id'] : NULL ?>">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>
                                        <?php
                                    }
                                    if ($obj->userWorkPermission($acc, 'delete')) {
                                        ?>
                                    <a onclick="return confirm('Are You Sure To Delete This Information ?');"
                                       href="?q=view_individual_item&dltoken=<?php echo isset($item_info['id']) ? $item_info['id'] : NULL; ?>"
                                       class="btn btn-xs btn-danger">
                                        Delete <span class="glyphicon glyphicon-remove"></span>
                                    </a>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <th colspan="3" style="text-align: right;">Total:</th>
                        <th colspan="6" style="text-align: left;"><?PHP echo $totalin; ?> <?php echo $itemDetails['unit']; ?> &nbsp;&nbsp; (Value: <?php echo number_format($totalOutValue, 2); ?>)</th>
                    </tr>
                    <tr>
                        <?php
                        $word = $formater->convert_number_to_words($totalin);
                        ?>

                        <td colspan="9" style="text-align: center;"><span style="color: green;"><b>Total Quantity In Word::&nbsp;&nbsp;&nbsp;</b><span
                                        style="color: red;"><b><?PHP echo $word; ?> <?php echo $itemDetails['unit']; ?> Only</b></span></span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <?php
} else {
    ?>
    <div class="row">
        <div class="col-md-12">
            <h2>
                <hr>
            </h2>
            <div class="alert alert-danger text-center" role="alert">
                <strong>Sorry! </strong>The Item you want to view the individual payment record is no longer exist.
                Please <a href="<?php echo $httpReferer; ?>" class="alert-link">go back</a>
            </div>
            <h2>
                <hr>
            </h2>
        </div>
    </div>
    <?php
}
?>