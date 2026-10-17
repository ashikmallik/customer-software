<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$notification = "";
$currentYear = date("Y");
$currentMonth = date("m");
$company_id =$obj->view_all("tbl_agent");
foreach($company_id as $company_id){
    $company_id=  $company_id['company_id'];
}

$ontime= $obj->get_sum_by_cond("connect_charge","vw_agent","ag_status=1 and Year(entry_date)='$currentYear' and Month(entry_date)='$currentMonth'");
$total_bill =$obj->get_sum_by_cond("taka","vw_agent","ag_status=1 and Year(entry_date)='$currentYear' and Month(entry_date)='$currentMonth'");
$com_name = $obj->details_by_cond("tbl_company","company_id='$company_id'");



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

</style>

<div class="row">
    <div class="col-md-12 bg-grey-800"
         style="margin-top:10px; margin-bottom: 15px; min-height:45px; padding:8px 0px 0px 15px; font-size:16px; font-family:Lucida Sans Unicode; font-weight:bold;">
        <div class="row">
            <div class="col-md-4">
                <b>View All Company Billing Information 
            </div>
           
                <div class="col-md-4" style="font-family: Helvetica;">
                    <div class="text-center">
                        <!-- <p><b>Total Bill Amount : <?php echo $total_bill ?></b> -->
                        </p>
                    </div>
                </div>
                <?php if ($ty == 'SA') { ?>
                <div class="col-md-4">
                    <div style="float:right; padding-right:10px">
                        
                        
                    </div>
                </div>
            <?php } ?>
            <div class="col-md-2">
                <div class="btn-group" role="group">
                <button id="printButton" onclick="printDiv('month_print')" class="btn btn-primary btn-sm">Print Invoice</button>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="row" style="margin-bottom:10px;">
    

</div>
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
<div class="progress" >
   
</div>

<div class="row" style="font-size:12px" id="month_print">
    <div class="col-md-12 table-responsive">
        <table class="table table-responsive table-bordered table-hover table-striped" id="datatable_view_due">
            <thead>
            <tr class="bg-teal-800">
                <th class="col-md-1">Invoice</th>
                <th class="col-md-1">Company Name</th>
             
                <th class="col-md-1">Phone Number</th>
                <th class="col-md-1">Company Address</th>
                
                <th class="col-md-1">Ontime Connection</th>
                <th class="col-md-1">Reguller Bill</th>
               
              
                
               
                
            </tr>
            </thead>
            <tbody>
            <?php
               $allDuePayment = $obj->view_all_by_cond("vw_agent","ag_status=1 and Year(entry_date)='$currentYear' and Month(entry_date)='$currentMonth' GROUP BY company_id");
                // $allDuePayment = $obj->view_all_by_cond("vw_bill_calc", "ag_status='1' and entry_by='6'  AND pay_status='1'   AND due_status='0'  ORDER BY `vw_bill_calc`.`ag_id` ASC");

                foreach ($allDuePayment as $key=>$value) {
                    ?>
                        <tr>
                            
                            <td><?php echo $key+1 ?></td>
                            
                            <td><?php echo $value['company_name'] ?></td>
                            <td><?php echo $value['phone'] ?></td>
                            <td><?php echo $value['company_address'] ?></td>
                            
                            <td><?php echo $ontime ?></td>
                            <td><?php echo $total_bill ?></td>
                            
                            
                            
                        </tr>
                        <?php  
                        }
                        ?>
                    <tr>
                       
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <th><p><b style="font-width:bold;font-size:17px">Sub total: <?php echo $ontime ?></b></p></th>
                        <th><p><b style="font-width:bold;font-size:17px">Total :<?php echo $total_bill ?></b></p></th>
                    </tr>
                        <?php 
                            $grand_total = $ontime+$total_bill
                        ?>
                    <tr>
                   
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        
                        <th colspan="2"><p style="margin-left: 100px;"><b style="font-width:bold;font-size:17px">Grand Total : <?php echo $grand_total ?></b></p></th>
                    </tr>
            </tbody>
        </table>
       
        
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

