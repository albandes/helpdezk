var global_idperson = '';
$(document).ready(function () {

    countdown.start(timesession);

    new gnMenu( document.getElementById( 'gn-menu' ) );

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    /*
     *  Chosen
     */
    $("#cmbGrpCompany").chosen({ width: "100%", no_results_text: "Nada encontrado!",disable_search_threshold: 10});

    /*
     * Mask
     */
    $('#attLevel').mask('999');


    //$('[data-toggle="tooltip"]').tooltip();
    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/helpdezk/hdkGroup/index');

    $("#btnCreateGroup").click(function(){

        if (!$("#create-group-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/helpdezk/hdkGroup/createGroup',
            dataType: 'json',
            data: $("#create-group-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-group');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'OK') {

                    $('#modal-notification').html(makeSmartyLabel('Alert_inserted'));
                    $("#btn-modal-ok").attr("href", path + '/helpdezk/hdkGroup/index');
                    $("#tipo-alert").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');

                } else {

                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-group');

                }

            }

        });
    });

    $("#btnUpdateGroup").click(function(){

        if (!$("#update-group-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/helpdezk/hdkGroup/updateGroup',
            dataType: 'json',
            data: $("#update-group-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-update-group');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'OK' ) {
                    
                    $('#modal-notification').html(makeSmartyLabel('Alert_success_update'));
                    $("#btn-modal-ok").attr("href",  path + '/helpdezk/hdkGroup/index');
                    $("#tipo-alert").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');

                } else {

                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-update-group');

                }

            }

        });


    });

    /*
     * Validate
     */
    $("#create-group-form").validate({
        ignore:[],
        rules: {
            grpName:        "required",
            attLevel:       "required",
            cmbGrpCompany:  "required"
        },
        messages: {
            grpName:        makeSmartyLabel('Alert_field_required'),
            attLevel:       makeSmartyLabel('Alert_field_required'),
            cmbGrpCompany:  makeSmartyLabel('Alert_field_required')
        }
    });

    $("#update-group-form").validate({
        ignore:[],
        rules: {
            grpName:        "required",
            attLevel:       "required",
            cmbGrpCompany:  "required"
        },
        messages: {
            title:          makeSmartyLabel('Alert_field_required'),
            attLevel:       makeSmartyLabel('Alert_field_required'),
            cmbGrpCompany:  makeSmartyLabel('Alert_field_required')
        }
    });

    /* clean modal's fields */
    $('.modal').on('hidden.bs.modal', function() {
        $("input[name='validity']:checked").iCheck('unCheck');
        $("input[name=availableOperatorNew][value=1]").iCheck('check');
        $("input[name=availableUserNew][value=1]").iCheck('check');
        $('#availableOpe_lineNew').addClass('hide');
        $('#availableUser_lineNew').addClass('hide');
        $('#topic-form').trigger('reset');
    });


});



