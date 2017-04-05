<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TestInstallation
 *
 * @author Janno
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../classes/installation/Installation.php";
class TestInstallation extends TestCase {
    
    public function testInstallation() {
        $this->expectException(PDOException::class);
        $this->assertInstanceOf("PDOException", Installation::initializeConfig("127.0.0.1", "0", "0", "0"));
    }
}
