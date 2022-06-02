$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    /*
     * Mask
     */
    $("#parentCpf").mask('999.999.999-99');

    $("#parentGender").chosen({  width: "100%",    no_results_text: makeSmartyLabel('API_No_result'),    disable_search_threshold: 10});
    $("#idstudent1").chosen({  width: "100%",    no_results_text: makeSmartyLabel('API_No_result'),    disable_search_threshold: 10});
    $("#idkinship1").chosen({  width: "100%",    no_results_text: makeSmartyLabel('API_No_result'),    disable_search_threshold: 10});
    $(".updCmbStudent").chosen({  width: "100%",    no_results_text: makeSmartyLabel('API_No_result'),    disable_search_threshold: 10});
    $(".updCmbKinship").chosen({  width: "100%",    no_results_text: makeSmartyLabel('API_No_result'),    disable_search_threshold: 10});


    // Buttons
    $("#btnCancel").attr("href", path + '/emq/emqParent/index');

    $("#btnCreateParent").click(function(){
        if (!$("#create-parent-form").valid()) {
            return false ;
        }

        var studentId = $("select[name='idstudent[]']"), studentNotEmpty = 0,
            kinshipId = $("select[name='idkinship[]']"), kinshipEmpty = 0;

        studentId.each(function(){
            if(this.value != ''){
                studentNotEmpty = studentNotEmpty + 1;
            }
        });

        if(studentNotEmpty == 0){
            //console.log('student empty');
            modalAlertMultiple('danger',makeSmartyLabel('emq_alert_no_student'),'alert-create-parent');
            return false;
        }

        kinshipId.each(function(){
            if(this.value == ''){
                kinshipEmpty = kinshipEmpty + 1;
            }
        });

        if(kinshipEmpty > 0){
            modalAlertMultiple('danger',makeSmartyLabel('emq_alert_no_kinship'),'alert-create-parent');
            return false;
        }

        $.ajax({
            type: "POST",
            url: path + '/emq/emqParent/createParent',
            dataType: 'json',
            data: $("#create-parent-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger', makeSmartyLabel('Alert_failure'),'alert-create-parent');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if($.isNumeric(obj.idparent)) {
                    showAlert(makeSmartyLabel('Alert_inserted'),'success',path + '/emq/emqParent/index');
                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-parent');
                }
            },
            beforeSend: function(){
                $("#btnCreateParent").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                $("#btnCancel").addClass('disabled');
            },
            complete: function(){
                $("#btnCreateParent").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                $("#btnCancel").removeClass('disabled');
            }
        });
    });

    $("#btnUpdateParent").click(function(){
        if (!$("#update-parent-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/emq/emqParent/updateParent',
            dataType: 'json',
            data: $("#update-parent-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger', makeSmartyLabel('Alert_failure'),'alert-update-parent');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if($.isNumeric(obj.idparent)) {
                    showAlert(makeSmartyLabel('Alert_success_update'),'success',path + '/emq/emqParent/index');
                } else {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-update-parent');
                }
            },
            beforeSend: function(){
                $("#btnUpdateParent").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                $("#btnCancel").addClass('disabled');
            },
            complete: function(){
                $("#btnUpdateParent").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                $("#btnCancel").removeClass('disabled');
            }
        });
    });

    $("#btnAddStudent").click(function(){
        duplicateRow('studentList');
    });

    /*
     * Validate
     */
    $("#create-parent-form").validate({
        ignore:[],
        rules: {
            parentName: "required",
            parentEmail: {email:true}
        },
        messages: {
            parentName: makeSmartyLabel('Alert_field_required'),
            parentEmail: {email:makeSmartyLabel('Alert_invalid_email')}
        }
    });

    $("#update-parent-form").validate({
        ignore:[],
        rules: {
            parentName: "required",
            parentEmail: {email:true}
        },
        messages: {
            parentName: makeSmartyLabel('Alert_field_required'),
            parentEmail: {email:makeSmartyLabel('Alert_invalid_email')}
        }
    });

});

function duplicateRow( strTableName ){
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
    $( "#idstudent"+ intCurrentRowId , clonedRow ).attr( { "id" :"idstudent" + intNewRowId, "accesskey" : intNewRowId  } );
    $( "#idkinship"+ intCurrentRowId , clonedRow ).attr( { "id" :"idkinship" + intNewRowId, "accesskey" : intNewRowId  } );
    $( "#checkEmailSms"+ intCurrentRowId , clonedRow ).attr( { "id" :"checkEmailSms" + intNewRowId, "accesskey" : intNewRowId, "value": "1" } );
    $( "#checkBankTicket"+ intCurrentRowId , clonedRow ).attr( { "id" :"checkBankTicket" + intNewRowId, "accesskey" : intNewRowId , "value": "1"} );
    $( "#checkAccessApp"+ intCurrentRowId , clonedRow ).attr( { "id" :"checkAccessApp" + intNewRowId, "accesskey" : intNewRowId , "value": "1"} );
    $( "#idstudent"+ intCurrentRowId +"_chosen" , clonedRow ).attr( { "id" :"idstudent" + intNewRowId +"_chosen", "accesskey" : intNewRowId} );
    $( "#idkinship"+ intCurrentRowId +"_chosen" , clonedRow ).attr( { "id" :"idkinship" + intNewRowId +"_chosen", "accesskey" : intNewRowId} );

    // Add to the new row to the original table
    $( "#" + strTableName ).append( clonedRow );
    // And finally change the ID of the last row to something we can
    //delete later, not sure why can not be done before the append :S
    $( "#" + strTableName + " tr:last" ).attr( "id", "detailsTr" + intNewRowId );

    $.post(path+"/emq/emqParent/ajaxStudent",
        function(valor) {
            $( "#idstudent"+ intNewRowId +"_chosen").remove();
            $( "#idstudent"+ intNewRowId).html(valor);
            $( "#idstudent"+ intNewRowId).chosen({  width: "100%",    no_results_text: makeSmartyLabel('API_No_result'),    disable_search_threshold: 10});
            return false;
        });

    $.post(path+"/emq/emqParent/ajaxKinship",
        function(valor) {
            $( "#idkinship"+ intNewRowId +"_chosen").remove();
            $( "#idkinship"+ intNewRowId).html(valor);
            $( "#idkinship"+ intNewRowId).chosen({  width: "100%",    no_results_text: makeSmartyLabel('API_No_result'),    disable_search_threshold: 10});
            return false;
        });

    $( "#checkEmailSms"+ intNewRowId).removeAttr('checked');
    $( "#checkBankTicket"+ intNewRowId).removeAttr('checked');
    $( "#checkAccessApp"+ intNewRowId).removeAttr('checked');

    $( "#idstudent"+ intNewRowId ).focus();
}

function removeRow(id,strTableName,ope){
    var i = id.parentNode.parentNode.rowIndex, msgDiv;

    msgDiv = ope == 'upd' ? 'alert-update-parent' : 'alert-create-parent';

    if($("#"+strTableName+" tbody tr").length == 1){
        modalAlertMultiple('info', makeSmartyLabel('Alert_dont_remove_row'),msgDiv);
    }else{
        document.getElementById(strTableName).deleteRow(i);
    }
}

function showAlert(msg,typeAlert,btnOk)
{
    $('#modal-notification').html(msg);
    $("#btn-modal-ok").attr("href", btnOk);
    $("#tipo-alert").attr('class', 'alert alert-'+typeAlert);
    $('#modal-alert').modal('show');

    return false;
}

function fontColorFormat(cellvalue, options, rowObject) {
    var color = "blue";
    var cellHtml = "<span style='color:" + color + "' originalValue='" + cellvalue + "'>" + cellvalue + "</span>";
    return cellHtml;
}