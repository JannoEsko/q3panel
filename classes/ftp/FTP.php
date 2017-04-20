<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FTP
 *
 * @author Janno
 */
class FTP {
    
    private $server;
    private $ftp;
    
    function __construct(Server $server) {
        $this->server = $server;
        $this->ftp = ftp_connect($this->server->getHost()->getHostname());
        ftp_login($this->ftp, $this->server->getHost_username(), $this->server->getHost_password());
    }
    
    function getDirectoryFileList($location) {
        $contents = ftp_nlist($this->ftp, $location);
        $dirs = array();
        $files = array();
        foreach ($contents as $content) {
            if ($this->ftp_is_dir($content)) {
                $dirs[] = array("dir" => "1", "content" => $content);
            } else {
                $files[] = array("dir" => "0", "content" => $content);
            }
        }
        sort($dirs);
        sort($files);
        return array_merge($dirs, $files);
    }
    
    function ftp_is_dir($content) {
        $origin = ftp_pwd($this->ftp);
        if (ftp_chdir($this->ftp, $content)) {
            ftp_chdir($this->ftp, $origin);
            return true;
        }
        return false;
    }
    
    function getFileContents($filename) {
        return array("filecontents" => file_get_contents("ftp://" . $this->server->getHost_username() . ":" . $this->server->getHost_password() . "@" . $this->server->getHostname() ."/". $filename));
    }
    
    function renameFileOrFolder($oldfilename, $newfilename) {
        return ftp_rename($this->ftp, $oldfilename, $newfilename);
    }
    
    function deleteFileOrDir($filename) {
        if (ftp_delete($this->ftp, $filename) === false) {
            if ($children = ftp_nlist($this->ftp, $filename)) {
                foreach($children as $p) {
                    $this->deleteFileOrDir($p);
                }
            }
            return ftp_rmdir($this->ftp, $filename);
        }
        return true;
    }
    
    
    function writeFile($filename, $filecontents) {
        
        if (ftp_delete($this->ftp, $filename)) {
            return file_put_contents("ftp://" . $this->server->getHost_username() . ":" . $this->server->getHost_password() . "@" . $this->server->getHostname() ."/". $filename, $filecontents, 0);
        } else {
            return array("error" => Constants::$ERRORS['FTP_DELETE_ERROR']);
        }
        
    }
    
}
