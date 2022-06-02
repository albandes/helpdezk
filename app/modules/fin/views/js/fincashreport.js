var global_idperson = '', all_movcaixa = false;
$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

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

    /*
     *  Chosen
     */
    $("#cmbCompany").chosen({ width: "100%", no_results_text: makeSmartyLabel("No_result"), disable_search_threshold: 10});

    /*
     * Combos
     */
    var objRemittanceRepData = {
        changeBank: function() {
            var companyId = $("#cmbCompany").val();
            $.post(path+"/fin/finBankReturnExport/ajaxBank",{companyId: companyId},
                function(valor){
                    $("#cmbBank").html(valor);
                    $("#cmbBank").trigger("chosen:updated");

                    if(!$('.returnBox').hasClass('hide'))
                        $('.returnBox').addClass('hide');

                    if(all_movcaixa){
                        all_movcaixa = false;
                        $("#btnCheckAll").html(makeSmartyLabel('emq_select_all'));
                    }
                })
        }
    }

    $("#cmbCompany").change(function(){
        objRemittanceRepData.changeBank();
    });


    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/fin/finCashReport/index');

    $("#btnSearch").click(function(){

        if (!$("#cash-report-form").valid()) {
            return false ;
        }

        //
        $.ajax({
            type: "POST",
            url: path + '/fin/finCashReport/getReport',
            dataType: 'json',
            data: $("#cash-report-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_get_data'),'alert-cash-report');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.data != null && obj.data.length > 0) {
                    $('.returnBox').removeClass('hide');
                    $("#returnTable tbody").empty();
                    $("#returnTablePrint tbody").empty();

                    $.each(obj.data, function(key, val) {
                        var identification = (val.tipo === "S" && val.identificacao === "") ? val.ctadestino : val.identificacao;
                        $("#returnTable tbody").append('<tr><td class="text-center"><input type="checkbox" name="idcaixa" id="'+val.codigo+'" value="'+ val.codigo +'"></td><td>'+ val.data +'</td><td>'+ val.nropagamento +'</td><td class="text-center">'+ identification +'</td><td class="text-center">'+ val.credito +'</td><td class="text-center">'+ val.debito +'</td><td class="text-center">'+ val.descricao +'</td></tr>');
                        $("#returnTablePrint tbody").append('<tr><td class="text-center"><input type="checkbox" name="idcaixa" id="'+val.codigo+'" value="'+ val.codigo +'"></td><td>'+ val.data +'</td><td>'+ val.nropagamento +'</td><td class="text-center">'+ identification +'</td><td class="text-center">'+ val.credito +'</td><td class="text-center">'+ val.debito +'</td><td class="text-center">'+ val.descricao +'</td></tr>');
                    });

                } else {
                    if(!$('.returnBox').hasClass('hide'))
                        $('.returnBox').addClass('hide');

                    modalAlertMultiple('warning',makeSmartyLabel('No_result'),'alert-cash-report');
                }

            },
            beforeSend: function(){
                $("#btnSearch").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                $("#returnTable tbody").empty();
                
                if(!$('.returnBox').hasClass('hide'))
                    $('.returnBox').addClass('hide');
                
                if(all_movcaixa){ 
                    all_movcaixa = false; 
                    $("#btnCheckAll").html(makeSmartyLabel('emq_select_all'));
                }
            },
            complete: function(){
                $("#btnSearch").html("<i class='fa fa-search'></i> "+ makeSmartyLabel('Search')).removeClass('disabled');
                //$("#btnCancel").removeClass('disabled');
            }

        });

    });

    $("#btnExport").click(function(e){

        e.preventDefault();
        if (!$("#cash-report-form").valid()) {
            return false ;
        }

        var movCaixaId = $("input[name=idcaixa]"), movCaixaIdNotEmpty = 0, arrMovCaixaId = [];

        movCaixaId.each(function(){
            if(this.checked){
                movCaixaIdNotEmpty = movCaixaIdNotEmpty + 1;
                arrMovCaixaId.push(this.value);
            }

        });

        if(movCaixaIdNotEmpty > 0){
            $.ajax({
                type: "POST",
                url: path + "/fin/finCashReport/exportReport",
                data: {
                    _token: $("#_token").val(),
                    movCaixaID: arrMovCaixaId,
                    companyID: $("#cmbCompany").val(),
                    dtstart: $("#dtstart").val()

                },
                error: function (fileName) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_get_data'),'alert-cash-report');
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
                },
                beforeSend: function(){
                    $("#btnExport").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                    $("#btnCheckAll").addClass('disabled');
                },
                complete: function(){
                    $("#btnExport").html("<i class='fa fa-print'></i> "+ makeSmartyLabel('Print')).removeClass('disabled');
                    $("#btnCheckAll").removeClass('disabled');
                }
            });
        }else{
            modalAlertMultiple('danger',makeSmartyLabel('FIN_alert_no_return_selected'),'alert-cash-report');
            return false;
        }


    });

    $("#btnPrint").click(function(e){
        /*e.preventDefault();
         printElement(document.getElementById("divReportTitle"));
         printElement(document.getElementById("divReturn"),true);
         window.print();*/
        $('#modal-web-print').modal('show');
    });

    $("#btnPrintModal").click(function(e){
        e.preventDefault();
        printElement(document.getElementById("divReportTitle"));
        printElement(document.getElementById("divReturn"),true);
        window.print();
    });

    $("#btnCheckAll").click(function(){
        var movcaixaId = $("input[name=idcaixa]"), check, msg;

        if(all_movcaixa){check = false; all_movcaixa = false; msg = makeSmartyLabel('emq_select_all');}
        else{check = true; all_movcaixa = true; msg = makeSmartyLabel('emq_unselect_all');}

        movcaixaId.each(function(){
            this.checked = check;
        });

        $("#btnCheckAll").html(msg);

    });


    /*
     * Validate
     */
    $("#cash-report-form").validate({
        ignore:[],
        rules: {
            cmbCompany: "required",
            dtstart: {required:true,},
        },
        messages: {
            cmbCompany: makeSmartyLabel('Alert_field_required'),
            dtstart: {required:makeSmartyLabel('Alert_field_required')}
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
        var parts = dtStart.split('/') , dtFinish = $("#dtfinish").val(), partsFinish = dtFinish.split('/');

        dtStart = new Date(parts[2], parts[1] - 1, parts[0]);
        dtFinish = new Date(partsFinish[2], partsFinish[1] - 1, partsFinish[0]);

        return dtStart <= dtFinish;

    }, makeSmartyLabel('Alert_start_date_error'));

    $.validator.addMethod('checkDtFinish', function(dtFinish) {
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
