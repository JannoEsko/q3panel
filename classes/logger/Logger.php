<?php

/**
 * Handles all of the logging events.
 * @author Janno
 */
class Logger {
    
    /**
     * Logs into the panel log.
     * @param SQL $sql The SQL handle.
     * @param int $user_id The user ID of the action initiator.
     * @param string $user_ip The user IP of the action initiator.
     * @param string $action The action performed.
     */
    static function log(SQL $sql, $user_id, $user_ip, $action) {
        $query = Constants::$INSERT_QUERIES['GENERIC_LOG_INSERT'];
        $params = array($user_id, $user_ip, $action);
        $sql->query($query, $params);
    }
    
    /**
     * Logs a failed login.
     * @param SQL $sql The SQL handle.
     * @param string $username The username, which was used to log in.
     * @param string $ip The IP of the user, who tried to log in.
     */
    static function logFailedLogin(SQL $sql, $username, $ip) {
        $query = Constants::$INSERT_QUERIES['FAILED_LOGIN_INSERT'];
        $params = array($username, $ip);
        $sql->query($query, $params);
    }
    
    /**
     * Logs into the server log.
     * @param SQL $sql The SQL handle
     * @param int $server_id The server ID
     * @param int $user_id The user ID of the action initiator.
     * @param string $user_ip The IP of the initiator.
     * @param int $severity The severity of the action.
     * @param string $action The action definition.
     */
    static function logServer(SQL $sql, $server_id, $user_id, $user_ip, $severity, $action) {
        $query = Constants::$INSERT_QUERIES['SERVER_LOG_INSERT'];
        $params = array($server_id, $user_id, $user_ip, $severity, $action);
        $sql->query($query, $params);
    }
    
    /**
     * Gets the server logs.
     * @param SQL $sql The SQL handle
     * @return array Returns array of the server logs.
     */
    static function getServerLogs($sql, $user_id = null) {
        $query = "";
        $params = null;
        $extSql = null;
        $extData = null;
        if ($user_id !== null) {
            //check if is panel admin, if is, show all, otherwise show the servers he sees.
            $check = User::canPerformAction($sql, $user_id, Constants::$PANEL_ADMIN);
            if ($check) {
                $query = Constants::$SELECT_QUERIES['GET_SERVER_LOGS_LEFT_JOIN_USERS_INNER_JOIN_SERVERS'];
            } else {
                //is server admin cuz we checked it in function call.
                $query = Constants::$SELECT_QUERIES['GET_SERVER_LOGS_LEFT_JOIN_USERS_INNER_JOIN_SERVERS_INNER_JOIN_SERVERMAP'];
                $params = array($user_id);
            }
        } else {
            $query = Constants::$SELECT_QUERIES['GET_SERVER_LOGS_LEFT_JOIN_USERS_INNER_JOIN_SERVERS'];
        }
        
        $data = $sql->query($query, $params);
        for ($i = 0; $i < sizeof($data); $i++) {
            if (intval($data[$i]['origin']) === 0) {
                if (intval($data[$i]['user_id']) === 0) {
                    $data[$i]['realName'] = "CRON Task";
                } else {
                    $data[$i]['realName'] = $data[$i]['username'];
                }
            } else {
                if ($extSql === null || !($extSql instanceof SQL)) {
                    $extData = User::getExtData($sql);
                    if (sizeof($extData) !== 0) {
                        $db_host = $extData['host'];
                        $db_username = $extData['db_username'];
                        $db_password = $extData['db_password'];
                        $db = $extData['db_name'];
                        $extSql = new SQL($db_host, $db_username, $db_password, $db);
                    } else {
                        $extData = null;
                    }
                    
                }
                $externalData = User::getExternalAccount($sql, $data[$i]['username'], true, $extSql, $extData);
                $data[$i]['realName'] = $externalData['data'][0][$externalData['extTable_spec']['username_field']];
            }
            $data[$i]['server_account'] = "";
            $data[$i]['server_password'] = "";
            $data[$i]['server_startscript'] = "";
            $data[$i]['password'] = "";
            $data[$i]['rconpassword'] = "";
            $action = $data[$i]['action'];
            $data[$i]['action'] = "<em class=\"fa " . Constants::$SERVER_LOG_FAICONS[$data[$i]['severity']] . "\"></em> $action";
        }
        return $data;
    }
    
    /**
     * Gets failed logins.
     * @param SQL $sql The SQL handle
     * @return array Returns array of the failed logins.
     */
    static function getFailedLogins($sql) {
        $query = Constants::$SELECT_QUERIES['GET_FAILED_LOGINS'];
        return $sql->query($query);
    }
    
    /**
     * Gets the panel logs.
     * @param SQL $sql The SQL handle
     * @return array Returns array with all of the panel logs.
     */
    static function getLogs($sql) {
        $query = Constants::$SELECT_QUERIES['GET_PANEL_LOGS'];
        $data = $sql->query($query);
        $extSql = null;
        $extData = null;
        for ($i = 0; $i < sizeof($data); $i++) {
            if (intval($data[$i]['origin']) === 0) {
                $data[$i]['realName'] = $data[$i]['username'];
            } else {
                if ($extSql === null || !($extSql instanceof SQL)) {
                    $extData = User::getExtData($sql);
                    if (sizeof($extData) !== 0) {
                        $db_host = $extData['host'];
                        $db_username = $extData['db_username'];
                        $db_password = $extData['db_password'];
                        $db = $extData['db_name'];
                        $extSql = new SQL($db_host, $db_username, $db_password, $db);
                    } else {
                        $extData = null;
                    }
                    
                }
                $externalData = User::getExternalAccount($sql, $data[$i]['username'], true, $extSql, $extData);
                $data[$i]['realName'] = $externalData['data'][0][$externalData['extTable_spec']['username_field']];
            }
            $data[$i]['password'] = "";
        }
        return $data;
    }
}
