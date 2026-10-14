<?php




header('Content-Type: application/json');

require(realpath(__DIR__ . '/../../services/Model.php'));


$obj = new Model();


try {

  
    $online   = $obj->countOnlineUsers(1);
    $offline  = $obj->countOfflineUsers(1);
    $disabled = $obj->countDisabledUsers(1);

    echo json_encode([
        'status'  => 'connected',
        'online'  => $online,
        'offline' => $offline,
        'disable' => $disabled
    ]);

} catch (Exception $e) {

    echo json_encode([
        'status' => 'disconnected'
    ]);
}
