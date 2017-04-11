/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(function() {
    var form = $("#form");
    var formMsg = $("#formMsg");
    var formMsgPanel = $("#formMsgPanel");
    var formTitle = $("#formTitle");

    $(form).submit(function(e) {
        e.preventDefault();
        formMsgPanel.removeClass("panel-danger");
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
                formTitle.html("Error2");
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
});
