<?php

/**
 * API class, handles all of the API calls.
 * @author Janno
 */
class API {
    
    private $username;
    private $password;
    
    /**
     * Constructs the API object.
     * @param string $username The username of the requestor.
     * @param string $password The password of the requestor.
     */
    function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }
    
    /**
     * Gets the servers.
     * $param SQL $sql The SQL handle.
     * @param int $server_id [optional] The server ID, if not specified gets all the servers.
     */
    public function getServers(SQL $sql, $server_id = null) {
        require_once __DIR__ . "/../Constants.php";
        require_once __DIR__ . "/../users/User.php";
        require_once __DIR__ . "/../servers/Server.php";
        $user = new User($this->username, $this->password);
        $dat = $user->authenticate($sql);
        if (!isset($dat['error'])) {
            if ($server_id !== null || strlen($server_id) > 0) {
                $dat = Server::getServersWithHostAndGame($sql, $dat['user_id'], $server_id);
                for ($i = 0; $i < sizeof($dat); $i++) {
                    $dat[$i]['server_password'] = "";
                }
                return $dat;
            }
            $dat = Server::getServersWithHostAndGame($sql, $dat['user_id']);
            for ($i = 0; $i < sizeof($dat); $i++) {
                $dat[$i]['server_password'] = "";
            }
            return $dat;
        } else {
            return "NOT_AUTHORIZED";
        }
        
    }
    
    /**
     * Stops the server
     * @param SQL $sql The SQL handle.
     * @param int $server_id The server ID which to stop.
     * @return string Returns the API message.
     */
    public function stopServer(SQL $sql, $server_id) {
        require_once __DIR__ . "/../Constants.php";
        require_once __DIR__ . "/../users/User.php";
        require_once __DIR__ . "/../servers/host/Host.php";
        require_once __DIR__ . "/../ssh/SSH.php";
        require_once __DIR__ . "/../servers/Server.php";
        require_once __DIR__ . "/../servers/Game.php";
        $user = new User($this->username, $this->password);
        $dat = $user->authenticate($sql);
        if (intval($dat['group_id']) !== Constants::$PANEL_ADMIN) {
            return "NOT_AUTHORIZED";
        }
        if (!isset($dat['error'])) {
            $srvData = Server::getServersWithHostAndGame($sql, $dat['user_id'], $server_id);
            if (sizeof($srvData) === 1) {
                $srvData = $srvData[0];
                $host = new Host($srvData['host_id'], $srvData['servername'], $srvData['hostname'], $srvData['sshport'], $srvData['host_username'], $srvData['host_password']);
                $game = new Game($srvData['game_id'], $srvData['game_name'], $srvData['game_location'], $srvData['startscript']);
                $server = new Server($srvData['server_id'], $host, $srvData['server_name'], $game, $srvData['server_port'], $srvData['server_account'], $srvData['server_password'], $srvData['server_status'], $srvData['server_startscript'], $srvData['current_players'], $srvData['max_players'], $srvData['rconpassword']);
                $dat = $server->stopServer($sql);
                if ($dat === true) {
                    return "SERVER_STOPPED";
                } else {
                    return $dat;
                }
            } else {
                return "BAD_METHOD_CALL";
            }
        } else {
            return "NOT_AUTHORIZED";
        }
    }
    
    /**
     * Starts the server
     * @param SQL $sql The SQL handle.
     * @param int $server_id The server ID which to start.
     * @return string Returns the API message.
     */
    public function startServer(SQL $sql, $server_id) {
        require_once __DIR__ . "/../Constants.php";
        require_once __DIR__ . "/../users/User.php";
        require_once __DIR__ . "/../servers/host/Host.php";
        require_once __DIR__ . "/../ssh/SSH.php";
        require_once __DIR__ . "/../servers/Server.php";
        require_once __DIR__ . "/../servers/Game.php";
        $user = new User($this->username, $this->password);
        $dat = $user->authenticate($sql);
        if (intval($dat['group_id']) !== Constants::$PANEL_ADMIN) {
            return "NOT_AUTHORIZED";
        }
        if (!isset($dat['error'])) {
            $srvData = Server::getServersWithHostAndGame($sql, $dat['user_id'], $server_id);
            if (sizeof($srvData) === 1) {
                $srvData = $srvData[0];
                $host = new Host($srvData['host_id'], $srvData['servername'], $srvData['hostname'], $srvData['sshport'], $srvData['host_username'], $srvData['host_password']);
                $game = new Game($srvData['game_id'], $srvData['game_name'], $srvData['game_location'], $srvData['startscript']);
                $server = new Server($srvData['server_id'], $host, $srvData['server_name'], $game, $srvData['server_port'], $srvData['server_account'], $srvData['server_password'], $srvData['server_status'], $srvData['server_startscript'], $srvData['current_players'], $srvData['max_players'], $srvData['rconpassword']);
                if (intval($server->getServer_status()) === Constants::$SERVER_DISABLED) {
                    return "SERVER_DISABLED_CANT_START";
                }
                $dat = $server->startServer($sql);
                if ($dat === true) {
                    return "SERVER_STARTED";
                } else {
                    return $dat;
                }
            } else {
                return "BAD_METHOD_CALL";
            }
        } else {
            return "NOT_AUTHORIZED";
        }
    }
    
    /**
     * Disables the server
     * @param SQL $sql The SQL handle.
     * @param int $server_id The server ID which to disable.
     * @return string Returns the API message.
     */
    public function disableServer(SQL $sql, $server_id) {
        require_once __DIR__ . "/../Constants.php";
        require_once __DIR__ . "/../users/User.php";
        require_once __DIR__ . "/../servers/host/Host.php";
        require_once __DIR__ . "/../ssh/SSH.php";
        require_once __DIR__ . "/../servers/Server.php";
        require_once __DIR__ . "/../servers/Game.php";
        $user = new User($this->username, $this->password);
        $dat = $user->authenticate($sql);
        if (intval($dat['group_id']) !== Constants::$PANEL_ADMIN) {
            return "NOT_AUTHORIZED";
        }
        if (!isset($dat['error'])) {
            $srvData = Server::getServersWithHostAndGame($sql, $dat['user_id'], $server_id);
            if (sizeof($srvData) === 1) {
                $srvData = $srvData[0];
                $host = new Host($srvData['host_id'], $srvData['servername'], $srvData['hostname'], $srvData['sshport'], $srvData['host_username'], $srvData['host_password']);
                $game = new Game($srvData['game_id'], $srvData['game_name'], $srvData['game_location'], $srvData['startscript']);
                $server = new Server($srvData['server_id'], $host, $srvData['server_name'], $game, $srvData['server_port'], $srvData['server_account'], $srvData['server_password'], $srvData['server_status'], $srvData['server_startscript'], $srvData['current_players'], $srvData['max_players'], $srvData['rconpassword']);
                $server->stopServer($sql);
                $dat = $server->disableServer($sql);
                if ($dat === true) {
                    return "SERVER_DISABLED";
                } else {
                    return $dat;
                }
            } else {
                return "BAD_METHOD_CALL";
            }
        } else {
            return "NOT_AUTHORIZED";
        }
    }
    
    /**
     * Enables the server
     * @param SQL $sql The SQL handle.
     * @param int $server_id The server ID which to enable.
     * @return string Returns the API message.
     */
    public function enableServer(SQL $sql, $server_id) {
        require_once __DIR__ . "/../Constants.php";
        require_once __DIR__ . "/../users/User.php";
        require_once __DIR__ . "/../servers/host/Host.php";
        require_once __DIR__ . "/../ssh/SSH.php";
        require_once __DIR__ . "/../servers/Server.php";
        require_once __DIR__ . "/../servers/Game.php";
        $user = new User($this->username, $this->password);
        $dat = $user->authenticate($sql);
        if (intval($dat['group_id']) !== Constants::$PANEL_ADMIN) {
            return "NOT_AUTHORIZED";
        }
        if (!isset($dat['error'])) {
            $srvData = Server::getServersWithHostAndGame($sql, $dat['user_id'], $server_id);
            if (sizeof($srvData) === 1) {
                $srvData = $srvData[0];
                $host = new Host($srvData['host_id'], $srvData['servername'], $srvData['hostname'], $srvData['sshport'], $srvData['host_username'], $srvData['host_password']);
                $game = new Game($srvData['game_id'], $srvData['game_name'], $srvData['game_location'], $srvData['startscript']);
                $server = new Server($srvData['server_id'], $host, $srvData['server_name'], $game, $srvData['server_port'], $srvData['server_account'], $srvData['server_password'], $srvData['server_status'], $srvData['server_startscript'], $srvData['current_players'], $srvData['max_players'], $srvData['rconpassword']);
                $dat = $server->enableServer($sql);
                if ($dat === true) {
                    return "SERVER_ENABLED";
                } else {
                    return $dat;
                }
            } else {
                return "BAD_METHOD_CALL";
            }
        } else {
            return "NOT_AUTHORIZED";
        }
    }
    
}
