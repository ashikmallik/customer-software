<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

$firsDayOfMonth = new DateTime('first day of this month');


$detail = $obj->view_all_by_cond("tbl_stock", "status='1' order by id");



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
            <b> Stock In Information</b>
        </div>
        <div class="col-md-8">
            
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
                    <th>Stock Name</th>
                    <th>Quantity</th>
                    <th>Added By</th>
                    <th>Action</th>
                </tr>
                </thead>
                <?php
                $i = '0';
                $totalExpense = 0;
                foreach ($detail as $value) {
                    $i++;
                    $totalExpense += intval($value['item_qty']);
                    ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo date("d-m-Y", strtotime(isset($value['entry_date']) ? $value['entry_date'] : "2016-02-1")); ?></td> 
                        <td><?php $item = $obj->details_by_cond('tbl_stock_item', "item_id = " . $value['item_id'] . ""); echo $item['item_name'];
                              ?></td>
                        <td><?php echo isset($value['item_qty']) ? $value['item_qty'] : NULL; ?> <?php $item = $obj->details_by_cond('tbl_stock_item', "item_id = " . $value['item_id'] . ""); echo $item['unit'];
                              ?></td>
                        
                        <td class="text-center"><?php
                                $userData = $obj->details_by_cond('_createuser', "UserId = " . $value['added_by'] . "");
                                echo $userData['FullName'];
                                ?></td>
                        
                        <td class="text-center">
                            <div class="btn-group">
                                <?php if( $obj->userWorkPermission($acc,'edit')){ ?>
                                    <a class="btn btn-xs btn-success"
                                       href="?q=edit_stock_in&token=<?php echo isset ($value['id']) ? $value['id'] : NULL ?>">
                                        Edit <span class="glyphicon glyphicon-edit"></span>
                                    </a>
                                <?php };
                                if( $obj->userWorkPermission($acc,'delete')){ ?>
                                    <a onclick="return confirm('Are You Sure To Delete This Expense ?');"
                                       href="?q=all_stock_in&dltoken=<?php echo isset($value['id']) ? $value['id'] : NULL; ?>"
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


        $(document).ready(function() {
            $('#example').DataTable( {
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'print',
                        text: 'Print Stock In',
                        exportOptions: {
                            columns: [ 0, 1, 2, 3, 4,5 ]
                        },
                        title: function(){
                            var printTitle = 'All Stock In Item';
                            return printTitle
                        },
                        customize: function ( win ) {
                            $(win.document.body).css( 'font-size', '12px' );
                            $(win.document.body).find( 'h1' ).addClass( 'text-center' ).css( 'font-size', '20px' );
                            $(win.document.body).find( 'table' ).removeClass( 'table-bordered' ).css( 'font-size', 'inherit' );
                        }
                    },
                ]
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
