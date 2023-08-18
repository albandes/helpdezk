var objRequestEmail = {
    loadType: function() {
        var areaId = $("#cmbArea").val();
        $.post(path+"/helpdezk/hdkTicket/ajaxTypes",{areaID: areaId},
            function(valor){
                $("#cmbType").html(valor);
                $("#cmbType").trigger("change");
                return objRequestEmail.loadItem();
            })
    },
    loadItem: function(){
        var typeId = $("#cmbType").val();
        $.post(path+"/helpdezk/hdkTicket/ajaxItens",{typeID: typeId},
            function(valor){
                $("#cmbItem").html(valor);
                $("#cmbItem").trigger("change");
                return objRequestEmail.loadService();
            });
    },
    loadService: function(){
        var itemId = $("#cmbItem").val();
        $.post(path+"/helpdezk/hdkTicket/ajaxServices",{itemID: itemId},
            function(valor){
                $("#cmbService").html(valor);
                $("#cmbService").trigger("change");
            });
    },
    loadDepartment: function(){
        var companyId = $("#cmbCompany").val();
        $.post(path+"/admin/person/ajaxDepartment",{companyId: companyId, _token:$("#_token").val()},
            function(valor){
                $("#cmbDepartment").html(valor);
                $("#cmbDepartment").trigger("change");
            });
    },
    changePort: function(){
        var val = $('#cmbServerType').val(), port = $('#serverPort');

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

$(document).ready(function () {
    countdown.start(timesession);
    /**
     * Select2
     */
    $('#cmbServerType').select2({width:"100%",height:"100%",placeholder:vocab['select'],allowClear:true,minimumResultsForSearch: 10});
    $('#cmbArea').select2({width:"100%",height:"100%",placeholder:vocab['select'],allowClear:true,minimumResultsForSearch: 10});
    $('#cmbType').select2({width:"100%",height:"100%",placeholder:vocab['select'],allowClear:true,minimumResultsForSearch: 10});
    $('#cmbItem').select2({width:"100%",height:"100%",placeholder:vocab['select'],allowClear:true,minimumResultsForSearch: 10});
    $('#cmbService').select2({width:"100%",height:"100%",placeholder:vocab['select'],allowClear:true,minimumResultsForSearch: 10});
    $('#cmbCompany').select2({width:"100%",height:"100%",placeholder:vocab['select'],allowClear:true,minimumResultsForSearch: 10});
    $('#cmbDepartment').select2({width:"100%",height:"100%",placeholder:vocab['select'],allowClear:true,minimumResultsForSearch: 10});
    $('#cmbLoginLayout').select2({width:"100%",height:"100%",placeholder:vocab['select'],allowClear:true,minimumResultsForSearch: 10});

    $("#cmbArea").change(function(){
        objRequestEmail.loadType();
    });

    $("#cmbType").change(function(){
        objRequestEmail.loadItem();
    });

    $("#cmbItem").change(function(){
        objRequestEmail.loadService();
    });

    $("#cmbCompany").change(function(){
        objRequestEmail.loadDepartment();
    });

    $("#cmbServerType").change(function(){
        objRequestEmail.changePort();
    });
    
    /**
     * iCheck - checkboxes/radios styling
     */
    $('#createUser,#deleteEmail,#insertNote').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    $("#createUser").on('ifChecked ifUnchecked',function(e){
        if(e.type == 'ifChecked'){
            $(".newUserLine").removeClass('d-none');
        }else{
            $(".newUserLine").addClass('d-none');
        }
    });

    /**
     * Buttons
     */    
    $("#btnCancel").attr("href", path + '/helpdezk/hdkRequestEmail/index');
    
    $("#btnCreateRequestEmail").click(function(){

        if (!$("#create-request-email-form").valid()) {
            return false ;
        }
        
        if(!$("#btnCreateRequestEmail").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkRequestEmail/createRequestEmail',
                dataType: 'json',
                data: $("#create-request-email-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-create-request-email');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
        
                    if(obj.success){
                        $('#modal-request-email-code').val(obj.requestEmailId);
                        $('#modal-server-url').val(obj.serverUrl);
                        $('#modal-server-type').val($("#cmbServerType option:selected").text());
                        $('#modal-request-email-create').modal('show');                        
                    }else{
                        modalAlertMultiple('danger',obj['message'],'alert-create-request-email');
                    }
                },
                beforeSend: function(){
                    $("#btnCreateRequestEmail").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnCreateRequestEmail").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
            });
        }
        
    });

    $("#btnUpdateRequestEmail").click(function(){

        if (!$("#update-request-email-form").valid()) {
            return false ;
        }

        if(!$("#btnUpdateRequestEmail").hasClass('disabled')){        
                $.ajax({     
                type: "POST",
                url: path + '/helpdezk/hdkRequestEmail/updateRequestEmail',
                dataType: 'json',
                data: $("#update-request-email-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Edit_failure'],'alert-update-group');
                },
                success: function(ret){    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));    
                    if(obj.success) {
                        showAlert(vocab['Edit_sucess'],'success');
                    } else {
                        modalAlertMultiple('danger',vocab['Edit_failure'],'alert-update-group');
                    }
                },
                beforeSend: function(){
                    $("#btnUpdateRequestEmail").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnUpdateRequestEmail").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }    
            });
        }
    });

    /**
     * Validate
     */
    $("#create-request-email-form").validate({
        ignore:[],
        rules: {
            serverUrl:{required:true},
            cmbServerType:{required:true},
            servePort:{required:true},
            userEmail:{
                required:true,
                email:true
            },
            userPassword:{required:true},
            cmbService:{required:true},
            cmbDepartment:{required:"#createUser:checked"},
            cmbLoginLayout:{required:"#createUser:checked"}
        },
        messages: {            
            serverUrl:{required:vocab['Alert_field_required']},
            cmbServerType:{required:vocab['Alert_field_required']},
            servePort:{required:vocab['Alert_field_required']},
            userEmail:{required:vocab['Alert_field_required'], email:vocab['Alert_invalid_email']},
            userPassword:{required:vocab['Alert_field_required']},
            cmbService:{required:vocab['Alert_field_required']},
            cmbDepartment:{required:vocab['Alert_field_required']},
            cmbLoginLayout:{required:vocab['Alert_field_required']}
        },
        errorPlacement: function (error, element) {
            var name = $(element).attr("name");
            error.appendTo($("#" + name + "_validate_error"));
        }
    });

    $("#update-request-email-form").validate({
        ignore:[],
        rules: {
            serverUrl:{required:true},
            cmbServerType:{required:true},
            servePort:{required:true},
            userEmail:{
                required:true,
                email:true
            },
            userPassword:{required:true},
            cmbService:{required:true},
            cmbDepartment:{required:"#createUser:checked"},
            cmbLoginLayout:{required:"#createUser:checked"}
        },
        messages: {            
            serverUrl:{required:vocab['Alert_field_required']},
            cmbServerType:{required:vocab['Alert_field_required']},
            servePort:{required:vocab['Alert_field_required']},
            userEmail:{required:vocab['Alert_field_required'], email:vocab['Alert_invalid_email']},
            userPassword:{required:vocab['Alert_field_required']},
            cmbService:{required:vocab['Alert_field_required']},
            cmbDepartment:{required:vocab['Alert_field_required']},
            cmbLoginLayout:{required:vocab['Alert_field_required']}
        },
        errorPlacement: function (error, element) {
            var name = $(element).attr("name");
            error.appendTo($("#" + name + "_validate_error"));
        }
    });

    /* when the modal is hidden */
    $('#modal-request-email-create').on('hidden.bs.modal', function() { 
        location.href = path + "/helpdezk/hdkRequestEmail/index";        
    });

    if($("#update-request-email-form").length > 0){
        $('#modal-alert').on('hidden.bs.modal', function() { 
            location.href = path + "/helpdezk/hdkRequestEmail/index" ;        
        });
    }
})
