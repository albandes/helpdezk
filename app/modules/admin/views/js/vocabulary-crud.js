//Initial settings of Dropzone
Dropzone.autoDiscover = false;
var dropzonefiles = 0,filesended = 0, flgerror = 0, errorname=[], upname=[], hasRestrict=0, btnClicked = 0, avaliableSave = true;

var objVocabularyData = {
    changeModule: function(selectedID) {
        $.post(path+"/admin/program/ajaxModule",{_token:$("#_token").val(),selectedID:selectedID},
            function(valor) {
                $("#cmbModule").html(valor);
                $("#cmbModule").trigger("change");
                return false;
            });
    }
};

$(document).ready(function () {
    countdown.start(timesession);
    
    /**
     * Select2
     */
    $('#cmbModule').select2({width:"100%",height:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10});
    $('#localeID_1').select2({width:"100%",height:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10});

    if($("#update-vocabulary-form").length > 0){
        $('.localeUpd').select2({width:"100%",height:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10});
    }
    
    if($("#modal-add-module-form").length > 0) {
        $('#modal-cmb-module').select2({width:"100%",height:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-add-vocabulary-form')});

        /**
         * Mask
         */
        $('#modulePath').mask('ZZZ', {
            translation: {
              'Z': {
                pattern: /[a-z|A-Z]/, optional: true
              }
            }
        });

        /**
         * iCheck - checkboxes/radios styling
         */
        $('#moduleDefault,#moduleRestrictIp').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });

        /**
         * Dropzone
         */
        var myDropzone = new Dropzone("#moduleLogo", {  url: path + "/admin/modules/saveLogo/",
            method: "post",
            dictDefaultMessage: "<i class='fa fa-file-image fa-2x' aria-hidden='true'></i><br>" + vocab['Drag_image_msg'],
            createImageThumbnails: true,
            maxFiles: 1,
            acceptedFiles: '.jpg, .jpeg, .png, .gif',
            parallelUploads: 1,
            autoProcessQueue: false,
            addRemoveLinks: true
        });

        myDropzone.on("maxfilesexceeded", function(file) {
            this.removeFile(file);
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
            var list,msg,typeMsg;

            if(errorname.length == 0 && (filesended == dropzonefiles)){
                if(btnClicked=="1"){
                    saveData(upname,'add');
                }else if(btnClicked=="2"){
                    saveData(upname,'upd')
                }                            
            }else{
                var totalAttach = dropzonefiles - filesended;
                list = '<h4>'+vocab['files_not_attach_list']+'</h4><br>';
                errorname.forEach(element => {
                    list = list+element+'<br>';
                });
                list = list+'<br><strong>'+vocab['logo_attach_after']+'</strong>';
                typeMsg = 'warning';
                msg = vocab['save_anyway_question'];
                showNextStep(list,msg,typeMsg,totalAttach);
            }        
            
            dropzonefiles = 0; 
            filesended = 0;
            flgerror = 0;
        });

        $("#moduleRestrictIp").on('ifChecked ifUnchecked',function(e){
            if(e.type == 'ifChecked'){
                $("#restrictionsLine").removeClass('d-none');
                hasRestrict = 1;
            }else{
                $("#restrictionsLine").addClass('d-none');
                hasRestrict = 0;
            }
        });

        // -- add new row to restrictions list
        $("#btnAddRow").click(function(){
            duplicateRow();
        });
    }
    
    /**
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/admin/vocabulary/index');

    $("#btnCreateVocabulary").click(function(){

        if (!$("#create-vocabulary-form").valid()) {
            return false ;
        }
        
        var checkFields = validateFields();
        if (!checkFields[0][0]) {console.log(checkFields[0][0]);
            modalAlertMultiple('danger',checkFields[1][0],'alert-create-vocabulary');
            return false ;
        }
        
        if(!$("#btnCreateVocabulary").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/vocabulary/createVocabulary',
                dataType: 'json',
                data: $("#create-vocabulary-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-create-vocabulary');
                },
                success: function(ret){
                    //console.log(ret);
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
        
                    if(obj.success){
                        $('#modal-vocabulary-module').val($("#cmbModule").find('option:selected').text());
                        $('#modal-vocabulary-name').val(obj.vocabularyName);
        
                        $('#modal-vocabulary-create').modal('show');      
                    }else{
                        modalAlertMultiple('danger',vocab['Alert_failure'],'alert-create-vocabulary');
                    }
                },
                beforeSend: function(){
                    $("#btnCancel").addClass('disabled');
                    $("#btnCreateVocabulary").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnCancel").removeClass('disabled');
                    $("#btnCreateVocabulary").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                }
            });
        }
        
    });

    $("#btnUpdateVocabulary").click(function(){

        if (!$("#update-vocabulary-form").valid()) {
            return false ;
        }

        var checkFields = validateFields();
        if (!checkFields[0][0]) {console.log(checkFields[0][0]);
            modalAlertMultiple('danger',checkFields[1][0],'alert-update-vocabulary');
            return false ;
        }

        if(!$("#btnUpdateVocabulary").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/vocabulary/updateVocabulary',
                dataType: 'json',
                data: $("#update-vocabulary-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-update-vocabulary');
                },
                success: function(ret){
                    //console.log(ret);
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
        
                    if(obj.success){
                        showAlert(vocab['Edit_sucess'],'success');
                    }else{
                        modalAlertMultiple('danger',vocab['Alert_failure'],'alert-update-vocabulary');
                    }
                },
                beforeSend: function(){
                    $("#btnCancel").addClass('disabled');
                    $("#btnUpdateVocabulary").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnCancel").removeClass('disabled');
                    $("#btnUpdateVocabulary").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                }
            });
        }

    });

    //show modal to add new module
    $("#btnAddModule").click(function(){
        $('#modal-add-module').modal('show');
    });

    $("#btnAddModuleSave").click(function(){

        if (!$("#modal-add-module-form").valid()) {
            return false ;
        }
        
        if(hasRestrict == 1){
            var checkFields = validateModuleFields();        
            if (!checkFields[0][0]) {
                modalAlertMultiple('danger',checkFields[1][0],'alert-modal-add-module');
                return false ;
            }
        }
        if(!avaliableSave){
            modalAlertMultiple('danger',vocab['restrict_fields_invalid_format'],'alert-modal-add-module');
            return false;
        }
        
        if(!$("#btnAddModuleSave").hasClass('disabled')){
            $("#btnAddModuleSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');

            if (myDropzone.getQueuedFiles().length > 0) {
                btnClicked = "1";
                dropzonefiles = myDropzone.getQueuedFiles().length;
                myDropzone.processQueue();
            } else {
                saveData(upname,'add');
            }
        }
        
    });

    $("#btnNextYes").click(function(){        
        $('#modal-next-step').modal('hide');
        if(btnClicked=="1"){
            saveData(upname,'add');
        }else if(btnClicked=="2"){
            saveData(upname,'upd');
        }      
    });

    $("#btnNextNo").click(function(){
        if (!$("#btnNextNo").hasClass('disabled')) {
            $("#btnNextNo").removeClass('disabled');
            $('#modal-next-step').modal('hide');
            errorname = [];
            upname = [];

            location.href = path + '/admin/modules/index'
        }
    });
    
    // -- add new row to locale list
    $("#btnAddVocabRow").click(function(){
        duplicateVocabRow();
    });

    /**
     * Validate
     */
    $("#create-vocabulary-form").validate({
        ignore:[],
        rules: {
            cmbModule:   {
                required: true
            },
            keyName:{
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                required: true,
                minlength: 3,
                remote: {
                    url: path + "/admin/vocabulary/checkKeyName",
                    type: "post",
                    dataType:'json',
                    data:{
                        _token: function(element){return $('#_token').val();},
                        moduleId: function(element){return $('#cmbModule').val();}
                    }
                },
                noAccent:true
            }
        },
        messages: {
            cmbModule: {required:vocab['Alert_field_required']},
            keyName: {required:vocab['Alert_field_required'], minlength: vocab['Alert_minimum_three_characters']}
        }
    });

    $("#update-vocabulary-form").validate({
        ignore:[],
        rules: {
            cmbModule:   {
                required: true
            },
            keyName:{
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                required: true,
                minlength: 3,
                remote: {
                    url: path + "/admin/vocabulary/checkKeyName",
                    type: "post",
                    dataType:'json',
                    data:{
                        _token: function(element){return $('#_token').val();},
                        moduleId: function(element){return $('#cmbModule').val();},
                        vocabulariesId: function(element){return $("#vocabulariesId").val();}
                    }
                },
                noAccent:true
            }
        },
        messages: {
            cmbModule: {required:vocab['Alert_field_required']},
            keyName: {required:vocab['Alert_field_required'], minlength: vocab['Alert_minimum_three_characters']}
        }
    });

    $("#modal-add-module-form").validate({
        ignore:[],
        rules: {
            moduleName:{
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
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
            modulePath:{
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
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
            moduleKeyName:   {
                required: true,
                minlength: 3
            }
        },
        messages: {
            moduleName: {required:vocab['Alert_field_required'], minlength: vocab['Alert_minimum_three_characters']},
            modulePath: {required:vocab['Alert_field_required'], minlength: vocab['Alert_minimum_three_characters']},
            moduleKeyName: {required:vocab['Alert_field_required'], minlength: vocab['Alert_minimum_three_characters']}
        }
    });

    $.validator.addMethod('noAccent', function(strValue) {
        var re = new RegExp("^[a-zA-Z0-9_]+$","i");
        return re.test(strValue);
    }, vocab['key_no_accents_no_whitespace']);

    /* when the modal is hidden */
    $('#modal-vocabulary-create').on('hidden.bs.modal', function() { 
        location.href = path + "/admin/vocabulary/index";        
    });

    if($("#update-vocabulary-form").length > 0){
        $('#modal-alert').on('hidden.bs.modal', function() { 
            location.href = path + "/admin/vocabulary/index" ;
        });
    }

    $('#modal-add-module').on('hidden.bs.modal', function() { 
        $("#modal-add-module-form").trigger('reset');
        
        $('#moduleDefault,#moduleRestrictIp').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });

        $("#moduleDefault").iCheck('uncheck');
        $("#moduleRestrictIp").iCheck('uncheck');

        if(!$("#restrictionsLine").hasClass('d-none'))
            $("#restrictionsLine").addClass('d-none');
    });

    $('.ipRestriction').keyup(delay(function (e) {
        if(this.value != "")
            validateIp(this.value);
      }, 500));

    $('.lbltooltip').tooltip();
});

function duplicateRow(){
    // First, lets create the new row using the last one as template...
    var clonedRow = $( "#restrictionsList tr:last" ).clone();
    // Take the current identifier, some number in the first cell of the row
    intCurrentRowId = parseInt($( "#restrictNumId:last",clonedRow ).val());
    // Set the new ID
    intNewRowId = intCurrentRowId + 1;
    // Change the current identifier of the row to the new one
    $( "#restrictNumId:last", clonedRow ).val( intNewRowId );

    // Change the Id / Name or anything you want for the new attribs...
    //here is where you need to add a lot of stuff to change the id of your variables

    // The following code works without problems on Firefox or IE7
    $( "#ipNumber_"+ intCurrentRowId , clonedRow ).attr( { "id" :"ipNumber_" + intNewRowId, "accesskey" : intNewRowId } ).val("");

    // Add to the new row to the original table
    $( "#restrictionsList" ).append( clonedRow );    

    // And finally change the ID of the last row to something we can
    //delete later, not sure why can not be done before the append :S
    $( "#restrictionsList tr:last" ).attr( "id", "restrictItem_" + intNewRowId );

    $( "#ipNumber_"+ intNewRowId).focus();

    $('.ipRestriction').keyup(delay(function (e) {
        if(this.value != "")
            validateIp(this.value);
    }, 500));
}

function removeRow(id,strTableName){
    var i = id.parentNode.parentNode.rowIndex,
        alertSection = 'alert-restrict-list';
    
    if($("#"+strTableName+" tbody tr").length == 1){
        modalAlertMultiple('info',vocab['Unable_to_Delete_Required_Items'],alertSection);
    }else{
        document.getElementById(strTableName).deleteRow(i);
    }
}

function validateModuleFields(){
    var ret = [], isEmpty=0;

    // check restrictions items
    $("input[name='ipNumber[]']").each(function(index, element) {
        if($(this).val() == ""){
            isEmpty = 1;
        }
    }); 
    
    if(isEmpty == 1){
        ret.push([false],[vocab['restrict_empty']]);
        return ret;
    }
    
    ret.push([true],['']);
    return ret;
}

function validateIp(ip){
    var re = new RegExp("^(((([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])(((\/([4-9]|[12][0-9]|3[0-2]))?)|\s?-\s?((([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))))(-\s?|$))","gm"),
        res, alert = 'alert-modal-add-module';
        
    if(!re.test(ip)){
        modalAlertMultiple('danger',vocab['restrict_invalid_format'],alert);
        avaliableSave = false;
    }else{
        avaliableSave = true;
    }
}

function delay(callback, ms) {
    var timer = 0;
    return function() {
      var context = this, args = arguments;
      clearTimeout(timer);
      timer = setTimeout(function () {
        callback.apply(context, args);
      }, ms || 0);
    };
}

function saveData(aAttachs,op)
{
    var method = op == 'add' ? 'createModule' : 'updateModule', 
        alert = op == 'add' ? 'alert-modal-add-module' : 'alert-modal-upd-module',
        btn = op == 'add' ? 'btnAddModuleSave' : 'btnUpdModuleSave',
        formName = op == 'add' ? 'modal-add-module-form' : 'modal-upd-module-form',
        data_save = $('#'+formName).serialize() + "&_token=" + $("#_token").val();
    
    // Add attachment's object to form serialized
    if(aAttachs.length > 0){
        data_save = data_save + "&attachments%5B%5D="+aAttachs;
    }

    $.ajax({
        type: "POST",
        url: path + '/admin/modules/'+ method,
        dataType: 'json',
        data: data_save,
        error: function (ret) {
            modalAlertMultiple('danger',vocab['Alert_failure'],alert);
        },
        success: function(ret){
            //console.log(ret);
            var obj = jQuery.parseJSON(JSON.stringify(ret));

            if(obj.success){
                if(op == 'add'){
                    modalAlertMultiple('success',vocab['Alert_sucess_module'],alert);
                    objVocabularyData.changeModule(obj.moduleId);
                    setTimeout(function(){
                        $('#modal-add-module').modal('hide');
                    },2000);
                }else{
                    showAlert(vocab['Edit_sucess'],'success');
                }
                
            }else{
                modalAlertMultiple('danger',vocab['Alert_failure'],alert);
            }
        },
        beforeSend: function(){
            /*$("#btnCreateCity").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');*/
        },
        complete: function(){
            $("#"+btn).html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
        }
    });

    return false ;

}

function duplicateVocabRow(){
    // First, lets create the new row using the last one as template...
    var clonedRow = $( "#localeList tr:last" ).clone();
    // Take the current identifier, some number in the first cell of the row
    intCurrentRowId = parseInt($( "#localeNumId:last",clonedRow ).val());
    // Set the new ID
    intNewRowId = intCurrentRowId + 1;
    // Change the current identifier of the row to the new one
    $( "#localeNumId:last", clonedRow ).val( intNewRowId );

    // Change the Id / Name or anything you want for the new attribs...
    //here is where you need to add a lot of stuff to change the id of your variables

    // The following code works without problems on Firefox or IE7
    $( "#localeID_"+ intCurrentRowId , clonedRow ).attr( { "id" :"localeID_" + intNewRowId, "accesskey" : intNewRowId } ).removeAttr("data-select2-id","aria-hidden","tabindex").removeClass("select2-hidden-accessible");
    $( "#keyValue_"+ intCurrentRowId , clonedRow ).attr( { "id" :"keyValue_" + intNewRowId, "accesskey" : intNewRowId } ).val("");
    if($("#update-vocabulary-form").length > 0){
        $( "#vocabularyID_"+ intCurrentRowId , clonedRow ).attr( { "id" :"vocabularyID_" + intNewRowId, "accesskey" : intNewRowId } ).val(0);
    }

    // Add to the new row to the original table
    $( "#localeList" ).append( clonedRow );
   
    
    $('#localeID_' +intNewRowId + ' + span').remove();
    $('#localeID_' +intNewRowId+' > option').removeAttr("data-select2-id","aria-hidden","tabindex");
    $('#localeID_' +intNewRowId).val("");
    $('#localeID_' +intNewRowId).select2({width:"100%",height:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10});

    // And finally change the ID of the last row to something we can
    //delete later, not sure why can not be done before the append :S
    $( "#localeList tr:last" ).attr( "id", "localeItem_" + intNewRowId );

    //$( "#localeID_"+ intNewRowId).focus();
}

function removeVocabRow(id,strTableName){
    var i = id.parentNode.parentNode.rowIndex,
        alertSection = 'alert-locale-list';
    
    if($("#"+strTableName+" tbody tr").length == 1){
        modalAlertMultiple('info',vocab['Unable_to_Delete_Required_Items'],alertSection);
    }else{
        document.getElementById(strTableName).deleteRow(i);
    }
}

function validateFields(){
    var ret = [], aLocale=[], isNotSelected=0, localeExists=0,isEmpty=0,msg;

    // check locale items
    $("select[name='localeID[]']").each(function(index, element) {
        if($(this).val()==""){
            isNotSelected=1;
        }else{   
            if(jQuery.inArray($(this).val(), aLocale) !== -1)
            {
                localeExists=1;                        
            }else{                
                aLocale.push($(this).val());
            } 
        }
        console.log($(this).val());
    }); 
                
    if(isNotSelected==1){
        ret.push([false],[vocab['one_more_not_select_locale']]);
        return ret;
    }

    if(localeExists==1){
        ret.push([false],[vocab['Duplicate_author']]);
        return ret;
    }

    // check adquisiton date items
    $("input[name='keyValue[]']").each(function(index, element) {
        if($(this).val()==""){
            isEmpty=1;
        }           
    });

    if(isEmpty==1){
        msg = vocab['one_more_no_key_value'];
        ret.push([false],[msg]);
        return ret;
    }

    ret.push([true],['']);
    return ret;
}
            