<?php
error_reporting(0);
ini_set('display_errors', 0);
require_once __DIR__ . '/../../pages/pdf/vendor/autoload.php';
require('../../services/Model.php');
$obj = new Model();
use Mpdf\Mpdf;

// ==========================
// Filters নিন
// ==========================
$zone     = $_GET['zone'] ?? '';
$bp       = $_GET['billing_person'] ?? '';
$package  = $_GET['package'] ?? '';
$status   = $_GET['status'] ?? '';
$ag_status_filter   = $_GET['ag_status_filter'] ?? '';
$dateFrom = $_GET['date_from'] ?? '';
$dateTo   = $_GET['date_to'] ?? '';
$search   = $_GET['search'] ?? '';
$per_page = $_GET['per_page'] ?? 20;
$page     = $_GET['pg'] ?? 1;

// ==========================
// WHERE condition
// ==========================
$where = " WHERE vw_agent.deleted_at IS NULL ";
if (!empty($_GET['zone'])) {
    $where .= " AND zone = $zone";
}
if (!empty($_GET['sub_zone'])) {
    $subzone = intval($_GET['sub_zone']);
    $where .= " AND sub_zone = $subzone";
}
if (!empty($bp)) $where .= " AND billing_person_id = " . intval($bp);
if (!empty($package)) $where .= " AND mb = '".$package."'";
if ($status !== '') $where .= " AND ag_status = '".intval($status)."'";

// if ($ag_status_filter !== '') $where .= " AND ag_status = '".intval($status)."'";

if (!empty($ag_status_filter)) {
    $map = [
        'active' => 1,
        'inactive' => 0,
        'free' => 2,
        'discontinue' => 3
    ];
    if (isset($map[$_GET['ag_status_filter']])) {
        $where .= " AND vw_agent.ag_status = '{$map[$ag_status_filter]}'";
    }
}

if (!empty($dateFrom)) $where .= " AND connection_date >= '".$dateFrom."'";
if (!empty($dateTo)) $where .= " AND connection_date <= '".$dateTo."'";
if (!empty($search)) {
    $s = $obj->escapeString($search);
    $where .= " AND (
        ag_name LIKE '%$s%' OR 
        cus_id LIKE '%$s%' OR 
        ip LIKE '%$s%' OR 
        ag_mobile_no LIKE '%$s%'
    )";
}

// ==========================
// Base query
// ==========================
$sql = "SELECT vw_agent.*, tbl_zone.zone_name, _createuser.FullName 
        FROM vw_agent 
        LEFT JOIN tbl_zone ON vw_agent.zone = tbl_zone.zone_id 
        LEFT JOIN _createuser ON vw_agent.billing_person_id = _createuser.UserId
        $where 
        ORDER BY vw_agent.ag_id DESC";

// ==========================
// Pagination (Always current page)
// ==========================
$limit = ($per_page === 'all') ? 20 : (int)$per_page; // যদি ভুলে 'all' যায় default 20
$page = max((int)$page, 1);
$offset = ($page - 1) * $limit;

$sql .= " LIMIT {$limit} OFFSET {$offset}";

// ==========================
// Data আনুন
// ==========================
$data = $obj->rawSql($sql);

// ==========================
// HTML টেবিল তৈরি
// ==========================
$html = '
<h2 style="text-align:center">Customer Report</h2>
<table border="1" cellpadding="6" cellspacing="0" width="100%">
<thead>
<tr>
    <th>SL</th>
    <th>ID</th>
    <th>IP</th>
    <th>Name</th>
    <th>Address</th>
    <th>Mobile</th>
    <th>Package</th>
    <th>Bill</th>
    <th>Con. Date</th>
    <th>Zone</th>
    <th>B. Person</th>
    <th>Status</th>
</tr>
</thead>
<tbody>';

$i = 1;
foreach($data as $row){
    $statusTxt = $row['ag_status']==1 ? 'Active' : ($row['ag_status']==0 ? 'Inactive' : ($row['ag_status']==2 ? 'Free' : 'Discontinue'));
    $html .= "<tr>
        <td>".$i++."</td>
        <td>".$row['cus_id']."</td>
        <td>".$row['ip']."</td>
        <td>".$row['ag_name']."</td>
        <td>".$row['ag_office_address']."</td>
        <td>".$row['ag_mobile_no']."</td>
        <td>".$row['mb']."</td>
        <td>".number_format($row['taka'],2)."</td>
        <td>".date('d-M-Y', strtotime($row['connection_date']))."</td>
        <td>".$row['zone_name']."</td>
        <td>".$row['FullName']."</td>
        <td>".$statusTxt."</td>
    </tr>";
}

$html .= "</tbody></table>";

// ==========================
// PDF আউটপুট
// ==========================
$mpdf = new Mpdf();
$mpdf->WriteHTML($html);
$mpdf->Output("Customer_Report.pdf","I");
