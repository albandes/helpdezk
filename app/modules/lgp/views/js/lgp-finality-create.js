var global_newsID = '', dropzonefiles = 0,filesended = 0, flgerror = 0, errorname=[];

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
     *  Chosen
     */
    $("#cmbUF").chosen({width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});

    /*
     * Datepicker
     */
    $('.input-group.date').datepicker(datepickerOpts);

    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/lgp/lgpFinality/index');
    

    $("#btnSave").click(function(){
        if (!$("#create-finality-form").valid()) {
            return false ;
        }
        if(!$("#btnSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lgp/lgpFinality/createFinality',
                dataType: 'json',
                data: $("#create-finality-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-finality-create');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Alert_inserted'),'success',path + '/lgp/lgpFinality/index');                        
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-finality-create');
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
        if (!$("#update-finality-form").valid()) {
            return false ;
        }

        if(!$("#btnSaveUpdate").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lgp/lgpFinality/updateFinality',
                dataType: 'json',
                data: $("#update-finality-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-finality-update');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Edit_sucess'),'success',path + '/lgp/lgpFinality/index');
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-finality-update');
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
    $("#create-finality-form").validate({
        ignore:[],
        rules: {
            finalityName:  {
                required:true,
                remote:{
                    url: path+"/lgp/lgpFinality/existFinality",
                    type: 'post',
                    dataType:'json',
                    async: false,
                }
            }
        },
        messages: {
            finalityName: {required:makeSmartyLabel('Alert_field_required')}
        }
    });

    $("#update-finality-form").validate({
        ignore:[],
        rules: {
            finalityName:  {
                required:true,
                remote:{
                    url: path+"/lgp/lgpFinality/existFinality",
                    type: 'post',
                    dataType:'json',
                    async: false,
                }
            }
        },
        messages: {
            finalityName: {required:makeSmartyLabel('Alert_field_required')}
        }
    });
});

/*function showAlert(msg,typeAlert,btnOk)
{
    $('#modal-notification').html(msg);
    $("#btn-modal-ok").attr("href", btnOk);
    $("#tipo-alert").attr('class', 'alert alert-'+typeAlert);
    $('#modal-alert').modal('show');

    return false;
}*/


