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
    $("#btnCancel").attr("href", path + '/lgp/lgpStorage/index');
	

    $("#btnSave").click(function(){
        if (!$("#create-storage-form").valid()) {
            return false ;
        }
        if(!$("#btnSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lgp/lgpStorage/createStorage',
                dataType: 'json',
                data: $("#create-storage-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-storage-create');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Alert_inserted'),'success',path + '/lgp/lgpStorage/index');                        
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-storage-create');
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
        if (!$("#update-storage-form").valid()) {
            return false ;
        }

        if(!$("#btnSaveUpdate").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lgp/lgpStorage/updateStorage',
                dataType: 'json',
                data: $("#update-storage-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-storage-update');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Edit_sucess'),'success',path + '/lgp/lgpStorage/index');
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-storage-update');
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
  /*  $("#create-storage-form").validate({
        ignore:[],
        rules: {
            //cmbUF: "required",
            tipoName:  "required"
        },
        messages: {
            //cmbUF: {required:makeSmartyLabel('Alert_field_required')},
            tipoName: {required:makeSmartyLabel('Alert_field_required')}
        }
    });

    $("#update-storage-form").validate({
        ignore:[],
        rules: {
            $tipoDefault: "required",
            tipoName:  "required"
        },
        messages: {
            default: {required:makeSmartyLabel('Alert_field_required')},
            tipoName: {required:makeSmartyLabel('Alert_field_required')}
        }
    });*/
	
	/*
     * Validate
     */
    $("#create-storage-form").validate({
        ignore:[],
        rules: {
            storageName:  {
                required:true,
                remote:{
                    url: path+"/lgp/lgpStorage/existStorage",
                    type: 'post',
                    dataType:'json',
                    async: false,
                }
            }
        },
        messages: {
            storageName: {required:makeSmartyLabel('Alert_field_required')}
        }
    });

    $("#update-storage-form").validate({
        ignore:[],
        rules: {
            storageName:  {
                required:true,
                remote:{
                    url: path+"/lgp/lgpStorage/existStorage",
                    type: 'post',
                    dataType:'json',
                    async: false,
                }
            }
        },
        messages: {
            storageName: {required:makeSmartyLabel('Alert_field_required')}
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


