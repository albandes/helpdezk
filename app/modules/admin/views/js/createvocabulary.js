var global_idperson = '';
/*
 * Combos
 */
var objCbmData = {
    loadLocale: function(id) {
        $.post(path+"/admin/vocabulary/ajaxLocale",
            function(res) {
                $("#localeID_"+id).html(res);
                $("#localeID_"+id).chosen({    width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
                return false;
            })
        return false ;
    }

}

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

    if($("#typeAction").val() != 'upd')
        objCbmData.loadLocale(1);

    /*
     *  Chosen
     */
    $("#cmbModule").chosen({    width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    if($("#typeAction").val() == 'upd')
        $(".cmbLocale").chosen({    width: "100%",    no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});

    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/admin/vocabulary/index');

    $("#btnCreateVocabulary").click(function(){

        if (!$("#create-vocabulary-form").valid()) {
            return false ;
        }

        var flgNotSelect = false, flgEmpty = false;
        $("select[name='localeID[]']").each(function(){
            var lineID = $(this).attr('id');
            lineID = lineID.split('_');
            if($(this).val() == ''){
                flgNotSelect = true;
            }else{
                $.post(path+"/admin/vocabulary/checkKeyName",
                    {
                        '_token' :$("#_token").val(),
                        'keyName':$("#keyName").val(),
                        'localeID':$(this).val(),
                        'localName': $("#localeID_"+ lineID[1] +" option:selected").text()
                    },
                    function(res) {
                        var obj = jQuery.parseJSON(JSON.stringify(res));
                        if(!obj.status) {
                            modalAlertMultiple('danger',obj.message,'alert-create-vocabulary');
                            return false;
                        }
                    },
                    'json'
                )
            }
        });

        if(flgNotSelect){
            modalAlertMultiple('danger',makeSmartyLabel('one_more_not_select_locale'),'alert-create-vocabulary');
            return false ;
        }

        $("input[name='keyValue[]']").each(function(){
            if(($(this).val() == '' || $(this).val() == ' ')){
                flgEmpty = true;
            }
        });

        if(flgEmpty){
            modalAlertMultiple('danger',makeSmartyLabel('one_more_no_key_value'),'alert-create-vocabulary');
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

        var flgNotSelect = false, flgEmpty = false;
        $("select[name='localeID[]']").each(function(){
            var lineID = $(this).attr('id');
            lineID = lineID.split('_');
            if($(this).val() == ''){
                flgNotSelect = true;
            }else{
                $.post(path+"/admin/vocabulary/checkKeyName",
                    {
                        '_token' :$("#_token").val(),
                        'keyName':$("#keyName").val(),
                        'vocabularyID':$("#vocabularyID_" + lineID[1]).val(),
                        'localeID':$(this).val(),
                        'localName': $("#localeID_"+ lineID[1] +" option:selected").text()
                    },
                    function(res) {
                        var obj = jQuery.parseJSON(JSON.stringify(res));
                        if(!obj.status) {
                            modalAlertMultiple('danger',obj.message,'alert-update-vocabulary');
                            return false;
                        }
                    },
                    'json'
                )
            }
        });

        if(flgNotSelect){
            modalAlertMultiple('danger',makeSmartyLabel('one_more_not_select_locale'),'alert-update-vocabulary');
            return false ;
        }

        $("input[name='keyValue[]']").each(function(){
            if($(this).val() == '' || $(this).val() == ' '){
                flgEmpty = true;
            }
        });

        if(flgEmpty){
            modalAlertMultiple('danger',makeSmartyLabel('one_more_no_key_value'),'alert-update-vocabulary');
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

    $("#btnAddKeyValue").click(function(){
        duplicateRow('localeTab',$('#typeAction').val());
    });

    /*
     * Validate
     */
    $("#create-vocabulary-form").validate({
        ignore:[],
        rules: {
            cmbModule:  "required",
            keyName: {
                required:true,
                noAccent:true
            }
        },
        messages: {
            cmbModule:  makeSmartyLabel('Alert_field_required'),
            keyName:    {required:makeSmartyLabel('Alert_field_required')}
        }
    });

    $("#update-vocabulary-form").validate({
        ignore:[],
        rules: {
            cmbModule: "required",
            keyName: {
                required:true,
                noAccent:true
            }
        },
        messages: {
            cmbModule:  makeSmartyLabel('Alert_field_required'),
            keyName:    {required:makeSmartyLabel('Alert_field_required')}
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

function duplicateRow(strTableName,ope){
    // First, lets create the new row using the last one as template...
    var clonedRow = $( "#" + strTableName + " tr:last" ).clone();
    // Take the current identifier, some number in the first cell of the row
    intCurrentRowId = parseInt( $( "#numId:last", clonedRow ).val() );
    // Set the new ID
    intNewRowId = intCurrentRowId + 1;
    // Change the current identifier of the row to the new one
    $( "#numId:last", clonedRow ).val( intNewRowId );

    // Change the Id / Name or anything you want for the new attribs...
    //here is where you need to add a lot of stuff to change the id of your variables

    // The following code works without problems on Firefox or IE7
    $( "#localeID_"+ intCurrentRowId , clonedRow ).attr( { "id" :"localeID_" + intNewRowId, "accesskey" : intNewRowId } );
    $( "#keyValue_"+ intCurrentRowId , clonedRow ).attr( { "id" :"keyValue_" + intNewRowId, "accesskey" : intNewRowId} );
    $( "#localeID_"+ intCurrentRowId + "_chosen" , clonedRow ).attr( { "id" :"localeID_" + intNewRowId + "_chosen", "accesskey" : intNewRowId } );
    if(ope == 'upd')
        $( "#vocabularyID_"+ intCurrentRowId , clonedRow ).attr( { "id" :"vocabularyID_" + intNewRowId, "accesskey" : intNewRowId, "value" : "0" } );

    // Add to the new row to the original table
    $( "#" + strTableName ).append( clonedRow );

    $( "#keyValue_"+ intNewRowId).val('');
    $( "#localeID_"+ intNewRowId + "_chosen").remove();
    objCbmData.loadLocale(intNewRowId);

    // And finally change the ID of the last row to something we can
    //delete later, not sure why can not be done before the append :S
    $( "#" + strTableName + " tr:last" ).attr( "id", "detailsTr" + intNewRowId );


    $( "#localeID_"+ intNewRowId ).focus();
}

function removeRow(id,strTableName,ope){
    var i = id.parentNode.parentNode.rowIndex, msgDiv;

    msgDiv = ope == 'upd' ? 'alert-update-vocabulary' : 'alert-create-vocabulary';

    if($("#"+strTableName+" tbody tr").length == 1){
        modalAlertMultiple('warning', makeSmartyLabel('Alert_dont_remove_row'),msgDiv);
    }else{
        document.getElementById(strTableName).deleteRow(i);
    }
}
