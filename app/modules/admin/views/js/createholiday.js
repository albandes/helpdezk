var global_idproduto = '';
$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    /*
     *  Chosen
     */
    $("#company").chosen({  width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#lastyear").chosen({ width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#nextyear").chosen({ width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});

    /*
     * Datepicker
     */
    $('.input-group.date').datepicker(datepickerOpts);

    /*
     * Combos
     */
    var objHolidayData = {
        changeCompany: function() {
            var companyId = $("#company").val();
            $("#lastyear").empty();
            $.post(path+"/admin/holidays/ajaxYearByCompany",{companyId:companyId},
                function(valor) {
                    $("#lastyear").html(valor);
                    $("#lastyear").trigger("chosen:updated");
                    if($("#lastyear").val() != "" && $("#lastyear").val() != "X"){objHolidayData.changeYear();}
                    $('#boxResult').addClass('hide');
                    $('.lineResult').addClass('hide');
                    return false;
                })
            return false ;
        },
        changeYear: function() {
            var companyId = $("#company").val(),
                prevyear = $("#lastyear").val();
            
            $('#boxResult').addClass('hide');
            $('.lineResult').addClass('hide');

            if(prevyear != 'X' && companyId != 'X'){
                $.ajax({
                    type: "POST",
                    url: path + '/admin/holidays/load',
                    dataType: 'json',
                    data: {companyId:companyId,prevyear:prevyear},
                    error: function (ret) {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_get_data'),'alert-create-pedidocompra');
                    },
                    success: function(ret){
    
                        var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                        $('#holiday-table').find('tbody').html(obj.result);
                        $("#nextyear").html(obj.yearto);
                        $("#nextyear").trigger("chosen:updated");
                        $('.txtYear').html(obj.year);
                        $('.txtCount').html(obj.count);
                        $('#boxResult').removeClass('hide');
                        $('.lineResult').removeClass('hide');
                    }
                });
            }            
            
            return false ;
        }

    }

    $("#company").change(function(){
        objHolidayData.changeCompany();
    });

    $("#lastyear").change(function(){
        objHolidayData.changeYear();
    });

    
    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/admin/holidays/index');

    $("#btnCreateHoliday").click(function(){

        if (!$("#create-holiday-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/admin/holidays/createHoliday',
            dataType: 'json',
            data: $("#create-holiday-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',aLang['Alert_failure'].replace (/\"/g, ""),'alert-create-holiday');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if($.isNumeric(obj.idholiday)) {

                    $('#modal-idholiday').html(obj.idholiday);
                    $('#modal-holiday-description').html(obj.description);

                    $("#btnModalAlert").attr("href", path + '/admin/holidays/index');

                    $('#modal-alert-create').modal('show');
                } else {
                    modalAlertMultiple('danger',aLang['Alert_failure'].replace (/\"/g, ""),'alert-create-holiday');
                }
            }
        });
    });

    $("#btnUpdateHoliday").click(function(){

        if (!$("#update-holiday-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/admin/holidays/updateHoliday/idholiday',
            dataType: 'json',
            data: $("#update-holiday-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',aLang['Edit_failure'].replace (/\"/g, ""),'alert-create-holiday');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'OK' ) {

                    $('#modal-notification').html(aLang['Edit_sucess'].replace (/\"/g, ""));
                    $("#btn-modal-ok").attr("href", path + '/admin/holidays/index');
                    $("#tipo_alerta").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');

                } else {

                    modalAlertMultiple('danger',aLang['Edit_failure'].replace (/\"/g, ""),'alert-create-holiday');

                }

            }

        });


    });

    $("#btnImportHoliday").click(function(){

        if (!$("#import-holiday-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/admin/holidays/import',
            dataType: 'json',
            data: $("#import-holiday-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',aLang['Import_failure'].replace (/\"/g, ""),'alert-create-holiday');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'OK' ) {

                    $('#modal-notification').html(aLang['Import_successfull'].replace (/\"/g, ""));
                    $("#btn-modal-ok").attr("href", path + '/admin/holidays/index');
                    $("#tipo_alerta").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');

                } else {

                    modalAlertMultiple('danger',aLang['Import_failure'].replace (/\"/g, ""),'alert-create-holiday');

                }

            }

        });


    });

    /*
     * Validate
     */
    $("#create-holiday-form").validate({
        ignore:[],
        rules: {
            holiday_description:    "required",
            holiday_date:           "required"
        },
        messages: {
            holiday_description:    "Campo obrigat&oacute;rio",
            holiday_date:           "Campo obrigat&oacute;rio"
        }
    });

    $("#update-holiday-form").validate({
        ignore:[],
        rules: {
            holiday_description:    "required",
            holiday_date:           "required"
        },
        messages: {
            holiday_description:    "Campo obrigat&oacute;rio",
            holiday_date:           "Campo obrigat&oacute;rio"
        }
    });

});

function sendNotification(transaction,codeRequest,hasAttachments)
{
    /*
     *
     * This was necessary because in some cases we have to send e-mail with the newly added attachments.
     * We only have access to these attachments after the execution of the dropzone.
     *
     */
    $.ajax({
        url : path + '/helpdezk/hdkTicket/sendNotification',
        type : 'POST',
        data : {
            transaction: transaction,
            code_request: codeRequest,
            has_attachment: hasAttachments
        },
        success : function(data) {

        },
        error : function(request,error)
        {

        }
    });

    return false ;

}