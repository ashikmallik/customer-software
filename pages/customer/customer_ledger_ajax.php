<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();




if (isset($_GET["token"])) {

    $token    = (int) $_GET['token'];
    $dateFrom = $_GET['date_from'] ?? '';
    $dateTo   = $_GET['date_to'] ?? '';

    $wherecond = '';
    if (!empty($dateFrom) && !empty($dateTo)) {
        $wherecond = " AND account.entry_date BETWEEN '$dateFrom' AND '$dateTo'";
    }

    $agentInfo = $obj->rawSqlSingle("
        SELECT *
        FROM tbl_agent
        LEFT JOIN tbl_zone ON tbl_agent.zone = tbl_zone.zone_id
        WHERE ag_id = $token
    ");

    $billing_info = $obj->rawSqlSingle("
        SELECT *
        FROM customer_billing
        WHERE agid = $token
    ");

    $accountsHistory = $obj->raw_sql("
        SELECT 
            account.acc_id,
            account.acc_amount,
            account.acc_description,
            account.created_at,
            account.entry_date,
            account.entry_by,
            user.FullName AS received_by,
            account.acc_type
        FROM tbl_account AS account
        LEFT JOIN _createuser AS user ON user.UserId = account.entry_by
        WHERE account.agent_id = $token
        $wherecond
        ORDER BY account.entry_date DESC
    ");

    $totalAmount = $obj->rawSqlSingle("
        SELECT SUM(account.acc_amount) AS totalAmount
        FROM tbl_account AS account
        WHERE agent_id = $token
        $wherecond
    ");
}


$customer_billing_history = [];

foreach ($accountsHistory as $key => $history) {
    $customer_billing_history[] = [
        "sl" => $key + 1,
        "date" => $formattedDate = date("Y-m-d h:i:s A", strtotime($history["created_at"])),
        "description" => $history["acc_description"],
        "amount" => $history["acc_amount"],
        "received_by" => $history["received_by"],
        "acc_id" => $history["acc_id"],
        "acc_type" => $history["acc_type"],
    ];
}


// Return JSON response
echo json_encode([
    "data" => $customer_billing_history,
    "totalAmount" => $totalAmount["totalAmount"],
    "history" => $accountsHistory,
    "billing_info" => $billing_info,
    "agentInfo" => $agentInfo
]);
exit();
