/*
 * Combos
 */
var objPedidoData = {
    changeProduto: function(id) {
        var id = id;
        $.post(path+"/scm/scmPedidoCompra/ajaxProduto",
            function(valor) {
                $("#produtos_"+id).html(valor);
                $("#produtos_"+id).chosen({width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
                //checkAvailability(id,'obj');
                return false;
            })
        return false ;
    },
    changeReplicateLbl: function(idturma) {
        var turmaList = '', newList = '';
        if(idturma){
            $.post(path+"/scm/scmPedidoCompra/ajaxReplicateLbl",{idturma:idturma},
                function(valor) {
                    var obj = jQuery.parseJSON(JSON.stringify(valor));
                    if(obj.length > 0){
                        $("#replicaList input[name='turmareplicar[]']").remove();
                        $.each(obj, function(key, val){
                            turmaList = turmaList === "" ? turmaList.concat(val.name) : turmaList.concat(', ',val.name);
                            $("#replicaList").append("<input type='hidden'  name='turmareplicar[]' id='turmareplicar_"+val.id+"' class='form-control input-sm' value='"+val.id+"' />");
                        });

                        $("#lblReplica").html(makeSmartyLabel('SCM_lbl_replica_list')+turmaList);
                        $("#replicaList").removeClass('hide');
                    }
                    return false;
                },"json");
        }else{
            if(!$("#replicaList").hasClass('hide'))
                $("#replicaList").addClass('hide');
        }

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
        $("#btnCreatePedidoCompra").addClass('hide');
    }

    if(access[2] != "Y"){
        $("#btnUpdatePedidoCompra").addClass('hide');
    }

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    $('#flagturma').on('ifChecked ifUnchecked',function(e){
        console.log(e.type);
        if(e.type == 'ifChecked'){
            $('#line_turma').removeClass('hidden');
        }else{
            $('#line_turma').addClass('hidden');
        }
    });

    /*
     *  Chosen
     */
    //$(".produtos").chosen({width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#cmbTurma").chosen({width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    $("#cmbOwner").chosen({width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});

    objPedidoData.changeProduto(1);

    /*
     * Datepicker
     */
    var startDate = moment().add(10,'d').format('DD/MM/YYYY');
    var endDate = moment().add(90,'d').format('DD/MM/YYYY');
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
        datesDisabled: holidays.dates,
        startDate: "'"+startDate+"'",
        endDate: "'"+endDate+"'"
    });

    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/scm/scmPedidoCompra/index');

    $("#btnCreatePedidoCompra").click(function(){

        if (!$("#create-pedidocompra-form").valid()) {
            return false ;
        }

        var flgZeroQt = false, flgEmptyQt = false;
        $("input[name='quantidades[]']").each(function(){
            if($(this).val() == ''){
                flgEmptyQt = true;
            }

            if(parseFloat($(this).val()) <= 0){
                flgZeroQt = true;
            }
        });

        if(flgEmptyQt){
            modalAlertMultiple('danger','Um ou mais itens não possuem quantidade!','alert-create-pedidocompra');
            return false ;
        }

        if(flgZeroQt){
            modalAlertMultiple('danger','Um ou mais itens possuem quantidade inválida!. Favor inserir quantidade superior a 0 (zero).','alert-create-pedidocompra');
            return false ;
        }

        // console.log($("#create-pedidocompra-form").serialize());

        //
        $.ajax({
            type: "POST",
            url: path + '/scm/scmPedidoCompra/createPedidoCompra',
            dataType: 'json',
            data: $("#create-pedidocompra-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-pedidocompra');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if($.isNumeric(obj.idpedidocompra)) {

                    var idpedidocompra = obj.idpedidocompra;
                    sendNotification('new-scmrequest-user',idpedidocompra);

                    //
                    $('#modal-idpedidocompra').html(idpedidocompra);
                    $('#modal-motivo').html(obj.motivo);
                    if(obj.replicateMsg){
                        $('#replicateAlert').removeClass('hide');
                        $('#modal-replicate-alert').html(obj.replicateMsg);
                    }

                    $("#btnModalAlert").attr("href", path + '/scm/scmPedidoCompra/index');

                    $('#modal-alert-create').modal('show');
                } else {
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-pedidocompra');
                }
            },
            beforeSend: function(){
                $("#btnCreatePedidoCompra").attr('disabled','disabled');
            },
            complete: function(){
                $("#btnCreatePedidoCompra").removeAttr('disabled');
            }
        });
    });

    $("#btnUpdatePedidoCompra").click(function(){

        if (!$("#update-pedidocompra-form").valid()) {
            return false ;
        }

        var flgZeroQt = false, flgEmptyQt = false;
        $("input[name='quantidades[]']").each(function(){
            if($(this).val() == ''){
                flgEmptyQt = true;
            }

            if(parseFloat($(this).val()) <= 0){
                flgZeroQt = true;
            }
        });

        if(flgEmptyQt){
            modalAlertMultiple('danger','Um ou mais itens não possuem quantidade!','alert-update-pedidocompra');
            return false ;
        }

        if(flgZeroQt){
            modalAlertMultiple('danger','Um ou mais itens possuem quantidade inválida!. Favor inserir quantidade superior a 0 (zero).','alert-update-pedidocompra');
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/scm/scmPedidoCompra/updatePedidoCompra/idpedidocompra/' + $('#idpedidocompra').val(),
            dataType: 'json',
            data: $("#update-pedidocompra-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-update-pedidocompra');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'OK' ) {

                    var idpedidocompra = obj.idpedidocompra;

                    $('#modal-notification').html('Pedido atualizado com sucesso');
                    $("#btn-modal-ok").attr("href", path + '/scm/scmPedidoCompra/index');
                    $("#tipo_alerta").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');

                } else {

                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-update-pedidocompra');

                }

            },
            beforeSend: function(){
                $("#btnUpdatePedidoCompra").attr('disabled','disabled');
            },
            complete: function(){
                $("#btnUpdatePedidoCompra").removeAttr('disabled')
            }
        });

    });

    $("#btnUpdateStatus").click(function(){
         $('#modal-form-status').modal('show');
     });

    $("#btnSendStatus").click(function(){
        if (!$("#status-form").valid()) {
            console.log('nao validou') ;
            return false;
        }

        $.ajax({
            type: "POST",
            url: path + '/scm/scmPedidoCompra/UpdateMotivoCancelamento',
            dataType: 'json',
            data: {
                _token: $("#_token").val(),
                motivocancelamento:  $('#motivocancelamento').val(),
                idpedidocompra:  $('#idpedidocompra').val()
            },

            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel alterar !','alert-status');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'OK' ) {

                    console.log("sdafdsfsda");
                    $('#modal-form-status').modal('hide');
                    $('#modal-notification').html('Pedido atualizado com sucesso');
                    $("#btn-modal-ok").attr("href", path + '/scm/scmPedidoCompra/index');
                    $("#tipo_alerta").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');

                } else {

                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-pedidocompra');

                }

            },
            beforeSend: function(){
                $("#btnSendStatus").attr('disabled','disabled');
            },
            complete: function(){
                $("#btnSendStatus").removeAttr('disabled')
            }

        });

    });

    var i = $("#_totalitens").val();

    $('#btnAddPedido').click (function() {
        console.log(i);
        i++;
        $('.pedido').append('<div class="form-group" id="item_'+i+'"><label class="col-sm-1 control-label">Itens:</label><div class="col-sm-5"><select class="form-control input-sm produtos" id="produtos_'+i+'" name="produtos[]"></select></div><div class="col-sm-1 btn-group"><button class="btn btnViewPicture" id="btnViewPicture" type="button" data-pedido="'+i+'"><i class="fa fa-image" aria-hidden="true"></i></button></div><label class="col-sm-1 control-label">Quantidade:</label><div class="col-sm-2"><input type="number" name="quantidades[]" class="form-control input-sm teste" placeholder="" step="0.25"  min="0" /></div><div class="col-sm-1"> <div class="btn-group"><button class="btn btn-danger btnRemovePedido" data-pedido="'+i+'" type="button"><i class="fa fa-times" aria-hidden="true"></i></button></div></div>');
        objPedidoData.changeProduto(i);
        $(".produtos").chosen({width: "100%",  no_results_text: makeSmartyLabel('No_result'), disable_search_threshold: 10});
    });

    $("#addItem").click(function(){
        duplicateRow('itemList');
    });

    $(document).on('click', '.btnRemovePedido', function() {
        $('#item_'+$(this).data('pedido')).remove();
    });

    $(document).on('click', '.btnViewPicture', function() {
        console.log($('#produtos_'+$(this).data('pedido')).val());

        $.ajax({
            type: "POST",
            url: path + '/scm/scmPedidoCompra/_makeProdutoGallery',
            data: {idproduto: $('#produtos_'+$(this).data('pedido')).val()},
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-pedidocompra');
            },
            success: function(ret){

                if(ret) {

                    $('#modal-form-picture').modal('show');
                    $('#gallery-inner').html(ret);
                } else {
                    modalAlertMultiple('warning','Este produto n&atilde;o possui imagens !','alert-create-pedidocompra');
                }
            }
        });


    });

    /*
     * Validate
     */
    $("#create-pedidocompra-form").validate({
        ignore:[],
        rules: {
            dataentrega: "required",
            motivo:      "required",
            cmbTurma: {required: function(element){
                    return ($('#iduserrole').val() == '1' || (($('#iduserrole').val() == '2' || $('#iduserrole').val() == '3') && "#flagturma:checked"));
                }
            }

        },
        messages: {
            dataentrega: makeSmartyLabel('Alert_field_required'),
            motivo:      makeSmartyLabel('Alert_field_required'),
            cmbTurma: {required: makeSmartyLabel('Alert_field_required')}

        }
    });

    $("#update-pedidocompra-form").validate({
        ignore:[],
        rules: {
            dataentrega: "required",
            motivo:      "required",
            cmbTurma: {required: function(element){
                return ($('#iduserrole').val() == '1' || (($('#iduserrole').val() == '2' || $('#iduserrole').val() == '3') && "#flagturma:checked"));
                }
            }

        },
        messages: {
            dataentrega: makeSmartyLabel('Alert_field_required'),
            motivo:      makeSmartyLabel('Alert_field_required'),
            cmbTurma: {required: makeSmartyLabel('Alert_field_required')}

        }
    });

    $("#cmbTurma").change(function(){
        objPedidoData.changeReplicateLbl($(this).val());
    });
});

function sendNotification(transaction,codeRequest)
{
    /*
     *
     * This was necessary because in some cases we have to send e-mail with the newly added attachments.
     * We only have access to these attachments after the execution of the dropzone.
     *
     */
    $.ajax({
        url : path + '/scm/scmPedidoCompra/sendNotification',
        type : 'POST',
        data : {
            transaction: transaction,
            code_request: codeRequest
        },
        success : function(data) {
            //console.log(data);
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
    $( "#produtos_"+ intCurrentRowId , clonedRow ).attr( { "id" :"produtos_" + intNewRowId, "accesskey" : intNewRowId } );
    $( "#btnViewPicture_"+ intCurrentRowId , clonedRow ).attr( { "id" :"btnViewPicture_" + intNewRowId, "data-pedido":intNewRowId, "accesskey" : intNewRowId, "value" :""  } );
    $( "#quantidades_"+ intCurrentRowId , clonedRow ).attr( { "id" :"quantidades_" + intNewRowId, "accesskey" : intNewRowId} ).val('');
    $( "#availability_"+ intCurrentRowId , clonedRow ).attr( { "id" :"availability_" + intNewRowId , "accesskey" : intNewRowId } );
    $( "#produtos_"+ intCurrentRowId + "_chosen" , clonedRow ).attr( { "id" :"produtos_" + intNewRowId + "_chosen", "accesskey" : intNewRowId } );

    // Add to the new row to the original table
    $( "#" + strTableName ).append( clonedRow );

    $( "#availability_"+ intNewRowId).html('');
    $( "#produtos_"+ intNewRowId + "_chosen").remove();
    objPedidoData.changeProduto(intNewRowId);

    // And finally change the ID of the last row to something we can
    //delete later, not sure why can not be done before the append :S
    $( "#" + strTableName + " tr:last" ).attr( "id", "detailsTr" + intNewRowId );


    $( "#produtos_"+ intNewRowId ).focus();
}

function removeRow(id,strTableName){
    var i = id.parentNode.parentNode.rowIndex, msgDiv;
    console.log(id.parentNode.parentNode.rowIndex);
    msgDiv = 'alert-create-pedidocompra';

    if($("#"+strTableName+" tbody tr").length == 1){
        modalAlertMultiple('info', makeSmartyLabel('Alert_dont_remove_row'),msgDiv);
    }else{
        document.getElementById(strTableName).deleteRow(i);
    }
}

function checkAvailability(id){

    var i = $(id).closest('tr').find('#numId').val(), productID = $("#produtos_"+i).val(), quantity = $("#quantidades_"+i).val();

    if($('#availability_'+i).hasClass('text-danger'))
        $('#availability_'+i).removeClass('text-danger');
    if($('#availability_'+i).hasClass('text-navy'))
        $('#availability_'+i).removeClass('text-navy');

    $('#availability_'+i).html("");

    $.post(path+"/scm/scmPedidoCompra/checkProductAvailability",
        {"productID":productID,"quantity":quantity},
        function(res) {
            var obj = jQuery.parseJSON(JSON.stringify(res));
            if(obj.success){
                $('#availability_'+i).html("<strong>"+obj.message+"</strong>").addClass(obj.txtType);
            }else{
                $('#availability_'+i).html(obj.message).addClass('text-danger');
            }
            return false;
        },"json");
}