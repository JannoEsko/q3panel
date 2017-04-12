<!DOCTYPE html>
<?php
if (!file_exists(__DIR__ . "/config.php")) {
    header("Location: install/");
}
session_start();
print_r($_SESSION);
require_once __DIR__ . "/classes/loader.php";
require_once __DIR__ . "/login.php";

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
        <button onclick="location.href='?logout';">Log out</button>
        <?php
        // put your code here
        ?>
    </body>
</html>
