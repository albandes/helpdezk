var global_idproduto = '';
var dropzonefiles = 0, filesended = 0, flgerror = 0, errorname=[], upname=[], btnClicked = 0;
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
        $("#btnCreateProduto").addClass('hide');
    }

    if(access[2] != "Y"){
        $("#btnUpdateProduto").addClass('hide');
    }

    /*
     *  Chosen
     */
    $("#idunidade").chosen({ width: "100%",no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});

    /*
     * Combos
     */
    var objProdutoData = {
        changeUnidade: function() {
            $.post(path+"/scm/scmProduto/ajaxUnidade",
                function(valor){
                    $("#idunidade").html(valor);
                    $("#idunidade").trigger("chosen:updated");
                    return false;
                })
            return false ;
        }

    }

    /*
     * Buttons
     */
    $("#btnCancel").click(function(){
        location.href = path + '/scm/scmProduto/index' ;
    });

    $("#btnCreateProduto").click(function(){

        if (!$("#create-produto-form").valid()) {
            return false ;
        }

        if(!$("#btnCreateProduto").hasClass('disabled')){
            $("#btnCreateProduto").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
            btnClicked = "1";
            if (myDropzone.getQueuedFiles().length > 0) {
                dropzonefiles = myDropzone.getQueuedFiles().length;
                myDropzone.processQueue();
            } else {
                saveProduct(upname);
            }
        }

        return false;
    });

    $("#btnUpdateProduto").click(function(){

        if (!$("#update-produto-form").valid()) {
            return false ;
        }

        if(!$("#btnUpdateProduto").hasClass('disabled')){
            $("#btnUpdateProduto").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
            btnClicked = "2";
            if (myDropzone.getQueuedFiles().length > 0) {
                dropzonefiles = myDropzone.getQueuedFiles().length;
                myDropzone.processQueue();
            } else {
                updateProduct(upname);
            }
        }

        return false;
        
    });

    $("#btnAddUnidade").click(function(){
        $('#modal-form-unidade').modal('show');
    });

    $("#btnSendUnidade").click(function(){
        if (!$("#unidade-form").valid()) {
            console.log('nao validou') ;
            return false;
        }
        $.ajax({
            type: "POST",
            url: path + '/scm/scmProduto/createUnidade',
            dataType: 'json',
            data: {
                _token: $("#_token").val(),
                nome:  $('#modal_unidade_nome').val()
            },
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-unidade');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if($.isNumeric(obj.idunidade)) {
                    modalAlertMultiple('success','Unidade inclu&iacute;da com sucesso !','alert-unidade');
                    objProdutoData.changeUnidade();
                } else {
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-unidade');
                }
            }
        });

    });

    /*
     * Dropzone
     */
    if($('#myDropzone').length > 0){
        Dropzone.autoDiscover = false;
        var myDropzone = new Dropzone("#myDropzone", {  url: path + "/scm/scmProduto/salvaFoto/",
            method: "post",
            dictDefaultMessage: "<br><i class='fa fa-file-image fa-2x' aria-hidden='true'></i><br>" + makeSmartyLabel('Drag_image_msg'),
            createImageThumbnails: true,
            maxFiles: 3,
            autoQueue: true,
            addRemoveLinks:true,
            acceptedFiles: '.jpg, .jpeg, .png, .gif',
            parallelUploads: 3,
            autoProcessQueue: false,
            init: function () {
                //console.log($('#idproduto').val());
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: path + '/scm/scmProduto/buscaImagem',
                    data: {
                        idproduto:  $('#idproduto').val()
                    },
                    success: function(response){
                        var existingFileCount = 0;
                        $.each(response, function(  key, value ) {
                            var arquivos = {
                                idimage: value.idimagem,
                                name: value.nome,
                                size: value.size,
                                url: urlPath
                            };
                            myDropzone.emit("addedfile", arquivos);
                            myDropzone.files.push(arquivos);
                            myDropzone.emit("thumbnail", arquivos, arquivos.url+arquivos.name);
                            myDropzone.emit("success", arquivos);
                            myDropzone.emit("complete", arquivos);
                            //myDropzone.uploadFiles([arquivos]);

                            existingFileCount = existingFileCount + 1; // The number of files already uploaded

                        });
                        myDropzone.options.maxFiles = myDropzone.options.maxFiles - existingFileCount;
                        console.log('tem '+ myDropzone.getQueuedFiles().length + ' arquivos');
                        console.log(path);
                    },
                    error: function (response) {
                        console.log("Erro no Dropzone!");
                    }
                });
            }
        });

        myDropzone.on("maxfilesexceeded", function(file) {
            this.removeFile(file);
        });

        myDropzone.on("removedfile", function(file) {
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: path + '/scm/scmProduto/removeImage',
                data: {
                    idimage:  file.idimage,
                    filename: file.name
                },
                success: function(response){
                    var obj = jQuery.parseJSON(JSON.stringify(response));
                    if(obj.status == 'OK'){
                        myDropzone.options.maxFiles = myDropzone.options.maxFiles + 1;
                    }
                },
                error: function (response) {
                    console.log("Erro no Dropzone!");
                }
            });

        });

        myDropzone.on("complete", function(file) {
        
            if(file.status === "canceled" || file.status === "error"){
                errorname.push(file.name);
                flgerror = 1;
            }else if((file.xhr)){
                var obj = JSON.parse(file.xhr.response);
            
                if(obj.success) {
                    filesended = filesended + 1;
                    upname.push(file.name);
                } else {
                    errorname.push(file.name);
                    flgerror = 1;
                }
            }
            
        });
    
        myDropzone.on("queuecomplete", function (file) {
            var msg,typeMsg;
    
            if(errorname.length == 0 && (filesended == dropzonefiles)){
                if(btnClicked=="1"){
                    saveProduct(upname);
                }else if(btnClicked=="2"){
                    updateProduct(upname);
                }                            
            }else{
                var totalAttach = dropzonefiles - filesended;
                msg = '<h4>'+makeSmartyLabel('files_not_attach_list')+'</h4><br>';
                errorname.forEach(element => {
                    msg = msg+element+'<br>';
                });
                msg = msg+'<br>'+makeSmartyLabel('scm_attach_after');
                typeMsg = 'warning';
                showNextStep(msg,typeMsg,totalAttach);
            }        
            
            dropzonefiles = 0; 
            filesended = 0;
            flgerror = 0;
        });
    }

    /*
     * Validate
     */
    $("#create-produto-form").validate({
        ignore:[],
        rules: {
            nome:           "required",
            descricao:      "required",
            estoque_inicial:"required",
            estoque_minimo: "required",
            codigo_barras:  "required"

        },
        messages: {

            nome:           "Campo obrigat&oacute;rio",
            descricao:      "Campo obrigat&oacute;rio",
            estoque_inicial:"Campo obrigat&oacute;rio",
            estoque_minimo: "Campo obrigat&oacute;rio",
            codigo_barras:  "Campo obrigat&oacute;rio"

        }
    });

    $("#unidade-form").validate({
        ignore:[],
        rules: {
            modal_unidade_nome: {
                required: true,
                remote: {
                    url: path + '/scm/scmProduto/buscaUnidade',
                    type: "post"
                }
            },
        },
        messages: {
            modal_unidade_nome: {
                required: "Campo obrigat&oacute;rio.",
                remote: "A unidade já existe!"
            },
        }
    });

    $("#update-produto-form").validate({
        ignore:[],
        rules: {
            nome:           "required",
            descricao:      "required",
            estoque_inicial:"required",
            estoque_minimo: "required",
            codigo_barras:  "required"

        },
        messages: {
            nome:           "Campo obrigat&oacute;rio",
            descricao:      "Campo obrigat&oacute;rio",
            estoque_inicial:"Campo obrigat&oacute;rio",
            estoque_minimo: "Campo obrigat&oacute;rio",
            codigo_barras:  "Campo obrigat&oacute;rio"

        }
    });

    $("#btnNextYes").click(function(){        
        $('#modal-next-step').modal('hide');
        if(btnClicked=="1"){
            saveProduct(upname);
        }else if(btnClicked=="2"){
            updateProduct(upname);
        }
    });

    $("#btnNextNo").click(function(){
        if (!$("#btnNextNo").hasClass('disabled')) {
            $("#btnNextNo").removeClass('disabled');
            $('#modal-next-step').modal('hide');
            errorname = [];
            upname = [];
            location.href = path + "/scm/scmProduto/index" ;
        }
    });
});

function showNextStep(msg,typeAlert,totalAttach)
{
    $('#nexttotalattach').val(totalAttach);
    $('#next-step-list').html(msg);
    $('#next-step-message').html(makeSmartyLabel('save_record_anyway_question'));
    $("#type-alert").attr('class', 'col-sm-12 col-xs-12 bs-callout-'+typeAlert);
    $('#modal-next-step').modal('show');

    return false;
}

function saveProduct(aAttachs)
{
    $.ajax({
        type: "POST",
        url: path + '/scm/scmProduto/checkProduto',
        dataType: 'json',
        data: {
            nome: $('#nome').val(),
            idunidade: $('#idunidade').val(),
            codigo_barras: $('#codigo_barras').val()
        },
        error: function (ret) {
            modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel realizar a consulta!','alert-create-produto');
        },
        success: function(ret){
            //console.log(ret);
            var obj = jQuery.parseJSON(JSON.stringify(ret));

            if(obj.status === "OK"){
                $.ajax({
                    type: "POST",
                    url: path + '/scm/scmProduto/createProduto',
                    dataType: 'json',
                    data: {
                        _token: $("#_token").val(),
                        nome: $('#nome').val(),
                        descricao: $('#descricao').val(),
                        idunidade: $('#idunidade').val(),
                        estoque_inicial: $('#estoque_inicial').val(),
                        estoque_minimo: $('#estoque_minimo').val(),
                        codigo_barras: $('#codigo_barras').val(),
                        attachments: aAttachs
                    },
                    error: function (res) {
                        modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-produto');
                    },
                    success: function(res){

                        var obj = jQuery.parseJSON(JSON.stringify(res));

                        if($.isNumeric(obj.idproduto)) {
                            $('#modal-idproduto').html(obj.idproduto);
                            $('#modal-nome').html(obj.nome);
                            $("#btnModalAlert").attr("href", path + '/scm/scmProduto/index');
                            $('#modal-alert-create').modal('show');
                            
                            errorname = [];
                            upname = [];
                            btnClicked = 0;
                        } else {
                            modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-produto');
                        }
                    }
                });
            }else{
                modalAlertMultiple('warning','Este produto foi cadastrado previamente.','alert-create-produto');
            }
        },
        beforeSend: function(){
            /*$("#btnCreateProduto").attr('disabled','disabled');*/
        },
        complete: function(){
            $("#btnCreateProduto").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
        }
    });

    return false ;

}

function updateProduct(aAttachs)
{
    $.ajax({
        type: "POST",
        url: path + '/scm/scmProduto/checkProduto',
        dataType: 'json',
        data: {
            nome: $('#nome').val(),
            idunidade: $('#idunidade').val(),
            codigo_barras: $('#codigo_barras').val(),
            idproduto: $('#idproduto').val()
        },
        error: function (ret) {
            modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel realizar a consulta!','alert-update-produto');
        },
        success: function(ret){
            //console.log(ret);
            var obj = jQuery.parseJSON(JSON.stringify(ret));

            if(obj.status === "OK"){
                $.ajax({
                    type: "POST",
                    url: path + '/scm/scmProduto/updateProduto', 
                    dataType: 'json',
                    data: {
                        _token: $("#_token").val(),
                        idproduto: $('#idproduto').val(),
                        nome: $('#nome').val(),
                        descricao: $('#descricao').val(),
                        idunidade: $('#idunidade').val(),
                        estoque_inicial: $('#estoque_inicial').val(),
                        estoque_minimo: $('#estoque_minimo').val(),
                        codigo_barras: $('#codigo_barras').val(),
                        attachments: aAttachs
                    },
                    error: function (res) {
                        modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-update-produto');
                    },
                    success: function(res){

                        var obj = jQuery.parseJSON(JSON.stringify(res));

                        if(obj.status == 'OK' ) {

                            var idproduto = obj.idproduto;

                            $('#modal-notification').html('Produto atualizado com sucesso');
                            $("#btn-modal-ok").attr("href", path + '/scm/scmProduto/index');
                            $("#tipo_alerta").attr('class', 'alert alert-success');
                            $('#modal-alert').modal('show');
                            
                            errorname = [];
                            upname = [];
                            btnClicked = 0;

                        } else {

                            modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-update-produto');

                        }

                    }

                });
            }else{
                modalAlertMultiple('warning','Este produto foi cadastrado previamente.','alert-update-produto');
            }
        },
        beforeSend: function(){
            /*$("#btnUpdateProduto").attr('disabled','disabled');*/
        },
        complete: function(){
            $("#btnUpdateProduto").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
        }
    });
    

    return false ;

}