<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();

if (isset($_POST['zone_id'])) {
    $zone_id = (int) $_POST['zone_id'];

    $subzones = $obj->rawSql("SELECT * FROM tbl_zone WHERE parent_id = $zone_id AND level = 2");

    echo '<option value="">Select Sub Zone</option>';

    foreach ($subzones as $sz) {
        echo '<option value="'.$sz['zone_id'].'">'.$sz['zone_name'].'</option>';
    }
}