
$(document).ready(function () {

    countdown.start(timesession);

    /*
     * Demo version
     */
    if (demoVersion == 1){
        if ($('#logindemo').val() == 'user' || $('#logindemo').val() == 'operator' ) {
            $('#btnUpdatePerson').prop('disabled', true);
        }
    }

    new gnMenu( document.getElementById( 'gn-menu' ) );

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });        

    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/lgp/lgpPersonAccess/index');

    $("#btnSave").click(function(){
        if (!$("#create-personac-form").valid()) {
            return false ;
        }

        if(!$("#btnSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lgp/lgpPersonAccess/createPersonac',
                dataType: 'json',
                data: $("#create-personac-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-personac-create');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Alert_inserted'),'success',path + '/lgp/lgpPersonAccess/index');
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-personac-create');
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
        if (!$("#update-personac-form").valid()) {
            return false ;
        }

        if(!$("#btnSaveUpdate").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lgp/lgpPersonAccess/updatePersonac',
                dataType: 'json',
                data: $("#update-personac-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-personac-update');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Edit_sucess'),'success',path + '/lgp/lgpPersonAccess/index');
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-personac-update');
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
     * Mask
     */
    $('#personacCPF').mask('000.000.000-00');
    $('#personacTelephone').mask('(00) 0000-0000');
    $('#personacCPhone').mask('(00) 00000-0000');

    /*
     * Validate
     */
    $("#create-personac-form").validate({
        ignore:[],
        rules: {
            personacName: {
                required:true,
                minlength: 3,
                remote:{
                    url: path+"/lgp/lgpPersonAccess/existPersonac",
                    type: 'post',
                    dataType:'json',
                    async: false,
                    data:{
                        cpf:function(element){return $("#personacCPF").val()}
                    }
                }
            },
            personacCPF: {
                remote:{
                    param:{
                        url: path+"/lgp/lgpPersonAccess/checkCPF",
                        type: 'post',
                        dataType:'json',
                        async: false
                    },
                    depends:{
                        function(element){return $("#personacCPF").val() != ''}
                    }
                    
                }
            },
            personacTelephone: {
                remote:{
                    param:{
                        url: path+"/lgp/lgpPersonAccess/checkPhones",
                        type: 'post',
                        dataType:'json',
                        async: false
                    },
                    depends:{ //Se o valor for diferente de vazio
                        function(element){return $("#personacTelephone").val() != ''}
                    }
                    
                }
            },
            personacCPhone: {
                remote:{
                    param:{
                        url: path+"/lgp/lgpPersonAccess/checkPhones",
                        type: 'post',
                        dataType:'json',
                        async: false
                    },
                    depends:{ //Se o valor for diferente de vazio
                        function(element){return $("#personacCPhone").val() != ''}
                    }
                    
                }
            },
        },
        messages: {
            personacName: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_word_min_letters')}
        }
    });

    $("#update-personac-form").validate({
        ignore:[],
        rules: {
            personacName: {
                required:true,
                minlength: 3,
                remote:{
                    url: path+"/lgp/lgpPersonAccess/existPersonac",
                    data:{
                        personacID: function(element){return $("#personacID").val()},
                        cpf:function(element){return $("#personacCPF").val()}
                    },
                    type: 'post',
                    dataType:'json',
                    async: false
                }
            },
            personacCPF: {
                remote:{
                    param:{
                        url: path+"/lgp/lgpPersonAccess/checkCPF",
                        data:{
                            personacID: function(element){return $("#personacID").val()}
                        },
                        type: 'post',
                        dataType:'json',
                        async: false
                    },
                    depends:{
                        function(element){return $("#personacCPF").val() != ''}
                    }
                }
            },
            personacTelephone: {
                remote:{
                    param:{
                        url: path+"/lgp/lgpPersonAccess/checkPhones",
                        type: 'post',
                        dataType:'json',
                        async: false
                    },
                    depends:{ //Se o valor for diferente de vazio
                        function(element){return $("#personacTelephone").val() != ''}
                    }
                    
                }
            },
            personacCPhone: {
                remote:{
                    param:{
                        url: path+"/lgp/lgpPersonAccess/checkPhones",
                        type: 'post',
                        dataType:'json',
                        async: false
                    },
                    depends:{ //Se o valor for diferente de vazio
                        function(element){return $("#personacCPhone").val() != ''}
                    }
                    
                }
            },
        },
        messages: {
            personacName: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_word_min_letters')}
        }
    });

});

function showAlert(msg,typeAlert,btnOk)
{
    $('#modal-notification').html(msg);
    $("#btn-modal-ok").attr("href", btnOk);
    $("#tipo-alert").attr('class', 'alert alert-'+typeAlert);
    $('#modal-alert').modal('show');

    return false;
}


