<?php

class User {
    
    private $username;
    private $password;
    
    function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }
    
    function authenticate() {
        
    }
    
    static function changePassword($user_id) {
        
    }
    
    static function forgotPassword($email) {
        
    }
}

