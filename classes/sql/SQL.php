<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Generic SQL class, uses PDO.
 *
 * @author Janno
 */
class SQL extends PDO {

    /**
     * Constructs the SQL object.
     * @param type $dsn The PDO DSN.
     * @param type $username Username of the SQL server.
     * @param type $password Password of the SQL server.
     * @throws PDOException Throws PDOException when the PDO connection can't be established.
     */
    function __construct($host, $username, $password, $db) {
        $dsn = $this->generateMySQLDSN($host, $db);
        parent::__construct($dsn, $username, $password, array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ));
    }    
    
    /**
     * Generic query function to insert/fetch data.
     * @param string $statement The SQL statement which should be runned.
     * @param array $params Optional parameters for binding values to the statement (values only).
     * @return Array Array with SELECT statement, returns all the objects, with other statement, returns the rows affected.
     */
    public function query($statement, $params = null) {
        $stmt = parent::prepare($statement);
        $stmt->execute($params);
        $queryType = substr(trim(strtoupper($statement)), 0, 6);
        if ($queryType === "SELECT") {
            return $stmt->fetchAll();
        } else {
            return array("rows_affected" => $stmt->rowCount(), "last_insert_id" => $this->pdo->lastInsertId());
        }
    }
    
    private function generateMySQLDSN($host, $db) {
        return "mysql:host=$host;dbname=$db";
    }

}
