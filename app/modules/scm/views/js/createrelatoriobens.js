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

    /*
     * Mask
     */

    /*
     *  Chosen
     */
    $(".bens").chosen({ width: "95%",    no_results_text: "Nada encontrado!"});
    $(".estado").chosen({ width: "95%",    no_results_text: "Nada encontrado!"});
    $(".local").chosen({ width: "95%",    no_results_text: "Nada encontrado!"});


    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/scm/scmRelatorioBens/index');


    /*
     * Validate
     */

});

function sendNotification(transaction,codeRequest,hasAttachments)
{
    /*
     *
     * This was necessary because in some cases we have to send e-mail with the newly added attachments.
     * We only have access to these attachments after the execution of the dropzone.
     *
     */
    $.ajax({
        url : path + '/helpdezk/hdkTicket/sendNotification',
        type : 'POST',
        data : {
            transaction: transaction,
            code_request: codeRequest,
            has_attachment: hasAttachments
        },
        success : function(data) {

        },
        error : function(request,error)
        {

        }
    });

    return false ;

}