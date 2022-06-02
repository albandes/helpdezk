$(document).ready(function () {

    countdown.start(timesession);

    /*
     * Demo version
     */
    if (demoVersion == 1){
        if ($('#logindemo').val() == 'user' || $('#logindemo').val() == 'operator' ) {
            $('#btnUpdatePerson').prop('disabled', true);
        }
    }

    new gnMenu( document.getElementById( 'gn-menu' ) );

    if(access[1] != "Y"){
        $("#btnCreateLocal").addClass('hide');
    }

    if(access[2] != "Y"){
        $("#btnUpdateLocal").addClass('hide');
    }

    /*
     * Mask
     */


    /*
     * Validate
     */
    $("#create-local-form").validate({
        ignore:[],
        rules: {
            nome:{
                required: true,
                remote: {
                    url: path + '/scm/scmLocal/buscaNomeLocal',
                    type: "post"
                }
            },
        },
        messages: {
            nome:{
                required: "Campo obrigat&oacute;rio",
                remote: "O nome do local já existe!"
            },
        }
    });

    $("#update-local-form").validate({
        ignore:[],
        rules: {
            nome:{
                required: true,
                remote: {
                    url: path + '/scm/scmLocal/buscaNomeLocal',
                    data: {
                        'idlocal' : $('#idlocal').val()
                    },
                    type: "post"
                }
            },
        },
        messages: {
            nome:{
                required: "Campo obrigat&oacute;rio",
                remote: "O nome do local já existe!"
            },
        }
    });

    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/scm/scmLocal/index');

    $("#btnCreateLocal").click(function(){

        if (!$("#create-local-form").valid()) {
            return false ;
        }
        //
        $.ajax({
            type: "POST",
            url: path + '/scm/scmLocal/createLocal',
            dataType: 'json',
            data: {
                _token: $("#_token").val(),
                nome: $('#nome').val(),
            },
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-local');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if($.isNumeric(obj.idlocal)) {

                    var idlocal = obj.idlocal;
                    //
                    $('#modal-idlocal').html(idlocal);
                    $('#modal-nome').html(obj.nome);

                    $("#btnModalAlert").attr("href", path + '/scm/scmLocal/index');

                    $('#modal-alert-create').modal('show');
                } else {

                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-local');

                }
            }
        });
    });

    $("#btnUpdateLocal").click(function(){

        if (!$("#update-local-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/scm/scmLocal/updateLocal/idlocal/' + $('#idlocal').val(),
            dataType: 'json',
            data: {
                _token: $("#_token").val(),
                nome: $('#nome').val(),

            },
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-local');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));
                var idlocal = obj.idlocal;

                if(obj.status == 'OK' ) {

                    var idlocal = obj.idlocal;
                    console.log(idlocal);
                    $('#modal-notification').html('Local atualizado com sucesso');
                    $("#btn-modal-ok").attr("href", path + '/scm/scmLocal/index');
                    $("#tipo_alerta").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');

                } else {
                    console.log(idlocal);
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-local');

                }

            }

        });


    });



});

function sendNotification(transaction,codeRequest,hasAttachments)
{
    /*
     *
     * This was necessary because in some cases we have to send e-mail with the newly added attachments.
     * We only have access to these attachments after the execution of the dropzone.
     *
     */
    $.ajax({
        url : path + '/helpdezk/hdkTicket/sendNotification',
        type : 'POST',
        data : {
            transaction: transaction,
            code_request: codeRequest,
            has_attachment: hasAttachments
        },
        success : function(data) {

        },
        error : function(request,error)
        {

        }
    });

    return false ;

}

