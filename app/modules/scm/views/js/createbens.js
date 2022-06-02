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
        $("#btnCreateBens").addClass('hide');
    }

    if(access[2] != "Y"){
        $("#btnUpdateBens").addClass('hide');
    }

    /*
     *  Chosen
     */
    $(".marca").chosen({ width: "95%",    no_results_text: "Nada encontrado!"})
    $(".estado").chosen({ width: "95%",    no_results_text: "Nada encontrado!"})
    $(".local").chosen({ width: "95%",    no_results_text: "Nada encontrado!"})
    $(".grupodebens").chosen({ width: "95%",    no_results_text: "Nada encontrado!"})
    $(".fornecedores").chosen({ width: "95%",    no_results_text: "Nada encontrado!"})

    /*
     * Mask
     */


    /*
     * Combos
     */
    var objBensData = {
        changeMarca: function() {
            $.post(path+"/scm/scmBens/ajaxMarca",
                function(valor){
                    $("#idmarca").html(valor);
                    $("#idmarca").trigger("chosen:updated");
                    return false;
                })
            return false ;
        },
        changeEstado: function() {
            $.post(path+"/scm/scmBens/ajaxEstado",
                function(valor){
                    $("#idestado").html(valor);
                    $("#idestado").trigger("chosen:updated");
                    return false;
                })
            return false ;
        },
        changeLocal: function() {
            $.post(path+"/scm/scmBens/ajaxLocal",
                function(valor){
                    $("#idlocal").html(valor);
                    $("#idlocal").trigger("chosen:updated");
                    return false;
                })
            return false ;
        }

    }

    /*
     * Validate
     */
    $("#create-bens-form").validate({
        ignore:[],
        rules: {
            descricao:        "required",
            numeropatrimonio: "required",
            dataaquisicao: "remote",
            dataaquisicao: "remote",

        },
        messages: {
            descricao:        "Campo obrigat&oacute;rio",
            numeropatrimonio: "Campo obrigat&oacute;rio",
            dataaquisicao: "Data formato incorreto",
            datagarantia: "Data formato incorreto",

        }
    });
    $("#update-bens-form").validate({
        ignore:[],
        rules: {
            descricao:        "required",
            numeropatrimonio: "required",
            dataaquisicao: "remote",
            dataaquisicao: "remote",
        },
        messages: {
            descricao:        "Campo obrigat&oacute;rio",
            numeropatrimonio: "Campo obrigat&oacute;rio",
            dataaquisicao: "Data formato incorreto",
            datagarantia: "Data formato incorreto",
        }
    });
    $("#marca-form").validate({
        ignore:[],
        rules: {
            modal_marca_nome: {
                required: true,
                remote: {
                    url: path + '/scm/scmBens/buscaMarca',
                    type: "post"
                }
            },
        },
        messages: {
            modal_marca_nome: {
                required: "Campo obrigat&oacute;rio.",
                remote: "A marca já existe!"
            },
        }
    });
    $("#estado-form").validate({
        ignore:[],
        rules: {
            modal_estado_nome: {
                required: true,
                remote: {
                    url: path + '/scm/scmBens/buscaEstado',
                    type: "post"
                }
            },
        },
        messages: {
            modal_estado_nome: {
                required: "Campo obrigat&oacute;rio.",
                remote: "O estado já existe!"
            },
        }
    });
    $("#local-form").validate({
        ignore:[],
        rules: {
            modal_local_nome: {
                required: true,
                remote: {
                    url: path + '/scm/scmBens/buscaLocal',
                    type: "post"
                }
            },
        },
        messages: {
            modal_local_nome: {
                required: "Campo obrigat&oacute;rio.",
                remote: "O local já existe!"
            },
        }
    });


    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/scm/scmBens/index');

    $("#btnCreateBens").click(function(){

        if (!$("#create-bens-form").valid()) {
            return false ;
        }

        console.log($("#create-bens-form").serialize());

        $.ajax({
            type: "POST",
            url: path + '/scm/scmBens/createBens',
            dataType: 'json',
            data: $("#create-bens-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-bens');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if($.isNumeric(obj.idbens)) {

                    var idbens = obj.idbens;
                    //
                    $('#modal-idbens').html(idbens);
                    $('#modal-descricao').html(obj.descricao);

                    $("#btnModalAlert").attr("href", path + '/scm/scmBens/index');

                    $('#modal-alert-create').modal('show');
                } else {

                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-bens');

                }
            }
        });
    });

    $("#btnUpdateBens").click(function(){

        if (!$("#update-bens-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/scm/scmBens/updateBens/idbens/' + $('#idbens').val(),
            dataType: 'json',
            data: $("#update-bens-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-bens');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'OK' ) {

                    var idbens = obj.idbens;

                    $('#modal-notification').html('Bem atualizado com sucesso');
                    $("#btn-modal-ok").attr("href", path + '/scm/scmBens/index');
                    $("#tipo_alerta").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');

                } else {

                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-bens');

                }

            }

        });


    });

    $("#btnAddMarca").click(function() {
        $('#modal-form-marca').modal('show');
    });

    $("#btnSendMarca").click(function() {
        if (!$("#marca-form").valid()) {
            console.log('nao validou') ;
            return false;
        }
        $.ajax({
            type: "POST",
            url: path + '/scm/scmBens/createMarca',
            dataType: 'json',
            data: {
                _token: $("#_token").val(),
                nome:  $('#modal_marca_nome').val()
            },
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-marca');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if($.isNumeric(obj.idmarca)) {
                    modalAlertMultiple('success','Marca inclu&iacute;do com sucesso !','alert-marca');
                    objBensData.changeMarca();
                } else {
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-marca');
                }
            }
        });

    });

    $("#btnAddEstado").click(function() {
        $('#modal-form-estado').modal('show');
    });

    $("#btnSendEstado").click(function() {
        if (!$("#estado-form").valid()) {
            console.log('nao validou') ;
            return false;
        }
        $.ajax({
            type: "POST",
            url: path + '/scm/scmBens/createEstado',
            dataType: 'json',
            data: {
                _token: $("#_token").val(),
                nome:  $('#modal_estado_nome').val()
            },
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-estado');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if($.isNumeric(obj.idestado)) {
                    modalAlertMultiple('success','Estado inclu&iacute;do com sucesso !','alert-estado');
                    objBensData.changeEstado();
                } else {
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-estado');
                }
            }
        });

    });

    $("#btnAddLocal").click(function() {
        $('#modal-form-local').modal('show');
    });

    $("#btnSendLocal").click(function() {
        if (!$("#local-form").valid()) {
            console.log('nao validou') ;
            return false;
        }
        $.ajax({
            type: "POST",
            url: path + '/scm/scmBens/createLocal',
            dataType: 'json',
            data: {
                _token: $("#_token").val(),
                nome:  $('#modal_local_nome').val()
            },
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-local');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if($.isNumeric(obj.idlocal)) {
                    modalAlertMultiple('success','Local inclu&iacute;do com sucesso !','alert-local');
                    objBensData.changeLocal();
                } else {
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-local');
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

