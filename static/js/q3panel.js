/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

toastr.options = {
    "debug": false,
    "progressBar": false,
    "positionClass": "toast-top-right",
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
}

$(document).ready(function() {
    
    /*
     * Taken from http://stackoverflow.com/a/12950620/5529540.
     * Adds active class automatically to the link you're currently on.
     */
    var url = window.location.href;
    $('ul.nav a').filter(function() {
        return this.href === url;
    }).parent().addClass('active');
});

function handleForm(id) {
    handleForm(id, false);
}

function setPreferencedTheme(theme) {
    $.post(window.location.href, {
        theme: "1",
        themename: theme
    }, function(data) {
        data = JSON.parse(data);
        if (typeof data !== "undefined" && typeof data.style_bg !== "undefined") {
            $("#themecolor").attr("content", data.style_bg);
        }
    });
}

function handleForm(id, useToaster) {
    var form = $("#" + id);
    var formMsg = $("#formMsg");
    var formMsgPanel = $("#formMsgPanel");
    var formTitle = $("#formTitle");
    $(form).submit(function(e) {
        e.preventDefault();
        formMsgPanel.removeClass("panel-danger");
        formMsgPanel.addClass("janno-panel");
        formMsgPanel.hide(500);
        var data = form.serialize();
        console.log(data);
        var url = $(form).attr('action');
        var type = $(form).attr('method');
        $.ajax({
            type: type,
            url: url,
            data: data
        }).done(function(response) {
            console.log(response);
            response = JSON.parse(response);
            if (typeof response.error !== "undefined") {
                if (useToaster) {
                    toastr.error(response.error);
                } else {
                    formTitle.html("Error");
                    formMsgPanel.removeClass("janno-panel");
                    formMsgPanel.addClass("panel-danger");
                    formMsg.html(response.error);
                    formMsgPanel.show(500);
                }
            } else {
                if (typeof response.href !== "undefined") {
                    location.href = response.href;
                } else {
                    if (useToaster) {
                        toastr.success(response.msg);
                        if (typeof response.refreshwebftptable !== "undefined") {
                            initWebFTPTable("webftptable", ".", $("#server_id").val());
                            $("#fileRenameModal").modal('toggle');
                        }
                        if (typeof response.successnewfolder !== "undefined") {
                            initWebFTPTable("webftptable", response.successnewfolder, $("#server_id").val());
                            $("#newFileFolderModal").modal('toggle');
                        }
                        if (typeof response.successnewfile !== "undefined") {
                            initWebFTPTable("webftptable", response.successnewfile, $("#server_id").val());
                            $("#newFileFolderModal").modal('toggle');
                        }
                        if (typeof response.successuploadfile !== "undefined") {
                            initWebFTPTable("webftptable", response.successuploadfile, $("#server_id").val());
                            $("#newFileFolderModal").modal('toggle');
                        }
                    } else {
                        formTitle.html("Success");
                        formMsg.html(response.msg);
                        formMsgPanel.show(500);
                    }
                    
                }
            }
        });
    });
}

function setPreferencedTheme(theme) {
    $.post(window.location.href, {
        theme: "1",
        themename: theme
    }, function(data) {
        data = JSON.parse(data);
        if (typeof data !== "undefined" && typeof data.style_bg !== "undefined") {
            $("#themecolor").attr("content", data.style_bg);
        }
    });
}

function initEditUserModal(id, user_id, username, email, origin, group, canChangeGroup) {
    $("#group").val(group).change();
    $("#userModalTitle").html("Edit user " + username);
    if (parseInt(origin) === 1) {
        $("#disabledmsg").html("Upper options are disabled because the user derives from an external system.");
        $("#username").prop('disabled', true);
        $("#password").prop('disabled', true);
        $("#email").prop('disabled', true);
    } else {
        $("#disabledmsg").html("");
        $("#username").prop('disabled', false);
        $("#password").prop('disabled', false);
        $("#email").prop('disabled', false);
    }
    if (canChangeGroup) {
        $("#group").removeAttr("disabled");
    } else {
        $("#group").prop("disabled", true);
    }
    $("#user_id").val(user_id);
    $("#username").val(username);
    $("#email").val(email);
    $("#origin").val(origin);
    $("#editUser").val(1);
    $("#delete").val(0);
    $("#userForm").show();
    $("#deleteSubmit").show();
    $("#editSubmit").show();
    $("#newUserForm").hide();
    $("#accountButtons").hide();
    $("#newUserForm").hide();
    $("#accountButtons").hide();
    $("#newExternalUser").hide();
    $("#newLocalUser").hide();
    $("#" + id).modal();
}

function initRegisterModal(id) {
    $("#deleteSubmit").hide();
    $("#editSubmit").hide();
    $("#userForm").hide();
    $("#newUserForm").show();
    $("#accountButtons").show();
    $("#" + id).modal();
}

function initEditGameModal(modal_id, game_id) {
    $.post(".", {
        getGame: 1,
        game_id: game_id
    }, function(data) {
        data = JSON.parse(data);
        $('#addGame').val(0);
        $('#deleteGame').val(0);
        $('#updateGame').val(1);
        $("#gameId").val(game_id);
        $("#game_name").val(data.game_name);
        $("#game_location").val(data.game_location);
        $("#startscript").html(data.startscript);
        $('#gameModalTitle').html('Edit game ' + data.game_name);
        $("#" + modal_id).modal();
    });
}

function initEditHostModal(modal_id, host_id) {
    $.post(".", {
        getHostData: 1,
        host_id: host_id
    }, function(data) {
        data = JSON.parse(data);
        console.log(data);
        $("#updateHost").val(1);
        $("#addHost").val(0);
        $("#hostId").val(data.host_id);
        $("#deleteHost").val(0);
        $("#servername").val(data.servername);
        $("#hostname").val(data.hostname);
        $("#sshport").val(data.sshport);
        $("#host_username").val(data.host_username);
        $("#deleteGameBtn").show();
        $("#" + modal_id).modal();
    });
}

function startServer(server_id) {
    $.post(".", {
        startServer: 1,
        server_id: server_id
    }, function(data) {
        data = JSON.parse(data);
        if (typeof data.error !== "undefined") {
            toastr.error(data.error);
        } else if (typeof data.msg !== "undefined") {
            $("#startServer").hide(500);
            $("#stopServer").show(500);
            toastr.success(data.msg);
        }
    });
}

function stopServer(server_id) {
    $.post(".", {
        stopServer: 1,
        server_id: server_id
    }, function(data) {
        data = JSON.parse(data);
        if (typeof data.error !== "undefined") {
            toastr.error(data.error);
        } else if (typeof data.msg !== "undefined") {
            $("#stopServer").hide(500);
            $("#startServer").show(500);
            toastr.success(data.msg);
        }
    });
}

function disableServer(server_id) {
    $.post(".", {
        disableServer: 1,
        server_id: server_id
    }, function(data) {
        data = JSON.parse(data);
        if (typeof data.error !== "undefined") {
            toastr.error(data.error);
        } else if (typeof data.msg !== "undefined") {
            $("#stopServer").hide(500);
            $("#startServer").hide(500);
            $("#enableServerBtn").show(500);
            $("#disableServerBtn").hide(500);
            toastr.success(data.msg);
        }
    });
}

function deleteServer(server_id) {
    $.post(".", {
        deleteServer: 1,
        server_id: server_id
    }, function(data) {
        data = JSON.parse(data);
        if (typeof data.error !== "undefined") {
            toastr.error(data.error);
        } else if (typeof data.href !== "undefined") {
            location.href = data.href;
        }
    });
}

function enableServer(server_id) {
    $.post(".", {
        enableServer: 1,
        server_id: server_id
    }, function(data) {
        data = JSON.parse(data);
        if (typeof data.error !== "undefined") {
            toastr.error(data.error);
        } else if (typeof data.msg !== "undefined") {
            $("#stopServer").hide(500);
            $("#startServer").show(500);
            $("#enableServerBtn").hide(500);
            $("#disableServerBtn").show(500);
            
            toastr.success(data.msg);
        }
    });
}

function initWebFTPTable(table_id, dir, server_id) {
    var table = $("#" + table_id);
    var tableItems = $("#" + table_id + "body");
    $("#newcurrdir").val(dir);
    $("#newFileUploadCurrDir").val(dir);
    $.post(".", {
        ftp: 1,
        getDirContents: dir,
        server_id: server_id
    }, function(data) {
        console.log(data);
        data = JSON.parse(data);
        if (data === null) {
            table.append("<tr><td><em class='fa fa-folder'></em> <a href='#' onclick='initWebFTPTable(\"" + table_id + "\", \"../\", \"" + server_id + "\");'>../</a></td></tr>");
        } else {
            if (typeof data.error !== "undefined") {
                toastr.error(data.error);
            } else {
                tableItems.empty();
                table.append("<tr><td><em class='fa fa-folder'></em> <a href='#' onclick='initWebFTPTable(\"" + table_id + "\", \"../\", \"" + server_id + "\");'>../</a></td></tr>");
                $.each(data, function(item_id, items) {
                   
                    var actionsButton = "";
                    if (items.content !== "../") {
                        actionsButton = " <button class=\"btn btn-default btn-sm\" onclick='deleteFromFTP(\"\",\"" + items.content + "\", \"" + server_id + "\", \"" + table_id + "\", \"" + dir + "\");'><em class=\"fa fa-trash-o\"></em> Delete</button><button class=\"btn btn-default btn-sm\" onclick='renameFileOrFolderModal(\"fileRenameModal\", \"" + items.content + "\");'><em class=\"fa fa-pencil-square-o\"> </em>Rename</button>";
                    }
                    
                    var dirIcon = "";
                    if (parseInt(items.dir) === 1) {
                        dirIcon = "<em class='fa fa-folder'></em> ";
                        table.append("<tr><td width=\"66%\">" + dirIcon + "<a href='#' onclick='initWebFTPTable(\"" + table_id + "\", \"" + items.content + "\", \"" + server_id + "\");'>" + items.content + "</a></td><td>" + actionsButton + "</td></tr>");
                    } else {
                        dirIcon = "<em class='fa fa-file'></em> ";
                        table.append("<tr><td width=\"66%\">" + dirIcon + "<a href='#' onclick='initFileEditModal(\"fileEditModal\", \"" + items.content + "\", \"" + server_id + "\");'>" + items.content + "</td><td>" + actionsButton + "</td></tr>");
                    }
                    


                });
             }

    }
});
}

function initFileEditModal(modal_id, filename, server_id) {
    $.post(".", {
        getFile: 1,
        fileName: filename,
        server_id: server_id
    }, function(data) {
        try {
            data = JSON.parse(data);
            if (typeof data.error !== "undefined") {
                toastr.error(data.error);
            } else {
                $("#fileContents").html(data.filecontents);
                $("#" + modal_id + "Title").html(filename);
                $("#filename").val(filename);
                $("#" + modal_id).modal();
            }
        } catch (SyntaxError) {
            $.post(".", {
                getFTPURIForFile: 1,
                fileName: filename,
                server_id: server_id
            }, function(data) {
                data = JSON.parse(data);
                if (typeof data.error !== "undefined") {
                    toastr.error(data.error);
                } else if (typeof data.href !== "undefined") {
                    toastr.success("File " + filename + " download will begin shortly.");
                    var link=document.createElement('a');
                    link.href=data.href;
                    link.download=filename;
                    link.click();
                    
                }
                
            });
        }
        
    });
}

function deleteFromFTP(name, server_id, table_id, currdir) {
    $.post(".", {
        deleteFromFTP: 1,
        filename: name,
        server_id: server_id
    }, function(data) {
        data = JSON.parse(data);
        if (typeof data.error !== "undefined") {
            toastr.error(data.error);
        } else {
            toastr.success(data.msg);
            console.log(data.msg);
            initWebFTPTable(table_id, currdir, server_id);
        }
    });
}

function renameFileOrFolderModal(modal_id, name) {
    $("#oldfilename").val(name);
    $("#newfilename").val(name);
    $("#" + modal_id + "Title").html("Rename file " + name);
    $("#" + modal_id).modal();
}

function renameFileOrFolder(oldname, newname, server_id, table_id, currdir) {
    $.post(".", {
        renameFileOrFolder: 1,
        oldfilename: name,
        newfilename: newname,
        server_id: server_id
    }, function(data) {
        data = JSON.parse(data);
        if (typeof data.error !== "undefined") {
            toastr.error(data.error);
        } else {
            toastr.success(data.msg);
            console.log(data.msg);
            initWebFTPTable(table_id, currdir, server_id);
        }
    });
}