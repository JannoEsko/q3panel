<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Writer
 *
 * @author Janno
 */
class Writer {
    
    private $fileLocation;
    
    function __construct($fileLocation) {
        $this->fileLocation = $fileLocation;
    }
    
    function write($str) {
        $fp = fopen($this->fileLocation, "w") or die ("Unable to create file " . $this->fileLocation
                . ", please check that your web engine user has sufficient privileges for that folder");
        fwrite($fp, $str);
        fclose($fp);
    }
}
