<?php

/**
 * Generic SQL class, uses PDO.
 *
 * @author Janno
 */
class SQL {
    
    private $sqltype;
    private PDO $pdo;

    /**
     * Constructs the SQL object.
     * @param type $dsn The PDO DSN.
     * @param type $username Username of the SQL server.
     * @param type $password Password of the SQL server.
     * @param int $sqltype 0 = MySQL, 1 = MSSQL, 2 = DB2 (ODBC PDO)
     * @param int $rw DB2 specific, 0 = read-write, 1 = read-call, 2 = read only.
     * @throws PDOException Throws PDOException when the PDO connection can't be established.
     */
    
    
    
    function __construct($host, $username, $password, $db, $sqltype = 0, $rw = 2) {
        $this->sqltype = $sqltype;
        switch ($sqltype) {
            case 0:
                $dsn = $this->generateMySQLDSN($host, $db);
                break;
            case 1:
                $dsn = $this->generateMsSQLDSN($host, $db);
                break;
            case 2:
                $dsn = $this->generateDB2DSN($host, $db, $rw);
        }
        if ($sqltype === 1) {
            /*
             * ATTR_PERSISTENT not supported on MSSQL
             */
            $this->pdo = new PDO($dsn, $username, $password, array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ));
        } else {
            $this->pdo = new PDO($dsn, $username, $password, array(
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ));
        }
        
    }   
    
    /**
     * Generic query function to insert/fetch data.
     * Simplifies the typical PDO objects.
     * @param string $statement The SQL statement which should be runned.
     * @param array $params Optional parameters for binding values to the statement (values only).
     * @return array Array with SELECT statement, returns all the objects, with other statement, returns the rows affected with the last_insert_id.
     */
    public function query($statement, $params = null, $forceSelect = false) {

        $stmt = $this->pdo->prepare($statement);
        if ($params !== null && is_array($params) && sizeof($params) === 0) {
            $params = null;
        }
        $stmt->execute($params);
        $queryType = substr(trim(strtoupper($statement)), 0, 6);
        
        if ($queryType === "SELECT" || $forceSelect) {
            return $stmt->fetchAll();
        } else {
            return array("rows_affected" => $stmt->rowCount(), "last_insert_id" => $this->pdo->lastInsertId());
        }
    }
    
    private function generateMySQLDSN($host, $db) {
        return "mysql:host=$host;dbname=$db";
    }
    
    private function generateMsSQLDSN($host, $db) {
        return "sqlsrv:Server=$host;Database=$db";
    }
    
    /**
     * 
     * @param type $host
     * @param type $db
     * @param int $rw 0 = read-write, 1 = read-call, 2 = readonly.
     * @return type
     */
    private function generateDB2DSN($host, $db, $rw = 2) {
        return "odbc:DRIVER={Client Access ODBC Driver (32-bit)};SYSTEM=$host;DATABASE=$db;DBQ=QGPL;DFTPKGLIB=QGPL;LANGUAGEID=ENU;PKG=QGPL/DEFAULT(IBM),2,0,1,0,512;CONNTYPE=$rw;";
    }
    
    public function beginTransaction() {
        $this->pdo->beginTransaction();
    }
    
    public function commit() {
        $this->pdo->commit();
    }
    
    public function rollBack() {
        $this->pdo->rollBack();
    }

}
