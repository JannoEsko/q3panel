
<?php
if (!file_exists(__DIR__ . "/../config.php")) {
    header("Location: ../install/");
}
session_start();
require_once __DIR__ . "/../classes/loader.php";
require_once __DIR__ . "/../login.php";

if (!User::canPerformAction($sql, $_SESSION['user_id'], Constants::$PANEL_ADMIN)) {
    header("Location: ../");
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta id="themecolor" name="theme-color" content="<?php echo $_SESSION['style_bg']; ?>">
        <title>Game setup | <?php echo Constants::$PANEL_NAME; ?></title>
        <?php echo Constants::getCSS($HOST_URL . "/static"); 
        echo Constants::getPreferencedCSS($HOST_URL . "/static", $_SESSION['style']);
        ?>
        
    </head>
    <body>
        <div class="wrapper">
            <?php require_once __DIR__ . "/../static/html/header_aside.php"; ?>
            <section>
                <div class="content-wrapper">
                    <div class="content-heading">
                        Game setup
                        <small>Here you can add different specifications regarding the games. The server-side setup must be done by you, you kind-of have to "teach" the panel to use the files.</small>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="panel janno-panel">
                                <div class="panel-heading">
                                    Game setup
                                </div>
                                <div class="panel-body">
                                    <div class="table-responsive table-bordered">
                                        <table class="table table-hover">
                                            <thead>
                                            <th>Game name</th>
                                            <th>Game location</th>
                                            <th>Game startscript</th>
                                            <th>Actions</th>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $games = Game::getGames($sql);
                                                foreach ($games as $game) {
                                                    ?>
                                                <tr>
                                                    <td><?php echo $game['game_name']; ?></td>
                                                    <td><?php echo $game['game_location']; ?></td>
                                                    <td><?php echo $game['startscript']; ?></td>
                                                    <td><button type="button" class="btn btn-default btn-block" onclick="initEditGameModal('gameModal', '<?php echo $game['game_id'];?>');">Edit game</button></td>
                                                </tr>
                                                <?php }
                                                
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <br>
                                    <button type="button" class="btn btn-default btn-block" onclick="$('#gameModalTitle').html('Add a new game');$('#addGame').val(1);$('#deleteGame').val(0);$('#updateGame').val(0);$('#gameModal').modal();">Add new game</button>
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
        <div id="gameModal" role="dialog" aria-labelledby="gameModalTitle" aria-hidden="true" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" data-dismiss="modal" aria-label="Close" class="close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 id="gameModalTitle" class="modal-title">Add a new game</h4>
                    </div>
                    <div class="modal-body" id="gameModalBody">
                         <div class="panel janno-panel" id="formMsgPanel" hidden>
                        <div class="panel-heading" id="formTitle">
                            
                        </div>
                        <div class="panel-body" id="formMsg">
                            
                        </div>
                        
                    </div>
                        <form id="gameForm" role="form" method="post" action="../functions.php">
                            <input id='addGame' type="hidden" name="addGame" value="1">
                            <input id='deleteGame' type="hidden" name="deleteGame" value="0">
                            <input id='gameId' type="hidden" name="gameId" value="0">
                            <input id='updateGame' type="hidden" name="updateGame" value="1">
                            <div class="form-group">
                                <label>Game name</label>
                                <input id='game_name' type="text" name="game_name" class="form-control" required placeholder="Friendly name for the game (will be used later on in the panel)">
                                
                            </div>
                            <div class="form-group">
                                <label>Game location</label>
                                <input id='game_location' type="text" name="game_location" class="form-control" required placeholder="Location of the game on the server">
                                <small>Small tip about this one. Best usage to this is to keep the required server files with this location, the actual game in a different one (can be handled within startscript by using <code>ln -s</code> before starting the game.</small>
                            </div>
                            <div class="form-group">
                                <label>Startscript</label>
                                <textarea id='startscript' class="textarea form-control" name="startscript" required></textarea>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-default btn-block">Submit</button>
                            </div>
                        </form>
                        
                        
                        </div>
                    <div class="modal-footer">
                        <div class="clearfix">
                            
                            <div class="pull-right">
                                <button id="deleteGame" type="button" class="btn btn-danger" onclick="$('#addGame').val(0);$('#deleteGame').val(1);$('#updateGame').val(0);$('#gameForm').submit();" hidden>Delete</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
                            </div>
                        </div>
                    </div>
                </div> </div>
            </div>
        
        <?php echo Constants::getJS($HOST_URL . "/static"); ?>
        <script>handleForm("gameForm");</script>
    </body>
</html>
