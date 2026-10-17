<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$token = isset($_GET['token']) ? $_GET['token'] : NULL;

$expense = $obj->details_by_cond("tbl_stock", "id='$token'");


if (isset($_POST['update'])) {
    extract($_POST);

    $form_data = array(

        'item_id' => $item_id,
        'ag_id' => $ag_id,
        'item_qty' => $quantity,
        'description' => str_replace("'", "", $description),
        'added_by' => $userid,
    );
    $expense_update = $obj->Update_data("tbl_stock", $form_data, "where id='$token'");

    if ($expense_update) {
        ?>
        <script>
            window.location = "?q=all_stock_out";
        </script>
        <?php
    } else {
        echo $notification = 'Update Failed';
    }
}
?>
<!--===================end Function===================-->

<div class="row" >
    <div class="col-md-12 bg-slate-700">
        <h4>Edit Stock Out</h4>
    </div>
</div>
<div class="col-md-12"
     style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
    <b><?php echo isset($notification) ? $notification : NULL; ?></b>
</div>

<hr>
<div class="row" style="padding:10px; font-size: 12px;">
    <form role="form" enctype="multipart/form-data" method="post">

        <div class="row">
            <div class="col-md-6 col-md-offset-3">

                <div class="form-group">
                    <label>Stock Name</label>
                    <select class="form-control" required="required" name="item_id" id="status">
                        <option value="">select</option>
                        <?php
                        $i = '0';
                        foreach ($obj->view_all("tbl_stock_item") as $value) {
                            $i++;
                            ?>
                            <option <?php if ($expense['item_id'] == $value['item_id']) echo 'selected' ?> value="<?php echo isset($value['item_id']) ? $value['item_id'] : NULL; ?>"><?php echo isset($value['item_name']) ? $value['item_name'] : NULL; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Customer</label>
                    <select class="form-control" required="required" name="ag_id" id="status">
                        <option value="">select</option>
                        <?php
                        $i = '0';
                        foreach ($obj->view_all("tbl_agent") as $value) {
                            $i++;
                            ?>
                            <option <?php if ($expense['ag_id'] == $value['ag_id']) echo 'selected' ?> value="<?php echo isset($value['ag_id']) ? $value['ag_id'] : NULL; ?>"><?php echo isset($value['ag_name']) ? $value['ag_name'] : NULL; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Quantity</label>
                    <input value="<?php echo $expense['item_qty'] ? $expense['item_qty'] : NULL; ?>" type="text"
                           name="quantity" class="form-control" id="ResponsiveTitle">
                </div>
                <div class="form-group">
                    <label>Details</label>
                    <textarea class="form-control" name="description" rows="6"><?php echo $expense['description'] ? $expense['description'] : NULL; ?></textarea>
                </div>

                <div class="form-group text-center">
                    <input type="submit" class="btn btn-success" name="update" value="Update"/>
                </div>

            </div>
            <div class="col-md-6"></div>
        </div>

        <div class="row" style="padding: 5px 0px 15px 25px; font-size: 12px;">

        </div>
    </form>
</div>
<hr></hr>