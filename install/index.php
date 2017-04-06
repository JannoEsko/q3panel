<!DOCTYPE html>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
//Install script cannot require loader.php because there's no config file yet.
require_once __DIR__ . "/../functions.php";
if (isset($_GET['writeSQL'])) {
    require_once __DIR__ . "/../classes/writer/Writer.php";
    $wrt = new Writer(__DIR__ . "/../config.php");
    $wrt->write("<?php\n\n\$sql = new SQL();\n\n$page_url = \"\";");
}

?>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        // put your code here
        ?>
    </body>
</html>
