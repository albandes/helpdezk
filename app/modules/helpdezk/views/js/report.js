//JAVASCRIPT DO PROGRAMA RELATÓRIO SOLICITAÇÕES
$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    showDefs = showDefaults();

    /*var objReportData = {
        loadCustomer: function() {        
            $.post(path+"/hdk/hdkRelSolicitacoes/ajaxCustomer",
                {"adviserID":$('#cmbAdviser').val()},
                function(valor) {
                    $("#cmbCustomer").html(valor);
                    $("#cmbCustomer").trigger("chosen:updated");
                    return false;
                });
            return false ;
        }
    }*/

    var objReportData = {
        loadCustomer: function() {        
            $.post(path+"/helpdezk/hdkRelSolicitacoes/ajaxCustomer",
                {"companyID":$('#cmbEmpresa').val()},
                function(valor) {
                    $("#cmbAtendente").html(valor);
                    $("#cmbAtendente").trigger("chosen:updated");
                    return false;
                });
            return false ;
        }
    }

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
     *  ----------------------------------------------ESCOLHAS E FILTROS---------------------------------------------------------
     */
    $("#cmbRelType").chosen({ width: "100%",   no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#cmbEmpresa").chosen({ width: "100%",   no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#cmbAtendente").chosen({ width: "100%",   no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#cmbArea").chosen({ width: "100%",   no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#cmbTipo").chosen({ width: "100%",   no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#cmbItem").chosen({ width: "100%",   no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#cmbServico").chosen({ width: "100%",   no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#cmbMotivo").chosen({ width: "100%",   no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#cmbTipoatend").chosen({ width: "100%",   no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#cmbTipoPeriodo").chosen({ width: "100%",   no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});


    //Sempre que o item selecionado no filtro "Empresa" alterar
    $("#cmbEmpresa").change(function(){
        objReportData.loadCustomer();
    });

    //Dinâmica de filtros considerando o valor do "tipo de relatório"
    $("#cmbRelType").change(function(){

        switch($(this).val()){

            case '':
                //Então os campos dependentes do filtro "tipo de relatório" desaparecem, exceto o próprio
                $(".relDep").addClass('hide');
                $("#campoCalendars").addClass('hide');

            break;

            case '1': //Resumido por empresa

                //Todos que são dependentes do tipo de rel desaparecem
                $(".relDep").addClass('hide');
            
                //Então os campos obrigatórios aparecem, que é únicamente o período
                $("#campoTimeSelect").removeClass('hide');

                //Então o campo Empresa aparece
                $("#campoCompany").removeClass('hide');

            break;

            case '2': //Resumido por atendente

                //Todos que são dependentes do tipo de rel desaparecem
                $(".relDep").addClass('hide');
            
                //Então os campos obrigatórios aparecem, que é únicamente o período
                $("#campoTimeSelect").removeClass('hide');

                //Então o campo Empresa aparece
                $("#campoCompany").removeClass('hide');

                //Então o campo Atendimento aparece
                $("#campoOperator").removeClass('hide');

            break;

            case '3': //Resumido por área

                //Todos que são dependentes do tipo de rel desaparecem
                $(".relDep").addClass('hide');

                //Então os campos obrigatórios aparecem, que é únicamente o período
                $("#campoTimeSelect").removeClass('hide');

                //Então aparece o filtro "Área"
                $("#campoArea").removeClass('hide');

            break;

            case '4': //Resumido por tipo

                //Todos que são dependentes do tipo de rel desaparecem
                $(".relDep").addClass('hide');

                //Então os campos obrigatórios aparecem, que é únicamente o período
                $("#campoTimeSelect").removeClass('hide');

                //Então aparece o filtro "Área"
                $("#campoArea").removeClass('hide');

                //O campo "Tipo" aparece, mas desabilitado até que seja escolhida uma área
                $("#campoType").removeClass('hide');

                if($("#cmbArea").val() != ''){

                    $("#cmbArea").change();
                }

            break;

            case '5': //Resumido por item

            //Todos que são dependentes do tipo de rel desaparecem
            $(".relDep").addClass('hide');

            //Então os campos obrigatórios aparecem, que é únicamente o período
            $("#campoTimeSelect").removeClass('hide');

            //Então aparece o filtro "Área"
            $("#campoArea").removeClass('hide');

            //O campo "Tipo" aparece, mas desabilitado até que seja escolhida uma área
            $("#campoType").removeClass('hide');

            //O campo "Item" aparece
            $("#campoItem").removeClass('hide');

            if($("#cmbTipo").val() != ''){

                $("#cmbTipo").change();
            }

        break;

        case '6': //Resumido por serviço
        
        //Todos que são dependentes do tipo de rel desaparecem
        $(".relDep").addClass('hide');

        //Então os campos obrigatórios aparecem, que é únicamente o período
        $("#campoTimeSelect").removeClass('hide');

        //Então aparece o filtro "Área"
        $("#campoArea").removeClass('hide');

        //O campo "Tipo" aparece, mas desabilitado até que seja escolhida uma área
        $("#campoType").removeClass('hide');

        //O campo "Item" aparece
        $("#campoItem").removeClass('hide');

        //O campo "Item" aparece
        $("#campoService").removeClass('hide');

        if($("#cmbItem").val() != ''){

            $("#cmbItem").change();
        }

    break;

    case '7': //Resumido por tipo de atendimento
        
        //Todos que são dependentes do tipo de rel desaparecem
        $(".relDep").addClass('hide');

        //Então os campos obrigatórios aparecem, que é únicamente o período
        $("#campoTimeSelect").removeClass('hide');

        //O campo "Item" aparece
        $("#campoAttendance").removeClass('hide');

        if($("#cmbServico").val() != ''){

            $("#cmbServico").change();
        }

    break;

    case '8': //Solicitações Finalizadas
        
        //Todos que são dependentes do tipo de rel desaparecem
        $(".relDep").addClass('hide');

        //Então os campos obrigatórios aparecem, que é únicamente o período
        $("#campoTimeSelect").removeClass('hide');

        //Então o campo Empresa aparece
        $("#campoCompany").removeClass('hide');

         //Então o campo Atendente aparece
         $("#campoOperator").removeClass('hide');

    break;
   
        }

    });

    //Dinâmica de filtros considerando o valor do "Área" //Área -> Tipo
    $("#cmbArea").change(function(){
        objNewArea.changeArea();
    });

    $("#cmbTipo").change(function(){
        objNewArea.changeType();
    });

    $("#cmbItem").change(function(){
        objNewArea.changeItem();
    });

    $("#cmbServico").change(function(){
        objNewArea.changeService();
    });
     
    var objNewArea = {

        changeArea: function() {

            //Se o tipo de relatório for "Resumido por "tipo", "item" ou "serviço"
            if($("#cmbRelType").val() === "4" || $("#cmbRelType").val() === "5" || $("#cmbRelType").val() === "6")
            {

                $("#campoType").removeClass('hide');

                var areaId = $("#cmbArea").val(); //Pegar valor da opção do filtro "Área"
                $.post(path+"/helpdezk/hdkTicket/ajaxTypes",{areaId: areaId}, //Para listar os tipos no filtro de "tipo"

                    function(valor){ //response


                        /*$('#cmbTipo').removeAttr('disabled');

                        $("#cmbTipo").html(retorno);
                        $("#cmbTipo").trigger("chosen:updated");*/

                        $('#cmbTipo').removeAttr('disabled'); console.log($("#cmbRelType").val());
                    

                        if (showDefs == 'YES') {
                            $("#cmbTipo").html(valor);
                            $("#cmbTipo").trigger("chosen:updated");
                            return objNewTicket.changeItem();
                        } else if (showDefs == 'NO') {
                            $("#cmbTipo").html('<option value="">'+makeSmartyLabel('Select')+'</option>' + '<option value="ALL">'+makeSmartyLabel('all')+'</option>' + valor);
                            $("#cmbTipo").val('');
                            $("#cmbTipo").trigger("chosen:updated");
                        }
                        
                        //Voltar para diasbled caso valor de Area seja "selecione"

                });
            }
        },
        //Se a opção do filtro "Tipo" mudar
        changeType: function(){

            var typeId = $("#cmbTipo").val(); //Pegar valor da opção do filtro "Tipo"

            if($("#cmbRelType").val() === "5" || $("#cmbRelType").val() === "6"){

                $("#campoItem").removeClass('hide'); //Remover a classe "hide" do filtro "Item"

                $.post(path+"/helpdezk/hdkTicket/ajaxItens",{typeId: typeId}, //Para listar os itens no filtro de "item"
                    function(valor){

                        $('#cmbItem').removeAttr('disabled');

                        if (showDefs == 'YES') {
                            $("#cmbItem").html(valor);
                            $("#cmbItem").trigger("chosen:updated");
                            return objNewTicket.changeService();
                        } else if (showDefs == 'NO') {
                            $("#cmbItem").html('<option value="">'+makeSmartyLabel('Select')+'</option>' + '<option value="ALL">'+makeSmartyLabel('all')+'</option>' + valor);
                            $("#cmbItem").val('');
                            $("#cmbItem").trigger("chosen:updated");
                        }

                    });

            }
        },
        //Se a opção do filtro "Item" mudar
        changeItem: function(){

            var itemId = $("#cmbItem").val(); //Pegar valor da opção do filtro "Item"

            if($("#cmbRelType").val() === "6"){

                $("#campoService").removeClass('hide'); //Remover a classe "hide" do filtro "Serviço"

                $.post(path+"/helpdezk/hdkTicket/ajaxServices",{itemId: itemId}, //Para listar os itens do filtro "serviço"
                    function(valor){

                        $('#cmbServico').removeAttr('disabled');

                        if (showDefs == 'YES') {
                            $("#cmbServico").html(valor);
                            $("#cmbServico").trigger("chosen:updated");
                            return objNewTicket.changeService();
                        } else if (showDefs == 'NO') {
                            $("#cmbServico").html('<option value="">'+makeSmartyLabel('Select')+'</option>' + '<option value="ALL">'+makeSmartyLabel('all')+'</option>' + valor);
                            $("#cmbServico").val('');
                            $("#cmbServico").trigger("chosen:updated");
                        }
                    });
            }
        },
        //Se a opção do filtro "Service" mudar
        changeService: function(){

            var serviceId = $("#cmbServico").val(); //Pegar valor da opção do filtro "Item"

            if($("#cmbRelType").val() === "6"){

                $("#campoReason").removeClass('hide'); //Remover a classe "hide" do filtro "Motivo"

                $.post(path+"/helpdezk/hdkTicket/ajaxReasons",{serviceId: serviceId}, //Para listar os itens do filtro "motivo"
                    function(valor){

                        $('#cmbMotivo').removeAttr('disabled');

                        if (showDefs == 'YES') {
                            $("#cmbMotivo").html(valor);
                            $("#cmbMotivo").trigger("chosen:updated");
                            return objNewTicket.changeService();
                        } else if (showDefs == 'NO') {
                            $("#cmbMotivo").html('<option value="">'+makeSmartyLabel('Select')+'</option>' + valor);
                            $("#cmbMotivo").val('');
                            $("#cmbMotivo").trigger("chosen:updated");
                        }
                    });
            }
        },

    }



    //Dinâmica de filtros considerando o valor do "período"
    $("#cmbTipoPeriodo").change(function(){

        switch($(this).val()){

            case '':
                //Então o campo de Calendários desaparece
                $("#campoCalendars").addClass('hide');
            break;
            case '1':
            case '2':
            case '3':
                //Então o campo de Calendários desaparece
                $("#campoCalendars").addClass('hide');
            break;

            case '4':
                //Então o campo de Calendários aparece
                $("#campoCalendars").removeClass('hide');
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
     * ----------------------------------------------EVENTOS DE BOTÃO-------------------------------------------------------
     */
    $("#btnCancel").attr("href", path + '/hdk/hdkRelSolicitacoes/index');

    $("#btnSearch").click(function(){

        if (!$("#inv-report-form").valid()) {
            return false ;
        }

        if(!$("#btnSearch").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/helpdezk/hdkRelSolicitacoes/getReport',
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
                    //$("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnSearch").html("<i class='fa fa-search'></i> "+ makeSmartyLabel('Search')).removeClass('disabled');
                    //$("#btnCancel").removeClass('disabled');
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

    $("#btnPrint").click(function(e){

        //console.log("teste");

        if(!$("#btnPrint").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + "/helpdezk/hdkRelSolicitacoes/exportReport",
                data: {_token:$("#_token").val(),typeFile:'PDF',txtDelimiter:''},
                beforeSend: console.log("Exportando tabela..."),
                error: function (fileName) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_get_data'),'alert-modal-export');
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
                            $.fileDownload(fileName);
    
                        }
    
                    }
                    else {
                    }
                },
                beforeSend: function(){
                    $("#btnPrint").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                    //$("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnPrint").html("<i class='fa fa-print'></i> "+ makeSmartyLabel('Print')).removeClass('disabled');
                    //$("#btnCancel").removeClass('disabled');
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

    /*---------------------------FIM DOS EVENTOS DE BOTÃO----------------------------------------------------- */


    /*
     * ------------------------------------VALIDAÇÕES DE CAMPOS E VALORES----------------------------------------
     */
    
    $("#inv-report-form").validate({
        ignore:[],
        rules: {
            cmbRelType: "required",
            cmbTipoPeriodo: "required",
            cmbEmpresa:  {
                //required quando...
                required:function(element){ return ($('#cmbRelType').val() == '1' || $('#cmbRelType').val() == '2' || $('#cmbRelType').val() == '8')}
            },
            cmbAtendente: {
                required:function(element){ return $('#cmbRelType').val() == '2' && $('#cmbEmpresa').val() != "" || $('#cmbRelType').val() == '8'}
            },cmbArea:  {
                required:function(element){ return $('#cmbRelType').val() == '3' || $('#cmbRelType').val() == '4' || $('#cmbRelType').val() == '5' || $('#cmbRelType').val() == '6'}
            },
            cmbTipo: {
                required:function(element){ return $('#cmbRelType').val() == '4' || $('#cmbRelType').val() == '5' || $('#cmbRelType').val() == '6'}
            },cmbItem: {
                required:function(element){ return $('#cmbRelType').val() == '5' || $('#cmbRelType').val() == '6'}
            },cmbServico: {
                required:function(element){ return $('#cmbRelType').val() == '6'}
            },cmbTipoatend: {
                required:function(element){ return $('#cmbRelType').val() == '7'}
            },dtstart: {
                required:function(element){return $('#cmbTipoPeriodo').val() == '4';},
                checkDtStart:function(element){return $('#cmbTipoPeriodo').val() == '4';}
            },dtfinish: {
                required:function(element){return $('#cmbTipoPeriodo').val() == '4';},
                checkDtFinish:function(element){return $('#cmbTipoPeriodo').val() == '4';}
            }

            /*cmbMotivo: {
                required:function(element){ return $('#cmbRelType').val() == '?'}
            },*/
        },
        messages: {
            cmbRelType: {required:makeSmartyLabel('Alert_field_required')}, 
            cmbEmpresa: {required:makeSmartyLabel('Alert_field_required')},
            cmbTipoPeriodo: {required:makeSmartyLabel('Alert_field_required')},
            cmbAtendente: {required:makeSmartyLabel('Alert_field_required')},
            cmbArea: {required:makeSmartyLabel('Alert_field_required')},
            cmbTipo: {required:makeSmartyLabel('Alert_field_required')},
            cmbItem: {required:makeSmartyLabel('Alert_field_required')},
            cmbServico: {required:makeSmartyLabel('Alert_field_required')},
            cmbTipoatend: {required:makeSmartyLabel('Alert_field_required')},
            cmbMotivo: {required:makeSmartyLabel('Alert_field_required')},
            dtstart: {required:makeSmartyLabel('Alert_field_required')},
            dtfinish: {required:makeSmartyLabel('Alert_field_required')}
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


    $.validator.addMethod('checkDtStart', function(dtStart) {
        //console.log($('#cmbTipoPeriodo').val());
        if($('#cmbTipoPeriodo').val() != '4')
            return true;

        var parts = dtStart.split('/') , dtFinish = $("#dtfinish").val(), partsFinish = dtFinish.split('/');

        dtStart = new Date(parts[2], parts[1] - 1, parts[0]);
        dtFinish = new Date(partsFinish[2], partsFinish[1] - 1, partsFinish[0]);

        return dtStart <= dtFinish;

    }, makeSmartyLabel('Alert_start_date_error'));

    $.validator.addMethod('checkDtFinish', function(dtFinish) {
        if($('#cmbTipoPeriodo').val() != '4')
            return true;

        var parts = dtFinish.split('/') , dtStart = $("#dtstart").val(), partsStart = dtStart.split('/');

        dtFinish = new Date(parts[2], parts[1] - 1, parts[0]);
        dtStart = new Date(partsStart[2], partsStart[1] - 1, partsStart[0]);

        return dtFinish >= dtStart;

    }, makeSmartyLabel('Alert_finish_date_error'));

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