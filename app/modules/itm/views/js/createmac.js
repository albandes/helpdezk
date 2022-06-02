var global_idmodule = '';
$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    /*
     * Mask
     */
    $("#macNumber").mask('FF:FF:FF:FF:FF:FF', {
        translation: {'F':{pattern:/[0-9A-Fa-f]/}},
        onKeyPress: function (value, event) {
            event.currentTarget.value = value.toUpperCase();
        }
    });

    /*
     *  Chosen
     */
    $("#cmbTypeHost").chosen({  width: "100%",    no_results_text: makeSmartyLabel('API_No_result'),    disable_search_threshold: 10});
    $("#cmbNetUser").chosen({   width: "100%",    no_results_text: makeSmartyLabel('API_No_result'),    disable_search_threshold: 10});
    $("#cmbUpBand").chosen({    width: "100%",    no_results_text: makeSmartyLabel('API_No_result'),    disable_search_threshold: 10});
    $("#cmbDownBand").chosen({  width: "100%",    no_results_text: makeSmartyLabel('API_No_result'),    disable_search_threshold: 10});

    var objMacData = {
        changeNetUser: function() {
            var idTypeHost = $("#cmbTypeHost").val();
            
            switch(idTypeHost){
                case '1':
                case '2':
                case '3':
                    $("#internalUsers").removeClass('hide');
                    $(".externalUsers").addClass('hide');

                    $.ajax({
                        type: "POST",
                        url: path + '/itm/itmMacAddress/ajaxNetUsers',
                        dataType: 'json',
                        data: {idtypehost:idTypeHost},
                        error: function (ret) {
                            modalAlertMultiple('danger', makeSmartyLabel('Alert_failure'),'alert-create-module');
                        },
                        success: function(ret){
            
                            var obj = jQuery.parseJSON(JSON.stringify(ret));
            
                            if(obj) {    
                                $("#netUserLbl").html(obj.lbl);
                                $("#cmbNetUser").html(obj.cmblist);
                                $("#cmbNetUser").trigger("chosen:updated");
                                
                            } else {
                                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-module');
                            }
                        }
                    });

                    break;

                case '4':
                case '5':
                case '10':
                    $("#internalUsers").removeClass('hide');
                    $(".externalUsers").removeClass('hide');

                    $.ajax({
                        type: "POST",
                        url: path + '/itm/itmMacAddress/ajaxNetUsers',
                        dataType: 'json',
                        data: {idtypehost:idTypeHost},
                        error: function (ret) {
                            modalAlertMultiple('danger', makeSmartyLabel('Alert_failure'),'alert-create-module');
                        },
                        success: function(ret){

                            var obj = jQuery.parseJSON(JSON.stringify(ret));

                            if(obj) {
                                $("#netUserLbl").html(obj.lbl);
                                $("#cmbNetUser").html(obj.cmblist);
                                $("#cmbNetUser").trigger("chosen:updated");

                            } else {
                                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-module');
                            }
                        }
                    });

                    break;

                default:
                    $("#internalUsers").addClass('hide');
                    $(".externalUsers").addClass('hide');
                    break;

            }            
            
        }
    }

    $("#cmbTypeHost").change(function(){
        objMacData.changeNetUser();
    });

    /*
     * Datepicker
     */
    $('.input-group.date').datepicker({
        format: "dd/mm/yyyy",
        language: "pt-BR",
        autoclose: true,
        startDate: '0'
    });

    /*
     * Clockpicker
     */
    $('.clockpicker').clockpicker({
        autoclose: true,
        default: 'now',
        fromnow: 6000
    });
 
    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/itm/itmMacAddress/index');

    $("#btnCreateMac").click(function(){

        if (!$("#create-mac-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/itm/itmMacAddress/createMac',
            dataType: 'json',
            data: $("#create-mac-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger', makeSmartyLabel('Alert_failure'),'alert-create-mac');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if($.isNumeric(obj.idhost)) {
                    $('#modal-notification').html(makeSmartyLabel('Alert_inserted'));
                    $("#btn-modal-ok").attr("href", path + '/itm/itmMacAddress/index');
                    $("#tipo-alert").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');
                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-mac');
                }
            },
            beforeSend: function(){
                $("#btnCreateMac").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                $("#btnCancel").addClass('disabled');
            },
            complete: function(){
                $("#btnCreateMac").html("<i class='fa fa-check-circle'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                $("#btnCancel").removeClass('disabled');
            }
        });
    });

    $("#btnUpdateMac").click(function(){

        if (!$("#update-mac-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/itm/itmMacAddress/updateMac',
            dataType: 'json',
            data: $("#update-mac-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',aLang['Edit_failure'].replace (/\"/g, ""),'alert-update-mac');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'OK' ) {
                    $('#modal-notification').html(aLang['Edit_sucess'].replace (/\"/g, ""));
                    $("#btn-modal-ok").attr("href", path + '/itm/itmMacAddress/index');
                    $("#tipo-alert").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');

                } else {
                    modalAlertMultiple('danger',aLang['Edit_failure'].replace (/\"/g, ""),'alert-update-mac');
                }

            },
            beforeSend: function(){
                $("#btnUpdateMac").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                $("#btnCancel").addClass('disabled');
            },
            complete: function(){
                $("#btnUpdateMac").html("<i class='fa fa-check-circle'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                $("#btnCancel").removeClass('disabled');
            }

        });


    });

    $("#btnAddExternal").click(function(){
        $('#modal-form-external-user').modal('show');
    });

    $("#btnSaveExtUser").click(function(){

        if (!$("#external-user-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/itm/itmMacAddress/insertExternalUser',
            dataType: 'json',
            data: $("#external-user-form").serialize() + '&_token=' + $("#_token").val(),
            error: function (ret) {
                modalAlertMultiple('danger', makeSmartyLabel('Alert_failure'),'alert-external-user');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if($.isNumeric(obj.idprofile)) {
                    modalAlertMultiple('success',makeSmartyLabel('Alert_inserted'),'alert-external-user');
                    var idTypeHost = $("#cmbTypeHost").val();
                    $.post(path + '/itm/itmMacAddress/ajaxNetUsers',{idtypehost:idTypeHost},
                        function(valor){
                            var obj2 = jQuery.parseJSON(JSON.stringify(valor));

                            $("#netUserLbl").html(obj2.lbl);
                            $("#cmbNetUser").html(obj2.cmblist);
                            $("#cmbNetUser").val(obj.idprofile);
                            $("#cmbNetUser").trigger("chosen:updated");
                        },'JSON');
                    setTimeout(function(){
                        $('#modal-form-external-user').modal('hide');
                        $('#external-user-form').trigger('reset');
                    },2000);
                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-external-user');
                }
            },
            beforeSend: function(){
                $("#btnSaveExtUser").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                $("#btnCancel").addClass('disabled');
            },
            complete: function(){
                $("#btnSaveExtUser").html("<i class='fa fa-check-circle'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                $("#btnCancel").removeClass('disabled');
            }
        });
    });

    /*
     * Validate
     */
    $("#create-mac-form").validate({
        ignore:[],
        rules: {
            hostName: {
                required: true,
                remote:{url: path + '/itm/itmMacAddress/hostNameVerification',
                    type: "post",
                    data:{type_query:"h"
                    }
                }
            },
            macNumber: {
                required: true,
                minlength: 17,
                remote:{url: path + '/itm/itmMacAddress/hostMacVerification',
                    type: "post"/*,
                    data:{mac_number:function(){return $("#macNumber").val();}}*/
                }
            },
            cmbTypeHost: "required",
            cmbNetUser: {
                required:function(element) {
                    return $("#cmbTypeHost").val() == 1 || $("#cmbTypeHost").val() == 2 || $("#cmbTypeHost").val() == 3 || $("#cmbTypeHost").val() == 4 || $("#cmbTypeHost").val() == 5 || $("#cmbTypeHost").val() == 10;
                }
            },
            dtend: {
                required:function(element) {
                    return $("#cmbTypeHost").val() == 4 || $("#cmbTypeHost").val() == 5 || $("#cmbTypeHost").val() == 10;
                }
            },
            timeend: {
                required:function(element) {
                    return $("#cmbTypeHost").val() == 4 || $("#cmbTypeHost").val() == 5 || $("#cmbTypeHost").val() == 10;
                },
                remote:{url: path + '/itm/itmMacAddress/checkTimeEnd',
                    type: "post",
                     data:{dtend:function(){return $("#dtend").val();}}
                }
            },
            description:    {required:true,minlength:5,maxlength:200}
        },
        messages: {
            hostName: {
                required: makeSmartyLabel('Alert_field_required'),
                remote: makeSmartyLabel('itm_exists_host')
            },
            macNumber: {
                required: makeSmartyLabel('Alert_field_required'),
                minlength: makeSmartyLabel('itm_description_minlength') + ' 17 caracteres'
            },
            cmbTypeHost:    makeSmartyLabel('Alert_field_required'),
            cmbNetUser:     {required:makeSmartyLabel('Alert_field_required')},
            dtend:          {required:makeSmartyLabel('Alert_field_required')},
            timeend:        {required:makeSmartyLabel('Alert_field_required')},
            description:    {required:makeSmartyLabel('Alert_field_required'),
                             minlength:makeSmartyLabel('itm_description_minlength') + ' 5 caracteres',
                             maxlength:makeSmartyLabel('itm_description_maxlength')  + ' 200 caracteres'
            }
        }
    });

    $("#update-mac-form").validate({
        ignore:[],
        rules: {
            hostName: {
                required: true,
                remote:{url: path + '/itm/itmMacAddress/hostNameVerification',
                    type: "post",
                    data:{
                        type_query:"h",
                        idhost: function(){return $("#idhost").val();}
                    }
                }
            },
            macNumber: {
                required: true,
                minlength: 17,
                remote:{url: path + '/itm/itmMacAddress/hostMacVerification',
                    type: "post",
                    data:{
                        idhost: function(){return $("#idhost").val();}
                    }
                }
            },
            cmbTypeHost: "required",
            cmbNetUser: {
                required:function(element) {
                    return $("#cmbTypeHost").val() == 1 || $("#cmbTypeHost").val() == 2 || $("#cmbTypeHost").val() == 3 || $("#cmbTypeHost").val() == 4 || $("#cmbTypeHost").val() == 5 || $("#cmbTypeHost").val() == 10;
                }
            },
            dtend: {
                required:function(element) {
                    return $("#cmbTypeHost").val() == 4 || $("#cmbTypeHost").val() == 5;
                }
            },
            timeend: {
                required:function(element) {
                    return $("#cmbTypeHost").val() == 4 || $("#cmbTypeHost").val() == 5 || $("#cmbTypeHost").val() == 10;
                },
                remote:{url: path + '/itm/itmMacAddress/checkTimeEnd',
                    type: "post",
                    data:{dtend:function(){return $("#dtend").val();}}
                }
            },
            description:    {required:true,minlength:5,maxlength:200}
        },
        messages: {
            hostName: {
                required: makeSmartyLabel('Alert_field_required'),
                remote: makeSmartyLabel('itm_exists_host')
            },
            macNumber: {
                required: makeSmartyLabel('Alert_field_required'),
                minlength: makeSmartyLabel('itm_description_minlength') + ' 17 caracteres'
            },
            cmbTypeHost:    makeSmartyLabel('Alert_field_required'),
            cmbNetUser:     {required:makeSmartyLabel('Alert_field_required')},
            dtend:          {required:makeSmartyLabel('Alert_field_required')},
            timeend:        {required:makeSmartyLabel('Alert_field_required')},
            description:    {required:makeSmartyLabel('Alert_field_required'),
                minlength:makeSmartyLabel('itm_description_minlength') + ' 5 caracteres',
                maxlength:makeSmartyLabel('itm_description_maxlength')  + ' 200 caracteres'
            }
        }
    });

    $("#external-user-form").validate({
        ignore:[],
        rules: {
            nameExternal:    {required:true}
        },
        messages: {
            nameExternal:    {required:makeSmartyLabel('Alert_field_required')}
        }
    });

    $('.tooltip-buttons').tooltip();

});
