<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Constants
 *
 * @author Janno
 */
class Constants {
    //put your code here
    
    /**
     * Holds all the table definitions which can be run on the database engine.
     */
    
    static $PANEL_ADMIN = 3;
    static $SERVER_ADMIN = 2;
    static $NORMAL_USER = 1;
    static $DISABLED_USER = 0;
    static $SERVER_STARTED = 2;
    static $SERVER_STOPPED = 1;
    static $SERVER_DISABLED = 0;
    static $EXTERNAL_ACCOUNT = 1;
    static $LOCAL_ACCOUNT = 0;
    static $CANNOT_EDIT_USER = 0;
    static $CANNOT_EDIT_GROUP = 3;
    static $ONLY_GROUP_EDIT = 2;
    static $CAN_EDIT_USER = 1;
    static $INCLUDE_PASSWORD = true;
    static $EXCLUDE_PASSWORD = false;
    static $Q3_RESOURCE_TIMEOUT = 1;
    static $HOST_RESOURCE_TIMEOUT = 2;
    
    
    static $SERVER_LOG_SEVERITIES = array(
        "error" => array("level" => 2, "fa-icon" => "fa-times")
        , "info" => array("level" => 1, "fa-icon" => "fa-info")
        , "success" => array("level" => 0, "fa-icon" => "fa-check")
    );
    
    
    static $CREATE_TABLES = array(
        "CREATE TABLE q3panel_users (user_id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, username VARCHAR(100) NOT NULL, password VARCHAR(255), origin TINYINT DEFAULT 0, email VARCHAR(255), group_id TINYINT, allow_emails TINYINT, CONSTRAINT username_must_be_unique UNIQUE(username))",
        "CREATE TABLE q3panel_hosts (host_id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, servername VARCHAR(255), hostname VARCHAR(255), sshport TINYINT, host_username VARCHAR(255), host_password VARCHAR(255), status TINYINT COMMENT '1 - ok, 2 - SSH problem, 3 - FTP problem')",
        "CREATE TABLE q3panel_servers (server_id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, host_id INTEGER NOT NULL, server_name VARCHAR(255), game_id INTEGER, server_port SMALLINT UNSIGNED NOT NULL, server_account VARCHAR(255), server_password VARCHAR(255), server_status TINYINT COMMENT '0 - disabled, 1 - offline, 2 - online', server_startscript TEXT, current_players TINYINT, max_players TINYINT, rconpassword VARCHAR(255), CONSTRAINT server_name_must_be_unique UNIQUE(server_name), CONSTRAINT server_account_must_be_unique UNIQUE(server_account), CONSTRAINT server_port_must_be_unique UNIQUE(server_port))",
        "CREATE TABLE q3panel_servers_map (servers_map_id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, server_id INTEGER NOT NULL, user_id INTEGER NOT NULL, can_see_rcon TINYINT, can_see_ftp TINYINT, can_access_config TINYINT, can_access_maps TINYINT, can_stop_server TINYINT, CONSTRAINT cant_have_duplicates UNIQUE(server_id, user_id))",
        "CREATE TABLE q3panel_servers_logs (server_log_id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, server_id INTEGER NOT NULL, user_id INTEGER NOT NULL, user_ip VARCHAR(255), severity TINYINT, action TEXT, timestamp DATETIME DEFAULT CURRENT_TIMESTAMP)",
        "CREATE TABLE q3panel_logs (log_id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, user_id INTEGER NOT NULL, user_ip VARCHAR(255), action TEXT, timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP)",
        "CREATE TABLE q3panel_games (game_id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, game_name VARCHAR(255), game_location TEXT, startscript TEXT)",
        "CREATE TABLE q3panel_forgottenpsw (forgottenpsw_id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, user_id INTEGER NOT NULL, request_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, request_key VARCHAR(255) NOT NULL)",
        "CREATE TABLE q3panel_failed_logins (failed_login_id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, failed_username VARCHAR(255), failed_ip VARCHAR(255), failed_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP)",
        "CREATE TABLE q3panel_support_ticket (support_ticket_id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, title TEXT, ticket_status TINYINT COMMENT '0 - open, 1 - closed, 2 - on hold, 3 - resolved', creation_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP)",
        "CREATE TABLE q3panel_support_ticket_map (support_ticket_map_id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, ticket_id INTEGER NOT NULL, user_id INTEGER NOT NULL)",
        "CREATE TABLE q3panel_support_ticket_messages (support_ticket_message_id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, ticket_id INTEGER NOT NULL, user_id INTEGER NOT NULL, user_ip VARCHAR(255), message_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, message TEXT)",
        "CREATE TABLE q3panel_external_authentication (ext_auth_id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, host VARCHAR(255), db_username VARCHAR(255), db_password VARCHAR(255), db_name VARCHAR(255), users_table_name VARCHAR(255), user_id_field VARCHAR(255), username_field VARCHAR(255), password_field VARCHAR(255), email_field VARCHAR(255))",
        "CREATE TABLE q3panel_email_service (email_service_id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, is_sendgrid TINYINT, from_name VARCHAR(255), from_email VARCHAR(255), api_key TEXT COMMENT 'empty if PHPMailer, key if SendGrid')",
        "CREATE TABLE q3panel_style_preference (style_preference_id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, style_id INTEGER NOT NULL, user_id INTEGER NOT NULL)",
        "CREATE TABLE q3panel_styles (style_id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, style_name VARCHAR(255), style_bg VARCHAR(255) COMMENT 'The background color from CSS of the object janno-panel')"
        
    );
    
    static $INSERT_QUERIES = array(
        "ADD_NEW_USER" => "INSERT INTO q3panel_users (username, password, origin, email, group_id, allow_emails) VALUES (?, ?, ?, ?, ?, ?)",
        "ADD_EXT_DB" => "INSERT INTO q3panel_external_authentication (host, db_username, db_password, db_name, users_table_name, user_id_field, username_field, password_field, email_field) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
        "ADD_EMAIL_SERVICE" => "INSERT INTO q3panel_email_service (is_sendgrid, from_name, from_email, api_key) VALUES (?, ?, ?, ?)"
        , "ADD_STYLES" => "INSERT INTO q3panel_styles (style_name, style_bg) VALUES ('theme-a.css', '#23b7e5'), ('theme-b.css', '#37bc9b'), ('theme-c.css', '#7266ba'), ('theme-d.css', '#f05050'), ('theme-e.css', '#1797be'), ('theme-f.css', '#2b957a'), ('theme-g.css', '#564aa3'), ('theme-h.css', '#ec2121')"
        , "SET_STYLE_PREFERENCE" => "INSERT INTO q3panel_style_preference (style_id, user_id) VALUES (?, ?)"
        , "SET_FORGOTTEN_PASSWORD" => "INSERT INTO q3panel_forgottenpsw (user_id, request_key) VALUES (?, ?)"
        , "ADD_NEW_GAME" => "INSERT INTO q3panel_games (game_name, game_location, startscript) VALUES (?, ?, ?)"
        , "ADD_NEW_HOST" => "INSERT INTO q3panel_hosts (servername, hostname, sshport, host_username, host_password) VALUES (?, ?, ?, ?, ?)"
        , "ADD_NEW_SERVER" => "INSERT INTO q3panel_servers (host_id, game_id, server_name, server_port, server_account, server_password, server_status, server_startscript, current_players, max_players, rconpassword) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        , "ADD_NEW_SERVER_MAPPING" => "INSERT INTO q3panel_servers_map (server_id, user_id, can_see_rcon, can_see_ftp, can_access_config, can_access_maps, can_stop_server) SELECT ? AS server_id, user_id, 1 AS can_see_rcon, 1 AS can_see_FTP, 1 AS can_access_config, 1 AS can_access_maps, 1 AS can_stop_servers FROM q3panel_users WHERE group_id = 3"
        , "GENERIC_LOG_INSERT" => "INSERT INTO q3panel_logs (user_id, user_ip, action) VALUES (?, ?, ?)"
        , "MAP_USER_TO_ALL_SERVERS" => "INSERT INTO q3panel_servers_map (server_id, user_id, can_see_rcon, can_see_ftp, can_access_config, can_access_maps, can_stop_server) SELECT server_id AS server_id, ? AS user_id, 1 AS can_see_rcon, 1 AS can_see_FTP, 1 AS can_access_config, 1 AS can_access_maps, 1 AS can_stop_servers FROM q3panel_servers ON DUPLICATE KEY UPDATE can_see_rcon = 1, can_see_ftp = 1, can_access_config = 1, can_access_maps = 1, can_stop_server = 1"
        , "FAILED_LOGIN_INSERT" => "INSERT INTO q3panel_failed_logins (failed_username, failed_ip) VALUES (?, ?)"
        , "SERVER_LOG_INSERT" => "INSERT INTO q3panel_servers_logs (server_id, user_id, user_ip, severity, action) VALUES (?, ?, ?, ?, ?)"
    );
    
    static $SELECT_QUERIES = array(
        "GET_LOCAL_USER_BY_NAME" => "SELECT * FROM q3panel_users INNER JOIN q3panel_style_preference ON q3panel_users.user_id = q3panel_style_preference.user_id INNER JOIN q3panel_styles ON q3panel_style_preference.style_id = q3panel_styles.style_id WHERE username = ? AND group_id > 0 AND origin = 0",
        "GET_EXT_DATA" => "SELECT * FROM q3panel_external_authentication",
        "GET_USER_BY_EMAIL" => "SELECT * FROM q3panel_users WHERE email = ?",
        "EXT_GET_FIRST_USER" => "SELECT {ext_usrname}, {ext_psw}, {ext_email} FROM {ext_usrtable} WHERE {ext_usrtable_id} = 1"
        , "EXT_AUTH_EXISTS" => "SELECT Count(ext_auth_id) AS count FROM q3panel_external_authentication"
        , "FIND_EXT_USER_SELECT2" => "SELECT {ext_usrtable_id} AS id, {ext_usrname} AS text FROM {ext_usrtable} WHERE {ext_usrname} LIKE ?"
        , "GET_EXTERNAL_ACCOUNT" => "SELECT {ext_usrtable_id}, {ext_usrname}, {ext_psw} FROM {ext_usrtable} WHERE {ext_usrname} = ?"
        , "GET_EXT_USER_BY_NAME" => "SELECT * FROM q3panel_users INNER JOIN q3panel_style_preference ON q3panel_users.user_id = q3panel_style_preference.user_id INNER JOIN q3panel_styles ON q3panel_style_preference.style_id = q3panel_styles.style_id WHERE username = ? AND group_id > 0 AND origin = 1"
        , "GET_STYLE_BY_NAME" => "SELECT * FROM q3panel_styles WHERE style_name = ?"
        , "GET_EMAIL_PREFERENCES" => "SELECT * FROM q3panel_email_service"
        , "GET_EXT_USERID_BY_EMAIL" => "SELECT {ext_usrtable_id} FROM {ext_usrtable} WHERE {ext_usrtable_email} = ?"
        , "GET_RECOVERY_DATA" => "SELECT * FROM q3panel_forgottenpsw WHERE request_key = ? AND request_time >= (CURRENT_TIMESTAMP - INTERVAL 24 HOUR)"
        , "GET_USER_BY_RECOVERY_DATA" => "SELECT * FROM q3panel_forgottenpsw INNER JOIN q3panel_users ON q3panel_users.user_id = q3panel_forgottenpsw.user_id WHERE request_key = ? AND request_time >= (CURRENT_TIMESTAMP - INTERVAL 24 HOUR)"
        , "GET_ALL_USERS" => "SELECT * FROM q3panel_users"
        , "GET_ALL_USERS_BY_GROUP_HIGHER_THAN" => "SELECT * FROM q3panel_users WHERE group_id >= ?"
        , "GET_EXT_USER_BY_ID" => "SELECT {ext_usrtable_id}, {ext_usrname}, {ext_email} FROM {ext_usrtable} WHERE {ext_usrtable_id} = ?"
        , "GET_USER_BY_ID" => "SELECT * FROM q3panel_users WHERE user_id = ?"
        , "GET_USER_BY_NAME" => "SELECT * FROM q3panel_users WHERE username = ?"
        , "GET_ALL_GAMES" => "SELECT * FROM q3panel_games"
        , "GET_GAME_BY_ID" => "SELECT * FROM q3panel_games WHERE game_id = ?"
        , "GET_USER_BY_ID_AND_GROUP" => "SELECT * FROM q3panel_users WHERE user_id = ? AND group_id = ?"
        , "GET_ALL_HOSTS" => "SELECT * FROM q3panel_hosts"
        , "GET_HOST_BY_ID" => "SELECT * FROM q3panel_hosts WHERE host_id = ?"
        , "GET_ALL_HOSTS_WITHOUT_PASSWORD" => "SELECT host_id, servername, hostname, sshport, host_username FROM q3panel_hosts"
        , "GET_HOST_BY_ID_WITHOUT_PASSWORD" => "SELECT host_id, servername, hostname, sshport, host_username FROM q3panel_hosts WHERE host_id = ?"
        , "GET_USER_BY_ID_AND_GROUP_LARGER_THAN" => "SELECT * FROM q3panel_users WHERE user_id = ? AND group_id >= ?"
        , "GET_SERVER_BY_HOSTID" => "SELECT * FROM q3panel_servers WHERE host_id = ?"
        , "GET_ALL_HOSTS" => "SELECT * FROM q3panel_hosts"
        , "GET_HOST_BY_ID" => "SELECT * FROM q3panel_hosts WHERE host_id = ?"
        , "GET_SERVER_BY_ID" => "SELECT * FROM q3panel_servers WHERE server_id = ?"
        , "GET_SERVERS" => "SELECT * FROM q3panel_servers"
        , "GET_SERVERS_WITH_HOST" => "SELECT * FROM q3panel_servers INNER JOIN q3panel_hosts ON q3panel_hosts.host_id = q3panel_servers.host_id"
        , "GET_SERVERS_WITH_HOST_BY_HOST_ID" => "SELECT * FROM q3panel_servers INNER JOIN q3panel_hosts ON q3panel_hosts.host_id = q3panel_servers.host_id WHERE q3panel_servers.host_id = ?"
        , "GET_SERVERS_WITH_HOST_BY_SERVER_ID" => "SELECT * FROM q3panel_servers INNER JOIN q3panel_hosts ON q3panel_hosts.host_id = q3panel_servers.host_id WHERE q3panel_servers.server_id = ?"
        , "GET_SERVERS_WITH_HOST_BY_HOST_ID_SERVER_ID" => "SELECT * FROM q3panel_servers INNER JOIN q3panel_hosts ON q3panel_hosts.host_id = q3panel_servers.host_id WHERE q3panel_servers.host_id = ? AND q3panel_servers.server_id = ?"
        , "GET_SERVERS_WITH_GAME_BY_GAME_ID" => "SELECT * FROM q3panel_servers WHERE game_id = ?"
        , "GET_SERVERS_WITH_HOST_AND_GAME_BY_HOST_ID_SERVER_ID_GAME_ID" => "SELECT * FROM q3panel_servers INNER JOIN q3panel_hosts ON q3panel_hosts.host_id = q3panel_servers.host_id INNER JOIN q3panel_games ON q3panel_games.game_id = q3panel_servers.game_id WHERE q3panel_servers.host_id = ? AND q3panel_servers.server_id = ? AND q3panel_servers.game_id = ?"
        , "GET_SERVERS_WITH_HOST_AND_GAME_BY_HOST_ID_SERVER_ID" => "SELECT * FROM q3panel_servers INNER JOIN q3panel_hosts ON q3panel_hosts.host_id = q3panel_servers.host_id INNER JOIN q3panel_games ON q3panel_games.game_id = q3panel_servers.game_id WHERE q3panel_servers.host_id = ? AND q3panel_servers.server_id = ?"
        , "GET_SERVERS_WITH_HOST_AND_GAME" => "SELECT * FROM q3panel_servers INNER JOIN q3panel_hosts ON q3panel_hosts.host_id = q3panel_servers.host_id INNER JOIN q3panel_games ON q3panel_games.game_id = q3panel_servers.game_id"
        , "GET_SERVERS_WITH_HOST_AND_GAME_BY_SERVER_ID_GAME_ID" => "SELECT * FROM q3panel_servers INNER JOIN q3panel_hosts ON q3panel_hosts.host_id = q3panel_servers.host_id INNER JOIN q3panel_games ON q3panel_games.game_id = q3panel_servers.game_id WHERE q3panel_servers.server_id = ? AND q3panel_servers.game_id = ?"
        , "GET_SERVERS_WITH_HOST_AND_GAME_BY_HOST_ID_GAME_ID" => "SELECT * FROM q3panel_servers INNER JOIN q3panel_hosts ON q3panel_hosts.host_id = q3panel_servers.host_id INNER JOIN q3panel_games ON q3panel_games.game_id = q3panel_servers.game_id WHERE q3panel_servers.host_id = ? AND q3panel_servers.game_id = ?"
        , "GET_SERVERS_WITH_HOST_AND_GAME_BY_SERVER_ID" => "SELECT * FROM q3panel_servers INNER JOIN q3panel_hosts ON q3panel_hosts.host_id = q3panel_servers.host_id INNER JOIN q3panel_games ON q3panel_games.game_id = q3panel_servers.game_id WHERE q3panel_servers.server_id = ?"
        , "GET_SERVERS_WITH_HOST_AND_GAME_BY_GAME_ID" => "SELECT * FROM q3panel_servers INNER JOIN q3panel_hosts ON q3panel_hosts.host_id = q3panel_servers.host_id INNER JOIN q3panel_games ON q3panel_games.game_id = q3panel_servers.game_id WHERE q3panel_servers.game_id = ?"
        , "GET_SERVERS_WITH_HOST_AND_GAME_BY_HOST_ID" => "SELECT * FROM q3panel_servers INNER JOIN q3panel_hosts ON q3panel_hosts.host_id = q3panel_servers.host_id INNER JOIN q3panel_games ON q3panel_games.game_id = q3panel_servers.game_id WHERE q3panel_servers.host_id = ?"
        , "GET_SERVERS_BY_GAME_ID" => "SELECT * FROM q3panel_servers WHERE game_id = ?"
        , "GET_SERVERS_WITH_MAP" => "SELECT * FROM q3panel_servers INNER JOIN q3panel_servers_map ON q3panel_servers_map.server_id = q3panel_servers.server_id INNER JOIN q3panel_hosts ON q3panel_hosts.host_id = q3panel_servers.host_id INNER JOIN q3panel_games ON q3panel_games.game_id = q3panel_servers.game_id WHERE q3panel_servers_map.user_id = ?"
        , "GET_SERVER_WITH_MAP" => "SELECT * FROM q3panel_servers INNER JOIN q3panel_servers_map ON q3panel_servers_map.server_id = q3panel_servers.server_id INNER JOIN q3panel_hosts ON q3panel_hosts.host_id = q3panel_servers.host_id INNER JOIN q3panel_games ON q3panel_games.game_id = q3panel_servers.game_id WHERE q3panel_servers.server_id = ? AND q3panel_servers_map.user_id = ?"
        , "GET_MAP_WITH_SERVERS_WITH_USERS" => "SELECT * FROM q3panel_servers_map INNER JOIN q3panel_servers ON q3panel_servers_map.server_id = q3panel_servers_map.server_id INNER JOIN q3panel_users ON q3panel_users.user_id = q3panel_servers_map.user_id"
        , "GET_MAP_WITH_SERVER_WITH_USERS_BY_SERVER_ID" => "SELECT * FROM q3panel_servers_map INNER JOIN q3panel_servers ON q3panel_servers_map.server_id = q3panel_servers.server_id INNER JOIN q3panel_users ON q3panel_users.user_id = q3panel_servers_map.user_id WHERE q3panel_servers_map.server_id = ?"
        , "GET_SERVERS_WITH_HOST_AND_GAME_BY_STATUS" => "SELECT * FROM q3panel_servers INNER JOIN q3panel_hosts ON q3panel_servers.host_id = q3panel_hosts.host_id INNER JOIN q3panel_games ON q3panel_servers.game_id = q3panel_games.game_id WHERE q3panel_servers.server_status = ?"
    );
    
    static $UPDATE_QUERIES = array(
        "SET_STYLE_FOR_USER" => "UPDATE q3panel_style_preference SET style_id = ? WHERE user_id = ?"
        , "UPDATE_HOST_BY_ID" => "UPDATE q3panel_hosts SET servername = ?, hostname = ?, sshport = ?, host_username = ?, host_password = ? WHERE host_id = ?"
        , "SET_SERVER_STATUS" => "UPDATE q3panel_servers SET server_status = ? WHERE server_id = ?"
        , "SET_NEW_SERVER_ACCOUNT_PASSWORD" => "UPDATE q3panel_servers SET server_password = ? WHERE server_id = ?"
        , "SET_SERVER_PLAYERS_BY_ID" => "UPDATE q3panel_servers SET current_players = ? WHERE server_id = ?"
    );
    
    static $DELETE_QUERIES = array(
        "DELETE_USER_BY_ID" => "DELETE FROM q3panel_users WHERE user_id = ?"
        , "DELETE_FORGOTTEN_DATA" => "DELETE FROM q3panel_forgottenpsw WHERE user_id = ?"
        , "DELETE_GAME_BY_ID" => "DELETE FROM q3panel_games WHERE game_id = ?"
        , "DELETE_HOST_BY_ID" => "DELETE FROM q3panel_hosts WHERE host_id = ?"
        , "DELETE_SERVER_BY_ID" => "DELETE FROM q3panel_servers WHERE server_id = ?"
        , "DELETE_NONEXISTANT_MAPPINGS" => "DELETE FROM q3panel_servers_map WHERE server_id NOT IN (SELECT server_id FROM q3panel_servers)"
        , "REMOVE_USER_FROM_SERVER_MAP" => "DELETE FROM q3panel_servers_map WHERE server_id = ? AND user_id = ?"
    );
    
    static $LOGGER_MESSAGES = array(
        "ERRORS" => array(
            "GET_SERVER_DATA_NOT_MAPPED_OR_DOESNT_EXIST" => "Tried to access a server which doesn't exist or isn't mapped. Server ID: "
            , "DIDNT_FIND_HOST" => "User requested a host, which couldn't be found."
            , "EDIT_ACCOUNT_ERROR" => "User tried to edit an account, but he/she was underprivileged to do so."
            , "GENERATE_NEW_FTP_ERROR" => "User tried to generate a new FTP password for a server, but an error occured. Message: "
            , "FTP_PSW_GENERATE_PRIVILEGE" => "User tried to generate a new FTP password for a server, but was underprivileged to do so. Server id: "
            , "GENERIC_SERVER_HOST_ERROR" => "User tried to perform an action which was executed on the host machine, but an error occured. "
            , "FTP_PSW_CHANGE_PRIVILEGE" => "User tried to generate a new FTP password for a server, but was underprivileged to do so. Server id: "
            , "FILE_UPLOAD_NOT_PRIVILEGED" => "User tried to upload a file to FTP, but he/she wasn't privileged enough to do so. Server id: "
            , "GENERIC_FTP_ERROR" => "User tried to perform an action in the Web FTP interface, but an error occured. "
            , "GENERIC_FTP_PERMISSION_ERROR" => "User tried to perform an action in the Web FTP interface, but he/she is underprivileged to do so. Server id: "
            , "DELETE_SERVER_GENERIC_ERROR" => "User tried to delete a server, but an error occured. Server id: "
            , "DISABLE_SERVER_GENERIC_ERROR" => "User tried to disable a server, but an error occured. Server id: "
            , "ENABLE_SERVER_GENERIC_ERROR" => "User tried to enable a server, but an error occured. Server id: "
            , "START_SERVER_GENERIC_ERROR" => "User tried to start a server, but an error occured. Server id: "
            , "START_SERVER_DISABLED_OR_NO_AUTH" => "User tried to start a server, which was either disabled or he had no permission to start it. Server id: "
            , "STOP_SERVER_GENERIC" => "User tried to stop a server, but an error occured. Server id: "
            , "ADD_SERVER_GENERIC" => "User tried to add a server, but an error occured. Host id: "
            , "HOST_UPDATE_GENERIC" => "User tried to update a host server, but an error occured. Host id: "
            , "HOST_DELETE_GENERIC" => "User tried to delete a host server, but an error occured. Host id: "
            , "NEW_HOSTSERVER_GENERIC" => "User tried to add a new host server, but an error occured. Error message: "
            , "UPDATE_GAME_GENERIC" => "User tried to update a game, but an error occured. Game id: "
            , "DELETE_GAME_GENERIC" => "User tried to delete a game, but an error occured. Game id: "
            , "GENERIC_NEW_EXT_USER_ERROR" => "User tried to add a new external user, but an error occured. Ext user id: "
            , "GENERIC_NEW_USER_ERROR" => "User tried to add a new user, but an error occured. New username: "
            , "EMAIL_SETUP_ERROR" => "User tried to set up / edit e-mail preferences, but an error occured. Error message: "
            , "ADD_GAME_GENERIC" => "User tried to add a new game, but an error occured."
            , "DELETE_USER_ERROR_GENERIC" => "User tried to delete an user, but an error occured. User id: "
            , "DELETE_USER_PRIVILEGE_ERROR" => "User tried to delete an user, but he/she is not privileged enough. User id: "
            , "REMOVE_USER_SERVER_MAP" => "User tried to delete mapping for user id {user_id} on server {server_id} but something went wrong (most likely the deletable user was a panel admin)."
        ),
        "SUCCESSES" => array(
            "FTP_PSW_GENERATE" => "User generated a new FTP password for server id "
            , "FTP_PSW_EDIT" => "User edited the FTP password for server id "
            , "DELETE_SERVER" => "User deleted a server with the id "
            , "SERVER_DISABLED" => "User disabled a server with the id "
            , "SERVER_ENABLED" => "User enabled a server with the id "
            , "START_SERVER" => "User started a server with the id "
            , "SERVER_STOPPED" => "User stopped a server with the id "
            , "ADD_SERVER" => "User added a new server to host with id "
            , "HOST_UPDATE" => "User updated a host server with id "
            , "HOST_DELETED" => "User deleted a host server with id "
            , "NEW_HOSTSERVER" => "User added a new host server with id "
            , "UPDATE_GAME" => "User updated a game with id "
            , "DELETE_GAME" => "User deleted a game with id "
            , "NEW_LOCAL_USER" => "User added a new local user with username "
            , "NEW_EXT_USER" => "User added a new external user with user id "
            , "EMAIL_SETUP" => "User has changed the e-mail preferences."
            , "ADD_GAME" => "User added a new game with id "
            , "DELETE_USER" => "User deleted an user with id "
            , "EDIT_USER" => "User edited an user with id "
            , "REMOVE_USER_SERVER_MAP_SUCCESS" => "User removed the server mapping for server id {server_id} for user {user_id}."
        ),
        "INFORMATION" => array(
            
        )
    );
    
    static $SERVER_ACTIONS = array(
        "SERVER_PID_DOWN_REBOOT_SUCCESSFUL" => "The server has been rebooted due to the screen being down."
        , "SERVER_PID_DOWN_REBOOT_ERROR" => "The server couldn't be rebooted due to an unknown error. Screen PID was down."
        , "GET_Q3_SERVER_INFO" => "\xFF\xFF\xFF\xFFgetinfo"
        , "GET_Q3_SERVER_STATUS" => "\xFF\xFF\xFF\xFFgetstatus"
        , "SERVER_REBOOT_SUCCESS_NO_INFOMSG" => "Restarted the server, because it didn't respond to the getinfo query."
        , "SERVER_REBOOT_ERROR_NO_INFOMSG" => "The server didn't respond to the getinfo query and it couldn't be restarted"
    );
    
    static $ERRORS = array(
        "AUTH_NO_DATA_ERROR" => "This account does not exist.",
        "AUTH_WRONG_PASSWORD_OR_DISABLED" => "Wrong password or the account is disabled."
        , "AUTH_NO_DATA_WRONG_PSW_OR_DISABLED" => "This account doesn't exist, you typed the wrong password or the account is disabled"
        , "FPSW_NO_DATA_ERROR" => "Either this account doesn't exist on this panel or the account's origin is from an external system. If it is from an external system, you have to fix your password issue over there."
        , "NO_RECOVERY_INFO" => "Recovery key is expired or is wrong. Please request password recovery again."
        , "CHANGE_FORGOTTEN_ERROR" => "Error occurred during the recovery process. Please request a new recovery key."
        , "GENERIC_PRIVILEGE_ERROR" => "You're not privileged enough to perform this action."
        , "GENERIC_ERROR" => "Something went wrong with your action, please, check the data you provided (if any), try again or refresh the page and try again."
        , "SSH2_AUTH_ERROR" => "Wrong username and/or password. Please recheck them. Also, if you got any SSH restrictions (where can accounts connect from etc), please, whitelist this page so it can have a connection with the host server."
        , "DELETE_HOST_HAS_SERVERS" => "You cannot delete this host, because it has gameservers deployed. Delete the servers first, then delete the host"
        , "DELETE_GAME_HAS_SERVERS" => "You cannot delete this game, because it has gameservers deployed. Delete the servers first, then delete the game."
        , "SERVER_DISABLED_OR_NOT_AUTHORIZED" => "You cannot perform this action, because either you're not privileged enough or the server is disabled."
        , "FTP_DELETE_ERROR" => "Couldn't remove the file you tried to update. Please check permissions."
        , "GENERIC_FTP_ERROR" => "Couldn't perform the action with FTP. Please check the permissions on the host server, if the file already exists on FTP or check if you're privileged enough."
    );
    
    static $EMAIL_TEMPLATE = array(
        "FORGOTTEN_TITLE" => "Forgotten password | Q3Panel"
        , "FORGOTTEN_MSG" => "Hello,<br><br>Someone (hopefully you) has just requested a new password on your account. To do so, please, click on here: <a href=\"{FORGOTTEN_URL_KEY}\">{FORGOTTEN_URL_KEY}</a><br>If it wasn't you, feel free to ignore this e-mail.<br><br>Best regards,<br>{SENDER_NAME}"
        , "FPSW_CHANGED" => "Hello,<br><br>You recently requested a new password from the page and it has been changed now. You have to use that password from now on.<br><br>Best regards,<br>{SENDER_NAME}"
        , "SERVER_REBOOT_TITLE" => "Server rebooted | Q3Panel"
        , "SERVER_REBOOT_MSG" => "Hello,<br><br>Your server, {server_name} (id {server_id}), was rebooted due to it being down. If this is a recurring problem, maybe you should check what causes the problem.<br><br>The server checker function returned this:<br>{out_msg}<br><br>Best regards,<br>{sender_name}"
        , "SERVER_DOWN_ERR_TITLE" => "Server down | Q3Panel"
        , "SERVER_DOWN_ERR_MSG" => "Hello,<br><br>Your server, {server_name} (id {server_id}), is down and it couldn't be rebooted for an unknown error.<br><br>Server checker output:<br>{out_err}<br><br>Best regards,<br>{sender_name}"
    );
    
    static $MESSAGES = array(
        "FPSW_SUCCESS" => "Further instructions sent to e-mail."
        , "ORIGIN" => array(0 => "Local", 1 => "External")
        , "GROUP" => array(0 => "Disabled", 1 => "Normal user", 2 => "Server owner", 3 => "Panel admin")
        , "SERVER_STATUSES" => array(0 => "Disabled", 1 => "Offline", 2 => "Online")
        , "FTP_FILE_UPDATE_SUCCESS" => "File has been successfully updated."
        , "FTP_FILE_OR_FOLDER_DELETE_SUCCESS" => "File/Folder has been successfully deleted."
        , "FTP_FILE_OR_FOLDER_RENAME_SUCCESS" => "File/Folder has been successfully renamed."
        , "FTP_NEW_FOLDER_SUCCESS" => "New folder has been successfully created."
        , "FTP_NEW_FILE_SUCCESS" => "New file has been successfully created."
        , "FTP_FILE_UPLOAD_SUCCESS" => "New file has been successfully uploaded."
        , "FTP_PASSWORD_CHANGE_SUCCESS" => "The server account password has been successfully changed."
        , "EMAIL_SETUP" => "The new e-mail preferences have been saved successfully."
        , "USER_MAPPING_REMOVED" => "The user has been successfully removed from the server map"
        , "USER_MAPPING_REMOVED_ERROR" => "Something went wrong with deleting the map for the user (most likely the user is a panel admin). Please refresh the page and try again."
    );
    
    static $SSH_COMMANDS = array(
        "WHOAMI" => "whoami"
        , "ADD_USER" => "useradd -m {server_account}"
        , "CHANGE_PASSWORD" => "echo \"{server_account}:{server_password}\" | chpasswd"
        , "COPY_GAME_FILES" => "cp -R {game_location}/* /home/{server_account}/"
        , "CHOWN_GAME_FILES" => "chown -R {server_account} /home/{server_account}"
        , "START_SERVER" => "screen -d -S {server_account} -m sh -c \"{server_startscript}\""
        , "GET_SCREEN_PID" => "screen -ls | grep -o '[0-9]\{1,5\}.{server_account}' | grep -o '[0-9]\{1,5\}' | head -1"
        , "STOP_SERVER" => "kill {screen_pid}"
        , "DELETE_ACCOUNT" => "userdel -fr {server_account}"
    );
    
    private static $CSS = <<<EOT

        <link rel="stylesheet" href="{}/css/font-awesome.min.css">
        <link rel="stylesheet" href="{}/css/simple-line-icons.css">
        <link rel="stylesheet" href="{}/css/animate.min.css">
        <link rel="stylesheet" href="{}/css/whirl.css">
        <link rel="stylesheet" href="{}/css/bootstrap.css">
        <link rel="stylesheet" href="{}/css/select2.css">
        <link rel="stylesheet" href="{}/css/toastr.min.css">
        <link rel="stylesheet" href="{}/css/app.css"> 

EOT;
    
    private static $JS = <<<EOT

        <script src="{}/js/jquery.js"></script>
        <script src="{}/js/modernizr.custom.js"></script>
        <script src="{}/js/matchMedia.js"></script>
        <script src="{}/js/bootstrap.js"></script>
        <script src="{}/js/jquery.easing.js"></script>
        <script src="{}/js/animo.js"></script>
        <script src="{}/js/jquery.slimscroll.min.js"></script>
        <script src="{}/js/screenfull.js"></script>
        <script src="{}/js/jquery.localize.js"></script>
        <script src="{}/js/jquery.storageapi.js"></script>
        <script src="{}/js/app.js"></script>
        <script src="{}/js/select2.js"></script>
        <script src="{}/js/toastr.min.js"></script>
        <script src="{}/js/q3panel.js"></script>

EOT;
    
    private static $PREFERENCED_STYLES = array(
        "theme-a.css" => "<link id=\"autoloaded-stylesheet\" rel=\"stylesheet\" href=\"{}/css/theme-a.css\"> ",
        "theme-b.css" => "<link id=\"autoloaded-stylesheet\" rel=\"stylesheet\" href=\"{}/css/theme-b.css\"> ",
        "theme-c.css" => "<link id=\"autoloaded-stylesheet\" rel=\"stylesheet\" href=\"{}/css/theme-c.css\"> ",
        "theme-d.css" => "<link id=\"autoloaded-stylesheet\" rel=\"stylesheet\" href=\"{}/css/theme-d.css\"> ",
        "theme-e.css" => "<link id=\"autoloaded-stylesheet\" rel=\"stylesheet\" href=\"{}/css/theme-e.css\"> ",
        "theme-f.css" => "<link id=\"autoloaded-stylesheet\" rel=\"stylesheet\" href=\"{}/css/theme-f.css\"> ",
        "theme-g.css" => "<link id=\"autoloaded-stylesheet\" rel=\"stylesheet\" href=\"{}/css/theme-g.css\"> ",
        "theme-h.css" => "<link id=\"autoloaded-stylesheet\" rel=\"stylesheet\" href=\"{}/css/theme-h.css\"> "
        
    );
    
    
    
    public static function getCSS($url) {
        return str_replace("{}", $url, self::$CSS);
    }
    
    public static function getJS($url) {
        return str_replace("{}", $url, self::$JS);
    }
    
    public static function getPreferencedCSS($url, $css) {
        return str_replace("{}", $url, self::$PREFERENCED_STYLES[$css]);
    }
    
    public static function getSelectGroups() {
        foreach(self::$MESSAGES['GROUP'] as $group_id => $group_name) {
            $data[] = "<option value=\"$group_id\">$group_name</option>";
        }
        return $data;
    }
    
}
