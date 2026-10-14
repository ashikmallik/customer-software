<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));

$obj = new Model();

// Ajax POST data
$year = $_POST['year'] ?? '';
$month = $_POST['month'] ?? '';
$start = $_POST['start'] ?? 0;
$length = $_POST['length'] ?? 10;

try {
    // Base query
    $query = "SELECT 
              p.ag_id, 
              p.prevous_due_amount, 
              p.created_at, 
              a.ag_name as agent_name, 
              a.cus_id as cus
          FROM previous_due_log p
          LEFT JOIN tbl_agent a ON a.ag_id = p.ag_id
          WHERE p.prevous_due_amount > 0";

    // Year & Month filter
    if(!empty($year)) {
        $query .= " AND YEAR(p.created_at) = '$year'";
    }
    if(!empty($month)) {
        $query .= " AND MONTH(p.created_at) = '$month'";
    }

    // Total count for DataTable
    $totalQuery = "SELECT COUNT(*) as total FROM previous_due_log p 
                   LEFT JOIN tbl_agent a ON a.ag_id = p.ag_id
                   WHERE 1=1";
    if(!empty($year)) $totalQuery .= " AND YEAR(p.created_at) = '$year'";
    if(!empty($month)) $totalQuery .= " AND MONTH(p.created_at) = '$month'";
    
    $totalRecords = $obj->raw_sql($totalQuery)[0]['total'] ?? 0;

    // Limit for pagination
    $query .= " ORDER BY p.created_at DESC LIMIT $start, $length";
    $data = $obj->raw_sql($query);

    // Summary
    $summaryQuery = "SELECT COUNT(*) as total_customers, SUM(prevous_due_amount) as total_due
                     FROM previous_due_log p
                     LEFT JOIN tbl_agent a ON a.ag_id = p.ag_id WHERE 1=1";
    if(!empty($year)) $summaryQuery .= " AND YEAR(p.created_at) = '$year'";
    if(!empty($month)) $summaryQuery .= " AND MONTH(p.created_at) = '$month'";
    
    $summary = $obj->raw_sql($summaryQuery)[0];

    echo json_encode([
        "draw" => $_POST['draw'] ?? 1,
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $totalRecords,
        "data" => $data,
        "summary" => $summary
    ]);

} catch (Exception $e) {
    echo json_encode([
        "draw" => $_POST['draw'] ?? 1,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "summary" => [
            "total_customers" => 0,
            "total_due" => 0
        ],
        "error" => $e->getMessage()
    ]);
}