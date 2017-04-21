<?php
/**
 * Holds all the required classes, so instead of requiring files separately where they are needed,
 * we can use this file to load everything through a single file.
 */

$d = __DIR__;



require_once "$d/sql/SQL.php"; 
require_once "$d/../config.php";

require_once "$d/api/API.php";
require_once "$d/email/Email.php";
require_once "$d/tickets/Ticket.php";
require_once "$d/ssh/SSH.php";
require_once "$d/writer/Writer.php";
require_once "$d/servers/host/Host.php";
require_once "$d/servers/Server.php";
require_once "$d/servers/Game.php";
require_once "$d/users/User.php";
require_once "$d/ftp/FTP.php";
require_once "$d/logger/Logger.php";
require_once "$d/Constants.php";
require_once "$d/../functions.php";



