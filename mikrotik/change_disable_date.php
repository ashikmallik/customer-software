<?php
session_start();

if (!empty($_SESSION['UserId'])) {

    //========================================
    include '../model/oop.php';
    include '../model/Bill.php';
    include '../model/Mikrotik.php';
    
    $bill = new Bill();

    $obj = new Controller();
    
    $agent = $obj->details_by_cond('tbl_agent','ag_id='.$_POST['id']);
    
    $mikrotikLoginData = $obj->details_by_cond('mikrotik_user', 'id = 1');

    $mikrotik = new Mikrotik($mikrotikLoginData['mik_ip'], $mikrotikLoginData['mik_username'], $mikrotikLoginData['mik_password']);

    $dateArray = explode('-',$_POST['date']);

    $day = $dateArray[0];
    
    if($day >= date('d')){
        $mikrotik->enableSingleSecret($agent['ip']);
    }
    
    if($day < date('d')){
        $mikrotik->disableSingleSecret($agent['ip']);
    }

    $form_data = array(
        'mikrotik_disconnect' => $day,
    );

    $obj->Update_data("tbl_agent", $form_data,'ag_id = '.$_POST['id']);


}