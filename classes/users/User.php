<?php

class User {
    
    private $username;
    private $password;
    private $origin;
    private $email;
    private $preferEmail;
    private $group_id;
    
    function __construct($username, $password, $origin = null, $email = null, $group_id = null, $preferEmail = null) {
        $this->username = $username;
        $this->password = $password;
        $this->origin = $origin;
        $this->email = $email;
        $this->preferEmail = $preferEmail;
        $this->group_id = $group_id;
    }
    
    function getGroup_id() {
        return $this->group_id;
    }

    function setGroup_id($group_id) {
        $this->group_id = $group_id;
    }

        
    function getUsername() {
        return $this->username;
    }

    function getPassword() {
        return $this->password;
    }

    function getOrigin() {
        return $this->origin;
    }

    function getEmail() {
        return $this->email;
    }

    function getPreferEmail() {
        return $this->preferEmail;
    }

    function setUsername($username) {
        $this->username = $username;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    function setOrigin($origin) {
        $this->origin = $origin;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setPreferEmail($preferEmail) {
        $this->preferEmail = $preferEmail;
    }

    function authenticate(SQL $sql) {
        $query = Constants::$SELECT_QUERIES['GET_USER_BY_NAME'];
        $params = array($this->getUsername());
        $data = $sql->query($query, $params);
        if (sizeof($data) === 1) {
            $data = $data[0];
            if ($data['origin'] === 1) {
                $ext_query = Constants::$SELECT_QUERIES['GET_EXT_DATA'];
                $ext_data = $sql->query($ext_query);
                if (sizeof($ext_data) === 1) {
                    $ext_data = $ext_data[0];
                    $ext_sql = new SQL($ext_data['host'], $ext_data['db_username'], $ext_data['db_password'], $ext_data['db_name']);
                    $user_id = $data['username'];
                    $ext_user_query = "SELECT " . $ext_data['username_field'] . ", " . $ext_data['password_field'] . ", " . $ext_data['email_field'] . " FROM " . $ext_data['users_table_name'] . " WHERE " . $ext_data['users_id_field'] . " = ?";
                    $ext_params = array($user_id);
                    $ext_user_data = $ext_sql->query($ext_user_query, $ext_params);
                    if (sizeof($ext_user_data) === 1) {
                        $ext_user_data = $ext_user_data[0];
                        $ext_password = $ext_user_data[$ext_data['password_field']];
                        if (password_verify($this->password, $ext_password)) {
                            return array("username" => $this->username, "password" => $this->password, "origin" => "1", "email" => $ext_user_data[$ext_data['email_field']], "group_id" => $data['group_id'], "allow_emails" => $data['group_id']);
                        } else {
                            return array("error" => Constants::$ERRORS['AUTH_WRONG_PASSWORD_OR_DISABLED']);
                        }
                    } else {
                        return array("error" => Constants::$ERRORS['AUTH_NO_DATA_ERROR']);
                    }
                } else {
                    throw new UnexpectedValueException("External users in system, but no external connection in database (or multiple defined).");
                }
            } else {
                $password = $data['password'];
                if (password_verify($this->getPassword(), $password)) {
                    return $data;
                } else {
                    return array("error" => Constants::$ERRORS['AUTH_WRONG_PASSWORD_OR_DISABLED']);
                }
            }
        }
        return array("error" => Constants::$ERRORS['AUTH_NO_DATA_ERROR']);
    }
    
    static function forgotPassword($email) {
        
    }
    
    function register(SQL $sql) {
        $query = Constants::$INSERT_QUERIES['ADD_NEW_USER'];
        $password = password_hash($this->password, PASSWORD_BCRYPT);
        $params = array(
            $this->getUsername(),
            $password,
            $this->getOrigin(),
            $this->getEmail(),
            $this->getGroup_id(),
            $this->getPreferEmail()
        );
        return $sql->query($query, $params);
    }
    
    static function editAccount(SQL $sql, $user_id, $username = null, $password = null, $origin = null, $email = null, $group_id = null, $preferEmail = null) {
        //requires a bit more complex query, can't use constant query.
        $query = "UPDATE q3panel_users SET ";
        $params = array();
        if ($username !== null) {
            $query .= "username = ?,";
            array_push($params, $username);
        }
        if ($password !== null) {
            $query .= "password = ?,";
            $hash = password_hash($password, PASSWORD_BCRYPT);
            array_push($params, $hash);
        }
        if ($origin !== null) {
            $query .= "origin = ?,";
            array_push($params, $origin);
        }
        if ($email !== null) {
            $query .= "email = ?,";
            array_push($params, $email);
        }
        if ($group_id !== null) {
            $query .= "group_id = ?,";
            array_push($params, $group_id);
        }
        if ($preferEmail !== null) {
            $query .= "allow_emails = ?,";
            array_push($params, $preferEmail);
        }
        
        $query = rtrim($query, ",");
        $query .= " WHERE user_id = ?";
        array_push($params, $user_id);
        return $sql->query($query, $params);
    }
    
    static function deleteAccount(SQL $sql, $user_id) {
        $query = Constants::$DELETE_QUERIES['DELETE_USER_BY_ID'];
        $params = array($user_id);
        return $sql->query($query, $params);
    }
}

