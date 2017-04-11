<!DOCTYPE html>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
//Install script cannot require loader.php because there's no config file yet.
require_once __DIR__ . "/../functions.php";
require_once __DIR__ . "/../classes/Constants.php";
if (isset($_GET['writeSQL'])) {
    require_once __DIR__ . "/../classes/writer/Writer.php";
    $wrt = new Writer(__DIR__ . "/../config.php");
    $wrt->write("<?php\n\n\$sql = new SQL();\n\n$page_url = \"\";");
}

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Installation | Q3Panel</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <?php echo Constants::getCSS("../static"); ?>
        <link rel="stylesheet" href="../static/css/theme-a.css" />
    </head>
    <body>
        <div class="container">
        <?php
        $ftp = extension_loaded("ftp");
        $pdo = extension_loaded("pdo_mysql");
        $ssh2 = extension_loaded("ssh2");
        $curl = extension_loaded("curl");
        ?>
        <br>
        <br>
        <br>
        <br>
        <section>
            <div class="row">
                <div class="panel panel-primary janno-panel">
                    <div class="panel-heading">
                        Welcome to Q3Panel!
                    </div>
                    <div class="panel-body">
                        <p>Q3Panel is a web application, which can be used to host Quake 3 engine-based games on Linux servers.</p>
                        <p>Whole idea about this panel is that it's open-source, easy to use and doesn't require you to be a Linux guru.</p>
                        <p></p>
                        <p class="text-bold">Dependencies</p>
                        <?php
if (!$ftp) {
?>
    <p class="text-danger">FTP extension not found.</p><?php
} else {
?>
    <p class="text-success">FTP extension found.</p><?php
}
if (!$pdo) {
?>
    <p class="text-danger">PDO_MySQL extension not found.</p><?php
} else {
?>
    <p class="text-success">PDO_MySQL extension found.</p><?php
}
if (!$ssh2) {
?>
    <p class="text-danger">SSH2 extension not found.</p><?php
} else {
?>
    <p class="text-success">SSH2 extension found.</p><?php
}
if (!$curl) {
?>
    <p class="text-danger">CURL not found. You cannot use Sendgrid API without CURL, but you can proceed.</p><?php
} else {
?>
    <p class="text-success">CURL extension found</p><?php
}
if ($ftp && $pdo && $ssh2) {
?>
    
    
    <p></p>
    <button class="btn btn-primary btn-block" onclick="location.href='step2/'">Continue</button>
<?php } else {
    ?>
    <p>You can search how to install the packages depending on your Linux distribution. </p>
    <p>Installing on Debian and its derivates using apt:</p>
    <code>sudo apt-get install php7.0-mysql php7.0-ssh2 php7.0-curl</code>
    
    <?php
}
?>
                    </div>
                </div>
            </div>
            
        </section>
            
        </div>    
            
        <?php echo Constants::getJS("../static"); ?>
    </body>
</html>
