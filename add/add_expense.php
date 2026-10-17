<?php

date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;


//===================Add Function===================

if (isset ($_POST['submit'])) {
    extract($_POST);

    $form_data = array(

        'acc_head' => $acc_id,
        'acc_sub_head_id' => $acc_sub_head_id,
        'date' => $date,
        'vouchar_number' => $vouchar_number,
        'payment_method' => $payment_method,
        'acc_type' => '1',
        'temp_amount' => $amount,
        'bank_number' => $bank_number,
        'acc_description' => str_replace("'", "", $Details),
        'entry_by' => $userid,
        'entry_date' => $date_time,
        'update_by' => $userid
    );
    $service_add = $obj->insert_data("tbl_account", $form_data, " ");

    if ($service_add) {
        ?>
        <script>
            window.location = "?q=view_expense";
        </script>
        <?php
    } else {
        echo $notification = 'Insert Failed';
    }
}
?>

<!--===================end Function===================-->
<script>

    function numbersOnly(e) // Numeric Validation
    {
        var unicode = e.charCode ? e.charCode : e.keyCode
        if (unicode != 8) {
            if ((unicode < 2534 || unicode > 2543) && (unicode < 48 || unicode > 57)) {
                return false;
            }
        }
    }

</script>

<div class="col-md-12"
     style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
    <b><?php echo isset($notification) ? $notification : NULL; ?></b>
</div>
<div class="row" style="padding:10px; font-size: 12px;">
    <form role="form" enctype="multipart/form-data" method="post">
        <div class="row" style="padding:10px; font-size: 12px;">

            <div class="col-md-6 col-md-offset-3">
            <div class="form-group">
                    <label>Expance Date</label>
                    <input onkeypress="return numbersOnly(event)" required="required" type="date" name="date"
                           class="form-control" id="ResponsiveTitle">
                </div>
                <div class="form-group">
                    <label>Vouchar Number </label>
                    <input onkeypress="return numbersOnly(event)" required="required" type="text" name="vouchar_number"
                           class="form-control" id="ResponsiveTitle">
                </div>
                <div class="form-group">
                    <label>Payment method </label>
                    <select class="form-control payment_method" required="required" name="payment_method" id="payment_method" onchange="checkPaymentMethod()">
                        <option>Select payment method</option>
                            <option value="cash">Cash</option>
                            <option value="bank">bank</option>
                    </select>
                </div>
                <div class="form-group" id="bank_number_group" style="display: none;">
                    <label>Bank Number</label>
                    <input onkeypress="return numbersOnly(event)"  type="text" name="bank_number" class="form-control" id="ResponsiveTitle">
                </div>

                <div class="form-group">
                    <label>Account Head</label>
                    <select class="form-control" required="required" name="acc_id" id="status" >
                        <option></option>
                        <?php
                        $i = '0';
                        foreach ($obj->view_all("tbl_accounts_head") as $value) {
                            $i++;
                            ?>
                            <option value="<?php echo isset($value['acc_id']) ? $value['acc_id'] : NULL; ?>"><?php echo isset($value['acc_name']) ? $value['acc_name'] : NULL; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Account Sub Head</label>
                    <select class="form-control" required="required" name="acc_sub_head_id" id="sub_head">
                        <option value="">Select Account Sub Head</option>
                    </select>
                </div>
                

                <div class="form-group">
                    <label>Amount</label>
                    <input onkeypress="return numbersOnly(event)" required="required" type="text" name="amount"
                           class="form-control" id="ResponsiveTitle">
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" name="Details" id="ResponsiveDetelis" rows="6"></textarea>
                </div>
                

            </div>
            <div class="col-md-6"></div>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-success" name="submit">Submit</button>
        </div>
    </form>
</div>
<hr>
<script>
    $(document).ready(function () {
        $('select[name="acc_id"]').select2({
            placeholder: "Select Expense",
            allowClear: true,
        });


    });
    
    
    $(document).ready(function(){

    $('select[name="acc_id"]').change(function(){

        var acc_id = $(this).val();
        console.log(acc_id);
        $.ajax({
            url:"add/get_sub_head.php",
            method:"POST",
             dataType:'json',
            data:{acc_id:acc_id},
            success:function(res){
                console.log(res);
                var options = '<option value="">Select Sub Head</option>';
                $.each(res.SubHead, function(i, item){
                    options += '<option value="'+item.acc_id+'">'+item.acc_sub_head_name+'</option>';
                });
                $('#sub_head').html(options);
            },
            error: function(err){
            console.log("AJAX error:", err);
            }
        });

    });

});
    
</script>
<script>
    function checkPaymentMethod() {
        var paymentMethod = document.getElementById("payment_method").value;
        var bankNumberGroup = document.getElementById("bank_number_group");

        if (paymentMethod === "bank") {
            bankNumberGroup.style.display = "block";
        } else {
            bankNumberGroup.style.display = "none";
        }
    }
</script>