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
                            Step 4 - E-mailing
                        </div>
                        <div class="panel-body">
                            This panel allows you to choose between 2 e-mail classes, one is PHPMailer library, which relies heavily on the php's original <code>mail()</code>
                             function, the other is SendGrid, which is a free-to-use service (as long as you don't go over the quotas), which just deals with the e-mailing side on itself. Benefits of using SendGrid is that PHPMailer's e-mails might end up in junk (SPF, rDNS issues etc), while SendGrid's e-mails are more often trusted. 
                             If you opt for PHPMailer, please check that you got sendmail installed (on Debian and its derivates, <code>sudo apt-get install sendmail</code>).
                           <br>
                                <div class="pull-left">
                                    <button type="button" class="btn btn-default btn-block btn-lg" onclick="$('#form').show(500);$('#form2').hide(500);">Use SendGrid</button>
                                </div>
                                <div class="pull-right">
                                    <button type="button" class="btn  btn-block btn-default btn-lg" onclick="$('#form2').show(500);$('#form').hide(500);">Use PHPMailer</button>
                                </div>
                                <br><br>
                                <form role="form" method="post" id="form" action="../../functions.php" hidden>
                                    <input type="hidden" name="isSendgrid" value="1">
                                <div class="form-group">
                                    <label>Name of the e-mail user (e.g. q3panel)</label>
                                    <input name="fromName" type="text" placeholder="Name of the e-mail user" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>E-mail address</label>
                                    <input name="fromEmail" type="email" placeholder="Enter the e-mail address from which the e-mails will be sent" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>API Key</label>
                                    <input id="api" name="api" type="text" placeholder="SendGrid's API key" class="form-control" required>
                                </div>
                                
                                <button type="submit" class="btn btn-block btn-default">Submit</button>
                                
                            </form>
                                <form role="form" method="post" id="form2" action="../../functions.php" hidden>
                                    <input type="hidden" name="isSendgrid" value="0">
                                <div class="form-group">
                                    <label>Name of the e-mail user (e.g. q3panel)</label>
                                    <input name="fromName" type="text" placeholder="Name of the e-mail user" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>E-mail address</label>
                                    <input name="fromEmail" type="email" placeholder="Enter the e-mail address from which the e-mails will be sent" class="form-control" required>
                                </div>
                                
                                <button type="submit" class="btn btn-block btn-default">Submit</button>
                                
                            </form>
                            </div>
                            
                        </div>
                    </div>
            </section>
        </div>
        <?php echo Constants::getJS($HOST_URL . "/static"); ?>
        <script>handleForm("form");handleForm("form2");</script>
    </body>
</html>
