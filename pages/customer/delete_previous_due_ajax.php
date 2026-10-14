<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();



// Validation (optional but recommended)
if (!empty($_POST['customerId']) && !empty($_POST['amount']) && !empty($_POST['previous_due_id'])) {
    $ag_id = $_POST['customerId'];
    $previous_due_id = intval($_POST['previous_due_id']);
    $amount = floatval($_POST['amount']);
    $ag_id = intval($_POST['customerId']);
    $obj->rawSqlSingle("DELETE FROM previous_due_log WHERE id = '$previous_due_id'");
    $customer  = $obj->rawSqlSingle("SELECT tbl_agent.cus_id FROM tbl_agent WHERE ag_id = '$ag_id'");
    $obj->rawSqlSingle(
        "UPDATE customer_billing 
            SET previousdue =  previousdue - $amount,
            dueadvance = dueadvance - $amount 
            WHERE agid = $ag_id"
    );
    $obj->createActivityLog(
        $_SESSION['userid'],
        '4',
        '3',
        'bonus',
        $previous_due_id,
        null,
        $amount,
        'Deleted Discount of ' . $amount . ' Taka for customer ID: ' . $customer['cus_id'],
        null,
        null,
        false,
        false,
        null,
        $ag_id
    );


    $_SESSION['discount_tab'] = true;

    echo json_encode([
        "success" => true,
        'data' => $customer,
        'previous_due_id' => $previous_due_id,
        "message" => "delete Successfully"
    ]);
    exit();
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid Request"
    ]);
    exit();
}
