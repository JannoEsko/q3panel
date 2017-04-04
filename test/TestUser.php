<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SimpleTest
 *
 * @author Janno
 */

use PHPUnit\Framework\TestCase;
require_once __DIR__ . "/../classes/Constants.php";
require_once __DIR__ . "/../classes/users/User.php";
require_once __DIR__ . "/../local_SQL.php";

class SimpleTest extends TestCase {
    private $insertData;
    private $user;
    public function testRegistration() {
        global $sql;
        $this->user = new User("testAccount", "testPassword", "0", "test@email.com", "0", "1");
        $this->insertData = $this->user->register($sql);
        $this->assertEquals(1, $this->insertData['rows_affected']);
    }
    
    public function testSecondRegistration() {
        //constraint username must be unique
        global $sql;
        $this->user = new User("testAccount", "testPassword", "0", "test@email.com", "0", "1");
        $this->expectException(PDOException::class);
        $this->assertInstanceOf("PDOException", $this->user->register($sql));
    }
    
    public function testCheckData() {
        global $sql;
        
    }
}
