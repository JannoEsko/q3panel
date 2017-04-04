<?php

/**
 * This file holds all the generic functions and is also the starting point for all of the class 
 * function callouts, POST/GET requests etc.
 */

if (isset($_GET['setuptables'])) {
    require_once __DIR__ . "/classes/installation/Installation.php";
    Installation::initializeTables(new SQL("127.0.0.1", "q3panel", "c6aFetra!", "q3panel"));
    print_r("Tables setup done");
}



