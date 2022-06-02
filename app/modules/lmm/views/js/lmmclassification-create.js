$(document).ready(function () {

    countdown.start(timesession);

    /*
     * Demo version
     */
    if (demoVersion == 1){
        if ($('#logindemo').val() == 'user' || $('#logindemo').val() == 'operator' ) {
            $('#btnUpdateClassification').prop('disabled', true);
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
    $("#btnCancel").attr("href", path + '/lmm/lmmClassification/index');

    $("#btnSave").click(function(){
        if (!$("#create-classification-form").valid()) {
            return false ;
        }

        if(!$("#btnSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lmm/lmmClassification/createClassification',
                dataType: 'json',
                data: $("#create-classification-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-classification-create');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Alert_inserted'),'success',path + '/lmm/lmmClassification/index');                        
                    }else{
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-classification-create');
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
        if (!$("#update-classification-form").valid()) {
            return false ;
        }

        if(!$("#btnSaveUpdate").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lmm/lmmClassification/updateClassification',
                dataType: 'json',
                data: $("#update-classification-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-classification-update');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success){
                        if(obj.message==""){
                            showAlert(makeSmartyLabel('Edit_sucess'),'success',path + '/lmm/lmmClassification/index');
                        }else{
                            showAlert(obj.message,'success',path + '/lmm/lmmClassification/index');
                        }
                    }else{
                        modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-classification-update');
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
    $("#create-classification-form").validate({
        ignore:[],
        rules: {
            classification: {
                required:true,
                minlength: 3,
                remote:{
                    url: path+'/lmm/lmmClassification/existClassification',
                    type: 'post',
                    dataType:'json',
                    async: false
                   
                }
            }
    
            
        },
        messages: {
            classification: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minimum_three_characters')},
            
        
        }
    });

    $("#update-classification-form").validate({
        ignore:[],
        rules: {
            classification: {
                required:true,
                minlength: 3,
                remote:{
                    url: path+'/lmm/lmmClassification/existClassificationUp',
                    type: 'post',
                    dataType:'json',
                    async: false,
                    data:{                       
                        idclassification:function(element){return $("#idclassification").val()}
                    }
                }

            }            
    
        },
        messages: {
            classification: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minimum_three_characters')},
            
          
        }
    });

});


