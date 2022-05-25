//Initial settings of Dropzone
Dropzone.autoDiscover = false;
var dropzonefiles = 0,filesended = 0, flgerror = 0, errorname=[], upname=[], flgDefault=0, btnClicked = 0;

$(document).ready(function () {
    countdown.start(timesession);
    /*
     * Select2
     */
 $('#cmbCompany').select2({width:"100%",placeholder:vocab['Select'],allowClear:true}); 
   
    /*
     * Buttons
     */
    
    $("#btnCancel").attr("href", path + '/helpdezk/hdkDepartment/index');
    

    $("#btnCreateDepartment").click(function(){

        if (!$("#create-department-form").valid()) {
            return false ;
        }
        
        if(!$("#btnCreateDepartment").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkDepartment/createDepartment',
                dataType: 'json',
                data: $("#create-department-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-create-department');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
        
                    if(obj.success){
                        $('#modal-id').val(obj.id);
                        $('#modal-department').val(obj.department);
                        $('#modal-company').val(obj.company);        
                        $('#modal-department-create').modal('show');                        
                    }else{
                        modalAlertMultiple('warning',vocab['Alert_failure'],'alert-create-department');
                    }
                },
                beforeSend: function(){
                    $("#btnCreateDepartment").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnCreateDepartment").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                }
            });
        }
        
    });

    $("#btnUpdateDepartment").click(function(){

        if (!$("#update-department-form").valid()) {
            return false ;
        }

        if(!$("#btnUpdateDepartment").hasClass('disabled')){        
                $.ajax({     
                type: "POST",
                url: path + '/helpdezk/hdkDepartment/updateDepartment',
                dataType: 'json',
                data: $("#update-department-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Edit_failure'],'alert-update-department');
                },
                success: function(ret){    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));    
                    if(obj.success) {
                        showAlert(vocab['Edit_sucess'],'success');
                    } else {
                        modalAlertMultiple('danger',vocab['Edit_failure'],'alert-update-department');
                    }
                },
                beforeSend: function(){
                    $("#btnUpdateDepartment").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnUpdateDepartment").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }    
            });
        }

    });

    /*
     * Validate
     */
    $("#create-department-form").validate({
        ignore:[],
        rules: {
            department:{
                required:true,
                normalizer: function(value) {
                    return value.replace(/<.*?>/gi, "");
                },
                minlength:2,
                remote:{
                    url: path+'/helpdezk/hdkDepartment/checkExist',
                    type: 'post',
                    dataType:'json',
                    async: false,
                    data:{  
                        _token:function(element){return $("#_token").val()},
                        idperson:function(element){return $("#cmbCompany").val()},
                    }
                }
            },
            cmbCompany:{
                required:true,
            }
        },
        messages: {            
            department:{required:vocab['Alert_field_required'], minlength:vocab['Alert_minimum_two_characters']},
            cmbCompany:{required:vocab['Alert_field_required']}
        }
    });

    $("#update-department-form").validate({
        ignore:[],
        rules: {
            department:{
                required:true,
                normalizer: function(value) {
                    return value.replace(/<.*?>/gi, "");
                },
                minlength:2,
                remote:{
                    url: path+'/helpdezk/hdkDepartment/checkExist',
                    type: 'post',
                    dataType:'json',
                    async: false,
                    data:{  
                        _token:function(element){return $("#_token").val()},
                        departmentID:function(element){return $("#departmentID").val()},
                        idperson:function(element){return $("#cmbCompany").val()}
                    }
                }
            },
            cmbCompany:{
                required:true,
            }    
        },
        messages: {
            department:{required:vocab['Alert_field_required'], minlength:vocab['Alert_minimum_two_characters']},
            cmbCompany:{required:vocab['Alert_field_required']}
        }
    });

    /* when the modal is hidden */
    $('#modal-department-create').on('hidden.bs.modal', function() { 
        location.href = path + "/helpdezk/hdkDepartment/index";        
    });

    if($("#update-department-form").length > 0){
        $('#modal-alert').on('hidden.bs.modal', function() { 
            location.href = path + "/helpdezk/hdkDepartment/index" ;        
        });
    }
})
