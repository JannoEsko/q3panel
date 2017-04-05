<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
/**
 * Holds all the required classes, so instead of requiring files separately where they are needed,
 * we can use this file to load everything through a single file.
 */

$d = __DIR__;



require_once "$d/sql/SQL.php"; 
require_once "$d/../config.php";
require_once "$d/api/API.php";
require_once "$d/servers/host/Host.php";
require_once "$d/servers/Server.php";
require_once "$d/users/User.php";
require_once "$d/Constants.php";
require_once "$d/../functions.php";


