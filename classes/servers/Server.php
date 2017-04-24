<?php
require_once __DIR__ . "/../ssh/SSH.php";
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Server
 *
 * @author Janno
 */
class Server extends SSH {
    
    private $host;
    private $server_name;
    private $game;
    private $server_port;
    private $server_account;
    private $server_password;
    private $server_status;
    private $server_startscript;
    private $current_players;
    private $max_players;
    private $rconpassword;
    private $ssh;
    private $server_id;
    
    function __construct($server_id, Host $host, $server_name, Game $game, $server_port, $server_account, $server_password, $server_status, $server_startscript, $current_players, $max_players, $rconpassword) {
        $this->server_id = $server_id;
        $this->host = $host;
        $this->server_name = $server_name;
        $this->game = $game;
        $this->server_port = $server_port;
        $this->server_account = $server_account;
        $this->server_password = $server_password;
        $this->server_status = $server_status;
        $this->server_startscript = $server_startscript;
        $this->current_players = $current_players;
        $this->max_players = $max_players;
        $this->rconpassword = $rconpassword;
        $this->ssh = parent::__construct($this->host->getHostname(), $this->host->getSshport(), $server_account, $server_password);
    }
    
    function getHost() {
        return $this->host;
    }

    function getServer_name() {
        return $this->server_name;
    }

    function getGame() {
        return $this->game;
    }

    function getServer_port() {
        return $this->server_port;
    }

    function getServer_account() {
        return $this->server_account;
    }

    function getServer_password() {
        return $this->server_password;
    }

    function getServer_status() {
        return $this->server_status;
    }

    function getServer_startscript() {
        return $this->server_startscript;
    }

    function getCurrent_players() {
        return $this->current_players;
    }

    function getMax_players() {
        return $this->max_players;
    }
    function getServer_id() {
        return $this->server_id;
    }

    function setServer_id($server_id) {
        $this->server_id = $server_id;
    }

    function getRconpassword() {
        return $this->rconpassword;
    }

    function getSsh() {
        return $this->ssh;
    }

    function setServer_name($server_name) {
        $this->server_name = $server_name;
    }

    function setServer_port($server_port) {
        $this->server_port = $server_port;
    }

    function setServer_account($server_account) {
        $this->server_account = $server_account;
    }

    function setServer_password($server_password) {
        $this->server_password = $server_password;
    }

    function setServer_status($server_status) {
        $this->server_status = $server_status;
    }

    function setServer_startscript($server_startscript) {
        $this->server_startscript = $server_startscript;
    }

    function setCurrent_players($current_players) {
        $this->current_players = $current_players;
    }

    function setMax_players($max_players) {
        $this->max_players = $max_players;
    }

    function setRconpassword($rconpassword) {
        $this->rconpassword = $rconpassword;
    }


    function startServer(SQL $sql) {
        $query = Constants::$UPDATE_QUERIES['SET_SERVER_STATUS'];
        $params = array(Constants::$SERVER_STARTED, $this->server_id);
        $startServer = Constants::$SSH_COMMANDS['START_SERVER'];
        $startServer = str_replace("{server_account}", $this->server_account, $startServer);
        $this->server_startscript = str_replace("{server_port}", $this->server_port, $this->server_startscript);
        $this->server_startscript = str_replace("{server_account}", $this->server_account, $this->server_startscript);
        $this->server_startscript = str_replace("{rconpassword}", $this->rconpassword, $this->server_startscript);
        $this->server_startscript = str_replace("{max_clients}", $this->max_players, $this->server_startscript);
        $startServer = str_replace("{server_startscript}", $this->server_startscript, $startServer);
        $this->sendCommand($startServer);
        $sql->query($query, $params);
        return true;
    }
    
    function stopServer(SQL $sql) {
        $query = Constants::$UPDATE_QUERIES['SET_SERVER_STATUS'];
        $params = array(Constants::$SERVER_STOPPED, $this->server_id);
        $getScreenPID = Constants::$SSH_COMMANDS['GET_SCREEN_PID'];
        $getScreenPID = str_replace("{server_account}", $this->server_account, $getScreenPID);
        $output = $this->sendCommand($getScreenPID, true);
        if (strlen($output['stdio']) !== 0) {
            $pid = trim($output['stdio']);
            $killServer = Constants::$SSH_COMMANDS['STOP_SERVER'];
            $killServer = str_replace("{screen_pid}", $pid, $killServer);
            $this->sendCommand($killServer);
        } else if (strlen($output['stderr']) !== 0) {
            return $output;
        }
        $sql->query($query, $params);
        return true;
    }
    
    function restartServer($sql) {
        return $this->stopServer($sql) && $this->startServer($sql);
    }
    
    function deleteServer(SQL $sql) {
        $this->stopServer($sql);
        $removeServerQuery = Constants::$DELETE_QUERIES['DELETE_SERVER_BY_ID'];
        $removeMappingsQuery = Constants::$DELETE_QUERIES['DELETE_NONEXISTANT_MAPPINGS'];
        $removeServerQueryParams = array($this->server_id);
        $removeServerSSH = Constants::$SSH_COMMANDS['DELETE_ACCOUNT'];
        $removeServerSSH = str_replace("{server_account}", $this->server_account, $removeServerSSH);
        $this->host->sendCommand($removeServerSSH);
        $sql->query($removeServerQuery, $removeServerQueryParams);
        $sql->query($removeMappingsQuery);
        return true;
    }
    
    function changeServerAccountPassword(SQL $sql, $newPass) {
        $change_password = Constants::$SSH_COMMANDS['CHANGE_PASSWORD'];
        $change_password = str_replace("{server_account}", $this->server_account, $change_password);
        $change_password = str_replace("{server_password}", $newPass, $change_password);
        $out = $this->host->sendCommand($change_password, true);
        if (strlen(trim($out['stderr'])) > 0) {
            return array("error" => $out['stderr']);
        } else {
            $sql->query(Constants::$UPDATE_QUERIES['SET_NEW_SERVER_ACCOUNT_PASSWORD'], array($newPass, $this->server_id));
            return true;
        }
    }
    
    function updateServer(SQL $sql) {
        $query = Constants::$UPDATE_QUERIES['UPDATE_SERVER_BY_ID'];
        $params = array($this->server_name, $this->server_port, $this->max_players, $this->rconpassword, $this->server_id);
        try {
            return $sql->query($query, $params);
        } catch (PDOException $ex) {
            return array("error" => $ex->getMessage());
        }
        
    }
    
    function disableServer(SQL $sql) {
        if ($this->stopServer($sql)) {
            $query = Constants::$UPDATE_QUERIES['SET_SERVER_STATUS'];
            $params = array(Constants::$SERVER_DISABLED, $this->server_id);
            $sql->query($query, $params);
            return true;
        } else {
            return false;
        }
    }
    
    function enableServer(SQL $sql) {
        $query = Constants::$UPDATE_QUERIES['SET_SERVER_STATUS'];
        $params = array(Constants::$SERVER_STOPPED, $this->server_id);
        $sql->query($query, $params);
        return true;
    }
    
    function addServer(SQL $sql) {
        //start off by checking if we need to autofill port and username.
        $getServers = self::getServers($sql);
        $getPort = false;
        $gotPort = false;
        if (intval($this->server_port) === 0) {
            $getPort = true;
        }
        $getServerAccount = false;
        $newAccountId = 0;
        $gotAccountId = false;
        if (strlen(trim($this->server_account)) === 0) {
            $getServerAccount = true;
        }
        foreach ($getServers as $server) {
            if ($getPort && intval($server['server_port']) >= $this->server_port) {
                $this->server_port = intval($server['server_port']) + 1;
                $gotPort = true;
            } else {
                if (intval($server['server_port']) === intval($this->server_port)) {
                    return array("error" => "Server port " . $this->server_port . " already exists. Please change and submit again.");
                }
            }
            if ($getServerAccount) {
                $account = $server['server_account'];
                $account = intval(substr_replace($account, "", 0, 3));
                if ($account >= $newAccountId) {
                    $newAccountId = $account + 1;
                    $this->server_account = "srv" . $newAccountId;
                    $gotAccountId = true;
                }
            } else {
                if (trim($server['server_account']) === trim($this->server_account)) {
                    return array("error" => "Server account " . $this->server_account . " already exists. Please change and submit again.");
                }
            }
            if (trim($server['server_name']) === trim($this->server_name)) {
                return array("error" => "Server name " . $this->server_name . " already exists. Please change and submit again.");
            }
        }
        if (!$gotPort && $getPort) {
            $this->server_port = 20100;
        }
        
        if (!$gotAccountId && $getServerAccount) {
            $this->server_account = "srv1";
        }
        $add_user = Constants::$SSH_COMMANDS['ADD_USER'];
        $add_user = str_replace("{server_account}", $this->server_account, $add_user);
        $change_password = Constants::$SSH_COMMANDS['CHANGE_PASSWORD'];
        $change_password = str_replace("{server_account}", $this->server_account, $change_password);
        $change_password = str_replace("{server_password}", $this->server_password, $change_password);
        $copy_game_files = Constants::$SSH_COMMANDS['COPY_GAME_FILES'];
        $copy_game_files = str_replace("{game_location}", $this->game->getGame_location(), $copy_game_files);
        $copy_game_files = str_replace("{server_account}", $this->server_account, $copy_game_files);
        $chown_game_files = Constants::$SSH_COMMANDS['CHOWN_GAME_FILES'];
        $chown_game_files = str_replace("{server_account}", $this->server_account, $chown_game_files);
        //These actions must be done with an account which can perform these actions.
        $out1 = $this->host->sendCommand($add_user, true);
        if (strlen(trim($out1['stderr'])) > 0) {
            return array("error" => $out1['stderr']);
        }
        $out2 = $this->host->sendCommand($change_password, true);
        if (strlen(trim($out2['stderr'])) > 0) {
            return array("error" => $out2['stderr']);
        }
        $out3 = $this->host->sendCommand($copy_game_files, true);
        if (strlen(trim($out3['stderr'])) > 0) {
            return array("error" => $out3['stderr']);
        }
        $out4 = $this->host->sendCommand($chown_game_files, true);
        if (strlen(trim($out4['stderr'])) > 0) {
            return array("error" => $out4['stderr']);
        }
        $serverInsertQuery = Constants::$INSERT_QUERIES['ADD_NEW_SERVER'];
        $serverInsertParams = array(
            $this->host->getHost_id(),
            $this->game->getGame_id(),
            $this->server_name,
            $this->server_port,
            $this->server_account,
            $this->server_password,
            1,
            $this->game->getStartscript(),
            0,
            $this->getMax_players(),
            $this->rconpassword
        );
        
        try {
            $serverInsert = $sql->query($serverInsertQuery, $serverInsertParams);
            $serverMapping = Constants::$INSERT_QUERIES['ADD_NEW_SERVER_MAPPING'];
            $serverMappingParams = array($serverInsert['last_insert_id']);
            $sql->query($serverMapping, $serverMappingParams);
        } catch (PDOException $ex) {
            return array("error" => $ex->getMessage());
        }
        
        if (isset($serverInsert['last_insert_id']) && intval($serverInsert['last_insert_id']) > 0) {
            return array($out1, $out2, $out3, $out4);
        }
        return array("error" => Constants::$ERRORS['GENERIC_ERROR']);
        
    }
    
    function sendQ3Command($command, $output = false, $use_rcon = false) {
        if ($use_rcon) {
            $command = str_replace("{rconpassword}", $this->rconpassword, $command);
        }
        $resource = fsockopen("udp://" . gethostbyname($this->host->getHostname()), $this->server_port);
        stream_set_timeout($resource, Constants::$Q3_RESOURCE_TIMEOUT);
        if ($output) {
            stream_set_blocking($resource, true);
            fwrite($resource, $command);
            $out = stream_get_contents($resource);
            fclose($resource);
            return $out;
        } else {
            fwrite($resource, $command);
            fclose($resource);
        }
    }
    
    function checkServer(SQL $sql) {
        //first, check if screen is up.
        $getScreenPID = Constants::$SSH_COMMANDS['GET_SCREEN_PID'];
        $getScreenPID = str_replace("{server_account}", $this->server_account, $getScreenPID);
        $out = $this->sendCommand($getScreenPID, true);
        if (strlen(trim($out['stderr'])) === 0) {
            $pid = intval(trim($out['stdio']));
            if ($pid > 0) {
                //means screen is up, time to check the server itself.
                $q3command = Constants::$SERVER_ACTIONS['GET_Q3_SERVER_INFO'];
                $out = $this->sendQ3Command($q3command, true);
                $outArr = explode("\\", $out);
                $serverStatusGot = false;
                $currentPlayers = 0;
                
                for ($i = 0; $i < sizeof($outArr); $i++) {
                    if (trim($outArr[$i]) === "clients") {
                        $i++;
                        $currentPlayers = intval(trim($outArr[$i]));
                        $serverStatusGot = true;
                    }
                }
                if ($serverStatusGot) {
                    return array("players" => $currentPlayers);
                } else {
                    //most likely we didnt get any output from the server.
                    $reboot = $this->restartServer($sql);
                    if ($reboot) {
                        return array("msg" => Constants::$SERVER_ACTIONS['SERVER_REBOOT_SUCCESS_NO_INFOMSG']);
                    } else {
                        return array("error" => Constants::$SERVER_ACTIONS['SERVER_REBOOT_ERROR_NO_INFOMSG']);
                    }
                }
            } else {
                $reboot = $this->restartServer($sql);
                if ($reboot) {
                    return array("msg" => Constants::$SERVER_ACTIONS['SERVER_PID_DOWN_REBOOT_SUCCESSFUL']);
                } else {
                    return array("error" => Constants::$SERVER_ACTIONS['SERVER_PID_DOWN_REBOOT_ERROR']);
                }
            }
        } else {
            return $out;
        }
        
    }
    
    static function getServers(SQL $sql, $server_id = null) {
        $query = "";
        $params = null;
        if ($server_id !== null) {
            $query = Constants::$SELECT_QUERIES['GET_SERVER_BY_ID'];
            $params = array($server_id);
        } else {
            $query = Constants::$SELECT_QUERIES['GET_SERVERS'];
        }
        return $sql->query($query, $params);
    }
    
    static function getRunningServers(SQL $sql) {
        $query = Constants::$SELECT_QUERIES['GET_SERVERS_WITH_HOST_AND_GAME_BY_STATUS'];
        $params = array(Constants::$SERVER_STARTED);
        return $sql->query($query, $params);
    }
    
    static function removeUserFromMapping(SQL $sql, $server_id, $user_id) {
        //first check if the user is a panel admin (so they can't be removed).
        if (User::checkUser($sql, $user_id, Constants::$PANEL_ADMIN) === false) {
            $query = Constants::$DELETE_QUERIES['REMOVE_USER_FROM_SERVER_MAP'];
            $params = array($server_id, $user_id);
            return $sql->query($query, $params);
        } else {
            return false;
        }
        
    }
    
    static function addUserMapping(SQL $sql, $server_id, $user_id, $can_stop_server, $can_see_rcon, $can_see_ftp) {
        if (User::checkUser($sql, $user_id, Constants::$PANEL_ADMIN) === false) {
            $query = Constants::$INSERT_QUERIES['ADD_USER_TO_SERVER_MAP'];
            $params = array($server_id, $user_id, $can_stop_server, $can_see_rcon, $can_see_ftp);
            return $sql->query($query, $params);
        } else {
            return false;
        }
    }
    
    static function editUserMapping(SQL $sql, $server_id, $user_id, $can_stop_server, $can_see_rcon, $can_see_ftp) {
        //first check if the user is a panel admin (so they can't be removed).
        if (User::checkUser($sql, $user_id, Constants::$PANEL_ADMIN) === false) {
            $query = Constants::$UPDATE_QUERIES['UPDATE_SERVER_MAP_BY_SERVER_ID_USER_ID'];
            $params = array($can_stop_server, $can_see_rcon, $can_see_ftp, $server_id, $user_id);
            return $sql->query($query, $params);
        } else {
            return false;
        }
        
    }
    
    static function getServersWithHost(SQL $sql, $server_id = null, $host_id = null) {
        $query = "";
        $params = null;
        if ($server_id !== null && $host_id !== null) {
            $query = Constants::$SELECT_QUERIES['GET_SERVERS_WITH_HOST_BY_HOST_ID_SERVER_ID'];
            $params = array($host_id, $server_id);
        } else if ($server_id !== null) {
            $query = Constants::$SELECT_QUERIES['GET_SERVERS_WITH_HOST_BY_SERVER_ID'];
            $params = array($server_id);
        } else if ($host_id !== null) {
            $query = Constants::$SELECT_QUERIES['GET_SERVERS_WITH_HOST_BY_HOST_ID'];
            $params = array($host_id);
        } else {
            $query = Constants::$SELECT_QUERIES['GET_SERVERS_WITH_HOST'];
        }
        return $sql->query($query, $params);
    }
    
    static function getServersWithHostAndGame(SQL $sql, $user_id = null, $server_id = null, $host_id = null, $game_id = null) {
        $query = "";
        $params = null;
        if ($user_id !== null && $server_id !== null) {
            $query = Constants::$SELECT_QUERIES['GET_SERVER_WITH_MAP'];
            $params = array($server_id, $user_id);
        } else if ($user_id !== null) {
            //means we want to get it all for the servers page.
            $query = Constants::$SELECT_QUERIES['GET_SERVERS_WITH_MAP'];
            $params = array($user_id);
        } else if ($server_id !== null && $host_id !== null && $game_id !== null) {
            $query = Constants::$SELECT_QUERIES['GET_SERVERS_WITH_HOST_AND_GAME_BY_HOST_ID_SERVER_ID_GAME_ID'];
            $params = array($host_id, $server_id, $game_id);
        } else if ($server_id !== null && $host_id !== null) {
            $query = Constants::$SELECT_QUERIES['GET_SERVERS_WITH_HOST_AND_GAME_BY_HOST_ID_SERVER_ID'];
            $params = array($host_id, $server_id);
        } else if ($server_id !== null && $game_id !== null) {
            $query = Constants::$SELECT_QUERIES['GET_SERVERS_WITH_HOST_AND_GAME_BY_SERVER_ID_GAME_ID'];
            $params = array($server_id, $game_id);
        } else if ($host_id !== null && $game_id !== null) {
            $query = Constants::$SELECT_QUERIES['GET_SERVERS_WITH_HOST_AND_GAME_BY_HOST_ID_GAME_ID'];
            $params = array($host_id, $game_id);
        } else if ($game_id !== null) {
            $query = Constants::$SELECT_QUERIES['GET_SERVERS_WITH_HOST_AND_GAME_BY_GAME_ID'];
            $params = array($game_id);
        } else if ($server_id !== null) {
            $query = Constants::$SELECT_QUERIES['GET_SERVERS_WITH_HOST_AND_GAME_BY_SERVER_ID'];
            $params = array($server_id);
        } else if ($host_id !== null) {
            $query = Constants::$SELECT_QUERIES['GET_SERVERS_WITH_HOST_AND_GAME_BY_HOST_ID'];
            $params = array($host_id);
        } else {
            $query = Constants::$SELECT_QUERIES['GET_SERVERS_WITH_HOST_AND_GAME'];
        }
        return $sql->query($query, $params);
    }
    
    static function mapUserToAllServers(SQL $sql, $user_id) {
        $query = Constants::$INSERT_QUERIES['MAP_USER_TO_ALL_SERVERS'];
        $params = array($user_id);
        $sql->query($query, $params);
    }
    
    static function getServersWithUserMapping(SQL $sql, $server_id) {
        $query = Constants::$SELECT_QUERIES['GET_MAP_WITH_SERVER_WITH_USERS_BY_SERVER_ID'];
        $params = array($server_id);
        return $sql->query($query, $params);
    }

}
