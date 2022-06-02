
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
    //$("#cmbAdvisor").chosen({width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});

    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/acd/acdKnowledgeArea/index');

    $("#btnSave").click(function(){
        if (!$("#create-area-form").valid()) {
            return false ;
        }

        if(!$("#btnSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/acd/acdKnowledgeArea/createKnowledgearea',
                dataType: 'json',
                data: $("#create-area-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-area-create');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Alert_inserted'),'success',path + '/acd/acdKnowledgeArea/index');
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-area-create');
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
        if (!$("#update-area-form").valid()) {
            return false ;
        }

        if(!$("#btnSaveUpdate").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/acd/acdKnowledgeArea/updateKnowledgearea',
                dataType: 'json',
                data: $("#update-area-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-area-update');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Edit_sucess'),'success',path + '/acd/acdKnowledgeArea/index');
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-area-update');
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
    $("#create-area-form").validate({
        ignore:[],
        rules: {
            areaDesc: {
                required:true,
                minlength: 3,
                remote:{
                    url: path+"/acd/acdKnowledgeArea/existArea",
                    type: 'post',
                    dataType:'json',
                    async: false,
                }
            },
            areaDescAbrev: {
                required:true,
                minlength: 2,
                remote:{
                    url: path+"/acd/acdKnowledgeArea/existArea",
                    type: 'post',
                    dataType:'json',
                    async: false,
                }
            },
        },
        messages: {
            areaDesc: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_word_min_letters')}, 
            areaDescAbrev: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_abbrev_min_letters')}
        }
    });

    $("#update-area-form").validate({
        ignore:[],
        rules: {
            areaDesc: {
                required:true,
                minlength: 3,
                remote:{
                    url: path+"/acd/acdKnowledgeArea/existArea",
                    type: 'post',
                    dataType:'json',
                    async: false,
                    data:{areaID:$("#areaID").val()}
                }
            },
            areaDescAbrev: {
                required:true,
                minlength: 2,
                remote:{
                    url: path+"/acd/acdKnowledgeArea/existArea",
                    type: 'post',
                    dataType:'json',
                    async: false,
                    data:{areaID:$("#areaID").val()}
                }
            },
        },
        messages: {
            areaDesc: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_word_min_letters')}, 
            areaDescAbrev: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_abbrev_min_letters')}, 
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


