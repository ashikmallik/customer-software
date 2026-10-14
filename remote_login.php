<?php

/**
 * REMOTE LOGIN ENDPOINT  (client side)
 * -------------------------------------------------------------------------
 * Put this file in the ROOT of every client install (next to index.php),
 * keeping the name remote_login.php.
 *
 * It lets the master billing panel (billing.bsdisp.com) list this install's
 * login users and sign a support admin straight into any of them.
 *
 * Two actions:
 *   ?action=list   -> JSON list of active _createuser rows (no passwords).
 *                     Called server-to-server by the master (curl), so there
 *                     is no browser and no Referer to check here.
 *   ?action=login  -> sets the same session + cookies the normal login sets
 *                     for one UserId, then redirects to the dashboard. This is
 *                     a top-level browser navigation from the master panel, so
 *                     its Referer must come from the master host.
 *
 * How a request is trusted (why a faked domain cannot get in):
 *   1. Every request is signed with REMOTE_LOGIN_SECRET using HMAC-SHA256.
 *      Only the master server knows that secret, so no other site can produce
 *      a valid signature however it fakes headers or origin.
 *   2. The signature covers a unix timestamp; requests older than the TTL are
 *      rejected, so a captured link stops working after a minute.
 *   3. A login token carries a random nonce that is remembered until it
 *      expires, so the same link cannot be replayed.
 *   4. For the browser login step the Referer host must equal the master host.
 *      (On its own the Referer is forgeable, which is why 1-3 are the real
 *      lock and this is only an extra layer, exactly as asked.)
 *
 *  >>> BEFORE DEPLOY: set REMOTE_LOGIN_SECRET to the SAME long random string
 *      that the master keeps in pages/customer/remote_login_config.php. <<<
 */

// ------------------------------------------------------------------ config
define('REMOTE_LOGIN_SECRET', 'Z9DFayTfhsu76MgZJzvw1AWpYjf0pdRRo8rhoSQsRYjmfo5QV7oprx');
define('REMOTE_LOGIN_MASTER_HOST', 'billing.bsdisp.com'); // the only allowed source
define('REMOTE_LOGIN_TTL', 60);            // seconds a signed request stays valid
define('REMOTE_LOGIN_REQUIRE_REFERER', true); // set false only if a proxy strips Referer

date_default_timezone_set('Asia/Dhaka');

// ------------------------------------------------------------------ error surfacing
// The server usually hides PHP errors (blank HTTP 500), which makes remote
// diagnosis impossible. Capture any fatal / uncaught error and return it as
// JSON. The detailed message is only revealed when ?debug=<secret> is passed,
// so ordinary callers just see a generic error.
error_reporting(E_ALL);
ini_set('display_errors', '0');

function rl_debug_allowed()
{
    return isset($_GET['debug']) && hash_equals(REMOTE_LOGIN_SECRET, (string) $_GET['debug']);
}

set_exception_handler(function ($ex) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => rl_debug_allowed()
            ? ('Server error: ' . $ex->getMessage() . ' @ ' . $ex->getFile() . ':' . $ex->getLine())
            : 'Server error on the client install.',
    ]);
    exit;
});

register_shutdown_function(function () {
    $e = error_get_last();
    if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: application/json');
        }
        echo json_encode([
            'success' => false,
            'message' => rl_debug_allowed()
                ? ('Fatal: ' . $e['message'] . ' @ ' . $e['file'] . ':' . $e['line'])
                : 'Server error on the client install.',
        ]);
    }
});

// ------------------------------------------------------------------ helpers

/** JSON response for the list action, then stop. */
function rl_json($ok, $payload = [])
{
    header('Content-Type: application/json');
    echo json_encode(array_merge(['success' => $ok], $payload));
    exit;
}

/** Plain fatal for the browser login step, then stop. */
function rl_die($message)
{
    http_response_code(403);
    echo '<!doctype html><meta charset="utf-8"><title>Remote login</title>'
        . '<div style="font:16px/1.6 system-ui,sans-serif;padding:60px;text-align:center;color:#444;">'
        . htmlspecialchars($message, ENT_QUOTES) . '</div>';
    exit;
}

/** Constant-time HMAC-SHA256 check over $message. */
function rl_check_sig($message, $sig)
{
    if (!is_string($sig) || $sig === '') {
        return false;
    }
    return hash_equals(hash_hmac('sha256', $message, REMOTE_LOGIN_SECRET), $sig);
}

/** Host part of a URL/string, lowercased, no port. */
function rl_host($value)
{
    $host = parse_url((string) $value, PHP_URL_HOST);
    if (!$host) {
        // header may be a bare host already
        $host = preg_replace('/:\d+$/', '', (string) $value);
    }
    return strtolower((string) $host);
}

/**
 * Records a nonce as used; returns true if it was ALREADY used (replay).
 * Kept in a small JSON file in the system temp dir, self-pruning.
 */
function rl_nonce_replayed($nonce, $expiresAt)
{
    $file = sys_get_temp_dir() . '/remote_login_nonce_' . md5(__DIR__ . REMOTE_LOGIN_MASTER_HOST) . '.json';
    $now  = time();

    $fp = fopen($file, 'c+');
    if ($fp === false) {
        // If we cannot persist nonces, fail closed rather than allow replay.
        return true;
    }
    flock($fp, LOCK_EX);
    $raw  = stream_get_contents($fp);
    $seen = $raw ? (json_decode($raw, true) ?: []) : [];

    foreach ($seen as $n => $exp) {
        if ($exp < $now) {
            unset($seen[$n]);
        }
    }

    $replayed = isset($seen[$nonce]);
    if (!$replayed) {
        $seen[$nonce] = $expiresAt;
        rewind($fp);
        ftruncate($fp, 0);
        fwrite($fp, json_encode($seen));
    }
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);

    return $replayed;
}

/** Verify timestamp window + signature; $forLogin toggles the Referer check. */
function rl_authorize($signedMessage, $sig, $ts, $forLogin)
{
    // Length check, not a placeholder compare, so replacing the secret above can
    // never accidentally trip this guard.
    if (!is_string(REMOTE_LOGIN_SECRET) || strlen(REMOTE_LOGIN_SECRET) < 16) {
        return 'Remote login is not configured on this install.';
    }
    if (!ctype_digit((string) $ts) || abs(time() - (int) $ts) > REMOTE_LOGIN_TTL) {
        return 'This link has expired. Please try again from the panel.';
    }
    if (!rl_check_sig($signedMessage, $sig)) {
        return 'Invalid signature - request is not from an authorised source.';
    }
    if ($forLogin && REMOTE_LOGIN_REQUIRE_REFERER) {
        $ref = $_SERVER['HTTP_REFERER'] ?? $_SERVER['HTTP_ORIGIN'] ?? '';
        if ($ref === '' || rl_host($ref) !== strtolower(REMOTE_LOGIN_MASTER_HOST)) {
            return 'This action must be started from ' . REMOTE_LOGIN_MASTER_HOST . '.';
        }
    }
    return null; // authorised
}

// ------------------------------------------------------------------ bootstrap
require(realpath(__DIR__ . '/services/Model.php'));
$obj    = new Model();
$action = $_GET['action'] ?? '';

// ------------------------------------------------------------------ list users
if ($action === 'list') {

    $ts  = $_GET['ts'] ?? '';
    $sig = $_GET['sig'] ?? '';
    // message = action|ts   (no per-user data in the list request)
    $err = rl_authorize('list|' . $ts, $sig, $ts, false);
    if ($err !== null) {
        rl_json(false, ['message' => $err]);
    }

    // _createuser is the source of truth for every login user. UserType lives in
    // vw_user_info (joined with _useraccess), so pull it with a LEFT JOIN and let
    // it be NULL for users that have no access row - they still show and can be
    // logged into.
    $users = $obj->raw_sql(
        "SELECT c.UserId, c.FullName, c.UserName, c.Email, c.MobileNo, v.UserType
         FROM _createuser c
         LEFT JOIN vw_user_info v ON v.UserId = c.UserId
         WHERE c.Status = 1
         ORDER BY c.FullName ASC"
    );

    rl_json(true, [
        'host'  => $_SERVER['HTTP_HOST'] ?? '',
        'count' => count($users),
        'users' => $users,
    ]);
}

// ------------------------------------------------------------------ login as user
if ($action === 'login') {

    $userId = (int) ($_GET['user_id'] ?? 0);
    $ts     = $_GET['ts'] ?? '';
    $nonce  = $_GET['nonce'] ?? '';
    $sig    = $_GET['sig'] ?? '';

    // message = login|user_id|ts|nonce
    $err = rl_authorize('login|' . $userId . '|' . $ts . '|' . $nonce, $sig, $ts, true);
    if ($err !== null) {
        rl_die($err);
    }
    if ($userId <= 0 || $nonce === '') {
        rl_die('Malformed login request.');
    }
    if (rl_nonce_replayed($nonce, (int) $ts + REMOTE_LOGIN_TTL)) {
        rl_die('This login link has already been used. Please open a fresh one.');
    }

    // The user must exist on THIS install and be active.
    $user = $obj->rawSqlSingle(
        "SELECT * FROM vw_user_info WHERE UserId = $userId AND Status = 1"
    );
    if (!$user) {
        rl_die('That user does not exist on this install (or is inactive).');
    }

    // Mirror exactly what the normal login (pages/auth/auth.php) sets, so the
    // rest of the app treats this like a real session.
    if (!isset($_SESSION['session'])) {
        session_start();
        $_SESSION['session'] = true;
    }
    $_SESSION['login']        = true;
    $_SESSION['userid']       = $user['UserId'];
    $_SESSION['username']     = $user['UserName'];
    $_SESSION['usertype']     = $user['UserType'];
    $_SESSION['userfullname'] = $user['FullName'];
    $_SESSION['userimage']    = $user['PhotoPath'] ?? '';

    $day = time() + (1 * 24 * 60 * 60);
    setcookie('login', true, $day, '/');
    setcookie('userid', $user['UserId'], $day, '/');
    setcookie('username', $user['UserName'], $day, '/');
    setcookie('usertype', $user['UserType'], $day, '/');
    setcookie('userfullname', $user['FullName'], $day, '/');
    setcookie('userimage', $user['PhotoPath'] ?? '', $day, '/');

    // Go to the app's front controller, not back to this script. A bare
    // "?page=dashboard" would resolve against /remote_login.php and loop here.
    header('Location: index.php?page=dashboard');
    exit;
}

// ------------------------------------------------------------------ unknown
rl_json(false, ['message' => 'Unknown action.']);
