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
        $("#btnCreateContaContabil").addClass('hide');
    }

    if(access[2] != "Y"){
        $("#btnUpdateContaContabil").addClass('hide');
    }

    /*
     *  Chosen
     */
    $("#idcentrocusto").chosen({ width: "95%",    no_results_text: "Nada encontrado!"});

    /*
     * Mask
     */
    $('#codigo').mask('9.99.999');


    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/scm/scmContaContabil/index');

    $("#btnCreateContaContabil").click(function(){

        if (!$("#create-contacontabil-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/scm/scmContaContabil/createContaContabil',
            dataType: 'json',
            data: {
                _token: $("#_token").val(),
                idcentrocusto: $('#idcentrocusto').val(),
                codigo: $('#codigo').val(),
                nome: $('#nome').val()

            },

            error: function (ret) {


                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-contacontabil');

            },

            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if($.isNumeric(obj.idcontacontabil)) {

                    var idcontacontabil = obj.idcontacontabil;

                    $('#modal-idcontacontabil').html(idcontacontabil);
                    $('#modal-nome').html(obj.nome);

                    $("#btnModalAlert").attr("href", path + '/scm/scmContaContabil/index');

                    $('#modal-alert-create').modal('show');
                } else {
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-contacontabil');
                }
            }
        });
    });


    $("#btnUpdateContaContabil").click(function(){

        if (!$("#update-contacontabil-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/scm/scmContaContabil/updateContaContabil/idcontacontabil/' + $('#idcontacontabil').val(),
            dataType: 'json',
            data: {
                _token: $("#_token").val(),
                idcentrocusto: $('#idcentrocusto').val(),
                codigo: $('#codigo').val(),
                nome: $('#nome').val(),

            },
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-contacontabil');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'OK' ) {

                    var idcontacontabil = obj.idcontacontabil;

                    $('#modal-notification').html('Conta Contábil atualizada com sucesso');
                    $("#btn-modal-ok").attr("href", path + '/scm/scmContaContabil/index');
                    $("#tipo_alerta").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');

                } else {

                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-contacontabil');

                }

            }

        });

    });

    /*
     * Validate
     */
    $("#create-contacontabil-form").validate({
        ignore:[],
        rules: {
            centrocusto: "required",
            codigo: {
                required: true,
                remote: {
                    url: path + '/scm/scmContaContabil/buscaContaContabil',
                    type: "post"
                }
            },
            nome: "required",

        },
        messages: {
            centrocusto: "Campo obrigat&oacute;rio",
            codigo:{
                required: "Campo obrigat&oacute;rio",
                remote: "O código CC já existe!"
            },
            nome: "Campo obrigat&oacute;rio",

        }
    });
    $("#update-contacontabil-form").validate({
        ignore:[],
        rules: {
            centrocusto: "required",
            codigo:{
                required: true,
                remote: {
                    url: path + '/scm/scmContaContabil/buscaContaContabil',
                    data: {
                        'idcontacontabil' : $('#idcontacontabil').val()
                    },
                    type: "post"
                }
            },
            nome: "required",

        },
        messages: {
            centrocusto: "Campo obrigat&oacute;rio",
            codigo:{
                required: "Campo obrigat&oacute;rio",
                remote: "O código CC já existe!"
            },
            nome: "Campo obrigat&oacute;rio",

        }
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