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
    $('#cmbValidity').select2({width:"100%",placeholder:vocab['Select'],allowClear:true}); 
    
    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/admin/ApiToken/index');

    $("#btnCreateApiToken").click(function(){

        if (!$("#create-apiToken-form").valid()) {
            return false ;
        }

        if(!$("#btnCreateApiToken").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/ApiToken/createApiToken',
                dataType: 'json',
                data: $("#create-apiToken-form").serialize(),
                
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-create-apiToken');
                },                
                success: function(ret){    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.success){
                        $('#modal-id').val(obj.id);
                        $('#modal-app').val(obj.app); 
                        $('#modal-company').val(obj.company); 
                        $('#modal-email').val(obj.email);  
                        $('#modal-apiToken-create').modal('show');                        
                    }else{
                        modalAlertMultiple('danger',vocab['Alert_failure'],'alert-create-apiToken');
                    }
                },
                beforeSend: function(){
                    $("#btnCreateApiToken").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnCreateApiToken").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
            });
        }
        
    });

    $("#btnUpdateApiToken").click(function(){

        if (!$("#update-apiToken-form").valid()) {
            return false ;
        }

        if(!$("#btnUpdateApiToken").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/ApiToken/updateApiToken',
                dataType: 'json',
                data: $("#update-apiToken-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Edit_failure'],'alert-update-apiToken');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(vocab['Edit_sucess'],'success');
                    } else {
                        modalAlertMultiple('danger',vocab['Edit_failure'],'alert-update-apiToken');
                    }
                },
                beforeSend: function(){
                    $("#btnUpdateApiToken").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnUpdateApiToken").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
    
            });
        }

    });

    /*
     * Validate
     */
    $("#create-apiToken-form").validate({
        ignore:[],
        rules: {
            app:{
                required:true,
                minlength:3,
            },
            company:{
                required:true,
                minlength:3,               
            },
            email:{
                required:true               
            },
            numberValidity:{
                required:true,
                min:1                
            },
            cmbValidity:{
                required:true               
            }    
        },
        messages: {
            app:{required:vocab['Alert_field_required'], minlength:vocab['Alert_minimum_three_characters']},
            company:{required:vocab['Alert_field_required'], minlength:vocab['Alert_minimum_three_characters']},
            email:{required:vocab['Alert_field_required'],email:vocab['valid_email']},
            numberValidity:{required:vocab['Alert_field_required'],min:vocab['Alert_minvalue_requered']+' 1.'},
            cmbValidity:{required:vocab['Alert_field_required']}
        }
     });

    $("#update-apiToken-form").validate({
        ignore:[],
        rules: {
            app:{
                required:true,
                minlength:3,
            },
            company:{
                required:true,
                minlength:3,               
            },
            email:{
                required:true
            },
            numberValidity:{
                required:true,
                min:1              
            },
            cmbValidity:{
                required:true               
            }    
        },
        messages: {
            app:{required:vocab['Alert_field_required'],minlength:vocab['Alert_minimum_three_characters']},
            company:{required:vocab['Alert_field_required'],minlength:vocab['Alert_minimum_three_characters']},
            email:{required:vocab['Alert_field_required'],email:vocab['valid_email']},
            numberValidity:{required:vocab['Alert_field_required'],min:vocab['Alert_minvalue_requered']+' 1.'},
            cmbValidity:{required:vocab['Alert_field_required']}
        }
    });

    /* when the modal is hidden */
    $('#modal-apiToken-create').on('hidden.bs.modal', function() { 
        location.href = path + "/admin/ApiToken/index" ;        
    });

    if($("#update-apiToken-form").length > 0){
        $('#modal-alert').on('hidden.bs.modal', function() { 
            location.href = path + "/admin/ApiToken/index" ;        
        });
    }
});
            