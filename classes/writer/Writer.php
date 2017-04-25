<?php

/**
 * Generic file-writer class.
 * @author Janno
 */
class Writer {
    
    private $fileLocation;
    
    /**
     * Constructs the writer object.
     * @param string $fileLocation The file location.
     */
    function __construct($fileLocation) {
        $this->fileLocation = $fileLocation;
    }
    
    /**
     * Writes to file.
     * @param string $str The string, what to write.
     * @return array Returns array with the key error, if an error occurred. Otherwise, returns an empty array.
     */
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
