$(document).ready(function () {

    countdown.start(timesession);
    new gnMenu( document.getElementById( 'gn-menu' ) );

    $('.input-group.date').datepicker({
        todayHighlight: true,
        orientation: "auto",
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
     *  Chosen
     */
    $("#cmbTypePerson").chosen({ width: "100%",   no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});

    $('input[name=filetype]').on('ifClicked',function(){
        if($(this).val() == 'CSV'){
            $('#csvDelimiter').removeClass('hide');
        }else{
            if(!$('#csvDelimiter').hasClass('hide'))
                $('#csvDelimiter').addClass('hide');
        }
    });

    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/admin/personReport/index');

    $("#btnSearch").click(function(){

        if (!$("#person-report-form").valid()) {
            return false ;
        }

        //
        $.ajax({
            type: "POST",
            url: path + '/admin/personReport/getReport',
            dataType: 'json',
            data: $("#person-report-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_get_data'),'alert-person-report');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.data != null && obj.data.length > 0) {
                    $('.returnBox').removeClass('hide');
                    $("#returnTable tbody").empty();
                    $("#returnTablePrint tbody").empty();

                    $.each(obj.data, function(key, val) {
                        $("#returnTable tbody").append('<tr><td class="text-center">'+ val.login +'</td><td>'+ val.name +'</td><td class="text-center">'+ val.typeperson +'</td><td class="text-center">'+ val.company +'</td></tr>');
                        $("#returnTablePrint tbody").append('<tr><td class="text-center">'+ val.login +'</td><td>'+ val.name +'</td><td class="text-center">'+ val.typeperson +'</td><td class="text-center">'+ val.company +'</td></tr>');
                    });

                } else {
                    if(!$('.returnBox').hasClass('hide'))
                        $('.returnBox').addClass('hide');

                        modalAlertMultiple('warning',makeSmartyLabel('No_result'),'alert-person-report');
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
            url: path + "/admin/personReport/exportReport",
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
        $("#titlePrint").html(makeSmartyLabel('pgr_person_report'));
        $("#subHeader").html('<div class="form-group"><div class="col-sm-12"><label class="control-label">'+makeSmartyLabel('pgr_person_report')+'</label></div></div>');
        $("#returnTablePrint thead").empty();
        $("#returnTablePrint thead").html("<tr><th class='col-sm-2 text-center'><h4><strong>"+makeSmartyLabel('Login')+"</strong></h4></th><th class='col-sm-5 text-center'><h4><strong>"+makeSmartyLabel('Name')+"</strong></h4></th><th class='col-sm-2 text-center'><h4><strong>"+makeSmartyLabel('Type')+"</strong></h4></th><th class='col-sm-3 text-center'><h4><strong>"+makeSmartyLabel('Company')+"</strong></h4></th></tr>");
        $('#modal-web-print').modal('show');
    });

    $("#btnPrintModal").click(function(e){
         e.preventDefault();
         printElement(document.getElementById("divReportTitle"));
         printElement(document.getElementById("divReturn"),true);
        window.print();
    });


    /*
     * Validate
     */
    $("#person-report-form").validate({
        ignore:[],
        rules: {
            cmbTypePerson: "required"
        },
        messages: {
            cmbTypePerson: makeSmartyLabel('Alert_field_required')
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