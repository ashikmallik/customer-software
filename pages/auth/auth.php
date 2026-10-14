<?php
// Auto-create the login attempt/lockout tracking table if it doesn't exist yet (no-op if it already does)
$obj->raw_sql(
    "CREATE TABLE IF NOT EXISTS login_attempts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip_address VARCHAR(45) NOT NULL,
        username VARCHAR(191) NULL DEFAULT NULL,
        attempt_count INT NOT NULL DEFAULT 0,
        locked_until DATETIME NULL DEFAULT NULL,
        last_attempt_at DATETIME NULL DEFAULT NULL,
        UNIQUE KEY uniq_ip (ip_address)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
);

// Migrate an older version of this table (keyed by username, or by username+ip) if it exists.
// The old per-account counters are just transient rate-limit state, safe to reset when switching strategy.
$hasOldUsernameKey = $obj->raw_sql("SHOW INDEX FROM login_attempts WHERE Key_name = 'uniq_username'");
$hasOldUsernameIpKey = $obj->raw_sql("SHOW INDEX FROM login_attempts WHERE Key_name = 'uniq_username_ip'");
$hasIpKey = $obj->raw_sql("SHOW INDEX FROM login_attempts WHERE Key_name = 'uniq_ip'");

if (empty($hasIpKey) && (!empty($hasOldUsernameKey) || !empty($hasOldUsernameIpKey))) {
    $obj->raw_sql("TRUNCATE TABLE login_attempts");
    if (!empty($hasOldUsernameKey)) {
        $obj->raw_sql("ALTER TABLE login_attempts DROP INDEX uniq_username");
    }
    if (!empty($hasOldUsernameIpKey)) {
        $obj->raw_sql("ALTER TABLE login_attempts DROP INDEX uniq_username_ip");
    }
    $obj->raw_sql("ALTER TABLE login_attempts ADD UNIQUE KEY uniq_ip (ip_address)");
}

// Client IP for this request (the sole lockout key: blocks the whole IP regardless of username tried)
function getClientIp()
{
    return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
}

// Returns remaining lock seconds for an IP, or 0 if not locked
// (comparison is done entirely in MySQL so PHP/DB clock or timezone differences can't throw it off)
function isLoginLocked($obj, $ip)
{
    $rows = $obj->raw_sql(
        "SELECT TIMESTAMPDIFF(SECOND, NOW(), locked_until) AS seconds_remaining
         FROM login_attempts
         WHERE ip_address = '$ip' AND locked_until IS NOT NULL AND locked_until > NOW()"
    );

    if (!empty($rows[0]['seconds_remaining']) && $rows[0]['seconds_remaining'] > 0) {
        return (int) $rows[0]['seconds_remaining'];
    }
    return 0;
}

// Records a failed login attempt; locks the IP for 5 minutes after 5 failed attempts (any username).
// Returns the lock duration in seconds if this attempt triggered a new lock, otherwise 0.
function registerFailedLogin($obj, $ip, $username)
{
    $maxAttempts = 5;
    $lockMinutes = 5;

    $obj->raw_sql(
        "INSERT INTO login_attempts (ip_address, username, attempt_count, last_attempt_at)
         VALUES ('$ip', '$username', 1, NOW())
         ON DUPLICATE KEY UPDATE
             attempt_count = IF(locked_until IS NOT NULL AND locked_until <= NOW(), 1, attempt_count + 1),
             username = VALUES(username),
             last_attempt_at = NOW(),
             locked_until = IF(locked_until IS NOT NULL AND locked_until <= NOW(), NULL, locked_until)"
    );

    $rows = $obj->raw_sql(
        "SELECT attempt_count FROM login_attempts WHERE ip_address = '$ip'"
    );

    if (!empty($rows[0]['attempt_count']) && $rows[0]['attempt_count'] >= $maxAttempts) {
        $obj->raw_sql(
            "UPDATE login_attempts SET locked_until = DATE_ADD(NOW(), INTERVAL $lockMinutes MINUTE) WHERE ip_address = '$ip'"
        );
        return $lockMinutes * 60;
    }
    return 0;
}

// Clears failed-attempt tracking for an IP after a successful login from it
function resetLoginAttempts($obj, $ip)
{
    $obj->raw_sql(
        "DELETE FROM login_attempts WHERE ip_address = '$ip'"
    );
}

// var_dump($obj->getAllData("tbl_agent", [['ag_id','>',0]]));
// var_dump($obj->getAllData("tbl_agent"));

// var_dump($obj->getSingleData("tbl_agent", [['ag_id','>',0]]));
// var_dump($obj->getSingleData("tbl_agent"));

// var_dump($obj->totalCount("tbl_agent", [['ag_id','>',0]]));
// // var_dump($obj->totalCount("tbl_agent"));

$messageNotice = "";
$date = date('Y-m-d');
$offdates = '2030-12-12';
$bnotice = $obj->getSingleData("tbl_notice", [['user_id', '=', 0]]);
$totacntuser = $obj->totalCount("tbl_agent");
$totalcountuser = ($totacntuser) ? $totacntuser : 0;

if ($bnotice) {
    if ($bnotice['trail_date'] >= $date) {
        $offdates = $bnotice['trail_date'];
        $trairemaining = floor((strtotime($bnotice['trail_date']) - time()) / 86400);
        if ($trairemaining < 7) {
            $trairemaining = $trairemaining + 1;
            $messageNotice = "Trail period will be end within $trairemaining days. Before Inactive contact with support " . $bnotice['disconnected_message'];
        }
    } elseif (($bnotice['next_disconnected_date'] >= $date) && ($bnotice['free_customer_limit'] <= $totalcountuser)) {
        $offdates = $bnotice['next_disconnected_date'];
        $disremaining = floor((strtotime($bnotice['next_disconnected_date']) - time()) / 86400);
        if ($disremaining < 7) {
            $disremaining = $disremaining + 1;
            $messageNotice = "Please Pay your software bill within $disremaining days. Before Inactive contact with support " . $bnotice['disconnected_message'];
        }
    } elseif (($bnotice['next_disconnected_date'] < $date) && ($bnotice['free_customer_limit'] <= $totalcountuser)) {
        $offdates = $bnotice['next_disconnected_date'];
        $messageNotice = "Please Pay your software bill to active your account. Contact with support " . $bnotice['disconnected_message'];
        session_destroy();
        // header("location: login.php");
    }
}


$date = strtotime("$offdates 4:00 PM"); //provide disconnect date    
$remaining = $date - time();
$days_remaining = floor($remaining / 86400);
$days_rem_pos = abs($days_remaining);
$hours_remaining = floor(($remaining % 86400) / 3600);
$hours_remaining_pos = abs($hours_remaining);
if ($days_remaining <= 0  && $hours_remaining == 0 || $hours_remaining < 0) {
    $amethod = "";
    $etype = "hidden";
    $ptype = "hidden";
    $rtype = "hidden";
    $battr = "hidden";
} else {
    $amethod = "POST";
    $etype = "text";
    $ptype = "password";
    $rtype = "checkbox";
    $battr = "";
}

if (isset($_POST['login']) ? $_POST['login'] : NULL) {

    if ((!isset($_POST['username']) || empty($_POST['username'])) || (!isset($_POST['password']) || empty($_POST['password']))) {
        $obj->notificationStore("User Name or Password Must Not be empty", 'danger');
        header('Location: ?page=login');
        exit();
    }
    $username = isset($_POST['username']) ? $obj->validation($_POST['username']) : NULL;
    $password = isset($_POST['password']) ? $obj->validation($_POST['password']) : NULL;

    $username = stripcslashes(trim($username));
    $password = stripcslashes(trim($password));
    $remember = (isset($_POST['remember'])) ? true : false;
    $clientIp = getClientIp();
    
    $re_users = false;
    try {
        $re_users = $obj->login_check("_reseller_createuser", $username, md5($password));
    } catch (\PDOException $e) {
        if (strpos($e->getMessage(), '1146') === false) {
            error_log("Reseller login check failed: " . $e->getMessage());
        }
        $re_users = false;
    }

    $lockSecondsRemaining = isLoginLocked($obj, $clientIp);
    if ($lockSecondsRemaining > 0) {
        setcookie('login_flash', "Too many failed attempts. Please try again in " . ceil($lockSecondsRemaining / 60) . " minute(s).", time() + 60, '/');
        header('Location: ?page=login');
        exit();
    }

    $users =  $obj->login_check("vw_user_info", $username, md5($password));
    if ($users) {
        resetLoginAttempts($obj, $clientIp);

        $_SESSION["login"] = true;
        $_SESSION['userid'] = $users['UserId'];
        $_SESSION['username'] = $users['UserName'];
        $_SESSION['usertype'] = $users['UserType'];
        $_SESSION['userfullname'] = $users['FullName'];
        $_SESSION['userimage'] = $users['PhotoPath'];

        if ($remember) {
            setcookie("login", true, time() + (1 * 24 * 60 * 60)); // 1 days
            setcookie("userid", $users['UserId'], time() + (1 * 24 * 60 * 60));
            setcookie("username", $users['UserName'], time() + (1 * 24 * 60 * 60));
            setcookie("usertype", $users['UserType'], time() + (1 * 24 * 60 * 60));
            setcookie("userfullname", $users['FullName'], time() + (1 * 24 * 60 * 60));
            setcookie("userimage", $users['PhotoPath'], time() + (1 * 24 * 60 * 60));
        }
        // $obj->notificationStore("Login Success", 'success');
        header('Location: ?page=dashboard');


        // create activity log for delete payment info
        $obj->createActivityLog(
            $users['UserId'],
            '1',
            '1',
            null,
            null,
            null,
            null,
            $users['FullName'] . ' Logged In Successfully.',
            null,
            null,
            true,
            true,
            null
        );
    } elseif ($re_users) {
       $userId = (int)$re_users['UserId'];
        $userId = (int)$userId;
        $reAccess = $obj->details_by_cond("_re_useraccess", "UserId='$userId'");
        $_SESSION["login"] = true;
        $_SESSION['userid'] = $re_users['UserId'];
        $_SESSION['username'] = $re_users['UserName'];
        $_SESSION['usertype'] = $reAccess['UserType'];
        $_SESSION['userfullname'] = $re_users['FullName'];
        $_SESSION['userimage'] = $re_users['PhotoPath'];
        if ($remember) {
            setcookie("login", true, time() + (1 * 24 * 60 * 60)); // 1 days
            setcookie("userid", $re_users['UserId'], time() + (1 * 24 * 60 * 60));
            setcookie("username", $re_users['UserName'], time() + (1 * 24 * 60 * 60));
            setcookie("usertype", $reAccess['UserType'], time() + (1 * 24 * 60 * 60));
            setcookie("userfullname", $re_users['FullName'], time() + (1 * 24 * 60 * 60));
            setcookie("userimage", $re_users['PhotoPath'], time() + (1 * 24 * 60 * 60));
        }
        
        $result = $obj->resellerHistoryLog([
            're_id'        => $re_users['UserId'],
            'header'       => 'login',
            'descriptions' => 'Reseller Logged In Successfully.',
            'type'         => 'login',
        ]);
        
        // $obj->notificationStore("Login Success", 'success');
        header('Location: ?page=reseller-dashboard');
        exit();
        
        // create activity log for delete payment info
        
    } else {
        $lockedSecondsAfterFail = registerFailedLogin($obj, $clientIp, $username);
        if ($lockedSecondsAfterFail > 0) {
            setcookie('login_flash', "Too many failed attempts. This device/IP has been temporarily locked for 5 minutes.", time() + 60, '/');
        } else {
            setcookie('login_flash', "Invalid username or password.", time() + 60, '/');
        }
    }
    header('Location: ?page=login');
    exit();
}

// Lockout status for the login page display (also evaluated on plain GET, e.g. after redirect).
// Checked purely by IP against the DB every load - not cookie/session-based - so it can't be
// bypassed by switching browsers or clearing cookies, and isn't affected by session_destroy() elsewhere.
$loginLockRemaining = isLoginLocked($obj, getClientIp());
if ($loginLockRemaining > 0) {
    $battr = "disabled";
}
