<?php

require('model/Mikrotik.php');
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$notification = "";

if(isset($_POST['username']) && isset($_POST['mk_ip'])){
    extract($_POST);
}else{
    $username ="No Mikrotik connected";
    $mik_id= 0;
    $mk_ip=0;
}

if(isset($_POST['coreName'])){
    $coreName = $_POST['coreName'];
    $coreIp=$_SESSION['coreIp'];
    $coreUser = $_SESSION['coreUser'];
    $coePassword = $_SESSION['coePassword'];
    
    $mk_ip="coreIp";
    $username = $coreName;
    $mikrotikLoginData['mik_ip'] =$coreIp;
    
    $mikrotik = new Mikrotik($coreIp,$coreUser,$coePassword);
}else{
    $mikrotikLoginData = $obj->details_by_cond('mikrotik_user', "mik_ip ='$mk_ip' ORDER BY id ASC");
    $mikrotik = new Mikrotik($mikrotikLoginData['mik_ip'], $mikrotikLoginData['mik_username'], $mikrotikLoginData['mik_password']);
}




if ($mikrotik->checkConnection()) {
    $userdata = $mikrotik->showSingleSecret($username);

    $userdatasingle = $mikrotik->interfacesingle("<pppoe-$username>");
    if(empty($userdatasingle)){
        $userdatasingle = $mikrotik->interfacesingle($username);
    }

    if(isset($userdatasingle[0])){
        $userdatasingle = $userdatasingle[0];
    }
   
    // var_dump(  $userdata);
   
    ?>
    
    <div class="row">
        <div class="col-md-8 col-md-offset-2 margin_15_px">
            <div class="alert alert-success text-center">
                <strong>Welcome To Mikrotik</strong>
                <br>
                <small>Successfully connected to Mikrotik Router by IP
                    :<?php echo $mikrotikLoginData['mik_ip']; ?> </small> (<b><?php echo $username; ?> </b>)
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 mikrotikTable table-responsive">
                      <table class="table table-responsive table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th class="text-center">Name</th>
                    <th class="text-center">Password</th>
                    <th class="text-center">Profile</th>
                    <th class="text-center">Mac Add.</th>
                    <th class="text-center">Local Add.</th>
                    <th class="text-center">Remote Add.</th>
                    <th class="text-center">Mtu</th>
                    <th class="text-center">Type</th>
                   
                    <th class="text-center">Total RxGb</th>
                    <th class="text-center">Total TxGb</th>
                    <th class="text-center">last UpTime</th>
                    <th class="text-center">Last Mac Add.</th>
                    <th class="text-center">Last lougout</th>
                    <th class="text-center">Status</th>
                </tr>
                </thead>
                <tbody>
                <?php if( !empty( $userdata)){}?>
                 <tr class="text-center">
                    <td><?php echo isset($userdata['name'])? $userdata['name'] : (isset($userdatasingle['name'])? $userdatasingle['name']:""); ?></td>
                    <td><?php echo isset($userdata['password'])? $userdata['password'] : '';  ?></td>
                    <td><?php echo isset($userdata['profile'])? $userdata['profile'] : '';  ?></td>
                    <td><?php echo isset($userdata['caller-id'])? $userdata['caller-id']: '';  ?></td>
                    <td><?php echo isset($userdata['local-address'])? $userdata['local-address'] : '';  ?></td>
                    <td><?php echo isset($userdata['remote-address'])? $userdata['remote-address'] : '';  ?></td>
                    <td><?php echo isset($userdatasingle['actual-mtu'])? $userdatasingle['actual-mtu'] : '';  ?></td>
                    <td><?php echo isset($userdatasingle['type'])? $userdatasingle['type'] : (isset( $userdata['type'])? $userdata['type'] : '');  ?></td>
                    <td><?php echo isset($userdatasingle['rx-byte'])? round((($userdatasingle['rx-byte']/1048576)/1024), 3)  : 0;  ?></td>
                    <td><?php echo isset($userdatasingle['tx-byte'])? round((($userdatasingle['tx-byte']/1048576)/1024), 3) : 0;  ?></td>
                    <td><?php echo isset($userdatasingle['last-link-up-time'])? $userdatasingle['last-link-up-time'] : '';  ?></td>
                    <td><?php echo isset($userdata['last-caller-id'])? $userdata['last-caller-id'] : ''; ?></td>
                    <td><?php echo ($userdata['last-logged-out'] != "jan/01/1970 00:00:00" )? $userdata['last-logged-out'] : '';  ?></td>
                    <td> 
                        <?php echo isset($userdata['disabled'])? (($userdata['disabled'] == "false")?"active" :"Disabled"):''; ?>  
                        <?php echo isset($userdatasingle['running'])? (($userdatasingle['running'] == "true")?"Running" :"Not Running"):''; ?>  
                    </td>
                 </tr>
                <?php  ?>
                </tbody>
            </table>
        </div>
    </div>



    <div id="microtikTraficGraph" class="row">
        <div class="col-md-6">
            <canvas id="rxtx" width="400" height="400"></canvas>
        </div>
        
        <div class="col-md-6">
            <canvas id="rxtxpacket" width="400" height="400"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="fprxtx" width="400" height="400"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="fprxtxpacket" width="400" height="400"></canvas>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3/dist/chart.min.js"></script>

    <!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.25/af-2.3.7/b-1.7.1/b-colvis-1.7.1/b-html5-1.7.1/b-print-1.7.1/cr-1.5.4/date-1.1.0/fc-3.3.3/fh-3.1.9/kt-2.6.2/r-2.2.9/rg-1.1.3/rr-1.2.8/sc-2.0.4/sb-1.1.0/sp-1.3.0/sl-1.3.3/datatables.min.css"/>
    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.25/af-2.3.7/b-1.7.1/b-colvis-1.7.1/b-html5-1.7.1/b-print-1.7.1/cr-1.5.4/date-1.1.0/fc-3.3.3/fh-3.1.9/kt-2.6.2/r-2.2.9/rg-1.1.3/rr-1.2.8/sc-2.0.4/sb-1.1.0/sp-1.3.0/sl-1.3.3/datatables.min.js"></script> -->


    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.25/af-2.3.7/cr-1.5.4/date-1.1.0/fc-3.3.3/fh-3.1.9/kt-2.6.2/r-2.2.9/rg-1.1.3/rr-1.2.8/sc-2.0.4/sb-1.1.0/sp-1.3.0/sl-1.3.3/datatables.min.css"/>
    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.25/af-2.3.7/cr-1.5.4/date-1.1.0/fc-3.3.3/fh-3.1.9/kt-2.6.2/r-2.2.9/rg-1.1.3/rr-1.2.8/sc-2.0.4/sb-1.1.0/sp-1.3.0/sl-1.3.3/datatables.min.js"></script>

<script>

$(document).ready(function () {

    $('table').DataTable( {
        dom: 'lBfrtip',
        responsive: true,
        "paging": false,
        "searching": false,
    } );


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


    function  ajaxFunctiondata(){

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
        var name = "<?php echo $username;?>";
        var mk_ip = "<?php echo $mk_ip;?>";
        
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

    // ===== Time interval =============
    setInterval(function() {
        ajaxFunctiondata();
        TxRxgraph(txps,rxps,times);
        TxRxPgraph(txpps,rxpps,times);
        // FpTxRxgraph(ftxps,frxps,times);
        // FpPgraph(ftxpps,frxpps,times);
    }, 1000);

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
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
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




//    =========== FP_RX && FP_TX =========

    
    // FPchart = FP_RX_TX_Packets('fprxtx',times,'Upload(FpTX)(Kbps)','Download(FpRX)(Kbps)');
    // function FpTxRxgraph(tx,rx,labels){          
    //     FPchart.data.datasets[0].data = tx;
    //     FPchart.data.datasets[1].data = rx;
    //     FPchart.data.labels = labels;
    //     FPchart.update();  
    //     }

//    =========== FP_Packets =========

    
    // FpPchart = FP_RX_TX_Packets('fprxtxpacket',times,'Packet(FpTX)','Packet(FpRX)');
    // function FpPgraph(tx,rx,labels){          
    //     FpPchart.data.datasets[0].data = tx;
    //     FpPchart.data.datasets[1].data = rx;
    //     FpPchart.data.labels = labels;
    //     FpPchart.update();  
    //     }




        //disbaled enble
        
        $('#example').on('click', 'button', function (e) {
                e.preventDefault();
                var secretName = $(this).data('name');
                var status = $(this).data('status');
                $.ajax({
                    type: 'get',
                    url: 'mikrotik/enableDisableSecret.php',
                    data: {name: secretName, status: status},
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






   
    <!-- // <script type="text/javascript" src="asset/js/Chart.js"></script> -->
    <!-- <script type="text/javascript" src="mikrotik/mikrotik_assets/js/custom_chart_mikrotik.js"></script> -->
   
    <?php

} ?>