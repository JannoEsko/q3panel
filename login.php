<!DOCTYPE html>
<?php
session_start();

if (!isset($_SESSION['group_id'], $_SESSION['user_id'], $_SESSION['username'])) {
    

?>
 
<html>
    <head>
        <meta charset="UTF-8">
        <title>Login | Q3Panel</title>
        <?php
        echo Constants::getCSS("$HOST_URL/static");
        ?>
    </head>
    <body>
        <div class="wrapper">
        <div class="block-center mt-xl wd-xl">
                    <div class="panel janno-panel" id="formMsgPanel" hidden>
                        <div class="panel-heading" id="formTitle">
                            
                        </div>
                        <div class="panel-body" id="formMsg">
                            
                        </div>
                        
                    </div>
                    <div class="panel panel-primary">
                        <div class="panel-heading text-center">
                            <a href="#" style="color:white;">Q3Panel</a>
                        </div>
                        <div class="panel-body">
                            
                            <p class="text-center pv">Please log in to the panel</p>
                            <form id="form" role="form" class="mb-lg" method="post" action="<?php echo $HOST_URL; ?>/functions.php">
                                <input type="hidden" name="login" value="1">
                                <div class="form-group has-feedback">
                                    <input type="name" placeholder="Enter your account" required class="form-control" name="username">
                                    <span class="fa fa-user form-control-feedback text-muted"></span>
                                </div>
                                <div class="form-group has-feedback">
                                    <input type="password" placeholder="Password" required class="form-control" name="password">
                                    <span class="fa fa-lock form-control-feedback text-muted"></span>
                                </div>

                                <button type="submit" class="btn btn-block btn-primary mt-lg">Login</button>
                                <div class="clearfix">
                                    <div class="text-center pv">
                                        <a href="#" class="text-muted">Forgot your password?</a>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                    <!-- END panel-->
                    <div class="p-lg text-center">
                        <span><a href="http://github.com/JannoEsko/q3panel">GitHub Repository</a></span>

                    </div>
                </div>
        </div>
        <?php
        
        echo Constants::getJS("$HOST_URL/static");
        
        ?>
        <script>handleForm("form");</script>
    </body>
</html>
<?php 


die(); //no need to show index if we're not logged in.
}