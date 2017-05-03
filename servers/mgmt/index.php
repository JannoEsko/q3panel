<?php
if (!file_exists(__DIR__ . "/../../config.php")) {
    header("Location: ../../install/");
}
session_start();
require_once __DIR__ . "/../../classes/loader.php";
require_once __DIR__ . "/../../login.php";
if (!isset($_GET['server_id']) || intval($_GET['server_id']) === 0) {
    header("Location: ../");
}

$server = Server::getServersWithHostAndGame($sql, $_SESSION['user_id'], $_GET['server_id']);
if (sizeof($server) !== 1) {
    header("Location: ../");
}
$server = $server[0];
$is_server_admin = User::canPerformAction($sql, $_SESSION['user_id'], Constants::$SERVER_ADMIN);
if (!intval($server['can_see_rcon']) === 1 && !$is_server_admin) {
    $server['rconpassword'] = "<i>hidden</i>";
}

if (!intval($server['can_see_ftp']) === 1 && !$is_server_admin) {
    $server['server_account'] = "<i>hidden</i>";
    $server['server_password'] = "<i>hidden</i>";
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta id="themecolor" name="theme-color" content="<?php echo $_SESSION['style_bg']; ?>">
        <title>Server management | <?php echo Constants::$PANEL_NAME; ?></title>
<?php
echo Constants::getCSS($HOST_URL . "/static");
echo Constants::getPreferencedCSS($HOST_URL . "/static", $_SESSION['style']);
?>

    </head>
    <body>
        <div class="wrapper">
<?php require_once __DIR__ . "/../../static/html/header_aside.php"; ?>
            <section>
                <div class="content-wrapper">
                    <div class="content-heading">
                        Server management
                        <small>Here you have the possibility to map users to server, disable, delete servers, resetting their FTP password, editing them and also accessing the web RCON and web FTP utility.</small>
                    </div>
                    <div id="toast"></div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="panel janno-panel">
                                <div class="panel-heading">
                                    Server management
                                </div>
                                <div class="panel-body">
                                    <h4>Server: <?php echo $server['server_name']; ?></h4>
                                    This server has currently <b><?php echo $server['current_players']; ?></b> players online, out of a total of <b><?php echo $server['max_players']; ?></b>.
                                    <br>
                                    It is currently <b><?php echo Constants::$MESSAGES['SERVER_STATUSES'][$server['server_status']]; ?></b>
                                    <br>
                                    The server's located at <b><?php echo $server['hostname'] . ":" . $server['server_port']; ?></b>
                                    <br>
                                    Its RCON password is <b><?php echo $server['rconpassword']; ?></b>, FTP account is <b><?php echo $server['server_account']; ?></b> and password is <b><?php echo $server['server_password']; ?></b>.
                                    <br>
                                    The startscript of the server is the following:
                                    <br>
                                    <code>
                                        <?php echo $server['server_startscript']; ?>
                                    </code>
                                    <?php if ($is_server_admin) { ?>
                                    <br>
                                    <br>
                                    <button type="button" class="btn btn-default btn-block" onclick="$('#serverModal').modal();">Edit this server</button>
                                    <?php } ?></div>
                            </div>

                            

                        </div>
                        <div class="col-md-4">
                            <div class="panel janno-panel">
                                <div class="panel-heading">
                                    <div class="panel-title">Actions</div>
                                </div>
                                <div class="panel-body">

<?php
if (User::canPerformAction($sql, $_SESSION['user_id'], Constants::$PANEL_ADMIN)) {
    ?> 
                                        

                                                <div <?php if (intval($server['server_status']) !== Constants::$SERVER_DISABLED) { ?> hidden <?php } ?> id="enableServerBtn">
                                                    <button class="btn btn-danger btn-block" onclick="enableServer('<?php echo $server['server_id']; ?>');">Enable server</button>

                                                </div>
                                                <div <?php if (intval($server['server_status']) === Constants::$SERVER_DISABLED) { ?> hidden <?php } ?> id="disableServerBtn">
                                                    <button class="btn btn-danger btn-block" onclick="disableServer('<?php echo $server['server_id']; ?>');">Disable server</button>
                                                </div>
                                   
                                            <br>
                                                <button class="btn btn-danger btn-block" onclick="deleteServer('<?php echo $server['server_id']; ?>');">Delete server</button>
                                                <br>

<?php } 

if (User::canPerformAction($sql, $_SESSION['user_id'], Constants::$SERVER_ADMIN)) {
?>
                                        
                                        
                                           
                                                <button class="btn btn-default btn-block" onclick="location.href='mapping/?server_id=<?php echo $_GET['server_id']; ?>';">Map users to server</button>
                                            
                                                <button class="btn btn-default btn-block" onclick="resetFtpPassword('ftppswreset');">Reset FTP password</button>
                                       
                                        

<?php } ?>




                                    
<?php
if (intval($server['can_stop_server']) === 1 || $is_server_admin) {
    ?>
                                                <div id="startServer"  <?php if (intval($server['server_status']) === Constants::$SERVER_STARTED || intval($server['server_status']) === Constants::$SERVER_DISABLED) { ?>hidden <?php } ?>>
                                                    <button  class="btn btn-default btn-block" onclick="startServer('<?php echo $server['server_id']; ?>');">Start server</button>
                                                </div>
                                                <div id="stopServer" <?php if (intval($server['server_status']) === Constants::$SERVER_STOPPED || intval($server['server_status']) === Constants::$SERVER_DISABLED) { ?>hidden <?php } ?>>
                                                    <button id="stopServer"  class="btn btn-default btn-block" onclick="stopServer('<?php echo $server['server_id']; ?>');">Stop server</button>
<?php if ((intval($server['can_see_rcon']) || $is_server_admin) && intval($server['server_status']) === Constants::$SERVER_STARTED) { ?>
                                                    <button id="rcon" class="btn btn-default btn-block" onclick="initRCONModal('rconModal');">Web RCON</button><?php } ?>
                                                </div>
    <?php
}
?>
                                        
                                            <?php
                                            if (intval($server['can_see_ftp']) === 1 || $is_server_admin) {
                                                ?>
                                                <button class="btn btn-default btn-block" onclick="location.href = 'webftp/?server_id=<?php echo $server['server_id']; ?>';">Web FTP</button>
<?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
            </section>

        </div>
        <div id="serverModal" role="dialog" aria-labelledby="serverModalTitle" aria-hidden="true" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" data-dismiss="modal" aria-label="Close" class="close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 id="serverModalTitle" class="modal-title">Edit server <?php echo $server['server_name']; ?></h4>
                    </div>
                    <div class="modal-body" id="serverModalBody">
                        <div class="panel janno-panel" id="formMsgPanel" hidden>
                            <div class="panel-heading" id="formTitle">

                            </div>
                            <div class="panel-body" id="formMsg">

                            </div>

                        </div>
                        <form id="serverForm" role="form" method="post" action="../../functions.php">
                            <input id='server_id' type="hidden" name="server_id" value="<?php echo $server['server_id']; ?>">
                            <input id='updateServer' type="hidden" name="updateServer" value="1">
                            <div class="form-group">
                                <label>Server name</label>
                                <input id='server_name' type="text" name="server_name" class="form-control" value="<?php echo $server['server_name']; ?>" required placeholder="Friendly name for the server">
                            </div>
                            <div class="form-group">
                                <label>Server port</label>
                                <input type="number" class="form-control" name="server_port" id="server_port" value="<?php echo $server['server_port']; ?>" placeholder="Port for the server" required>
                            </div>
                            <div class="form-group">
                                <label>Max clients</label>
                                <input type="number" class="form-control" name="max_players" id="max_players" value="<?php echo $server['max_players']; ?>" required placeholder="Maximum amount of players the server can have.">
                            </div>
                            <div class="form-group">
                                <label>RCONPassword</label>
                                <input type="text" class="form-control" name="rconpassword" id="rconpassword" value="<?php echo $server['rconpassword']; ?>" required placeholder="The RCON password for the server">
                            </div>
                            <div class="form-group">
                                <label>Startscript</label>
                                <textarea class="textarea form-control" rows="8" name="server_startscript" id="startscript" required><?php echo $server['server_startscript']; ?></textarea> 
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-default btn-block">Submit</button>
                                <small>Do note that this will restart the server.</small>
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
                <div id="ftppswreset" role="dialog" aria-labelledby="ftppswresetModalTitle" aria-hidden="true" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" data-dismiss="modal" aria-label="Close" class="close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 id="ftppswresetModalTitle" class="modal-title">Reset FTP Password</h4>
                    </div>
                    <div class="modal-body">
                        If you choose generate, it'll generate a random 8-digit password and change it itself. If you choose to type your own, it'll change the password to that.
                        <div class="clearfix">
                            <div class="pull-left">
                                <button type="button" class="btn btn-default btn-block" onclick="autoGenerateNewFTPPsw('<?php echo $_GET['server_id']; ?>', 'ftppswreset');">Generate</button>
                            </div>
                            <div class="pull-right">
                                <button type="button" class="btn btn-default btn-block" onclick="$('#ftppswchangeform').show(500);">Type your own new password</button>
                            </div>
                        </div>
                        <form id="ftppswchangeform" hidden method="post" action="../../functions.php" role="form">
                            <input type="hidden" name="server_id" value="<?php echo $_GET['server_id']; ?>">
                            <input type="hidden" name="resetFTPPassword" value="1">
                            <div class="form-group">
                                <label>New password</label>
                                <input type="text" name="newFTPPassword" required class="form-control" placeholder="Type in the new FTP password you wish to use">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-default btn-block">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div id="rconModal" role="dialog" aria-labelledby="rconModalTitle" aria-hidden="true" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" data-dismiss="modal" aria-label="Close" class="close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 id="rconModalTitle" class="modal-title">Web RCON utility</h4>
                    </div>
                    <div class="modal-body">
                        <pre id="console"></pre>
                        <input type="text" class="form-control" name="command" id="command" placeholder="Enter a command to the server">
                        <br>
                        <div class="clearfix">
                            <div class="pull-left">
                                <button type="button" class="btn btn-default" onclick="sendCommand(<?php echo $server['server_id']; ?>);">Send command</button>
                            </div>
                            <div class="pull-right">
                                <button type="button" class="btn btn-default" onclick="$('#console').html('');">Clear</button>
                            </div>
                            
                            
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>

<?php echo Constants::getJS($HOST_URL . "/static"); ?>
        <script>handleForm("ftppswchangeform", true);handleForm("serverForm", true);
        
        $('input').keyup(function(e) {
            if (e.keyCode === 13) {
                sendCommand(<?php echo $server['server_id']; ?>);
            }
        });
        </script>
    </body>
</html>
