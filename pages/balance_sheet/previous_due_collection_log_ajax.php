<?php
session_start();
require(realpath(__DIR__ . '/../../services/Model.php'));
$obj = new Model();



$previous_due_history = $obj->rawSql("SELECT
    x.due_month,
    MONTHNAME(STR_TO_DATE(x.due_month, '%m')) AS month_name,
    SUM(x.previous_due) AS previous_due,
    SUM(x.collection) AS collection,
    SUM(LEAST(x.previous_due, x.collection)) AS previous_due_collection
FROM
(
    SELECT
        pd.tbl_agent_id,
        pd.due_month,
        pd.previous_due,
        COALESCE(ac.collection, 0) AS collection
    FROM
    (
        SELECT
            tbl_agent_id,
            MONTH(updated_at) AS due_month,
            SUM(previous_due_amount) AS previous_due
        FROM tbl_previous_due
        WHERE YEAR(updated_at) = 2026
        GROUP BY tbl_agent_id, MONTH(updated_at)
    ) pd

    LEFT JOIN
    (
        SELECT
            agent_id,
            MONTH(entry_date) AS collection_month,
            SUM(acc_amount) AS collection
        FROM tbl_account
        WHERE acc_type = 3
          AND YEAR(entry_date) = 2026
        GROUP BY agent_id, MONTH(entry_date)
    ) ac
      ON pd.tbl_agent_id = ac.agent_id
     AND pd.due_month = ac.collection_month
) x

GROUP BY x.due_month
ORDER BY x.due_month;");


// Return JSON response
echo json_encode([
    "data" => $previous_due_history
]);
exit();
