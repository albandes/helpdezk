//Initial settings of Dropzone
Dropzone.autoDiscover = false;
var dropzonefiles = 0,filesended = 0, flgerror = 0, errorname=[], upname=[], flgDefault=0, btnClicked = 0;

$(document).ready(function () {
    countdown.start(timesession);

    /*
     * iCheck - checkboxes/radios styling
     */
    $('#permissionGroup').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });
       
    /*
     * Buttons
     */
    
    $("#btnCancel").attr("href", path + '/admin/UserType/index');
    

    $("#btnCreateUserType").click(function(){

        if (!$("#create-userType-form").valid()) {
            return false ;
        }
        
        if(!$("#btnCreateUserType").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/UserType/createUserType',
                dataType: 'json',
                data: $("#create-userType-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-create-userType');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
        
                    if(obj.success){
                        $('#modal-id').val(obj.id);
                        $('#modal-userType').val(obj.userType);
                        $('#modal-permissionGroup').val(obj.permissionGroup);
                        $('#modal-langKeyName').val(obj.langKeyName);         
                        $('#modal-userType-create').modal('show');                        
                    }else{
                        modalAlertMultiple('warning',vocab['Alert_failure'],'alert-create-userType');
                    }
                },
                beforeSend: function(){
                    $("#btnCreateUserType").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnCreateUserType").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                }
            });
        }
        
    });

    $("#btnUpdateUserType").click(function(){

        if (!$("#update-userType-form").valid()) {
            return false ;
        }

        if(!$("#btnUpdateUserType").hasClass('disabled')){        
                $.ajax({     
                type: "POST",
                url: path + '/admin/UserType/updateUserType',
                dataType: 'json',
                data: $("#update-userType-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Edit_failure'],'alert-update-userType');
                },
                success: function(ret){    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));    
                    if(obj.success) {
                        showAlert(vocab['Edit_sucess'],'success');
                    } else {
                        modalAlertMultiple('danger',vocab['Edit_failure'],'alert-update-userType');
                    }
                },
                beforeSend: function(){
                    $("#btnUpdateUserType").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnUpdateUserType").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }    
            });
        }

    });

    /*
     * Validate
     */
    $("#create-userType-form").validate({
        ignore:[],
        rules: {
            userType:{
                required:true,
                noAccent:true,
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                minlength:3,
                remote:{
                    url: path+'/admin/UserType/checkExist',
                    type: 'post',
                    dataType:'json',
                    async: false,
                    data:{  
                        _token:function(element){return $("#_token").val()},
                    }
                }
            },
            langKeyName:{
                required:true,
                noAccent:true,
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },                
                minlength:3,
            }
        },
        messages: {            
            userType:{required:vocab['Alert_field_required'], minlength:vocab['Alert_minimum_three_characters']},
            langKeyName:{required:vocab['Alert_field_required'], minlength:vocab['Alert_minimum_three_characters']},
        }
    });

    $("#update-userType-form").validate({
        ignore:[],
        rules: {
            userType:{
                required:true,
                noAccent:true,
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },
                minlength:3,
                remote:{
                    url: path+'/admin/UserType/checkExist',
                    type: 'post',
                    dataType:'json',
                    async: false,
                    data:{  
                        _token:function(element){return $("#_token").val()},
                        userTypeID:function(element){return $("#userTypeID").val()},
                    }
                }
            },
            langKeyName:{
                required:true,
                noAccent:true,
                normalizer: function(value) {
                    value = value.replace(/<.*?>/gi, "");
                    return value.replace(/(^\s+|\s+$)/gm, "");
                },                
                minlength:3,
            }
        },
        messages: {            
            userType:{required:vocab['Alert_field_required'], minlength:vocab['Alert_minimum_three_characters']},
            langKeyName:{required:vocab['Alert_field_required'], minlength:vocab['Alert_minimum_three_characters']},
        }
    });
    $.validator.addMethod('noAccent', function(strValue) {
        var re = new RegExp("^[a-zA-Z0-9_]+$","i");
        return re.test(strValue);
    }, vocab['key_no_accents_no_whitespace']);

    /* when the modal is hidden */
    $('#modal-userType-create').on('hidden.bs.modal', function() { 
        location.href = path + "/admin/UserType/index";        
    });

    if($("#update-userType-form").length > 0){
        $('#modal-alert').on('hidden.bs.modal', function() { 
            location.href = path + "/admin/UserType/index" ;        
        });
    }

    $("#permissionGroup").on('ifChecked ifUnchecked',function(e){
        if(e.type == 'ifChecked'){
            flgDefault = 1;
        }else{
            flgDefault = 0;
        }
    });
})
