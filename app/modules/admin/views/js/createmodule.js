var global_idmodule = '';
$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });
 
    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/admin/modules/index');

    $("#btnCreateModule").click(function(){

        if (!$("#create-module-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/admin/modules/createModule',
            dataType: 'json',
            data: $("#create-module-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',aLang['Alert_failure'].replace (/\"/g, ""),'alert-create-module');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if($.isNumeric(obj.idmodule)) {

                    var idmodule = obj.idmodule;
                    console.log((myDropzone.getQueuedFiles().length)+" Número de arquivos");
                    if (myDropzone.getQueuedFiles().length > 0) {
                        console.log('tem '+ myDropzone.getQueuedFiles().length + ' arquivos');
                        global_idmodule = idmodule;
                        myDropzone.options.params = {idmodule: idmodule };
                        myDropzone.processQueue();
                    } else {
                        console.log('No files, no dropzone processing');
                        //sendNotification('new-ticket-user',ticket,false);
                    }

                    $('#modal-idmodule').html(obj.idmodule);
                    $('#modal-module-description').html(obj.description);

                    $("#btnModalAlert").attr("href", path + '/admin/modules/index');

                    $('#modal-alert-create').modal('show');
                } else {
                    modalAlertMultiple('danger',aLang['Alert_failure'].replace (/\"/g, ""),'alert-create-module');
                }
            }
        });
    });

    $("#btnUpdateModule").click(function(){

        if (!$("#update-module-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/admin/modules/updateModule',
            dataType: 'json',
            data: $("#update-module-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',aLang['Edit_failure'].replace (/\"/g, ""),'alert-update-module');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'OK' ) {
                    var idmodule = obj.idmodule;                                                          4
                    console.log((myDropzone.getQueuedFiles().length)+" Número de arquivos");
                    if (myDropzone.getQueuedFiles().length > 0) {
                        console.log('tem '+ myDropzone.getQueuedFiles().length + ' arquivos');
                        global_idmodule = idmodule;
                        myDropzone.options.params = {idmodule: idmodule };
                        myDropzone.processQueue();
                    } else {
                        console.log('No files, no dropzone processing');
                        //sendNotification('new-ticket-user',ticket,false);
                    }

                    $('#modal-notification').html(aLang['Edit_sucess'].replace (/\"/g, ""));
                    $("#btn-modal-ok").attr("href", path + '/admin/modules/index');
                    $("#tipo_alerta").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');

                } else {

                    modalAlertMultiple('danger',aLang['Edit_failure'].replace (/\"/g, ""),'alert-update-module');

                }

            }

        });


    });

    /*
     * Validate
     */
    $("#create-module-form").validate({
        ignore:[],
        rules: {
            txtName:        "required",
            txtPath:        "required",
            txtSmartyVar:   "required",
            txtPrefix:      "required"
        },
        messages: {
            txtName:        "Campo obrigat&oacute;rio",
            txtPath:        "Campo obrigat&oacute;rio",
            txtSmartyVar:   "Campo obrigat&oacute;rio",
            txtPrefix:      "Campo obrigat&oacute;rio"
        }
    });

    $("#update-module-form").validate({
        ignore:[],
        rules: {
            txtName:        "required",
            txtPath:        "required",
            txtSmartyVar:   "required",
            txtPrefix:      "required"
        },
        messages: {
            txtName:        "Campo obrigat&oacute;rio",
            txtPath:        "Campo obrigat&oacute;rio",
            txtSmartyVar:   "Campo obrigat&oacute;rio",
            txtPrefix:      "Campo obrigat&oacute;rio"
        }
    });

    /*
     * Dropzone
     */
    Dropzone.autoDiscover = false;
    var myDropzone = new Dropzone("#myDropzone", {  url: path + "/admin/modules/saveLogo/",
        method: "post",
        dictDefaultMessage: aLang['Drag_image_msg'].replace (/\"/g, ""),
        createImageThumbnails: true,
        maxFiles: 1,
        acceptedFiles: '.jpg, .jpeg, .png, .gif',
        parallelUploads: 1,
        autoProcessQueue: false,
        addRemoveLinks: true,
        init: function () {
            //console.log($('#idproduto').val());
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: path + '/admin/modules/loadImage',
                data: {
                    idmodule:  $('#idmodule').val()
                },
                success: function(response){
                    var existingFileCount = 0;
                    console.log(response.length);
                    $.each(response, function(  key, value ) {
                        var arquivos = {
                            idmodule: value.idmodule,
                            name: value.filename,
                            size: value.size,
                            url: path +"/app/uploads/logos/"
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
            url: path + '/admin/modules/removeLogo',
            data: {
                idmodule:  file.idmodule,
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

    myDropzone.on("queuecomplete", function (file) {
        console.log('Completed the dropzone queue');
        //sendNotification('new-ticket-user',global_coderequest,true);
        //console.log('Sent email, with attachments');
    });

});
