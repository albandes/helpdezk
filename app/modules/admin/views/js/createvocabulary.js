var global_idperson = '';
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
        $("#btnCreateVocabulary").addClass('hide');
    }

    if(access[2] != "Y"){
        $("#btnUpdateVocabulary").addClass('hide');
    }

    /*
     *  Chosen
     */
    $("#cmbLocale").chosen({    width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#cmbModule").chosen({    width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});

    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/admin/vocabulary/index');

    $("#btnCreateVocabulary").click(function(){

        if (!$("#create-vocabulary-form").valid()) {
            return false ;
        }

        if(!$("#btnCreateVocabulary").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/vocabulary/createVocabulary',
                dataType: 'json',
                data: $("#create-vocabulary-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-vocabulary');
                },
                success: function(ret){
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.status) {
                        showAlert(makeSmartyLabel('Alert_inserted'),'success',path + '/admin/vocabulary/index');
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-vocabulary');
                    }
                },
                beforeSend: function(){
                    $("#btnCreateVocabulary").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnCreateVocabulary").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
            });
        }


    });

    $("#btnUpdateVocabulary").click(function(){

        if (!$("#update-vocabulary-form").valid()) {
            return false ;
        }

        if(!$("#btnUpdateVocabulary").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/admin/vocabulary/updateVocabulary',
                dataType: 'json',
                data: $("#update-vocabulary-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-update-vocabulary');
                },
                success: function(ret){

                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.status) {
                        showAlert(makeSmartyLabel('Edit_sucess'),'success',path + '/admin/vocabulary/index');
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-update-vocabulary');
                    }

                },
                beforeSend: function(){
                    $("#btnUpdateVocabulary").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnUpdateVocabulary").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
            });
        }
    });

    /*
     * Validate
     */
    $("#create-vocabulary-form").validate({
        ignore:[],
        rules: {
            cmbLocale:  "required",
            cmbModule:  "required",
            keyName: {
                required:true,
                remote:{
                    url: path+"/admin/vocabulary/checkKeyName",
                    type: 'post',
                    data: {
                        _token: function() {return $('#_token').val();},
                        localeID:  function() {return $('#cmbLocale').val();}
                    }
                },
                noAccent:true
            },
            keyValue:   "required"
        },
        messages: {
            cmbLocale:  makeSmartyLabel('Alert_field_required'),
            cmbModule:  makeSmartyLabel('Alert_field_required'),
            keyName:    {required:makeSmartyLabel('Alert_field_required')},
            keyValue:   makeSmartyLabel('Alert_field_required')
        }
    });

    $("#update-vocabulary-form").validate({
        ignore:[],
        rules: {
            cmbLocale: "required",
            cmbModule: "required",
            keyName: {
                required:true,
                remote:{
                    url: path+"/admin/vocabulary/checkKeyName",
                    type: 'post',
                    data: {
                        _token: function() {return $('#_token').val();},
                        vocabularyID: function() {return $('#idvocabulary').val();},
                        localeID: function() {return $('#cmbLocale').val();}
                    }
                },
                noAccent:true
            },
            keyValue: "required"
        },
        messages: {
            cmbLocale:  makeSmartyLabel('Alert_field_required'),
            cmbModule:  makeSmartyLabel('Alert_field_required'),
            keyName:    {required:makeSmartyLabel('Alert_field_required')},
            keyValue:   makeSmartyLabel('Alert_field_required')
        }
    });

    $.validator.addMethod('noAccent', function(strValue) {
        var re = new RegExp("^[a-zA-Z0-9_]+$","i");
        return re.test(strValue);
    }, makeSmartyLabel('key_no_accents_no_whitespace'));

    // tooltips
    $('.tooltip-buttons').tooltip();

});

function showAlert(msg,typeAlert,btnOk)
{
    $('#modal-notification').html(msg);
    $("#btn-modal-ok").attr("href", btnOk);
    $("#tipo-alert").attr('class', 'alert alert-'+typeAlert);
    $('#modal-alert').modal('show');

    return false;
}
