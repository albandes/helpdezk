$(document).ready(function () {
    countdown.start(timesession);

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
    
    /*
     * Select2
     */
    $('#company').select2({placeholder:vocab['Select_company'],allowClear:true});
    $('#lastyear').select2({placeholder:vocab['select_year'],allowClear:true});
    $('#nextyear').select2({placeholder:vocab['select_year'],allowClear:true});

    /*
     * Combos
     */
    var objHolidayData = {
        changeCompany: function() {
            var companyID = $("#company").val();
            $("#lastyear").empty();
            $.post(path+"/admin/holidays/ajaxYearByCompany",{companyID:companyID},function(valor){
                $("#lastyear").html(valor);
                $("#lastyear").trigger("change");
                if($("#lastyear").val() != "" && $("#lastyear").val() != "X"){objHolidayData.changeYear();}
                $('#boxResult').addClass('hide');
                $('.lineResult').addClass('hide');
                return false;
            });
            return false ;
        },
        changeYear: function() {
            var companyID = $("#company").val(),
                prevyear = $("#lastyear").val();
            
            $('#boxResult').addClass('d-none');
            $('.lineResult').addClass('d-none');

            if((prevyear != 'X' && prevyear != '') && (companyID != 'X' && companyID != '')){
                $.ajax({
                    type: "POST",
                    url: path + '/admin/holidays/load',
                    dataType: 'json',
                    data: {companyID:companyID,prevyear:prevyear},
                    error: function (ret) {
                        modalAlertMultiple('danger',vocab['Alert_get_data'],'alert-create-pedidocompra');
                    },
                    success: function(ret){
    
                        var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                        $('#holiday-table').find('tbody').html(obj.result);
                        $("#nextyear").html(obj.yearto);
                        $("#nextyear").trigger("change");
                        $('.txtYear').html(obj.year);
                        $('.txtCount').html(obj.count);
                        $('#boxResult').removeClass('d-none');
                        $('.lineResult').removeClass('d-none');
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
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-create-holiday');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if($.isNumeric(obj.idholiday)) {
    
                        $('#modal-idholiday').val(obj.idholiday);
                        $('#modal-holiday-description').val(obj.description);
    
                        $('#modal-holiday-create').modal('show');
                    } else {
                        modalAlertMultiple('danger',vocab['Alert_failure'],'alert-create-holiday');
                    }
                },
                beforeSend: function(){
                    $("#btnCreateHoliday").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnCreateHoliday").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
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
                url: path + '/admin/holidays/updateHoliday',
                dataType: 'json',
                data: $("#update-holiday-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Edit_failure'],'alert-update-holiday');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(vocab['Edit_sucess'],'success');
                    } else {
                        modalAlertMultiple('danger',vocab['Edit_failure'],'alert-update-holiday');
                    }
                },
                beforeSend: function(){
                    $("#btnUpdateHoliday").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnUpdateHoliday").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
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
                modalAlertMultiple('danger',vocab['Import_failure'],'alert-import-holiday');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.success) {
                    showAlert(vocab['Import_successfull'],'success');
                } else {
                    modalAlertMultiple('danger',vocab['Import_failure'],'alert-import-holiday');
                }
            },
            beforeSend: function(){
                $("#btnImportHoliday").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                $("#btnCancel").addClass('disabled');
            },
            complete: function(){
                $("#btnImportHoliday").html("<i class='fa fa-download'></i> "+ vocab['Import']).removeClass('disabled');
                $("#btnCancel").removeClass('disabled');
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
            holiday_description:    vocab['Alert_field_required'],
            holiday_date:           vocab['Alert_field_required']
        }
    });

    $("#update-holiday-form").validate({
        ignore:[],
        rules: {
            holiday_description:    "required",
            holiday_date:           "required"
        },
        messages: {
            holiday_description:    vocab['Alert_field_required'],
            holiday_date:           vocab['Alert_field_required']
        }
    });

    $("#import-holiday-form").validate({
        ignore:[],
        rules: {
            company:    "required",
            lastyear:   "required",
            nextyear:   "required"
        },
        messages: {
            company:    vocab['Alert_field_required'],
            lastyear:   vocab['Alert_field_required'],
            nextyear:   vocab['Alert_field_required']
        }
    });

    /* when the modal is hidden */
    $('#modal-holiday-create').on('hidden.bs.modal', function() { 
        location.href = path + "/admin/holidays/index" ;        
    });

    if($("#import-holiday-form").length > 0 || $("#update-holiday-form").length > 0){
        $('#modal-alert').on('hidden.bs.modal', function() { 
            location.href = path + "/admin/holidays/index" ;        
        });
    }
});
            