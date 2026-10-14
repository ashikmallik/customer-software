<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
$day = date('d');
$todayDay = $day;
$lastDayOfMonth = date('t'); // এই মাসের shes din

$m = date('m');
$y = date('Y');

require(__DIR__ . '/services/Model.php');
$obj = new Model();

$adminMobile = $obj->getSettingValue('mobile', 'mobile');
if (strpos($adminMobile, '88') !== 0) {
    $adminMobile = '88' . $adminMobile;
}

$totaluser = 0;
$disconnectCount = 0;

$isMikrotikDisconnect = $obj->getSettingValue('mikrotikDisconCronJob', 'mikrotikDisconCronJob');
$cronSmsDay = $obj->getSettingValue('cronSmsSend', 'cronSmsSend');
$mikDisDay = $obj->getSettingValue('MikDisconday', 'MikDisconday');

if (isset($mikDisDay) && $mikDisDay == 'cronday') {
    $dd = $todayDay;
} else {
    $dd = $mikDisDay;
}

if (isset($isMikrotikDisconnect) && $isMikrotikDisconnect == 'active') {
    $agWhereCon = "";
    $MikDisconBillStatusRes = $obj->getSettingValue('MikDisconBillStatus', 'MikDisconBillStatus');
    if (isset($MikDisconBillStatusRes) && $MikDisconBillStatusRes == 'inc_par_paid') {
        $agWhereCon = "AND customer_billing.dueadvance > 0";
    } else {
        $agWhereCon = "AND tbl_agent.bill_status = '0' AND customer_billing.dueadvance > 0";
    }

    foreach ($obj->view_all('mikrotik_user') as $mikrotiks) {
        $individualMikDisconUsers = 0;
        $mikrotikId = $mikrotiks['id'];

        $agents = $obj->raw_sql(
            "SELECT * 
            FROM tbl_agent 
            LEFT JOIN customer_billing ON tbl_agent.ag_id = customer_billing.agid
            WHERE tbl_agent.ag_status = '1' 
            AND (
                tbl_agent.mikrotik_disconnect = '$todayDay'
                OR (tbl_agent.mikrotik_disconnect > '$lastDayOfMonth' AND '$todayDay' = '$lastDayOfMonth')
            )
            AND tbl_agent.deleted_at IS NULL 
            AND tbl_agent.mikrotik_id='" . $mikrotiks['id'] . "' 
            $agWhereCon
            ORDER BY `ag_id` ASC"
        );
        $totaluser += count($agents);

        if ($obj->checkConnection($mikrotikId)) {
            foreach ($agents as $agent) {
                $obj->disableSingleSecret($mikrotikId, $agent['ip']);
                $disconnectCount++;
                $individualMikDisconUsers++;
            }

            $obj->insertData("cron_job_logs", [
                'type' => 'mikrotik',
                'mikrotik_ip' => $mikrotiks['mik_ip'],
                'total_disconnect' => $individualMikDisconUsers,
                'description' => $individualMikDisconUsers <= 0 ? 'No user found to disconnect.' : '',
                'status' => 'success',
                'date' => date('Y-m-d')
            ]);
        } else {
            $mikrotikIP = isset($mikrotiks['mik_ip']) ? $mikrotiks['mik_ip'] : '';
            $obj->insertData("cron_job_logs", [
                'type' => 'mikrotik',
                'description' => 'Mikrotik is not connected',
                'mikrotik_ip' => $mikrotikIP,
                'status' => 'failed',
                'date' => date('Y-m-d')
            ]);

            if (!empty($adminMobile)) {
                $msg = "আপনার সফটওয়্যার থেকে  {$mikrotikIP} ডিজেবল আছে। এজন্যে এই  ip  এর কাস্টমার গুলো ডিসকানেক্ট  হয়নি।";
                $obj->sendsms($adminMobile, $msg);
            }
        }
    }

    if ($disconnectCount > 0) {
        $total_dis = "আজকে  {$disconnectCount} জন কাস্টমারের লাইন অফ হইসে।";
        $obj->sendsms($adminMobile, $total_dis);
    }
}

// ================= SMS Reminder =================
$smsInfo = $obj->rawSqlSingle("SELECT * FROM sms WHERE status = '6'");

if (isset($smsInfo['smshead']) && $smsInfo['smshead'] == 'active') {

    /*
     * ----- Multi-day SMS reminder support -----
     * cronSmsSend এখন কমা দিয়ে আলাদা করা একাধিক দিন রাখতে পারে, যেমন: "1,2,5"
     * প্রতিটা মান হচ্ছে disconnect হওয়ার এত দিন আগে (before) reminder পাঠানোর অফসেট।
     * অর্থাৎ mikrotik_disconnect (disconnect day-of-month) যদি আজকের দিন + সেই
     * অফসেট এর day-of-month এর সাথে মিলে যায়, তাহলে সেই কাস্টমারকে SMS যাবে।
     * একই মেসেজ বডি সব offset এর জন্য ব্যবহার হবে।
     */

    $smsDayIntervals = [];
    if (!empty($cronSmsDay)) {
        $cronSmsDayArr = array_filter(array_map('trim', explode(',', $cronSmsDay)), function ($v) {
            return $v !== '' && is_numeric($v);
        });

        foreach ($cronSmsDayArr as $offsetDay) {
            $offsetDay = intval($offsetDay);
            $smsDayIntervals[] = "DAY(CURDATE() + INTERVAL '$offsetDay' DAY)";
        }
    }

    if (empty($smsDayIntervals)) {
        // কোনো valid day-offset সেট করা নেই, তাই স্কিপ করা হচ্ছে।
        $obj->insertData("cron_job_logs", [
            'type' => 'sms',
            'total_sms_sent' => 0,
            'status' => 'success',
            'description' => 'No valid cronSmsSend day-offset configured, skipped.',
            'date' => date('Y-m-d')
        ]);
    } else {

        $smsDayInClause = implode(', ', $smsDayIntervals);

        $smsArray = [];
        $smsI = 0;
        // $allcustomerforsms = $obj->raw_sql("SELECT * FROM tbl_agent WHERE ag_status = '1' AND bill_status= '0' AND deleted_at IS NULL AND mikrotik_disconnect = $day ORDER BY ag_id ASC");

        $allcustomerforsms = $obj->raw_sql(
            "SELECT * 
            FROM tbl_agent
            LEFT JOIN customer_billing ON tbl_agent.ag_id = customer_billing.agid
            WHERE tbl_agent.ag_status = '1' 
              AND tbl_agent.bill_status = '0' 
              AND tbl_agent.deleted_at IS NULL
              AND customer_billing.dueadvance > 0
              AND tbl_agent.mikrotik_disconnect IN ($smsDayInClause)
            ORDER BY ag_id ASC"
        );

        foreach ($allcustomerforsms as $agent) {
            $sms_b = isset($smsInfo['smsbody']) ? $smsInfo['smsbody'] : NULL;
            $ip = $agent['ip'];
            $cid = $agent['cus_id'];
            $mobile =  $agent['ag_mobile_no'];
            //  echo $mobile.'<br>';

            $totalDueAmount = $obj->rawSqlSingle("SELECT dueadvance FROM customer_billing WHERE agid = '$agent[ag_id]'")['dueadvance'];

            $var_replacement = [
                '{CUSTOMER_NAME}' => $agent['ag_name'],
                '{DUE_AMOUNT}' => $totalDueAmount,
                '{CUSTOMER_ID}' => $agent['cus_id'],
                '{IP_ADDRESS}' => $agent['ip'],
                // '{PACKAGE_NAME}' => $agent['mb'],
                '{PACKAGE_NAME}' =>  $obj->rawSqlSingle("SELECT package_name FROM tbl_package WHERE net_speed = '$agent[mb]'")['package_name'],
                '{MONTHLY_BILL}' => $agent['taka'],
            ];

            $sms_b = strtr($sms_b, $var_replacement);
            $mass = "$sms_b";

            if ($mobile) {
                // $mobilenum=$mobile;
                $mobilenum = "88" . $mobile;
                $smsArray[$smsI++] = ["to" => $mobilenum, "message" => $mass];
            }
        }

        $smsSuccess =  $obj->sms_send($smsArray);
        $smsCountArray = count($smsArray);
        if ($smsSuccess === true) {
            $obj->insertData("cron_job_logs", [
                'type' => 'sms',
                'total_sms_sent' => $smsCountArray,
                'status' => 'success',
                'date' => date('Y-m-d')
            ]);
        } else {
            $obj->insertData("cron_job_logs", [
                'type' => 'sms',
                'total_sms_sent' => $smsCountArray,
                'status' => $smsCountArray > 0 ? 'failed' : 'success',
                'description' => $smsCountArray > 0 ? $smsSuccess['error_message'] : 'No user found to send sms.',
                'date' => date('Y-m-d')
            ]);
        }
    }
}

?>