<?php
require_once __DIR__ . "/../sql/SQL.php";
require_once __DIR__ . "/../writer/Writer.php";
require_once __DIR__ . "/../Constants.php";

/**
 * Handles some of the functionality coming from the installation.
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
    static function initializeConfig($db_host, $db_username, $db_password, $db, $url) {
        try {
            $sql = new SQL($db_host, $db_username, $db_password, $db);
            $test = $sql->query("SELECT 1 + 1 AS sum");
            if ($test[0]['sum'] == 2) { 
                $wtr = new Writer(__DIR__ . "/../../config.php");
                $url = rtrim($url, "/");
                $return = $wtr->write("<?php\n\n\$sql = new SQL(\"$db_host\", \"$db_username\", \"$db_password\", \"$db\");\n\n\n\$HOST_URL = \"$url\";");
                if (isset($return["error"])) {
                    return $return;
                }
                return array("href" => "../step3/");
            }
            
        } catch (PDOException $e) {
            return array("error" => $e->getMessage());
        }
    }
    
    /**
     * Writes all the tables into the SQL database.
     * @param SQL $sql The SQL connection.
     */
    static function initializeTables(SQL $sql) {
        try {
            foreach (Constants::$CREATE_TABLES as $table_query) {
                $sql->query($table_query);
            }
            $sql->query(Constants::$INSERT_QUERIES['ADD_STYLES']);
        } catch (PDOException $ex) {
            return array("error" => $ex->getMessage());
        }
        
    }
    
    /**
     * Initializes the external DB connection and saves it to the database.
     * @param SQL $sql The local SQL handle.
     * @param string $db_host The hostname of the external database.
     * @param string $db_name The database username of the external database.
     * @param string $db_password The database password of the external database.
     * @param string $db The database name of the external database.
     * @param string $table The table name where the user data relies.
     * @param string $id The table's primary key (id) field.
     * @param string $usrname The username field.
     * @param string $psw The password field.
     * @param string $email The e-mail field.
     * @return array Returns array with the href where to forward the user, error otherwise.
     */
    static function initializeExternalConnection(SQL $sql, $db_host, $db_name, $db_password, $db, $table, $id, $usrname, $psw, $email) {
        try {
            $ext_sql = new SQL($db_host, $db_name, $db_password, $db);
            $ext_firstUserQuery = Constants::$SELECT_QUERIES['EXT_GET_FIRST_USER'];
            $ext_firstUserQuery = str_replace("{ext_usrname}", $usrname, $ext_firstUserQuery);
            $ext_firstUserQuery = str_replace("{ext_psw}", $psw, $ext_firstUserQuery);
            $ext_firstUserQuery = str_replace("{ext_email}", $email, $ext_firstUserQuery);
            $ext_firstUserQuery = str_replace("{ext_usrtable}", $table, $ext_firstUserQuery);
            $ext_firstUserQuery = str_replace("{ext_usrtable_id}", $id, $ext_firstUserQuery);
            $data = $ext_sql->query($ext_firstUserQuery);
            if (sizeof($data) === 1) {
                //means we didn't encounter errors.
                $query = Constants::$INSERT_QUERIES['ADD_EXT_DB'];
                $params = array($db_host, $db_name, $db_password, $db, $table, $id, $usrname, $psw, $email);
                $sql->query($query, $params);
                return array("href" => "../step4/");
            }
        } catch (PDOException $ex) {
            return array("error" => $ex->getMessage());
        }
    }
}
