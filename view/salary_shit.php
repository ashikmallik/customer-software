<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;


$salary = $obj->view_all('tbl_employee_transaction');


?>
<div class="row">
    <div class="col-md-12 padding_5_px">
        <?php $obj->notificationShowRedirect(); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12 bg-teal-800">
        <h4 class="col-md-9">View All Employee</h4>
        <button type="submit" class="btn btn-primary btn-sm pull-right" onclick="printDiv('print_transaction')">Print Transaction
        </button>
    </div>
</div>
<hr>
<div class="row" style="font-size:12px;" id="print_transaction">
    <div class="col-md-12">
        <table class="table table-responsive table-bordered table-hover " id="datatable">
            <thead>
                <tr class="bg-teal-800">
                    <th class="col-md-1">SL</th>
                    <th class="col-md-1">Employee Name</th>
                    <th class="col-md-1">Mobile</th>
                    <th class="col-md-1">Designation</th>
                    <th class="col-md-1">Salay</th>
                    <th class="col-md-1">Joining Date</th>
                    <th class="col-md-1">Employee Details</th>
                    
                </tr>
            </thead>
            <tbody>
            <?php $i = 0;
            foreach ($salary as $salary) {
                 $employee_Id = $salary['employee_Id'] ;
                $employee = $obj->view_all_by_cond("tbl_employee", "id='$employee_Id'");
                foreach($employee as $employee){
                
            ?>
            
                <tr>
                    <td class="text-center"> <?php echo ++$i ?> </td>
                    
                    <td class="text-center"><?php echo $employee['employee_name'] ? $employee['employee_name'] : NULL; ?> </td>
                    <td class="text-center"> <?php echo $employee['employee_mobile_no'] ?> </td>
                    <td class="text-center"> <?php echo $employee['employee_email'] ?> </td>
                    <td class="text-center"> <?php echo $salary['salary_amount'] ?> </td>
                    <td class="text-center"> <?php echo $employee['designation'] ?> </td>
                    <td class="text-center"> <?php echo $employee['employee_address'] ?> </td>
                    
                </tr>
            <?php }} ?>
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

<link rel="stylesheet" type="text/css" href="asset/css/bootstrap-datepicker.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>