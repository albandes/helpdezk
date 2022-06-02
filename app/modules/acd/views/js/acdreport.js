var global_idperson = '';
$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    /*
     *  Chosen
     */
    $("#cmbReportType").chosen({ width: "100%", no_results_text: "Nada encontrado!",disable_search_threshold: 10});
    $("#cmbAcdYear").chosen({ width: "100%", no_results_text: "Nada encontrado!",disable_search_threshold: 10});
    $("#cmbCourse").chosen({ width: "100%", no_results_text: "Nada encontrado!",disable_search_threshold: 10});
    $("#cmbArea").chosen({ width: "100%", no_results_text: "Nada encontrado!",disable_search_threshold: 10});
    $("#cmbDisciplina").chosen({ width: "100%", no_results_text: "Nada encontrado!",disable_search_threshold: 10});
    $("#cmbSerie").chosen({ width: "100%", no_results_text: "Nada encontrado!",disable_search_threshold: 10});


    /*
     * Combos
     */
    var formWarningData = $(document.getElementById("create-warning-form"));
    var objReportData = {
        changeDisciplina: function() {
            var areaId = $("#cmbArea").val();
            $.post(path+"/acd/acdIndicadoresNotas/ajaxDisciplina",{areaId: areaId},
                function(valor){
                    $("#cmbDisciplina").html(valor);
                    $("#cmbDisciplina").trigger("chosen:updated");
                    //return objPersonData.changeCity();
                })
        },
        changeSerie: function() {
            var courseId = $("#cmbCourse").val();
            $.post(path+"/acd/acdIndicadoresNotas/ajaxSerie",{courseId: courseId},
                function(valor) {
                    $("#cmbSerie").html(valor);
                    $("#cmbSerie").trigger("chosen:updated");
                    //return objPersonData.changeNeighborhood();
                });
        },
        loadOptions: function(){
            var idtype = $("#cmbReportType").val();

            switch(idtype) {
                case '2':
                    $("#line_area").addClass('hidden');
                    $("#line_disciplina").addClass('hidden');
                    $("#line_serie").removeClass('hidden');
                    break;
                case '3':
                    $("#line_area").addClass('hidden');
                    $("#line_disciplina").addClass('hidden');
                    $("#line_serie").addClass('hidden');
                    break;
                default:
                    $("#line_area").removeClass('hidden');
                    $("#line_disciplina").removeClass('hidden');
                    $("#line_serie").removeClass('hidden');
            }
        }

    }

    $("#cmbReportType").change(function(){
        objReportData.loadOptions();
    });

    $("#cmbCourse").change(function(){
        objReportData.changeSerie();
    });

    $("#cmbArea").change(function(){
        objReportData.changeDisciplina();
    });


    $("#btnPrintReport").click(function(){

        /*if (!$("#create-warning-form").valid()) {
            return false ;
        }*/

        //
        $.ajax({
            type: "POST",
            url: path + "/acd/acdIndicadoresNotas/makeReport",
            data: $('#acd-report-form').serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-reopen');
            },
            success: function(fileName) {
                //console.log(fileName);
                if(fileName){
                    /*
                     * I had to make changes to open the file in a new window
                     * because I could not use the jquery.download with the .pdf extension
                     */
                    if (fileName.indexOf(".pdf") >= 0) {
                        window.open(fileName, '_blank');
                    } else {
                        $.fileDownload(fileName );

                    }

                }
                else {
                }
            }
        });


    });

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

});


