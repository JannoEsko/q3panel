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
    } else if (isset($out['msg'])) {
        Logger::logServer($sql, $server['server_id'], 0, gethostbyname(gethostname()), Constants::$SERVER_LOG_SEVERITIES['info']['level'], $out['msg']);
        echo $out['msg'] . " Server name: " . $server['server_name'] . ", hostname: " . $server['hostname'] . "\n";
    } else if (isset($out['error'])) {
        Logger::logServer($sql, $server['server_id'], 0, gethostbyname(gethostname()), Constants::$SERVER_LOG_SEVERITIES['error']['level'], $out['error']);
        echo $out['error'] . " Server name: " . $server['server_name'] . ", hostname: " . $server['hostname'] . "\n";
    }
}
