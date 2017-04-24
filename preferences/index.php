
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
                            <div class="panel janno-panel">
                                <div class="panel-heading">
                                    Panel preferences
                                </div>
                                <div class="panel-body">
                                    <div class="col-md-6">
                                        <div class="panel janno-panel">
                                            <div class="panel-heading">
                                                External authentication
                                            </div>
                                            <div class="panel-body">
                                                <?php if (isExternalAuthEnabled($sql)) { ?>
                                                Currently, external authentication is enabled.
                                                <br>
                                                <?php
                                                $data = User::getExtData($sql);
                                                
                                                ?>
                                                <h5 class="h5 text-danger">Please note, that any kind of misconfiguration over here can (and will) lock out external user accounts (including yourself, if your account comes from an external system as well). Only way to fix it if nobody has a local account, is to edit it inside the SQL (table <b>q3panel_external_authentication</b>).<br><i>If it works, don't fix it.</i></h5>
                                                <br>
                                                
                                                <form id="extauth" method="post" action="../functions.php" role="form">
                                                    <div class="form-group">
                                                        <label>Host</label>
                                                        <input type="text" class="form-control" name="host" required value="<?php echo $data['host']; ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Database username</label>
                                                        <input type="text" class="form-control" name="db_username" required value="<?php echo $data['db_username']; ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Database password</label>
                                                        <input type="password" class="form-control" name="db_password" required placeholder="The password of the database">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Database name</label>
                                                        <input type="text" class="form-control" name="db_name" required value="<?php echo $data['db_name']; ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Users table name</label>
                                                        <input type="text" class="form-control" required name="users_table_name" value="<?php echo $data['users_table_name']; ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>User ID field</label>
                                                        <input type="text" class="form-control" required name="user_id_field" value="<?php echo $data['user_id_field']; ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Username field</label>
                                                        <input type="text" class="form-control" required name="username_field" value="<?php echo $data['username_field']; ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Password field</label>
                                                        <input type="text" class="form-control" required name="password_field" value="<?php echo $data['password_field']; ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>E-mail field</label>
                                                        <input type="text" class="form-control" required name="email_field" value="<?php echo $data['email_field']; ?>">
                                                    </div>
                                                    <input type="hidden" name="editExternalAuth" value="1">
                                                    <button type="submit" class="form-control btn btn-default btn-block">Submit</button>
                                                </form>
                                                <?php } else { ?>
                                                Currently, external authentication is disabled.
                                                <form id="extauth" method="post" action="../functions.php" role="form">
                                                    <div class="form-group">
                                                        <label>Host</label>
                                                        <input type="text" class="form-control" name="host" required >
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Database username</label>
                                                        <input type="text" class="form-control" name="db_username" required >
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Database password</label>
                                                        <input type="password" class="form-control" name="db_password" required placeholder="The password of the database">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Database name</label>
                                                        <input type="text" class="form-control" name="db_name" required >
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Users table name</label>
                                                        <input type="text" class="form-control" required name="users_table_name" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label>User ID field</label>
                                                        <input type="text" class="form-control" required name="user_id_field" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Username field</label>
                                                        <input type="text" class="form-control" required name="username_field" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Password field</label>
                                                        <input type="text" class="form-control" required name="password_field" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label>E-mail field</label>
                                                        <input type="text" class="form-control" required name="email_field" >
                                                    </div>
                                                    <input type="hidden" name="addExternalAuth" value="1">
                                                    <button type="submit" class="form-control btn btn-default btn-block">Submit</button>
                                                </form>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="panel janno-panel">
                                            <div class="panel-heading">
                                                E-mail service
                                            </div>
                                            <div class="panel-body">
                                                <?php
                                                $emailprefs = Email::getEmailPreferences($sql);
                                                $checked = intval($emailprefs['is_sendgrid']) === 1 ? "checked" : "";
                                                ?>
                                                <form method="post" action="../functions.php" id="emailservice" role="form">
                                                    <input type="hidden" name="update_email_service" value="1">
                                                    <div class="checkbox">
                                                        <input <?php echo $checked; ?> type="checkbox" id="is_sendgrid" name="is_sendgrid" class="styled"><label for="is_sendgrid">Is it SendGrid?</label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>From name</label>
                                                        <input class="form-control" type="text" name="from_name" value="<?php echo $emailprefs['from_name']; ?>" required placeholder="The name from which the e-mail will be sent form">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>From e-mail</label>
                                                        <input class="form-control" type="email" name="from_email" value="<?php echo $emailprefs['from_email']; ?>" required placeholder="The e-mail address from which the e-mail will appear to originate from">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>API Key</label>
                                                        <input class="form-control" type="text" name="api_key" value="<?php echo $emailprefs['api_key']; ?>" required placeholder="The e-mail address from which the e-mail will appear to originate from">
                                                    </div>
                                                    <button type="submit" class="btn btn-default btn-block">Submit</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            
                        
                    </div>
                </div>
            </section>

        </div>
        <?php echo Constants::getJS($HOST_URL . "/static"); ?>
        <script>handleForm("extauth", true);handleForm("emailservice", true);</script>
    </body>
</html>
