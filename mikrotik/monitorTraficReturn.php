<?php

use function PHPSTORM_META\type;

session_start();

$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$FullName = isset($_SESSION['FullName']) ? $_SESSION['FullName'] : NULL;
$UserName = isset($_SESSION['UserName']) ? $_SESSION['UserName'] : NULL;
$PhotoPath = isset($_SESSION['PhotoPath']) ? $_SESSION['PhotoPath'] : NULL;
$ty = isset($_SESSION['UserType']) ? $_SESSION['UserType'] : NULL;


if (!empty($_SESSION['UserId'])) {

//========================================
    include '../model/oop.php';
    require('../model/Mikrotik.php');
    $obj = new Controller();
    
    $coreIP = false;
     if(isset($_GET['mk_id'])){
          extract($_GET);
          $mikrotikLoginData = $obj->details_by_cond('mikrotik_user', "id ='$mk_user_id'");
          
     }elseif(isset($_GET['mk_ip'])){
          extract($_GET);
          $mikrotikLoginData = $obj->details_by_cond('mikrotik_user', "mik_ip='$mk_ip'");
          if($_GET['mk_ip'] == "coreIp"){ $coreIP = true;}
          
     }elseif(isset($_GET['user_id'])){
       extract($_GET);
        $mikrotikLoginData = $obj->details_by_cond('mikrotik_user', "user_id ='$user_id' ORDER BY id ASC");
     }else{
         $mikrotikLoginData = $obj->details_by_cond('mikrotik_user', "user_id ='$userid' ORDER BY id ASC");
     }
    
    
    if($coreIP){
        $coreIp=$_SESSION['coreIp'];
        $coreUser = $_SESSION['coreUser'];
        $coePassword = $_SESSION['coePassword'];
        $mikrotik = new Mikrotik($coreIp,$coreUser,$coePassword);
    }else{
        $mikrotik = new Mikrotik($mikrotikLoginData['mik_ip'], $mikrotikLoginData['mik_username'], $mikrotikLoginData['mik_password']);
    }
    


    $mikrotikConnectOrDisconnect = $mikrotik->checkConnection();

    if(isset($_GET['name'])){
       extract($_GET);

        if ($mikrotik->checkConnection()) {
           
            $ARRAY = $mikrotik->monitorTraficSingle('<pppoe-'.$name.'>');
            
            if(isset($ARRAY[0])){
                $traficarray = $ARRAY[0];
            }else{
                $ARRAYC = $mikrotik->monitorTraficSingle($name);
                if(isset($ARRAYC[0])){
                    $traficarray = $ARRAYC[0];
                }else{
                    $traficarray = "";
                }
            }

           if(  empty($traficarray)){
                $JSonData = [
                    "rx_per_second" => 0,
                    "tx_per_second" => 0,
                    "fp_rx_bits_per_second" =>0,
                    "fp_tx_bits_per_second" => 0, 
                    "rx_packets_per_second" => 0,
                    "tx_packets_per_second" => 0,
                    "fp_rx_packets_per_second" => 0,
                    "fp_tx_packets_per_second" => 0,
                    ];
                        
            }else{
                $JSonData = array(
                    // 'asas'=>$ARRAY,
                    "rx_per_second" => ($traficarray['rx-bits-per-second'] / 1000),
                    "tx_per_second" => ($traficarray['tx-bits-per-second'] / 1000),
                    "fp_rx_bits_per_second" => ($traficarray['fp-rx-bits-per-second'] / 1000),
                    "fp_tx_bits_per_second" => ($traficarray['fp-tx-bits-per-second'] / 1000), 
                    "rx_packets_per_second" => ($traficarray['rx-packets-per-second'] ),
                    "tx_packets_per_second" => ($traficarray['tx-packets-per-second'] ),
                    "fp_rx_packets_per_second" => ($traficarray['fp-rx-packets-per-second']),
                    "fp_tx_packets_per_second" => ($traficarray['fp-tx-packets-per-second'] ),
                );
            }
            
            echo json_encode($JSonData);
            // echo json_encode($name);
        }
    }else{
        echo json_encode(['']);
    }
    
}
?>
