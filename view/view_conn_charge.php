<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
//$date        = date('Y-m-d');
$Month = date('m', strtotime($date));
$Year = date('Y', strtotime($date));
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;


$flag = isset($_GET['key']) ? "1" : "0";
$where = "";
$title = " " . date("M, Y");

if (isset($_POST['search'])) {

    $dateform = date('Y-m-d', strtotime($_POST['dateform']));
    $dateto = date('Y-m-d', strtotime($_POST['dateto']));

    $flag = "1";
    $title = "" . date('d M Y', strtotime($dateform)) . " to " . date('d M Y', strtotime($dateto));
    $where = "entry_date BETWEEN '$dateform' and '$dateto'";
}

if ($flag == "0") {
    $where = "MONTH(entry_date)='$Month' and YEAR(entry_date)='$Year'";
}

?>


<div class="row">
    <div class="col-md-12 bg-grey-800" >
        <h4><b>View All Connection Charge Information For <?php echo $title ?></b></h4>
    </div>
</div>

<div class="col-md-12"
     style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
    <b><?php echo isset($notification) ? $notification : NULL; ?></b>
</div>

<div class="row">
    <div class="col-md-12 bg-grey-800" style="margin-bottom: 20px;padding:5px 0px">
        <form action="" method="POST">
            <div class="col-md-5">
                <div class="form-group">
                    <label class="control-label col-sm-4" style="padding-top:5px" for="dateMonth">Form Date</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control datepicker" placeholder="Date" name="dateform"
                               id="new_flight_date" required>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label class="control-label col-sm-4" style="padding-top:5px" for="damageRate">To Date</label>
                    <div class="col-sm-8">
                        <input style="color: black;" id="old_flight_date" type="text" class="form-control datepicker"
                               placeholder="Date" name="dateto" required>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" name="search" class="btn btn-primary">Search </button>
            </div>
        </form>
    </div>
</div>

<div class="row" id="">

    <div class="col-md-12" id="month_print">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped" id="datatable">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Customer Name</th>
                    <th>Address</th>
                    <th>Mobile No</th>
                    <th>Speed Amount</th>
                    <th>IP</th>
                    <th>Connection Charge</th>
                </tr>

                </thead>
                <tbody>
                <?php
                $total = 0;
                foreach ($obj->view_all_by_cond("tbl_agent", $where . " ORDER BY entry_date DESC") as $value) {

                    ?>
                    <tr>
                        <td><?php echo date("d-m-Y", strtotime(isset($value['entry_date']) ? $value['entry_date'] : "2016-02-1")); ?></td>
                        <td>
                            <a href="?q=view_customer_payment_individual&token2=<?php echo isset ($value['ag_id']) ? $value['ag_id'] : NULL ?>"><?php echo isset($value['ag_name']) ? $value['ag_name'] : NULL; ?></a>
                        </td>
                        <td><?php echo isset($value['ag_office_address']) ? $value['ag_office_address'] : NULL; ?></td>
                        <td><?php echo isset($value['ag_mobile_no']) ? $value['ag_mobile_no'] : NULL; ?></td>
                        <td><?php echo isset($value['mb']) ? $value['mb'] : NULL; ?></td>
                        <td><?php echo isset($value['ip']) ? $value['ip'] : NULL; ?></td>
                        <td><?php echo isset($value['connect_charge']) ? number_format($value['connect_charge'], "2", ".", "") : NULL; ?></td>
                    </tr>
                    <?php
                    $total += isset($value['connect_charge']) ? $value['connect_charge'] : 0;
                }
                ?>
                </tbody>
                <tfoot>
                <tr style="font-size:18px;font-weight:900;">
                    <td colspan="6" style="text-align:right;"><b>Grand Total</b></td>
                    <td><b><?php echo number_format($total, "2", ".", "") ?></b></td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {

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
    function printDiv(divName) {
        $(".print").css("margin-left", "0px");
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
<!-- here end table -->

	

