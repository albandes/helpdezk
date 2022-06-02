

<div class="modal fade"  id="modal-form-note" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >

    <!-- Hidden input to send value by POST  -->


            <div class="modal-dialog modal-lg"  role="document" style="width:730px;">

            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Apontamentos do Pedido Nº <label id="idpedidohead">{$hidden_idpedidooperador}</label></h4>
                </div>

                <div class="modal-body">
                    {*
                     Need to change "div id" if have more than 1 modal in the page,
                     and use modalAlertMultiple instead modalAlert
                     *}
                    <div id="alert-evaluate"></div>



                    <form role="form" id="note-form"  method="post" class="form-horizontal">
                        <!-- Hidden -->

                        <input type="hidden" name="_token" id= "_token" value="{$token}">
                        <input type="hidden" name="idpedidonote" id= "idpedidonote" value="{$hidden_idpedidooperador}">
                        <input type="hidden" name="typeuser" id="typeuser" value="">

                        <div class="row wrapper">
                            <div class="col-sm-12 b-l">
                                <label  class="control-label">{$smarty.config.Insert_note}:</label>
                            </div>
                        </div>

                        <div class="row wrapper">
                            <div class="col-sm-12 b-l">
                                <div id="pedidonote"></div>
                            </div>
                        </div>

                        <div class="col-sm-12 white-bg" style="height:10px;"></div>

                        <div id="display_line" class="row wrapper {$flagdispplay}">
                            <div class="col-sm-12 b-l">
                                <div class="form-group">
                                    <label  class="control-label">Visível:</label>
                                    <label class="radio-inline i-checks"> <input type="radio" name="displayUser" id="displayUser_1" value="1" checked>&nbsp;Solicitante</label>
                                    <label class="radio-inline i-checks"> <input type="radio" name="displayUser" id="displayUser_0" value="0">&nbsp;Operador / Aprovador</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 white-bg" style="height:10px;"></div>

                        <div class="row wrapper">
                            <div class="col-sm-12 b-l">
                                <button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.Close}</button>
                                <button type="submit" class="btn btn-primary btnSendNote" id="btnSendNote" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> {$smarty.config.Processing}">{$smarty.config.Send}</button>
                            </div>
                        </div>

                        <div class="col-sm-12 white-bg" style="height:10px;"></div>

                        <div class="row wrapper">
                            <div class="form-group col-lg-12" style="padding-right: 5px;">
                                <div id="alert-noteadd"></div>
                            </div>
                        </div>

                        <div class="col-sm-12 white-bg" style="height:10px;"></div>

                        <div class="row wrapper">
                            <div id="notes_line" class="col-sm-12 b-l">
                                {$notes}
                            </div>
                        </div>

                    </form>



                </div>

                <div class="modal-footer">
                    <!--<button type="button" class="btn btn-default" data-dismiss="modal">{$smarty.config.Close}</button>
                    <button type="submit" class="btn btn-primary btnSendNote" id="btnSendNote" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> {$smarty.config.Processing}">{$smarty.config.Send}</button>-->
                </div>
            </div>
        </div>
    </form>
</div>
