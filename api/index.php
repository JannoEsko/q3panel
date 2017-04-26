<?php
require_once __DIR__ . "/../classes/sql/SQL.php";
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../classes/api/API.php";
/**
 * The API page, to which you can send requests.
 * 
 * For every request, your POST parameters must include the username and the password.
 */


static $API_ERRORS = array(
    "NO_USERNAME_PASSWORD_DEFINED" => "NO_USERNAME_PASSWORD_DEFINED",
    "NOT_AUTHORIZED" => "NOT_AUTHORIZED",
    "BAD_METHOD_CALL" => "BAD_METHOD_CALL"
    , "SERVER_DISABLED_CANT_START" => "SERVER_DISABLED_CANT_START"
    
);

static $API_METHODS = array(
    "getServers" => array("method" => "POST", "requires" => array("username", "password", "getServers" => array("SERVER_ID_OR_BLANK"))),
    "stopServer" => array("method" => "POST", "requires" => array("username", "password", "stopServer" => array("SERVER_ID"))),
    "startServer" => array("method" => "POST", "requires" => array("username", "password", "startServer" => array("SERVER_ID"))),
    "disableServer" => array("method" => "POST", "requires" => array("username", "password", "disableServer" => array("SERVER_ID"))),
    "enableServer" => array("method" => "POST", "requires" => array("username", "password", "enableServer" => array("SERVER_ID")))
);

if (isset($_POST['username'], $_POST['password'])) {
    if (isset($_POST['getServers'])) {
        $api = new API($_POST['username'], $_POST['password']);
        if (intval($_POST['getServers']) > 0) {
            $dat = $api->getServers($sql, $_POST['getServers']);
            if (isset($API_ERRORS[$dat])) {
                die(json_encode(array("error" => $API_ERRORS[$dat])));
            }
            die(json_encode(array("servers" => $dat)));
        } else {
            $dat = $api->getServers($sql);
            if (isset($API_ERRORS[$dat])) {
                die(json_encode(array("error" => $API_ERRORS[$dat])));
            }
            die(json_encode(array("servers" => $dat)));
        }
    } else if (isset($_POST['stopServer']) && intval($_POST['stopServer']) > 0) {
        $api = new API($_POST['username'], $_POST['password']);
        $dat = $api->stopServer($sql, $_POST['stopServer']);
        if (isset($API_ERRORS[$dat])) {
            die(json_encode(array("error" => $API_ERRORS[$dat])));
        }
        die(json_encode(array("msg" => $dat)));
    } else if (isset($_POST['startServer']) && intval($_POST['startServer']) > 0) {
        $api = new API($_POST['username'], $_POST['password']);
        $dat = $api->startServer($sql, $_POST['startServer']);
        if (isset($API_ERRORS[$dat])) {
            die(json_encode(array("error" => $API_ERRORS[$dat])));
        }
        die(json_encode(array("msg" => $dat)));
    } else if (isset($_POST['disableServer']) && intval($_POST['disableServer']) > 0) {
        $api = new API($_POST['username'], $_POST['password']);
        $dat = $api->disableServer($sql, $_POST['disableServer']);
        if (isset($API_ERRORS[$dat])) {
            die(json_encode(array("error" => $API_ERRORS[$dat])));
        }
        die(json_encode(array("msg" => $dat)));
    } else if (isset($_POST['enableServer']) && intval($_POST['enableServer']) > 0) {
        $api = new API($_POST['username'], $_POST['password']);
        $dat = $api->enableServer($sql, $_POST['enableServer']);
        if (isset($API_ERRORS[$dat])) {
            die(json_encode(array("error" => $API_ERRORS[$dat])));
        }
        die(json_encode(array("msg" => $dat)));
    } else {
        die(json_encode(array("api_methods" => $API_METHODS)));
    }
} else {
    die(json_encode(array("error" => $API_ERRORS['NO_USERNAME_PASSWORD_DEFINED'])));
}

