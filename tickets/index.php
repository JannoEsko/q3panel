
<?php
if (!file_exists(__DIR__ . "/../config.php")) {
    header("Location: ../install/");
}
session_start();
require_once __DIR__ . "/../classes/loader.php";
require_once __DIR__ . "/../login.php";

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
                                    Tickets
                                </div>
                                <div class="panel-body">
                                    <div class="panel janno-panel">
                                        <div class="panel-heading">
                                            Open tickets
                                        </div>
                                        <div class="panel-body">
                                            <div class="table table-bordered">
                                                <table class="table table-hover">
                                                    aa
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <button type="button" class="btn btn-default btn-block" onclick="$('#newTicket').modal();">Create a new ticket</button>
                                    <br>
                                    <div class="panel janno-panel">
                                        <div class="panel-heading">
                                            Closed tickets
                                        </div>
                                        <div class="panel-body">
                                            bb
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            
                        
                    </div>
                </div>
            </section>

        </div>
        <div id="newTicket" role="dialog" aria-labelledby="newTicketTitle" aria-hidden="true" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" data-dismiss="modal" aria-label="Close" class="close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 id="newTicketTitle" class="modal-title">Add a new ticket</h4>
                    </div>
                    <div class="modal-body">
                        <form id="ticketForm" role="form" method="post" action="../functions.php">
                            <input type="hidden" name="newTicket" value="1">
                            <div class="form-group">
                                <label>Title of the ticket</label>
                                <input class="form-control" type="text" name="title" required placeholder="The ticket title">
                            </div>
                            <div class="form-group">
                                <label>Message</label>
                                <textarea class="textarea form-control" rows="10" required name="message"></textarea>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-default btn-block">Submit</button>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div class="clearfix">
                            
                            <div class="pull-right">
                                <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
            
        </div>
        <div id="ticketModal" role="dialog" aria-labelledby="ticketModalTitle" aria-hidden="true" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" data-dismiss="modal" aria-label="Close" class="close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 id="ticketModalTitle" class="modal-title" id="ticketTitle"></h4>
                    </div>
                    <div class="modal-body">
                        
                    </div>
                    <div class="modal-footer">
                        <div class="clearfix">
                            
                            <div class="pull-right">
                                <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
            
        </div>
        <?php echo Constants::getJS($HOST_URL . "/static"); ?>
        <script>handleForm("ticketForm", true);</script>
    </body>
</html>
