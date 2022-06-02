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
        $("#btnCreateFornecedor").addClass('hide');
    }

    if(access[2] != "Y"){
        $("#btnUpdateFornecedor").addClass('hide');
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
    //$('#rg').mask('00.000.000-0');
    $('#ein_cnpj').mask('00.000.000/0000-00');

    /*
     * Combos
     */

    var objPersonData = {
        changeState: function() {
            var countryId = $("#pais").val();
            $.post(path+"/scm/scmFornecedor/ajaxStates",{countryId: countryId},
                function(valor){
                    $("#estado").html(valor);
                    $("#estado").trigger("chosen:updated");
                    return objPersonData.changeCity();
                })
        },
        changeLougradoro: function() {
            $.post(path+"/scm/scmFornecedor/ajaxlogradouro",
                function(valor){
                    $("#tipologra").html(valor);
                    $("#tipologra").trigger("chosen:updated");
                    return false;
                })
            return false ;
        },
        changeCity: function() {
            var stateId = $("#estado").val();
            $.post(path+"/scm/scmFornecedor/ajaxCities",{stateId: stateId},
                function(valor) {
                    $("#cidade").html(valor);
                    $("#cidade").trigger("chosen:updated");
                    return objPersonData.changeNeighborhood();
                });
        },
        changeNeighborhood: function() {
            var cityId = $("#cidade").val();
            $.post(path+"/scm/scmFornecedor/ajaxNeighborhood",{cityId: cityId},
                function(valor){
                    $("#bairro").html(valor);
                    $("#bairro").trigger("chosen:updated");
                    return false;
                })
            return false ;
        },
        checkCPFCNPJ: function(typecheck,idperson,valuecheck) {
            if(valuecheck != ''){
                $.ajax({
                    type: "POST",
                    url: path+"/scm/scmFornecedor/checkCPFCNPJ",
                    dataType: 'json',
                    data: {typecheck:typecheck,idperson:idperson,valuecheck:valuecheck},
                    error: function (ret) {
                        modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-fornecedor');
                    },
                    success: function(ret){

                        var obj = jQuery.parseJSON(JSON.stringify(ret));
                        //console.log(obj);

                        if(!obj.status){
                            if($.isNumeric(obj.idfornecedor)) {
                                $("#btn-modal-ok").attr("href", 'javascript:alertClose()');
                                $('#modal-notification').html(obj.message + '<br> ID Fornecedor: ' + obj.idfornecedor +'<br> Nome: '+ obj.namefornecedor);
                                $("#tipo-alert").attr('class', 'alert alert-danger');
                                $('#modal-alert').modal('show');
                            }else{
                                $("#btn-modal-ok").attr("href", 'javascript:alertClose()');
                                $('#modal-notification').html(obj.message);
                                $("#tipo-alert").attr('class', 'alert alert-danger');
                                $('#modal-alert').modal('show');
                            }

                        }else {
                            return false ;
                        }
                    }
                });
            }
            return false ;
        },
        checkCNPJ: function() {
            var cityId = $("#cidade").val();
            $.post(path+"/scm/scmFornecedor/ajaxNeighborhood",{cityId: cityId},
                function(valor){
                    $("#bairro").html(valor);
                    $("#bairro").trigger("chosen:updated");
                    return false;
                })
            return false ;
        },
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

    $("#cpf").on('blur',function(){
        objPersonData.checkCPFCNPJ('F',$('#idperson').val(),$('#cpf').val());
    });

    $("#ein_cnpj").on('blur',function(){
        objPersonData.checkCPFCNPJ('J',$('#idperson').val(),$('#ein_cnpj').val());
    });

    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/scm/scmFornecedor/index');

    $("#btnCreateFornecedor").click(function () {

        if (!$("#create-fornecedor-form").valid()) {
            return false;
        }
        //
        $.ajax({
            type: "POST",
            url: path + '/scm/scmFornecedor/createFornecedor',
            dataType: 'json',
            data: {
                _token: $("#_token").val(),
                nomefisico: $('#nomefisico').val(),
                razaosocial: $('#razaosocial').val(),
                nomefantasia: $('#nomefantasia').val(),
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
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-fornecedor');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));
                console.log(obj);
                if($.isNumeric(obj.idperson)) {

                    var idperson = obj.idperson;
                    var nomefornecedor = obj.nomefornecedor;

                    $('#modal-idperson').html(idperson);
                    $('#modal-nome').html(nomefornecedor);

                    $("#btnModalAlert").attr("href", path + '/scm/scmFornecedor/index');

                    $('#modal-alert-create').modal('show');
                } else {
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-fornecedor');
                }
            }
        });
    });

    $("#btnUpdateFornecedor").click(function () {

        if (!$("#update-fornecedor-form").valid()) {
            return false;
        }

        $.ajax({
            type: "POST",
            url: path + '/scm/scmFornecedor/updateFornecedor/idperson/' + $('#idperson').val(),
            dataType: 'json',
            data: {
                _token: $("#_token").val(),
                nomefisico: $('#nomefisico').val(),
                razaosocial: $('#razaosocial').val(),
                nomefantasia: $('#nomefantasia').val(),
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
                modalAlertMultiple('danger', 'N&atilde;o foi poss&iacute;vel atualizar !', 'alert-create-fornecedor');
            },
            success: function (ret) {

                console.log(ret);

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if (obj.status == 'OK') {

                    var idperson = obj.idperson;

                    $('#modal-notification').html('Fornecedor atualizado com sucesso');
                    $("#btn-modal-ok").attr("href", path + '/scm/scmFornecedor/index');
                    $("#tipo_alerta").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');

                } else {

                    modalAlertMultiple('danger', 'N&atilde;o foi poss&iacute;vel atualizar !', 'alert-create-fornecedor');

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
            url: path + '/scm/scmFornecedor/createBairro',
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
            url: path + '/scm/scmFornecedor/createEstado',
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
            url: path + '/scm/scmFornecedor/createCidade',
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
            url: path + '/scm/scmFornecedor/createLogradouro',
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
    $("#create-fornecedor-form").validate({
        ignore:[],
        rules: {
            razaosocial: {required:function(element){return $('input[name="tipo"]:checked').val() == 2;}},
            nomefisico: {required:function(element){return $('input[name="tipo"]:checked').val() == 1;}},
            ein_cnpj: {
                required:function(element){return $('input[name="tipo"]:checked').val() == 2;},
                remote: {
                    url: path+"/scm/scmFornecedor/isvalidCPFCNPJ",
                    type: 'post',
                    data: { typecheck:"J",
                            idperson:function(){return $('#idperson').val();}
                    }
                }
            },
            cpf: {
                required:function(element){return $('input[name="tipo"]:checked').val() == 1;},
                remote: {
                    url: path+"/scm/scmFornecedor/isvalidCPFCNPJ",
                    type: 'post',
                    data: { typecheck:"F",
                        idperson:function(){return $('#idperson').val();}
                    }
                }
            },
            emailjuridica: {
                required:function(element){return $('input[name="tipo"]:checked').val() == 2;},
                email: true
            },
            emailfisica: {
                required:function(element){return $('input[name="tipo"]:checked').val() == 1;},
                email: true
            }
        },
        messages: {
            razaosocial: {required:"Campo Obrigat&oacute;rio"},
            nomefisico: {required:"Campo Obrigat&oacute;rio"},
            ein_cnpj: {
                required:"Campo Obrigat&oacute;rio"
            },
            cpf: {
                required:"Campo Obrigat&oacute;rio"
            },
            emailfisica: {
                required:"Campo Obrigat&oacute;rio",
                email:"E-mail formato incorreto"
            },
            emailjuridica: {
                required:"Campo Obrigat&oacute;rio",
                email:"E-mail formato incorreto"
            }
        }
    });

    $("#update-fornecedor-form").validate({
        ignore:[],
        rules: {
            razaosocial: {required:function(element){return $('input[name="tipo"]:checked').val() == 2;}},
            nomefisico: {required:function(element){return $('input[name="tipo"]:checked').val() == 1;}},
            ein_cnpj: {
                required:function(element){return $('input[name="tipo"]:checked').val() == 2;},
                remote: {
                    param:{
                        url: path+"/scm/scmFornecedor/isvalidCPFCNPJ",
                        type: 'post',
                        data: { typecheck:"J",
                            idperson:function(){return $('#idperson').val();}
                        }
                    },
                    depends:function(){return $('input[name="tipo"]:checked').val() == 2;},
                }
            },
            cpf: {
                required:function(element){return $('input[name="tipo"]:checked').val() == 1;},
                remote: {
                    param: {
                        url: path+"/scm/scmFornecedor/isvalidCPFCNPJ",
                        type: 'post',
                        data: { typecheck:"F",
                            idperson:function(){return $('#idperson').val();}
                        }
                    },
                    depends:function(){return $('input[name="tipo"]:checked').val() == 1;},
                }
            },
            emailjuridica: {
                required:function(element){return $('input[name="tipo"]:checked').val() == 2;},
                email: true
            },
            emailfisica: {
                required:function(element){return $('input[name="tipo"]:checked').val() == 1;},
                email: true
            }
        },
        messages: {
            razaosocial: {required:"Campo Obrigat&oacute;rio"},
            nomefisico: {required:"Campo Obrigat&oacute;rio"},
            ein_cnpj: {
                required:"Campo Obrigat&oacute;rio"
            },
            cpf: {
                required:"Campo Obrigat&oacute;rio"
            },
            emailfisica: {
                required:"Campo Obrigat&oacute;rio",
                email:"E-mail formato incorreto"
            },
            emailjuridica: {
                required:"Campo Obrigat&oacute;rio",
                email:"E-mail formato incorreto"
            }
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

function alertClose()
{
    $('#modal-alert').modal('hide');

}


