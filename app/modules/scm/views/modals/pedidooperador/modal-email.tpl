

<div class="modal fade"  id="modal-form-email" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->


            <div class="modal-dialog modal-lg"  role="document"> {*style="width:1250px;"*}

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Solicitação de cotação do Pedido Nº {$hidden_idpedidooperador}</h4>
                </div>

                <div class="modal-body">
                    {*
                     Need to change "div id" if have more than 1 modal in the page,
                     and use modalAlertMultiple instead modalAlert
                     *}
                    <div id="alert-evaluate"></div>



                    <form role="form" id="email-form"  method="post">
                        <!-- Hidden -->

                        <input type="hidden" name="_token" id= "_token" value="{$token}">
                        <input type="hidden" name="idpedidoemail" id= "idpedidoemail" value="{$hidden_idpedidooperador}">

                        <div class="row col-lg-12 ">
                            <div class="form-group col-lg-9" style="padding-right: 5px;">

                                <div class="col-sm-12">
                                    <label class="control-label">Fornecedor: </label>
                                    <select class="form-control input-sm fornecedores"  name="fornecedor" id="fornecedor">
                                        {html_options values=$fornecedorids output=$fornecedorvals selected=$itenscotacao.idperson}
                                    </select>
                                </div>
                                <div class="col-sm-12">
                                    <h2>Selecione os Itens: </h2>

                                </div>
                                {foreach $arrItens as $key => $value}
                                    <div class="col-sm-12">
                                        <input type="checkbox" name="itensemail" value="{$value.iditempedido}">
                                        <h4 class="itenslayout">{$value.nome}</h4> / <h4 class="itenslayout">{$value.quantidade}</h4>
                                        <input type="hidden" id="quantidade" value="{$value.quantidade}" name="quantidade[{$value.iditempedido}]">
                                        <input type="hidden" id="iditempedido" value="{$value.iditempedido}" name="iditempedido[{$value.iditempedido}]">
                                        <input type="hidden" id="nome" value="{$value.nome}" name="nome[{$value.iditempedido}]">
                                    </div>
                                {/foreach}
                            </div>
                        </div>
                        <div class="row col-lg-12 ">

                        </div>
                    </form>


                    <div class="row col-lg-12 ">
                        <div class="form-group col-lg-12" style="padding-right: 5px;">
                            <div id="alert-unidade"></div>
                        </div>
                    </div>

                    <div class="row">

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.Close}</button>
                    <button type="submit" class="btn btn-primary" id="btnSendEmail" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> {$smarty.config.Processing}">{$smarty.config.Send}</button>
                </div>
            </div>
        </div>
    </form>
</div>
