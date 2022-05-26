//Initial settings of Dropzone
Dropzone.autoDiscover = false;
var dropzonefiles = 0,filesended = 0, flgerror = 0, errorname=[], upname=[], flgDefault=0, btnClicked = 0;

$(document).ready(function () {
    countdown.start(timesession);
    /*
     * Select2
     */
 $('#cmbArea').select2({width:"100%",placeholder:vocab['Select'],allowClear:true});
 $('#cmbType').select2({width:"100%",placeholder:vocab['Select'],allowClear:true}); 
 $('#cmbItem').select2({width:"100%",placeholder:vocab['Select'],allowClear:true}); 
 $('#cmbService').select2({width:"100%",placeholder:vocab['Select'],allowClear:true});  

 
    /*
     * Combos
     */
    var objRequestEmail = {
        changeArea: function() {
            var areaID = $("#cmbArea").val();
            $.post(path+"/helpdezk/hdkReason/ajaxTypes",{areaID: areaID},
                function(valor){
                    $("#cmbType").html(valor);
                    $("#cmbType").trigger("chosen:updated");
                    return objRequestEmail.changeItem();
                })
        },
        changeItem: function(){
            var typeID = $("#cmbType").val();
            $.post(path+"/helpdezk/hdkReason/ajaxItens",{typeID: typeID},
                function(valor){
                    $("#cmbItem").html(valor);
                    $("#cmbItem").trigger("chosen:updated");
                    return objRequestEmail.changeService();
                });
        },
        changeService: function(){
            var itemID = $("#cmbItem").val();
            $.post(path+"/helpdezk/hdkReason/ajaxServices",{itemID: itemID},
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
        
        if(!$("#btnCreateReason").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkReason/createReason',
                dataType: 'json',
                data: $("#create-reason-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Alert_failure'],'alert-create-reason');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
        
                    if(obj.success){
                        $('#modal-id').val(obj.id);
                        $('#modal-reason').val(obj.reason);
                        $('#modal-area').val(obj.area);
                        $('#modal-type').val(obj.type);
                        $('#modal-item').val(obj.item);
                        $('#modal-service').val(obj.service);        
                        $('#modal-reason-create').modal('show');                        
                    }else{
                        modalAlertMultiple('warning',vocab['Alert_failure'],'alert-create-reason');
                    }
                },
                beforeSend: function(){
                    $("#btnCreateReason").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                },
                complete: function(){
                    $("#btnCreateReason").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                }
            });
        }
        
    });

    $("#btnUpdateReason").click(function(){

        if (!$("#update-reason-form").valid()) {
            return false ;
        }

        if(!$("#btnUpdateReason").hasClass('disabled')){        
                $.ajax({     
                type: "POST",
                url: path + '/helpdezk/hdkReason/updateReason',
                dataType: 'json',
                data: $("#update-reason-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',vocab['Edit_failure'],'alert-update-reason');
                },
                success: function(ret){    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));    
                    if(obj.success) {
                        showAlert(vocab['Edit_sucess'],'success');
                    } else {
                        modalAlertMultiple('danger',vocab['Edit_failure'],'alert-update-reason');
                    }
                },
                beforeSend: function(){
                    $("#btnUpdateReason").html("<i class='fa fa-spinner fa-spin'></i> "+ vocab['Processing']).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnUpdateReason").html("<i class='fa fa-save'></i> "+ vocab['Save']).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }    
            });
        }

    });

    /*
     * Validate
     */
    $("#create-reason-form").validate({
        ignore:[],
        rules: {
            reason:{
                required:true,
                minlength:5,
                remote:{
                    url: path+'/helpdezk/hdkReason/checkExist',
                    type: 'post',
                    dataType:'json',
                    async: false,
                    data:{  
                        _token:function(element){return $("#_token").val()},
                        idservice:function(element){return $("#cmbService").val()},
                    }
                }
            },
            cmbArea:{
                required:true,
            },
            cmbType:{
                required:true,
            },
            cmbItem:{
                required:true,
            },
            cmbService:{
                required:true,
            }
        },
        messages: {            
            reason:{required:vocab['Alert_field_required'], minlength:vocab['Alert_minimum_five_characters']},
            cmbArea:{required:vocab['Alert_field_required']},
            cmbType:{required:vocab['Alert_field_required']},
            cmbItem:{required:vocab['Alert_field_required']},
            cmbService:{required:vocab['Alert_field_required']}
        }
    });

    $("#update-reason-form").validate({
        ignore:[],
        rules: {
            reason:{
                required:true,
                minlength:5,
                remote:{
                    url: path+'/helpdezk/hdkReason/checkExist',
                    type: 'post',
                    dataType:'json',
                    async: false,
                    data:{  
                        _token:function(element){return $("#_token").val()},
                        reasonID:function(element){return $("#reasonID").val()},
                        idservice:function(element){return $("#cmbService").val()}
                    }
                }
            },
            cmbArea:{
                required:true,
            },
            cmbType:{
                required:true,
            },
            cmbItem:{
                required:true,
            },
            cmbService:{
                required:true,
            }
        },
        messages: {            
            reason:{required:vocab['Alert_field_required'], minlength:vocab['Alert_minimum_five_characters']},
            cmbArea:{required:vocab['Alert_field_required']},
            cmbType:{required:vocab['Alert_field_required']},
            cmbItem:{required:vocab['Alert_field_required']},
            cmbService:{required:vocab['Alert_field_required']}
        }
    });

    /* when the modal is hidden */
    $('#modal-reason-create').on('hidden.bs.modal', function() { 
        location.href = path + "/helpdezk/hdkReason/index";        
    });

    if($("#update-reason-form").length > 0){
        $('#modal-alert').on('hidden.bs.modal', function() { 
            location.href = path + "/helpdezk/hdkReason/index" ;        
        });
    }
})
