<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();

// Validate dates (Y-m-d), default today
$isValidDate = function ($d) {
    $dt = DateTime::createFromFormat('Y-m-d', $d);
    return $dt && $dt->format('Y-m-d') === $d;
};
$fromDate = isset($_GET['from']) && $isValidDate($_GET['from']) ? $_GET['from'] : date('Y-m-d');
$toDate   = isset($_GET['to']) && $isValidDate($_GET['to']) ? $_GET['to'] : date('Y-m-d');
if ($fromDate > $toDate) {
    [$fromDate, $toDate] = [$toDate, $fromDate];
}

// Customer collections only (exclude expense = 1 and 22), grouped by collecting user
$sql = "
    SELECT
        acc.entry_by,
        COALESCE(u.FullName, 'Unknown') AS user_name,
        COUNT(DISTINCT acc.agent_id) AS total_customers,
        COUNT(acc.acc_id) AS total_transactions,
        SUM(CASE WHEN acc.acc_type = 3 THEN acc.acc_amount ELSE 0 END) AS bill_collection,
        SUM(acc.acc_amount) AS total_amount
    FROM tbl_account acc
    LEFT JOIN _createuser u ON acc.entry_by = u.UserId
    WHERE acc.acc_type NOT IN (1, 22)
      AND acc.agent_id IS NOT NULL AND acc.agent_id > 0
      AND DATE(acc.entry_date) BETWEEN '$fromDate' AND '$toDate'
    GROUP BY acc.entry_by, u.FullName
    ORDER BY total_amount DESC
";

$rows = $obj->rawSql($sql);

$data = [];
$summary = ['users' => 0, 'customers' => 0, 'transactions' => 0, 'amount' => 0];
$i = 1;
foreach ($rows as $row) {
    $data[] = [
        'sl' => $i++,
        'user_name' => $row['user_name'],
        'total_customers' => (int) $row['total_customers'],
        'total_transactions' => (int) $row['total_transactions'],
        'bill_collection' => (float) $row['bill_collection'],
        'total_amount' => (float) $row['total_amount'],
    ];
    $summary['users']++;
    $summary['customers'] += (int) $row['total_customers'];
    $summary['transactions'] += (int) $row['total_transactions'];
    $summary['amount'] += (float) $row['total_amount'];
}

header('Content-Type: application/json');
echo json_encode([
    'from' => $fromDate,
    'to' => $toDate,
    'data' => $data,
    'summary' => $summary,
]);
exit();
