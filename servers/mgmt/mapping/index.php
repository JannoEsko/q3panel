
<?php
if (!file_exists(__DIR__ . "/../../../config.php")) {
    header("Location: ../../../install/");
}
session_start();
require_once __DIR__ . "/../../../classes/loader.php";
require_once __DIR__ . "/../../../login.php";
if (!isset($_GET['server_id']) || intval($_GET['server_id']) === 0) {
    header("Location: ../");
}
if (!User::canPerformAction($sql, $_SESSION['user_id'], Constants::$SERVER_ADMIN)) {
    header("Location: ../");
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta id="themecolor" name="theme-color" content="<?php echo $_SESSION['style_bg']; ?>">
        <title>Map users to server | <?php echo Constants::$PANEL_NAME; ?></title>
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
                        Map users to server
                        <small>Here you can map users to the server (who can see the server and who can do what). When a server admin is mapped, he can access everything on the server. Panel admins are automatically mapped to the server.</small>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="panel janno-panel">
                                <div class="panel-heading">
                                    Server and User mapping and permissions
                                </div>
                                <div class="panel-body">
                                    Users who are mapped, can see the server. There are 3 specific options what to show the user. Stopping server option means that the user can start/stop the server. Seeing RCON means that the user sees the RCON of the server. Using FTP means that the user sees the FTP login and password and can also use the Web FTP interface for the server. Panel admins are automatically mapped to the servers with the default options. After removing the panel admin group from the server, the permissions <b class="text-danger">will stay</b> for the user, so you have to remove the mappings / edit the mappings from those users.
                                    <br><br><div class="table-responsive table-bordered">
                                        <table class="table table-hover" id="mapTable">
                                            <thead>
                                            <th>Username</th>
                                            <th>Stopping server</th>
                                            <th>Seeing RCON</th>
                                            <th>Using FTP</th>
                                            <th>Actions</th>
                                            </thead>
                                            <tbody>
                                    <?php
                                    $serverMapping = Server::getServersWithUserMapping($sql, $_GET['server_id']);
                                    foreach($serverMapping as $serverMap) {
                                        ?><tr id="tr<?php echo $serverMap['user_id']; ?>"><?php
                                        if (intval($serverMap['origin']) === Constants::$EXTERNAL_ACCOUNT) {
                                            $extQry = User::getExternalAccount($sql, $serverMap['username'], true);
                                            $tableSpec = $extQry['extTable_spec'];
                                            $extData = $extQry['data'];
                                            if (sizeof($extData) === 1) {
                                                $extData = $extData[0];
                                            }
                                            ?><td><?php echo $extData[$tableSpec['username_field']]; ?></td>
                                            <td id="tdcss<?php echo $serverMap['user_id']; ?>"><?php echo intbool2str($serverMap['can_stop_server']); ?></td>
                                            <td id="tdcsr<?php echo $serverMap['user_id']; ?>"><?php echo intbool2str($serverMap['can_see_rcon']); ?></td>
                                            <td id="tdcsf<?php echo $serverMap['user_id']; ?>"><?php echo intbool2str($serverMap['can_see_ftp']); ?></td>
                                            <td><?php if (intval($serverMap['group_id']) !== Constants::$PANEL_ADMIN) { ?><button type="button" class="btn btn-block btn-default" onclick="editServerMapping('serverMap', '<?php echo $serverMap['user_id']; ?>', '<?php echo $extData[$tableSpec['username_field']]; ?>', <?php echo $serverMap['can_stop_server'] . ", " . $serverMap['can_see_rcon'] . ", " . $serverMap['can_see_ftp']; ?>);">Edit</button><button type="button" class="btn btn-block btn-default" onclick="$('#removeMapUser').val('<?php echo $serverMap['user_id']; ?>');$('#submitRemoveMap').click();">Remove</button><?php } ?></td>
                                        <?php } else { ?>
                                            <td ><?php echo $serverMap['username']; ?></td>
                                            <td id="tdcss<?php echo $serverMap['user_id']; ?>"><?php echo intbool2str($serverMap['can_stop_server']); ?></td>
                                            <td id="tdcsr<?php echo $serverMap['user_id']; ?>"><?php echo intbool2str($serverMap['can_see_rcon']); ?></td>
                                            <td id="tdcsf<?php echo $serverMap['user_id']; ?>"><?php echo intbool2str($serverMap['can_see_ftp']); ?></td>
                                            <td><?php if (intval($serverMap['group_id']) !== Constants::$PANEL_ADMIN) { ?><button type="button" class="btn btn-block btn-default" onclick="editServerMapping('serverMap', '<?php echo $serverMap['user_id']; ?>', '<?php echo $serverMap['username']; ?>', <?php echo $serverMap['can_stop_server'] . ", " . $serverMap['can_see_rcon'] . ", " . $serverMap['can_see_ftp']; ?>);">Edit</button><button type="button" class="btn btn-block btn-default" onclick="$('#removeMapUser').val('<?php echo $serverMap['user_id']; ?>');$('#submitRemoveMap').click();">Remove</button><?php } ?></td>
                                        <?php }
                                        ?></tr><?php 
                                    }
                                    
                                    ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <small>The edit button appears only for accounts, which aren't in the Panel Admin group (Panel Admin can access all servers, all hosts).</small>
                                    <br><br>
                                    <button class="btn btn-block btn-default" onclick="$('#editMap').val(0);$('#addMap').val(1);$('#addUserId').prop('disabled', false);$('#editMapUserId').prop('disabled', true);$('#addMapSelect').show();$('#serverMapTitle').html('Map new user');$('#serverMap').modal();">Add new user to the map</button>
                                </div>
                            </div>
                            
                           
                        </div>
                        <div class="col-md-4">
                            <div class="panel janno-panel">
                                <div class="panel-heading">
                                    Information
                                </div>
                                <div class="panel-body">
                                    <?php
                                    $dat = getStats($sql);
                                    ?>
                                    The panel hosts a total of <?php echo $dat['totalServers']; ?> server(s), from which, <?php echo $dat['runningServers']; ?> is/are currently running. <br>It has <?php echo $dat['totalUsers']; ?> user accounts, out of which <?php echo $dat['extUsers']; ?> of them derive from an external system and <?php echo $dat['localUsers']; ?> of them is registered locally.<br><?php echo $dat['extAuth']; ?><br><?php echo $dat['mailer']; ?>
                                    <br>
                                    <br>
                                    Did you know that by clicking <b><em class="icon-wrench"></em>Themes</b> from the upper bar, you can change the color theme of the panel?
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
        <div id="serverMap" role="dialog" aria-labelledby="serverMapTitle" aria-hidden="true" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" data-dismiss="modal" aria-label="Close" class="close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 id="serverMapTitle" class="modal-title"></h4>
                    </div>
                    <div class="modal-body">
                        <form id="mapForm" role="form" method="post" action="../../../functions.php">
                            <input type="hidden" name="server_id" value="<?php echo $_GET['server_id']; ?>">
                            <input type="hidden" name="editMap" value="0" id="editMap">
                            <input type="hidden" name="addMap" value="0" id="addMap">
                            <input type="hidden" name="user_id" id="editMapUserId" value="0" disabled>
                            <div id="addMapSelect" hidden class="form-group">
                                <select disabled id="addUserId" name="user_id" class="form-control">
                                    <?php
                                    $dat = User::getAllUsers($sql);
                                    foreach ($dat as $user) {
                                        echo "<option value=\"" . $user['user_id'] . "\">" . $user['realName'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group checkbox">
                                <input class="styled" type="checkbox" id="can_stop_server" name="can_stop_server"><label for="can_stop_server">Stopping server</label>
                            </div>
                            <div class="form-group checkbox">
                                <input class="styled" type="checkbox" id="can_see_rcon" name="can_see_rcon"><label for="can_see_rcon">Seeing RCON</label>
                            </div>
                            <div class="form-group checkbox">
                                <input class="styled" type="checkbox" id="can_see_ftp" name="can_see_ftp"><label for="can_see_ftp">Using FTP</label>
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
        <form id="removeMapping" hidden method="post" action="../../../functions.php">
            <input type="hidden" name="server_id" value="<?php echo $_GET['server_id']; ?>">
            <input type="hidden" name="deleteMap" value="1">
            <input type="hidden" id="removeMapUser" name="removeMapUser">
            <button id="submitRemoveMap" type="submit" hidden></button>
        </form>
        <?php echo Constants::getJS($HOST_URL . "/static"); ?>
        <script>handleForm("removeMapping", true);handleForm("mapForm", true);</script>
    </body>
</html>
