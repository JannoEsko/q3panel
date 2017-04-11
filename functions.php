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

if (isset($_POST['host']) && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['db']) && isset($_POST['url'])) {
    require_once __DIR__ . "/classes/installation/Installation.php";
    $db_host = $_POST['host'];
    $db_username = $_POST['username'];
    $db_password = $_POST['password'];
    $db = $_POST['db'];
    $url = $_POST['url'];
    
    echo Installation::initializeConfig($db_host, $db_username, $db_password, $db, $url);
}

/**
 * Taken from http://stackoverflow.com/questions/1846202/php-how-to-generate-a-random-unique-alphanumeric-string/13733588#13733588.
 * @param type $length
 * @return string
 */
function generateRandomKey($length = 50) {
    $key = "";
    $possibleValues = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    for ($i = 0; $i < $length; $i++) {
        $key .= $possibleValues[keyRandomizer(0, strlen($possibleValues))];
    }
    return $key;
}

/**
 * Taken from http://stackoverflow.com/questions/1846202/php-how-to-generate-a-random-unique-alphanumeric-string/13733588#13733588
 * @param type $min
 * @param type $max
 * @return type
 */
function keyRandomizer($min, $max) {
    $range = $max - $min;
    if ($range < 0) return $min;
    $log = log($range, 2);
    $bytes = (int) ($log / 8) + 1;
    $bits = (int) $log + 1;
    $filter = (int) (1 << $bits) - 1;
    do {
        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
        $rnd = $rnd & $filter;
    } while ($rnd >= $range);
    return $min + $rnd;
}



