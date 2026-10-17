      <?php
      /*$alldata = $obj->view_all_by_cond("tbl_agent", "int_mb='0.00' and ag_status='1'");
      foreach ($alldata as $value) {
        $int_mb = preg_replace('/[^.0-9]/', '', $value['mb']);
      //  $int_mb_final = 0;
        $str_fr_mb = preg_replace('/[^a-zA-Z]/', '', $value['mb']);
        if($str_fr_mb =='kbps' || $str_fr_mb =='kb' || $str_fr_mb =='k')
        {
            $int_mb = ($int_mb/1024);
        }
        $token=$value['ag_id'];
        if($int_mb < 1000){
        $up = $obj->Update_data("tbl_agent", ['int_mb' => $int_mb], "where ag_id='$token'");
        }
    }*/

    /*$alldata = $obj->view_all_by_cond("tbl_account", "acc_type='3'");
      foreach ($alldata as $value) {
        $aggid=$value['agent_id'];
        $token=$value['entry_by'];
        $up = $obj->Update_data("tbl_agent", ['billing_person_id' => $token], "where ag_id='$aggid'");
    }*/

        //   $rowCountin = $obj->Total_Count("tbl_agent", "ag_status='0'");
        //   $customer = $obj->Total_Count("tbl_agent", "ag_status='1'");
        //   $allAgentData = $obj->view_all_by_cond("tbl_agent", "ag_status='1' and pay_status='1' AND due_status='0'");

    //     $total_due_amount = 0;

    //   foreach ($allAgentData as $value) {
    //     $all_due = $bill->get_customer_dues(isset($value['ag_id']) ? $value['ag_id'] : NULL);
    //     $total_due_amount += $all_due;
    //      }
    //      $firsDayOfMonth = new DateTime('first day of this month');
    //      $dateform =$firsDayOfMonth->format('Y-m-d');
    //      $dateto = date('Y-m-d');

    // $accountData = $obj->view_all_by_cond("vw_account", "MONTH(entry_date)='" . date('m') . "' and YEAR(entry_date)='" . date('Y') . "' ORDER BY `vw_account`.`entry_date` ASC");
     
    //             $i = 0;
    //             $totalin = 0;
    //             $balance = 0;
    //             $debit_total = 0;
    //             $credit_total = 0;
    //             foreach ($accountData as $acount) {
    //                 extract($acount);
    //                 $i++;
    //                 $totalin += $acount['acc_amount'];

				// 	$cus_id = isset($acount['agent_id']) ? $acount['agent_id'] : 0;
    //                 if($cus_id != 0){
    //                     $agentDetails = $obj->details_by_cond("tbl_agent", "ag_id=$cus_id");
    //                     if($agentDetails){
    //                         $customerName = $agentDetails['ag_name'];
    //                         $customerIp = $agentDetails['ip'];
    //                         $pointerClass = 'pointer';
    //                     }else{
    //                         $customerName = '--';
    //                         $customerIp = '--';
    //                         $pointerClass = '';
    //                     }
    //                 }else{
    //                     $customerName = '--';
    //                     $customerIp = '--';
    //                     $pointerClass = '';
    //                 }


                  
    //                     $get_acc_type = isset($acount['acc_type']) ? $acount['acc_type'] : NULL;
    //                     $amount = isset($acount['acc_amount']) ? $acount['acc_amount'] : NULL;
    //                     $debit = 0;
    //                     $credit = 0;

    //                     if ($get_acc_type == 1) {
    //                         $debit = $amount;
    //                         $balance -= $debit;
    //                         $debit_total += $debit;
    //                     } else {
    //                         $credit = $amount;
    //                         $balance += $credit;
    //                         $credit_total += $credit;
    //                     }

    //             }
                
if($ty=='SA') {?>

      <!-- <div class="col-md-12" style=" background:#606060; min-height:40px; margin-top:20px; padding:8px 0px 0px 15px;
        font-size:16px; font-family:Lucida Sans Unicode; color:#FFFFFF; font-weight:bold;">
			<b>Dashboard</b>
		</div> -->
<div class="row">
  <div class="panel panel-default" style="margin-bottom: 10px;">
	<div class="panel-body" style="padding: 5px;">
       <div class="col-lg-16">
         
            <div class="col-lg-2" style="padding-right: 0px;
    padding-left: 4px;">
                <div class="card text-white bg-flat-color-2">
                    <div class="card-body pb-0 text-center">
                        <p class="text-light">Total Active Link</p>
                         <h4 class="mb-0">
                            <span class="count"><?php echo $obj->Total_Count("tbl_agent", "ag_status='1'"); ?></span>
                        </h4>
                        <div class="chart-wrapper px-0" style="height:70px;" height="70">
                            <i class="fa fa-user-plus" style="font-size: 50px;"></i>
                        </div>

                    </div>

                </div>
            </div>
            <div class="col-lg-2"style="padding-right: 0px;
    padding-left: 4px;">
                <div class="card text-white bg-flat-color-3">
                    <div class="card-body pb-0 text-center">
                        <p class="text-light"> Monthly Total Bill</p>
                         <h4 class="mb-0">
                            <span class="count"><?php  $tbillamount= $obj->get_sum_by_cond('taka','tbl_agent','ag_status =1'); echo isset($tbillamount)? $tbillamount: 0; ?></span>
                        </h4>
                        <div class="chart-wrapper px-0" style="height:70px;" height="70">
                            <i class="fa fa-money" style="font-size: 50px;"></i>
                        </div>

                    </div>

                </div>
            </div>
   
            <!--<div class="col-lg-2"style="padding-right: 0px;     padding-left: 4px;">-->
            <!--    <div class="card text-white bg-flat-color-4">-->
            <!--        <div class="card-body pb-0 text-center">-->
            <!--            <p class="text-light">Total Due Bill</p>-->
            <!--             <h4 class="mb-0">-->
            <!--                <span class="count"><?php //    $tdbillamount= $obj->get_sum_by_cond('total_bill','vw_bill_calc','total_bill > 0');   echo isset($tdbillamount)? $tdbillamount: 0; ?></span>-->
            <!--            </h4>-->
            <!--            <div class="chart-wrapper px-0" style="height:70px;" height="70">-->
            <!--                <i class="fa fa-money" style="font-size: 50px;"></i>-->
            <!--            </div>-->

            <!--        </div>-->

            <!--    </div>-->
            <!--</div>-->
            
            <div class="col-lg-2"style="padding-right: 0px;  padding-top: 1px;   padding-left: 4px;">
                <div class="card text-white" style="background-color:green">
                    <div class="card-body pb-0 text-center">
                        <p class="text-light">This Month Bill Collection</p>
                         <h4 class="mb-0">
                            <span class="count"><?php $taccamount= $obj->get_sum_by_cond('acc_amount','tbl_account',"agent_id > 0 AND  acc_type != 1 AND MONTH(entry_date)= Date('m')"); echo isset($taccamount)? $taccamount: 0;   ; ?></span>
                        </h4>
                        <div class="chart-wrapper px-0" style="height:70px;" height="70">
                            <i class="fa fa-money" style="font-size: 50px;"></i>
                        </div>

                    </div>

                </div>
            </div>
            
            <!-- <div class="col-lg-2"style="padding-right: 0px;
    padding-left: 4px;">
                <div class="card text-white bg-flat-color-5">
                    <div class="card-body pb-0 text-center" style="padding: 0 2px;">
                        <p class="text-light">Total Bandwidth Sell</p>
                         <h4 class="mb-0">
                            <span class=""><?php  $tbandwitd =$obj->get_total_bandwidth();  echo isset($tbandwitd)? $tbandwitd: 0; ?></span>
                        </h4>
                        <div class="chart-wrapper px-0" style="height:70px;" height="70">
                            <i class="fa fa-bolt" style="font-size: 50px;"></i>
                        </div>

                    </div>

                </div>
            </div> -->
            <div class="col-lg-2"style="padding-right: 0px;    padding-left: 4px;">
                <div class="card text-white bg-flat-color-6">
                    <div class="card-body pb-0 text-center">
                        <p class="text-light">Total Collection</p>
                         <h4 class="mb-0">
                            <span class="count"><?php $taccamount= $obj->get_sum_by_cond('acc_amount','tbl_account','agent_id > 0 AND  acc_type != 1'); echo isset($taccamount)? $taccamount: 0;   ; ?></span>
                        </h4>
                        <div class="chart-wrapper px-0" style="height:70px;" height="70">
                            <i class="fa fa-money" style="font-size: 50px;"></i>
                        </div>

                    </div>

                </div>
            </div>
            <div class="col-lg-2" style="padding-right: 0px;     padding-left: 4px;">
                <div class="card text-white bg-flat-color-1">
                    <div class="card-body pb-0 text-center">
                        <p class="text-light">Total Inactive Customer</p>
                         <h4 class="mb-0">
                            <span class="count"><?php  echo $obj->Total_Count("tbl_agent", "ag_status='0'"); ?></span>
                        </h4>
                        <div class="chart-wrapper px-0" style="height:70px;" height="70">
                            <i class="fa fa-user-circle" style="font-size: 50px;"></i>
                        </div>

                    </div>

                </div>
            </div>
            
            
        </div>
      </div>
  </div>
</div
        <br>
        
        <div class="row">
			<div class="col-md-6">
				<h4 class="text-center">Collection of Bill for Previous 12 month</h4>
				<canvas id="accountsChart"></canvas>
			</div>
			<div class="col-md-6">
				<h4 class="text-center">All Customer Of Last 12 Month</h4>
				<canvas id="cusChart"></canvas>
			</div>
		</div>
        
        
		<!--<div class="row">-->
		<!--	<div class="col-md-10 col-md-offset-1">-->
		<!--		<h4 class="text-center">Collection of Bill for Previous 12 month</h4>-->
		<!--		<canvas id="accountsChart"></canvas>-->
		<!--	</div>-->
		<!--</div>-->
		<!-- <div class="row">
			<div class="col-md-3 col-md-offset-2">
				<h4 class="text-center padding_10_px">Total Billing Summary</h4>
				<div class="preData" style="position:absolute; top:50%; left:0;">
					<div class="col-md-12 text-center">
						<h2 class="border-teal-800 btn-block"><span class="glyphicon
						glyphicon-transfer text-slate-800"></span></h2>
					</div>
					<div class ="col-md-12 text-center">
						<p class="text-muted">Please Wait</p>
						<h4 class="text-slate-800">Your Billing Data is Calculating ...</h4>
					</div>
				</div>
				<canvas id="billInfoChart"></canvas>
			</div>
			<div class="col-md-3 col-md-offset-2">
				<h4 class="text-center padding_10_px">Expense & Income of <?php //echo date("F"); ?></h4>
				<canvas id="expenseIncome"></canvas>
			</div>
		</div> -->
		<!--<div class="row">-->
		<!--	<div class="col-md-12 col-md-offset-0">-->
		<!--		<h4 class="text-center">All Customer Of Last 12 Month</h4>-->
		<!--		<canvas id="cusChart"></canvas>-->
		<!--	</div>-->
		<!--</div>-->
        <!--<div class="row">-->
        <!--<div class="col-md-12 col-md-offset-0">-->
        <!--        <h4 class="text-center">Customer By Bandwidth Range</h4>-->
        <!--        <canvas id="BandWidthChart"></canvas>-->
        <!--    </div>-->
        <!--</div>-->
		<!--<div class="row">-->
		<!--<div class="col-md-12 col-md-offset-0">-->
		<!--		<h4 class="text-center">Customer By Price Range</h4>-->
		<!--		<canvas id="RangeChart"></canvas>-->
		<!--	</div>-->
		<!--</div>-->
		<!-- <div class="row">
			<div class="col-md-5 col-md-offset-3">
				<h4 class="text-center">Total,Paid & Non-Paid Customer of <?php // echo date("F"); ?></h4>
				<canvas id="paidnonpaid"></canvas>
			</div>
		</div> -->
	</div>
	    <script type="text/javascript" src="asset/js/jquery.min.js"></script>
	    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.4.0/chart.min.js" integrity="sha512-JxJpoAvmomz0MbIgw9mx+zZJLEvC6hIgQ6NcpFhVmbK1Uh5WynnRTTSGv3BTZMNBpPbocmdSJfldgV5lVnPtIw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	    <!--<script type="text/javascript" src="asset/js/Chart.bundle.min.js"></script>-->
		<!--<script type="text/javascript" src="asset/js/Chart.js"></script>-->
		<!--<script type="text/javascript" src="asset/js/widgets.js"></script>-->
		<script type="text/javascript" src="asset/js/CustomChartData.js"></script>
		
	
<?php } ?>
			