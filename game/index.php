
<?php
if (!file_exists(__DIR__ . "/../config.php")) {
    header("Location: ../install/");
}
session_start();
require_once __DIR__ . "/../classes/loader.php";
require_once __DIR__ . "/../login.php";
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
            <?php require_once __DIR__ . "/../static/html/header_aside.php"; ?>
            <section>
                <div class="content-wrapper">
                    <div class="content-heading">
                        Homepage
                        <small>Welcome to Q3Panel</small>
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
                                                </tr>
                                                <?php }
                                                
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <br>
                                    <button type="button" class="btn btn-default btn-block" onclick="$('#gameModal').modal();">Add new game</button>
                                </div>
                            </div>
                            
                            <div class="panel janno-panel">
                                <div class="panel-heading">
                                    Required files
                                </div>
                                <div class="panel-body"><?php echo nl2br(print_r(get_required_files(), true)); ?></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="panel janno-panel">
                                <div class="panel-heading">
                                    <div class="panel-title">Latest news</div>
                                </div>
                                <div class="panel-body list-group">
                                    <div class="list-group-item">
                                        <div class="media-box">
                                            <div class="pull-left">
                                                <span class="fa-stack">

                                                    <em class="fa text-success fa-check fa-stack-2x"></em>
                                                </span>
                                            </div>
                                            <div class="media-box-body clearfix">
                                                <div class="media-box-heading text-success m0">some logs</div>
                                                <p class="m0">
                                                    <small>xxx<br>xxxxx</small>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="panel-body list-group">
                                    <div class="list-group-item">
                                        <div class="media-box">
                                            <div class="pull-left">
                                                <span class="fa-stack">

                                                    <em class="fa text-success fa-check fa-stack-2x"></em>
                                                </span>
                                            </div>
                                            <div class="media-box-body clearfix">
                                                <div class="media-box-heading text-success m0">some other stuff</div>
                                                <p class="m0">
                                                    <small>text<br>text</small>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

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
                            <input type="hidden" name="addGame" value="1">
                            <div class="form-group">
                                <label>Game name</label>
                                <input type="text" name="game_name" class="form-control" required placeholder="Friendly name for the game (will be used later on in the panel)">
                                
                            </div>
                            <div class="form-group">
                                <label>Game location</label>
                                <input type="text" name="game_location" class="form-control" required placeholder="Location of the game on the server">
                                <small>Small tip about this one. Best usage to this is to keep the required server files with this location, the actual game in a different one (can be handled within startscript by using <code>ln -s</code> before starting the game.</small>
                            </div>
                            <div class="form-group">
                                <label>Startscript</label>
                                <textarea class="textarea form-control" name="startscript" required></textarea>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-default btn-block">Submit</button>
                            </div>
                        </form>
                        
                        
                        </div>
                    <div class="modal-footer">
                        <div class="clearfix">
                            <div id="save" class="pull-left">
                                <button type="button" class="btn btn-success" onclick="$('#gameForm').submit();">Save</button>
                            </div>
                            <div class="pull-right">
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
