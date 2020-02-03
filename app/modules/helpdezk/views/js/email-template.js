var global_coderequest = '';
$(document).ready(function () {

    countdown.start(timesession);

    new gnMenu( document.getElementById( 'gn-menu' ) );

    $("#btnCancel").attr("href", path + '/helpdezk/hdkEmailConfig/index');

    $('#description').summernote(
        {
            toolbar:[
                ["style",["style"]],
                ["font",["bold","italic","underline","clear"]],
                ["fontname",["fontname"]],["color",["color"]],
                ["para",["ul","ol","paragraph"]],
                ["table",["table"]],
                ["insert",["link"]],
                ["view",["codeview"]],
                ["help",["help"]]
            ],
            disableDragAndDrop: true,
            minHeight: null,  // set minimum height of editor
            maxHeight: 250,   // set maximum height of editor
            height: 250,      // set editor height
            //width: 750,       // set editor width
            focus: false,     // set focus to editable area after initializing summernote
            placeholder:  makeSmartyLabel('Editor_Placeholder_email_body')

        }
    );


    /**
     ** .validate() is what initializes the Validation plugin on your form.
     ** .valid() returns true or false depending on if your form is presently valid.
     **/
    $("#update-emailconf-form").validate({
        ignore:[],
        rules: {
            templateName: "required"  // simple rule, converted to {required:true}
        },
        messages: {
            templateName: makeSmartyLabel('Alert_empty_subject')
        }

    });


    $("#btnUpdateTemplate").click(function(){

        if ($("#update-emailconf-form").valid()) {
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkEmailConfig/updateTemplate',
                dataType: 'json',
                data: $("#update-emailconf-form").serialize() + '&description=' + $('#description').summernote('code'),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-update-template');
                },
                success: function(ret){

                    var obj = jQuery.parseJSON(JSON.stringify(ret));

                    if(obj.status == 'OK') {

                        showAlert(makeSmartyLabel('Alert_success_update'),'success',path + '/helpdezk/hdkEmailConfig/index');

                    } else {

                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-update-template');

                    }

                },
                beforeSend: function(){
                    $("#btnUpdateTemplate").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnUpdateTemplate").html("<i class='fa fa-check-circle'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }

            });
        } else {
            return false;
        } ;

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


