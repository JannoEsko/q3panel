<?php

/**
 * FTP handling class. Called out by the Web FTP interface.
 * @author Janno
 */
class FTP {
    
    private $server;
    private $ftp;
    
    /**
     * Constructs new FTP object.
     * @param Server $server The server object.
     */
    function __construct(Server $server) {
        $this->server = $server;
        $this->ftp = ftp_connect($this->server->getHost()->getHostname());
        ftp_login($this->ftp, $this->server->getHost_username(), $this->server->getHost_password());
    }
    
    /**
     * Gets directory file list of the given location.
     * @param string $location The location, from which to get the list.
     * @return array Returns array of folders and files.
     */
    function getDirectoryFileList($location) {
//return $location;
//$location = str_replace("&", "%26", $location);
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
    
    /**
     * Checks whether the input is a directory or a file.
     * @param string $content The location to test.
     * @return boolean Returns true, if it's a folder, false if it's a file.
     */
    function ftp_is_dir($content) {
  //      $content = str_replace("&", "\&", $content);
        $origin = ftp_pwd($this->ftp);
        if (substr($content, -4) === ".ent") {return false;} 
        if (ftp_chdir($this->ftp, $content)) {
            ftp_chdir($this->ftp, $origin);
            return true;
        }
        return false;
    }
    
    /**
     * Gets the file contents of the given filename from the FTP server.
     * @param string $filename The filename you wish to get.
     * @return array Returns array with the key filecontents.
     */
    function getFileContents($filename) {
    //    $filename = str_replace("&", "\&", $filename);
        return array("filecontents" => file_get_contents("ftp://" . $this->server->getHost_username() . ":" . $this->server->getHost_password() . "@" . $this->server->getHostname() ."/". $filename));
    }
    
    /**
     * Gets the file download URI for a specific file.
     * @param string $filename The filename you wish to get.
     * @return string The file download URI.
     */
    function getFileDownloadURI($filename) {
        return "ftp://" . $this->server->getHost_username() . ":" . $this->server->getHost_password() . "@" . $this->server->getHostname() ."/". $filename;
    }
    
    /**
     * Renames a file/folder.
     * @param string $oldfilename The old file/folder name
     * @param string $newfilename THe new file/folder name
     * @return bool TRUE on success or FALSE on failure.
     */
    function renameFileOrFolder($oldfilename, $newfilename) {
        return ftp_rename($this->ftp, $oldfilename, $newfilename);
    }
    
    /**
     * Creates a new folder in the given location.
     * @param string $location The location, where to create the folder.
     * @param string $newFolderName The new folder name.
     * @return mixed Returns the newly created directory name on success or FALSE on error.
     */
    function createNewFolder($location, $newFolderName) {
        return ftp_mkdir($this->ftp, rtrim($location, "/") . "/" . $newFolderName);
    }
    
    /**
     * Creates a new file.
     * @param string $location The file location.
     * @param string $newfilename The new file name.
     * @param string $newfilecontents The new file contents.
     * @return mixed This function returns the number of bytes that were written to the file, or FALSE on failure.
     */
    function createNewFile($location, $newfilename, $newfilecontents) {
        $location = rtrim($location, "/");
        return file_put_contents("ftp://" . $this->server->getHost_username() . ":" . $this->server->getHost_password() . "@" . $this->server->getHostname() ."/". $location . "/" . $newfilename, $newfilecontents);
    }
    
    /**
     * Removes a file/directory (recursively).
     * @param string $filename The file/folder name.
     * @return boolean Returns true, if it succeeded, false otherwise.
     */
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
    
    /**
     * Uploads a new file to FTP
     * @param string $dir The directory, where to upload.
     * @param string $uploadedFileName The new file name.
     * @param mixed $uploadedFile The file contents.
     * @return mixed This function returns the number of bytes that were written to the file, or FALSE on failure.
     */
    function uploadNewFile($dir, $uploadedFileName, $uploadedFile) {
        $dir = rtrim($dir, "/");
        return file_put_contents("ftp://" . $this->server->getHost_username() . ":" . $this->server->getHost_password() . "@" . $this->server->getHostname() ."/$dir/". $uploadedFileName, file_get_contents($uploadedFile));
    }
    
    /**
     * Writes a file to FTP.
     * @param string $filename
     * @param string $filecontents
     * @return mixed Returns array with key error, if it couldn't delete a file, returns the number of bytes that were written to the file, or FALSE on failure.
     */
    function writeFile($filename, $filecontents) {
        if (ftp_delete($this->ftp, $filename)) {
            return file_put_contents("ftp://" . $this->server->getHost_username() . ":" . $this->server->getHost_password() . "@" . $this->server->getHostname() ."/". $filename, $filecontents);
        } else {
            return array("error" => Constants::$ERRORS['FTP_DELETE_ERROR']);
        }
        
    }
    
    /**
     * Gets the FTP handle.
     * @return resource Returns the FTP handle.
     */
    function getFTPHandle() {
        return $this->ftp;
    }
    
}
