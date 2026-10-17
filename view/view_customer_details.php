<?php
$token = isset($_GET['token2'])? $_GET['token2'] :NULL;
$data = $obj->details_by_cond('vw_agent',"ag_id='$token'");
$data2 = $obj->details_by_cond('tbl_agent',"ag_id='$token'");
$remark = $obj->details_by_cond('tbl_agent',"ag_id='$token'");
$billperson = $data['billing_person_id'];
$data3=$obj->details_by_cond('_createuser',"UserId='$billperson'");
      extract($data);
      $allDuePayment = $obj->details_by_cond("vw_agent", "ag_id='$token' and ag_status='1' and pay_status='1' AND due_status='0'");
    //   $all_due = $bill->get_customer_dues(isset($_GET['token2']) ? $_GET['token2'] : NULL);
    $all_due = $obj->details_by_cond("tbl_agent", "ag_id='$token' and ag_status='1'");

        $previousDue = ( $all_due['taka'] - (isset($allDuePayment['taka']) ? $allDuePayment['taka'] : 0) );

?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> 

  <div class="row panel panel-success" style="margin-top:2%;">
            <div class="panel-heading lead">
                <div class="row">
                    <div class="col-lg-8 col-md-8"><i class="fa fa-users"></i> View Customer Details</div>
                    <div class="col-lg-4 col-md-4 text-right">
                    </div>
                </div>
            </div>
            <div class="panel-body">
                            
                
                                                          
                    <div class="row">
                        <div class="col-lg-12 col-md-12">

                            <div class="row">
                                <div class="col-lg-9 col-md-9">
                                    <div class="tab-content">
                                        <div id="Summery" class="tab-pane fade in active">

                                            <div class="table-responsive panel">
                                                <table class="table">
                                                    <tbody>
    
                                                            <tr>
                                                                <td class="text-success"><i class="fa fa-user"></i>  Name</td>
                                                                <td><?php echo $data['link_name'] ;?></td>
                                                            </tr>
                                                            
                                                            <tr>
                                                                <td class="text-success"><i class="fa fa-suitcase"></i> Office Address</td>
                                                                <td><?php echo $data['link_address']  ; ?></td>
                                                            </tr>
                                                           
                                                            <tr>
                                                                <td class="text-success"><i class="fa fa-phone"></i> Regular Phone Number </td>
                                                                <td> <?php echo $data2['regular_mobile']; ?></td>
                                                            </tr>
                                                          
                                                           
                                                           
                                                              <tr>
                                                                <td class="text-success"><i class="fa fa-usd"></i> Billing Amount</td>
                                                                <td>
                                                                 <?php echo isset($data['taka'])? $data['taka']:NULL; ?> Tk</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-success"><i class="fa fa-usd">Total Link(KMZ)</i></td>
                                                                <td>
                                                                 <?php echo isset($data['map_link'])? $data['map_link']:NULL  ?> </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-success"><i class="fa fa-usd">Actual Link(KMZ)</i></td>
                                                                <td>
                                                                 <?php echo isset($data['actual_link'])? $data['actual_link']:NULL  ?> </td>
                                                            </tr>
                                                            
                                                             <tr>
                                                                <td class="text-success"><i class="fa fa-usd">Customer Latitude/Longitude</i></td>
                                                                <td>
                                                                 <?php echo isset($data['customar_latitude'])? $data['customar_latitude']:NULL  ?> </td>
                                                            </tr>
                                                            
                                                            
                                                            <tr>
                                                                <td class="text-success"><i class="fa fa-usd">BTS Latitude/Longitude</i></td>
                                                                <td>
                                                                 <?php echo isset($data['bts_lat_long'])? $data['bts_lat_long']:NULL  ?> </td>
                                                            </tr>
                                                            
                                                            <tr>
                                                                <td class="text-success"><i class="fa fa-usd">Total Distance(OTDR)</i></td>
                                                                <td>
                                                                 <?php echo isset($data['total_distant'])? $data['total_distant']:NULL  ?> </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-success"><i class="fa fa-usd">Billing Length</i></td>
                                                                <td>
                                                                 <?php echo isset($data['actual_distance'])? $data['actual_distance']:NULL  ?> </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-success"><i class="fa fa-usd">Impact Link</i></td>
                                                                <td>
                                                                 <?php echo isset($data['share_link'])? $data['share_link']:NULL  ?> </td>
                                                            </tr>
                                                             <tr>
                                                                <td class="text-success"><i class="fa fa-usd">Remarks</i></td>
                                                                <td>
                                                                 <?php echo isset($data['comment'])? $data['comment']:NULL  ?> </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-success"><i class="fa fa-street-view"></i> Zone</td>
                                                                <td>
                                                                 <?php echo isset($data['zone_name'])? $data['zone_name']:NULL; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-success"><i class="fa fa-user-circle"></i> Billing Person</td>
                                                                <td>
                                                                 <?php echo isset($data3['FullName'])? $data3['FullName']:NULL; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-success"><i class="fa fa-calendar"></i> Connection Date</td>
                                                                <td>
                                                                 <?php echo isset($data['connection_date'])? $data['connection_date']:NULL; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-success"><i class="fa fa-play-circle"></i> Status</td>
                                                                <td>
                                                                 <?php 
                                                                 if($data['ag_status']==1){echo"Active";}else{echo"Inactive";}
                                                                  ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-success"><i class="fa fa-check-circle"></i> Payment For This Running Month</td>
                                                                <td>
                                                                 <?php if($data['pay_status']==0)
                                                                  {
                                                                    echo "ok";
                                                                  } else{echo"no";} ?></td>
                                                            </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div id="Address" class="tab-pane fade">
                                            <div class="table-responsive panel">
                                                <table class="table">
                                                    <tbody>
                      
                                                            <tr>
                                                                <td class="text-success"><i class="fa fa-home"></i> Address</td>
                                                                <td>
                                                                    <address>
                                                                        <strong>
        C-***, Amahiya </strong><br>
        Kharobar, ****** <br>
        Gorakhpur, Utter Pradesh, India<br>
                                                                    </address>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div id="Contact" class="tab-pane fade">
                                            <div class="table-responsive panel">
                                                <table class="table">
                                                    <tbody>
                        
                                                            <tr>
                                                                <td class="text-success"><i class="fa fa-envelope-o"></i> Email ID</td>
                                                                <td><a href="mailto:****@pawanmall.net?subject=Email from &amp;body=Hello, Viddhyut Mall">****@pawanmall.net</a></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-success"><i class="glyphicon glyphicon-phone"></i> Mobile Number</td>
                                                                <td>88********</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-success"><i class="fa fa-flag"></i> Nationality</td>
                                                                <td>Indian</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-success"><i class="fa fa-user"></i> Father's Name</td>
                                                                <td>Ajay Mall</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-success"><i class="glyphicon glyphicon-phone"></i> Father's Mobile</td>
                                                                <td>+91 99********</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-success"><i class="fa fa-user"></i> Mother's Name</td>
                                                                <td>Hemlata Mall</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-success"><i class="glyphicon glyphicon-phone"></i> Mother's Mobile</td>
                                                                <td>+91 90********</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-success"><i class="fa fa-user"></i> Emergency Contact Person</td>
                                                                <td>Pawan Mall</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-success"><i class="glyphicon glyphicon-phone"></i> Emergency Contact Person's Mobile</td>
                                                                <td>+91 88********</td>
                                                            </tr>
                            
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div id="General" class="tab-pane fade">
                                            <div class="table-responsive panel">
                                                <table class="table">
                                                    <tbody>
                                                                <tr>
                                                                <td class="text-success"><i class="fa fa-university"></i> Last School</td>
                                                                <td>Pawan Mall's School</td>
                                                            </tr>                                        
                                                            <tr>
                                                                <td class="text-success"><i class="fa fa-calendar"></i> Date of Admission</td>
                                                                <td>March 4, 2009</td>
                                                            </tr>                                        
                                                            <tr>
                                                                <td class="text-success"><i class="fa fa-home"></i> Birth Place</td>
                                                                <td>Delhi</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-success"><i class="fa fa-calendar"></i> Academic Year</td>
                                                                <td>2015-2016</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-success"><i class="fa fa-medkit"></i> Medical Condition</td>
                                                                <td>Normal</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-success"><i class="fa fa-thumbs-up"></i> Active/Inactive</td>
                                                                <td>Student is Active</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-success"><i class="glyphicon glyphicon-time"></i> Last Editing</td>
                                                                <td>2015-08-20 09:41:56</td>
                                                            </tr>
                              
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                <!-- /.table-responsive -->
                
            </div>
        </div> 
