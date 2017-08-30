<?php

/*
 * To make this run, add this into your crontab. If you got a lot of servers (well above 15-20), set it to run 
 * every 2 minutes. Otherwise, once per minute is okay.
 */

$d = __DIR__;
require_once "$d/classes/sql/SQL.php";
require_once "$d/config.php";
require_once "$d/classes/Constants.php";
require_once "$d/classes/ssh/SSH.php";
require_once "$d/classes/servers/host/Host.php";
require_once "$d/classes/servers/Game.php";
require_once "$d/classes/servers/Server.php";
require_once "$d/classes/logger/Logger.php";
require_once "$d/classes/email/Email.php";
require_once "$d/classes/users/User.php";


$sql->query(Constants::$UPDATE_QUERIES['SET_NOT_STARTED_SERVER_PLAYER_COUNTS'], array(Constants::$SERVER_STARTED));
//Lets get all the servers we should check (status = 2 aka running).

$servers = Server::getRunningServers($sql);

foreach ($servers as $server) {
    $host = new Host($server['host_id'], $server['servername'], $server['hostname'], $server['sshport'], $server['host_username'], $server['host_password']);
    $game = new Game($server['game_id'], $server['game_name'], $server['game_location'], $server['startscript']);
    $srv = new Server($server['server_id'], $host, $server['server_name'], $game, $server['server_port'], $server['server_account'], $server['server_password'], $server['server_status'], $server['server_startscript'], $server['current_players'], $server['max_players'], $server['rconpassword']);
    $out = $srv->checkServer($sql);
    if (isset($out['players'])) {
        $query = Constants::$UPDATE_QUERIES['SET_SERVER_PLAYERS_BY_ID'];
        $players = intval(trim($out['players']));
        $params = array($players, $server['server_id']);
        $sql->query($query, $params);
        echo "Set server " . $server['server_name'] . " (on hostname " . $server['hostname'] . ") players to " . $players . "\n";
        if (intval($srv->getServer_id()) === 2) {
            $str = $srv->sendQ3Command(Constants::$SERVER_ACTIONS['Q3_RCON_COMMAND'] . "g_enablehash", true, true);
            $str = str_replace("\xFF\xFF\xFF\xFFprint", "", $str);
            $str = str_replace("\n", "", $str);
            $str = preg_replace("/(\^.)/", "", $str);
            $str = str_replace("\"", "\n", $str);
            $arr = explode("\n", $str);
            for ($i = 0; $i < sizeof($arr); $i++) {
                $val = $arr[$i];
                if (trim($val) === "g_enablehash") {
                    $i++;
                    $val = $arr[$i];
                    if (trim($val) === "is:") {
                        $i++;
                        $hashval = intval(trim($arr[$i]));
                        if ($hashval !== 0) {
                            Logger::logServer($sql, $srv->getServer_id(), 0, gethostbyname(gethostname()), Constants::$SERVER_LOG_SEVERITIES['error']['level'], "Shutted down the server, because the crontask detected that the value of g_enablehash is 1. Fuck you Robert!");
                            $srv->stopServer($sql);
                        } 
                    }
                }
            }
        }
    } else if (isset($out['msg'])) {
        Logger::logServer($sql, $server['server_id'], 0, gethostbyname(gethostname()), Constants::$SERVER_LOG_SEVERITIES['info']['level'], $out['msg']);
        Email::notifyServerUsers($sql, Constants::$PANEL_ADMIN, Constants::$EMAIL_TEMPLATE['SERVER_REBOOT_TITLE'], Constants::$EMAIL_TEMPLATE['SERVER_REBOOT_MSG'], array("{server_id}" => $srv->getServer_id(), "{server_name}" => $srv->getServer_name(), "{out_msg}" => $out['msg']));
        echo $out['msg'] . " Server name: " . $server['server_name'] . ", hostname: " . $server['hostname'] . "\n";
    } else if (isset($out['error'])) {
        Logger::logServer($sql, $server['server_id'], 0, gethostbyname(gethostname()), Constants::$SERVER_LOG_SEVERITIES['error']['level'], $out['error']);
        Email::notifyServerUsers($sql, Constants::$PANEL_ADMIN, Constants::$EMAIL_TEMPLATE['SERVER_DOWN_ERR_TITLE'], Constants::$EMAIL_TEMPLATE['SERVER_DOWN_ERR_MSG'], array("{server_id}" => $srv->getServer_id(), "{server_name}" => $srv->getServer_name(), "{out_err}" => $out['error']));
        echo $out['error'] . " Server name: " . $server['server_name'] . ", hostname: " . $server['hostname'] . "\n";
    }
    
}
