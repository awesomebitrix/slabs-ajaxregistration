$(document).ready( function() {

    $('.registration').on('submit','.form-ajax',function (e) {
        var attrAction,
            self = $(this),
            sendMethod = self.attr('method'),
            sendUrl = self.attr('action'),
            sendData = self.serialize(),
            sendDataType = "HTML";
        e.preventDefault();
        ajaxRequest(sendMethod,sendUrl,sendData,sendDataType,self);
    });

    $('.registration').on('click','.sl-button-ajax',function () {
        var self = $(this),
            attrAction  = self.attr('data-action'),
            sendUrl = self.attr('data-url'),
            sendData = {},
            sendMethod = "POST",
            sendDataType = "HTML";

        if( attrAction != undefined ){
            sendData["action"] = attrAction;
            sendDataType = 'json';
        }

        ajaxRequest(sendMethod,sendUrl,sendData,sendDataType,self);
    });
    
    function ajaxRequest(sendMethod,sendUrl,sendData,sendDataType,self) {
        $.ajax({
            method: sendMethod,
            url: sendUrl,
            data: sendData,
            dataType: sendDataType,
            success: function(data){
                var modal = $('.modal'),
                    modalBody = $('.modal .modal-content'),
                    content ='';

                if( self.attr('data-action') == "captcha" ){
                    self.attr("src","/bitrix/tools/captcha.php?captcha_sid="+data);
                    $('[name="captcha_sid"]').attr('value',data);
                }else{

                    modalBody.html(data);
                    modal.modal();
                }
            }
        });
    }



});