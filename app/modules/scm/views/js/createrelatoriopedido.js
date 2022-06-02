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
    $(".centrodecusto").chosen({ width: "95%",    no_results_text: "Nada encontrado!"});
    $(".produto").chosen({ width: "95%",    no_results_text: "Nada encontrado!"});
    $(".status").chosen({ width: "95%",    no_results_text: "Nada encontrado!"});
    $(".solicitante").chosen({ width: "95%",    no_results_text: "Nada encontrado!"});


    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/scm/scmRelatorioPedido/index');


    /*
     * Validate
     */
    $("#create-relatoriopedido-form").validate({
        ignore:[],
        rules: {
            codigo: "required",
            nome: "required",
            tipo: "required"
        },
        messages: {
            codigo: "Campo obrigat&oacute;rio",
            nome: "Campo obrigat&oacute;rio",
            tipo: "Campo obrigat&oacute;rio"
        }
    });
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