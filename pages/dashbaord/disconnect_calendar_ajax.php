<?php
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();

header('Content-Type: application/json');

$todayDate = date('Y-m-d');
$startDate = date('Y-m-d', strtotime('-5 days'));
$endDate   = date('Y-m-d', strtotime('+24 days'));

$rows = $obj->rawSql(
    "SELECT 
        CAST(vw_agent.mikrotik_disconnect AS UNSIGNED) AS day_num,
        COUNT(*) AS total
     FROM vw_agent
     LEFT JOIN customer_billing ON customer_billing.agid = vw_agent.ag_id
     WHERE vw_agent.deleted_at IS NULL
       AND vw_agent.ag_status = 1
       AND customer_billing.dueadvance > 0
       AND vw_agent.mikrotik_disconnect IS NOT NULL
       AND vw_agent.mikrotik_disconnect != ''
       AND CAST(vw_agent.mikrotik_disconnect AS UNSIGNED) BETWEEN 1 AND 31
     GROUP BY CAST(vw_agent.mikrotik_disconnect AS UNSIGNED)
     ORDER BY day_num ASC"
);

$billingData = [];
foreach ($rows as $r) {
    $billingData[(int)$r['day_num']] = (int)$r['total'];
}

// Date range diye days array banai
$days = [];
$cur = strtotime($startDate);
$end = strtotime($endDate);
while ($cur <= $end) {
    $d = (int)date('d', $cur);
    $days[] = [
        'date'     => date('Y-m-d', $cur),
        'day'      => $d,
        'weekday'  => (int)date('w', $cur),
        'count'    => $billingData[$d] ?? 0,
        'is_today' => date('Y-m-d', $cur) === $todayDate,
    ];
    $cur = strtotime('+1 day', $cur);
}

$startMonth = date('F Y', strtotime($startDate));
$endMonth   = date('F Y', strtotime($endDate));

echo json_encode([
    'month_title' => $startMonth === $endMonth ? $startMonth : "$startMonth - $endMonth",
    'range_label' => date('d M Y', strtotime($startDate)) . ' — ' . date('d M Y', strtotime($endDate)),
    'total_due'   => array_sum($billingData),
    'days'        => $days,
]);