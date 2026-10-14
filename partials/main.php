<?php
if (!isset($_SESSION["session"])) {
    session_start();
    $_SESSION["session"] = true;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Asia/Dhaka');
if (!isset($_SESSION["login"]) && isset($_COOKIE["login"])) {
    // Restore session from cookie
    if ($_COOKIE["login"]) {
        $_SESSION["login"] = $_COOKIE["login"];
        $_SESSION['userid'] = $_COOKIE['userid'] ?? NULL;
        $_SESSION['username'] = $_COOKIE['username'] ?? NULL;
        $_SESSION['usertype'] = $_COOKIE['usertype'] ?? NULL;
        $_SESSION['userfullname'] = $_COOKIE['userfullname'] ?? NULL;
        $_SESSION['userimage'] = $_COOKIE['userimage'] ?? '';
    }
}
$page = isset($_GET['page']) ? $_GET['page'] : null;
$login = isset($_SESSION['login']) ? $_SESSION['login'] : NULL;
$userId = isset($_SESSION['userid']) ? $_SESSION['userid'] : NULL;
$userName = isset($_SESSION['username']) ? $_SESSION['username'] : NULL;
$fullName = isset($_SESSION['userfullname']) ? $_SESSION['userfullname'] : NULL;
$userImage = isset($_SESSION['userimage']) ? $_SESSION['userimage'] : NULL;
$ty = isset($_SESSION['usertype']) ? $_SESSION['usertype'] : NULL;

require(realpath(__DIR__ . '/../services/Model.php'));
$obj = new Model();

if (($page == 'logout') && $login) {
    session_unset();
    session_destroy();
    // Clear cookies
    setcookie("login", false, time() - 3600);
    setcookie("userid", "", time() - 3600);
    setcookie("username", "", time() - 3600);
    setcookie("usertype", "", time() - 3600);
    setcookie("userfullname", "", time() - 3600);
    setcookie("userimage", "", time() - 3600);
    $obj->notificationStore("Logout Success", 'success');
    header('Location: ?page=logout');
    exit();
}

//previous logout code moved to pages/auth/logout.php
// setTimeout(function(){
//     window.location.href = "?page=login";
// }, 1000);


if (!$login) {
    if (!$login && $page === 'logout') {
        $obj->notificationStore("Logout Success", 'success');
        echo '<script type="text/javascript">
                        window.location.href = "?page=login";
                </script>';
        exit();
    }
} else {

    if ($page == 'login') header('Location: ?page=dashboard');
}
