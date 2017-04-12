<!DOCTYPE html>
<?php
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['installer'])) { 
    header("Location: ../../");
}
require_once __DIR__ . "/../../classes/loader.php";

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Installation | Q3Panel</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <?php echo Constants::getCSS($HOST_URL . "/static"); ?>
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
                            Step 3 - External authentication
                        </div>
                        <div class="panel-body">
                            Do you wish to set up an external authentication service? External authentication service works for newer PHP-based software solutions, which use <code>password_hash()</code> function to hash the passwords. By using external authentication, you can link accounts from a different database to this software in such a way that the password, username and e-mail account is taken from the external database, while the panel's database holds only the group of the user.
                                <br>
                                <div class="pull-left">
                                    <button type="button" class="btn btn-default btn-block btn-lg" onclick="$('#form').show(500);">Yes</button>
                                </div>
                                <div class="pull-right">
                                    <button type="button" class="btn  btn-block btn-default btn-lg" onclick="location.href='../step4/';">No</button>
                                </div>
                                <br><br>
                                <form role="form" method="post" id="form" action="../../functions.php" hidden>
                                <div class="form-group">
                                    <label>SQL Server host</label>
                                    <input name="exthost" type="text" placeholder="Enter the host of the SQL server" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>SQL Server username</label>
                                    <input name="extusername" type="text" placeholder="Enter the username of the SQL server account" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input id="extpassword" name="password" type="password" placeholder="The password of the SQL server account" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>SQL Database</label>
                                    <input name="extdb" type="text" placeholder="Enter the database, where the external program runs" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Users table name</label>
                                    <input name="usrtable" type="text" placeholder="Enter the users table name of your software" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Users table ID field name</label>
                                    <input name="usrtableid" type="text" placeholder="Enter the users table ID field name of your external software" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Username field name</label>
                                    <input name="usrtablename" type="text" placeholder="Enter the username field of your external software" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Password field name</label>
                                    <input name="usrtablepsw" type="text" placeholder="Enter the password field of your external software" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>E-mail field</label>
                                    <input name="usrtableemail" type="text" placeholder="Enter the e-mail field of your external software" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-block btn-default">Submit</button>
                                
                            </form>
                            </div>
                            
                        </div>
                    </div>
            </section>
        </div>
        <?php echo Constants::getJS($HOST_URL . "/static"); ?>
        <script>handleForm("form");</script>
    </body>
</html>
