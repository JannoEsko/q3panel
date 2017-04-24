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
    
    static function getServerLogs($sql) {
        $query = Constants::$SELECT_QUERIES['GET_SERVER_LOGS_LEFT_JOIN_USERS'];
        $data = $sql->query($query);
        for ($i = 0; $i < sizeof($data); $i++) {
            if (intval($data[$i]['origin']) === 0) {
                if (intval($data[$i]['user_id']) === 0) {
                    $data[$i]['realName'] = "CRON Task";
                } else {
                    $data[$i]['realName'] = $data[$i]['username'];
                }
            } else {
                $externalData = User::getExternalAccount($sql, $data[$i]['username'], true);
                $data[$i]['realName'] = $externalData['data'][0][$externalData['extTable_spec']['username_field']];
            }
            $action = $data[$i]['action'];
            $data[$i]['action'] = "<em class=\"fa " . Constants::$SERVER_LOG_FAICONS[$data[$i]['severity']] . "\"></em> $action";
        }
        return $data;
    }
    
    static function getFailedLogins($sql) {
        $query = Constants::$SELECT_QUERIES['GET_FAILED_LOGINS'];
        return $sql->query($query);
    }
    
    static function getLogs($sql) {
        $query = Constants::$SELECT_QUERIES['GET_PANEL_LOGS'];
        $data = $sql->query($query);
        for ($i = 0; $i < sizeof($data); $i++) {
            if (intval($data[$i]['origin']) === 0) {
                $data[$i]['realName'] = $data[$i]['username'];
            } else {
                $externalData = User::getExternalAccount($sql, $data[$i]['username'], true);
                $data[$i]['realName'] = $externalData['data'][0][$externalData['extTable_spec']['username_field']];
            }
        }
        return $data;
    }
}
