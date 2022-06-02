
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
     *  Chosen
     */
    $("#cmbGroupCompany").chosen({width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});

    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/lgp/lgpGroup/index');

    $("#btnSave").click(function(){
        if (!$("#create-group-form").valid()) {
            return false ;
        }

        if(!$("#btnSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lgp/lgpGroup/createGroup',
                dataType: 'json',
                data: $("#create-group-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-group-create');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Alert_inserted'),'success',path + '/lgp/lgpGroup/index');
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-group-create');
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
        if (!$("#update-group-form").valid()) {
            return false ;
        }

        if(!$("#btnSaveUpdate").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lgp/lgpGroup/updateGroup',
                dataType: 'json',
                data: $("#update-group-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-group-update');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Edit_sucess'),'success',path + '/lgp/lgpGroup/index');
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-group-update');
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
    $("#create-group-form").validate({
        ignore:[],
        rules: {
            groupName: {
                required:true,
                minlength: 3,
                remote:{
                    url: path+"/lgp/lgpGroup/existGroup",
                    type: 'post',
                    dataType:'json',
                    async: false

                }
            },
            cmbGroupCompany: "required",
        },
        messages: {
            groupName: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_word_min_letters')},
            cmbGroupCompany: {required:makeSmartyLabel('Alert_field_required')},
        }
    });

    $("#update-group-form").validate({
        ignore:[],
        rules: {
            groupName: {
                required:true,
                minlength: 3,
                remote:{
                    url: path+"/lgp/lgpGroup/existGroup",
                    data: 
                    {groupID: function(element){return $("#groupID").val()}},
                    type: 'post',
                    dataType:'json',
                    async: false

                }
            },
            cmbGroupCompany: "required",
        },
        messages: {
            groupName: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_word_min_letters')},
            cmbGroupCompany: {required:makeSmartyLabel('Alert_field_required')},
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


