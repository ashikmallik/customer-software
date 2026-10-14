<?php

session_start();


        require(realpath(__DIR__ . '/../../services/Model.php'));
        $obj = new Model();

    $coreIP = false;


    if (isset($_GET['name'])) {
        // extract($_GET);

        
            $ARRAY = $obj->monitorTraficSingle($_GET['mik_id'],$_GET['name']);

            if (isset($ARRAY)) {
                $traficarray = $ARRAY;
            } else {
                $ARRAYC = $obj->monitorTraficSingle($_GET['mik_id'],$_GET['name']);
                // $ARRAY = $mikrotik->monitorTraficSingle($_GET['mik_id'],'<pppoe-'.$_GET['name'].'>');
                if (isset($ARRAYC[0])) {
                    $traficarray = $ARRAYC[0];
                } else {
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
                    'asas'=>$ARRAY,
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
            // echo json_encode($_GET['mik_id']);
        
    } else {
        echo json_encode(['somthing is wrong']);
    }

?>
