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
     * @param String $db_host The database host (IP, URL)
     * @param String $db_username The database's username
     * @param String $db_password The database's password
     * @param String $db The database
     */
    static function initializeConfig($db_host, $db_username, $db_password, $db) {
        try {
            $sql = new SQL($db_host, $db_username, $db_password, $db);
            $test = $sql->query("SELECT 1 + 1 AS sum");
            if ($test[0]['sum'] === 2) {
                $wtr = new Writer(__DIR__ . "/../../config.php");
                $wtr->write("<?php\n\n\$sql = new SQL(\"$db_host\", \"$db_username\", \"$db_password\", \"$db\");\n");
            }
            
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    /**
     * Writes all the tables into the SQL database.
     * @param SQL $sql The SQL connection.
     */
    static function initializeTables(SQL $sql) {
        foreach (Constants::$CREATE_TABLES as $table_query) {
            $sql->query($table_query);
        }
    }
}
