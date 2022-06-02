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

    if(access[1] == 'N'){
        $("#btnUpdatePedidoOperador").removeClass('hide');
        $("#btnUpdatePedidoOperador").addClass('hide');
    }

    /*
     *  Chosen
     */
    $(".produtos").chosen({ width: "100%",    no_results_text: "Nada encontrado!"});
    $(".status").chosen({ width: "100%",    no_results_text: "Nada encontrado!"});
    $(".fornecedores").chosen({ width: "100%",    no_results_text: "Nada encontrado!"});
    $(".contacontabil").chosen({ width: "100%",    no_results_text: "Nada encontrado!"});
    $(".centrodecusto").chosen({ width: "100%",    no_results_text: "Nada encontrado!"});

    /*
   * Mask
   */
    $("input[id='valoresunitarios']").mask('0000000000.00', {reverse: true});
    $("input[id='valorestotais']").mask('0000000000.00', {reverse: true});
    $("input[id='valoresfrete']").mask('0000000000.00', {reverse: true});


    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });


    /*
     * Combos
     */
    var objPedidoData = {
        changeProduto: function(id) {
            var id = id;
            $.post(path+"/scm/scmPedidoOperador/ajaxProduto",
                function(valor) {
                    $("#produtos_"+id).html(valor);
                    $("#produtos_"+id).trigger("chosen:updated");
                    return false;
                })
            return false ;
        }

    }

    var objCotacaoData = {
        changeFornecedores: function(id) {
            var id = id;
            $.post(path+"/scm/scmPedidoOperador/ajaxFornecedor",
                function(valor) {
                    $("#fornecedores_"+id).html(valor);
                    $("#fornecedores_"+id).trigger("chosen:updated");
                    return false;
                })
            return false ;
        },
        totalizadorItens: function() {
            var valorlist = $("input[id='valorestotais']"), total = 0,
                totalfrete = $("#totalfrete").val();

            for(var i = 0; i < valorlist.length; i++){
                if(valorlist[i].value !== ''){
                    total = total + parseFloat(valorlist[i].value);
                }else{
                    total = total + 0;
                }
            }

            totalfrete = totalfrete !== '' ? parseFloat(totalfrete) : 0;
            var valortotal = total + totalfrete;
            $("#totalitens").val(total.toFixed(2));
            $("#totalpedido").val(valortotal.toFixed(2));
            return false ;
        },
        totalizadorFretes: function() {
            var valorlist = $("input[id='valoresfrete']"), total = 0,
                totalitens = $("#totalitens").val();

            for(var i = 0; i < valorlist.length; i++){
                if(valorlist[i].value !== ''){
                    total = total + parseFloat(valorlist[i].value);
                }else{
                    total = total + 0;
                }
            }

            totalitens = totalitens !== '' ? parseFloat(totalitens) : 0;
            var valorpedido = total + totalitens;
            $("#totalfrete").val(total.toFixed(2));
            $("#totalpedido").val(valorpedido.toFixed(2));
            return false ;
        }

    }

    var objPedidoOperadorData = {
        changeContaContabil: function() {
            var centrodecustoId = $(".centrodecusto").val(), pedidoId  = $("#idpedidooperador").val();
            $.post(path+"/scm/scmPedidoOperador/ajaxContaContabil",{centrodecustoId: centrodecustoId,pedidoId: pedidoId},
                function(valor) {
                    $(".contacontabil").html(valor);
                    $(".contacontabil").trigger("chosen:updated");
                    return false;
                })
            return false ;
        },
    }

    $(".centrodecusto").change(function(){
        objPedidoOperadorData.changeContaContabil();


    });

    $(".status").change(function(){

        if($( "select[id='idstatus'] option:selected" ).val() == 9 || $( "select[id='idstatus'] option:selected" ).val() == 21){
            //console.log('habilita');
            $("#line_motivo").removeClass('hidden');
            $(".itenscotacaolayout").addClass('hidden');
        }else{
            //console.log('nao habilita');
            $("#line_motivo").addClass('hidden');
            $(".itenscotacaolayout").removeClass('hidden');
        }

    });

    $("input[id='valorestotais']").on('keyup',function (e) {
        objCotacaoData.totalizadorItens();
    });

    $("input[id='valoresfrete']").on('keyup',function (e) {
        objCotacaoData.totalizadorFretes();
    });

    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/scm/scmPedidoOperador/index');

    $(document).on("submit", "form", function(event)
    {
        event.preventDefault();

        var url=$(this).attr("action");
        $.ajax({
            url: url,
            type: $(this).attr("method"),
            dataType: "JSON",
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function (data, status)
            {

            },
            error: function (xhr, desc, err)
            {


            }
        });

    });

    $("#btnUpdatePedidoOperador").click(function(){

        if (!$("#update-pedidooperador-form").valid()) {
            return false ;
        }

        var parent = $(this).parents('form');

        if(!$("#btnUpdatePedidoOperador").hasClass('disabled')){
            $.ajax({
                type: "POST",
                contentType: false,
                processData: false,
                url: path + '/scm/scmPedidoOperador/updatePedidoOperador/idpedidooperador/' + $('#idpedidooperador').val(),
                dataType: 'json',
                data: new FormData(parent.get(0)),
                error: function (ret) {
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-pedidooperador');
                },
                success: function(ret){

                    //console.log(ret);

                    var obj = jQuery.parseJSON(JSON.stringify(ret));

                    if(obj.status == 'OK' ) {

                        var idpedidooperador = obj.idpedidooperador;

                        if(obj.sendmail == 'S'){sendNotification('approve-scmrequest-operator',idpedidooperador);}

                        $('#modal-notification').html('Pedido atualizado com sucesso');
                        $("#btn-modal-ok").attr("href", path + '/scm/scmPedidoOperador/index');
                        $("#tipo_alerta").attr('class', 'alert alert-success');
                        $('#modal-alert').modal('show');

                    } else {
                        modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-pedidooperador');
                    }
                },
                beforeSend: function(){
                    $("#btnUpdatePedidoOperador").attr('disabled','disabled');
                },
                complete: function(){
                    $("#btnUpdatePedidoOperador").removeAttr('disabled')
                }
            });
        }

    });

    // Buttons
    $("#btnEmail").click(function(){
        $('#modal-form-email').modal('show');
    });

    $("#btnPrint").click(function(){
        if (!$("#update-pedidooperador-form").valid()) {
            return false ;
        }

        //console.log($("#update-pedidooperador-form").serialize());

        $.ajax({
            type: "POST",
            url: path + "/scm/scmPedidoOperador/makeReport",
            data: $("#update-pedidooperador-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger',makeSmartyLabel('Alert_failure'),'alert-reopen');
            },
            success: function(fileName) {

                if(fileName){
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
                else {
                }
            }
        });
        return false;
    });

    var i = $("#_totalitens").val();

    $('.btnAddCotacao').click (function() {
        //console.log(i);
        $('.cotacao-'+$(this).data('cotacao')).append(
            '<div class="col-sm-12 form-group itenscotacaolayout" id="item_'+i+'">'+
             '<div class="col-sm-11">'+
                '<div class="col-sm-5">'+
                    '<label class="control-label">Fornecedor:</label>'+
                    '<select class="form-control input-sm fornecedores" id="fornecedores_'+i+'" name="fornecedores['+$(this).data('cotacao')+'][]"></select>'+
                '</div>'+
                '<div class="col-sm-2">' +
                    '<label class="control-label">Unitário: </label>' +
                    '<input type="text" id="valoresunitarios" name="valoresunitarios['+$(this).data('cotacao')+'][]" class="form-control input-sm valoresunitarios'+$(this).data('cotacao')+i+'" >' +
                '</div> ' +
                '<div class="col-sm-2">' +
                    '<label class="control-label">Total:</label>' +
                    '<input type="text" id="valorestotais" name="valorestotais['+$(this).data('cotacao')+'][]" class="form-control input-sm valorestotais'+$(this).data('cotacao')+i+'" >' +
                '</div>' +
                '<div class="col-sm-2">' +
                    '<label class="control-label">Frete:</label>' +
                    '<input type="text" id="valoresfrete" name="valoresfrete['+$(this).data('cotacao')+'][]" class="form-control input-sm valoresfrete'+$(this).data('cotacao')+i+'" >' +
                '</div>' +
             '</div>' +
             '<div class="col-sm-11">' +
                '<div class="col-sm-4">' +
                    '<label class="control-label">Pdf:</label>' +
                    '<input type="file" id="arquivos" data-arquivo="'+$(this).data('cotacao')+i+'" name="arquivos['+$(this).data('cotacao')+'][]" placeholder="" class="form-control input-sm arquivos" >' +
                '</div>' +
                '<div class="col-sm-1 text-center">' +
                    '<label class="control-label">Transp.</label>' +
                    '<div class="checkbox i-checks">' +
                        '<label> <input type="checkbox" id="flagcarrier" name="flagcarrier['+$(this).data('cotacao')+'][]" value="S"> <i></i> &nbsp;</label>' +
                    '</div>' +
                '</div>' +
             '</div>' +
            '<div class="col-sm-1 btnAddRemovelayout">' +
                '<br>' +
                '<button class="btn btn-danger btnRemoveCotacao" data-cotacao="'+i+'" type="button"><i class="fa fa-times" aria-hidden="true"></i></button>' +
            '</div>' +
           '</div>'
        );
        objCotacaoData.changeFornecedores(i);
        $(".fornecedores").chosen({ width: "100%", no_results_text: "Nada encontrado!"});
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });
        i++;
    });

    $(document).on('click', '.btnRemoveCotacao', function() {
        $('#item_'+$(this).data('cotacao')).remove();
    });

    $("#btnSendEmail").click(function(){
        if (!$("#email-form").valid()) {
            //console.log('nao validou') ;
            return false;
        }

        var itens = [];
        $("input:checkbox[name=itensemail]:checked").each(function(){
            itens.push($(this).val());
        });

        //console.log($("#quantidade").val());

        $.ajax({
            type: "POST",
            url: path + '/scm/scmPedidoOperador/sendEmail',
            dataType: 'json',
            data: $("#email-form").serialize() + "&itens=" + itens

            ,
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel enviar !','alert-email');
            },
            success: function(ret){
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if($.isNumeric(obj.idunidade)) {
                    modalAlertMultiple('success','Email enviado com sucesso !','alert-email');
                    objProdutoData.changeUnidade();
                } else {
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel enviar !','alert-email');
                }
            }
        });

    });

    $("#btnNote").click(function(){
        $('#modal-form-note').modal('show');
        $('#pedidonote').code('');
        $('#typeuser').val('operator');
    });

    $("#btnSendNote").click(function(){
        var _token = $('#_token').val(), idpedido = $('#idpedidonote').val(),
            noteContent = $('#pedidonote').code(), displayType = $("input[name='displayUser']:checked").val();

        if(noteContent == ''){
            modalAlertMultiple('warning','Favor digite o apontamento','alert-noteadd');
            return false;
        }else{
            $.ajax({
                type: "POST",
                url: path + "/scm/scmPedidoOperador/savePedidoNote",
                dataType: 'json',
                data: {_token: _token,idpedido:idpedido,noteContent:noteContent,displayType:displayType},
                error: function (ret) {
                    modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
                },
                success: function(ret) {
                    var obj = jQuery.parseJSON(JSON.stringify(ret));
                    if(obj.status == 'OK') {
                        modalAlertMultiple('info',makeSmartyLabel('Alert_note_sucess'),'alert-noteadd');
                        if($('#typeuser').val() == 'operator'){
                            sendNotification('addnote-operator',$('#idpedidonote').val());
                        }else{
                            sendNotification('addnote-user',$('#idpedidonote').val());
                        }

                        $('#notes_line').empty();
                        $('#notes_line').html(obj.addednotes);
                        $('#pedidonote').code('');
                    } else {
                        modalAlertMultiple('danger',makeSmartyLabel('Error_insert_note'),'alert-noteadd');
                    }
                },
                beforeSend: function(){
                    $("#btnSendNote").attr('disabled','disabled');
                },
                complete: function(){
                    $("#btnSendNote").removeAttr('disabled')
                }
            });
        }
        return false;
    });

    /*
     * Validate
     */
    $("#create-pedidooperador-form").validate({
        ignore:[],
        rules: {
            dataentrega: "required",
            motivo:      "required",


        },
        messages: {
            dataentrega: "Campo obrigat&oacute;rio",
            motivo:      "Campo obrigat&oacute;rio",

        }
    });

    $("#update-pedidooperador-form").validate({
        ignore:[],
        rules: {
            dataentrega: "required",
            motivo:      "required",
            motivorejeicao:{
                required: function(element) {
                    return ($("#idstatus").val() == 9 || $("#idstatus").val() == 21);
                }
            }
        },
        messages: {
            dataentrega: "Campo obrigat&oacute;rio",
            motivo:      "Campo obrigat&oacute;rio",
            motivorejeicao:{required:"Campo obrigat&oacute;rio"}
        }
    });

    $(document).on('blur', '.arquivos', function() {
        //console.log($(this).data('arquivo')+" Arquivo");
        $('#item_'+$(this).data('cotacao')).remove();
        //console.log("guto");
        //console.log($('.valoresunitarios'+$(this).data('arquivo')));
        if($(this).val()){
            $(".valoresunitarios"+$(this).data('arquivo')).attr("required","required");
            $(".valorestotais"+$(this).data('arquivo')).attr("required","required");
        }
    });

    objPedidoOperadorData.changeContaContabil();

    $('#pedidonote').summernote(
        {
            toolbar:[
                ["style",["style"]],
                ["font",["bold","italic","underline","clear"]],
                ["fontname",["fontname"]],["color",["color"]],
                ["para",["ul","ol","paragraph"]]
            ],
            disableDragAndDrop: true,
            minHeight: null,  // set minimum height of editor
            maxHeight: 250,   // set maximum height of editor
            height: 250,      // set editor height
            width: 600,       // set editor width
            focus: false     // set focus to editable area after initializing summernote


        }
    );

    $(document).on('click', '.btnViewPicture', function() {
        var idproduto = $('#produtos_'+$(this).data('pedido')).val();

        $.ajax({
            type: "POST",
            url: path + '/scm/scmPedidoCompra/_makeProdutoGallery',
            data: {idproduto: idproduto},
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel inserir !','alert-update-pedidoaprovador');
            },
            success: function(ret){

                if(ret) {

                    $('#modal-form-picture').modal('show');
                    $('#gallery-inner').html(ret);
                } else {
                    modalAlertMultiple('warning','Este produto n&atilde;o possui imagens !','alert-update-pedidoaprovador');
                }
            }
        });


    });

    $("#btnSaveExchange").click(function(){

        var parent = $(this).parents('form');

        if(!$("#btnSaveExchange").hasClass('disabled')){
            $.ajax({
                type: "POST",
                url: path + '/scm/scmPedidoOperador/saveExchange',
                dataType: 'json',
                data: $("#exchange-pedidooperador-form").serialize(),
                error: function (ret) {
                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-exchange-pedidooperador');
                },
                success: function(ret){

                    //console.log(ret);

                    var obj = jQuery.parseJSON(JSON.stringify(ret));

                    if(obj.success) {

                        $('#modal-notification').html(obj.message);
                        $("#btn-modal-ok").attr("href", path + '/scm/scmPedidoOperador/index');
                        $("#tipo-alert").attr('class', 'alert alert-'+obj.alerttype);
                        $('#modal-alert').modal('show');

                    } else {
                        modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-exchange-pedidooperador');
                    }
                },
                beforeSend: function(){
                    $("#btnSaveExchange").addClass('disabled');
                    $("#btnCancel").addClass('disabled');
                },
                complete: function(){
                    $("#btnSaveExchange").removeClass('disabled');
                    $("#btnCancel").removeClass('disabled');
                }
            });
        }

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

        },
        error : function(request,error)
        {

        }
    });

    return false ;

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