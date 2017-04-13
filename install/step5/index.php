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
                            Step 5 - Creating an account
                        </div>
                        <div class="panel-body">
                            As the final step, you have to set up an account for yourself. Your account will have access to everything. If you set up the external authentication in step 3, you can use that to get an account here for yourself, if not, then just choose normal authentication.<br><br>External authentication will use the password, which is set in the external service, the panel will not handle the password for those accounts nor let those password be reset. For normal authentication, the account management stays on this page.
                            <br>
                                <div class="pull-left">
                                    <button type="button" class="btn btn-default btn-block btn-lg" onclick="$('#form').show(500);$('#form2').hide(500);" <?php if (!isExternalAuthEnabled($sql)) {?> disabled <?php } ?>>Use external authentication</button>
                                </div>
                                <div class="pull-right">
                                    <button type="button" class="btn  btn-block btn-default btn-lg" onclick="$('#form2').show(500);$('#form').hide(500);">Use normal authentication</button>
                                </div>
                                <br><br>
                                <form role="form" method="post" id="form" action="../../functions.php">
                                    <input type="hidden" name="extAccount" value="1">
                                    <input type="hidden" name="extUserGroup" value="3">
                                <div class="form-group">
                                    <label>Name of the account</label>
                                    <select id="ext_users" class="form-control" name="extUser">
                                        <option>Search for users in the external database...</option>
                                    </select>
                                </div>
                                
                                <button type="submit" class="btn btn-block btn-default">Submit</button>
                                
                            </form>
                                <form role="form" method="post" id="form2" action="../../functions.php" hidden>
                                    <input type="hidden" name="userGroup" value="3">
                                    <input type="hidden" name="register" value="1">
                                <div class="form-group">
                                    <label>Username</label>
                                    <input name="username" type="text" placeholder="Your desired username" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input name="password" type="password" placeholder="Your desired password" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>E-mail</label>
                                    <input name="email" type="email" placeholder="Your e-mail address" class="form-control" required>
                                </div>
                                
                                <button type="submit" class="btn btn-block btn-default">Submit</button>
                                
                            </form>
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
