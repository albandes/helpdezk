$(document).ready(function () {
    countdown.start(timesession);
    /**
     * Select2
     */
    $('#cmbTopic').select2({width:"100%",height:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10});
    $('#cmbShowIn').select2({width:"100%",height:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10});
    $('#cmbFeatureType').select2({width:"100%",height:"100%",placeholder:vocab['Select'],allowClear:true,minimumResultsForSearch: 10,dropdownParent: $(this).find('#modal-add-feature-form')});

    $('#modal-validity-hours').mask("000");
    $('#modal-validity-days').mask("0000");
    
    /**
     * Datepicker
     */
    if(dtpLanguage == '' || dtpLanguage === 'undefined' || !dtpLanguage){
        // Default language en (English)
        var dpOptions = {
            format: dtpFormat,
            autoclose:  dtpAutoclose,
            orientation: dtpOrientation
        };
    }else{
        var dpOptions = {
            format: dtpFormat,
            language:  dtpLanguage,
            autoclose:  dtpAutoclose,
            orientation: dtpOrientation
        };
    }

    $('.input-group.date').datepicker(dpOptions);

    /**
     * Clockpicker
     */
    mdtimepicker('#start-time', { is24hour: true, theme: 'parracho' });
    mdtimepicker('#end-time', { is24hour: true, theme: 'parracho' });

    /**
     * Buttons
     */    
    $("#btnCancel").attr("href", path + '/helpdezk/hdkWarning/index');
    
    $("#btnCreateWarning").click(function(){

        if (!$("#create-warning-form").valid()) {
            return false ;
        }
        
        if(!$("#btnCreateWarning").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkWarning/createWarning',
                dataType: 'json',
                data: $("#create-warning-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-create-warning');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
        
                    if(obj.success){
                        $('#modal-warning-code').val(obj.warningId);
                        $('#modal-warning-topic').val($("#cmbTopic option:selected").text());
                        $('#modal-warning-title').val($("#warning-title").val());
                        $('#modal-warning-create').modal('show');
                    }else{
                        modalAlertMultiple('danger',obj['message'],'alert-create-warning');
                    }
                },
                beforeSend: function(){
                    $("#btnCreateWarning").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnCreateWarning").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
            });
        }
        
    });

    $("#btnUpdateWarning").click(function(){

        if (!$("#update-warning-form").valid()) {
            return false ;
        }

        if(!$("#btnUpdateWarning").hasClass('disabled')){
                $.ajax({     
                type: "POST",
                url: path + '/helpdezk/hdkWarning/updateWarning',
                dataType: 'json',
                data: $("#update-warning-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Edit_failure'],'alert-update-warning');
                },
                success: function(ret){    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));    
                    if(obj.success) {
                        showAlert(vocab['Edit_sucess'],'success');
                    } else {
                        modalAlertMultiple('danger',vocab['Edit_failure'],'alert-update-warning');
                    }
                },
                beforeSend: function(){
                    $("#btnUpdateWarning").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnUpdateWarning").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }    
            });
        }
    });

    $("#btnStartTime").click(function(){
        mdtimepicker('#start-time', "show");
    });

    $("#btnEndTime").click(function(){
        mdtimepicker('#end-time', "show");
    });

    /**
     * Validate
     */
    $("#create-warning-form").validate({
        ignore:[],
        rules: {
            'warning-title':{required:true},
            'warning-description':{required:true},
            'start-date':{
                required:true,
                checkStartDate:function(element){return !$('#flag-until-closed').is(':checked');}
            },
            'start-time':{
                required:true,
                checkStartTime:function(element){return !$('#flag-until-closed').is(':checked');}
            },
            'end-date':{
                required:function(element){return !$('#flag-until-closed').is(':checked');},
                checkEndDate:function(element){return !$('#flag-until-closed').is(':checked');}
            },
            'end-time':{
                required:function(element){return !$('#flag-until-closed').is(':checked');},
                checkEndTime:function(element){return !$('#flag-until-closed').is(':checked');}
            }
        },
        messages: {            
            'warning-title':{required:vocab['Alert_field_required']},
            'warning-description':{required:vocab['Alert_field_required']},
            'start-date':{required:vocab['Alert_field_required']},
            'start-time':{required:vocab['Alert_field_required']},
            'end-date':{required:vocab['Alert_field_required']},
            'end-time':{required:vocab['Alert_field_required']}
        },
        errorPlacement: function (error, element) {
            var name = $(element).attr("name");
            error.appendTo($("#" + name + "_validate_error"));
        }
    });

    $("#update-warning-form").validate({
        ignore:[],
        rules: {
            'warning-title':{required:true},
            'warning-description':{required:true},
            'start-date':{
                required:true,
                checkStartDate:function(element){return !$('#flag-until-closed').is(':checked');}
            },
            'start-time':{
                required:true,
                checkStartTime:function(element){return !$('#flag-until-closed').is(':checked');}
            },
            'end-date':{
                required:function(element){return !$('#flag-until-closed').is(':checked');},
                checkEndDate:function(element){return !$('#flag-until-closed').is(':checked');}
            },
            'end-time':{
                required:function(element){return !$('#flag-until-closed').is(':checked');},
                checkEndTime:function(element){return !$('#flag-until-closed').is(':checked');}
            }
        },
        messages: {            
            'warning-title':{required:vocab['Alert_field_required']},
            'warning-description':{required:vocab['Alert_field_required']},
            'start-date':{required:vocab['Alert_field_required']},
            'start-time':{required:vocab['Alert_field_required']},
            'end-date':{required:vocab['Alert_field_required']},
            'end-time':{required:vocab['Alert_field_required']}
        },
        errorPlacement: function (error, element) {
            var name = $(element).attr("name");
            error.appendTo($("#" + name + "_validate_error"));
        }
    });

    $("#modal-add-topic-form").validate({
        ignore:[],
        rules: {
            'modal-topic-name':{required:true},
            'modal-validity-hours':{required:function(element){return $('#modal-validity-2').is(':checked');}},
            'modal-validity-days':{required:function(element){return $('#modal-validity-3').is(':checked');}}
        },
        messages: {            
            'modal-topic-name':{required:vocab['Alert_field_required']},
            'modal-validity-hours':{required:vocab['Alert_field_required']},
            'modal-validity-days':{required:vocab['Alert_field_required']}
        },
        errorPlacement: function (error, element) {
            var name = $(element).attr("name");
            error.appendTo($("#" + name + "_validate_error"));
        }
    });

    $.validator.addMethod('checkStartDate', function(startDate) {
        var parts = startDate.split('/'), startTimeTmp = $("#start-time").val(), partsTime = startTimeTmp.split(':'), 
            endDateTmp = $("#end-date").val(), partsEnd = endDateTmp.split('/'), endTimeTmp = $("#end-time").val(), partsEndTime = endTimeTmp.split(':');

        startDate = new Date(parts[2], parts[1] - 1, parts[0], partsTime[0], partsTime[1]);
        endDate = new Date(partsEnd[2], partsEnd[1] - 1, partsEnd[0], partsEndTime[0], partsEndTime[1]);

        return startDate <= endDate;

    }, vocab['Alert_start_date_error']);

    $.validator.addMethod('checkEndDate', function(endDate) {
        var parts = endDate.split('/'), endTimeTmp = $("#end-time").val(), partsTime = endTimeTmp.split(':'), 
            startDateTmp = $("#start-date").val(), partsStart = startDateTmp.split('/'), startTimeTmp = $("#start-time").val(), partsStartTime = startTimeTmp.split(':');

        endDate = new Date(parts[2], parts[1] - 1, parts[0], partsTime[0], partsTime[1]);
        startDate = new Date(partsStart[2], partsStart[1] - 1, partsStart[0], partsStartTime[0], partsStartTime[1]);

        return endDate > startDate;

    }, vocab['Alert_finish_date_error']);

    $.validator.addMethod('checkStartTime', function(startTime) {
        var startDateTmp = $("#start-date").val(), parts = startDateTmp.split('/'), partsTime = startTime.split(':'), 
            endDateTmp = $("#end-date").val(), partsEnd = endDateTmp.split('/'), endTimeTmp = $("#end-time").val(), partsEndTime = endTimeTmp.split(':');

        startDate = new Date(parts[2], parts[1] - 1, parts[0], partsTime[0], partsTime[1]);
        endDate = new Date(partsEnd[2], partsEnd[1] - 1, partsEnd[0], partsEndTime[0], partsEndTime[1]);

        return startDate <= endDate;

    }, vocab['Alert_start_date_error']);

    $.validator.addMethod('checkEndTime', function(endTime) {
        var endDateTmp = $("#end-date").val(), parts = endDateTmp.split('/'), partsTime = endTime.split(':'), 
            startDateTmp = $("#start-date").val(), partsStart = startDateTmp.split('/'), startTimeTmp = $("#start-time").val(), partsStartTime = startTimeTmp.split(':');

        endDate = new Date(parts[2], parts[1] - 1, parts[0], partsTime[0], partsTime[1]);
        startDate = new Date(partsStart[2], partsStart[1] - 1, partsStart[0], partsStartTime[0], partsStartTime[1]);

        return endDate > startDate;

    }, vocab['Alert_finish_date_error']);

    //show modal to add new topic
    $("#btnAddTopic").click(function(){
        $("#modal-add-topic-title").html(vocab['Warning_new_topic']);
        $("#modal-topic-id").val("");

        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });

        $('input[name=modal-validity]:checked').iCheck('unCheck');
        $('#modal-validity-1').iCheck('check');
        $("#modal-add-topic").modal('show');

        $("input[name=modal-validity]").on('ifClicked',function(){
            if($(this).val() == 1){
                $('#modal-validity-hours').val('').attr('disabled','disabled');
                $('#modal-validity-days').val('').attr('disabled','disabled');
            }else if($(this).val() == 2){
                $('#modal-validity-days').val('').attr('disabled','disabled');
                $('#modal-validity-hours').removeAttr('disabled');
            }else{
                $('#modal-validity-hours').val('').attr('disabled','disabled');
                $('#modal-validity-days').removeAttr('disabled');
            }
        });

        $("input[name=topic-available-group]").on('ifClicked',function(){
            if($(this).val() == 1){
                $('#group-list').addClass('d-none');
                
                $("input[name='checkGroups[]']").each(function() {
                    var id = $(this).attr('id');
                    $("#"+id).iCheck('unCheck');
                });
            }else{
                $('#group-list').removeClass('d-none');
            }
        });
    
        $("input[name=topic-available-company]").on('ifClicked',function(){
            if($(this).val() == 1){
                $('#company-list').addClass('d-none');
                
                $("input[name='checkCompanies[]']").each(function() {
                    var id = $(this).attr('id');
                    $("#"+id).iCheck('unCheck');
                });
            }else{
                $('#company-list').removeClass('d-none');
            }
        });
    });

    $("#flag-until-closed").on('ifChecked ifUnchecked',function(e){
        if(e.type == 'ifChecked'){
            $("#valid-until-cell").addClass('d-none');
        }else{
            $("#valid-until-cell").removeClass('d-none');
        }
    });

    /**
     * iCheck - checkboxes/radios styling
     */
    $('input[type=checkbox]').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    $("#btnAddTopicSave").click(function(){
        if (!$("#modal-add-topic-form").valid()) {
            return false ;
        }

        var method,msg,msgError;

        if(!$("#modal-topic-id").val() || $("#modal-topic-id").val() == 0){
            method = "createTopic";
            msg = vocab['Alert_inserted'];
            msgError = vocab['Alert_failure'];
        }else{
            method = "updateTopic";
            msg = vocab['Edit_sucess'];
            msgError = vocab['Edit_failure'];
        }

        if(!$("#btnAddTopicSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkWarning/'+ method,
                dataType: 'json',
                data: $("#modal-add-topic-form").serialize() + "&_token="+ $("#_token").val(),
                error: function (ret) {
                    modalAlertMultiple('danger',msgError,'alert-modal-add-topic');
                },
                success: function(ret){    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));    
                    if(obj.success) {
                        modalAlertMultiple('success',msg,'alert-modal-add-topic');

                        setTimeout(function(){
                            $('#modal-add-topic').modal('hide');
                            reloadCmbTopic(obj.topicId);
                        },2000);
                    } else {
                        modalAlertMultiple('danger',msgError,'alert-modal-add-topic');
                    }
                },
                beforeSend: function(){
                    $("#btnAddTopicSave").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnAddTopicClose").addClass('disabled');
                },
                complete: function(){
                    $("#btnAddTopicSave").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnAddTopicClose").removeClass('disabled');
                }    
            });
        }

        return false;
    });

    /* when the modal is hidden */
    $('#modal-warning-create').on('hidden.bs.modal', function() { 
        location.href = path + "/helpdezk/hdkWarning/index";        
    });

    if($("#update-warning-form").length > 0){
        $('#modal-alert').on('hidden.bs.modal', function() { 
            location.href = path + "/helpdezk/hdkWarning/index" ;        
        });
    }

    $('#modal-add-topic').on('hidden.bs.modal', function() {
        
        $("input[name='checkGroups[]']").each(function() {
            var id = $(this).attr('id');
            $("#"+id).iCheck('unCheck');
        });

        $("input[name='checkCompanies[]']").each(function() {
            var id = $(this).attr('id');
            $("#"+id).iCheck('unCheck');
        });

        $('input[name=modal-topic-send-email]:checked').iCheck('unCheck');
        $('input[name=topic-available-group]:checked').iCheck('unCheck');
        $('input[name=topic-available-company]:checked').iCheck('unCheck');
        
        $('#topic-available-group-1').iCheck('check');
        $('#topic-available-company-1').iCheck('check');
        $('#group-list').addClass('d-none');
        $('#company-list').addClass('d-none');

        $("#modal-add-topic-form").trigger('reset');
    });

    $('.lbltooltip').tooltip();

    $('#cmbTopic').change(function(){
        $.ajax({
            type: "POST",
            url: path + '/helpdezk/hdkWarning/topicFormUpdate',
            dataType: 'json',
            data: {
                _token: $("#_token").val(),
                topicId: $(this).val()
            },
            error: function (ret) {
                showAlert(vocab['generic_error_msg'],'danger');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                if(obj.success){
                    if(obj.topicValidityType != 1){
                        var curDateTmp = $("#start-date").val(), aCurDate = curDateTmp.split("/"), curDate = aCurDate[2]+"-"+aCurDate[1]+"-"+aCurDate[0],
                            curTime = $("#start-time").val(), endDateTimeTmp,aEndDateTime;
                        
                            if(obj.topicValidityType == 2){
                            endDateTimeTmp = moment(curDate+" "+curTime).add(obj.topicValidity,'h').format('DD/MM/YYYY HH:mm');
                        }else if(obj.topicValidityType == 3){
                            endDateTimeTmp = moment(curDate+" "+curTime).add(obj.topicValidity,'d').format('DD/MM/YYYY HH:mm');
                        }

                        aEndDateTime = endDateTimeTmp.split(" ");
                        $("#end-date").val(aEndDateTime[0]);
                        $("#end-time").val(aEndDateTime[1]);
                        $("#flag-until-closed").iCheck("unCheck");
                    }else{
                        $("#end-date").val('');
                        $("#end-time").val('');
                        $("#flag-until-closed").iCheck("check");
                    }

                    (obj.topicFlgSendEmail == "Y") ? $("#flag-send-alert").iCheck("check") : $("#flag-send-alert").iCheck("unCheck");
                }else{
                    showAlert(vocab['generic_error_msg'],'danger');
                }
            }
        });
    });

    if($("#update-warning-form").length <= 0){
        $('#cmbTopic').change();
    }
});


/**
 * Reload topics dropdrown list
 * 
 * @param  {int}selectedId
 * @return {void}      
 */
function reloadCmbTopic(selectedId)
{
    $.ajax({
        type: "POST",
        url: path + '/helpdezk/hdkWarning/ajaxTopics',
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
                $("#cmbTopic").html(obj.data);
                $("#cmbTopic").trigger("change");
            } else {
                showAlert(vocab['generic_error_msg'],'danger');
            }
        }
    });

    return false;
}