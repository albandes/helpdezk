$(document).ready(function () {
    countdown.start(timesession);
    /**
     * Select2
     */
    $('#cmbSetting').select2({width:"100%",height:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10});
    $('#cmbLocale').select2({width:"100%",height:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10});
    $('#cmbFeatureType').select2({width:"100%",height:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-add-feature-form')});
    
    /**
     * Summernote
     */
    $('#template-body').summernote(
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
            maxHeight: 400,   // set maximum height of editor
            height: 400,      // set editor height
            //width: 750,       // set editor width
            focus: false,     // set focus to editable area after initializing summernote
            placeholder:  vocab['template_body_placeholder']
        }
    );

    /**
     * Buttons
     */    
    $("#btnCancel").attr("href", path + '/helpdezk/hdkEmailConfig/index');
    
    $("#btnCreateEmailConfig").click(function(){

        if (!$("#create-email-config-form").valid()) {
            return false ;
        }

        if ($('#template-body').summernote('isEmpty')) {
            modalAlertMultiple('danger',vocab['Alert_empty_reason'],'alert-create-email-config');
            return false;
        }
        
        if(!$("#btnCreateEmailConfig").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkEmailConfig/createEmailConfig',
                dataType: 'json',
                data: $("#create-email-config-form").serialize() + "&template-body=" + $('#template-body').summernote('code'),
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-create-email-config');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
        
                    if(obj.success){
                        $('#modal-email-config-code').val(obj.emailConfigId);
                        $('#modal-notification-name').val($("#cmbSetting option:selected").text());
                        $('#modal-locale-name').val($("#cmbLocale option:selected").text());
                        $('#modal-subject').val(obj.subject);
                        $('#modal-email-config-create').modal('show');
                    }else{
                        modalAlertMultiple('danger',obj['message'],'alert-create-email-config');
                    }
                },
                beforeSend: function(){
                    $("#btnCreateEmailConfig").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnCreateEmailConfig").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
            });
        }
        
    });

    $("#btnUpdateEmailConfig").click(function(){

        if (!$("#update-email-config-form").valid()) {
            return false ;
        }

        if ($('#template-body').summernote('isEmpty')) {
            modalAlertMultiple('danger',vocab['Alert_empty_reason'],'alert-update-email-config');
            return false;
        }

        if(!$("#btnUpdateEmailConfig").hasClass('disabled')){
                $.ajax({     
                type: "POST",
                url: path + '/helpdezk/hdkEmailConfig/updateEmailConfig',
                dataType: 'json',
                data: $("#update-email-config-form").serialize() + "&template-body=" + $('#template-body').summernote('code'),
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Edit_failure'],'alert-update-email-config');
                },
                success: function(ret){    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));    
                    if(obj.success) {
                        showAlert(vocab['Edit_sucess'],'success');
                    } else {
                        modalAlertMultiple('danger',vocab['Edit_failure'],'alert-update-email-config');
                    }
                },
                beforeSend: function(){
                    $("#btnUpdateEmailConfig").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnUpdateEmailConfig").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }    
            });
        }
    });

    /**
     * Validate
     */
    $("#create-email-config-form").validate({
        ignore:[],
        rules: {
            cmbSetting:{required:true},
            cmbLocale:{
                required:true,
                remote: {
                    url: path + '/helpdezk/hdkEmailConfig/checkExistLangKey',
                    type: 'post',
                    dataType: 'json',
                    async: false,
                    data: {
                        _token: function(element) { return $("#_token").val() },
                        featureId: function(element) { return $("#cmbSetting").val() }
                    }
                }
            },
            "template-subject":{
                required:true,
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                remote: {
                    url: path + '/helpdezk/hdkEmailConfig/checkExist',
                    type: 'post',
                    dataType: 'json',
                    async: false,
                    data: {
                        _token: function(element) { return $("#_token").val() },
                        featureId: function(element) { return $("#cmbSetting").val() },
                        localeId: function(element) { return $("#cmbLocale").val() }
                    }
                }
            }
        },
        messages: {            
            cmbSetting:{required:vocab['Alert_field_required']},
            cmbLocale:{required:vocab['Alert_field_required']},
            "template-subject":{required:vocab['Alert_field_required']}
        },
        errorPlacement: function (error, element) {
            var name = $(element).attr("name");
            error.appendTo($("#" + name + "_validate_error"));
        }
    });

    $("#update-email-config-form").validate({
        ignore:[],
        rules: {
            cmbLocale:{
                required:true,
                remote: {
                    url: path + '/helpdezk/hdkEmailConfig/checkExistLangKey',
                    type: 'post',
                    dataType: 'json',
                    async: false,
                    data: {
                        _token: function(element) { return $("#_token").val() },
                        featureId: function(element) { return $("#cmbSetting").val() },
                        templateId: function(element) { return $("#templateId").val() }
                    }
                }
            },
            "template-subject":{
                required:true,
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                remote: {
                    url: path + '/helpdezk/hdkEmailConfig/checkExist',
                    type: 'post',
                    dataType: 'json',
                    async: false,
                    data: {
                        _token: function(element) { return $("#_token").val() },
                        featureId: function(element) { return $("#featureId").val() },
                        localeId: function(element) { return $("#cmbLocale").val() },
                        templateId: function(element) { return $("#templateId").val() }
                    }
                }
            }
        },
        messages: {            
            cmbLocale:{required:vocab['Alert_field_required']},
            "template-subject":{required:vocab['Alert_field_required']}
        },
        errorPlacement: function (error, element) {
            var name = $(element).attr("name");
            error.appendTo($("#" + name + "_validate_error"));
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

    //show modal to add new setting
    $("#btnAddSetting").click(function(){
        $("#modal-add-feature").modal('show');
    });

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
     * iCheck - checkboxes/radios styling
     */
    $('input[type=checkbox]').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
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
                            reloadCmbSetting(obj.featureId);
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

    /* when the modal is hidden */
    $('#modal-email-config-create').on('hidden.bs.modal', function() { 
        location.href = path + "/helpdezk/hdkEmailConfig/index";        
    });

    if($("#update-email-config-form").length > 0){
        $('#modal-alert').on('hidden.bs.modal', function() { 
            location.href = path + "/helpdezk/hdkEmailConfig/index" ;        
        });
    }

    $('#modal-add-feature').on('hidden.bs.modal', function() {
        $('#new-feature-default').iCheck('unCheck');
        $('#new-feature-value-check').iCheck('unCheck'); 
        $("#modal-add-feature-form").trigger('reset');
    });

    $('.lbltooltip').tooltip();
});


/**
 * Reload cmbSettings dropdrown list
 * 
 * @param  {int}selectedId
 * @return {void}      
 */
function reloadCmbSetting(selectedId)
{
    $.ajax({
        type: "POST",
        url: path + '/helpdezk/hdkEmailConfig/ajaxEmailSettings',
        dataType: 'json',
        data: {
            _token: $("#_token").val(),
            selectedId: selectedId
        },
        error: function (ret) {
            showAlert(vocab['generic_error_msg'],'danger');
        },
        success: function(ret){
            var obj = jQuery.parseJSON(JSON.stringify(ret));
            if(obj.success) {
                $("#cmbSetting").html(obj.data);
                $("#cmbSetting").trigger("change");
            } else {
                showAlert(vocab['generic_error_msg'],'danger');
            }
        }
    });

    return false;
}