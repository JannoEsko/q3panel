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
    
    function changeHost(SQL $sql) {
        
    }
    
    function deleteHost(SQL $sql) {
        
    }
    
    function addHost(SQL $sql) {
        
    }

}
