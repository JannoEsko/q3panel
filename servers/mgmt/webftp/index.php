
<?php
if (!file_exists(__DIR__ . "/../../../config.php")) {
    header("Location: ../../../install/");
}
session_start();
require_once __DIR__ . "/../../../classes/loader.php";
require_once __DIR__ . "/../../../login.php";
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
                                        <table class="table table-hover">
                                            <?php
                                            $ftp = ftp_connect("127.0.0.1");
                                            ftp_login($ftp, "srv101", "loll");
                                            $contents = ftp_nlist($ftp, ".");
                                            foreach ($contents as $content) {
                                                ?><tr><td><em class="fa <?php if (is_dir($content)) {?>fa-folder<?php } else { ?> fa-file<?php } ?>"></em> <a href="<?php echo $content; ?>"><?php echo $content; ?></td></tr>
                                           <?php }
                                            ?>
                                        </table>
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
        <div id="serverModal" role="dialog" aria-labelledby="serverModalTitle" aria-hidden="true" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" data-dismiss="modal" aria-label="Close" class="close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 id="serverModalTitle" class="modal-title">Add a new game</h4>
                    </div>
                    <div class="modal-body" id="serverModalBody">
                         <div class="panel janno-panel" id="formMsgPanel" hidden>
                        <div class="panel-heading" id="formTitle">
                            
                        </div>
                        <div class="panel-body" id="formMsg">
                            
                        </div>
                        
                    </div>
                        <form id="serverForm" role="form" method="post" action="../functions.php">
                            <input id='addServer' type="hidden" name="addServer" value="1">
                            <input id='deleteServer' type="hidden" name="deleteServer" value="0">
                            <input id='server_id' type="hidden" name="server_id" value="0">
                            <input id='updateServer' type="hidden" name="updateServer" value="1">
                            <div class="form-group">
                                <label>Host server</label>
                                <select class="form-control" name="host_id" required>
                                    <?php echo Host::getHostsSelect($sql); ?>
                                </select>
                                <small>If it's empty, click <a href="<?php echo "$HOST_URL/hosts/"; ?>">here</a> to add it.</small>
                            </div>
                            <div class="form-group">
                                <label>Game</label>
                                <select class="form-control" name="game_id" required>
                                    <?php echo Game::getGamesSelect($sql); ?>
                                </select>
                                <small>If it's empty, click <a href="<?php echo "$HOST_URL/hosts/"; ?>">here</a> to add it.</small>
                            </div>
                            <div class="form-group">
                                <label>Server name</label>
                                <input id='server_name' type="text" name="server_name" class="form-control" required placeholder="Friendly name for the server">
                            </div>
                            <div class="form-group">
                                <label>Server port</label>
                                <input type="number" class="form-control" name="server_port" id="server_port" placeholder="Port for the server">
                                <small>This is not a requirement. If specified, the server will use that port. If not, then if there are no servers deployed, it will default to port 20100, otherwise it will pick the server with the largest port value and increment that.</small>
                            </div>
                            <div class="form-group">
                                <label>FTP Account</label>
                                <input type="name" class="form-control" name="server_account" id="server_account" placeholder="Server account (for FTP usage)">
                                <small>This value is not required. If set, the server will use the account specified. If not, then if there are no servers deployed, it will default it to srv1, otherwise it'll pick the server with the largest srv ID and increment that.<br>NB! The panel will create the account itself, so no such account can exist on the panel.</small>
                            </div>
                            <div class="form-group">
                                <label>FTP Password</label>
                                <input type="password" class="form-control" name="server_password" id="server_password" placeholder="Server password (for FTP usage)">
                                <small>Not required, it'll automatically generate a 8-character password for the account if not entered.</small>
                            </div>
                            <div class="form-group">
                                <label>Max clients</label>
                                <input type="number" class="form-control" name="max_players" id="max_players" required placeholder="Maximum amount of players the server can have.">
                            </div>
                            <div class="form-group">
                                <label>RCONPassword</label>
                                <input type="text" class="form-control" name="rconpassword" id="rconpassword" required placeholder="The RCON password for the server">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-default btn-block">Submit</button>
                            </div>
                        </form>
                        
                        
                        </div>
                    <div class="modal-footer">
                        <div class="clearfix">
                            
                            <div class="pull-right">
                                <div id="deleteGameBtn" hidden class="pull-left">
                                <button  type="button" class="btn btn-danger" onclick="$('#addHost').val(0);$('#deleteHost').val(1);$('#updateHost').val(0);$('#hostForm').submit();" >Delete</button>
                                </div>&nbsp;<button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
                            </div>
                        </div>
                    </div>
                </div> </div>
            </div>
        
        <?php echo Constants::getJS($HOST_URL . "/static"); ?>
        <script></script>
    </body>
</html>
