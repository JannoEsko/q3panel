
<?php
if (!file_exists(__DIR__ . "/../../../config.php")) {
    header("Location: ../../../install/");
}
session_start();
require_once __DIR__ . "/../../../classes/loader.php";
require_once __DIR__ . "/../../../login.php";

if (!isset($_GET['server_id']) || intval($_GET['server_id']) === 0) {
    header("Location: ../../");
}

$data = Server::getServersWithHostAndGame($sql, $_SESSION['user_id'], $_GET['server_id']);
if (sizeof($data) !== 1) {
    header("Location: ../../");
}
$data = $data[0];
$is_server_admin = User::canPerformAction($sql, $_SESSION['user_id'], Constants::$SERVER_ADMIN);
if (intval($data['can_see_ftp']) === 0 && !$is_server_admin) {
    header("Location: ../../");
}

$host = new Host($data['host_id'], $data['servername'], $data['hostname'], $data['sshport'], $data['host_username'], $data['host_password']);
$game = new Game($data['game_id'], $data['game_name'], $data['game_location'], $data['startscript']);
$server = new Server($data['server_id'], $host, $data['server_name'], $game, $data['server_port'], $data['server_account'], $data['server_password'], $data['server_status'], $data['server_startscript'], $data['current_players'], $data['max_players'], $data['rconpassword']);
$ftp = new FTP($server);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta id="themecolor" name="theme-color" content="<?php echo $_SESSION['style_bg']; ?>">
        <title>tt</title>
        <?php echo Constants::getCSS($HOST_URL . "/static"); 
        echo Constants::getPreferencedCSS($HOST_URL . "/static", $_SESSION['style']);
        ?>
        
    </head>
    <body>
        <div class="wrapper">
            <?php require_once __DIR__ . "/../../../static/html/header_aside.php"; ?>
            <section>
                <div class="content-wrapper">
                    <div class="content-heading">
                        Homepage
                        <small>Welcome to Q3Panel</small>
                    </div>
                    <div class="row">
                            <div class="panel janno-panel">
                                <div class="panel-heading">
                                    Web FTP interface
                                </div>
                                <div class="panel-body">
                                    Pick a folder or a file to proceed
                                    <div class="table-responsive table-bordered">
                                        <table class="table table-hover" id="webftptable">
                                            <thead><th>File name</th><th></th></thead>
                                        <tbody id="webftptablebody"></tbody>
                                        </table>
                                    </div>
                                    <br>
                                    <button class="btn btn-default btn-block" onclick="$('#newFileUploadForm').show();$('#newFileFolderForm').hide();$('#newFileFolderModalTitle').html('Upload a new file');$('#newFileFolderModal').modal();">Upload file</button>
                                    <button class="btn btn-default btn-block" onclick="$('#newFileUploadForm').hide();$('#newfile').hide();$('#creatableFileName').prop('disabled', true);$('#newfilecontents').prop('disabled', true);$('#newfolder').show();$('#newfoldername').prop('disabled', false);$('#newFileFolderForm').show();$('#newFileFolderModalTitle').html('Create a new folder');$('#newFileFolderModal').modal();">New folder</button>
                                    <button class="btn btn-default btn-block" onclick="$('#newFileUploadForm').hide();$('#newfile').show();$('#creatableFileName').prop('disabled', false);$('#newfilecontents').prop('disabled', false);$('#newfolder').hide();$('#newfoldername').prop('disabled', true);$('#newFileFolderForm').show();$('#newFileFolderModalTitle').html('Create a new file');$('#newFileFolderModal').modal();">New file</button>
                                    </div>
                                    
                                </div>
                            </div>
                            
                            <div class="panel janno-panel">
                                <div class="panel-heading">
                                    Required files
                                </div>
                                <div class="panel-body"><?php echo nl2br(print_r(get_required_files(), true)); ?></div>
                            </div>
                        
                    </div>
                </div>
            </section>

        </div>
        <div id="fileEditModal" role="dialog" aria-labelledby="fileEditModalTitle" aria-hidden="true" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" data-dismiss="modal" aria-label="Close" class="close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 id="fileEditModalTitle" class="modal-title"></h4>
                    </div>
                    <div class="modal-body" id="serverModalBody">
                         <div class="panel janno-panel" id="formMsgPanel" hidden>
                        <div class="panel-heading" id="formTitle">
                            
                        </div>
                        <div class="panel-body" id="formMsg">
                            
                        </div>
                        
                    </div>
                        <form id="fileForm" role="form" method="post" action="../../../functions.php">
                            <input type="hidden" id="server_id" name="server_id" value="<?php echo $_GET['server_id']; ?>">
                            <input id="filename" name="filename" type="hidden">
                            <input type="hidden" name="editFile" value="1">
                            <div class="form-group">
                                <label>File contents</label>
                                <textarea name="fileContents" class="form-control" rows="15" id="fileContents"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-default btn-block">Submit</button>
                            </div>
                        </form>
                        
                        
                        </div>
                    <div class="modal-footer">
                        <div class="clearfix">
                            
                            <div class="pull-right">
                                <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
                            </div>
                        </div>
                    </div>
                </div> </div>
            </div>
        
            <div id="fileRenameModal" role="dialog" aria-labelledby="fileRenameModalTitle" aria-hidden="true" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" data-dismiss="modal" aria-label="Close" class="close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 id="fileRenameModalTitle" class="modal-title"></h4>
                    </div>
                    <div class="modal-body" id="serverModalBody">
                         <div class="panel janno-panel" id="formMsgPanel" hidden>
                        <div class="panel-heading" id="formTitle">
                            
                        </div>
                        <div class="panel-body" id="formMsg">
                            
                        </div>
                        
                    </div>
                        <form id="fileRenameForm" role="form" method="post" action="../../../functions.php">
                            <input type="hidden" id="server_id" name="server_id" value="<?php echo $_GET['server_id']; ?>">
                            <input id="oldfilename" name="oldfilename" type="hidden">
                            <input type="hidden" name="renameFileOrFolder" value="1">
                            <div class="form-group">
                                <label>New file name</label>
                                <input id="newfilename" name="newfilename" type="text" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-default btn-block">Submit</button>
                            </div>
                        </form>
                        
                        
                        </div>
                    <div class="modal-footer">
                        <div class="clearfix">
                            
                            <div class="pull-right">
                                <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
                            </div>
                        </div>
                    </div>
                </div> </div>
            </div>
        <div id="newFileFolderModal" role="dialog" aria-labelledby="newFileFolderModalTitle" aria-hidden="true" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" data-dismiss="modal" aria-label="Close" class="close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 id="newFileFolderModalTitle" class="modal-title"></h4>
                    </div>
                    <div class="modal-body" id="serverModalBody">
                         
                        <form hidden id="newFileFolderForm" role="form" method="post" action="../../../functions.php">
                            <input type="hidden" id="server_id" name="server_id" value="<?php echo $_GET['server_id']; ?>">
                            <input type="hidden" name="newFileOrFolder" value="1">
                            <input type="hidden" id="newcurrdir" name="newcurrdir">
                            <div id="newfile" hidden>
                            <div class="form-group" >
                                <label>New file name</label>
                                <input disabled id="creatableFileName" name="creatableFileName" type="text" class="form-control" required>
                            </div>
                            <div class="form-group" >
                                <label>New file contents</label>
                                <textarea disabled id="newfilecontents" name="newfilecontents" class="form-control"></textarea>
                            </div>
                            </div>
                            <div id="newfolder" hidden>
                            <div class="form-group" >
                                <label>New folder name</label>
                                <input disabled id="newfoldername" name="newfoldername" type="text" class="form-control" required>
                            </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-default btn-block">Submit</button>
                            </div>
                        </form>
                        <form id="newFileUploadForm" hidden method="post" enctype="multipart/form-data">
                            <input type="hidden" name="server_id" value="<?php echo $_GET['server_id']; ?>">
                            <input type="hidden" name="newFileUpload" value="1">
                            <input type="hidden" name="newcurrdir" id="newFileUploadCurrDir">
                            <div class="form-group">
                                <label>Choose a file</label>
                                <input type="file" class="form-control" name="newUploadedFile" required>
                            </div>
                            
                            <button type="submit" class="btn btn-default btn-block">Submit</button>
                        </form>
                        
                        </div>
                    <div class="modal-footer">
                        <div class="clearfix">
                            
                            <div class="pull-right">
                                <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
                            </div>
                        </div>
                    </div>
                </div> </div>
            </div>
        <?php echo Constants::getJS($HOST_URL . "/static"); ?>
        <script>
    
    
    handleForm("newFileFolderForm", true);handleForm("fileForm");handleForm("fileRenameForm", true);$(document).ready(function() {<?php if (isset($_SESSION['fileUploadStatus'])) {
        echo "toastr." . $_SESSION['fileUploadStatus'] . "(\"" . $_SESSION['fileUploadMsg'] . "\");";
        unset($_SESSION['fileUploadStatus']);
        unset($_SESSION['fileUploadMsg']);
    }        ?>initWebFTPTable('webftptable', '.', '<?php echo $_GET['server_id']; ?>');});</script>
    </body>
</html>
