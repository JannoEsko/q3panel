
<?php
if (!file_exists(__DIR__ . "/../../../config.php")) {
    header("Location: ../../../install/");
}
session_start();
require_once __DIR__ . "/../../../classes/loader.php";
require_once __DIR__ . "/../../../login.php";
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
            <?php require_once __DIR__ . "/../../../static/html/header_aside.php"; ?>
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
                                    Server and User mapping and permissions
                                </div>
                                <div class="panel-body">
                                    <div class="table-responsive table-bordered">
                                        <table class="table table-hover" id="mapTable">
                                            <thead>
                                            <th>Username</th>
                                            <th>Stopping server</th>
                                            <th>Seeing RCON</th>
                                            <th>Using FTP</th>
                                            <th>Actions</th>
                                            </thead>
                                            <tbody>
                                    <?php
                                    $serverMapping = Server::getServersWithUserMapping($sql, $_GET['server_id']);
                                    if (sizeof($serverMapping) === 1) {
                                        $serverMapping = $serverMapping[0];
                                    }
                                    foreach($serverMapping as $serverMap) {
                                        ?><tr><?php
                                        if (intval($serverMap['origin']) === Constants::$EXTERNAL_ACCOUNT) {
                                            $extQry = User::getExternalAccount($sql, $serverMap['username'], true);
                                            $tableSpec = $extQry['extTable_spec'];
                                            $extData = $extQry['data'];
                                            if (sizeof($extData) === 1) {
                                                $extData = $extData[0];
                                            }
                                            ?><td><?php echo $extData[$tableSpec['username_field']]; ?></td>
                                            <td><?php echo intbool2str($serverMap['can_stop_server']); ?></td>
                                            <td><?php echo intbool2str($serverMap['can_see_rcon']); ?></td>
                                            <td><?php echo intbool2str($serverMap['can_see_ftp']); ?></td>
                                            <td><?php if (intval($serverMap['group_id']) !== Constants::$PANEL_ADMIN) { ?><button type="button" class="btn btn-block btn-default">Edit</button><button type="button" class="btn btn-block btn-default">Remove</button><?php } ?></td>
                                        <?php } else { ?>
                                            <td><?php echo $serverMap['username']; ?></td>
                                            <td><?php echo intbool2str($serverMap['can_stop_server']); ?></td>
                                            <td><?php echo intbool2str($serverMap['can_see_rcon']); ?></td>
                                            <td><?php echo intbool2str($serverMap['can_see_ftp']); ?></td>
                                            <td><?php if (intval($serverMap['group_id']) !== Constants::$PANEL_ADMIN) { ?><button type="button" class="btn btn-block btn-default">Edit</button><button type="button" class="btn btn-block btn-default">Remove</button><?php } ?></td>
                                        <?php }
                                        ?></tr><?php 
                                    }
                                    
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
        <?php echo Constants::getJS($HOST_URL . "/static"); ?>
    </body>
</html>
