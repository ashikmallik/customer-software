<?php

require('model/Mikrotik.php');
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$ty = isset($_SESSION['UserType']) ? $_SESSION['UserType'] : NULL;
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
if ($ty=='RE'){
    $agent_by_user = $obj->view_all_by_cond('tbl_agent','entry_by='.$userid);
}

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
        <h4 id="mk_ip" data-mk_ip="<?php echo (isset($mkDisconnected['online'])) ? $mkDisconnected['online'] : '' ?>" > <strong>Connected Mikrotik IP :<?php echo (isset($mkDisconnected['online'])) ? $mkDisconnected['online'] : '0.0.0.0' ?> </strong> </h4>
    </div>
    <div class="box-tools col-md-12">
        <div class="btn-group">
                <?php foreach ($mikrotiks as $mkip){ ?>
                <a href="?q=mikrotik_all_secret&mik_id=<?php echo $mkip['id'] ?>" class="btn btn-info" > <?php echo $mkip['mik_ip'] ?></a>
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
    $mikrotiks = $obj->view_all_by_cond('mikrotik_user','user_id=0');
    
    
    $singleMikrotikAgent = $obj->view_all_by_cond('tbl_agent', "mikrotik_id =$mik_id order by ag_id ASC");
    $agentList = [];
     if(!empty($singleMikrotikAgent)){
         //all agent data set to array
         foreach($singleMikrotikAgent as $agent){
             $agentList[$agent['ip']] = $agent['ip'];
         }
     }
 
    ?>

    <div class="row">
        <div class="col-md-12 mikrotikTable table-responsive">
           <div class="panel panel-default">
               <h4 class="text-center">All Microtik Secret Information</h4>
               <input type="hidden" name="mik_id_hid" id ="mik_id" value="<?= $mik_id ?>">
           </div>
            <table class="table table-responsive table-bordered table-hover table-striped" id="example">
                <thead>
                <tr>
                    <th class="text-center">SL</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">Pasword</th>
                    <th class="text-center">Profile</th>
                    <th class="text-center">Service</th>
                    <th class="text-center">Local Add</th>
                    <th class="text-center">Remote Add</th>
                    <th class="text-center">Last Log out</th>
                    <th class="text-center">Status</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $serial = 1;

                foreach ($allSecretData as $secretData) {
                    
                     if(@$agentList[$secretData['name']] != $secretData['name']){
                        // echo $agentList[$secretData['name']] .'='.$secretData['name'] .' , ';
                        continue;
                    }
                    
                    // $comnt =$secretData['comment'];
                    // $obj->Update_data('tbl_agent', ['ag_office_address'=>addslashes(($comnt))], 'ip="'.$secretData['name'].'" AND mikrotik_id='.$mik_id);
                   
                    if ($ty=='RE'){
                        foreach ($agent_by_user as $agent){
                            if ($agent['ip']==$secretData['name']){ ?>
                                <tr>
                                    <td><?php echo $serial; ?></td>
                                    <td><?php echo $secretData['name'] ?></td>
                                    <td><?php echo $secretData['password'] ?></td>
                                    <td><?php echo $secretData['profile'] ?></td>
                                    <td><?php echo $secretData['service'] ?></td>
                                    <td class="text-center"><?php echo  isset ($secretData['local-address']) ? ($secretData['local-address']) : '-' ?></td>
                                    <td class="text-center"><?php echo  isset ($secretData['remote-address']) ? ($secretData['remote-address']) : '-' ?></td>
                                    <td class="text-center">
                                        <?php
                                        if(isset($secretData['last-logged-out'])){

                                            if(($secretData['last-logged-out'] != 'jan/01/1970 00:00:00')){
                                                echo ucfirst($secretData['last-logged-out']);
                                            }else{
                                                echo '-';
                                            }
                                        } ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($secretData['disabled'] == 'false') {
                                            echo '<button id="secretCangeStatus" data-status="1" data-name="'.$secretData['name'].'" class="btn btn-xs btn-success">Enable</button>';
                                        } else {
                                            echo'<button id="secretCangeStatus"  data-status="0" data-name="'.$secretData['name'].'" class="btn btn-xs btn-danger">Disable</button>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php
                            }
                        }
                    }else{?>
                        <tr>
                            <td><?php echo $serial; ?></td>
                            <td><?php echo $secretData['name'] ?></td>
                            <td><?php echo $secretData['password'] ?></td>
                            <td><?php echo $secretData['profile'] ?></td>
                            <td><?php echo $secretData['service'] ?></td>
                            <td class="text-center"><?php echo  isset ($secretData['local-address']) ? ($secretData['local-address']) : '-' ?></td>
                            <td class="text-center"><?php echo  isset ($secretData['remote-address']) ? ($secretData['remote-address']) : '-' ?></td>
                            <td class="text-center">
                                <?php
                                if(isset($secretData['last-logged-out'])){

                                    if(($secretData['last-logged-out'] != 'jan/01/1970 00:00:00')){
                                        echo ucfirst($secretData['last-logged-out']);
                                    }else{
                                        echo '-';
                                    }
                                } ?>
                            </td>
                            <td>
                                <?php
                                if ($secretData['disabled'] == 'false') {
                                    echo '<button id="secretCangeStatus" data-status="1" data-name="'.$secretData['name'].'" class="btn btn-xs btn-success">Enable</button>';
                                } else {
                                    echo'<button id="secretCangeStatus"  data-status="0" data-name="'.$secretData['name'].'" class="btn btn-xs btn-danger">Disable</button>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>

                    <?php $serial++;  } ?>
                </tbody>
            </table>
        </div>
    </div>
    <script type="text/javascript" src="asset/js/Chart.js"></script>
    <script type="text/javascript" src="mikrotik/mikrotik_assets/js/custom_chart_mikrotik.js"></script>
    <script>
        $(document).ready(function () {
            $('#example').DataTable();

            $('#example').on('click', 'tbody tr td button#secretCangeStatus', function (e) {
                e.preventDefault();
                var secretName = $(this).data('name');
                var status = $(this).html();
                var mik_id = $('#mik_id').val();
                $.ajax({
                    type: 'get',
                    url: 'mikrotik/enableDisableSecret.php',
                    data: {name: secretName, state: status, mik_id_hid: mik_id},
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
//     echo '<div class="row"><div class="col-md-8 col-md-offset-2 margin_15_px"><div class="alert alert-danger text-center"><strong>Sorry, We cant connect to Mikrotik</strong><br><small>Please check your connection IP, Login Name and Password.</small></div></div></div>';
// }
?>


