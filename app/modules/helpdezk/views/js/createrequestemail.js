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
    $("#cmbSrvType").chosen({ width: "100%",   no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#cmbArea").chosen({ width: "100%",  no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#cmbType").chosen({ width: "100%",  no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#cmbItem").chosen({ width: "100%",   no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#cmbService").chosen({ width: "100%",  no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#cmbCompany").chosen({ width: "100%",  no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#cmbDepartment").chosen({ width: "100%",  no_results_text: "Nada encontrado!", disable_search_threshold: 10});
    $("#cmbLoginLayout").chosen({ width: "100%",  no_results_text: "Nada encontrado!", disable_search_threshold: 10});

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
        },
        changeDepartment: function(){
            var companyId = $("#cmbCompany").val();
            $.post(path+"/helpdezk/hdkRequestEmail/ajaxDepartments",{companyId: companyId},
                function(valor){
                    $("#cmbDepartment").html(valor);
                    $("#cmbDepartment").trigger("chosen:updated");
                });
        },
        changePort: function(){
            var val = $('#cmbSrvType').val(), port = $('#txtPort');

            switch(val){
                case "pop":
                    port.val("110");
                    port.removeAttr('readonly');
                    break;
                case "imap":
                    port.val("143");
                    port.removeAttr('readonly');
                    break;
                case "pop-gmail":
                    port.val("995");
                    port.attr('readonly','readonly');
                    break;
                case "imap-gmail":
                    port.val("993");
                    port.attr('readonly','readonly');
                    break;
                default:
                    port.val("");
                    port.removeAttr('readonly');
                    break;
            }
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

    $("#cmbCompany").change(function(){
        objRequestEmail.changeDepartment();
    });

    $("#cmbSrvType").change(function(){
        objRequestEmail.changePort();
    });

    $("#checkCreateUser").on('ifChecked ifUnchecked',function(e){
        if(e.type == 'ifChecked'){
            $(".createNewUser").removeClass('hide');
        }else{
            $(".createNewUser").addClass('hide');
        }
    });

    
    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/helpdezk/hdkRequestEmail/index');

    $("#btnCreateReqEmail").click(function(){

        if (!$("#create-request-email-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/helpdezk/hdkRequestEmail/createRequestEmail',
            dataType: 'json',
            data: $("#create-request-email-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-request-email');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == "Ok") {
                    $('#modal-notification').html(makeSmartyLabel('Alert_inserted'));
                    $("#btn-modal-ok").attr("href", path + '/helpdezk/hdkRequestEmail/index');
                    $("#tipo-alert").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');
                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-request-email');
                }
            }
        });
    });

    $("#btnUpdateReqEmail").click(function(){

        if (!$("#update-request-email-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/helpdezk/hdkRequestEmail/updateRequestEmail',
            dataType: 'json',
            data: $("#update-request-email-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-update-request-email');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'Ok' ) {
                    $('#modal-notification').html(makeSmartyLabel('Edit_sucess'));
                    $("#btn-modal-ok").attr("href", path + '/helpdezk/hdkRequestEmail/index');
                    $("#tipo-alert").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');

                } else {

                    modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-update-request-email');

                }

            }

        });


    });

    /*
     * Validate
     */
    $("#create-request-email-form").validate({
        ignore:[],
        rules: {
            txtServer:      "required",
            cmbSrvType:     "required",
            txtPort:        "required",
            txtEmail:       {required:true,email:true},
            txtPassword:    "required",
            cmbService:     "required",
            cmbDepartment:  {required:"#checkCreateUser:checked"}
        },
        messages: {
            txtServer:      makeSmartyLabel('Alert_field_required'),
            cmbSrvType:     makeSmartyLabel('Alert_field_required'),
            txtPort:        makeSmartyLabel('Alert_field_required'),
            txtEmail:       {required:makeSmartyLabel('Alert_field_required'),email:makeSmartyLabel('Alert_invalid_email')},
            txtPassword:    makeSmartyLabel('Alert_field_required'),
            cmbService:     makeSmartyLabel('Alert_field_required'),
            cmbDepartment:  {required:makeSmartyLabel('Alert_field_required')}
        }
    });

    $("#update-request-email-form").validate({
        ignore:[],
        rules: {
            txtServer:      "required",
            cmbSrvType:     "required",
            txtPort:        "required",
            txtEmail:       {required:true,email:true},
            txtPassword:    "required",
            cmbService:     "required",
            cmbDepartment:  {required:"#checkCreateUser:checked"}
        },
        messages: {
            txtServer:      makeSmartyLabel('Alert_field_required'),
            cmbSrvType:     makeSmartyLabel('Alert_field_required'),
            txtPort:        makeSmartyLabel('Alert_field_required'),
            txtEmail:       {required:makeSmartyLabel('Alert_field_required'),email:makeSmartyLabel('Alert_invalid_email')},
            txtPassword:    makeSmartyLabel('Alert_field_required'),
            cmbService:     makeSmartyLabel('Alert_field_required'),
            cmbDepartment:  {required:makeSmartyLabel('Alert_field_required')}
        }
    });

});

