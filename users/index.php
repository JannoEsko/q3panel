
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
                        <div class="col-md-8">
                            <div class="panel janno-panel">
                                <div class="panel-heading">
                                    Users
                                </div>
                                <div class="panel-body">
                                    <div class="table-responsive table-bordered">
                                        <table class="table table-hover">
                                            <thead>
                                            <th>Username</th><th>E-mail</th><th>Origin</th><th>Group</th><th>Action</th>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $users = User::getAllUsers($sql);
                                                print_r($users);
                                                foreach($users as $user) {
                                                    ?>
                                                <tr>
                                                    <td><?php echo $user['realName']; ?></td>
                                                    <td><?php echo $user['email']; ?></td>
                                                    <td><?php echo Constants::$MESSAGES['ORIGIN'][$user['origin']]; ?></td>
                                                    <td><?php echo Constants::$MESSAGES['GROUP'][$user['group_id']]; ?></td>
                                                    <td><?php if (User::canEditUser($sql, $_SESSION['user_id'])) {
                                                        ?><button type="button" class="btn btn-default btn-block" onclick="editUser('<?php echo $user['user_id']; ?>');">Edit user</button> <?php
                                                    } ?></td>
                                                </tr>
                                                <?php }
                                                
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="panel janno-panel">
                                <div class="panel-heading">
                                    Required files
                                </div>
                                <div class="panel-body"><?php echo nl2br(print_r(get_required_files(), true)); ?></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="panel janno-panel">
                                <div class="panel-heading">
                                    <div class="panel-title">Latest news</div>
                                </div>
                                <div class="panel-body list-group">
                                    <div class="list-group-item">
                                        <div class="media-box">
                                            <div class="pull-left">
                                                <span class="fa-stack">

                                                    <em class="fa text-success fa-check fa-stack-2x"></em>
                                                </span>
                                            </div>
                                            <div class="media-box-body clearfix">
                                                <div class="media-box-heading text-success m0">some logs</div>
                                                <p class="m0">
                                                    <small>xxx<br>xxxxx</small>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="panel-body list-group">
                                    <div class="list-group-item">
                                        <div class="media-box">
                                            <div class="pull-left">
                                                <span class="fa-stack">

                                                    <em class="fa text-success fa-check fa-stack-2x"></em>
                                                </span>
                                            </div>
                                            <div class="media-box-body clearfix">
                                                <div class="media-box-heading text-success m0">some other stuff</div>
                                                <p class="m0">
                                                    <small>text<br>text</small>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
        <div id="userModal" role="dialog" aria-labelledby="userModalTitle" aria-hidden="true" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" data-dismiss="modal" aria-label="Close" class="close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 id="userModalTitle" class="modal-title">Add a new user</h4>
                    </div>
                    <div class="modal-body" id="usrBody">
                        <h5 hidden id="newErrormsg"></h5>
                        <form id="userForm" role="form" onsubmit="addNewAccount();" hidden disabled>
                            
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" id="newusername" class="form-control" placeholder="The username of the account" required value="username">
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input id="newpassword" type="password" class="form-control" placeholder="The password of the account" required value="password">
                            </div>
                            <div class="form-group">
                                <label>E-mail</label>
                                <input type="email" id="newemail" class="form-control" placeholder="The e-mail of the account" required>
                            </div>
                            <div class="form-group">
                                <label>Full name</label>
                                <input type="text" id="newfullname" class="form-control" placeholder="The full name of the user" required>
                            </div>
                            <div class="form-group" id="disabledmsg"></div>
                            <div class="form-group">
                                <label>Status</label>
                                <select id="newstatus" class="form-control m-b">
                                    <?php  ?>
                                </select>
                            </div>
                            <input type="hidden" name="origin" id="origin">
                            <button type="submit" hidden id="newAccount"></button>
                        </form>
                        <div hidden id="addIpsAccount">
                            <label>Search for users in the IPS4 database</label>
                        <select id="ips4_users" class="form-control" hidden style="width: 100%;">
                            <option value="">Search for users in IPS4 database</option>
                        </select>
                                    <br><br>
                                    <label>Status</label>
                                <select id="statusIps" class="form-control m-b" hidden >
                                    <?php  ?>
                                </select>
                                    <br>
                                    
                        <button type="button" class="btn btn-block btn-default" onclick="saveIpsAccount();">Save</button>
                        </div>
                        </div>
                    <div class="modal-footer">
                        <div class="clearfix">
                            <div id="newUserButton" class="pull-left" hidden>
                                <button type="button" class="btn btn-success" onclick="submitNew();">Add new user</button>
                            </div>
                            <div class="pull-right">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Delete</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php echo Constants::getJS($HOST_URL . "/static"); ?>
    </body>
</html>
