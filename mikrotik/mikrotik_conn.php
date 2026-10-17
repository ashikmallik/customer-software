<?php

require('model/Mikrotik.php');
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$ty = isset($_SESSION['UserType']) ? $_SESSION['UserType'] : NULL;

$notification = "";
$mikrotikConnect = false;
$mkDisconnected = [];

if (isset($_GET['mik_id'])){
    $mik_id = $_GET['mik_id'];
    $mikro = $obj->details_by_cond('mikrotik_user', "id =$mik_id");
    $mikrotik = new Mikrotik($mikro['mik_ip'], $mikro['mik_username'], $mikro['mik_password']);
    if ($mikrotik->checkConnection()) {
        $checkmikrotik = true;
        $mikrotikConnect = true;
        $_SESSION['microtik_conn'] = true;
        $mkDisconnected['online']= $mikro['mik_ip'];
    }
    else{
      $mkDisconnected[]= $mikro['mik_ip'];
    }
    
    $mikrotiks = $obj->view_all('mikrotik_user');
    
}else{


    $mkfind= $obj->view_all_by_cond('mikrotik_user', "entry_by =$userid order by id ASC");
    if(!empty($mkfind)){
        $mikrotiks =$mkfind;
    }else{
        $mikrotiks = $obj->view_all('mikrotik_user');
    }// end if
    
 
   
    $checkmikrotik =false;
    foreach($mikrotiks as $mikro){
        if($checkmikrotik == true){
            continue;
        }
        $mikrotik = new Mikrotik($mikro['mik_ip'], $mikro['mik_username'], $mikro['mik_password']);
        if ($mikrotik->checkConnection()) {
            $checkmikrotik = true;
            $mikrotikConnect = true;
            $_SESSION['microtik_conn'] = true;
            $mkDisconnected['online']= $mikro['mik_ip'];
            $mik_id = $mikro['id'];
        }
        else{
          $mkDisconnected[]= $mikro['mik_ip'];
        }
    }//end foreach


} //end if get

?>
<div class="row">
    <div class="col-md-12 bg-slate-800" style="margin-top:20px; margin-bottom: 15px;">
        <h4 id="mk_ip" data-mk_ip="<?php echo (isset($mkDisconnected['online'])) ? $mkDisconnected['online'] : '0.0.0.0' ?>" > <strong>Connected Mikrotik IP :<?php echo (isset($mkDisconnected['online'])) ? $mkDisconnected['online'] : '0.0.0.0' ?> </strong> </h4>
    </div>
    <div class="box-tools col-md-12">
        <div class="btn-group">
                <?php foreach ($mikrotiks as $mkip){ ?>
                <a href="?q=mikrotik_conn&mik_id=<?php echo $mkip['id'] ?>" class="btn btn-info" > <?php echo $mkip['mik_ip'] ?></a>
                <?php } ?>
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
</div>

<?php

// $allAgentData = $obj->view_selected_field_by_cond('tbl_agent', 'ip as ip', 'ag_id > 1400');
// $i = 0;
//foreach($allAgentData as $agent){
//    echo $agent['ip'];
//    echo ' - '.$i++."<br>";
//    $mikrotik->createNewSecret(trim($agent['ip']), 1234, 'pppoe', array_rand(['256 Kbps' => 1, '512 Kbps' => 2],1));
//}

if ($mikrotik->checkConnection()) {

    $interface = $mikrotik->interfaceStatus();
    $profile = $mikrotik->profileStatus();
    ?>
    
    <div class="row" id="interfaces">
        <h4 class="text-center"><b>Interface Other Information</b></h4>
        <div class="col-md-3">
            <div class="table-responsive" style="padding-top:10px;">
                <p class="text-center  bg-info"><strong>Ether List</strong></p>
                <table class="table table-striped no-margin">
                    <tbody>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Running</th>
                        <th>Status</th>
                    </tr>
                    <?php foreach ($interface as $singleInterface) {
                    if($singleInterface['type']=="ether"){
                    ?>
                        <tr>
                            <td><button class="btn" data-name="<?php echo $singleInterface['name'] ?>" ><?php echo $singleInterface['name'] ?> </button> </td>
                            <td><?php echo $singleInterface['type'] ?></td>
                            <td>
                                <?php if ($singleInterface['running'] == 'false') {
                                    echo '<span class="label label-danger"><span class="glyphicon glyphicon-remove"></span></span>';
                                } else {
                                    echo '<span class="label label-success"><span class="glyphicon glyphicon-random"></span></span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($singleInterface['disabled'] == 'false') {
                                    echo '<span class="label label-success">Enable</span>';
                                } else {
                                    echo '<span class="label label-danger">Disable</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } } ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="table-responsive" style="padding-top:10px;">
                <p class="text-center  bg-info"><strong>Bridge List</strong></p>
                <table class="table table-striped no-margin">
                    <tbody>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Running</th>
                        <th>Status</th>
                    </tr>
                    <?php foreach ($interface as $singleInterface) {
                    if($singleInterface['type']=="bridge"){
                    ?>
                        <tr>
                            <td><button class="btn" data-name="<?php echo $singleInterface['name'] ?>" ><?php echo $singleInterface['name'] ?> </button> </td>
                            <td><?php echo $singleInterface['type'] ?></td>
                            <td>
                                <?php if ($singleInterface['running'] == 'false') {
                                    echo '<span class="label label-danger"><span class="glyphicon glyphicon-remove"></span></span>';
                                } else {
                                    echo '<span class="label label-success"><span class="glyphicon glyphicon-random"></span></span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($singleInterface['disabled'] == 'false') {
                                    echo '<span class="label label-success">Enable</span>';
                                } else {
                                    echo '<span class="label label-danger">Disable</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } } ?>
                    </tbody>
                </table>
            </div>
        </div>
          
        <div class="col-md-3">
            <div class="table-responsive" style="padding-top:10px;">
                <p class="text-center  bg-info"><strong>Vlan List</strong></p>
                <table class="table table-striped no-margin">
                    <tbody>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Running</th>
                        <th>Status</th>
                    </tr>
                    <?php foreach ($interface as $singleInterface) {
                    if($singleInterface['type']=="vlan"){
                    ?>
                        <tr>
                             <td><button class="btn" data-name="<?php echo $singleInterface['name'] ?>" ><?php echo $singleInterface['name'] ?> </button> </td>
                            <td><?php echo $singleInterface['type'] ?></td>
                            <td>
                                <?php if ($singleInterface['running'] == 'false') {
                                    echo '<span class="label label-danger"><span class="glyphicon glyphicon-remove"></span></span>';
                                } else {
                                    echo '<span class="label label-success"><span class="glyphicon glyphicon-random"></span></span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($singleInterface['disabled'] == 'false') {
                                    echo '<span class="label label-success">Enable</span>';
                                } else {
                                    echo '<span class="label label-danger">Disable</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } } ?>
                    </tbody>
                </table>
            </div>
        </div>    
            <div class="col-md-3">
            <div class="table-responsive" style="padding-top:10px;">
                <p class="text-center  bg-info"><strong>Pppoe-out List</strong></p>
                <table class="table table-striped no-margin">
                    <tbody>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Running</th>
                        <th>Status</th>
                    </tr>
                    <?php foreach ($interface as $singleInterface) {
                    if($singleInterface['type']=="pppoe-out"){
                    ?>
                        <tr>
                             <td><button class="btn" data-name="<?php echo $singleInterface['name'] ?>" ><?php echo $singleInterface['name'] ?> </button> </td>
                            <td><?php echo $singleInterface['type'] ?></td>
                            <td>
                                <?php if ($singleInterface['running'] == 'false') {
                                    echo '<span class="label label-danger"><span class="glyphicon glyphicon-remove"></span></span>';
                                } else {
                                    echo '<span class="label label-success"><span class="glyphicon glyphicon-random"></span></span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($singleInterface['disabled'] == 'false') {
                                    echo '<span class="label label-success">Enable</span>';
                                } else {
                                    echo '<span class="label label-danger">Disable</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } } ?>
                    </tbody>
                </table>
            </div>
        </div>  
     </div>  
    
    <div id="microtikTraficGraph" class="row">
        
            <h3 id="title" class="text-center"></h3>
        <div class="col-md-5">
            <canvas id="rxtx" width="400" height="400"></canvas>
        </div>
        
        <div class="col-md-5">
            <canvas id="rxtxpacket" width="400" height="400"></canvas>
        </div>
        
        <div class="col-md-2">
            <div class="table-responsive">
                <p class="text-center bg-info"><strong>Profile List</strong></p>
                <table class="table no-margin table-striped">
                    <tbody>
                    <?php foreach ($profile as $singleProfile) { ?>
                        <tr>
                            <td><?php echo $singleProfile['name'] ?></td>
                            <td><?php echo isset($singleProfile['rate-limit']) ? $singleProfile['rate-limit'] : "No Data" ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3/dist/chart.min.js"></script>
    <script>
        $(document).ready(function () {


        
                var rxps = [0];
                var txps = [0];
                var date  = new Date();
                var times = [date.getHours()+":"+date.getMinutes()+":"+date.getSeconds()];
                var rxpps = [0];
                var txpps = [0];
                var frxps = [0];
                var ftxps = [0]    
                var frxpps = [0];
                var ftxpps = [0];
            
            
                function  ajaxFunctiondata(name,mk_ip){
            
                    if(txps.length == 60 || rxps.size == 60  ){
                     rxps = [0];
                     txps = [0];
                     rxpps = [0];
                     txpps = [0];
                     times = [date.getHours()+":"+date.getMinutes()+":"+date.getSeconds()];
                     frxps = [0];
                     ftxps = [0]    
                     frxpps = [0];
                     ftxpps = [0];
                    }
                 
                        $.ajax({
                            url: "mikrotik/monitorTraficReturn.php",
                            method: "GET",
                            data: {name,mk_ip},
                            dataType: "json",
                            success: function(result) {
                                var r =result.rx_per_second;
                                var t = result.tx_per_second;
                                var rp =result.rx_packets_per_second;
                                var tp = result.tx_packets_per_second;
                                var fr =result.fp_rx_bits_per_second;
                                var ft = result.fp_tx_bits_per_second;
                                var frp =result.fp_rx_packets_per_second;
                                var ftp = result.fp_tx_packets_per_second;
            
                                rxps.push(r);
                                txps.push(t); 
                                rxpps.push(rp);
                                txpps.push(tp);
                                frxps.push(fr);
                                ftxps.push(ft);
                                frxpps.push(frp);
                                ftxpps.push(ftp);
                                
                                var date  = new Date();
                                times.push(date.getHours()+":"+date.getMinutes()+":"+date.getSeconds());
                                // console.log(result)
                            } 
                        });
                }
            

                // =====Chat =========
                function FP_RX_TX_Packets(id,times,label1,label2){
                    
                    var FpTxRxP = document.getElementById(id).getContext('2d');
                    var FpTxRxPchart = new Chart(FpTxRxP, {
                        type: 'bar',
                        data: {
                            labels: times,
                            datasets: [{
                                label: label1,
                                data: 0,
                                backgroundColor: ['rgba(255, 99, 132, 0.2)' ],
                                borderColor: [ 'rgba(255, 99, 132, 1)', ],
                                borderWidth: 1,
                                maxWidth: 50
                            },{
                                label: label2,
                                data: 0,
                                backgroundColor: ['rgba(255, 159, 64, 0.2)' ],
                                borderColor: ['rgba(255, 159, 64, 1)'],
                                borderWidth: 1
                            }]
                        },
                         options: {
                            scales: { y: {beginAtZero: true},},
                        }
                    });
                    return FpTxRxPchart;    
                }
            
            
            //    ========= RX && TX ==========
            
                TxRxchart = FP_RX_TX_Packets('rxtx',times,'Upload(TX)(Kbps)','Download(RX)(Kbps)');
                 function TxRxgraph(tx,rx,labels){  
                    TxRxchart.data.datasets[0].data = tx;
                    TxRxchart.data.datasets[1].data = rx;
                    TxRxchart.data.labels = labels;
                    TxRxchart.update();  
                          
                }
            
            
            //    ========= RX_TX_Packet ==========
            
                TxRxPchart = FP_RX_TX_Packets('rxtxpacket',times,'Packet(TX)','Packet(RX)');
                 function TxRxPgraph(tx,rx,labels){  
                    TxRxPchart.data.datasets[0].data = tx;
                    TxRxPchart.data.datasets[1].data = rx;
                    TxRxPchart.data.labels = labels;
                    TxRxPchart.update();  
                          
                }
                
        function charttxrxpackectfun(name,mk_ip){ 
                     rxps = [0];
                     txps = [0];
                     date  = new Date();
                     times = [date.getHours()+":"+date.getMinutes()+":"+date.getSeconds()];
                     rxpps = [0];
                     txpps = [0];
                    
                 // ===== Time interval =============
                    setInterval(function() {
                        ajaxFunctiondata(name,mk_ip);
                        TxRxgraph(txps,rxps,times);
                        TxRxPgraph(txpps,rxpps,times);
                    }, 1000);
         }
                    
    
              $('#interfaces').on('click', 'button', function (e) {
                  var name = $(this).data('name');
                  var mk_ip = $('#mk_ip').data('mk_ip');
                  
                   $("#title").text(name);
                   charttxrxpackectfun(name,mk_ip);
                 
                // console.log(mk_ip);  console.log(name);
              });
            
            
            
            $('#example').on('click', 'tbody tr td button#secretCangeStatus', function (e) {
                e.preventDefault();
                var secretName = $(this).data('name');
                var status = $(this).html();
                $.ajax({
                    type: 'get',
                    url: 'mikrotik/enableDisableSecret.php',
                    data: {name: secretName, state: status},
                    success: function (result) {

                    }
                });
                if (status == 'Enable') {
                    $(this).html('Disable');
                    $(this).removeClass('btn-success');
                    $(this).addClass('btn-danger');
                } else {
                    $(this).html('Enable');
                    $(this).removeClass('btn-danger');
                    $(this).addClass('btn-success');
                }
            });
        });
    </script>
    <?php

}
// else {
//     echo '<div class="row"><div class="col-md-8 col-md-offset-2 margin_15_px"><div class="alert alert-danger text-center"><strong>Sorry, We cant connect to Mikrotik ('.$mikrotikLoginData['mik_ip'].')</strong><br><small>Please check your connection IP, Login Name and Password.</small></div></div></div>';
// }
?>


