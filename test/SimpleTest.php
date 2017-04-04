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
require_once __DIR__ . "/../classes/users/User.php";
require_once __DIR__ . "/../classes/Constants.php";
require_once __DIR__ . "/../local_SQL.php";

class TestUser extends TestCase {
    public function testUserCreation() {
        $user = new User("testUser", "test123");
        $this->assertEquals(2, 1 + 1);
    }
}
