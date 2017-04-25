<?php

/**
 * Generic SSH class, handles all the I/O actions with the host/gameservers.
 * @author Janno
 */
class SSH {
    
    private $hostname;
    private $sshport;
    private $host_username;
    private $host_password;
    private $ssh = null;
    
    /**
     * Constructs a new SSH object.
     * @param string $hostname The hostname.
     * @param int $sshport The SSH port.
     * @param string $host_username The SSH username.
     * @param string $host_password The SSH password.
     */
    function __construct($hostname, $sshport, $host_username, $host_password) {
        $this->hostname = $hostname;
        $this->sshport = $sshport;
        $this->host_username = $host_username;
        $this->host_password = $host_password;
        
    }
    
    /**
     * Function for sending commands through SSH.
     * @param string $command The command which to send through SSH
     * @param bool $output If true, returns the output of the command, if not, returns nothing.
     * @return array Returns an array with the stdio/stderr keys if the command was successful, error key if it wasn't.
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
            stream_set_timeout($stream, Constants::$HOST_RESOURCE_TIMEOUT);
            $stream_err = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
            return array("stdio" => stream_get_contents($stream), "stderr" => stream_get_contents($stream_err));
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
