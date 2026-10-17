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


// $singleMikrotikAgent = $obj->view_all_by_cond('tbl_agent', "mikrotik_id =$mik_id order by ag_id ASC");

 
 
    if (isset($_GET['zone'])) {

        $zone = $_GET['zone'];

         $allDueCustomer = $obj->view_all_by_cond("vw_bill_calc", "ag_status='1' and pay_status='1' and mikrotik_id=$mik_id AND due_status='0' AND zone = $zone AND total_bill > 0 AND mikrotik_disconnect <= Date('d') ORDER BY `ag_id` ASC");

    } else {

        $allDueCustomer = $obj->view_all_by_cond("vw_bill_calc", "ag_status='1' and pay_status='1' and mikrotik_id=$mik_id AND due_status='0' AND total_bill > 0 AND mikrotik_disconnect <= Date('d') ORDER BY `ag_id` ASC");
    }
    
    

      $secretList = [];
      $secretListDisbaled = [];
     if ($mikrotik->checkConnection()) {
        $allSecretData = $mikrotik->viewAllPppSecret();
       
      //all secret set to array 
        foreach($allSecretData as $secretData){
          $secretList[$secretData['name']] = $secretData['name'];
          $secretListDisbaled[$secretData['name']] = $secretData['disabled'];
        }
        
     }
 
//  var_dump(count($secretListDisbaled));
 

 $DueCC= count($allDueCustomer)-(isset($_SESSION[$mkDisconnected['online']])?$_SESSION[$mkDisconnected['online']]:0);


//  echo ' allcustomerDue_'.count($allDueCustomer);
//   echo " sessiondisconnectedprevious_".$_SESSION[$mkDisconnected['online']];
//   echo ' avaivake to duisconnect user_'.$DueCC;
 
if($DueCC > 2000){
    $Duecount = round($DueCC/5);
    
}elseif($DueCC > 1500){
    $Duecount = round($DueCC/4);
    
}elseif($DueCC > 1200){
    $Duecount = round($DueCC/3);
    
}elseif($DueCC > 1000){
    $Duecount = round($DueCC/2);
    
}elseif($DueCC > 500){
    $Duecount = round($DueCC/2);
}
else{
    $Duecount = $DueCC;
}

//  echo 'current will be disconnect _'.$Duecount;

 
$c= 1;
if(isset($_POST['disconnectmkid'])){
    
      $_SESSION[$mkDisconnected['online']] += $Duecount;
      foreach($allDueCustomer as $agent){
         if($c++ <= $Duecount){ 
             if(($secretListDisbaled[$agent['ip']] == "false")){
                  $mikrotik->disableSingleSecret(trim($agent['ip']));
                  $secretListDisbaled[$agent['ip']] = "true";
             }
              
         }
     }
}

//  echo ' after disconnect updated session_'.$_SESSION[$mkDisconnected['online']].'    ';
//  var_dump($_SESSION);
 
if($Duecount == 0){
    if(isset($_SESSION[$mkDisconnected['online']])){
         unset($_SESSION[$mkDisconnected['online']]);
    }
}

?>

<div class="row">
    <div class="col-md-12 bg-slate-800" style="margin-top:20px; margin-bottom: 15px;">
        <h4 id="mk_ip" data-mk_ip="<?php echo (isset($mkDisconnected['online'])) ? $mkDisconnected['online'] : '' ?>" > <strong>Connected Mikrotik IP :<?php echo (isset($mkDisconnected['online'])) ? $mkDisconnected['online'] : '0.0.0.0' ?> </strong> </h4>
    </div>
    <div class="box-tools col-md-12">
        <div class="btn-group">
                <?php foreach ($mikrotiks as $mkip){ ?>
                <a href="?q=disconnectByDue&mik_id=<?php echo $mkip['id'] ?>" class="btn btn-info" > <?php echo $mkip['mik_ip'] ?></a>
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
     <div class="col-md-12 text-center">
        <form method="POST">
             <input hidden name="mik_id" value="<?php echo $mkip['id'] ?>">
             <button type="submit" class="btn btn-md btn-danger"  name="disconnectmkid"><i class="fa fa-check-square"></i> Disconnect Due Customer For <?php echo $mkDisconnected['online'] ?> (Remaining <?php echo (count($allDueCustomer)-(isset($_SESSION[$mkDisconnected['online']])?$_SESSION[$mkDisconnected['online']]:0)) ?> ) </button>
        </form>
    </div>
</div>

<div class="row" style="font-size:12px">
    <div class="col-md-12 table-responsive">
        <table class="table table-bordered table-hover table-striped" id="datatable">
            <thead>
            <tr>
                <th class="col-md-1">Id</th>
                <th class="col-md-1">Customer</th>
                <th class="col-md-1">IP/Secret</th>
                <th class="col-md-1">Address</th>
                <!--<th class="col-md-1">Zone</th> <td>  '.$cusIfo["zone"].'</td> -->
                <th class="col-md-1">Mobile No</th>
                <th class="col-md-1">Speed</th>
                <th class="col-md-1">Bill</th>
                <th class="col-md-1">Pre. Due</th>
                <th class="col-md-1">Total<br>Dues</th>
                <th class="col-md-2">Disc.<br>Date</th>
                <th class="d col-md-1">Action</th>
            </tr>
            </thead>
            <tbody>
                <?php foreach($allDueCustomer as $cusIfo ){
               
                echo '<tr>
                        <td>  '.$cusIfo["cus_id"].'</td>
                        <td>  '.$cusIfo["ag_name"].'</td>
                        <td>  '.$cusIfo["ip"].'</td>
                        <td>  '.$cusIfo["ag_office_address"].'</td>
                        
                        <td>  '.$cusIfo["ag_mobile_no"].'</td>
                        <td>  '.$cusIfo["mb"].'</td>
                        <td>  '.$cusIfo["taka"].'</td>
                        <td>  '.($cusIfo["total_bill"]-$cusIfo["taka"]).'</td>
                        <td>  '.$cusIfo["total_bill"].'</td>
                        <td>  '.$cusIfo["mikrotik_disconnect"].'-'.date('m-Y').'</td>
                        <td>  '.(($secretListDisbaled[$cusIfo['ip']] == "true")? '<button id="secretChangeStatus" data-name="'. $cusIfo['ip'].'" class="btn btn-xs btn-danger">Disable</button>' : '<button id="secretChangeStatus" data-name="'.$cusIfo['ip'].'" class="btn btn-xs btn-success">Enable</button>' ).'</td>
                    </tr>';

                } ?>
            </tbody>
        </table>
        
    </div>
</div>

<script>
    
            $('#datatable').on('click', 'tbody tr td button#secretChangeStatus', function (e) {

                e.preventDefault();
                var secretName = $(this).data('name');
                var status = $(this).html();
                var mik_id = `<?php echo $mkip['id'] ?>`;
                
                // console.log(secretName);
                
                $.ajax({

                    type: 'get',
                    url: 'mikrotik/enableDisableSecret.php',
                    data: {name: secretName, state: status, mik_id_hid: mik_id},

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

        }); // secret enable disable
</script>