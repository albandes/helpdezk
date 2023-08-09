//Initial settings of Dropzone
Dropzone.autoDiscover = false;
var dropzonefiles = 0,filesended = 0, flgerror = 0, errorname=[], upname=[], flgDefault=0, btnClicked = 0;

$(document).ready(function () {
    countdown.start(timesession);
    /**
     * Select2
     */
    $('#cmbCompany').select2({width:"100%",height:"100%",placeholder:vocab['select'],allowClear:true,minimumResultsForSearch: 10});
    
    /**
     * iCheck - checkboxes/radios styling
     */
    $('#onlyForward').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    /**
     * Buttons
     */    
    $("#btnCancel").attr("href", path + '/helpdezk/hdkGroup/index');
    
    $("#btnCreateGroup").click(function(){

        if (!$("#create-group-form").valid()) {
            return false ;
        }
        
        if(!$("#btnCreateGroup").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkGroup/createGroup',
                dataType: 'json',
                data: $("#create-group-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-create-group');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
        
                    if(obj.success){
                        $('#modal-group-code').val(obj.groupId);
                        $('#modal-group-company').val($("#cmbCompany option:selected").text());
                        $('#modal-group-name').val(obj.groupName);
                        $('#modal-group-level').val(obj.groupLevel);
                        $('#modal-group-create').modal('show');                        
                    }else{
                        modalAlertMultiple('warning',vocab['Alert_failure'],'alert-create-group');
                    }
                },
                beforeSend: function(){
                    $("#btnCreateGroup").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnCreateGroup").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
            });
        }
        
    });

    $("#btnUpdateGroup").click(function(){

        if (!$("#update-group-form").valid()) {
            return false ;
        }

        if(!$("#btnUpdateGroup").hasClass('disabled')){        
                $.ajax({     
                type: "POST",
                url: path + '/helpdezk/hdkGroup/updateGroup',
                dataType: 'json',
                data: $("#update-group-form").serialize(),
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
                    $("#btnUpdateGroup").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnUpdateGroup").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }    
            });
        }
    });

    /**
     * Validate
     */
    $("#create-group-form").validate({
        ignore:[],
        rules: {
            cmbCompany:{
                required:true,
            },
            groupName:{
                required:true,
                normalizer: function(value) {
                    return value.replace(/<.*?>/gi, "");
                },
                remote:{
                    url: path+'/helpdezk/hdkGroup/checkExist',
                    type: 'post',
                    dataType:'json',
                    async: false,
                    data:{  
                        _token:function(element){return $("#_token").val()},
                        companyId:function(element){return $("#cmbCompany").val()}
                    }
                }
            },
            groupLevel:{
                required:true,
                min:1,
                max:99
            }
        },
        messages: {            
            groupName:{required:vocab['Alert_field_required']},
            groupLevel:{required:vocab['Alert_field_required'], min:vocab['insert_min_value'] + " 1", max:vocab['insert_max_value'] + " 99"},
            cmbCompany:{required:vocab['Alert_field_required']}
        }
    });

    $("#update-group-form").validate({
        ignore:[],
        rules: {
            cmbCompany:{
                required:true,
            },
            groupName:{
                required:true,
                normalizer: function(value) {
                    return value.replace(/<.*?>/gi, "");
                },
                remote:{
                    url: path+'/helpdezk/hdkGroup/checkExist',
                    type: 'post',
                    dataType:'json',
                    async: false,
                    data:{  
                        _token:function(element){return $("#_token").val()},
                        groupId:function(element){return $("#groupId").val()},
                        companyId:function(element){return $("#cmbCompany").val()}
                    }
                }
            },
            groupLevel:{
                required:true,
                min:1,
                max:99
            }
        },
        messages: {            
            groupName:{required:vocab['Alert_field_required']},
            groupLevel:{required:vocab['Alert_field_required'], min:vocab['insert_min_value'] + " 1", max:vocab['insert_max_value'] + " 99"},
            cmbCompany:{required:vocab['Alert_field_required']}
        }
    });

    /* when the modal is hidden */
    $('#modal-group-create').on('hidden.bs.modal', function() { 
        location.href = path + "/helpdezk/hdkGroup/index";        
    });

    if($("#update-group-form").length > 0){
        $('#modal-alert').on('hidden.bs.modal', function() { 
            location.href = path + "/helpdezk/hdkGroup/index" ;        
        });
    }
})
