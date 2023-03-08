//Initial settings of Dropzone
Dropzone.autoDiscover = false;
var dropzonefiles = 0,filesended = 0, flgerror = 0, errorname=[], upname=[], hasRestrict=0, btnClicked = 0, avaliableSave = true;

var objProgramData = {
    changeModule: function(selectedID) {
        $.post(path+"/admin/program/ajaxModule",{_token:$("#_token").val(),selectedID:selectedID},
            function(valor) {
                $("#cmbModule").html(valor);
                $("#cmbModule").trigger("change");
                return false;
            });
    },
    changeCategory: function(selectedID=null) {
        var moduleId = $("#cmbModule").val(), _token = $("#_token").val();

        $.post(path+"/admin/program/ajaxCategory",{_token:_token,moduleId:moduleId,selectedID:selectedID},
            function(valor) {
                $("#cmbCategory").html(valor);
                $("#cmbCategory").trigger("change");
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

$(document).ready(function () {
    countdown.start(timesession);
    
    /**
     * Select2
     */
    $('#cmbModule').select2({width:"100%",height:"100%",placeholder:vocab['plh_module_select'],allowClear:true,minimumResultsForSearch: 10});
    $('#cmbCategory').select2({width:"100%",height:"100%",placeholder:vocab['plh_category_select'],allowClear:true,minimumResultsForSearch: 10});
    $('#modal-cmb-module').select2({width:"100%",height:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-add-vocabulary-form')});
    $('#localeID_1').select2({width:"100%",height:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-add-vocabulary-form')});

    
    if($("#modal-add-module-form").length > 0) {
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
     * iCheck - checkboxes/radios styling
     */
    $('.checkOperations').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    $('#cmbModule').change(function(){
        objProgramData.changeCategory();
    });

    if($("#update-program-form").length > 0) {
        $('#changeOperations').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });

        objProgramData.changeCategory($("#categorySelected").val());

        $('#changeOperations').on('ifChecked ifUnchecked',function(e){
            if(e.type == 'ifChecked'){
                $('#operationsLine').removeClass('d-none');            
            }else{
                $('#operationsLine').addClass('d-none');
            }
        });
    }
    
    /**
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/admin/program/index');

    $("#btnCreateProgram").click(function(){

        if (!$("#create-program-form").valid()){
            return false ;
        }
        
        if(!$("#btnCreateProgram").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/program/createProgram',
                dataType: 'json',
                data: $("#create-program-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],"alert-create-program");
                },
                success: function(ret){
                    //console.log(ret);
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
        
                    if(obj.success){
                        $('#modal-program-code').val(obj.programId);
                        $('#modal-program-module').val($("#cmbModule").find('option:selected').text());
                        $('#modal-program-category').val($("#cmbCategory").find('option:selected').text());
                        $('#modal-program-name').val(obj.programName);
    
                        $('#modal-program-create').modal('show');
                    }else{
                        modalAlertMultiple('danger',vocab['Alert_failure'],"alert-create-program");
                    }
                },
                beforeSend: function(){
                    $("#btnCreateProgram").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnCreateProgram").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                }
            });
        }        
    });

    $("#btnUpdateProgram").click(function(){

        if (!$("#update-program-form").valid()) {
            return false ;
        }

        if(!$("#btnUpdateProgram").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/program/updateProgram',
                dataType: 'json',
                data: $("#update-program-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],"alert-update-program");
                },
                success: function(ret){
                    //console.log(ret);
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
        
                    if(obj.success){
                        showAlert(vocab['Edit_sucess'],'success');
                    }else{
                        modalAlertMultiple('danger',vocab['Alert_failure'],"alert-update-program");
                    }
                },
                beforeSend: function(){
                    $("#btnUpdateProgram").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnUpdateProgram").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
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
            var checkFields = validateFields();        
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

    //show modal to add new program category
    $("#btnAddCategory").click(function(){
        var moduleId = $('#cmbModule').val();
        if(moduleId == 0 || moduleId == ""){
            showAlert(vocab['Select_module'],'danger');
        }else{
            $('#moduleId').val($('#cmbModule').val());
            $('#modal-module-name').val($("#cmbModule").find('option:selected').text());
            
            $('#modal-add-category').modal('show');
        }
    });

    $("#btnAddCategorySave").click(function(){

        if (!$("#modal-add-category-form").valid()){
            return false ;
        }
        
        if(!$("#btnAddCategorySave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/program/createCategory',
                dataType: 'json',
                data: $("#modal-add-category-form").serialize() + "&_token=" + $("#_token").val(),
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],"alert-modal-add-category");
                },
                success: function(ret){
                    //console.log(ret);
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
        
                    if(obj.success){
                        modalAlertMultiple('success',vocab['Alert_sucess_category'],"alert-modal-add-category");
                        objProgramData.changeCategory(obj.categoryId);
                        setTimeout(function(){
                            $('#modal-add-category').modal('hide');
                        },2000);
                    }else{
                        modalAlertMultiple('danger',vocab['Alert_failure'],"alert-modal-add-category");
                    }
                },
                beforeSend: function(){
                    $("#btnAddCategorySave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnAddCategorySave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                }
            });
        }        
    });
    
    //show modal to add vocabulary key name
    $("#btnAddKeyName").click(function(){
        $("#modal-cmb-module").val($("#cmbModule").val());
        $("#modal-cmb-module").trigger("change");
        $("#modal-add-vocabulary").modal('show');
    });

    /**
     * Validate
     */
    $("#create-program-form").validate({
        ignore:[],
        rules: {
            cmbCategory:   {
                required: true
            },
            programName:{
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                required: true,
                minlength: 3,
                remote: {
                    url: path + "/admin/program/checkExist",
                    type: "post",
                    dataType:'json',
                    data:{
                        _token: function(element){return $('#_token').val();},
                        categoryId: function(element){return $('#cmbCategory').val();}
                    }
                }
            },
            programController:{
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                required: true,
                minlength: 3
            },
            programKeyName:   {
                required: true,
                minlength: 3
            }
        },
        messages: {
            cmbCategory: {required:vocab['Alert_field_required']},
            programName: {required:vocab['Alert_field_required'], minlength: vocab['Alert_minimum_three_characters']},
            programController: {required:vocab['Alert_field_required'], minlength: vocab['Alert_minimum_three_characters']},
            programKeyName: {required:vocab['Alert_field_required'], minlength: vocab['Alert_minimum_three_characters']}
        }
    });

    $("#update-program-form").validate({
        ignore:[],
        rules: {
            cmbCategory:   {
                required: true
            },
            programName:{
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                required: true,
                minlength: 3,
                remote: {
                    url: path + "/admin/program/checkExist",
                    type: "post",
                    dataType:'json',
                    data:{
                        _token: function(element){return $('#_token').val();},
                        categoryId: function(element){return $('#cmbCategory').val();},
                        programId: function(element){return $('#programId').val();}
                    }
                }
            },
            programController:{
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                required: true,
                minlength: 3
            },
            programKeyName:   {
                required: true,
                minlength: 3
            }
        },
        messages: {
            cmbCategory: {required:vocab['Alert_field_required']},
            programName: {required:vocab['Alert_field_required'], minlength: vocab['Alert_minimum_three_characters']},
            programController: {required:vocab['Alert_field_required'], minlength: vocab['Alert_minimum_three_characters']},
            programKeyName: {required:vocab['Alert_field_required'], minlength: vocab['Alert_minimum_three_characters']}
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

    $("#modal-add-category-form").validate({
        ignore:[],
        rules: {
            "modal-category-name":{
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                required: true,
                minlength: 3,
                remote: {
                    url: path + "/admin/program/checkCategory",
                    type: "post",
                    dataType:'json',
                    data:{
                        _token: function(element){return $('#_token').val();},
                        moduleId: function(element){return $('#moduleId').val();}
                    }
                }
            },
            "modal-category-keyname":   {
                required: true,
                minlength: 3
            }
        },
        messages: {
            "modal-category-name": {required:vocab['Alert_field_required'], minlength: vocab['Alert_minimum_three_characters']},
            "modal-category-keyname": {required:vocab['Alert_field_required'], minlength: vocab['Alert_minimum_three_characters']}
        }
    });

    /* when the modal is hidden */
    $('#modal-program-create').on('hidden.bs.modal', function() { 
        location.href = path + "/admin/program/index";        
    });

    if($("#update-program-form").length > 0){
        $('#modal-alert').on('hidden.bs.modal', function() { 
            location.href = path + "/admin/program/index" ;
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

    $('#modal-add-category').on('hidden.bs.modal', function() { 
        $("#modal-add-category-form").trigger('reset');
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
                    objProgramData.changeModule(obj.moduleId);
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
