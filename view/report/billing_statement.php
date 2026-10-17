<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$day = date('M-Y');


//check_bill_create_or_not
$cnt = $obj->Total_Count("monthly_bill_making_check", "month_year='$day'");
if ($cnt != 1) {
    // pull all the customer in bill collection
    //weather the have no dues or paid
    //in next step we erase those customer who have no dues.
    $form_data = array(
        'pay_status' => 1, //Customer didn't pay yet
        'due_status' => 0 //no dues
    );
    $obj->Update_data("tbl_agent", $form_data, "pay_status!=5");

    $form_data2 = array(
        'month_year' => $day //Insert current month date for future check weather bill is created or notification
    );
    $obj->Insert_data("monthly_bill_making_check", $form_data2);
}


// ========== Now remove the customer from bill collection =================
// ========== Who paid already or no dues =================
foreach ($obj->view_all_by_cond("tbl_agent", "ag_status='1'") as $detailsid) {
    extract($detailsid);
    $id = $detailsid['ag_id'];
    $dues =$bill->get_customer_dues($id);
    //customer remove from bill collection list who have advanced paid or no dues
    if ($dues <= 0) {
        $form_data = array(
            'pay_status' => 0, //Customer paid
            'due_status' => 0 //no dues
        );
        $obj->Update_data("tbl_agent", $form_data, "where ag_id='$id'");
    }
    // this condition for if delete any given entry then comes bill list
    if ($dues > 0) {
        $form_data = array(
            'pay_status' => 1, //Customer paid
        );
        $obj->Update_data("tbl_agent", $form_data, "where ag_id='$id'");
    }
}

//End word text
?>


<div class="col-md-12" style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
    <b><?php echo isset($notification) ? $notification : NULL; ?></b>
</div>
<div class="col-md-12" style=" background:#606060; margin-top:20px; margin-bottom: 15px; min-height:45px; padding:8px 0px 0px 15px; font-size:16px; font-family:Lucida Sans Unicode; color:#FFFFFF; font-weight:bold;">
    <div class="col-md-6">
        <b>Account Statement </b>
    </div>               
</div>
<div class="col-md-12" style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
    <b><?php echo isset($notification) ? $notification : NULL; ?></b>
</div>
<div class="col-md-12" style=" background:#606060; margin-top:20px; margin-bottom: 15px; min-height:45px; padding:8px 0px 0px 15px; font-size:16px; font-family:Lucida Sans Unicode; color:#FFFFFF; font-weight:bold;">   
    <form  action="" method="POST">                      
        <div class="col-md-5">
            <div class="form-group">
                <label class="col-sm-6" >Year</label>
                <select class="form-control" required="required" name="dateto" id="status">
                    <option  value="">--select--</option>                                  
                    <option  value="2015">2015</option>
                    <option  value="2016">2016</option>
                    <option  value="2017">2017</option>
                    <option  value="2018">2018</option>
                    <option  value="2019">2019</option>
                    <option  value="2020">2020</option>
                    <option  value="2021">2021</option>
                    <option  value="2022">2022</option>
                    <option  value="2023">2023</option>
                    <option  value="2024">2024</option>
                    <option  value="2025">2025</option>                                
                </select>
            </div>                 
        </div>            
        <div class="col-md-2" style="margin-top: 30px;">
            <button type="submit"  name="search" class="btn btn-primary"><i class="fa fa-search"></i>&nbsp;&nbsp;Search</button> 
        </div>
    </form>                  
</div>

<?php
if (isset($_POST['search'])) {
    extract($_POST);
    ?>
    <div class="row">
        <div class="col-md-1 col-md-offset-10" >
            <button type="submit" class="btn btn-primary btn-lg pull-right" onclick="printDiv('year_table')"  >Print Statement</button>
        </div>
    </div>
    <div class="row" id="year_table">  
        <div class="col-md-11" >
            <h2 style="text-align:center;">The Yearly Statement of <?php echo $dateto ?></h2>
        </div>

        <div class="col-md-11" >
            <table class="table table-responsive table-bordered table-hover table-striped">
                <thead> 
                    <tr class="bg-teal-800">
                        <th class="col-md-1 text-center">
                            Month
                        </th>
                        <th class="col-md-1 text-center">
                            Total Client
                        </th>

                        <th class="col-md-1 text-center">
                            Generated Client
                        </th>
                        <th class="col-md-1 text-center">
                            New Client
                        </th>
                        <th class="col-md-1 text-center">
                            Bill
                        </th>
                        <th class="col-md-1 text-center">
                            General Bill
                        </th>
                        <th class="col-md-1 text-center">
                            New Client Bill
                        </th>
                        <th class="col-md-1 text-center">
                            Previous Due 
                        </th>
                        <th class="col-md-1 text-center">
                            Total Bill
                        </th>
                        <th class="col-md-1 text-center">
                            Total Collection
                        </th>
                        <th class="col-md-1 text-center">
                            Total Due bill
                        </th>

                    </tr>
                </thead> 
                <tbody>
                    <?php
                    $max = 12;
                    $i = 0;
                    $total_client = 0;
                    $total_bill = 0;
                    $month_digit = date('n', strtotime(($date)));
                    $year_digit = date('Y', strtotime(($date)));
                    if ($year_digit == $dateto) {
                        $max = $month_digit;
                    }
                    for ($j = 1; $j <= $max; $j++) {
                    
                        $new_client = $obj->Total_Count('tbl_agent', "ag_status = 1 AND MONTH(entry_date)='$j' and YEAR(entry_date)='$dateto'");
                        
                        $new_client_for_calculate_total = $obj->Total_Count('tbl_agent', "ag_status = 1 AND MONTH(entry_date)='($j-1)' and YEAR(entry_date)='$dateto'");
                        
                        $total_client += $new_client_for_calculate_total + $new_client;
                        
                        
                        $total_generated_client = $obj->Total_Count("tbl_agent", "ag_status='1' and pay_status='1' AND due_status='0' AND MONTH(entry_date)='$j' and YEAR(entry_date)='$dateto' ORDER BY `tbl_agent`.`ag_id` DESC");
                        
                        
                        $bill_for_calculate_total_bill = $obj -> view_selected_field_by_cond('tbl_agent', "sum(`taka`) as 'taka'", "ag_status = 1 AND MONTH(entry_date)='$j' and YEAR(entry_date)='$dateto'");
                        $last_month_bill = $obj -> view_selected_field_by_cond('tbl_agent', "sum(`taka`) as 'taka'", "ag_status = 1 AND MONTH(entry_date)='($j-1)' and YEAR(entry_date)='$dateto'");
                        
                        $total_bill += $last_month_bill[0]['taka'] + $bill_for_calculate_total_bill[0]['taka'];
                        ?>
                        <tr>

                            <td style="text-align: center;">
                                <?php
                                if ($j == 1) {
                                    echo 'January';
                                } else if ($j == 2) {
                                    echo 'February';
                                } else if ($j == 3) {
                                    echo 'March';
                                } else if ($j == 4) {
                                    echo 'April';
                                } else if ($j == 5) {
                                    echo 'May';
                                } else if ($j == 6) {
                                    echo 'June';
                                } else if ($j == 7) {
                                    echo 'July';
                                } else if ($j == 8) {
                                    echo 'August';
                                } else if ($j == 9) {
                                    echo 'September';
                                } else if ($j == 10) {
                                    echo 'October';
                                } else if ($j == 11) {
                                    echo 'November';
                                } else if ($j == 12) {
                                    echo 'December';
                                }
                                ?>
                            </td>
                            <td style="text-align: center;"><?php echo $total_client ?></td>
                            <td style="text-align: center;"><?php echo $total_generated_client ?></td>
                            <td style="text-align: center;"><?php echo $new_client ?></td>
                            <td style="text-align: center;"><?php echo number_format($total_bill) ?></td>
                            <td style="text-align: center;"><?php echo number_format($bill_for_calculate_total_bill[0]['taka']) ?></td>
                            <td style="text-align: center;"></td>
                            <td style="text-align: center;"></td>
                            <td style="text-align: center;"></td>
                            <td style="text-align: center;"></td>
                            <td style="text-align: center;"></td>
                        <?php }
                    } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- end new update part -->

<script>

    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
