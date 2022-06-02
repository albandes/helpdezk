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
    $("#tipo").chosen({ width: "100%",    no_results_text: "Nada encontrado!"});
    $("#solicitante").chosen({ width: "100%",    no_results_text: "Nada encontrado!"});
    $("#idcentrocusto").chosen({ width: "100%",    no_results_text: "Nada encontrado!"});
    $("#idproduto").chosen({ width: "100%",    no_results_text: "Nada encontrado!"});
    $("#idstatus").chosen({ width: "100%",    no_results_text: "Nada encontrado!"});

    /*
     * Datepicker
     */
    var holidays = $.ajax({
        type: "POST",
        url: path+"/scm/scmCommon/_getHolidays",
        data: {cmbYear: moment().format('YYYY')},
        async: false,
        dataType: 'json'
    }).responseJSON;

    $('.input-group.date').datepicker({
        todayHighlight: true,
        orientation: "bottom auto",
        format: "dd/mm/yyyy",
        language: "pt-BR",
        autoclose: true,
        daysOfWeekDisabled: "0,6",
        datesDisabled: holidays.dates
    });

    // Buttons
    $("#btnPrint").click(function(){
        if (!$("#relatoriopedido-form").valid()) {
            return false ;
        }

        console.log($("#relatoriopedido-form").serialize());

        $.ajax({
            type: "POST",
            url: path + "/scm/scmRelatorioPedido/makeReport",
            data: $("#relatoriopedido-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-reopen');
            },
            success: function(fileName) {

                if(fileName == 'A pesquisa não obteve resultados'){
                    modalAlertMultiple('warning',fileName,'alert-create-relatoriopedido');
                }
                else {
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
            }
        });
        return false;
    });


});



