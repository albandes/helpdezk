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

    /*
     *  Chosen
     */
    $("#cmbModule").chosen({ width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#cmbCategory").chosen({ width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#cmbModuleMod").chosen({ width: "100%", no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#cmbModuleVocab").chosen({ width: "100%", no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});

    var objProgramData = {
        changeModule: function(selectedID) {
            $.post(path+"/admin/program/ajaxModule",{selectedID:selectedID},
                function(valor) {
                    $("#cmbModule").html(valor);
                    $("#cmbModule").trigger("chosen:updated");
                    $("#cmbModule").change();
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
        },
        changeModuleCat: function(selectedID) {
            $.post(path+"/admin/program/ajaxModule",{selectedID:selectedID},
                function(valor) {
                    $("#cmbModuleMod").html(valor);
                    $("#cmbModuleMod").trigger("chosen:updated");
                    return false;
                });
        },
        changeModuleVocab: function(selectedID) {
            $.post(path+"/admin/program/ajaxModule",{selectedID:selectedID},
                function(valor) {
                    $("#cmbModuleVocab").html(valor);
                    $("#cmbModuleVocab").trigger("chosen:updated");
                    return false;
                });
        }
    };

    $("#cmbModule").change(function(){
        objProgramData.changeCategory();
    });

    objCbmLocale.loadLocale(1);
 
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
        $('#modal-form-module').modal('show');
    });

    $("#btnAddCategory").click(function(){
        var newSelected = $('#cmbModule').val();
        $('#cmbModuleMod option:selected').removeAttr('selected');
        objProgramData.changeModuleCat(newSelected);
        $('#modal-form-category').modal('show');
    });

    $("#btnAddVocabulary").click(function(){
        var newSelected = $('#cmbModule').val();
        $('#cmbModuleMod option:selected').removeAttr('selected');
        objProgramData.changeModuleVocab(newSelected);
        $('#modal-form-vocabulary').modal('show');
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

    $("#btnSendCategory").click(function(){
        console.log('clicou salvar');
        if (!$("#category-form").valid()) {
            return false;
        }
        $.ajax({
            type: "POST",
            url: path + '/admin/program/categoryinsert',
            dataType: 'json',
            data: {
                _token: $("#_token").val(),
                moduleID: $("#cmbModuleMod").val(),
                categoryName: $("#txtNewCategory").val(),
                txtSmartyCat: $("#txtCatSmartyVar").val()
            },
            error: function (ret) {
                modalAlertMultiple('danger',aLang['Alert_failure'].replace (/\"/g, ""),'alert-category');
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
                    modalAlertMultiple('danger',aLang['Alert_failure'].replace (/\"/g, ""),'alert-category');
                }
            }
        });

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
    $("#create-program-form").validate({
        ignore:[],
        rules: {
            txtName:        {
                required: true,
                minlength: 3,
                remote: {
                    url: path + "/admin/program/checkProgram",
                    type: "post",
                    dataType:'json',
                    data:{
                        _token: function(element){return $('#_token').val();},
                        categoryID: function(element){return $('#cmbCategory').val();}
                    }
                }
            },
            txtController:  "required",
            txtSmarty:      "required",
            cmbCategory:    "required"
        },
        messages: {
            txtName:        {
                required:makeSmartyLabel('Alert_field_required'), 
                minlength:makeSmartyLabel('Alert_word_min_letters')
            },
            txtController:  makeSmartyLabel('Alert_field_required'),
            txtSmarty:      makeSmartyLabel('Alert_field_required'),
            cmbCategory:    makeSmartyLabel('Alert_field_required')
        }
    });

    $("#update-program-form").validate({
        ignore:[],
        rules: {
            txtName:        {
                required: true,
                minlength: 3,
                remote: {
                    url: path + "admin/program/checkProgram",
                    type: "post",
                    dataType:'json',
                    data:{
                        _token: function(element){return $('#_token').val();},
                        categoryID: function(element){return $('#cmbCategory').val();},
                        programID: function(element){return $('#idprogram').val();}
                    }
                }
            },
            txtController:  "required",
            txtSmarty:      "required",
            cmbCategory:    "required"
        },
        messages: {
            txtName:        {
                required:makeSmartyLabel('Alert_field_required'), 
                minlength:makeSmartyLabel('Alert_word_min_letters')
            },
            txtController:  makeSmartyLabel('Alert_field_required'),
            txtSmarty:      makeSmartyLabel('Alert_field_required'),
            cmbCategory:    makeSmartyLabel('Alert_field_required')
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

    $("#category-form").validate({
        ignore:[],
        rules: {
            txtNewCategory: {
                required: true,
                minlength: 3,
                remote: {
                    url: path + "/admin/program/checkCategory",
                    type: "post",
                    dataType:'json',
                    data:{
                        _token: function(element){return $('#_token').val();},
                        moduleID: function(element){return $('#cmbModuleMod').val();}
                    }
                }
            }
        },
        messages: {
            txtNewCategory: {
                required:makeSmartyLabel('Alert_field_required'), 
                minlength:makeSmartyLabel('Alert_word_min_letters')
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
        $('#category-form').trigger('reset');
        $('#vocabulary-form').trigger('reset');
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
