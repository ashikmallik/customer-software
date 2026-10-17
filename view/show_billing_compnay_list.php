<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$currentYear = date("Y");
$currentMonth = date("m");
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$token = isset($_GET['token1']) ? $_GET['token1'] : NULL;
$notification = "";
$ontime= $obj->get_sum_by_cond("acc_amount","tbl_account","company_id='$token' and acc_type=4 and Year(entry_date)='$currentYear' and Month(entry_date)='$currentMonth'");
$total_bill =$obj->get_sum_by_cond("taka","tbl_agent","company_id='$token' and Year(entry_date)='$currentYear' and Month(entry_date)='$currentMonth'");
$com_name = $obj->details_by_cond("tbl_company","company_id='$token'");


//taking month and years
$day = date('M-Y');
// $rowCount = $obj->Total_Count("tbl_agent", " ag_status='1' AND pay_status='1' AND due_status='0' AND  bill_status='0' "); 
?>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<!--<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>-->
<script src="asset/js/jquery-ui.min.js"></script>
<style>

    .ui-progressbar {
        position: relative;
    }

    .progress-label {
        text-align: center;
        position: absolute;
        left: 50%;
        top: 4px;
        font-weight: bold;
        /*text-shadow: 1px 1px 0 #fff;*/
    }

    .ui-widget-header {
        border: 1px solid #2f7d2f;
        background: #5cb85c;
        color: #333333;
        font-weight: bold;
    }

    #datatable_view_due_filter input {
        height: 30px;
        border-radius: 5px;
        padding: 5px;
    }
   
        #printButton {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
    

</style>


<div class="row">
    <div class="col-md-12 bg-grey-800"
         style="margin-top:10px; margin-bottom: 15px; min-height:45px; padding:8px 0px 0px 15px; font-size:16px; font-family:Lucida Sans Unicode; font-weight:bold;">
        <div class="row">
            <div class="col-md-4">
                <b>Company Billing Information
                    
            </div>
           
                <div class="col-md-6" style="font-family: Helvetica;">
                <!-- <b>Company Name: <?php echo $com_name['company_name'] ?> -->
                </div>
              
            <div class="col-md-2">
                 <!--First page (billing_info.php) -->
                <!--<button id="printButton" onclick="window.location.href='?q=company_bill_print'" class="btn btn-primary btn-sm">-->
                <!--        Print Invoice-->
                <!--</button>-->

                <button id="printButton" onclick="printDiv('bill_print')" class="btn btn-primary btn-sm">Print Invoice</button>
            </div>
            

        </div>
    </div>
</div>

<div class="row" style="margin-bottom:10px;">
    

<?php
$msg = isset($_GET['msg']) ? $_GET['msg'] : null;
if ($msg != null) {
    ?>
    <div class="col-md-12">
        <div class="alert alert-info">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Message: </strong><?= $msg ?>
        </div>
    </div>
    <?php
}
?>
<div id="bill_print">
     
<div class="row" style="font-size:17px">
            <div class="col-md-2" style="font-family: Helvetica;margin-left: 76%;">
                Date : <?php echo date('d-F-Y') ?>
            </div>
            <div class="col-md-10" style="font-family: Helvetica;padding-bottom: 9px;padding-left: 26px;">
                <b>Bill to : <?php echo $com_name['company_name'] ?>.<br>
                <b> <?php echo $com_name['company_address'] ?>
            </div>
    </div>
    <div class="col-md-10" style="font-family: Helvetica;padding-bottom: 9px;padding-left: 10px;">
           
            <b>Description of Bill : Bill for <?php echo Date('F') ?>-<?php echo Date('Y') ?> monthly basis
    </div>
<div class="row" style="font-size:12px">
    <div class="col-md-12 table-responsive">
        <table class="table table-responsive table-bordered table-hover table-striped" id="datatable_view_due">
            <thead>
                <tr class="bg-teal-800">
                    <th class="col-md-1">Sl</th>
                    <th class="col-md-1">Zone</th>
                    <th class="col-md-1">Area</th>
                    <th class="col-md-1">Client Name</th>
                    <th class="col-md-1">District</th>
                    <th class="col-md-1">Service Start Date</th>
                    <th class="col-md-1">One-time Installation</th>
                    <th class="col-md-1">Regular Bill</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $previous_zone = '';
                $previous_area = '';
                $zone_count = 0;
                $area_count = 0;
                $rowspan_data = [];
                
                // Fetch data for the current month and year
                $allDuePayment = $obj->view_all_by_cond("vw_agent", "company_id='$token' and Year(entry_date)='$currentYear' and Month(entry_date)='$currentMonth'");

                // First pass: count occurrences of zone and area combinations
                foreach ($allDuePayment as $key => $value) {
                    $zone = $value['zone_name'];
                    $area = $value['sub_zone_name'];
                    $rowspan_data[$zone][$area][] = $value;
                }
                
                // Now render the table with rowspan applied
                $row_index = 1;
                foreach ($rowspan_data as $zone => $areas) {
                    foreach ($areas as $area => $rows) {
                        $zone_rowspan = count($rows); // Number of rows for this zone
                        $area_rowspan = count($rows); // Number of rows for this area

                        foreach ($rows as $index => $row) {
                            $is_first_zone_row = $index == 0; // First row of this zone
                            $is_first_area_row = $index == 0; // First row of this area
                            
                            echo '<tr>';
                            
                            // Sl column
                            echo '<td>' . $row_index++ . '</td>';
                            
                            // Zone column with rowspan (only on the first row of the zone group)
                            if ($is_first_zone_row) {
                                echo '<td rowspan="' . $zone_rowspan . '">' . $zone . '</td>';
                            }

                            // Area column with rowspan (only on the first row of the area group)
                            if ($is_first_area_row) {
                                echo '<td rowspan="' . $area_rowspan . '">' . $area . '</td>';
                            }

                            // Other columns
                            echo '<td>' . $row['link_name'] . '</td>';
                            echo '<td>' . $row['district'] . '</td>';
                            echo '<td>' . $row['connection_date'] . '</td>';
                            echo '<td>' . $row['connect_charge'] . '</td>';
                            echo '<td>' . $row['taka'] . '</td>';
                            
                            echo '</tr>';
                        }
                    }
                }
            ?>
            <tr>
                <td colspan="6"></td>
                <th><p><b style="font-width:bold;font-size:17px">Sub total: <?php echo $ontime ?></b></p></th>
                <th><p><b style="font-width:bold;font-size:17px">Total: <?php echo $total_bill ?></b></p></th>
            </tr>
            <?php 
                $grand_total = $ontime + $total_bill;
            ?>
            <tr>
                <td colspan="6"></td>
                <th colspan="2">
                    <p style="margin-left: 100px;">
                        <b style="font-width:bold;font-size:17px">Grand Total: <?php echo $grand_total ?></b>
                    </p>
                </th>
            </tr>
            
            </tbody>
            <tr>
                <th colspan="6" style="text-align: center;"><span style="color: green;">Total Ammount In Word::&nbsp;&nbsp;&nbsp;<span
                                        style="color: red;"><?PHP echo ucwords($formater->convert_number_to_words($grand_total)); ?> Taka only</span></span>
                </th>
            </tr>
        </table>
    </div>
</div>

</div>
<script>
    function printDiv(divName) {
        $(".print").css("margin-left", "0px");
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>

    