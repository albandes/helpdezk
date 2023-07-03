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
    $('#cmbFeatureCat').select2({width:"100%",height:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-add-feature-form')});
    $('#cmbFeatureType').select2({width:"100%",height:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-add-feature-form')});

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

    $('#cmbFeatureType').change(function(){
        if($(this).val() == 'checkbox'){
            $("#check-field-line").removeClass('d-none');
            $("#new-feature-value-input").addClass('d-none');
        }else{
            $("#new-feature-value-input").removeClass('d-none');
            $("#check-field-line").addClass('d-none');
        }
    });
    
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
                showAlert(vocab['Edit_failure'),'danger');
            },
            success: function(ret) {

                var obj = jQuery.parseJSON(JSON.stringify(ret));
                //console.log(obj);
                if(obj.status == 'OK') {
                    showAlert(vocab['Edit_sucess'),'success');
                } else {
                    showAlert(vocab['Edit_failure'),'danger');
                }
            },
            beforeSend: function(){
                $("#btnSaveLog").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing')).addClass('disabled');
            },
            complete: function(){
                $("#btnSaveLog").html("<i class='fa fa-save'></i> "+ vocab['Save')).removeClass('disabled');
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
    
    //show modal to add new setting
    $("#btnAddSetting").click(function(){
        var moduleId = $("#cmbModule").val(), moduleName = $('#cmbModule').find(":selected").text(),
            _token = $("#_token").val();
        
            $.ajax({
                type: "POST",
                url: path + '/admin/features/modalNewFeature',
                dataType: 'json',
                data: {
                    moduleId : moduleId,
                    _token : _token = _token
                },
                error: function (ret) {
                    showAlert(vocab['Permission_error'],'danger');
                },
                success: function(ret) {
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                
                    if(!obj.success){       
                        showAlert(vocab['Permission_error'],'danger');
                    }else{
                        $("#moduleId").val(moduleId);
                        $("#new-feature-module").val(moduleName);
                        $("#cmbFeatureCat").html(obj.catOptions);
                        $("#cmbFeatureCat").trigger("change");
                        $("#modal-add-feature").modal('show');
                    }
                }
            });
    });

    $("#btnAddFeatureSave").click(function(){

        if (!$("#modal-add-feature-form").valid()) {
            return false ;
        }

        if(!$("#btnAddFeatureSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/features/saveNewFeature',
                dataType: 'json',
                data: $('#modal-add-feature-form').serialize() + "&_token=" + $('#_token').val(),
                error: function (ret) {
                    modalAlertMultiple('danger', vocab['Alert_failure'],'alert-modal-add-feature');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        modalAlertMultiple('success',vocab['Alert_inserted'],'alert-modal-add-feature');
    
                        setTimeout(function(){
                            $('#modal-add-feature').modal('hide');
                            $('#new-feature-default').iCheck('unCheck');
                            $('#new-feature-value-check').iCheck('unCheck');
                            $('#modal-add-feature-form').trigger('reset');
                            objFeatures.viewConfigs();
                        },2000);
    
                    } else {
                        modalAlertMultiple('danger',vocab['Alert_failure'],'alert-modal-add-feature');
                    }
                },
                beforeSend: function(){
                    $("#btnAddFeatureSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnAddFeatureCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnAddFeatureSave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnAddFeatureCancel").removeClass('disabled');
                }
            });
        }
    });

    $("#btnAddCategory").click(function(){
        var moduleId = $("#cmbModule").val(), moduleName = $('#cmbModule').find(":selected").text();

        $("#new-cat-module-id").val(moduleId);
        $("#new-cat-module-name").val(moduleName);
        $("#modal-add-category").modal('show');
    });

    $("#btnAddCategorySave").click(function(){

        if (!$("#modal-add-category-form").valid()) {
            return false ;
        }

        if(!$("#btnAddCategorySave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/features/saveNewCategory',
                dataType: 'json',
                data: $('#modal-add-category-form').serialize() + "&_token=" + $('#_token').val(),
                error: function (ret) {
                    modalAlertMultiple('danger', vocab['Alert_failure'],'alert-modal-add-category');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        modalAlertMultiple('success',vocab['Alert_inserted'],'alert-modal-add-category');
    
                        setTimeout(function(){
                            $('#modal-add-category').modal('hide');
                            $('#modal-add-category-form').trigger('reset');
                        },2000);
    
                    } else {
                        modalAlertMultiple('danger',vocab['Alert_failure'],'alert-modal-add-category');
                    }
                },
                beforeSend: function(){
                    $("#btnAddCategorySave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnAddCategoryClose").addClass('disabled');
                },
                complete: function(){
                    $("#btnAddCategorySave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnAddCategoryClose").removeClass('disabled');
                }
            });
        }
    });

    /**
     * Validate
     */
    $("#modal-add-category-form").validate({
        ignore:[],
        rules: {
            "new-cat-name":{
                required:true,
                remote:{
                    url: path+"/admin/features/checkCategory",
                    type: 'post',
                    data: {
                        moduleId:function(){return $('#new-cat-module-id').val();},
                        _token:function(){return $('#_token').val();}
                    }
                }
            },
            "new-cat-keyname":"required"
        },
        messages: {
            "new-cat-name": {required: vocab['Alert_field_required']},
            "new-cat-keyname": vocab['Alert_field_required']

        }
    });

    $("#modal-add-feature-form").validate({
        ignore:[],
        rules: {
            "new-feature-name":{
                required:true,
                remote:{
                    url: path+"/admin/features/checkField",
                    type: 'post',
                    data: {
                        moduleId:function(){return $('#moduleId').val();},
                        fieldName:'name',
                        _token:function(){return $('#_token').val();}
                    }
                }
            },
            "new-feature-session": {
                required:true,
                remote:{
                    url: path+"/admin/features/checkField",
                    type: 'post',
                    data: {
                        moduleId:function(){return $('#moduleId').val();},
                        fieldName:'session_name',
                        _token:function(){return $('#_token').val();}
                    }
                }
            },
            "new-feature-lang-key":"required",
            cmbFeatureType:"required",
            "new-feature-value-input":{required: function(e){return $('#cmbFeatureType').val() == 'input';}}
        },
        messages: {
            "new-feature-name": {required:vocab['Alert_field_required']},
            "new-feature-session": {required:vocab['Alert_field_required']},
            "new-feature-lang-key": vocab['Alert_field_required'],
            cmbFeatureType: vocab['Alert_field_required'],
            "new-feature-value-input": {required:vocab['Alert_field_required']}

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

    $('#modal-add-feature').on('hidden.bs.modal', function() {
        $('#new-feature-default').iCheck('unCheck');
        $('#new-feature-value-check').iCheck('unCheck'); 
        $("#modal-add-feature-form").trigger('reset');
    });

    $('.lbltooltip').tooltip();
});

function setDemoVersion() {
    $(':button').prop('disabled', true);   // Disable all the buttons
    $(':input').prop('disabled', true);    // Disable all the inputs
    $(':checkbox').prop('disabled', true); // Disable all the checkbox
}
