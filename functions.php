<?php

    session_start();

require_once __DIR__ . "/local_SQL.php";
require_once __DIR__ . "/classes/loader.php";
/**
 * This file holds all the generic functions and is also the starting point for all of the class 
 * function callouts, POST/GET requests etc.
 */

if (isset($_GET['getExternalUser'], $_GET['extUserName'])) {
    echo json_encode(User::getExternalAccounts($sql, $_GET['extUserName']));
}

if (isset($_POST['extAccount'], $_POST['extUser'], $_POST['extUserGroup']) && (isset($_SESSION['installer']) || User::canAddUser($sql, $_SESSION['user_id']))) {
    $user = new User($_POST['extUser'], null, "1", null, $_POST['extUserGroup'], 1);
    echo json_encode($user->register($sql));
}

if (isset($_POST['register'], $_POST['userGroup'], $_POST['username'], $_POST['password'], $_POST['email']) && (isset($_SESSION['installer']) || User::canAddUser($sql, $_SESSION['user_id']))) {
    $user = new User($_POST['username'], $_POST['password'], 0, $_POST['email'], $_POST['userGroup'], 1);
    echo json_encode($user->register($sql));
}

if (isset($_GET['setuptables'])) {
    require_once __DIR__ . "/classes/installation/Installation.php";
    Installation::initializeTables($sql);
    print_r("Tables setup done");
}

if (isset($_POST['host']) && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['db']) && isset($_POST['url'])) {
    require_once __DIR__ . "/classes/installation/Installation.php";
    $db_host = $_POST['host'];
    $db_username = $_POST['username'];
    $db_password = $_POST['password'];
    $db = $_POST['db'];
    $url = $_POST['url'];
    
    $r = Installation::initializeConfig($db_host, $db_username, $db_password, $db, $url);
    if (!isset($r['error'])) {
        $_SESSION['installer'] = "1";
        require_once __DIR__ . "/classes/sql/SQL.php";
        $sql = new SQL($db_host, $db_username, $db_password, $db);
        $ret2 = Installation::initializeTables($sql);
        if ($ret2['error'] !== null) {
            echo json_encode($ret2);
            die();
        }
    }
    echo json_encode($r);
}

if (isset($_POST['exthost'], $_POST['extusername'], $_POST['password'], $_POST['extdb'], $_POST['usrtable'], $_POST['usrtableid'], $_POST['usrtablename'], $_POST['usrtablepsw'], $_POST['usrtableemail'])) {
    require_once __DIR__ . "/classes/installation/Installation.php";
    $ext_host = $_POST['exthost'];
    $ext_user = $_POST['extusername'];
    $ext_pass = $_POST['password'];
    $ext_db = $_POST['extdb'];
    $ext_usrtable = $_POST['usrtable'];
    $ext_usrtableid = $_POST['usrtableid'];
    $ext_usrtableusrname = $_POST['usrtablename'];
    $ext_usrtablepsw = $_POST['usrtablepsw'];
    $ext_usrtableemail = $_POST['usrtableemail'];
    
    echo json_encode(Installation::initializeExternalConnection($sql, $ext_host, $ext_user, $ext_pass, $ext_db, $ext_usrtable, $ext_usrtableid, $ext_usrtableusrname, $ext_usrtablepsw, $ext_usrtableemail));
}

if (isset($_POST['isSendgrid'], $_POST['fromName'], $_POST['fromEmail'])) {
    $is_sendgrid = intval($_POST['isSendgrid']);
    $api_key = null;
    if ($is_sendgrid === 1) {
        $api_key = $_POST['api'];
    }
    $from_name = $_POST['fromName'];
    $from_email = $_POST['fromEmail'];
    $result = Email::saveEmailPreferences($sql, $is_sendgrid, $from_name, $from_email, $api_key);
    if (isset($result['error'])) {
        return json_encode($result);
    }
    echo json_encode(array("href" => "../step5/"));
}


/**
 * Taken from http://stackoverflow.com/questions/1846202/php-how-to-generate-a-random-unique-alphanumeric-string/13733588#13733588.
 * @param type $length
 * @return string
 */
function generateRandomKey($length = 50) {
    $key = "";
    $possibleValues = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    for ($i = 0; $i < $length; $i++) {
        $key .= $possibleValues[keyRandomizer(0, strlen($possibleValues))];
    }
    return $key;
}

/**
 * Taken from http://stackoverflow.com/questions/1846202/php-how-to-generate-a-random-unique-alphanumeric-string/13733588#13733588
 * @param type $min
 * @param type $max
 * @return type
 */
function keyRandomizer($min, $max) {
    $range = $max - $min;
    if ($range < 0) return $min;
    $log = log($range, 2);
    $bytes = (int) ($log / 8) + 1;
    $bits = (int) $log + 1;
    $filter = (int) (1 << $bits) - 1;
    do {
        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
        $rnd = $rnd & $filter;
    } while ($rnd >= $range);
    return $min + $rnd;
}


function isExternalAuthEnabled($sql) {
    $query = Constants::$SELECT_QUERIES['EXT_AUTH_EXISTS'];
    $ret = $sql->query($query);
    if (intval($ret[0]['count']) === 1) {
        return true;
    }
    return false;
}




