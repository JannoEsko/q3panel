<?php

    session_start();

require_once __DIR__ . "/local_SQL.php";
require_once __DIR__ . "/classes/loader.php";
/**
 * This file holds all the generic functions and is also the starting point for all of the class 
 * function callouts, POST/GET requests etc. Here it all gets logged as well.
 */

//server_id: server_id,
//        command: command,
//        sendRCONCommand: 1
if (isset($_POST['server_id'], $_POST['command'], $_POST['sendRCONCommand']) && intval($_POST['sendRCONCommand']) === 1 && intval($_POST['server_id']) > 0) {
    $data = Server::getServersWithHostAndGame($sql, $_SESSION['user_id'], $_POST['server_id']);
    if (sizeof($data) === 1) {
        $data = $data[0];
        if (intval($data['can_see_rcon']) === 1 || User::canPerformAction($sql, $_SESSION['user_id'], Constants::$SERVER_ADMIN)) {
            $host = new Host($data['host_id'], $data['servername'], $data['hostname'], $data['sshport'], $data['host_username'], $data['host_password']);
            $game = new Game($data['game_id'], $data['game_name'], $data['game_location'], $data['startscript']);
            $server = new Server($data['server_id'], $host, $data['server_name'], $game, $data['server_port'], $data['server_account'], $data['server_password'], $data['server_status'], $data['server_startscript'], $data['current_players'], $data['max_players'], $data['rconpassword']);
            $output = $server->sendQ3Command(Constants::$SERVER_ACTIONS['Q3_RCON_COMMAND'] . $_POST['command'], true, true);
            $output = str_replace("\xFF\xFF\xFF\xFFprint\n", "", $output);
            die(json_encode(array("output" => $output)));
        } else {
            Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GET_SERVER_DATA_NOT_MAPPED_OR_DOESNT_EXIST'] . $_POST['server_id']);
            die(json_encode(array("error" => Constants::$ERRORS['GENERIC_PRIVILEGE_ERROR'])));
        }
    }
    Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GET_SERVER_DATA_NOT_MAPPED_OR_DOESNT_EXIST'] . $_POST['server_id']);
    die(json_encode(array("error" => Constants::$ERRORS['GENERIC_PRIVILEGE_ERROR'])));
}


if (isset($_POST['updateServer'], $_POST['server_id'], $_POST['server_name'], $_POST['server_port'], $_POST['max_players'], $_POST['rconpassword']) && intval($_POST['server_id']) > 0 && intval($_POST['updateServer']) === 1 && strlen($_POST['server_name']) > 0 && intval($_POST['server_port']) > 0 && intval($_POST['max_players']) > 0 && strlen($_POST['rconpassword']) > 0 && User::canPerformAction($sql, $_SESSION['user_id'], Constants::$SERVER_ADMIN)) {
    $data = Server::getServersWithHostAndGame($sql, $_SESSION['user_id'], $_POST['server_id']);
    if (sizeof($data) === 1) {
        $data = $data[0];
        $host = new Host($data['host_id'], $data['servername'], $data['hostname'], $data['sshport'], $data['host_username'], $data['host_password']);
        $game = new Game($data['game_id'], $data['game_name'], $data['game_location'], $data['startscript']);
        $server = new Server($data['server_id'], $host, $data['server_name'], $game, $data['server_port'], $data['server_account'], $data['server_password'], $data['server_status'], $data['server_startscript'], $data['current_players'], $data['max_players'], $data['rconpassword']);
        //First stop, then edit, push the edits to SQL and then start if it's started.
        $isServerStarted = intval($data['server_status']) === Constants::$SERVER_STARTED;
        
        $server->setRconpassword($_POST['rconpassword']);
        $server->setMax_players($_POST['max_players']);
        $server->setServer_port($_POST['server_port']);
        $server->setServer_name($_POST['server_name']);
        $out = $server->updateServer($sql);
        if (isset($out['rows_affected'])) {
            if ($isServerStarted) {
                $server->restartServer($sql);
            }
            die(json_encode(array("msg" => Constants::$MESSAGES['SERVER_EDIT_SUCCESS'], "toggleModal" => "serverModal")));
        } else {
            die(json_encode($out));
        }
    }
    die(Constants::$ERRORS['GENERIC_PRIVILEGE_ERROR']);
}


if (isset($_POST['server_id'], $_POST['addMap'], $_POST['user_id']) && intval($_POST['server_id']) > 0 && intval($_POST['addMap']) === 1 && intval($_POST['user_id']) > 0 && User::canPerformAction($sql, $_SESSION['user_id'], Constants::$SERVER_ADMIN)) {
    $can_stop_server = bool2int(isset($_POST['can_stop_server']) && trim($_POST['can_stop_server']) === "on");
    $can_see_rcon = bool2int(isset($_POST['can_see_rcon']) && trim($_POST['can_see_rcon']) === "on");
    $can_see_ftp = bool2int(isset($_POST['can_see_ftp']) && trim($_POST['can_see_ftp']) === "on");
    $dat = Server::addUserMapping($sql, $_POST['server_id'], $_POST['user_id'], $can_stop_server, $can_see_rcon, $can_see_ftp);
    if ($dat === false || !isset($dat['last_insert_id'])) {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), str_replace("{user_id}", $_POST['user_id'], str_replace("{server_id}", $_POST['server_id'], Constants::$LOGGER_MESSAGES['ERRORS']['ADD_MAPPING'])));
        die(json_encode(array("error" => Constants::$ERRORS['ADD_MAPPING_ERROR'])));
        
    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), str_replace("{user_id}", $_POST['user_id'], str_replace("{server_id}", $_POST['server_id'], Constants::$LOGGER_MESSAGES['SUCCESSES']['ADD_MAPPING'])));
        die(json_encode(array("href" => "?server_id=" . $_POST['server_id'])));
        
    }
}


if (isset($_POST['server_id'], $_POST['editMap'], $_POST['user_id']) && intval($_POST['server_id']) > 0 && intval($_POST['editMap']) === 1 && intval($_POST['user_id']) > 0 && User::canPerformAction($sql, $_SESSION['user_id'], Constants::$SERVER_ADMIN)) {
    $can_stop_server = bool2int(isset($_POST['can_stop_server']) && trim($_POST['can_stop_server']) === "on");
    $can_see_rcon = bool2int(isset($_POST['can_see_rcon']) && trim($_POST['can_see_rcon']) === "on");
    $can_see_ftp = bool2int(isset($_POST['can_see_ftp']) && trim($_POST['can_see_ftp']) === "on");
    $dat = Server::editUserMapping($sql, $_POST['server_id'], $_POST['user_id'], $can_stop_server, $can_see_rcon, $can_see_ftp);
    if ($dat === false || !isset($dat['rows_affected'])) {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), str_replace("{user_id}", $_POST['user_id'], str_replace("{server_id}", $_POST['server_id'], Constants::$LOGGER_MESSAGES['ERRORS']['EDIT_MAPPING'])));
        die(json_encode(array("error" => Constants::$ERRORS['EDIT_MAPPING_ERROR'], "toggleModal" => "serverMap")));
    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), str_replace("{user_id}", $_POST['user_id'], str_replace("{server_id}", $_POST['server_id'], Constants::$LOGGER_MESSAGES['SUCCESSES']['EDIT_MAPPING'])));
        die(json_encode(array("msg" => Constants::$MESSAGES['EDIT_MAPPING_SUCCESS'], "toggleModal" => "serverMap", "action" => "serverMapUpdate", "updateRow" => $_POST['user_id'], "can_see_rcon" => $can_see_rcon, "can_see_ftp" => $can_see_ftp, "can_stop_server" => $can_stop_server)));
    }
}

function bool2int($input) {
    if ($input) {
        return 1;
    }
    return 0;
}


if (isset($_POST['deleteMap'], $_POST['server_id'], $_POST['removeMapUser']) && intval($_POST['deleteMap']) > 0 && intval($_POST['server_id']) > 0 && intval($_POST['removeMapUser']) > 0 && User::canPerformAction($sql, $_SESSION['user_id'], Constants::$SERVER_ADMIN)) {
    $dat = Server::removeUserFromMapping($sql, $_POST['server_id'], $_POST['removeMapUser']);
    if ($dat !== false && intval($dat['rows_affected']) === 1) {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), str_replace("{server_id}", $_POST['server_id'], str_replace("{user_id}", $_POST['removeMapUser'], Constants::$LOGGER_MESSAGES['SUCCESSES']['REMOVE_USER_SERVER_MAP_SUCCESS'])));
        die(json_encode(array("msg" => Constants::$MESSAGES['USER_MAPPING_REMOVED'], "removeMapTableRow" => $_POST['removeMapUser'])));
    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), str_replace("{server_id}", $_POST['server_id'], str_replace("{user_id}", $_POST['removeMapUser'], Constants::$LOGGER_MESSAGES['ERRORS']['REMOVE_USER_SERVER_MAP'])));
        die(json_encode(array("error" => Constants::$MESSAGES['USER_MAPPING_REMOVED_ERROR'])));
    }
    
}

if (isset($_GET['server_id']) && isset($_GET['dev'])) {
    $_POST['server_id'] = $_GET['server_id'];
    $data = Server::getServersWithHostAndGame($sql, null, $_POST['server_id']);
    if (sizeof($data) === 1) {
        $data = $data[0];
            $host = new Host($data['host_id'], $data['servername'], $data['hostname'], $data['sshport'], $data['host_username'], $data['host_password']);
            $game = new Game($data['game_id'], $data['game_name'], $data['game_location'], $data['startscript']);
            $server = new Server($data['server_id'], $host, $data['server_name'], $game, $data['server_port'], $data['server_account'], $data['server_password'], $data['server_status'], $data['server_startscript'], $data['current_players'], $data['max_players'], $data['rconpassword']);
            die(print_r($server->checkServer($sql)));
    }
}

if (isset($_POST['server_id'], $_POST['generateNewFTP']) && intval($_POST['server_id']) > 0 && intval($_POST['generateNewFTP']) === 1 && User::canPerformAction($sql, $_SESSION['user_id'], Constants::$SERVER_ADMIN)) {
    $data = Server::getServersWithHostAndGame($sql, $_SESSION['user_id'], $_POST['server_id']);
    if (sizeof($data) === 1) {
        $data = $data[0];
        $host = new Host($data['host_id'], $data['servername'], $data['hostname'], $data['sshport'], $data['host_username'], $data['host_password']);
        $game = new Game($data['game_id'], $data['game_name'], $data['game_location'], $data['startscript']);
        $server = new Server($data['server_id'], $host, $data['server_name'], $game, $data['server_port'], $data['server_account'], $data['server_password'], $data['server_status'], $data['server_startscript'], $data['current_players'], $data['max_players'], $data['rconpassword']);
        $output = $server->changeServerAccountPassword($sql, generateRandomKey(8));
        if (isset($output['error'])) {
            Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GENERATE_NEW_FTP_ERROR'] . $output['error']);
            die(json_encode($output));
        } else {
            Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['SUCCESSES']['FTP_PSW_GENERATE'] . $_POST['server_id']);
            die(json_encode(array("msg" => Constants::$MESSAGES['FTP_PASSWORD_CHANGE_SUCCESS'])));
        }
    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['FTP_PSW_GENERATE_PRIVILEGE'] . $_POST['server_id']);
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_PRIVILEGE_ERROR'])));
    }

}

if (isset($_POST['server_id'], $_POST['resetFTPPassword'], $_POST['newFTPPassword']) && intval($_POST['server_id']) > 0 && intval($_POST['resetFTPPassword']) === 1 && User::canPerformAction($sql, $_SESSION['user_id'], Constants::$SERVER_ADMIN)) {
    $data = Server::getServersWithHostAndGame($sql, $_SESSION['user_id'], $_POST['server_id']);
    if (sizeof($data) === 1) {
        $data = $data[0];
        $host = new Host($data['host_id'], $data['servername'], $data['hostname'], $data['sshport'], $data['host_username'], $data['host_password']);
        $game = new Game($data['game_id'], $data['game_name'], $data['game_location'], $data['startscript']);
        $server = new Server($data['server_id'], $host, $data['server_name'], $game, $data['server_port'], $data['server_account'], $data['server_password'], $data['server_status'], $data['server_startscript'], $data['current_players'], $data['max_players'], $data['rconpassword']);
        $output = $server->changeServerAccountPassword($sql, $_POST['newFTPPassword']);
        if (isset($output['error'])) {
            Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GENERIC_SERVER_HOST_ERROR'] . " Server id: " . $_POST['server_id'] . ", Host id: " . $data['host_id'] . ". Error: " . $output['error']);
            die(json_encode($output));
        } else {
            Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['SUCCESSES']['FTP_PSW_EDIT'] . $_POST['server_id']);
            die(json_encode(array("msg" => Constants::$MESSAGES['FTP_PASSWORD_CHANGE_SUCCESS'], "newFTPPasswordSet" => "1")));
        }
    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['FTP_PSW_CHANGE_PRIVILEGE'] . $_POST['server_id']);
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_PRIVILEGE_ERROR'])));
    }

} 


    
if (isset($_POST['getFTPURIForFile'], $_POST['fileName'], $_POST['server_id']) && intval($_POST['getFTPURIForFile']) > 0) {
    $data = Server::getServersWithHostAndGame($sql, $_SESSION['user_id'], $_POST['server_id']);
    if (sizeof($data) === 1) {
        $data = $data[0];
        if (intval($data['can_see_ftp']) === 1 || User::canPerformAction($sql, $_SESSION['user_id'], Constants::$SERVER_ADMIN)) {
            $host = new Host($data['host_id'], $data['servername'], $data['hostname'], $data['sshport'], $data['host_username'], $data['host_password']);
            $game = new Game($data['game_id'], $data['game_name'], $data['game_location'], $data['startscript']);
            $server = new Server($data['server_id'], $host, $data['server_name'], $game, $data['server_port'], $data['server_account'], $data['server_password'], $data['server_status'], $data['server_startscript'], $data['current_players'], $data['max_players'], $data['rconpassword']);
            $ftp = new FTP($server);
            $uri = $ftp->getFileDownloadURI($_POST['fileName']);
            die(json_encode(array("href" => $ftp->getFileDownloadURI($_POST['fileName']))));
        } else {
            Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GET_FTP_FILE_NOT_PRIVILEGED'] . ", server id: " . $_POST['server_id'] . ", file name: " . $_POST['fileName']);
            die(json_encode(array("error" => Constants::$ERRORS['GENERIC_FTP_ERROR'])));
            
        }

    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GET_SERVER_DATA_NOT_MAPPED_OR_DOESNT_EXIST'] . $_POST['server_id']);
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_PRIVILEGE_ERROR'])));
    }
}

if (isset($_POST['server_id'], $_POST['newFileUpload'], $_POST['newcurrdir']) && intval($_POST['server_id']) > 0 && intval($_POST['newFileUpload']) > 0) {
    $data = Server::getServersWithHostAndGame($sql, $_SESSION['user_id'], $_POST['server_id']);
    if (sizeof($data) === 1) {
        $data = $data[0];
        if (intval($data['can_see_ftp']) === 1  || User::canPerformAction($sql, $_SESSION['user_id'], Constants::$SERVER_ADMIN)) {
            $host = new Host($data['host_id'], $data['servername'], $data['hostname'], $data['sshport'], $data['host_username'], $data['host_password']);
            $game = new Game($data['game_id'], $data['game_name'], $data['game_location'], $data['startscript']);
            $server = new Server($data['server_id'], $host, $data['server_name'], $game, $data['server_port'], $data['server_account'], $data['server_password'], $data['server_status'], $data['server_startscript'], $data['current_players'], $data['max_players'], $data['rconpassword']);
            $ftp = new FTP($server);
            $output = $ftp->uploadNewFile($_POST['newcurrdir'], $_FILES['newUploadedFile']['name'], $_FILES['newUploadedFile']['tmp_name']);
            if ($output === false) {
                $_SESSION['fileUploadStatus'] = "error";
                Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GENERIC_FTP_ERROR'] . "Server id: " . $_POST['server_id']);
                $_SESSION['fileUploadMsg'] = Constants::$ERRORS['GENERIC_FTP_ERROR'];
            } else {
                $_SESSION['fileUploadStatus'] = "success";
                $_SESSION['fileUploadMsg'] = Constants::$MESSAGES['FTP_FILE_UPLOAD_SUCCESS'];
            }
        } else {
            Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['FILE_UPLOAD_NOT_PRIVILEGED'] . $_POST['server_id']);
        }

    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GET_SERVER_DATA_NOT_MAPPED_OR_DOESNT_EXIST'] . $_POST['server_id']);
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_PRIVILEGE_ERROR'])));
    }
}


if (isset($_POST['server_id'], $_POST['newFileOrFolder'], $_POST['newcurrdir'], $_POST['creatableFileName'], $_POST['newfilecontents']) && 
        intval($_POST['server_id']) > 0 && intval($_POST['newFileOrFolder']) === 1 && strlen(trim($_POST['newcurrdir'])) > 0
        && strlen(trim($_POST['creatableFileName'])) > 0) {
    $data = Server::getServersWithHostAndGame($sql, $_SESSION['user_id'], $_POST['server_id']);
    if (sizeof($data) === 1) {
        $data = $data[0];
        if (intval($data['can_see_ftp']) === 1 || User::canPerformAction($sql, $_SESSION['user_id'], Constants::$SERVER_ADMIN)) {
            $host = new Host($data['host_id'], $data['servername'], $data['hostname'], $data['sshport'], $data['host_username'], $data['host_password']);
            $game = new Game($data['game_id'], $data['game_name'], $data['game_location'], $data['startscript']);
            $server = new Server($data['server_id'], $host, $data['server_name'], $game, $data['server_port'], $data['server_account'], $data['server_password'], $data['server_status'], $data['server_startscript'], $data['current_players'], $data['max_players'], $data['rconpassword']);
            $ftp = new FTP($server);
            $output = $ftp->createNewFile($_POST['newcurrdir'], $_POST['creatableFileName'], $_POST['newfilecontents']);
            if ($output === false) {
                Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GENERIC_FTP_ERROR'] . "Server id: " . $_POST['server_id']);
                die(json_encode(array("error" => Constants::$ERRORS['GENERIC_FTP_ERROR'])));
            } else {
                die(json_encode(array("msg" => Constants::$MESSAGES['FTP_NEW_FILE_SUCCESS'], "successnewfile" => $_POST['newcurrdir'])));
            }
        }

    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GET_SERVER_DATA_NOT_MAPPED_OR_DOESNT_EXIST'] . $_POST['server_id']);
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_PRIVILEGE_ERROR'])));
    }
}

  
if (isset($_POST['server_id'], $_POST['newFileOrFolder'], $_POST['newcurrdir'], $_POST['newfoldername']) && intval($_POST['newFileOrFolder']) === 1 && strlen(trim($_POST['newcurrdir'])) > 0 && strlen(trim($_POST['newfoldername'])) > 0 && intval($_POST['server_id']) > 0) {
    $data = Server::getServersWithHostAndGame($sql, $_SESSION['user_id'], $_POST['server_id']);
    if (sizeof($data) === 1) {
        $data = $data[0];
        if (intval($data['can_see_ftp']) === 1 || User::canPerformAction($sql, $_SESSION['user_id'], Constants::$SERVER_ADMIN)) {
            $host = new Host($data['host_id'], $data['servername'], $data['hostname'], $data['sshport'], $data['host_username'], $data['host_password']);
            $game = new Game($data['game_id'], $data['game_name'], $data['game_location'], $data['startscript']);
            $server = new Server($data['server_id'], $host, $data['server_name'], $game, $data['server_port'], $data['server_account'], $data['server_password'], $data['server_status'], $data['server_startscript'], $data['current_players'], $data['max_players'], $data['rconpassword']);
            $ftp = new FTP($server);
            $output = $ftp->createNewFolder($_POST['newcurrdir'], $_POST['newfoldername']);
            if ($output === false) {
                Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GENERIC_FTP_ERROR'] . "Server id: " . $_POST['server_id']);
                die(json_encode(array("error" => Constants::$ERRORS['GENERIC_FTP_ERROR'])));
            } else {
                die(json_encode(array("msg" => Constants::$MESSAGES['FTP_NEW_FOLDER_SUCCESS'], "successnewfolder" => $_POST['newcurrdir'])));
            }
        }

    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GET_SERVER_DATA_NOT_MAPPED_OR_DOESNT_EXIST'] . $_POST['server_id']);
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_PRIVILEGE_ERROR'])));
    }
}

if (isset($_POST['renameFileOrFolder'], $_POST['oldfilename'], $_POST['server_id'], $_POST['newfilename']) && intval($_POST['renameFileOrFolder']) === 1 && intval($_POST['server_id']) > 0 && strlen(trim($_POST['oldfilename'])) > 0 && strlen(trim($_POST['newfilename'])) > 0) {
    $data = Server::getServersWithHostAndGame($sql, $_SESSION['user_id'], $_POST['server_id']);
    if (sizeof($data) === 1) {
        $data = $data[0];
        if (intval($data['can_see_ftp']) === 1 || User::canPerformAction($sql, $_SESSION['user_id'], Constants::$SERVER_ADMIN)) {
            $host = new Host($data['host_id'], $data['servername'], $data['hostname'], $data['sshport'], $data['host_username'], $data['host_password']);
            $game = new Game($data['game_id'], $data['game_name'], $data['game_location'], $data['startscript']);
            $server = new Server($data['server_id'], $host, $data['server_name'], $game, $data['server_port'], $data['server_account'], $data['server_password'], $data['server_status'], $data['server_startscript'], $data['current_players'], $data['max_players'], $data['rconpassword']);
            $ftp = new FTP($server);
            $output = $ftp->renameFileOrFolder($_POST['oldfilename'], $_POST['newfilename']);
            if ($output === false) {
                Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GENERIC_FTP_ERROR'] . "Server id: " . $_POST['server_id']);
                die(json_encode(array("error" => Constants::$ERRORS['GENERIC_FTP_ERROR'])));
            } else {
                die(json_encode(array("msg" => Constants::$MESSAGES['FTP_FILE_OR_FOLDER_RENAME_SUCCESS'], "refreshwebftptable" => "1")));
            }
        }

    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GET_SERVER_DATA_NOT_MAPPED_OR_DOESNT_EXIST'] . $_POST['server_id']);
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_PRIVILEGE_ERROR'])));
    } 
}

if (isset($_POST['deleteFromFTP'], $_POST['filename'], $_POST['server_id']) && intval($_POST['deleteFromFTP']) === 1 && intval($_POST['server_id']) > 0) {
    $data = Server::getServersWithHostAndGame($sql, $_SESSION['user_id'], $_POST['server_id']);
    if (sizeof($data) === 1) {
        $data = $data[0];
        if (intval($data['can_see_ftp']) === 1 || User::canPerformAction($sql, $_SESSION['user_id'], Constants::$SERVER_ADMIN)) {
            $host = new Host($data['host_id'], $data['servername'], $data['hostname'], $data['sshport'], $data['host_username'], $data['host_password']);
            $game = new Game($data['game_id'], $data['game_name'], $data['game_location'], $data['startscript']);
            $server = new Server($data['server_id'], $host, $data['server_name'], $game, $data['server_port'], $data['server_account'], $data['server_password'], $data['server_status'], $data['server_startscript'], $data['current_players'], $data['max_players'], $data['rconpassword']);
            $ftp = new FTP($server);
            $output = $ftp->deleteFileOrDir($_POST['filename']);
            if ($output === false) {
                Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GENERIC_FTP_ERROR'] . "Server id: " . $_POST['server_id']);
                die(json_encode(array("error" => Constants::$ERRORS['GENERIC_FTP_ERROR'])));
            } else {
                die(json_encode(array("msg" => Constants::$MESSAGES['FTP_FILE_OR_FOLDER_DELETE_SUCCESS'])));
            }
        }

    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GET_SERVER_DATA_NOT_MAPPED_OR_DOESNT_EXIST'] . $_POST['server_id']);
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_PRIVILEGE_ERROR'])));
    }
}


if (isset($_POST['editFile'], $_POST['server_id'], $_POST['filename'], $_POST['fileContents']) && intval($_POST['editFile']) === 1 && intval($_POST['server_id']) > 0) {
    $data = Server::getServersWithHostAndGame($sql, $_SESSION['user_id'], $_POST['server_id']);
    if (sizeof($data) === 1) {
        $data = $data[0];
        if (intval($data['can_see_ftp']) === 1 || User::canPerformAction($sql, $_SESSION['user_id'], Constants::$SERVER_ADMIN)) {
            $host = new Host($data['host_id'], $data['servername'], $data['hostname'], $data['sshport'], $data['host_username'], $data['host_password']);
            $game = new Game($data['game_id'], $data['game_name'], $data['game_location'], $data['startscript']);
            $server = new Server($data['server_id'], $host, $data['server_name'], $game, $data['server_port'], $data['server_account'], $data['server_password'], $data['server_status'], $data['server_startscript'], $data['current_players'], $data['max_players'], $data['rconpassword']);
            $ftp = new FTP($server);
            $output = $ftp->writeFile($_POST['filename'], $_POST['fileContents']);
            if ($output === false) {
                Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GENERIC_FTP_ERROR'] . "Server id: " . $_POST['server_id']);
                die(json_encode(array("error" => Constants::$ERRORS['GENERIC_FTP_ERROR'])));
            } else {
                die(json_encode(array("msg" => Constants::$MESSAGES['FTP_FILE_UPDATE_SUCCESS'])));
            }
        }

    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GET_SERVER_DATA_NOT_MAPPED_OR_DOESNT_EXIST'] . $_POST['server_id']);
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_PRIVILEGE_ERROR'])));
    } 
}


if (isset($_POST['getFile'], $_POST['fileName'], $_POST['server_id']) && intval($_POST['getFile']) === 1 && intval($_POST['server_id']) > 0) {
    $data = Server::getServersWithHostAndGame($sql, $_SESSION['user_id'], $_POST['server_id']);
    if (sizeof($data) === 1) {
        $data = $data[0];
        if (intval($data['can_see_ftp']) === 1 || User::canPerformAction($sql, $_SESSION['user_id'], Constants::$SERVER_ADMIN)) {
            $host = new Host($data['host_id'], $data['servername'], $data['hostname'], $data['sshport'], $data['host_username'], $data['host_password']);
            $game = new Game($data['game_id'], $data['game_name'], $data['game_location'], $data['startscript']);
            $server = new Server($data['server_id'], $host, $data['server_name'], $game, $data['server_port'], $data['server_account'], $data['server_password'], $data['server_status'], $data['server_startscript'], $data['current_players'], $data['max_players'], $data['rconpassword']);
            $ftp = new FTP($server);
            die(json_encode($ftp->getFileContents($_POST['fileName'])));
        } else {
            Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GENERIC_FTP_PERMISSION_ERROR'] . $_POST['server_id']);
        }

    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GET_SERVER_DATA_NOT_MAPPED_OR_DOESNT_EXIST'] . $_POST['server_id']);
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_PRIVILEGE_ERROR'])));
    }
}

if (isset($_POST['ftp'], $_POST['getDirContents'], $_POST['server_id']) && intval($_POST['ftp']) === 1 && strlen($_POST['getDirContents']) > 0 && intval($_POST['server_id']) > 0) {
    if (!isset($_SESSION['ftp_last_dir'])) {
        $_SESSION['ftp_last_dir'] = ".";
    } 
    if ($_POST['getDirContents'] === "../") {
        $_POST['getDirContents'] = $_SESSION['ftp_last_dir'] . "/..";
        
    }
    $_SESSION['ftp_last_dir'] = $_POST['getDirContents'];
    $data = Server::getServersWithHostAndGame($sql, $_SESSION['user_id'], $_POST['server_id']);
    if (sizeof($data) === 1) {
        $data = $data[0];
        if (intval($data['can_see_ftp']) === 1 || User::canPerformAction($sql, $_SESSION['user_id'], Constants::$SERVER_ADMIN)) {
            $host = new Host($data['host_id'], $data['servername'], $data['hostname'], $data['sshport'], $data['host_username'], $data['host_password']);
            $game = new Game($data['game_id'], $data['game_name'], $data['game_location'], $data['startscript']);
            $server = new Server($data['server_id'], $host, $data['server_name'], $game, $data['server_port'], $data['server_account'], $data['server_password'], $data['server_status'], $data['server_startscript'], $data['current_players'], $data['max_players'], $data['rconpassword']);
            $ftp = new FTP($server);
            die(json_encode($ftp->getDirectoryFileList($_POST['getDirContents'])));
        } else {
            Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GENERIC_FTP_PERMISSION_ERROR'] . $_POST['server_id']);
        }


    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GET_SERVER_DATA_NOT_MAPPED_OR_DOESNT_EXIST'] . $_POST['server_id']);
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_PRIVILEGE_ERROR'])));
    }
}

if (isset($_POST['deleteServer'], $_POST['server_id']) && intval($_POST['deleteServer']) === 1 && intval($_POST['server_id']) > 0 && User::canPerformAction($sql, $_SESSION['user_id'], Constants::$PANEL_ADMIN)) {
    $data = Server::getServersWithHostAndGame($sql, null, $_POST['server_id']);
    if (sizeof($data) === 1) {
        $data = $data[0];
        $host = new Host($data['host_id'], $data['servername'], $data['hostname'], $data['sshport'], $data['host_username'], $data['host_password']);
        $game = new Game($data['game_id'], $data['game_name'], $data['game_location'], $data['startscript']);
        $server = new Server($data['server_id'], $host, $data['server_name'], $game, $data['server_port'], $data['server_account'], $data['server_password'], $data['server_status'], $data['server_startscript'], $data['current_players'], $data['max_players'], $data['rconpassword']);
        if ($server->deleteServer($sql)) {
            Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['SUCCESSES']['DELETE_SERVER'] . $_POST['server_id'] . " and name " . $data['server_name']);
            die(json_encode(array("href" => "../")));
        } else {
            Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['DELETE_SERVER_GENERIC_ERROR'] . $_POST['server_id']);
            die(json_encode(array("error" => Constants::$ERRORS['GENERIC_ERROR'])));
        }
    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GET_SERVER_DATA_NOT_MAPPED_OR_DOESNT_EXIST'] . $_POST['server_id']);
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_PRIVILEGE_ERROR'])));
    }
}


if (isset($_POST['disableServer'], $_POST['server_id']) && intval($_POST['disableServer']) === 1 && intval($_POST['server_id']) > 0 && User::canPerformAction($sql, $_SESSION['user_id'], Constants::$PANEL_ADMIN)) {
    $data = Server::getServersWithHostAndGame($sql, null, $_POST['server_id']);
    if (sizeof($data) === 1) {
        $data = $data[0];
        $host = new Host($data['host_id'], $data['servername'], $data['hostname'], $data['sshport'], $data['host_username'], $data['host_password']);
        $game = new Game($data['game_id'], $data['game_name'], $data['game_location'], $data['startscript']);
        $server = new Server($data['server_id'], $host, $data['server_name'], $game, $data['server_port'], $data['server_account'], $data['server_password'], $data['server_status'], $data['server_startscript'], $data['current_players'], $data['max_players'], $data['rconpassword']);
        if ($server->disableServer($sql)) {
            Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['SUCCESSES']['SERVER_DISABLED'] . $_POST['server_id']);
            die(json_encode(array("msg" => "Server successfully disabled")));
        } else {
            Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['DISABLE_SERVER_GENERIC_ERROR'] . $_POST['server_id']);
            die(json_encode(array("error" => Constants::$ERRORS['GENERIC_ERROR'])));
        }
    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GET_SERVER_DATA_NOT_MAPPED_OR_DOESNT_EXIST'] . $_POST['server_id']);
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_PRIVILEGE_ERROR'])));
    }
}

if (isset($_POST['enableServer'], $_POST['server_id']) && intval($_POST['enableServer']) === 1 && intval($_POST['server_id']) > 0 && User::canPerformAction($sql, $_SESSION['user_id'], Constants::$PANEL_ADMIN)) {
    $data = Server::getServersWithHostAndGame($sql, null, $_POST['server_id']);
    if (sizeof($data) === 1) {
        $data = $data[0];
        $host = new Host($data['host_id'], $data['servername'], $data['hostname'], $data['sshport'], $data['host_username'], $data['host_password']);
        $game = new Game($data['game_id'], $data['game_name'], $data['game_location'], $data['startscript']);
        $server = new Server($data['server_id'], $host, $data['server_name'], $game, $data['server_port'], $data['server_account'], $data['server_password'], $data['server_status'], $data['server_startscript'], $data['current_players'], $data['max_players'], $data['rconpassword']);
        if ($server->enableServer($sql)) {
            Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['SUCCESSES']['ENABLE_SERVER'] . $_POST['server_id']);
            die(json_encode(array("msg" => "Server successfully enabled")));
        } else {
            Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['ENABLE_SERVER_GENERIC_ERROR'] . $_POST['server_id']);
            die(json_encode(array("error" => Constants::$ERRORS['GENERIC_ERROR'])));
        }
    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GET_SERVER_DATA_NOT_MAPPED_OR_DOESNT_EXIST'] . $_POST['server_id']);
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_PRIVILEGE_ERROR'])));
    }
}

if (isset($_POST['startServer'], $_POST['server_id']) && intval($_POST['startServer']) === 1 && intval($_POST['server_id']) > 0) {
    //$data = Server::getServersWithHost($sql, $_POST['server_id']);
    $data = Server::getServersWithHostAndGame($sql, $_SESSION['user_id'], $_POST['server_id']);
    if (sizeof($data) === 1) {
        $data = $data[0];
        if ((intval($data['can_stop_server']) === 1  || User::canPerformAction($sql, $_SESSION['user_id'], Constants::$SERVER_ADMIN)) && intval($data['server_status']) !== Constants::$SERVER_DISABLED) {
            $host = new Host($data['host_id'], $data['servername'], $data['hostname'], $data['sshport'], $data['host_username'], $data['host_password']);
            $game = new Game($data['game_id'], $data['game_name'], $data['game_location'], $data['startscript']);
            $server = new Server($data['server_id'], $host, $data['server_name'], $game, $data['server_port'], $data['server_account'], $data['server_password'], $data['server_status'], $data['server_startscript'], $data['current_players'], $data['max_players'], $data['rconpassword']);
            if ($server->startServer($sql)) {
                Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['SUCCESSES']['START_SERVER'] . $_POST['server_id']);
                die(json_encode(array("msg" => "Server successfully started")));
            } else {
                Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['START_SERVER_GENERIC_ERROR'] . $_POST['server_id']);
                die(json_encode(array("error" => Constants::$ERRORS['GENERIC_ERROR'])));
            }
        } else {
            Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['START_SERVER_DISABLED_OR_NO_AUTH'] . $_POST['server_id']);
            die(json_encode(array("error" => Constants::$ERRORS['SERVER_DISABLED_OR_NOT_AUTHORIZED'])));
        }
        
    
    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GET_SERVER_DATA_NOT_MAPPED_OR_DOESNT_EXIST'] . $_POST['server_id']);
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_PRIVILEGE_ERROR'])));
    }
    
}


if (isset($_POST['stopServer'], $_POST['server_id']) && intval($_POST['stopServer']) === 1 && intval($_POST['server_id']) > 0) {
    $data = Server::getServersWithHostAndGame($sql, $_SESSION['user_id'], $_POST['server_id']);
    if (sizeof($data) === 1) {
        $data = $data[0];
        if ((intval($data['can_stop_server']) === 1  || User::canPerformAction($sql, $_SESSION['user_id'], Constants::$SERVER_ADMIN)) && intval($data['server_status']) !== Constants::$SERVER_DISABLED) {
            $host = new Host($data['host_id'], $data['servername'], $data['hostname'], $data['sshport'], $data['host_username'], $data['host_password']);
            $game = new Game($data['game_id'], $data['game_name'], $data['game_location'], $data['startscript']);
            $server = new Server($data['server_id'], $host, $data['server_name'], $game, $data['server_port'], $data['server_account'], $data['server_password'], $data['server_status'], $data['server_startscript'], $data['current_players'], $data['max_players'], $data['rconpassword']);
            if ($server->stopServer($sql)) {
                Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['SUCCESSES']['SERVER_STOPPED'] . $_POST['server_id']);
                die(json_encode(array("msg" => "Server successfully stopped")));
            } else {
                Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['STOP_SERVER_GENERIC'] . $_POST['server_id']);
                die(json_encode(array("error" => Constants::$ERRORS['GENERIC_ERROR'])));
            }
        } else {
            die(json_encode(array("error" => Constants::$ERRORS['SERVER_DISABLED_OR_NOT_AUTHORIZED'])));
        }
        
    
    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GET_SERVER_DATA_NOT_MAPPED_OR_DOESNT_EXIST'] . $_POST['server_id']);
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_PRIVILEGE_ERROR'])));
    }
    
}


if (isset($_POST['addServer'], $_POST['server_name'], $_POST['server_port'], $_POST['server_account'], $_POST['server_password'], $_POST['max_players'], $_POST['rconpassword']) && intval($_POST['addServer']) === 1 && User::canPerformAction($sql, $_SESSION['user_id'], Constants::$PANEL_ADMIN)) {
    $getHost = Host::getHosts($sql, $_POST['host_id'], Constants::$INCLUDE_PASSWORD);
    if (sizeof($getHost) === 1) {
        $getGame = Game::getGames($sql, $_POST['game_id']);
        if (sizeof($getGame) === 1) {
            $getGame = $getGame[0];
            $getHost = $getHost[0];
            $host = new Host($getHost['host_id'], $getHost['servername'], $getHost['hostname'], $getHost['sshport'], $getHost['host_username'], $getHost['host_password']);
            $game = new Game($getGame['game_id'], $getGame['game_name'], $getGame['game_location'], $getGame['startscript']);
            $server = new Server(null, $host, $_POST['server_name'], $game, $_POST['server_port'], $_POST['server_account'], $_POST['server_password'], null, null, null, $_POST['max_players'], $_POST['rconpassword']);
            if (strlen(trim($_POST['server_password'])) === 0) {
                $server->setServer_password(generateRandomKey(8));
            }
            
            $dat = $server->addServer($sql);
            if (isset($dat['error'])) {
                Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['ADD_SERVER_GENERIC'] . $_POST['host_id'] . ", error message: " . $_dat['error']);
                die(json_encode($dat));
            } else {
                Logger.log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['SUCCESSES']['ADD_SERVER'] . $_POST['host_id'] . ". New server name - " . $_POST['server_name']);
                die(json_encode(array("href" => ".")));
            }
            
        }
        
    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['DIDNT_FIND_HOST']);
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_PRIVILEGE_ERROR'])));
    }
}

if (isset($_POST['updateHost'], $_POST['hostId'], $_POST['servername'], $_POST['hostname'], $_POST['sshport'], $_POST['host_username'], $_POST['host_password']) && intval($_POST['updateHost']) === 1 && User::canPerformAction($sql, $_SESSION['user_id'], Constants::$PANEL_ADMIN)) {
    $host = new Host($_POST['hostId'], $_POST['servername'], $_POST['hostname'], $_POST['sshport'], $_POST['host_username'], $_POST['host_password']);
    $out = $host->updateHost($sql);
    if (isset($out['rows_affected']) && intval($out['rows_affected']) === 1) {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['SUCCESSES']['HOST_UPDATE'] . $_POST['hostId']);
        die(json_encode(array("href" => ".")));
    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['HOST_UPDATE_GENERIC'] . $_POST['hostId'] . ", error message: " . $out['error']);
        die(json_encode($out));
    }
}

if (isset($_POST['deleteHost'], $_POST['hostId']) && intval($_POST['deleteHost']) === 1 && User::canPerformAction($sql, $_SESSION['user_id'], Constants::$PANEL_ADMIN)) {
    $dat = Host::deleteHost($sql, $_POST['hostId']);
    if (isset($dat['error'])) {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['HOST_DELETE_GENERIC'] . $_POST['hostId'] . ", error message: " . $dat['error']);
        die(json_encode($dat));
    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['SUCCESSES']['HOST_DELETED'] . $_POST['hostId']);
        die(json_encode(array("href" => ".")));
    }
}

if (isset($_POST['getHostData'], $_POST['host_id']) && intval($_POST['getHostData']) === 1 && intval($_POST['host_id']) > 0) {
    die(json_encode(Host::getHosts($sql, $_POST['host_id'])[0]));
}

if (isset($_POST['addHost'], $_POST['servername'], $_POST['hostname'], $_POST['sshport'], $_POST['host_username'], $_POST['host_password']) && intval($_POST['addHost']) === 1 && User::canPerformAction($sql, $_SESSION['user_id'], Constants::$PANEL_ADMIN)) {
    $host = new Host(null, $_POST['servername'], $_POST['hostname'], $_POST['sshport'], $_POST['host_username'], $_POST['host_password']);
    $dat = $host->addHost($sql);
    if (isset($dat['rows_affected']) && intval($dat['rows_affected']) === 1) {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['SUCCESSES']['NEW_HOSTSERVER'] . $dat['last_insert_id']);
        die(json_encode(array("href" => ".")));
    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['NEW_HOSTSERVER_GENERIC'] . $dat['error']);
        die(json_encode($dat));
    }
}


if (isset($_POST['updateGame'], $_POST['gameId'], $_POST['game_name'], $_POST['game_location'], $_POST['startscript']) && intval($_POST['updateGame']) === 1 && User::canPerformAction($sql, $_SESSION['user_id'], Constants::$PANEL_ADMIN)) {
    $dat = Game::updateGame($sql, $_POST['gameId'], $_POST['game_name'], $_POST['game_location'], $_POST['startscript']);
    if (intval($dat['rows_affected']) === 1) {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['SUCCESSES']['UPDATE_GAME'] . $_POST['gameId']);
        die(json_encode(array("href" => ".")));
    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['UPDATE_GAME_GENERIC'] . $_POST['gameId']);
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_ERROR'])));
    }
}


if (isset($_POST['deleteGame'], $_POST['gameId']) && intval($_POST['deleteGame']) === 1 && User::canPerformAction($sql, $_SESSION['user_id'], Constants::$PANEL_ADMIN)) {
    $dat = Game::deleteGame($sql, $_POST['gameId']);
    if (isset($dat['error'])) {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['DELETE_GAME_GENERIC'] . $_POST['gameId'] . ", error message: " . $dat['error']);
        die(json_encode($dat));
    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['SUCCESSES']['DELETE_GAME'] . $_POST['gameId']);
        die(json_encode(array("href" => ".")));
    }
}

if (isset($_POST['getGame'], $_POST['game_id']) && intval($_POST['game_id']) > 0 && User::canPerformAction($sql, $_SESSION['user_id'], Constants::$PANEL_ADMIN)) {
    $dat = Game::getGames($sql, $_POST['game_id']);
    if (sizeof($dat) === 1) {
        $dat = $dat[0];
        die(json_encode($dat));
    } else {
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_ERROR'])));
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
}

if (isset($_POST['requestRecovery'], $_POST['email'])) {
    echo json_encode(User::forgotPassword($sql, $_POST['email'], generateRandomKey(), $HOST_URL));
}

if (isset($_GET['recover'])) {
    $dat = User::recovery($sql, $_GET['recover']);
    if (isset($dat['error'])) {
        $_SESSION['FPSW_ERROR'] = $dat['error'];
    }
}

if (isset($_POST['recover'], $_POST['password'])) { 
    $data = User::changeForgottenPassword($sql, $_POST['password'], $_POST['recover']);
    if (isset($data['error'])) {
        echo json_encode($data);
    } else {
        echo json_encode(array("href" => "."));
    }
    
}

if (isset($_POST['theme'], $_POST['themename'])) {
    echo json_encode(User::changeUserStylePreference($sql, $_SESSION['user_id'], $_POST['themename']));
    die();
}

if (isset($_POST['login'], $_POST['username'], $_POST['password'])) {
    session_destroy();
    $user = new User($_POST['username'], $_POST['password']);
    $data = $user->authenticate($sql);
    if (isset($data['error'])) {
        Logger::logFailedLogin($sql, $_POST['username'], getUserIP());
        echo json_encode($data);
    } else if (isset($_SESSION['installer'])) {
        echo json_encode(array("href" => "../step6/"));
    } else {
        echo json_encode(array("href" => "."));
    }
}

if (isset($_GET['testsendgrid'])) {
    $emailPrefs = Email::getEmailPreferences($sql);
    $email = new Email($emailPrefs['from_email'], "eskojanno@gmail.com", "Test email", "Test email<br>hello", $emailPrefs['from_name'] . " SendGrid", "Janno");
    echo json_encode($email->sendEmail(int2bool($emailPrefs['is_sendgrid']), $emailPrefs['api_key']));
    die();
}

if (isset($_GET['testphpmailer'])) {
    $emailPrefs = Email::getEmailPreferences($sql);
    $email = new Email($emailPrefs['from_email'], "eskojanno@gmail.com", "Test email", "Test email<br>hello", $emailPrefs['from_name'] . " PHPMailer", "Janno");
    echo json_encode($email->sendEmail());
    die();
}

function int2bool($input) {
    return intval($input) === 1;
}

if (isset($_GET['getExternalUser'], $_GET['extUserName'])) {
    echo json_encode(User::getExternalAccountSelect2($sql, $_GET['extUserName']));
}

if (isset($_POST['extAccount'], $_POST['extUser'], $_POST['extUserGroup']) && (isset($_SESSION['installer']) || User::canPerformAction($sql, $_SESSION['user_id'], Constants::$PANEL_ADMIN))) {
    
    $user = new User($_POST['extUser'], null, "1", null, $_POST['extUserGroup'], 1);
    $dat = $user->register($sql);
    if (isset($dat['error'])) {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GENERIC_NEW_EXT_USER_ERROR'] . $_POST['extUser'] . ", error message: " . $dat['error']);
        echo json_encode($dat);
    } else if (isset($_SESSION['installer'])) {
        echo json_encode(array("href" => "../step6/"));
    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['SUCCESSES']['NEW_EXT_USER'] . $_POST['extUser']);
        echo json_encode(array("href" => "."));
    }
}

if (isset($_POST['register'], $_POST['userGroup'], $_POST['username'], $_POST['password'], $_POST['email']) && (isset($_SESSION['installer']) || User::canPerformAction($sql, $_SESSION['user_id'], Constants::$PANEL_ADMIN))) {
    $user = new User($_POST['username'], $_POST['password'], 0, $_POST['email'], $_POST['userGroup'], 1);
    $dat = $user->register($sql);
    if (isset($dat['error'])) {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['GENERIC_NEW_USER_ERROR'] . $_POST['username'] . ", error message: " . $dat['error']);
        echo json_encode($dat);
    } else if (isset($_SESSION['installer'])) {
        echo json_encode(array("href" => "../step6/"));
    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['SUCCESSES']['NEW_LOCAL_USER'] . $_POST['username']);
        echo json_encode(array("href" => "."));
    }
}

if (isset($_GET['setuptables'])) {
    require_once __DIR__ . "/classes/installation/Installation.php";
    Installation::initializeTables($sql);
    print_r("Tables setup done");
}

if (isset($_POST['host']) && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['db']) && isset($_POST['url']) && !file_exists(__DIR__ . "/config.php")) {
    require_once __DIR__ . "/classes/installation/Installation.php";
    $db_host = $_POST['host'];
    $db_username = $_POST['username'];
    $db_password = $_POST['password'];
    $db = $_POST['db'];
    $url = $_POST['url'];
    
    $r = Installation::initializeConfig($db_host, $db_username, $db_password, $db, $url);
    if (!isset($r['error'])) {
        $_SESSION['installer'] = "1";
        require_once __DIR__ . "/classes/sql/SQL.php";
        $sql = new SQL($db_host, $db_username, $db_password, $db);
        $ret2 = Installation::initializeTables($sql);
        if ($ret2['error'] !== null) {
            echo json_encode($ret2);
            die();
        }
    }
    echo json_encode($r);
}

if (isset($_POST['exthost'], $_POST['extusername'], $_POST['password'], $_POST['extdb'], $_POST['usrtable'], $_POST['usrtableid'], $_POST['usrtablename'], $_POST['usrtablepsw'], $_POST['usrtableemail'], $_SESSION['installer'])) {
    require_once __DIR__ . "/classes/installation/Installation.php";
    $ext_host = $_POST['exthost'];
    $ext_user = $_POST['extusername'];
    $ext_pass = $_POST['password'];
    $ext_db = $_POST['extdb'];
    $ext_usrtable = $_POST['usrtable'];
    $ext_usrtableid = $_POST['usrtableid'];
    $ext_usrtableusrname = $_POST['usrtablename'];
    $ext_usrtablepsw = $_POST['usrtablepsw'];
    $ext_usrtableemail = $_POST['usrtableemail'];
    
    echo json_encode(Installation::initializeExternalConnection($sql, $ext_host, $ext_user, $ext_pass, $ext_db, $ext_usrtable, $ext_usrtableid, $ext_usrtableusrname, $ext_usrtablepsw, $ext_usrtableemail));
}

if (isset($_POST['isSendgrid'], $_POST['fromName'], $_POST['fromEmail']) && (isset($_SESSION['installer']) || User::canPerformAction($sql, $_SESSION['user_id'], Constants::$PANEL_ADMIN))) {
    $is_sendgrid = intval($_POST['isSendgrid']);
    $api_key = null;
    if ($is_sendgrid === 1) {
        $api_key = $_POST['api'];
    }
    $from_name = $_POST['fromName'];
    $from_email = $_POST['fromEmail'];
    $result = Email::saveEmailPreferences($sql, $is_sendgrid, $from_name, $from_email, $api_key);
    if (isset($result['error'])) {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['EMAIL_SETUP_ERROR'] . $result['error']);
        die(json_encode($result));
    } else if (isset($_SESSION['installer'])) {
        die(json_encode(array("href" => "../step5/")));
    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['SUCCESSES']['EMAIL_SETUP']);
        die(json_encode(array("msg" => Constants::$MESSAGES['EMAIL_SETUP'])));
    }
    
}


/**
 * Taken from http://stackoverflow.com/questions/1846202/php-how-to-generate-a-random-unique-alphanumeric-string/13733588#13733588.
 * @param type $length
 * @return string
 */
function generateRandomKey($length = 50) {
    $key = "";
    $possibleValues = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    for ($i = 0; $i < $length; $i++) {
        $key .= $possibleValues[keyRandomizer(0, strlen($possibleValues))];
    }
    return $key;
}

/**
 * Taken from http://stackoverflow.com/questions/1846202/php-how-to-generate-a-random-unique-alphanumeric-string/13733588#13733588
 * @param type $min
 * @param type $max
 * @return type
 */
function keyRandomizer($min, $max) {
    $range = $max - $min;
    if ($range < 0) return $min;
    $log = log($range, 2);
    $bytes = (int) ($log / 8) + 1;
    $bits = (int) $log + 1;
    $filter = (int) (1 << $bits) - 1;
    do {
        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
        $rnd = $rnd & $filter;
    } while ($rnd >= $range);
    return $min + $rnd;
}


function isExternalAuthEnabled($sql) {
    $query = Constants::$SELECT_QUERIES['EXT_AUTH_EXISTS'];
    $ret = $sql->query($query);
    if (intval($ret[0]['count']) === 1) {
        return true;
    }
    return false;
}


if (isset($_POST['addGame'], $_POST['game_name'], $_POST['game_location'], $_POST['startscript']) && intval($_POST['addGame']) === 1 && User::canPerformAction($sql, $_SESSION['user_id'], Constants::$PANEL_ADMIN)) {
    $data = Game::saveGame($sql, $_POST['game_name'], $_POST['game_location'], $_POST['startscript']);
    if (intval($data['rows_affected']) === 1) {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['SUCCESSES']['ADD_GAME'] . $data['last_insert_id']);
        die(json_encode(array("href" => ".")));
    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['ADD_GAME_GENERIC']);
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_ERROR'])));
    }
}

if (isset($_POST['user_id'], $_POST['delete']) && intval($_POST['delete']) === 1) {
    if (User::canEditUser($sql, $_SESSION['user_id'], $_POST['user_id']) > Constants::$CANNOT_EDIT_USER && intval($_SESSION['user_id']) !== intval($_POST['user_id'])) {
        //We won't grab the group id from session data because someone might've changed it in the meantime
        //and thus, we will check it from SQL, so if the user can actually edit an user (delete incl), 
        //we'll let him do it if and only if it's allowed.
        $data = User::deleteAccount($sql, $_POST['user_id']);
        if (intval($data['rows_affected']) === 1) {
            Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['SUCCESSES']['DELETE_USER'] . $_POST['user_id']);
            die(json_encode(array("href" => ".")));
        } else {
            Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['DELETE_USER_ERROR_GENERIC'] . $_POST['user_id']);
            die(json_encode(array("error" => Constants::$ERRORS['GENERIC_ERROR'])));
        }
    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['DELETE_USER_PRIVILEGE_ERROR'] . $_POST['user_id']);
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_PRIVILEGE_ERROR'])));
    }
    
}

if (isset($_POST['user_id'], $_POST['origin'], $_POST['editUser']) && intval($_POST['editUser']) === 1) {
    //first check that if he is editing his own account,
    //is he trying to edit his own group to a lower group (hence, locking himself out from the system).
    $canEditUser = User::canEditUser($sql, $_SESSION['user_id'], $_POST['user_id']);
    if ($canEditUser === Constants::$CANNOT_EDIT_GROUP) {
        $_POST['group'] = null;
    } else if ($canEditUser === Constants::$ONLY_GROUP_EDIT) {
        $_POST['username'] = null;
    } 
    if ($canEditUser > Constants::$CANNOT_EDIT_USER) {
        if (isset($_POST['group']) && intval($_POST['group']) === Constants::$PANEL_ADMIN) {
            Server::mapUserToAllServers($sql, $_POST['user_id']);
        } 
        User::editAccount($sql, $_POST['user_id'], $_POST['username'], $_POST['password'], null, $_POST['email'], $_POST['group']);
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['SUCCESSES']['EDIT_USER'] . $_POST['user_id']);
        die(json_encode(array("href" => ".")));
    } else {
        Logger::log($sql, $_SESSION['user_id'], getUserIP(), Constants::$LOGGER_MESSAGES['ERRORS']['EDIT_ACCOUNT_ERROR']);
        die(json_encode(array("error" => Constants::$ERRORS['GENERIC_ERROR'])));
    }
    
}

/**
 * Taken from http://stackoverflow.com/questions/15699101/get-the-client-ip-address-using-php
 * @return string Returns the requestor IP
 */
function getUserIP() {
    $ipaddress = "";
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    } else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else if(isset($_SERVER['HTTP_X_FORWARDED'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    } else if(isset($_SERVER['HTTP_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    } else if(isset($_SERVER['HTTP_FORWARDED'])) {
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    } else if(isset($_SERVER['REMOTE_ADDR'])) {
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    } else {
        $ipaddress = "UNKNOWN";
    }
    return $ipaddress;
}

function intbool2str($input) {
    $input = int2bool($input);
    if ($input) {
        return "Yes";
    }
    return "No";
}
