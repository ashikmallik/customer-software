<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
//$date        = date('Y-m-d');
$Month = date('m', strtotime($date));
$Year = date('Y', strtotime($date));
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

if (isset($_GET['zonePrint'])) {

    $dataForPrint = $obj->view_all_by_cond("vw_agent", "zone = " . $_GET['zonePrint'] . "");

}
elseif(isset($_GET['billingperson']))
{
    $dataForPrint = $obj->view_all_by_cond("vw_agent", "billing_person_id = " . $_GET['billingperson'] . "");
}
 else {

    $dataForPrint = $obj->view_all_by_cond("vw_agent",'1 order by ip asc');
}

?>

<div class="col-md-12"
     style=" background:#606060; margin-top:20px; margin-bottom: 15px; min-height:45px; padding:8px 0px 0px 15px; font-size:16px; font-family:Lucida Sans Unicode; color:#FFFFFF; font-weight:bold;">
    <div class="col-md-6">
        <b>Client List For Print</b>
    </div>
</div>

<div class="row" style="padding:10px; font-size: 12px;">

    <div class="col-md-1 col-md-offset-11">
        <button type="submit" class="btn btn-primary pull-right" onclick="printDiv('client_print')">Print Statement
        </button>
    </div>
    <div class="row" id="client_print">

        <div class="col-md-6">
            <b>All Client List Of <?php
                if (isset($_GET['zonePrint'])) {
                    $zoneData = $obj->details_by_cond('tbl_zone', 'zone_id = ' . $_GET['zonePrint'] . '');
                    echo $zoneData['zone_name'];
                }
                if (isset($_GET['billingperson'])) {
                    $zoneData = $obj->details_by_cond('_createuser', 'UserId = ' . $_GET['billingperson'] . '');
                    echo $zoneData['FullName'];
                }
                ?></b>
        </div>
        <div class="col-md-12">
            <table class="table table-responsive table-bordered table-hover table-striped" id="monthly_tbl">
                <thead>
                <tr>
                    <th>Sl</th>
                     <th>IP</th>
                    <th>Client name</th>
                    <th>Address</th>
                    <th>Mobile</th>
                    <th>Zone</th>
                    <th>Speed</th>
                    <th>Amount</th>
                    <th>Connection Date</th>
                    <th>Billing Person</th>
                </tr>
                </thead>

                <tbody>
                <?php
                $i = 0;
                $total_bill = 0;
                foreach ($dataForPrint as $value) {
                    $i++;
                    ?>
                    <tr>
                        <td> <?php echo $i; ?></td>
                        <td><?php echo isset($value['ip']) ? $value['ip'] : NULL; ?></td>
                        <td><?php echo isset($value['ag_name']) ? $value['ag_name'] : NULL; ?></td>
                        
                        <td><small><?php echo isset($value['ag_office_address']) ? $value['ag_office_address'] : NULL; ?></small></td>
                        <td><?php echo isset($value['ag_mobile_no']) ? $value['ag_mobile_no'] : NULL; ?></td>
                        <td><?php echo isset($value['zone_name']) ? $value['zone_name'] : NULL; ?></td>
                        <td><?php echo isset($value['mb']) ? $value['mb'] : NULL; ?></td>
                        <td><?php echo $total = isset($value['taka']) ? $value['taka'] : NULL; ?></td>
                        <td><?php echo isset($value['connection_date']) ? $value['connection_date'] : NULL; ?></td>
                        <td><?php $bp = $obj->details_by_cond('_createuser', 'UserId = ' . @$value['billing_person_id'] . '');  echo @$bp['FullName']  ?></td>
                        <?php
                        $total_bill += $total;
                        ?>
                    </tr>
                    <?php
                }
                ?>
                </tr>

                </tbody>
            </table>
        </div>
    </div>
    <!-- here end table -->
</div>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

<script type="text/javascript" src="http://cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function () {
        //$('#monthly_tbl').dataTable();
    });
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
<script>
    $(document).ready(function () {
        $("tbody tr").dblclick(function () {
            _id = this.id;
            if (_id != "0")
                window.location = "?q=view_customer_payment_individual&token2=" + _id;
        });
    });
</script>
<!-- here end table -->