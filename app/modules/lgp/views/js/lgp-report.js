$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    showDefs = showDefaults();

    $('.input-group.date').datepicker({
        todayHighlight: true,
        orientation: "bottom",
        format: "dd/mm/yyyy",
        language: "pt-BR",
        autoclose: true
    });

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    $('.icoToolTip').tooltip();

    /*
     *  Combos configuration
     */
    $("#cmbType").chosen({ width: "100%",   no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#cmbHoldertype").chosen({ width: "100%",   no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#cmbDatatype").chosen({ width: "100%",   no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#cmbStorage").chosen({ width: "100%",   no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#cmbWhoaccess").chosen({ width: "100%",   no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#cmbSharedWhom").chosen({ width: "100%",   no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});

    /*
     *  Filter dynamics considering the option chosen in the report type list
     */
    $("#cmbType").change(function(){

        switch($(this).val()){

            case '2': //Second type option - "Shared"

                $(".relDep").addClass('hide');
                //All filters that are dependent on the "report type" field appear 
                $(".relDep").removeClass('hide');
                //Filter "shared with whom" turn invisible
                $("#fieldWhoAccess").addClass('hide');

            break;

            default:
                $(".relDep").addClass('hide');
                //All filters that are dependent on the "report type" field appear 
                $(".relDep").removeClass('hide');
                //Filter "shared with whom" turn invisible
                $("#fieldSharedWhom").addClass('hide');
            break;
   
        }

    });


    $('input[name=filetype]').on('ifClicked',function(){
        if($(this).val() == 'CSV'){
            $('#csvDelimiter').removeClass('hide');
        }else{
            if(!$('#csvDelimiter').hasClass('hide'))
                $('#csvDelimiter').addClass('hide');
        }
    });

    /*
     * Buttons Events
     */
    $("#btnCancel").attr("href", path + '/hdk/hdkRelSolicitacoes/index');

    $("#btnSearch").click(function(){

        if (!$("#inv-report-form").valid()) {
            return false ;
        }

        //When the form is send
        if(!$("#btnSearch").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/lgp/lgpReport/getReport',
                dataType: 'json',
                data: $("#inv-report-form").serialize(),
                beforeSend: function(){
                    console.log("Enviando os dados do relatório...");
                },
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_get_data'),'alert-inv-report');
                },
                success: function(ret){
                    
                    //Para evitar sobreposição de conteúdo
                    $("#returnTable tbody").empty();
                    $("#returnTablePrint tbody").empty();

                    if(ret){

                        $('.returnBox').removeClass('hide');
                        $("#returnTable tbody").html(ret);
                        $("th").css("text-align", "center");

                    }else{
                        if(!$('.returnBox').hasClass('hide'))
                            $('.returnBox').addClass('hide');

                        modalAlertMultiple('warning',makeSmartyLabel('No_result'),'alert-inv-report');
                    }


                },
                beforeSend: function(){
                    $("#btnSearch").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                },
                complete: function(){
                    $("#btnSearch").html("<i class='fa fa-search'></i> "+ makeSmartyLabel('Search')).removeClass('disabled');
                }

            });
        }

    });

    $("#btnSave").click(function(){
        
        $("#titleExport").html(makeSmartyLabel('pgr_person_report'));
        $("#modal-export").modal('show');
    });

    $("#btnExport").click(function(e){

        e.preventDefault();

        if (!$("#form-export-select").valid()) {
            return false ;
        }

        //
        $.ajax({
            type: "POST",
            url: path + "/helpdezk/hdkRelSolicitacoes/exportReport",
            data: {_token:$("#_token").val(),typeFile:$("input[name='filetype']:checked").val(),txtDelimiter:$("#txtDelimiter").val()},
            error: function (fileName) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_get_data'),'alert-modal-export');
            },
            success: function(fileName) {
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

    $("#btnPrint").click(function(e){

        if(!$("#btnPrint").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + "/lgp/lgpReport/exportReport",
                data: {_token:$("#_token").val(),typeFile:'PDF',txtDelimiter:''},
                beforeSend: console.log("Exportando tabela..."),
                error: function (fileName) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_get_data'),'alert-modal-export');
                },
                success: function(fileName) {
                    if(fileName){
                        /*
                         * I had to make changes to open the file in a new window
                         * because I could not use the jquery.download with the .pdf extension
                         */
                        if (fileName.indexOf(".pdf") >= 0) {
                            window.open(fileName, '_blank');
                        } else {
                            $.fileDownload(fileName);
    
                        }
    
                    }
                    else {
                    }
                },
                beforeSend: function(){
                    $("#btnPrint").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                },
                complete: function(){
                    $("#btnPrint").html("<i class='fa fa-print'></i> "+ makeSmartyLabel('Print')).removeClass('disabled');
                }
            });
        }
    
    });

    $("#btnPrintModal").click(function(e){
         e.preventDefault();
         printElement(document.getElementById("divReportTitle"));
         printElement(document.getElementById("divReturn"),true);
        window.print();
    });
    
    /*
     * Validate Events
     */
    $("#inv-report-form").validate({
        ignore:[],
        rules: {
            cmbType: "required",
            cmbHoldertype:  {
                 //required quando...
                required:function(element){ return ($('#cmbType').val() == '1' || $('#cmbType').val() == '2')}
            },
            cmbDatatype: {
                required:function(element){ return ($('#cmbType').val() == '1' || $('#cmbType').val() == '2')}
            },
            cmbStorage:  {
                required:function(element){ return ($('#cmbType').val() == '1' || $('#cmbType').val() == '2')}
            },
            cmbWhoaccess: {
                required:function(element){ return ($('#cmbType').val() == '1')}
            },
            cmbSharedWhom: {
                required:function(element){ return ($('#cmbType').val() == '2')}
            }
        },
        messages: {
            cmbType: {required:makeSmartyLabel('Alert_field_required')},
            cmbHoldertype: {required:makeSmartyLabel('Alert_field_required')}, 
            cmbDatatype: {required:makeSmartyLabel('Alert_field_required')}, 
            cmbStorage: {required:makeSmartyLabel('Alert_field_required')}, 
            cmbWhoaccess: {required:makeSmartyLabel('Alert_field_required')}, 
            cmbSharedWhom: {required:makeSmartyLabel('Alert_field_required')},  
        }
    });

    $("#form-export-select").validate({
        ignore:[],
        rules: {
            txtDelimiter: {required:"#csv:checked"},
        },
        messages: {
            txtDelimiter: makeSmartyLabel('Alert_field_required')
        }
    });

});

function printElement(elem, append, delimiter) {

    var domClone = elem.cloneNode(true);

    var $printSection = document.getElementById("printSection");

    if (!$printSection) {
        var $printSection = document.createElement("div");
        $printSection.id = "printSection";
        document.body.appendChild($printSection);
    }

    if (append !== true) {
        $printSection.innerHTML = "";
    }

    else if (append === true) {
        if (typeof(delimiter) === "string") {
            $printSection.innerHTML += delimiter;
        }
        else if (typeof(delimiter) === "object") {
            $printSection.appendChlid(delimiter);
        }
    }

    $printSection.appendChild(domClone);
}

function showDefaults()
{
    var result="";
    $.ajax({
        url: path+"/helpdezk/hdkTicket/showDefaults" ,
        type: "POST",
        async: false,
        success: function(data) {
            result = data;
        }
    });
    return result;

}