<script>
    function startTime() {
        var today = new Date();
        var h = today.getHours();
        var m = today.getMinutes();
        var s = today.getSeconds();
        m = checkTime(m);
        s = checkTime(s);
        document.getElementById('clock').innerHTML =
            h + ":" + m + ":" + s;
        var t = setTimeout(startTime, 500);
    }
    function checkTime(i) {
        if (i < 10) {
            i = "0" + i
        }
        ;  // add zero in front of numbers < 10
        return i;
    }
</script>
<link rel="stylesheet" type="text/css" href="asset/css/main.css">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<div id="header_area" class="col-md-12" style="padding:5px 5px 5px 5px;">

    <div class="col-md-5">
        <div style="float:left;">
            <img title="Visit Site" alt="Our logo" src="./logo.png" width="100" height="60">
           <!-- <h3 style="float:right;color:white;margin-left:10px">THE SKY TRADERS (PVT) LTD</h3>--!>
        </div>
    </div>
<?php if($obj->hasPermission($ty, 'show_total_bill')){ ?>
    <!--<div class="col-md-3 text-center" style="margin-top: 15px;">-->
 
    <!--    <small class="padding_3_10_px label btn-primary">-->
           
            <!--Total Bill Generated : <span class="badge"><?php // $tbillamount= $obj->get_sum_by_cond('taka','tbl_agent','ag_status =1'); echo isset($tbillamount)? $tbillamount: 0;?> </span>&nbsp; Taka-->
    <!--    </small>-->
      
    <!--</div>-->
<?php } ?>
<?php if($obj->hasPermission($ty, 'total_bandwidth')){ ?>
    <!--<div class="col-md-2 text-center" style="margin-top: 15px;">-->
 
    <!--    <small class="padding_3_10_px label btn-primary">-->
           
            <!--Total Bandwidth Sell : <span class="badge"></span> <?php // $tbandwitd =$obj->get_total_bandwidth();  echo isset($tbandwitd)? $tbandwitd: 0; ?> </span>&nbsp; Mbps-->
    <!--    </small>-->
      
    <!--</div>-->
    <?php } ?>
    <!-- <div class="col-md-2 text-center" style="color:#CCCCCC; font-size:15px;
				 font-family:Lucida Sans Unicode;">
        <h2 style="margin-top: 10px;" id="clock"></h2>
    </div> -->
  <?php if($obj->hasPermission($ty, 'show_due_bill')){ ?>
    <!--<div class="col-md-3 text-center" style="margin-top: 15px;">-->
        <!--<small class="padding_3_10_px label btn-danger">   Total Due Bill : <span class="badge"><?php // $tdbillamount= $obj->get_sum_by_cond('total_bill','vw_bill_calc','total_bill > 0'); echo isset($tdbillamount)? $tdbillamount: 0; ?>  </span>&nbsp; Taka-->
        <!--</small>-->
        
    <!--</div>-->
   <?php } ?>

    <div class="col-md-2 pull-right" style="margin-top: 12px">
        <a id="logout"
           style="background:url(asset/img/logout.png) left center no-repeat; font-size:12px; padding-left:20px; color:#FFFFFF;"
           href="include/logout.php"><b>Logout from <?php echo $_SESSION['FullName']; ?></b></a>
    </div>
</div>


