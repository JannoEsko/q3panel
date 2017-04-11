<!DOCTYPE html>
<?php
error_reporting(E_ALL);
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
                            Step 3 - 
                        </div>
                        <div class="panel-body">
                            <form role="form" method="post" id="form" action="../../functions.php">
                                
                                <button type="submit" class="btn btn-block btn-default">Submit</button>
                                
                            </form>
                            
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php echo Constants::getJS($HOST_URL . "/static"); ?>
    </body>
</html>
