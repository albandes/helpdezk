//Initial settings of Dropzone
Dropzone.autoDiscover = false;
var dropzonefiles = 0,filesended = 0, flgerror = 0, errorname=[], upname=[], hasRestrict=0, btnClicked = 0, avaliableSave = true;

$(document).ready(function () {
    countdown.start(timesession);
    
    /**
     * Select2
     */
    if($("#update-module-form").length > 0){
        $('#modal-cmb-module').select2({width:"100%",height:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-add-vocabulary-form')});
        $('#localeID_1').select2({width:"100%",height:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-add-vocabulary-form')});
    }

    /**
     * Mask
     */
    if($("#create-module-form").length > 0) {
        $('#modulePath').mask('ZZZ', {
            translation: {
              'Z': {
                pattern: /[a-z|A-Z]/, optional: true
              }
            }
        });
    }

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
    
    /**
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/admin/modules/index');

    $("#btnCreateModule").click(function(){

        if (!$("#create-module-form").valid()) {
            return false ;
        }
        
        if(hasRestrict == 1){
            var checkFields = validateFields();        
            if (!checkFields[0][0]) {
                modalAlertMultiple('danger',checkFields[1][0],'alert-create-module');
                return false ;
            }
        }

        if(!avaliableSave){
            modalAlertMultiple('danger',vocab['restrict_fields_invalid_format'],'alert-create-module');
            return false;
        }
        
        if(!$("#btnCreateModule").hasClass('disabled')){
            $("#btnCreateModule").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');

            if (myDropzone.getQueuedFiles().length > 0) {
                btnClicked = "1";
                dropzonefiles = myDropzone.getQueuedFiles().length;
                myDropzone.processQueue();
            } else {
                saveData(upname,'add');
            }
        }
        
    });

    $("#btnUpdateModule").click(function(){

        if (!$("#update-module-form").valid()) {
            return false ;
        }

        if(hasRestrict == 1){
            var checkFields = validateFields();        
            if (!checkFields[0][0]) {
                modalAlertMultiple('danger',checkFields[1][0],'alert-update-module');
                return false ;
            }
        }

        if(!avaliableSave){
            modalAlertMultiple('danger',vocab['restrict_fields_invalid_format'],'alert-update-module');
            return false;
        }

        if(!$("#btnUpdateModule").hasClass('disabled')){
            $("#btnUpdateModule").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');

            if (myDropzone.getQueuedFiles().length > 0) {
                btnClicked = "2";
                dropzonefiles = myDropzone.getQueuedFiles().length;
                myDropzone.processQueue();
            } else {
                saveData(upname,'upd');
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
    
    //show modal to add vocabulary key name
    $("#btnAddKeyName").click(function(){
        $("#modal-cmb-module").val($("#moduleId").val());
        $("#modal-cmb-module").trigger("change");
        $("#modal-add-vocabulary").modal('show');
    });

    // -- add new row to locale list
    $("#btnAddVocabRow").click(function(){
        duplicateVocabRow();
    });

    $("#btnAddVocabSave").click(function(){

        if (!$("#modal-add-vocabulary-form").valid()) {
            return false ;
        }
        
        var checkFields = validateVocabFields();
        if (!checkFields[0][0]) {console.log(checkFields[0][0]);
            modalAlertMultiple('danger',checkFields[1][0],'alert-modal-add-vocabulary');
            return false ;
        }
        
        if(!$("#btnAddVocabSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/vocabulary/createVocabulary',
                dataType: 'json',
                data: $("#modal-add-vocabulary-form").serialize() + "&_token=" + $("#_token").val(),
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-modal-add-vocabulary');
                },
                success: function(ret){
                    //console.log(ret);
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
        
                    if(obj.success){
                        modalAlertMultiple('success',vocab['Alert_inserted'],"alert-modal-add-vocabulary");
                        setTimeout(function(){
                            $("#modal-add-vocabulary").modal('hide');
                        },2000);
                    }else{
                        modalAlertMultiple('danger',vocab['Alert_failure'],'alert-modal-add-vocabulary');
                    }
                },
                beforeSend: function(){
                    $("#btnAddVocabCancel").addClass('disabled');
                    $("#btnAddVocabSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnAddVocabCancel").removeClass('disabled');
                    $("#btnAddVocabSave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                }
            });
        }
        
    });

    // -- add new row to restrictions list
    $("#btnAddRow").click(function(){
        duplicateRow();
    });

    /**
     * Validate
     */
    $("#create-module-form").validate({
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

    $("#update-module-form").validate({
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
                        _token: function(element){return $('#_token').val();},
                        moduleId: function(element){return $('#moduleId').val();}
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
            moduleKeyName: {required:vocab['Alert_field_required'], minlength: vocab['Alert_minimum_three_characters']}
        }
    });

    $("#modal-add-vocabulary-form").validate({
        ignore:[],
        rules: {
            'modal-cmb-module':   {
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
                        moduleId: function(element){return $('#modal-cmb-module').val();}
                    }
                },
                noAccent:true
            }
        },
        messages: {
            'modal-cmb-module': {required:vocab['Alert_field_required']},
            keyName: {required:vocab['Alert_field_required'], minlength: vocab['Alert_minimum_three_characters']}
        }
    });

    $.validator.addMethod('noAccent', function(strValue) {
        var re = new RegExp("^[a-zA-Z0-9_]+$","i");
        return re.test(strValue);
    }, vocab['key_no_accents_no_whitespace']);

    /* when the modal is hidden */
    $('#modal-module-create').on('hidden.bs.modal', function() { 
        location.href = path + "/admin/modules/index";        
    });

    if($("#update-module-form").length > 0){
        $('#modal-alert').on('hidden.bs.modal', function() { 
            location.href = path + "/admin/modules/index" ;
        });
    }

    $('#modal-add-vocabulary').on('hidden.bs.modal', function() { 
        $("#modal-add-vocabulary-form").trigger('reset');
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

    $('.ipRestriction').keyup(delay(function (e) {
        if(this.value != "")
            validateIp(this.value);
      }, 500));

    $('.lbltooltip').tooltip();
});

function saveData(aAttachs,op)
{
    var method = op == 'add' ? 'createModule' : 'updateModule', 
        alert = op == 'add' ? 'alert-create-module' : 'alert-update-module',
        btn = op == 'add' ? 'btnCreateModule' : 'btnUpdateModule',
        formName = op == 'add' ? 'create-module-form' : 'update-module-form',
        data_save = $('#'+formName).serialize();
    
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
                    $('#modal-module-code').val(obj.moduleId);
                    $('#modal-module-name').val(obj.moduleName);
                    $('#modal-module-path').val(obj.modulePath);

                    if(!obj.dirCreated){
                        $('#module-structure-alert').html(obj.dirMessage);

                        if($('#moduleStrAlertLine').hasClass("d-none"))
                            $('#moduleStrAlertLine').removeClass("d-none");
                    }
    
                    $('#modal-module-create').modal('show');
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

function validateFields(){
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
        res, alert = ($("#create-module-form").length > 0) ? 'alert-create-module' : 'alert-update-module';
    
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

    // Add to the new row to the original table
    $( "#localeList" ).append( clonedRow );

    $('#localeID_' +intNewRowId + ' + span').remove();
    $('#localeID_' +intNewRowId).val("");
    $('#localeID_' +intNewRowId).select2({width:"100%",height:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#localeList tbody')});

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

function validateVocabFields(){
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
            