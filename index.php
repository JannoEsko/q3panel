
<?php
if (!file_exists(__DIR__ . "/config.php")) {
    header("Location: install/");
}
session_start();
require_once __DIR__ . "/classes/loader.php";
require_once __DIR__ . "/login.php";
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta id="themecolor" name="theme-color" content="<?php echo $_SESSION['style_bg']; ?>">
        <title>Homepage | <?php echo Constants::$PANEL_NAME; ?></title>
        <?php echo Constants::getCSS($HOST_URL . "/static"); 
        echo Constants::getPreferencedCSS($HOST_URL . "/static", $_SESSION['style']);
        ?>
        
    </head>
    <body>
        <div class="wrapper">
            <?php require_once __DIR__ . "/static/html/header_aside.php"; ?>
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
                                    Q3Panel
                                </div>
                                <div class="panel-body">
                                   Hello, <?php echo $_SESSION['username']; ?>. Please choose the action from the left sidebar<br>If you're on a handheld device, you can toggle navigation with the <em class="fa fa-navicon"></em> button.
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
        <?php echo Constants::getJS($HOST_URL . "/static"); ?>
    </body>
</html>
