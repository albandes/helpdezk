

<div class="modal fade"  id="modal-form-repass" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->


            <div class="modal-dialog modal-lg"  role="document" > {*style="width:1250px;"*}

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
                                    <label class="col-sm-2 control-label">Repassar para:</label>
                                    <div class="col-sm-5">
                                        <select class="form-control input-sm"  id="cmbAprovator" name="cmbAprovator" >
                                            {html_options values=$aprovatorids output=$aprovatorvals selected=$idaprovator}
                                        </select>
                                    </div>
                                </div>
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
