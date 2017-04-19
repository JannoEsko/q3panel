<?php
require_once __DIR__ . "/../../ssh/SSH.php";
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Host
 *
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
    
    function updateHost(SQL $sql) {
        $out = parent::sendCommand("whoami", true);
        if (trim($out) === trim($this->host_username)) {
            $query = Constants::$UPDATE_QUERIES['UPDATE_HOST_BY_ID'];
            $params = array($this->servername, $this->hostname, $this->sshport, $this->host_username, 
                $this->host_password, $this->host_id);
            return $sql->query($query, $params);
        } else {
            return array("error" => Constants::$ERRORS['GENERIC_ERROR']);
        }
    }
    
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
    
    static function getHosts(SQL $sql, $host_id = null) {
        $query = "";
        $params = null;
        if ($host_id === null) {
            $query = Constants::$SELECT_QUERIES['GET_ALL_HOSTS_WITHOUT_PASSWORD'];
        } else {
            $query = Constants::$SELECT_QUERIES['GET_HOST_BY_ID_WITHOUT_PASSWORD'];
            $params = array($host_id);
        }
        return $sql->query($query, $params);
    }
    
    function addHost(SQL $sql) {
        //first check that can we actually send a command.
        //lets do it by sending a whoami command to the VPS and checking the output.
        $out = parent::sendCommand("whoami", true);
        if (trim($out) === trim($this->host_username)) {
            $query = Constants::$INSERT_QUERIES['ADD_NEW_HOST'];
            $params = array($this->servername, $this->hostname, $this->sshport, $this->host_username, $this->host_password);
            return $sql->query($query, $params);
        } else {
            return array("error" => Constants::$ERRORS['GENERIC_ERROR']);
        }
        
    }

}
