<?php

require('model/Mikrotik.php');

date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$notification = "";
$day = date('M-Y');
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
                <a href="?q=online_mikrotik_secret&mik_id=<?php echo $mkip['id'] ?>" class="btn btn-info" > <?php echo $mkip['mik_ip'] ?></a>
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

if ($mikrotik->checkConnection()) {
    $allSecretData = $mikrotik->viewAllPppSecret();
    $allRunnigSecretData = $mikrotik->interfaceStatus();
    
?>
   
      <div class="row">
        <div class="col-md-12 mikrotikTable table-responsive">
            <div class="panel panel-default">
                <h4 class="text-center">All Running Secret Information of <?php echo  isset($mkDisconnected['online'])?$mkDisconnected['online']:''; ?></h4>
            </div>
            <table class="table table-responsive table-bordered table-hover table-striped" id="example">
                <thead>
                <tr>
                    <th class="text-center">SL</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">Mtu</th>
                    <th class="text-center">Type</th>
                    <!--<th class="text-center">last UpTime</th>-->
                     <th class="text-center">Connected Time</th> 
                    <th class="text-center">Total Rx(GB)</th>
                    <th class="text-center">Total Tx(GB)</th>
                    <!-- <th class="text-center">Rx Packet</th>
                    <th class="text-center">Tx Packet</th> -->
                    <th class="text-center">Status</th>
                    <th class="text-center">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $serial = 1;

                foreach ($allRunnigSecretData as $secretData) {
                    // var_dump($secretData);
                    $secretData['name'] = trim(str_replace('<pppoe-','',$secretData['name']),'>');
                    if ($ty=='RE'){
                        foreach ($agent_by_user as $agent){
                            if ($agent['ip']==$secretData['name']){ ?>
                                <tr>
                                    <td><?php echo $serial; ?></td>
                                    <td><?php echo $secretData['name'] ?></td>
                                    <td><?php echo isset($secretData['actual-mtu'])? $secretData['actual-mtu'] : '';  ?></td>
                                    <td><?php echo isset($secretData['type'])? $secretData['type'] : '';  ?></td>
                                    <!-- <td><?php // echo isset($secretData['last-link-up-time'])? $secretData['last-link-up-time'] : '';  ?></td> -->
                                    <td><?php 
                                    if(isset($secretData['last-link-up-time'])){
                                        $dteStart = DateTime::createFromFormat("M/d/Y H:i:s", $secretData['last-link-up-time']);
                                        $dteEnd  = new DateTime("now");
                                        $dteDiff  = $dteStart->diff($dteEnd);
                                        echo $dteDiff->format("%m Months %d days %H:%I:%S");
                                    }else{
                                        echo " ";
                                    }
                                    ?></td>
                                    <td><?php  $totalrx=(($secretData['rx-byte']/1048576)/1024); echo round($totalrx, 3); ?></td>
                                    <td><?php  $totaltx=(($secretData['tx-byte']/1048576)/1024);  echo round($totaltx, 3);?></td>
                                    <!-- <td><?php // echo $secretData['rx-packet'] ?></td>
                                    <td><?php // echo $secretData['tx-packet'] ?></td> -->
                                    <td>
                                        <?php
                                        if ($secretData['running'] == 'false') {
                                            echo '<button data-status="1" data-name="'.$secretData['name'].'" class="btn btn-xs btn-danger">Not Running</button>';
                                        } else {
                                            echo'<button data-status="0" data-name="'.$secretData['name'].'" class="btn btn-xs btn-success">Running</button>';
                                        }
                                        ?>
                                    </td>
                                    <td> 
                                    <form action="index.php?q=mikrotik_user_details" method="POST" target="_blank">
                                    <input type="hidden" name="username" value="<?php echo $secretData['name'];?>">
                                     <input type="hidden" name="mk_ip" value="<?php echo isset($mkDisconnected['online'])?$mkDisconnected['online']:"0.0.0.0";?>">
                                    <input type="submit" class="btn btn-xs btn-success" value="View">
                                    </form>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                    }else{?>
                        <tr>
                            <td><?php echo $serial; ?></td>
                            <td><?php echo $secretData['name'] ?></td>
                            <td><?php echo isset($secretData['actual-mtu'])? $secretData['actual-mtu'] : '';  ?></td>
                            <td><?php echo isset($secretData['type'])? $secretData['type'] : '';  ?></td>
                            <!-- <td><?php // echo isset($secretData['last-link-up-time'])? $secretData['last-link-up-time'] : '';  ?></td> -->
                            <td><?php 
                                    if(isset($secretData['last-link-up-time'])){
                                        $dteStart = DateTime::createFromFormat("M/d/Y H:i:s", $secretData['last-link-up-time']);
                                        $dteEnd  = new DateTime("now");
                                        $dteDiff  = $dteStart->diff($dteEnd);
                                        echo $dteDiff->format("%m Months %d days %H:%I:%S");
                                    }else{
                                        echo " ";
                                    }
                            ?></td>
                            <td><?php  $totalrx=(($secretData['rx-byte']/1048576)/1024); echo round($totalrx, 3); ?></td>
                            <td><?php  $totaltx=(($secretData['tx-byte']/1048576)/1024);  echo round($totaltx, 3);?></td>
                            <!-- <td><?php // echo $secretData['rx-packet'] ?></td>
                            <td><?php // echo $secretData['tx-packet'] ?></td> -->
                            <td>
                                <?php
                                if ($secretData['running'] == 'false') {
                                    echo '<button data-status="1" data-name="'.$secretData['name'].'" class="btn btn-xs btn-danger">Not Running</button>';
                                } else {
                                    echo'<button  data-status="0" data-name="'.$secretData['name'].'" class="btn btn-xs btn-success">Running</button>';
                                }
                                ?>
                            </td>
                            <td> 
                            <form action="index.php?q=mikrotik_user_details" method="POST" target="_blank">
                            <input type="hidden" name="username" value="<?php echo $secretData['name'];?>">
                             <input type="hidden" name="mk_ip" value="<?php echo isset($mkDisconnected['online'])?$mkDisconnected['online']:"0.0.0.0";?>">
                            <input type="submit" class="btn btn-xs btn-success"  value="View">
                            </form>
                            </td>
                        </tr>
                    <?php } ?>
                   
                    
                    <?php $serial++;  } ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- <script type="text/javascript" src="asset/js/Chart.js"></script> -->
    <!-- <script type="text/javascript" src="mikrotik/mikrotik_assets/js/custom_chart_mikrotik.js"></script> -->
    <script>
        $(document).ready(function () {
            // $('#example').DataTable();

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

} else {?>
    <div class="row"><div class="col-md-8 col-md-offset-2 margin_15_px"><div class="alert alert-danger text-center"><strong>Sorry, We cant connect to Mikrotik <?php echo $mikrotikLoginData['mik_ip'];?></strong><br><small>Please check your connection IP, Login Name and Password.</small></div></div></div>
<?php  } ?>
   


    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.25/af-2.3.7/b-1.7.1/b-colvis-1.7.1/b-html5-1.7.1/b-print-1.7.1/cr-1.5.4/date-1.1.0/fc-3.3.3/fh-3.1.9/kt-2.6.2/r-2.2.9/rg-1.1.3/rr-1.2.8/sc-2.0.4/sb-1.1.0/sp-1.3.0/sl-1.3.3/datatables.min.css"/>
    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.25/af-2.3.7/b-1.7.1/b-colvis-1.7.1/b-html5-1.7.1/b-print-1.7.1/cr-1.5.4/date-1.1.0/fc-3.3.3/fh-3.1.9/kt-2.6.2/r-2.2.9/rg-1.1.3/rr-1.2.8/sc-2.0.4/sb-1.1.0/sp-1.3.0/sl-1.3.3/datatables.min.js"></script>
    <script>

        $(document).ready(function () {

            $('table').DataTable( {
                dom: 'Bfrtip',
                responsive: true,
                // "paging": false,
                // "searching": false,
                // order: [[1, 'asc']],
                rowGroup: {
                    endRender: function ( rows, group ) {
                        var avg = rows
                            .data()
                            .pluck(1)
                            .reduce( function (a, b) {
                                return a + b.replace(/[^\d]/g, '')*1;
                            }, 0) / rows.count();
        
                        return;
                    },
                    dataSrc: 3
                },

            } );

        })

    </script>