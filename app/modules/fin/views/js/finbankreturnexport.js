var global_idperson = '', all_mensalidade = false;
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
    $("#cmbBank").chosen({ width: "100%", no_results_text: makeSmartyLabel("No_result"), disable_search_threshold: 10});


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

                    if(all_mensalidade){
                        all_mensalidade = false;
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
    $("#btnCancel").attr("href", path + '/fin/finBankReturnExport/index');

    $("#btnSearch").click(function(){

        if (!$("#bank-return-export-form").valid()) {
            return false ;
        }

        //
        $.ajax({
            type: "POST",
            url: path + '/fin/finBankReturnExport/getReport',
            dataType: 'json',
            data: $("#bank-return-export-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_get_data'),'alert-bank-return-export');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.data != null && obj.data.length > 0) {
                    $('.returnBox').removeClass('hide');
                    $("#returnTable tbody").empty();
                    $("#returnTablePrint tbody").empty();

                    $.each(obj.data, function(key, val) {
                        $("#returnTable tbody").append('<tr><td class="text-center"><input type="checkbox" name="idmensalidade" id="'+val.MENCodigo+'" value="'+ val.MENCodigo +'"></td><td>'+ val.RESNome +'</td><td class="text-center">'+ val.MENParcela +'</td><td class="text-center">'+ val.MENValorPago +'</td><td class="text-center">'+ val.MENVencimento +'</td><td class="text-center">'+ val.MENPagamento +'</td></tr>');
                        $("#returnTablePrint tbody").append('<tr><td class="text-center"><input type="checkbox" name="idmensalidade" id="'+val.MENCodigo+'" value="'+ val.MENCodigo +'"></td><td>'+ val.RESNome +'</td><td class="text-center">'+ val.MENParcela +'</td><td class="text-center">'+ val.MENValorPago +'</td><td class="text-center">'+ val.MENVencimento +'</td><td class="text-center">'+ val.MENPagamento +'</td></tr>');
                    });

                } else {
                    if(!$('.returnBox').hasClass('hide'))
                        $('.returnBox').addClass('hide');

                    modalAlertMultiple('warning',makeSmartyLabel('No_result'),'alert-bank-return-export');
                }

            },
            beforeSend: function(){
                $("#btnSearch").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                $("#returnTable tbody").empty();
                
                if(!$('.returnBox').hasClass('hide'))
                    $('.returnBox').addClass('hide');
                
                    if(all_mensalidade){ 
                    all_mensalidade = false; 
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
        if (!$("#bank-return-export-form").valid()) {
            return false ;
        }

        var mensalidadeId = $("input[name=idmensalidade]"), mensalidadeIdNotEmpty = 0, arrMensalidadeId = [];

        mensalidadeId.each(function(){
            if(this.checked){
                mensalidadeIdNotEmpty = mensalidadeIdNotEmpty + 1;
                arrMensalidadeId.push(this.value);
            }

        });

        if(mensalidadeIdNotEmpty > 0){
            $.ajax({
                type: "POST",
                url: path + "/fin/finBankReturnExport/exportReport",
                dataType: 'json',
                data: {
                    _token: $("#_token").val(),
                    mensalidadeID: arrMensalidadeId,
                    companyID: $("#cmbCompany").val(),
                    bankID: $("#cmbBank").val(),
                    dtstart: $("#dtstart").val(),
                    dtfinish: $("#dtfinish").val()

                },
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_get_data'),'alert-bank-return-export');
                },
                success: function(ret) {
                    var obj = jQuery.parseJSON(JSON.stringify(ret));

                    if(obj.notexport.length){
                        $.each(obj.notexport, function(key, val) {
                            $("#notExportTable tbody").append('<tr><td>'+ val.RESNome +'</td><td class="text-center">'+ val.MENParcela +'</td><td class="text-center">'+ val.MENValorPago +'</td><td class="text-center">'+ val.MENVencimento +'</td><td class="text-center">'+ val.MENPagamento +'</td></tr>');
                        });

                        $('#modal-not-export').modal('show');
                    }

                    if(obj.txt){
                        /*
                         * I had to make changes to open the file in a new window
                         * because I could not use the jquery.download with the .pdf extension
                         */
                        if (obj.txt.indexOf(".pdf") >= 0) {
                            window.open(obj.txt, '_blank');
                        } else {
                            //$.fileDownload(fileName );
                            var content = obj.txt, dt = $("#dtstart").val().replace('/','').replace('/','');

                            var fname = "exportaRetorno_"+dt+".txt";

                            var blob = new Blob([content], {
                                type: "text/plain;charset=utf-8"
                            });

                            saveAs(blob, fname);
                        }

                    }
                    else {
                    }
                },
                beforeSend: function(){
                    $("#btnExport").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                },
                complete: function(){
                    $("#btnExport").html("<i class='fa fa-file-export'></i> "+ makeSmartyLabel('Export')).removeClass('disabled');
                    //$("#btnCancel").removeClass('disabled');
                }
            });
        }else{
            modalAlertMultiple('danger',makeSmartyLabel('FIN_alert_no_return_selected'),'alert-bank-return-export');
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

    $("#btnPrintList").click(function(e){
        e.preventDefault();
        printElement(document.getElementById("divReportTitle"));
        printElement(document.getElementById("divNotExport"),true);
        window.print();
    });

    $("#btnCheckAll").click(function(){
        var mensalidadeId = $("input[name=idmensalidade]"), check, msg;

        if(all_mensalidade){check = false; all_mensalidade = false; msg = makeSmartyLabel('emq_select_all');}
        else{check = true; all_mensalidade = true; msg = makeSmartyLabel('emq_unselect_all');}

        mensalidadeId.each(function(){
            this.checked = check;
        });

        $("#btnCheckAll").html(msg);

    });


    /*
     * Validate
     */
    $("#bank-return-export-form").validate({
        ignore:[],
        rules: {
            cmbCompany: "required",
            dtstart: {required:true,checkDtStart:true},
            dtfinish: {required:true,checkDtFinish:true}
        },
        messages: {
            cmbCompany: makeSmartyLabel('Alert_field_required'),
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
