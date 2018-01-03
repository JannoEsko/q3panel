<?php
session_start();
//Install script cannot require loader.php because there's no config file yet.
require_once __DIR__ . "/../../functions.php";
require_once __DIR__ . "/../../classes/Constants.php";
if (file_exists(__DIR__ . "/../../config.php")) {
    die("config.php exists, remove the file if you want to proceed.");
}



?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Installation | Q3Panel</title>
        <meta id="themecolor" name="theme-color" content="#23b7e5">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <?php echo Constants::getCSS("../../static"); ?>
        <link rel="stylesheet" href="../../static/css/theme-a.css" />
    </head>
    <body>
        <div class="container">
            <br>
            <br>
            <br>
            <br>
            <section>
                <div class="row">
                    <div class="panel janno-panel" id="formMsgPanel" hidden>
                        <div class="panel-heading" id="formTitle">
                            
                        </div>
                        <div class="panel-body" id="formMsg">
                            
                        </div>
                        
                    </div>
                    <div class="panel panel-primary janno-panel">
                        <div class="panel-heading">
                            Step 2 - Connection to the SQL server and the URL of the panel
                        </div>
                        <div class="panel-body">
                            <form role="form" method="post" id="form" action="../../functions.php">
                                <div class="form-group">
                                    <label>SQL Server host</label>
                                    <input name="host" type="text" placeholder="Enter the host of the SQL server" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>SQL Server username</label>
                                    <input name="username" type="text" placeholder="Enter the username of the SQL server account" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input id="password" name="password" type="password" placeholder="The password of the SQL server account" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>SQL Database</label>
                                    <input name="db" type="text" placeholder="Enter the database, where your tables will be" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>URL of the panel</label>
                                    <input name="url" type="url" placeholder="Enter the URL of your site" class="form-control" required>
                                
                                    
                                </div>
                                <button type="submit" class="btn btn-block btn-default">Submit</button>
                                
                            </form>
                            
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php echo Constants::getJS("../../static"); ?>
        <script>handleForm("form");</script>
    </body>
</html>
