<?php

    session_start();

require_once __DIR__ . "/local_SQL.php";
require_once __DIR__ . "/classes/loader.php";
/**
 * This file holds all the generic functions and is also the starting point for all of the class 
 * function callouts, POST/GET requests etc.
 */


if (isset($_POST['deleteServer'], $_POST['server_id']) && intval($_POST['deleteServer']) === 1 && intval($_POST['server_id']) > 0 && User::canPerformAction($sql, $_SESSION['user_id'], Constants::$PANEL_ADMIN)) {
    $data = Server::getServersWithHostAndGame($sql, null, $_POST['server_id']);
    if (sizeof($data) === 1) {
        $data = $data[0];
        $host = new Host($data['host_id'], $data['servername'], $data['hostname'], $data['sshport'], $data['host_username'], $data['host_password']);
        $game = new Game($data['game_id'], $data['game_name'], $data['game_location'], $data['startscript']);
        $server = new Server($data['server_id'], $host, $data['server_name'], $game, $data['server_port'], $data['server_account'], $data['server_password'], $data['server_status'], $data['server_startscript'], $data['current_players'], $data['max_players'], $data['rconpassword']);
        if ($server->deleteServer($sql)) {
            die(json_encode(array("href" => "../")));
        } else {
            die(json_encode(array("error" => Constants::$ERRORS['GENERIC_ERROR'])));
        }
    }
}


if (isset($_POST['disableServer'], $_POST['server_id']) && intval($_POST['disableServer']) === 1 && intval($_POST['server_id']) === 1 && User::canPerformAction($sql, $_SESSION['user_id'], Constants::$PANEL_ADMIN)) {
    $data = Server::getServersWithHostAndGame($sql, null, $_POST['server_id']);
    if (sizeof($data) === 1) {
        $data = $data[0];
        $host = new Host($data['host_id'], $data['servername'], $data['hostname'], $data['sshport'], $data['host_username'], $data['host_password']);
        $game = new Game($data['game_id'], $data['game_name'], $data['game_location'], $data['startscript']);
        $server = new Server($data['server_id'], $host, $data['server_name'], $game, $data['server_port'], $data['server_account'], $data['server_password'], $data['server_status'], $data['server_startscript'], $data['current_players'], $data['max_players'], $data['rconpassword']);
        if ($server->disableServer($sql)) {
            die(json_encode(array("msg" => "Server successfully disabled")));
        } else {
            die(json_encode(array("error" => Constants::$ERRORS['GENERIC_ERROR'])));
        }
    }
}

if (isset($_POST['enableServer'], $_POST['server_id']) && intval($_POST['enableServer']) === 1 && intval($_POST['server_id']) === 1 && User::canPerformAction($sql, $_SESSION['user_id'], Constants::$PANEL_ADMIN)) {
    $data = Server::getServersWithHostAndGame($sql, null, $_POST['server_id']);
    if (sizeof($data) === 1) {
        $data = $data[0];
        $host = new Host($data['host_id'], $data['servername'], $data['hostname'], $data['sshport'], $data['host_username'], $data['host_password']);
        $game = new Game($data['game_id'], $data['game_name'], $data['game_location'], $data['startscript']);
        $server = new Server($data['server_id'], $host, $data['server_name'], $game, $data['server_port'], $data['server_account'], $data['server_password'], $data['server_status'], $data['server_startscript'], $data['current_players'], $data['max_players'], $data['rconpassword']);
        if ($server->enableServer($sql)) {
            die(json_encode(array("msg" => "Server successfully enabled")));
        } else {
            die(json_encode(array("error" => Constants::$ERRORS['GENERIC_ERROR'])));
        }
    }
}

if (isset($_POST['startServer'], $_POST['server_id']) && intval($_POST['startServer']) === 1 && intval($_POST['server_id']) > 0) {
    //$data = Server::getServersWithHost($sql, $_POST['server_id']);
    $data = Server::getServersWithHostAndGame($sql, $_SESSION['user_id'], $_POST['server_id']);
    if (sizeof($data) === 1) {
        $data = $data[0];
        if (intval($data['can_stop_server']) === 1 && intval($data['server_status']) !== Constants::$SERVER_DISABLED) {
            $host = new Host($data['host_id'], $data['servername'], $data['hostname'], $data['sshport'], $data['host_username'], $data['host_password']);
            $game = new Game($data['game_id'], $data['game_name'], $data['game_location'], $data['startscript']);
            $server = new Server($data['server_id'], $host, $data['server_name'], $game, $data['server_port'], $data['server_account'], $data['server_password'], $data['server_status'], $data['server_startscript'], $data['current_players'], $data['max_players'], $data['rconpassword']);
            if ($server->startServer($sql)) {
                die(json_encode(array("msg" => "Server successfully started")));
            } else {
                die(json_encode(array("error" => Constants::$ERRORS['GENERIC_ERROR'])));
            }
        } else {
            die(json_encode(array("error" => Constants::$ERRORS['SERVER_DISABLED_OR_NOT_AUTHORIZED'])));
        }
        
    
    }
    
}

if (isset($_POST['stopServer'], $_POST['server_id']) && intval($_POST['stopServer']) === 1 && intval($_POST['server_id']) > 0) {
    //$data = Server::getServersWithHost($sql, $_POST['server_id']);
    $data = Server::getServersWithHostAndGame($sql, $_SESSION['user_id'], $_POST['server_id']);
    if (sizeof($data) === 1) {
        $data = $data[0];
        if (intval($data['can_stop_server']) === 1 && intval($data['server_status']) !== Constants::$SERVER_DISABLED) {
            $host = new Host($data['host_id'], $data['servername'], $data['hostname'], $data['sshport'], $data['host_username'], $data['host_password']);
            $game = new Game($data['game_id'], $data['game_name'], $data['game_location'], $data['startscript']);
            $server = new Server($data['server_id'], $host, $data['server_name'], $game, $data['server_port'], $data['server_account'], $data['server_password'], $data['server_status'], $data['server_startscript'], $data['current_players'], $data['max_players'], $data['rconpassword']);
            if ($server->stopServer($sql)) {
                die(json_encode(array("msg" => "Server successfully stopped")));
            } else {
                die(json_encode(array("error" => Constants::$ERRORS['GENERIC_ERROR'])));
            }
        } else {
            die(json_encode(array("error" => Constants::$ERRORS['SERVER_DISABLED_OR_NOT_AUTHORIZED'])));
        }
        
    
    }
    
}


if (isset($_POST['addServer'], $_POST['server_name'], $_POST['server_port'], $_POST['server_account'], $_POST['server_password'], $_POST['max_players'], $_POST['rconpassword']) && intval($_POST['addServer']) === 1) {
    $getHost = Host::getHosts($sql, $_POST['host_id'], true);
    if (sizeof($getHost) === 1) {
        $getGame = Game::getGames($sql, $_POST['game_id']);
        if (sizeof($getGame) === 1) {
            $getGame = $getGame[0];
            $getHost = $getHost[0];
            $host = new Host($getHost['host_id'], $getHost['servername'], $getHost['hostname'], $getHost['sshport'], $getHost['host_username'], $getHost['host_password']);
            $game = new Game($getGame['game_id'], $getGame['game_name'], $getGame['game_location'], $getGame['startscript']);
            $server = new Server(null, $host, $_POST['server_name'], $game, $_POST['server_port'], $_POST['server_account'], $_POST['server_password'], null, null, null, $_POST['max_players'], $_POST['rconpassword']);
            if (strlen(trim($_POST['server_password'])) === 0) {
                $server->setServer_password(generateRandomKey(8));
            }
            
            $dat = $server->addServer($sql);
            if (isset($dat['error'])) {
                die(json_encode($dat));
            }
            die(json_encode(array("href" => ".")));
        }
        
    }
}

if (isset($_POST['updateHost'], $_POST['hostId'], $_POST['servername'], $_POST['hostname'], $_POST['sshport'], $_POST['host_username'], $_POST['host_password']) && intval($_POST['updateHost']) === 1) {
    $host = new Host($_POST['hostId'], $_POST['servername'], $_POST['hostname'], $_POST['sshport'], $_POST['host_username'], $_POST['host_password']);
    $out = $host->updateHost($sql);
    if (isset($out['rows_affected']) && intval($out['rows_affected']) === 1) {
        die(json_encode(array("href" => ".")));
    } else {
        die(json_encode($out));
    }
}

if (isset($_POST['deleteHost'], $_POST['hostId']) && intval($_POST['deleteHost']) === 1 ) {
    if (User::canPerformAction($sql, $_SESSION['user_id'], 3)) {
        $dat = Host::deleteHost($sql, $_POST['hostId']);
        if (isset($dat['error'])) {
            die(json_encode($dat));
        } else {
            die(json_encode(array("href" => ".")));
        }
    } else {
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_PRIVILEGE_ERROR'])));
    }
}

if (isset($_POST['getHostData'], $_POST['host_id']) && intval($_POST['getHostData']) === 1 && intval($_POST['host_id']) > 0) {
    die(json_encode(Host::getHosts($sql, $_POST['host_id'])[0]));
}

if (isset($_POST['addHost'], $_POST['servername'], $_POST['hostname'], $_POST['sshport'], $_POST['host_username'], $_POST['host_password']) && intval($_POST['addHost']) === 1) {
    $host = new Host(null, $_POST['servername'], $_POST['hostname'], $_POST['sshport'], $_POST['host_username'], $_POST['host_password']);
    $dat = $host->addHost($sql);
    if (isset($dat['rows_affected']) && intval($dat['rows_affected']) === 1) {
        die(json_encode(array("href" => ".")));
    } else {
        die(json_encode($dat));
    }
}


if (isset($_POST['updateGame'], $_POST['gameId'], $_POST['game_name'], $_POST['game_location'], $_POST['startscript']) && intval($_POST['updateGame']) === 1) {
    $dat = Game::updateGame($sql, $_POST['gameId'], $_POST['game_name'], $_POST['game_location'], $_POST['startscript']);
    if (intval($dat['rows_affected']) === 1) {
        die(json_encode(array("href" => ".")));
    } else {
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_ERROR'])));
    }
}


if (isset($_POST['deleteGame'], $_POST['gameId']) && intval($_POST['deleteGame']) === 1) {
    $dat = Game::deleteGame($sql, $_POST['gameId']);
    if (isset($dat['error'])) {
        die(json_encode($dat));
    } else {
        die(json_encode(array("href" => ".")));
    }
}

if (isset($_POST['getGame'], $_POST['game_id']) && intval($_POST['game_id']) > 0) {
    $dat = Game::getGames($sql, $_POST['game_id']);
    if (sizeof($dat) === 1) {
        $dat = $dat[0];
        die(json_encode($dat));
    } else {
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_ERROR'])));
    }
}

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

if (isset($_POST['addGame'], $_POST['game_name'], $_POST['game_location'], $_POST['startscript']) && intval($_POST['addGame']) === 1) {
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
