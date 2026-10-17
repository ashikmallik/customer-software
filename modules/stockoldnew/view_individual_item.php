<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

$token = isset($_GET['token']) ? $_GET['token'] : NULL;
$httpReferer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '#';

// $itemDetails = $obj->details_by_cond("tbl_stock_item", "item_name='$token'");
$itemDetails = $obj->view_all_by_cond("tbl_stock_item", "item_name='$token'");
// var_dump($itemDetails);exit;

// ========== Delete Function Start =================
    if ($obj->userWorkPermission($acc, 'delete')) {

        $dltoken = isset($_GET['dltoken']) ? $_GET['dltoken'] : NULL;
        if (!empty($dltoken)) {

            $dele = $obj->Delete_data("tbl_stock", "id='$dltoken'");

            if ($dele) {
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

 if (!empty($itemDetails)) { ?>

    <div class="col-md-12" style="margin-top:5px; margin-bottom:5px; font-size:14px; color:red; font-weight:bold; text-align:center;">
        <b><?php echo isset($notification) ? $notification : ''; ?></b>
    </div>

    <div class="col-md-12 bg-grey-800" style="margin-bottom:20px;">
        <div class="col-md-10">
            <h4>View Stock Item Information - <?php echo htmlspecialchars($token); ?></h4>
        </div>
        <div class="col-md-2">
            <a class="btn btn-success btn-sm pull-right" href="<?php echo $httpReferer; ?>" style="margin-top:3px;">
                <span class="glyphicon glyphicon-circle-arrow-left"></span> Go Back
            </a>
        </div>
    </div>

    <div class="row" style="font-size: 12px;">
        <div class="col-md-12">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th class="text-center">SL</th>
                        <th class="text-center">Stock Name</th>
                        <th class="text-center">Stock Price</th>
                        <th class="text-center">In Qty</th>
                        <th class="text-center">Out Qty</th>
                        <th class="text-center">Available</th>
                        <th class="text-center">Category</th>
                        <th class="text-center">Created By</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $grand_total_in = 0;
                $grand_total_out = 0;
                $grand_available = 0;
                $i = 0;

                foreach ($itemDetails as $item) { 
                    $i++;
                    $item_id = $item['item_id'];
                    $in  = $obj->get_total_stock_in($item_id);
                    $out = $obj->get_total_stock_out($item_id);
                    $qty = $in - $out;
                    
                    $grand_total_in += $in;
                    $grand_total_out += $out;
                    $grand_available += $qty;

                    $catData = $obj->details_by_cond('tbl_stock_category', "cat_id = " . $item['cat_id']);
                    $userData = $obj->details_by_cond('_createuser', "UserId = " . $item['created_by']);
                ?>
                    <tr>
                        <td class="text-center"><?php echo $i; ?></td>
                        <td class="text-center"><?php echo htmlspecialchars($item['item_name']); ?></td>
                        <td class="text-center"><?php echo number_format($item['item_price'] * $qty); ?></td>
                        <td class="text-center"><?php echo $in; ?> <?php echo $item['unit']; ?></td>
                        <td class="text-center"><?php echo $out; ?> <?php echo $item['unit']; ?></td>
                        <td class="text-center"><strong><?php echo $qty; ?></strong> <?php echo $item['unit']; ?></td>
                        <td class="text-center"><?php echo $catData['cat_name']; ?></td>
                        <td class="text-center"><?php echo $userData['FullName']; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
                <!-- Grand Total Row -->
                <tfoot>
                    <tr style="background:#f0f0f0; font-weight:bold;">
                        <td colspan="3" class="text-right">Grand Total:</td>
                        <td class="text-center"><?php echo $grand_total_in; ?></td>
                        <td class="text-center"><?php echo $grand_total_out; ?></td>
                        <td class="text-center"><?php echo $grand_available; ?></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Stock In & Stock Out এর জন্য সব item_id এর ডাটা দেখানো -->
    <?php 
    // সব item_id সংগ্রহ করা
    $all_item_ids = array_column($itemDetails, 'item_id');
    $item_ids_str = implode(',', $all_item_ids);
    ?>

    <!-- Stock In Information -->
    <div class="row" style="padding:10px; font-size: 12px;">
        <div class="col-md-12">
            <h4 style="text-align: center; color: green">Stock In Information (All Batches)</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Date</th>
                            <th>Quantity</th>
                            <th>Added By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <?php
                    $i = 0;
                    $totalin = 0;
                    foreach ($obj->view_all_by_cond("tbl_stock", "item_id IN ($item_ids_str) AND status='1' ORDER BY id") as $item_info) {
                        $i++;
                        $totalin += $item_info['item_qty'];
                    ?>
                        <tr>
                            <td class="text-center"><?php echo $i; ?></td>
                            <td class="text-center"><?php echo date("d-m-Y", strtotime($item_info['entry_date'] ?? '2016-02-01')); ?></td>
                            <td class="text-center"><?php echo $item_info['item_qty']; ?> <?php echo $itemDetails[0]['unit']; ?></td>
                            <td class="text-center">
                                <?php
                                $userData = $obj->details_by_cond('_createuser', "UserId = " . $item_info['added_by']);
                                echo $userData['FullName'] ?? '';
                                ?>
                            </td>
                            <td>
                                <?php if ($obj->userWorkPermission($acc, 'delete')) { ?>
                                    <a onclick="return confirm('Are You Sure?');" 
                                       href="?q=view_individual_item&dltoken=<?php echo $item_info['id']; ?>"
                                       class="btn btn-xs btn-danger">
                                        Delete
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <th colspan="2" class="text-right">Total In:</th>
                        <th colspan="3"><?php echo $totalin; ?> <?php echo $itemDetails[0]['unit']; ?></th>
                    </tr>
                </table>
            </div>
        </div>
    </div>


<?php } else {
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