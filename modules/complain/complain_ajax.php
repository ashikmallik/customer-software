<?php
session_start();

if (!empty($_SESSION['UserId'])) {
//========================================
$filepath = realpath(dirname(__FILE__));
include_once ($filepath.'/../../model/oop.php');

$obj = new Controller();
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

    date_default_timezone_set('Asia/Dhaka');
    $date_time = date('Y-m-d g:i:sA');
    $ip_add = $_SERVER['REMOTE_ADDR'];
    $userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

    $datefrom = isset($_GET['datefrom']) ? $_GET['datefrom'] : '';
$dateto = isset($_GET['dateto']) ? $_GET['dateto'] : '';
$company_name = isset($_GET['company_name']) ? $_GET['company_name'] : '';

// Construct dynamic query
$whereClauses = [];
if ($datefrom && $dateto) {
    $whereClauses[] = "complain_date BETWEEN '$datefrom' AND '$dateto'";
}
if ($company_name) {
    $whereClauses[] = "company_id = '$company_name'";
}

$whereQuery = $whereClauses ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

$query = "SELECT * FROM tbl_complains $whereQuery";
$result = $obj->query($query);

if ($result) {
    $dataRows = [];
    while ($row = $result->fetch_assoc()) {
        $dataRows[] = $row;
    }

    if (count($dataRows) > 0) {
        // Create dynamic HTML table
        $htmlOutput = '<table class="table table-bordered">';
        $htmlOutput .= '<thead><tr>
                            <th>TT Number</th>
                            <th>Company Name</th>
                            <th>Details</th>
                            <th>Complain Time</th>
                         </tr></thead>';
        $htmlOutput .= '<tbody>';
        foreach ($dataRows as $row) {
            $htmlOutput .= "<tr>
                                <td>{$row['tt_name']}</td>
                                <td>{$row['company_name']}</td>
                                <td>{$row['details']}</td>
                                <td>" . date('d M Y, h:i A', strtotime($row['complain_date'])) . "</td>
                             </tr>";
        }
        $htmlOutput .= '</tbody></table>';

        echo json_encode(['success' => true, 'html' => $htmlOutput]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false, 'error' => $db->error]);
}
    


} else {
    header("location: include/login.php");
}
?>