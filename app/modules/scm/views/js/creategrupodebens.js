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
        $("#btnCreateGrupoDeBens").addClass('hide');
    }

    if(access[2] != "Y"){
        $("#btnUpdateGrupoDeBens").addClass('hide');
    }

    /*
     *  Chosen
     */
    $(".contacontabil").chosen({ width: "95%",    no_results_text: "Nada encontrado!"});

    /*
     * Mask
     */
    $('#depreciacaoporcentagem').mask('999.99');


    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/scm/scmGrupoDeBens/index');

    $("#btnCreateGrupoDeBens").click(function(){

        if (!$("#create-grupodebens-form").valid()) {
            return false ;
        }

        console.log($("#create-grupodebens-form").serialize());

        $.ajax({
            type: "POST",
            url: path + '/scm/scmGrupoDeBens/createGrupoDeBens',
            dataType: 'json',
            data: $("#create-grupodebens-form").serialize(),

            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-grupodebens');
            },

            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if($.isNumeric(obj.idgrupodebens)) {

                    var idgrupodebens = obj.idgrupodebens;

                    $('#modal-idgrupodebens').html(idgrupodebens);
                    $('#modal-descricao').html(obj.descricao);

                    $("#btnModalAlert").attr("href", path + '/scm/scmGrupoDeBens/index');

                    $('#modal-alert-create').modal('show');
                } else {
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-grupodebens');
                }
            }
        });
    });


    $("#btnUpdateGrupoDeBens").click(function(){

        if (!$("#update-grupodebens-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/scm/scmGrupoDeBens/updateGrupoDeBens/idgrupodebens/' + $('#idgrupodebens').val(),
            dataType: 'json',
            data: $("#update-grupodebens-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-grupodebens');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'OK' ) {

                    var idgrupodebens = obj.idgrupodebens;

                    $('#modal-notification').html('Grupo de bens atualizado com sucesso');
                    $("#btn-modal-ok").attr("href", path + '/scm/scmGrupoDeBens/index');
                    $("#tipo_alerta").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');

                } else {

                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-grupodebens');

                }

            }

        });


    });

    if ($('input[name="depreciacao"]:checked').val() == 'S') {
        $('.depreciacao').show();
        $('.depreciacaoacumulada').show();
    } else {
        $('.depreciacao').hide();
        $('.depreciacaoacumulada').hide();
    }

    $('input[name="depreciacao"]').change(function () {

        if ($('input[name="depreciacao"]:checked').val() == 'S') {
            $('.depreciacao').show();
            $('.depreciacaoacumulada').show();
        } else {
            $('#iddepreciacaoconta').val('');
            $('.depreciacao').hide();
            $('#iddepreciacaoacumuladaconta').val('');
            $('.depreciacaoacumulada').hide();
        }

        console.log( $('#iddepreciacaoconta'));
    });

    /*
     * Validate
     */
    $("#create-grupodebens-form").validate({
        ignore:[],
        rules: {
            descricao:              "required",
            depreciacaoporcentagem: {required:function(element){ return $('input[name="depreciacao"]:checked').val() == 'S'}},

        },
        messages: {
            descricao:              "Campo obrigat&oacute;rio",
            depreciacaoporcentagem: "Campo obrigat&oacute;rio",

        }
    });
    $("#update-grupodebens-form").validate({
        ignore:[],
        rules: {
            descricao:             "required",
            depreciacaoporcentagem:"required",


        },
        messages: {
            descricao:              "Campo obrigat&oacute;rio",
            depreciacaoporcentagem: "Campo obrigat&oacute;rio",


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