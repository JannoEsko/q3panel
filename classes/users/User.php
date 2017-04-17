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
        $query = Constants::$SELECT_QUERIES['GET_LOCAL_USER_BY_NAME'];
        $params = array($this->getUsername());
        $data = $sql->query($query, $params); 
        if (sizeof($data) === 1) {
           
            $data = $data[0];
            $password = $data['password'];
            if (password_verify($this->getPassword(), $password)) {
                $data['realUsername'] = $data['username'];
                self::setSessionVariables($data);
                return $data;
            } else {
                return array("error" => Constants::$ERRORS['AUTH_WRONG_PASSWORD_OR_DISABLED']);
            }
            
        } else {
            $data = self::getExtData($sql);
            if (sizeof($data) !== 0) {
                $query = Constants::$SELECT_QUERIES['GET_EXTERNAL_ACCOUNT'];
                $db_host = $data['host'];
                $db_username = $data['db_username'];
                $db_password = $data['db_password'];
                $db = $data['db_name'];
                $users_table = $data['users_table_name'];
                $user_id = $data['user_id_field'];
                $username_field = $data['username_field'];
                $password_field = $data['password_field'];
                //"SELECT {ext_usrtable_id} FROM {ext_usrtable} WHERE {ext_usrname} = ?"
                $query = str_replace("{ext_usrtable_id}", $user_id, $query);
                $query = str_replace("{ext_usrname}", $username_field, $query);
                $query = str_replace("{ext_usrtable}", $users_table, $query);
                $query = str_replace("{ext_psw}", $password_field, $query);

                $params = array($this->getUsername());
                $ext_sql = new SQL($db_host, $db_username, $db_password, $db);
                $extUsers = $ext_sql->query($query, $params);
                if (sizeof($extUsers) === 1) {
                    $extUsers = $extUsers[0];
                    $ext_member_id = $extUsers[$user_id];
                    $ext_member_psw = $extUsers[$password_field];
                    $ext_username = $extUsers[$username_field];
                    if (password_verify($this->getPassword(), $ext_member_psw)) {
                        //atleast password is correct, check now that do we have that user in our database.
                        $query = Constants::$SELECT_QUERIES['GET_EXT_USER_BY_NAME'];
                        $params = array($ext_member_id);
                        $data = $sql->query($query, $params);
                        if (sizeof($data) === 1) {
                            $data = $data[0];
                            $data['realUsername'] = $ext_username;
                            self::setSessionVariables($data);
                            return $data;
                        } else {
                            return array("error" => Constants::$ERRORS['AUTH_NO_DATA_WRONG_PSW_OR_DISABLED']);
                        }
                    } else {
                        return array("error" => Constants::$ERRORS['AUTH_WRONG_PASSWORD_OR_DISABLED']);
                    }
                }
            }
        }
        return array("error" => Constants::$ERRORS['AUTH_NO_DATA_ERROR']);
    }
    
    static function getExternalQuery($query, $ext_usrtable, $ext_usrtable_id = null, $ext_usrname = null, $ext_psw = null, $ext_email = null) {
        return str_replace("{ext_usrtable}", $ext_usrtable, 
            str_replace("{ext_usrtable_id}", $ext_usrtable_id,
                str_replace("{ext_usrname}", $ext_usrname, 
                    str_replace("{ext_psw}", $ext_psw, 
                            str_replace("{ext_email}", $ext_email, $query)
                    )
                )
            )
        );
        
    }
    
    static function getExternalConnection($db_host, $db_name, $db_pass, $db) {
        return new SQL($db_host, $db_name, $db_pass, $db);
    }
    
    static function forgotPassword(SQL $sql, $email, $request_key, $HOST_URL) {
        require_once __DIR__ . "/../email/Email.php";
        $query = Constants::$SELECT_QUERIES['GET_USER_BY_EMAIL'];
        $params = array($email);
        $data = $sql->query($query, $params);
        if (sizeof($data) === 1) {
            $data = $data[0];
            $user_id = $data['user_id'];
            $forgottenpswquery = Constants::$INSERT_QUERIES['SET_FORGOTTEN_PASSWORD'];
            $forgottenparams = array($user_id, $request_key);
            $sql->query($forgottenpswquery, $forgottenparams);
            $emailPrefs = Email::getEmailPreferences($sql);
            $emailBody = Constants::$EMAIL_TEMPLATE['FORGOTTEN_MSG'];
            $emailBody = str_replace("{FORGOTTEN_URL_KEY}", $HOST_URL . "/?recover=$request_key", $emailBody);
            $emailBody = str_replace("{SENDER_NAME}", $emailPrefs['from_name'], $emailBody);
            $em = new Email($emailPrefs['from_email'], $email, Constants::$EMAIL_TEMPLATE['FORGOTTEN_TITLE'], $emailBody, $emailPrefs['from_name'], null);
            $em->sendEmail(int2bool($emailPrefs['is_sendgrid']), $emailPrefs['api_key']);
            return array("msg" => Constants::$MESSAGES['FPSW_SUCCESS']);
        } else {
           return array("error" => Constants::$ERRORS['FPSW_NO_DATA_ERROR']);
        }
    }
    
    static function recovery(SQL $sql, $request_key) {
        $query = Constants::$SELECT_QUERIES['GET_RECOVERY_DATA'];
        $params = array($request_key);
        $data = $sql->query($query, $params);
        if (sizeof($data) === 1) {
            return array("showform" => "1");
        } else {
            return array("error" => Constants::$ERRORS['NO_RECOVERY_INFO']);
        }
    }
    
    static function changeForgottenPassword(SQL $sql, $newPass, $requestKey) {
        $query = Constants::$SELECT_QUERIES['GET_USER_BY_RECOVERY_DATA'];
        $params = array($requestKey);
        $data = $sql->query($query, $params);
        if (sizeof($data) === 1) {
            $data = $data[0];
            $user_id = $data['user_id'];
            $removeQuery = Constants::$DELETE_QUERIES['DELETE_FORGOTTEN_DATA'];
            $removeParams = array($user_id);
            $sql->query($removeQuery, $removeParams);
            $email = $data['email'];
            $emailPrefs = Email::getEmailPreferences($sql);
            $emailBody = Constants::$EMAIL_TEMPLATE['FPSW_CHANGED'];
            $emailBody = str_replace("{SENDER_NAME}", $emailPrefs['from_name'], $emailBody);
            $em = new Email($emailPrefs['from_email'], $email, Constants::$EMAIL_TEMPLATE['FORGOTTEN_TITLE'], $emailBody, $emailPrefs['from_name'], null);
            $em->sendEmail(int2bool($emailPrefs['is_sendgrid']), $emailPrefs['api_key']);
            return self::editAccount($sql, $user_id, null, $newPass);
        }
        return array("error" => Constants::$ERRORS['CHANGE_FORGOTTEN_ERROR']);
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
        try {
            $data = $sql->query($query, $params);
            $user_id = $data["last_insert_id"];
            $styleQuery = Constants::$INSERT_QUERIES['SET_STYLE_PREFERENCE'];
            $styleParams = array("1", $user_id);
            $sql->query($styleQuery, $styleParams);
            return array();
        } catch (PDOException $ex) {
            return array("error" => $ex->getMessage());
        }
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
    
    //User::canEditUser($sql, $_SESSION['user_id'])
    
    /**
     * @todo Server-based account modifications.
     * @param SQL $sql The SQL handle.
     * @param int $user_id The user id of the requestor.
     * @param int $editable_user_id The user id, which you want to edit.
     * @return int Returns 0, if the account can't be edited with this user, 1 if it can be edited, 2 if it's an external account (so only group edits are allowed), -1 if an error occured and 3 if group can't be edited.
     */
    static function canEditUser(SQL $sql, $user_id, $editable_user_id) {
        $query = Constants::$SELECT_QUERIES['GET_USER_BY_ID'];
        $params = array($user_id);
        $data = $sql->query($query, $params);
        $params2 = array($editable_user_id);
        $editableUser = $sql->query($query, $params2);
        if (sizeof($data) === 1 && sizeof($editableUser) === 1) {
            $data = $data[0];
            $editableUser = $editableUser[0];
            $user_group_id = intval($data['group_id']);
            if ($user_group_id === 3) {
                if (intval($editableUser['origin']) === 1) {
                    if ($user_id === $editable_user_id) {
                        return 0;
                    } else {
                        return 2;
                    }
                } else {
                    if ($user_id === $editable_user_id) {
                        return 3;
                    } else {
                        return 1;
                    }
                }
            }
            
        }
        return -1;
    }
    
    static function getAllUsers(SQL $sql) {
        $extData = self::getExtData($sql);
        $localQuery = Constants::$SELECT_QUERIES['GET_ALL_USERS'];
        $localData = $sql->query($localQuery);
        $extExists = false;
        $extSql = null;
        $extQuery = "";
        $user_id = null;
        $username_field = null;
        $email_field = null;
        if (sizeof($extData) !== 0) {
            $db_host = $extData['host'];
            $db_username = $extData['db_username'];
            $db_password = $extData['db_password'];
            $db = $extData['db_name'];
            $users_table = $extData['users_table_name'];
            $user_id = $extData['user_id_field'];
            $username_field = $extData['username_field'];
            $email_field = $extData['email_field'];
            $extSql = self::getExternalConnection($db_host, $db_username, $db_password, $db);
            $extExists = true;
            $extQuery = self::getExternalQuery(Constants::$SELECT_QUERIES['GET_EXT_USER_BY_ID'], $users_table, $user_id, $username_field, null, $email_field);
        }
        for ($i = 0; $i < sizeof($localData); $i++) {
           
            if (intval($localData[$i]['origin']) === 0) {
                $localData[$i]['realName'] = $localData[$i]['username'];
            } else if ($extExists) {
                $extUser = $extSql->query($extQuery, array($localData[$i]['username']));
                if (sizeof($extUser) === 1) {
                    $extUser = $extUser[0];
                    $localData[$i]['realName'] = $extUser[$username_field];
                    $localData[$i]['email'] = $extUser[$email_field];
                    $localData[$i]['extId'] = $extUser[$user_id];
                }
                
            }
        }
        return $localData;
    }
    
    static function getExternalAccount(SQL $sql, $username) {
        $query = Constants::$SELECT_QUERIES['FIND_EXT_USER_SELECT2'];
        $data = self::getExtData($sql);
        if (sizeof($data) !== 0) {
            $db_host = $data['host'];
            $db_username = $data['db_username'];
            $db_password = $data['db_password'];
            $db = $data['db_name'];
            $users_table = $data['users_table_name'];
            $user_id = $data['user_id_field'];
            $username_field = $data['username_field'];
            //SELECT {ext_usrtable_id}, {ext_usrname} FROM {ext_usrtable} WHERE {ext_usrname} LIKE CONCAT('%', ?, '%')
            $query = str_replace("{ext_usrtable_id}", $user_id, $query);
            $query = str_replace("{ext_usrname}", $username_field, $query);
            $query = str_replace("{ext_usrtable}", $users_table, $query);
            
            $params = array("%" . $username . "%");
            $ext_sql = new SQL($db_host, $db_username, $db_password, $db);
            $extUsers = $ext_sql->query($query, $params);
            foreach($extUsers as $extUser) {
                $dat[] = array("id" => $extUser['id'], "text" => $extUser['text']);
            }
            return $dat;
        }
        return array();
    }
    
    static function getExtData(SQL $sql) {
        $query = Constants::$SELECT_QUERIES['GET_EXT_DATA'];
        $data = $sql->query($query);
        if (sizeof($data) === 1) {
            $data = $data[0];
            return $data;
        }
        return array();
    }
    
    static function canAddUser($sql, $user_id) {
        //Panel admin can add accounts.
        $query = Constants::$SELECT_QUERIES['GET_USER_BY_ID'];
        $params = array($user_id);
        $data = $sql->query($query, $params);
        if (sizeof($data) === 1) {
            $data = $data[0];
            return intval($data['group_id']) === 3;
        }
        return false;
        
    }
    
    static function setSessionVariables($data) {
        session_start();
        $_SESSION['user_id'] = $data['user_id'];
        $_SESSION['group_id'] = $data['group_id'];
        $_SESSION['username'] = $data['realUsername'];
        $_SESSION['style'] = $data['style_name'];
        $_SESSION['style_bg'] = $data['style_bg'];
    }
    
    static function changeUserStylePreference(SQL $sql, $user_id, $style) {
        session_start();
        $getStyleId = Constants::$SELECT_QUERIES['GET_STYLE_BY_NAME'];
        $styleParams = array($style);
        $data = $sql->query($getStyleId, $styleParams);
        if (sizeof($data) === 1) {
            $data = $data[0];
            $style_id = $data['style_id'];
            $setStyle = Constants::$UPDATE_QUERIES['SET_STYLE_FOR_USER'];
            $setStyleParams = array($style_id, $user_id);
            $sql->query($setStyle, $setStyleParams);
            $_SESSION['style'] = $style;
            $_SESSION['style_bg'] = $data['style_bg'];
            return $data;
        }
        return array("error" => "Couldn't load new style");
        
    }
}

