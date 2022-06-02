

<div class="modal fade"  id="modal-form-repass" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->


            <div class="modal-dialog modal-lg"  role="document" style="width:1150px;"> {*style="width:1250px;"*}

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Pedido Nº <label id="idpedidorepasshead">{$hidden_idpedidoaprovador}</label></h4>
                </div>

                <div class="modal-body">
                    {*
                     Need to change "div id" if have more than 1 modal in the page,
                     and use modalAlertMultiple instead modalAlert
                     *}
                    <div id="alert-evaluate"></div>


                    <form role="form" id="repass-form" class="form-horizontal" method="post">
                        <!-- Hidden -->
                        <input type="hidden" name="_token" id= "_token" value="{$token}">
                        <input type="hidden" name="idpedidorepass" id= "idpedidorepass" value="{$hidden_idpedidoaprovador}">

                        <div class="row wrapper  white-bg ">
                            <div class="col-sm-1 b-l">
                            </div>

                            <div class="col-sm-11 b-l">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Solicitante:</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="personname" name="personname" class="form-control input-sm"  value="{$codigonomecentrodecusto}" readonly />
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row wrapper  white-bg ">
                            <div class="col-sm-1 b-l">
                                <div class="text-center" style="height:50px;"></div>
                            </div>

                            <div class="col-sm-11 b-l">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Data Entrega:</label>
                                    <div class="col-sm-3">
                                        <input type="text" id="dataentrega" name="dataentrega" class="form-control input-sm" placeholder="" value="" readonly />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="line_turma" class="row wrapper  white-bg " >
                            <div class="col-sm-1 b-l">
                            </div>

                            <div class="col-sm-11 b-l">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Turma:</label>
                                    <div class="col-sm-3">
                                        <input type="text" id="turmaabrev" class="form-control input-sm" value="" readonly />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row wrapper  white-bg ">
                            <div class="col-sm-1 b-l">
                            </div>

                            <div class="col-sm-11 b-l">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Motivo da Compra:</label>
                                    <div class="col-sm-5">
                                        <textarea id="motivo" name="motivo" rows="6" class="form-control input-sm" required placeholder="" value="" readonly ></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row wrapper  white-bg ">
                            <div class="col-sm-1 b-l">
                            </div>

                            <div class="col-sm-11 b-l">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Centro de Custo:</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="codigonomecentrodecusto" name="codigonomecentrodecusto" class="form-control input-sm"  value="" readonly />
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row wrapper  white-bg ">
                            <div class="col-sm-1 b-l">
                            </div>

                            <div class="col-sm-11 b-l">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Conta Contábil:</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="codigonomecontacontabil" name="codigonomecontacontabil" class="form-control input-sm"  value="" readonly />
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row wrapper  white-bg ">
                            <div class="col-sm-1 b-l">
                            </div>

                            <div class="col-sm-11 b-l">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Status do pedido:</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="nomestatus" name="nomestatus" class="form-control input-sm"  value="" readonly />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row wrapper  white-bg ">
                            <div class="col-sm-1 b-l">
                            </div>

                            <div class="col-sm-11 b-l">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Repassar para:</label>
                                    <div class="col-sm-5">
                                        <select class="form-control input-sm"  id="cmbAprovator" name="cmbAprovatora" >
                                            {html_options values=$aprovatorids output=$aprovatorvals selected=$idaprovator}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row wrapper  white-bg">
                            <div class="col-sm-1 b-l"></div>
                            <div class="col-sm-10 b-l">
                                <table class="table">
                                    <colgroup>
                                        <col class="col-sm-5"/>
                                        <col class="col-sm-2"/>
                                        <col class="col-sm-3"/>
                                    </colgroup>
                                    <thead>
                                    <th style="text-align: center">ITEM</th>
                                    <th style="text-align: center">QUANTIDADE</th>
                                    <th style="text-align: center">STATUS</th>
                                    </thead>
                                    <tbody id="itemlist"></tbody>
                                </table>
                            </div>
                        </div>

                    </form>

                    <div class="row col-lg-12 ">
                        <div class="form-group col-lg-12" style="padding-right: 5px;">
                            <div id="alert-repass"></div>
                        </div>
                    </div>

                    <div class="row">

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.Close}</button>
                    <button type="submit" class="btn btn-primary" id="btnSendRepass" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> {$smarty.config.Processing}">{$smarty.config.Send}</button>
                </div>
            </div>
        </div>
    </form>
</div>
