
<?php
if (!file_exists(__DIR__ . "/../../config.php")) {
    header("Location: install/");
}
session_start();
require_once __DIR__ . "/../../classes/loader.php";
require_once __DIR__ . "/../../login.php";
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta id="themecolor" name="theme-color" content="<?php echo $_SESSION['style_bg']; ?>">
        <title>Server logs | <?php echo Constants::$PANEL_NAME; ?></title>
        <?php echo Constants::getCSS($HOST_URL . "/static"); 
        echo Constants::getPreferencedCSS($HOST_URL . "/static", $_SESSION['style']);
        ?>
        
    </head>
    <body>
        <div class="wrapper">
            <?php require_once __DIR__ . "/../../static/html/header_aside.php"; ?>
            <section>
                <div class="content-wrapper">
                    <div class="content-heading">
                        Server logs
                        <small>Here you see all the logs regarding the server.</small>
                    </div>
                    <div class="row">
                            <div class="panel janno-panel">
                                <div class="panel-heading">
                                    Server logs
                                </div>
                                <div class="panel-body">
                                    <div class="table-responsive table-bordered">
                                        <table id="serverLogs" class="table table-hover">
                                            <thead>
                                            <th>Username</th>
                                            <th>IP</th>
                                            <th>Server</th>
                                            <th>Action</th>
                                            <th>Timestamp</th>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            
                        </div>
                        
                </div>
            </section>

        </div>
        <?php echo Constants::getJS($HOST_URL . "/static"); ?>
        <script>
            $(document).ready(function() {
                $('#serverLogs').DataTable({
                    "order": [[4, "desc"]],

                    ajax: '../../functions.php?getServerLogs=1',
                    "columns": [
                        {"data": "realName"},
                        {"data": "user_ip"},
                        {"data": "server_name"},
                        {"data": "action", "orderable": false},
                        {"data": "timestamp"}
                    ]
                });
            });
        
        </script>
    </body>
</html>
