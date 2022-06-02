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
        $("#btnCreateEntradaProduto").addClass('hide');
    }

    if(access[2] != "Y"){
        $("#btnUpdateEntradaProduto").addClass('hide');
    }

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    /*
     *  Chosen
     */
    $(".produtos").chosen({ width: "100%",    no_results_text: "Nada encontrado!"});
    $(".fornecedores").chosen({ width: "100%",    no_results_text: "Nada encontrado!"})


    /*
     * Mask
     */
    $('#numeropedido').mask('999999/9999');
    $("input[id='valores']").mask('0000000000.00', {reverse: true});
    //$("input[id='quantidades']").mask('0000000000.0', {reverse: true});
    $("input[id='valorestotais']").mask('0000000000.00', {reverse: true});
    $("input[id='valorestotaisnotafiscal']").mask('0000000000.00', {reverse: true});

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
     * Combos
     */
    var objPedidoData = {
        changeProduto: function(id) {
            var id = id;
            $.post(path+"/scm/scmPedidoCompra/ajaxProduto",
                function(valor) {
                    $("#produtos_"+id).html(valor);
                    $("#produtos_"+id).trigger("chosen:updated");
                    return false;
                })
            return false ;
        },
        totalizeItens: function(){
            var total = 0, itemqt = $(document).find('.quantidades'), itemval = $(document).find('.valores');
            //console.log(itemqt.length);
            for(var i=0;i<itemqt.length; i++){
                total += itemqt[i].value * itemval[i].value;
            }
            $('#valorestotais').val(total.toFixed(2));
            $('#valorestotaisnotafiscal').val(total.toFixed(2));
        }

    }

    $("input[name='tipo']").on('ifClicked', function() { // bind a function to the change event
        var typeFlg = $(this).val();

        if(typeFlg == 'L'){
            $('.numeropedidodiv').hide();
            $('.notafiscaldiv').hide();
            $('.fornecedordiv').hide();
        }else {
            $('.numeropedidodiv').show();
            $('.notafiscaldiv').show();
            $('.fornecedordiv').show();
        }
    });

    $(document).on('keyup', '.quantidades',function(){
        objPedidoData.totalizeItens();
    });

    $(document).on('keyup', '.valores',function(){
        objPedidoData.totalizeItens();
    });


    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/scm/scmEntradaProduto/index');

    $("#btnCreateEntradaProduto").click(function(){

        if (!$("#create-entradaproduto-form").valid()) {
            return false ;
        }

        console.log($("#create-entradaproduto-form").serialize());

        //
        $.ajax({
            type: "POST",
            url: path + '/scm/scmEntradaProduto/createEntradaProduto',
            dataType: 'json',
            data: $("#create-entradaproduto-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-entradaproduto');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if($.isNumeric(obj.identradaproduto)) {

                    var identradaproduto = obj.identradaproduto;
                    //
                    $('#modal-identradaproduto').html(identradaproduto);
                    $('#modal-numeropedido').html(obj.numeropedido);

                    $("#btnModalAlert").attr("href", path + '/scm/scmEntradaProduto/index');

                    $('#modal-alert-create').modal('show');
                } else {
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-create-entradaproduto');
                }
            }
        });
    });


    $("#btnUpdateEntradaProduto").click(function(){

        if (!$("#update-entradaproduto-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/scm/scmEntradaProduto/updateEntradaProduto/identradaproduto/' + $('#identradaproduto').val(),
            dataType: 'json',
            data: $("#update-entradaproduto-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-entradaproduto');
            },
            success: function(ret){

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'OK' ) {

                    var identradaproduto = obj.identradaproduto;

                    $('#modal-notification').html('Pedido atualizado com sucesso');
                    $("#btn-modal-ok").attr("href", path + '/scm/scmEntradaProduto/index');
                    $("#tipo_alerta").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');

                } else {

                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-entradaproduto');
                }
            }
        });

    });

    var i = $("#_totalitens").val();
    $('#btnAddPedido').click (function() {
        console.log(i);
        $('.pedido').append('<div class="form-group itenspedidolayout" id="item_'+i+'"><div class="col-sm-7"><label class="control-label">Produto:</label><select class="form-control input-sm produtos" id="produtos_'+i+'" name="produtos[]"></select></div><div class="col-sm-2"><label class="control-label">Quantidade:</label><input type="number" id="quantidades" name="quantidades[]" class="form-control input-sm quantidades" step="0.25" min="0" /></div><div class="col-sm-2"><label class="control-label">Valor:</label> <input type="text" id="valores" name="valores[]" class="form-control input-sm valores" ></div><div class="col-sm-1"><div class="btn-group btnAddRemovelayout"><button class="btn btn-danger btnRemovePedido" data-pedido="'+i+'" type="button"><i class="fa fa-times" aria-hidden="true"></i></button></div></div></div>');
        objPedidoData.changeProduto(i);
        $(".produtos").chosen({ width: "100%", no_results_text: "Nada encontrado!"});
        i++;
    });

    $(document).on('click', '.btnRemovePedido', function() {
        $('#item_'+$(this).data('pedido')).remove();
    });

    /*
     * Validate
     */
    $("#create-entradaproduto-form").validate({
        ignore:[],
        rules: {
            numeronotafiscal:{required:function(element){return $("input[name='tipo']:checked").val() == 'C';}},
            idfornecedor:{required:function(element){return $("input[name='tipo']:checked").val() == 'C';}},
            dtnotafiscal: {required:function(element){return $("input[name='tipo']:checked").val() == 'C';}},
            motivo:      "required",
            valorestotais: "required",
            valorestotaisnotafiscal: "required"
        },
        messages: {
            numeronotafiscal:{required:makeSmartyLabel('Alert_field_required')},
            idfornecedor:{required:makeSmartyLabel('Alert_field_required')},
            dtnotafiscal: {required:makeSmartyLabel('Alert_field_required')},
            motivo:      makeSmartyLabel('Alert_field_required'),
            valorestotais: makeSmartyLabel('Alert_field_required'),
            valorestotaisnotafiscal: makeSmartyLabel('Alert_field_required'),

        }
    });
    $("#update-entradaproduto-form").validate({
        ignore:[],
        rules: {
            numeronotafiscal:{required:function(element){return $("input[name='tipo']:checked").val() == 'C';}},
            idfornecedor:{required:function(element){return $("input[name='tipo']:checked").val() == 'C';}},
            dtnotafiscal: {required:function(element){return $("input[name='tipo']:checked").val() == 'C';}},
            motivo:      "required",
            valorestotais: "required",
            valorestotaisnotafiscal: "required"
        },
        messages: {
            numeronotafiscal:{required:makeSmartyLabel('Alert_field_required')},
            idfornecedor:{required:makeSmartyLabel('Alert_field_required')},
            dtnotafiscal: {required:makeSmartyLabel('Alert_field_required')},
            motivo:       makeSmartyLabel('Alert_field_required'),
            valorestotais:  makeSmartyLabel('Alert_field_required'),
            valorestotaisnotafiscal:  makeSmartyLabel('Alert_field_required')
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