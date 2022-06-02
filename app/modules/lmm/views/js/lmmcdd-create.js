$(document).ready(function () {

    countdown.start(timesession);

    /*
     * Demo version
     */
    if (demoVersion == 1){
        if ($('#logindemo').val() == 'user' || $('#logindemo').val() == 'operator' ) {
            $('#btnUpdateCDD').prop('disabled', true);
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
    $("#btnCancel").attr("href", path + '/lmm/lmmCDD/index');

    $("#btnSave").click(function(){
        if (!$("#create-cdd-form").valid()) {
            return false ;
        }

        if(!$("#btnSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lmm/lmmCDD/createCDD',
                dataType: 'json',
                data: $("#create-cdd-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-cdd-create');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Alert_inserted'),'success',path + '/lmm/lmmCDD/index');                        
                    }else{
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-cdd-create');
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
        if (!$("#update-cdd-form").valid()) {
            return false ;
        }

        if(!$("#btnSaveUpdate").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lmm/lmmCDD/updateCDD',
                dataType: 'json',
                data: $("#update-cdd-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-cdd-update');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Edit_sucess'),'success',path + '/lmm/lmmCDD/index');
                    }else{
                        modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-cdd-update');
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
    $("#create-cdd-form").validate({
        ignore:[],
        rules: {
            code: {
                required:true,
                minlength: 3,
                remote:{
                    url: path+'/lmm/lmmCDD/existCodeCDD',
                    type: 'post',
                    dataType:'json',
                    async: false
                }

            },

            descr: {
                required:true,
                minlength: 3,
                remote:{
                    url: path+'/lmm/lmmCDD/existDescrCDD',
                    type: 'post',
                    dataType:'json',
                    async: false,
                    data:{
                        code:function(element){return $("#code").val()}
                    }
                }
            }
            
        },
        messages: {
            code: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minimum_three_characters')},
            descr: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minimum_three_characters')},
          
        }
    });

    $("#update-cdd-form").validate({
        ignore:[],
        rules: {
            code: {
                required:true,
                minlength: 3,
                remote:{
                    url: path+'/lmm/lmmCDD/existCodeCDD',
                    type: 'post',
                    dataType:'json',
                    async: false,
                    data:{                       
                        idcdd:function(element){return $("#idcdd").val()}
                    }
                }

            },
            
            descr: {
                required:true,
                minlength: 3,
                remote:{
                    url: path+'/lmm/lmmCDD/existDescrCDD',
                    type: 'post',
                    dataType:'json',
                    async: false,

                    data:{
                        code:function(element){return $("#code").val()},
                        idcdd:function(element){return $("#idcdd").val()}
                    }
                } 
            }             
            
        },
        messages: {
            code: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minimum_three_characters')},
            descr: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_minimum_three_characters')},
          
        }
    });

});



