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
        $("#btnCreatePedidoAprovador").addClass('hide');
    }

    if(access[2] != "Y"){
        $("#btnUpdatePedidoAprovador").addClass('hide');
    }

    /*
     *  Chosen
     */
    $(".produtos").chosen({ width: "95%",    no_results_text: "Nada encontrado!"});
    $(".status").chosen({ width: "100%",    no_results_text: "Nada encontrado!"});
    $(".fornecedores").chosen({ width: "95%",    no_results_text: "Nada encontrado!"});
    $(".contacontabil").chosen({ width: "95%",    no_results_text: "Nada encontrado!"});
    $(".centrodecusto").chosen({ width: "95%",    no_results_text: "Nada encontrado!"});
    $("#cmbAprovator").chosen({ width: "100%",  no_results_text: "Nada encontrado!"});

    /*
     * Mask
     */
    $('.valoresunitarios').mask('000000000000000,00', {reverse: true});
    $('.valorestotais').mask('000000000000000,00', {reverse: true});

    /*
     * Combos
     */


    /*
     * Buttons
     */
    $("#btnCancel").attr("href", path + '/scm/scmPedidoAprovador/index');

    $("#btnUpdatePedidoAprovador").click(function(){

        if (!$("#update-pedidoaprovador-form").valid()) {
            return false ;
        }

        $.ajax({
            type: "POST",
            url: path + '/scm/scmPedidoAprovador/updatePedidoAprovador/idpedidoaprovador/' + $('#idpedidoaprovador').val(),
            dataType: 'json',
            data: $("#update-pedidoaprovador-form").serialize(),
            error: function (ret) {
                modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-pedidoaprovador');
            },
            success: function(ret){

                console.log(ret);

                var obj = jQuery.parseJSON(JSON.stringify(ret));

                if(obj.status == 'OK' ) {

                    var idpedidoaprovador = obj.idpedidoaprovador;

                    if(obj.sendmail == 'S'){sendNotification('approve-scmrequest-operator',idpedidoaprovador);}

                    $('#modal-notification').html('Pedido atualizado com sucesso');
                    $("#btn-modal-ok").attr("href", path + '/scm/scmPedidoAprovador/index');
                    $("#tipo_alerta").attr('class', 'alert alert-success');
                    $('#modal-alert').modal('show');

                } else {

                    modalAlertMultiple('danger','N&atilde;o foi poss&iacute;vel atualizar !','alert-create-pedidoaprovador');

                }

            },
            beforeSend: function(){
                $("#btnUpdatePedidoAprovador").attr('disabled','disabled');
            },
            complete: function(){
                $("#btnUpdatePedidoAprovador").removeAttr('disabled')
            }

        });

    });

    $("#btnRepass").click(function(){
        $('#modal-form-repass').modal('show');
    });

    $("#btnSendRepass").click(function(){
        var _token = $('#_token').val(), idpedido = $('#idpedidorepass').val(), idincharge = $('#cmbAprovator').val();
        $.ajax({
            type: "POST",
            url: path + "/scm/scmPedidoAprovador/repassPedido",
            dataType: 'json',
            data: {_token: _token,idpedido:idpedido,idincharge:idincharge},
            error: function (ret) {
                modalAlertMultiple('danger','Erro ao repassar o pedido','alert-repass');
            },
            success: function(ret) {
                var obj = jQuery.parseJSON(JSON.stringify(ret));
                if(obj.status == 'OK') {
                    modalAlertMultiple('info','Pedido repassado com sucesso','alert-repass');
                    sendNotification('repass-request',$('#idpedidorepass').val());
                    setTimeout(function(){
                        $('#modal-form-repass').modal('hide');
                        location.href = path + "/scm/scmPedidoAprovador/index";
                    },2000);

                } else {
                    modalAlertMultiple('danger','Erro ao repassar o pedido','alert-repass');
                }
            },
            beforeSend: function(){
                $("#btnSendRepass").attr('disabled','disabled');
            },
            complete: function(){
                $("#btnSendRepass").removeAttr('disabled')
            }
        });

        return false;
    });

    /*
     * Validate
     */
    $("#create-pedidoaprovador-form").validate({
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
    $("#update-pedidoaprovador-form").validate({
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

    $(".status").change(function(){

        if($( "select[id='idstatus'] option:selected" ).val() == 9){
            //console.log('habilita');
            $("#line_motivo").removeClass('hidden');
            $(".itenscotacaolayout").addClass('hidden');
        }else{
            //console.log('nao habilita');
            $("#line_motivo").addClass('hidden');
            $(".itenscotacaolayout").removeClass('hidden');
        }

    });

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    if($('#typeoperation').val() == 'view'){
        $('.i-checks').iCheck('disable');
    }

    $(document).on('click', '.btnViewPicture', function() {
        var idproduto = $(this).data('pedido');

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