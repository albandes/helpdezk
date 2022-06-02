$(document).ready(function () {

    countdown.start(timesession);

    /*
     * Demo version
     */
    if (demoVersion == 1){
        if ($('#logindemo').val() == 'user' || $('#logindemo').val() == 'operator' ) {
            $('#btnUpdateCollection').prop('disabled', true);
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
    $("#btnCancel").attr("href", path + '/lmm/lmmCollection/index');

    $("#btnSave").click(function(){
        if (!$("#create-collection-form").valid()) {
            return false ;
        }

        if(!$("#btnSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lmm/lmmCollection/createCollection',
                dataType: 'json',
                data: $("#create-collection-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-colletion-create');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Alert_inserted'),'success',path + '/lmm/lmmCollection/index');                        
                    }else{
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-collection-create');
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
        if (!$("#update-collection-form").valid()) {
            return false ;
        }

        if(!$("#btnSaveUpdate").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lmm/lmmCollection/updateCollection',
                dataType: 'json',
                data: $("#update-collection-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-collection-update');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Edit_sucess'),'success',path + '/lmm/lmmCollection/index');
                    }else{
                        modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-collection-update');
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
    $("#create-collection-form").validate({
        ignore:[],
        rules: {
            collection: {
                required:true,
                minlength: 5,
                remote:{
                    url: path+'/lmm/lmmCollection/existCollection',
                    type: 'post',
                    dataType:'json',
                    async: false
                }
            }
    
            
        },
        messages: {
            collection: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minimum_five_characters')},
            
        
        }
    });

    $("#update-collection-form").validate({
        ignore:[],
        rules: {
            collection: {
                required:true,
                minlength: 5,
                remote:{
                    url: path+'/lmm/lmmCollection/existCollection',
                    type: 'post',
                    dataType:'json',
                    async: false
                }

            }
            
    
        },
        messages: {
            collection: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minimum_five_characters')},
            
          
        }
    });

});


