var twoFactorAuth = false ;
$(document).ready(function() {

    // Check if use Google 2FA
    /*$.getJSON('login/getGoogle2fa/', function (data) {
        console.log(data);
        if(data.success == 1) {
            var secret = "<input name='token' type='text' class='form-control' placeholder='Token' required=''>"
            $('#secret').html(secret);
        }
    });*/

    $('#username').val('')

    $('#urlLostPasssword').click(function(){
        lostPasswordAjax();
    });

    $("#btnSend").click(function(){
        var $this = $(this);
        $this.button('loading');
        lostPasswordAjax($this);
    });
    
    
    $("#frm-login").submit(function()
    {
        var $self       = $(this),
            login       = $('[name=login]').val(),
            password    = $('[name=password]').val(),
            token       = $('[name=token]').val()    ;

        $.post("login/auth/", {
            login : login,
            password : password,
            token: token
        }, function(data) {
            if(data.success == 0){
                modalAlert('danger',data.msg);
                $('[name=login]').val('');
                $('[name=password]').val('');
                $('[name=login]').val('');
            } else if(data.success == 1){
                self.location = data.redirect;
            }
        },"json").complete(function(){
            $(".loaderLogin").hide();
        });
        return false;

    });


    // I use $(".className") instead $('#id') because the page have 2 links href.
    $('.just_for_reference').click(function(){

        console.log( $(this).attr('href'));
        var idWarning = $(this).attr('href') ;

        $.getJSON("login/getWarning/id/"+idWarning, function(jsonData) {
            /*
            $.each(jsonData, function(key, val) {
                console.log(val.name);
            });
            */

            $("#title_topic").html(jsonData[0].title_topic);
            $("#title_warning").html(jsonData[0].title_warning);
            $("#description").html(jsonData[0].description);
            $("#valid_msg").html(jsonData[0].valid_msg);

            $("#myModal").modal();

        })
        .success(function() {

        })
        .error(function(jqXHR, textStatus, errorThrown) {
            console.log("error " + textStatus);
            console.log("incoming Text " + jqXHR.responseText);
            $("#msg").html("incoming Text " + jqXHR.responseText);
            $("#myModal").modal();
         })
        .complete(function() {

        });


    });

    /* reset modal's form */
    $('.modal').on('hidden.bs.modal', function() {
        $('#username').val('');
    });

});

function lostPasswordAjax($ButtonSend)
{

    $.ajax({
        type: "POST",
        url: "login/lostPassword",
        data: { username:$('#username').val()},
        error: function (ret) {
            modalAlert('danger',makeSmartyLabel('Alert_failure'));
            $ButtonSend.button('reset');
        },
        success: function(ret) {
            $('#modal-form-lost-password').modal('hide');
            if(ret){
                if (ret ==  '1')  {
                    modalAlert('info',makeSmartyLabel('Lost_password_pop'));
                    $ButtonSend.button('reset');
                } else if (ret == '2') {
                    modalAlert('info',makeSmartyLabel('Lost_password_ad'));
                    $ButtonSend.button('reset');
                } else if (ret ==  'NOT_EXISTS' ) {
                    modalAlert('danger',makeSmartyLabel('Lost_password_not'));
                    $ButtonSend.button('reset');
                } else if (ret ==  'MASTER' ) {
                    modalAlert('info',makeSmartyLabel('Lost_password_master'));
                    $ButtonSend.button('reset');
                } else {
                    modalAlert('success',makeSmartyLabel('Lost_password_suc'));
                    $ButtonSend.button('reset');
                }
            }
            else
            {
                modalAlert('danger',makeSmartyLabel('Lost_password_err'));
                $ButtonSend.button('reset');
            }
        }
    });

}