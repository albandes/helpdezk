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

    // Buttons
    $("#btnPrint").click(function(){
        if (!$("#relatoriobens-form").valid()) {
            return false ;
        }

        console.log($("#relatoriobens-form").serialize());

        $.ajax({
            type: "POST",
            url: path + "/scm/scmRelatorioBens/makeReport",
            data: $("#relatoriobens-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-reopen');
            },
            success: function(fileName) {

                if(fileName){
                    /*
                     * I had to make changes to open the file in a new window
                     * because I could not use the jquery.download with the .pdf extension
                     */
                    if (fileName.indexOf(".pdf") >= 0) {
                        window.open(fileName, '_blank'); //abre em nova janela o relatório
                    } else {
                        $.fileDownload(fileName );

                    }

                }
                else {
                }
            }
        });
        return false;
    });


});



