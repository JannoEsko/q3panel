<?php

    session_start();

require_once __DIR__ . "/local_SQL.php";
require_once __DIR__ . "/classes/loader.php";
/**
 * This file holds all the generic functions and is also the starting point for all of the class 
 * function callouts, POST/GET requests etc.
 */



if (isset($_GET['logout'])) {
    session_destroy();
}

if (isset($_POST['requestRecovery'], $_POST['email'])) {
    echo json_encode(User::forgotPassword($sql, $_POST['email'], generateRandomKey(), $HOST_URL));
}

if (isset($_GET['recover'])) {
    $dat = User::recovery($sql, $_GET['recover']);
    if (isset($dat['error'])) {
        $_SESSION['FPSW_ERROR'] = $dat['error'];
    }
}

if (isset($_POST['recover'], $_POST['password'])) { 
    $data = User::changeForgottenPassword($sql, $_POST['password'], $_POST['recover']);
    if (isset($data['error'])) {
        echo json_encode($data);
    } else {
        echo json_encode(array("href" => "."));
    }
    
}

if (isset($_POST['theme'], $_POST['themename'])) {
    echo json_encode(User::changeUserStylePreference($sql, $_SESSION['user_id'], $_POST['themename']));
    die();
}

if (isset($_POST['login'], $_POST['username'], $_POST['password'])) {
    session_destroy();
    $user = new User($_POST['username'], $_POST['password']);
    $data = $user->authenticate($sql);
    if (isset($data['error'])) {
        echo json_encode($data);
    } else if (isset($_SESSION['installer'])) {
        echo json_encode(array("href" => "../step6/"));
    } else {
        echo json_encode(array("href" => "."));
    }
}

if (isset($_GET['testsendgrid'])) {
    $emailPrefs = Email::getEmailPreferences($sql);
    $email = new Email($emailPrefs['from_email'], "eskojanno@gmail.com", "Test email", "Test email<br>hello", $emailPrefs['from_name'] . " SendGrid", "Janno");
    echo json_encode($email->sendEmail(int2bool($emailPrefs['is_sendgrid']), $emailPrefs['api_key']));
    die();
}

if (isset($_GET['testphpmailer'])) {
    $emailPrefs = Email::getEmailPreferences($sql);
    $email = new Email($emailPrefs['from_email'], "eskojanno@gmail.com", "Test email", "Test email<br>hello", $emailPrefs['from_name'] . " PHPMailer", "Janno");
    echo json_encode($email->sendEmail());
    die();
}

function int2bool($input) {
    return intval($input) === 1;
}

if (isset($_GET['getExternalUser'], $_GET['extUserName'])) {
    echo json_encode(User::getExternalAccount($sql, $_GET['extUserName']));
}

if (isset($_POST['extAccount'], $_POST['extUser'], $_POST['extUserGroup']) && (isset($_SESSION['installer']) || User::canAddUser($sql, $_SESSION['user_id']))) {
    
    $user = new User($_POST['extUser'], null, "1", null, $_POST['extUserGroup'], 1);
    $dat = $user->register($sql);
    if (isset($dat['error'])) {
        echo json_encode($dat);
    } else if (isset($_SESSION['installer'])) {
        echo json_encode(array("href" => "../step6/"));
    } else {
        echo json_encode(array("href" => "."));
    }
}

if (isset($_POST['register'], $_POST['userGroup'], $_POST['username'], $_POST['password'], $_POST['email']) && (isset($_SESSION['installer']) || User::canAddUser($sql, $_SESSION['user_id']))) {
    $user = new User($_POST['username'], $_POST['password'], 0, $_POST['email'], $_POST['userGroup'], 1);
    $dat = $user->register($sql);
    if (isset($dat['error'])) {
        echo json_encode($dat);
    } else if (isset($_SESSION['installer'])) {
        echo json_encode(array("href" => "../step6/"));
    } else {
        echo json_encode(array("href" => "."));
    }
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

if (isset($_POST['getUserData'], $_POST['user_id'])) {
    echo json_encode();
}

if (isset($_POST['addGame'], $_POST['game_name'], $_POST['game_location'], $_POST['startscript'])) {
    $data = Game::saveGame($sql, $_POST['game_name'], $_POST['game_location'], $_POST['startscript']);
    if (intval($data['rows_affected']) === 1) {
        die(json_encode(array("href" => ".")));
    } else {
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_ERROR'])));
    }
}

if (isset($_POST['user_id'], $_POST['delete']) && intval($_POST['delete']) === 1) {
    if (User::canEditUser($sql, $_SESSION['user_id'], $_POST['user_id']) > 0 && intval($_SESSION['user_id']) !== intval($_POST['user_id'])) {
        //We won't grab the group id from session data because someone might've changed it in the meantime
        //and thus, we will check it from SQL, so if the user can actually edit an user (delete incl), 
        //we'll let him do it if and only if it's allowed.
        $data = User::deleteAccount($sql, $_POST['user_id']);
        if (intval($data['rows_affected']) === 1) {
            die(json_encode(array("href" => ".")));
        } else {
            die(json_encode(array("error" => Constants::$ERRORS['GENERIC_ERROR'])));
        }
    } else {
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_PRIVILEGE_ERROR'])));
    }
    
}

if (isset($_POST['user_id'], $_POST['origin'], $_POST['editUser']) && intval($_POST['editUser']) === 1) {
    //first check that if he is editing his own account,
    //is he trying to edit his own group to a lower group (hence, locking himself out from the system).
    if (User::canEditUser($sql, $_SESSION['user_id'], $_POST['user_id']) > 0) {
        User::editAccount($sql, $_POST['user_id'], $_POST['username'], $_POST['password'], null, $_POST['email'], $_POST['group']);
        die(json_encode(array("href" => ".")));
    } else {
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_ERROR'])));
    }
    
}

if (isset($_POST['origin'], $_POST['register'], $_POST['extUser'], $_POST['group'])) {
    
}
