var global_coderequest = '', htmlArea = '', showDefs  = '', dropzonefiles = 0,
    filesended = 0, flgerror = 0, errorname=[], upname=[], btnClicked="";

$(document).ready(function () {

    countdown.start(timesession);

    /*
     * Demo version
     */
    if (demoVersion == 1){
        if ($('#logindemo').val() == 'user' || $('#logindemo').val() == 'operator' ) {
            $('#btnUpdatePerson').prop('disabled', true);
        }
    }

    new gnMenu( document.getElementById( 'gn-menu' ) );

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    var objAttendanceData = {
        loadParents: function(studentID) {
            $.post(path+"/acd/acdAttendance/ajaxParents",{_token:$("#_token").val(),studentID:studentID},function(valor) {
                $('#parents-line').html(valor);

                $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green',
                });
                return false;
            });
        }
    };

    /*
     * Datepicker
     */
    var startDate = moment().add(0,'d').format('DD/MM/YYYY');
    var holidays = $.ajax({
        type: "POST",
        url: path+"/scm/scmCommon/_getHolidays",
        data: {cmbYear: moment().format('YYYY')},
        async: false,
        dataType: 'json'
    }).responseJSON;

    datepickerOpts.daysOfWeekDisabled = "0,6";
    datepickerOpts.datesDisabled = holidays.dates;
    datepickerOpts.startDate = "'"+startDate+"'";
    
    $('.input-group.date').datepicker(datepickerOpts);
    
    /*
     * Clockpicker
     */
    $('.clockpicker').clockpicker({
        autoclose: true
    });

    /*
     * Mask
     */
    $('#strHour').mask('00');
    $('#strMinute').mask('00');

    /*
     *  Chosen
     */
    $("#cmbStudent").chosen({ width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});

    $("#cmbStudent").change(function(){
        objAttendanceData.loadParents($(this).val());
    });

    $("#strHour").change(function(){
        var tmp = parseInt(this.value,10); console.log(this.value);
        if((tmp<10) && this.value != '00')
            this.value='0'+tmp;
        
        if(tmp>3)
            this.value='03';
    });

    $("#strMinute").change(function(){
        var tmp = parseInt(this.value,10);
        if((tmp<10) && this.value != '00')
            this.value='0'+tmp;
        
        if(tmp>59)
            this.value='00';
    });

    if($("#update-attendance-form").length > 0){
        $('#description').summernote(
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
                placeholder:  makeSmartyLabel('editor_plh_description')
    
            }
        );
    }

    /*
     * Validate
     */
    $("#create-attendance-form").validate({
        ignore:[],
        rules: {
            cmbStudent:"required",
            subject:{required:true,minlength:3},
            dtstart:"required",
            timestart:"required"
        },
        messages: {
            cmbStudent:makeSmartyLabel('Alert_field_required'),
            subject:{required:makeSmartyLabel('Alert_field_required'),minlength:makeSmartyLabel('Alert_minlength')},
            dtstart:makeSmartyLabel('Alert_field_required'),
            timestart:makeSmartyLabel('Alert_field_required')
        }
    });

    $("#update-attendance-form").validate({
        ignore:[],
        rules: {
            cmbStudent:"required",
            subject:{required:true,minlength:3},
            dtstart:"required",
            timestart:"required"
        },
        messages: {
            cmbStudent:makeSmartyLabel('Alert_field_required'),
            subject:{required:makeSmartyLabel('Alert_field_required'),minlength:makeSmartyLabel('Alert_minlength')},
            dtstart:makeSmartyLabel('Alert_field_required'),
            timestart:makeSmartyLabel('Alert_field_required')
        }
    });


    /*
     * Buttons
     */
    $("#btnCancel").click(function(){
        location.href = path + "/acd/acdAttendance/index";
    });

    $("#btnSave").click(function(){

        if (!$("#create-attendance-form").valid()) {
            return false ;
        }

        var flgEmptyQt = $("input[name='parent[]']:checked").length,
            strHour = $('#strHour').val(), strMin = $('#strMinute').val();
        
        if(flgEmptyQt <= 0){
            modalAlertMultiple('danger',makeSmartyLabel('alert_select_parent'),'alert-attendance-create');
            return false ;
        }
        
        if(Number(strHour) == 0 && Number(strMin) == 0){
            modalAlertMultiple('danger',makeSmartyLabel('alert_select_duration'),'alert-attendance-create');
            return false ;
        }

        if(!$("#btnSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/acd/acdAttendance/checkSchedule',
                dataType: 'json',
                data: {
                    _token: $("#_token").val(),
                    dtstart: $("#dtstart").val(),
                    timestart: $("#timestart").val()
                },
                error: function (ret) {
                    modalAlertMultiple('danger', makeSmartyLabel('generic_error_msg'),'alert-attendance-create');
                },
                success: function(ret){    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        saveAttendance();
                    } else {
                        modalAlertMultiple(obj.type,obj.message,'alert-attendance-create');
                    }
                },
                beforeSend: function(){
                    $("#btnSave").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnSave").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
            });
        }
        
    });

    $("#btnSaveUpdate").click(function(){

        if (!$("#update-attendance-form").valid()) {
            return false ;
        }

        var flgEmptyQt = $("input[name='parent[]']:checked").length;
        if(flgEmptyQt <= 0){
            modalAlertMultiple('danger',makeSmartyLabel('alert_select_parent'),'alert-attendance-update');
            return false ;
        }

        if(Number(strHour) == 0 && Number(strMin) == 0){
            modalAlertMultiple('danger',makeSmartyLabel('alert_select_duration'),'alert-attendance-update');
            return false ;
        }

        if ($('#description').summernote('isEmpty')) {
            modalAlertMultiple('danger',makeSmartyLabel('APP_requireDescription'),'alert-attendance-update');
            return false;
        }

        var description = $('#description').summernote('code'), text = $('<p>').html(description).text();
        if (text.length < 3) {
            modalAlertMultiple('danger',makeSmartyLabel('editor_min_char'),'alert-attendance-update');
            return false;
        }
        
        console.log(text);
        if(!$("#btnSaveUpdate").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/acd/acdAttendance/updateAttendance',
                dataType: 'json',
                data: $("#update-attendance-form").serialize()+"&description="+description,
                error: function (ret) {
                    modalAlertMultiple('danger', makeSmartyLabel('Edit_failure'),'alert-attendance-update');
                },
                success: function(ret){    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Edit_sucess'),'success',path + '/acd/acdAttendance/index');
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-attendance-update');
                    }
                },
                beforeSend: function(){
                    $("#btnSaveUpdate").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnSaveUpdate").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
            });
        }
        
    });

    /* clean modal form */
    $('#modal-form-requester').on('hidden.bs.modal', function() {
        $('#requester-form').trigger('reset');
    });

    /* tooltip */
    $('[data-toggle="tooltip"]').tooltip();

});

function saveAttendance()
{
    $.ajax({
        type: "POST",
        url: path + '/acd/acdAttendance/createAttendance',
        dataType: 'json',
        data: $("#create-attendance-form").serialize(),
        error: function (ret) {
            modalAlertMultiple('danger', makeSmartyLabel('Alert_failure'),'alert-attendance-create');
        },
        success: function(ret){    
            var obj = jQuery.parseJSON(JSON.stringify(ret));

            if(obj.success) {
                showAlert(makeSmartyLabel('Alert_inserted'),'success',path + '/acd/acdAttendance/index');
            } else {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-attendance-create');
            }
        }
    });

    return false ;

}

function showNotes(codeRequest)
{
    $.ajax({
        url : path + '/lgp/lgpDPORequest/ajaxNotes',
        type : 'POST',
        data : {
            _token: $("#_token").val(),
            code_request: codeRequest
        },
        success : function(data) {
            $('#ticket_notes').html(data);
            /* tooltip */
            $('[data-toggle="tooltip"]').tooltip();
        },
        error : function(request,error)
        {

        }
    });

    return false ;
}

function download(idFile, typeAttach)
{
    var urlDownload = path+'/lgp/lgpDPORequest/downloadFile/id/'+idFile+'/type/'+typeAttach+'/';
    $(location).attr('href',urlDownload);
}


