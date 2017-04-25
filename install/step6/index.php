<!DOCTYPE html>
<?php
session_start();

if (!isset($_SESSION['installer'])) { 
    header("Location: ../../");
}
require_once __DIR__ . "/../../classes/loader.php";
session_destroy();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Installation | Q3Panel</title>
        <meta id="themecolor" name="theme-color" content="#23b7e5">
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
                            Step 6 - All done!
                        </div>
                        <div class="panel-body">
                            You're all done! You can proceed to the start page by clicking <a href="../../">here</a>
                            </div>
                            
                        </div>
                    </div>
            </section>
        </div>
        <?php echo Constants::getJS($HOST_URL . "/static"); ?>
        <script>handleForm("form");handleForm("form2");
            $(document).ready(function() {
                $("#form").hide(500);
            });
        $("#ext_users").select2({
            ajax: {
                method: "GET",
                url: "../../functions.php",
                dataType: "json",
                delay: 250,
                data: function(params) {
                    return {
                        getExternalUser: 1,
                        extUserName: params.term
                    };
                }, processResults: function(data) {
                    console.log(data);
                    return {
                        results: data
                    };
                },
                cache: true
            }, minimumInputLength: 1
        });
        </script>
    </body>
</html>
