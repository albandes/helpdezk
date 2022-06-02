var global_idmodule = '', gIcon = 0;
$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    /*
     * Mask
     */
    $("#txtEmail").mask('F', {
        translation: {'F':{pattern:/[0-9A-Za-z._-]/, recursive: true}},
        onKeyPress: function (value, event) {
            event.currentTarget.value = value.toLowerCase();
        }
    });
    /*$("#enrollmentId").mask('0#');*/

    $("#btnCreateEmailParent").click(function(){

        if (!$("#create-email-parent").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/emq/emqEmailParent/createEmail',
            dataType: 'json',
            data: $("#create-email-parent").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',aLang['Alert_failure'].replace (/\"/g, ""),'alert-create-email-parent');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'OK' ) {
                    $('#modal-notification').html(aLang['Alert_inserted'].replace (/\"/g, ""));
                    $("#btn-modal-ok").attr("href", path + '/emq/emqEmailParent/index');
                    $("#tipo-alert").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');

                } else {
                    modalAlertMultiple('danger',aLang['Alert_failure'].replace (/\"/g, ""),'alert-create-email-parent');
                }

            },
            beforeSend: function(){
                $("#btnCreateEmailParent").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                $("#btnCancel").addClass('disabled');
            },
            complete: function(){
                $("#btnCreateEmailParent").html("<i class='fa fa-check-circle'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                $("#btnCancel").removeClass('disabled');
            }

        });


    });


    /*
     * Validate
     */
    $("#create-email-parent").validate({
        ignore:[],
        rules: {
            txtEmail: {
                required: true,
                remote:{url: path + '/emq/emqEmailParent/checkEmail',
                    type: "post"
                }
            }
        },
        messages: {
            txtEmail: {
                required: makeSmartyLabel('Alert_field_required')
            }
        }
    });

});
