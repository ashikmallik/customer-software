<?php
header("Access-Control-Allow-Origin: *"); // সব ডোমেইন থেকে অ্যাকসেস অনুমোদন
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$day = date('d');

$dd = date('d');
$m = date('m');
$y = date('Y');

require(__DIR__ . '/services/Model.php');


$obj = new Model();
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET' && isset($_GET['notice_info'])) {
    $data = $obj->rawSqlSingle("SELECT * FROM `countdown_notice` WHERE 1");
    echo json_encode($data);
}

$input = json_decode(file_get_contents("php://input"), true);
if ($method == 'POST' && isset($input['UPDATE_NOTICE'])) {
    $data = $obj->rawSqlSingle(
        "UPDATE countdown_notice 
        SET 
        bill_date = '$input[bill_date]',
        bill_month = '$input[bill_month]',
        bill_year = '$input[bill_year]',
        grace_period = '$input[grace_period]',
        notice_display_before = '$input[notice_display_before]',
        notice_displayable = '$input[notice_displayable]',
        forcefully_stop = '$input[forcefully_stop]',
        permanently_stop = '$input[permanently_stop]',
        complete_shutdown = '$input[complete_shutdown]',
        cus_limit = '$input[cus_limit]',
        dueadvance = '$input[dueadvance]',
        notice = '$input[notice]' 
        WHERE id = '1'
        "
    );
    echo true;
}


if ($method == 'POST' && isset($input['UPDATE_MONTHLY_BILL'])) {
    $data = $obj->rawSqlSingle(
        "UPDATE countdown_notice 
        SET 
        monthly_bill = '$input[monthly_bill]'
        WHERE id = '1'
        "
    );
    echo true;
}


// if ($method == 'POST' && isset($input['HIDE_NOTICE'])) {
//     $data = $obj->rawSqlSingle(
//         "UPDATE countdown_notice 
//         SET 
//         notice_displayable = '2',
//         complete_shutdown = '2',
//         dueadvance = '$input[dueadvance]'
//         WHERE id = '1'
//         "
//     );
//     echo true;
// }


// if ($method == 'POST' && isset($input['HIDE_NOTICE'])) {
//     $noticeInfo = $obj->rawSqlSingle("SELECT monthly_bill, bill_month, bill_year FROM countdown_notice WHERE id = 1");

//     $monthly_bill = (float)$noticeInfo['monthly_bill'];
//     $current_month = (int)$noticeInfo['bill_month'];
//     $current_year = (int)$noticeInfo['bill_year'];

//     $paidAmount = (float)$input['paidAmount'];

//     $months_paid = floor($paidAmount / $monthly_bill);
//     $new_month = $current_month + $months_paid;
//     $new_year = $current_year;

//     if ($new_month > 12) {
//         $extra_years = floor(($new_month - 1) / 12);
//         $new_month = ($new_month - 1) % 12 + 1;
//         $new_year += $extra_years;
//     }

//     $obj->rawSqlSingle("
//         UPDATE countdown_notice 
//         SET 
//             notice_displayable = '2',
//             complete_shutdown = '2',
//             dueadvance = '{$input['dueadvance']}',
//             bill_month = '$new_month',
//             bill_year = '$new_year'
//         WHERE id = '1'
//     ");

//     echo true;
// }


if ($method == 'POST' && isset($input['HIDE_NOTICE'])) {
    $noticeInfo = $obj->rawSqlSingle("SELECT monthly_bill, bill_month, bill_year, dueadvance FROM countdown_notice WHERE id = 1");

    if (!$noticeInfo) {
        echo json_encode(["error" => "Record not found"]);
        exit;
    }

    $monthly_bill = (float)$noticeInfo['monthly_bill'];
    $current_month = (int)$noticeInfo['bill_month'];
    $current_year = (int)$noticeInfo['bill_year'];
    $new_dueadvance = $noticeInfo['dueadvance'];

    
    $paidAmount = isset($input['paidAmount']) ? (float)$input['paidAmount'] : 0;

    
    $months_paid = 0;
    if ($monthly_bill > 0 && $paidAmount >= $monthly_bill) {
        $months_paid = floor($paidAmount / $monthly_bill);
    }

    
    // NEW YEAR AND MONTH
    $new_month = $current_month + $months_paid;
    $new_year = $current_year;

    if ($new_month > 12) {
        $extra_years = floor(($new_month - 1) / 12);
        $new_month = ($new_month - 1) % 12 + 1;
        $new_year += $extra_years;
    }
    
    $new_dueadvance_esc = addslashes($new_dueadvance);
    $new_month_esc = (int)$new_month;
    $new_year_esc = (int)$new_year;

    $obj->rawSqlSingle("
        UPDATE countdown_notice SET
            notice_displayable = 2,
            complete_shutdown = 2,
            dueadvance = '$new_dueadvance_esc',
            bill_month = '$new_month_esc',
            bill_year = '$new_year_esc'
        WHERE id = 1
    ");

    echo json_encode(["success" => true, "extended_months" => $months_paid]);
}


if ($method == 'GET' && isset($_GET['customer_info'])) {
    $data = $obj->rawSql(
        "SELECT 
            CASE ag_status
                WHEN 0 THEN 'Inactive'
                WHEN 1 THEN 'Active'
                WHEN 2 THEN 'Free'
                WHEN 3 THEN 'Discontinue'
            END AS status_name,
            COUNT(*) AS total_agents
        FROM tbl_agent
        WHERE deleted_at IS NULL
        GROUP BY ag_status;
        "
    );
    echo json_encode($data);
}



if ($method == 'POST' && isset($input['insert_remarks'])) {
    $remarks = $obj->insertData('admin_remarks', ['description' => $input['description'], 'entry_by' => $input['entry_by']]);

    echo $remarks;
}

if ($method == 'GET' && isset($_GET['remarks_info'])) {
    $data = $obj->rawSql("SELECT * FROM admin_remarks");
    echo json_encode($data);
}


if ($method == 'POST' && isset($_GET['delete_remarks_id'])) {
    $delete_remarks_id = $_GET['delete_remarks_id'];
    $obj->rawSql("DELETE FROM admin_remarks WHERE id=$delete_remarks_id");
    echo "1";
}


if ($method == 'GET' && isset($_GET['sms_info'])) {
    $apiKey = $obj->getSettingValue('sms', 'pass');
    echo strlen($apiKey) > 15 ? "true" : "false";
}


if ($method == 'GET' && isset($_GET['activity_log'])) {
    $activityLogs = $obj->rawSql('SELECT * FROM activity_logs ORDER BY id DESC LIMIT 10');
    echo json_encode($activityLogs);
}
