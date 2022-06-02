var global_idperson = '';
$(document).ready(function () {

    countdown.start(timesession);




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
    $('#telefone').mask('(00) 0000-0000');
    $('#celular').mask('(00) 00000-0000');
    $('#cep').mask('00000-000');
    $('#cpf').mask('000.000.000-00');

    /*
     * Autocomplete
     */
    $("#endereco").autocomplete({
        source:[{
            url: path+"/spm/spmCadastroAtleta/completeStreet/search/%QUERY%",
            type: 'remote'
        }
        ],
        accents: true,
        replaceAccentsForRemote: false,
        minLength: 1
    });
    /*
     * Combos
     */
    var formPersonData = $(document.getElementById("create-atleta-form"));
    var objPersonData = {
        changeState: function() {
            var countryId = $("#pais").val();
            $.post(path+"/spm/spmCadastroAtleta/ajaxStates",{countryId: countryId},
                function(valor){
                    $("#estado").html(valor);
                    $("#estado").trigger("chosen:updated");
                    return objPersonData.changeCity();
                })
        },
        changeCity: function() {
            var stateId = $("#estado").val();
            $.post(path+"/spm/spmCadastroAtleta/ajaxCities",{stateId: stateId},
                function(valor) {
                    $("#cidade").html(valor);
                    $("#cidade").trigger("chosen:updated");
                    return objPersonData.changeNeighborhood();
                });
        },
        changeNeighborhood: function() {
            var cityId = $("#cidade").val();
            $.post(path+"/spm/spmCadastroAtleta/ajaxNeighborhood",{cityId: cityId},
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

    //$('[data-toggle="tooltip"]').tooltip();
    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/spm/spmCadastroAtleta/index');

    $("#btnCreateAtleta").click(function(){

        if (!$("#create-atleta-form").valid()) {
            return false ;
        }

        //
            $.ajax({
                type: "POST",
                url: path + '/spm/spmCadastroAtleta/createAtleta',
                dataType: 'json',
                data: {
                    apelido: $('#apelido').val(),
                    nome: $('#nome').val(),
                    posicao: $('#posicao').val(),
                    condicao: $('#condicao').val(),
                    departamento: $('#departamento').val(),
                    cpf: $('#cpf').val().replace(/[^0-9]/gi, ''),
                    dtnasc: $('#dtnasc').val(),
                    email: $('#email').val(),
                    telefone: $('#telefone').val().replace(/[^0-9]/gi, ''),
                    celular:$('#celular').val().replace(/[^0-9]/gi, ''),
                    pais: $('#pais').val(),
                    estado: $('#estado').val(),
                    cidade: $('#cidade').val(),
                    cep: $('#cep').val().replace(/[^0-9]/gi, ''),
                    bairro: $('#bairro').val(),
                    tipologra: $('#tipologra').val(),
                    endereco: $('#endereco').val(),
                    numero: $('#numero').val().replace(/[^0-9]/gi, ''),
                    complemento: $('#complemento').val()
                },
                error: function (ret) {
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-atleta');
                },
                success: function(ret){

                    var obj = jQuery.parseJSON(JSON.stringify(ret));

                    if($.isNumeric(obj.idperson)) {

                        var idperson = obj.idperson;
                        //
                        if (myDropzone.getQueuedFiles().length > 0) {
                            console.log('tem '+ myDropzone.getQueuedFiles().length + ' arquivos');
                            global_idperson = idperson;
                            myDropzone.options.params = {idperson: idperson };
                            myDropzone.processQueue();
                        } else {
                            console.log('No files, no dropzone processing');
                            //sendNotification('new-ticket-user',ticket,false);
                        }
                        //
                        $('#modal-idperson').html(idperson);
                        $('#modal-login').html(obj.login);
                        $('#modal-apelido').html(obj.apelido);

                        $("#btnModalAlert").attr("href", path + '/spm/spmCadastroAtleta/index');

                        $('#modal-alert-create').modal('show');
                    } else {

                        modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-atleta');

                    }

                }

            });


    });


    $("#btnUpdateAtleta").click(function(){

        if (!$("#update-atleta-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/spm/spmCadastroAtleta/updateAtleta/idperson/' + $('#idperson').val(),
            dataType: 'json',
            data: {
                apelido: $('#apelido').val(),
                nome: $('#nome').val(),
                posicao: $('#posicao').val(),
                condicao: $('#condicao').val(),
                departamento: $('#departamento').val(),
                cpf: $('#cpf').val().replace(/[^0-9]/gi, ''),
                dtnasc: $('#dtnasc').val(),
                email: $('#email').val(),
                telefone: $('#telefone').val().replace(/[^0-9]/gi, ''),
                celular:$('#celular').val().replace(/[^0-9]/gi, ''),
                pais: $('#pais').val(),
                estado: $('#estado').val(),
                cidade: $('#cidade').val(),
                cep: $('#cep').val().replace(/[^0-9]/gi, ''),
                bairro: $('#bairro').val(),
                tipologra: $('#tipologra').val(),
                endereco: $('#endereco').val(),
                numero: $('#numero').val().replace(/[^0-9]/gi, ''),
                complemento: $('#complemento').val()
            },
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-atleta');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'OK' ) {

                    var idperson = obj.idperson;

                    //
                    if (myDropzone.getQueuedFiles().length > 0) {
                        console.log('tem '+ myDropzone.getQueuedFiles().length + ' arquivos');
                        global_idperson = idperson;
                        myDropzone.options.params = {idperson: idperson };
                        myDropzone.processQueue();
                    } else {
                        console.log('No files, no dropzone processing');
                        //sendNotification('new-ticket-user',ticket,false);
                    }
                    //
                    $('#modal-notification').html('Atleta atualizado com sucesso');
                    $("#btn-modal-ok").attr("href", path + '/spm/spmCadastroAtleta/index');
                    $("#tipo_alerta").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');

                } else {

                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-atleta');

                }

            }

        });


    });


    $("#btnAddBairro").click(function(){
        console.log($("#cidade").find('option:selected').text());
        idcidade = $("#cidade").val();
        $('#hidden-idcidade').val(idcidade);
        $('#modal-cidade-nome').val($("#cidade").find('option:selected').text());
        $('#modal-form-bairro').modal('show');
    });

    $("#btnSendBairro").click(function(){
        console.log('clicou salvar');
        if (!$("#bairro-form").valid()) {
            console.log('nao validou') ;
            return false;
        }
        $.ajax({
            type: "POST",
            url: path + '/spm/spmCadastroAtleta/createBairro',
            dataType: 'json',
            data: {
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

    /*
     * Dropzone
     */
    Dropzone.autoDiscover = false;
    var myDropzone = new Dropzone("#myDropzone", {  url: path + "/spm/spmCadastroAtleta/salvaFoto/",
        method: "post",
        dictDefaultMessage: "Arraste <br>o arquivo<br> com a foto<br> ou<br> clique aqui.",
        createImageThumbnails: true,
        maxFiles: 1,
        acceptedFiles: ticketAcceptedFiles,
        parallelUploads: 1,
        autoProcessQueue: false
    });

    myDropzone.on("maxfilesexceeded", function(file) {
        this.removeFile(file);
    });

    myDropzone.on("queuecomplete", function (file) {
        console.log('Completed the dropzone queue');
        //sendNotification('new-ticket-user',global_coderequest,true);
        //console.log('Sent email, with attachments');
    });

    /*
     * Validate
     */
    $("#create-atleta-form").validate({
        ignore:[],
        rules: {
            apelido: "required",
            nome: "required",
            dtnasc: "required",
            celular: "required"
        },
        messages: {
            apelido: "Campo obrigat&oacute;rio",
            nome: "Campo obrigat&oacute;rio",
            dtnasc: "Campo obrigat&oacute;rio",
            celular: "Campo obrigat&oacute;rio"
        }
    });
    $("#update-atleta-form").validate({
        ignore:[],
        rules: {
            apelido: "required",
            nome: "required",
            dtnasc: "required",
            celular: "required"
        },
        messages: {
            apelido: "Campo obrigat&oacute;rio",
            nome: "Campo obrigat&oacute;rio",
            dtnasc: "Campo obrigat&oacute;rio",
            celular: "Campo obrigat&oacute;rio"
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

