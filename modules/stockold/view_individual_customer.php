<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

$token = isset($_GET['token']) ? $_GET['token'] : NULL;
$httpReferer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '#';

$itemDetails = $obj->details_by_cond("tbl_stock", "ag_id='$token'");

if ($itemDetails) {

    if (isset($_POST['update'])) {
        $form_data = array(
            'item_qty' =>0,
        );
        $expense_update = $obj->Update_data("tbl_stock", $form_data, "where ag_id='$token'");
        if ($expense_update) {
            ?>
            <script>
                window.location = history.back();
            </script>
            <?php
        } 
    }
    ?>


    <div class="col-md-12"
         style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
        <b><?php echo isset($notification) ? $notification : NULL; ?></b>
    </div>
    <div class="col-md-12 bg-grey-800" style="margin-bottom:20px;">
        <div class="col-md-10">
            <h4>View Customer Information</h4>
        </div>
        <div class="col-md-2">
            <a class="btn btn-success btn-sm pull-right" href="<?php echo $httpReferer; ?>"
               style="margin-top:3px;"><span class="glyphicon glyphicon-circle-arrow-left"></span> Go Back </a>
        </div>

    </div>

   
    <div class="row" style="padding:10px; font-size: 12px;">
        <div class="col-md-12">
                <h4 style="text-align: center; color: green">Stock Out Information  For <span style="color: black"><?php $userData = $obj->details_by_cond('tbl_agent', "ag_id = " . $token . "");echo $userData['ag_name']; ?></span> </h4>
                    <form role="form" enctype="multipart/form-data" method="post">
                            <input type="submit" class="btn btn-success pull-right" name="update" value="All Return"/>
                    </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                    <tr>
                        <th class="col-md-1 text-center">SL</th>
                        <th class="col-md-1 text-center">Item</th>
                        <th class="col-md-1 text-center">Category</th>
                        <th class="col-md-1 text-center">Date</th>
                        <th class="col-md-1 text-center">By</th>
                         <th class="col-md-1 text-center">Quantity</th>
                        <th class="col-md-2 text-center">Description</th>
                        <th class="col-md-1 text-center">Action</th>
                    </tr>
                    </thead>
                    <?php
                    $i = 0;
                    $totalin = 0;
                    foreach ($obj->view_all_by_cond("tbl_stock", "ag_id='$token' AND item_qty>'0' order by id") as $item_info) {
                        extract($item_info);
                        $i++;
                        $totalin += $item_info['item_qty'];
                        ?>
                        <tr>
                            <td style="text-align: center;"><?php echo $i; ?></td>
                            <td style="text-align: center;"><?php
                                    $item = $obj->details_by_cond('tbl_stock_item', "item_id = " . $item_info['item_id'] . "");
                                    echo $item['item_name'];
                                    ?>
                            </td>
                            <td style="text-align: center;"><?php $item = $obj->details_by_cond('tbl_stock_item', "item_id = " . $item_info['item_id'] . ""); $cat = $obj->details_by_cond('tbl_stock_category', "cat_id = " . $item['cat_id'] . ""); echo $cat['cat_name'];
                              ?></td>
                            <td style="text-align: center;"><?php echo date("d-m-Y", strtotime(isset($item_info['entry_date']) ? $item_info['entry_date'] : "2016-02-1")); ?></td>
                            
                            <td style="text-align: center;"><?php
                                    $userData = $obj->details_by_cond('_createuser', "UserId = " . $item_info['added_by'] . "");
                                    echo $userData['FullName'];
                                    ?>
                            </td>
                            <td style="text-align: center;"><?php echo isset($item_info['item_qty']) ? $item_info['item_qty'] : NULL; ?><?php $item = $obj->details_by_cond('tbl_stock_item', "item_id = " . $itemDetails['item_id'] . ""); echo $item['unit'];
                                ?></td>
                            <td style="text-align: center;"><?php echo isset($item_info['description']) ? $item_info['description'] : NULL; ?></td>
                            

                            <td>
                                <div class="">
                                    <?php if ($obj->userWorkPermission($acc, 'edit')) { ?>
                                        <a class="btn btn-xs btn-info"
                                           href="?q=stock_return&token=<?php echo isset($item_info['id']) ? $item_info['id'] : NULL ?>">
                                            <span class="btn btn-xs btn-dange">Return</span>
                                        </a>
                                        <?php
                                    } ?>
                                    
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <th colspan="5" style="text-align: right;">Total:</th>
                        <th colspan="3" style="text-align: left;"><?PHP echo $totalin; ?> <?php $item = $obj->details_by_cond('tbl_stock_item', "item_id = " . $itemDetails['item_id'] . ""); echo $item['unit'];
                                ?></td></th>
                    </tr>
                    <tr>
                        <?php
                        $word = $formater->convert_number_to_words($totalin);
                        ?>

                        <td colspan="8" style="text-align: center;"><span style="color: green;"><b>Total Quantity In Word::&nbsp;&nbsp;&nbsp;</b><span
                                        style="color: red;"><b><?PHP echo $word; ?> <?php $item = $obj->details_by_cond('tbl_stock_item', "item_id = " . $itemDetails['item_id'] . ""); echo $item['unit'];
                                ?></td></b></span></span></td>
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