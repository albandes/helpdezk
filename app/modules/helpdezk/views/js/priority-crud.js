//Initial settings of Dropzone
Dropzone.autoDiscover = false;
var dropzonefiles = 0,filesended = 0, flgerror = 0, errorname=[], upname=[], flgDefault=0, btnClicked = 0;

$(document).ready(function () {
    countdown.start(timesession);
    /**
     * Select2
     */
    $('#cmbArea').select2({width:"100%",placeholder:vocab['Select'],allowClear:true});
    $('#cmbType').select2({width:"100%",placeholder:vocab['Select'],allowClear:true});
    $('#cmbItem').select2({width:"100%",placeholder:vocab['Select'],allowClear:true});
    $('#cmbService').select2({width:"100%",placeholder:vocab['Select'],allowClear:true});
    
    /**
     * iCheck - checkboxes/radios styling
     */
    $('#priorityDefault,#priorityVip').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    /**
     * Mask
     */
    $('#exhibitionOrder').mask('000');

    /**
     * Colorpicker
     */
    $('#color-picker-component').colorpicker();

   
    /*
     * Buttons
     */
    
    $("#btnCancel").attr("href", path + '/helpdezk/hdkPriority/index');
    

    $("#btnCreatePriority").click(function(){

        if (!$("#create-priority-form").valid()) {
            return false ;
        }
        
        if(!$("#btnCreatePriority").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkPriority/createPriority',
                dataType: 'json',
                data: $("#create-priority-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-create-priority');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
        
                    if(obj.success){
                        $('#modal-priority-code').val(obj.priorityId);
                        $('#modal-priority-name').val(obj.priorityName);
                        $('#modal-priority-order').val(obj.priorityOrder);
                        $('#modal-priority-color').val(obj.priorityColor);
                        $('#modal-priority-create').modal('show');                        
                    }else{
                        modalAlertMultiple('warning',vocab['Alert_failure'],'alert-create-priority');
                    }
                },
                beforeSend: function(){
                    $("#btnCreatePriority").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnCreatePriority").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
            });
        }
        
    });

    $("#btnUpdatePriority").click(function(){

        if (!$("#update-priority-form").valid()) {
            return false ;
        }

        if(!$("#btnUpdatePriority").hasClass('disabled')){        
                $.ajax({     
                type: "POST",
                url: path + '/helpdezk/hdkPriority/updatePriority',
                dataType: 'json',
                data: $("#update-priority-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Edit_failure'],'alert-update-priority');
                },
                success: function(ret){    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));    
                    if(obj.success) {
                        showAlert(vocab['Edit_sucess'],'success');
                    } else {
                        modalAlertMultiple('danger',vocab['Edit_failure'],'alert-update-priority');
                    }
                },
                beforeSend: function(){
                    $("#btnUpdatePriority").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnUpdatePriority").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }    
            });
        }
    });

    /**
     * Validate
     */
    $("#create-priority-form").validate({
        ignore:[],
        rules: {
            priorityName:{
                required:true,
                normalizer: function(value) {
                    return value.replace(/<.*?>/gi, "");
                },
                remote:{
                    url: path+'/helpdezk/hdkPriority/checkExist',
                    type: 'post',
                    dataType:'json',
                    async: false,
                    data:{  
                        _token:function(element){return $("#_token").val()}
                    }
                }
            },
            exhibitionOrder:{
                required:true,
            },
            priorityColor:{
                required:true,
            }
        },
        messages: {            
            priorityName:{required:vocab['Alert_field_required']},
            exhibitionOrder:{required:vocab['Alert_field_required']},
            priorityColor:{required:vocab['Alert_field_required']}
        }
    });

    $("#update-priority-form").validate({
        ignore:[],
        rules: {
            priorityName:{
                required:true,
                normalizer: function(value) {
                    return value.replace(/<.*?>/gi, "");
                },
                remote:{
                    url: path+'/helpdezk/hdkPriority/checkExist',
                    type: 'post',
                    dataType:'json',
                    async: false,
                    data:{  
                        _token:function(element){return $("#_token").val()},
                        priorityId:function(element){return $("#priorityId").val()}
                    }
                }
            },
            exhibitionOrder:{
                required:true,
            },
            priorityColor:{
                required:true,
            }
        },
        messages: {            
            priorityName:{required:vocab['Alert_field_required']},
            exhibitionOrder:{required:vocab['Alert_field_required']},
            priorityColor:{required:vocab['Alert_field_required']}
        }
    });

    /* when the modal is hidden */
    $('#modal-priority-create').on('hidden.bs.modal', function() { 
        location.href = path + "/helpdezk/hdkPriority/index";        
    });

    if($("#update-priority-form").length > 0){
        $('#modal-alert').on('hidden.bs.modal', function() { 
            location.href = path + "/helpdezk/hdkPriority/index" ;        
        });
    }
})
