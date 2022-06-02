var global_idperson = '';
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
        $("#btnCreateTransportadora").addClass('hide');
    }

    if(access[2] != "Y"){
        $("#btnUpdateTransportadora").addClass('hide');
    }

    /*
    *  Chosen
    */
    $("#pais").chosen({ width: "95%", no_results_text: "Nada encontrado!"});
    $("#estado").chosen({ width: "95%",   no_results_text: "Nada encontrado!"});
    $("#cidade").chosen({ width: "95%",    no_results_text: "Nada encontrado!"});
    $("#bairro").chosen({ width: "95%",    no_results_text: "Nada encontrado!"});
    $("#tipologra").chosen({ width: "95%", no_results_text: "Nada encontrado!"});

    /*
     * Mask
     */
    $('#dtnasc').mask('00/00/0000');
    $('#numero').mask('0000');
    $('#comercial').mask('(00) 0000-0000');
    $('#phone_number').mask('(00) 0000-0000');
    $('#cel_phone').mask('(00) 00000-0000');
    $('#cep').mask('00000-000');
    $('#cpf').mask('000.000.000-00');
    $('#rg').mask('00.000.000-0');
    $('#ein_cnpj').mask('00.000.000/0000-00');

    /*
     * Combos
     */

    var objPersonData = {
        changeState: function() {
            var countryId = $("#pais").val();
            $.post(path+"/scm/scmTransportadora/ajaxStates",{countryId: countryId},
                function(valor){
                    $("#estado").html(valor);
                    $("#estado").trigger("chosen:updated");
                    return objPersonData.changeCity();
                })
        },
        changeLougradoro: function() {
            $.post(path+"/scm/scmTransportadora/ajaxlogradouro",
                function(valor){
                    $("#tipologra").html(valor);
                    $("#tipologra").trigger("chosen:updated");
                    return false;
                })
            return false ;
        },
        changeCity: function() {
            var stateId = $("#estado").val();
            $.post(path+"/scm/scmTransportadora/ajaxCities",{stateId: stateId},
                function(valor) {
                    $("#cidade").html(valor);
                    $("#cidade").trigger("chosen:updated");
                    return objPersonData.changeNeighborhood();
                });
        },
        changeNeighborhood: function() {
            var cityId = $("#cidade").val();
            $.post(path+"/scm/scmTransportadora/ajaxNeighborhood",{cityId: cityId},
                function(valor){
                    $("#bairro").html(valor);
                    $("#bairro").trigger("chosen:updated");
                    return false;
                })
            return false ;
        }

    }
    $("#pais").change(function(){
        objPersonData.changeState();
    });

    $("#estado").change(function(){
        objPersonData.changeCity();
    });

    $("#cidade").change(function(){
        objPersonData.changeNeighborhood();
    });
    $("#logradouro").change(function(){
        objPersonData.changeLougradoro();
    });

    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/scm/scmTransportadora/index');

    $("#btnCreateTransportadora").click(function () {

        if (!$("#create-transportadora-form").valid()) {
            return false;
        }
        //
        $.ajax({
            type: "POST",
            url: path + '/scm/scmTransportadora/createTransportadora',
            dataType: 'json',
            data: {
                _token: $("#_token").val(),
                nomefisico: $('#nomefisico').val(),
                razaosocial: $('#razaosocial').val(),
                ein_cnpj: $("#ein_cnpj").val(),
                iestadual: $("#iestadual").val(),
                rg: $('#rg').val().replace(/[^0-9]/gi, ''),
                cpf: $('#cpf').val(),
                emailfisica: $("#emailfisica").val(),
                emailjuridica: $("#emailjuridica").val(),
                email: $('#email').val(),
                phone_number: $('#phone_number').val().replace(/[^0-9]/gi, ''),
                cel_phone: $('#cel_phone').val().replace(/[^0-9]/gi, ''),
                pais: $('#pais').val(),
                estado: $('#estado').val(),
                cidade: $('#cidade').val(),
                cep: $('#cep').val().replace(/[^0-9]/gi, ''),
                bairro: $('#bairro').val(),
                tipologra: $('#tipologra').val(),
                endereco: $('#endereco').val(),
                tipo: $('input[name="tipo"]:checked').val(),
                numero: $('#numero').val().replace(/[^0-9]/gi, ''),
                complemento: $('#complemento').val()

            },

            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-transportadora');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));
                console.log(obj);
                if($.isNumeric(obj.idperson)) {

                    var idperson = obj.idperson;
                    var nometransportadora = obj.nometransportadora;

                    $('#modal-idperson').html(idperson);
                    $('#modal-nome').html(nometransportadora);

                    $("#btnModalAlert").attr("href", path + '/scm/scmTransportadora/index');

                    $('#modal-alert-create').modal('show');
                } else {
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-transportadora');
                }
            }
        });
    });


    $("#btnUpdateTransportadora").click(function () {

        if (!$("#update-transportadora-form").valid()) {
            return false;
        }

        $.ajax({
            type: "POST",
            url: path + '/scm/scmTransportadora/updateTransportadora/idperson/' + $('#idperson').val(),
            dataType: 'json',
            data: {
                _token: $("#_token").val(),
                nomefisico: $('#nomefisico').val(),
                razaosocial: $('#razaosocial').val(),
                ein_cnpj: $("#ein_cnpj").val(),
                iestadual: $("#iestadual").val(),
                rg: $('#rg').val().replace(/[^0-9]/gi, ''),
                ssn_cpf: $('#cpf').val(),
                emailfisica: $("#emailfisica").val(),
                emailjuridica: $("#emailjuridica").val(),
                email: $('#email').val(),
                phone_number: $('#phone_number').val().replace(/[^0-9]/gi, ''),
                cel_phone: $('#cel_phone').val().replace(/[^0-9]/gi, ''),
                pais: $('#pais').val(),
                estado: $('#estado').val(),
                cidade: $('#cidade').val(),
                cep: $('#cep').val().replace(/[^0-9]/gi, ''),
                bairro: $('#bairro').val(),
                tipologra: $('#tipologra').val(),
                endereco: $('#endereco').val(),
                tipo: $('input[name="tipo"]:checked').val(),
                numero: $('#numero').val().replace(/[^0-9]/gi, ''),
                complemento: $('#complemento').val()
            },

            error: function (ret) {
                modalAlertMultiple('danger', 'N&atilde;o foi poss&iacute;vel atualizar !', 'alert-create-transportadora');
            },
            success: function (ret) {

                console.log(ret);

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if (obj.status == 'OK') {

                    var idperson = obj.idperson;

                    $('#modal-notification').html('Transportadora atualizada com sucesso');
                    $("#btn-modal-ok").attr("href", path + '/scm/scmTransportadora/index');
                    $("#tipo_alerta").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');

                } else {

                    modalAlertMultiple('danger', 'N&atilde;o foi poss&iacute;vel atualizar !', 'alert-create-transportadora');

                }

            }

        });

    });

    $("#btnAddBairro").click(function(){
        idcidade = $("#cidade").val();
        $('#hidden-idcidade').val(idcidade);
        $('#modal-cidade-nome').val($("#cidade").find('option:selected').text());
        $('#modal-form-bairro').modal('show');
    });

    $("#btnSendBairro").click(function(){
        if (!$("#bairro-form").valid()) {
           console.log('nao validou') ;
           return false;
        }
        $.ajax({
            type: "POST",
            url: path + '/scm/scmTransportadora/createBairro',
            dataType: 'json',
            data: {
                _token: $("#_token").val(),
                cidade: $('#hidden-idcidade').val(),
                bairro: $('#modal_bairro_nome').val()
            },
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-bairro');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if($.isNumeric(obj.idbairro)) {
                    modalAlertMultiple('success','Bairro inclu&iacute;do com sucesso !','alert-bairro');
                    objPersonData.changeNeighborhood();
                } else {
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-bairro');
                }
            }
        });

    });

    $("#btnAddEstado").click(function(){
        idpais = $("#pais").val();
        $('#hidden-idpais').val(idpais);
        $('#modal-pais-nome').val($("#pais").find('option:selected').text());
        $('#modal-form-estado').modal('show');
    });

    $("#btnSendEstado").click(function(){
        if (!$("#estado-form").valid()) {
            console.log('nao validou') ;
            return false;
        }
        $.ajax({
            type: "POST",
            url: path + '/scm/scmTransportadora/createEstado',
            dataType: 'json',
            data: {
                _token: $("#_token").val(),
                pais: $('#hidden-idpais').val(),
                estado: $('#modal_estado_nome').val()
            },
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-estado');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if($.isNumeric(obj.idestado)) {
                    modalAlertMultiple('success','Estado inclu&iacute;do com sucesso !','alert-estado');
                    objPersonData.changeState();
                } else {
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-estado');
                }
            }
        });

    });

    $("#btnAddCidade").click(function(){
        idestado = $("#estado").val();
        $('#hidden-idestado').val(idestado);
        $('#modal-estado-nome').val($("#estado").find('option:selected').text());
        $('#modal-form-cidade').modal('show');
    });

    $("#btnSendCidade").click(function(){
        if (!$("#cidade-form").valid()) {
            console.log('nao validou') ;
            return false;
        }

        $.ajax({
            type: "POST",
            url: path + '/scm/scmTransportadora/createCidade',
            dataType: 'json',
            data: {
                _token: $("#_token").val(),
                estado: $('#hidden-idestado').val(),
                cidade: $('#modal_cidade_nome').val()
            },
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-cidade');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if($.isNumeric(obj.idcidade)) {
                    modalAlertMultiple('success','Cidade inclu&iacute;do com sucesso !','alert-cidade');
                    objPersonData.changeCity();
                } else {
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-cidade');
                }
            }
        });

    });

    $("#btnAddLogradouro").click(function(){
        $('#modal-form-logradouro').modal('show');
    });

    $("#btnSendLogradouro").click(function(){
        if (!$("#logradouro-form").valid()) {
            console.log('nao validou') ;
            return false;
        }
        $.ajax({
            type: "POST",
            url: path + '/scm/scmTransportadora/createLogradouro',
            dataType: 'json',
            data: {
                _token: $("#_token").val(),
                nome:  $('#modal-logradouro-nome').val()
            },
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-logradouro');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if($.isNumeric(obj.idlogradouro)) {
                    modalAlertMultiple('success','Logradouro inclu&iacute;do com sucesso !','alert-logradouro');
                    objPersonData.changeLougradoro();
                } else {
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-logradouro');
                }
            }
        });

    });

        $('#fisica').hide();
        $('#fisica1').hide();

        if($('input[name="tipo"]:checked').val() == 1){
            $('#fisica').show();
            $('#fisica1').show();
            $('#juridica').hide();
            $('#juridica1').hide();
        }

        $('input[name="tipo"]').change(function () {

            if ($('input[name="tipo"]:checked').val() == "2") {
                $('#juridica').show();
                $('#juridica1').show();
                $('#fisica').hide();
                $('#fisica1').hide();
            } else {
                $('#fisica').show();
                $('#fisica1').show();
                $('#juridica').hide();
                $('#juridica1').hide();
            }

        });

        /*
         * Validate
         */
    $("#create-transportadora-form").validate({
        ignore:[],
        rules: {
            email: "remote",
            emailfisica: "remote",
            emailjuridica: "remote",
            ein_cnpj: {
                remote: {
                    url: path + '/scm/scmTransportadora/buscacnpj',
                    type: "post"
                }
            },
            cpf: {
                remote: {
                    url: path + '/scm/scmTransportadora/buscacpf',
                    type: "post"
                }
            },
        },
        messages: {
            email: "E-mail formato incorreto",
            emailfisica: "E-mail formato incorreto",
            emailjuridica: "E-mail formato incorreto",
            ein_cnpj:{
                remote: "O CNPJ já existe!"
            },
            cpf:{
                remote: "O CPF já existe!"
            },

        }
    });
    $("#update-transportadora-form").validate({
        ignore:[],
        rules: {
            email: "remote",
            emailfisica: "remote",
            emailjuridica: "remote",
            ein_cnpj: {
                remote: {
                    url: path + '/scm/scmTransportadora/buscacnpj',
                    data: {
                        'idperson' : $('#idperson').val()
                    },
                    type: "post"
                }
            },
            cpf: {
                remote: {
                    url: path + '/scm/scmTransportadora/buscacpf',
                    data: {
                        'idperson' : $('#idperson').val()
                    },
                    type: "post"
                }
            },

        },
        messages: {
            email: "E-mail formato incorreto",
            emailfisica: "E-mail formato incorreto",
            emailjuridica: "E-mail formato incorreto",
            ein_cnpj:{
                remote: "O CNPJ já existe!"
            },
            cpf:{
                remote: "O CPF já existe!"
            },

        }
    });
    $("#bairro-form").validate({
        ignore:[],
        rules: {modal_bairro_nome: "required"},
        messages: {modal_bairro_nome: "Nome da cidade obrigat&oacute;rio."}
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


