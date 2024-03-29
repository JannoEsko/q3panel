<?php

/**
 * Generic User class, handles authentication, registration etc.
 * @author Janno
 */
class User {
    
    private $username;
    private $password;
    private $origin;
    private $email;
    private $preferEmail;
    private $group_id;
    
    /**
     * Constructs a new user object.
     * @param string $username The username.
     * @param string $password The password.
     * @param int $origin Origin of the account. 
     * @param string $email The e-mail address.
     * @param int $group_id The group ID.
     * @param int $preferEmail Whether the user prefers e-mails or not.
     */
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

    /**
     * Authenticates the user account.
     * @param SQL $sql The SQL handle.
     * @return array Returns the error or the SQL output.
     */
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
                //20022018 - if XenForo is defined:
                if (defined("IS_XENFORO") && IS_XENFORO) {
                    require_once __DIR__ . "/../../extensions/XenForo/XenForo.php";
                    $ext_sql = new SQL($db_host, $db_username, $db_password, $db);
                    $auth_data = XenForo::authenticate($ext_sql, $this->getUsername(), $this->getPassword());
                    if (!$auth_data) {
                        return array("error" => Constants::$ERRORS['AUTH_NO_DATA_WRONG_PSW_OR_DISABLED']);
                    }
                    $ext_member_id = $auth_data['member_id'];
                    $ext_username = $auth_data['name'];
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
        }
        return array("error" => Constants::$ERRORS['AUTH_NO_DATA_ERROR']);
    }
    
    /**
     * Replaces the missing parts in an external query.
     * @param string $query The initial query with the {ext_ tags.
     * @param string $ext_usrtable The external usertable.
     * @param string $ext_usrtable_id The external usertable primary key (id field)
     * @param string $ext_usrname The external usertable username field.
     * @param string $ext_psw The external usertable password field.
     * @param string $ext_email The external usertable email field.
     * @return string The query with the parts changed.
     */
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
    
    /**
     * Generates a new SQL object for the external connection.
     * @param string $db_host The database hostname.
     * @param string $db_name The database username.
     * @param string $db_pass The database password.
     * @param string $db The database.
     * @return \SQL Returns the SQL object.
     */
    static function getExternalConnection($db_host, $db_name, $db_pass, $db) {
        return new SQL($db_host, $db_name, $db_pass, $db);
    }
    
    /**
     * Deals with the forgotten password (generates a new key, sends the e-mail out etc).
     * @param SQL $sql The SQL handle.
     * @param string $email The e-mail of the user.
     * @param string $request_key The forgotten password request key.
     * @param string $HOST_URL The host URL.
     * @return array Returns the array with the information (msg or error keys).
     */
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
    
    /**
     * Checks the recovery data.
     * @param SQL $sql The SQL handle.
     * @param string $request_key The recovery request key.
     * @return array Returns the error message or the command to show the form.
     */
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
    
    /**
     * Changes the forgotten password.
     * @param SQL $sql The SQL handle.
     * @param type $newPass New password.
     * @param type $requestKey The forgotten password request key.
     * @return array Returns the SQL output, error if an error was thrown.
     */
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
    
    /**
     * Registeres a new account.
     * @param SQL $sql The SQL handle.
     * @return array Returns empty array, if all went well, array with an error key if an error occurred.
     */
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
            if (intval($this->group_id) === Constants::$PANEL_ADMIN) {
                $serverMapQuery = Constants::$INSERT_QUERIES['MAP_USER_TO_ALL_SERVERS'];
                $serverMapParams = array($user_id);
                $sql->query($serverMapQuery, $serverMapParams);
            }
            return array();
        } catch (PDOException $ex) {
            return array("error" => $ex->getMessage());
        }
    }
    
    /**
     * Edits an user account.
     * @param SQL $sql The SQL handle.
     * @param int $user_id The user ID field.
     * @param string $username [optional] The username (if you wish to change)
     * @param string $password [optional] The password (if you wish to change)
     * @param int $origin [optional] The origin (if you wish to change)
     * @param string $email [optional] The email (if you wish to change)
     * @param int $group_id [optional] The group_id (if you wish to change)
     * @param int $preferEmail [optional] Prefer emails (whether you wish to change).
     * @return array Returns the SQL output.
     */
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
    
    /**
     * Deletes an user account.
     * @param SQL $sql The SQL handle.
     * @param int $user_id The user ID to delete.
     * @return array Returns the SQL output.
     */
    static function deleteAccount(SQL $sql, $user_id) {
        $query = Constants::$DELETE_QUERIES['DELETE_USER_BY_ID'];
        $params = array($user_id);
        return $sql->query($query, $params);
    }
    
    /**
     * Checks the user by id and group.
     * @param SQL $sql The SQL handle.
     * @param int $user_id The user ID to search for.
     * @param int $group_id The group ID, the user must match.
     * @return bool Returns true, if data was found, false otherwise.
     */
    static function checkUser(SQL $sql, $user_id, $group_id) {
        $query = Constants::$SELECT_QUERIES['GET_USER_BY_ID_AND_GROUP'];
        $params = array($user_id, $group_id);
        return sizeof($sql->query($query, $params)) === 1;
    }
    
    /**
     * Gets user by the ID.
     * @param SQL $sql The SQL handle.
     * @param int $user_id The user ID
     * @return mixed Returns array, if it found the data. False otherwise.
     */
    static function getUserById(SQL $sql, $user_id) {
        $query = Constants::$SELECT_QUERIES['GET_USER_BY_ID'];
        $params = array($user_id);
        $data = $sql->query($query, $params);
        if (sizeof($data) === 1) {
            $data = $data[0];
            if (intval($data['origin']) === 1) {
                $extdata = self::getExternalAccount($sql, $data['username'], true);
                $data['email'] = $extdata['data'][0][$extdata['extTable_spec']['email_field']];
                $data['realName'] = $extdata['data'][0][$extdata['extTable_spec']['username_field']];
            } else {
                $data['realName'] = $data['username'];
            }
            return $data;
        }
        return false;
    }
    
    /**
     * Check whether the given user can edit the user it wants to edit.
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
            if (intval($user_id === $editable_user_id)) {
                if (intval($editableUser['origin']) === Constants::$EXTERNAL_ACCOUNT) {
                    return Constants::$CANNOT_EDIT_USER;
                } else {
                    return Constants::$CANNOT_EDIT_GROUP;
                }
            }
            $user_group_id = intval($data['group_id']);
            if ($user_group_id === 3) {
                if (intval($editableUser['origin']) === 1) {
                    if ($user_id === $editable_user_id) {
                        return Constants::$CANNOT_EDIT_USER;
                    } else {
                        return Constants::$ONLY_GROUP_EDIT;
                    }
                } else {
                    if ($user_id === $editable_user_id) {
                        return Constants::$CANNOT_EDIT_GROUP;
                    } else {
                        return Constants::$CAN_EDIT_USER;
                    }
                }
            }
            
        }
        return -1;
    }
    
    /**
     * Gets all users.
     * @param SQL $sql The SQL handle.
     * @param type $group_id [optional] If specified, it'll search all user accounts with the group id higher than or equal to the input.
     * @return array Returns the user ID's.
     */
    static function getAllUsers(SQL $sql, $group_id = null) {
        $extData = self::getExtData($sql);
        $localQuery = "";
        $params = null;
        if ($group_id !== null) {
            $localQuery = Constants::$SELECT_QUERIES['GET_ALL_USERS_BY_GROUP_HIGHER_THAN'];
            $params = array($group_id);
        } else {
            $localQuery = Constants::$SELECT_QUERIES['GET_ALL_USERS'];
        }
        
        $localData = $sql->query($localQuery, $params);
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
    
    /**
     * Gets the external account information.
     * @param SQL $sql The SQL handle.
     * @param int $ext_user_id The external user ID.
     * @param bool $extTable_spec [optional] If true, it will return the data with the external table specification (so you can use it to get the data out from the array).
     * @return array Returns the external account.
     */
    static function getExternalAccount(SQL $sql, $ext_user_id, $extTable_spec = false, SQL $extSql = null, $extData = null) {
        if ($extSql !== null && $extSql instanceof SQL && $extData !== null) {
            $extExists = false;
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
                $extExists = true;
                $extQuery = self::getExternalQuery(Constants::$SELECT_QUERIES['GET_EXT_USER_BY_ID'], $users_table, $user_id, $username_field, null, $email_field);
                if ($extTable_spec) {
                    return array("extTable_spec" => array("user_id_field" => $user_id, "username_field" => $username_field, "email_field" => $email_field), "data" => $extSql->query($extQuery, array($ext_user_id)));
                }
                return $extSql->query($extQuery, array($ext_user_id));
            } 
            return array();
        } else {
        $extData = self::getExtData($sql);
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
            if ($extTable_spec) {
                return array("extTable_spec" => array("user_id_field" => $user_id, "username_field" => $username_field, "email_field" => $email_field), "data" => $extSql->query($extQuery, array($ext_user_id)));
            }
            return $extSql->query($extQuery, array($ext_user_id));
            
        }
        return array();
        }
    }
    
    /**
     * Gets the Select2 output for external accounts.
     * @param SQL $sql The SQL handle
     * @param string $username The searchable username.
     * @return array Returns an array which can be parsed by Select2.
     */
    static function getExternalAccountSelect2(SQL $sql, $username) {
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
    
    /**
     * Gets the external database data.
     * @param SQL $sql The SQL handle.
     * @return array Returns array with the external data, empty array if an unknown error occurred.
     */
    static function getExtData(SQL $sql) {
        $query = Constants::$SELECT_QUERIES['GET_EXT_DATA'];
        $data = $sql->query($query);
        if (sizeof($data) === 1) {
            $data = $data[0];
            return $data;
        }
        return array();
    }
    
    /**
     * Sets the session variables.
     * @param array $data The data, which to set to session variables.
     */
    static function setSessionVariables($data) {
        session_start();
        $_SESSION['user_id'] = $data['user_id'];
        $_SESSION['group_id'] = $data['group_id'];
        $_SESSION['username'] = $data['realUsername'];
        $_SESSION['style'] = $data['style_name'];
        $_SESSION['style_bg'] = $data['style_bg'];
    }
    
    /**
     * Saves the user style preference into the database.
     * @param SQL $sql The SQL handle.
     * @param int $user_id The user id, for which to change it.
     * @param string $style The new style.
     * @return array Returns the SQL output, an array with the error key if an error occurred.
     */
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
    
    /**
     * A generic function to check whether the given user can perform an action with the given group id.
     * Basically, it's just a group check, does the given user id have a higher or equal group level as the given action level.
     * @param SQL $sql The SQL handle.
     * @param int $user_id The user id, who wants to perform an action.
     * @param int $action_level The level of the action (see user levels in Constants).
     * @return boolean Returns true, if the user's group is >= than $action_level, false otherwise.
     */
    static function canPerformAction(SQL $sql, $user_id, $action_level) {
        $query = Constants::$SELECT_QUERIES['GET_USER_BY_ID_AND_GROUP_LARGER_THAN'];
        $params = array($user_id, $action_level);
        $data = $sql->query($query, $params);
        return sizeof($data) === 1;        
    }
}
