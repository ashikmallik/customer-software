<?php
require('routeros_api.class.php');
    
class Mikrotik extends RouterosAPI{

    private $connectStatus = false;
    
    public function __construct($ip, $name, $password) {
        if($this->connect($ip, $name, $password)){
            $this->connectStatus = true;
        }
    }
    
    public function checkConnection(){
        return $this->connectStatus;
    }

    public function checkSecretActive($secretName) {

        $this ->write("/ppp/secret/print",false);
        $this ->write("?name=".$secretName);
        $READ = $this->read(false);
        $singleSecretData = $this->parseResponse($READ);
        
        if (isset($singleSecretData[0]['disabled'])) {

           if ($singleSecretData[0]['disabled'] == 'false') {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function showSingleSecret($secretName) {

        $this ->write("/ppp/secret/print",false);
        $this ->write("?name=".$secretName);
        $READ = $this->read(false);
        $singleSecretData = $this->parseResponse($READ);
        if(!empty($singleSecretData)){
            return $singleSecretData[0];
        }else{
            return null;
        }
    }
    
    public function removeSecretActive($secretName) {

        $this->write('/interface/pppoe-server/print', false);
        $this->write('=.proplist=.id', false);
        $this->write('?name=<pppoe-'.$secretName.'>');
        $ARRAY = $this->read();
        if(isset($ARRAY[0])){
            $this->write('/interface/pppoe-server/remove',false);
            $this->write('=.id='.$ARRAY[0]['.id']);
            $READ = $this->read(false);
            return $this->parseResponse($READ);
        }
        
    }

    public function viewAllPppSecret(){
        
        $this->write('/ppp/secret/print');
        $READ = $this->read(false);
        $allData = $this->parseResponse($READ);
        return $allData;
    }
    
    public function monitorTrafic(){
        
        $this->write('/interface/monitor-traffic', true);
        $this->write('=interface=LAN',false);
        $this->write('=once=');
        $READ = $this->read(false);
        $traficArray = $this->parseResponse($READ);
        return $traficArray;
    }
    
     public function monitorTraficSingle($user){
        
        $this->write('/interface/monitor-traffic', false);
        $this->write('=interface='.$user,false); // <pppoe-server> && pppoe-client
        $this->write('=once=');
        $READ = $this->read(false);
        $traficArray = $this->parseResponse($READ);
        return $traficArray;

    }
    
    public function interfaceStatus(){
        
        $this->write('/interface/print', true);
        $READ = $this->read(false);
        $interface = $this->parseResponse($READ);
        return $interface;
    }

    public function interfacesingle($user){

        $this->write('/interface/getall',false);
        $this->write('?name='.$user); //// <pppoe-server> && pppoe-client
        $READ = $this->read(true);
        return $READ;
    }
    public function profileStatus(){
        
        $this->write('/ppp/profile/print', true);
        $READ = $this->read(false);
        $profile = $this->parseResponse($READ);
        return $profile;
    }

    
    public function enableSingleSecret($userName) {
        
        $this->comm("/ppp/secret/enable", array(
            "numbers" => $userName,
        ));
    }

    
    public function disableSingleSecret($userName){
        
        $this->comm("/ppp/secret/disable", array(
            "numbers" => $userName,
        ));
        $this->removeSecretActive($userName);
    }

    
    public function createNewSecret($user, $password, $service='pppoe', $profile,$comment){
        $this->comm("/ppp/secret/add", array(
            "name" => $user,
            "password" => $password,
            "service" => $service,
            "profile" => $profile,
            "comment" => $comment,
        ));
    }


    public function updateExistingSecret($user, $password, $profile,$comment, $existingUsername){

        $this->comm("/ppp/secret/set", array(
            "numbers" => $existingUsername,
            "name" => $user,
            "password" => $password,
            "profile" => $profile,
              "comment" => $comment,
        ));
        
        $this->removeSecretActive($existingUsername);
    }
    public function removeSecret($userName){
        $this->comm("/ppp/secret/remove", array(
            "numbers" => $userName,
        ));
         $this->removeSecretActive($userName);
    }



    //Crate a simple queue //ok
 
    public function createNewQueue($name, $target, $limitAt, $maxLimit, $comment)
    {
        $this->comm("/queue/simple/add", [
            "name" => $name,
            "target" => $target,
            "limit-at" => $limitAt,
            "max-limit" => $maxLimit,
            "comment" => $comment,
        ]);
    }

    
    // Update a simple queue //ok
    public function updateSimpleQueue($queueId, $target, $limitAt, $maxLimit,$name)
    {
        $this->comm("/queue/simple/set", [
            ".id" => $queueId,
            "target" => $target,
            "limit-at" => $limitAt,
            "max-limit" => $maxLimit,
            "name" => $name,
        ]);
    }

    // Update a simple queue //ok
    public function updateTLSimpleQueue($queueId, $target, $limitAt, $maxLimit)
    {
        $this->comm("/queue/simple/set", [
            ".id" => $queueId,
            "target" => $target,
            "limit-at" => $limitAt,
            "max-limit" => $maxLimit,
        ]);
    }

    
    // Update and enable/disable a simple queue //ok
    public function updateEnableDisableSimpleQueue($queueId, $limitAt, $maxLimit, $disabled)
    {
        $this->comm("/queue/simple/set", [
            ".id" => $queueId,
            "limit-at" => $limitAt,
            "max-limit" => $maxLimit,
            "disabled" => $disabled ? "true" : "false",
        ]);
    }


    // Enable a simple queue //ok
    public function enableSimpleQueue($queueId)
    {
        $this->comm("/queue/simple/enable", [
            ".id" => $queueId,
        ]);
    }

    // Disable a simple queue //ok
    public function disableSimpleQueue($queueId)
    {
        $this->comm("/queue/simple/disable", [
            ".id" => $queueId,
        ]);
    }

     // Remove  a simple queue //ok
    public function removeSimpleQueue($queueId)
    {
        $this->comm("/queue/simple/remove", [
            ".id" => $queueId,
        ]);
    }
    
    // Single a simple queue //ok
    public function getSingleQueue($queueId) {
        $response = $this->comm("/queue/simple/print", [
            "?name" => $queueId,
        ]);
    
        if (isset($response[0])) {
            return $response[0];
        } else {
            return null;
        }
    }

   // all a simple queue //ok
    public function getAllQueues() {
        $response = $this->comm("/queue/simple/print");
    
        return $response;
    }
    

    public function __destruct() {
        
        $this->disconnect();
    }

}
 //Create working
  
    // Create a simple queue
    // $mikrotik->createNewQueue("queue1m", "192.168.1.11", "1M/1M","1M/1M", "Queue for test");


    // Enable a simple queue
    // $mikrotik->enableSimpleQueue("queue1m");

    // Disable a simple queue
    // $mikrotik->disableSimpleQueue("queue1");


    // Update and enable a simple queue
    // $mikrotik-> updateEnableDisableSimpleQueue("queue1m", "10M/10M", "10M/10M", false);
    // Update and disable a simple queue
    // $mikrotik->updateEnableDisableSimpleQueue("queue1m", "12M/12M", "12M/12M", true);
    // Update  a simple queue
    // $mikrotik->updateSimpleQueue("queue52m", "192.168.1.12", "12M/12M", "12M/12M","queue1m");
    // Update  a Target limit simple queue
    //  $mikrotik->updateTLSimpleQueue("queue1m", "192.168.1.10", "10M/10M", "10M/10M");
    // Remove simple queue
    //  $mikrotik->removeSimpleQueue("queue1m");
    // Remove simple queue
    //  $mkcust = $mikrotik->getSingleQueue("queue1m");
    //  var_dump($mkcust);
     // all simple queue
    //  $mkallqu = $mikrotik->getAllQueues();
    //  var_dump($mkallqu);