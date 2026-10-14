<?php

require '../vendor/autoload.php';
require('../services/Model.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$obj = new Model();

// ==========================
// Filters (demo / real)
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
// Base SQL query (same logic)
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
if (!empty($ag_status_filter)) {
    $map = ['active'=>1,'inactive'=>0,'free'=>2,'discontinue'=>3];
    if(isset($map[$ag_status_filter])) $where .= " AND vw_agent.ag_status='{$map[$ag_status_filter]}'";
}
if(!empty($dateFrom)) $where .= " AND connection_date >= '".$dateFrom."'";
if(!empty($dateTo)) $where .= " AND connection_date <= '".$dateTo."'";
if(!empty($search)){
    $s = $obj->escapeString($search);
    $where .= " AND (ag_name LIKE '%$s%' OR cus_id LIKE '%$s%' OR ip LIKE '%$s%' OR ag_mobile_no LIKE '%$s%')";
}

$sql = "SELECT vw_agent.*, tbl_zone.zone_name, _createuser.FullName 
        FROM vw_agent 
        LEFT JOIN tbl_zone ON vw_agent.zone = tbl_zone.zone_id 
        LEFT JOIN _createuser ON vw_agent.billing_person_id = _createuser.UserId
        $where 
        ORDER BY vw_agent.ag_id DESC";

// Pagination
$limit = ($per_page === 'all') ? 20 : (int)$per_page;
$page = max((int)$page, 1);
$offset = ($page - 1) * $limit;
$sql .= " LIMIT {$limit} OFFSET {$offset}";

// Fetch data
$data = $obj->rawSql($sql);

// ==========================
// PhpSpreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Customer Report');

// Header row
$headers = ['SL','ID','IP','Name','Address','Mobile','Package','Bill','Con. Date','Zone','B. Person','Status'];
$col = 'A';
foreach($headers as $h){
    $sheet->setCellValue($col.'1',$h);
    $col++;
}

// Data rows
$rowNum = 2;
$i = 1;
foreach($data as $row){
    $statusTxt = $row['ag_status']==1 ? 'Active' : ($row['ag_status']==0 ? 'Inactive' : ($row['ag_status']==2 ? 'Free' : 'Discontinue'));
    
    $sheet->setCellValue('A'.$rowNum, $i++);
    $sheet->setCellValue('B'.$rowNum, $row['cus_id']);
    $sheet->setCellValue('C'.$rowNum, $row['ip']);
    $sheet->setCellValue('D'.$rowNum, $row['ag_name']);
    $sheet->setCellValue('E'.$rowNum, $row['ag_office_address']);
    $sheet->setCellValue('F'.$rowNum, $row['ag_mobile_no']);
    $sheet->setCellValue('G'.$rowNum, $row['mb']);
    $sheet->setCellValue('H'.$rowNum, number_format($row['taka'],2));
    $sheet->setCellValue('I'.$rowNum, date('d-M-Y', strtotime($row['connection_date'])));
    $sheet->setCellValue('J'.$rowNum, $row['zone_name']);
    $sheet->setCellValue('K'.$rowNum, $row['FullName']);
    $sheet->setCellValue('L'.$rowNum, $statusTxt);
    $rowNum++;
}

// Auto column width
foreach(range('A','L') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Output Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Customer_Report.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;