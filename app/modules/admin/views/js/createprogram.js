var global_idmodule = '';
$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    /*
     *  Chosen
     */
    $("#cmbModule").chosen({ width: "100%",    no_results_text: "Nada encontrado!"});
    $("#cmbCategory").chosen({ width: "100%",    no_results_text: "Nada encontrado!"});
    $("#cmbModuleMod").chosen({ width: "100%",    no_results_text: "Nada encontrado!"});

    var objProgramData = {
        changeModule: function() {
            $.post(path+"/admin/program/ajaxModule",
                function(valor) {
                    $("#cmbModule").html(valor);
                    $("#cmbModule").trigger("chosen:updated");
                    return false;
                });
        },
        changeCategory: function() {
            var moduleId = $("#cmbModule").val();
            $.post(path+"/admin/program/ajaxCategory",{idmodule:moduleId},
                function(valor) {
                    $("#cmbCategory").html(valor);
                    $("#cmbCategory").trigger("chosen:updated");
                    return false;
                });
        }
    }

    $("#cmbModule").change(function(){
        objProgramData.changeCategory();
    });
 
    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/admin/program/index');

    $("#btnCreateProgram").click(function(){

        if (!$("#create-program-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/admin/program/createProgram',
            dataType: 'json',
            data: $("#create-program-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',aLang['Alert_failure'].replace (/\"/g, ""),'alert-create-program');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if($.isNumeric(obj.idprogram)) {

                    var idprogram = obj.idprogram;

                    $('#modal-idprogram').html(obj.idprogram);
                    $('#modal-program-description').html(obj.description);

                    $("#btnModalAlert").attr("href", path + '/admin/program/index');

                    $('#modal-alert-create').modal('show');
                } else {
                    modalAlertMultiple('danger',aLang['Alert_failure'].replace (/\"/g, ""),'alert-create-program');
                }
            }
        });
    });

    $("#btnUpdateProgram").click(function(){

        if (!$("#update-program-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/admin/program/updateProgram',
            dataType: 'json',
            data: $("#update-program-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',aLang['Edit_failure'].replace (/\"/g, ""),'alert-update-program');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'OK' ) {
                    var idprogram = obj.idprogram;
                    
                    $('#modal-notification').html(aLang['Edit_sucess'].replace (/\"/g, ""));
                    $("#btn-modal-ok").attr("href", path + '/admin/program/index');
                    $("#tipo_alerta").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');

                } else {

                    modalAlertMultiple('danger',aLang['Edit_failure'].replace (/\"/g, ""),'alert-update-program');

                }

            }

        });


    });

    $("#btnAddModule").click(function(){
        /*objTopicData.getOperatorGroup();
        objTopicData.getCorporation();*/
        $('#modal-form-module').modal('show');
    });

    $("#btnAddCategory").click(function(){
        /*objTopicData.getOperatorGroup();
        objTopicData.getCorporation();*/
        $('#modal-form-category').modal('show');
    });

    $("#btnSendModule").click(function(){
        console.log('clicou salvar');
        if (!$("#module-form").valid()) {
            console.log('nao validou') ;
            return false;
        }
        $.ajax({
            type: "POST",
            url: path + '/admin/modules/createModule',
            dataType: 'json',
            data: $('#module-form').serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',aLang['Alert_'].replace (/\"/g, ""),'alert-module');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if($.isNumeric(obj.idmodule)) {
                    modalAlertMultiple('success',aLang['Alert_sucess_module'].replace (/\"/g, ""),'alert-module');
                    objProgramData.changeModule();
                    setTimeout(function(){
                        $('#modal-form-module').modal('hide');
                    },2000);
                } else {
                    modalAlertMultiple('danger',aLang['Alert_'].replace (/\"/g, ""),'alert-module');
                }
            }
        });

    });

    $("#btnSendCategory").click(function(){
        console.log('clicou salvar');
        if (!$("#category-form").valid()) {
            console.log('nao validou') ;
            return false;
        }
        $.ajax({
            type: "POST",
            url: path + '/admin/program/categoryinsert',
            dataType: 'json',
            data: $('#category-form').serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',aLang['Alert_'].replace (/\"/g, ""),'alert-category');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if($.isNumeric(obj.idcategory)) {
                    modalAlertMultiple('success',aLang['Alert_sucess_category'].replace (/\"/g, ""),'alert-category');
                    objProgramData.changeCategory();
                    setTimeout(function(){
                        $('#modal-form-category').modal('hide');
                    },2000);
                } else {
                    modalAlertMultiple('danger',aLang['Alert_'].replace (/\"/g, ""),'alert-category');
                }
            }
        });

    });

    /*
     * Validate
     */
    $("#create-program-form").validate({
        ignore:[],
        rules: {
            txtName:        "required",
            txtController:  "required",
            txtSmarty:      "required",
            cmbCategory:    "required"
        },
        messages: {
            txtName:        makeSmartyLabel('Alert_field_required'),
            txtController:  makeSmartyLabel('Alert_field_required'),
            txtSmarty:      makeSmartyLabel('Alert_field_required'),
            cmbCategory:    makeSmartyLabel('Alert_field_required')
        }
    });

    $("#update-program-form").validate({
        ignore:[],
        rules: {
            txtName:        "required",
            txtController:  "required",
            txtSmarty:      "required",
            cmbCategory:    "required"
        },
        messages: {
            txtName:        makeSmartyLabel('Alert_field_required'),
            txtController:  makeSmartyLabel('Alert_field_required'),
            txtSmarty:      makeSmartyLabel('Alert_field_required'),
            cmbCategory:    makeSmartyLabel('Alert_field_required')
        }
    });

    $("#module-form").validate({
        ignore:[],
        rules: {
            txtName:        "required",
            txtPath:        "required",
            txtSmartyVar:   "required",
            txtPrefix:      "required"
        },
        messages: {
            txtName:        makeSmartyLabel('Alert_field_required'),
            txtPath:        makeSmartyLabel('Alert_field_required'),
            txtSmartyVar:   makeSmartyLabel('Alert_field_required'),
            txtPrefix:      makeSmartyLabel('Alert_field_required')
        }
    });

    $("#category-form").validate({
        ignore:[],
        rules: {
            txtNewCategory:        "required"
        },
        messages: {
            txtNewCategory:        makeSmartyLabel('Alert_field_required')
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

    /* limpa campos modal */
    $('.modal').on('hidden.bs.modal', function() {
        $('#module-form').trigger('reset');
        $('#category-form').trigger('reset');
    });

    $('#flagPerm').on('ifChecked ifUnchecked',function(e){
        if(e.type == 'ifChecked'){
            $('#boxPerms').removeClass('hidden');            
        }else{
            $('#boxPerms').addClass('hidden');
        }
    });

    $('.lbltooltip').tooltip();


});
