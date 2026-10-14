<?php
session_start();
header('Content-Type: application/json');
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();

/* ================= Get Filters ================= */
$zone              = $_GET['zone'] ?? '';
$billing_person    = $_GET['bid'] ?? '';
$collection_person = $_GET['cid'] ?? '';
$dateft            = $_GET['date'] ?? '';
$typeid            = $_GET['typeid'] ?? '';
$pay_method        = $_GET['pay_method'] ?? '';

$wherecond = " WHERE 1 ";

/* ================= Apply Filters ================= */
if ($zone != '')              $wherecond .= " AND tbl_agent.zone = '$zone'";
if ($billing_person != '')    $wherecond .= " AND tbl_agent.billing_person_id = '$billing_person'";
if ($collection_person != '') $wherecond .= " AND tbl_account.entry_by = '$collection_person'";
if ($pay_method != '')        $wherecond .= " AND tbl_account.payment_method = '$pay_method'";

if ($typeid != '') {
    if ($typeid == 5) {
        $wherecond .= " AND tbl_account.acc_type IN (4,5)";
    } elseif ($typeid == 1) {
        $wherecond .= " AND tbl_account.acc_type IN (1,22)";
    } else {
        $wherecond .= " AND tbl_account.acc_type = '$typeid'";
    }
}

/* ================= Date Filter ================= */
$dateFrom = date('Y-m-d');
$dateTo   = date('Y-m-d');

if ($dateft != '') {
    $datearay = explode('/', $dateft);
    $dateFrom = date('Y-m-d', strtotime($datearay[0]));
    $dateTo   = date('Y-m-d', strtotime($datearay[1]));
}

/* ================= Opening Balance ================= */
$openingBalanceData = $obj->rawSqlSingle("
    SELECT 
        SUM(
            CASE 
                WHEN tbl_account.acc_type IN (1,22) 
                THEN -tbl_account.acc_amount 
                ELSE tbl_account.acc_amount 
            END
        ) AS openingBalance
    FROM tbl_account
    LEFT JOIN tbl_agent ON tbl_agent.ag_id = tbl_account.agent_id
    $wherecond
    AND tbl_account.entry_date < '$dateFrom'
")['openingBalance'] ?? 0;

/* ================= Ledger Data ================= */
$allData = $obj->rawSql("
    SELECT 
        tbl_account.*,
        tbl_agent.ag_name,
        tbl_agent.cus_id,
        tbl_agent.ip,
        tbl_zone.zone_name,
        tbl_accounts_head.acc_name,
        _createuser.FullName
    FROM tbl_account
    LEFT JOIN tbl_agent ON tbl_agent.ag_id = tbl_account.agent_id
    LEFT JOIN tbl_zone ON tbl_zone.zone_id = tbl_agent.zone
    LEFT JOIN tbl_accounts_head ON tbl_accounts_head.acc_id = tbl_account.acc_head
    LEFT JOIN _createuser ON _createuser.UserId = tbl_account.entry_by
    $wherecond
    AND tbl_account.entry_date BETWEEN '$dateFrom' AND '$dateTo'
    ORDER BY tbl_account.entry_date ASC, tbl_account.acc_id ASC
");

/* ================= Total Credit ================= */
$totalCredits = $obj->rawSqlSingle("
    SELECT SUM(tbl_account.acc_amount) AS totalCredits
    FROM tbl_account
    LEFT JOIN tbl_agent ON tbl_agent.ag_id = tbl_account.agent_id
    $wherecond
    AND tbl_account.acc_type NOT IN (1,22)
    AND tbl_account.entry_date BETWEEN '$dateFrom' AND '$dateTo'
")['totalCredits'] ?? 0;

/* ================= Total Debit ================= */
$totalDebits = $obj->rawSqlSingle("
    SELECT SUM(tbl_account.acc_amount) AS totalDebits
    FROM tbl_account
    LEFT JOIN tbl_agent ON tbl_agent.ag_id = tbl_account.agent_id
    $wherecond
    AND tbl_account.acc_type IN (1,22)
    AND tbl_account.entry_date BETWEEN '$dateFrom' AND '$dateTo'
")['totalDebits'] ?? 0;

/* ================= Prepare Ledger ================= */
$balance = $openingBalanceData;
$i = 1;
$allAgentData = [];

/* ===== Opening Balance Row ===== */
$openingDebit  = $openingBalanceData < 0 ? abs($openingBalanceData) : 0;
$openingCredit = $openingBalanceData > 0 ? $openingBalanceData : 0;

$allAgentData[] = [
    'sl' => '',
    'entry_date' => '',
    'acc_description' => '',
    'acchead' => '<b>Balance BD</b>',
    'FullName' => '',
    'debit' => '<b>' .number_format($openingDebit, 2). '<b>',
    'credit' => '<b>' . number_format($openingCredit, 2). '<b>',
    'balance' => '<b>' . number_format($balance, 2) . '</b>'
];

/* ===== Ledger Rows ===== */
foreach ($allData as $row) {

    $debit = 0;
    $credit = 0;

    if ($row['acc_type'] == 1 || $row['acc_type'] == 22) {
        $debit = $row['acc_amount'];
        $balance -= $debit;
    } else {
        $credit = $row['acc_amount'];
        $balance += $credit;
    }

    $cus_id = $row['cus_id'] ? ' (ID: ' . $row['cus_id'] . ')' : '';
    $ip     = $row['ip'] ? ' (IP: ' . $row['ip'] . ')' : '';

    $allAgentData[] = [
        'sl' => $i++,
        'entry_date' => $row['entry_date'],
        'acc_description' => $row['acc_description'],
        'acchead' => ($row['agent_id'] > 0)
            ? $row['ag_name'] . $cus_id . $ip
            : $row['acc_name'],
        'FullName' => $row['FullName'],
        'debit' => number_format($debit, 2),
        'credit' => number_format($credit, 2),
        'balance' => number_format($balance, 2)
    ];
}

/* ===== Closing Balance Row ===== */
$allAgentData[] = [
    'sl' => '',
    'entry_date' => '',
    'acc_description' => '',
    'acchead' => '<b>Closing Balance</b>',
    'FullName' => '',
    'debit' => '',
    'credit' => '',
    'balance' => '<b>' . number_format($balance, 2) . '</b>'
];

/* ================= JSON Response ================= */
echo json_encode([
    'recordsTotal' => count($allAgentData),
    'recordsFiltered' => count($allAgentData),
    'data' => $allAgentData,
    'totalCredits' => number_format($totalCredits, 2),
    'totalDebits' => number_format($totalDebits, 2)
], JSON_PRETTY_PRINT);

exit();
