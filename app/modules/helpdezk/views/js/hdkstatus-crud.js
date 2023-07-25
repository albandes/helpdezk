//Initial settings of Dropzone
Dropzone.autoDiscover = false;
var dropzonefiles = 0,filesended = 0, flgerror = 0, errorname=[], upname=[], flgDefault=0, btnClicked = 0;

$(document).ready(function () {
    countdown.start(timesession);
    /**
     * Select2
     */
    $('#cmbStatusGroup').select2({width:"100%",height:"100%",placeholder:vocab['select'],allowClear:true,minimumResultsForSearch: 10});
    
    /**
     * iCheck - checkboxes/radios styling
     */
    $('#stopTimeFlag').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    /**
     * Mask
     */
    //$('#userView').mask('000');

    /**
     * Colorpicker
     */
    $('#color-picker-component').colorpicker();

   
    /**
     * Buttons
     */    
    $("#btnCancel").attr("href", path + '/helpdezk/hdkStatus/index');
    
    $("#btnCreateHdkStatus").click(function(){

        if (!$("#create-hdkstatus-form").valid()) {
            return false ;
        }
        
        if(!$("#btnCreateHdkStatus").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkStatus/createStatus',
                dataType: 'json',
                data: $("#create-hdkstatus-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-create-hdkstatus');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
        
                    if(obj.success){
                        $('#modal-hdkstatus-code').val(obj.hdkStatusId);
                        $('#modal-hdkstatus-name').val(obj.hdkStatusName);
                        $('#modal-hdkstatus-user-view').val(obj.userView);
                        $('#modal-hdkstatus-color').val(obj.hdkStatusColor);
                        $('#modal-hdkstatus-create').modal('show');                        
                    }else{
                        modalAlertMultiple('warning',vocab['Alert_failure'],'alert-create-hdkstatus');
                    }
                },
                beforeSend: function(){
                    $("#btnCreateHdkStatus").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnCreateHdkStatus").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
            });
        }
        
    });

    $("#btnUpdateHdkStatus").click(function(){

        if (!$("#update-hdkstatus-form").valid()) {
            return false ;
        }

        if(!$("#btnUpdateHdkStatus").hasClass('disabled')){        
                $.ajax({     
                type: "POST",
                url: path + '/helpdezk/hdkStatus/updateStatus',
                dataType: 'json',
                data: $("#update-hdkstatus-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Edit_failure'],'alert-update-hdkstatus');
                },
                success: function(ret){    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));    
                    if(obj.success) {
                        showAlert(vocab['Edit_sucess'],'success');
                    } else {
                        modalAlertMultiple('danger',vocab['Edit_failure'],'alert-update-hdkstatus');
                    }
                },
                beforeSend: function(){
                    $("#btnUpdateHdkStatus").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnUpdateHdkStatus").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }    
            });
        }
    });

    /**
     * Validate
     */
    $("#create-hdkstatus-form").validate({
        ignore:[],
        rules: {
            hdkStatusName:{
                required:true,
                normalizer: function(value) {
                    return value.replace(/<.*?>/gi, "");
                },
                remote:{
                    url: path+'/helpdezk/hdkStatus/checkExist',
                    type: 'post',
                    dataType:'json',
                    async: false,
                    data:{  
                        _token:function(element){return $("#_token").val()}
                    }
                }
            },
            userView:{
                required:true,
                normalizer: function(value) {
                    return value.replace(/<.*?>/gi, "");
                },
                remote:{
                    url: path+'/helpdezk/hdkStatus/checkExistUserView',
                    type: 'post',
                    dataType:'json',
                    async: false,
                    data:{  
                        _token:function(element){return $("#_token").val()}
                    }
                }
            },
            hdkStatusColor:{
                required:true,
            }
        },
        messages: {            
            hdkStatusName:{required:vocab['Alert_field_required']},
            userView:{required:vocab['Alert_field_required']},
            hdkStatusColor:{required:vocab['Alert_field_required']}
        }
    });

    $("#update-hdkstatus-form").validate({
        ignore:[],
        rules: {
            hdkStatusName:{
                required:true,
                normalizer: function(value) {
                    return value.replace(/<.*?>/gi, "");
                },
                remote:{
                    url: path+'/helpdezk/hdkStatus/checkExist',
                    type: 'post',
                    dataType:'json',
                    async: false,
                    data:{  
                        _token:function(element){return $("#_token").val()},
                        hdkStatusId:function(element){return $("#hdkStatusId").val()}
                    }
                }
            },
            userView:{
                required:true,
                normalizer: function(value) {
                    return value.replace(/<.*?>/gi, "");
                },
                remote:{
                    url: path+'/helpdezk/hdkStatus/checkExistUserView',
                    type: 'post',
                    dataType:'json',
                    async: false,
                    data:{  
                        _token:function(element){return $("#_token").val()},
                        hdkStatusId:function(element){return $("#hdkStatusId").val()}
                    }
                }
            },
            hdkStatusColor:{
                required:true,
            }
        },
        messages: {            
            hdkStatusName:{required:vocab['Alert_field_required']},
            userView:{required:vocab['Alert_field_required']},
            hdkStatusColor:{required:vocab['Alert_field_required']}
        }
    });

    /* when the modal is hidden */
    $('#modal-hdkstatus-create').on('hidden.bs.modal', function() { 
        location.href = path + "/helpdezk/hdkStatus/index";        
    });

    if($("#update-hdkstatus-form").length > 0){
        $('#modal-alert').on('hidden.bs.modal', function() { 
            location.href = path + "/helpdezk/hdkStatus/index" ;        
        });
    }
})
