<div class="modal fade" data-backdrop="static" id="modal-form-exchange" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <div class="modal-dialog modal-lg" role="document" style="width:1000px;">

        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Troca do Pedido Nº <span id="idpedidoex"></span></h4>
            </div>

            <div class="modal-body">
                {*
                    Need to change "div id" if have more than 1 modal in the page,
                    and use modalAlertMultiple instead modalAlert
                    *}
                <div id="alert-evaluate"></div>

                <form role="form" id="exchange-form"  method="post" class="form-horizontal">
                    <!-- Hidden -->

                    <input type="hidden" name="_tokenEx" id= "_tokenEx" value="">
                    <input type="hidden" name="idpedidoexchange" id= "idpedidoexchange" value="">

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-1 b-l"></div>

                        <div class="col-sm-11 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Solicitante:</label>
                                <div id="txtOwner"class="col-sm-5 form-control-static"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg ">

                        <div class="col-sm-1 b-l"></div>

                        <div class="col-sm-11 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Data Entrega:</label>
                                <div id="txtDtDelivery" class="col-sm-3 form-control-static"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg turmaLine hide" >
                        <div class="col-sm-1 b-l"></div>

                        <div class="col-sm-11 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Turma:</label>
                                <div id="txtClass"class="col-sm-3 form-control-static"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg turmaLine hide" >
                        <div class="col-sm-1 b-l"></div>

                        <div class="col-sm-11 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Aula:</label>
                                <div id="txtAula" class="col-sm-6 form-control-static"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-1 b-l"></div>

                        <div class="col-sm-11 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Motivo da Compra:</label>
                                <div class="col-sm-6 form-control-static">
                                    <textarea rows="6" cols="100" id="motivoEx" name="motivoEx" class="form-control input-sm" required readonly></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg">
                        <div class="col-sm-1 b-l"></div>

                        <div class="col-sm-11 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Centro de Custo:</label>
                                <div id="txtCentroCusto" class="col-sm-5 form-control-static"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-1 b-l"></div>

                        <div class="col-sm-11 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Conta Contábil:</label>
                                <div id="txtContaContab" class="col-sm-5 form-control-static"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row wrapper  white-bg ">
                        <div class="col-sm-1 b-l"></div>

                        <div class="col-sm-11 b-l">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Status do pedido:</label>
                                <div id="txtStatus" class="col-sm-5 form-control-static"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row wrapper  white-bg">
                        <div class="col-sm-12 b-l">
                            <div class="col-sm-1 b-l"></div>
                            <div class="col-sm-10 b-l">
                                <table id="itemList" class="table table-hover table-striped">
                                    <thead>
                                    <tr>
                                        <th class="col-sm-5 text-center"><h4><strong>{$smarty.config.Item}</strong></h4></th>
                                        <th class="col-sm-1 text-center"><h4><strong>&nbsp;</strong></h4></th>
                                        <th class="col-sm-2 text-center"><h4><strong>{$smarty.config.availability}</strong></h4></th>
                                        <th class="col-sm-1 text-center"><h4><strong>{$smarty.config.scm_Quantidade}</strong></h4></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.Close}</button>
                <button type="submit" class="btn btn-primary" id="btnSave">{$smarty.config.Save}</button>
            </div>
        </div>
    </div>
</div>
