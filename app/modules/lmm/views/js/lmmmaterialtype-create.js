$(document).ready(function () {

    countdown.start(timesession);

    /*
     * Demo version
     */
    if (demoVersion == 1){
        if ($('#logindemo').val() == 'user' || $('#logindemo').val() == 'operator' ) {
            $('#btnUpdateMaterialtype').prop('disabled', true);
        }
    }

    new gnMenu( document.getElementById( 'gn-menu' ) );

    if(access[1] != "Y"){
        $("#btnCreateRequest").addClass('hide');
    }

    if(access[2] != "Y"){
        $("#btnUpdateRequest").addClass('hide');
    }

    $('.i-checks').iCheck({
      checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
   });


    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/lmm/lmmMaterialType/index');

    $("#btnSave").click(function(){
        if (!$("#create-materialtype-form").valid()) {
            return false ;
        }

        if(!$("#btnSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lmm/lmmMaterialType/createMaterialtype',
                dataType: 'json',
                data: $("#create-materialtype-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-materialtype-create');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Alert_inserted'),'success',path + '/lmm/lmmMaterialType/index');                        
                    }else{
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-materialtype-create');
                    }
                },
                beforeSend: function(){
                    $("#btnSave").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnSave").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
            });
        }
        
    });

    $("#btnSaveUpdate").click(function(){
        if (!$("#update-materialtype-form").valid()) {
            return false ;
        }

        if(!$("#btnSaveUpdate").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lmm/lmmMaterialType/updateMaterialtype',
                dataType: 'json',
                data: $("#update-materialtype-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-materialtype-update');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Edit_sucess'),'success',path + '/lmm/lmmMaterialType/index');
                    }else{
                        modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-materialtype-update');
                    }
                },
                beforeSend: function(){
                    $("#btnSaveUpdate").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnSaveUpdate").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
            });
        }       
    });

    /*
     * Validate
     */
    $("#create-materialtype-form").validate({
        ignore:[],
        rules: {
            nome: {
                required:true,
                minlength: 5,
                remote:{
                    url: path+'/lmm/lmmMaterialType/existMaterialtype',
                    type: 'post',
                    dataType:'json',
                    async: false
                }

            },
            perioddays:{
                required:true,
                checkprazo:true
        
            },
            dest_f:{
                required:true,
            }
        },
        messages: {
            nome: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minimum_five_characters')},
            perioddays: {required:makeSmartyLabel('Alert_field_required')},
            dest_f: {required:makeSmartyLabel('Alert_field_required')},
            
        }
    });

    $("#update-materialtype-form").validate({
        ignore:[],
        rules: {
            nome: {
                required:true,
                minlength: 5,
                remote:{
                    url: path+'/lmm/lmmMaterialType/existMaterialtype',
                    type: 'post',
                    dataType:'json',
                    async: false
                }

            },
            
            perioddays:{
                required:true,
                checkprazo:true
        
            },
            dest_f:{
                required:true,
            }
        },
        messages: {
            nome: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minimum_five_characters')},
            perioddays: {required:makeSmartyLabel('Alert_field_required')},
            dest_f: {required:makeSmartyLabel('Alert_field_required')},
          
        }
    });

});


