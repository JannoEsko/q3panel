<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SSH
 *
 * @author Janno
 */
class SSH {
    
    private $hostname;
    private $sshport;
    private $host_username;
    private $host_password;
    private $ssh = null;
    
    function __construct($hostname, $sshport, $host_username, $host_password) {
        $this->hostname = $hostname;
        $this->sshport = $sshport;
        $this->host_username = $host_username;
        $this->host_password = $host_password;
        
    }
    
    /**
     * Function for sending commands through SSH.
     * @param string $command The command which to send through SSH
     * @param boolean $output If true, returns the output of the command, if not, returns nothing.
     * @return string Returns the output of the command, if output is requested.
     */
    function sendCommand($command, $output = false) {
        if ($this->ssh === null) {
            $this->ssh = ssh2_connect($this->hostname, $this->sshport);
            if (!ssh2_auth_password($this->ssh, $this->host_username, $this->host_password)) {
                return array("error" => Constants::$ERRORS['SSH2_AUTH_ERROR']);
            }
        }
        $command = $command . PHP_EOL;
        if ($output) {
            $stream = ssh2_exec($this->ssh, $command);
            stream_set_blocking($stream, $output);
            return fgets($stream);
        } else {
            ssh2_exec($this->ssh, $command);
        }
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



}
