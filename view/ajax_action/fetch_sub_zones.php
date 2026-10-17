
<?php

//========================================
include '../../model/oop.php';
include '../../model/Bill.php';
//include '../../model/SSP.php';

$obj = new Controller();
$bill = new Bill();
//========================================

$user_id = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$FullName = isset($_SESSION['FullName']) ? $_SESSION['FullName'] : NULL;
$UserName = isset($_SESSION['UserName']) ? $_SESSION['UserName'] : NULL;
$PhotoPath = isset($_SESSION['PhotoPath']) ? $_SESSION['PhotoPath'] : NULL;
$ty = isset($_SESSION['UserType']) ? $_SESSION['UserType'] : NULL;


/* =============================================
 * =============================================
 *  Here we use raw query only enhance performance
 *  When the agent number is more than 1500+
 *  It take significant time to show them.*
 * =============================================
 * =============================================*/
    // Include the necessary database connection or object
include('path_to_your_database_connection_file.php');  // Adjust this path

// Get the zone_id from the AJAX request
if (isset($_GET['zone_id'])) {
    $zoneId = $_GET['zone_id'];

    // Fetch sub zones related to the selected zone
    $subZones = $obj->view_all_by_cond('tbl_sub_zone', "zone_id = '$zoneId'");

    // Check if sub zones exist and generate the options
    if (!empty($subZones)) {
        echo '<option value="">Select Sub Zone /Area</option>';  // Default prompt option
        foreach ($subZones as $subZone) {
            echo '<option value="' . $subZone['sub_zone_name'] . '">' . $subZone['sub_zone_name'] . '</option>';
        }
    } else {
        // If no sub zones are found, display a message
        echo '<option value="">No Sub Zones available</option>';
    }
}

?>