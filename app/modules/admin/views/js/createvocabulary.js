var global_idperson = '';
/*
 * Combos
 */
var objCbmData = {
    loadLocale: function(id) {
        $.post(path+"/admin/vocabulary/ajaxLocale",
            function(res) {
                $("#localeID_"+id).html(res);
                $("#localeID_"+id).chosen({    width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
                return false;
            })
        return false ;
    }

};

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
        $("#btnCreateVocabulary").addClass('hide');
    }

    if(access[2] != "Y"){
        $("#btnUpdateVocabulary").addClass('hide');
    }

    if($("#typeAction").val() != 'upd')
        objCbmData.loadLocale(1);

    /*
     *  Chosen
     */
    $("#cmbModule").chosen({    width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    if($("#typeAction").val() == 'upd')
        $(".cmbLocale").chosen({    width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});

    var objProgramData = {
        changeModule: function(selectedID) {
            $.post(path+"/admin/program/ajaxModule",{selectedID:selectedID},
                function(valor) {
                    $("#cmbModule").html(valor);
                    $("#cmbModule").trigger("chosen:updated");
                    $("#cmbModule").change();
                    return false;
                });
        }
    };
    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/admin/vocabulary/index');

    $("#btnCreateVocabulary").click(function(){

        if (!$("#create-vocabulary-form").valid()) {
            return false ;
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

        if(!$("#btnCreateVocabulary").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/vocabulary/createVocabulary',
                dataType: 'json',
                data: $("#create-vocabulary-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-vocabulary');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.status) {
                        showAlert(makeSmartyLabel('Alert_inserted'),'success',path + '/admin/vocabulary/index');
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-vocabulary');
                    }
                },
                beforeSend: function(){
                    $("#btnCreateVocabulary").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnCreateVocabulary").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
            });
        }


    });

    $("#btnUpdateVocabulary").click(function(){

        if (!$("#update-vocabulary-form").valid()) {
            return false ;
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
                        'vocabularyID':$("#vocabularyID_" + lineID[1]).val(),
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
            modalAlertMultiple('danger',makeSmartyLabel('one_more_not_select_locale'),'alert-update-vocabulary');
            return false ;
        }

        $("input[name='keyValue[]']").each(function(){
            if($(this).val() == '' || $(this).val() == ' '){
                flgEmpty = true;
            }
        });

        if(flgEmpty){
            modalAlertMultiple('danger',makeSmartyLabel('one_more_no_key_value'),'alert-update-vocabulary');
            return false ;
        }

        if(!$("#btnUpdateVocabulary").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/vocabulary/updateVocabulary',
                dataType: 'json',
                data: $("#update-vocabulary-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-update-vocabulary');
                },
                success: function(ret){

                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.status) {
                        showAlert(makeSmartyLabel('Edit_sucess'),'success',path + '/admin/vocabulary/index');
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-update-vocabulary');
                    }

                },
                beforeSend: function(){
                    $("#btnUpdateVocabulary").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnUpdateVocabulary").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
            });
        }
    });

    $("#btnAddKeyValue").click(function(){
        duplicateRow('localeTab',$('#typeAction').val());
    });

    $("#btnAddModule").click(function(){
        $('#modal-form-module').modal('show');
    });

    $("#btnSendModule").click(function(){
        
        if (!$("#module-form").valid()) {
            console.log('nao validou') ;
            return false;
        }

        if(!$("#btnSendModule").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/modules/createModule',
                dataType: 'json',
                data: $('#module-form').serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',aLang['Alert_failure'].replace (/\"/g, ""),'alert-module');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if($.isNumeric(obj.idmodule)) {
                        modalAlertMultiple('success',aLang['Alert_sucess_module'].replace (/\"/g, ""),'alert-module');
                        objProgramData.changeModule(obj.idmodule);
                        setTimeout(function(){
                            $('#modal-form-module').modal('hide');
                        },2000);
                    } else {
                        modalAlertMultiple('danger',aLang['Alert_failure'].replace (/\"/g, ""),'alert-module');
                    }
                },
                beforeSend: function(){
                    $("#btnbtnSendModule").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                    $("#btnCancelModule").addClass('disabled');
                },
                complete: function(){
                    $("#btnbtnSendModule").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                    $("#btnCancelModule").removeClass('disabled');
                }
            });
        }

    });

    /*
     * Validate
     */
    $("#create-vocabulary-form").validate({
        ignore:[],
        rules: {
            cmbModule:  "required",
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

    $("#update-vocabulary-form").validate({
        ignore:[],
        rules: {
            cmbModule: "required",
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

    $("#module-form").validate({
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

    $.validator.addMethod('noAccent', function(strValue) {
        var re = new RegExp("^[a-zA-Z0-9_]+$","i");
        return re.test(strValue);
    }, makeSmartyLabel('key_no_accents_no_whitespace'));

    // tooltips
    $('.tooltip-buttons').tooltip();

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
    });

    /* clean modal's fields */
    $('.modal').on('hidden.bs.modal', function() {
        $('#module-form').trigger('reset');
    });

});

function showAlert(msg,typeAlert,btnOk)
{
    $('#modal-notification').html(msg);
    $("#btn-modal-ok").attr("href", btnOk);
    $("#tipo-alert").attr('class', 'alert alert-'+typeAlert);
    $('#modal-alert').modal('show');

    return false;
}

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
    objCbmData.loadLocale(intNewRowId);

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
