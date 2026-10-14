<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : NULL;


$customers = $obj->view_all_by_cond("tbl_agent", "ag_status=1");
$templates = $obj->view_all("tbl_complain_templates");
$apiKey = $obj->getSettingValue('sms', 'pass');
$smsPass = $obj->getSettingValue('sms', 'password');
$sender = $obj->getSettingValue('sms', 'sender');

$smsUser = $obj->getSettingValue('sms', 'user');
$billGenerateInfo = $obj->getSettingValue('billGenerate', 'billGenerate');

$logo = $obj->getSettingValue('logo', 'logo');
$companyName = $obj->getSettingValue('excel', 'name');
$address = $obj->getSettingValue('address', 'address');
$mobile = $obj->getSettingValue('mobile', 'mobile');
$email = $obj->getSettingValue('email', 'email');
$employees = $obj->view_all_by_cond('tbl_employee', 'employee_status=1');
$supportLink = $obj->getSettingValue('supportLink', 'supportLink');
$customerIdPrefix = $obj->getSettingValue('customerIdPrefix', 'customerIdPrefix');

// var_dump($byId);
// exit();
if (isset($_POST['submit'])) {

    // Input theke value newa
    $smsApiKey = $_POST['sms_api_key'];
    $smsSender = $_POST['sms_sender_number'];
    $smsUsername = $_POST['sms_user_name'];
    $smspassword = $_POST['sms_pass_name'];

    // image upload
    if (isset($_FILES['logo'])) {
        $targetDir = "assets/images/bsd/";
        $fileName = $_FILES["logo"]["name"];
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $targetFilePath)) {
            // Insert image file name into database
            $obj->updateSettingValue('logo', $targetFilePath, 'logo');
        }
    }
    if (isset($_POST['companyName'])) {
        $companyName = $_POST['companyName'];
        $obj->updateSettingValue('excel', $companyName, 'name');
    }

    $addressInfo = isset($_POST['address']) ? $_POST['address'] : "";
    if (isset($address)) {
        $obj->updateSettingValue('address', $addressInfo, 'address');
    } else {
        $obj->insertData('tbl_setting', ['value' => $addressInfo, 'field' => 'address', 'other_parameter' => 'address', 'description' => 'Company Address']);
    }

    $mobileInfo = isset($_POST['mobile']) ? $_POST['mobile'] : "";
    if (isset($mobile)) {
        $obj->updateSettingValue('mobile', $mobileInfo, 'mobile');
    } else {
        $obj->insertData('tbl_setting', ['value' => $mobileInfo, 'field' => 'mobile', 'other_parameter' => 'mobile', 'description' => 'Mobile Number']);
    }

    $emailInfo = isset($_POST['email']) ? $_POST['email'] : "";
    if (isset($email)) {
        $obj->updateSettingValue('email', $emailInfo, 'email');
    } else {
        $obj->insertData('tbl_setting', ['value' => $emailInfo, 'field' => 'email', 'other_parameter' => 'email', 'description' => 'Email Address']);
    }

    $billGenerateStatus = isset($_POST['billGenerateStatus']) && $_POST['billGenerateStatus'] == 'active' ? 'active' : 'inactive';
    if ($billGenerateInfo) {
        $obj->updateSettingValue('billGenerate', $billGenerateStatus, 'billGenerate');
    } else {
        $obj->insertData('tbl_setting', ['value' => $billGenerateStatus, 'field' => 'billGenerate', 'other_parameter' => 'billGenerate', 'description' => 'Bill Generate Status if Active then Bill Generate will be enable']);
    }

    $supportLinkPost = isset($_POST['supportLink']) ? $_POST['supportLink'] : "";
    if (isset($supportLink)) {
        $obj->updateSettingValue('supportLink', $supportLinkPost, 'supportLink');
    } else {
        $obj->insertData('tbl_setting', ['value' => $supportLinkPost, 'field' => 'supportLink', 'other_parameter' => 'supportLink', 'description' => 'Support Link']);
    }

    $customerIdPrefixPost = isset($_POST['customerIdPrefix']) ? $_POST['customerIdPrefix'] : "";
    if (isset($customerIdPrefix)) {
        $obj->updateSettingValue('customerIdPrefix', $customerIdPrefixPost, 'customerIdPrefix');
    } else {
        $obj->insertData('tbl_setting', ['value' => $customerIdPrefixPost, 'field' => 'customerIdPrefix', 'other_parameter' => 'customerIdPrefix', 'description' => 'Customer ID Prefix']);
    }

    // Empty check
    if ($smsApiKey != '' || $smsSender != '' || $smsUsername != '') {

        // Update each setting
        $obj->updateSettingValue('sms', $smsApiKey, 'pass');
        $obj->updateSettingValue('sms', $smsSender, 'sender');
        $obj->updateSettingValue('sms', $smsUsername, 'user');
        $obj->updateSettingValue('sms', $smspassword, 'password');

        // Notification and redirect
        $obj->notificationStore('Data Updated Successfully.', 'success');
        echo '<script> window.location="?page=setting"; </script>';
    } else {
        $obj->notificationStore('Data Added Failed.', 'success');
        echo '<script> window.location="?page=setting"; </script>';
    }
}

$procedureExists = false;
try {
    $checkSql = "SELECT ROUTINE_NAME 
                 FROM information_schema.ROUTINES 
                 WHERE ROUTINE_SCHEMA = DATABASE() 
                 AND ROUTINE_TYPE='PROCEDURE' 
                 AND ROUTINE_NAME='sales_empty_database'";
    $res = $obj->rawSql($checkSql); // $obj->rawSql() returns array of rows
    if (!empty($res)) {
        $procedureExists = true;
    }
} catch (Exception $e) {
    $procedureExists = false;
}
$macking_chek = $obj->rawSql("select * from monthly_bill_making_check");

// ==== Empty Database Procedure Call ====
if (isset($_POST['empty_database_btn'])) {
    try {
        $sql = "CALL sales_empty_database()";
        $succ = $obj->rawSqlSingle($sql);
        if ($succ) {
            $obj->notificationStore('Database emptied successfully!', 'success');
            echo "<script>alert('✅ Database emptied successfully!'); window.location='?page=setting';</script>";
        } else {
            echo "<script>alert('❌ Failed to execute procedure.');</script>";
        }
    } catch (Exception $e) {
        echo "<script>alert('⚠️ Error: " . $e->getMessage() . "');</script>";
    }
}

if (isset($_POST['insert_monthly'])) {
    $dattee = date("M-Y");
    $data = [
        'month_year' => $dattee,
        'tbillgenerate' => 1,
        'tcustomer' => 1
    ];
    $insucc = $obj->insertData("monthly_bill_making_check", $data);
}

if (isset($_POST['empty_making_check'])) {
    $del = $obj->rawSql("TRUNCATE TABLE monthly_bill_making_check");
}

?>
<style>
    .selection {
        width: 100%;
    }
</style>
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <h6 class="text-md text-neutral-500">Settiongs</h6>

            <!-- Form Wizard Start -->
            <div class="form-wizard">
                <form enctype="multipart/form-data" method="post">

                    <fieldset class="wizard-fieldset show">
                        <div class="row gy-3">

                            <div class="col-sm-4">
                                <label class="form-label">Sms Api Key</label>
                                <div class="position-relative">
                                    <textarea name="sms_api_key" class="form-control wizard-required" placeholder="Enter Sms Api Key"><?php echo $apiKey ?></textarea>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Sms Sender Number</label>
                                <div class="position-relative">
                                    <textarea name="sms_sender_number" class="form-control wizard-required" placeholder="Enter Sms Sender Number"><?php echo $sender ?></textarea>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Sms User Name</label>
                                <div class="position-relative">
                                    <textarea name="sms_user_name" class="form-control wizard-required" placeholder="Sms User Name"><?php echo $smsUser ?></textarea>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Sms Password</label>
                                <div class="position-relative">
                                    <input type="password" name="sms_pass_name" value="<?php echo $smsPass ?>" class="form-control wizard-required" placeholder="Sms User Name">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Company Name</label>
                                <div class="position-relative">
                                    <textarea name="companyName" class="form-control wizard-required" placeholder="Company Name"><?php echo $companyName ?></textarea>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Address</label>
                                <div class="position-relative">
                                    <textarea name="address" class="form-control wizard-required" placeholder="Address"><?php echo $address ?></textarea>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Mobile Number</label>
                                <div class="position-relative">
                                    <textarea name="mobile" class="form-control wizard-required" placeholder="Mobile Number"><?php echo $mobile ?></textarea>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Email Address</label>
                                <div class="position-relative">
                                    <input name="email" class="form-control wizard-required" value="<?php echo $email ?>" placeholder="Email Address">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Support Link</label>
                                <div class="position-relative">
                                    <input type="text" name="supportLink" class="form-control wizard-required" value="<?php echo $supportLink ?>" placeholder="Support Link">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Customer ID Prefix</label>
                                <div class="position-relative">
                                    <input type="text" name="customerIdPrefix" class="form-control wizard-required" value="<?php echo $customerIdPrefix ?? 'CUS' ?>" placeholder="Customer ID Prefix">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Logo</label>
                                <div class="row">
                                    <div class="position-relative col-md-10">
                                        <input name="logo" class="form-control wizard-required" type="file" placeholder="Logo">
                                    </div>
                                    <div class="col-md-2">
                                        <img src="<?php echo $logo ?>" style="height: 50px;" class="img-fluid" alt="logo">
                                    </div>
                                </div>

                            </div>

                            <div class="form-check mb-2" style="margin-top: 50px; margin-left: 10px;">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    value="active"
                                    id="billGenerateStatus"
                                    name="billGenerateStatus"
                                    <?php echo isset($billGenerateInfo) && $billGenerateInfo == 'active' ? 'checked' : ''; ?>>
                                <label class="form-check-label" style="margin-left: 5px;" for="billGenerateStatus">
                                    Bill Generate Status
                                    <?php if (isset($billGenerateInfo) && $billGenerateInfo == 'active') { ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php } else { ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php } ?>
                                </label>

                            </div>

                            <div class="form-group text-end">
                                <button type="submit" name="submit" class="form-wizard-next-btn btn btn-success-600 px-32">Submit</button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <!-- Form Wizard End -->

        </div>
    </div>




    <!-- ==============================================================================
    ========================== cron job setup start ==================================
    =============================================================================== -->
    <!-- cron job setting start -->
    <?php
        $host = $_SERVER['HTTP_HOST'];
        $parts = explode('.', $host);
        $domain = '';
        if(count($parts) >= 2){
            $domain = $parts[count($parts)-2] . '.' . $parts[count($parts)-1];
        }
        $serverUserName = "";
        if($domain == "robotisp.com"){
          $serverUserName = "robotis2";
        } else if($domain == "bsdnew.com"){
          $serverUserName = "bsdnewco";
        } else if($domain == "bsdisp.com"){
          $serverUserName = "bsdispco";
        } else if($domain == "bsdbd.xyz"){
          $serverUserName = "bsdbdxyz";
        } else if($domain == "robotispsoft.net"){
          $serverUserName = "robotisp";
        } else if($domain == "bsdisp.xyz"){
          $serverUserName = "bsdispxy";
        }

        if(isset($_POST['set_cron'])){
            // Get form values
            $minute = $_POST['minute'];
            $hour   = $_POST['hour'];
            $day    = $_POST['day'];
            $month  = $_POST['month'];
            $week   = $_POST['week'];
            $host = $_SERVER['HTTP_HOST'];


            // The script you want to run
            $script = "/usr/local/bin/php /home/" . $serverUserName . "/" . $host . "/schedule_disconnect.php";

            // Build new cron job line
            $newjob = "$minute $hour $day $month $week $script";

            // Read existing cron jobs
            $current = shell_exec("/bin/crontab -l 2>/dev/null");

            $lines = preg_split("/\r\n|\n|\r/", trim($current));
            
            $lines = array_filter($lines, function ($line) {
            
                $line = trim($line);
            
                if ($line === '') {
                    return false;
                }
            
                // Skip backup messages
                if (strpos($line, 'Backup of ') === 0) {
                    return false;
                }
            
                // Keep environment variables
                if (preg_match('/^(MAILTO|SHELL)=/', $line)) {
                    return true;
                }
            
                // Keep comments
                if (strpos($line, '#') === 0) {
                    return true;
                }
            
                // Keep only valid cron entries
                return preg_match('/^(\S+\s+){5}.+$/', $line);
            });

            $found = false;
            foreach($lines as $index => $line){
                if(strpos($line, $script) !== false){
                    // Replace existing cron line
                    $lines[$index] = $newjob;
                    $found = true;
                    break; // stop after first match
                }
            }

            if(!$found){
                // append if not found
                $lines[] = $newjob;
            }

            // Prepare updated cron content
            $updated = implode(PHP_EOL, array_filter($lines)) . PHP_EOL;

            // Save to temp file
            // $file = "/tmp/cron_robotisp.txt";
            $file = "/tmp/cron_" . $serverUserName . ".txt";
            file_put_contents($file, $updated);

            // Load updated cron
            shell_exec("crontab $file");
            
            $obj->notificationStore("Created Cron Job Successful", 'success');
            echo '<script>window.location="?page=setting";</script>';
        }
    ?>

    <!-- read cron job -->
    <?php
        // Path of the script to check
        $scriptPath = "/home" . "/" . $serverUserName . "/" . $host . "/schedule_disconnect.php";

        // Read existing crons
        $current = shell_exec("crontab -l 2>/dev/null");

        // Default values
        $cronExists = false;
        $cronMinute = "";
        $cronHour   = "";
        $cronDay    = "";
        $cronMonth  = "";
        $cronWeek   = "";

        if(!empty($current)){
            $lines = explode("\n", $current);
            foreach($lines as $line){
                if(strpos($line, $scriptPath) !== false){
                    $cronExists = true;
                    // Parse cron timing
                    $parts = preg_split('/\s+/', $line);
                    if(count($parts) >= 6){
                        $cronMinute = $parts[0];
                        $cronHour   = $parts[1];
                        $cronDay    = $parts[2];
                        $cronMonth  = $parts[3];
                        $cronWeek   = $parts[4];
                    }
                    break; // stop at first match
                }
            }
        }
    ?>

    <!-- cron delete start -->
    <?php
        if(isset($_POST['delete_cron'])){
            // Path of the script to delete
            $scriptPath = "/home" . "/" . $serverUserName . "/" . $host . "/schedule_disconnect.php";

            // Read existing cron
            $current = shell_exec("/bin/crontab -l 2>/dev/null");

            $lines = preg_split("/\r\n|\n|\r/", trim($current));
            
            $lines = array_filter($lines, function ($line) {
            
                $line = trim($line);
            
                if ($line === '') {
                    return false;
                }
            
                // Skip backup message
                if (strpos($line, 'Backup of ') === 0) {
                    return false;
                }
            
                // Keep environment variables
                if (preg_match('/^(MAILTO|SHELL)=/', $line)) {
                    return true;
                }
            
                // Keep comments
                if (strpos($line, '#') === 0) {
                    return true;
                }
            
                // Keep only valid cron entries
                return preg_match('/^(\S+\s+){5}.+$/', $line);
            });
            
            $found = false;

            // Loop to remove the target cron
            foreach($lines as $index => $line){
                if(strpos($line, $scriptPath) !== false){
                    unset($lines[$index]);
                    $found = true;
                    break;
                }
            }

            if($found){
                // Save updated cron
                $updated = implode(PHP_EOL, array_filter($lines)) . PHP_EOL;
                // $file = "/tmp/cron_robotisp.txt";
                $file = "/tmp/cron_" . $serverUserName . ".txt";
                file_put_contents($file, $updated);
                shell_exec("crontab $file");
                
                $obj->notificationStore("Deleted Cron Job Successful", 'success');
                echo '<script>window.location="?page=setting";</script>';
            } else {
                echo "<p style='color:orange;'>No Cron Job Found to Delete</p>";
            }
        }
    ?>
    <!-- cron delete end -->
    
    <div class="d-flex justify-content-center flex-wrap gap-4 mt-5">
        <!-- Add Due SMS Section -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <!-- Form Wizard Start -->
                    <div class="form-wizard">
                        <fieldset class="wizard-fieldset show">
                            <div class="text-center">
                                <div class="col-md-12 bg-teal-800">
                                    <h6 class="text-lg text-neutral-500">CRON JOB Setup</h6>
                                </div>
                                <hr>
                            </div>

                            <form style="padding:20px;" method="post" class="d-flex flex-column align-items-center w-100">

                                <div class="row w-100">

                                    <!-- Minute -->
                                    <div class="col-md-4" style="margin-top:20px;">
                                        <label>Minute</label>
                                        <select required name="minute" class="form-control">
                                            <option value="">Select</option>
                                            <option <?php echo $cronMinute == "*" ? "selected" : "" ?> value="*">Every Minute</option>
                                            <?php for ($i=0;$i<60;$i++){ ?>
                                                <option <?php echo $cronMinute == $i ? "selected" : "" ?> value="<?php echo $i;?>"><?php echo $i;?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <!-- Hour -->
                                    <div class="col-md-4" style="margin-top:20px;">
                                        <label>Hour</label>
                                        <select required name="hour" class="form-control">
                                            <option value="">Select</option>
                                            <option <?php echo $cronHour == "*" ? "selected" : "" ?> value="*">Every Hour</option>
                                            <?php for ($i=0;$i<24;$i++){ ?>
                                                <option <?php echo $cronHour == $i ? "selected" : "" ?> value="<?php echo $i;?>"><?php echo $i;?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <!-- Day -->
                                    <div class="col-md-4" style="margin-top:20px;">
                                        <label>Day of Month</label>
                                        <select required name="day" class="form-control">
                                            <option value="">Select</option>
                                            <option <?php echo $cronDay == "*" ? "selected" : "" ?> value="*">Every Day</option>
                                            <?php for ($i=1;$i<=31;$i++){ ?>
                                                <option <?php echo $cronDay == $i ? "selected" : "" ?> value="<?php echo $i;?>"><?php echo $i;?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <!-- Month -->
                                    <div class="col-md-4" style="margin-top:20px;">
                                        <label>Month</label>
                                        <select required name="month" class="form-control">
                                            <option value="">Select</option>
                                            <option <?php echo $cronMonth == "*" ? "selected" : "" ?> value="*">Every Month</option>
                                            <?php for ($i=1;$i<=12;$i++){ ?>
                                                <option <?php echo $cronMonth == $i ? "selected" : "" ?> value="<?php echo $i;?>"><?php echo $i;?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <!-- Week -->
                                    <div class="col-md-4" style="margin-top:20px;">
                                        <label>Day of Week</label>
                                        <select required name="week" class="form-control">
                                            <option value="">Select</option>
                                            <option <?php echo $cronWeek == "*" ? "selected" : "" ?> value="*">Everyday</option>
                                            <option <?php echo $cronWeek == "0" ? "selected" : "" ?> value="0">Sunday</option>
                                            <option <?php echo $cronWeek == "1" ? "selected" : "" ?> value="1">Monday</option>
                                            <option <?php echo $cronWeek == "2" ? "selected" : "" ?> value="2">Tuesday</option>
                                            <option <?php echo $cronWeek == "3" ? "selected" : "" ?> value="3">Wednesday</option>
                                            <option <?php echo $cronWeek == "4" ? "selected" : "" ?> value="4">Thursday</option>
                                            <option <?php echo $cronWeek == "5" ? "selected" : "" ?> value="5">Friday</option>
                                            <option <?php echo $cronWeek == "6" ? "selected" : "" ?> value="6">Saturday</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4" style="margin-top:50px; text-align: center;">
                                        <button type="submit" name="set_cron" class="btn btn-success">
                                            <?php echo isset($cronExists) && $cronExists == true ? "Update Cron Job" : "Set Cron Job"; ?>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </fieldset>
                        <div class="row mt-3">
                            <div class="col-6">
                                Cron Status: 
                                <?php if (isset($cronExists) && $cronExists == true) { ?>
                                    <span class="badge bg-success">Active</span>
                                <?php } else { ?>
                                    <span class="badge bg-danger">Not Set Yet</span>
                                <?php } ?>
                            </div>
                            <div class="col-6">
                                <?php if(isset($cronExists) && $cronExists == true){ ?>
                                    <form id="delete_cron_job" method="post">
                                        <input type="hidden" name="delete_cron">
                                        <button type="button" id="delete_cron_job_button" class="btn btn-danger btn-sm">
                                            Delete Cron Job
                                        </button>
                                    </form>
                                <?php  } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- cron job setting end -->
    <!-- ==============================================================================
    ========================== cron job setup start ==================================
    =============================================================================== -->



    <?php
    $smsInfo = $obj->details_by_cond("sms", "status='6'");
    $mikrotikDisconCronJob = $obj->getSettingValue('mikrotikDisconCronJob', 'mikrotikDisconCronJob');
    $cronJobSmsSend = $obj->getSettingValue('cronSmsSend', 'cronSmsSend');
    $MikDisconBillStatusRes = $obj->getSettingValue('MikDisconBillStatus', 'MikDisconBillStatus');
    $MikDisconBillday = $obj->getSettingValue('MikDisconday', 'MikDisconday');
    if (isset($_POST['sms_submit'])) {
        extract($_POST);
        $form_data_sms = array(
            'smsbody' => $sms_body,
            'smshead' => isset($activeStatus) && $activeStatus == 'active' ? 'active' : 'inactive',
        );


        if (isset($smsInfo['status']) && $smsInfo['status'] == 6) {
            $smsRow = $obj->Update_data("sms", $form_data_sms, " status='6' ");
        } else {
            $form_data_sms['status'] = 6;
            $smsRow = $obj->insertData("sms", $form_data_sms);
        }

        $mikrotikDisconnectStatus = isset($_POST['mikrotikDisconnect']) && $_POST['mikrotikDisconnect'] == 'active' ? 'active' : 'inactive';
        if ($mikrotikDisconCronJob) {
            $obj->updateSettingValue('mikrotikDisconCronJob', $mikrotikDisconnectStatus, 'mikrotikDisconCronJob');
        } else {
            $obj->insertData('tbl_setting', ['value' => $mikrotikDisconnectStatus, 'field' => 'mikrotikDisconCronJob', 'other_parameter' => 'mikrotikDisconCronJob', 'description' => 'If Active then mikrotik Secret Disconnect will perform by cron job']);
        }

        $cronSmsDay = $_POST['SelectSmsSendingDays'];
        if ($cronJobSmsSend) {
            $obj->updateSettingValue('cronSmsSend', $cronSmsDay, 'cronSmsSend');
        } else {
            $obj->insertData('tbl_setting', ['value' => $cronSmsDay, 'field' => 'cronSmsSend', 'other_parameter' => 'cronSmsSend', 'description' => 'The sms will send before selected days']);
        }


        $BillStatusVal = $_POST['MikDisconBillStatus'];
        if ($MikDisconBillStatusRes) {
            $obj->updateSettingValue('MikDisconBillStatus', $BillStatusVal, 'MikDisconBillStatus');
        } else {
            $obj->insertData('tbl_setting', ['value' => $BillStatusVal, 'field' => 'MikDisconBillStatus', 'other_parameter' => 'MikDisconBillStatus', 'description' => 'bill_status column of tbl_agent']);
        }


        $BillDisDay = $_POST['MikDisconday'];
        if ($MikDisconBillday) {
            $obj->updateSettingValue('MikDisconday', $BillDisDay, 'MikDisconday');
        } else {
            $obj->insertData('tbl_setting', ['value' => $BillDisDay, 'field' => 'MikDisconday', 'other_parameter' => 'MikDisconday', 'description' => 'bill_dis day column of tbl_agent']);
        }


        if ($smsRow) {
            echo '<script> window.location="?page=setting"; </script>';
        } else {
            echo $notification = 'Insert Failed';
        }
    }
    ?>


    <!-- cron job sms settings -->
    <div class="d-flex justify-content-center flex-wrap gap-4 mt-5">
        <!-- Add Due SMS Section -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <!-- Form Wizard Start -->
                    <div class="form-wizard">
                        <fieldset class="wizard-fieldset show">
                            <div class="text-center">
                                <div class="col-md-12 bg-teal-800">
                                    <h6 class="text-lg text-neutral-500">CRON JOB Configuration</h6>
                                </div>
                                <hr>
                                <b><?php echo isset($notification) ? $notification : NULL; ?></b>
                            </div>

                            <form style="padding: 20px;" role="form" enctype="multipart/form-data" method="post" class="d-flex flex-column align-items-center w-100">
                                <div class="mb-3">
                                    <strong style="font-size: 14px" class="badge bg-success" title="Customer Name">{CUSTOMER_NAME}</strong>
                                    <strong style="font-size: 14px" class="badge bg-primary" title="Total Due Amount">{DUE_AMOUNT}</strong>
                                    <strong style="font-size: 14px" class="badge bg-secondary">{PACKAGE_NAME}</strong>
                                    <strong style="font-size: 14px" class="badge bg-warning" title="Regular Monthly Bill">{MONTHLY_BILL}</strong>
                                    <strong style="font-size: 14px" class="badge bg-success">{CUSTOMER_ID}</strong>
                                    <strong style="font-size: 14px" class="badge bg-warning">{IP_ADDRESS}</strong>
                                </div>

                                <div class="form-group w-100">
                                    <label style="font-size:14px">DUE SMS Details</label>
                                    <textarea class="form-control" onkeyup="countCharB(this)" name="sms_body" id="ResponsiveDetelis"
                                        rows="4" style="width: 100%;"><?php echo isset($smsInfo['smsbody']) ? $smsInfo['smsbody'] : NULL; ?></textarea>

                                    <!-- <input type="checkbox" name="status" value="active" id="status" class="form-control"> -->
                                    <!-- checked -->
                                    <div><span id="charNumB"></span></div>
                                </div>

                                <div class="row w-100">
                                    <div class="col-md-4" style="margin-top: 25px;">
                                        <div class="form-check mt-2 mb-10">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                value="active"
                                                id="mikrotikDisconnect"
                                                name="mikrotikDisconnect"
                                                <?php echo isset($mikrotikDisconCronJob) && $mikrotikDisconCronJob == 'active' ? 'checked' : ''; ?>>
                                            <label class="form-check-label" style="margin-left: 5px;" for="mikrotikDisconnect">
                                                Mikrotik User Disconnect
                                                <?php if (isset($mikrotikDisconCronJob) && $mikrotikDisconCronJob == 'active') { ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php } else { ?>
                                                    <span class="badge bg-danger">Inactive</span>
                                                <?php } ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <?php
                                        $currentMonth = date("F");
                                        $totalDays = date("t");
                                        $today = date("d");
                                        ?>

                                        <div class="">
                                            <label for="">Mikrotik Disconnect for day</label>
                                            <select style="width: 300px;" id="MikDisconday" name="MikDisconday" class="form-control">
                                                <option value="cronday" <?php echo (isset($MikDisconBillday) && $MikDisconBillday == 'cronday') ? 'selected' : ''; ?>>Cron job wise</option>
                                                <option value="<?php echo $today ?>" <?php echo (isset($MikDisconBillday) && $MikDisconBillday == $today) ? 'selected' : ''; ?>>Today</option>
                                                <?php for ($i = 1; $i <= $totalDays; $i++): ?>
                                                    <option
                                                        value="<?php echo $i; ?>"
                                                        <?php echo (isset($MikDisconBillday) && $MikDisconBillday == $i) ? 'selected' : ''; ?>>
                                                        <?php echo $currentMonth . " " . $i  ?>
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="">
                                            <label for="">Mikrotik Disconnect for Bill Status</label>
                                            <select style="width: 300px;" id="MikDisconBillStatus" name="MikDisconBillStatus" class="form-control">
                                                <option <?php echo isset($MikDisconBillStatusRes) && $MikDisconBillStatusRes == 'inc_par_paid' ? 'selected' : '' ?> value="inc_par_paid">Include Partial Paid</option>
                                                <option <?php echo isset($MikDisconBillStatusRes) && $MikDisconBillStatusRes == 'exc_par_paid' ? 'selected' : '' ?> value="exc_par_paid">Exclude Partial Paid</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row w-100 mt-5">
                                    <div class="col-md-4">
                                        <div class="form-check" style="margin-top: 25px;">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                value="active"
                                                id="flexCheckDefault"
                                                name="activeStatus"
                                                <?php echo isset($smsInfo['smshead']) && $smsInfo['smshead'] == 'active' ? 'checked' : ''; ?>>
                                            <label class="form-check-label" style="margin-left: 5px;" for="flexCheckDefault">
                                                SMS Status
                                                <?php if (isset($smsInfo['smshead']) && $smsInfo['smshead'] == 'active') { ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php } else { ?>
                                                    <span class="badge bg-danger">Inactive</span>
                                                <?php } ?>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="" style="width: 300px;">
                                            <label for="">Send Sms</label>
                                            <select id="SelectSmsSendingDays" name="SelectSmsSendingDays" class="form-control">
                                                <option <?php echo isset($cronJobSmsSend) && $cronJobSmsSend == '1' ? 'selected' : '' ?> value="1">Before 1 day</option>
                                                <option <?php echo isset($cronJobSmsSend) && $cronJobSmsSend == '2' ? 'selected' : '' ?> value="2">Before 2 days</option>
                                                <option <?php echo isset($cronJobSmsSend) && $cronJobSmsSend == '3' ? 'selected' : '' ?> value="3">Before 3 days</option>
                                                <option <?php echo isset($cronJobSmsSend) && $cronJobSmsSend == '4' ? 'selected' : '' ?> value="4">Before 4 days</option>
                                                <option <?php echo isset($cronJobSmsSend) && $cronJobSmsSend == '5' ? 'selected' : '' ?> value="5">Before 5 days</option>
                                                <option <?php echo isset($cronJobSmsSend) && $cronJobSmsSend == '6' ? 'selected' : '' ?> value="6">Before 6 days</option>
                                                <option <?php echo isset($cronJobSmsSend) && $cronJobSmsSend == '7' ? 'selected' : '' ?> value="7">Before 7 days</option>
                                                <option <?php echo isset($cronJobSmsSend) && $cronJobSmsSend == '8' ? 'selected' : '' ?> value="8">Before 8 days</option>
                                                <option <?php echo isset($cronJobSmsSend) && $cronJobSmsSend == '9' ? 'selected' : '' ?> value="9">Before 9 days</option>
                                                <option <?php echo isset($cronJobSmsSend) && $cronJobSmsSend == '10' ? 'selected' : '' ?> value="10">Before 10 days</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4 pull-right text-center">
                                        <button type="submit" class="btn btn-success text-center" name="sms_submit" style="margin-top: 30px;">
                                            SAVE SMS
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>



   

    <?php
    $cards = 0;

    if ($procedureExists) {
        $cards++;
    }

    if (!empty($macking_chek) && count($macking_chek) == 1) {
        $cards++;
    }

    if (empty($macking_chek)) {
        $cards++;
    }

    // Column width decide
    if ($cards == 1) {
        $col = "col-md-12";
    } elseif ($cards == 2) {
        $col = "col-md-6";
    } else {
        $col = "col-md-4";
    }
    // echo $cards;
    ?>

    <div class="row">
        <?php if ($procedureExists): ?>
            <div class="<?= $col ?> mt-5">

                <!-- Separate Section Wrapper -->
                <div class="p-3 mb-3" style="background:#f8f9fa; border-left:4px solid #dc3545; border-radius:8px;">
                    <h6 class="text-danger mb-2 text-center" style="font-size:14px;">
                        ⚠️ Critical Action Required
                    </h6>
                    <p class="text-muted text-center" style="font-size:12px; line-height:16px;">
                        নিচের বাটনে ক্লিক করলে সম্পূর্ণ ডাটাবেস খালি হয়ে যাবে।
                        দয়া করে খুব নিশ্চিত হয়ে কাজ করুন।
                    </p>
                </div>

                <!-- Main Danger Card -->
                <div class="card border-danger shadow-sm" style="border-width:2px;">
                    <div class="card-body text-center py-4">
                        <form method="post">
                            <button type="submit" name="empty_database_btn"
                                class="btn btn-danger btn-sm px-4"
                                onclick="return confirm('আপনি কি নিশ্চিত যে ডাটাবেস খালি করতে চান? এই কাজটি ফিরিয়ে আনা যাবে না!')">
                                <i class="fa fa-trash"></i> Delete All Data
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        <?php endif; ?>


        <?php if (!empty($macking_chek) && count($macking_chek) == 1): ?>
            <div class="<?= $col ?> mt-5">

                <!-- Separate Section Wrapper -->
                <div class="p-3 mb-3" style="background:#f8f9fa; border-left:4px solid #dc3545; border-radius:8px;">
                    <h6 class="text-danger mb-2 text-center" style="font-size:14px;">
                        ⚠️ Critical Action Required
                    </h6>
                </div>



            </div>
        <?php endif; ?>


        <?php if (empty($macking_chek)): ?>
            <div class="<?= $col ?> mt-5">

                <!-- Section Wrapper -->
                <div class="p-3 mb-3" style="background:#f8f9fa; border-left:4px solid #2df277; border-radius:8px;">
                    <h6 class="text-info mb-2 text-center" style="font-size:14px;">
                        Insert Monthly Bill Making Check
                    </h6>
                </div>

                <!-- Main Success Card -->
                <div class="card border-success shadow-sm" style="border-width:2px;">
                    <div class="card-body text-center py-4">
                        <h6 class="text-success mb-2" style="font-size:15px;">Monthly Bill Making Check Insert</h6>

                        <form method="post">
                            <button type="submit" name="insert_monthly"
                                class="btn btn-success btn-sm px-4"
                                onclick="return confirm('Are you sure? Perform the action')">
                                Insert
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        <?php endif; ?>

    </div>

</div>
<?php $obj->start_script(); ?>
<script>
    $(document).ready(function() {



        // Initialize select2 for multiple select
        $('#support_employees').select2({
            placeholder: "Choose employees...",
        });


    });

    /*$('input[name="complain_date"]').datepicker({
        autoclose: true,
        toggleActive: true,
        format: 'dd-mm-yyyy'
    // });*/
    $('input[name="complain_time"]').timepicker();

    $("#delete_cron_job_button").click(function(){
        
        if(confirm("are you sure?")){
            $("#delete_cron_job").submit();
        }
    })
</script>

<!-- Init js-->
<script src="assets/js/pages/form-advanced.init.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Add Select2 JS (at the end of your body tag) -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<?php $obj->end_script(); ?>