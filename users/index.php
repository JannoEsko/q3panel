
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
        <title>Users | <?php echo Constants::$PANEL_NAME; ?></title>
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
                        Users
                        <small>Here you can manage everything related to user accounts.</small>
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
                                                foreach($users as $user) {
                                                    ?>
                                                <tr>
                                                    <td><?php echo $user['realName']; ?></td>
                                                    <td><?php echo $user['email']; ?></td>
                                                    <td><?php echo Constants::$MESSAGES['ORIGIN'][$user['origin']]; ?></td>
                                                    <td><?php echo Constants::$MESSAGES['GROUP'][$user['group_id']]; ?></td>
                                                    <td><?php if (User::canEditUser($sql, $_SESSION['user_id'], $user['user_id']) > 0) {
                                                        ?><button type="button" class="btn btn-default btn-block" onclick="initEditUserModal('userModal', <?php echo "'" . $user['user_id'] . "', '" . $user['realName'] . "', '" . $user['email'] . "', '".$user['origin'] . "', '" . $user['group_id']. "', ";if(intval($_SESSION['user_id']) === intval($user['user_id'])) {echo "false";} else {echo "true";} ?>);">Edit user</button>
                                                            <?php
                                                    } ?></td>
                                                </tr>
                                                <?php }
                                                
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <br>
                                    <?php if (User::canPerformAction($sql, $_SESSION['user_id'], Constants::$PANEL_ADMIN)) { ?>
                                    <button type="button" class="btn btn-default btn-block" onclick="initRegisterModal('userModal');">Register new user</button>
                                    <?php } ?></div>
                            </div>
                            
                            
                        </div>
                        <div class="col-md-4">
                            <div class="panel janno-panel">
                                <div class="panel-heading">
                                    Information
                                </div>
                                <div class="panel-body">
                                    <?php
                                    $dat = getStats($sql);
                                    ?>
                                    The panel hosts a total of <?php echo $dat['totalServers']; ?> server(s), from which, <?php echo $dat['runningServers']; ?> is/are currently running. <br>It has <?php echo $dat['totalUsers']; ?> user accounts, out of which <?php echo $dat['extUsers']; ?> of them derive from an external system and <?php echo $dat['localUsers']; ?> of them is registered locally.<br><?php echo $dat['extAuth']; ?><br><?php echo $dat['mailer']; ?>
                                    <br>
                                    <br>
                                    Did you know that by clicking <b><em class="icon-wrench"></em>Themes</b> from the upper bar, you can change the color theme of the panel?
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
                        <h4 id="userModalTitle" class="modal-title"></h4>
                    </div>
                    <div class="modal-body" id="usrBody">
                         <div class="panel janno-panel" id="formMsgPanel" hidden>
                        <div class="panel-heading" id="formTitle">
                            
                        </div>
                        <div class="panel-body" id="formMsg">
                            
                        </div>
                        
                    </div>
                        
                        <div class="clearfix" id="accountButtons">
                            <div class="pull-left">
                                <button type="button" class="btn btn-default btn-block" <?php if (!isExternalAuthEnabled($sql)) { ?> disabled <?php } ?> onclick="$('#newExternalUser').show(500);$('#newLocalUser').hide(500);">Add an external account</button>
                            </div>
                            <div class="pull-right">
                                <button type="button" class="btn btn-default btn-block" onclick="$('#newExternalUser').hide(500);$('#newLocalUser').show(500);">Add a local account</button>
                            </div>
                        </div>
                        <form id="newExternalUser" role="form" method="post" action="../functions.php" hidden>
                            <input type="hidden" name="extAccount" value="1">
                            <div class="form-group">
                                <label>Name of the account</label>
                                <select id="ext_users" class="form-control" name="extUser" width="100%" required>
                                    
                                </select>
                                <small>Click on the box and start typing to find users.</small>
                            </div>
                            <div class="form-group">
                                <label>Group</label>
                                <select name="extUserGroup" id="extgroup" name="group" class="form-control m-b">
                                    <?php echo implode("", Constants::getSelectGroups()); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-default btn-block">Submit</button>
                            </div>
                        </form>
                        <form id="newLocalUser" role="form" method="post" action="../functions.php" hidden> 
                            <input type="hidden" name="register" value="1">
                            <div class="form-group">
                                <label>Username</label>
                                <input value="" type="text" id="newusername" name="username" class="form-control" placeholder="The username of the account" required>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input id="newpassword" name="password" type="password" class="form-control" placeholder="The password of the account" required>
                            </div>
                            <div class="form-group">
                                <label>E-mail</label>
                                <input type="email" id="newemail" name="email" class="form-control" placeholder="The e-mail of the account" required>
                            </div>
                            <div class="form-group" id="disabledmsg"></div>
                            <div class="form-group">
                                <label>Group</label>
                                <select name="userGroup" id="newgroup" name="group" class="form-control m-b">
                                    <?php echo implode("", Constants::getSelectGroups()); ?>
                                </select>
                            </div>
                                <div class="form-group">
                                <button type="submit" class="btn btn-default btn-block">Submit</button>
                            </div>
                        </form>
                        <form id="userForm" role="form" method="post" action="../functions.php" hidden>
                            
                            <div class="form-group">
                                <label>Username</label>
                                <input value="" type="text" id="username" name="username" class="form-control" placeholder="The username of the account" required>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input id="password" name="password" type="password" class="form-control" placeholder="The password of the account" required>
                            </div>
                            <div class="form-group">
                                <label>E-mail</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="The e-mail of the account" required>
                            </div>
                            <div class="form-group" id="disabledmsg"></div>
                            <div class="form-group">
                                <label>Group</label>
                                <select name="group" id="group" name="group" class="form-control m-b">
                                    <?php echo implode("", Constants::getSelectGroups()); ?>
                                </select>
                            </div>
                            <input type="hidden" name="origin" id="origin">
                            <input type="hidden" name="user_id" id="user_id">
                            <input type="hidden" name="editUser" id="editUser">
                            <input type="hidden" name="delete" id="delete">
                        </form>
                        
                                    
                        
                        </div>
                    <div class="modal-footer">
                        <div class="clearfix">
                            <div id="save" class="pull-left">
                                <button hidden id="editSubmit" type="button" class="btn btn-success" onclick="$('#editUser').val(1);$('#delete').val(0);$('#userForm').submit();">Save</button>
                            </div>
                            <div class="pull-right">
                                <button hidden id="deleteSubmit" type="button" class="btn btn-danger" onclick="$('#editUser').val(0);$('#delete').val(1);$('#userForm').submit();">Delete</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
                            </div>
                        </div>
                    </div>
                </div> </div>
            </div>
        
        <?php echo Constants::getJS($HOST_URL . "/static"); ?>
        <script>handleForm("newLocalUser");handleForm("newExternalUser");handleForm("userForm");$("#ext_users").select2({
            ajax: {
                method: "GET",
                url: "../functions.php",
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
        //ugly fix to Select2 not being 100% when it's been hidden previously.
        $(".select2").css("width", "100%");
        
        </script>
    </body>
</html>
