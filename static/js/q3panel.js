/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
                    formMsgPanel.html(response.msg);
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
    });
}

