$(document).ready(function () {
    countdown.start(timesession);

    /*
     * Datepicker
     */
    $('.input-group.date').datepicker({
        format: dtpFormat,
        language:  dtpLanguage,
        autoclose:  dtpAutoclose,
        orientation: dtpOrientation
    });
    
    /*
     * Select2
     */
    $('#company').select2();

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

    };

    if($("#import-holiday-form").length > 0){
        $("#company").change(function(){
            objHolidayData.changeCompany();
        });
    
        $("#lastyear").change(function(){
            objHolidayData.changeYear();
        });
    } 
    
    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/admin/holidays/index');

    $("#btnCreateHoliday").click(function(){

        if (!$("#create-holiday-form").valid()) {
            return false ;
        }

        if(!$("#btnCreateHoliday").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/holidays/createHoliday',
                dataType: 'json',
                data: $("#create-holiday-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',translateLabel('Alert_failure'),'alert-create-holiday');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if($.isNumeric(obj.idholiday)) {
    
                        $('#modal-idholiday').val(obj.idholiday);
                        $('#modal-holiday-description').val(obj.description);
    
                        $('#modal-holiday-create').modal('show');
                    } else {
                        modalAlertMultiple('danger',translateLabel('Alert_failure'),'alert-create-holiday');
                    }
                },
                beforeSend: function(){
                    $("#btnCreateHoliday").html("<i class='fa fa-spinner fa-spin'></i> "+ translateLabel('Processing')).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnCreateHoliday").html("<i class='fa fa-check-circle'></i> "+ translateLabel('Save')).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
            });
        }
        
    });

    $("#btnUpdateHoliday").click(function(){

        if (!$("#update-holiday-form").valid()) {
            return false ;
        }

        if(!$("#btnUpdateHoliday").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/holidays/updateHoliday/idholiday',
                dataType: 'json',
                data: $("#update-holiday-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',translateLabel('Edit_failure'),'alert-update-holiday');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(translateLabel('Edit_sucess'),'success');
                    } else {
                        modalAlertMultiple('danger',translateLabel('Edit_failure'),'alert-update-holiday');
                    }
                },
                beforeSend: function(){
                    $("#btnUpdateHoliday").html("<i class='fa fa-spinner fa-spin'></i> "+ translateLabel('Processing')).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnUpdateHoliday").html("<i class='fa fa-check-circle'></i> "+ translateLabel('Save')).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
    
            });
        }

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
            holiday_description:    translateLabel('Alert_field_required'),
            holiday_date:           translateLabel('Alert_field_required')
        }
    });

    $("#update-holiday-form").validate({
        ignore:[],
        rules: {
            holiday_description:    "required",
            holiday_date:           "required"
        },
        messages: {
            holiday_description:    translateLabel('Alert_field_required'),
            holiday_date:           translateLabel('Alert_field_required')
        }
    });

    /* when the modal is hidden */
    $('#modal-holiday-create').on('hidden.bs.modal', function() { 
        location.href = path + "/admin/holidays/index" ;        
    });
});
            