<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

$token = isset($_GET['token']) ? $_GET['token'] : NULL;
$agent = $obj->details_by_cond("tbl_agent", "ag_id='$token'");
$countRm = $obj->raw_sql("select remarks from tbl_remarks where `ag_id`='$token'");






// var_dump($connectMKS );
// exit;





$previous_due = $obj->details_by_cond("tbl_previous_due", "tbl_agent_id='$token'");

$runningMonthConnectinChargeDue = $obj->details_by_cond("tbl_due_opening_amount_and_con_charge", "tbl_agent_id='$token'");

if ($ty == 'SA' || $obj->userWorkPermission($acc,'edit')) {

    if (isset($_POST['update'])) {
        extract($_POST);
        // $shareLink = implode(',',$link_share);
        $form_data = array(
            'company_id' => $company_name,
            'comment' => $remarks,
            'map_link' => str_replace("'", '', $map_link),
            'link_name' => str_replace("'", '', $link_name),
            'actual_link' => str_replace("'", '', $actual_link),
            'link_address' => str_replace("'", '', $link_address),
            'district' => str_replace("'", '', $district),
            'customar_latitude' => str_replace("'", "", $cus_lat),
            // 'customar_longitude' => str_replace("'", "", $customar_longitude),
            'bts_lat_long' => str_replace("'", "", $bts_lat_long),
            'total_distant' => str_replace("'", "", $total_distance),
            'share_distance' => str_replace("'", "", $share_distance),
            'actual_distance' => str_replace("'", "", $actual_distance),
            'share_link' => str_replace("'", "", $link_share),
            'taka' => $taka,
            'zone' => $zone,
            'sub_zone_name' => str_replace("'", '', $sub_zone),
            'ag_status' => $status,
            'billing_person_id' => $billingperson,
            'connection_date' => date('Y-m-d', strtotime($connection_date)),
            'update_by' => $userid,
            'bill_date' => $bill_date
        );
 /* Agent Activity*/   
      if($form_data['ag_status'] == 0)
      {
        $preAgentActData = $obj->details_by_cond('tbl_agent_activity', "agent_id = $token");
        if ( ($preAgentActData['agent_id'] != $token))
        {
         $form_data_inac=array(
             'inactive_date' => date('Y-m-d', strtotime('first day of this month')),
         );
         $upin = $obj->Update_data("`tbl_agent`", $form_data_inac, "`tbl_agent`.`ag_id`=$token");
         $form_data_ag_act=array(
            'agent_id' => $token,
            'inactive_date' => date('Y-m-d', strtotime('first day of this month')),
            'previous_due' => isset($previous_due['previous_due_amount']) ? $previous_due['previous_due_amount'] : 0,
            'updated_by' => $userid,
            'created_at' => date('Y-m-d', strtotime('first day of this month')),
            'last_update' => date('Y-m-d', strtotime('first day of this month'))
        );
        $activity_add = $obj->Reg_user_cond("tbl_agent_activity", $form_data_ag_act, " ");
        }
        else
        {
            $form_data_ag_act=array(
            'agent_id' => $token,
            'inactive_date' => date('Y-m-d', strtotime('first day of this month')),
            'previous_due' => isset($previous_due['previous_due_amount']) ? $previous_due['previous_due_amount'] : 0,
            'updated_by' => $userid,
            'last_update' => date('Y-m-d', strtotime('first day of this month'))
          );
          $updateActivity = $obj->Update_data("`tbl_agent_activity`", $form_data_ag_act, "`tbl_agent_activity`.`agent_id`=$token");
          $form_data_inac=array(
             'inactive_date' => date('Y-m-d', strtotime('first day of this month')),
         );
         $upin = $obj->Update_data("`tbl_agent`", $form_data_inac, "`tbl_agent`.`ag_id`=$token");
        }
      }
     if(($form_data['ag_status'] == 1) and ($status_check == 0))
      {
        $form_data_ag_act_date=array(
            'updated_by' => $userid,
            'last_update' => date('Y-m-d', strtotime('first day of this month')),
            'active_date' => date('Y-m-d', strtotime('first day of this month'))
        );
        $updateActivityDate = $obj->Update_data("`tbl_agent_activity`", $form_data_ag_act_date, "`tbl_agent_activity`.`agent_id`=$token");

        $preAgentActData = $obj->details_by_cond('tbl_agent_activity', "agent_id = $token");
        $inactive_month = 0;
        $start = strtotime($preAgentActData['active_date']);
        $end = strtotime($preAgentActData['inactive_date']);
        while (($end = strtotime('+1 MONTH', $end)) <= $start)
        {
            $inactive_month++;
        }
        $ag_details = $obj->details_by_cond("tbl_agent", "ag_id='$token'");
        $inactive_bill = (($inactive_month * $ag_details['taka']) + $preAgentActData['inactive_ammount']);
        $form_data_ag_act_amm=array(
        'inactive_ammount' => $inactive_bill
        );
        $updateActivity = $obj->Update_data("`tbl_agent_activity`", $form_data_ag_act_amm, "`tbl_agent_activity`.`agent_id`=$token");
      }
      /*End Agent Activity*/

      $form_data['mikrotik_disconnect'] = isset($mikrtk_disconnect_date) ? $mikrtk_disconnect_date : 0;
        
/*Changes Bill Collection*/
        $preAgentData = $obj->details_by_cond('tbl_agent', "ag_id = $token");

        // if ( ($preAgentData['taka'] != $taka)) {

        //     if($preAgentData['entry_date'] != date('Y-m-d', strtotime('first day of this month')) && $agent['pay_status'] == 0)
        //     {

        //          $dueForThisUser = $bill->get_customer_only_total_dues($token, $taka);
        //          if($taka > $preAgentData['taka'])
        //          {
        //             $dueForThisUser = $dueForThisUser - ($taka - $preAgentData['taka']);
        //          }
        //          if($taka < $preAgentData['taka'])
        //          {
        //             $dueForThisUser = $dueForThisUser + ($preAgentData['taka'] - $taka);
                    
        //          }
        //         $billAmountChangeData = $obj->details_by_cond('tbl_bill_amount_change', 'agent_id = ' . $token . '');

        //         $form_data_for_tbl_bill = array(
        //             'bill_amount' => $taka,
        //             'agent_id' => $token,
        //             'previous_bill_amount' => $preAgentData['taka'],
        //             'dueTillEdit' => $dueForThisUser,
        //             'created_at' => date('Y-m-d', strtotime('first day of last month')),
        //         );

        //         $existTblBillAmountChangeSameDate = $obj->Total_Count('tbl_bill_amount_change', "agent_id = '$token' AND created_at = '" . date('Y-m-d', strtotime('first day of last month')) . "'");

        //         if ($existTblBillAmountChangeSameDate == 0) {

        //             $bill_amount_change = $obj->Reg_user_cond("tbl_bill_amount_change", $form_data_for_tbl_bill, " ");
        //         } else {
        //             $form_data_for_update = array(
        //                 'bill_amount' => $taka,
        //                 'dueTillEdit' => $dueForThisUser,
        //             );

        //             $bill_amount_update = $obj->Update_data("tbl_bill_amount_change", $form_data_for_update, "WHERE agent_id = '$token' AND created_at = '" . date('Y-m-d', strtotime('first day of last month')) . "'");
        //         }

        //     }

        //     if($preAgentData['entry_date'] != date('Y-m-d', strtotime('first day of this month')) && $agent['pay_status'] != 0){

        //         $dueForThisUser = $bill->get_customer_only_total_dues($token, $taka);

        //         $billAmountChangeData = $obj->details_by_cond('tbl_bill_amount_change', 'agent_id = ' . $token . '');

        //         $form_data_for_tbl_bill = array(
        //             'bill_amount' => $taka,
        //             'agent_id' => $token,
        //             'previous_bill_amount' => $preAgentData['taka'],
        //             'dueTillEdit' => $dueForThisUser,
        //             'created_at' => date('Y-m-d', strtotime('first day of last month')),
        //         );

        //         $existTblBillAmountChangeSameDate = $obj->Total_Count('tbl_bill_amount_change', "agent_id = '$token' AND created_at = '" . date('Y-m-d', strtotime('first day of last month')) . "'");

        //         if ($existTblBillAmountChangeSameDate == 0) {

        //             $bill_amount_change = $obj->Reg_user_cond("tbl_bill_amount_change", $form_data_for_tbl_bill, " ");
        //         } else {
        //             $form_data_for_update = array(
        //                 'bill_amount' => $taka,
        //                 'dueTillEdit' => $dueForThisUser,
        //             );

        //             $bill_amount_update = $obj->Update_data("tbl_bill_amount_change", $form_data_for_update, "WHERE agent_id = '$token' AND created_at = '" . date('Y-m-d', strtotime('first day of last month')) . "'");
        //         }
        //     }
        // }
        /*End Changes Bill Collection*/

        // if previous due is more then zero then client already have previous due so update it
        // if not, check its not zero if so leave it.
        // if input not zero then create previous due entry in database table.

        

        // handle client had Connection Charge Due and Running month due

      /* $form_data_tbl_due = array(
            'connection_charge_due' => $connection_charge_due_have,
            'running_month_due' => $running_month_due_have,
            'entry_by' => $userid,
        );*/

        

        $updateAction = $obj->Update_data("`tbl_agent`", $form_data, "`tbl_agent`.`ag_id`=$token");


        if ($updateAction) {
            

                if ($obj->column_by_cond("taka","total_calc", "ag_id='$token'") > 0) {
                    $obj->Update_data("tbl_agent", ['pay_status' => 1,'due_status' => 0,'bill_status' => 0], "where ag_id='$token'");
                } else {
                     $obj->Update_data("tbl_agent", ['pay_status' => 0,'due_status' => 1,'bill_status' => 1], "where ag_id='$token'");
                }


            
            ?>
            <script>
                var notification = 'Update Successfull';
                window.location = "?q=edit_agent&token=<?php echo $token ?>&notify="+notification;
            </script>
            <?php
        } else {

            $notification = 'Update Failed';
        }
    }

//end of update
    if (isset($_POST['packageAddSubmit'])) {

        $package_form_data = array(
            'package_name' => $_POST['packageName'],
            'net_speed' => $_POST['packageNetSpeed'],
            'bill_amount' => $_POST['packageBillAmount'],
            'created_by' => $userid
        );
        $addPackage = $obj->Reg_user_cond("tbl_package", $package_form_data, " ");
        if ($addPackage) {
            ?>
            <script> window.location = "?q=add_agent"; </script>  <?php

        } else {
            echo $notification = 'Package Insert Failed';
        }
    }

    if (isset($_POST['zoneAddSubmit'])) {

        $zone_form_data = array(
            'zone_name' => $_POST['zoneName'],
            'created_by' => $userid
        );
        $addZone = $obj->Reg_user_cond("tbl_zone", $zone_form_data, " ");
        if ($addZone) {

            ?>
            <script>  window.location = "?q=add_agent";  </script>  <?php

        } else {
            echo $notification = 'Zone Insert Failed';
        }
    }


    ?>
    <!--<h1> Don't Update Any User.. Developer is Working On This page.Thank you</h1>-->
    


    <div class="col-md-12"
         style=" margin-top:5px; margin-bottom: 5px; font-size:14px;  color:red; font-weight:bold; text-align: center;">
        <b><?php $obj->notificationShowRedirect(); if(isset($_GET['notify'])){echo $_GET['notify'];}echo isset($notification) ? $notification : NULL; ?></b>
    </div>
    
    <div class="row">
        <div class="col-md-12 bg-teal-800" style="margin-top:20px; margin-bottom: 15px;">
            <h4><strong>Edit Customer : <?php echo $agent['ag_name'] ? $agent['ag_name'] : NULL; ?></strong></h4>
        </div>
    </div>
    <div class="row" style="padding:10px; font-size: 12px;">
        <form role="form" enctype="multipart/form-data" method="post">
            <div class="row">
                <div class="col-md-10 col-md-offset-1">

                    
                </div>
            </div>
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="col-md-6">


                    <div class="form-group">
                            <label>Link Name</label>
                            <input value="<?php echo $agent['link_name'] ? $agent['link_name'] : NULL; ?>" type="text"
                                   name="link_name" class="form-control">
                                   <input value="<?php echo $agent['ag_status'] ? $agent['ag_status'] : NULL; ?>" type="hidden"
                                   name="status_check" class="form-control">
								    
                        </div>
                        <div class="form-group">
                            <label>Link Address</label>
                            <input value="<?php echo $agent['link_address'] ? $agent['link_address'] : NULL; ?>" type="text"
                                   name="link_address" class="form-control">
                                   
								    
                        </div>
                        
                        <div class="form-group">
                            <label>Total Link (KMZ)</label>
                            <input value="<?php echo $agent['map_link'] ? $agent['map_link'] : NULL; ?>" type="text"
                                   name="map_link" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label>Actual Link(KMZ)</label>
                            <textarea class="form-control" name="actual_link" id="actual_link" rows="4"><?php echo $agent['actual_link'] ? $agent['actual_link'] : NULL; ?></textarea>
                        </div>
                        
                    <div class="form-group">
                    <label>Company Name*</label>
                    
                    <select class="form-control" name="company_name" required="required">
                        <option value=""></option>
                        <?php foreach ($obj->view_all('tbl_company') as $singleCompany) { ?>
                            <option
                            
                                    value="<?php echo $singleCompany['company_id'] ?> " <?php  echo ($agent['company_id'] == $singleCompany['company_id']) ? 'selected' : ''; ?>><?php echo $singleCompany['company_name'] ? $singleCompany['company_name'] : NULL; ?></option>
                        <?php }// foreach ?>
                    </select>
                    
                </div>
                        
                        
                        <div class="form-group">
                            <label>District</label>
                            <select class="form-control" name="district">
                                <option value="<?php echo $agent['district'] ?>"><?php echo $agent['district'] ?></option>
                                <?php foreach ($obj->view_all('districts') as $singleZone) { ?>
                                    <option
                                            value="<?php echo $singleZone['name'] ?>"><?php echo $singleZone['name'] ?></option>
                                <?php }// foreach ?>
                            </select>
                    
                        </div>
                        <div class="form-group">
                    <label>Customer Latitude/Longitude</label>
                    <input   type="text" name="cus_lat" value="<?php echo $agent['customar_latitude'] ? $agent['customar_latitude'] : NULL; ?>" class="form-control">
                </div>
                <!--<div class="form-group">-->
                <!--    <label>Customer Longitude</label>-->
                <!--    <input   type="text" name="customar_longitude" value="<?php echo $agent['customar_longitude'] ? $agent['customar_longitude'] : NULL; ?>" class="form-control">-->
                <!--</div>-->
                <div class="form-group">
                    <label>BTS Latitude/longitude</label>
                    <input   type="text" name="bts_lat_long" value="<?php echo $agent['bts_lat_long'] ? $agent['bts_lat_long'] : NULL; ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label>Total Distance(OTDR)</label>
                    <input   type="text" name="total_distance" value="<?php echo $agent['total_distant'] ? $agent['total_distant'] : NULL; ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label>Share Distance</label>
                    <input   type="text" name="share_distance" value="<?php echo $agent['share_distance'] ? $agent['share_distance'] : NULL; ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label>Billing Length</label>
                    <input   type="text" name="actual_distance" value="<?php echo $agent['actual_distance'] ? $agent['actual_distance'] : NULL; ?>" class="form-control">
                </div>

                

            </div>

            <div class="col-md-6">
                        
                        
          
                            <!--<div class="form-group">-->
                            <!--        <label>Share With Link</label>-->
                                    <?php 
                                        // $linkShare = array_map('trim', explode(",", $agent['share_link']));
                                    ?>
                                    
                                    <!--<select class="form-control" name="link_share[]" multiple>-->
                                    <!--    <option value=""></option>-->
                                    <!--    <?php foreach ($obj->view_all('tbl_agent') as $singleUser) { ?>-->
                                    <!--        <option value="<?php echo trim($singleUser['link_name']); ?>" -->
                                    <!--            <?php echo in_array(trim($singleUser['link_name']), $linkShare) ? 'selected' : '' ?>>-->
                                    <!--            <?php echo $singleUser['link_name']; ?>-->
                                    <!--        </option>-->
                                    <!--    <?php } ?>-->
                                    <!--</select>-->
                            <!--</div>-->
                            
                            <div class="form-group">
                                <label>Impact Link</label>
                                <textarea class="form-control" name="link_share"  id="remarks" rows="4"><?php echo $agent['share_link'] ? $agent['share_link'] : NULL; ?></textarea>
                            </div>
                            
                            <div class="form-group">
                            <label>Billing Date</label>
                            <input   value="<?php echo $agent['bill_date'] ? $agent['bill_date'] : NULL; ?>" type="text" name="bill_date" class="form-control" required autocomplete="off">
                            </div>
                        
                        

                        <div class="form-group" style="margin-bottom: 22px; ">
                            <label class="pull-left">Select Zone</label>
                            <button type="button" style="padding-top: 0px;padding-bottom : 0px;"
                                    class="btn btn-default border-white btn-sm pull-right" data-toggle="modal"
                                    data-target="#addZoneModal">
                                <span class="glyphicon glyphicon-plus"></span> Add Zone
                            </button>
                           
                            <select class="form-control" name="zone" required="required" >
                                <option value='' >Select Zone</option>
                                <?php foreach ($obj->view_all('tbl_zone') as $singleZone) { ?>
                                    <option value="<?php echo $singleZone['zone_id'] ?>" <?php  echo ($agent['zone'] == $singleZone['zone_id']) ? 'selected' : ''; ?> ><?php  echo $singleZone['zone_name'] ?></option>
                                <?php }// foreach ?>
                            </select>
                            
                        </div>

                    <div class="form-group">
                    <label class="pull-left">Sub Zone/Area*</label>
                    <a type="button" style="padding-top: 0px;padding-bottom : 0px;"
                            class="btn btn-default border-white btn-sm pull-right" href="?q=view_all_sub_zone">
                        <span class="glyphicon glyphicon-plus"></span> Add Sub Zone
                    </a>
                    <select class="form-control" name="sub_zone">
                        <option value=""> Select Sub Zone /Area</option>
                        <?php foreach ($obj->view_all('tbl_sub_zone') as $singleSubZone) { ?>
                            <option
                                    value="<?php echo $singleSubZone['sub_zone_name'] ?>" <?php  echo (trim($agent['sub_zone_name']) == trim($singleSubZone['sub_zone_name'])) ? 'selected' : ''; ?>><?php echo $singleSubZone['sub_zone_name'] ?></option>
                        <?php }// foreach ?>
                    </select>
                </div>
                        

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Regular Bill</label>

                                    <div class="input-group" id ="billAmount">
                                        <input style="background: #f9f9f9 none repeat scroll 0 0" value="<?php echo isset($agent['taka']) ? $agent['taka'] : NULL; ?>" name="taka"     class="form-control">
                                        <span class="input-group-addon">TK</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                <label>One Time Installation</label>
                                    <div class="input-group">
                                        <input value="<?php echo isset($agent['connect_charge']) ? $agent['connect_charge'] : null; ?>" type="number" name="connect_charge"   class="form-control">
                                        <span class="input-group-addon">TK</span>
                                    </div>

                                </div>
                            </div>
                        </div>


                        <!-- <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Running Month Amount Due</label>
                                    <div class="input-group">
                                        <input value="<?php echo isset($runningMonthConnectinChargeDue['running_month_due']) ? $runningMonthConnectinChargeDue['running_month_due'] : null; ?>" type="number" name="running_month_due_have"     class="form-control">
                                        <span class="input-group-addon">TK</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Connection Charge Amount Due</label>
                                    <div class="input-group">
                                        <input value="<?php echo isset($runningMonthConnectinChargeDue['connection_charge_due']) ? $runningMonthConnectinChargeDue['connection_charge_due'] : null; ?>" type="number" name="connection_charge_due_have"   class="form-control">
                                        <span class="input-group-addon">TK</span>
                                    </div>
                                </div>
                            </div>
                        </div> -->

                        <div class="form-group">
                            <label>Connection Date</label>
                            <input type="text" name="connection_date" class="form-control"
                                   value="<?php echo $agent['connection_date'] ? date('d-m-Y', strtotime($agent['connection_date'])) : NULL; ?>"
                                  >
                        </div>
             
                       <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" required="required" name="status" id="status">
                                <option <?php if ($agent['ag_status'] == '1') echo 'selected'; ?> value="1">Active
                                </option>
                                <option <?php if ($agent['ag_status'] == '0') echo 'selected' ?> value="0">Inactive
                                </option>
                            </select>
                        </div>
                 <div class="form-group">
                    <label class="pull-left">Select Billing Person</label>
                    <select class="form-control" name="billingperson" required="required">
                        <option>Select</option>
                        <?php foreach ($obj->view_all('_createuser') as $singleUser) { ?>
                            <option
                                    value="<?php echo $singleUser['UserId'] ?>"<?php echo ($agent['billing_person_id'] == $singleUser['UserId']) ? "selected" : ""; ?>><?php echo $singleUser['FullName'] ?></option>
                        <?php }// foreach ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Remarks/Comment</label>
                    <textarea class="form-control" name="remarks"  rows="2"><?php echo $agent['comment']; ?></textarea>
                </div>
                
                    </div>
                </div>
            </div>
            <div class="row" style="text-align: center; padding: 5px 0px 15px 25px; font-size: 12px;">
                <button type="submit" class="btn btn-success" name="update">Update</button>
            </div>
        </form>
    </div>
    <hr>


    <!-- Modal Start-->
    <div class="modal fade" id="addZoneModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title text-center">Insert New Zone</h4>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <div class="row" style="padding:0 10px;">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="row">
                                        <label>Zone Name</label>
                                        <input type="text" name="zoneName" class="form-control"
                                               placeholder="Provide Zone Name">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-default" name="zoneAddSubmit"> Insert Zone
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    
    <!--//Trnasfer Modal -->
    <div class="modal fade" id="transferModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
            <?php if($userCurrentMikrotik['mikrotikConnect']){  if(!empty($userCMkDetails) && isset($userCMkDetails['name'])) { ?>    
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title text-center">User Transfer</h4>
                    <h3 >Mikro Tik : <span style='font-weight: 900;'><?php echo $userCurrentMikrotik['online']; ?> </span> TO <span id="toMk" style=' font-weight: 900;' > </span> </h3>
                </div>
                <form method="POST" id="transferModalForm" >
                    <div class="modal-body">
                        <div class="row">
                            
                            <input type="hidden" name="newmkid" class="form-control" >
                            <input type="hidden" value="<?php echo $userCurrentMikrotik['mik_id']; ?>" name="oldmkid" class="form-control" >
                            <input type="hidden" value="<?php echo $token ?>" name="customerId" class="form-control" >
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Customer IP/Secret Name </label> <small><?php echo $userCMkDetails['name']; ?></small>
                                    <input type="text"  name="usecret" id="TransferclientIp" data-usersecret="<?php echo $userCMkDetails['name']; ?>" class="form-control" placeholder="User IP/Secret Name" required="required"  onkeypress="return noSpace(event)"  >
                                    <input type="hidden" value="<?php echo $userCMkDetails['name']; ?>" name="usecretold" class="form-control" >
                                    <div  class="text-center" id="checkavailablityClientIdsoft"> </div> 
                                    <div  class="text-center" id="checkavailablityClientIdmk"> </div> 
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Customer Password </label> <small><?php echo $userCMkDetails['password']; ?></small>
                                    <input type="text"  name="upassword" class="form-control" placeholder="Password" onkeypress="return noSpace(event)">
                                    <input type="hidden" value="<?php echo $userCMkDetails['password']; ?>" name="upasswordold" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Customer Profile </label> <small><?php echo $userCMkDetails['profile']; ?></small>
                                    <div id="uprofile"></div>
                                    <!--<input type="text" value="<?php // echo $userCMkDetails['profile']; ?>" name="uprofile" class="form-control" placeholder="Profile">-->
                                     <input type="hidden" value="<?php echo $userCMkDetails['profile']; ?>" name="uprofileold" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-6" hidden>
                                <div class="form-group"> 
                                    <label>Customer Service </label>
                                    <input type="text" readonly value="<?php echo $userCMkDetails['service']; ?>" name="uservice" class="form-control" placeholder="Service Type">
                                    <input type="hidden" value="<?php echo $userCMkDetails['service']; ?>" name="userviceold" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Customer Comments </label> <small><?php echo $userCMkDetails['comment']; ?></small>
                                    <textarea name="ucomment" class="form-control" row="5" onkeypress="return noSpace(event)" ><?php echo $userCMkDetails['comment']; ?></textarea>
                                   
                                    <input type="hidden" value="<?php echo $userCMkDetails['comment']; ?>" name="ucommentold" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group text-center">
                                    <button onclick="return confirmTransfer()"  type="submit" class="btn btn-default" name="transfareUser"> Transfare</button>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </form>
                <?php } } ?> 
            </div>
        </div>
    </div>
    <!--============== jquery part ===================-->
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
        $(document).ready(function () {

            $('select[name="package"]').select2({
                placeholder: "Select a Profile",
                allowClear: true,
            });
            
            $('select[name="link_share[]"]').select2({
            placeholder: " Select share with link",
            multiple: true,
        });

            $('input[name="connection_date"]').datepicker({
                autoclose: true,
                toggleActive: true,
                format: 'dd-mm-yyyy'
            });
            
            
         // look client Id is unique or not --->
        $('#clientIp').focusout(function () {
            var clientIpData = $(this).val();
            var clientIpDataOLD = $(this).data('usersec');
            // console.log(clientIpDataOLD);
            //  console.log(clientIpData);
            if (clientIpData != '') {
                if(clientIpData != clientIpDataOLD){
                    var url = 'view/ajax_action/add_ajax_data.php';
                    var postData = {check: clientIpData}
                    $.post(url, postData, function (data) {
                        $('#checkavailablityClientId').html(data);
                    });
                }
                
            } else {
                $('#checkavailablityClientId').html('');
            }
            
              var cldmkid = `<?php echo $userCurrentMikrotik['mik_id']; ?>`;
            
             if (clientIpData != '') {
                  if(clientIpData != clientIpDataOLD){
                    var url = 'view/ajax_action/add_ajax_data.php';
                    var postData = {checkmsecret: clientIpData, checkmkid:cldmkid}
                    $.post(url, postData, function (data) {
                        $('#checkavailablityClientIdm').html(data);
                    });
                  }
            } else {
                $('#checkavailablityClientIdm').html('');
            }
            
        });   
        
        
 
            
            // while package change autometic bill and speed will be updated
        $('select#packageListd').on('change', function () {
            var package = $('select#packageListd option:selected').val();
            var speed = $('select#packageListd option:selected').data('speed');
            var bill = $('select#packageListd option:selected').data('bill');

            $("div#billAmount input[name='taka']").val(bill);
            $("div#netSpeed input[name='mb']").val(speed);

        }); // on change

            $('input[name="running_month_due_have"]').prop('disabled', true);
            $('input[name="connection_charge_due_have"]').prop('disabled', true);

        });
        
        
        function confirmTransfer() {

        return confirm('Customer will be Transfer,\n Are You sure? ');

        }
        //transfer 
        $('button.transferbtn').on('click', function () {
            var mkTransferid = $(this).data('mktransfer_id');
            var mkTransdareip = $(this).data('mktransdare_ip');
            

            $("#transferModalForm input[type='text']").val("");
            $("#toMk").text(mkTransdareip);
            $("#transferModalForm input[name='newmkid']").val(mkTransferid);
            $("#transferModalForm input[name='usecret']").val(`<?php echo $userCMkDetails['name']; ?>`);
            $("#transferModalForm input[name='upassword']").val(`<?php echo $userCMkDetails['password']; ?>`);
            // $("#transferModalForm input[name='uprofile']").val(`<?php echo $userCMkDetails['profile']; ?>`);
            $("#transferModalForm input[name='uservice']").val(`<?php echo $userCMkDetails['service']; ?>`);
            $("#transferModalForm textarea[name='ucomment']").val(`<?php echo $userCMkDetails['comment']; ?>`);
            
            var tclientIpData =`<?php echo $userCMkDetails['name']; ?>`;
           
        //   mikrotik check 
            if (tclientIpData != '') {
                    var url = 'view/ajax_action/add_ajax_data.php';
                    var postData = {checkmsecret: tclientIpData, checkmkid:mkTransferid}
                    $.post(url, postData, function (data) {
                        $('#checkavailablityClientIdmk').html(data);
                    });
                
            } else {
                $('#checkavailablityClientIdmk').html('');
            }
            
            // getprofile 
            if (mkTransferid != '') {
                    var url = 'view/ajax_action/add_ajax_data.php';
                    var postData = {profileGetmkid:mkTransferid}
                    $.post(url, postData, function (data) {
                        $('#transferModalForm #uprofile').html(data);
                    });
                
            } else {
                $('#transferModalForm #uprofile').html('');
            }
             
        });
        
        // TransferclientIp
        $('#TransferclientIp').focusout(function () {
            var tclientIpData = $(this).val();
            var tclientIpDataOLD = $(this).data('usersecret');
            
            if (tclientIpData != '') {
                if(tclientIpData != tclientIpDataOLD){
                    var url = 'view/ajax_action/add_ajax_data.php';
                    var postData = {check: tclientIpData}
                    $.post(url, postData, function (data) {
                        $('#checkavailablityClientIdsoft').html(data);
                    });
                }
                
            } else {
                $('#checkavailablityClientIdsoft').html('');
            }
            var newmkid = $("#transferModalForm input[name='newmkid']").val();
            
             if (tclientIpData != '') {
                    var url = 'view/ajax_action/add_ajax_data.php';
                    var postData = {checkmsecret: tclientIpData, checkmkid:newmkid}
                    $.post(url, postData, function (data) {
                        $('#checkavailablityClientIdmk').html(data);
                    });
                
            } else {
                $('#checkavailablityClientIdmk').html('');
            }
            
            
        });
        
        

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
    </script>

<?php } else {

    ?>
    <script> window.location = "?q=main_configuration"; </script> <?php

}