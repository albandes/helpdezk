var global_idmodule = '';
var objCbmLocale = {
    loadLocale: function(id) {
        $.post(path+"/admin/vocabulary/ajaxLocale",
            function(res) {
                $("#localeID_"+id).html(res);
                $("#localeID_"+id).chosen({    width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
                return false;
            })
        return false ;
    }

}

$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    var objModuleData = {
        changeModuleVocab: function() {
            $.post(path+"/admin/program/ajaxModule",{selectedID:'1'},
                function(valor) {
                    $("#cmbModuleVocab").html(valor);
                    $("#cmbModuleVocab").trigger("chosen:updated");
                    return false;
                });
        }
    };

    /*
     *  Chosen
     */
    $("#cmbModuleVocab").chosen({ width: "100%", no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
 
    objCbmLocale.loadLocale(1);

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
                modalAlertMultiple('danger', makeSmartyLabel('Alert_failure'),'alert-create-module');
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
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-module');
                }
            },
            beforeSend: function(){
                $("#btnCreateModule").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                $("#btnCancel").addClass('disabled');
            },
            complete: function(){
                $("#btnCreateModule").html("<i class='fa fa-check-circle'></i> "+ makeSmartyLabel('Yes')).removeClass('disabled');
                $("#btnCancel").removeClass('disabled');
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
                    $("#tipo-alert").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');

                } else {

                    modalAlertMultiple('danger',aLang['Edit_failure'].replace (/\"/g, ""),'alert-update-module');

                }

            }

        });


    });

    $("#btnAddVocabulary").click(function(){
        objModuleData.changeModuleVocab();
        $('#modal-form-vocabulary').modal('show');
    });

    $("#btnSaveVocabulary").click(function(){
        if (!$("#vocabulary-form").valid()) {
            return false;
        }

        var flgNotSelect = false, flgEmpty = false;
        $("select[name='localeID[]']").each(function(){
            var lineID = $(this).attr('id');
            lineID = lineID.split('_');
            if($(this).val() == ''){
                flgNotSelect = true;
            }else{
                $.ajax({
                    type: "POST",
                     url: path+"/admin/vocabulary/checkKeyName",
                dataType: 'json',
                    data: {
                        '_token' : $("#_token").val(),
                        'keyName':$("#keyName").val(),
                        'localeID':$(this).val(),
                        'localName': $("#localeID_"+ lineID[1] +" option:selected").text()
                    },
                   async: false,
                   error: function (ret) {
                        modalAlertMultiple('danger',aLang['Alert_failure'].replace (/\"/g, ""),'alert-create-vocabulary');
                    },
                 success: function(ret){
                        var obj = jQuery.parseJSON(JSON.stringify(ret));
                        if(!obj.status) {
                            modalAlertMultiple('danger',obj.message,'alert-create-vocabulary');
                            return false;
                        }
                    }
                });
            }
        });

        if(flgNotSelect){
            modalAlertMultiple('danger',makeSmartyLabel('one_more_not_select_locale'),'alert-create-vocabulary');
            return false ;
        }

        $("input[name='keyValue[]']").each(function(){
            if(($(this).val() == '' || $(this).val() == ' ')){
                flgEmpty = true;
            }
        });

        if(flgEmpty){
            modalAlertMultiple('danger',makeSmartyLabel('one_more_no_key_value'),'alert-create-vocabulary');
            return false ;
        }

        if(!$("#btnSaveVocabulary").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/vocabulary/createVocabulary',
                dataType: 'json',
                data: {
                    _token: $("#_token").val(),
                    cmbModule: $("#cmbModuleVocab").val(),
                    'keyName': $("#keyName").val(),
                    'localeID': $("select[name='localeID[]']").map(function(){return $(this).val();}).get(),
                    'keyValue': $("input[name='keyValue[]']").map(function(){return $(this).val();}).get()
                },
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-vocabulary');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.status) {
                        modalAlertMultiple('success',aLang['Alert_inserted'].replace (/\"/g, ""),'alert-create-vocabulary');
                        setTimeout(function(){
                            $('#modal-form-vocabulary').modal('hide');
                        },2000);
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-vocabulary');
                    }
                },
                beforeSend: function(){
                    $("#btnSaveVocabulary").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                    $("#btnCancelVocabulary").addClass('disabled');
                },
                complete: function(){
                    $("#btnSaveVocabulary").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                    $("#btnCancelVocabulary").removeClass('disabled');
                }
            });
        }

    });

    $("#btnAddKeyValue").click(function(){
        duplicateRow('localeTab','add');
    });

    /*
     * Validate
     */
    $("#create-module-form").validate({
        ignore:[],
        rules: {
            txtName:        {
                required: true,
                minlength: 3,
                remote: {
                    url: path + "/admin/modules/checkModule",
                    type: "post",
                    dataType:'json',
                    data:{
                        _token: function(element){return $('#_token').val();}
                    }
                }
            },
            txtPath:{
                required: true,
                minlength: 3,
                remote: {
                    url: path + "/admin/modules/checkModulePath",
                    type: "post",
                    dataType:'json',
                    data:{
                        _token: function(element){return $('#_token').val();}
                    }
                }
            },
            txtSmartyVar:   "required",
            txtPrefix:      {
                required: true, 
                equalTo: "#txtPath"
            }
        },
        messages: {
            txtName:       {
                required:makeSmartyLabel('Alert_field_required'), 
                minlength:makeSmartyLabel('Alert_word_min_letters')
            },
            txtPath:        {
                required:makeSmartyLabel('Alert_field_required'), 
                minlength:makeSmartyLabel('Alert_word_min_letters')
            },
            txtSmartyVar:   makeSmartyLabel('Alert_field_required'),
            txtPrefix:     {
                required:makeSmartyLabel('Alert_field_required'), 
                equalTo: makeSmartyLabel('Alert_different_path')
            }
        }
    });

    $("#update-module-form").validate({
        ignore:[],
        rules: {
            txtName:        {
                required: true,
                minlength: 3,
                remote: {
                    url: path + "/admin/modules/checkModule",
                    type: "post",
                    dataType:'json',
                    data:{
                        _token: function(element){return $('#_token').val();},
                        moduleID: function(element){return $('#idmodule').val();}
                    }
                }
            },
            txtPath:{
                required: true,
                minlength: 3,
                remote: {
                    url: path + "/admin/modules/checkModulePath",
                    type: "post",
                    dataType:'json',
                    data:{
                        _token: function(element){return $('#_token').val();},
                        moduleID: function(element){return $('#idmodule').val();}
                    }
                }
            },
            txtSmartyVar:   "required",
            txtPrefix:      {
                required: true, 
                equalTo: "#txtPath"
            }
        },
        messages: {
            txtName:       {
                required:makeSmartyLabel('Alert_field_required'), 
                minlength:makeSmartyLabel('Alert_word_min_letters')
            },
            txtPath:        {
                required:makeSmartyLabel('Alert_field_required'), 
                minlength:makeSmartyLabel('Alert_word_min_letters')
            },
            txtSmartyVar:   makeSmartyLabel('Alert_field_required'),
            txtPrefix:     {
                required:makeSmartyLabel('Alert_field_required'), 
                equalTo: makeSmartyLabel('Alert_different_path')
            }
        }
    });

    $("#vocabulary-form").validate({
        ignore:[],
        rules: {
            cmbModuleVocab:  "required",
            keyName: {
                required:true,
                noAccent:true
            }
        },
        messages: {
            cmbModule:  makeSmartyLabel('Alert_field_required'),
            keyName:    {required:makeSmartyLabel('Alert_field_required')}
        }
    });

    $.validator.addMethod('noAccent', function(strValue) {
        var re = new RegExp("^[a-zA-Z0-9_]+$","i");
        return re.test(strValue);
    }, makeSmartyLabel('key_no_accents_no_whitespace'));

    /*
     * Dropzone
     */
    Dropzone.autoDiscover = false;
    var myDropzone = new Dropzone("#myDropzone", {  url: path + "/admin/modules/saveLogo/",
        method: "post",
        dictDefaultMessage: "<i class='fa fa-file-image fa-2x' aria-hidden='true'></i><br>" + makeSmartyLabel('Drag_image_msg'),
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
        $('#vocabulary-form').trigger('reset');
    });

    $('.lbltooltip').tooltip();

});

function duplicateRow(strTableName,ope){
    // First, lets create the new row using the last one as template...
    var clonedRow = $( "#" + strTableName + " tr:last" ).clone();
    // Take the current identifier, some number in the first cell of the row
    intCurrentRowId = parseInt( $( "#numId:last", clonedRow ).val() );
    // Set the new ID
    intNewRowId = intCurrentRowId + 1;
    // Change the current identifier of the row to the new one
    $( "#numId:last", clonedRow ).val( intNewRowId );

    // Change the Id / Name or anything you want for the new attribs...
    //here is where you need to add a lot of stuff to change the id of your variables

    // The following code works without problems on Firefox or IE7
    $( "#localeID_"+ intCurrentRowId , clonedRow ).attr( { "id" :"localeID_" + intNewRowId, "accesskey" : intNewRowId } );
    $( "#keyValue_"+ intCurrentRowId , clonedRow ).attr( { "id" :"keyValue_" + intNewRowId, "accesskey" : intNewRowId} );
    $( "#localeID_"+ intCurrentRowId + "_chosen" , clonedRow ).attr( { "id" :"localeID_" + intNewRowId + "_chosen", "accesskey" : intNewRowId } );
    if(ope == 'upd')
        $( "#vocabularyID_"+ intCurrentRowId , clonedRow ).attr( { "id" :"vocabularyID_" + intNewRowId, "accesskey" : intNewRowId, "value" : "0" } );

    // Add to the new row to the original table
    $( "#" + strTableName ).append( clonedRow );

    $( "#keyValue_"+ intNewRowId).val('');
    $( "#localeID_"+ intNewRowId + "_chosen").remove();
    objCbmLocale.loadLocale(intNewRowId);

    // And finally change the ID of the last row to something we can
    //delete later, not sure why can not be done before the append :S
    $( "#" + strTableName + " tr:last" ).attr( "id", "detailsTr" + intNewRowId );


    $( "#localeID_"+ intNewRowId ).focus();
}

function removeRow(id,strTableName,ope){
    var i = id.parentNode.parentNode.rowIndex, msgDiv;

    msgDiv = ope == 'upd' ? 'alert-update-vocabulary' : 'alert-create-vocabulary';

    if($("#"+strTableName+" tbody tr").length == 1){
        modalAlertMultiple('warning', makeSmartyLabel('Alert_dont_remove_row'),msgDiv);
    }else{
        document.getElementById(strTableName).deleteRow(i);
    }
}