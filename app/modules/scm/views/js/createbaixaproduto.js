var objPedidoData = {
    changeProduto: function(id) {
        var id = id;
        $.post(path+"/scm/scmPedidoCompra/ajaxProduto",
            function(valor) {
                $("#produtos_"+id).html(valor);
                $("#produtos_"+id).chosen({width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
                //objPedidoData.quantityConf(id);
                return false;
            })
        return false ;
    },
    quantityConf: function(id) {
        
        $.post(path+"/scm/scmBaixaProduto/_ajaxQuantityConf",{produtoID:$("#produtos_"+id).val()},
            function(valor) {
                var obj = jQuery.parseJSON(JSON.stringify(valor));
                if(!obj.flgstep)
                    $("#quantidades_"+id).val('').attr({max:obj.stock,step:'0.25'});
                else{
                    $("#quantidades_"+id).val('').attr({max:obj.stock,step:'1'}).keypress(function (e){
                        //if the letter is not digit then display error and don't type anything
                        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                            return false;
                        }
                    });
                }
                    
                return false;
            },'json')
        return false ;
    }

}

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

    if(access[1] != "Y"){
        $("#btnCreateBaixaProduto").addClass('hide');
    }

    if(access[2] != "Y"){
        $("#btnUpdateBaixaProduto").addClass('hide');
    }

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    /*
     *  Chosen
     */
    if($("#update-baixaproduto-form").length > 0){
        $(".produtos").chosen({width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    }
    $("#cmbDestination").chosen({width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10})
    
    if($("#create-baixaproduto-form").length > 0){
        objPedidoData.changeProduto(1);
    }
    

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
        format: "dd/mm/yyyy",
        language: "pt-BR",
        autoclose: true,
        daysOfWeekDisabled: "0,6",
        datesDisabled: holidays.dates
    });


    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/scm/scmBaixaProduto/index');

    $("#btnSaveBaixa").click(function(){

        if (!$("#create-baixaproduto-form").valid()) {
            return false ;
        }

        var flgZeroQt = false, flgEmptyQt = false, flgErrorQt = 0;
        $("input[name='quantidades[]']").each(function(){
            var selID = this.parentNode.parentNode.rowIndex;
            
            if(validateQt(selID))
                flgErrorQt++ ;
        });

        if (!$("#btnSaveBaixa").hasClass('disabled')) {
            $.ajax({
                type: "POST",
                url: path + '/scm/scmBaixaProduto/createBaixa',
                dataType: 'json',
                data: $("#create-baixaproduto-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-baixaproduto');
                },
                success: function(ret){    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Alert_inserted'),'success',path + '/scm/scmBaixaProduto/index');                        
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-create-baixaproduto');
                    }
                },
                beforeSend: function(){
                    $("#btnSaveBaixa").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnSaveBaixa").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
            });
        }

    });

    $("#btnUpdateBaixa").click(function(){

        if (!$("#update-baixaproduto-form").valid()) {
            return false ;
        }

        var flgZeroQt = false, flgEmptyQt = false, flgErrorQt = 0;
        $("input[name='quantidades[]']").each(function(){
            var selID = this.parentNode.parentNode.rowIndex;
            
            if(validateQt(selID))
                flgErrorQt++ ;
        });

        if(flgErrorQt > 0){
            return false ;
        }

        if (!$("#btnUpdateBaixa").hasClass('disabled')) {
            $.ajax({
                type: "POST",
                url: path + '/scm/scmBaixaProduto/updateBaixa',
                dataType: 'json',
                data: $("#update-baixaproduto-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-update-baixaproduto');
                },
                success: function(ret){    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
    
                    if(obj.success) {
                        showAlert(makeSmartyLabel('Edit_sucess'),'success',path + '/scm/scmBaixaProduto/index');                        
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Edit_failure'),'alert-update-baixaproduto');
                    }
                },
                beforeSend: function(){
                    $("#btnUpdateBaixa").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnUpdateBaixa").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
            });
        }

    });

    $("#addItem").click(function(){
        duplicateRow('itemList');
    });

    $("#btnAddDest").click(function(){
        $("#modal-add-destination").modal('show');
    });

    $("#btnSaveDest").click(function(){

        if (!$("#add-destination-form").valid()) {
            return false ;
        }

        if(!$("#btnSaveDest").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/scm/scmBaixaProduto/addDestination',
                dataType: 'json',
                data: {
                    _token: $("#_token").val(),
                    destName: $("#destName").val()
                },
                error: function (ret) {
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-add-destination');
                },
                success: function(ret){    
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.success) {
                        modalAlertMultiple('success',makeSmartyLabel('Alert_inserted'),'alert-add-destination');
                        loadDestination(obj.newDestID);
                        setTimeout(function(){
                            $('#modal-add-destination').modal('hide');
                        },2000);

                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-add-destination');
                    }
                },
                beforeSend: function(){
                    $("#btnSaveDest").html("<i class='fa fa-spinner fa-spin'></i> "+ makeSmartyLabel('Processing')).addClass('disabled');
                    $("#btnModalClose").addClass('disabled');
                },
                complete: function(){
                    $("#btnSaveDest").html("<i class='fa fa-save'></i> "+ makeSmartyLabel('Save')).removeClass('disabled');
                    $("#btnModalClose").removeClass('disabled');
                }
            });
        }

    });

    /*
     * Validate
     */
    $("#create-baixaproduto-form").validate({
        ignore:[],
        rules: {
            motivo: "required"
        },
        messages: {
            motivo: makeSmartyLabel('Alert_field_required')
        }
    });

    $("#update-baixaproduto-form").validate({
        ignore:[],
        rules: {
            motivo: "required"
        },
        messages: {
            motivo: makeSmartyLabel('Alert_field_required')
        }
    });

    $("#add-destination-form").validate({
        ignore:[],
        rules: {
            destName:"required"
        },
        messages: {
            destName:  makeSmartyLabel('Alert_field_required')
        }
    });

    /* clean modal's fields */
    $('#modal-add-destination').on('hidden.bs.modal', function() { 
        $('#add-destination-form').trigger('reset');        
    });

    $(document).on('keyup click blur', '.qtCheck', function() {
        var id = $(this).data('qtcheck'), msgtmp='', msg='';
        
        if($("#quantidades_"+id+"-error").length > 0){
            $("#quantidades_"+id+"-error").remove();
        }
        
        if($("#quantidades_"+id).val() != ''){
            if(parseFloat($("#quantidades_"+id).val()) <= 0){
                if(!$("#quantidades_"+id).hasClass('valid') || ($("#quantidades_"+id).hasClass('valid') && !$("#quantidades_"+id).hasClass('invalid')))
                    $("#quantidades_"+id).addClass('invalid').removeClass('valid').attr('aria-invalid','true');
                $("#quantidades_"+id).after('<label id="quantidades_'+id+'-error" class="error" for="quantidades_'+id+'">'+makeSmartyLabel('Alert_min_qt')+'</label>');
            }else{
                $.post(path+"/scm/scmBaixaProduto/_ajaxQuantityConf",{produtoID:$("#produtos_"+id).val()},
                function(valor) {
                    var obj = jQuery.parseJSON(JSON.stringify(valor));
                    if(!obj.flgstep)
                        $("#quantidades_"+id).attr({step:'0.25'});
                    else{
                        $("#quantidades_"+id).attr({step:'1'}).keypress(function (e){
                            //if the letter is not digit then display error and don't type anything
                            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                                return false;
                            }
                        });
                    }
                    
                    var valTmp = $("#quantidades_"+id).val(), valRes = valTmp.replace(",","."), thisValTmp = Number(valRes),thisVal = parseFloat(thisValTmp.toFixed(2)),
                        stockTmp = obj.stock, stockRes = stockTmp.replace(",","."), stockValTmp = Number(stockRes),  stockVal = parseFloat(stockValTmp.toFixed(2));
                    
                    if(obj.stock <= 0){
                        var msg = makeSmartyLabel('product_out_stock');
                        if($("#quantidades_"+id+"-error").length > 0){
                            $("#quantidades_"+id+"-error").html(msg).css('display','block');
                        }else{
                            $("#quantidades_"+id).after('<label id="quantidades_'+id+'-error" class="error" for="quantidades_'+id+'">'+msg+'</label>');
                        }
                        flgErrorQt = true;
    
                    }else {
                        if(thisVal > stockVal){
                        
                            msgtmp = makeSmartyLabel('Alert_maxstock_exceeded'), msg = msgtmp.replace("%",obj.stock);
                            if(!$("#quantidades_"+id).hasClass('valid') || ($("#quantidades_"+id).hasClass('valid') && !$("#quantidades_"+id).hasClass('invalid')))
                                $("#quantidades_"+id).addClass('invalid').removeClass('valid').attr('aria-invalid','true');
                            
                            if($("#quantidades_"+id+"-error").length > 0){
                                $("#quantidades_"+id+"-error").html('<label id="quantidades_'+id+'-error" class="error" for="quantidades_'+id+'">'+msg+'</label>');
                            }else{
                                $("#quantidades_"+id).after('<label id="quantidades_'+id+'-error" class="error" for="quantidades_'+id+'">'+msg+'</label>');
                            }
                        }
                    }
                
                    return false;
                },'json');
            }
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

function duplicateRow( strTableName ){
    // First, lets create the new row using the last one as template...
    var clonedRow = $( "#" + strTableName + " tr:last" ).clone();
    // Take the current identifier, some number in the first cell of the row
    intCurrentRowId = parseInt( $( "#numId:last", clonedRow ).val() );
    // Set the new ID
    intNewRowId = intCurrentRowId + 1;
    // Change the current identifier of the row to the new one
    $( "#numId:last", clonedRow ).val( intNewRowId );

    // Change the Id / Name or anything you want for the new attribs...
    //here is where you need to add a lot of stuff to change the id of your variables

    // The following code works without problems on Firefox or IE7
    if($("#update-baixaproduto-form").length > 0){
        $( "#iditembaixas_"+ intCurrentRowId , clonedRow ).attr( { "id" :"iditembaixas_" + intNewRowId, "accesskey" : intNewRowId } );
    }
    $( "#produtos_"+ intCurrentRowId , clonedRow ).attr( { "id" :"produtos_" + intNewRowId, "accesskey" : intNewRowId } );
    $( "#quantidades_"+ intCurrentRowId , clonedRow ).attr( { "id" :"quantidades_" + intNewRowId, "accesskey" : intNewRowId, "data-qtCheck" : intNewRowId} ).val('');
    $( "#produtos_"+ intCurrentRowId + "_chosen" , clonedRow ).attr( { "id" :"produtos_" + intNewRowId + "_chosen", "accesskey" : intNewRowId } );

    // Add to the new row to the original table
    $( "#" + strTableName ).append( clonedRow );

    $( "#produtos_"+ intNewRowId + "_chosen").remove();
    objPedidoData.changeProduto(intNewRowId);
    if($("#update-baixaproduto-form").length > 0){
        $( "#iditembaixas_"+ intNewRowId).remove();
    }

    // And finally change the ID of the last row to something we can
    //delete later, not sure why can not be done before the append :S
    $( "#" + strTableName + " tr:last" ).attr( "id", "detailsTr" + intNewRowId );
    $("#detailsTr" + intNewRowId +" #quantidades_"+intCurrentRowId+"-error").remove();


    $( "#produtos_"+ intNewRowId ).focus();
}

function removeRow(id,strTableName,ope='add'){
    var i = id.parentNode.parentNode.rowIndex, msgDiv;
    
    if(ope == 'upd')
        msgDiv = 'alert-update-baixaproduto';
    else
        msgDiv = 'alert-create-baixaproduto';

    if($("#"+strTableName+" tbody tr").length == 1){
        modalAlertMultiple('info', makeSmartyLabel('Alert_dont_remove_row'),msgDiv);
    }else{
        document.getElementById(strTableName).deleteRow(i);
    }
}

function loadDestination(selectedID)
{
    $.post(path+"/scm/scmBaixaProduto/ajaxDestination",
    {"selectedID":selectedID},
    function(res) {
        $("#cmbDestination").html(res);
        $("#cmbDestination").trigger("chosen:updated");
        return false;
    });
    return false ;
}

function removeRow(id,strTableName,ope='add'){
    var i = id.parentNode.parentNode.rowIndex, msgDiv;
    
    if(ope == 'upd')
        msgDiv = 'alert-update-baixaproduto';
    else
        msgDiv = 'alert-create-baixaproduto';

    if($("#"+strTableName+" tbody tr").length == 1){
        modalAlertMultiple('info', makeSmartyLabel('Alert_dont_remove_row'),msgDiv);
    }else{
        document.getElementById(strTableName).deleteRow(i);
    }
}

function loadMaxQt(id)
{
    objPedidoData.quantityConf(id);
}

function validateQt(selID)
{
    flgErrorQt = false;

    if($("#quantidades_"+selID).val() == ''){
        if($("#quantidades_"+selID+"-error").length > 0){
            $("#quantidades_"+selID+"-error").html(makeSmartyLabel('Alert_field_required')).css('display','block');
        }else{
            $("#quantidades_"+selID).after('<label id="quantidades_'+selID+'-error" class="error" for="quantidades_'+selID+'">'+makeSmartyLabel('Alert_field_required')+'</label>');
        }
        flgErrorQt = true;
    }else if(parseFloat($("#quantidades_"+selID).val()) <= 0){
        if($("#quantidades_"+selID+"-error").length > 0){
            $("#quantidades_"+selID+"-error").html(makeSmartyLabel('Alert_min_qt')).css('display','block');
        }else{
            $("#quantidades_"+selID).after('<label id="quantidades_'+selID+'-error" class="error" for="quantidades_'+selID+'">'+makeSmartyLabel('Alert_min_qt')+'</label>');
        }
        flgErrorQt = true;
    }else{
        $.ajax({
            type: "POST",
            url: path+"/scm/scmBaixaProduto/_ajaxQuantityConf",
            dataType: 'json',
            data: {produtoID:$("#produtos_"+selID).val()},
            async: false,
            error: function (ret) {
                if($("#quantidades_"+selID+"-error").length > 0){
                    $("#quantidades_"+selID+"-error").html(makeSmartyLabel('generic_error_msg')).css('display','block');
                }else{
                    $("#quantidades_"+selID).after('<label id="quantidades_'+selID+'-error" class="error" for="quantidades_'+selID+'">'+makeSmartyLabel('generic_error_msg')+'</label>');
                }
            },
            success: function(ret){    
                var obj = jQuery.parseJSON(JSON.stringify(ret)),
                valTmp = $("#quantidades_"+selID).val(), valRes = valTmp.replace(",","."), thisValTmp = Number(valRes), thisVal = parseFloat(thisValTmp.toFixed(2)),
                stockTmp = obj.stock, stockRes = stockTmp.replace(",","."), stockValTmp = Number(stockRes),  stockVal = parseFloat(stockValTmp.toFixed(2));

                if(obj.stock <= 0){
                    var msg = makeSmartyLabel('product_out_stock');
                    if($("#quantidades_"+selID+"-error").length > 0){
                        $("#quantidades_"+selID+"-error").html(msg).css('display','block');
                    }else{
                        $("#quantidades_"+selID).after('<label id="quantidades_'+selID+'-error" class="error" for="quantidades_'+selID+'">'+msg+'</label>');
                    }
                    flgErrorQt = true;

                }else {
                    if(thisVal > stockVal){
                        var msgtmp = makeSmartyLabel('Alert_maxstock_exceeded'), msg = msgtmp.replace("%",obj.stock);
                        if($("#quantidades_"+selID+"-error").length > 0){
                            $("#quantidades_"+selID+"-error").html(msg).css('display','block');
                        }else{
                            $("#quantidades_"+selID).after('<label id="quantidades_'+selID+'-error" class="error" for="quantidades_'+selID+'">'+msg+'</label>');
                        }
                        flgErrorQt = true;
                    }
                }
                
            }
        });
    }
    
    return flgErrorQt;

    
}