<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$token = isset($_GET['token']) ? $_GET['token'] : NULL;

$cusInfo = $obj->details_by_cond("tbl_employee", "id='$token'");

if (isset($_POST['update'])) {
    
        $old_photo = isset($cusInfo['PhotoPath']) ? $cusInfo['PhotoPath'] : NULL;
        $user_photo_path = $old_photo; // default old photo

    if (!empty($_FILES["user_photo"]["name"])) {
        
        // নতুন ফাইলের জন্য রেন্ডম নাম তৈরি
        $seed = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        $rand = array_rand($seed, 6);
        $character = implode('', array_map(fn($n) => $seed[$n], $rand));

        $seed = str_split('1234567890');
        $rand = array_rand($seed, 4);
        $digit = implode('', array_map(fn($n) => $seed[$n], $rand));

        $rend_code = "BSTL" . $character . $digit;
        $nep = "asset/employee/" . $rend_code . ".jpg";

        // পুরানো ছবি থাকলে আগে ডিলিট করো
        if ($old_photo && file_exists($old_photo)) {
            unlink($old_photo);
        }

        // নতুন ছবি আপলোড
        if (copy($_FILES["user_photo"]["tmp_name"], $nep)) {
            $user_photo_path = $nep;
        } else {
            $notification = 'Photo Upload Failed';
        }
    }
    
    
    extract($_POST);
    $joining_update_date = date('Y-m-d', strtotime(str_replace('/','-',$joining_date)));
    $form_data_for_update = array(
        'PhotoPath' => $user_photo_path,
        'em_salary' => $em_salary,
        'employee_id' => $employee_id,
        'employee_name' => str_replace("'",'',$employee_name),
        'employee_details' => str_replace("'",'',$employee_details),
        'employee_mobile_no' => isset($employee_mobile_no)?str_replace("'",'',$employee_mobile_no): null,
        'employee_address' => str_replace("'", "", $employee_address),
        'employee_email' => str_replace("'",'',$email),
        'employee_national_id' => str_replace("'",'',$employee_national_id),
        'designation' => str_replace("'",'',$designation),
        'joining_date' => str_replace("'",'',$joining_update_date),
        'employee_status' => str_replace("'",'',$employee_status),
        'resign_date' => ($resign_date == 0)? date('Y-m-d', strtotime($resign_date)):NULL,
        'update_by' => $userid
    );
    
    
    
    $customer_update = $obj->Update_data("tbl_employee", $form_data_for_update, "where id='$token'");
// exit;
    if ($customer_update) {
        ?>
        <script>
            window.location = "?q=edit_employee&token=<?php echo $token; ?>";
        </script>
        <?php
    } else {
        $notification = 'Update Failed';
    }
}
?>

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

<?php if (isset($notification)) {
    ?>
    <div class="col-md-8 col-md-offset-2">
        <div class="bs-example bs-example-standalone" data-example-id=dismissible-alert-js>
            <div class="alert alert-warning alert-dismissible fade in" role=alert>
                <button type=button class=close data-dismiss=alert aria-label=Close><span aria-hidden=true>&times;</span>
                </button>
                <strong><?php echo $notification; ?></strong>.
            </div>
        </div>
    </div>
    <?php
}
?>
<span style="color: red; font-size: 20px;text-align:left;"></span>
<div class="row" style="padding:10px; font-size: 12px;">
    <form role="form" enctype="multipart/form-data" method="post">
        <div class="row" style="padding:10px; font-size: 12px;">
            <div class="col-md-6 col-md-offset-3 bg-teal-700 text-center" style="margin-bottom:5px;">
                <h4>Welcome to Edit page of <?php ?></h4>
            </div>
            <div class="col-md-6 col-md-offset-3">
            <div class="form-group">
                <img width="140" height="140" 
                     src="<?php echo isset($cusInfo['PhotoPath']) && file_exists($cusInfo['PhotoPath']) ? 
                                $cusInfo['PhotoPath'] : 'asset/img/def_img.png'; ?>" 
                     alt="..." class="img-thumbnail" id="pre_photo">
            </div>
            <div class="form-group">
                        <label for="exampleInputFile">Chose Photo</label>
                        <input type="file" value="<?php echo isset($cusInfo['user_photo']) ? $cusInfo['user_photo']:NULL; ?>" name="user_photo" onchange="usershow_photo(this)" id="photo">
            </div>

                <div class="form-group">
                    <label>Employee's Basic Salary</label>
                    <input id="" type="text" name="em_salary" value="<?php echo isset($cusInfo['em_salary']) ? $cusInfo['em_salary']:NULL; ?>" class="form-control" required>
                </div>
            
                <div class="form-group">
                    <label>Employee's ID</label>
                    <input type="text" name="employee_id" value="<?php echo isset($cusInfo['employee_id']) ?
                    $cusInfo['employee_id']:NULL;
                    ?>" class="form-control" placeholder="Edit employee Name"
                           required="required">
                </div>
                <div class="form-group">
                    <label>Employee's Name</label>
                    <input type="text" name="employee_name" value="<?php echo isset($cusInfo['employee_name']) ?
                    $cusInfo['employee_name']:NULL;
                    ?>" class="form-control" placeholder="Edit employee Name"
                           required="required">
                </div>
                
                <div class="form-group">
                    <label>Mobile No</label>
                    <input type="text" value="<?php echo isset($cusInfo['employee_mobile_no']) ? $cusInfo['employee_mobile_no']:NULL; ?>" name="employee_mobile_no" onkeypress="return numbersOnly(event)" class="form-control"
                           id="ResponsiveTitle"
                           placeholder="Edit Employee Mobile No">
                </div>
                <div class="form-group">
                    <label>Employee Address</label>
                    <textarea class="form-control" name="employee_address" id="ResponsiveDetelis" rows="3"><?php echo isset($cusInfo['employee_address']) ? $cusInfo['employee_address']:NULL; ?></textarea>
                </div>
                
                
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email"  value="<?php echo isset($cusInfo['employee_email']) ? $cusInfo['employee_email']:NULL; ?>"
                           name="email" class="form-control" id="ResponsiveTitle"
                           placeholder="Edit Email Address">
                </div>
                
                <div class="form-group">
                    <label>Employee National ID</label>
                    <textarea class="form-control" name="employee_national_id" id="ResponsiveDetelis" rows="1"><?php echo isset($cusInfo['employee_national_id']) ? $cusInfo['employee_national_id']:NULL; ?></textarea>
                </div>
                <div class="form-group">
                    <label>Employee Designation</label>
                    <textarea class="form-control" name="designation" id="ResponsiveDetelis" rows="1"><?php echo isset($cusInfo['designation']) ? $cusInfo['designation']:NULL; ?></textarea>
                </div>
                <div class="form-group">
                    <label>Employee's Joining Date</label>
                    <input id="datepicker" type="text" name="joining_date" value="<?php echo isset($cusInfo['joining_date']) ? date('d/m/Y',strtotime($cusInfo['joining_date'])) :NULL; ?>" class="form-control" placeholder="dd/mm/yyyy" required>
                </div>
                
                <div class="form-group">
                <label>Employee's  Note</label>
                <textarea class="form-control" name="employee_details" id="ResponsiveDetelis" rows="3"><?php echo isset($cusInfo['employee_details']) ? $cusInfo['employee_details']:NULL; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Employee Resign</label>
                    <select class="form-control" name="employee_status">
                        <option <?php echo isset($cusInfo['employee_status'])? (($cusInfo['employee_status'] ==1)?'selected':''):'' ?> value="1">Running</option>
                        <option <?php echo isset($cusInfo['employee_status'])?(($cusInfo['employee_status'] ==0)?'selected':''):'' ?> value="0">Resign</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Employee's Resign Date</label>
                    <input id="datepickerres" type="text" name="resign_date" value="<?php echo isset($cusInfo['resign_date'])? ( ($cusInfo['employee_status'] == 1)?'': date('d/m/Y',strtotime($cusInfo['resign_date'])))  :NULL; ?>" class="form-control" placeholder="dd/mm/yyyy" >
                </div>
                
            </div>
        </div>
        <!--            row-->
        <div class="row" style="text-align: center; padding: 5px 0px 15px 25px; font-size: 12px;">
            <button type="submit" class="btn btn-success text-center" name="update">Update Employee</button>
        </div>
    </form>
</div>

<link rel="stylesheet" type="text/css" href="asset/css/bootstrap-datepicker.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>

<script>
    $(document).ready(function () {
        $('#datepicker').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            toggleActive: true,
        });
        $('#datepickerres').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            toggleActive: true,
        });
    })
</script>
<script>
    $(document).ready(function () {
        function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function (e) {
                $('#pre_photo').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#photo").change(function(){
        readURL(this);
    });
    });
</script>