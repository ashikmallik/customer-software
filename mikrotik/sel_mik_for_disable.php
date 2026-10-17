<?php
$mikrotikConnect = false;
require('model/Mikrotik.php');
if ($obj->tableExists('mikrotik_user')) {
   $userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
   /*if ($_SESSION['UserType']=='SA'){
       $mik_id= $userid;
       $mikrotikLoginData = $obj->details_by_cond('mikrotik_user', 'user_id ='.$mik_id);
   }
   else{
            $mik_id = 1;
            $mikrotikLoginData = $obj->details_by_cond('mikrotik_user', 'id ='.$mik_id);
    }*/
 if ($_SESSION['UserType']=='SA'){
  if (isset($_GET['mik_id'])){
        $mik_id = $_GET['mik_id'];
    }else{
        $mik_id = 1;
    }
    $mikrotikLoginData = $obj->details_by_cond('mikrotik_user', 'id ='.$mik_id);
   }
   else
    {
    $mikrotikLoginData = $obj->details_by_cond('mikrotik_user', 'id = 1');
    }
   // $mikrotikLoginData = $obj->details_by_cond('mikrotik_user', 'id = 1');
    $mikrotik = new Mikrotik($mikrotikLoginData['mik_ip'], $mikrotikLoginData['mik_username'], $mikrotikLoginData['mik_password']);
    if ($mikrotik->checkConnection()) {

        $mikrotikConnect = true;
        $_SESSION['microtik_conn'] = true;
        $mikrotiks = $obj->view_all_by_cond('mikrotik_user','user_id=0');

    }
}

date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
?>

<div class="row">
    <div class="col-md-12 bg-slate-800" style="margin-top:20px; margin-bottom: 15px;">
        
    </div>
        <?php if ($_SESSION['UserType']=='SA'){?>
               <div class="box-tools pull-left col-md-2">
                   <div class="btn-group">
                       <button type="button" class="btn btn-info">Select Mikrotik</button>
                       <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                           <span class="caret"></span>
                           <span class="sr-only">Toggle Dropdown</span>
                       </button>
                       <ul class="dropdown-menu" role="menu">
                           <?php foreach ($mikrotiks as $mikrotikmen){?>
                           <li><a href="?q=check_bill_info&mik_id=<?= $mikrotikmen['id']?>"><?= $mikrotikmen['mik_username']?> - <?= $mikrotikmen['mik_ip']?></a></li>
                           <?php } ?>
                       </ul>
                   </div>
               </div>
<?php } ?>
</div>
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
    function noSpace(e) {
        if (e.which == 32) {
            return false;
        }
    }


    }); // document ready function
</script>
