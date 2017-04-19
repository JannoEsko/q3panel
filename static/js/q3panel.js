/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
                formTitle.html("Error");
                formMsgPanel.removeClass("janno-panel");
                formMsgPanel.addClass("panel-danger");
                formMsg.html(response.error);
                formMsgPanel.show(500);
            } else {
                if (typeof response.href !== "undefined") {
                    location.href = response.href;
                } else {
                    formTitle.html("Success");
                    formMsg.html(response.msg);
                    formMsgPanel.show(500);
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

