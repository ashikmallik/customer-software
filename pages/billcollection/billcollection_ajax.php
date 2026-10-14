<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();

$where = [];
$wherecond = '';
$allAgentData = [];
$total_bill = $totalconnectionFee = $totalcustomer = 0;
$i = 1;

// Apply filters from AJAX request
if (isset($_GET['zone']) && !empty($_GET['zone'])) {
    $zone = $_GET['zone'];
    $wherecond .= " AND vw_agent.zone = $zone";
}
if (isset($_GET['sub_zone']) && !empty($_GET['sub_zone'])) {
    $sub_zone = $_GET['sub_zone'];
    $wherecond .= " AND vw_agent.sub_zone = $sub_zone";
}
if (isset($_GET['bid']) && !empty($_GET['bid'])) {
    $bid = $_GET['bid'];
    $wherecond .= " AND vw_agent.billing_person_id = $bid";
}
if (isset($_GET['status']) && $_GET['status'] !== '') {
    $status = $_GET['status'];
    $wherecond .= " AND vw_agent.bill_status = $status";
}
if (isset($_GET['datefrom']) && isset($_GET['dateto']) && !empty($_GET['datefrom']) && !empty($_GET['dateto'])) {
    $dateFrom = date('d', strtotime($_GET['datefrom']));
    $dateTo = date('d', strtotime($_GET['dateto']));
    $wherecond .= " AND ((vw_agent.bill_date >= $dateFrom) AND (vw_agent.bill_date <= $dateTo))";
}

if (isset($_GET['disconnectdatefrom']) && isset($_GET['disconnectdateto']) && !empty($_GET['disconnectdatefrom']) && !empty($_GET['disconnectdateto'])) {
    $disconnectdatefrom = date('d', strtotime($_GET['disconnectdatefrom']));
    $disconnectdateto = date('d', strtotime($_GET['disconnectdateto']));
    $wherecond .= " AND ((vw_agent.mikrotik_disconnect >= $disconnectdateto) AND (vw_agent.mikrotik_disconnect <= $disconnectdatefrom))";
}
// ====== New Filter: disconnect_day (from calendar) ======
if (isset($_GET['disconnect_day']) && $_GET['disconnect_day'] !== '') {
    $disconnectDay = (int)$_GET['disconnect_day'];
    if ($disconnectDay >= 1 && $disconnectDay <= 31) {
        $wherecond .= " AND CAST(vw_agent.mikrotik_disconnect AS UNSIGNED) = $disconnectDay";
    }
}

//heck entry oparetor wise data
$entry_oparetor_id = $_SESSION['userid'];

// if($_SESSION['usertype'] == 'EO'){
//     $wherecond .= " AND vw_agent.billing_person_id = $entry_oparetor_id";
// }

// if (isset($_GET['datefrom']) && !empty($_GET['datefrom'])) {
//     $dateFrom = date('d', strtotime($_GET['datefrom']));
//     $wherecond .= " AND mikrotik_disconnect = $dateFrom";
// }
$order_column = $_GET['order'][0]['column'] ?? '';  // Column index
$order_dir = $_GET['order'][0]['dir'] ?? '';  // Order direction (asc or desc)
$columns = [
    'ag_id',
    'ag_id',
    'cus_id',
    'ip',
    'ag_name',
    'ag_office_address',
    'ag_mobile_no',
    'mb',
    'taka',
    'dueadvance',
    'bill_date',
    'mikrotik_disconnect',
    'zone_name',
    'FullName',
    'bill_status',
];
// Construct the ORDER BY clause dynamically
if (isset($order_column) && isset($columns[$order_column])) {
    if ($order_column == "14") {
        // If the column is FullName, order by created_by (or the corresponding column)
        $order_by = "vw_agent.billing_person_id " . $order_dir;
    }
    elseif ($order_column == "13") {
        // If the column is FullName, order by created_by (or the corresponding column)
        $order_by = "vw_agent.zone " . $order_dir;
    }
    elseif ($order_column == 11) {

    $smartDate = "
        CASE 
            WHEN vw_agent.mikrotik_disconnect < DAY(CURDATE())
                THEN STR_TO_DATE(
                    CONCAT(vw_agent.mikrotik_disconnect, '-', DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 MONTH), '%m-%Y')),
                    '%d-%m-%Y'
                )
            ELSE STR_TO_DATE(
                    CONCAT(vw_agent.mikrotik_disconnect, '-', DATE_FORMAT(CURDATE(), '%m-%Y')),
                    '%d-%m-%Y'
                )
        END
    ";

    $order_by = "
        CASE 
            WHEN $smartDate < CURDATE() THEN 1  -- expired bottom
            ELSE 0                              -- upcoming top
        END ASC,
        $smartDate $order_dir
    ";
}
elseif ($order_column == "9") {
        // If the column is FullName, order by created_by (or the corresponding column)
        $order_by = "customer_billing.dueadvance " . $order_dir;
    } else {
        // Else use the valid column name from the array
        $order_by = "vw_agent." . $columns[$order_column] . ' ' . $order_dir;
    }
} else {
    // Default order by id descending
    $order_by = "vw_agent.ag_id DESC";
}

// Apply search filter (if search is provided)
$search = $_GET['search']['value'] ?? '';
if (!empty($search)) {
    $wherecond .= " AND (
        vw_agent.cus_id LIKE '%$search%' OR 
        vw_agent.ip LIKE '%$search%' OR 
        vw_agent.mb LIKE '%$search%' OR 
        vw_agent.ag_mobile_no LIKE '%$search%' OR 
        vw_agent.taka LIKE '%$search%' OR
        customer_billing.dueadvance LIKE '%$search%' OR
        vw_agent.ag_name LIKE '%$search%' OR 
        vw_agent.ag_office_address LIKE '%$search%' OR
        tbl_zone.zone_name LIKE '%$search%' OR
        _createuser.FullName LIKE '%$search%' 
    )";
}
// var_dump($where);

// Pagination (start and length)
$start = $_GET['start'] ?? 0;
$length = $_GET['length'] ?? 10;

// Fetch filtered data with pagination
$allData = $obj->rawSql(
    "SELECT 
        vw_agent.*, 
        ROW_NUMBER() OVER (ORDER BY $order_by) AS sl,
        DATE_FORMAT(STR_TO_DATE(CONCAT(vw_agent.bill_date, '-', DATE_FORMAT(CURDATE(), '%m-%Y')), '%d-%m-%Y'), '%d-%m-%Y') AS bill_date,
        DATE_FORMAT(STR_TO_DATE(CONCAT(vw_agent.mikrotik_disconnect, '-', DATE_FORMAT(CURDATE(), '%m-%Y')), '%d-%m-%Y'), '%d-%m-%Y') AS mikrotik_disconnect,
        customer_billing.dueadvance,
        tbl_zone.zone_name,
        _createuser.FullName 
    FROM vw_agent 
    left join customer_billing on customer_billing.agid = vw_agent.ag_id 
    left join _createuser ON vw_agent.billing_person_id = _createuser.UserId 
    left join tbl_zone on tbl_zone.zone_id = vw_agent.zone 
    WHERE vw_agent.deleted_at is NULL 
        AND customer_billing.dueadvance > 0 
        AND vw_agent.ag_status = 1 $wherecond 
    ORDER BY $order_by 
    LIMIT $length OFFSET $start;"
);

$totalData = $obj->rawSqlSingle(
    "SELECT COUNT(*) AS total 
    FROM vw_agent 
    left join customer_billing on customer_billing.agid = vw_agent.ag_id 
    WHERE vw_agent.deleted_at is NULL 
    AND customer_billing.dueadvance > 0 
    AND vw_agent.ag_status = 1"
)["total"];

$totalFiltered = $obj->rawSqlSingle(
    "SELECT COUNT(*) AS total
    FROM vw_agent 
    left join customer_billing on customer_billing.agid = vw_agent.ag_id 
    left join _createuser ON vw_agent.billing_person_id = _createuser.UserId 
    left join tbl_zone on tbl_zone.zone_id = vw_agent.zone 
    WHERE vw_agent.deleted_at is NULL 
        AND customer_billing.dueadvance > 0 
        AND vw_agent.ag_status = 1 
        $wherecond"
)["total"];

// Fetch filtered data with pagination
$totalBillSummary = $obj->rawSqlSingle(
    "SELECT 
            SUM(customer_billing.dueadvance) AS dueAdvacne,
            SUM(IF(customer_billing.dueadvance > vw_agent.taka, vw_agent.taka, customer_billing.dueadvance)) AS runningMonthDue,
            SUM(IF(customer_billing.dueadvance > vw_agent.taka, (customer_billing.dueadvance - vw_agent.taka), 0)) AS totalPreviousDue,
            COUNT(*) AS totalDueCustomers
        FROM vw_agent 
        left join customer_billing on customer_billing.agid = vw_agent.ag_id 
        left join _createuser ON vw_agent.billing_person_id = _createuser.UserId 
        left join tbl_zone on tbl_zone.zone_id = vw_agent.zone WHERE vw_agent.deleted_at is NULL AND customer_billing.dueadvance > 0 AND vw_agent.ag_status = 1 $wherecond;"
);

// Return JSON response for DataTable
echo json_encode([
    "draw" => intval($_GET['draw']),
    "recordsTotal" => $totalData,
    "recordsFiltered" => $totalFiltered,
    "data" => $allData,
    "totalbill" => $total_bill,
    "totalconnectionFee" => $totalconnectionFee,
    'totalDueAdvanceBill' => $totalBillSummary
], JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
exit();
