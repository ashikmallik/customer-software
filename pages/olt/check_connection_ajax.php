<?php
$olt_ip = "172.35.156.14";
$community = "bsd";
$oid = ".1.3.6.1.4.1.3320.101.10.5.1.3"; // ONU Status

$onuList = snmpwalk("udp:$olt_ip:161", $community, $oid);

foreach ($onuList as $index => $line) {
    echo "ONU " . ($index + 1) . ": " . $line . "<br>";
}
?>