var global_idproduto = '';
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
    $("#cmbArea").chosen({ width: "100%",  no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#cmbType").chosen({ width: "100%",  no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#cmbItem").chosen({ width: "100%",   no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#cmbService").chosen({ width: "100%",  no_results_text: "Nada encontrado!", disable_search_threshold: 10});

    /*
     * Datepicker
     */
    $('.input-group.date').datepicker({
        format: "dd/mm/yyyy",
        language: "pt-BR",
        autoclose: true
    });

    /*
     * Combos
     */
    var objRequestEmail = {
        changeArea: function() {
            var areaId = $("#cmbArea").val();
            $.post(path+"/helpdezk/hdkRequestEmail/ajaxTypes",{areaId: areaId},
                function(valor){
                    $("#cmbType").html(valor);
                    $("#cmbType").trigger("chosen:updated");
                    return objRequestEmail.changeItem();
                })
        },
        changeItem: function(){
            var typeId = $("#cmbType").val();
            $.post(path+"/helpdezk/hdkRequestEmail/ajaxItens",{typeId: typeId},
                function(valor){
                    $("#cmbItem").html(valor);
                    $("#cmbItem").trigger("chosen:updated");
                    return objRequestEmail.changeService();
                });
        },
        changeService: function(){
            var itemId = $("#cmbItem").val();
            $.post(path+"/helpdezk/hdkRequestEmail/ajaxServices",{itemId: itemId},
                function(valor){
                    $("#cmbService").html(valor);
                    $("#cmbService").trigger("chosen:updated");
                });
        }
    }

    $("#cmbArea").change(function(){
        objRequestEmail.changeArea();
    });

    $("#cmbType").change(function(){
        objRequestEmail.changeItem();
    });

    $("#cmbItem").change(function(){
        objRequestEmail.changeService();
    });
    
    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/helpdezk/hdkReason/index');

    $("#btnCreateReason").click(function(){

        if (!$("#create-reason-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/helpdezk/hdkReason/createReason',
            dataType: 'json',
            data: $("#create-reason-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-reason');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == "Ok") {
                    $('#modal-notification').html(makeSmartyLabel('Alert_inserted'));
                    $("#btn-modal-ok").attr("href", path + '/helpdezk/hdkReason/index');
                    $("#tipo-alert").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');
                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-reason');
                }
            },
            beforeSend: function(){
                $("#btnCancel").addClass('disabled');
                $("#btnCreateReason").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
            },
            complete: function(){
                $("#btnCancel").removeClass('disabled');
                $("#btnCreateReason").html("<i class='fa fa-check-circle'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
            }
        });
    });

    $("#btnUpdateReason").click(function(){

        if (!$("#update-reason-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/helpdezk/hdkReason/updateReason',
            dataType: 'json',
            data: $("#update-reason-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-update-reason');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'Ok' ) {
                    $('#modal-notification').html(makeSmartyLabel('Edit_sucess'));
                    $("#btn-modal-ok").attr("href", path + '/helpdezk/hdkReason/index');
                    $("#tipo-alert").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');

                } else {

                    modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-update-reason');

                }

            },
            beforeSend: function(){
                $("#btnCancel").addClass('disabled');
                $("#btnUpdateReason").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
            },
            complete: function(){
                $("#btnCancel").removeClass('disabled');
                $("#btnUpdateReason").html("<i class='fa fa-check-circle'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
            }

        });


    });

    /*
     * Validate
     */
    $("#create-reason-form").validate({
        ignore:[],
        rules: {
            cmbService: "required",
            txtReason:  {required:true}
        },
        messages: {
            cmbService: makeSmartyLabel('Alert_field_required'),
            txtReason:  {required:makeSmartyLabel('Alert_field_required')}
        }
    });

    $("#update-reason-form").validate({
        ignore:[],
        rules: {
            cmbService: "required",
            txtReason:  {required:true}
        },
        messages: {
            cmbService: makeSmartyLabel('Alert_field_required'),
            txtReason:  {required:makeSmartyLabel('Alert_field_required')}
        }
    });

});

