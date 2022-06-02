$(document).ready(function () {

    countdown.start(timesession);

    /*
     * Demo version
     */
    if (demoVersion == 1){
        if ($('#logindemo').val() == 'user' || $('#logindemo').val() == 'operator' ) {
            $('#btnUpdateOrigin').prop('disabled', true);
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
    $("#btnCancel").attr("href", path + '/lmm/lmmLibrary/index');

    $("#btnSave").click(function(){
        if (!$("#create-library-form").valid()) {
            return false ;
        }

        if(!$("#btnSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lmm/lmmLibrary/createLibrary',
                dataType: 'json',
                data: $("#create-library-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-library-create');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Alert_inserted'),'success',path + '/lmm/lmmLibrary/index');                        
                    }else{
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-library-create');
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
        if (!$("#update-library-form").valid()) {
            return false ;
        }

        if(!$("#btnSaveUpdate").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lmm/lmmLibrary/updateLibrary',
                dataType: 'json',
                data: $("#update-library-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-library-update');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success){
                        if(obj.message==""){
                            showAlert(makeSmartyLabel('Edit_sucess'),'success',path + '/lmm/lmmLibrary/index');                            
                        }else{
                            showAlert(obj.message,'success',path + '/lmm/lmmLibrary/index');                                                    
                        }
                    }else{
                        modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-library-update');
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
    $("#create-library-form").validate({
        ignore:[],
        rules: {
            library: {
                required:true,
                minlength: 5,
                remote:{
                    url: path+'/lmm/lmmLibrary/existLibrary',
                    type: 'post',
                    dataType:'json',
                    async: false
                }
            }
    
            
        },
        messages: {
            library: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minimum_five_characters')},
            
        
        }
    });

    $("#update-library-form").validate({
        ignore:[],
        rules: {
            library: {
                required:true,
                minlength: 5,
                remote:{
                    url: path+'/lmm/lmmLibrary/existLibraryUp',
                    type: 'post',
                    dataType:'json',
                    async: false,
                    data:{                       
                        idlibrary:function(element){return $("#idlibrary").val()}
                    }
                }

            }
            
    
        },
        messages: {
            library: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minimum_five_characters')},
        
          
        }
    });

});


