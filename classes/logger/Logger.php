<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Logger
 *
 * @author Janno
 */
class Logger {
    
    static function log(SQL $sql, $user_id, $user_ip, $action) {
        $query = Constants::$INSERT_QUERIES['GENERIC_LOG_INSERT'];
        $params = array($user_id, $user_ip, $action);
        $sql->query($query, $params);
    }
    
    static function logFailedLogin($sql, $username, $ip) {
        $query = Constants::$INSERT_QUERIES['FAILED_LOGIN_INSERT'];
        $params = array($username, $ip);
        $sql->query($query, $params);
    }
    
    static function logServer($sql, $server_id, $user_id, $user_ip, $severity, $action) {
        $query = Constants::$INSERT_QUERIES['SERVER_LOG_INSERT'];
        $params = array($server_id, $user_id, $user_ip, $severity, $action);
        $sql->query($query, $params);
    }
}
