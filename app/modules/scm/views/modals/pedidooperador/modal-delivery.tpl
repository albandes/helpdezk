

<div class="modal fade"  id="modal-form-delivery" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->


            <div class="modal-dialog modal-lg"  role="document" style="width:800px;"> {*style="width:1250px;"*}

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Comprovante de Entrega do Pedido Nº <span id="numpedido"></span></h4>
                </div>

                <div class="modal-body">
                    {*
                     Need to change "div id" if have more than 1 modal in the page,
                     and use modalAlertMultiple instead modalAlert
                     *}
                    <div id="alert-evaluate"></div>


                    <form role="form" id="delivery-form"  method="post" class="form-horizontal">
                        <!-- Hidden -->

                        <input type="hidden" name="_token" id= "_token" value="">
                        <input type="hidden" name="idpedidodelivery" id= "idpedidodelivery" value="">

                        <div class="row col-lg-12 ">
                            <div class="row wrapper  white-bg ">
                                <div class="col-sm-1 b-l">
                                    <div class="text-center" style="height:50px;">

                                    </div>
                                </div>

                                <div class="col-sm-11 b-l">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Data Entrega:</label>
                                        <div class="col-sm-3">
                                            <input type="text" id="dataentrega" name="dataentrega" class="form-control input-sm" value="" readonly />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row wrapper  white-bg " id="turmaline">
                                <div class="col-sm-1 b-l">
                                </div>

                                <div class="col-sm-11 b-l">
                                    <div class="form-group" >
                                        <label class="col-sm-3 control-label">Turma:</label>
                                        <div class="col-sm-3">
                                            <input type="text" class="form-control input-sm" id="txtTurma" value="" readonly />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row wrapper  white-bg ">
                                <div class="col-sm-1 b-l">
                                </div>

                                <div class="col-sm-11 b-l">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Motivo da Compra:</label>
                                        <div class="col-sm-9">
                                            <textarea rows="6" cols="100" id="motivo" name="motivo" class="form-control input-sm" readonly></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row wrapper  white-bg ">
                                <div class="col-sm-1 b-l">
                                </div>

                                <div class="col-sm-11 b-l">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Centro de Custo:</label>
                                        <div class="col-sm-5">
                                            <input type="text" id="nomecentrodecusto" name="nomecentrodecusto" class="form-control input-sm" value="" readonly />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row wrapper  white-bg ">
                                <div class="col-sm-1 b-l">
                                </div>

                                <div class="col-sm-11 b-l">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Conta Contábil:</label>
                                        <div class="col-sm-5">
                                            <input type="text" id="nomecontacontabil" name="nomecontacontabil" class="form-control input-sm" value="" readonly />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row wrapper  white-bg ">
                                <div class="col-sm-1 b-l">
                                </div>

                                <div class="col-sm-11 b-l">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Status do pedido:</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control input-sm" id="txtStatusPedido" value="" readonly />
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row wrapper  white-bg ">
                                <div class="col-sm-12 b-l">
                                    <h3>Selecione os Itens: </h3>
                                </div>
                            </div>

                            <div class="row wrapper  white-bg ">
                                <div class="col-sm-12 b-l" id="itemlist">
                                </div>
                            </div>
                        </div>
                        <div class="row col-lg-12 ">
                        </div>
                    </form>


                    <div class="row col-lg-12 ">
                        <div class="form-group col-lg-12" style="padding-right: 5px;">
                            <div id="alert-delivery"></div>
                        </div>
                    </div>

                    <div class="row">

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.Close}</button>
                    <button type="submit" class="btn btn-primary" id="btnSendPrint" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> {$smarty.config.Processing}">{$smarty.config.Print}</button>
                </div>
            </div>
        </div>
    </form>
</div>
