<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

$path = realpath(__DIR__ . '/../../services/Model.php');
if(!$path){
  echo json_encode([
    "draw"=>1,"recordsTotal"=>0,"recordsFiltered"=>0,"data"=>[],
    "error"=>"Model.php not found",
    "debug_path"=>__DIR__ . '/../../services/Model.php'
  ],);
  exit;
}
require_once $path;
$obj = new Model();

$draw   = (int)($_GET['draw'] ?? 1);
$start  = (int)($_GET['start'] ?? 0);
$length = (int)($_GET['length'] ?? 10);

$searchValue = $_GET['search']['value'] ?? '';
$datefrom = $_GET['datefrom'] ?? '';
$dateto   = $_GET['dateto'] ?? '';
$entryby  = $_GET['entryby'] ?? '';

$where = "WHERE 1=1";

// Date filter
if (!empty($datefrom) && !empty($dateto)) {
  $datefrom = addslashes($datefrom);
  $dateto   = addslashes($dateto);
  $where .= " AND DATE(b.date) BETWEEN '$datefrom' AND '$dateto'";
}

// EntryBy filter (bonus.entryby -> _createuser.UserId)
if ($entryby !== '') {
  $entryby = (int)$entryby;
  $where .= " AND b.entryby = $entryby";
}

// Search filter
if ($searchValue !== '') {
  $s = addslashes($searchValue);
  $where .= " AND (
    cus.ag_name LIKE '%$s%' OR
    cus.ag_mobile_no LIKE '%$s%' OR
    cus.ag_email LIKE '%$s%' OR
    cu.FullName LIKE '%$s%' OR
    cu.MobileNo LIKE '%$s%' OR
    b.description LIKE '%$s%' OR
    b.amount LIKE '%$s%' OR
    b.date LIKE '%$s%'
  )";
}

// Total records
$totalRecordsRow = $obj->rawSqlSingle("SELECT COUNT(*) AS cnt FROM bonus");
$totalRecords = (int)($totalRecordsRow['cnt'] ?? 0);

// Filtered records
$totalFilteredRow = $obj->rawSqlSingle("
  SELECT COUNT(*) AS cnt
  FROM bonus b
  LEFT JOIN tbl_agent cus  ON b.ag_id = cus.ag_id
  LEFT JOIN _createuser cu ON b.entryby = cu.UserId
  $where
");
$totalFiltered = (int)($totalFilteredRow['cnt'] ?? 0);

// Business order: Active first then amount desc then latest
$orderBy = "
  ORDER BY
    CASE WHEN cus.ag_status IN (1,2) THEN 0 ELSE 1 END ASC,
    b.amount DESC,
    b.date DESC,
    b.id DESC
";

// Rows
$rows = $obj->rawSql("
  SELECT
    b.id, b.date, b.amount, b.description,
    cus.ag_name AS customer_name,
    cus.ag_mobile_no AS customer_mobile,
    cus.ag_status AS customer_status,
    cu.FullName AS given_by_name
  FROM bonus b
  LEFT JOIN tbl_agent cus  ON b.ag_id = cus.ag_id
  LEFT JOIN _createuser cu ON b.entryby = cu.UserId
  $where
  $orderBy
  LIMIT $start, $length
");

// Totals (filtered)
$totalDiscountRow = $obj->rawSqlSingle("
  SELECT COALESCE(SUM(b.amount),0) AS totalDiscount
  FROM bonus b
  LEFT JOIN tbl_agent cus  ON b.ag_id = cus.ag_id
  LEFT JOIN _createuser cu ON b.entryby = cu.UserId
  $where
");
$totalDiscount = $totalDiscountRow['totalDiscount'] ?? 0;

$totalRecipientsRow = $obj->rawSqlSingle("
  SELECT COUNT(DISTINCT b.ag_id) AS totalRecipients
  FROM bonus b
  LEFT JOIN tbl_agent cus  ON b.ag_id = cus.ag_id
  LEFT JOIN _createuser cu ON b.entryby = cu.UserId
  $where
");
$totalRecipients = $totalRecipientsRow['totalRecipients'] ?? 0;

// Data format
$data = [];
$sl = $start + 1;
foreach ($rows as $r) {
  $data[] = [
    'sl' => $sl++,
    'date' => $r['date'] ?? 'N/A',
    'customer_name' => $r['customer_name'] ?? 'N/A',
    'customer_mobile' => $r['customer_mobile'] ?? 'N/A',
    'amount' => $r['amount'] ?? 0,
    'description' => $r['description'] ?? '',
    'given_by_name' => $r['given_by_name'] ?? 'N/A',
    'customer_status' => $r['customer_status'] ?? 0
  ];
}

echo json_encode([
  'draw' => $draw,
  'recordsTotal' => $totalRecords,
  'recordsFiltered' => $totalFiltered,
  'data' => $data,
  'totalDiscount' => $totalDiscount,
  'totalRecipients' => $totalRecipients
], JSON_UNESCAPED_UNICODE);
exit;
