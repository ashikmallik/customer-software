<?php

require('model/Mikrotik.php');
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$ty = isset($_SESSION['UserType']) ? $_SESSION['UserType'] : NULL;
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

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






$singleMikrotikAgent = $obj->view_all_by_cond('tbl_agent', "mikrotik_id =$mik_id order by ag_id ASC");
$agentList = [];
 if(!empty($singleMikrotikAgent)){
     //all agent data set to array
     foreach($singleMikrotikAgent as $agent){
         $agentList[$agent['ip']] = $agent['ip'];
     }
 }
 

 
 
 $secretList = [];
 if ($mikrotik->checkConnection()) {
    $allSecretData = $mikrotik->viewAllPppSecret();
   
   //all secret set to array 
    foreach($allSecretData as $secretData){
       $secretList[$secretData['name']] = $secretData['name'];
    }
    
 }

//   echo count($singleMikrotikAgent).' - '. count($agentList).' == '.count($allSecretData).' - '. count($secretList);
 
 
 //Add to software
 if ($mikrotikConnect) {
     if(isset($_GET['addToSoft'])){
         
    $data = $obj->details_by_cond("tbl_agent", "ag_status='1' ORDER BY ag_id DESC limit 1");
    // $data['ag_id']= 0;
    if (($data['ag_id'] + 1) < 10) {
        $STD = "CUS000";
    } else if (($data['ag_id'] + 1) < 100) {
        $STD = "CUS000";
    } else if (($data['ag_id'] + 1) < 1000) {
        $STD = "CUS00";
    } else if (($data['ag_id'] + 1) < 10000) {
        $STD = "CUS0";
    } else {
        $STD = "CUS";
    }
    $STD .= $data['ag_id'] + 1;
    
    
         foreach ($allSecretData as $secretData) {
            if(@$agentList[$secretData['name']] == $secretData['name']){
                continue;
            }
             $packagesPrice =  $obj->details_by_cond('tbl_package', 'net_speed ="'.$secretData['profile'].'"')['bill_amount'];
             $formArray =[
                 'cus_id'=> $STD++,
                 'ip'=> $secretData['name'],
                 'ag_name'=> $secretData['name'],
                 'ag_mobile_no'=> $secretData['password'],
                 'mb'=> $secretData['profile'],
                 'int_mb'=> $secretData['profile'],
                 'taka'=> $packagesPrice,
                 'ag_status'=> ($secretData['disabled'] == 'false')?1:0,
                  'entry_date' => date('Y-m-d', strtotime('first day of this month')),
                 'mikrotik_id' => $mik_id,
                 'entry_date'=>$date,
                 'connection_date'=>date('Y-m-d'),
                 'ag_office_address'=>addslashes(($secretData['comment']))
                 ];
            //   var_dump($packagesPrice);
            // echo $secretData['profile'];
            
             $obj->Reg_user_cond("tbl_agent", $formArray, " ");
         }
         
         $obj->notificationStore('Insert Successfull');
         echo '<script>window.location = "../index.php?q=mikrotik_unmatching&mik_id='.$mik_id.'";</script>';
     }
 
 }

 //delete from software
 if ($mikrotikConnect) {
     if(isset($_GET['deleteFromSoft'])){
          foreach ($singleMikrotikAgent as $agent) {
            if(@$secretList[$agent['ip']] == $agent['ip']){
                continue;
            }
            // var_dump($agent);
            $obj->Delete_data("tbl_agent", "ag_id='".$agent['ag_id']."'");
          }
         $obj->notificationStore('Delete Successfull');
         echo '<script>window.location = "../index.php?q=mikrotik_unmatching&mik_id='.$mik_id.'";</script>';
     }
 }
 
 
 //packageupdate
//  foreach($obj->view_all('tbl_package') as $pack){
//      $obj->Update_data('tbl_agent', ['taka'=>$pack['bill_amount'],'entry_date'=>"2022-01-01"], 'mb="'.$pack['net_speed'].'"');
//  }


//  var_dump($agentList);exit;
?>
<div class="row">
    <div class="col-md-12 bg-slate-800" style="margin-top:20px; margin-bottom: 15px;">
        <h4 id="mk_ip" data-mk_ip="<?php echo (isset($mkDisconnected['online'])) ? $mkDisconnected['online'] : '' ?>" > <strong>Connected Mikrotik IP :<?php echo (isset($mkDisconnected['online'])) ? $mkDisconnected['online'] : '0.0.0.0' ?> </strong> </h4>
    </div>
    <div class="box-tools col-md-12">
        <div class="btn-group">
                <?php foreach ($mikrotiks as $mkip){ ?>
                <a href="?q=mikrotik_unmatching&mik_id=<?php echo $mkip['id'] ?>" class="btn btn-info" > <?php echo $mkip['mik_ip'] ?></a>
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
    // $allSecretData = $mikrotik->viewAllPppSecret();
    // $mikrotiks = $obj->view_all_by_cond('mikrotik_user','user_id=0');
    ?>
    <!--//new entry only mikrotik ...unmacting to software-->
    <div class="row">
        <div class="col-md-12 mikrotikTable table-responsive">
           <div class="panel panel-default">
               <h4 class="text-center alert-success">Those Secret are not included Software</h4>
               <input type="hidden" name="mik_id_hid" id ="mik_id" value="<?= $mik_id ?>">
               <div class="card">
                   <form method="get" action="">
                        <input type="hidden" name="q" value="mikrotik_unmatching" >
                       <input type="hidden" name="mik_id" value="<?php echo $mik_id; ?>" >
                       <input type="submit" class="btn btn-success btn-lg" name="addToSoft" value="Add All Unmatching To Software">
                  </form>
               </div>
           </div>
            <table class="table table-responsive table-bordered table-hover table-striped" id="example">
                <thead>
                <tr>
                    <th class="text-center">SL</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">Pasword</th>
                    <th class="text-center">Profile</th>
                    <th class="text-center">Service</th>
                    <th class="text-center">Last Log out</th>
                    <th class="text-center">Status</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $serial = 1;  //var_dump($agentList);
                foreach ($allSecretData as $secretData) { //echo $agentList[$secretData['name']] .'='.$secretData['name'] .' , ';
                    if(@$agentList[$secretData['name']] == $secretData['name']){
                        // echo $agentList[$secretData['name']] .'='.$secretData['name'] .' , ';
                        continue;
                    }
                 ?>
                        <tr>
                            <td><?php echo $serial; ?></td>
                            <td><?php echo $secretData['name'] ?></td>
                            <td><?php echo $secretData['password'] ?></td>
                            <td><?php echo $secretData['profile'] ?></td>
                            <td><?php echo $secretData['service'] ?></td>
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

                    <?php $serial++;  } ?>
                </tbody>
            </table>
        </div>
    </div>
   
   
   
   <!--//unmacting in software entry only software -->
      <div class="row">
        <div class="col-md-12 soft table-responsive">
           <div class="panel panel-default">
               <h4 class="text-center alert-danger">Those Secret are not included Mikrotik</h4>
               <input type="hidden" name="mik_id_hid" id ="mik_id" value="<?= $mik_id ?>">
               <div class="card">
                   <form method="get" action="">
                        <input type="hidden" name="q" value="mikrotik_unmatching" >
                       <input type="hidden" name="mik_id" value="<?php echo $mik_id; ?>" >
                       <input type="submit" class="btn btn-danger btn-lg" name="deleteFromSoft" value="Delete All Unmatching From Software">
                  </form>
               </div>
           </div>
            <table class="table table-responsive table-bordered table-hover table-striped" id="softtbl">
                <thead>
                <tr>
                    <th class="text-center">SL</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">Package</th>
                    <th class="text-center">Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $sla = 1;
                foreach ($singleMikrotikAgent as $agent) {
                    if(@$secretList[$agent['ip']] == $agent['ip']){
                        continue;
                    }
                 ?>
                        <tr>
                            <td><?php echo $sla; ?></td>
                            <td><?php echo $agent['ip'] ?></td>
                            <td><?php echo $agent['mb'] ?></td>
                            <td><?php echo ($agent['ag_status']  == 1)? 'active': 'Inactive' ?></td>
                            <th> <button data-id="<?php echo $agent['ag_id'] ?>" type="button" class="btn btn-danger bt-md">Delete </button>  </th>
                        </tr>
                    <?php $sla++;  } ?>
                </tbody>
            </table>
        </div>
    </div>
   
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.11.4/af-2.3.7/b-2.2.2/b-html5-2.2.2/b-print-2.2.2/cr-1.5.5/sl-1.3.4/datatables.min.css"/>
 
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.11.4/af-2.3.7/b-2.2.2/b-html5-2.2.2/b-print-2.2.2/cr-1.5.5/sl-1.3.4/datatables.min.js"></script>
   
   <script>
    $(document).ready(function () {
        $('#softtbl').on('click', 'button',function () {
            var agentId = $(this).data('id');
            console.log(agentId);
             $.get("view/ajax_action/delete_unmatcing_only_software_entry_agent.php", { agentId: agentId }, function (result) { alert(result); });
             $(this).attr('disabled', 'disabled');
        });
    });
    
</script>
   

    <script>
        $(document).ready(function () {
            
            $('#example').DataTable( {
                dom: 'Bfrtip',
                 lengthMenu: [
                    [ 20, 100, 500, -1 ],
                    [ '20 rows', '100 rows', '500 rows', 'Show all' ]
                ],
                buttons: [
                    'colvis',
                    'excel',
                    'print',
                    'pdf',
                    'pageLength',
                ]
            } );
            $('#softtbl').DataTable( {
                dom: 'Bfrtip',
                 lengthMenu: [
                    [ 20, 100, 500, -1 ],
                    [ '20 rows', '100 rows', '500 rows', 'Show all' ]
                ],
                buttons: [
                    'colvis',
                    'excel',
                    'print',
                    'pdf',
                    'pageLength',
                ]
            } );
            
            // $('#example').DataTable();
            // $('#softtbl').DataTable();

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

<?php } ?>