var global_idperson = '';
$(document).ready(function () {

    countdown.start(timesession);

    new gnMenu( document.getElementById( 'gn-menu' ) );

    $('.summernote').summernote({
        height: 200
    });

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    /*
     * Datepicker
     */
    $('.input-group.date').datepicker({
        format: "dd/mm/yyyy",
        language: "pt-BR",
        autoclose: true
    });

    if($('#flagUntil').val() == 'S'){
        $('#line_dtend').hide();
        $('#line_timeend').hide();
    }else{
        $('#line_dtend').show();
        $('#line_timeend').show();
    }

    $('#warningend').on('ifChecked ifUnchecked',function(e){
        if(e.type == 'ifChecked'){
            $('#line_dtend').hide();
            $('#line_timeend').hide();
        }else{
            $('#line_dtend').show();
            $('#line_timeend').show();
        }
    });

    $('input[name=availableOperatorNew]').on('ifClicked',function(){
        if($(this).val() == 1){
            $('#availableOpe_lineNew').addClass('hide');
        }else{
            $('#availableOpe_lineNew').removeClass('hide');
        }
    });

    $('input[name=availableUserNew]').on('ifClicked',function(){
        if($(this).val() == 1){
            $('#availableUser_lineNew').addClass('hide');
        }else{
            $('#availableUser_lineNew').removeClass('hide');
        }
    });

    $('input[name=validity]').on('ifClicked',function(){
        if($(this).val() == 1){
            $('#hoursValidity').val("");
            $('#daysValidity').val("");
        }else if($(this).val() == 2){
            $('#hoursValidity').focus();
            $('#daysValidity').val("");
        }else{
            $('#hoursValidity').val("");
            $('#daysValidity').focus();
        }
    });

    /*
     *  Chosen
     */
    $("#topic").chosen({ width: "100%", no_results_text: "Nada encontrado!",disable_search_threshold: 10});
    $("#showin").chosen({ width: "100%", no_results_text: "Nada encontrado!",disable_search_threshold: 10});

    /*
     * Mask
     */
    $('#timestart').mask('00:00');
    $('#timeend').mask('00:00');


    var formWarningData = $(document.getElementById("create-warning-form"));

    var objTopicData = {
        getOperatorGroup: function() {
            var countryId = $("#pais").val();
            $.post(path+"/helpdezk/hdkWarning/ajaxOperatorGroup",
                function(valor){
                    $("#availableOpe_listNew").html(valor);
                    $('#availableOpe_listNew .i-checks').iCheck('update');
                })
        },
        getCorporation: function() {
            $.post(path+"/helpdezk/hdkWarning/ajaxCorporation",
                function(valor) {
                    $("#availableUser_listNew").html(valor);
                });
        },
        changeTopic: function() {
            $.post(path+"/helpdezk/hdkWarning/ajaxTopic",
                function(valor) {
                    $("#topic").html(valor);
                    $("#topic").trigger("chosen:updated");
                    return false;
                });
        }
    }

    //$('[data-toggle="tooltip"]').tooltip();
    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/helpdezk/hdkWarning/index');

    $("#btnCreateWarning").click(function(){

        if (!$("#create-warning-form").valid()) {
            return false ;
        }

        var flagend, flagsendmail;

        if($('#warningend').is(":checked")){flagend = 'S';}
        else {flagend = 'N';}

        if($('#sendemailconf').is(":checked")){flagsendmail = 'S';}
        else {flagsendmail = 'N';}
        //
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkWarning/createWarning',
                dataType: 'json',
                data: {
                    _token: $('#_token').val(),
                    topic: $('#topic').val(),
                    title: $('#title').val(),
                    description: $('#description').val(),
                    dtstart: $('#dtstart').val(),
                    timestart: $('#timestart').val(),
                    dtend: $('#dtend').val(),
                    timeend: $('#timeend').val(),
                    warningend: flagend,
                    sendemailconf: flagsendmail,
                    showin: $('#showin').val()
                },
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-warning');
                },
                success: function(ret){

                    var obj = jQuery.parseJSON(JSON.stringify(ret));

                    if(obj.status == 'OK') {

                        $('#modal-notification').html(makeSmartyLabel('Alert_inserted'));
                        $("#btn-modal-ok").attr("href", path + '/helpdezk/hdkWarning/index');
                        $("#tipo-alert").attr('class', 'alert alert-success');
                        $('#modal-alert').modal('show');

                    } else {

                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-warning');

                    }

                }

            });


    });

    $("#btnUpdateWarning").click(function(){

        if (!$("#update-warning-form").valid()) {
            return false ;
        }

        var flagend, flagsendmail;

        if($('#warningend').is(":checked")){flagend = 'S';}
        else {flagend = 'N';}

        if($('#sendemailconf').is(":checked")){flagsendmail = 'S';}
        else {flagsendmail = 'N';}

        $.ajax({
            type: "POST",
            url: path + '/helpdezk/hdkWarning/updateWarning/idwarning/' + $('#idwarning').val(),
            dataType: 'json',
            data: {
                _token: $('#_token').val(),
                topic: $('#topic').val(),
                title: $('#title').val(),
                description: $('#description').val(),
                dtstart: $('#dtstart').val(),
                timestart: $('#timestart').val(),
                dtend: $('#dtend').val(),
                timeend: $('#timeend').val(),
                warningend: flagend,
                sendemailconf: flagsendmail,
                showin: $('#showin').val()
            },
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-warning');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'OK' ) {

                    //
                    $('#modal-notification').html(makeSmartyLabel('Alert_success_update'));
                    $("#btn-modal-ok").attr("href", path + '/helpdezk/hdkWarning/index');
                    $("#tipo-alert").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');

                } else {

                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-warning');

                }

            }

        });


    });

    $("#btnAddTopic").click(function(){
        objTopicData.getOperatorGroup();
        objTopicData.getCorporation();
        $('#modal-form-topic').modal('show');
    });

    $("#btnSendTopic").click(function(){
        console.log('clicou salvar');
        if (!$("#topic-form").valid()) {
            console.log('nao validou') ;
            return false;
        }
        $.ajax({
            type: "POST",
            url: path + '/helpdezk/hdkWarning/createTopic',
            dataType: 'json',
            data: $('#topic-form').serialize(),
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-topic');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if($.isNumeric(obj.idtopic)) {
                    modalAlertMultiple('success','T&oacute;pico inclu&iacute;do com sucesso !','alert-topic');
                    objTopicData.changeTopic();
                    setTimeout(function(){
                        $('#modal-form-topic').modal('hide');
                        $("input[name='validity']:checked").iCheck('unCheck');
                        $("input[name=availableOperatorNew][value=1]").iCheck('check');
                        $("input[name=availableUserNew][value=1]").iCheck('check');
                        $('#availableOpe_lineNew').addClass('hide');
                        $('#availableUser_lineNew').addClass('hide');
                        $('#topic-form').trigger('reset');
                    },2000);
                } else {
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-topic');
                }
            }
        });

    });

    /*
     * Validate
     */
    $("#create-warning-form").validate({
        ignore:[],
        rules: {
            title:          "required",
            description:    "required",
            dtstart:        "required",
            dtend:          {required:function(element){return !$('#warningend').is(':checked');}},
            timeend:        {required:function(element){return !$('#warningend').is(':checked');}}
        },
        messages: {
            title:          makeSmartyLabel('Alert_field_required'),
            description:    makeSmartyLabel('Alert_field_required'),
            dtstart:        makeSmartyLabel('Alert_field_required'),
            dtend:          makeSmartyLabel('Alert_field_required'),
            timeend:        makeSmartyLabel('Alert_field_required')
        }
    });

    $("#update-warning-form").validate({
        ignore:[],
        rules: {
            title:          "required",
            description:    "required",
            dtstart:        "required",
            dtend:          {required:function(element){return !$('#warningend').is(':checked');}},
            timeend:        {required:function(element){return !$('#warningend').is(':checked');}}
        },
        messages: {
            title:          makeSmartyLabel('Alert_field_required'),
            description:    makeSmartyLabel('Alert_field_required'),
            dtstart:        makeSmartyLabel('Alert_field_required'),
            dtend:          makeSmartyLabel('Alert_field_required'),
            timeend:        makeSmartyLabel('Alert_field_required')
        }
    });

    $("#topic-form").validate({
        ignore:[],
        rules: {
            modal_topic_title: "required"
        },
        messages: {
            modal_topic_title: makeSmartyLabel('Alert_field_required')
        }
    });

    /*
     * Clockpicker
     */
    $('.clockpicker').clockpicker({
        autoclose: true
    });

    /* limpa campos modal */
    $('.modal').on('hidden.bs.modal', function() {        
        $("input[name='validity']:checked").iCheck('unCheck');
        $("input[name=availableOperatorNew][value=1]").iCheck('check');
        $("input[name=availableUserNew][value=1]").iCheck('check');
        $('#availableOpe_lineNew').addClass('hide');
        $('#availableUser_lineNew').addClass('hide');
        $('#topic-form').trigger('reset');
    });


});



