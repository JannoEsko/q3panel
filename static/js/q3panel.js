
/**
 * Initializes some of the toastr options.
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
};

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

/**
 * Generic function to handle forms.
 * @param {int} id The ID of the form.
 * @returns {void} Returns nothing.
 */
function handleForm(id) {
    handleForm(id, false);
}

/**
 * Sends the theme you chose to the server so it would save it into the database.
 * @param {string} theme The theme name.
 * @returns {void} Returns nothing.
 */
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

/**
 * Turns an integer into a boolean-string (Yes/No).
 * @param {int} input The integer which will be checked.
 * @returns {String} Returns Yes, if the integer is 1, No if it's not.
 */
function int2boolstr(input) {
    if (input === 1) {
        return "Yes";
    }
    return "No";
}

/**
 * Generic function to handle forms.
 * @param {int} id The ID of the form.
 * @param {boolean} useToaster Tells the form whether to use toaster or not.
 * @returns {void} Returns nothing.
 */
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
        var url = $(form).attr('action');
        var type = $(form).attr('method');
        $.ajax({
            type: type,
            url: url,
            data: data
        }).done(function(response) {
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
                    if (response.href === "href") {
                        window.location.reload();
                    } else {
                        location.href = response.href;
                    }
                    
                } else {
                    if (useToaster) {
                        toastr.success(response.msg);
                        if (typeof response.refreshwebftptable !== "undefined") {
                            initWebFTPTable("webftptable", response.refreshwebftptable, $("#server_id").val());
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
                        if (typeof response.newFTPPasswordSet !== "undefined") {
                            $("#ftppswreset").modal('toggle');
                        }
                        if (typeof response.changerconpsw !== "undefined") {
                            $("#changerconpsw").modal('toggle');
                        }
                        if (typeof response.removeMapTableRow !== "undefined") {
                            $("table#mapTable tr#tr" + response.removeMapTableRow).remove();
                        }
                        if (typeof response.toggleModal !== "undefined") {
                            $("#" + response.toggleModal).modal('toggle');
                        }
                        if (typeof response.updateRow !== "undefined" && typeof response.action !== "undefined" && response.action === "serverMapUpdate") {
                            var csf = int2boolstr(response.can_see_ftp);
                            var csr = int2boolstr(response.can_see_rcon);
                            var css = int2boolstr(response.can_stop_server);
                            var row = response.updateRow;
                            $("#tdcss" + row).html(css);
                            $("#tdcsr" + row).html(csr);
                            $("#tdcsf" + row).html(csf);
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

/**
 * Initializes the edit-user modal. Page-specific.
 * @param {String} id The modal ID
 * @param {int} user_id The user ID which to edit.
 * @param {String} username The username.
 * @param {String} email The e-mail.
 * @param {int} origin 1 if external, 0 if local.
 * @param {int} group The group ID
 * @param {boolean} canChangeGroup Whether he can change his own group or not.
 * @returns {void} Returns nothing.
 */
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

/**
 * Initializes the registration modal.
 * @param {String} id The modal ID
 * @returns {void} Returns nothing.
 */
function initRegisterModal(id) {
    $("#deleteSubmit").hide();
    $("#editSubmit").hide();
    $("#userForm").hide();
    $("#newUserForm").show();
    $("#accountButtons").show();
    $("#" + id).modal();
}

/**
 * Initializes edit game modal (page-specific).
 * @param {String} modal_id The modal ID.
 * @param {int} game_id The game ID
 * @returns {void} Returns nothing.
 */
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

/**
 * Initializes edit host modal (page-specific).
 * @param {String} modal_id The modal ID
 * @param {int} host_id The host ID
 * @returns {void} Returns nothing.
 */
function initEditHostModal(modal_id, host_id) {
    $.post(".", {
        getHostData: 1,
        host_id: host_id
    }, function(data) {
        data = JSON.parse(data);
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

/**
 * Starts a server.
 * @param {int} server_id The server ID.
 * @returns {void} Returns nothing.
 */
function startServer(server_id) {
    $.post(".", {
        startServer: 1,
        server_id: server_id
    }, function(data) {
        data = JSON.parse(data);
        if (typeof data.error !== "undefined") {
            toastr.error(data.error);
        } else if (typeof data.msg !== "undefined") {
            $("#rcon").show(500);
            $("#startServer").hide(500);
            $("#stopServer").show(500);
            toastr.success(data.msg);
        }
    });
}

/**
 * Stops a server.
 * @param {int} server_id The server ID.
 * @returns {void} Returns nothing.
 */
function stopServer(server_id) {
    $.post(".", {
        stopServer: 1,
        server_id: server_id
    }, function(data) {
        data = JSON.parse(data);
        if (typeof data.error !== "undefined") {
            toastr.error(data.error);
        } else if (typeof data.msg !== "undefined") {
            $("#rcon").hide(500);
            $("#stopServer").hide(500);
            $("#startServer").show(500);
            toastr.success(data.msg);
        }
    });
}

/**
 * Disables a server.
 * @param {int} server_id The server ID.
 * @returns {void} Returns nothing.
 */
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

/**
 * Deletes a server.
 * @param {int} server_id The server ID.
 * @returns {void} Returns nothing.
 */
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

/**
 * Enables a server.
 * @param {int} server_id The server ID.
 * @returns {void} Returns nothing.
 */
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

/**
 * Initializes the web FTP table (page-specific). Can be called over and over again.
 * @param {String} table_id The table id where the FTP contents are being shown.
 * @param {String} dir The directory contents you wish to see.
 * @param {int} server_id The server ID.
 * @returns {void} Returns nothing.
 */
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
                        actionsButton = " <button class=\"btn btn-default btn-sm\" onclick='deleteFromFTP(\"" + items.content + "\", \"" + server_id + "\", \"" + table_id + "\", \"" + dir + "\");'><em class=\"fa fa-trash-o\"></em> Delete</button><button class=\"btn btn-default btn-sm\" onclick='renameFileOrFolderModal(\"fileRenameModal\", \"" + items.content + "\");'><em class=\"fa fa-pencil-square-o\"> </em>Rename</button>";
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

/**
 * Initializes file edit modal (page-specific).
 * @param {String} modal_id The modal ID
 * @param {String} filename The file name which to edit.
 * @param {int} server_id The server ID
 * @returns {void} Returns nothing.
 */
function initFileEditModal(modal_id, filename, server_id) {
    $("#formMsgPanel").hide();
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
                $("#fileContents").val(data.filecontents);
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
                    /*
                     * http://stackoverflow.com/questions/3499597/javascript-jquery-to-download-file-via-post-with-json-data
                     */
                    var link=document.createElement('a');
                    link.href=data.href;
                    link.download=filename;
                    link.click();
                    
                }
                
            });
        }
        
    });
}

/**
 * Deletes a file/folder from FTP (page-specific).
 * @param {String} name The name of the file/folder to delete.
 * @param {int} server_id The server ID
 * @param {String} table_id The table id of the FTP contents.
 * @param {String} currdir The current directory.
 * @returns {void} Returns nothing.
 */
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
            initWebFTPTable(table_id, currdir, server_id);
        }
    });
}

/**
 * Initializes rename file/folder modal.
 * @param {String} modal_id The modal ID.
 * @param {String} name The current file name.
 * @returns {void} Returns nothing.
 */
function renameFileOrFolderModal(modal_id, name) {
    $("#oldfilename").val(name);
    $("#newfilename").val(name);
    $("#" + modal_id + "Title").html("Rename file " + name);
    $("#" + modal_id).modal();
}

/**
 * Renames a file/folder.
 * @param {String} oldname The old name of the file/folder.
 * @param {String} newname The new name of the file/folder.
 * @param {int} server_id The server ID
 * @param {String} table_id The table ID, where the FTP contents are being shown.
 * @param {String} currdir The current directory.
 * @returns {void} Returns nothing.
 */
function renameFileOrFolder(oldname, newname, server_id, table_id, currdir) {
    $.post(".", {
        renameFileOrFolder: 1,
        oldfilename: oldname,
        newfilename: newname,
        server_id: server_id
    }, function(data) {
        data = JSON.parse(data);
        if (typeof data.error !== "undefined") {
            toastr.error(data.error);
        } else {
            toastr.success(data.msg);
            initWebFTPTable(table_id, currdir, server_id);
        }
    });
}

/**
 * Initializes FTP Password reset modal.
 * @param {String} modal_id The modal ID.
 * @returns {void} Returns nothing.
 */
function resetFtpPassword(modal_id) {
    $("#" + modal_id).modal();
}

/**
 * Initializes RCON Password reset modal.
 * @param {String} modal_id The modal ID.
 * @returns {void} Returns nothing.
 */
function changeRconPassword(modal_id) {
    $("#" + modal_id).modal();
}

/**
 * Automatically generates a new FTP password.
 * @param {int} server_id The server ID.
 * @param {String} modal_id The modal ID.
 * @returns {void} Returns nothing.
 */
function autoGenerateNewFTPPsw(server_id, modal_id) {
    $.post(".", {
        generateNewFTP: 1,
        server_id: server_id
    }, function(data) {
        data = JSON.parse(data);
        $("#" + modal_id).modal('toggle');
        if (typeof data.error !== "undefined") {
            toastr.error(data.error);
        } else if (typeof data.msg !== "undefined") {
            toastr.success(data.msg);
        }
    });
}

/**
 * Automatically generates a new RCON password.
 * @param {int} server_id The server ID.
 * @param {String} modal_id The modal ID.
 * @returns {void} Returns nothing.
 */
function autoGenerateNewRCONPsw(server_id, modal_id) {
    $.post(".", {
        generateNewRCON: 1,
        server_id: server_id
    }, function(data) {
        data = JSON.parse(data);
        $("#" + modal_id).modal('toggle');
        if (typeof data.error !== "undefined") {
            toastr.error(data.error);
        } else if (typeof data.msg !== "undefined") {
            toastr.success(data.msg);
        }
    });
}

/**
 * Edits the server mapping for a specific user (page-specific).
 * @param {String} modal_id The modal ID.
 * @param {int} user_id The user ID.
 * @param {String} username The username.
 * @param {String} can_stop_server
 * @param {String} can_see_rcon
 * @param {String} can_see_ftp
 * @returns {void} Returns nothing.
 */
function editServerMapping(modal_id, user_id, username, can_stop_server, can_see_rcon, can_see_ftp) {
    $("#" + modal_id + "Title").html("Edit mapping for user " + username);
    $("#editMap").val("1");
    $("#addMap").val("0");
    $("#editMapUserId").val(user_id);
    $("#addUserId").prop("disabled", true);
    $("#addUserSelect").hide();
    $('#editMapUserId').prop('disabled', false);
    if (can_stop_server === 1) {
        $("#can_stop_server").prop("checked", true);
    } else {
        $("#can_stop_server").prop("checked", false);
    }
    if (can_see_rcon === 1) {
        $("#can_see_rcon").prop("checked", true);
    } else {
        $("#can_see_rcon").prop("checked", false);
    }
    if (can_see_ftp === 1) {
        $("#can_see_ftp").prop("checked", true);
    } else {
        $("#can_see_ftp").prop("checked", false);
    }
    $("#" + modal_id).modal();
}

/**
 * Initializes web RCON tool.
 * @param {String} modal_id The modal which to initialize.
 * @returns {void} Returns nothing.
 */
function initRCONModal(modal_id) {
    $("#" + modal_id).modal();
}

/**
 * Sends a command to the Q3 server.
 * @param {int} server_id The server, to which to send the command.
 * @returns {void} Returns nothing.
 */
function sendCommand(server_id) {
    var command = $("#command").val();
    $("#console").append("<i>You sent a command: " + command + "</i>\n\n");
    $.post(".", {
        server_id: server_id,
        command: command,
        sendRCONCommand: 1
    }, function(data) {
        data = JSON.parse(data);
        if (typeof data.error !== "undefined") {
            toastr.error(data.error);
        } else {
            $("#console").append(data.output);
        }
    });
}

/**
 * Initializes ticket details modal (with all the messages etc). Page-specific.
 * @param {String} modal_id The modal ID
 * @param {int} ticket_id The ticket ID
 * @param {boolean} showReplyForm If true, shows the reply form (checked on backend as well).
 * @returns {void} Returns nothing.
 */
function initTicketDetails(modal_id, ticket_id, showReplyForm) {
    $.post(".", {
        getAllTicketData: 1,
        ticket_id: ticket_id
    }, function(data) {
        data = JSON.parse(data);
        if (typeof data.error !== "undefined") {
            toastr.error(data.error);
        } else {
            $("#messages").html("");
            if (showReplyForm) {
                $("#newTicketMessage").show();
            } else {
                $("#newTicketMessage").hide();
            }
            $.each(data, function(item_id, row) {
                $("#" + modal_id + "Title").html(row.title);
                $("#support_ticket_id").val(row.support_ticket_id);
                $("#messages").prepend('<a class="list-group-item"><div class="media-box"><div class="media-box-body clearfix"><small class="pull-right">' + row.message_date + '</small><strong class="media-box-heading text-primary"><span class="text-left"></span>' + row.realName + ' (' + row.group_text + ')</strong><p class="mb-sm"><br><small>' + row.message + '</small></p><small class="pull-right">IP: ' + row.user_ip + '</small></div></div></a>');
            });
            $("#" + modal_id).modal();
        }
    });
}
