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
        $fp = fopen($this->fileLocation, "w");
        if (!$fp) {
            return array("error" => "Couldn't open file " . $this->fileLocation . " for writing. Please recheck permissions!");
        }
        fwrite($fp, $str);
        fclose($fp);
        return array();
        
    }
}
