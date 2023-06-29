var objFeatures = {
    updateConfig: function(idConfig,newVal){
        $.ajax({
            type: "POST",
            url: path + '/admin/features/updateConfig',
            dataType: 'json',
            data: {
                id : idConfig,
                newVal : newVal,
                _token : $('#_token').val()
            },
            error: function (ret) {
                showAlert(vocab['Permission_error'],'danger');
            },
            success: function(ret) {
                var obj = jQuery.parseJSON(JSON.stringify(ret));
            
                if(!obj.success){       
                    showAlert(vocab['Permission_error'],'danger');
                }else{
                    showAlert(vocab['Edit_sucess'],'success');
                }
            }
        });
    },
    changePopPort: function(){
        var val = $('#cmbPopType').val(), popport = $('#popPort');
        
        switch(val){
            case "GMAIL":
                popport.val("993");
                break;
            case "POP":
                popport.val("110");
                break;
            case "IMAP":
                popport.val("143");
                break;
            default:
                popport.val("");
                break;
        }
    },
    viewConfigs: function(){
        var idmodule = $('#cmbModule').val();

        if(idmodule == '1'){
            $('.main-features').removeClass('d-none');
            $('#modulesFeatures').addClass('d-none');
            $('#add-setting-line').addClass('d-none');
        }else{

            $.ajax({
                type: "POST",
                url: path + '/admin/features/loadModuleConfs',
                data: {
                    idmodule : idmodule,
                    _token : $('#_token').val()
                },
                error: function (ret) {
                    showAlert(vocab['Alert_get_data'],'danger');
                },
                success: function(ret) {

                    if(ret){
                        $("#modulesFeatures").html(ret);
                        $('#modulesFeatures').removeClass('d-none');
                        if(idmodule != '2' && idmodule != '3'){
                            $('#add-setting-line').removeClass('d-none');
                        }else{
                            $('#add-setting-line').addClass('d-none');
                        }


                        $('.main-features').addClass('d-none');

                        $('input[type=checkbox]').iCheck({
                            checkboxClass: 'icheckbox_square-green',
                            radioClass: 'iradio_square-green'
                        });

                        $('.changeConfigStatus').on('ifChecked ifUnchecked',function(e){
                            var idconfig = e.target.attributes.value.nodeValue;
                    
                            if(e.type == 'ifChecked'){
                                objFeatures.updateConfig(idconfig,'1');
                            }else{
                                objFeatures.updateConfig(idconfig,'0');
                            }
                        });
                    
                        $('.changeConfigValue').change(function(e){        
                            var idConfig = e.target.attributes.id.nodeValue, configVal = $("#"+idConfig).val();                                
                    
                            objFeatures.updateConfig(idConfig,configVal);
                        });

                        $('.removeConfig').click(function(){
                            objFeatures.removeConfig($(this).data('id'));
                        });


                    }
                    else {
                        showAlert(vocab['Alert_get_data'],'danger');
                    }
                },
                complete: function(){
                    // Demo Version
                    if (demoVersion == 1){
                        setDemoVersion();
                    }
                }
            });
        }
        return false;
    },
    changeLogServer: function(){
        var hosttype = $('#logHostType').val();

        if(hosttype == 'remote'){
            $('#logServer').removeAttr('disabled');
        }else{
            $('#logServer').attr('disabled','disabled');
        }
    },
    removeConfig: function(idConfig){
        $.ajax({
            type: "POST",
            url: path + '/admin/features/removeConfig',
            dataType: 'json',
            data: {
                id : idConfig,
                _token : $('#_token').val()
            },
            error: function (ret) {
                showAlert(vocab['Permission_error'],'danger');
            },
            success: function(ret) {
                var obj = jQuery.parseJSON(JSON.stringify(ret));
            
                if(!obj.success){       
                    showAlert(vocab['Permission_error'],'danger');
                }else{
                    showAlert(vocab['Edit_sucess'],'success');
                    objFeatures.viewConfigs();
                }
            }
        });
    }
}

$(document).ready(function () {
    countdown.start(timesession);

    /**
     * Demo version
     */
    if (demoVersion == 1){
        setDemoVersion();
        $('#cmbModule').removeAttr('disabled');
        $("#title").append("<p class='text-danger'><b>Disabled in demo version !!!</b></p>");
    }
    
    /**
     * Select2
     */
    $('#cmbModule').select2({width:"100%",height:"100%",placeholder:vocab['plh_module_select'],allowClear:true,minimumResultsForSearch: 10});
    $('#cmbPopType').select2({width:"100%",height:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10});
    $('#cmbLdapType').select2({width:"100%",height:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10});
    $('#cmbDefCountry').select2({width:"100%",height:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10});

    /**
     * iCheck - checkboxes/radios styling
     */
    $('input[type=checkbox]').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    //Summernote Editor
    $('#emailHeader').summernote(
        {
            toolbar:[
                ["style",["style"]],
                ["font",["bold","italic","underline","clear"]],
                ["fontname",["fontname"]],["color",["color"]],
                ["para",["ul","ol","paragraph"]],
                ["table",["table"]],
                ["insert",["link"]],
                ["view",["codeview"]],
                ["help",["help"]]
            ],
            disableDragAndDrop: true,
            minHeight: null,  // set minimum height of editor
            maxHeight: 250,   // set maximum height of editor
            height: 250,      // set editor height
            //width: 750,       // set editor width
            focus: false,     // set focus to editable area after initializing summernote

        }
    );

    $('#emailFooter').summernote(
        {
            toolbar:[
                ["style",["style"]],
                ["font",["bold","italic","underline","clear"]],
                ["fontname",["fontname"]],["color",["color"]],
                ["para",["ul","ol","paragraph"]],
                ["table",["table"]],
                ["insert",["link"]],
                ["view",["codeview"]],
                ["help",["help"]]
            ],
            disableDragAndDrop: true,
            minHeight: null,  // set minimum height of editor
            maxHeight: 250,   // set maximum height of editor
            height: 250,      // set editor height
            //width: 750,       // set editor width
            focus: false,     // set focus to editable area after initializing summernote

        }
    );

    $('#maintenanceMsg').summernote(
        {
            toolbar:[
                ["help",[]]
            ],
            disableDragAndDrop: true,
            minHeight: null,  // set minimum height of editor
            maxHeight: 250,   // set maximum height of editor
            height: 250,      // set editor height
            //width: 750,       // set editor width
            focus: false,     // set focus to editable area after initializing summernote

        }
    );

    $('#cmbModule').change(function(e){
        objFeatures.viewConfigs();
    });

    $('#cmbPopType').change(function(){
        objFeatures.changePopPort();
    });

    /* $('#logHostType').change(function(){
        objFeatures.changeLogServer();
    }); */
    
    /**
     * Buttons
     */
    $("#btnEmailFeatSave").click(function(){
        if(!$("#btnEmailFeatSave").hasClass('disabled')){
            var mailHeader = $('#emailHeader').summernote('code'),
            mailFooter = $('#emailFooter').summernote('code');

            $.ajax({
                type: "POST",
                url: path + '/admin/features/saveEmailChanges',
                data: $('#email-feature-form').serialize()+ '&_token=' + $('#_token').val() + '&mailHeader=' + mailHeader + '&mailFooter=' + mailFooter, 
                dataType: 'json',
                error: function (ret) {
                    showAlert(vocab['Edit_failure'],'danger');
                },
                success: function(ret) {
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    //console.log(obj);
                    if(obj.success) {
                        showAlert(vocab['Edit_sucess'],'success');
                    } else {
                        showAlert(vocab['Edit_failure'],'danger');
                    }
                },
                beforeSend: function(){
                    $("#btnEmailFeatSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnEmailFeatSave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                }
            });
        }
    });

    $("#btnPopFeatSave").click(function(){
        if(!$("#btnPopFeatSave").hasClass('disabled')){

            $.ajax({
                type: "POST",
                url: path + '/admin/features/savePopChanges',
                data: $('#pop-feature-form').serialize()+ '&_token=' + $('#_token').val(), 
                dataType: 'json',
                error: function (ret) {
                    showAlert(vocab['Edit_failure'],'danger');
                },
                success: function(ret) {
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    //console.log(obj);
                    if(obj.success) {
                        showAlert(vocab['Edit_sucess'],'success');
                    } else {
                        showAlert(vocab['Edit_failure'],'danger');
                    }
                },
                beforeSend: function(){
                    $("#btnPopFeatSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnPopFeatSave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                }
            });
        }
    });

    $("#btnLdapFeatSave").click(function(){
        if(!$("#btnLdapFeatSave").hasClass('disabled')){

            $.ajax({
                type: "POST",
                url: path + '/admin/features/saveLdapChanges',
                data: $('#ldap-feature-form').serialize()+ '&_token=' + $('#_token').val(), 
                dataType: 'json',
                error: function (ret) {
                    showAlert(vocab['Edit_failure'],'danger');
                },
                success: function(ret) {
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    //console.log(obj);
                    if(obj.success) {
                        showAlert(vocab['Edit_sucess'],'success');
                    } else {
                        showAlert(vocab['Edit_failure'],'danger');
                    }
                },
                beforeSend: function(){
                    $("#btnLdapFeatSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnLdapFeatSave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                }
            });
        }
    });

    $("#btnMaintenanceFeatSave").click(function(){
        if(!$("#btnMaintenanceFeatSave").hasClass('disabled')){
            var msg = $('#maintenanceMsg').summernote('code');

            $.ajax({
                type: "POST",
                url: path + '/admin/features/saveMaintenanceChanges',
                data: $('#maintenance-feature-form').serialize()+ '&_token=' + $('#_token').val() + '&maintenanceMsg=' + msg, 
                dataType: 'json',
                error: function (ret) {
                    showAlert(vocab['Edit_failure'],'danger');
                },
                success: function(ret) {
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    //console.log(obj);
                    if(obj.success) {
                        showAlert(vocab['Edit_sucess'],'success');
                    } else {
                        showAlert(vocab['Edit_failure'],'danger');
                    }
                },
                beforeSend: function(){
                    $("#btnMaintenanceFeatSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnMaintenanceFeatSave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                }
            });
        }
    });

    /*  $("#btnSaveLog").click(function(){
        $.ajax({
            type: "POST",
            url: path + '/admin/features/saveLogChange',
            data:$('#formLog').serialize() + '&_token=' + $('#_token').val(),
            dataType: 'json',
            error: function (ret) {
                showAlert(makeSmartyLabel('Edit_failure'),'danger');
            },
            success: function(ret) {

                var obj = jQuery.parseJSON(JSON.stringify(ret));
                //console.log(obj);
                if(obj.status == 'OK') {
                    showAlert(makeSmartyLabel('Edit_sucess'),'success');
                } else {
                    showAlert(makeSmartyLabel('Edit_failure'),'danger');
                }
            },
            beforeSend: function(){
                $("#btnSaveLog").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
            },
            complete: function(){
                $("#btnSaveLog").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
            }
        });

    }); */

    $("#btnMiscFeatSave").click(function(){
        if(!$("#btnMiscFeatSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/features/saveMiscChanges',
                data: $('#miscellaneous-feature-form').serialize()+ '&_token=' + $('#_token').val(), 
                dataType: 'json',
                error: function (ret) {
                    showAlert(vocab['Edit_failure'],'danger');
                },
                success: function(ret) {
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    //console.log(obj);
                    if(obj.success) {
                        showAlert(vocab['Edit_sucess'],'success');
                    } else {
                        showAlert(vocab['Edit_failure'],'danger');
                    }
                },
                beforeSend: function(){
                    $("#btnMiscFeatSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnMiscFeatSave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                }
            });
        }
    });

    $("#btnAddSetting").click(function(){
        var moduleID = $("#cmbModule").val(), moduleName = $('#cmbModule').find(":selected").text(),
            _token = $("#_token").val();
        
        
        /* $.post(path + '/admin/features/ajaxFeatureCategory', {
           moduleID: moduleID
        }, function(response) {

            if (response == false) {
                showAlert(makeSmartyLabel('Alert_get_data'),'danger');
            }else{
                $("#idmoduleAddFeat").val(moduleID);
                $("#moduleName").html(moduleName);
                $("#cmbFeatureCat").html(response);
                $("#cmbFeatureCat").trigger("chosen:updated");
                $("#modal-add-feature").modal('show');
            }
        }); */
        $("#modal-add-feature").modal('show');

    });

    $("#btnSaveNewFeat").click(function(){

        if (!$("#feature-add-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/admin/features/saveNewFeature',
            dataType: 'json',
            data: $('#feature-add-form').serialize() + "&_token=" + $('#_token').val(),
            error: function (ret) {
                modalAlertMultiple('danger', makeSmartyLabel('Alert_failure'),'alert-add-feature');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if($.isNumeric(obj.idfeature)) {
                    modalAlertMultiple('success',makeSmartyLabel('Alert_inserted'),'alert-add-feature');

                    setTimeout(function(){
                        $('#modal-add-feature').modal('hide');
                        $('#featureDefault').iCheck('unCheck');
                        $('#feature-add-form').trigger('reset');
                        objFeatures.viewConfigs();
                    },2000);

                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-add-feature');
                }
            },
            beforeSend: function(){
                $("#btnSaveNewFeat").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                $("#btnCancel").addClass('disabled');
            },
            complete: function(){
                $("#btnSaveNewFeat").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                $("#btnCancel").removeClass('disabled');
            }
        });
    });

    $("#btnAddConfCateg").click(function(){
        var moduleID = $("#cmbModule").val(), moduleName = $('#cmbModule').find(":selected").text();

        $("#idmoduleAddCateg").val(moduleID);
        $("#moduleNameCateg").html(moduleName);
        $("#modal-add-category").modal('show');

    });

    $("#btnSaveNewCateg").click(function(){

        if (!$("#categ-feat-add-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/admin/features/saveNewCategory',
            dataType: 'json',
            data: $('#categ-feat-add-form').serialize() + "&_token=" + $('#_token').val(),
            error: function (ret) {
                modalAlertMultiple('danger', makeSmartyLabel('Alert_failure'),'alert-add-category');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if($.isNumeric(obj.idcategory)) {
                    modalAlertMultiple('success',makeSmartyLabel('Alert_inserted'),'alert-add-category');

                    setTimeout(function(){
                        $('#modal-add-category').modal('hide');
                        $('#categorySetup').iCheck('unCheck');
                        $('#categ-feat-add-form').trigger('reset');
                    },2000);

                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-add-category');
                }
            },
            beforeSend: function(){
                $("#btnSaveNewCateg").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                $("#btnCloseNewCateg").addClass('disabled');
            },
            complete: function(){
                $("#btnSaveNewCateg").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                $("#btnCloseNewCateg").removeClass('disabled');
            }
        });
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

    $('#modal-add-vocabulary').on('hidden.bs.modal', function() { 
        $("#modal-add-vocabulary-form").trigger('reset');
    });

    $('.lbltooltip').tooltip();
});

function setDemoVersion() {
    $(':button').prop('disabled', true);   // Disable all the buttons
    $(':input').prop('disabled', true);    // Disable all the inputs
    $(':checkbox').prop('disabled', true); // Disable all the checkbox
}
