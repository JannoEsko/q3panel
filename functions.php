<?php
require_once __DIR__ . "/local_SQL.php";
/**
 * This file holds all the generic functions and is also the starting point for all of the class 
 * function callouts, POST/GET requests etc.
 */

if (isset($_GET['setuptables'])) {
    require_once __DIR__ . "/classes/installation/Installation.php";
    Installation::initializeTables($sql);
    print_r("Tables setup done");
}



