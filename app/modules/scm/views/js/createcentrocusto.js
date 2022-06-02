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
        $("#btnCreateCentroCusto").addClass('hide');
    }

    if(access[2] != "Y"){
        $("#btnUpdateCentroCusto").addClass('hide');
    }


    /*
     * Mask
     */
    $('#codigo').mask('99.99.9');
    /*
     * Validate
     */
    $("#create-centrocusto-form").validate({
        ignore:[],
        rules: {
            codigo: {
                required: true,
                remote: {
                    url: path + '/scm/scmCentroCusto/buscaCentroCusto',
                    type: "post"
                }
            },
            nome: "required",
            tipo: "required"
        },
        messages: {
            codigo:{
                required: "Campo obrigat&oacute;rio",
                remote: "O código CC já existe!"
            },
            nome: "Campo obrigat&oacute;rio",
            tipo: "Campo obrigat&oacute;rio"
        }
    });
    $("#update-centrocusto-form").validate({
        ignore:[],
        rules: {
            codigo:{
                required: true,
                remote: {
                    url: path + '/scm/scmCentroCusto/buscaCentroCusto',
                    data: {
                        'idcentrocusto' : $('#idcentrocusto').val()
                    },
                    type: "post"
                }
            },
            nome: "required",
            tipo: "required"
        },
        messages: {
            codigo:{
                required: "Campo obrigat&oacute;rio",
                remote: "O código CC já existe!"
            },
            nome: "Campo obrigat&oacute;rio",
            tipo: "Campo obrigat&oacute;rio"
        }
    });
    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/scm/scmCentroCusto/index');

    $("#btnCreateCentroCusto").click(function(){

        if (!$("#create-centrocusto-form").valid()) {
            return false ;
        }
        //
        $.ajax({
            type: "POST",
            url: path + '/scm/scmCentroCusto/createCentroCusto',
            dataType: 'json',
            data: {
                _token: $("#_token").val(),
                codigo: $('#codigo').val(),
                nome: $('#nome').val(),
                tipo: $('#tipo').val()
            },
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-centrocusto');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if($.isNumeric(obj.idcentrocusto)) {

                    var idcentrocusto = obj.idcentrocusto;
                    //
                    $('#modal-idcentrocusto').html(idcentrocusto);
                    $('#modal-nome').html(obj.nome);

                    $("#btnModalAlert").attr("href", path + '/scm/scmCentroCusto/index');

                    $('#modal-alert-create').modal('show');
                } else {

                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-centrocusto');

                }
            }
        });
    });

    $("#btnUpdateCentroCusto").click(function(){

        if (!$("#update-centrocusto-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/scm/scmCentroCusto/updateCentroCusto/idcentrocusto/' + $('#idcentrocusto').val(),
            dataType: 'json',
            data: {
                _token: $("#_token").val(),
                codigo: $('#codigo').val(),
                nome: $('#nome').val(),
                tipo: $('#tipo').val()
            },
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-centrocusto');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));
                var idcentrocusto = obj.idcentrocusto;
                console.log(idcentrocusto+'asdasd');
                if(obj.status == 'OK' ) {

                    var idcentrocusto = obj.idcentrocusto;
                    console.log(idcentrocusto);
                    $('#modal-notification').html('Centro de Custo atualizado com sucesso');
                    $("#btn-modal-ok").attr("href", path + '/scm/scmCentroCusto/index');
                    $("#tipo_alerta").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');

                } else {
                    console.log(idcentrocusto);
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-centrocusto');

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

