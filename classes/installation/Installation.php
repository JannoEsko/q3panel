<?php
require_once __DIR__ . "/../sql/SQL.php";
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Installation
 *
 * @author Janno
 */
class Installation {
    
    /**
     * First step of the installation process, gets the SQL data, tests the connection, writes it into config.php file.
     * @param type $db_host The database host (IP, URL)
     * @param type $db_username The database's username
     * @param type $db_password The database's password
     * @param type $db The database
     */
    static function initializeConfig($db_host, $db_username, $db_password, $db) {
        try {
            $sql = new SQL($db_host, $db_username, $db_password, $db);
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
