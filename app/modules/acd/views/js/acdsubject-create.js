
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
    $("#cmbSubArea").chosen({width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});

    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/acd/acdSubject/index');

    $("#btnSave").click(function(){
        if (!$("#create-subject-form").valid()) {
            return false ;
        }

        if(!$("#btnSave").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/acd/acdSubject/createSubject',
                dataType: 'json',
                data: $("#create-subject-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-subject-create');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Alert_inserted'),'success',path + '/acd/acdSubject/index');
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-subject-create');
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
        if (!$("#update-subject-form").valid()) {
            return false ;
        }

        if(!$("#btnSaveUpdate").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/acd/acdSubject/updateSubject',
                dataType: 'json',
                data: $("#update-subject-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-subject-update');
                },
                success: function(ret){
    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Edit_sucess'),'success',path + '/acd/acdSubject/index');
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-subject-update');
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
    $("#create-subject-form").validate({
        ignore:[],
        rules: {
            subjectName: {
                required:true,
                minlength: 3,
                remote:{
                    url: path+"/acd/acdSubject/existSubject",
                    type: 'post',
                    dataType:'json',
                    async: false,
                }
            },
            subjectAbrev: {
                required:true,
                minlength: 2,
                remote:{
                    url: path+"/acd/acdSubject/existSubject",
                    type: 'post',
                    dataType:'json',
                    async: false,
                }
            },
            cmbSubArea: "required",
        },
        messages: {
            subjectName: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_word_min_letters')},
            subjectAbrev: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_abbrev_min_letters')}, 
            cmbSubArea: {required:makeSmartyLabel('Alert_field_required')},
        }
    });

    $("#update-subject-form").validate({
        ignore:[],
        rules: {
            subjectName: {
                required:true,
                minlength: 3,
                remote:{
                    url: path+"/acd/acdSubject/existSubject",
                    type: 'post',
                    dataType:'json',
                    async: false,
                    data:{subID:$("#subID").val()},
                }
            },
            subjectAbrev: {
                required:true,
                minlength: 2,
                remote:{
                    url: path+"/acd/acdSubject/existSubject",
                    type: 'post',
                    data: {subID: $("#subID").val()},
                    dataType:'json',
                    async: false,
                }
            },
            cmbSubArea: "required",
        },
        messages: {
            subjectName: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_word_min_letters')}, 
            subjectAbrev: {required:makeSmartyLabel('Alert_field_required'), minlength:makeSmartyLabel('Alert_abbrev_min_letters')},
            cmbSubArea: {required:makeSmartyLabel('Alert_field_required')},
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


