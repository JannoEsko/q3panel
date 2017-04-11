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
    private $game_id;
    private $server_port;
    private $server_account;
    private $server_password;
    private $server_status;
    private $server_startscript;
    private $current_players;
    private $max_players;
    private $rconpassword;
    private $ssh;
    
    function __construct(Host $host, $server_name, $game_id, $server_port, $server_account, $server_password, $server_status, $server_startscript, $current_players, $max_players, $rconpassword) {
        $this->host = $host;
        $this->server_name = $server_name;
        $this->game_id = $game_id;
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

    function getGame_id() {
        return $this->game_id;
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


    function startServer() {
        
    }
    
    function stopServer() {
        
    }
    
    function restartServer() {
        $this->stopServer();
        $this->startServer();
    }
    
    function deleteServer() {
        
    }
    
    function updateServer(SQL $sql) {
        
    }
    
    function addServer(SQL $sql) {
        
    }

}
