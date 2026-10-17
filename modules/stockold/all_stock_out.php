<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

$firsDayOfMonth = new DateTime('first day of this month');

// $expense_detail = $obj->view_all_by_cond_left_join('tbl_stock_expense', 'tbl_stock', 'stock_id', 'stock_id', 'stock_name');

$expense_detail = $obj->view_all_by_cond("tbl_stock", "status='0' AND item_qty>'0' order by id");



if( $obj->userWorkPermission($acc,'delete')){

    if (isset($_GET['dltoken']) && !empty($_GET['dltoken'])) {
        $dltoken = $_GET['dltoken'];
        
        if ($obj->Delete_data("tbl_stock", "id='$dltoken'")) {
            
            $obj->notificationStore('Delete Successfull', 'success');
        } else {

            $obj->notificationStore('Sorry ! Delete Failed. Try Again');
        }

        echo '<script> window.location = "?q=all_stock_out"; </script>';
    }
}
// ========== Delete Function End =================

?>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">

<div class="row">
    <div class="col-md-12 bg-teal-800" style="margin-bottom: 15px; padding:10px 0px 3px 15px; font-size:16px; font-family:Lucida Sans Unicode;">
        <div class="col-md-4">
            <b> Stock Out Information</b>
        </div>
        <div class="col-md-8">
            <?php if ($ty == 'SA' || $ty == 'EO') { ?>
                <a class="btn btn-primary btn-sm pull-right" href="?q=stock_out">STOCK OUT<span class="glyphicon
        glyphicon-plus"></span></a>
            <?php } ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?php $obj->notificationShowRedirect(); ?>
    </div>
</div>

<div class="row" style="padding:10px; font-size: 12px;">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped" id="example">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Agent Name</th>
                    <th>Stock Name</th>
                    <th>Quantity</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
                </thead>
                <?php
                $i = '0';
                $totalExpense = 0;
                foreach ($expense_detail as $value) {
                    $i++;
                    $totalExpense += intval($value['item_qty']);
                    ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo date("d-m-Y", strtotime(isset($value['entry_date']) ? $value['entry_date'] : "2016-02-1")); ?></td> 
                        <td><a href="?q=view_individual_cus&token=<?php echo isset($value['ag_id']) ? $value['ag_id'] : NULL ?>"><?php $user = $obj->details_by_cond('tbl_agent', "ag_id = " . $value['ag_id'] . ""); echo $user['ag_name'];
                              ?></a></td>
                        <td><?php $item = $obj->details_by_cond('tbl_stock_item', "item_id = " . $value['item_id'] . ""); echo $item['item_name'];
                              ?></td>
                        <td><?php echo isset($value['item_qty']) ? $value['item_qty'] : NULL; ?> <?php $item = $obj->details_by_cond('tbl_stock_item', "item_id = " . $value['item_id'] . ""); echo $item['unit'];
                              ?></td>
                        <td><?php $item = $obj->details_by_cond('tbl_stock_item', "item_id = " . $value['item_id'] . ""); $cat = $obj->details_by_cond('tbl_stock_category', "cat_id = " . $item['cat_id'] . ""); echo $cat['cat_name'];
                              ?></td>
                        <td><?php echo isset($value['description']) ? $value['description'] : NULL; ?></td>
                        <td class="text-center">
                            <div class="btn-group">
                                <?php if( $obj->userWorkPermission($acc,'edit')){ ?>
                                    <a class="btn btn-xs btn-success"
                                       href="?q=edit_stock_out&token=<?php echo isset ($value['id']) ? $value['id'] : NULL ?>">
                                        Edit <span class="glyphicon glyphicon-edit"></span>
                                    </a>
                                <?php };
                                if( $obj->userWorkPermission($acc,'delete')){ ?>
                                    <a onclick="return confirm('Are You Sure To Delete This Expense ?');"
                                       href="?q=all_stock_out&dltoken=<?php echo isset($value['id']) ? $value['id'] : NULL; ?>"
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
                <!-- <tfoot>
                    <tr>
                        <th></th>
                        <th colspan="3">Total Stock Out</th>
                        <th colspan="4"><?php echo $totalExpense; ?> pcs</th>
                    </tr>
                </tfoot> -->
            </table>
        </div>
    </div>
</div>

<script>


    $(document).ready(function () {


        var title = $('h4#preview_date').html();
        $(document).ready(function() {
            $('#example').DataTable( {
                dom: 'Bfrtip',
                buttons: [
                    'print',

                ],


            } );
        } );
        
        $('input[name="dateform"]').datepicker({
            autoclose: true,
            toggleActive: true,
            format: 'dd-mm-yyyy'
        });

        $('input[name="dateto"]').datepicker({
            autoclose: true,
            toggleActive: true,
            format: 'dd-mm-yyyy'
        });
    });

</script>

<script type="application/javascript"
        src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script type="application/javascript"
        src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
