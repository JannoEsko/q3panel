/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(function() {
    var form = $("#form");
    var formMsg = $("#formMsg");
    var formMsgPanel = $("#formMsgPanel");
    formMsgPanel.removeClass("panel-danger");
    formMsgPanel.hide(500);
    $(form).submit(function(e) {
        e.preventDefault();
        var data = form.serialize();
        var type = $(form).attr('action');
        var url = $(form).attr('method');
        $.ajax({
            type: type,
            url: url,
            data: data
        }).done(function(response) {
            if (response.error !== "") {
                
                formMsgPanel.addClass("panel-danger");
                formMsg.html(response.error);
                formMsgPanel.show(500);
            } else {
                if (response.href !== "") {
                    location.href = response.href;
                } else {
                    formMsgPanel.html(response.msg);
                    formMsgPanel.show(500);
                }
            }
        });
    });
});
