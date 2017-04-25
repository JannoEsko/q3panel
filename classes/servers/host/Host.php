<?php
require_once __DIR__ . "/../../ssh/SSH.php";

/**
 * Generic host class, handles everything related to host servers.
 * @author Janno
 */
class Host extends SSH {
    
    private $host_id;
    private $servername;
    private $hostname;
    private $sshport;
    private $host_username;
    private $host_password;
    private $ssh;
    
    /**
     * Constructs the host object.
     * @param int $host_id The host ID
     * @param string $servername The host server's name.
     * @param string $hostname The hostname
     * @param int $sshport The SSH port, to which we can send commands.
     * @param string $host_username The SSH username (easiest way is to set it up with the root account).
     * @param string $host_password The SSH password
     */
    function __construct($host_id, $servername, $hostname, $sshport, $host_username, $host_password) {
        $this->host_id = $host_id;
        $this->servername = $servername;
        $this->hostname = $hostname;
        $this->sshport = $sshport;
        $this->host_username = $host_username;
        $this->host_password = $host_password;
        $this->ssh = parent::__construct($hostname, $sshport, $host_username, $host_password);
    }
    
    function getSSH() {
        return $this->ssh;
    }
    
    function getHost_id() {
        return $this->host_id;
    }

    function getServername() {
        return $this->servername;
    }

    function getHostname() {
        return $this->hostname;
    }

    function getSshport() {
        return $this->sshport;
    }

    function getHost_username() {
        return $this->host_username;
    }

    function getHost_password() {
        return $this->host_password;
    }

    function setHost_id($host_id) {
        $this->host_id = $host_id;
    }

    function setServername($servername) {
        $this->servername = $servername;
    }

    function setHostname($hostname) {
        $this->hostname = $hostname;
    }

    function setSshport($sshport) {
        $this->sshport = $sshport;
    }

    function setHost_username($host_username) {
        $this->host_username = $host_username;
    }

    function setHost_password($host_password) {
        $this->host_password = $host_password;
    }
    
    /**
     * Updates the host.
     * @param SQL $sql The SQL handle.
     * @return array Returns an array with the error key or the SQL rows_affected key.
     */
    function updateHost(SQL $sql) {
        $outArr = parent::sendCommand("whoami", true);
        if (strlen(trim($outArr['stderr'])) > 0) {
            return array("error" => $outArr['stderr']);
        }
        $out = $outArr['stdio'];
        if (trim($out) === trim($this->host_username)) {
            $query = Constants::$UPDATE_QUERIES['UPDATE_HOST_BY_ID'];
            $params = array($this->servername, $this->hostname, $this->sshport, $this->host_username, 
                $this->host_password, $this->host_id);
            return $sql->query($query, $params);
        } else {
            return array("error" => Constants::$ERRORS['GENERIC_ERROR']);
        }
    }
    
    /**
     * Deletes a host from the panel.
     * @param SQL $sql The SQL handle.
     * @param int $host_id The host id which you want to delete.
     * @return array Returns array with the key rows_affected if no errors occurred, or array with the key error.
     */
    static function deleteHost(SQL $sql, $host_id) {
        //first check for servers. We cannot delete a host which has servers deployed to it.
        $chksrv = Constants::$SELECT_QUERIES['GET_SERVER_BY_HOSTID'];
        $chkparam = array($host_id);
        $data = $sql->query($chksrv, $chkparam);
        if (sizeof($data) === 0) {
            //proceed
            $deletesrvquery = Constants::$DELETE_QUERIES['DELETE_HOST_BY_ID'];
            $params = array($host_id);
            return $sql->query($deletesrvquery, $params);
            
        } else {
            return array("error" => Constants::$ERRORS['DELETE_HOST_HAS_SERVERS']);
        }
    }
    
    /**
     * Gets all hosts.
     * @param SQL $sql The SQL handle
     * @param int $host_id [optional] If specified, it will get host by the given ID.
     * @param bool $includePassword [optional] If true, it will return the host server's password with the data.  
     * @return array Returns the hosts.
     */
    static function getHosts(SQL $sql, $host_id = null, $includePassword = false) {
        $query = "";
        $params = null;
        if ($host_id === null) {
            if ($includePassword) {
                $query = Constants::$SELECT_QUERIES['GET_ALL_HOSTS'];
            } else {
                $query = Constants::$SELECT_QUERIES['GET_ALL_HOSTS_WITHOUT_PASSWORD'];
            }
            
        } else {
            if ($includePassword) {
                $query = Constants::$SELECT_QUERIES['GET_HOST_BY_ID'];
            } else {
                $query = Constants::$SELECT_QUERIES['GET_HOST_BY_ID_WITHOUT_PASSWORD'];
            }
            
            $params = array($host_id);
        }
        return $sql->query($query, $params);
    }
    
    /**
     * Adds host to the database, tests the connection.
     * @param SQL $sql The SQL handle.
     * @return array Returns a SQL response array, or an array with the error key.
     */
    function addHost(SQL $sql) {
        //first check that can we actually send a command.
        //lets do it by sending a whoami command to the VPS and checking the output.
        $outArr = parent::sendCommand("whoami", true);
        if (strlen(trim($outArr['stderr'])) > 0) {
            return array("error" => $outArr['stderr']);
        }
        $out = $outArr['stdio'];
        if (trim($out) === trim($this->host_username)) {
            $query = Constants::$INSERT_QUERIES['ADD_NEW_HOST'];
            $params = array($this->servername, $this->hostname, $this->sshport, $this->host_username, $this->host_password);
            return $sql->query($query, $params);
        } else {
            return array("error" => Constants::$ERRORS['GENERIC_ERROR']);
        }
    }

    /**
     * Gets the host select-option fields.
     * @param SQL $sql The SQL handle.
     * @return string Returns the option-values of the hosts.
     */
    static function getHostsSelect(SQL $sql) {
        $hosts = self::getHosts($sql);
        $str = "";
        foreach ($hosts as $host) {
            $str .= "<option value='" . $host['host_id'] . "'>" . $host['servername'] . "</option>";
        }
        return $str;
    }
    
}
