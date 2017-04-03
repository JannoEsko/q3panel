<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Constants
 *
 * @author Janno
 */
class Constants {
    //put your code here
    
    static $INIT_QUERIES = array(
        "CREATE TABLE q3panel_users (user_id INTEGER NOT NULL AUTO_INCREMENT, username VARCHAR(100) NOT NULL, password VARCHAR(255), origin TINYINT DEFAULT 0, email VARCHAR(255), group_id TINYINT, allow_emails TINYINT, PRIMARY KEY(user_id))",
        ""
        
    );
    static $USER_QUERIES = array();
    static $SERVER_QUERIES = array();
}
