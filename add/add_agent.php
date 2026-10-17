<?php
$mikrotikConnect = false;
require('model/Mikrotik.php');
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$mkDisconnected = [];



    // if (isset($_GET['mik_id'])){
    //     $mik_id = $_GET['mik_id'];
    //     $mikro = $obj->details_by_cond('mikrotik_user', "id =$mik_id");
    //     $mikrotik = new Mikrotik($mikro['mik_ip'], $mikro['mik_username'], $mikro['mik_password']);
    //     if ($mikrotik->checkConnection()) {
    //         $checkmikrotik = true;
    //         $mikrotikConnect = true;
    //         $_SESSION['microtik_conn'] = true;
    //         $mkDisconnected['online']= $mikro['mik_ip'];
    //     }
    //     else{
    //       $mkDisconnected[]= $mikro['mik_ip'];
    //     }
        
    //     $mikrotiks = $obj->view_all('mikrotik_user');
    //     $totrow = $obj->Total_Count('mikrotik_user','id > 0');
    // }else{
   
    //    $totrow = $obj->Total_Count('mikrotik_user','id > 0');
    //     $mkfind= $obj->view_all_by_cond('mikrotik_user', "entry_by =$userid order by id ASC");
    //     if(!empty($mkfind)){
    //         $mikrotiks =$mkfind;
    //     }else{
    //         $mikrotiks = $obj->view_all('mikrotik_user');
    //     }// end if
    //     $mkDisconnected[] = "Not Found .Please Select Mikrotik";
     
       
    //     // $checkmikrotik =false;
    //     // foreach($mikrotiks as $mikro){
    //     //     if($checkmikrotik == true){
    //     //         continue;
    //     //     }
    //     //     $mikrotik = new Mikrotik($mikro['mik_ip'], $mikro['mik_username'], $mikro['mik_password']);
    //     //     if ($mikrotik->checkConnection()) {
    //     //         $checkmikrotik = true;
    //     //         $mikrotikConnect = true;
    //     //         $_SESSION['microtik_conn'] = true;
    //     //         $mkDisconnected['online']= $mikro['mik_ip'];
    //     //         $mik_id = $mikro['id'];
    //     //     }
    //     //     else{
    //     //       $mkDisconnected[]= $mikro['mik_ip'];
    //     //     }
    //     // }//end foreach


    // } //end if get




// if($totrow > 0){
?>


<!-- <div class="row">
    <div class="col-md-12 bg-slate-800" style="margin-top:20px; margin-bottom: 15px;">
        <h4> <strong>Create PPPoE Customer  <?php  echo (isset($mikrotik)) ? '<small>(PPPoE Secret in Mikrotik)</small>' : '' ?> </strong> </h4>
    </div>
        <?php if ($_SESSION['UserType']=='SA'){?><?php } ?>
        <div class="box-tools pull-right col-md-2">
            <div class="btn-group">
                <button type="button" class="btn btn-info">Select Mikrotik</button>
                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <?php foreach ($mikrotiks as $mikrotikmen){?>
                    <li><a href="?q=add_agent&mik_id=<?= $mikrotikmen['id']?>"><?= $mikrotikmen['mik_username']?> - <?= $mikrotikmen['mik_ip']?></a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>

</div>


<div class="row">
    <div class="col-md-12">
        <?php 
         if(!empty($mkDisconnected[0])){
         echo '<div class="row"><div class="col-md-8 col-md-offset-2 margin_15_px"><div class="alert alert-danger text-center"><strong>Sorry, We cant connect to Mikrotik '.$mkDisconnected[0].'</strong><br><small>Please check your connection IP, Login Name and Password.</small></div></div></div>';
      }else{
          echo '<div class="row">
        <div class="col-md-8 col-md-offset-2 margin_15_px">
            <div class="alert alert-success text-center">
                <strong>Welcome To Mikrotik</strong>
                <br>
                <small>Successfully connected to Mikrotik Router by IP
                    :'.$mkDisconnected['online'].'</small>
            </div>
        </div>
    </div>';
      } 
        $obj->notificationShowRedirect(); ?>
    </div>
</div> -->

<?php  // ($totrow > 0) end  ?> 

<div class="row" style="padding:10px; font-size: 12px;">
    <form role="form" action="add/add_agent_action.php" method="post" style="padding:10px; font-size: 12px;">
        <div class="row">
            <div class="col-md-6">
            <div class="form-group">
                    <label>Link Name*</label>
                    <input type="text" name="linkName" class="form-control" required="required">
                </div>
                <div class="form-group">
                    <label>Company Name*</label>
                    <a type="button" style="padding-top: 0px;padding-bottom : 0px;"
                            class="btn btn-default border-white btn-sm pull-right" href="?q=view_all_company">
                        <span class="glyphicon glyphicon-plus"></span> Add Company
                    </a>
                    <select class="form-control" name="company_name" required="required">
                        <option value="">Select Company Name</option>
                        <?php foreach ($obj->view_all('tbl_company') as $singleZone) { ?>
                            <option
                                    value="<?php echo $singleZone['company_id'] ?>"><?php echo $singleZone['company_name'] ?></option>
                        <?php }// foreach ?>
                    </select>
                    
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input onkeypress="return numbersOnly(event)" type="text" name="mobile" class="form-control">
                </div>

                <div class="form-group">
                    <label>Link Address</label>
                    <textarea class="form-control" name="link_address" id="ResponsiveDetelis" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Total Link(KMZ)</label>
                    <textarea class="form-control" name="map_link" id="map_link" rows="4"></textarea>
                </div>
                <div class="form-group">
                    <label>Actual Link(KMZ)</label>
                    <textarea class="form-control" name="actual_link" id="actual_link" rows="4"></textarea>
                </div>

                <div class="form-group">
                    <label>District</label>
                    <select class="form-control" name="district">
                        <option value="">Select District </option>
                        <?php foreach ($obj->view_all('districts') as $singleZone) { ?>
                            <option
                                    value="<?php echo $singleZone['name'] ?>"><?php echo $singleZone['name'] ?></option>
                        <?php }// foreach ?>
                    </select>
                    
                </div>
              
                <div class="form-group">
                    <label>Customer Latitude/Longitude</label>
                    <input type="text" name="cus_lat" class="form-control">
                </div>

                <div class="form-group">
                    <label>BTS Latitude/Longitude</label>
                    <input  type="text" name="bts_lat_long" class="form-control">
                </div>
                <div class="form-group">
                    <label>Total Distance(OTDR)</label>
                    <input  type="text" name="total_distance" class="form-control">
                </div>


                

            </div>
            <div class="col-md-6">
                
                 <div class="form-group">
                    <label>Share Distance</label>
                    <input  type="text" name="share_distance" class="form-control">
                </div>
                <div class="form-group">
                    <label>Billing Length</label>
                    <input  type="text" name="actual_distance" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Impact Link</label>
                    <textarea class="form-control" name="link_share"  id="remarks" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label class="pull-left">Billing Person*</label>
                    <select class="form-control" name="billingperson" required="required">
                        <?php foreach ($obj->view_all('_createuser') as $singleUser) { ?>
                            <option
                                    value="<?php echo $singleUser['UserId'] ?>"><?php echo $singleUser['FullName'] ?></option>
                        <?php }// foreach ?>
                    </select>
                </div>
                
                <div class="form-group">
                <label class="pull-left">Zone/Area*</label>
                <a type="button" style="padding-top: 0px;padding-bottom : 0px;"
                   class="btn btn-default border-white btn-sm pull-right" href="?q=view_all_zone">
                    <span class="glyphicon glyphicon-plus"></span> Add Zone
                </a>
                <select class="form-control" name="zone" id="zone" required="required">
                    <option value="">Select Zone /Area</option>
                    <?php foreach ($obj->view_all('tbl_zone') as $singleZone) { ?>
                        <option value="<?php echo $singleZone['zone_id'] ?>"><?php echo $singleZone['zone_name'] ?></option>
                    <?php } ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="pull-left">Sub Zone/Area</label>
                <a type="button" style="padding-top: 0px;padding-bottom : 0px;"
                   class="btn btn-default border-white btn-sm pull-right" href="?q=view_all_sub_zone">
                    <span class="glyphicon glyphicon-plus"></span> Add Sub Zone
                </a>
                <select class="form-control" name="sub_zone" id="sub_zone">
                    <option value="">Select Sub Zone /Area</option>
                </select>
            </div>


                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>One Time Installation</label>
                            <input onkeypress="return numbersOnly(event)" type="text" name="connect_charge" placeholder="input installation ammount" value="0"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Connection Date*</label>
                            <input id="datepicker" type="text" name="con_date" class="form-control" required autocomplete="off" placeholder="Customer Connection Date">
                        </div>
                    </div>

                </div>

                

                <div class="form-group" id = "netSpeed">
                    <input type="hidden" name="mb" class="form-control"  placeholder="Provide Net Speed">
                </div>

                <div class="form-group" id ="billAmount">
                    <label>Regular Bill*</label>
                    <input onkeypress="return numbersOnly(event)" type="text" name="taka" class="form-control"
                           placeholder="Provide Bill Amount">
                </div>

            

                <div class="form-group" id>
                    <label>Bill Date*</label>
                    <input onkeypress="return numbersOnly(event)" type="text" name="bill_date" value="10" class="form-control"
                           placeholder="Provide Bill Date">
                </div>
            
               
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" required="required" name="status" id="status">
                                <option></option>
                                <option selected value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                </div>

              <div class="form-group">
                    <label>Remarks</label>
                    <textarea class="form-control" name="remarks"  id="remarks" rows="4"></textarea>
                </div>


            </div>
        </div>
        <!--            row-->
        <div class="row text-center">
            <button type="submit" class="btn btn-success" name="submit">Save New Customer</button>
        </div>
    </form>
</div>


<script>
    $(document).ready(function() {
    // Event listener for when the zone is selected
    $('#zone').on('change', function() {
        var zoneId = $(this).val();  // Get the selected zone ID
        
        if (zoneId) {
            // Make AJAX request to fetch sub zones for the selected zone
            $.ajax({
                url: 'view/ajax_action/fetch_sub_zones.php',  // PHP file to handle the AJAX request
                type: 'GET',
                data: { zone_id: zoneId },  // Send the selected zone ID to the server
                success: function(response) {
                    console.log("subzoneid",response);
                    // Update the sub-zone dropdown with the response
                    $('#sub_zone').html(response);
                },
                error: function() {
                    alert('An error occurred while fetching sub zones.');
                }
            });
        } else {
            // If no zone is selected, reset the sub zone dropdown
            $('#sub_zone').html('<option value="">Select Sub Zone /Area</option>');
        }
    });
});

</script>


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


    function noSpace(e) {
        if (e.which == 32) {
            return false;
        }
    }

    $(document).ready(function () {
        // look client Id is unique or not --->
        $('#clientIp').focusout(function () {
            var clientIpData = $(this).val();
            if (clientIpData != '') {
                var url = 'view/ajax_action/add_ajax_data.php';
                var postData = {check: clientIpData}
                $.post(url, postData, function (data) {
                    $('#checkavailablityclientIp').html(data);
                });
            } else {
                $('#checkavailablityclientIp').html('');
            }
        });

        // while package change autometic bill and speed will be updated
        $('select#packageList').on('change', function () {
            var package = $('select#packageList option:selected').val();
            var speed = $('select#packageList option:selected').data('speed');
            var bill = $('select#packageList option:selected').data('bill');

            $("div#billAmount input[name='taka']").val(bill);
            $("div#netSpeed input[name='mb']").val(speed);

        }); // on change


        $('#running_month_amount').on('change', 'input[name="running_month_due_have"]', function () {

            var dueAmountText = $(this).closest('.row').find('input[name="running_month_due_amount"]');
            dueAmountText.val("");
            if (dueAmountText.is(':disabled')) {
                dueAmountText.prop('disabled', false);
            } else {
                dueAmountText.prop('disabled', true);
            }
        });

        $('#connection_charge_amount').on('change', 'input[name="connection_charge_due_have"]', function () {

            var dueAmountText = $(this).closest('.row').find('input[name="connection_charge_due_amount"]');
            dueAmountText.val("");
            if (dueAmountText.is(':disabled')) {
                dueAmountText.prop('disabled', false);
            } else {
                dueAmountText.prop('disabled', true);
            }
        });


        $('select[name="package"]').select2({
            placeholder: "Select Package",
            allowClear: true,
        });
        $('select[name="link_share[]"]').select2({
            placeholder: " Select share with link",
            multiple: true,
        });


        $('#datepicker').datepicker({
            autoclose: true,
            toggleActive: true,
            format: 'dd-mm-yyyy'
        });


    }); // document ready function
</script>
